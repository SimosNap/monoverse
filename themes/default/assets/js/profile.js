document.addEventListener('DOMContentLoaded', () => {
	
	const profileSidebarDetails =
		document.querySelector(
			'.mv-public-profile-sidebar-details'
		);
	
	if (profileSidebarDetails) {
	
		const mobileQuery =
			window.matchMedia('(max-width: 980px)');
	
		const syncSidebarState = () => {
	
			if (mobileQuery.matches) {
				profileSidebarDetails.removeAttribute('open');
				return;
			}
	
			profileSidebarDetails.setAttribute(
				'open',
				''
			);
	
		};
	
		syncSidebarState();
	
		mobileQuery.addEventListener(
			'change',
			syncSidebarState
		);
	
	}
	
	const infiniteList =
		document.getElementById('profile-ping-infinite-list');
	
	const infiniteTrigger =
		document.getElementById('profile-ping-infinite-trigger');
	
	if (
		infiniteList &&
		infiniteTrigger
	) {
	
		let loading = false;
	
		const pageSize = Number(
			infiniteList.dataset.pageSize
				?? 5
		);
	
		let nextOffset = Number(
			infiniteList.dataset.nextOffset
				?? pageSize
		);
	
		const username =
			infiniteList.dataset.username
				?? '';
	
		if (username !== '') {
	
			const observer = new IntersectionObserver(
				async (entries) => {
	
					const entry = entries[0];
	
					if (
						!entry?.isIntersecting ||
						loading
					) {
						return;
					}
	
					loading = true;
	
					observer.unobserve(
						infiniteTrigger
					);
	
					try {
	
						const response = await fetch(
							`/profile/${encodeURIComponent(username)}/load?offset=${encodeURIComponent(nextOffset)}`,
							{
								method: 'GET',
								credentials: 'same-origin',
								headers: {
									'X-Requested-With': 'XMLHttpRequest'
								}
							}
						);
	
						if (!response.ok) {
							throw new Error(
								`HTTP ${response.status}`
							);
						}
	
						const html =
							await response.text();
	
						const template =
							document.createElement('template');
	
						template.innerHTML =
							html.trim();
	
						const loadedPosts =
							template.content.querySelectorAll(
								'.ping-card'
							).length;
	
						if (loadedPosts === 0) {
	
							observer.disconnect();
							infiniteTrigger.remove();
	
							return;
						}
	
						infiniteList.appendChild(
							template.content
						);
	
						nextOffset += loadedPosts;
	
						infiniteList.dataset.nextOffset =
							String(nextOffset);
	
						if (loadedPosts < pageSize) {
	
							observer.disconnect();
							infiniteTrigger.remove();
	
							return;
						}
	
						observer.observe(
							infiniteTrigger
						);
	
					} catch (error) {
	
						console.error(
							'Errore caricamento Ping profilo:',
							error
						);
	
						observer.observe(
							infiniteTrigger
						);
	
					} finally {
	
						loading = false;
	
					}
	
				},
				{
					root: null,
					rootMargin: '600px 0px',
					threshold: 0
				}
			);
	
			observer.observe(
				infiniteTrigger
			);
	
		}
	
	}

	const modal = document.getElementById('mv-moderation-modal');

	if (!modal) {
		return;
	}

	const form = document.getElementById('mv-moderation-form');
	const title = document.getElementById('mv-modal-title');
	const submit = document.getElementById('mv-modal-submit');

	function closeModal() {
		modal.hidden = true;
		document.body.classList.remove('mv-modal-open');
	}

	function openModal(button) {

		form.action = button.dataset.action;
		title.textContent = button.dataset.title;
		submit.textContent = button.dataset.submit;

		modal.hidden = false;
		document.body.classList.add('mv-modal-open');

		const reason = document.getElementById('moderation-reason');

		if (reason) {
			reason.focus();
		}
	}

	document.querySelectorAll('.js-open-moderation-modal').forEach(button => {

		button.addEventListener('click', () => {
			openModal(button);
		});

	});

	modal.querySelector('.mv-modal-backdrop')
		.addEventListener('click', closeModal);

	modal.querySelector('.mv-modal-close')
		.addEventListener('click', closeModal);

	modal.querySelector('.mv-modal-cancel')
		.addEventListener('click', closeModal);

	document.addEventListener('keydown', event => {

		if (event.key === 'Escape' && !modal.hidden) {
			closeModal();
		}

	});

});