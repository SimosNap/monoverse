<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use RuntimeException;

class PageService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function listAll(): array
    {
        return $this->database->fetchAll(
            'SELECT *
             FROM mv_pages
             ORDER BY created_at DESC, id DESC'
        );
    }

    public function listForAdmin(): array
    {
        $pages = $this->listAll();

        foreach ($pages as &$page) {
            $slug = (string) ($page['slug'] ?? '');

            $page['url'] = '/' . ltrim(
                $slug,
                '/'
            );

            $page['block_page_key'] = $this->blockPageKey(
                $slug
            );
        }

        unset($page);

        return $pages;
    }

    public function findById(
        int $id
    ): ?array {
        $page = $this->database->fetchOne(
            'SELECT *
             FROM mv_pages
             WHERE id = :id
             LIMIT 1',
            [
                'id' => $id,
            ]
        );

        return $page ?: null;
    }

    public function findBySlug(
        string $slug
    ): ?array {
        $page = $this->database->fetchOne(
            'SELECT *
             FROM mv_pages
             WHERE slug = :slug
             LIMIT 1',
            [
                'slug' => $slug,
            ]
        );

        return $page ?: null;
    }

    public function findPublishedBySlug(
        string $slug
    ): ?array {
        $page = $this->database->fetchOne(
            'SELECT *
             FROM mv_pages
             WHERE slug = :slug
             AND status = :status
             LIMIT 1',
            [
                'slug' => $slug,
                'status' => 'published',
            ]
        );

        return $page ?: null;
    }

    public function blockPageKey(
        string $slug
    ): string {
        return 'page:' . trim($slug);
    }

    public function create(
        array $data
    ): void {
        $this->database->insert(
            'mv_pages',
            [
                'title' => trim(
                    (string) ($data['title'] ?? '')
                ),
                'slug' => trim(
                    (string) ($data['slug'] ?? '')
                ),
                'content' => null,
                'status' => $this->normalizeStatus(
                    (string) (
                        $data['status']
                        ?? 'draft'
                    )
                ),
                'show_in_navigation' => !empty(
                    $data['show_in_navigation']
                )
                    ? 1
                    : 0,
                'menu_label' => $this->nullableString(
                    $data['menu_label'] ?? null
                ),
                'navigation_group' => $this->normalizeNavigationGroup(
                    $data['navigation_group'] ?? 'default'
                ),
                'sort_order' => max(
                    0,
                    (int) (
                        $data['sort_order']
                        ?? 0
                    )
                ),
                'meta_title' => $this->nullableString(
                    $data['meta_title'] ?? null
                ),
                'meta_description' => $this->nullableString(
                    $data['meta_description'] ?? null
                ),
                'created_by' => isset($data['created_by'])
                    ? (int) $data['created_by']
                    : null,
                'updated_by' => isset($data['updated_by'])
                    ? (int) $data['updated_by']
                    : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ]
        );
    }

    public function update(
        int $id,
        array $data
    ): void {
        $page = $this->findById($id);

        if ($page === null) {
            throw new RuntimeException(
                'Pagina non trovata.'
            );
        }

        $this->database->update(
            'mv_pages',
            [
                'title' => trim(
                    (string) ($data['title'] ?? '')
                ),
                'slug' => trim(
                    (string) ($data['slug'] ?? '')
                ),
                'status' => $this->normalizeStatus(
                    (string) (
                        $data['status']
                        ?? 'draft'
                    )
                ),
                'show_in_navigation' => !empty(
                    $data['show_in_navigation']
                )
                    ? 1
                    : 0,
                'menu_label' => $this->nullableString(
                    $data['menu_label'] ?? null
                ),
                'navigation_group' => $this->normalizeNavigationGroup(
                    $data['navigation_group'] ?? 'default'
                ),
                'sort_order' => max(
                    0,
                    (int) (
                        $data['sort_order']
                        ?? 0
                    )
                ),
                'meta_title' => $this->nullableString(
                    $data['meta_title'] ?? null
                ),
                'meta_description' => $this->nullableString(
                    $data['meta_description'] ?? null
                ),
                'updated_by' => isset($data['updated_by'])
                    ? (int) $data['updated_by']
                    : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $id,
            ]
        );
    }

    public function delete(
        int $id
    ): void {
        $page = $this->findById($id);

        if ($page === null) {
            throw new RuntimeException(
                'Pagina non trovata.'
            );
        }

        $this->database->delete(
            'mv_pages',
            [
                'id' => $id,
            ]
        );
    }

    public function slugExists(
        string $slug,
        ?int $excludeId = null
    ): bool {
        $sql =
            'SELECT id
             FROM mv_pages
             WHERE slug = :slug';

        $params = [
            'slug' => $slug,
        ];

        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $sql .= ' LIMIT 1';

        return $this->database->fetchOne(
            $sql,
            $params
        ) !== false;
    }
    
    public function navigationItems(
        string $group = 'default'
    ): array {
        return $this->database->fetchAll(
            'SELECT
                title,
                slug,
                menu_label
            FROM mv_pages
            WHERE
                status = :status
                AND show_in_navigation = 1
                AND navigation_group = :navigation_group
            ORDER BY
                sort_order ASC,
                title ASC',
            [
                'status' => 'published',
                'navigation_group' => $group,
            ]
        );
    }

    private function normalizeStatus(
        string $status
    ): string {
        return in_array(
            $status,
            [
                'draft',
                'published',
                'private',
            ],
            true
        )
            ? $status
            : 'draft';
    }

    private function normalizeNavigationGroup(
        mixed $value
    ): string {
        if (!is_scalar($value)) {
            return 'default';
        }

        $value = trim(
            strtolower(
                (string) $value
            )
        );

        if ($value === '') {
            return 'default';
        }

        $value = preg_replace(
            '/[^a-z0-9_-]+/',
            '-',
            $value
        );

        $value = trim(
            (string) $value,
            '-'
        );

        return $value !== ''
            ? $value
            : 'default';
    }

    private function nullableString(
        mixed $value
    ): ?string {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value !== ''
            ? $value
            : null;
    }
}
