<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class ContentTranslationService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function get(
        string $entityType,
        int $entityId,
        string $locale,
        string $field
    ): ?string {
        $row = $this->database->fetchOne(
            'SELECT value
             FROM mv_content_translations
             WHERE entity_type = :entity_type
               AND entity_id = :entity_id
               AND locale = :locale
               AND field = :field
             LIMIT 1',
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'locale' => $locale,
                'field' => $field,
            ]
        );
        
        if (!is_array($row)) {
            return null;
        }
        
        return (string) ($row['value'] ?? '');
    }

    public function getAllForEntity(
        string $entityType,
        int $entityId
    ): array {
        $rows = $this->database->fetchAll(
            'SELECT locale, field, value
             FROM mv_content_translations
             WHERE entity_type = :entity_type
               AND entity_id = :entity_id
             ORDER BY locale ASC, field ASC',
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]
        );

        $translations = [];

        foreach ($rows as $row) {
            $locale = (string) $row['locale'];
            $field = (string) $row['field'];

            $translations[$locale][$field] =
                (string) ($row['value'] ?? '');
        }

        return $translations;
    }

    public function set(
        string $entityType,
        int $entityId,
        string $locale,
        string $field,
        ?string $value
    ): void {
        $value = trim((string) $value);

        if ($value === '') {
            $this->delete(
                $entityType,
                $entityId,
                $locale,
                $field
            );

            return;
        }

        $existing = $this->database->fetchOne(
            'SELECT id
             FROM mv_content_translations
             WHERE entity_type = :entity_type
               AND entity_id = :entity_id
               AND locale = :locale
               AND field = :field
             LIMIT 1',
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'locale' => $locale,
                'field' => $field,
            ]
        );

        if (is_array($existing)) {
            $this->database->update(
                'mv_content_translations',
                [
                    'value' => $value,
                ],
                [
                    'id' => (int) ($existing['id'] ?? 0),
                ]
            );

            return;
        }

        $this->database->insert(
            'mv_content_translations',
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'locale' => $locale,
                'field' => $field,
                'value' => $value,
            ]
        );
    }

    public function delete(
        string $entityType,
        int $entityId,
        string $locale,
        string $field
    ): void {
        $this->database->delete(
            'mv_content_translations',
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'locale' => $locale,
                'field' => $field,
            ]
        );
    }

    public function deleteEntity(
        string $entityType,
        int $entityId
    ): void {
        $this->database->delete(
            'mv_content_translations',
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]
        );
    }
}
