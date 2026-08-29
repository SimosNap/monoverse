<?php require __DIR__ . '/includes/installer-steps.php'; ?>

<section>

    <h2>
        <?= htmlspecialchars(
            $t('installer.admin.heading'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h2>

    <p>
        <?= htmlspecialchars(
            $t('installer.admin.description'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <?php if (!empty($errors)): ?>

        <div class="mv-alert mv-alert-danger">

            <?php foreach ($errors as $error): ?>

                <p>
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <form method="post" action="/install/admin">

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.admin.username'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="text"
                    name="admin_username"
                    value="<?= htmlspecialchars(
                        (string) ($old['admin_username'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>
            </label>
        </p>

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.admin.password'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="password"
                    name="admin_password"
                    required>
            </label>
        </p>

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.admin.password_confirm'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="password"
                    name="admin_password_confirm"
                    required>
            </label>
        </p>

        <p>
            <button type="submit">
                <?= htmlspecialchars(
                    $t('installer.common.continue'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </button>
        </p>

    </form>

    <p>
        <a href="/install/oauth">
            <?= htmlspecialchars(
                $t('installer.admin.back'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>
    </p>

</section>
