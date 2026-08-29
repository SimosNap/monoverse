<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class FollowService
{
	public function __construct(
		private Database $database,
		private BlockService $blocks
	) {
	}

	public function follow(
		string $followerSub,
		string $followedSub
	): bool {
		if ($followerSub === $followedSub) {
			return false;
		}

		if ($this->blocks->isEitherBlocked(
			$followerSub,
			$followedSub
		)) {
			return false;
		}

		return $this->database->execute(
			'INSERT IGNORE INTO user_follows
			 (follower_sub, followed_sub)
			 VALUES (?, ?)',
			[
				$followerSub,
				$followedSub,
			]
		);
	}

	public function unfollow(
		string $followerSub,
		string $followedSub
	): bool {
		return $this->database->execute(
			'DELETE FROM user_follows
			 WHERE follower_sub = ?
			   AND followed_sub = ?',
			[
				$followerSub,
				$followedSub,
			]
		);
	}

	public function isFollowing(
		string $followerSub,
		string $followedSub
	): bool {
		return $this->database->fetchOne(
			'SELECT 1
			 FROM user_follows
			 WHERE follower_sub = ?
			   AND followed_sub = ?
			 LIMIT 1',
			[
				$followerSub,
				$followedSub,
			]
		) !== false;
	}

	public function followersCount(
		string $sub
	): int {
		$row = $this->database->fetchOne(
			'SELECT COUNT(*) AS total
			 FROM user_follows
			 WHERE followed_sub = ?',
			[
				$sub,
			]
		);

		return (int) ($row['total'] ?? 0);
	}

	public function followingCount(
		string $sub
	): int {
		$row = $this->database->fetchOne(
			'SELECT COUNT(*) AS total
			 FROM user_follows
			 WHERE follower_sub = ?',
			[
				$sub,
			]
		);

		return (int) ($row['total'] ?? 0);
	}
	
	public function findFollowing(string $followerSub): array
	{
		return $this->database->fetchAll(
			'
			SELECT
				p.oauth_sub,
				p.username,
				p.nickname,
				p.avatar_url,
				p.show_avatar,
				p.public_profile,
				uf.created_at AS followed_at
			FROM user_follows uf
			INNER JOIN profiles p
				ON p.oauth_sub = uf.followed_sub
			WHERE uf.follower_sub = ?
			  AND p.external_account_exists = 1
			ORDER BY uf.created_at DESC
			',
			[
				$followerSub,
			]
		);
	}
}
