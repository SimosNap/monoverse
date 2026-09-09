<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class AdministratorService
{
	private const ALLOWED_ROLES = [
		'siteadmin',
		'contentadmin',
	];

	public function __construct(
		private Database $database
	) {
	}

	public function findAll(): array
	{
		return $this->database->fetchAll(
			'SELECT
				id,
				username,
				role,
				enabled,
				created_at,
				last_login_at,
				last_login_ip
			 FROM administrators
			 ORDER BY
				CASE
					WHEN role = "administrator" THEN 0
					ELSE 1
				END,
				username ASC'
		);
	}

	public function findById(int $id): ?array
	{
		$admin = $this->database->fetchOne(
			'SELECT
				id,
				username,
				role,
				enabled,
				created_at,
				last_login_at,
				last_login_ip
			 FROM administrators
			 WHERE id = ?
			 LIMIT 1',
			[$id]
		);

		return $admin ?: null;
	}

	public function findByUsername(string $username): ?array
	{
		$admin = $this->database->fetchOne(
			'SELECT
				id,
				username,
				role,
				enabled,
				created_at,
				last_login_at,
				last_login_ip
			 FROM administrators
			 WHERE username = ?
			 LIMIT 1',
			[$username]
		);

		return $admin ?: null;
	}

	public function create(
		string $username,
		string $password,
		string $role
	): int {
		$username = trim($username);
		$role = trim($role);

		if ($username === '') {
			throw new \InvalidArgumentException(
				'username_required'
			);
		}

		if ($password === '') {
			throw new \InvalidArgumentException(
				'password_required'
			);
		}

		if (!$this->isAllowedRole($role)) {
			throw new \InvalidArgumentException(
				'invalid_role'
			);
		}

		if ($this->findByUsername($username) !== null) {
			throw new \RuntimeException(
				'username_exists'
			);
		}

		$passwordHash = password_hash(
			$password,
			PASSWORD_DEFAULT
		);

		$this->database->execute(
			'INSERT INTO administrators (
				username,
				password_hash,
				role,
				enabled,
				created_at
			 ) VALUES (?, ?, ?, 1, ?)',
			[
				$username,
				$passwordHash,
				$role,
				time(),
			]
		);

		return (int) $this->database->lastInsertId();
	}

	public function update(
		int $id,
		string $username,
		string $role,
		bool $enabled,
		?string $password = null
	): void {
		$admin = $this->findById($id);

		if ($admin === null) {
			throw new \RuntimeException(
				'not_found'
			);
		}

		if (($admin['role'] ?? '') === 'administrator') {
			throw new \RuntimeException(
				'original_cannot_modify'
			);
		}

		$username = trim($username);
		$role = trim($role);

		if ($username === '') {
			throw new \InvalidArgumentException(
				'username_required'
			);
		}

		if (!$this->isAllowedRole($role)) {
			throw new \InvalidArgumentException(
				'invalid_role'
			);
		}

		$existing = $this->findByUsername($username);

		if (
			$existing !== null
			&& (int) $existing['id'] !== $id
		) {
			throw new \RuntimeException(
				'username_exists'
			);
		}

		$password = $password !== null
			? trim($password)
			: '';

		if ($password !== '') {
			$this->database->execute(
				'UPDATE administrators
				 SET
					username = ?,
					password_hash = ?,
					role = ?,
					enabled = ?
				 WHERE id = ?',
				[
					$username,
					password_hash(
						$password,
						PASSWORD_DEFAULT
					),
					$role,
					$enabled ? 1 : 0,
					$id,
				]
			);

			return;
		}

		$this->database->execute(
			'UPDATE administrators
			 SET
				username = ?,
				role = ?,
				enabled = ?
			 WHERE id = ?',
			[
				$username,
				$role,
				$enabled ? 1 : 0,
				$id,
			]
		);
	}

	public function remove(int $id): void
	{
		$admin = $this->findById($id);

		if ($admin === null) {
			return;
		}

		if (($admin['role'] ?? '') === 'administrator') {
			throw new \RuntimeException(
				'original_cannot_delete'
			);
		}

		$this->database->execute(
			'DELETE FROM administrators WHERE id = ?',
			[$id]
		);
	}

	public function isAllowedRole(string $role): bool
	{
		return in_array(
			$role,
			self::ALLOWED_ROLES,
			true
		);
	}
}
