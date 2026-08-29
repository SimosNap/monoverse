<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Editions\Community\CommunityEdition;

class NavigationService
{
    public function __construct(
        private Translator $translator
    ) {
    }

    public function items(): array
    {
        $items = [

            [
                'title' => $this->translator->translate(
                    'admin.navigation.dashboard'
                ),
                'url' => '/admin',
                'icon' => 'dashboard',
            ],

            [
                'title' => $this->translator->translate(
                    'admin.navigation.settings'
                ),
                'url' => '/admin/settings',
                'icon' => 'settings',
            ],

            [
                'title' => $this->translator->translate(
                    'admin.navigation.blocks'
                ),
                'url' => '/admin/blocks',
                'icon' => 'widgets',
            ],

            [
                'title' => $this->translator->translate(
                    'admin.navigation.pages'
                ),
                'url' => '/admin/pages',
                'icon' => 'pages',
            ],

        ];

        $edition = new CommunityEdition();

        $editionItems = [];

        foreach ($edition->navigation() as $item) {
            if (!is_array($item)) {
                continue;
            }

            $translationKey = trim(
                (string) ($item['translation_key'] ?? '')
            );

            if ($translationKey !== '') {
                $item['title'] = $this->translator->translate(
                    $translationKey
                );
            }

            $editionItems[] = $item;
        }

        return array_merge(
            $items,
            $editionItems
        );
    }
}
