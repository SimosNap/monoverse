<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use RuntimeException;

class CategoryService
{
	public function __construct(
		private Database $database,
		private LocaleService $locales,
		private ContentTranslationService $translations
	) {
	}

	public function listAll(string $type): array
	{
		return $this->database->fetchAll(
			'SELECT *
			 FROM mv_categories
			 WHERE type = :type
			 ORDER BY sort_order ASC, name ASC',
			[
				'type' => $type,
			]
		);
	}

	public function listAllLocalized(
		string $type
	): array {
		$categories = $this->listAll($type);

		foreach ($categories as &$category) {
			$category = $this->localizeCategory(
				$category
			);
		}

		unset($category);

		return $categories;
	}

	public function listWithPublishedArticleCount(
		string $type
	): array {
		$categories = $this->database->fetchAll(
			'SELECT
				c.*,
				COUNT(a.id) AS article_count
			 FROM mv_categories c
			 LEFT JOIN mv_articles a
				ON a.category_id = c.id
			   AND a.status = :status
			   AND a.published_at IS NOT NULL
			 WHERE c.type = :type
			 GROUP BY c.id
			 ORDER BY c.sort_order ASC, c.name ASC',
			[
				'status' => 'published',
				'type' => $type,
			]
		);

		foreach ($categories as &$category) {
			$category = $this->localizeCategory(
				$category
			);
		}

		unset($category);

		return $categories;
	}

	public function findByUuid(string $uuid): ?array
	{
		$category = $this->database->fetchOne(
			'SELECT *
			 FROM mv_categories
			 WHERE uuid = :uuid
			 LIMIT 1',
			[
				'uuid' => $uuid,
			]
		);

		return $category ?: null;
	}

	public function findBySlug(
		string $type,
		string $slug
	): ?array {
		$category = $this->database->fetchOne(
			'SELECT *
			 FROM mv_categories
			 WHERE type = :type
			 AND slug = :slug
			 LIMIT 1',
			[
				'type' => $type,
				'slug' => $slug,
			]
		);

		return $category ?: null;
	}

	public function findLocalizedBySlug(
		string $type,
		string $slug
	): ?array {
		$category = $this->findBySlug(
			$type,
			$slug
		);

		if ($category === null) {
			return null;
		}

		return $this->localizeCategory(
			$category
		);
	}

	public function create(
		string $type,
		array $data
	): int {
		$this->database->insert(
			'mv_categories',
			[
				'uuid' => $this->uuid(),
				'type' => $type,
				'name' => trim(
					(string) ($data['name'] ?? '')
				),
				'description' => trim(
					(string) ($data['description'] ?? '')
				),
				'slug' => trim(
					(string) ($data['slug'] ?? '')
				),
				'sort_order' => (int) (
					$data['sort_order'] ?? 0
				),
			]
		);

		return (int) $this->database->lastInsertId();
	}

	public function update(
		string $uuid,
		array $data
	): void {
		$this->database->update(
			'mv_categories',
			[
				'name' => trim(
					(string) ($data['name'] ?? '')
				),
				'description' => trim(
					(string) ($data['description'] ?? '')
				),
				'slug' => trim(
					(string) ($data['slug'] ?? '')
				),
				'sort_order' => (int) (
					$data['sort_order'] ?? 0
				),
			],
			[
				'uuid' => $uuid,
			]
		);
	}

	public function delete(
		string $uuid
	): void {
		$category = $this->findByUuid($uuid);

		if ($category === null) {
			throw new RuntimeException(
				'Categoria non trovata.'
			);
		}

		$this->database->delete(
			'mv_categories',
			[
				'id' => $category['id'],
			]
		);
	}

	public function slugExists(
		string $type,
		string $slug,
		?string $excludeUuid = null
	): bool {
		$sql =
			'SELECT id
			 FROM mv_categories
			 WHERE type = :type
			 AND slug = :slug';

		$params = [
			'type' => $type,
			'slug' => $slug,
		];

		if ($excludeUuid !== null) {
			$sql .= ' AND uuid <> :uuid';
			$params['uuid'] = $excludeUuid;
		}

		$sql .= ' LIMIT 1';

		return $this->database->fetchOne(
			$sql,
			$params
		) !== false;
	}

	private function localizeCategory(
		array $category
	): array {
		$categoryId = (int) ($category['id'] ?? 0);

		if ($categoryId <= 0) {
			return $category;
		}

		$currentLocale = $this->locales->getCurrentLocale();
		$defaultLocale = $this->locales->getDefaultLocale();

		if ($currentLocale === $defaultLocale) {
			return $category;
		}

		foreach (
			[
				'name',
				'description',
			] as $field
		) {
			$translatedValue = trim(
				(string) (
					$this->translations->get(
						'category',
						$categoryId,
						$currentLocale,
						$field
					)
					?? ''
				)
			);

			if ($translatedValue !== '') {
				$category[$field] = $translatedValue;
			}
		}

		return $category;
	}

	private function uuid(): string
	{
		$data = random_bytes(16);

		$data[6] = chr(
			(ord($data[6]) & 0x0f) | 0x40
		);

		$data[8] = chr(
			(ord($data[8]) & 0x3f) | 0x80
		);

		return vsprintf(
			'%s%s-%s-%s-%s-%s%s%s',
			str_split(
				bin2hex($data),
				4
			)
		);
	}
}
