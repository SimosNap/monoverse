<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class UserModerationService
{
	public function __construct(
		private Database $database
	) {
	}

	public function findBySub(string $sub): array|false
	{
		return $this->database->fetchOne(
			'SELECT *
			 FROM user_moderation
			 WHERE oauth_sub = ?
			 LIMIT 1',
			[$sub]
		);
	}
	
	public function ensure(string $sub): void
	{
		if ($this->findBySub($sub)) {
			return;
		}
	
		$now = time();
	
		$this->database->execute(
			'INSERT INTO user_moderation (
				oauth_sub,
				created_at,
				updated_at
			) VALUES (?, ?, ?)',
			[
				$sub,
				$now,
				$now,
			]
		);
	}
	
	public function isMuted(string $sub): bool
	{
		$moderation = $this->findBySub($sub);
	
		if (!$moderation) {
			return false;
		}
	
		if (!(bool) $moderation['muted']) {
			return false;
		}
	
		$expiresAt = $moderation['mute_expires_at'];
	
		if ($expiresAt !== null && (int) $expiresAt < time()) {
			$this->unmute($sub);
			return false;
		}
	
		return true;
	}
	
	public function isBanned(string $sub): bool
	{
		$moderation = $this->findBySub($sub);
	
		if (!$moderation) {
			return false;
		}
	
		if (!(bool) $moderation['banned']) {
			return false;
		}
	
		$expiresAt = $moderation['ban_expires_at'];
	
		if ($expiresAt !== null && (int) $expiresAt < time()) {
			$this->unban($sub);
			return false;
		}
	
		return true;
	}
	
	public function mute(
		string $sub,
		string $moderatorSub,
		?string $reason = null,
		?int $expiresAt = null
	): void {
		$this->ensure($sub);
	
		$this->database->execute(
			'UPDATE user_moderation
			 SET
				muted = ?,
				mute_reason = ?,
				muted_by = ?,
				mute_expires_at = ?,
				updated_at = ?
			 WHERE oauth_sub = ?',
			[
				1,
				$reason,
				$moderatorSub,
				$expiresAt,
				time(),
				$sub,
			]
		);
	}
	
	public function ban(
		string $sub,
		string $moderatorSub,
		?string $reason = null,
		?int $expiresAt = null
	): void {
		$this->ensure($sub);
	
		$this->database->execute(
			'UPDATE user_moderation
			 SET
				banned = ?,
				ban_reason = ?,
				banned_by = ?,
				ban_expires_at = ?,
				updated_at = ?
			 WHERE oauth_sub = ?',
			[
				1,
				$reason,
				$moderatorSub,
				$expiresAt,
				time(),
				$sub,
			]
		);
	}
	
	public function unmute(string $sub): void
	{
		$this->ensure($sub);
	
		$this->database->execute(
			'UPDATE user_moderation
			 SET
				muted = ?,
				mute_reason = ?,
				muted_by = ?,
				mute_expires_at = ?,
				updated_at = ?
			 WHERE oauth_sub = ?',
			[
				0,
				null,
				null,
				null,
				time(),
				$sub,
			]
		);
	}
	
	public function unban(string $sub): void
	{
		$this->ensure($sub);
	
		$this->database->execute(
			'UPDATE user_moderation
			 SET
				banned = ?,
				ban_reason = ?,
				banned_by = ?,
				ban_expires_at = ?,
				updated_at = ?
			 WHERE oauth_sub = ?',
			[
				0,
				null,
				null,
				null,
				time(),
				$sub,
			]
		);
	}
	
	public function getActiveBans(): array
	{
		return $this->database->fetchAll(
			'SELECT
				m.*,
	
				p.username,
				p.nickname,
				p.avatar_url,
				p.public_profile,
	
				moderator.username AS moderator_username,
				moderator.nickname AS moderator_nickname,
				moderator.avatar_url AS moderator_avatar
	
			FROM user_moderation m
	
			LEFT JOIN profiles p
				ON p.oauth_sub = m.oauth_sub
	
			LEFT JOIN profiles moderator
				ON moderator.oauth_sub = m.banned_by
	
			WHERE m.banned = 1
				AND (
					m.ban_expires_at IS NULL
					OR m.ban_expires_at >= ?
				)
			
			ORDER BY m.updated_at DESC',
						[
							time(),
						]
		);
	}
	
	public function getActiveMutes(): array
	{
		return $this->database->fetchAll(
			'SELECT
				m.*,
	
				p.username,
				p.nickname,
				p.avatar_url,
				p.public_profile,
	
				moderator.username AS moderator_username,
				moderator.nickname AS moderator_nickname,
				moderator.avatar_url AS moderator_avatar
	
			FROM user_moderation m
	
			LEFT JOIN profiles p
				ON p.oauth_sub = m.oauth_sub
	
			LEFT JOIN profiles moderator
				ON moderator.oauth_sub = m.muted_by
	
			WHERE m.muted = 1
				AND (
					m.mute_expires_at IS NULL
					OR m.mute_expires_at >= ?
				)
			
			ORDER BY m.updated_at DESC',
						[
							time(),
						]
		);
	}
}
