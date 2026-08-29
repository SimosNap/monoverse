<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\DateHelper;

class ProfileService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function findBySub(string $sub): array|false
    {
        return $this->database->fetchOne(
            'SELECT * FROM profiles WHERE oauth_sub = ? LIMIT 1',
            [$sub]
        );
    }

    public function findExternalAccountDueForCheck(
        int $maxAge = 86400
    ): array|false {
        $checkBefore = time() - max(3600, $maxAge);

        return $this->database->fetchOne(
            'SELECT
                oauth_sub,
                oauth_uid,
                username,
                external_account_exists,
                external_account_checked_at
             FROM profiles
             WHERE (
                    (
                        oauth_uid IS NOT NULL
                        AND oauth_uid <> \'\'
                        AND oauth_uid <> \'0\'
                    )
                    OR username <> \'\'
                  )
               AND (
                    external_account_checked_at IS NULL
                    OR external_account_checked_at < ?
               )
             ORDER BY
                 external_account_checked_at IS NULL DESC,
                 external_account_checked_at ASC,
                 id ASC
             LIMIT 1',
            [
                $checkBefore,
            ]
        );
    }

    public function markExternalAccountExisting(string $sub): bool
    {
        $now = time();

        return $this->database->execute(
            'UPDATE profiles
             SET external_account_exists = 1,
                 external_account_checked_at = ?,
                 external_account_missing_at = NULL,
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                $now,
                $now,
                $sub,
            ]
        );
    }

    public function markExternalAccountMissing(string $sub): bool
    {
        $now = time();

        return $this->database->execute(
            'UPDATE profiles
             SET external_account_exists = 0,
                 external_account_checked_at = ?,
                 external_account_missing_at = COALESCE(
                     external_account_missing_at,
                     ?
                 ),
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                $now,
                $now,
                $now,
                $sub,
            ]
        );
    }

    public function updateExternalAccountUid(
        string $sub,
        string $uid
    ): bool {
        $uid = trim($uid);

        if ($uid === '' || $uid === '0') {
            return false;
        }

        return $this->database->execute(
            'UPDATE profiles
             SET oauth_uid = ?,
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                $uid,
                time(),
                $sub,
            ]
        );
    }

    public function findPublicByUsername(string $username): array|false
    {
        return $this->database->fetchOne(
            'SELECT
                profiles.*,
                mv_users.role AS user_role
             FROM profiles
             LEFT JOIN mv_users
                ON mv_users.sub = profiles.oauth_sub
             WHERE profiles.username = ?
               AND profiles.public_profile = 1
               AND profiles.external_account_exists = 1
             LIMIT 1',
            [$username]
        );
    }

    public function findByUsername(string $username): array|false
    {
        return $this->database->fetchOne(
            'SELECT *
             FROM profiles
             WHERE username = ?
             LIMIT 1',
            [$username]
        );
    }

    public function listRegisteredUsers(): array
    {
        return $this->database->fetchAll(
            'SELECT
                oauth_sub,
                username,
                avatar_url
             FROM profiles
             WHERE oauth_sub <> \'\'
               AND username <> \'\'
               AND external_account_exists = 1
             ORDER BY username ASC'
        );
    }

    public function searchMentionUsers(
        string $query,
        int $limit = 5
    ): array {
        $query = trim($query);
        $limit = max(1, min($limit, 10));

        if ($query === '') {
            return [];
        }

        return $this->database->fetchAll(
            'SELECT
                username,
                avatar_url,
                show_avatar
             FROM profiles
             WHERE oauth_sub <> \'\'
               AND username <> \'\'
               AND external_account_exists = 1
               AND username LIKE ?
             ORDER BY
                CASE
                    WHEN username LIKE ? THEN 0
                    ELSE 1
                END,
                username ASC
             LIMIT ' . $limit,
            [
                '%' . $query . '%',
                $query . '%',
            ]
        );
    }

    public function listPublicProfiles(
        int $limit = 20,
        int $offset = 0,
        string $search = ''
    ): array {
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        $where = [
            'public_profile = 1',
            'external_account_exists = 1',
            'username <> \'\'',
        ];

        $params = [];

        if ($search !== '') {
            $where[] = 'username LIKE ?';
            $params[] = '%' . $search . '%';
        }

        $sql = '
            SELECT
                username,
                avatar_url,
                motto,
                interests,
                show_avatar
            FROM profiles
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY username ASC
            LIMIT ' . $limit . '
            OFFSET ' . $offset;

        $profiles = $this->database->fetchAll(
            $sql,
            $params
        );

        foreach ($profiles as &$profile) {
            $decodedInterests = json_decode(
                (string) ($profile['interests'] ?? ''),
                true
            );

            $profile['interests'] = is_array($decodedInterests)
                ? array_values(
                    array_filter(
                        $decodedInterests,
                        static fn ($interest): bool =>
                            is_string($interest)
                            && trim($interest) !== ''
                    )
                )
                : [];
        }

        unset($profile);

        return $profiles;
    }

    public function latestPublicProfiles(
        int $limit = 5,
        string $locale = 'it'
    ): array {
        $limit = max(
            1,
            min(20, $limit)
        );

        $profiles = $this->database->fetchAll(
            'SELECT
                username,
                avatar_url,
                show_avatar,
                created_at
             FROM profiles
             WHERE public_profile = 1
               AND external_account_exists = 1
               AND username <> \'\'
             ORDER BY created_at DESC
             LIMIT ' . $limit
        );

        foreach ($profiles as &$profile) {

            $profile['created_at_human'] = DateHelper::timeAgo(
                (string) $profile['created_at'],
                true,
                $locale
            );
        }

        unset($profile);

        return $profiles;
    }

    public function mostActiveProfiles(
        int $limit = 20
    ): array {
        $limit = max(
            1,
            min(100, $limit)
        );

        return $this->database->fetchAll(
            '
            SELECT
                p.oauth_sub,
                p.username,
                p.nickname,
                p.avatar_url,
                p.show_avatar,

                (
                    SELECT COUNT(*)
                    FROM community_posts cp
                    WHERE cp.author_sub = p.oauth_sub
                      AND cp.status = ?
                      AND cp.deleted_at IS NULL
                      AND cp.published_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ) AS ping_count,

                (
                    SELECT COUNT(*)
                    FROM community_post_comments cc
                    WHERE cc.author_sub = p.oauth_sub
                      AND cc.status = ?
                      AND cc.deleted_at IS NULL
                      AND cc.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ) AS pong_count,

                (
                    SELECT COUNT(*)
                    FROM community_post_votes cv
                    WHERE cv.author_sub = p.oauth_sub
                      AND cv.vote > 0
                      AND cv.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ) AS upvote_count,

                (
                    SELECT COUNT(*)
                    FROM community_post_votes cv
                    WHERE cv.author_sub = p.oauth_sub
                      AND cv.vote < 0
                      AND cv.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ) AS downvote_count

            FROM profiles p

            WHERE p.public_profile = 1
              AND p.external_account_exists = 1
              AND p.username <> \'\'

            ORDER BY (
                ping_count
                + pong_count
                + upvote_count
                + downvote_count
            ) DESC,
            p.username ASC

            LIMIT ' . $limit,
            [
                'published',
                'published',
            ]
        );
    }

    public function listIndexableProfilesForSitemap(): array
    {
        return $this->database->fetchAll(
            'SELECT
                username,
                updated_at
             FROM profiles
             WHERE public_profile = 1
               AND allow_indexing = 1
               AND external_account_exists = 1
               AND username <> \'\'
             ORDER BY username ASC'
        );
    }

    public function countPublicProfiles(
        string $search = ''
    ): int {
        $where = [
            'public_profile = 1',
            'external_account_exists = 1',
            'username <> \'\'',
        ];

        $params = [];

        if ($search !== '') {
            $where[] = 'username LIKE ?';
            $params[] = '%' . $search . '%';
        }

        $result = $this->database->fetchOne(
            'SELECT COUNT(*) AS total
             FROM profiles
             WHERE ' . implode(' AND ', $where),
            $params
        );

        return (int) ($result['total'] ?? 0);
    }

    public function deleteBySub(string $sub): bool
    {
        return $this->database->execute(
            'DELETE FROM profiles WHERE oauth_sub = ?',
            [$sub]
        );
    }

    public function updateBio(string $sub, string $bio): bool
    {
        return $this->database->execute(
            'UPDATE profiles
             SET bio = ?, updated_at = ?
             WHERE oauth_sub = ?',
            [
                $bio,
                time(),
                $sub,
            ]
        );
    }

    public function updatePublicProfile(
        string $sub,
        string $bio,
        string $motto,
        array $interests,
        string $website,
        string $telegram
    ): bool {
        $interestsJson = json_encode(
            array_values($interests),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($interestsJson === false) {
            $interestsJson = '[]';
        }

        return $this->database->execute(
            'UPDATE profiles
             SET bio = ?,
                 motto = ?,
                 interests = ?,
                 website = ?,
                 telegram = ?,
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                $bio,
                $motto,
                $interestsJson,
                $website,
                $telegram,
                time(),
                $sub,
            ]
        );
    }

    public function updateDogeTipSettings(
        string $sub,
        ?string $source,
        ?string $address
    ): bool {
        $source = $source !== null
            ? trim($source)
            : null;

        $address = $address !== null
            ? trim($address)
            : null;

        if (
            $source !== null
            && !in_array(
                $source,
                [
                    'mydogemask',
                    'simosnap',
                ],
                true
            )
        ) {
            return false;
        }

        if ($source === 'simosnap') {
            $address = null;
        }

        if ($source === null) {
            $address = null;
        }

        return $this->database->execute(
            'UPDATE profiles
             SET doge_tip_source = ?,
                 doge_tip_address = ?,
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                $source,
                $address,
                time(),
                $sub,
            ]
        );
    }

    public function syncOAuthIdentity(
        string $sub,
        string $oauthUid,
        string $username,
        string $avatarUrl,
        array $aliases = []
    ): bool {
        $profile = $this->findBySub($sub);

        if ($profile === false) {
            $now = time();

            $aliases = array_values(
                array_unique(
                    array_filter(
                        array_map(
                            static fn ($alias): string => trim((string) $alias),
                            $aliases
                        ),
                        static fn (string $alias): bool => $alias !== ''
                    )
                )
            );

            $aliasesJson = json_encode(
                $aliases,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            if ($aliasesJson === false) {
                $aliasesJson = '[]';
            }

            return $this->database->execute(
                'INSERT INTO profiles
                (
                    oauth_sub,
                    oauth_uid,
                    username,
                    nickname,
                    avatar_url,
                    aliases,
                    public_profile,
                    allow_indexing,
                    external_account_exists,
                    external_account_checked_at,
                    created_at,
                    updated_at
                )
                VALUES (?, ?, ?, ?, ?, ?, 0, 0, 1, ?, ?, ?)',
                [
                    $sub,
                    $oauthUid,
                    $username,
                    $username,
                    $avatarUrl !== '' ? $avatarUrl : null,
                    $aliasesJson,
                    $now,
                    $now,
                    $now,
                ]
            );
        }

        $now = time();

        if (empty($profile['public_profile'])) {
            return $this->database->execute(
                'UPDATE profiles
                 SET oauth_uid = ?,
                     external_account_exists = 1,
                     external_account_checked_at = ?,
                     external_account_missing_at = NULL,
                     updated_at = ?
                 WHERE oauth_sub = ?',
                [
                    $oauthUid,
                    $now,
                    $now,
                    $sub,
                ]
            );
        }

        $aliases = array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($alias): string => trim((string) $alias),
                        $aliases
                    ),
                    static fn (string $alias): bool => $alias !== ''
                )
            )
        );

        $aliasesJson = json_encode(
            $aliases,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($aliasesJson === false) {
            $aliasesJson = '[]';
        }

        return $this->database->execute(
            'UPDATE profiles
             SET oauth_uid = ?,
                 username = ?,
                 nickname = ?,
                 avatar_url = ?,
                 aliases = ?,
                 external_account_exists = 1,
                 external_account_checked_at = ?,
                 external_account_missing_at = NULL,
                 updated_at = ?
             WHERE oauth_sub = ?',
            [
                $oauthUid,
                $username,
                $username,
                $avatarUrl,
                $aliasesJson,
                $now,
                $now,
                $sub,
            ]
        );
    }

    public function upsert(array $data): bool
    {
        $sub = (string) ($data['oauth_sub'] ?? '');

        if ($sub === '') {
            return false;
        }

        $profile = $this->findBySub($sub);
        $now = time();

        $username = (string) ($data['username'] ?? '');
        $oauthUid = trim(
            (string) ($data['oauth_uid'] ?? '')
        );

        if (
            $oauthUid === ''
            && $profile !== false
        ) {
            $oauthUid = trim(
                (string) ($profile['oauth_uid'] ?? '')
            );
        }
        $nickname = (string) ($data['nickname'] ?? '');
        $avatarUrl = (string) ($data['avatar_url'] ?? '');
        $age = (string) ($data['age'] ?? '');
        $city = (string) ($data['city'] ?? '');
        $sex = (string) ($data['sex'] ?? 'U');

        $aliases = $data['aliases'] ?? [];

        if (!is_array($aliases)) {
            $aliases = [];
        }

        $aliases = array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($alias): string => trim((string) $alias),
                        $aliases
                    ),
                    static fn (string $alias): bool => $alias !== ''
                )
            )
        );

        $aliasesJson = json_encode(
            $aliases,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($aliasesJson === false) {
            $aliasesJson = '[]';
        }

        $publicProfile = !empty($data['public_profile']) ? 1 : 0;
        $allowIndexing = !empty($data['allow_indexing']) ? 1 : 0;
        $showAvatar = !empty($data['show_avatar']) ? 1 : 0;
        $showAliases = !empty($data['show_aliases']) ? 1 : 0;
        $showAge = !empty($data['show_age']) ? 1 : 0;
        $showCity = !empty($data['show_city']) ? 1 : 0;
        $showSex = !empty($data['show_sex']) ? 1 : 0;
        $showIrcStats = !empty($data['show_irc_stats']) ? 1 : 0;

        if ($profile !== false) {
            return $this->database->execute(
                'UPDATE profiles
                 SET oauth_uid = ?,
                     username = ?,
                     nickname = ?,
                     avatar_url = ?,
                     aliases = ?,
                     age = ?,
                     city = ?,
                     sex = ?,
                     public_profile = ?,
                     allow_indexing = ?,
                     show_avatar = ?,
                     show_aliases = ?,
                     show_age = ?,
                     show_city = ?,
                     show_sex = ?,
                     show_irc_stats = ?,
                     updated_at = ?
                 WHERE oauth_sub = ?',
                [
                    $oauthUid,
                    $username,
                    $nickname,
                    $avatarUrl,
                    $aliasesJson,
                    $age,
                    $city,
                    $sex,
                    $publicProfile,
                    $allowIndexing,
                    $showAvatar,
                    $showAliases,
                    $showAge,
                    $showCity,
                    $showSex,
                    $showIrcStats,
                    $now,
                    $sub,
                ]
            );
        }

        return $this->database->execute(
            'INSERT INTO profiles
            (
                oauth_sub,
                oauth_uid,
                username,
                nickname,
                avatar_url,
                aliases,
                age,
                city,
                sex,
                public_profile,
                allow_indexing,
                show_avatar,
                show_aliases,
                show_age,
                show_city,
                show_sex,
                show_irc_stats,
                created_at,
                updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $sub,
                $oauthUid,
                $username,
                $nickname,
                $avatarUrl,
                $aliasesJson,
                $age,
                $city,
                $sex,
                $publicProfile,
                $allowIndexing,
                $showAvatar,
                $showAliases,
                $showAge,
                $showCity,
                $showSex,
                $showIrcStats,
                $now,
                $now,
            ]
        );
    }
}