<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Core\Session;

class AdminAuthService
{
    public function __construct(
        private Database $database,
        private Session $session
    ) {
    }

    public function attempt(string $username, string $password): bool
    {
        $admin = $this->database->fetchOne(
            'SELECT * FROM administrators WHERE username = ? AND enabled = 1 LIMIT 1',
            [$username]
        );

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return false;
        }

        $this->session->set('admin', [
            'id' => (int) $admin['id'],
            'username' => $admin['username'],
            'role' => $admin['role'],
        ]);

        $this->database->execute(
            'UPDATE administrators SET last_login_at = ?, last_login_ip = ? WHERE id = ?',
            [time(), $_SERVER['REMOTE_ADDR'] ?? '', $admin['id']]
        );

        return true;
    }

    public function check(): bool
    {
        return $this->session->has('admin');
    }

    public function user(): ?array
    {
        return $this->session->get('admin');
    }

    public function role(): ?string
    {
        $admin = $this->user();

        if (!is_array($admin)) {
            return null;
        }

        return isset($admin['role'])
            ? (string) $admin['role']
            : null;
    }

    public function can(string $area): bool
    {
        $role = $this->role();

        return match ($area) {
            'administrators' => $role === 'administrator',

            'site' => in_array(
                $role,
                [
                    'administrator',
                    'siteadmin',
                ],
                true
            ),

            'content' => in_array(
                $role,
                [
                    'administrator',
                    'siteadmin',
                    'contentadmin',
                ],
                true
            ),

            default => false,
        };
    }

    public function changePassword(
        string $currentPassword,
        string $newPassword
    ): bool {
        $admin = $this->user();

        if (!is_array($admin)) {
            return false;
        }

        $adminId = (int) (
            $admin['id']
            ?? 0
        );

        if ($adminId <= 0) {
            return false;
        }

        $currentPassword = trim($currentPassword);
        $newPassword = trim($newPassword);

        if (
            $currentPassword === ''
            || $newPassword === ''
        ) {
            return false;
        }

        $administrator = $this->database->fetchOne(
            'SELECT password_hash
             FROM administrators
             WHERE id = ?
             AND enabled = 1
             LIMIT 1',
            [$adminId]
        );

        if (
            !$administrator
            || !password_verify(
                $currentPassword,
                (string) $administrator['password_hash']
            )
        ) {
            return false;
        }

        $this->database->execute(
            'UPDATE administrators
             SET password_hash = ?
             WHERE id = ?',
            [
                password_hash(
                    $newPassword,
                    PASSWORD_DEFAULT
                ),
                $adminId,
            ]
        );

        return true;
    }

    public function logout(): void
    {
        $this->session->remove('admin');
    }
}
