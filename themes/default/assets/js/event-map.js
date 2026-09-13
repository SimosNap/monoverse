'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const openButton = document.querySelector('[data-event-map-open]');
    const modal = document.querySelector('[data-event-map-modal]');
    const mapElement = document.querySelector('[data-event-map]');

    if (!openButton || !modal || !mapElement) {
        return;
    }

    if (typeof L === 'undefined') {
        return;
    }

    const latitude = Number.parseFloat(
        mapElement.dataset.latitude || ''
    );

    const longitude = Number.parseFloat(
        mapElement.dataset.longitude || ''
    );

    if (
        !Number.isFinite(latitude)
        || !Number.isFinite(longitude)
    ) {
        return;
    }

    const closeButtons = modal.querySelectorAll(
        '[data-event-map-close]'
    );

    let map = null;

    const openModal = () => {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');

        document.body.classList.add(
            'events-map-modal-open'
        );

        if (map === null) {
            map = L.map(mapElement).setView(
                [latitude, longitude],
                15
            );

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    attribution:
                        '&copy; OpenStreetMap contributors',
                }
            ).addTo(map);

            L.marker(
                [latitude, longitude]
            ).addTo(map);
        }

        window.setTimeout(() => {
            map.invalidateSize();
        }, 0);
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove(
            'events-map-modal-open'
        );

        openButton.focus();
    };

    openButton.addEventListener(
        'click',
        openModal
    );

    closeButtons.forEach((button) => {
        button.addEventListener(
            'click',
            closeModal
        );
    });

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape'
            && modal.classList.contains('is-open')
        ) {
            closeModal();
        }
    });
});
