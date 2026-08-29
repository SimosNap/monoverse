<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Entities\Edition;

class EditionService
{
    /**
     * @return Edition[]
     */
    public function all(): array
    {
        $editions = [];

        $directories = glob(
            __DIR__ . '/../../editions/*',
            GLOB_ONLYDIR
        );

        if ($directories === false) {
            return [];
        }

        foreach ($directories as $directory) {
            $manifest = $directory . '/edition.json';

            if (!is_file($manifest)) {
                continue;
            }

            $json = file_get_contents($manifest);

            if ($json === false) {
                continue;
            }

            $data = json_decode($json, true);

            if (!is_array($data)) {
                continue;
            }

            $editions[] = new Edition(
                id: $data['id'] ?? basename($directory),
                name: $data['name'] ?? basename($directory),
                description: $data['description'] ?? '',
                version: $data['version'] ?? '1.0.0',
                author: $data['author'] ?? '',
                website: $data['website'] ?? '',
                icon: $data['icon'] ?? '',
                modules: $data['modules'] ?? [],
                status: (string) ($data['status'] ?? 'available')
            );
        }

        return $editions;
    }
}
