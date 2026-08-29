<section>

    <h2>
        <?= htmlspecialchars(
            $t('installer.database.heading'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h2>

    <p>
        <?= htmlspecialchars(
            $t('installer.database.selected_edition'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
        <strong><?= htmlspecialchars(
            (string) ($selectedEdition ?? ''),
            ENT_QUOTES,
            'UTF-8'
        ) ?></strong>
    </p>

    <?php if (!empty($errors)): ?>

        <div class="alert alert-danger">

            <?php foreach ($errors as $error): ?>

                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <form method="post" action="/install/database">

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.database.host'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="text"
                    name="db_host"
                    value="<?= htmlspecialchars(
                        (string) ($old['host'] ?? 'localhost'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>
            </label>
        </p>

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.database.name'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="text"
                    name="db_name"
                    value="<?= htmlspecialchars(
                        (string) ($old['name'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>
            </label>
        </p>

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.database.user'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="text"
                    name="db_user"
                    value="<?= htmlspecialchars(
                        (string) ($old['user'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>
            </label>
        </p>

        <p>
            <label>
                <?= htmlspecialchars(
                    $t('installer.database.password'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?><br>

                <input
                    type="password"
                    name="db_pass"
                    value="<?= htmlspecialchars(
                        (string) ($old['pass'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">
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
        <a href="/install/edition">
            <?= htmlspecialchars(
                $t('installer.database.back'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>
    </p>

</section>
