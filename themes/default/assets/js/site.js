document.addEventListener('DOMContentLoaded', () => {

	document
		.querySelectorAll('.mv-site-nav-dropdown')
		.forEach((dropdown) => {

			const button = dropdown.querySelector(
				'.mv-site-nav-dropdown-toggle'
			);

			if (!button) {
				return;
			}

			button.addEventListener('click', (event) => {

				event.preventDefault();
				event.stopPropagation();

				document
					.querySelectorAll(
						'.mv-site-nav-dropdown.is-open'
					)
					.forEach((item) => {

						if (item === dropdown) {
							return;
						}

						item.classList.remove('is-open');

						const otherButton = item.querySelector(
							'.mv-site-nav-dropdown-toggle'
						);

						if (otherButton) {
							otherButton.setAttribute(
								'aria-expanded',
								'false'
							);
						}

					});

				const isOpen = dropdown.classList.toggle(
					'is-open'
				);

				button.setAttribute(
					'aria-expanded',
					isOpen
						? 'true'
						: 'false'
				);

			});

		});

	document
		.querySelectorAll('.mv-locale-nav')
		.forEach((dropdown) => {

			const button = dropdown.querySelector(
				'.mv-locale-nav-toggle'
			);

			const menu = dropdown.querySelector(
				'.mv-locale-nav-menu'
			);

			if (!button || !menu) {
				return;
			}

			button.addEventListener('click', (event) => {

				event.preventDefault();
				event.stopPropagation();

				document
					.querySelectorAll(
						'.mv-locale-nav.is-open'
					)
					.forEach((item) => {

						if (item === dropdown) {
							return;
						}

						item.classList.remove('is-open');

						const otherButton = item.querySelector(
							'.mv-locale-nav-toggle'
						);

						const otherMenu = item.querySelector(
							'.mv-locale-nav-menu'
						);

						if (otherButton) {
							otherButton.setAttribute(
								'aria-expanded',
								'false'
							);
						}

						if (otherMenu) {
							otherMenu.hidden = true;
						}

					});

				const isOpen = dropdown.classList.toggle(
					'is-open'
				);

				button.setAttribute(
					'aria-expanded',
					isOpen
						? 'true'
						: 'false'
				);

				menu.hidden = !isOpen;

			});

		});

	document
		.querySelectorAll('.mv-site-account-dropdown')
		.forEach((dropdown) => {

			const button = dropdown.querySelector(
				'.mv-account-nav-toggle'
			);

			const menu = dropdown.querySelector(
				'.mv-account-nav-menu'
			);

			if (!button || !menu) {
				return;
			}

			button.addEventListener('click', (event) => {

				event.preventDefault();
				event.stopPropagation();

				document
					.querySelectorAll(
						'.mv-site-account-dropdown.is-open'
					)
					.forEach((item) => {

						if (item === dropdown) {
							return;
						}

						item.classList.remove('is-open');

						const otherButton = item.querySelector(
							'.mv-account-nav-toggle'
						);

						const otherMenu = item.querySelector(
							'.mv-account-nav-menu'
						);

						if (otherButton) {
							otherButton.setAttribute(
								'aria-expanded',
								'false'
							);
						}

						if (otherMenu) {
							otherMenu.hidden = true;
						}

					});

				const isOpen = dropdown.classList.toggle(
					'is-open'
				);

				button.setAttribute(
					'aria-expanded',
					isOpen
						? 'true'
						: 'false'
				);

				menu.hidden = !isOpen;

			});

		});

	document.addEventListener('click', (event) => {

		document
			.querySelectorAll(
				'.mv-site-nav-dropdown.is-open'
			)
			.forEach((dropdown) => {

				if (dropdown.contains(event.target)) {
					return;
				}

				dropdown.classList.remove('is-open');

				const button = dropdown.querySelector(
					'.mv-site-nav-dropdown-toggle'
				);

				if (button) {
					button.setAttribute(
						'aria-expanded',
						'false'
					);
				}

			});

		document
			.querySelectorAll(
				'.mv-locale-nav.is-open'
			)
			.forEach((dropdown) => {

				if (dropdown.contains(event.target)) {
					return;
				}

				dropdown.classList.remove('is-open');

				const button = dropdown.querySelector(
					'.mv-locale-nav-toggle'
				);

				const menu = dropdown.querySelector(
					'.mv-locale-nav-menu'
				);

				if (button) {
					button.setAttribute(
						'aria-expanded',
						'false'
					);
				}

				if (menu) {
					menu.hidden = true;
				}

			});

		document
			.querySelectorAll(
				'.mv-site-account-dropdown.is-open'
			)
			.forEach((dropdown) => {

				if (dropdown.contains(event.target)) {
					return;
				}

				dropdown.classList.remove('is-open');

				const button = dropdown.querySelector(
					'.mv-account-nav-toggle'
				);

				const menu = dropdown.querySelector(
					'.mv-account-nav-menu'
				);

				if (button) {
					button.setAttribute(
						'aria-expanded',
						'false'
					);
				}

				if (menu) {
					menu.hidden = true;
				}

			});

	});

	document.addEventListener('keydown', (event) => {

		if (event.key !== 'Escape') {
			return;
		}

		document
			.querySelectorAll(
				'.mv-site-nav-dropdown.is-open'
			)
			.forEach((dropdown) => {

				dropdown.classList.remove('is-open');

				const button = dropdown.querySelector(
					'.mv-site-nav-dropdown-toggle'
				);

				if (button) {
					button.setAttribute(
						'aria-expanded',
						'false'
					);
				}

			});

		document
			.querySelectorAll(
				'.mv-locale-nav.is-open'
			)
			.forEach((dropdown) => {

				dropdown.classList.remove('is-open');

				const button = dropdown.querySelector(
					'.mv-locale-nav-toggle'
				);

				const menu = dropdown.querySelector(
					'.mv-locale-nav-menu'
				);

				if (button) {
					button.setAttribute(
						'aria-expanded',
						'false'
					);
				}

				if (menu) {
					menu.hidden = true;
				}

			});

		document
			.querySelectorAll(
				'.mv-site-account-dropdown.is-open'
			)
			.forEach((dropdown) => {

				dropdown.classList.remove('is-open');

				const button = dropdown.querySelector(
					'.mv-account-nav-toggle'
				);

				const menu = dropdown.querySelector(
					'.mv-account-nav-menu'
				);

				if (button) {
					button.setAttribute(
						'aria-expanded',
						'false'
					);
				}

				if (menu) {
					menu.hidden = true;
				}

			});

	});

});
