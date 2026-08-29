<?php
declare(strict_types=1);

namespace Monoverse\Core;

class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        return rtrim($path, '/') ?: '/';
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function queryAll(): array
    {
        return $_GET;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function postAll(): array
    {
        return $_POST;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $_COOKIE[$key] ?? $default;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }

    public function ip(): string
    {
        return $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isAjax(): bool
    {
        return strtolower(
            (string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')
        ) === 'xmlhttprequest';
    }
}
