<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Community;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\CommunityService;
use Monoverse\Services\Translator;

final class LatestMembersBlock implements BlockInterface
{
    public function __construct(
        private CommunityService $community,
        private Translator $translator
    ) {
    }

    public function type(): string
    {
        return 'latest-members';
    }

    public function label(): string
    {
        return 'Nuovi membri';
    }

    public function category(): string
    {
        return 'community';
    }

    public function icon(): string
    {
        return 'fa-user-plus';
    }

    public function description(): string
    {
        return 'Mostra gli ultimi membri registrati nella community.';
    }

    public function configurable(): bool
    {
        return true;
    }

    public function template(): string
    {
        return 'community/latest-members';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Nuovi membri',
            'limit' => 5,
            'show_avatar' => true,
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
                    ?? 'Nuovi membri'
                ),
            ],
            [
                'type' => 'number',
                'name' => 'limit',
                'label' => 'Numero di membri',
                'min' => 1,
                'max' => 20,
                'value' => (int) (
                    $settings['limit']
                    ?? 5
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_avatar',
                'label' => 'Mostra avatar',
                'checked' => (bool) (
                    $settings['show_avatar']
                    ?? true
                ),
            ],
        ];
    }

    public function stylesheets(): array
    {
        return [
            'widgets/latest-members',
        ];
    }

    public function data(
        array $settings = [],
        array $context = []
    ): array {
        $limit = max(
            1,
            min(
                20,
                (int) ($settings['limit'] ?? 5)
            )
        );

        return [
            'title' => trim(
                (string) (
                    $settings['title']
                    ?? 'Nuovi membri'
                )
            ),
            'show_avatar' => $this->booleanSetting(
                $settings,
                'show_avatar',
                true
            ),
            'members' => $this->community->latestMembers(
                $limit,
                $this->translator->getLocale()
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
