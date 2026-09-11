<?php
declare(strict_types=1);

namespace Monoverse\Installer;

use PDO;
use Throwable;

class InstallationRunner
{
    public function run(array $data): bool
    {
        $database = $data['database'] ?? [];
        $admin = $data['admin'] ?? [];

        try {
            $pdo = $this->connect($database);

            $this->createSchema($pdo);
            $this->seedSettings(
                $pdo,
                (string) ($data['locale'] ?? 'it')
            );
            $this->createAdministrator($pdo, $admin);

            if (!$this->writeDatabaseConfig($database)) {
                throw new \RuntimeException(
                    'Unable to write database configuration.'
                );
            }

            if (!$this->writeOAuthConfig($data['oauth'] ?? [])) {
                throw new \RuntimeException(
                    'Unable to write OAuth configuration.'
                );
            }

            if (!$this->writeAppConfig()) {
                throw new \RuntimeException(
                    'Unable to write application configuration.'
                );
            }

            if (!$this->writeInstalledLock($data)) {
                throw new \RuntimeException(
                    'Unable to write installation lock.'
                );
            }

            return true;
        } catch (Throwable $e) {
            file_put_contents(
                __DIR__ . '/../../storage/install-error.log',
                '[' . date('c') . '] ' . $e->getMessage() . "\n",
                FILE_APPEND | LOCK_EX
            );

            return false;
        }
    }

    private function connect(array $database): PDO
    {
        $host = $database['host'] ?? 'localhost';
        $name = $database['name'] ?? '';
        $user = $database['user'] ?? '';
        $pass = $database['pass'] ?? '';

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $host,
            $name
        );

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private function createSchema(PDO $pdo): void
    {
        $schemaFile = __DIR__ . '/../../database/schema.sql';

        if (!is_file($schemaFile)) {
            throw new \RuntimeException(
                'Database schema file not found: ' . $schemaFile
            );
        }

        $schema = file_get_contents($schemaFile);

        if ($schema === false || trim($schema) === '') {
            throw new \RuntimeException(
                'Database schema file is empty or unreadable.'
            );
        }

        $pdo->exec($schema);
    }

    private function seedSettings(
        PDO $pdo,
        string $locale
    ): void {
        $now = time();

        if (!in_array($locale, ['it', 'en'], true)) {
            $locale = 'it';
        }

        $settings = [
            'site_name' => 'Monoverse',
            'site_tagline' => '',
            'site_url' => '',
            'meta_description' => '',

            'pages_navigation_main' => '1',

            'media_audio_upload_enabled' => '1',
            'media_audio_max_mb' => '50',
            'media_video_upload_enabled' => '1',
            'media_video_max_mb' => '50',
            'media_require_text_with_audio_video' => '1',

            'chanzine_user_submissions_enabled' => '0',
            'chanzine_ping_author_name' => 'Chanzine',

            'crypto_tips_enabled' => '0',
            'crypto_tips_profiles_enabled' => '0',
            'crypto_tips_pings_enabled' => '0',

            'default_locale' => $locale,
            'available_locales' => 'it,en',

            'github_api_token' => '',

            'chat_default_channel' => '#chat',
            'chat_title' => '#chat - Chat',
            'chat_theme' => 'Osprey',
            'chat_state_key' => '',

            'landing_show_hero' => '1',
            'landing_show_channel_card' => '1',

            'site_logo' => '',
            'site_favicon' => '',
            'site_apple_touch_icon' => '',
            'site_og_image' => '',
        ];

        $stmt = $pdo->prepare(
            'INSERT INTO settings
                (setting_key, setting_value, created_at, updated_at)
             VALUES
                (:setting_key, :setting_value, :created_at, :updated_at)'
        );

        foreach ($settings as $key => $value) {
            $stmt->execute([
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function createAdministrator(PDO $pdo, array $admin): void
    {
        $username = $admin['username'] ?? '';
        $passwordHash = $admin['password_hash'] ?? '';

        $stmt = $pdo->prepare("
            INSERT INTO administrators
                (username, password_hash, role, enabled, created_at)
            VALUES
                (:username, :password_hash, 'administrator', 1, :created_at)
        ");

        $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash,
            'created_at' => time(),
        ]);
    }

    private function writeDatabaseConfig(array $database): bool
    {
        $configFile = __DIR__ . '/../../config/database.php';

        $content = "<?php\n";
        $content .= "declare(strict_types=1);\n\n";
        $content .= "return [\n";
        $content .= "    'host' => " . var_export($database['host'] ?? 'localhost', true) . ",\n";
        $content .= "    'database' => " . var_export($database['name'] ?? '', true) . ",\n";
        $content .= "    'username' => " . var_export($database['user'] ?? '', true) . ",\n";
        $content .= "    'password' => " . var_export($database['pass'] ?? '', true) . ",\n";
        $content .= "    'charset' => 'utf8mb4',\n";
        $content .= "];\n";

        return file_put_contents($configFile, $content, LOCK_EX) !== false;
    }

    private function writeOAuthConfig(array $oauth): bool
    {
        $configFile = __DIR__ . '/../../config/oauth.php';

        $clientId = (string) ($oauth['client_id'] ?? '');
        $clientSecret = (string) ($oauth['client_secret'] ?? '');

        $content = "<?php\n";
        $content .= "declare(strict_types=1);\n\n";
        $content .= "return [\n\n";

        $content .= "    'authorize_url' => 'https://www.simosnap.org/rest/service.php/oauth/authorize',\n";
        $content .= "    'token_url' => 'https://www.simosnap.org/rest/service.php/oauth/token',\n";
        $content .= "    'userinfo_url' => 'https://www.simosnap.org/rest/service.php/oauth/userinfo',\n";
        $content .= "    'account_uid_lookup_url' => 'https://www.simosnap.org/rest/service.php/lookupaccountuid',\n\n";

        $content .= "    'client_id' => " . var_export($clientId, true) . ",\n";
        $content .= "    'client_secret' => " . var_export($clientSecret, true) . ",\n\n";

        $content .= "    'scope' => 'openid profile',\n";
        $content .= "];\n";

        return file_put_contents(
            $configFile,
            $content,
            LOCK_EX
        ) !== false;
    }

    private function writeAppConfig(): bool
    {
        $configFile = __DIR__ . '/../../config/app.php';

        $content = file_get_contents($configFile);

        if ($content === false) {
            return false;
        }

        $scheme = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https'
        )
            ? 'https'
            : 'http';

        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));

        if ($host === '') {
            return false;
        }

        $baseUrl = $scheme . '://' . $host;

        $updated = preg_replace(
            "/('base_url'\\s*=>\\s*)'[^']*'/",
            '$1' . var_export($baseUrl, true),
            $content,
            1,
            $count
        );

        if ($updated === null || $count !== 1) {
            return false;
        }

        return file_put_contents(
            $configFile,
            $updated,
            LOCK_EX
        ) !== false;
    }

    private function writeInstalledLock(array $data): bool
    {
        $lockFile = __DIR__ . '/../../storage/installed.lock';

        $payload = [
            'installed_at' => date('c'),
            'edition' => $data['edition'] ?? null,
            'database' => [
                'host' => $data['database']['host'] ?? null,
                'name' => $data['database']['name'] ?? null,
                'user' => $data['database']['user'] ?? null,
            ],
            'admin' => [
                'username' => $data['admin']['username'] ?? null,
            ],
        ];

        return file_put_contents(
            $lockFile,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        ) !== false;
    }
}
