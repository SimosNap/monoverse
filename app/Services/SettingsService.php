<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class SettingsService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $row = $this->database->fetchOne(
            'SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1',
            [$key]
        );

        if (!$row) {
            return $default;
        }

        return $row['setting_value'];
    }

    public function set(string $key, mixed $value): void
    {
        $now = time();

        $this->database->execute(
            'INSERT INTO settings (setting_key, setting_value, created_at, updated_at)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                updated_at = VALUES(updated_at)',
            [
                $key,
                (string) $value,
                $now,
                $now,
            ]
        );
    }

    public function all(): array
    {
        $rows = $this->database->fetchAll(
            'SELECT setting_key, setting_value FROM settings ORDER BY setting_key ASC'
        );

        $settings = [];

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }
}