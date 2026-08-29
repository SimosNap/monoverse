<?php
declare(strict_types=1);

namespace Monoverse\Installer;

class EditionManager
{
    private string $editionsPath;

    public function __construct(string $editionsPath)
    {
        $this->editionsPath = rtrim($editionsPath, '/');
    }

    /**
     * @return Edition[]
     */
    public function all(): array
    {
        $editions = [];

        foreach (glob($this->editionsPath . '/*/distribution.json') as $file) {
            $data = json_decode((string) file_get_contents($file), true);

            if (!is_array($data)) {
                continue;
            }

            $key = basename(dirname($file));

            $editions[] = new Edition(
                strtolower($data['key'] ?? $key),
                (string) ($data['name'] ?? $key),
                (string) ($data['description'] ?? ''),
                (string) ($data['version'] ?? '0.1.0'),
                (string) ($data['status'] ?? 'available')
            );
        }

        return $editions;
    }
}

