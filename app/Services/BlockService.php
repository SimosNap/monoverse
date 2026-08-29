<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class BlockService
{
	public function __construct(
		private Database $database
	) {
	}

	public function block(
		string $blockerSub,
		string $blockedSub
	): bool {
		if ($blockerSub === $blockedSub) {
			return false;
		}

		return $this->database->execute(
			'INSERT IGNORE INTO user_blocks
			 (blocker_sub, blocked_sub)
			 VALUES (?, ?)',
			[
				$blockerSub,
				$blockedSub,
			]
		);
	}

	public function unblock(
		string $blockerSub,
		string $blockedSub
	): bool {
		return $this->database->execute(
			'DELETE FROM user_blocks
			 WHERE blocker_sub = ?
			   AND blocked_sub = ?',
			[
				$blockerSub,
				$blockedSub,
			]
		);
	}

	public function isBlocked(
		string $blockerSub,
		string $blockedSub
	): bool {
		return $this->database->fetchOne(
			'SELECT 1
			 FROM user_blocks
			 WHERE blocker_sub = ?
			   AND blocked_sub = ?
			 LIMIT 1',
			[
				$blockerSub,
				$blockedSub,
			]
		) !== false;
	}

	public function isEitherBlocked(
		string $subA,
		string $subB
	): bool {
		return $this->database->fetchOne(
			'SELECT 1
			 FROM user_blocks
			 WHERE (blocker_sub = ? AND blocked_sub = ?)
				OR (blocker_sub = ? AND blocked_sub = ?)
			 LIMIT 1',
			[
				$subA,
				$subB,
				$subB,
				$subA,
			]
		) !== false;
	}

	public function blockedCount(
		string $sub
	): int {
		$row = $this->database->fetchOne(
			'SELECT COUNT(*) AS total
			 FROM user_blocks
			 WHERE blocker_sub = ?',
			[
				$sub,
			]
		);

		return (int) ($row['total'] ?? 0);
	}

	public function listBlocked(
		string $blockerSub
	): array {
		return $this->database->fetchAll(
			'SELECT
			
				ub.blocked_sub,
				ub.created_at,
				p.username,
				p.avatar_url,
				p.show_avatar,
				p.external_account_exists,
				p.external_account_missing_at
			
			FROM user_blocks ub
			
			INNER JOIN profiles p
				ON p.oauth_sub = ub.blocked_sub
			
			WHERE ub.blocker_sub = ?
			
			ORDER BY p.username ASC',
			[
				$blockerSub,
			]
		);
	}

	public function findBlockedByUsername(
		string $blockerSub,
		string $username
	): array|false {
		return $this->database->fetchOne(
			'SELECT
				ub.blocked_sub,
				ub.created_at,
				p.username,
				p.avatar_url,
				p.show_avatar
			 FROM user_blocks ub

			 INNER JOIN profiles p
				 ON p.oauth_sub = ub.blocked_sub

			 WHERE ub.blocker_sub = ?
			   AND p.username = ?

			 LIMIT 1',
			[
				$blockerSub,
				$username,
			]
		);
	}
}