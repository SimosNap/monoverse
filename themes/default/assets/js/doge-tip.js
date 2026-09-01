const dogeTipI18n = window.MonoverseDogeTipI18n || {
	cancel: 'Annulla',
	close: 'Chiudi',
	copy: 'Copia',
	copied: 'Copiato',

	errors: {
		notificationFailed:
			'Impossibile creare la notifica.',
		pongInvalid:
			'Dati del Pong della mancia non validi.',
		pongFailed:
			'Impossibile pubblicare il Pong.',
		pingFailed:
			'Impossibile pubblicare il Ping.',
		addressUnavailable:
			'Indirizzo Dogecoin non disponibile.',
		invalidAmount:
			'Inserisci un importo DOGE valido.',
		myDogeUnavailable:
			'MyDogeMask non è disponibile in questo browser.',
		missingTxId:
			'MyDogeMask non ha restituito un transaction ID.',
		sendFailed:
			'Invio non riuscito:',
		unknown:
			'errore sconosciuto'
	},

	status: {
		confirmTransaction:
			'Conferma la transazione in MyDogeMask…',
		sent:
			'Mancia inviata.',
		viewTransaction:
			'Visualizza transazione',
		pingShared:
			'Condivisa anche in un Ping.',
		pingFailed:
			'La mancia è stata inviata, ma il Ping non è stato pubblicato.',
		pongShared:
			'Aggiunta anche in un Pong.',
		pongFailed:
			'La mancia è stata inviata, ma il Pong non è stato pubblicato.'
	}
};

async function notifyDogeTip(
	username,
	amount,
	txId
) {
	const response = await fetch(
		'/doge-tip/notify',
		{
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type':
					'application/x-www-form-urlencoded;charset=UTF-8',
				'X-Requested-With':
					'XMLHttpRequest'
			},
			body: new URLSearchParams({
				username: username,
				amount: amount,
				tx_id: txId
			})
		}
	);

	const data = await response.json();

	if (
		!response.ok
		|| !data.ok
	) {
		throw new Error(
			data.error
				|| dogeTipI18n.errors.notificationFailed
		);
	}

	return true;
}

const dogeModal =
	document.getElementById('mv-doge-tip-modal');

const dogeTipButtons =
	document.querySelectorAll('.js-doge-tip');

let dogeTipButton = null;

