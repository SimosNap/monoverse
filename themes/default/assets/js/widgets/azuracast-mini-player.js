document.addEventListener('DOMContentLoaded', () => {
	document
		.querySelectorAll('.mv-azuracast-mini-player')
		.forEach((player) => {
			const audio = player.querySelector(
				'.mv-azuracast-mini-audio'
			);

			const playButton = player.querySelector(
				'.mv-azuracast-mini-play'
			);

			const detachButton = player.querySelector(
				'.mv-azuracast-mini-detach'
			);

			const status = player.querySelector(
				'.mv-azuracast-mini-status'
			);

			if (
				!(audio instanceof HTMLAudioElement)
				|| !(playButton instanceof HTMLButtonElement)
			) {
				return;
			}

			const i18n = {
				play:
					player.dataset.i18nPlay
						|| 'Avvia la radio',

				pause:
					player.dataset.i18nPause
						|| 'Metti in pausa la radio',

				playing:
					player.dataset.i18nPlaying
						|| 'In riproduzione',

				paused:
					player.dataset.i18nPaused
						|| 'In pausa',

				unavailable:
					player.dataset.i18nUnavailable
						|| 'Riproduzione non disponibile',

				error:
					player.dataset.i18nError
						|| 'Errore durante la riproduzione',

				ready:
					player.dataset.i18nReady
						|| 'Pronto'
			};

			const playIcon = playButton.querySelector('i');

			const setPlayingState = (isPlaying) => {
				playButton.setAttribute(
					'aria-pressed',
					isPlaying ? 'true' : 'false'
				);

				playButton.setAttribute(
					'aria-label',
					isPlaying
						? i18n.pause
						: i18n.play
				);

				if (playIcon) {
					playIcon.className = isPlaying
						? 'fa-solid fa-pause'
						: 'fa-solid fa-play';
				}

				if (status) {
					status.textContent = isPlaying
						? i18n.playing
						: i18n.paused;
				}
			};

			playButton.addEventListener('click', async () => {
				if (audio.paused) {
					try {
						await audio.play();
					} catch {
						if (status) {
							status.textContent =
								i18n.unavailable;
						}
					}

					return;
				}

				audio.pause();
			});

			audio.addEventListener('play', () => {
				setPlayingState(true);
			});

			audio.addEventListener('pause', () => {
				setPlayingState(false);
			});

			audio.addEventListener('error', () => {
				setPlayingState(false);

				if (status) {
					status.textContent =
						i18n.error;
				}
			});

			if (
				detachButton instanceof HTMLButtonElement
			) {
				detachButton.addEventListener('click', () => {
					const source = audio.querySelector('source');
					const streamUrl = source?.src ?? '';

					if (streamUrl === '') {
						return;
					}

					const detachedWindow = window.open(
						streamUrl,
						'monoverse-radio-player',
						'width=420,height=180,resizable=yes'
					);

					if (!detachedWindow) {
						return;
					}

					audio.pause();
					playButton.disabled = true;
					detachButton.disabled = true;

					player.classList.add('is-detached');

					const detachedWindowCheck = window.setInterval(() => {
						if (!detachedWindow.closed) {
							return;
						}

						window.clearInterval(detachedWindowCheck);

						playButton.disabled = false;
						detachButton.disabled = false;

						player.classList.remove('is-detached');

						if (status) {
							status.textContent =
								i18n.ready;
						}
					}, 500);
				});
			}
		});
});
