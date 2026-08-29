<?php
declare(strict_types=1);

namespace Monoverse\Core;

use PDO;
use PDOException;

class Database
{
    private array $config;
    private ?PDO $pdo = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function pdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $this->config['host'],
            $this->config['database'],
            $this->config['charset'] ?? 'utf8mb4'
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode()
            );
        }

        return $this->pdo;
    }

    public function fetchOne(
        string $sql,
        array $params = []
    ): array|false {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch();
    }

    public function fetchAll(
        string $sql,
        array $params = []
    ): array {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function execute(
        string $sql,
        array $params = []
    ): bool {
        $stmt = $this->pdo()->prepare($sql);

        return $stmt->execute($params);
    }

    public function insert(
        string $table,
        array $data
    ): bool {
        if ($data === []) {
            return false;
        }

        $columns = array_keys($data);

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(
                ', ',
                array_map(
                    static fn(string $column): string => "`{$column}`",
                    $columns
                )
            ),
            implode(
                ', ',
                array_map(
                    static fn(string $column): string => ':' . $column,
                    $columns
                )
            )
        );

        return $this->execute(
            $sql,
            $data
        );
    }

    public function update(
        string $table,
        array $data,
        array $where
    ): bool {
        if (
            $data === []
            || $where === []
        ) {
            return false;
        }

        $set = [];
        $params = [];

        foreach ($data as $column => $value) {
            $set[] = "`{$column}` = :set_{$column}";
            $params["set_{$column}"] = $value;
        }

        $conditions = [];

        foreach ($where as $column => $value) {
            $conditions[] = "`{$column}` = :where_{$column}";
            $params["where_{$column}"] = $value;
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $set),
            implode(' AND ', $conditions)
        );

        return $this->execute(
            $sql,
            $params
        );
    }

    public function delete(
        string $table,
        array $where
    ): bool {
        if ($where === []) {
            return false;
        }

        $conditions = [];
        $params = [];

        foreach ($where as $column => $value) {
            $conditions[] = "`{$column}` = :where_{$column}";
            $params["where_{$column}"] = $value;
        }

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s',
            $table,
            implode(' AND ', $conditions)
        );

        return $this->execute(
            $sql,
            $params
        );
    }

    public function lastInsertId(): string
    {
        return $this->pdo()->lastInsertId();
    }
}
