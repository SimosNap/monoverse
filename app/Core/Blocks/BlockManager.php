<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

use Monoverse\Repositories\BlockRepository;

final class BlockManager
{
    /**
     * @var array<int,string>
     */
    private array $stylesheets = [];

    /**
     * @var array<int,string>
     */
    private array $scripts = [];

    public function __construct(
        private BlockRepository $repository,
        private BlockRenderer $renderer,
        private \Monoverse\Services\LocaleService $localeService,
        private \Monoverse\Services\ContentTranslationService $contentTranslations
    ) {
    }

    public function renderArea(
        string $page,
        string $area,
        array $context = []
    ): string {
        $html = '';

        $blocks = $this->repository->findEnabledByArea(
            $page,
            $area
        );

        foreach ($blocks as $block) {
            $type = trim(
                (string) ($block['type'] ?? '')
            );

            if ($type === '') {
                continue;
            }

            foreach (
                $this->renderer->stylesheets($type)
                as $stylesheet
            ) {
                $this->addStylesheet($stylesheet);
            }

            foreach (
                $this->renderer->scripts($type)
                as $script
            ) {
                $this->addScript($script);
            }

            $settings = $this->decodeSettings(
                $block['settings'] ?? null
            );

            $currentLocale = $this->localeService->getCurrentLocale();
            $defaultLocale = $this->localeService->getDefaultLocale();

            if (
                $currentLocale !== ''
                && $currentLocale !== $defaultLocale
                && isset($block['id'])
            ) {
                $translatedTitle = $this->contentTranslations->get(
                    'block',
                    (int) $block['id'],
                    $currentLocale,
                    'title'
                );

                if (
                    $translatedTitle !== null
                    && trim($translatedTitle) !== ''
                ) {
                    $block['title'] = $translatedTitle;
                }
            }

            $blockContext = array_merge(
                $context,
                [
                    'block' => $block,
                    'page' => $page,
                    'area' => $area,
                ]
            );

            $html .= $this->renderer->render(
                $type,
                $settings,
                $blockContext
            );
        }

        return $html;
    }

    /**
     * @return array<int,string>
     */
    public function stylesheets(): array
    {
        return $this->stylesheets;
    }

    /**
     * @return array<int,string>
     */
    public function scripts(): array
    {
        return $this->scripts;
    }

    private function addStylesheet(
        string $stylesheet
    ): void {
        $stylesheet = trim($stylesheet);

        if ($stylesheet === '') {
            return;
        }

        if (!in_array(
            $stylesheet,
            $this->stylesheets,
            true
        )) {
            $this->stylesheets[] = $stylesheet;
        }
    }

    private function addScript(
        string $script
    ): void {
        $script = trim($script);

        if ($script === '') {
            return;
        }

        if (!in_array(
            $script,
            $this->scripts,
            true
        )) {
            $this->scripts[] = $script;
        }
    }

    private function decodeSettings(mixed $settings): array
    {
        if (
            !is_string($settings)
            || trim($settings) === ''
        ) {
            return [];
        }

        $decoded = json_decode(
            $settings,
            true
        );

        return is_array($decoded)
            ? $decoded
            : [];
    }
}