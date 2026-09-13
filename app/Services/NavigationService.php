<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Editions\Community\CommunityEdition;

class NavigationService
{
    public function __construct(
        private Translator $translator,
        private AdminAuthService $auth
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

            [
                'title' => $this->translator->translate(
                    'admin.navigation.faq'
                ),
                'url' => '/admin/faq',
                'icon' => 'faq',
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

        $items = array_merge(
            $items,
            $editionItems
        );

        if ($this->auth->role() === 'administrator') {
            $items[] = [
                'title' => $this->translator->translate(
                    'admin.navigation.administrators'
                ),
                'url' => '/admin/administrators',
                'icon' => 'administrators',
            ];
        }

        if ($this->auth->role() !== 'contentadmin') {
            return $items;
        }

        $allowedUrls = [
            '/admin',
            '/admin/articles',
            '/admin/categories',
            '/admin/events',
            '/admin/moderators',
        ];

        return array_values(
            array_filter(
                $items,
                static function (array $item) use ($allowedUrls): bool {
                    return in_array(
                        (string) ($item['url'] ?? ''),
                        $allowedUrls,
                        true
                    );
                }
            )
        );
    }
}
