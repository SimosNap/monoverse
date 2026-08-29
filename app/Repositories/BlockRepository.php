<?php
declare(strict_types=1);

namespace Monoverse\Repositories;

use Monoverse\Core\Database;

final class BlockRepository
{
    public function __construct(
        private Database $database
    ) {
    }

    public function findById(int $id): ?array
    {
        return $this->database->fetchOne(
            '
                SELECT *
                FROM mv_blocks
                WHERE id = :id
                LIMIT 1
            ',
            [
                'id' => $id,
            ]
        );
    }

    public function findByArea(
        string $page,
        string $area
    ): array {
        return $this->database->fetchAll(
            '
                SELECT *
                FROM mv_blocks
                WHERE page = :page
                  AND area = :area
                ORDER BY position ASC, id ASC
            ',
            [
                'page' => $page,
                'area' => $area,
            ]
        );
    }

    public function findEnabledByArea(
        string $page,
        string $area
    ): array {
        return $this->database->fetchAll(
            '
                SELECT *
                FROM mv_blocks
                WHERE page = :page
                  AND area = :area
                  AND enabled = 1
                ORDER BY position ASC, id ASC
            ',
            [
                'page' => $page,
                'area' => $area,
            ]
        );
    }

    public function nextPosition(
        string $page,
        string $area
    ): int {
        $row = $this->database->fetchOne(
            '
                SELECT MAX(position) AS position
                FROM mv_blocks
                WHERE page = :page
                  AND area = :area
            ',
            [
                'page' => $page,
                'area' => $area,
            ]
        );

        return ((int) ($row['position'] ?? 0)) + 10;
    }

    public function create(array $data): int
    {
        $this->database->execute(
            '
                INSERT INTO mv_blocks (
                    page,
                    area,
                    type,
                    name,
                    title,
                    settings,
                    width,
                    position,
                    enabled
                )
                VALUES (
                    :page,
                    :area,
                    :type,
                    :name,
                    :title,
                    :settings,
                    :width,
                    :position,
                    :enabled
                )
            ',
            $data
        );

        return (int) $this->database->lastInsertId();
    }

    public function update(
        int $id,
        array $data
    ): void {
        $data['id'] = $id;

        $this->database->execute(
            '
                UPDATE mv_blocks
                SET
                    name = :name,
                    title = :title,
                    settings = :settings,
                    width = :width,
                    enabled = :enabled
                WHERE id = :id
            ',
            $data
        );
    }

    public function setEnabled(
        int $id,
        bool $enabled
    ): void {
        $this->database->execute(
            '
                UPDATE mv_blocks
                SET enabled = :enabled
                WHERE id = :id
            ',
            [
                'id' => $id,
                'enabled' => $enabled ? 1 : 0,
            ]
        );
    }

    public function reorder(
        string $page,
        string $area,
        array $ids
    ): void {
        $position = 10;

        foreach ($ids as $id) {
            $id = (int) $id;

            if ($id <= 0) {
                continue;
            }

            $this->database->execute(
                '
                    UPDATE mv_blocks
                    SET position = :position
                    WHERE id = :id
                      AND page = :page
                      AND area = :area
                ',
                [
                    'id' => $id,
                    'page' => $page,
                    'area' => $area,
                    'position' => $position,
                ]
            );

            $position += 10;
        }
    }

    public function renamePage(
        string $oldPage,
        string $newPage
    ): void {
        $this->database->execute(
            '
                UPDATE mv_blocks
                SET page = :new_page
                WHERE page = :old_page
            ',
            [
                'new_page' => $newPage,
                'old_page' => $oldPage,
            ]
        );
    }

    public function deleteByPage(
        string $page
    ): void {
        $this->database->execute(
            '
                DELETE FROM mv_blocks
                WHERE page = :page
            ',
            [
                'page' => $page,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->database->execute(
            '
                DELETE FROM mv_blocks
                WHERE id = :id
            ',
            [
                'id' => $id,
            ]
        );
    }
}
