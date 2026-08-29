<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\ArticleService;

final class LatestArticlesBlock implements BlockInterface
{
    public function __construct(
        private ArticleService $articles
    ) {
    }

    public function type(): string
    {
        return 'latest-articles';
    }

    public function label(): string
    {
        return 'Ultimi articoli';
    }

    public function category(): string
    {
        return 'content';
    }

    public function icon(): string
    {
        return 'fa-newspaper';
    }

    public function description(): string
    {
        return 'Mostra gli ultimi articoli pubblicati nel Chanzine.';
    }

    public function configurable(): bool
    {
        return true;
    }

    public function template(): string
    {
        return 'content/latest-articles';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Ultimi articoli',
            'limit' => 5,
            'show_date' => true,
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
                    ?? 'Ultimi articoli'
                ),
            ],
            [
                'type' => 'number',
                'name' => 'limit',
                'label' => 'Numero articoli',
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
        ];
    }

    public function stylesheets(): array
    {
        return [
            'widgets/latest-articles',
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
                    ?? 'Ultimi articoli'
                )
            ),
            'show_date' => filter_var(
                $settings['show_date'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
            'articles' => $this->articles->listPublished(
                $limit,
                0
            ),
        ];
    }
}
