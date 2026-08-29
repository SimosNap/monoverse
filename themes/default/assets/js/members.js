'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-members-page]');

    if (!page) {
        return;
    }

    const buttons = Array.from(
        page.querySelectorAll('[data-members-view]')
    );

    if (buttons.length === 0) {
        return;
    }

    const storageKey = 'monoverse-members-view';

    const setView = (view) => {
        const isList = view === 'list';

        page.classList.toggle(
            'is-list-view',
            isList
        );

        for (const button of buttons) {
            const active =
                button.dataset.membersView === view;

            button.classList.toggle(
                'is-active',
                active
            );

            button.setAttribute(
                'aria-pressed',
                active ? 'true' : 'false'
            );
        }

        try {
            window.localStorage.setItem(
                storageKey,
                view
            );
        } catch (error) {
        }
    };

    let initialView = 'grid';

    try {
        const savedView = window.localStorage.getItem(
            storageKey
        );

        if (savedView === 'grid' || savedView === 'list') {
            initialView = savedView;
        }
    } catch (error) {
    }

    setView(initialView);

    for (const button of buttons) {
        button.addEventListener('click', () => {
            setView(
                button.dataset.membersView === 'list'
                    ? 'list'
                    : 'grid'
            );
        });
    }
});
