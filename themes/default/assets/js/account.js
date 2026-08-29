(function (window, document) {
	'use strict';

	const storageKey = 'monoverse.chat.preferences';

	/**
	 * @returns {Object|null}
	 */
	function loadPreferences() {
		const raw = window.localStorage.getItem(storageKey);

		if (!raw) {
			return null;
		}

		try {
			const data = JSON.parse(raw);

			return {
				nickname: String(data.nickname || ''),
				age: String(data.age || ''),
				city: String(data.city || ''),
				sex: String(data.sex || 'U')
			};
		} catch (error) {
			window.localStorage.removeItem(storageKey);
			return null;
		}
	}

	/**
	 * @param {Object} data
	 */
	function savePreferences(data) {
		window.localStorage.setItem(storageKey, JSON.stringify({
			nickname: String(data.nickname || ''),
			age: String(data.age || ''),
			city: String(data.city || ''),
			sex: String(data.sex || 'U')
		}));
	}

	function clearPreferences() {
		window.localStorage.removeItem(storageKey);
	}

	window.MonoverseChatPreferences = {
		load: loadPreferences,
		save: savePreferences,
		clear: clearPreferences
	};

	function initAccount() {

		if (window.__monoverseAccountInitialized) {
			return;
		}

		window.__monoverseAccountInitialized = true;

		const form = document.getElementById(
			'account-chat-preferences'
		);

		if (!form) {
			return;
		}

		const data = form.dataset || {};

		const i18n = {
			localSaved:
				data.i18nLocalSaved
					|| 'Preferenze salvate in questo browser.',

			localCleared:
				data.i18nLocalCleared
					|| 'Dati salvati nel browser eliminati.',

			dogeConnecting:
				data.i18nDogeConnecting
					|| 'Connessione a MyDogeMask in corso…',

			dogeConnectedAddress:
				data.i18nDogeConnectedAddress
					|| 'MyDogeMask collegato. Indirizzo: :address',

			dogeConnectFailed:
				data.i18nDogeConnectFailed
					|| 'Impossibile collegare MyDogeMask.',

			dogeUseSimosnapAddress:
				data.i18nDogeUseSimosnapAddress
					|| 'Verrà utilizzato l’indirizzo Dogecoin configurato sul tuo account SimosNap.',

			dogeWalletConnectedAddress:
				data.i18nDogeWalletConnectedAddress
					|| 'MyDogeMask connesso. Indirizzo: :address',

			dogeConfiguredAddress:
				data.i18nDogeConfiguredAddress
					|| 'Indirizzo MyDogeMask configurato: :address',

			dogeWalletDetected:
				data.i18nDogeWalletDetected
					|| 'MyDogeMask rilevato. Collega il wallet per usare il suo indirizzo.',

			dogeUnavailable:
				data.i18nDogeUnavailable
					|| 'MyDogeMask non è disponibile in questo browser.',

			dogeNotAuthorized:
				data.i18nDogeNotAuthorized
					|| 'Connessione a MyDogeMask non autorizzata.'
		};

		function translate(template, replacements) {
			let value = String(template || '');

			Object.entries(replacements || {}).forEach(
				function (entry) {
					const key = entry[0];
					const replacement = entry[1];

					value = value.split(
						':' + key
					).join(
						String(replacement ?? '')
					);
				}
			);

			return value;
		}

		/*
		 * Crypto Tips — MyDogeMask
		 */

		function dogeTipsForm() {
			return document.getElementById(
				'mv-doge-tips-form'
			);
		}

		function setDogeStatus(message, type) {
			const statusBox = document.getElementById(
				'mv-doge-wallet-status'
			);

			if (!statusBox) {
				return;
			}

			statusBox.textContent = message || '';

			statusBox.classList.remove(
				'is-success',
				'is-error',
				'is-info'
			);

			if (type) {
				statusBox.classList.add('is-' + type);
			}
		}

		async function connectDogeWallet() {
			if (!window.MonoverseMyDoge) {
				setDogeStatus(
					i18n.dogeUnavailable,
					'error'
				);
				return;
			}

			setDogeStatus(
				i18n.dogeConnecting,
				'info'
			);

			try {
				const state =
					await window.MonoverseMyDoge.connect();

				if (
					!state.connected
					|| !state.address
				) {
					setDogeStatus(
						i18n.dogeNotAuthorized,
						'error'
					);
					return;
				}

				const addressField = document.getElementById(
					'doge_tip_address'
				);

				if (addressField) {
					addressField.value = state.address;
				}

				const myDogeRadio = document.querySelector(
					'input[name="doge_tip_source"][value="mydogemask"]'
				);

				if (myDogeRadio) {
					myDogeRadio.checked = true;
				}

				setDogeStatus(
					translate(
						i18n.dogeConnectedAddress,
						{
							address: state.address
						}
					),
					'success'
				);

				syncDogeSource();
			} catch (error) {
				console.error(
					'MyDogeMask connection error:',
					error
				);

				setDogeStatus(
					error?.message
						|| i18n.dogeConnectFailed,
					'error'
				);
			}
		}

		async function checkDogeWalletConnection() {
			if (!window.MonoverseMyDoge) {
				return;
			}

			let state =
				await window.MonoverseMyDoge.refresh();

			if (
				!state.available
				|| !state.connected
			) {
				await new Promise(function (resolve) {
					window.setTimeout(resolve, 600);
				});

				state =
					await window.MonoverseMyDoge.refresh();
			}

			if (
				!state.connected
				|| !state.address
			) {
				return;
			}

			const addressField = document.getElementById(
				'doge_tip_address'
			);

			if (addressField) {
				addressField.value = state.address;
			}
		}

		function syncDogeSource() {
			const dogeForm = dogeTipsForm();

			if (!dogeForm) {
				return;
			}

			const selected = dogeForm.querySelector(
				'input[name="doge_tip_source"]:checked'
			);

			const source = selected
				? selected.value
				: '';

			const connectButton = document.getElementById(
				'mv-doge-connect'
			);

			const addressField = document.getElementById(
				'doge_tip_address'
			);

			const myDogeState = window.MonoverseMyDoge
				? window.MonoverseMyDoge.getState()
				: {
					available: false,
					connected: false,
					address: ''
				};

			if (connectButton) {
				connectButton.hidden =
					source !== 'mydogemask'
						|| myDogeState.connected;
			}

			if (source === 'simosnap') {
				setDogeStatus(
					i18n.dogeUseSimosnapAddress,
					'info'
				);
				return;
			}

			if (
				source === 'mydogemask'
				&& myDogeState.connected
				&& myDogeState.address
			) {
				if (addressField) {
					addressField.value =
						myDogeState.address;
				}

				setDogeStatus(
					translate(
						i18n.dogeWalletConnectedAddress,
						{
							address:
								myDogeState.address
						}
					),
					'success'
				);

				return;
			}

			if (
				source === 'mydogemask'
				&& addressField
				&& addressField.value.trim() !== ''
			) {
				setDogeStatus(
					translate(
						i18n.dogeConfiguredAddress,
						{
							address:
								addressField.value.trim()
						}
					),
					'success'
				);
				return;
			}

			if (source === 'mydogemask') {
				if (
					window.MonoverseMyDoge
					&& window.MonoverseMyDoge.isAvailable()
				) {
					setDogeStatus(
						i18n.dogeWalletDetected,
						'info'
					);
				} else {
					setDogeStatus(
						i18n.dogeUnavailable,
						'error'
					);
				}

				return;
			}

			setDogeStatus('', null);
		}

		document.addEventListener(
			'change',
			function (event) {
				if (
					event.target
					&& event.target.matches(
						'input[name="doge_tip_source"]'
					)
				) {
					syncDogeSource();
				}
			}
		);

		document.addEventListener(
			'click',
			async function (event) {
				const connectButton = event.target.closest(
					'#mv-doge-connect'
				);

				if (!connectButton) {
					return;
				}

				connectButton.disabled = true;

				try {
					await connectDogeWallet();
				} finally {
					connectButton.disabled = false;
				}
			}
		);

		const clearButton = document.getElementById(
			'clear-local-chat-preferences'
		);

		const profileBox = document.getElementById(
			'public-profile-options'
		);

		const saveCards = form.querySelectorAll(
			'.mv-save-card'
		);

		const fields = {
			nickname: document.getElementById('pref_nick'),
			age: document.getElementById('pref_age'),
			city: document.getElementById('pref_location'),
			sex: document.getElementById('pref_sex')
		};

		function selectedTarget() {
			const checked = form.querySelector(
				'input[name="save_target"]:checked'
			);

			return checked
				? checked.value
				: 'local';
		}

		function updateSaveCards() {
			saveCards.forEach(function (card) {
				const radio = card.querySelector(
					'input[name="save_target"]'
				);

				if (!radio) {
					return;
				}

				card.classList.toggle(
					'is-active',
					radio.checked
				);
			});

			if (profileBox) {
				profileBox.hidden =
					selectedTarget() !== 'database';
			}
		}

		function populateForm(data) {
			if (!data) {
				return;
			}

			if (
				fields.nickname
				&& data.nickname
			) {
				fields.nickname.value =
					data.nickname;
			}

			if (fields.age) {
				fields.age.value =
					data.age || '';
			}

			if (fields.city) {
				fields.city.value =
					data.city || '';
			}

			if (fields.sex) {
				fields.sex.value =
					data.sex || 'U';
			}
		}

		function readForm() {
			return {
				nickname: fields.nickname
					? fields.nickname.value
					: '',

				age: fields.age
					? fields.age.value
					: '',

				city: fields.city
					? fields.city.value
					: '',

				sex: fields.sex
					? fields.sex.value
					: 'U'
			};
		}

		function resetLocalFields() {
			if (
				fields.nickname
				&& fields.nickname.options
			) {
				fields.nickname.selectedIndex = 0;
			}

			if (fields.age) {
				fields.age.value = '';
			}

			if (fields.city) {
				fields.city.value = '';
			}

			if (fields.sex) {
				fields.sex.value = 'U';
			}
		}

		saveCards.forEach(function (card) {
			card.addEventListener(
				'click',
				function () {
					const radio = card.querySelector(
						'input[name="save_target"]'
					);

					if (!radio) {
						return;
					}

					radio.checked = true;

					updateSaveCards();

					if (radio.value === 'local') {
						populateForm(
							loadPreferences()
						);
					}
				}
			);
		});

		form.addEventListener(
			'submit',
			function (event) {
				if (selectedTarget() !== 'local') {
					return;
				}

				event.preventDefault();

				savePreferences(
					readForm()
				);

				window.alert(
					i18n.localSaved
				);
			}
		);

		if (clearButton) {
			clearButton.addEventListener(
				'click',
				function () {
					clearPreferences();
					resetLocalFields();

					window.alert(
						i18n.localCleared
					);
				}
			);
		}

		updateSaveCards();

		checkDogeWalletConnection()
			.finally(function () {
				syncDogeSource();
			});

		if (selectedTarget() === 'local') {
			populateForm(
				loadPreferences()
			);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener(
			'DOMContentLoaded',
			initAccount
		);
	} else {
		initAccount();
	}

})(window, document);
