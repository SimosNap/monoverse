<?php
declare(strict_types=1);

namespace Monoverse\Services;

class ThemeService
{
    public function __construct(
        private readonly string $themesPath
    ) {
    }

    public function all(): array
    {
        if (!is_dir($this->themesPath)) {
            return [];
        }

        $themes = [];

        foreach (scandir($this->themesPath) ?: [] as $entry) {
            if (
                $entry === '.'
                || $entry === '..'
                || str_starts_with($entry, '.')
            ) {
                continue;
            }

            if (
                !is_dir(
                    $this->themesPath
                    . DIRECTORY_SEPARATOR
                    . $entry
                )
            ) {
                continue;
            }

            $themes[] = $entry;
        }

        natcasesort($themes);

        $themes = array_values($themes);

        if (
            ($defaultKey = array_search(
                'default',
                $themes,
                true
            )) !== false
        ) {
            unset($themes[$defaultKey]);

            array_unshift(
                $themes,
                'default'
            );

            $themes = array_values($themes);
        }

        return $themes;
    }

    public function exists(string $theme): bool
    {
        return in_array(
            $theme,
            $this->all(),
            true
        );
    }

    public function resolve(string $theme): string
    {
        if ($this->exists($theme)) {
            return $theme;
        }

        return 'default';
    }
}
