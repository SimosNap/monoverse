<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class SavedItemService
{
	public function __construct(
		private Database $database
	) {
	}

	public function save(
		string $userSub,
		string $objectType,
		string $objectUuid
	): bool {
		if ($this->isSaved($userSub, $objectType, $objectUuid)) {
			return true;
		}

		return $this->database->execute(
			'
			INSERT INTO community_saved_items
			(
				uuid,
				user_sub,
				object_type,
				object_uuid,
				created_at
			)
			VALUES
			(
				?, ?, ?, ?, ?
			)
			',
			[
				Uuid::v4(),
				$userSub,
				$objectType,
				$objectUuid,
				date('Y-m-d H:i:s'),
			]
		);
	}

	public function remove(
		string $userSub,
		string $objectType,
		string $objectUuid
	): bool {
		return $this->database->execute(
			'
			DELETE FROM community_saved_items
			WHERE user_sub = ?
			  AND object_type = ?
			  AND object_uuid = ?
			',
			[
				$userSub,
				$objectType,
				$objectUuid,
			]
		);
	}

	public function isSaved(
		string $userSub,
		string $objectType,
		string $objectUuid
	): bool {
		return $this->database->fetchOne(
			'
			SELECT id
			FROM community_saved_items
			WHERE user_sub = ?
			  AND object_type = ?
			  AND object_uuid = ?
			LIMIT 1
			',
			[
				$userSub,
				$objectType,
				$objectUuid,
			]
		) !== false;
	}

	public function findByUser(string $userSub): array
	{
		return $this->database->fetchAll(
			'
			SELECT
				uuid,
				object_type,
				object_uuid,
				created_at
			FROM community_saved_items
			WHERE user_sub = ?
			ORDER BY created_at DESC
			',
			[
				$userSub,
			]
		);
	}
}