<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

use Monoverse\Core\View;

final class BlockRenderer
{
    public function __construct(
        private BlockRegistry $registry,
        private View $view
    ) {
    }

    public function render(
        string $type,
        array $settings = [],
        array $context = []
    ): string {
        $block = $this->registry->get($type);

        if ($block === null) {
            return '';
        }

        return $this->view->partial(
            $block->template(),
            array_merge(
                $context,
                $block->data(
                    $settings,
                    $context
                )
            )
        );
    }

    /**
     * @return array<int,string>
     */
    public function stylesheets(string $type): array
    {
        $block = $this->registry->get($type);

        if ($block === null) {
            return [];
        }

        $stylesheets = [];

        foreach ($block->stylesheets() as $stylesheet) {
            $stylesheet = trim(
                (string) $stylesheet
            );

            if (
                $stylesheet !== ''
                && !in_array(
                    $stylesheet,
                    $stylesheets,
                    true
                )
            ) {
                $stylesheets[] = $stylesheet;
            }
        }

        return $stylesheets;
    }

    /**
     * @return array<int,string>
     */
    public function scripts(string $type): array
    {
        $block = $this->registry->get($type);

        if (
            $block === null
            || !method_exists($block, 'scripts')
        ) {
            return [];
        }

        $scripts = [];

        foreach ($block->scripts() as $script) {
            $script = trim(
                (string) $script
            );

            if (
                $script !== ''
                && !in_array(
                    $script,
                    $scripts,
                    true
                )
            ) {
                $scripts[] = $script;
            }
        }

        return $scripts;
    }
}
