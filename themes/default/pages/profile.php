<?php
declare(strict_types=1);

$profile = is_array($profile ?? null) ? $profile : [];
$interests = is_array($interests ?? null) ? $interests : [];
$moderation = is_array($moderation ?? null)
    ? $moderation
    : [];
$irc = is_array($irc ?? null) ? $irc : [];
$followersCount = (int) ($followersCount ?? 0);
$followingCount = (int) ($followingCount ?? 0);
$currentUser = is_array($user ?? null) ? $user : [];
$posts = is_array($posts ?? null)
    ? $posts
    : [];

$widgetAreas = is_array($widgetAreas ?? null)
    ? $widgetAreas
    : [];

$widgetsBeforeContent = trim(
    (string) ($widgetAreas['beforeContent'] ?? '')
);

$widgetsSidebar = trim(
    (string) ($widgetAreas['sidebar'] ?? '')
);

$widgetsAfterContent = trim(
    (string) ($widgetAreas['afterContent'] ?? '')
);

$isLogged = !empty($currentUser['sub']);
$isModerator = !empty($currentUser['is_moderator']);

$isOwnProfile = $isLogged
    && ($currentUser['sub'] ?? '') === ($profile['oauth_sub'] ?? '');

$canModerate = $isModerator && !$isOwnProfile;
$isFollowing = (bool) ($isFollowing ?? false);

$cryptoTipsEnabled = (
    ($settings['crypto_tips_enabled'] ?? '0') === '1'
);

$cryptoTipsProfilesEnabled = (
    ($settings['crypto_tips_profiles_enabled'] ?? '1') === '1'
);

$dogeTipSource = trim(
    (string) ($profile['doge_tip_source'] ?? '')
);

$dogeTipAddress = trim(
    (string) ($dogeTipAddress ?? '')
);

$canReceiveDogeTip = (
    $cryptoTipsEnabled
    && $cryptoTipsProfilesEnabled
    && in_array(
        $dogeTipSource,
        [
            'mydogemask',
            'simosnap',
        ],
        true
    )
    && $dogeTipAddress !== ''
);

$username = trim(
    (string) (
        $profile['username']
        ?? $t('profile.user')
    )
);

$avatarUrl = trim(
    (string) ($profile['avatar_url'] ?? '')
);

$bio = trim((string) ($profile['bio'] ?? ''));
$motto = trim((string) ($profile['motto'] ?? ''));
$website = trim((string) ($profile['website'] ?? ''));
$telegram = trim((string) ($profile['telegram'] ?? ''));

$showAvatar = !empty($profile['show_avatar']);
$showAliases = !empty($profile['show_aliases']);
$showAge = !empty($profile['show_age']);
$showCity = !empty($profile['show_city']);
$showSex = !empty($profile['show_sex']);
$showIrcStats = !empty($profile['show_irc_stats']);

$age = trim((string) ($profile['age'] ?? ''));
$city = trim((string) ($profile['city'] ?? ''));
$sex = (string) ($profile['sex'] ?? 'U');

$sexLabels = [
    'M' => $t('profile.sex.male'),
    'F' => $t('profile.sex.female'),
    'O' => $t('profile.sex.other'),
];

$aliases = [];

if ($showAliases && !empty($profile['aliases'])) {
    $decodedAliases = json_decode(
        (string) $profile['aliases'],
        true
    );

    if (is_array($decodedAliases)) {
        $aliases = array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($alias): string => trim(
                            (string) $alias
                        ),
                        $decodedAliases
                    ),
                    static fn (string $alias): bool => $alias !== ''
                )
            )
        );
    }
}

$telegramUsername = ltrim($telegram, '@');

$hasPublicDetails = (
    ($showAge && $age !== '')
    || ($showCity && $city !== '')
    || ($showSex && isset($sexLabels[$sex]))
);

$ircProfile = is_array($irc['profile'] ?? null)
    ? $irc['profile']
    : [];

$ircChannels = is_array($irc['channels'] ?? null)
    ? $irc['channels']
    : [];

$ircActivity = is_array($irc['activity'] ?? null)
    ? $irc['activity']
    : [];

$ircTotalActivity = [];

