<section>

    <h2>
        <?= htmlspecialchars(
            $t('installer.edition.heading'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h2>

    <p>
        <?= htmlspecialchars(
            $t('installer.edition.description'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <?php foreach ($editions as $edition): ?>

        <article class="edition-card">

            <h3>
                <?= htmlspecialchars(
                    $t(
                        'installer.edition.'
                        . $edition->id
                        . '.name'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

                <?php if (!$edition->isAvailable()): ?>
                    <small>
                        — <?= htmlspecialchars(
                            $t('installer.common.coming_soon'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>
                <?php endif; ?>
            </h3>

            <p>
                <?= htmlspecialchars(
                    $t(
                        'installer.edition.'
                        . $edition->id
                        . '.description'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <small>
                <?= htmlspecialchars(
                    $t('installer.common.version'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
                <?= htmlspecialchars(
                    $edition->version,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>

            <?php if ($edition->isAvailable()): ?>

                <form method="post" action="/install/edition">
                    <input
                        type="hidden"
                        name="edition"
                        value="<?= htmlspecialchars(
                            $edition->id,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <button type="submit">
                        <?= htmlspecialchars(
                            $t('installer.edition.install'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </button>
                </form>

            <?php else: ?>

                <p>
                    <small>
                        <?= htmlspecialchars(
                            $t('installer.edition.unavailable'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>
                </p>

            <?php endif; ?>

        </article>

        <hr>

    <?php endforeach; ?>

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

</section>
