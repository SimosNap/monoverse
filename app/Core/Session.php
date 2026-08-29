<?php
declare(strict_types=1);

namespace Monoverse\Core;

class Session
{
    public function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;

        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function destroy(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
