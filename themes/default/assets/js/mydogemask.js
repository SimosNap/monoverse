(function (window) {
	'use strict';

	if (window.MonoverseMyDoge) {
		return;
	}

	const state = {
		available: false,
		connected: false,
		connecting: false,
		address: '',
		balance: null
	};

	function getUiConfig() {
		const nav = document.getElementById(
			'mv-mydoge-nav'
		);

		const data = nav
			? nav.dataset
			: {};

		return {
			locale: data.locale || 'it',

			unavailable:
				data.i18nUnavailable
				|| 'MyDogeMask non disponibile',

			connected:
				data.i18nConnected
				|| 'Connesso',

			notConnected:
				data.i18nNotConnected
				|| 'Non connesso',

			disconnect:
				data.i18nDisconnect
				|| 'Scollega MyDogeMask',

			connect:
				data.i18nConnect
				|| 'Collega MyDogeMask',

			copied:
				data.i18nCopied
				|| 'Copiato',

			copy:
				data.i18nCopy
				|| 'Copia',

			walletConnected:
				data.i18nWalletConnected
				|| 'Wallet connesso',

			walletNotConnected:
				data.i18nWalletNotConnected
				|| 'Wallet non connesso',
				
			browserUnavailable:
				data.i18nBrowserUnavailable
					|| 'MyDogeMask non è disponibile in questo browser.',
			
			connectionNotAuthorized:
				data.i18nConnectionNotAuthorized
					|| 'Connessione a MyDogeMask non autorizzata.',
			
			addressUnavailable:
				data.i18nAddressUnavailable
					|| 'Indirizzo Dogecoin non disponibile.',
			
			invalidAmount:
				data.i18nInvalidAmount
					|| 'Inserisci un importo DOGE valido.',
			
			missingTxId:
				data.i18nMissingTxid
					|| 'MyDogeMask non ha restituito un transaction ID.',
		};
	}

	function detect() {
		state.available = Boolean(
			window.doge
			&& window.doge.isMyDoge === true
		);

		return state.available;
	}

	function getState() {
		return {
			available: state.available,
			connected: state.connected,
			connecting: state.connecting,
			address: state.address,
			balance: state.balance
		};
	}

	function resetConnection() {
		state.connected = false;
		state.connecting = false;
		state.address = '';
		state.balance = null;
	}

	async function refresh() {
		if (
			!detect()
			|| typeof window.doge.getConnectionStatus !== 'function'
		) {
			resetConnection();
			return getState();
		}

		try {
			const result =
				await window.doge.getConnectionStatus();

			if (
				!result
				|| result.connected !== true
			) {
				resetConnection();
				return getState();
			}

			state.connected = true;
			state.address = result.address
				? String(result.address).trim()
				: '';

			return getState();
		} catch (error) {
			resetConnection();
			return getState();
		}
	}

	async function connect() {
		const ui = getUiConfig();
		
		if (state.connecting) {
			return getState();
		}

		if (state.connected) {
			return getState();
		}

		if (
			!detect()
			|| typeof window.doge.connect !== 'function'
		) {
			throw new Error(
				ui.browserUnavailable
			);
		}

		state.connecting = true;

		try {
			const result = await window.doge.connect();

			if (
				!result
				|| result.approved !== true
				|| !result.address
			) {
				throw new Error(
					ui.connectionNotAuthorized
				);
			}

			state.connected = true;
			state.address = String(
				result.address
			).trim();

			return getState();
		} finally {
			state.connecting = false;
		}
	}

	async function disconnect() {
		if (
			detect()
			&& typeof window.doge.disconnect === 'function'
		) {
			await window.doge.disconnect();
		}

		resetConnection();

		return getState();
	}

	async function getBalance() {
		if (
			!detect()
			|| typeof window.doge.getBalance !== 'function'
		) {
			return null;
		}

		const result = await window.doge.getBalance();

		const rawBalance = (
			result
			&& typeof result === 'object'
			&& 'balance' in result
		)
			? result.balance
			: result;

		if (
			rawBalance === null
			|| rawBalance === undefined
			|| rawBalance === ''
		) {
			state.balance = null;
			return null;
		}

		const koinu = Number(rawBalance);

		if (!Number.isFinite(koinu)) {
			state.balance = null;
			return null;
		}

		const doge = koinu / 100000000;

		const ui = getUiConfig();

		state.balance = doge.toLocaleString(
			ui.locale,
			{
				minimumFractionDigits: 0,
				maximumFractionDigits: 8
			}
		);

		return state.balance;
	}

	async function sendTip(address, amount) {
		const ui = getUiConfig();
		
		if (
			!detect()
			|| typeof window.doge.requestTransaction !== 'function'
		) {
			throw new Error(
				ui.browserUnavailable
			);
		}

		address = String(address ?? '').trim();
		amount = String(amount ?? '').trim();

		if (address === '') {
			throw new Error(
				ui.addressUnavailable
			);
		}

		if (
			amount === ''
			|| Number(amount) <= 0
		) {
			throw new Error(
				ui.invalidAmount
			);
		}

		const result =
			await window.doge.requestTransaction({
				recipientAddress: address,
				dogeAmount: amount
			});

		if (
			!result
			|| !result.txId
		) {
			throw new Error(
				ui.missingTxId
			);
		}

		return {
			txId: String(result.txId)
		};
	}

	function initNavDropdown() {
		const nav = document.getElementById('mv-mydoge-nav');
		const toggle = document.getElementById('mv-mydoge-nav-toggle');
		const dropdown = document.getElementById('mv-mydoge-dropdown');

		if (!nav || !toggle || !dropdown) {
			return;
		}

		toggle.addEventListener('click', function (event) {
			event.stopPropagation();

			const isOpen = !dropdown.hidden;

			dropdown.hidden = isOpen;

			toggle.setAttribute(
				'aria-expanded',
				isOpen ? 'false' : 'true'
			);
		});

		document.addEventListener('click', function (event) {
			if (
				!dropdown.hidden
				&& !nav.contains(event.target)
			) {
				dropdown.hidden = true;
				toggle.setAttribute(
					'aria-expanded',
					'false'
				);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (
				event.key === 'Escape'
				&& !dropdown.hidden
			) {
				dropdown.hidden = true;
				toggle.setAttribute(
					'aria-expanded',
					'false'
				);
			}
		});

		const connectAction = document.getElementById(
			'mv-mydoge-connect-action'
		);

		if (connectAction) {
			connectAction.addEventListener(
				'click',
				async function () {

					connectAction.disabled = true;

					try {
						const currentState = getState();

						if (currentState.connected) {
							await disconnect();
						} else {
							await connect();
						}

						await syncNavStatus();
					} catch (error) {
						console.error(
							'MyDogeMask nav action error:',
							error
						);
					} finally {
						connectAction.disabled = false;
					}
				}
			);
		}

		const copyAddress = document.getElementById(
			'mv-mydoge-copy-address'
		);

		const fallbackAddress = document.getElementById(
			'mv-mydoge-fallback-address'
		);

		if (
			copyAddress
			&& fallbackAddress
		) {
			copyAddress.addEventListener(
				'click',
				async function () {

					const address =
						fallbackAddress.textContent.trim();

					if (!address) {
						return;
					}

					const ui = getUiConfig();

					try {
						await navigator.clipboard.writeText(
							address
						);

						copyAddress.textContent =
							ui.copied;

						window.setTimeout(
							function () {
								copyAddress.textContent =
									ui.copy;
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
	}

	function renderDogeQr(target, address, amount = '') {
		if (
			!target
			|| typeof window.QRCode !== 'function'
		) {
			return;
		}

		address = String(address ?? '').trim();
		amount = String(amount ?? '').trim();

		if (address === '') {
			return;
		}

		target.innerHTML = '';

		let uri = 'dogecoin:' + address;

		if (
			amount !== ''
			&& Number(amount) > 0
		) {
			uri += '?amount=' + encodeURIComponent(amount);
		}

		new window.QRCode(
			target,
			{
				text: uri,
				width: 180,
				height: 180,
				correctLevel: window.QRCode.CorrectLevel.M
			}
		);
	}

	async function syncNavStatus() {
		const dot = document.getElementById(
			'mv-mydoge-status-dot'
		);

		if (!dot) {
			return;
		}

		const currentState = await refresh();
		const ui = getUiConfig();

		const dropdownDot = document.getElementById(
			'mv-mydoge-dropdown-dot'
		);

		const connectionLabel = document.getElementById(
			'mv-mydoge-connection-label'
		);

		const walletBox = document.getElementById(
			'mv-mydoge-dropdown-wallet'
		);

		const balanceBox = document.getElementById(
			'mv-mydoge-balance'
		);

		const addressBox = document.getElementById(
			'mv-mydoge-address'
		);

		const connectAction = document.getElementById(
			'mv-mydoge-connect-action'
		);

		const fallbackBox = document.getElementById(
			'mv-mydoge-fallback'
		);

		if (dropdownDot) {
			dropdownDot.classList.toggle(
				'is-online',
				currentState.connected
			);

			dropdownDot.classList.toggle(
				'is-offline',
				!currentState.connected
			);
		}

		if (connectionLabel) {
			connectionLabel.textContent =
				!currentState.available
					? ui.unavailable
					: (
						currentState.connected
							? ui.connected
							: ui.notConnected
					);
		}

		if (walletBox) {
			walletBox.hidden = !currentState.connected;
		}

		if (fallbackBox) {
			fallbackBox.hidden =
				currentState.available;
		}

		if (addressBox && currentState.address) {
			addressBox.textContent =
				currentState.address;
		}

		if (connectAction) {
			connectAction.hidden =
				!currentState.available;

			if (currentState.available) {
				connectAction.textContent =
					currentState.connected
						? ui.disconnect
						: ui.connect;
			}
		}

		if (
			currentState.connected
			&& balanceBox
		) {
			const balance = await getBalance();

			balanceBox.textContent =
				balance !== null
					? balance
					: '—';
		}

		dot.classList.toggle(
			'is-online',
			currentState.connected
		);

		dot.classList.toggle(
			'is-offline',
			!currentState.connected
		);

		dot.setAttribute(
			'aria-label',
			currentState.connected
				? ui.walletConnected
				: ui.walletNotConnected
		);
	}

	detect();

	window.MonoverseMyDoge = {
		isAvailable: detect,
		getState: getState,
		refresh: refresh,
		connect: connect,
		disconnect: disconnect,
		getBalance: getBalance,
		sendTip: sendTip,
		syncNavStatus: syncNavStatus,
		renderQr: renderDogeQr
	};

	async function initNavStatus() {
		initNavDropdown();

		await syncNavStatus();

		if (!state.available) {
			window.setTimeout(
				syncNavStatus,
				500
			);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener(
			'DOMContentLoaded',
			initNavStatus
		);
	} else {
		initNavStatus();
	}

})(window);
