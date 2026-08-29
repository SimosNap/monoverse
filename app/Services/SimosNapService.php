<?php
declare(strict_types=1);

namespace Monoverse\Services;

class SimosNapService
{
    private const API_BASE =
        'https://www.simosnap.org/rest/service.php';

    private const CHANNEL_USERS_CACHE_TTL = 20;
    private const CHANNEL_INFO_CACHE_TTL = 60;
    private const USER_ACTIVITY_CACHE_TTL = 300;

    public function __construct(
        private SettingsService $settings
    ) {
    }

    public function getProfile(string $account): array
    {
        return $this->request(
            '/users/account/' . rawurlencode($account)
        );
    }

    public function getChannels(string $account): array
    {
        return $this->request(
            '/users/account/' . rawurlencode($account) . '/channels'
        );
    }

    public function getChannel(string $channel): array
    {
        $cacheDirectory = __DIR__ . '/../../storage/cache';

        $cacheFile = $cacheDirectory
            . '/simosnap-channel-info-'
            . hash('sha256', strtolower(trim($channel)))
            . '.json';

        if (
            is_file($cacheFile)
            && (time() - (int) filemtime($cacheFile))
                < self::CHANNEL_INFO_CACHE_TTL
        ) {
            $cachedJson = file_get_contents($cacheFile);

            if ($cachedJson !== false && $cachedJson !== '') {

                $cachedChannel = json_decode(
                    $cachedJson,
                    true
                );

                if (is_array($cachedChannel)) {
                    return $cachedChannel;
                }

            }
        }

        $channelInfo = $this->request(
            '/channels/' . rawurlencode($channel)
        );

        if ($channelInfo !== []) {

            $temporaryFile = $cacheFile . '.tmp';

            $encodedChannel = json_encode(
                $channelInfo,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );

            if ($encodedChannel !== false) {

                if (
                    file_put_contents(
                        $temporaryFile,
                        $encodedChannel,
                        LOCK_EX
                    ) !== false
                ) {
                    rename(
                        $temporaryFile,
                        $cacheFile
                    );
                }

            }

        }

        return $channelInfo;
    }

    public function getChannelUsers(string $channel): array
    {
        $cacheDirectory = __DIR__ . '/../../storage/cache';

        $cacheFile = $cacheDirectory
            . '/simosnap-channel-users-'
            . hash('sha256', strtolower(trim($channel)))
            . '.json';

        if (
            is_file($cacheFile)
            && (time() - (int) filemtime($cacheFile))
                < self::CHANNEL_USERS_CACHE_TTL
        ) {
            $cachedJson = file_get_contents($cacheFile);

            if ($cachedJson !== false && $cachedJson !== '') {
                $cachedUsers = json_decode($cachedJson, true);

                if (is_array($cachedUsers)) {
                    return $cachedUsers;
                }
            }
        }

        $users = $this->request(
            '/channels/' . rawurlencode($channel) . '/users'
        );

        if ($users !== []) {
            $temporaryFile = $cacheFile . '.tmp';

            $encodedUsers = json_encode(
                $users,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );

            if ($encodedUsers !== false) {
                if (
                    file_put_contents(
                        $temporaryFile,
                        $encodedUsers,
                        LOCK_EX
                    ) !== false
                ) {
                    rename($temporaryFile, $cacheFile);
                }
            }
        }

        return $users;
    }

    public function getAccountPresence(
        string $account
    ): array
    {
        $channel = trim(
            (string) $this->settings->get(
                'chat_default_channel',
                '#chat'
            )
        );

        $users = $this->getChannelUsers($channel);

        if ($users === []) {
            return [
                'status' => 'offline',
                'online' => false,
                'away'   => false,
            ];
        }

        $found = false;
        $away = true;

        foreach ($users as $user) {

            if (
                !empty($user['service'])
                || !empty($user['bot'])
            ) {
                continue;
            }

            if (
                strcasecmp(
                    (string) ($user['account'] ?? ''),
                    $account
                ) !== 0
            ) {
                continue;
            }

            $found = true;

            if (empty($user['away'])) {
                $away = false;
                break;
            }
        }

        if (!$found) {
            return [
                'status' => 'offline',
                'online' => false,
                'away'   => false,
            ];
        }

        if ($away) {
            return [
                'status' => 'away',
                'online' => true,
                'away'   => true,
            ];
        }

        return [
            'status' => 'online',
            'online' => true,
            'away'   => false,
        ];
    }

