<?php
declare(strict_types=1);

$title = trim(
    (string) (
        $title
        ?? $t(
            'blocks.community.users_in_chat.default_title'
        )
    )
);

$users = is_array($users ?? null)
    ? $users
    : [];

$total = max(
    0,
    (int) ($total ?? 0)
);

$showTotal = (bool) ($show_total ?? true);
$showAvatar = (bool) ($show_avatar ?? true);
$blockWidth = (int) ($block['width'] ?? 12);

if (!in_array($blockWidth, [3, 4, 6, 8, 9, 12], true)) {
    $blockWidth = 12;
}

$widthClass = 'mv-block-width-' . $blockWidth;

$connectionLabels = [
    'webchat' => 'Webchat',
    'irccloud' => 'IRCCloud',
    'bouncer' => 'Bouncer',
    'irc' => 'Client IRC',
];

$connectionIcons = [
    'webchat' => 'fa-globe',
    'irccloud' => 'fa-cloud',
    'bouncer' => 'fa-server',
    'irc' => 'fa-terminal',
];

$statusLabels = [
    'online' => 'Online',
    'away' => 'Away',
];
?>

<div class="mv-widget mv-users-in-chat-widget <?= htmlspecialchars(
    $widthClass,
    ENT_QUOTES,
    'UTF-8'
) ?>">

    <header class="mv-users-in-chat-header">

        <h3>
            <?= htmlspecialchars(
                $title,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h3>

        <?php if ($showTotal): ?>

            <p>
                <?= number_format(
                    $total,
                    0,
                    ',',
                    '.'
                ) ?>

                <?= htmlspecialchars(
                    $t(
                        'blocks.community.users_in_chat.total'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        <?php endif; ?>

    </header>

    <?php if ($users !== []): ?>

        <ul class="mv-users-in-chat-list">

            <?php foreach ($users as $user): ?>

                <?php
                $account = trim(
                    (string) ($user['account'] ?? '')
                );

                $profileUsername = trim(
                    (string) (
                        $user['profile_username'] ?? ''
                    )
                );

                $avatarUrl = trim(
                    (string) ($user['avatar_url'] ?? '')
                );

                $connections = is_array(
                    $user['connections'] ?? null
                )
                    ? $user['connections']
                    : [];

                if ($connections === []) {
                    continue;
                }

                $firstNickname = trim(
                    (string) (
                        $connections[0]['nickname']
                        ?? ''
                    )
                );

                $displayName = $account !== ''
                    ? $account
                    : $firstNickname;

                if ($displayName === '') {
                    continue;
                }

                $hasAvatar = $showAvatar
                    && !empty($user['show_avatar'])
                    && $avatarUrl !== '';

                $status = trim(
                    (string) ($user['status'] ?? 'online')
                );

                if (!isset($statusLabels[$status])) {
                    $status = 'online';
                }

                $isAccount = $account !== '';
                ?>

                <li class="mv-users-in-chat-item">

                    <div class="mv-users-in-chat-user">

                        <span class="mv-users-in-chat-avatar">

                            <?php if ($hasAvatar): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        $avatarUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt=""
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <span>
                                    <?= htmlspecialchars(
                                        mb_strtoupper(
                                            mb_substr(
                                                $displayName,
                                                0,
                                                1
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            <?php endif; ?>

                            <i
                                class="fa-solid fa-circle mv-users-in-chat-presence is-<?= htmlspecialchars(
                                    $status,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                aria-hidden="true"
                            ></i>

                        </span>

                        <div class="mv-users-in-chat-info">

                            <div class="mv-users-in-chat-identity">

                                <?php if ($profileUsername !== ''): ?>

                                    <a
                                        class="mv-users-in-chat-name"
                                        href="/profile/<?= rawurlencode(
                                            $profileUsername
                                        ) ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $displayName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </a>

                                <?php else: ?>

                                    <span class="mv-users-in-chat-name">
                                        <?= htmlspecialchars(
                                            $displayName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                <?php endif; ?>

                                <?php if ($isAccount): ?>

                                    <span class="mv-users-in-chat-account-label">
                                        <i
                                            class="fa-solid fa-user-check"
                                            aria-hidden="true"
                                        ></i>

                                        Account
                                    </span>

                                <?php endif; ?>

                            </div>

                            <div class="mv-users-in-chat-connections">

                                <?php foreach ($connections as $connection): ?>

                                    <?php
                                    $nickname = trim(
                                        (string) (
                                            $connection['nickname']
                                            ?? ''
                                        )
                                    );

                                    if ($nickname === '') {
                                        continue;
                                    }

                                    $connectionType = trim(
                                        (string) (
                                            $connection['connection']
                                            ?? 'irc'
                                        )
                                    );

                                    if (!isset(
                                        $connectionLabels[$connectionType]
                                    )) {
                                        $connectionType = 'irc';
                                    }

                                    $connectionStatus = trim(
                                        (string) (
                                            $connection['status']
                                            ?? 'online'
                                        )
                                    );

                                    if (!isset(
                                        $statusLabels[$connectionStatus]
                                    )) {
                                        $connectionStatus = 'online';
                                    }

                                    $connectionLabel =
                                        $connectionLabels[$connectionType];

                                    $connectionIcon =
                                        $connectionIcons[$connectionType];

                                    $statusLabel =
                                        $statusLabels[$connectionStatus];
                                    ?>

                                    <div class="mv-users-in-chat-connection">

                                        <span class="mv-users-in-chat-alias">

                                            <?php if ($isAccount): ?>

                                                <i
                                                    class="fa-solid fa-turn-up mv-users-in-chat-branch"
                                                    aria-hidden="true"
                                                ></i>

                                            <?php endif; ?>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $nickname,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>

                                        </span>

                                        <span class="mv-users-in-chat-client">

                                            <i
                                                class="fa-solid <?= htmlspecialchars(
                                                    $connectionIcon,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                aria-hidden="true"
                                            ></i>

                                            <?= htmlspecialchars(
                                                $connectionLabel,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>

                                        <span
                                            class="mv-users-in-chat-status is-<?= htmlspecialchars(
                                                $connectionStatus,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                            <i
                                                class="fa-solid fa-circle"
                                                aria-hidden="true"
                                            ></i>

                                            <?= htmlspecialchars(
                                                $statusLabel,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                        <?php if ($profileUsername !== ''): ?>

                            <a
                                class="mv-users-in-chat-profile-link"
                                href="/profile/<?= rawurlencode(
                                    $profileUsername
                                ) ?>"
                                aria-label="<?= htmlspecialchars(
                                    $displayName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                <i
                                    class="fa-solid fa-chevron-right"
                                    aria-hidden="true"
                                ></i>
                            </a>

                        <?php endif; ?>

                    </div>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php else: ?>

        <p class="mv-users-in-chat-empty">
            <?= htmlspecialchars(
                $t(
                    'blocks.community.users_in_chat.empty'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    <?php endif; ?>

</div>
