document.addEventListener('DOMContentLoaded', function () {
	'use strict';

	const form = document.getElementById('landing-chat-form');
	
	if (!form) {
		return;
	}
	
	const registeredOnly =
	form.dataset.registeredOnly === 'true';

	const nickname = document.getElementById('nickinput');
	const age = document.getElementById('age');
	const city = document.getElementById('location');
	const sex = document.getElementById('sex-select');

	const nickNotice = document.getElementById('nsnotify');
	const nickOk = document.getElementById('nsok');
	const registeredOnlyNotice =
	document.getElementById(
		'nsregisteredonly'
	);
	const nickLoading = document.getElementById('nsloading');
	const passwordInput = document.getElementById('nspwd');
	
	const entryStep =
		document.getElementById(
			'landing-chat-step-entry'
		);
	
	const authStep =
		document.getElementById(
			'landing-chat-step-auth'
		);
	
	const authNickname =
		authStep?.querySelector(
			'[data-auth-nickname]'
		);
	
	const authBack =
		authStep?.querySelector(
			'[data-auth-back]'
		);
	
	let nicknameVerifiedForSubmit = false;

	const identity =
		form.querySelector(
			'[data-chat-identity]'
		);
	
	const identityToggle =
		form.querySelector(
			'[data-chat-identity-toggle]'
		);
	
	const identityMenu =
		form.querySelector(
			'[data-chat-identity-menu]'
		);
	
	const identityNickname =
		form.querySelector(
			'[data-chat-identity-nickname]'
		);
	
	const identityOptions =
		form.querySelectorAll(
			'[data-chat-identity-option]'
		);
	
	if (
		identityToggle
		&& identityMenu
	) {
	
		identityToggle.addEventListener(
			'click',
			function () {
	
				const willOpen =
					identityMenu.hidden;
	
				identityMenu.hidden =
					!willOpen;
	
				identityToggle.setAttribute(
					'aria-expanded',
					willOpen
						? 'true'
						: 'false'
				);
	
				identity.classList.toggle(
					'is-open',
					willOpen
				);
	
			}
		);
	
		identityOptions.forEach(
			function (option) {
	
				option.addEventListener(
					'click',
					function () {
	
						const selected =
							option.dataset.nickname
								?? '';
	
						if (
							selected === ''
							|| !nickname
						) {
							return;
						}
	
						nickname.value = selected;
	
						if (identityNickname) {
							identityNickname.textContent =
								selected;
						}
	
						identityOptions.forEach(
							function (item) {
	
								const active =
									item === option;
	
								item.classList.toggle(
									'is-active',
									active
								);
	
								const check =
									item.querySelector(
										'.fa-check'
									);
	
								if (check) {
									check.hidden =
										!active;
								}
	
							}
						);
	
						identityMenu.hidden = true;
	
						identityToggle.setAttribute(
							'aria-expanded',
							'false'
						);
	
						identity.classList.remove(
							'is-open'
						);
	
						nicknameVerifiedForSubmit = false;
	
					}
				);
	
			}
		);
	
	}

	// Usa localStorage solo in assenza di un profilo server.
	if (
		form.dataset.profileSource === 'local'
		&& window.MonoverseChatPreferences
	) {
		const preferences = window.MonoverseChatPreferences.load();

		if (preferences) {
			if (nickname && preferences.nickname) {
				nickname.value = preferences.nickname;
			}

			if (age) {
				age.value = preferences.age || '';
			}

			if (city) {
				city.value = preferences.city || '';
			}

			if (sex) {
				sex.value = preferences.sex || 'U';
			}
		}
	}

	function resetNicknameStatus() {

		if (nickNotice) {
			nickNotice.hidden = true;
		}

		if (nickOk) {
			nickOk.hidden = true;
		}

		if (nickLoading) {
			nickLoading.hidden = true;
		}

		if (passwordInput) {
			passwordInput.required = false;
		}
		
		if (registeredOnlyNotice) {
			registeredOnlyNotice.hidden = true;
		}

	}

	function showFreeNickname() {

		if (nickOk) {
			nickOk.hidden = false;
		}

	}

	function showLoadingNickname() {

		resetNicknameStatus();

		if (nickLoading) {
			nickLoading.hidden = false;
		}

	}

	function showRegisteredNickname() {
	
		if (nickNotice) {
			nickNotice.hidden = true;
		}
	
		if (entryStep) {
			entryStep.hidden = true;
		}
	
		if (authStep) {
			authStep.hidden = false;
		}
	
		if (authNickname && nickname) {
			authNickname.textContent =
				nickname.value.trim();
		}
	
		if (passwordInput) {
			passwordInput.required = true;
			passwordInput.value = '';
			passwordInput.focus();
		}
	
	}
	
	if (authBack) {
	
		authBack.addEventListener(
			'click',
			function () {
	
				if (authStep) {
					authStep.hidden = true;
				}
	
				if (entryStep) {
					entryStep.hidden = false;
				}
	
				if (passwordInput) {
					passwordInput.value = '';
					passwordInput.required = false;
				}
	
				nicknameVerifiedForSubmit = false;
	
				if (nickname) {
					nickname.focus();
				}
	
			}
		);
	
	}
	
	form.addEventListener('submit', async function (event) {
	
		if (!nickname) {
			return;
		}
	
		if (
			nicknameVerifiedForSubmit
			&& authStep
			&& !authStep.hidden
		) {
			return;
		}
	
		const value = nickname.value.trim();
	
		if (value.length < 2) {
			return;
		}
	
		event.preventDefault();
	
		nicknameVerifiedForSubmit = false;
	
		resetNicknameStatus();
		showLoadingNickname();
	
		try {
	
			const response = await fetch(
				'/api/simosnap/nick/check?nickname='
					+ encodeURIComponent(value),
				{
					method: 'GET',
					headers: {
						'Accept': 'application/json'
					}
				}
			);
	
			if (!response.ok) {
	
				// Fail-open sui canali normali.
				form.submit();
				return;
			}
	
			const data = await response.json();
	
			if (nickLoading) {
				nickLoading.hidden = true;
			}
	
			const isRegistered =
				data
				&& (
					data.registered === true
					|| data.registered === 1
					|| data.registered === '1'
					|| data.registered === 'yes'
				);
	
			if (isRegistered) {
			
				showRegisteredNickname();
			
				nicknameVerifiedForSubmit = true;
			
				return;
			}
			
			if (registeredOnly) {
			
				resetNicknameStatus();
			
				if (registeredOnlyNotice) {
					registeredOnlyNotice.hidden = false;
				}
			
				if (nickname) {
					nickname.focus();
				}
			
				return;
			}
			
			showFreeNickname();
			
			form.submit();
	
		} catch (error) {
		
			resetNicknameStatus();
		
			// Fail-closed sui canali registered-only.
			if (registeredOnly) {
			
				if (registeredOnlyNotice) {
					registeredOnlyNotice.hidden = false;
			
					const message =
						registeredOnlyNotice.querySelector('span');
			
					if (message) {
						message.textContent =
							'Impossibile verificare il nickname. Riprova tra qualche istante.';
					}
				}
			
				return;
			}
		
			// Fail-open sui canali normali.
			form.submit();
		
		}
		
		});
});