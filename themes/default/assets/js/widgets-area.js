'use strict';

document.addEventListener('DOMContentLoaded', () => {
	const list = document.querySelector(
		'[data-widget-sortable]'
	);

	if (!list) {
		return;
	}

	const page = list.dataset.page || '';
	const area = list.dataset.area || '';

	let draggedItem = null;
	let previousOrder = [];

	const getItems = () => Array.from(
		list.querySelectorAll(
			'.mv-widget-area-item[data-widget-id]'
		)
	);

	const getOrder = () => getItems()
		.map((item) => Number(item.dataset.widgetId))
		.filter((id) => Number.isInteger(id) && id > 0);

	const findInsertionPoint = (clientY) => {
		const candidates = getItems().filter(
			(item) => item !== draggedItem
		);

		return candidates.reduce(
			(closest, item) => {
				const box = item.getBoundingClientRect();
				const offset = clientY
					- box.top
					- (box.height / 2);

				if (
					offset < 0
					&& offset > closest.offset
				) {
					return {
						offset,
						element: item,
					};
				}

				return closest;
			},
			{
				offset: Number.NEGATIVE_INFINITY,
				element: null,
			}
		).element;
	};

	const restoreOrder = () => {
		for (const id of previousOrder) {
			const item = list.querySelector(
				`[data-widget-id="${id}"]`
			);

			if (item) {
				list.appendChild(item);
			}
		}
	};

	const setSavingState = (saving) => {
		list.classList.toggle(
			'is-saving',
			saving
		);

		for (const item of getItems()) {
			item.setAttribute(
				'draggable',
				saving ? 'false' : 'true'
			);
		}
	};

	const saveOrder = async () => {
		const order = getOrder();

		if (
			JSON.stringify(order)
			=== JSON.stringify(previousOrder)
		) {
			return;
		}

		setSavingState(true);

		const body = new URLSearchParams({
			page,
			area,
			order: JSON.stringify(order),
		});

		try {
			const response = await fetch(
				'/admin/blocks/reorder',
				{
					method: 'POST',
					headers: {
						'Content-Type':
							'application/x-www-form-urlencoded; charset=UTF-8',
						'X-Requested-With':
							'XMLHttpRequest',
					},
					body: body.toString(),
				}
			);

			const result = await response.json();

			if (!response.ok || !result.ok) {
				throw new Error(
					result.message
						|| 'Impossibile salvare l’ordine.'
				);
			}

			previousOrder = order;

			list.classList.add('is-saved');

			window.setTimeout(() => {
				list.classList.remove('is-saved');
			}, 700);
		} catch (error) {
			restoreOrder();

			window.alert(
				error instanceof Error
					? error.message
					: 'Impossibile salvare l’ordine.'
			);
		} finally {
			setSavingState(false);
		}
	};

	previousOrder = getOrder();

	for (const item of getItems()) {
		item.addEventListener('dragstart', (event) => {
			draggedItem = item;
			previousOrder = getOrder();

			item.classList.add('is-dragging');

			event.dataTransfer.effectAllowed = 'move';
			event.dataTransfer.setData(
				'text/plain',
				item.dataset.widgetId || ''
			);
		});

		item.addEventListener('dragend', async () => {
			item.classList.remove('is-dragging');

			draggedItem = null;

			await saveOrder();
		});
	}

	list.addEventListener('dragover', (event) => {
		if (!draggedItem) {
			return;
		}

		event.preventDefault();

		const insertionPoint = findInsertionPoint(
			event.clientY
		);

		if (insertionPoint) {
			list.insertBefore(
				draggedItem,
				insertionPoint
			);
		} else {
			list.appendChild(draggedItem);
		}
	});
});
