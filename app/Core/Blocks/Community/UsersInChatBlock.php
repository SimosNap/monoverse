<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Community;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\CommunityService;

final class UsersInChatBlock implements BlockInterface
{
    public function __construct(
        private CommunityService $community
    ) {
    }

    public function type(): string
    {
        return 'users-in-chat';
    }

    public function label(): string
    {
        return 'Adesso in chat';
    }

    public function category(): string
    {
        return 'community';
    }

    public function icon(): string
    {
        return 'fa-comments';
    }

    public function description(): string
    {
        return 'Mostra le persone presenti in questo momento nella chat IRC.';
    }

    public function configurable(): bool
    {
        return true;
    }

    public function template(): string
    {
        return 'community/users-in-chat';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Adesso in chat',
            'limit' => 10,
            'show_total' => true,
            'show_avatar' => true,
            'show_join_link' => true,
        ];
    }

    public function settingsForm(
        array $settings = []
    ): array {
        return [
            [
                'type' => 'text',
                'name' => 'title',
                'label' => 'Titolo',
                'value' => (string) (
                    $settings['title']
                    ?? 'Adesso in chat'
                ),
            ],
            [
                'type' => 'number',
                'name' => 'limit',
                'label' => 'Numero di persone',
                'min' => 1,
                'max' => 50,
                'value' => (int) (
                    $settings['limit']
                    ?? 10
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_total',
                'label' => 'Mostra il totale delle persone in chat',
                'checked' => (bool) (
                    $settings['show_total']
                    ?? true
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_avatar',
                'label' => 'Mostra gli avatar dei membri registrati',
                'checked' => (bool) (
                    $settings['show_avatar']
                    ?? true
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_join_link',
                'label' => 'Mostra il collegamento "Entra in chat"',
                'checked' => (bool) (
                    $settings['show_join_link']
                    ?? true
                ),
            ],
        ];
    }

    public function stylesheets(): array
    {
        return [
            'widgets/users-in-chat',
        ];
    }

    public function data(
        array $settings = [],
        array $context = []
    ): array {
        $limit = max(
            1,
            min(
                50,
                (int) ($settings['limit'] ?? 10)
            )
        );

        $chat = $this->community->usersInChat(
            $limit
        );

        return [
            'title' => (string) (
                $context['block']['title']
                ?? ''
            ),
            'limit' => $limit,
            'show_total' => $this->booleanSetting(
                $settings,
                'show_total',
                true
            ),
            'show_avatar' => $this->booleanSetting(
                $settings,
                'show_avatar',
                true
            ),
            'show_join_link' => $this->booleanSetting(
                $settings,
                'show_join_link',
                true
            ),
            'users' => $chat['users'] ?? [],
            'total' => (int) (
                $chat['total'] ?? 0
            ),
        ];
    }

    private function booleanSetting(
        array $settings,
        string $name,
        bool $default
    ): bool {
        if (!array_key_exists($name, $settings)) {
            return $default;
        }

        return filter_var(
            $settings[$name],
            FILTER_VALIDATE_BOOL
        );
    }
}