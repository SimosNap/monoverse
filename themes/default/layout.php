<?php
declare(strict_types=1);

$user = is_array($user ?? null) ? $user : [];
$isLogged = !empty($user['sub']);

$currentProfile = is_array($currentProfile ?? null)
    ? $currentProfile
    : [];

$cryptoTipsEnabled = (
        ($settings['crypto_tips_enabled'] ?? '0') === '1'
    );

$myDogeConfigured = (
    $isLogged
    && ($currentProfile['doge_tip_source'] ?? '') === 'mydogemask'
    && trim(
        (string) ($currentProfile['doge_tip_address'] ?? '')
    ) !== ''
);

$notificationCount = (int) ($notificationCount ?? 0);

$pagesNavigationMain = (bool) (
    $pagesNavigationMain
    ?? true
);

$navigationPages = is_array(
    $navigationPages
    ?? null
)
    ? $navigationPages
    : [];

$currentLocale = trim(
        (string) ($currentLocale ?? 'it')
    );

    $availableLocales = is_array(
        $availableLocales
        ?? null
    )
        ? $availableLocales
        : [];

    $isMultilingual = (bool) (
        $isMultilingual
        ?? false
    );

$siteName = (string) ($settings['site_name'] ?? 'Monoverse');
$siteTagline = (string) (
    $settings['site_tagline']
    ?? 'IRC community websites by SimosNap'
);

$siteLogo = trim((string) ($settings['site_logo'] ?? ''));

$siteFavicon = trim((string) ($settings['site_favicon'] ?? ''));
$siteAppleTouchIcon = trim((string) ($settings['site_apple_touch_icon'] ?? ''));
$siteOgImage = trim((string) ($settings['site_og_image'] ?? ''));

$currentPath = parse_url(
    (string) ($_SERVER['REQUEST_URI'] ?? '/'),
    PHP_URL_PATH
);

$currentPath = is_string($currentPath) ? $currentPath : '/';

$isHome = $currentPath === '/';
$isAccount = str_starts_with($currentPath, '/account');
$configuredSiteUrl = rtrim(
    trim((string) ($settings['site_url'] ?? '')),
    '/'
);

$scheme = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || strtolower(
        trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))
    ) === 'https'
)
    ? 'https'
    : 'http';

$host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));

$detectedBaseUrl = $host !== ''
    ? $scheme . '://' . $host
    : '';

$baseUrl = $configuredSiteUrl !== ''
    ? $configuredSiteUrl
    : $detectedBaseUrl;

$absoluteUrl = static function (?string $value) use ($baseUrl): string {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (preg_match('~^https?://~i', $value) === 1) {
            return $value;
        }

        if ($baseUrl === '') {
            return '';
        }

        return $baseUrl . '/' . ltrim($value, '/');
    };

$pageTitle = trim((string) ($title ?? $siteName));

$defaultMetaDescription = trim(
    (string) ($settings['meta_description'] ?? '')
);

if ($defaultMetaDescription === '') {
    $defaultMetaDescription = trim($siteTagline);
}

$metaDescription = trim(
    (string) ($metaDescription ?? $defaultMetaDescription)
);

$canonicalPath = trim(
    (string) ($canonicalPath ?? $currentPath)
);

$canonicalUrl = $canonicalPath !== ''
    ? $absoluteUrl($canonicalPath)
    : '';

$openGraph = is_array($openGraph ?? null)
    ? $openGraph
    : [];

$ogType = trim((string) ($openGraph['type'] ?? 'website'));
$ogTitle = trim((string) ($openGraph['title'] ?? $pageTitle));
$ogDescription = trim(
    (string) ($openGraph['description'] ?? $metaDescription)
);

$ogUrl = $absoluteUrl(
    (string) ($openGraph['path'] ?? $canonicalPath)
);

$ogImage = $absoluteUrl(
    (string) (
        $openGraph['image']
        ?? ($siteOgImage !== ''
            ? 'storage/brand/' . $siteOgImage
            : '')
    )
);

$ogLocale = match ($currentLocale) {
    'en' => 'en_US',
    default => 'it_IT',
};

$ogPublishedTime = trim(
    (string) ($openGraph['publishedTime'] ?? '')
);
?>

<!doctype html>
<html lang="<?= htmlspecialchars(
    $currentLocale,
    ENT_QUOTES,
    'UTF-8'
) ?>">

