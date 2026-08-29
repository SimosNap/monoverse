<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class CommentService
{
	public function __construct(
		private Database $database,
		private NotificationService $notifications,
		private MentionService $mentions,
		private BlockService $blocks
	) {
	}

	public function create(array $data): bool
	{
		$postId = (int) $data['post_id'];
		$authorSub = (string) $data['author_sub'];
	
		$post = $this->database->fetchOne(
			'
			SELECT
				uuid,
				author_sub
			FROM community_posts
			WHERE id = ?
			  AND status = ?
			  AND deleted_at IS NULL
			LIMIT 1
			',
			[
				$postId,
				'published'
			]
		);
	
		if (!$post) {
			return false;
		}
	
		$postAuthorSub = (string) $post['author_sub'];
	
		if ($this->blocks->isEitherBlocked(
			$authorSub,
			$postAuthorSub
		)) {
			return false;
		}
	
		$uuid = Uuid::v4();
		$now = date('Y-m-d H:i:s');
	
		$metadata = null;
	
		if (
			isset($data['metadata'])
			&& is_array($data['metadata'])
		) {
			$metadata = json_encode(
				$data['metadata'],
				JSON_UNESCAPED_UNICODE
				| JSON_UNESCAPED_SLASHES
			);
		}
	
		$created = $this->database->execute(
			'
			INSERT INTO community_post_comments
			(
				uuid,
				post_id,
				author_sub,
				content,
				status,
				created_at,
				updated_at,
				metadata
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?)
			',
			[
				$uuid,
				$postId,
				$authorSub,
				$data['content'],
				'published',
				$now,
				$now,
				$metadata,
			]
		);
	
		if (!$created) {
			return false;
		}
	
		$this->notifications->createReplyNotification(
			$postAuthorSub,
			$authorSub,
			(string) $post['uuid']
		);
	
		$this->mentions->notifyMentions(
			$data['content'],
			$authorSub,
			'comment',
			$uuid
		);
	
		return true;
	}

	public function findByUuid(string $uuid): ?array
	{
		$comment = $this->database->fetchOne(
			'
			SELECT
				c.*,
				p.uuid AS post_uuid
			FROM community_post_comments c
			INNER JOIN community_posts p
				ON p.id = c.post_id
			WHERE c.uuid = ?
			  AND c.deleted_at IS NULL
			LIMIT 1
			',
			[
				$uuid
			]
		);

		return $comment ?: null;
	}

	public function update(string $uuid, string $content): bool
	{
		return $this->database->execute(
			'
			UPDATE community_post_comments
			SET content = ?,
				updated_at = ?
			WHERE uuid = ?
			  AND deleted_at IS NULL
			',
			[
				$content,
				date('Y-m-d H:i:s'),
				$uuid,
			]
		);
	}

	public function delete(string $uuid): bool
	{
		$this->notifications->deleteByObject(
			'comment',
			$uuid
		);

		return $this->database->execute(
			'
			DELETE FROM community_post_comments
			WHERE uuid = ?
			',
			[
				$uuid
			]
		);
	}

	public function listByPostId(
		int $postId,
		?string $viewerSub = null,
		int $limit = 20,
		int $offset = 0
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);
	
		if ($viewerSub === null || $viewerSub === '') {
			return $this->database->fetchAll(
				'
				SELECT
					c.*,
					pr.username,
					pr.nickname,
					pr.avatar_url,
					pr.show_avatar,
					pr.public_profile,
					pr.external_account_exists
				FROM community_post_comments c
				LEFT JOIN profiles pr
					ON pr.oauth_sub = c.author_sub
				WHERE c.post_id = ?
				  AND c.status = ?
				  AND c.deleted_at IS NULL
				ORDER BY c.created_at ASC
				LIMIT ' . $limit . '
				OFFSET ' . $offset . '
				',
				[
					$postId,
					'published'
				]
			);
		}
	
		return $this->database->fetchAll(
			'
			SELECT
				c.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.external_account_exists
			FROM community_post_comments c
			LEFT JOIN profiles pr
				ON pr.oauth_sub = c.author_sub
			WHERE c.post_id = ?
			  AND c.status = ?
			  AND c.deleted_at IS NULL
			  AND NOT EXISTS (
					SELECT 1
					FROM user_blocks ub
					WHERE (
						ub.blocker_sub = ?
						AND ub.blocked_sub = c.author_sub
					)
					OR (
						ub.blocker_sub = c.author_sub
						AND ub.blocked_sub = ?
					)
			  )
			ORDER BY c.created_at ASC
			LIMIT ' . $limit . '
			OFFSET ' . $offset . '
			',
			[
				$postId,
				'published',
				$viewerSub,
				$viewerSub,
			]
		);
	}
}