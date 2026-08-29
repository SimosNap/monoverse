<?php
declare(strict_types=1);

$title = trim(
    (string) (
        $title
        ?? $t('blocks.community.latest_members.default_title')
    )
);

$members = is_array($members ?? null)
    ? $members
    : [];

$showAvatar = (bool) ($show_avatar ?? true);

$blockWidth = (int) ($block['width'] ?? 12);

if (!in_array(
    $blockWidth,
    [3, 4, 6, 8, 9, 12],
    true
)) {
    $blockWidth = 12;
}

$widthClass = 'mv-block-width-' . $blockWidth;
?>

<div class="mv-widget mv-latest-members-widget <?= htmlspecialchars(
    $widthClass,
    ENT_QUOTES,
    'UTF-8'
) ?>">

    <header class="mv-latest-members-header">

        <h3>
            <?= htmlspecialchars(
                $title,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h3>

    </header>

    <?php if ($members !== []): ?>

        <ul class="mv-latest-members-list">

            <?php foreach ($members as $member): ?>

                <?php
                $username = trim(
                    (string) ($member['username'] ?? '')
                );

                if ($username === '') {
                    continue;
                }

                $avatarUrl = trim(
                    (string) ($member['avatar_url'] ?? '')
                );

                $createdAt = trim(
                    (string) (
                        $member['created_at_human']
                        ?? ''
                    )
                );

                $hasAvatar = $showAvatar
                    && !empty($member['show_avatar'])
                    && $avatarUrl !== '';
                ?>

                <li>

                    <a
                        class="mv-latest-members-user"
                        href="/profile/<?= rawurlencode(
                            $username
                        ) ?>"
                    >

                        <span class="mv-latest-members-avatar">

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

                        </span>

                        <span class="mv-latest-members-body">

                            <strong class="mv-latest-members-name">
                                <?= htmlspecialchars(
                                    $username,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                            <?php if ($createdAt !== ''): ?>

                                <span class="mv-latest-members-date">

                                    <?= htmlspecialchars(
                                        $t(
                                            'blocks.community.latest_members.joined'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $createdAt,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </span>

                        <i
                            class="fa-solid fa-chevron-right"
                            aria-hidden="true"
                        ></i>

                    </a>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php else: ?>

        <p class="mv-latest-members-empty">
            <?= htmlspecialchars(
                $t(
                    'blocks.community.latest_members.empty'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    <?php endif; ?>

</div>