<head>

    <meta charset="utf-8">

    <?php if ($siteFavicon !== ''): ?>

        <link
            rel="icon"
            href="<?= htmlspecialchars(
                '/storage/brand/' . $siteFavicon,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <?php if ($siteAppleTouchIcon !== ''): ?>

        <link
            rel="apple-touch-icon"
            href="<?= htmlspecialchars(
                '/storage/brand/' . $siteAppleTouchIcon,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <title>
        <?= htmlspecialchars(
            $pageTitle,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <?php if ($metaDescription !== ''): ?>

        <meta
            name="description"
            content="<?= htmlspecialchars(
                $metaDescription,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <?php if ($canonicalUrl !== ''): ?>

        <link
            rel="canonical"
            href="<?= htmlspecialchars(
                $canonicalUrl,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <meta
        property="og:site_name"
        content="<?= htmlspecialchars(
            $siteName,
            ENT_QUOTES,
            'UTF-8'
        ) ?>">

    <meta
    property="og:locale"
    content="<?= htmlspecialchars(
        $ogLocale,
        ENT_QUOTES,
        'UTF-8'
    ) ?>">

    <meta
        property="og:type"
        content="<?= htmlspecialchars(
            $ogType,
            ENT_QUOTES,
            'UTF-8'
        ) ?>">

    <meta
        property="og:title"
        content="<?= htmlspecialchars(
            $ogTitle,
            ENT_QUOTES,
            'UTF-8'
        ) ?>">

    <?php if ($ogDescription !== ''): ?>

        <meta
            property="og:description"
            content="<?= htmlspecialchars(
                $ogDescription,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <?php if ($ogUrl !== ''): ?>

        <meta
            property="og:url"
            content="<?= htmlspecialchars(
                $ogUrl,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <?php if ($ogImage !== ''): ?>

        <meta
            property="og:image"
            content="<?= htmlspecialchars(
                $ogImage,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

        <meta
            property="og:image:alt"
            content="<?= htmlspecialchars(
                $ogTitle,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <?php if (
        $ogType === 'article'
        && $ogPublishedTime !== ''
    ): ?>

        <meta
            property="article:published_time"
            content="<?= htmlspecialchars(
                $ogPublishedTime,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <meta
        name="twitter:card"
        content="<?= $ogImage !== ''
            ? 'summary_large_image'
            : 'summary' ?>">

    <meta
        name="twitter:title"
        content="<?= htmlspecialchars(
            $ogTitle,
            ENT_QUOTES,
            'UTF-8'
        ) ?>">

    <?php if ($ogDescription !== ''): ?>

        <meta
            name="twitter:description"
            content="<?= htmlspecialchars(
                $ogDescription,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <?php if ($ogImage !== ''): ?>

        <meta
            name="twitter:image"
            content="<?= htmlspecialchars(
                $ogImage,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

    <?php endif; ?>

    <?php if (!empty($noIndex)): ?>

        <meta
            name="robots"
            content="noindex, nofollow">

    <?php endif; ?>

    <?php
    $pageCssFiles = is_array($cssFiles ?? null)
        ? $cssFiles
        : ['base'];

    $blockCssFiles = is_array($blockCssFiles ?? null)
        ? $blockCssFiles
        : [];

    $allCssFiles = array_values(
        array_unique(
            array_merge(
                $pageCssFiles,
                $blockCssFiles
            )
        )
    );

    if ($cryptoTipsEnabled) {
        array_unshift(
            $allCssFiles,
            'doge-tip'
        );

        $allCssFiles = array_values(
            array_unique($allCssFiles)
        );
    }

    ?>

    <?php foreach ($allCssFiles as $css): ?>

        <?php
        $css = trim((string) $css);

        if ($css === '') {
            continue;
        }
        ?>

        <link
            rel="stylesheet"
            href="/themes/default/assets/css/<?= htmlspecialchars(
                $css,
                ENT_QUOTES,
                'UTF-8'
            ) ?>.css">

    <?php endforeach; ?>

    <link
    rel="stylesheet"
    href="/assets/vendor/fontawesome/css/all.min.css">

</head>

<body>

