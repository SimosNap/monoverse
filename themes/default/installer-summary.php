<?php require __DIR__ . '/includes/installer-steps.php'; ?>

<section>

    <h2>
        <?= htmlspecialchars(
            $t('installer.summary.heading'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h2>

    <p>
        <?= htmlspecialchars(
            $t('installer.summary.description'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <h3>
        <?= htmlspecialchars(
            $t('installer.summary.edition'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h3>

    <p>
        <?= htmlspecialchars(
            (string) ($edition ?? ''),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <h3>
        <?= htmlspecialchars(
            $t('installer.summary.database'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h3>

    <ul>
        <li>
            <?= htmlspecialchars(
                $t('installer.summary.host'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>:
            <?= htmlspecialchars(
                (string) ($database['host'] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </li>

        <li>
            <?= htmlspecialchars(
                $t('installer.summary.name'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>:
            <?= htmlspecialchars(
                (string) ($database['name'] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </li>

        <li>
            <?= htmlspecialchars(
                $t('installer.summary.user'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>:
            <?= htmlspecialchars(
                (string) ($database['user'] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </li>
    </ul>

    <h3>
        <?= htmlspecialchars(
            $t('installer.summary.admin'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h3>

    <p>
        <?= htmlspecialchars(
            $t('installer.summary.username'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>

        <strong>
            <?= htmlspecialchars(
                (string) ($admin['username'] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>
    </p>

    <form method="post" action="/install/run">

        <button type="submit">
            <?= htmlspecialchars(
                $t('installer.summary.install'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </button>

    </form>

    <p>
        <a href="/install/admin">
            <?= htmlspecialchars(
                $t('installer.summary.back'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>
    </p>

</section>