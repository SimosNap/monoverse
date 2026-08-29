<section>
    <h2><?= htmlspecialchars($t('installer.requirements.heading'), ENT_QUOTES, 'UTF-8') ?></h2>

    <p>
        <?= htmlspecialchars($t('installer.requirements.welcome'), ENT_QUOTES, 'UTF-8') ?>
    </p>

    <form method="post" action="/locale">
        <label for="installer-locale">
            <?= htmlspecialchars($t('installer.language.label'), ENT_QUOTES, 'UTF-8') ?>
        </label>

        <select
            id="installer-locale"
            name="locale"
            onchange="this.form.submit()"
        >
            <option
                value="it"
                <?= $currentLocale === 'it' ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($t('installer.language.italian'), ENT_QUOTES, 'UTF-8') ?>
            </option>

            <option
                value="en"
                <?= $currentLocale === 'en' ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($t('installer.language.english'), ENT_QUOTES, 'UTF-8') ?>
            </option>
        </select>

        <noscript>
            <button type="submit">
                <?= htmlspecialchars($t('installer.language.change'), ENT_QUOTES, 'UTF-8') ?>
            </button>
        </noscript>
    </form>

    <h3>
        <?= htmlspecialchars($t('installer.requirements.system_requirements'), ENT_QUOTES, 'UTF-8') ?>
    </h3>

    <ul>
        <?php foreach (($requirements ?? []) as $requirement): ?>
            <?php
            $currentKey = match ($requirement['current']) {
                'loaded' => 'installer.requirements.loaded',
                'missing' => 'installer.requirements.missing',
                'supported' => 'installer.requirements.supported',
                'writable' => 'installer.requirements.writable',
                'not writable' => 'installer.requirements.not_writable',
                default => null,
            };

            $currentValue = $currentKey !== null
                ? $t($currentKey)
                : (string) $requirement['current'];
            ?>
            <li>
                <?= $requirement['ok'] ? '✅' : '❌' ?>
                <strong><?= htmlspecialchars($requirement['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                —
                <?= htmlspecialchars($currentValue, ENT_QUOTES, 'UTF-8') ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <p>
        <a href="/install/edition">
            <?= htmlspecialchars($t('installer.common.continue'), ENT_QUOTES, 'UTF-8') ?>
        </a>
    </p>
</section>