<a class="mv-skip-link" href="#main-content">
    <?= htmlspecialchars(
        $t('common.skip_to_content'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</a>

<header class="mv-site-header">

    <div class="mv-site-header-inner">

        <a class="mv-site-brand" href="/" aria-label="<?= htmlspecialchars(
            $siteName,
            ENT_QUOTES,
            'UTF-8'
        ) ?>">

            <?php if ($siteLogo !== ''): ?>

                <span class="mv-site-brand-mark">

                    <img
                        src="/storage/brand/<?= htmlspecialchars(
                            $siteLogo,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        alt="<?= htmlspecialchars(
                            $siteName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">

                </span>

            <?php else: ?>

                <span
                    class="mv-site-brand-mark"
                    aria-hidden="true">

                    M

                </span>

            <?php endif; ?>

            <span class="mv-site-brand-copy">

                <strong>
                    <?= htmlspecialchars(
                        $siteName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <small>
                    <?= htmlspecialchars(
                        $siteTagline,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </small>

            </span>

        </a>

        <button
            type="button"
            class="mv-nav-toggle"
            id="mv-nav-toggle"
            aria-controls="mv-primary-navigation"
            aria-expanded="false"
            aria-label="<?= htmlspecialchars(
                $t('navigation.open_menu'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

            <span></span>
            <span></span>
            <span></span>

        </button>

        <nav
            class="mv-site-nav"
            id="mv-primary-navigation"
            aria-label="<?= htmlspecialchars(
                $t('navigation.primary_navigation'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

            <div class="mv-site-nav-main">

                <a
                    href="/"
                    class="<?= $isHome ? 'is-active' : '' ?>"
                    <?= $isHome ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars(
                        $t('navigation.home'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

                <a
                    href="/members"
                    class="<?= str_starts_with($currentPath, '/members')
                        ? 'is-active'
                        : '' ?>"
                    <?= str_starts_with($currentPath, '/members')
                        ? 'aria-current="page"'
                        : '' ?>>
                    <?= htmlspecialchars(
                        $t('navigation.members'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

                <a
                    href="/ping"
                    class="<?= str_starts_with($currentPath, '/ping')
                        ? 'is-active'
                        : '' ?>"
                    <?= str_starts_with($currentPath, '/ping')
                        ? 'aria-current="page"'
                        : '' ?>>
                    Ping
                </a>

                <a
                    href="/chanzine"
                    class="<?= str_starts_with($currentPath, '/chanzine')
                        ? 'is-active'
                        : '' ?>"
                    <?= str_starts_with($currentPath, '/chanzine')
                        ? 'aria-current="page"'
                        : '' ?>>
                    Chanzine
                </a>

                <?php if (
                    $pagesNavigationMain
                    && $navigationPages !== []
                ): ?>

                    <?php if (count($navigationPages) === 1): ?>

                        <?php
                        $navigationPage = $navigationPages[0];

                        $navigationSlug = trim(
                            (string) (
                                $navigationPage['slug']
                                ?? ''
                            )
                        );

                        $navigationLabel = trim(
                            (string) (
                                $navigationPage['menu_label']
                                ?? ''
                            )
                        );

                        if ($navigationLabel === '') {
                            $navigationLabel = trim(
                                (string) (
                                    $navigationPage['title']
                                    ?? ''
                                )
                            );
                        }

                        $navigationPath = '/'
                            . ltrim(
                                $navigationSlug,
                                '/'
                            );

                        $isNavigationPageActive = (
                            $currentPath === $navigationPath
                        );
                        ?>

                        <?php if (
                            $navigationSlug !== ''
                            && $navigationLabel !== ''
                        ): ?>

                            <a
                                href="<?= htmlspecialchars(
                                    $navigationPath,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                class="<?= $isNavigationPageActive
                                    ? 'is-active'
                                    : '' ?>"
                                <?= $isNavigationPageActive
                                    ? 'aria-current="page"'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $navigationLabel,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </a>

                        <?php endif; ?>

                    <?php else: ?>

                        <?php
                        $hasActiveNavigationPage = false;

                        foreach ($navigationPages as $navigationPage) {
                            $navigationSlug = trim(
                                (string) (
                                    $navigationPage['slug']
                                    ?? ''
                                )
                            );

                            if (
                                $navigationSlug !== ''
                                && $currentPath === '/'
                                    . ltrim(
                                        $navigationSlug,
                                        '/'
                                    )
                            ) {
                                $hasActiveNavigationPage = true;
                                break;
                            }
                        }
                        ?>

                        <div
                            class="mv-site-nav-dropdown<?= $hasActiveNavigationPage
                                ? ' is-active'
                                : '' ?>"
                        >

                            <button
                                type="button"
                                class="mv-site-nav-dropdown-toggle"
                                aria-expanded="false"
                            >
                                <?= htmlspecialchars(
                                    $t('navigation.pages'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                                <i
                                    class="fa-solid fa-chevron-down"
                                    aria-hidden="true"
                                ></i>
                            </button>

                            <div class="mv-site-nav-dropdown-menu">

                                <?php foreach (
                                    $navigationPages
                                    as $navigationPage
                                ): ?>

                                    <?php
                                    $navigationSlug = trim(
                                        (string) (
                                            $navigationPage['slug']
                                            ?? ''
                                        )
                                    );

                                    $navigationLabel = trim(
                                        (string) (
                                            $navigationPage['menu_label']
                                            ?? ''
                                        )
                                    );

                                    if ($navigationLabel === '') {
                                        $navigationLabel = trim(
                                            (string) (
                                                $navigationPage['title']
                                                ?? ''
                                            )
                                        );
                                    }

                                    if (
                                        $navigationSlug === ''
                                        || $navigationLabel === ''
                                    ) {
                                        continue;
                                    }

                                    $navigationPath = '/'
                                        . ltrim(
                                            $navigationSlug,
                                            '/'
                                        );

                                    $isNavigationPageActive = (
                                        $currentPath
                                        === $navigationPath
                                    );
                                    ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $navigationPath,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        class="<?= $isNavigationPageActive
                                            ? 'is-active'
                                            : '' ?>"
                                        <?= $isNavigationPageActive
                                            ? 'aria-current="page"'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $navigationLabel,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </a>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                <?php endif; ?>

                <a href="/#entra">
                    <?= htmlspecialchars(
                        $t('navigation.join_chat'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

            </div>

            <div class="mv-site-nav-account">

                <?php if ($isLogged): ?>

                    <?= $component('notification-badge', [
                        'count' => $notificationCount,
                        'active' => str_starts_with($currentPath, '/notifications'),
                    ]) ?>

                    <?php if ($myDogeConfigured): ?>

                        <div
                            class="mv-mydoge-nav"
                            id="mv-mydoge-nav"
                            data-locale="<?= htmlspecialchars(
                                $currentLocale,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-unavailable="<?= htmlspecialchars(
                                $t('crypto.mydoge.unavailable'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-connected="<?= htmlspecialchars(
                                $t('crypto.mydoge.connected'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-not-connected="<?= htmlspecialchars(
                                $t('crypto.mydoge.not_connected'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-disconnect="<?= htmlspecialchars(
                                $t('crypto.mydoge.disconnect'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-connect="<?= htmlspecialchars(
                                $t('crypto.mydoge.connect'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-copied="<?= htmlspecialchars(
                                $t('crypto.mydoge.copied'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-copy="<?= htmlspecialchars(
                                $t('crypto.mydoge.copy'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-wallet-connected="<?= htmlspecialchars(
                                $t('crypto.mydoge.wallet_connected'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-wallet-not-connected="<?= htmlspecialchars(
                                $t('crypto.mydoge.wallet_not_connected'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-browser-unavailable="<?= htmlspecialchars(
                                $t('crypto.js.errors.mydoge_unavailable'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-connection-not-authorized="<?= htmlspecialchars(
                                $t('crypto.js.errors.connection_not_authorized'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-address-unavailable="<?= htmlspecialchars(
                                $t('crypto.js.errors.address_unavailable'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-invalid-amount="<?= htmlspecialchars(
                                $t('crypto.js.errors.invalid_amount'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-i18n-missing-txid="<?= htmlspecialchars(
                                $t('crypto.js.errors.missing_txid'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                            <button
                                type="button"
                                class="mv-site-nav-link mv-mydoge-nav-toggle"
                                id="mv-mydoge-nav-toggle"
                                aria-expanded="false"
                            >

                                <span>MyDoge</span>

                                <span
                                    class="mv-mydoge-status-dot is-offline"
                                    id="mv-mydoge-status-dot"
                                    aria-label="<?= htmlspecialchars(
                                        $t('crypto.mydoge.wallet_not_connected'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                ></span>

                            </button>

                            <div
                                class="mv-mydoge-dropdown"
                                id="mv-mydoge-dropdown"
                                hidden
                            >
                                <div class="mv-mydoge-dropdown-status">
                                    <span
                                        class="mv-mydoge-dropdown-dot is-offline"
                                        id="mv-mydoge-dropdown-dot"
                                    ></span>

                                    <strong id="mv-mydoge-connection-label">
                                        <?= htmlspecialchars(
                                            $t('crypto.mydoge.not_connected'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                </div>

                                <div
                                    class="mv-mydoge-dropdown-wallet"
                                    id="mv-mydoge-dropdown-wallet"
                                    hidden
                                >
                                    <div class="mv-mydoge-dropdown-label">
                                        <?= htmlspecialchars(
                                            $t('crypto.mydoge.balance'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </div>

                                    <div class="mv-mydoge-dropdown-balance">
                                        <strong id="mv-mydoge-balance">—</strong>
                                        <span>DOGE</span>
                                    </div>

                                    <div
                                        class="mv-mydoge-dropdown-address"
                                        id="mv-mydoge-address"
                                    >
                                        <?= htmlspecialchars(
                                            (string) $currentProfile['doge_tip_address'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </div>
                                </div>

                                <div
                                    class="mv-mydoge-fallback"
                                    id="mv-mydoge-fallback"
                                    hidden
                                >
                                    <div class="mv-mydoge-dropdown-label">
                                        <?= htmlspecialchars(
                                            $t('crypto.mydoge.your_doge_address'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </div>

                                    <div class="mv-mydoge-fallback-address">
                                        <code id="mv-mydoge-fallback-address"><?= htmlspecialchars(
                                            (string) $currentProfile['doge_tip_address'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?></code>

                                        <button
                                            type="button"
                                            class="mv-mydoge-copy-address"
                                            id="mv-mydoge-copy-address"
                                        >
                                            <?= htmlspecialchars(
                                                $t('crypto.mydoge.copy'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </button>
                                    </div>

                                    <div class="mv-mydoge-fallback-help">
                                        <?= htmlspecialchars(
                                            $t('crypto.mydoge.fallback_help'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="mv-mydoge-dropdown-action"
                                    id="mv-mydoge-connect-action"
                                >
                                    <?= htmlspecialchars(
                                        $t('crypto.mydoge.connect'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </button>
                            </div>

                        </div>

                    <?php endif; ?>

                    <div class="mv-site-account-dropdown">

                        <button
                            type="button"
                            class="mv-account-nav-toggle"
                            aria-expanded="false"
                        >

                            <?php if (!empty($user['avatar_url'])): ?>

                                <img
                                    class="mv-nav-avatar"
                                    src="<?= htmlspecialchars(
                                        (string) $user['avatar_url'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $t('navigation.avatar'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                            <?php endif; ?>

                            <span>
                                <?= htmlspecialchars(
                                    (string) (
                                        $user['nickname']
                                        ?? $user['preferred_username']
                                        ?? $t('navigation.account')
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                            <i
                                class="fa-solid fa-chevron-down"
                                aria-hidden="true"
                            ></i>

                        </button>

                        <div
                            class="mv-account-nav-menu"
                            hidden
                        >

                            <a
                                href="/account"
                                class="<?= $isAccount
                                    ? 'is-active'
                                    : '' ?>"
                            >
                                <?= htmlspecialchars(
                                    $t('navigation.account'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </a>

                            <?php
                            /*
                            if (
                                $isMultilingual
                                && count($availableLocales) > 1
                            ):
                            ?>

                                <div class="mv-account-nav-languages">

                                    <div class="mv-account-nav-label">
                                        <?= htmlspecialchars(
                                            $t('common.language_label'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </div>

                                    <?php foreach ($availableLocales as $locale): ?>

                                        <?php
                                        $locale = trim((string) $locale);

                                        $localeLabelKey = match ($locale) {
                                            'it' => 'common.language.italian',
                                            'en' => 'common.language.english',
                                            default => '',
                                        };

                                        if ($localeLabelKey === '') {
                                            continue;
                                        }
                                        ?>

                                        <form method="post" action="/locale">
                                            <input
                                                type="hidden"
                                                name="locale"
                                                value="<?= htmlspecialchars(
                                                    $locale,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="mv-account-nav-language<?= $locale === $currentLocale
                                                    ? ' is-active'
                                                    : '' ?>"
                                            >
                                                <?= htmlspecialchars(
                                                    $t($localeLabelKey),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </button>
                                        </form>

                                    <?php endforeach; ?>

                                </div>

                            <?php endif;
                            */
                            ?>

                            <a
                                href="/account/logout"
                                class="mv-account-nav-logout"
                            >
                                <?= htmlspecialchars(
                                    $t('navigation.logout'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </a>

                        </div>

                    </div>

                <?php else: ?>

                    <?php
                    /*
                    if (
                        $isMultilingual
                        && count($availableLocales) > 1
                    ):
                    ?>

                        <div class="mv-locale-nav">

                            <button
                                type="button"
                                class="mv-locale-nav-toggle"
                                aria-expanded="false"
                            >
                                <?php
                                $currentLocaleLabelKey = match ($currentLocale) {
                                    'it' => 'common.language.italian',
                                    'en' => 'common.language.english',
                                    default => '',
                                };
                                ?>

                                <span>
                                    <?= htmlspecialchars(
                                        $currentLocaleLabelKey !== ''
                                            ? $t($currentLocaleLabelKey)
                                            : strtoupper($currentLocale),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                                <i
                                    class="fa-solid fa-chevron-down"
                                    aria-hidden="true"
                                ></i>
                            </button>

                            <div
                                class="mv-locale-nav-menu"
                                hidden
                            >
                                <?php foreach ($availableLocales as $locale): ?>

                                    <?php
                                    $locale = trim((string) $locale);

                                    $localeLabelKey = match ($locale) {
                                        'it' => 'common.language.italian',
                                        'en' => 'common.language.english',
                                        default => '',
                                    };

                                    if ($localeLabelKey === '') {
                                        continue;
                                    }
                                    ?>

                                    <form
                                        method="post"
                                        action="/locale"
                                    >
                                        <input
                                            type="hidden"
                                            name="locale"
                                            value="<?= htmlspecialchars(
                                                $locale,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="mv-locale-nav-option<?= $locale === $currentLocale
                                                ? ' is-active'
                                                : '' ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $t($localeLabelKey),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </button>
                                    </form>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif;
                    */
                    ?>

                    <a
                        href="/register"
                        class="mv-nav-link"
                    >
                        <?= htmlspecialchars(
                            $t('navigation.register'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>

                    <a
                        href="/oauth/login"
                        class="mv-nav-login"
                    >
                        <?= htmlspecialchars(
                            $t('navigation.login'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>

                    <?php endif; ?>

            </div>

        </nav>

    </div>

</header>

<main id="main-content">
    <?= $body ?>
</main>

<?php
if ($cryptoTipsEnabled && $isLogged) {
    echo $component('doge-tip-modal', [
        'username' => '',
        'dogeTipAddress' => '',
    ]);
}
?>
<?php
$navOpenLabel = $t('navigation.open_menu');
$navCloseLabel = $t('navigation.close_menu');
?>
<script>
(function () {
    'use strict';

    const toggle = document.getElementById('mv-nav-toggle');
    const navigation = document.getElementById('mv-primary-navigation');

    if (!toggle || !navigation) {
        return;
    }

    function closeMenu() {
        navigation.classList.remove('is-open');
        toggle.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute(
            'aria-label',
            <?= json_encode(
                $navOpenLabel,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            ) ?>
        );
    }

    function openMenu() {
        navigation.classList.add('is-open');
        toggle.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute(
            'aria-label',
            <?= json_encode(
                $navCloseLabel,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            ) ?>
        );
    }

    toggle.addEventListener('click', function () {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';

        if (isOpen) {
            closeMenu();
            return;
        }

        openMenu();
    });

    navigation.addEventListener('click', function (event) {
        if (event.target.closest('a')) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    document.addEventListener('click', function (event) {
        if (
            navigation.classList.contains('is-open')
            && !navigation.contains(event.target)
            && !toggle.contains(event.target)
        ) {
            closeMenu();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 800) {
            closeMenu();
        }
    });
})();
</script>

<?php
$pageJsFiles = is_array($jsFiles ?? null)
    ? $jsFiles
    : [];

$blockJsFiles = is_array($blockJsFiles ?? null)
    ? $blockJsFiles
    : [];

$allJsFiles = array_values(
    array_unique(
        array_merge(
            $pageJsFiles,
            $blockJsFiles
        )
    )
);
?>
<?php
if ($cryptoTipsEnabled) {
    array_unshift(
        $allJsFiles,
        'mydogemask',
        'doge-tip'
    );

    $allJsFiles = array_values(
        array_unique($allJsFiles)
    );
}
?>
<?php if ($cryptoTipsEnabled): ?>

    <script src="/assets/vendor/qrcodejs/qrcode.min.js"></script>

    <script>
    window.MonoverseDogeTipI18n = <?= json_encode(
        [
            'cancel' => $t('crypto.js.cancel'),
            'close' => $t('crypto.js.close'),
            'copy' => $t('crypto.js.copy'),
            'copied' => $t('crypto.js.copied'),

            'errors' => [
                'notificationFailed' => $t(
                    'crypto.js.errors.notification_failed'
                ),
                'pongInvalid' => $t(
                    'crypto.js.errors.pong_invalid'
                ),
                'pongFailed' => $t(
                    'crypto.js.errors.pong_failed'
                ),
                'pingFailed' => $t(
                    'crypto.js.errors.ping_failed'
                ),
                'addressUnavailable' => $t(
                    'crypto.js.errors.address_unavailable'
                ),
                'invalidAmount' => $t(
                    'crypto.js.errors.invalid_amount'
                ),
                'myDogeUnavailable' => $t(
                    'crypto.js.errors.mydoge_unavailable'
                ),
                'missingTxId' => $t(
                    'crypto.js.errors.missing_txid'
                ),
                'sendFailed' => $t(
                    'crypto.js.errors.send_failed'
                ),
                'unknown' => $t(
                    'crypto.js.errors.unknown'
                ),
            ],

            'status' => [
                'confirmTransaction' => $t(
                    'crypto.js.status.confirm_transaction'
                ),
                'sent' => $t(
                    'crypto.js.status.sent'
                ),
                'viewTransaction' => $t(
                    'crypto.js.status.view_transaction'
                ),
                'pingShared' => $t(
                    'crypto.js.status.ping_shared'
                ),
                'pingFailed' => $t(
                    'crypto.js.status.ping_failed'
                ),
                'pongShared' => $t(
                    'crypto.js.status.pong_shared'
                ),
                'pongFailed' => $t(
                    'crypto.js.status.pong_failed'
                ),
            ],
        ],
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?>;
    </script>

<?php endif; ?>

<?php if (in_array('ping', $allJsFiles, true)): ?>

    <script>
    window.MonoversePingI18n = <?= json_encode(
        [
            'upload' => [
                'processing' => $t('ping.js.upload.processing'),
                'uploading' => $t('ping.js.upload.uploading'),
                'failed' => $t('ping.js.upload.failed'),
            ],

            'attachments' => [
                'label' => $t('ping.js.attachments.label'),
                'one' => $t('ping.js.attachments.one'),
                'count' => $t('ping.js.attachments.count'),
                'remove' => $t('ping.js.attachments.remove'),

                'audioNotAllowed' => $t(
                    'ping.js.attachments.audio_not_allowed'
                ),
                'videoNotAllowed' => $t(
                    'ping.js.attachments.video_not_allowed'
                ),

                'audioTooLarge' => $t(
                    'ping.js.attachments.audio_too_large'
                ),
                'videoTooLarge' => $t(
                    'ping.js.attachments.video_too_large'
                ),
            ],

            'lightbox' => [
                'previousImage' => $t(
                    'ping.js.lightbox.previous_image'
                ),
                'nextImage' => $t(
                    'ping.js.lightbox.next_image'
                ),
            ],

            'audio' => [
                'pause' => $t('ping.js.audio.pause'),
                'play' => $t('ping.js.audio.play'),
                'unmute' => $t('ping.js.audio.unmute'),
                'mute' => $t('ping.js.audio.mute'),
            ],
        ],
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?>;
    </script>

<?php endif; ?>

<?php foreach ($allJsFiles as $js): ?>

    <?php
    $js = trim((string) $js);

    if ($js === '') {
        continue;
    }
    ?>

    <script
        src="/themes/default/assets/js/<?= htmlspecialchars(
            $js,
            ENT_QUOTES,
            'UTF-8'
        ) ?>.js">
    </script>

<?php endforeach; ?>

</body>
</html>