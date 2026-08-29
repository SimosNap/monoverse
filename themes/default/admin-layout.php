<!DOCTYPE html>
<html lang="<?= htmlspecialchars(
    (string) ($currentLocale ?? 'it'),
    ENT_QUOTES,
    'UTF-8'
) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($title ?? 'Monoverse'), ENT_QUOTES, 'UTF-8') ?></title>
    <?php foreach (($cssFiles ?? ['base']) as $css): ?>

        <link
            rel="stylesheet"
            href="/themes/default/assets/css/<?= htmlspecialchars(
                (string) $css,
                ENT_QUOTES,
                'UTF-8'
            ) ?>.css">

    <?php endforeach; ?>
    <link
        rel="stylesheet"
        href="/assets/vendor/fontawesome/css/all.min.css">
</head>
<body class="mv-admin">

<header class="mv-admin-header">
    <div>
        <strong>Monoverse</strong>
    </div>

    <div>
        <?php if (!empty($admin['username'])): ?>
            <?= htmlspecialchars((string) $admin['username'], ENT_QUOTES, 'UTF-8') ?>
            ·
        <?php endif; ?>

        <a href="/admin/logout">
            <?= htmlspecialchars(
                $t('admin.layout.logout'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>
    </div>
</header>

<nav class="mv-admin-nav">
    <?php foreach (($navigation ?? []) as $item): ?>

        <a href="<?= htmlspecialchars((string) $item['url'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars((string) $item['title'], ENT_QUOTES, 'UTF-8') ?>
        </a>

    <?php endforeach; ?>
</nav>

<main class="mv-admin-main">

    <?php if (
        isset($session)
        && ($message = $session->getFlash('success'))
    ): ?>

        <div class="mv-alert mv-alert-success">
            <?= htmlspecialchars(
                (string) $message,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>

    <?php if (
        isset($session)
        && ($message = $session->getFlash('warning'))
    ): ?>

        <div class="mv-alert mv-alert-warning">
            <?= htmlspecialchars(
                (string) $message,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>

    <?php if (
        isset($session)
        && ($message = $session->getFlash('error'))
    ): ?>

        <div class="mv-alert mv-alert-danger">
            <?= nl2br(
                htmlspecialchars(
                    (string) $message,
                    ENT_QUOTES,
                    'UTF-8'
                )
            ) ?>
        </div>

    <?php endif; ?>

    <?= $body ?>

</main>

<?php foreach (($jsFiles ?? []) as $js): ?>
    <script
        src="/themes/default/assets/js/<?= htmlspecialchars(
            (string) $js,
            ENT_QUOTES,
            'UTF-8'
        ) ?>.js"
        defer></script>
<?php endforeach; ?>

</body>
</html>
