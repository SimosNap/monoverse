<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class EventService
{
	public function __construct(
		private Database $database
	) {
	}

	public function findByUuid(string $uuid): array|false
	{
		return $this->database->fetchOne(
			'
			SELECT *
			FROM mv_events
			WHERE uuid = ?
			LIMIT 1
			',
			[
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
			SELECT *
			FROM mv_events
			WHERE uuid = ?
			  AND submitted_by_sub = ?
			  AND status = ?
			LIMIT 1
			',
			[
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
			SELECT *
			FROM mv_events
			WHERE slug = ?
			  AND status = ?
			  AND published_at IS NOT NULL
			LIMIT 1
			',
			[
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
			SELECT *
			FROM mv_events
			WHERE status IN (\'draft\', \'published\')
			ORDER BY starts_at DESC
			LIMIT ' . $limit . '
			OFFSET ' . $offset
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
			SELECT *
			FROM mv_events
			WHERE status = ?
			ORDER BY submitted_at ASC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
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
			SELECT *
			FROM mv_events
			WHERE submitted_by_sub = ?
			ORDER BY submitted_at DESC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				$sub,
			]
		);
	}

	public function listPublished(
		int $limit = 20,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		return $this->database->fetchAll(
			'
			SELECT *
			FROM mv_events
			WHERE status = ?
			  AND published_at IS NOT NULL
			ORDER BY starts_at ASC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				'published',
			]
		);
	}

	public function listUpcoming(
		int $limit = 10,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		return $this->database->fetchAll(
			'
			SELECT *
			FROM mv_events
			WHERE status = ?
			  AND published_at IS NOT NULL
			  AND (
					ends_at >= ?
					OR (ends_at IS NULL AND starts_at >= ?)
			  )
			ORDER BY starts_at ASC
			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			[
				'published',
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
			]
		);
	}

	public function create(array $data): bool
	{
		$now = date('Y-m-d H:i:s');

		return $this->database->execute(
			'
			INSERT INTO mv_events
			(
				uuid,
				title,
				slug,
				description,
				starts_at,
				ends_at,
				location,
				latitude,
				longitude,
				external_url,
				cover,
				status,
				published_at,
				created_at,
				updated_at
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			',
			[
				Uuid::v4(),
				$data['title'],
				$data['slug'],
				$data['description'],
				$data['starts_at'],
				$data['ends_at'] ?? null,
				$data['location'] ?? null,
				$data['latitude'] ?? null,
				$data['longitude'] ?? null,
				$data['external_url'] ?? null,
				$data['cover'] ?? null,
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
			INSERT INTO mv_events
			(
				uuid,
				title,
				slug,
				description,
				starts_at,
				ends_at,
				location,
				latitude,
				longitude,
				external_url,
				cover,
				status,
				published_at,
				submitted_by_sub,
				submitted_by_nickname,
				submitted_at,
				rejected_at,
				created_at,
				updated_at
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			',
			[
				Uuid::v4(),
				$data['title'],
				$data['slug'],
				$data['description'],
				$data['starts_at'],
				$data['ends_at'] ?? null,
				$data['location'] ?? null,
				$data['latitude'] ?? null,
				$data['longitude'] ?? null,
				$data['external_url'] ?? null,
				$data['cover'] ?? null,
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
			UPDATE mv_events
			SET title = ?,
				slug = ?,
				description = ?,
				starts_at = ?,
				ends_at = ?,
				location = ?,
				latitude = ?,
				longitude = ?,
				external_url = ?,
				cover = ?,
				updated_at = ?
			WHERE uuid = ?
			',
			[
				$data['title'],
				$data['slug'],
				$data['description'],
				$data['starts_at'],
				$data['ends_at'] ?? null,
				$data['location'] ?? null,
				$data['latitude'] ?? null,
				$data['longitude'] ?? null,
				$data['external_url'] ?? null,
				$data['cover'] ?? null,
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
			UPDATE mv_events
			SET title = ?,
				description = ?,
				starts_at = ?,
				ends_at = ?,
				location = ?,
				latitude = ?,
				longitude = ?,
				external_url = ?,
				cover = ?,
				updated_at = ?
			WHERE uuid = ?
			  AND submitted_by_sub = ?
			  AND status = ?
			',
			[
				$data['title'],
				$data['description'],
				$data['starts_at'],
				$data['ends_at'] ?? null,
				$data['location'] ?? null,
				$data['latitude'] ?? null,
				$data['longitude'] ?? null,
				$data['external_url'] ?? null,
				$data['cover'] ?? null,
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
			UPDATE mv_events
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
			UPDATE mv_events
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
			UPDATE mv_events
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
			DELETE FROM mv_events
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
			$event = $this->database->fetchOne(
				'
				SELECT id
				FROM mv_events
				WHERE slug = ?
				  AND uuid != ?
				LIMIT 1
				',
				[
					$slug,
					$excludeUuid,
				]
			);

			return $event !== false;
		}

		$event = $this->database->fetchOne(
			'
			SELECT id
			FROM mv_events
			WHERE slug = ?
			LIMIT 1
			',
			[
				$slug,
			]
		);

		return $event !== false;
	}
}
