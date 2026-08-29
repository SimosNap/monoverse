<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class NotificationService
{
	public function __construct(
		private Database $database
	) {
	}

	public function createReplyNotification(
		string $recipientSub,
		string $actorSub,
		string $postUuid
	): bool {
		if ($recipientSub === $actorSub) {
			return true;
		}

		return $this->create(
			$recipientSub,
			$actorSub,
			'reply',
			'post',
			$postUuid
		);
	}

	public function createMentionNotification(
		string $recipientSub,
		string $actorSub,
		string $objectType,
		string $objectUuid
	): bool {
		if ($recipientSub === $actorSub) {
			return true;
		}

		return $this->create(
			$recipientSub,
			$actorSub,
			'mention',
			$objectType,
			$objectUuid
		);
	}

	public function createUpvoteNotification(
		string $recipientSub,
		string $actorSub,
		string $postUuid
	): bool {
		if ($recipientSub === $actorSub) {
			return true;
		}

		return $this->create(
			$recipientSub,
			$actorSub,
			'upvote',
			'post',
			$postUuid
		);
	}

	public function createSystemNotification(
		string $recipientSub,
		string $objectType,
		string $objectUuid
	): bool {
		return $this->create(
			$recipientSub,
			'system',
			'system',
			$objectType,
			$objectUuid
		);
	}
	
	public function createReportNotification(
		string $recipientSub,
		string $actorSub,
		string $reportUuid
	): bool {
		if ($recipientSub === $actorSub) {
			return true;
		}
	
		return $this->create(
			$recipientSub,
			$actorSub,
			'report',
			'report',
			$reportUuid
		);
	}
	
	public function createDogeTipNotification(
		string $recipientSub,
		string $actorSub,
		string $txId,
		string $amount
	): bool {
		if ($recipientSub === $actorSub) {
			return true;
		}
	
		return $this->create(
			$recipientSub,
			$actorSub,
			'doge_tip',
			'doge_transaction',
			$txId,
			[
				'amount' => $amount,
			]
		);
	}

	public function listForUser(
		string $recipientSub,
		int $limit = 50
	): array {
		$limit = max(1, min($limit, 100));
	
		return $this->database->fetchAll(
			'
			SELECT
				n.id,
				n.uuid,
				n.metadata,
				n.recipient_sub,
				n.actor_sub,
				n.type,
				n.object_type,
				n.object_uuid,
				n.is_read,
				n.created_at,
				n.read_at,
	
				p.id AS post_id,
				p.author_sub AS post_author_sub,
				p.content AS post_content,
				p.status AS post_status,
				p.published_at AS post_published_at,
				p.deleted_at AS post_deleted_at
	
			FROM community_notifications AS n
	
			LEFT JOIN community_posts AS p
				ON n.object_type = \'post\'
				AND p.uuid = n.object_uuid
	
			WHERE n.recipient_sub = ?
	
			ORDER BY
				n.created_at DESC,
				n.id DESC
	
			LIMIT ' . $limit,
			[
				$recipientSub,
			]
		);
	}	

	private function create(
		string $recipientSub,
		string $actorSub,
		string $type,
		string $objectType,
		string $objectUuid,
		?array $metadata = null
	): bool {
		$now = date('Y-m-d H:i:s');
	
		$metadataJson = $metadata !== null
			? json_encode(
				$metadata,
				JSON_UNESCAPED_UNICODE
				| JSON_UNESCAPED_SLASHES
			)
			: null;
	
		return $this->database->execute(
			'
			INSERT INTO community_notifications
			(
				uuid,
				recipient_sub,
				actor_sub,
				type,
				object_type,
				object_uuid,
				metadata,
				is_read,
				created_at
			)
			VALUES
			(
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				0,
				?
			)
			',
			[
				Uuid::v4(),
				$recipientSub,
				$actorSub,
				$type,
				$objectType,
				$objectUuid,
				$metadataJson,
				$now,
			]
		);
	}
	
	public function deleteByObject(
		string $objectType,
		string $objectUuid
	): bool {
		return $this->database->execute(
			'
			DELETE FROM community_notifications
			WHERE object_type = ?
			  AND object_uuid = ?
			',
			[
				$objectType,
				$objectUuid,
			]
		);
	}
	
	public function delete(
		string $uuid,
		string $recipientSub
	): bool {
		return $this->database->execute(
			'
			DELETE FROM community_notifications
			WHERE uuid = ?
			  AND recipient_sub = ?
			',
			[
				$uuid,
				$recipientSub,
			]
		);
	}
	
	public function deleteAll(string $recipientSub): bool
	{
		return $this->database->execute(
			'
			DELETE FROM community_notifications
			WHERE recipient_sub = ?
			',
			[
				$recipientSub,
			]
		);
	}
	
	public function countUnread(string $recipientSub): int
	{
		$row = $this->database->fetchOne(
			'
			SELECT COUNT(*) AS total
			FROM community_notifications
			WHERE recipient_sub = ?
			  AND is_read = 0
			',
			[
				$recipientSub,
			]
		);
	
		return (int) ($row['total'] ?? 0);
	}
	
	public function markAllAsRead(string $recipientSub): bool
	{
		$now = date('Y-m-d H:i:s');
	
		return $this->database->execute(
			'
			UPDATE community_notifications
			SET
				is_read = 1,
				read_at = ?
			WHERE recipient_sub = ?
			  AND is_read = 0
			',
			[
				$now,
				$recipientSub,
			]
		);
	}
}
