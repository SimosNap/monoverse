<?php
declare(strict_types=1);

namespace Monoverse\Models;

use PDO;

class Setting
{
    private PDO $pdo;
    private array $cache = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        if (!empty($this->cache)) {
            return $this->cache;
        }

        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM mv_settings");

        foreach ($stmt->fetchAll() as $row) {
            $this->cache[$row['setting_key']] = $row['setting_value'];
        }

        return $this->cache;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }
}