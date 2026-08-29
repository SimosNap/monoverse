<?php
declare(strict_types=1);

$currentPath = parse_url(
    (string) ($_SERVER['REQUEST_URI'] ?? '/account'),
    PHP_URL_PATH
);

$currentPath = is_string($currentPath)
    ? rtrim($currentPath, '/')
    : '/account';

if ($currentPath === '') {
    $currentPath = '/account';
}

$isPreferences = $currentPath === '/account';

$isProfile = str_starts_with(
    $currentPath,
    '/account/profile'
);

$isSaved = str_starts_with(
    $currentPath,
    '/account/saved'
);

$isArticles = str_starts_with(
    $currentPath,
    '/account/articles'
);

$isPrivacy = str_starts_with(
    $currentPath,
    '/account/blocked'
);

$isModeration = str_starts_with(
    $currentPath,
    '/account/moderation'
);

$isModerator = !empty($user['is_moderator'])
    || !empty($user['isModerator']);

$articleSubmissionsEnabled =
    (($settings['chanzine_user_submissions_enabled'] ?? '0') === '1');
?>

<nav
    class="mv-account-nav"
    aria-label="<?= htmlspecialchars(
        $t('account.navigation.aria_label'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>

    <a
        href="/account"
        class="<?= $isPreferences ? 'is-active' : '' ?>"
        <?= $isPreferences ? 'aria-current="page"' : '' ?>
    >
        <?= htmlspecialchars(
            $t('account.navigation.preferences'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </a>

    <a
        href="/account/profile"
        class="<?= $isProfile ? 'is-active' : '' ?>"
        <?= $isProfile ? 'aria-current="page"' : '' ?>
    >
        <?= htmlspecialchars(
            $t('account.navigation.profile'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </a>

    <a
        href="/account/saved"
        class="<?= $isSaved ? 'is-active' : '' ?>"
        <?= $isSaved ? 'aria-current="page"' : '' ?>
    >
        <?= htmlspecialchars(
            $t('account.navigation.saved'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </a>

    <?php if ($articleSubmissionsEnabled): ?>

        <a
            href="/account/articles"
            class="<?= $isArticles ? 'is-active' : '' ?>"
            <?= $isArticles ? 'aria-current="page"' : '' ?>
        >
            <?= htmlspecialchars(
                $t('account.navigation.articles'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>

    <?php endif; ?>

    <a
        href="/account/blocked"
        class="<?= $isPrivacy ? 'is-active' : '' ?>"
        <?= $isPrivacy ? 'aria-current="page"' : '' ?>
    >
        <?= htmlspecialchars(
            $t('account.navigation.privacy'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </a>

    <?php if ($isModerator): ?>

        <a
            href="/account/moderation"
            class="<?= $isModeration ? 'is-active' : '' ?>"
            <?= $isModeration ? 'aria-current="page"' : '' ?>
        >
            <?= htmlspecialchars(
                $t('account.navigation.moderation'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>

    <?php endif; ?>

    <a href="/account/logout">
        <?= htmlspecialchars(
            $t('account.navigation.logout'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </a>

</nav>
