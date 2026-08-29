<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class ArticleService
{
	public function __construct(
		private Database $database
	) {
	}

	public function findByUuid(string $uuid): array|false
	{
		return $this->database->fetchOne(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.uuid = ?
			LIMIT 1
			',
			[
				'chanzine',
				$uuid,
			]
		);
	}

	public function findEditableSubmissionByUser(
		string $uuid,
		string $sub
	): array|false {
		return $this->database->fetchOne(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.uuid = ?
			  AND a.submitted_by_sub = ?
			  AND a.status = ?
			LIMIT 1
			',
			[
				'chanzine',
				$uuid,
				$sub,
				'submitted',
			]
		);
	}

	public function findPublishedBySlug(string $slug): array|false
	{
		return $this->database->fetchOne(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.slug = ?
			  AND a.status = ?
			  AND a.published_at IS NOT NULL
			LIMIT 1
			',
			[
				'chanzine',
				$slug,
				'published',
			]
		);
	}

	public function listAll(
		int $limit = 50,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		return $this->database->fetchAll(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			   WHERE a.status IN (\'draft\', \'published\')
			ORDER BY a.created_at DESC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				'chanzine',
			]
		);
	}

	public function listSubmitted(
		int $limit = 50,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		return $this->database->fetchAll(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.status = ?
			ORDER BY a.submitted_at ASC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				'chanzine',
				'submitted',
			]
		);
	}

	public function listSubmittedByUser(
		string $sub,
		int $limit = 50,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		return $this->database->fetchAll(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.submitted_by_sub = ?
			ORDER BY a.submitted_at DESC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				'chanzine',
				$sub,
			]
		);
	}

	public function listPublished(
		int $limit = 10,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		return $this->database->fetchAll(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.status = ?
			  AND a.published_at IS NOT NULL
			ORDER BY a.published_at DESC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				'chanzine',
				'published',
			]
		);
	}

	public function listPublishedByCategory(
		int $categoryId,
		int $limit = 10,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		return $this->database->fetchAll(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.category_id = ?
			  AND a.status = ?
			  AND a.published_at IS NOT NULL
			ORDER BY a.published_at DESC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				'chanzine',
				$categoryId,
				'published',
			]
		);
	}

	public function searchPublished(
		string $query,
		?int $categoryId = null,
		int $limit = 20,
		int $offset = 0
	): array {
		$query = trim($query);

		if ($query === '') {
			return [];
		}

		$limit = max(1, $limit);
		$offset = max(0, $offset);

		$categoryFilter = '';
		$params = [
			'chanzine',
			'published',
		];

		if ($categoryId !== null) {
			$categoryFilter = '
				AND a.category_id = ?
			';

			$params[] = $categoryId;
		}

		$search = '%' . $query . '%';

		$params[] = $search;
		$params[] = $search;
		$params[] = $search;

		return $this->database->fetchAll(
			'
			SELECT
				a.*,
				c.name AS category_name,
				c.slug AS category_slug
			FROM mv_articles a
			LEFT JOIN mv_categories c
				ON c.id = a.category_id
			   AND c.type = ?
			WHERE a.status = ?
			  AND a.published_at IS NOT NULL
			  ' . $categoryFilter . '
			  AND (
					a.title LIKE ?
					OR a.excerpt LIKE ?
					OR a.content LIKE ?
			  )
			ORDER BY a.published_at DESC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			$params
		);
	}

	public function listPublishedForSitemap(): array
	{
		return $this->database->fetchAll(
			'
			SELECT
				slug,
				published_at,
				updated_at
			FROM mv_articles
			WHERE status = ?
			  AND published_at IS NOT NULL
			ORDER BY published_at DESC
			',
			[
				'published',
			]
		);
	}

	public function create(array $data): bool
	{
		$now = date('Y-m-d H:i:s');

		return $this->database->execute(
			'
			INSERT INTO mv_articles
			(
				uuid,
				title,
				slug,
				excerpt,
				content,
				cover,
				category_id,
				status,
				published_at,
				created_at,
				updated_at
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			',
			[
				Uuid::v4(),
				$data['title'],
				$data['slug'],
				$data['excerpt'] ?? null,
				$data['content'],
				$data['cover'] ?? null,
				$data['category_id'] ?? null,
				'draft',
				null,
				$now,
				$now,
			]
		);
	}

	public function createSubmission(array $data): bool
	{
		$now = date('Y-m-d H:i:s');

		return $this->database->execute(
			'
			INSERT INTO mv_articles
			(
				uuid,
				title,
				slug,
				excerpt,
				content,
				cover,
				category_id,
				status,
				published_at,
				submitted_by_sub,
				submitted_by_nickname,
				submitted_at,
				rejected_at,
				created_at,
				updated_at
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			',
			[
				Uuid::v4(),
				$data['title'],
				$data['slug'],
				$data['excerpt'] ?? null,
				$data['content'],
				$data['cover'] ?? null,
				$data['category_id'] ?? null,
				'submitted',
				null,
				$data['submitted_by_sub'],
				$data['submitted_by_nickname'] ?? null,
				$now,
				null,
				$now,
				$now,
			]
		);
	}

	public function update(
		string $uuid,
		array $data
	): bool {
		return $this->database->execute(
			'
			UPDATE mv_articles
			SET title = ?,
				slug = ?,
				excerpt = ?,
				content = ?,
				cover = ?,
				category_id = ?,
				updated_at = ?
			WHERE uuid = ?
			',
			[
				$data['title'],
				$data['slug'],
				$data['excerpt'] ?? null,
				$data['content'],
				$data['cover'] ?? null,
				$data['category_id'] ?? null,
				date('Y-m-d H:i:s'),
				$uuid,
			]
		);
	}

	public function updateSubmissionByUser(
		string $uuid,
		string $sub,
		array $data
	): bool {
		return $this->database->execute(
			'
			UPDATE mv_articles
			SET title = ?,
				slug = ?,
				excerpt = ?,
				content = ?,
				cover = ?,
				category_id = ?,
				updated_at = ?
			WHERE uuid = ?
			  AND submitted_by_sub = ?
			  AND status = ?
			',
			[
				$data['title'],
				$data['slug'],
				$data['excerpt'] ?? null,
				$data['content'],
				$data['cover'] ?? null,
				$data['category_id'] ?? null,
				date('Y-m-d H:i:s'),
				$uuid,
				$sub,
				'submitted',
			]
		);
	}

	public function publish(string $uuid): bool
	{
		return $this->database->execute(
			'
			UPDATE mv_articles
			SET status = ?,
				published_at = COALESCE(published_at, ?),
				rejected_at = NULL,
				rejection_reason = NULL,
				updated_at = ?
			WHERE uuid = ?
			',
			[
				'published',
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				$uuid,
			]
		);
	}

	public function moveToDraft(string $uuid): bool
	{
		return $this->database->execute(
			'
			UPDATE mv_articles
			SET status = ?,
				updated_at = ?
			WHERE uuid = ?
			',
			[
				'draft',
				date('Y-m-d H:i:s'),
				$uuid,
			]
		);
	}

	public function reject(
		string $uuid,
		string $reason
	): bool {
		return $this->database->execute(
			'
			UPDATE mv_articles
			SET status = ?,
				rejected_at = ?,
				rejection_reason = ?,
				updated_at = ?
			WHERE uuid = ?
			',
			[
				'rejected',
				date('Y-m-d H:i:s'),
				$reason,
				date('Y-m-d H:i:s'),
				$uuid,
			]
		);
	}

	public function delete(string $uuid): bool
	{
		return $this->database->execute(
			'
			DELETE FROM mv_articles
			WHERE uuid = ?
			',
			[
				$uuid,
			]
		);
	}

	public function slugExists(
		string $slug,
		?string $excludeUuid = null
	): bool {
		if ($excludeUuid !== null) {
			$article = $this->database->fetchOne(
				'
				SELECT id
				FROM mv_articles
				WHERE slug = ?
				  AND uuid != ?
				LIMIT 1
				',
				[
					$slug,
					$excludeUuid,
				]
			);

			return $article !== false;
		}

		$article = $this->database->fetchOne(
			'
			SELECT id
			FROM mv_articles
			WHERE slug = ?
			LIMIT 1
			',
			[
				$slug,
			]
		);

		return $article !== false;
	}

	public function setPingUuid(
		string $uuid,
		string $pingUuid
	): bool {
		return $this->database->execute(
			'
			UPDATE mv_articles
			SET ping_uuid = ?,
				updated_at = ?
			WHERE uuid = ?
			',
			[
				$pingUuid,
				date('Y-m-d H:i:s'),
				$uuid,
			]
		);
	}
}
