'use strict';

document.querySelectorAll('.mv-azuracast-widget').forEach((widget) => {
	const audio = widget.querySelector('.mv-azuracast-audio');
	const playButton = widget.querySelector('.mv-azuracast-play');
	const playIcon = playButton?.querySelector('i');
	const status = widget.querySelector('.mv-azuracast-status');
	const waveform = widget.querySelector('.mv-azuracast-waveform-bars');
	const muteButton = widget.querySelector('.mv-azuracast-mute');
	const muteIcon = muteButton?.querySelector('i');
	const detachButton = widget.querySelector('.mv-azuracast-detach');

	const volumeRange = widget.querySelector(
		'.mv-azuracast-volume-range'
	);

	if (
		!(audio instanceof HTMLAudioElement)
		|| !(playButton instanceof HTMLButtonElement)
	) {
		return;
	}

	const i18n = {
		play:
			widget.dataset.i18nPlay
				|| 'Avvia la radio',

		pause:
			widget.dataset.i18nPause
				|| 'Metti in pausa la radio',

		playing:
			widget.dataset.i18nPlaying
				|| 'In riproduzione',

		paused:
			widget.dataset.i18nPaused
				|| 'In pausa',

		ready:
			widget.dataset.i18nReady
				|| 'Pronto',

		mute:
			widget.dataset.i18nMute
				|| 'Disattiva audio',

		unmute:
			widget.dataset.i18nUnmute
				|| 'Riattiva audio',

		unavailable:
			widget.dataset.i18nUnavailable
				|| 'Stream temporaneamente non disponibile.',

		connecting:
			widget.dataset.i18nConnecting
				|| 'Connessione…',

		startFailed:
			widget.dataset.i18nStartFailed
				|| 'Impossibile avviare la radio',

		slowConnection:
			widget.dataset.i18nSlowConnection
				|| 'Connessione lenta…',

		playerUnavailable:
			widget.dataset.i18nPlayerUnavailable
				|| 'Radio non disponibile',

		detach:
			widget.dataset.i18nDetach
				|| 'Apri il player in una finestra separata',

		detached:
			widget.dataset.i18nDetached
				|| 'Player aperto in una finestra separata'
	};

	let detachedWindow = null;
	let detachedWindowWatcher = null;

	const setStatus = (text) => {
		if (status) {
			status.textContent = text;
		}
	};

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

		waveform?.classList.toggle(
			'is-playing',
			isPlaying
		);

		if (widget.classList.contains('is-detached')) {
			setStatus(i18n.detached);
			return;
		}

		setStatus(
			isPlaying
				? i18n.playing
				: i18n.paused
		);
	};

	const setMuteState = () => {
		if (!(muteButton instanceof HTMLButtonElement)) {
			return;
		}

		const isMuted = audio.muted || audio.volume === 0;

		muteButton.setAttribute(
			'aria-pressed',
			isMuted ? 'true' : 'false'
		);

		muteButton.setAttribute(
			'aria-label',
			isMuted
				? i18n.unmute
				: i18n.mute
		);

		if (muteIcon) {
			if (isMuted) {
				muteIcon.className =
					'fa-solid fa-volume-xmark';
			} else if (audio.volume < 0.5) {
				muteIcon.className =
					'fa-solid fa-volume-low';
			} else {
				muteIcon.className =
					'fa-solid fa-volume-high';
			}
		}
	};

	const setDetachedState = (isDetached) => {
		widget.classList.toggle(
			'is-detached',
			isDetached
		);

		playButton.disabled = isDetached;

		if (muteButton instanceof HTMLButtonElement) {
			muteButton.disabled = isDetached;
		}

		if (volumeRange instanceof HTMLInputElement) {
			volumeRange.disabled = isDetached;
		}

		if (detachButton instanceof HTMLButtonElement) {
			detachButton.disabled = isDetached;
		}

		if (isDetached) {
			waveform?.classList.remove('is-playing');
			setStatus(i18n.detached);
		} else {
			setPlayingState(false);
			setStatus(i18n.ready);
		}
	};

	const stopDetachedWindowWatcher = () => {
		if (detachedWindowWatcher !== null) {
			window.clearInterval(detachedWindowWatcher);
			detachedWindowWatcher = null;
		}
	};

	const restoreEmbeddedPlayer = () => {
		stopDetachedWindowWatcher();
		detachedWindow = null;
		setDetachedState(false);
	};

	const watchDetachedWindow = () => {
		stopDetachedWindowWatcher();

		detachedWindowWatcher = window.setInterval(() => {
			if (!detachedWindow || detachedWindow.closed) {
				restoreEmbeddedPlayer();
			}
		}, 500);
	};

	playButton.addEventListener('click', async () => {
		if (widget.classList.contains('is-detached')) {
			return;
		}

		if (!audio.paused) {
			audio.pause();
			return;
		}

		setStatus(i18n.connecting);

		try {
			await audio.play();
		} catch (error) {
			setStatus(i18n.startFailed);
			setPlayingState(false);
		}
	});

	audio.addEventListener('playing', () => {
		setPlayingState(true);
	});

	audio.addEventListener('pause', () => {
		setPlayingState(false);
	});

	audio.addEventListener('waiting', () => {
		if (!widget.classList.contains('is-detached')) {
			setStatus(i18n.connecting);
		}
	});

	audio.addEventListener('stalled', () => {
		if (!widget.classList.contains('is-detached')) {
			setStatus(i18n.slowConnection);
		}
	});

	audio.addEventListener('error', () => {
		if (widget.classList.contains('is-detached')) {
			return;
		}

		setPlayingState(false);
		setStatus(i18n.playerUnavailable);
	});

	if (muteButton instanceof HTMLButtonElement) {
		muteButton.addEventListener('click', () => {
			if (widget.classList.contains('is-detached')) {
				return;
			}

			audio.muted = !audio.muted;
			setMuteState();
		});
	}

	if (volumeRange instanceof HTMLInputElement) {
		volumeRange.addEventListener('input', () => {
			if (widget.classList.contains('is-detached')) {
				return;
			}

			const volume = Number.parseFloat(
				volumeRange.value
			);

			audio.volume = Number.isFinite(volume)
				? Math.min(1, Math.max(0, volume))
				: 1;

			audio.muted = audio.volume === 0;

			setMuteState();
		});
	}

	if (detachButton instanceof HTMLButtonElement) {
		detachButton.addEventListener('click', () => {
			const streamUrl = audio.currentSrc || audio.src;

			if (!streamUrl) {
				setStatus(i18n.playerUnavailable);
				return;
			}

			detachedWindow = window.open(
				streamUrl,
				'monoverse-radio-player',
				'width=420,height=180,resizable=yes'
			);

			if (!detachedWindow) {
				return;
			}

			audio.pause();

			try {
				audio.currentTime = 0;
			} catch (error) {
			}

			setDetachedState(true);
			watchDetachedWindow();
		});
	}

	window.addEventListener('beforeunload', () => {
		stopDetachedWindowWatcher();
	});

	audio.volume = 1;

	setPlayingState(false);
	setStatus(i18n.ready);
	setMuteState();

	if (detachButton instanceof HTMLButtonElement) {
		detachButton.setAttribute(
			'aria-label',
			i18n.detach
		);

		detachButton.setAttribute(
			'title',
			i18n.detach
		);
	}
});
