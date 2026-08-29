<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class ModeratorService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function findAll(): array
    {
        return $this->database->fetchAll(
            'SELECT
                m.*,
                p.avatar_url
             FROM moderators m
             LEFT JOIN profiles p
                ON p.oauth_sub = m.oauth_sub
             ORDER BY m.username ASC'
        );
    }

    public function findEnabled(): array
    {
        return $this->database->fetchAll(
            'SELECT
                m.*,
                p.avatar_url
             FROM moderators m
             LEFT JOIN profiles p
                ON p.oauth_sub = m.oauth_sub
             WHERE m.enabled = 1
             ORDER BY m.username ASC'
        );
    }

    public function findBySub(string $sub): array|false
    {
        return $this->database->fetchOne(
            'SELECT *
             FROM moderators
             WHERE oauth_sub = ?
             LIMIT 1',
            [$sub]
        );
    }

    public function add(array $profile): bool
    {
        $sub = (string) ($profile['oauth_sub'] ?? '');

        if ($sub === '') {
            return false;
        }

        if ($this->findBySub($sub)) {
            return true;
        }

        $now = time();

        return $this->database->execute(
            'INSERT INTO moderators
            (
                oauth_sub,
                username,
                role,
                enabled,
                created_at,
                updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?)',
            [
                $sub,
                (string) ($profile['username'] ?? ''),
                'moderator',
                1,
                $now,
                $now,
            ]
        );
    }

    public function addByProfile(array $profile): bool
    {
        return $this->add($profile);
    }

    public function enable(string $sub): bool
    {
        return $this->database->execute(
            'UPDATE moderators
             SET enabled = 1,
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                time(),
                $sub,
            ]
        );
    }

    public function disable(string $sub): bool
    {
        return $this->database->execute(
            'UPDATE moderators
             SET enabled = 0,
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                time(),
                $sub,
            ]
        );
    }

    public function remove(string $sub): bool
    {
        return $this->database->execute(
            'DELETE FROM moderators
             WHERE oauth_sub = ?',
            [$sub]
        );
    }

    public function isModerator(string $sub): bool
    {
        $moderator = $this->findBySub($sub);

        return $moderator !== false
            && (int) $moderator['enabled'] === 1;
    }
}
