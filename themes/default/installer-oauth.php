<?php require __DIR__ . '/includes/installer-steps.php'; ?>

<section>

    <h2>
        <?= htmlspecialchars(
            $t('installer.oauth.heading'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h2>

    <p>
        <?= htmlspecialchars(
            $t('installer.oauth.description'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <?php if (!empty($errors)): ?>

        <div class="alert alert-danger">

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

    <form method="post" action="/install/oauth">

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.oauth.client_id'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="text"
                    name="oauth_client_id"
                    value="<?= htmlspecialchars(
                        (string) ($old['oauth_client_id'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>
            </label>
        </p>

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.oauth.client_secret'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="password"
                    name="oauth_client_secret"
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
        <a href="/install/database">
            <?= htmlspecialchars(
                $t('installer.oauth.back'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>
    </p>

</section>
