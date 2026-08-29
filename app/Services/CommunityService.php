<?php
declare(strict_types=1);

namespace Monoverse\Services;

final class CommunityService
{
    public function __construct(
        private SimosNapService $simosnap,
        private SettingsService $settings,
        private ProfileService $profiles
    ) {
    }

    /**
     * @return array{
     *     users: array<int,array<string,mixed>>,
     *     total: int
     * }
     */
    public function usersInChat(int $limit = 10): array
    {
        $limit = max(
            1,
            min(50, $limit)
        );

        $channel = trim(
            (string) $this->settings->get(
                'chat_default_channel',
                '#chat'
            )
        );

        if ($channel === '') {
            return $this->emptyChatResult();
        }

        $channelUsers = $this->simosnap->getChannelUsers(
            $channel
        );

        if ($channelUsers === []) {
            return $this->emptyChatResult();
        }

        $groups = [];

        foreach ($channelUsers as $channelUser) {
            if (!is_array($channelUser)) {
                continue;
            }

            if ($this->isServiceOrBot($channelUser)) {
                continue;
            }

            $nickname = trim(
                (string) (
                    $channelUser['nickname']
                    ?? $channelUser['nick']
                    ?? ''
                )
            );

            if ($nickname === '') {
                continue;
            }

            $account = trim(
                (string) ($channelUser['account'] ?? '')
            );

            if ($account === '*') {
                $account = '';
            }

            $away = !empty(
                $channelUser['away']
            );

            $connection = [
                'nickname' => $nickname,
                'away' => $away,
                'status' => $away
                    ? 'away'
                    : 'online',
                'connection' => $this->detectConnectionType(
                    (string) ($channelUser['client'] ?? '')
                ),
            ];

            if ($account === '') {
                $groups[] = [
                    'account' => '',
                    'registered' => false,
                    'profile_username' => '',
                    'avatar_url' => '',
                    'show_avatar' => false,
                    'connections' => [
                        $connection,
                    ],
                ];

                continue;
            }

            $groupKey = mb_strtolower(
                $account,
                'UTF-8'
            );

            if (!isset($groups[$groupKey])) {
                $profile = $this->profiles->findPublicByUsername(
                    $account
                );

                $avatarUrl = '';
                $showAvatar = false;
                $profileUsername = '';

                if (is_array($profile)) {
                    $profileUsername = trim(
                        (string) ($profile['username'] ?? '')
                    );

                    $avatarUrl = trim(
                        (string) ($profile['avatar_url'] ?? '')
                    );

                    $showAvatar = !empty(
                        $profile['show_avatar']
                    ) && $avatarUrl !== '';
                }

                $groups[$groupKey] = [
                    'account' => $account,
                    'registered' => $profileUsername !== '',
                    'profile_username' => $profileUsername,
                    'avatar_url' => $avatarUrl,
                    'show_avatar' => $showAvatar,
                    'connections' => [],
                ];
            }

            $groups[$groupKey]['connections'][] = $connection;
        }

        $users = array_values($groups);

        foreach ($users as &$user) {
            if (!isset($user['connections'])) {
                continue;
            }

            usort(
                $user['connections'],
                static function (
                    array $first,
                    array $second
                ): int {
                    return strnatcasecmp(
                        (string) ($first['nickname'] ?? ''),
                        (string) ($second['nickname'] ?? '')
                    );
                }
            );

            $hasOnline = false;

            foreach ($user['connections'] as $connection) {
                if (($connection['status'] ?? '') === 'online') {
                    $hasOnline = true;
                    break;
                }
            }

            $user['status'] = $hasOnline
                ? 'online'
                : 'away';

            $user['away'] = !$hasOnline;
        }

        unset($user);

        usort(
            $users,
            static function (
                array $first,
                array $second
            ): int {
                $firstName = trim(
                    (string) ($first['account'] ?? '')
                );

                if ($firstName === '') {
                    $firstName = trim(
                        (string) (
                            $first['connections'][0]['nickname']
                            ?? ''
                        )
                    );
                }

                $secondName = trim(
                    (string) ($second['account'] ?? '')
                );

                if ($secondName === '') {
                    $secondName = trim(
                        (string) (
                            $second['connections'][0]['nickname']
                            ?? ''
                        )
                    );
                }

                return strnatcasecmp(
                    $firstName,
                    $secondName
                );
            }
        );

        $total = 0;

        foreach ($users as $user) {
            $total += count(
                $user['connections'] ?? []
            );
        }

        return [
            'users' => array_slice(
                $users,
                0,
                $limit
            ),
            'total' => $total,
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function mostActiveUsers(
        int $limit = 5
    ): array {
        $limit = max(
            1,
            min(20, $limit)
        );

        $channel = trim(
            (string) $this->settings->get(
                'chat_default_channel',
                '#chat'
            )
        );

        if ($channel === '') {
            return [];
        }

        $candidates = $this->profiles->mostActiveProfiles(
            100
        );

        if ($candidates === []) {
            return [];
        }

        $users = [];

        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) {
                continue;
            }

            $username = trim(
                (string) ($candidate['username'] ?? '')
            );

            if ($username === '') {
                continue;
            }

            $activity = $this->simosnap->getActivity(
                $username,
                $channel
            );

            $ircLines = 0;

            foreach ($activity as $period) {
                if (!is_array($period)) {
                    continue;
                }

                if (
                    strtolower(
                        trim(
                            (string) ($period['type'] ?? '')
                        )
                    ) !== 'monthly'
                ) {
                    continue;
                }

                $ircLines = max(
                    0,
                    (int) ($period['lines'] ?? 0)
                );

                break;
            }

            $candidate['ping_count'] = max(
                0,
                (int) ($candidate['ping_count'] ?? 0)
            );

            $candidate['pong_count'] = max(
                0,
                (int) ($candidate['pong_count'] ?? 0)
            );

            $candidate['upvote_count'] = max(
                0,
                (int) ($candidate['upvote_count'] ?? 0)
            );

            $candidate['downvote_count'] = max(
                0,
                (int) ($candidate['downvote_count'] ?? 0)
            );

            $candidate['irc_lines'] = $ircLines;

            $candidate['site_activity'] =
                $candidate['ping_count']
                + $candidate['pong_count']
                + $candidate['upvote_count']
                + $candidate['downvote_count'];

            $users[] = $candidate;
        }

        if ($users === []) {
            return [];
        }

        $maxSiteActivity = 0;
        $maxIrcActivity = 0;

        foreach ($users as $user) {
            $maxSiteActivity = max(
                $maxSiteActivity,
                (int) ($user['site_activity'] ?? 0)
            );

            $maxIrcActivity = max(
                $maxIrcActivity,
                (int) ($user['irc_lines'] ?? 0)
            );
        }

        foreach ($users as &$user) {
            $siteScore = $maxSiteActivity > 0
                ? (
                    (int) $user['site_activity']
                    / $maxSiteActivity
                )
                : 0.0;

            $ircScore = $maxIrcActivity > 0
                ? (
                    (int) $user['irc_lines']
                    / $maxIrcActivity
                )
                : 0.0;

            $user['activity_score'] =
                ($siteScore * 0.5)
                + ($ircScore * 0.5);
        }

        unset($user);

        usort(
            $users,
            static function (
                array $first,
                array $second
            ): int {
                $scoreComparison = (
                    (float) ($second['activity_score'] ?? 0)
                ) <=> (
                    (float) ($first['activity_score'] ?? 0)
                );

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                return strnatcasecmp(
                    (string) ($first['username'] ?? ''),
                    (string) ($second['username'] ?? '')
                );
            }
        );

        return array_slice(
            $users,
            0,
            $limit
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function latestMembers(
        int $limit = 5,
        string $locale = 'it'
    ): array {
        return $this->profiles->latestPublicProfiles(
            $limit,
            $locale
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function latestPings(int $limit = 5): array
    {
        return [];
    }

    /**
     * @return array<string,int>
     */
    public function statistics(): array
    {
        return [
            'members' => 0,
            'pings' => 0,
            'comments' => 0,
            'in_chat' => $this->usersInChat(1)['total'],
        ];
    }

    private function detectConnectionType(string $client): string
    {
        $client = trim($client);

        if ($client === '') {
            return 'irc';
        }

        if (stripos($client, 'IRCCloud') !== false) {
            return 'irccloud';
        }

        if (
            stripos($client, 'ZNC') !== false
            || stripos($client, 'BNC') !== false
        ) {
            return 'bouncer';
        }

        if (strcasecmp($client, 'Kiwi IRC') === 0) {
            return 'webchat';
        }

        return 'irc';
    }

    /**
     * @return array{
     *     users: array<int,array<string,mixed>>,
     *     total: int
     * }
     */
    private function emptyChatResult(): array
    {
        return [
            'users' => [],
            'total' => 0,
        ];
    }

    private function isServiceOrBot(
        array $channelUser
    ): bool {
        if (!empty($channelUser['service'])) {
            return true;
        }

        if (!empty($channelUser['bot'])) {
            return true;
        }

        $hostname = strtolower(
            trim(
                (string) ($channelUser['hostname'] ?? '')
            )
        );

        $username = strtolower(
            trim(
                (string) ($channelUser['username'] ?? '')
            )
        );

        $realname = strtolower(
            trim(
                (string) ($channelUser['realname'] ?? '')
            )
        );

        return str_contains($hostname, 'bot.')
            || str_contains($username, 'bot')
            || str_contains($realname, 'bot');
    }
}