    public function getActivity(
        string $account,
        ?string $channel = null
    ): array {
        $account = trim($account);
        $channel = trim((string) $channel);

        if ($account === '') {
            return [];
        }

        $cacheDirectory = __DIR__ . '/../../storage/cache';

        $cacheKey = strtolower(
            $account . '|' . $channel
        );

        $cacheFile = $cacheDirectory
            . '/simosnap-user-activity-'
            . hash('sha256', $cacheKey)
            . '.json';

        if (
            is_file($cacheFile)
            && (time() - (int) filemtime($cacheFile))
                < self::USER_ACTIVITY_CACHE_TTL
        ) {
            $cachedJson = file_get_contents($cacheFile);

            if ($cachedJson !== false && $cachedJson !== '') {
                $cachedActivity = json_decode(
                    $cachedJson,
                    true
                );

                if (is_array($cachedActivity)) {
                    return $cachedActivity;
                }
            }
        }

        $endpoint =
            '/users/account/'
            . rawurlencode($account)
            . '/activity';

        if ($channel !== '') {
            $endpoint .= '/' . rawurlencode($channel);
        }

        $activity = $this->request($endpoint);

        if ($activity !== []) {
            $temporaryFile = $cacheFile . '.tmp';

            $encodedActivity = json_encode(
                $activity,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );

            if ($encodedActivity !== false) {
                if (
                    file_put_contents(
                        $temporaryFile,
                        $encodedActivity,
                        LOCK_EX
                    ) !== false
                ) {
                    rename(
                        $temporaryFile,
                        $cacheFile
                    );
                }
            }
        }

        return $activity;
    }

    public function getMisc(string $nickname): array
    {
        return $this->request(
            '/nmisc/' . rawurlencode($nickname)
        );
    }

    public function getDogecoinAddress(string $nickname): ?string
    {
        $misc = $this->getMisc($nickname);

        $address = trim(
            (string) ($misc['DOGECOIN'] ?? '')
        );

        return $address !== ''
            ? $address
            : null;
    }

    public function getPublicProfile(string $account): array
    {
        $profile = $this->getProfile($account);

        if ($profile === []) {
            return [];
        }

        return [
            'profile'  => $profile,
            'channels' => $this->getChannels($account),
            'activity' => $this->getActivity($account),
            'misc'     => $this->getMisc($account),
        ];
    }

    private function request(string $endpoint): array
    {
        $url = self::API_BASE . $endpoint;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
                'ignore_errors' => true,
                'header' => [
                    'Accept: application/json',
                    'User-Agent: Monoverse/1.0'
                ],
            ],
        ]);

        $json = @file_get_contents(
            $url,
            false,
            $context
        );

        if ($json === false || $json === '') {
            return [];
        }

        $decoded = json_decode(
            $json,
            true
        );

        return is_array($decoded)
            ? $decoded
            : [];
    }

    public function getChannelFeatures(array $channelInfo): array
    {
        $modes = (string) ($channelInfo['modes'] ?? '');

        $features = [];

        if (str_contains($modes, 'm')) {
            $features[] = [
                'icon' => 'fa-comments',
                'title' => 'Canale moderato',
                'description' => 'Solo gli utenti autorizzati possono parlare.',
            ];
        }

        if (str_contains($modes, 'M')) {
            $features[] = [
                'icon' => 'fa-user-check',
                'title' => 'Solo utenti registrati possono parlare',
                'description' => 'Per partecipare alla conversazione è necessario utilizzare un nickname registrato.',
            ];
        }

        if (str_contains($modes, 'R')) {
            $features[] = [
                'icon' => 'fa-right-to-bracket',
                'title' => 'Accesso riservato agli utenti registrati',
                'description' => 'Solo i nickname registrati possono entrare nel canale.',
            ];
        }

        if (str_contains($modes, 'i')) {
            $features[] = [
                'icon' => 'fa-envelope-open',
                'title' => 'Accesso solo su invito',
                'description' => 'Per entrare è necessario ricevere un invito.',
            ];
        }

        if (str_contains($modes, 'u')) {
            $features[] = [
                'icon' => 'fa-user-group',
                'title' => 'Auditorium',
                'description' => 'Gli utenti vedono solo i moderatori e i propri messaggi.',
            ];
        }

        if (str_contains($modes, 'z')) {
            $features[] = [
                'icon' => 'fa-lock',
                'title' => 'Connessione sicura',
                'description' => 'È richiesta una connessione SSL/TLS.',
            ];
        }

        if (str_contains($modes, 'H')) {
            $features[] = [
                'icon' => 'fa-clock-rotate-left',
                'title' => 'Cronologia disponibile',
                'description' => 'I nuovi utenti ricevono gli ultimi messaggi del canale.',
            ];
        }

        if (preg_match('/W\s+([^ ]+)/', $modes)) {
            $features[] = [
                'icon' => 'fa-hourglass-half',
                'title' => 'Slow mode attivo',
                'description' => 'La frequenza dei messaggi è limitata per favorire conversazioni ordinate.',
            ];
        }

        return $features;
    }
}