foreach ($ircActivity as $activityRow) {
    if (
        is_array($activityRow)
        && ($activityRow['type'] ?? '') === 'total'
    ) {
        $ircTotalActivity = $activityRow;
        break;
    }
}

$hasIrcStats = (
    $showIrcStats
    && $ircProfile !== []
);
?>

<section class="mv-public-profile">

    <?php if ($hasBlocked): ?>

        <div class="mv-public-profile-alert mv-public-profile-alert-warning">

            <i
                class="fa-solid fa-user-slash"
                aria-hidden="true"
            ></i>

            <div>

                <strong>
                    <?= htmlspecialchars(
                        $t('profile.blocked.you_blocked_title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <span>
                    <?= htmlspecialchars(
                        $t('profile.blocked.you_blocked_text'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

        </div>

    <?php elseif ($isBlockedBy): ?>

        <div class="mv-public-profile-alert">

            <i
                class="fa-solid fa-ban"
                aria-hidden="true"
            ></i>

            <div>

                <strong>
                    <?= htmlspecialchars(
                        $t('profile.blocked.blocked_by_title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <span>
                    <?= htmlspecialchars(
                        $t('profile.blocked.blocked_by_text'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

        </div>

    <?php endif; ?>

    <?php if ($widgetsBeforeContent !== ''): ?>

        <section
            class="mv-block-area mv-public-profile-widget-area mv-public-profile-widget-area-before"
            aria-label="<?= htmlspecialchars(
                $t('profile.areas.before'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
            <?= $widgetsBeforeContent ?>
        </section>

    <?php endif; ?>

    <div class="mv-public-profile-layout">

        <main class="mv-public-profile-timeline">

            <?php
            $profileFeed = (string) ($feed ?? 'all');

            $profileUrl = '/profile/' . rawurlencode(
                (string) ($profile['username'] ?? '')
            );
            ?>

            <nav
                class="ping-feed-tabs"
                aria-label="<?= htmlspecialchars(
                    $t('profile.pings.filters_label'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

                <a
                    href="<?= htmlspecialchars(
                        $profileUrl,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    class="ping-feed-tab <?= $profileFeed === 'all'
                        ? 'is-active'
                        : '' ?>"
                >
                    <?= htmlspecialchars(
                        $t('profile.pings.filters.all'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

                <a
                    href="<?= htmlspecialchars(
                        $profileUrl . '?feed=audio',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    class="ping-feed-tab <?= $profileFeed === 'audio'
                        ? 'is-active'
                        : '' ?>"
                >
                    <?= htmlspecialchars(
                        $t('profile.pings.filters.audio'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

                <a
                    href="<?= htmlspecialchars(
                        $profileUrl . '?feed=video',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    class="ping-feed-tab <?= $profileFeed === 'video'
                        ? 'is-active'
                        : '' ?>"
                >
                    <?= htmlspecialchars(
                        $t('profile.pings.filters.video'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

                <?php if ($isLogged && !$isOwnProfile): ?>

                    <a
                        href="<?= htmlspecialchars(
                            $profileUrl . '?feed=interactions',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="ping-feed-tab <?= $profileFeed === 'interactions'
                            ? 'is-active'
                            : '' ?>"
                    >
                        <?= htmlspecialchars(
                            $t('profile.pings.filters.interactions'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>

                <?php endif; ?>

            </nav>

            <?php if ($posts === []): ?>

                <div class="mv-public-profile-pings-empty">

                    <span class="mv-public-profile-pings-empty-icon">

                        <i
                            class="fa-regular fa-message"
                            aria-hidden="true"
                        ></i>

                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $t('profile.pings.empty_title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                    <p>
                        <?= htmlspecialchars(
                            $t('profile.pings.empty_text'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            <?php else: ?>

                <div
                    class="mv-public-profile-pings-list"
                    id="profile-ping-infinite-list"
                    data-next-offset="<?= count($posts) ?>"
                    data-page-size="<?= (int) $pageSize ?>"
                    data-username="<?= htmlspecialchars(
                        (string) ($profile['username'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    data-feed="<?= htmlspecialchars(
                        $profileFeed,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                    <?php foreach ($posts as $post): ?>

                        <?= $component('ping-card', [
                            'post' => $post,
                            'user' => $currentUser,
                            'session' => $session,
                        ]) ?>

                    <?php endforeach; ?>

                </div>

                <?php if (count($posts) >= $pageSize): ?>

                    <div
                        id="profile-ping-infinite-trigger"
                        aria-hidden="true"
                    ></div>

                <?php endif; ?>

            <?php endif; ?>

        </main>

        <aside class="mv-public-profile-sidebar">

            <details
                class="mv-public-profile-sidebar-details"
                open
            >

                <summary class="mv-public-profile-sidebar-summary">

                    <span>

                        <i
                            class="fa-solid fa-user"
                            aria-hidden="true"
                        ></i>

                        <?= htmlspecialchars(
                            $t('profile.sidebar.profile_details'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>

                    <i
                        class="fa-solid fa-chevron-down mv-public-profile-sidebar-chevron"
                        aria-hidden="true"
                    ></i>

                </summary>

                <div class="mv-public-profile-sidebar-content">

                    <section class="mv-public-profile-card mv-public-profile-identity-card">

                        <div class="mv-public-profile-identity-top">

                            <?php if ($showAvatar): ?>

                                <div class="mv-public-profile-avatar">

                                    <?php if ($avatarUrl !== ''): ?>

                                        <img
                                            src="<?= htmlspecialchars(
                                                $avatarUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            alt=""
                                        >

                                    <?php else: ?>

                                        <span>
                                            <?= htmlspecialchars(
                                                mb_strtoupper(
                                                    mb_substr(
                                                        $username,
                                                        0,
                                                        1
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

                            <?php endif; ?>

                            <div class="mv-public-profile-identity-name">

                                <div class="mv-public-profile-name-row">

                                    <?= $component('user-presence', [
                                        'presence' => $presence ?? [],
                                    ]) ?>

                                    <h2>
                                        <?= htmlspecialchars(
                                            $username,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </h2>

                                </div>

                                <?php if (!empty($isProfileModerator)): ?>

                                    <div class="mv-public-profile-role">

                                        <i
                                            class="fa-solid fa-shield-halved"
                                            aria-hidden="true"
                                        ></i>

                                        <span>
                                            <?= htmlspecialchars(
                                                $t('profile.roles.moderator'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                        <?php if ($motto !== ''): ?>

                            <p class="mv-public-profile-motto">
                                <?= htmlspecialchars(
                                    $motto,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>

                        <?php endif; ?>

                        <?php if ($bio !== ''): ?>

                            <div class="mv-public-profile-bio">
                                <?= nl2br(
                                    htmlspecialchars(
                                        $bio,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ) ?>
                            </div>

                        <?php endif; ?>

                        <?php if ($hasPublicDetails): ?>

                            <div class="mv-public-profile-meta">

                                <?php if ($showAge && $age !== ''): ?>

                                    <span>

                                        <i
                                            class="fa-regular fa-calendar"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $age,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        <?= htmlspecialchars(
                                            $t('profile.details.years'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                                <?php if ($showCity && $city !== ''): ?>

                                    <span>

                                        <i
                                            class="fa-solid fa-location-dot"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $city,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                                <?php if (
                                    $showSex
                                    && isset($sexLabels[$sex])
                                ): ?>

                                    <span>

                                        <i
                                            class="fa-regular fa-user"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $sexLabels[$sex],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                        <div class="mv-public-profile-follow-stats">

                            <div>

                                <strong>
                                    <?= number_format(
                                        $followersCount,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $t('profile.details.followers'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </div>

                            <div>

                                <strong>
                                    <?= number_format(
                                        $followingCount,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $t('profile.details.following'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </div>

                            <div>

                                <strong>
                                    <?= number_format(
                                        (int) $totalPosts,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars(
                                        $t('profile.details.pings'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </div>

                        </div>

                        <?php if (
                            $isLogged
                            && !$isOwnProfile
                            && !$hasBlocked
                            && !$isBlockedBy
                        ): ?>

                            <div class="mv-public-profile-actions">

                                <?php if ($canReceiveDogeTip): ?>

                                    <button
                                        type="button"
                                        class="mv-button mv-button-doge js-doge-tip"
                                        data-doge-address="<?= htmlspecialchars(
                                            $dogeTipAddress,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        data-doge-username="<?= htmlspecialchars(
                                            $username,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >

                                        <i
                                            class="fa-solid fa-dog"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $t('profile.actions.send_doge'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </button>

                                <?php endif; ?>

                                <form
                                    method="post"
                                    action="<?= $isFollowing
                                        ? '/profile/' . urlencode(
                                            $profile['username']
                                        ) . '/unfollow'
                                        : '/profile/' . urlencode(
                                            $profile['username']
                                        ) . '/follow' ?>"
                                >

                                    <button
                                        type="submit"
                                        class="mv-button <?= $isFollowing
                                            ? ''
                                            : 'mv-button-primary' ?>"
                                    >

                                        <i
                                            class="fa-solid <?= $isFollowing
                                                ? 'fa-user-minus'
                                                : 'fa-user-plus' ?>"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $isFollowing
                                                ? $t('profile.actions.unfollow')
                                                : $t('profile.actions.follow'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </button>

                                </form>

                                <form
                                    method="post"
                                    action="/profile/<?= urlencode(
                                        $profile['username']
                                    ) ?>/block"
                                    onsubmit="return confirm(<?= htmlspecialchars(
                                        json_encode(
                                            $t('profile.actions.block_confirm'),
                                            JSON_HEX_APOS
                                            | JSON_HEX_QUOT
                                            | JSON_UNESCAPED_UNICODE
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>);"
                                >

                                    <button
                                        type="submit"
                                        class="mv-button mv-button-danger"
                                    >

                                        <i
                                            class="fa-solid fa-user-slash"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $t('profile.actions.block'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </button>

                                </form>

                            </div>

                        <?php endif; ?>

                    </section>

                    <?php if ($isOwnProfile): ?>

                        <section class="mv-public-profile-card mv-public-profile-shortcuts">

                            <header class="mv-public-profile-card-header">

                                <h2>
                                    <?= htmlspecialchars(
                                        $t('profile.shortcuts.title'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                            </header>

                            <nav
                                class="mv-public-profile-shortcuts-nav"
                                aria-label="<?= htmlspecialchars(
                                    $t('profile.shortcuts.title'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                                <a href="/account">

                                    <i
                                        class="fa-solid fa-gear"
                                        aria-hidden="true"
                                    ></i>

                                    <span>
                                        <?= htmlspecialchars(
                                            $t('profile.shortcuts.account'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <i
                                        class="fa-solid fa-chevron-right"
                                        aria-hidden="true"
                                    ></i>

                                </a>

                                <a href="/account/profile">

                                    <i
                                        class="fa-regular fa-id-card"
                                        aria-hidden="true"
                                    ></i>

                                    <span>
                                        <?= htmlspecialchars(
                                            $t('profile.shortcuts.edit_profile'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <i
                                        class="fa-solid fa-chevron-right"
                                        aria-hidden="true"
                                    ></i>

                                </a>

                                <a href="/account/saved">

                                    <i
                                        class="fa-regular fa-bookmark"
                                        aria-hidden="true"
                                    ></i>

                                    <span>
                                        <?= htmlspecialchars(
                                            $t('profile.shortcuts.saved'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <i
                                        class="fa-solid fa-chevron-right"
                                        aria-hidden="true"
                                    ></i>

                                </a>

                                <a href="/account/blocked">

                                    <i
                                        class="fa-solid fa-user-slash"
                                        aria-hidden="true"
                                    ></i>

                                    <span>
                                        <?= htmlspecialchars(
                                            $t('profile.shortcuts.blocked'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <i
                                        class="fa-solid fa-chevron-right"
                                        aria-hidden="true"
                                    ></i>

                                </a>

                                <?php if ($isModerator): ?>

                                    <a href="/account/moderation">

                                        <i
                                            class="fa-solid fa-shield-halved"
                                            aria-hidden="true"
                                        ></i>

                                        <span>
                                            <?= htmlspecialchars(
                                                $t('profile.shortcuts.moderation'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                        <i
                                            class="fa-solid fa-chevron-right"
                                            aria-hidden="true"
                                        ></i>

                                    </a>

                                <?php endif; ?>

                            </nav>

                        </section>

                    <?php endif; ?>

                    <?php if ($hasIrcStats): ?>

                        <section class="mv-public-profile-card">

                            <header class="mv-public-profile-card-header">

                                <h2>
                                    <?= htmlspecialchars(
                                        $t('profile.irc.title'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                                <span
                                    class="mv-public-profile-irc-state <?= !empty(
                                        $ircProfile['online']
                                    )
                                        ? 'is-online'
                                        : 'is-offline' ?>"
                                >

                                    <i
                                        class="fa-solid fa-circle"
                                        aria-hidden="true"
                                    ></i>

                                    <?= htmlspecialchars(
                                        !empty($ircProfile['online'])
                                            ? $t('profile.irc.connected')
                                            : $t('profile.irc.not_connected'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            </header>

                            <dl class="mv-public-profile-side-stats">

                                <?php if (!empty($ircProfile['operator'])): ?>

                                    <div>

                                        <dt>
                                            <?= htmlspecialchars(
                                                $t('profile.irc.role'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </dt>

                                        <dd>
                                            <?= htmlspecialchars(
                                                trim(
                                                    (string) (
                                                        $ircProfile['operator_level']
                                                        ?? $t('profile.irc.operator')
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </dd>

                                    </div>

                                <?php endif; ?>

                                <div>

                                    <dt>
                                        <?= htmlspecialchars(
                                            $t('profile.irc.channels'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </dt>

                                    <dd>
                                        <?= number_format(
                                            count($ircChannels),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </dd>

                                </div>

                                <div>

                                    <dt>
                                        <?= htmlspecialchars(
                                            $t('profile.irc.messages'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </dt>

                                    <dd>
                                        <?= number_format(
                                            (int) (
                                                $ircTotalActivity['lines']
                                                ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </dd>

                                </div>

                                <div>

                                    <dt>
                                        <?= htmlspecialchars(
                                            $t('profile.irc.words'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </dt>

                                    <dd>
                                        <?= number_format(
                                            (int) (
                                                $ircTotalActivity['words']
                                                ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </dd>

                                </div>

                                <div>

                                    <dt>
                                        <?= htmlspecialchars(
                                            $t('profile.irc.characters'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </dt>

                                    <dd>
                                        <?= number_format(
                                            (int) (
                                                $ircTotalActivity['letters']
                                                ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </dd>

                                </div>

                            </dl>

                        </section>

                    <?php endif; ?>

                    <?php if ($interests !== []): ?>

                        <section class="mv-public-profile-card">

                            <header class="mv-public-profile-card-header">

                                <h2>
                                    <?= htmlspecialchars(
                                        $t('profile.sections.interests'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                            </header>

                            <div class="mv-public-profile-side-tags">

                                <?php foreach ($interests as $interest): ?>

                                    <span>
                                        <?= htmlspecialchars(
                                            (string) $interest,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                <?php endforeach; ?>

                            </div>

                        </section>

                    <?php endif; ?>

                    <?php if ($aliases !== []): ?>

                        <section class="mv-public-profile-card">

                            <header class="mv-public-profile-card-header">

                                <h2>
                                    <?= htmlspecialchars(
                                        $t('profile.sections.aliases'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                            </header>

                            <div class="mv-public-profile-side-tags">

                                <?php foreach ($aliases as $alias): ?>

                                    <span>
                                        <?= htmlspecialchars(
                                            $alias,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                <?php endforeach; ?>

                            </div>

                        </section>

                    <?php endif; ?>

                    <?php if (
                        $website !== ''
                        || $telegramUsername !== ''
                    ): ?>

                        <section class="mv-public-profile-card">

                            <header class="mv-public-profile-card-header">

                                <h2>
                                    <?= htmlspecialchars(
                                        $t('profile.sections.links'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                            </header>

                            <div class="mv-public-profile-side-links">

                                <?php if ($website !== ''): ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $website,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >

                                        <i
                                            class="fa-solid fa-globe"
                                            aria-hidden="true"
                                        ></i>

                                        <span>
                                            <?= htmlspecialchars(
                                                $t('profile.sections.website'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                        <i
                                            class="fa-solid fa-arrow-up-right-from-square"
                                            aria-hidden="true"
                                        ></i>

                                    </a>

                                <?php endif; ?>

                                <?php if ($telegramUsername !== ''): ?>

                                    <a
                                        href="https://t.me/<?= rawurlencode(
                                            $telegramUsername
                                        ) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >

                                        <i
                                            class="fa-brands fa-telegram"
                                            aria-hidden="true"
                                        ></i>

                                        <span>
                                            <?= htmlspecialchars(
                                                $t('profile.sections.telegram'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                        <i
                                            class="fa-solid fa-arrow-up-right-from-square"
                                            aria-hidden="true"
                                        ></i>

                                    </a>

                                <?php endif; ?>

                            </div>

                        </section>

                    <?php endif; ?>

                    <?php if ($canModerate): ?>

                        <section class="mv-public-profile-card mv-profile-moderation">

                            <header class="mv-public-profile-card-header">

                                <h2>
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.title'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                                <?php if (!$isMuted && !$isBanned): ?>

                                    <span class="mv-profile-moderation-state is-clear">

                                        <i
                                            class="fa-solid fa-circle-check"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $t('profile.moderation.clear'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </header>

                            <?php if ($isMuted || $isBanned): ?>

                                <div class="mv-profile-moderation-statuses">

                                    <?php if ($isMuted): ?>

                                        <div class="mv-profile-moderation-status is-muted">

                                            <div class="mv-profile-moderation-status-icon">

                                                <i
                                                    class="fa-solid fa-volume-xmark"
                                                    aria-hidden="true"
                                                ></i>

                                            </div>

                                            <div class="mv-profile-moderation-status-content">

                                                <strong>
                                                    <?= htmlspecialchars(
                                                        $t('profile.moderation.muted_title'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </strong>

                                                <span>
                                                    <?= htmlspecialchars(
                                                        $t('profile.moderation.muted_text'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>

                                                <?php if (
                                                    !empty(
                                                        $moderation['mute_reason']
                                                    )
                                                ): ?>

                                                    <small>

                                                        <strong>
                                                            <?= htmlspecialchars(
                                                                $t('profile.moderation.reason'),
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>
                                                        </strong>

                                                        <?= htmlspecialchars(
                                                            (string) $moderation['mute_reason'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>

                                                    </small>

                                                <?php endif; ?>

                                            </div>

                                            <form
                                                method="post"
                                                action="/profile/<?= rawurlencode(
                                                    (string) $profile['username']
                                                ) ?>/unmute"
                                            >

                                                <button
                                                    type="submit"
                                                    class="mv-profile-moderation-button is-revoke"
                                                >
                                                    <?= htmlspecialchars(
                                                        $t('profile.moderation.reactivate'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </button>

                                            </form>

                                        </div>

                                    <?php endif; ?>

                                    <?php if ($isBanned): ?>

                                        <div class="mv-profile-moderation-status is-banned">

                                            <div class="mv-profile-moderation-status-icon">

                                                <i
                                                    class="fa-solid fa-user-slash"
                                                    aria-hidden="true"
                                                ></i>

                                            </div>

                                            <div class="mv-profile-moderation-status-content">

                                                <strong>
                                                    <?= htmlspecialchars(
                                                        $t('profile.moderation.banned_title'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </strong>

                                                <span>
                                                    <?= htmlspecialchars(
                                                        $t('profile.moderation.banned_text'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>

                                                <?php if (
                                                    !empty(
                                                        $moderation['ban_reason']
                                                    )
                                                ): ?>

                                                    <small>

                                                        <strong>
                                                            <?= htmlspecialchars(
                                                                $t('profile.moderation.reason'),
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>
                                                        </strong>

                                                        <?= htmlspecialchars(
                                                            (string) $moderation['ban_reason'],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>

                                                    </small>

                                                <?php endif; ?>

                                            </div>

                                            <form
                                                method="post"
                                                action="/profile/<?= rawurlencode(
                                                    (string) $profile['username']
                                                ) ?>/unban"
                                            >

                                                <button
                                                    type="submit"
                                                    class="mv-profile-moderation-button is-revoke"
                                                >
                                                    <?= htmlspecialchars(
                                                        $t('profile.moderation.reactivate'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </button>

                                            </form>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            <?php endif; ?>

                            <div class="mv-profile-moderation-actions">

                                <?php if (!$isMuted): ?>

                                    <button
                                        type="button"
                                        class="mv-profile-moderation-button is-mute js-open-moderation-modal"
                                        data-action="/profile/<?= rawurlencode(
                                            (string) $profile['username']
                                        ) ?>/mute"
                                        data-title="<?= htmlspecialchars(
                                            $t('profile.moderation.mute_user'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        data-submit="<?= htmlspecialchars(
                                            $t('profile.moderation.mute'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        data-type="mute"
                                    >

                                        <i
                                            class="fa-solid fa-volume-xmark"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $t('profile.moderation.mute'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </button>

                                <?php endif; ?>

                                <?php if (!$isBanned): ?>

                                    <button
                                        type="button"
                                        class="mv-profile-moderation-button is-ban js-open-moderation-modal"
                                        data-action="/profile/<?= rawurlencode(
                                            (string) $profile['username']
                                        ) ?>/ban"
                                        data-title="<?= htmlspecialchars(
                                            $t('profile.moderation.ban_user'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        data-submit="<?= htmlspecialchars(
                                            $t('profile.moderation.ban'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        data-type="ban"
                                    >

                                        <i
                                            class="fa-solid fa-user-slash"
                                            aria-hidden="true"
                                        ></i>

                                        <?= htmlspecialchars(
                                            $t('profile.moderation.ban'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </button>

                                <?php endif; ?>

                            </div>

                        </section>

                    <?php endif; ?>

                    <?php if ($widgetsSidebar !== ''): ?>

                        <div
                            class="mv-block-area mv-public-profile-widget-area mv-public-profile-widget-area-sidebar"
                            aria-label="<?= htmlspecialchars(
                                $t('profile.areas.sidebar'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >
                            <?= $widgetsSidebar ?>
                        </div>

                    <?php endif; ?>

                </div>

            </details>

        </aside>

    </div>

    <?php if ($widgetsAfterContent !== ''): ?>

        <section
            class="mv-block-area mv-public-profile-widget-area mv-public-profile-widget-area-after"
            aria-label="<?= htmlspecialchars(
                $t('profile.areas.after'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
            <?= $widgetsAfterContent ?>
        </section>

    <?php endif; ?>

    <?php if ($canModerate): ?>

        <div
            id="mv-moderation-modal"
            class="mv-modal"
            hidden
        >

            <div class="mv-modal-backdrop"></div>

            <div
                class="mv-modal-dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="mv-modal-title"
            >

                <form
                    id="mv-moderation-form"
                    method="post"
                    action=""
                >

                    <div class="mv-modal-header">

                        <h3 id="mv-modal-title">
                            <?= htmlspecialchars(
                                $t('profile.moderation.modal_title'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h3>

                        <button
                            type="button"
                            class="mv-modal-close"
                            aria-label="<?= htmlspecialchars(
                                $t('profile.moderation.close'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >
                            &times;
                        </button>

                    </div>

                    <div class="mv-modal-body">

                        <div class="mv-form-group">

                            <label for="moderation-reason">
                                <?= htmlspecialchars(
                                    $t('profile.moderation.reason_label'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </label>

                            <textarea
                                id="moderation-reason"
                                name="reason"
                                rows="4"
                                placeholder="<?= htmlspecialchars(
                                    $t('profile.moderation.reason_placeholder'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            ></textarea>

                        </div>

                        <div class="mv-form-group">

                            <label for="moderation-duration">
                                <?= htmlspecialchars(
                                    $t('profile.moderation.duration'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </label>

                            <select
                                id="moderation-duration"
                                name="duration"
                            >

                                <option value="0">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.permanent'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="900">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.15_minutes'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="1800">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.30_minutes'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="3600">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.1_hour'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="21600">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.6_hours'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="43200">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.12_hours'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="86400">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.1_day'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="259200">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.3_days'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="604800">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.7_days'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                                <option value="2592000">
                                    <?= htmlspecialchars(
                                        $t('profile.moderation.duration_options.30_days'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mv-modal-footer">

                        <button
                            type="button"
                            class="mv-button mv-modal-cancel"
                        >
                            <?= htmlspecialchars(
                                $t('profile.moderation.cancel'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </button>

                        <button
                            id="mv-modal-submit"
                            type="submit"
                            class="mv-button mv-button-primary"
                        >
                            <?= htmlspecialchars(
                                $t('profile.moderation.confirm'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </button>

                    </div>

                </form>

            </div>

        </div>

    <?php endif; ?>

</section>
