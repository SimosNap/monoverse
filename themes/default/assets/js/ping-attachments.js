document.addEventListener('DOMContentLoaded', () => {

	const i18n = (
		window.MonoversePingI18n
		&& window.MonoversePingI18n.attachments
	)
		? window.MonoversePingI18n.attachments
		: {
			label: 'Allegati',
			one: '1 allegato',
			count: ':count allegati',
			remove: 'Rimuovi :file',
			audioNotAllowed:
				'Il caricamento di allegati audio non è consentito.',
			videoNotAllowed:
				'Il caricamento di allegati video non è consentito.',
			audioTooLarge:
				'Il file audio supera il limite di :max MB.',
			videoTooLarge:
				'Il file video supera il limite di :max MB.'
		};

	const replace = (text, values = {}) => {

		let result = String(text);

		Object.entries(values).forEach(
			([key, value]) => {

				result = result.replaceAll(
					`:${key}`,
					String(value)
				);

			}
		);

		return result;
	};

	const mediaInput = document.querySelector(
		'.ping-composer-media input[type="file"]'
	);

	const preview = document.querySelector('.ping-media-preview');
	const mediaArea = document.querySelector('.ping-composer-media');
	const mediaToggle = document.querySelector('.ping-composer-attachments-toggle');
	const mediaClose = document.querySelector('.ping-composer-media-close');
	const mediaError =
		mediaArea?.querySelector('[data-media-error]');

	const audioUploadEnabled =
		mediaArea?.dataset.audioUploadEnabled === '1';

	const audioMaxMb =
		Number.parseInt(
			mediaArea?.dataset.audioMaxMb || '50',
			10
		);

	const videoUploadEnabled =
		mediaArea?.dataset.videoUploadEnabled === '1';

	const videoMaxMb =
		Number.parseInt(
			mediaArea?.dataset.videoMaxMb || '50',
			10
		);

	if (!mediaInput || !preview || !mediaArea || !mediaToggle || !mediaClose) {
		return;
	}

	['dragenter', 'dragover'].forEach(eventName => {

		mediaArea.addEventListener(eventName, event => {

			event.preventDefault();
			event.stopPropagation();

			mediaArea.classList.add('dragover');

		});

	});

	['dragleave', 'dragend'].forEach(eventName => {

		mediaArea.addEventListener(eventName, event => {

			event.preventDefault();
			event.stopPropagation();

			mediaArea.classList.remove('dragover');

		});

	});

	mediaArea.addEventListener('drop', event => {

		event.preventDefault();
		event.stopPropagation();

		mediaArea.classList.remove('dragover');

		const dataTransfer = new DataTransfer();
		let errorMessage = '';

		Array.from(mediaInput.files).forEach(file => {
			dataTransfer.items.add(file);
		});

		Array.from(event.dataTransfer.files).forEach(file => {

			const error = validateFile(file);

			if (error !== '') {

				if (errorMessage === '') {
					errorMessage = error;
				}

				return;
			}

			dataTransfer.items.add(file);

		});

		mediaInput.files = dataTransfer.files;

		showMediaError(errorMessage);

		renderPreviews();
		updateAttachmentButton();

	});

	mediaToggle.addEventListener('click', () => {

		const isHidden = mediaArea.hidden;

		mediaArea.hidden = !isHidden;

		mediaToggle.setAttribute('aria-expanded', String(isHidden));

		if (isHidden) {
			mediaInput.focus();
		}

	});

	mediaClose.addEventListener('click', () => {

		mediaArea.hidden = true;

		mediaToggle.setAttribute('aria-expanded', 'false');

	});

	mediaInput.addEventListener('change', () => {

		const dataTransfer = new DataTransfer();
		let errorMessage = '';

		Array.from(mediaInput.files).forEach(file => {

			const error = validateFile(file);

			if (error !== '') {

				if (errorMessage === '') {
					errorMessage = error;
				}

				return;
			}

			dataTransfer.items.add(file);

		});

		mediaInput.files = dataTransfer.files;

		showMediaError(errorMessage);

		renderPreviews();
		updateAttachmentButton();

	});

	function showMediaError(message) {

		if (!mediaError) {
			return;
		}

		if (message === '') {
			mediaError.textContent = '';
			mediaError.hidden = true;
			return;
		}

		mediaError.textContent = message;
		mediaError.hidden = false;

	}

	function validateFile(file) {

		if (
			file.type.startsWith('audio/')
			&& !audioUploadEnabled
		) {
			return i18n.audioNotAllowed;
		}

		if (
			file.type.startsWith('video/')
			&& !videoUploadEnabled
		) {
			return i18n.videoNotAllowed;
		}

		if (
			file.type.startsWith('audio/')
			&& file.size > audioMaxMb * 1024 * 1024
		) {
			return replace(
				i18n.audioTooLarge,
				{
					max: audioMaxMb
				}
			);
		}

		if (
			file.type.startsWith('video/')
			&& file.size > videoMaxMb * 1024 * 1024
		) {
			return replace(
				i18n.videoTooLarge,
				{
					max: videoMaxMb
				}
			);
		}

		return '';
	}

	function updateAttachmentButton() {

		const count = mediaInput.files.length;

		let label = i18n.label;

		if (count === 1) {
			label = i18n.one;
		} else if (count > 1) {
			label = replace(
				i18n.count,
				{
					count
				}
			);
		}

		mediaToggle.setAttribute(
			'title',
			label
		);

		mediaToggle.setAttribute(
			'aria-label',
			label
		);

		mediaToggle.innerHTML =
			'<i class="fa-solid fa-upload" aria-hidden="true"></i>';

	}

	function renderPreviews() {

		preview.innerHTML = '';

		Array.from(mediaInput.files).forEach((file, index) => {

			const item = document.createElement('div');
			item.className = 'ping-media-preview-item';

			const remove = document.createElement('button');
			remove.type = 'button';
			remove.className = 'ping-media-preview-remove';
			remove.innerHTML = '&times;';

			remove.setAttribute(
				'aria-label',
				replace(
					i18n.remove,
					{
						file: file.name
					}
				)
			);

			remove.addEventListener('click', () => {

				const dataTransfer = new DataTransfer();

				Array.from(mediaInput.files).forEach((currentFile, currentIndex) => {

					if (currentIndex !== index) {
						dataTransfer.items.add(currentFile);
					}

				});

				mediaInput.files = dataTransfer.files;

				renderPreviews();
				updateAttachmentButton();

			});

			item.appendChild(remove);

			if (file.type.startsWith('image/')) {

				const image = document.createElement('img');
				image.src = URL.createObjectURL(file);
				image.alt = file.name;

				item.appendChild(image);

			} else {

				const wrapper = document.createElement('div');
				wrapper.className = 'ping-media-preview-item-file';

				const icon = document.createElement('div');
				icon.className = 'ping-media-preview-icon';

				if (file.type.startsWith('video/')) {
					icon.textContent = '🎥';
				} else if (file.type.startsWith('audio/')) {
					icon.textContent = '🎵';
				} else {
					icon.textContent = '📄';
				}

				const name = document.createElement('div');
				name.className = 'ping-media-preview-name';
				name.textContent = file.name;

				wrapper.appendChild(icon);
				wrapper.appendChild(name);

				item.appendChild(wrapper);

			}

			preview.appendChild(item);

		});

	}

	updateAttachmentButton();

});
