<?php
declare(strict_types=1);

namespace Monoverse\Installer;

use Monoverse\Core\Request;

class Installer
{
    public function isInstalled(): bool
    {
        return file_exists(__DIR__ . '/../../storage/installed.lock');
    }

    public function shouldRun(Request $request): bool
    {
        if ($this->isInstalled()) {
            return false;
        }

        return str_starts_with($request->path(), '/install')
            || $request->path() === '/';
    }

    public function requirements(): array
    {
        $gdInfo = extension_loaded('gd')
            ? gd_info()
            : [];

        $ffmpeg = '/usr/bin/ffmpeg';

        return [
            [
                'name' => 'PHP 8.2+',
                'ok' => version_compare(
                    PHP_VERSION,
                    '8.2.0',
                    '>='
                ),
                'current' => PHP_VERSION,
            ],
            [
                'name' => 'PDO',
                'ok' => extension_loaded('pdo'),
                'current' => extension_loaded('pdo')
                    ? 'loaded'
                    : 'missing',
            ],
            [
                'name' => 'PDO MySQL',
                'ok' => extension_loaded('pdo_mysql'),
                'current' => extension_loaded('pdo_mysql')
                    ? 'loaded'
                    : 'missing',
            ],
            [
                'name' => 'JSON',
                'ok' => extension_loaded('json'),
                'current' => extension_loaded('json')
                    ? 'loaded'
                    : 'missing',
            ],
            [
                'name' => 'mbstring',
                'ok' => extension_loaded('mbstring'),
                'current' => extension_loaded('mbstring')
                    ? 'loaded'
                    : 'missing',
            ],
            [
                'name' => 'cURL',
                'ok' => extension_loaded('curl'),
                'current' => extension_loaded('curl')
                    ? 'loaded'
                    : 'missing',
            ],
            [
                'name' => 'GD',
                'ok' => extension_loaded('gd'),
                'current' => extension_loaded('gd')
                    ? 'loaded'
                    : 'missing',
            ],
            [
                'name' => 'GD JPEG',
                'ok' => !empty(
                    $gdInfo['JPEG Support']
                ),
                'current' => !empty(
                    $gdInfo['JPEG Support']
                )
                    ? 'supported'
                    : 'missing',
            ],
            [
                'name' => 'GD PNG',
                'ok' => !empty(
                    $gdInfo['PNG Support']
                ),
                'current' => !empty(
                    $gdInfo['PNG Support']
                )
                    ? 'supported'
                    : 'missing',
            ],
            [
                'name' => 'GD WebP',
                'ok' => !empty(
                    $gdInfo['WebP Support']
                ),
                'current' => !empty(
                    $gdInfo['WebP Support']
                )
                    ? 'supported'
                    : 'missing',
            ],
            [
                'name' => 'FFmpeg',
                'ok' => is_file($ffmpeg)
                    && is_executable($ffmpeg),
                'current' => is_file($ffmpeg)
                    && is_executable($ffmpeg)
                        ? $ffmpeg
                        : 'missing',
            ],
            [
                'name' => 'storage/',
                'ok' => is_writable(
                    __DIR__ . '/../../storage'
                ),
                'current' => is_writable(
                    __DIR__ . '/../../storage'
                )
                    ? 'writable'
                    : 'not writable',
            ],
        ];
    }
}