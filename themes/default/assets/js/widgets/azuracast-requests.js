'use strict';

document.addEventListener('DOMContentLoaded', () => {
	document
		.querySelectorAll('.mv-azuracast-requests-widget')
		.forEach((widget) => {
			initAzuraCastRequests(widget);
		});
});

function initAzuraCastRequests(widget) {
	const requestsUrl = String(
		widget.dataset.requestsUrl ?? ''
	).trim();

	const locale = String(
		document.documentElement.lang || 'it'
	).trim() || 'it';

	const i18n = {
		noResults:
			widget.dataset.i18nNoResults
				|| 'Nessun brano trovato',

		sent:
			widget.dataset.i18nSent
				|| 'Richiesta inviata con successo.',

		failed:
			widget.dataset.i18nFailed
				|| 'Impossibile inviare la richiesta.',

		sentLabel:
			widget.dataset.i18nSentLabel
				|| 'Inviata',

		connectionError:
			widget.dataset.i18nConnectionError
				|| 'Errore di connessione durante l’invio della richiesta.',

		paginationLabel:
			widget.dataset.i18nPaginationLabel
				|| 'Paginazione brani disponibili',

		previousPage:
			widget.dataset.i18nPreviousPage
				|| 'Pagina precedente',

		nextPage:
			widget.dataset.i18nNextPage
				|| 'Pagina successiva',

		resultsCount:
			widget.dataset.i18nResultsCount
				|| ':start–:end di :total brani',

		identifyFailed:
			widget.dataset.i18nIdentifyFailed
				|| 'Impossibile identificare il brano richiesto.',

		sending:
			widget.dataset.i18nSending
				|| 'Invio...',

		sendingStatus:
			widget.dataset.i18nSendingStatus
				|| 'Invio della richiesta in corso...'
	};

	const translate = (
		template,
		replacements = {}
	) => {
		let value = String(template || '');

		Object.entries(replacements).forEach(
			([key, replacement]) => {
				value = value
					.split(`:${key}`)
					.join(
						String(replacement ?? '')
					);
			}
		);

		return value;
	};

	const searchInput = widget.querySelector(
		'.mv-azuracast-requests-search-input'
	);

	const clearButton = widget.querySelector(
		'.mv-azuracast-requests-search-clear'
	);

	const counter = widget.querySelector(
		'.mv-azuracast-requests-counter'
	);

	const list = widget.querySelector(
		'.mv-azuracast-requests-list'
	);

	const noResults = widget.querySelector(
		'.mv-azuracast-requests-no-results'
	);

	const feedback = widget.querySelector(
		'.mv-azuracast-requests-feedback'
	);

	if (
		!searchInput
		|| !clearButton
		|| !counter
		|| !list
		|| !noResults
		|| !feedback
	) {
		return;
	}

	const cards = Array.from(
		list.querySelectorAll(
			'.mv-azuracast-request-card'
		)
	);

	const requestButtons = Array.from(
		list.querySelectorAll(
			'.mv-azuracast-request-button'
		)
	);

	if (cards.length === 0) {
		return;
	}

	widget
		.querySelectorAll(
			'.mv-azuracast-requests-pagination'
		)
		.forEach((element) => {
			element.remove();
		});

	const pagination = document.createElement('nav');

	pagination.className =
		'mv-azuracast-requests-pagination';

	pagination.setAttribute(
		'aria-label',
		i18n.paginationLabel
	);

	pagination.innerHTML = `
		<button
			type="button"
			class="mv-azuracast-pagination-direction mv-azuracast-pagination-previous"
			aria-label="${escapeHtmlAttribute(
				i18n.previousPage
			)}"
		>
			<i
				class="fa-solid fa-chevron-left"
				aria-hidden="true"
			></i>
		</button>

		<span
			class="mv-azuracast-pagination-summary"
			aria-live="polite"
		></span>

		<button
			type="button"
			class="mv-azuracast-pagination-direction mv-azuracast-pagination-next"
			aria-label="${escapeHtmlAttribute(
				i18n.nextPage
			)}"
		>
			<i
				class="fa-solid fa-chevron-right"
				aria-hidden="true"
			></i>
		</button>
	`;

	list.insertAdjacentElement(
		'afterend',
		pagination
	);

	const previousButton = pagination.querySelector(
		'.mv-azuracast-pagination-previous'
	);

	const nextButton = pagination.querySelector(
		'.mv-azuracast-pagination-next'
	);

	const paginationSummary = pagination.querySelector(
		'.mv-azuracast-pagination-summary'
	);

	if (
		!previousButton
		|| !nextButton
		|| !paginationSummary
	) {
		pagination.remove();
		return;
	}

	let currentPage = 1;
	let pageSize = resolvePageSize(widget);
	let filteredCards = [...cards];
	let requestInProgress = false;

	const normalizeText = (value) => {
		return String(value)
			.toLocaleLowerCase(locale)
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '')
			.replace(/\s+/g, ' ')
			.trim();
	};

	const setElementVisible = (
		element,
		visible,
		displayValue = 'block'
	) => {
		element.hidden = !visible;

		element.style.setProperty(
			'display',
			visible
				? displayValue
				: 'none',
			'important'
		);
	};

	const hideAllCards = () => {
		cards.forEach((card) => {
			setElementVisible(
				card,
				false,
				'grid'
			);
		});
	};

	const hideFeedback = () => {
		feedback.classList.remove(
			'is-success',
			'is-error',
			'is-loading'
		);

		feedback.textContent = '';

		setElementVisible(
			feedback,
			false,
			'block'
		);
	};

	const showFeedback = (
		message,
		type
	) => {
		feedback.classList.remove(
			'is-success',
			'is-error',
			'is-loading'
		);

		feedback.classList.add(
			`is-${type}`
		);

		feedback.textContent = message;

		setElementVisible(
			feedback,
			true,
			'block'
		);
	};

	const setRequestButtonsDisabled = (
		disabled
	) => {
		requestButtons.forEach((button) => {
			button.disabled = disabled;
		});
	};

	const render = () => {
		const totalItems = filteredCards.length;

		const totalPages = totalItems > 0
			? Math.ceil(totalItems / pageSize)
			: 0;

		if (totalPages > 0) {
			currentPage = Math.min(
				Math.max(currentPage, 1),
				totalPages
			);
		} else {
			currentPage = 1;
		}

		const startIndex = (
			currentPage - 1
		) * pageSize;

		const endIndex = Math.min(
			startIndex + pageSize,
			totalItems
		);

		hideAllCards();

		filteredCards
			.slice(startIndex, endIndex)
			.forEach((card) => {
				setElementVisible(
					card,
					true,
					'grid'
				);
			});

		const hasResults = totalItems > 0;

		setElementVisible(
			list,
			hasResults,
			'grid'
		);

		setElementVisible(
			noResults,
			!hasResults,
			'grid'
		);

		if (!hasResults) {
			counter.textContent =
				i18n.noResults;

			setElementVisible(
				pagination,
				false,
				'grid'
			);

			return;
		}

		counter.textContent = translate(
			i18n.resultsCount,
			{
				start: startIndex + 1,
				end: endIndex,
				total: totalItems
			}
		);

		paginationSummary.textContent =
			`${currentPage} / ${totalPages}`;

		previousButton.disabled =
			currentPage <= 1;

		nextButton.disabled =
			currentPage >= totalPages;

		setElementVisible(
			pagination,
			totalPages > 1,
			'grid'
		);
	};

	const applySearch = () => {
		const query = normalizeText(
			searchInput.value
		);

		setElementVisible(
			clearButton,
			query !== '',
			'inline-flex'
		);

		if (query === '') {
			filteredCards = [...cards];
		} else {
			filteredCards = cards.filter((card) => {
				const searchText = normalizeText(
					card.dataset.search ?? ''
				);

				return searchText.includes(query);
			});
		}

		currentPage = 1;
		render();
	};

	const submitRequest = async (
		button
	) => {
		if (requestInProgress) {
			return;
		}

		const requestId = String(
			button.dataset.requestId ?? ''
		).trim();

		if (
			requestsUrl === ''
			|| requestId === ''
		) {
			showFeedback(
				i18n.identifyFailed,
				'error'
			);

			return;
		}

		requestInProgress = true;

		hideFeedback();

		setRequestButtonsDisabled(true);

		const originalHtml = button.innerHTML;

		button.innerHTML = `
			<i
				class="fa-solid fa-spinner fa-spin"
				aria-hidden="true"
			></i>

			<span>${escapeHtml(
				i18n.sending
			)}</span>
		`;

		showFeedback(
			i18n.sendingStatus,
			'loading'
		);

		try {
			const response = await fetch(
				'/api/azuracast/request',
				{
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						requests_url: requestsUrl,
						request_id: requestId,
					}),
				}
			);

			let result = null;

			try {
				result = await response.json();
			} catch (error) {
				result = null;
			}

			const message = String(
				result?.message
					?? (
						response.ok
							? i18n.sent
							: i18n.failed
					)
			).trim();

			if (
				response.ok
				&& result?.success === true
			) {
				showFeedback(
					message,
					'success'
				);

				button.innerHTML = `
					<i
						class="fa-solid fa-check"
						aria-hidden="true"
					></i>

					<span>${escapeHtml(
						i18n.sentLabel
					)}</span>
				`;

				return;
			}

			showFeedback(
				message,
				'error'
			);

			button.innerHTML = originalHtml;
		} catch (error) {
			showFeedback(
				i18n.connectionError,
				'error'
			);

			button.innerHTML = originalHtml;
		} finally {
			requestInProgress = false;

			requestButtons.forEach(
				(requestButton) => {
					if (requestButton !== button) {
						requestButton.disabled = false;
					}
				}
			);

			if (
				!button.querySelector(
					'.fa-check'
				)
			) {
				button.disabled = false;
			}
		}
	};

	searchInput.addEventListener(
		'input',
		applySearch
	);

	searchInput.addEventListener(
		'search',
		applySearch
	);

	clearButton.addEventListener(
		'click',
		() => {
			searchInput.value = '';
			searchInput.focus();
			applySearch();
		}
	);

	previousButton.addEventListener(
		'click',
		() => {
			if (currentPage <= 1) {
				return;
			}

			currentPage--;
			render();
		}
	);

	nextButton.addEventListener(
		'click',
		() => {
			const totalPages = Math.ceil(
				filteredCards.length / pageSize
			);

			if (
				totalPages === 0
				|| currentPage >= totalPages
			) {
				return;
			}

			currentPage++;
			render();
		}
	);

	requestButtons.forEach((button) => {
		button.addEventListener(
			'click',
			() => {
				submitRequest(button);
			}
		);
	});

	if ('ResizeObserver' in window) {
		const resizeObserver = new ResizeObserver(
			() => {
				const newPageSize =
					resolvePageSize(widget);

				if (newPageSize === pageSize) {
					return;
				}

				pageSize = newPageSize;
				currentPage = 1;
				render();
			}
		);

		resizeObserver.observe(widget);
	}

	setElementVisible(
		clearButton,
		false,
		'inline-flex'
	);

	setElementVisible(
		noResults,
		false,
		'grid'
	);

	hideFeedback();
	render();
}

function resolvePageSize(widget) {
	const width = widget.getBoundingClientRect().width;

	if (width < 520) {
		return 4;
	}

	if (width < 820) {
		return 6;
	}

	return 8;
}

function escapeHtml(value) {
	const element = document.createElement('div');

	element.textContent = String(value ?? '');

	return element.innerHTML;
}

function escapeHtmlAttribute(value) {
	return escapeHtml(value);
}
