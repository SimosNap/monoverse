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

    public function logout(): void
    {
        $this->session->remove('admin');
    }
}
