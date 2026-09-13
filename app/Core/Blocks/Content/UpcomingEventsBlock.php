<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\EventService;

final class UpcomingEventsBlock implements BlockInterface
{
    public function __construct(
        private EventService $events
    ) {
    }

    public function type(): string
    {
        return 'upcoming-events';
    }

    public function label(): string
    {
        return 'Prossimi eventi';
    }

    public function category(): string
    {
        return 'content';
    }

    public function icon(): string
    {
        return 'fa-calendar-days';
    }

    public function description(): string
    {
        return 'Mostra i prossimi eventi pubblicati.';
    }

    public function configurable(): bool
    {
        return true;
    }

    public function template(): string
    {
        return 'content/upcoming-events';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Prossimi eventi',
            'limit' => 5,
            'show_date' => true,
            'show_location' => true,
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
                    ?? 'Prossimi eventi'
                ),
            ],
            [
                'type' => 'number',
                'name' => 'limit',
                'label' => 'Numero eventi',
                'min' => 1,
                'max' => 20,
                'value' => (int) (
                    $settings['limit']
                    ?? 5
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_date',
                'label' => 'Mostra data',
                'checked' => (bool) (
                    $settings['show_date']
                    ?? true
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_location',
                'label' => 'Mostra luogo',
                'checked' => (bool) (
                    $settings['show_location']
                    ?? true
                ),
            ],
        ];
    }

    public function stylesheets(): array
    {
        return [
            'widgets/upcoming-events',
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
                    $context['block']['title']
                    ?? ''
                )
            ),
            'show_date' => filter_var(
                $settings['show_date'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
            'show_location' => filter_var(
                $settings['show_location'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
            'events' => $this->events->listUpcoming(
                $limit
            ),
        ];
    }
}