if (
	dogeModal &&
	dogeTipButtons.length > 0
) {
	const dogeAmount =
		document.getElementById('mv-doge-tip-amount');

	const dogeStatus =
		document.getElementById('mv-doge-tip-status');

	const dogeFallback =
		document.getElementById('mv-doge-tip-fallback');

	const dogeFallbackAddress =
		document.getElementById('mv-doge-tip-fallback-address');

	const dogeCopyButton =
		document.getElementById('mv-doge-tip-copy');

	const dogeQr =
		document.getElementById('mv-doge-tip-qr');

	const dogeSendButton =
		document.getElementById('mv-doge-tip-send');

	const dogeShareProfile =
		document.getElementById(
			'mv-doge-tip-share-profile'
		);

	const dogeSharePong =
		document.getElementById(
			'mv-doge-tip-share-pong'
		);

	const dogePongCheckbox =
		document.getElementById(
			'mv-doge-tip-pong'
		);

	const dogePongMessageWrap =
		document.getElementById(
			'mv-doge-tip-pong-message-wrap'
		);

	const dogePongMessage =
		document.getElementById(
			'mv-doge-tip-pong-message'
		);

	const closeDogeModal = () => {

		dogeModal.hidden = true;

		document.body.classList.remove(
			'mv-modal-open'
		);

		if (dogeAmount) {
			dogeAmount.value = '';
		}

		const shareTip =
			document.getElementById(
				'mv-doge-tip-share'
			);

		if (shareTip) {
			shareTip.checked = false;
		}

		if (dogeStatus) {
			dogeStatus.textContent = '';
		}

	};

	const openDogeModal = async () => {

		const address =
			dogeTipButton?.dataset.dogeAddress
				?? '';

		const username =
			dogeTipButton?.dataset.dogeUsername
				?? '';

		const context =
			dogeTipButton?.dataset.dogeContext
				?? 'profile';

		const isPingContext =
			context === 'ping';

		if (dogeShareProfile) {
			dogeShareProfile.hidden =
				isPingContext;
		}

		if (dogeSharePong) {
			dogeSharePong.hidden =
				!isPingContext;
		}

		if (dogePongCheckbox) {
			dogePongCheckbox.checked = false;
		}

		if (dogePongMessageWrap) {
			dogePongMessageWrap.hidden = true;
		}

		if (dogePongMessage) {
			dogePongMessage.value = '';
		}

		const recipient =
			document.getElementById(
				'mv-doge-tip-recipient'
			);

		if (recipient) {
			recipient.textContent = username;
		}

		if (dogeFallbackAddress) {
			dogeFallbackAddress.textContent = address;
		}

		dogeModal.hidden = false;

		dogeModal
			.querySelectorAll('.mv-doge-tip-footer-close')
			.forEach(button => {
				button.textContent =
					dogeTipI18n.cancel;
			});

		document.body.classList.add(
			'mv-modal-open'
		);

		let myDogeConnected = false;

		if (
			window.MonoverseMyDoge
			&& window.MonoverseMyDoge.isAvailable()
		) {
			const state =
				await window.MonoverseMyDoge.refresh();

			myDogeConnected =
				state.connected === true;
		}

		if (dogeFallback) {
			dogeFallback.hidden = myDogeConnected;
		}

		if (
			!myDogeConnected
			&& dogeQr
			&& dogeFallbackAddress
			&& window.MonoverseMyDoge
		) {
			window.MonoverseMyDoge.renderQr(
				dogeQr,
				dogeFallbackAddress.textContent.trim()
			);
		}

		if (dogeAmount) {
			dogeAmount.closest('.mv-form-group').hidden =
				!myDogeConnected;
		}

		if (dogeSendButton) {
			dogeSendButton.hidden =
				!myDogeConnected;
		}

		if (dogeAmount) {
			dogeAmount.focus();
		}

	};

	dogeTipButtons.forEach(button => {

		button.addEventListener(
			'click',
			() => {
				dogeTipButton = button;
				openDogeModal();
			}
		);

	});

	dogeModal
		.querySelectorAll('.mv-doge-tip-close')
		.forEach(button => {

			button.addEventListener(
				'click',
				closeDogeModal
			);

		});

	const dogeBackdrop =
		dogeModal.querySelector(
			'.mv-modal-backdrop'
		);

	if (dogeBackdrop) {

		dogeBackdrop.addEventListener(
			'click',
			closeDogeModal
		);

	}

	if (
		dogeCopyButton
		&& dogeFallbackAddress
	) {

		dogeCopyButton.addEventListener(
			'click',
			async () => {

				const address =
					dogeFallbackAddress.textContent.trim();

				if (address === '') {
					return;
				}

				try {

					await navigator.clipboard.writeText(
						address
					);

					dogeCopyButton.textContent =
						dogeTipI18n.copied;

					setTimeout(
						() => {
							dogeCopyButton.textContent =
								dogeTipI18n.copy;
						},
						1500
					);

				} catch (error) {

					console.error(
						'Dogecoin address copy error:',
						error
					);

				}

			}
		);

	}

	if (dogePongCheckbox) {

		dogePongCheckbox.addEventListener(
			'change',
			() => {

				if (dogePongMessageWrap) {
					dogePongMessageWrap.hidden =
						!dogePongCheckbox.checked;
				}

				if (
					dogePongCheckbox.checked
					&& dogePongMessage
				) {
					dogePongMessage.focus();
				}

			}
		);

	}

	async function createDogeTipPong(
		postUuid,
		username,
		amount,
		message
	) {
		postUuid = String(postUuid ?? '').trim();
		username = String(username ?? '').trim();
		amount = String(amount ?? '').trim();
		message = String(message ?? '').trim();

		if (
			postUuid === ''
			|| username === ''
			|| amount === ''
		) {
			throw new Error(
				dogeTipI18n.errors.pongInvalid
			);
		}

		const response = await fetch(
			'/ping/'
				+ encodeURIComponent(postUuid)
				+ '/doge-tip-comment',
			{
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type':
						'application/x-www-form-urlencoded;charset=UTF-8',
					'X-Requested-With':
						'XMLHttpRequest'
				},
				body: new URLSearchParams({
					username: username,
					amount: amount,
					message: message
				})
			}
		);

		const data = await response.json();

		if (
			!response.ok
			|| !data.ok
		) {
			throw new Error(
				data.error
					|| dogeTipI18n.errors.pongFailed
			);
		}

		return true;
	}

	if (dogeSendButton) {

		dogeSendButton.addEventListener(
			'click',
			async () => {

				const address =
					dogeTipButton.dataset.dogeAddress
						?? '';

				const username =
					dogeTipButton.dataset.dogeUsername
						?? '';

				const context =
					dogeTipButton.dataset.dogeContext
						?? 'profile';

				const postUuid =
					dogeTipButton.dataset.dogePostUuid
						?? '';

				const amount = dogeAmount
					? dogeAmount.value
						.trim()
						.replace(',', '.')
					: '';

				const shareTip =
					document.getElementById(
						'mv-doge-tip-share'
					);

				if (address === '') {

					if (dogeStatus) {
						dogeStatus.textContent =
							dogeTipI18n.errors.addressUnavailable;
					}

					return;
				}

				if (
					amount === ''
					|| Number(amount) <= 0
				) {

					if (dogeStatus) {
						dogeStatus.textContent =
							dogeTipI18n.errors.invalidAmount;
					}

					return;
				}

				if (
					!window.MonoverseMyDoge
					|| !window.MonoverseMyDoge.isAvailable()
				) {

					if (dogeStatus) {
						dogeStatus.textContent =
							dogeTipI18n.errors.myDogeUnavailable;
					}

					return;
				}

				dogeSendButton.disabled = true;

				if (dogeStatus) {
					dogeStatus.textContent =
						dogeTipI18n.status.confirmTransaction;
				}

				try {

					const result =
						await window.MonoverseMyDoge.sendTip(
							address,
							amount
						);

					if (
						!result
						|| !result.txId
					) {
						throw new Error(
							dogeTipI18n.errors.missingTxId
						);
					}

					try {

						await notifyDogeTip(
							username,
							amount,
							result.txId
						);

					} catch (error) {

						console.error(
							'Dogecoin tip notification error:',
							error
						);

					}

					let pingShared = false;
					let pingShareFailed = false;

					let pongShared = false;
					let pongShareFailed = false;

					if (
						context === 'ping'
						&& dogePongCheckbox
						&& dogePongCheckbox.checked
						&& postUuid !== ''
					) {

						try {

							await createDogeTipPong(
								postUuid,
								username,
								amount,
								dogePongMessage
									? dogePongMessage.value
									: ''
							);

							pongShared = true;

						} catch (error) {

							pongShareFailed = true;

							console.error(
								'Dogecoin tip Pong share error:',
								error
							);

						}

					}

					if (
						shareTip
						&& shareTip.checked
						&& username !== ''
					) {

						try {

							const response = await fetch(
								'/ping/doge-tip',
								{
									method: 'POST',
									credentials: 'same-origin',
									headers: {
										'Content-Type':
											'application/x-www-form-urlencoded;charset=UTF-8',
										'X-Requested-With':
											'XMLHttpRequest'
									},
									body: new URLSearchParams({
										username: username,
										amount: amount
									})
								}
							);

							const data =
								await response.json();

							if (
								!response.ok
								|| !data.ok
							) {
								throw new Error(
									data.error
										|| dogeTipI18n.errors.pingFailed
								);
							}

							pingShared = true;

						} catch (error) {

							pingShareFailed = true;

							console.error(
								'Dogecoin tip Ping share error:',
								error
							);

						}

					}

					if (
						window.MonoverseMyDoge
						&& typeof window.MonoverseMyDoge.syncNavStatus
							=== 'function'
					) {
						await window.MonoverseMyDoge.syncNavStatus();
					}

					if (dogeStatus) {

						dogeStatus.innerHTML =
							dogeTipI18n.status.sent
							+ ' '
							+ '<a href="https://dogechain.info/tx/'
							+ encodeURIComponent(result.txId)
							+ '" target="_blank" rel="noopener noreferrer">'
							+ dogeTipI18n.status.viewTransaction
							+ '</a>'
							+ (
								pingShared
									? ' · ' + dogeTipI18n.status.pingShared
									: ''
							)
							+ (
								pingShareFailed
									? ' · ' + dogeTipI18n.status.pingFailed
									: ''
							)
							+ (
								pongShared
									? ' · ' + dogeTipI18n.status.pongShared
									: ''
							)
							+ (
								pongShareFailed
									? ' · ' + dogeTipI18n.status.pongFailed
									: ''
							);

					}

					dogeSendButton.hidden = true;

					dogeModal
						.querySelectorAll('.mv-doge-tip-footer-close')
						.forEach(button => {
							button.textContent =
								dogeTipI18n.close;
						});

				} catch (error) {

					console.error(
						'MyDogeMask transaction error:',
						error
					);

					if (dogeStatus) {
						dogeStatus.textContent =
							dogeTipI18n.errors.sendFailed
							+ ' '
							+ (
								error?.message
									?? dogeTipI18n.errors.unknown
							);
					}

				} finally {

					dogeSendButton.disabled = false;

				}

			}
		);

	}

	document.addEventListener(
		'keydown',
		event => {

			if (
				event.key === 'Escape'
				&& !dogeModal.hidden
			) {
				closeDogeModal();
			}

		}
	);
}
