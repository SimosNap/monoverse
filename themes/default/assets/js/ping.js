document.addEventListener('DOMContentLoaded', () => {

	const i18n = window.MonoversePingI18n || {
		upload: {
			processing: 'Elaborazione…',
			uploading: 'Caricamento…',
			failed: 'Caricamento non riuscito'
		},

		lightbox: {
			previousImage: 'Immagine precedente',
			nextImage: 'Immagine successiva'
		},

		audio: {
			pause: 'Pausa',
			play: 'Riproduci',
			unmute: 'Attiva audio',
			mute: 'Disattiva audio'
		}
	};

	const textarea = document.querySelector('.ping-composer-textarea');
	const counter = document.querySelector('.ping-character-counter');

	if (textarea) {

		function resizeTextarea(target) {
			target.style.height = 'auto';
			target.style.height = target.scrollHeight + 'px';
		}

		function updateCounter() {

			if (counter) {
				counter.textContent =
					`${textarea.value.length} / ${textarea.maxLength}`;
			}

		}

		function updateComposer() {
			resizeTextarea(textarea);
			updateCounter();
		}

		textarea.addEventListener('input', updateComposer);

		updateComposer();

	}

	const codeToggle =
		document.querySelector(
			'[data-ping-code-toggle]'
		);

	const codeComposer =
		document.querySelector(
			'[data-ping-code-composer]'
		);

	const codeTextarea =
		codeComposer?.querySelector(
			'textarea[name="code"]'
		);

	const codeCounter =
		codeComposer?.querySelector(
			'.ping-code-counter'
		);

	if (
		codeToggle &&
		codeComposer &&
		codeTextarea
	) {

		function updateCodeCounter() {

			if (codeCounter) {
				codeCounter.textContent =
					`${codeTextarea.value.length} / ${codeTextarea.maxLength}`;
			}

		}

		codeToggle.addEventListener(
			'click',
			() => {

				const opening =
					codeComposer.hidden;

				codeComposer.hidden =
					!opening;

				codeToggle.setAttribute(
					'aria-expanded',
					opening
						? 'true'
						: 'false'
				);

				if (opening) {
					codeTextarea.focus();
				}

			}
		);

		codeTextarea.addEventListener(
			'input',
			updateCodeCounter
		);

		updateCodeCounter();

	}

	const pongCodeToggle =
		document.querySelector(
			'[data-pong-code-toggle]'
		);

	const pongCodeComposer =
		document.querySelector(
			'[data-pong-code-composer]'
		);

	const pongCodeTextarea =
		pongCodeComposer?.querySelector(
			'textarea[name="code"]'
		);

	const pongCodeCounter =
		pongCodeComposer?.querySelector(
			'.ping-code-counter'
		);

	if (
		pongCodeToggle &&
		pongCodeComposer &&
		pongCodeTextarea
	) {

		function updatePongCodeCounter() {

			if (pongCodeCounter) {
				pongCodeCounter.textContent =
					`${pongCodeTextarea.value.length} / ${pongCodeTextarea.maxLength}`;
			}

		}

		pongCodeToggle.addEventListener(
			'click',
			() => {

				const opening =
					pongCodeComposer.hidden;

				pongCodeComposer.hidden =
					!opening;

				pongCodeToggle.setAttribute(
					'aria-expanded',
					opening
						? 'true'
						: 'false'
				);

				if (opening) {
					pongCodeTextarea.focus();
				}

			}
		);

		pongCodeTextarea.addEventListener(
			'input',
			updatePongCodeCounter
		);

		updatePongCodeCounter();

	}

	/*
	|--------------------------------------------------------------------------
	| Upload progress audio / video
	|--------------------------------------------------------------------------
	*/

	const uploadForm =
		document.querySelector(
			'form.ping-composer[enctype="multipart/form-data"]'
		);

	if (uploadForm) {

		const uploadInput =
			uploadForm.querySelector(
				'input[type="file"][name="media[]"]'
			);

		const uploadProgress =
			uploadForm.querySelector(
				'[data-upload-progress]'
			);

		const uploadStatus =
			uploadForm.querySelector(
				'[data-upload-status]'
			);

		const uploadPercent =
			uploadForm.querySelector(
				'[data-upload-percent]'
			);

		const uploadBytes =
			uploadForm.querySelector(
				'[data-upload-bytes]'
			);

		const uploadProgressBar =
			uploadForm.querySelector(
				'[data-upload-progressbar]'
			);

		const uploadProgressBarInner =
			uploadForm.querySelector(
				'[data-upload-progress-bar]'
			);

		const submitButton =
			uploadForm.querySelector(
				'.ping-composer-submit'
			);

		const audioMetadata =
			uploadForm.querySelector(
				'[data-audio-metadata]'
			);

		const mediaCloseButton =
			uploadForm.querySelector(
				'.ping-composer-media-close'
			);

		function resetAudioMetadata() {

			if (!audioMetadata) {
				return;
			}

			audioMetadata.hidden = true;

			audioMetadata
				.querySelectorAll(
					'input, textarea'
				)
				.forEach(
					(field) => {
						field.value = '';
					}
				);

		}

		function updateAudioMetadata() {

			if (!audioMetadata) {
				return;
			}

			const hasAudio =
				uploadInput?.files?.length
					? Array
						.from(uploadInput.files)
						.some(
							(file) =>
								file.type.startsWith(
									'audio/'
								)
						)
					: false;

			if (hasAudio) {
				audioMetadata.hidden = false;
				return;
			}

			resetAudioMetadata();

		}

		if (uploadInput) {

			uploadInput.addEventListener(
				'change',
				updateAudioMetadata
			);

		}

		if (mediaCloseButton) {

			mediaCloseButton.addEventListener(
				'click',
				() => {
					resetAudioMetadata();
				}
			);

		}

		let uploadInProgress = false;

		function formatUploadBytes(bytes) {

			if (
				!Number.isFinite(bytes) ||
				bytes <= 0
			) {
				return '0 MB';
			}

			const megabytes =
				bytes / (1024 * 1024);

			if (megabytes >= 1) {
				return `${megabytes.toFixed(1)} MB`;
			}

			const kilobytes =
				bytes / 1024;

			return `${kilobytes.toFixed(0)} KB`;

		}

		function hasLargeMediaUpload() {

			if (!uploadInput?.files?.length) {
				return false;
			}

			return Array
				.from(uploadInput.files)
				.some(
					(file) =>
						file.type.startsWith('audio/')
						|| file.type.startsWith('video/')
				);

		}

		function setUploadProgress(
			loaded,
			total
		) {

			const percentage =
				total > 0
					? Math.min(
						100,
						Math.max(
							0,
							Math.round(
								(loaded / total) * 100
							)
						)
					)
					: 0;

			if (uploadPercent) {
				uploadPercent.textContent =
					`${percentage}%`;
			}

			if (uploadBytes) {
				uploadBytes.textContent =
					`${formatUploadBytes(loaded)} / ${formatUploadBytes(total)}`;
			}

			if (uploadProgressBarInner) {
				uploadProgressBarInner.style.width =
					`${percentage}%`;
			}

			if (uploadProgressBar) {
				uploadProgressBar.setAttribute(
					'aria-valuenow',
					String(percentage)
				);
			}

		}

		function setProcessingState() {

			if (!uploadProgress) {
				return;
			}

			uploadProgress.classList.add(
				'is-processing'
			);

			if (uploadStatus) {
				uploadStatus.textContent =
					i18n.upload.processing;
			}

			if (uploadPercent) {
				uploadPercent.textContent =
					'100%';
			}

			if (uploadProgressBar) {
				uploadProgressBar.setAttribute(
					'aria-valuenow',
					'100'
				);
			}

		}

		function setUploadError() {

			if (!uploadProgress) {
				return;
			}

			uploadProgress.classList.remove(
				'is-processing'
			);

			if (uploadStatus) {
				uploadStatus.textContent =
					i18n.upload.failed;
			}

			if (submitButton) {
				submitButton.disabled = false;
			}

			uploadInProgress = false;

		}

		uploadForm.addEventListener(
			'submit',
			(event) => {

				// Immagini e PDF usano il submit HTML.
				if (!hasLargeMediaUpload()) {
					return;
				}

				event.preventDefault();

				if (uploadInProgress) {
					return;
				}

				uploadInProgress = true;

				if (submitButton) {
					submitButton.disabled = true;
				}

				if (uploadProgress) {

					uploadProgress.hidden = false;

					uploadProgress.classList.remove(
						'is-processing'
					);

				}

				if (uploadStatus) {
					uploadStatus.textContent =
						i18n.upload.uploading;
				}

				setUploadProgress(
					0,
					Array
						.from(
							uploadInput.files
						)
						.reduce(
							(total, file) =>
								total + file.size,
							0
						)
				);

				const formData =
					new FormData(uploadForm);

				const xhr =
					new XMLHttpRequest();

				xhr.open(
					uploadForm.method || 'POST',
					uploadForm.action || '/ping',
					true
				);

				xhr.setRequestHeader(
					'X-Requested-With',
					'XMLHttpRequest'
				);

				xhr.upload.addEventListener(
					'progress',
					(progressEvent) => {

						if (!progressEvent.lengthComputable) {
							return;
						}

						setUploadProgress(
							progressEvent.loaded,
							progressEvent.total
						);

					}
				);

				xhr.upload.addEventListener(
					'load',
					() => {

						// Upload completato, passa all'elaborazione server.
						setProcessingState();

					}
				);

				xhr.addEventListener(
					'load',
					() => {

						if (
							xhr.status >= 200 &&
							xhr.status < 400
						) {

							window.location.href =
								'/ping';

							return;

						}

						setUploadError();

						if (uploadStatus) {
							uploadStatus.textContent =
								xhr.responseText.trim() !== ''
									? xhr.responseText.trim()
									: i18n.upload.failed;
						}

						console.error(
							'Errore upload Ping:',
							`HTTP ${xhr.status}`,
							xhr.responseText
						);

					}
				);

				xhr.addEventListener(
					'error',
					() => {

						setUploadError();

						console.error(
							'Errore di rete durante upload Ping.'
						);

					}
				);

				xhr.addEventListener(
					'timeout',
					() => {

						setUploadError();

						console.error(
							'Timeout durante upload Ping.'
						);

					}
				);

				xhr.addEventListener(
					'abort',
					() => {

						setUploadError();

						console.error(
							'Upload Ping interrotto.'
						);

					}
				);

				xhr.send(formData);

			}
		);

	}

	const lightbox = document.createElement('div');

	lightbox.className = 'ping-lightbox';

	lightbox.innerHTML = `
		<div class="ping-lightbox-backdrop"></div>

		<button
			type="button"
			class="ping-lightbox-arrow ping-lightbox-arrow-prev"
			aria-label="${i18n.lightbox.previousImage}"
		>
			&#10094;
		</button>

		<div class="ping-lightbox-content">

			<img
				class="ping-lightbox-image"
				src=""
				alt=""
			>

			<div class="ping-lightbox-thumbnails"></div>

		</div>

		<button
			type="button"
			class="ping-lightbox-arrow ping-lightbox-arrow-next"
			aria-label="${i18n.lightbox.nextImage}"
		>
			&#10095;
		</button>
	`;

	document.body.appendChild(lightbox);

	const lightboxImage = lightbox.querySelector('.ping-lightbox-image');
	const lightboxContent = lightbox.querySelector('.ping-lightbox-content');
	const thumbnailsContainer = lightbox.querySelector('.ping-lightbox-thumbnails');
	const previousButton = lightbox.querySelector('.ping-lightbox-arrow-prev');
	const nextButton = lightbox.querySelector('.ping-lightbox-arrow-next');

	let currentImages = [];
	let currentIndex = 0;

	let pointerStartX = 0;
	let pointerStartY = 0;
	let pointerDeltaX = 0;
	let pointerActive = false;

	function showLightboxImage(index) {

		currentIndex = index;

		lightboxImage.classList.add('is-loading');

		const image = new Image();

		image.onload = () => {

			lightboxImage.src = image.src;

			requestAnimationFrame(() => {
				lightboxImage.classList.remove('is-loading');
			});

		};

		image.src = currentImages[currentIndex];

		updateActiveThumbnail();

		preloadAdjacentImages();

	}

	function buildThumbnails() {

		thumbnailsContainer.innerHTML = '';

		currentImages.forEach((src, i) => {

			const thumb = document.createElement('img');

			thumb.src = src;
			thumb.className = 'ping-lightbox-thumbnail';

			thumb.addEventListener('click', (event) => {

				event.stopPropagation();

				showLightboxImage(i);

			});

			thumbnailsContainer.appendChild(thumb);

		});

	}

	function updateActiveThumbnail() {

		thumbnailsContainer
			.querySelectorAll('.ping-lightbox-thumbnail')
			.forEach((thumb, i) => {

				const active = (i === currentIndex);

				thumb.classList.toggle('is-active', active);

				if (active) {

					thumb.scrollIntoView({
						behavior: 'smooth',
						inline: 'center',
						block: 'nearest'
					});

				}

			});

	}

	function preloadAdjacentImages() {

		if (currentImages.length <= 1) {
			return;
		}

		const previous =
			(currentIndex - 1 + currentImages.length) % currentImages.length;

		const next =
			(currentIndex + 1) % currentImages.length;

		new Set([previous, next]).forEach(index => {

			const image = new Image();

			image.src = currentImages[index];

		});

	}

	function openLightbox(images, index) {

		currentImages = images;

		const multiple = currentImages.length > 1;

		previousButton.style.display = multiple ? '' : 'none';
		nextButton.style.display = multiple ? '' : 'none';
		thumbnailsContainer.style.display = multiple ? 'flex' : 'none';

		buildThumbnails();

		showLightboxImage(index);

		lightbox.classList.add('is-open');

		document.body.style.overflow = 'hidden';

	}

	function closeLightbox() {

		lightbox.classList.remove('is-open');

		lightboxImage.removeAttribute('src');

		currentImages = [];
		currentIndex = 0;

		document.body.style.overflow = '';

	}

	function showPreviousImage() {

		if (currentImages.length <= 1) {
			return;
		}

		showLightboxImage(
			(currentIndex - 1 + currentImages.length) % currentImages.length
		);

	}

	function showNextImage() {

		if (currentImages.length <= 1) {
			return;
		}

		showLightboxImage(
			(currentIndex + 1) % currentImages.length
		);

	}

	previousButton.addEventListener('click', (event) => {

		event.stopPropagation();

		showPreviousImage();

	});

	nextButton.addEventListener('click', (event) => {

		event.stopPropagation();

		showNextImage();

	});

	lightbox.addEventListener('pointerdown', (event) => {

		if (
			!lightbox.classList.contains('is-open') ||
			event.target !== lightboxImage
		) {
			return;
		}

		pointerActive = true;

		pointerStartX = event.clientX;
		pointerStartY = event.clientY;

		lightbox.setPointerCapture(event.pointerId);

	});

	lightbox.addEventListener('pointerup', (event) => {

		if (!pointerActive) {
			return;
		}

		pointerActive = false;
		lightbox.releasePointerCapture(event.pointerId);

		const deltaX = event.clientX - pointerStartX;
		const deltaY = event.clientY - pointerStartY;

		pointerDeltaX = 0;

		if (Math.abs(deltaX) < 60) {

			lightboxContent.style.transition = 'transform .18s ease';

			lightboxContent.style.transform = 'translate3d(0, 0, 0)';

			requestAnimationFrame(() => {

				requestAnimationFrame(() => {

					lightboxContent.style.transition = '';

				});

			});

		}

		if (Math.abs(deltaY) > Math.abs(deltaX)) {
			return;
		}

		if (deltaX > 60) {

			showPreviousImage();

		} else if (deltaX < -60) {

			showNextImage();

		}

	});

	lightbox.addEventListener('pointercancel', () => {

		pointerActive = false;

	});

	document.addEventListener('click', (event) => {

		const link = event.target.closest('.ping-image-link');

		if (!link) {
			return;
		}

		event.preventDefault();

		const post = link.closest('.ping-card');

		const images = Array.from(
			post.querySelectorAll('.ping-image-link')
		).map(image => image.dataset.full);

		openLightbox(
			images,
			Number(link.dataset.index)
		);

	});

	lightbox.addEventListener('click', (event) => {

		if (event.target.closest('.ping-lightbox-content')) {
			return;
		}

		closeLightbox();

	});

	document.addEventListener('keydown', (event) => {

		if (
			event.key === 'Escape' &&
			lightbox.classList.contains('is-open')
		) {

			closeLightbox();

		}

		if (
			event.key === 'ArrowLeft' &&
			lightbox.classList.contains('is-open')
		) {

			showPreviousImage();

		}

		if (
			event.key === 'ArrowRight' &&
			lightbox.classList.contains('is-open')
		) {

			showNextImage();

		}

	});

	lightbox.addEventListener('pointermove', (event) => {

		if (!pointerActive) {
			return;
		}
		pointerDeltaX = event.clientX - pointerStartX;
		lightboxContent.style.transform =
			`translate3d(${pointerDeltaX * 0.35}px, 0, 0)`;

	});

	document.addEventListener('click', (event) => {

		const editButton = event.target.closest('.ping-edit-button');

		if (editButton) {

			const card = editButton.closest('.ping-card');
			const content = card.querySelector('[data-content]');
			const editor = card.querySelector('.ping-editor');
			const textarea = editor?.querySelector('textarea');

			if (!content || !editor) {
				return;
			}

			content.style.display = 'none';
			editor.style.display = '';
			editButton.style.display = 'none';

			if (textarea) {
				resizeTextarea(textarea);
				textarea.focus();
				textarea.setSelectionRange(
					textarea.value.length,
					textarea.value.length
				);
			}

			return;
		}

		const pongEditButton = event.target.closest('.pong-edit-button');

		if (pongEditButton) {

			const card = pongEditButton.closest('.pong-card');
			const content = card.querySelector('[data-content]');
			const editor = card.querySelector('.pong-editor');
			const textarea = editor?.querySelector('textarea');

			if (!content || !editor) {
				return;
			}

			content.style.display = 'none';
			editor.style.display = '';

			pongEditButton.style.display = 'none';

			if (textarea) {
				resizeTextarea(textarea);
				textarea.focus();
				textarea.setSelectionRange(
					textarea.value.length,
					textarea.value.length
				);
			}

			return;
		}

		const pongCancelButton = event.target.closest('.pong-edit-cancel');

		if (pongCancelButton) {

			const card = pongCancelButton.closest('.pong-card');
			const content = card.querySelector('[data-content]');
			const editor = card.querySelector('.pong-editor');
			const editButton = card.querySelector('.pong-edit-button');

			if (!content || !editor) {
				return;
			}

			editor.style.display = 'none';
			content.style.display = '';

			if (editButton) {
				editButton.style.display = '';
			}

			return;
		}

		const cancelButton = event.target.closest('.ping-edit-cancel');

		if (cancelButton) {

			const card = cancelButton.closest('.ping-card');
			const content = card.querySelector('[data-content]');
			const editor = card.querySelector('.ping-editor');

			if (!content || !editor) {
				return;
			}

			editor.style.display = 'none';
			content.style.display = '';
			const editButton = card.querySelector('.ping-edit-button');

			if (editButton) {
				editButton.style.display = '';
			}
		}

	});

	document.addEventListener('keydown', (event) => {

		const textarea = event.target.closest(
			'.ping-editor textarea, .pong-editor textarea'
		);

		if (!textarea) {
			return;
		}

		if (
			event.key === 'Enter' &&
			(event.ctrlKey || event.metaKey)
		) {
			event.preventDefault();
			textarea.form.submit();
		}

	});

	document.addEventListener('keydown', (event) => {

		const textarea = event.target.closest(
			'.ping-editor textarea, .pong-editor textarea'
		);

		if (!textarea || event.key !== 'Escape') {
			return;
		}

		event.preventDefault();

		const card =
			textarea.closest('.ping-card') ??
			textarea.closest('.pong-card');

		const content = card.querySelector('[data-content]');

		const editor =
			card.querySelector('.ping-editor') ??
			card.querySelector('.pong-editor');

		const editButton =
			card.querySelector('.ping-edit-button') ??
			card.querySelector('.pong-edit-button');

		editor.style.display = 'none';
		content.style.display = '';

		if (editButton) {
			editButton.style.display = '';
		}

	});

	document.addEventListener('input', (event) => {

		if (
			!event.target.matches(
				'.ping-editor textarea, .pong-editor textarea'
			)
		) {
			return;
		}

		resizeTextarea(event.target);

	});

	function updateEditTimers() {

		const now = Math.floor(Date.now() / 1000);

		document
			.querySelectorAll('[data-edit-expires-at]')
			.forEach((timer) => {

				const expiresAt = Number(timer.dataset.editExpiresAt);
				const remaining = expiresAt - now;

				if (!expiresAt || remaining <= 0) {

					const card =
						timer.closest('.ping-card') ??
						timer.closest('.pong-card');

					const editButton =
						card?.querySelector('.ping-edit-button') ??
						card?.querySelector('.pong-edit-button');

					const editor =
						card?.querySelector('.ping-editor') ??
						card?.querySelector('.pong-editor');

					const content = card?.querySelector('[data-content]');

					editButton?.remove();
					timer.remove();

					if (editor && content) {
						editor.style.display = 'none';
						content.style.display = '';
					}

					return;
				}

				const minutes = Math.floor(remaining / 60);
				const seconds = remaining % 60;

				timer.classList.remove('warning', 'danger');

				if (remaining <= 60) {
					timer.classList.add('danger');
				} else if (remaining <= 300) {
					timer.classList.add('warning');
				}

				timer.textContent =
					`✏️ ${minutes}:${String(seconds).padStart(2, '0')}`;

			});

	}

	updateEditTimers();

	setInterval(updateEditTimers, 1000);

	/*
	|--------------------------------------------------------------------------
	| Report modal
	|--------------------------------------------------------------------------
	*/

	const modal = document.getElementById('ping-report-modal');

	if (modal) {

		const form = document.getElementById('ping-report-form');
		const targetType = document.getElementById('report-target-type');
		const targetUuid = document.getElementById('report-target-uuid');

		const descriptionGroup =
			document.getElementById('report-description-group');

		const description =
			document.getElementById('report-description');

		function closeModal() {

			modal.hidden = true;

			document.body.classList.remove('mv-modal-open');

			form.reset();

			descriptionGroup.hidden = true;

		}

		document.addEventListener('click', event => {

			const button = event.target.closest('.js-open-report-modal');

			if (!button) {
				return;
			}

			targetType.value = button.dataset.reportType;
			targetUuid.value = button.dataset.reportUuid;

			modal.hidden = false;
			document.body.classList.add('mv-modal-open');

		});

		form.querySelectorAll('input[name="reason"]').forEach(radio => {

			radio.addEventListener('change', () => {

				const show =
					radio.checked &&
					radio.value === 'other';

				descriptionGroup.hidden = !show;

				if (show) {
					description.focus();
				}

			});

		});

		modal.querySelector('.mv-modal-backdrop')
			.addEventListener('click', closeModal);

		modal.querySelector('.mv-modal-close')
			.addEventListener('click', closeModal);

		modal.querySelector('.mv-modal-cancel')
			.addEventListener('click', closeModal);

		document.addEventListener('keydown', event => {

			if (
				event.key === 'Escape' &&
				!modal.hidden
			) {
				closeModal();
			}

		});

	}

	/*
	|--------------------------------------------------------------------------
	| Menu altre azioni Ping
	|--------------------------------------------------------------------------
	*/

	document.addEventListener('click', (event) => {

		document
			.querySelectorAll('.ping-more-menu[open]')
			.forEach((menu) => {

				if (!menu.contains(event.target)) {
					menu.removeAttribute('open');
				}

			});

	});

	document.addEventListener('keydown', (event) => {

		if (event.key !== 'Escape') {
			return;
		}

		document
			.querySelectorAll('.ping-more-menu[open]')
			.forEach((menu) => {
				menu.removeAttribute('open');
			});

	});

	/*
	|--------------------------------------------------------------------------
	| Audio player
	|--------------------------------------------------------------------------
	*/

	const initializedAudioPlayers = new WeakSet();

	function formatAudioTime(seconds) {

		if (
			!Number.isFinite(seconds) ||
			seconds < 0
		) {
			return '0:00';
		}

		const minutes =
			Math.floor(seconds / 60);

		const remainingSeconds =
			Math.floor(seconds % 60);

		return `${minutes}:${String(remainingSeconds).padStart(2, '0')}`;

	}

	function initializeAudioPlayer(player) {

		if (
			initializedAudioPlayers.has(player)
		) {
			return;
		}

		const audio =
			player.querySelector(
				'.ping-audio-engine'
			);

		const playButton =
			player.querySelector(
				'[data-audio-play]'
			);

		const waveform =
			player.querySelector(
				'[data-audio-waveform]'
			);

		const progress =
			player.querySelector(
				'[data-audio-progress]'
			);

		const currentTime =
			player.querySelector(
				'[data-audio-current]'
			);

		const duration =
			player.querySelector(
				'[data-audio-duration]'
			);

		const volumeButton =
			player.querySelector(
				'[data-audio-volume]'
			);

		const volumeRange =
			player.querySelector(
				'[data-audio-volume-range]'
			);

		if (
			!audio ||
			!playButton ||
			!waveform ||
			!progress
		) {
			return;
		}

		initializedAudioPlayers.add(
			player
		);

		function updatePlayButton() {

			const icon =
				playButton.querySelector('i');

			const playing =
				!audio.paused &&
				!audio.ended;

			playButton.classList.toggle(
				'is-playing',
				playing
			);

			playButton.setAttribute(
				'aria-label',
				playing
					? i18n.audio.pause
					: i18n.audio.play
			);

			if (icon) {
				icon.className =
					playing
						? 'fa-solid fa-pause'
						: 'fa-solid fa-play';
			}

		}

		function updateProgress() {

			const audioDuration =
				audio.duration;

			const percentage =
				Number.isFinite(audioDuration) &&
				audioDuration > 0
					? (
						audio.currentTime /
						audioDuration
					) * 100
					: 0;

			progress.style.width =
				`${Math.min(
					100,
					Math.max(
						0,
						percentage
					)
				)}%`;

			if (currentTime) {
				currentTime.textContent =
					formatAudioTime(
						audio.currentTime
					);
			}

		}

		function updateDuration() {

			if (duration) {
				duration.textContent =
					formatAudioTime(
						audio.duration
					);
			}

			updateProgress();

		}

		function updateVolumeIcon() {

			if (!volumeButton) {
				return;
			}

			const icon =
				volumeButton.querySelector('i');

			if (!icon) {
				return;
			}

			if (
				audio.muted ||
				audio.volume === 0
			) {

				icon.className =
					'fa-solid fa-volume-xmark';

				volumeButton.setAttribute(
					'aria-label',
					i18n.audio.unmute
				);

			} else if (audio.volume < .5) {

				icon.className =
					'fa-solid fa-volume-low';

				volumeButton.setAttribute(
					'aria-label',
					i18n.audio.mute
				);

			} else {

				icon.className =
					'fa-solid fa-volume-high';

				volumeButton.setAttribute(
					'aria-label',
					i18n.audio.mute
				);

			}

		}

		playButton.addEventListener(
			'click',
			async () => {

				if (audio.paused) {

					document
						.querySelectorAll(
							'.ping-audio-engine'
						)
						.forEach(
							otherAudio => {

								if (
									otherAudio !== audio &&
									!otherAudio.paused
								) {
									otherAudio.pause();
								}

							}
						);

					try {

						await audio.play();

					} catch (error) {

						console.error(
							'Errore riproduzione audio:',
							error
						);

					}

				} else {

					audio.pause();

				}

			}
		);

		waveform.addEventListener(
			'click',
			(event) => {

				if (
					!Number.isFinite(
						audio.duration
					) ||
					audio.duration <= 0
				) {
					return;
				}

				const rect =
					waveform.getBoundingClientRect();

				const ratio =
					Math.min(
						1,
						Math.max(
							0,
							(
								event.clientX -
								rect.left
							) /
							rect.width
						)
					);

				audio.currentTime =
					ratio * audio.duration;

				updateProgress();

			}
		);

		if (volumeRange) {

			volumeRange.addEventListener(
				'input',
				() => {

					audio.volume =
						Math.min(
							1,
							Math.max(
								0,
								Number(
									volumeRange.value
								)
							)
						);

					audio.muted = false;

					updateVolumeIcon();

				}
			);

		}

		if (volumeButton) {

			volumeButton.addEventListener(
				'click',
				() => {

					audio.muted =
						!audio.muted;

					updateVolumeIcon();

				}
			);

		}

		audio.addEventListener(
			'loadedmetadata',
			updateDuration
		);

		audio.addEventListener(
			'durationchange',
			updateDuration
		);

		audio.addEventListener(
			'timeupdate',
			updateProgress
		);

		audio.addEventListener(
			'play',
			updatePlayButton
		);

		audio.addEventListener(
			'pause',
			updatePlayButton
		);

		audio.addEventListener(
			'ended',
			() => {

				updatePlayButton();
				updateProgress();

			}
		);

		audio.addEventListener(
			'volumechange',
			updateVolumeIcon
		);

		updatePlayButton();
		updateVolumeIcon();
		updateDuration();

	}

	function initializeAudioPlayers(root = document) {

		root
			.querySelectorAll(
				'[data-audio-player]'
			)
			.forEach(
				initializeAudioPlayer
			);

	}

	initializeAudioPlayers();

	const audioPlayerObserver =
		new MutationObserver(
			(mutations) => {

				mutations.forEach(
					(mutation) => {

						mutation.addedNodes.forEach(
							(node) => {

								if (
									!(
										node instanceof Element
									)
								) {
									return;
								}

								if (
									node.matches(
										'[data-audio-player]'
									)
								) {
									initializeAudioPlayer(
										node
									);
								}

								initializeAudioPlayers(
									node
								);

							}
						);

					}
				);

			}
		);

	audioPlayerObserver.observe(
		document.body,
		{
			childList: true,
			subtree: true
		}
	);



	/*
	|--------------------------------------------------------------------------
	| Infinite scroll Ping
	|--------------------------------------------------------------------------
	*/

	const infiniteList =
		document.getElementById('ping-infinite-list');

	const infiniteTrigger =
		document.getElementById('ping-infinite-trigger');

	if (
		infiniteList &&
		infiniteTrigger
	) {

		let loading = false;

		const pageSize = Number(
			infiniteList.dataset.pageSize
				?? 20
		);

		let nextOffset = Number(
			infiniteList.dataset.nextOffset
				?? pageSize
		);

		const feed =
			infiniteList.dataset.feed
				?? 'all';

		const query =
			new URLSearchParams(
				window.location.search
			).get('q') ?? '';

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
						`/ping/load?offset=${encodeURIComponent(nextOffset)}&feed=${encodeURIComponent(feed)}&q=${encodeURIComponent(query)}`,
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

					const html = await response.text();

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
						'Errore caricamento Ping:',
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

	/* =========================================================
	   Infinite scroll Ping profilo
	   ========================================================= */

	const profilePingList = document.getElementById(
		'profile-ping-infinite-list'
	);

	const profilePingTrigger = document.getElementById(
		'profile-ping-infinite-trigger'
	);

	if (profilePingList && profilePingTrigger) {
		let profilePingLoading = false;
		let profilePingFinished = false;

		let profilePingOffset = Number.parseInt(
			profilePingList.dataset.nextOffset || '0',
			10
		);

		const profilePingPageSize = Number.parseInt(
			profilePingList.dataset.pageSize || '20',
			10
		);

		const profilePingUsername = (
			profilePingList.dataset.username || ''
		).trim();

		const profilePingFeed = (
			profilePingList.dataset.feed || 'all'
		).trim();

		const profilePingObserver = new IntersectionObserver(
			async (entries) => {
				const entry = entries[0];

				if (
					!entry?.isIntersecting
					|| profilePingLoading
					|| profilePingFinished
					|| profilePingUsername === ''
				) {
					return;
				}

				profilePingLoading = true;

				try {
					const params = new URLSearchParams({
						offset: String(profilePingOffset),
						feed: profilePingFeed,
					});

					const response = await fetch(
						'/profile/'
						+ encodeURIComponent(profilePingUsername)
						+ '/load?'
						+ params.toString(),
						{
							headers: {
								'X-Requested-With': 'XMLHttpRequest',
							},
						}
					);

					if (!response.ok) {
						throw new Error(
							'Profile Ping load failed'
						);
					}

					const html = await response.text();

					if (html.trim() === '') {
						profilePingFinished = true;
						profilePingObserver.disconnect();
						return;
					}

					const template = document.createElement(
						'template'
					);

					template.innerHTML = html.trim();

					const loadedPosts = template.content.children.length;

					profilePingList.append(
						template.content
					);

					profilePingOffset += loadedPosts;

					profilePingList.dataset.nextOffset = String(
						profilePingOffset
					);

					if (
						loadedPosts === 0
						|| loadedPosts < profilePingPageSize
					) {
						profilePingFinished = true;
						profilePingObserver.disconnect();
					}
				} catch (error) {
					console.error(
						'Unable to load profile Pings:',
						error
					);
				} finally {
					profilePingLoading = false;
				}
			},
			{
				rootMargin: '300px 0px',
			}
		);

		profilePingObserver.observe(
			profilePingTrigger
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Infinite scroll Pong
	|--------------------------------------------------------------------------
	*/

	const pongInfiniteList =
		document.getElementById('pong-infinite-list');

	const pongInfiniteTrigger =
		document.getElementById('pong-infinite-trigger');

	if (
		pongInfiniteList &&
		pongInfiniteTrigger
	) {

		let loadingPongs = false;

		const pongPageSize = Number(
			pongInfiniteList.dataset.pageSize
				?? 5
		);

		let pongNextOffset = Number(
			pongInfiniteList.dataset.nextOffset
				?? pongPageSize
		);

		const postUuid =
			pongInfiniteList.dataset.postUuid
				?? '';

		if (postUuid !== '') {

			const pongObserver = new IntersectionObserver(
				async (entries) => {

					const entry = entries[0];

					if (
						!entry?.isIntersecting ||
						loadingPongs
					) {
						return;
					}

					loadingPongs = true;

					pongObserver.unobserve(
						pongInfiniteTrigger
					);

					try {

						const response = await fetch(
							`/ping/${encodeURIComponent(postUuid)}/comments/load?offset=${encodeURIComponent(pongNextOffset)}`,
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

						const loadedPongs =
							template.content.querySelectorAll(
								'.pong-card'
							).length;

						if (loadedPongs === 0) {

							pongObserver.disconnect();
							pongInfiniteTrigger.remove();

							return;
						}

						pongInfiniteList.appendChild(
							template.content
						);

						pongNextOffset += loadedPongs;

						pongInfiniteList.dataset.nextOffset =
							String(pongNextOffset);

						if (loadedPongs < pongPageSize) {

							pongObserver.disconnect();
							pongInfiniteTrigger.remove();

							return;
						}

						pongObserver.observe(
							pongInfiniteTrigger
						);

					} catch (error) {

						console.error(
							'Errore caricamento Pong:',
							error
						);

						pongObserver.observe(
							pongInfiniteTrigger
						);

					} finally {

						loadingPongs = false;

					}

				},
				{
					root: null,
					rootMargin: '600px 0px',
					threshold: 0
				}
			);

			pongObserver.observe(
				pongInfiniteTrigger
			);

		}

	}

});



