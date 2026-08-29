<?php
declare(strict_types=1);

$profile = is_array($profile ?? null) ? $profile : [];
$user = is_array($user ?? null) ? $user : [];

$displayUsername = trim((string) (
    $profile['username']
    ?? $user['username']
    ?? $user['preferred_username']
    ?? $t('account.profile.user')
));

$avatarUrl = trim((string) (
    $profile['avatar_url']
    ?? $user['avatar_url']
    ?? ''
));

$selectedInterests = [];

if (!empty($profile['interests'])) {
    $decodedInterests = json_decode(
        (string) $profile['interests'],
        true
    );

    if (is_array($decodedInterests)) {
        $selectedInterests = array_values(
            array_filter(
                $decodedInterests,
                static fn ($interest): bool =>
                    is_string($interest)
                    && trim($interest) !== ''
            )
        );
    }
}

$accountAliases = [];

if (!empty($profile['aliases'])) {
    $decodedAliases = json_decode(
        (string) $profile['aliases'],
        true
    );

    if (is_array($decodedAliases)) {
        $accountAliases = array_values(
            array_unique(
                array_filter(
                    $decodedAliases,
                    static fn ($alias): bool =>
                        is_string($alias)
                        && trim($alias) !== ''
                )
            )
        );
    }
}

$showAliases = !empty($profile['show_aliases']);

$availableInterests = [
    'Musica' => $t('account.profile.interests.items.Music'),
    'Cinema' => $t('account.profile.interests.items.Cinema'),
    'Serie TV' => $t('account.profile.interests.items.TV Series'),
    'Gaming' => $t('account.profile.interests.items.Gaming'),
    'Anime' => $t('account.profile.interests.items.Anime'),
    'Sport' => $t('account.profile.interests.items.Sport'),
    'Libri' => $t('account.profile.interests.items.Books'),
    'Tecnologia' => $t('account.profile.interests.items.Technology'),
    'IRC' => $t('account.profile.interests.items.IRC'),
    'Linux' => $t('account.profile.interests.items.Linux'),
    'Mac' => $t('account.profile.interests.items.Mac'),
    'Windows' => $t('account.profile.interests.items.Windows'),
    'Radio' => $t('account.profile.interests.items.Radio'),
    'Podcast' => $t('account.profile.interests.items.Podcast'),
    'Viaggi' => $t('account.profile.interests.items.Travel'),
    'Fotografia' => $t('account.profile.interests.items.Photography'),
    'Cucina' => $t('account.profile.interests.items.Cooking'),
    'Auto e moto' => $t(
        'account.profile.interests.items.Cars and motorcycles'
    ),
];

$previewBioEmpty = $t(
    'account.profile.bio.preview_empty'
);
?>


<section class="mv-account mv-account-profile-page">

    <?= $component('account-navigation', [
        'user' => $user ?? [],
        'settings' => $settings ?? [],
    ]) ?>

    <?php if (isset($_GET['saved'])): ?>

        <div class="mv-alert mv-alert-success">

            <strong>
                <?= htmlspecialchars(
                    $t('account.profile.saved.title'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>

            <p>
                <?= htmlspecialchars(
                    $t('account.profile.saved.text'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

    <?php endif; ?>

    <form
        id="public-profile-form"
        class="mv-profile-editor"
        method="post"
        action="/account/profile">

        <div class="mv-profile-editor-layout">

            <div class="mv-profile-editor-main">

                <section class="mv-profile-editor-section">

                    <header class="mv-profile-editor-section-header">

                        <div>

                            <p class="mv-profile-editor-kicker">
                                <?= htmlspecialchars(
                                    $t('account.profile.about.kicker'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>

                            <h2>
                                <?= htmlspecialchars(
                                    $t('account.profile.about.title'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </h2>

                        </div>

                        <span class="mv-profile-editor-step">
                            01
                        </span>

                    </header>

                    <div class="mv-profile-editor-card">

                        <div class="mv-profile-form-section">

                            <div class="mv-profile-field">

                                <label for="profile_bio">
                                    <?= htmlspecialchars(
                                        $t('account.profile.bio.label'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </label>

                                <textarea
                                    id="profile_bio"
                                    name="bio"
                                    rows="7"
                                    maxlength="1000"
                                    placeholder="<?= htmlspecialchars(
                                        $t(
                                            'account.profile.bio.placeholder'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"><?= htmlspecialchars(
                                        (string) ($profile['bio'] ?? ''),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?></textarea>

                                <div class="mv-field-meta">

                                    <span>
                                        <?= htmlspecialchars(
                                            $t('account.profile.bio.max'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <span id="profile-bio-counter">
                                        0 / 1000
                                    </span>

                                </div>

                            </div>

                            <div class="mv-profile-field">

                                <label for="profile_motto">
                                    <?= htmlspecialchars(
                                        $t(
                                            'account.profile.motto.label'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </label>

                                <input
                                    type="text"
                                    id="profile_motto"
                                    name="motto"
                                    maxlength="120"
                                    value="<?= htmlspecialchars(
                                        (string) ($profile['motto'] ?? ''),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    placeholder="<?= htmlspecialchars(
                                        $t(
                                            'account.profile.motto.placeholder'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                                <div class="mv-field-meta">

                                    <span>
                                        <?= htmlspecialchars(
                                            $t(
                                                'account.profile.motto.optional'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <span>
                                        <?= htmlspecialchars(
                                            $t(
                                                'account.profile.motto.max'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </section>

                <section class="mv-profile-editor-section">

                    <header class="mv-profile-editor-section-header">

                        <div>

                            <p class="mv-profile-editor-kicker">
                                <?= htmlspecialchars(
                                    $t(
                                        'account.profile.interests.kicker'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>

                            <h2>
                                <?= htmlspecialchars(
                                    $t(
                                        'account.profile.interests.title'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </h2>

                        </div>

                        <span class="mv-profile-editor-step">
                            02
                        </span>

                    </header>

                    <div class="mv-profile-editor-card">

                        <div class="mv-interest-grid">

                            <?php foreach (
                                $availableInterests
                                as $interestValue => $interestLabel
                            ): ?>

                                <?php
                                $interestId = 'interest-' . strtolower(
                                    (string) preg_replace(
                                        '/[^a-z0-9]+/i',
                                        '-',
                                        $interestValue
                                    )
                                );

                                $isSelected = in_array(
                                    $interestValue,
                                    $selectedInterests,
                                    true
                                );
                                ?>

                                <label
                                    class="mv-interest-tile <?= $isSelected
                                        ? 'is-selected'
                                        : '' ?>"
                                    for="<?= htmlspecialchars(
                                        $interestId,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                                    <input
                                        type="checkbox"
                                        id="<?= htmlspecialchars(
                                            $interestId,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        name="interests[]"
                                        value="<?= htmlspecialchars(
                                            $interestValue,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        data-interest-label="<?= htmlspecialchars(
                                            $interestLabel,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        <?= $isSelected
                                            ? 'checked'
                                            : '' ?>>

                                    <span
                                        class="mv-interest-tile-mark"
                                        aria-hidden="true">
                                        <?= $isSelected ? '✓' : '+' ?>
                                    </span>

                                    <span>
                                        <?= htmlspecialchars(
                                            $interestLabel,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </section>

                <section class="mv-profile-editor-section">

                    <header class="mv-profile-editor-section-header">

                        <div>

                            <p class="mv-profile-editor-kicker">
                                <?= htmlspecialchars(
                                    $t('account.profile.links.kicker'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>

                            <h2>
                                <?= htmlspecialchars(
                                    $t('account.profile.links.title'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </h2>

                        </div>

                        <span class="mv-profile-editor-step">
                            03
                        </span>

                    </header>

                    <div class="mv-profile-editor-card">

                        <div class="mv-profile-links-grid">

                            <div class="mv-profile-field">

                                <label for="profile_website">
                                    <?= htmlspecialchars(
                                        $t(
                                            'account.profile.links.website'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </label>

                                <input
                                    type="url"
                                    id="profile_website"
                                    name="website"
                                    value="<?= htmlspecialchars(
                                        (string) (
                                            $profile['website'] ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    placeholder="https://">

                            </div>

                            <div class="mv-profile-field">

                                <label for="profile_telegram">
                                    <?= htmlspecialchars(
                                        $t(
                                            'account.profile.links.telegram'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </label>

                                <input
                                    type="text"
                                    id="profile_telegram"
                                    name="telegram"
                                    value="<?= htmlspecialchars(
                                        (string) (
                                            $profile['telegram'] ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    placeholder="@username">

                            </div>

                        </div>

                    </div>

                </section>

                <div class="mv-profile-save-bar">

                    <div>

                        <strong>
                            <?= htmlspecialchars(
                                $t('account.profile.save.title'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $t('account.profile.save.help'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                    <button type="submit">
                        <?= htmlspecialchars(
                            $t('account.profile.save.button'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </button>

                </div>

            </div>

            <aside class="mv-profile-preview">

                <div class="mv-profile-preview-card">

                    <p class="mv-profile-preview-label">
                        <?= htmlspecialchars(
                            $t('account.profile.preview.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <div class="mv-profile-preview-avatar">

                        <?php if ($avatarUrl !== ''): ?>

                            <img
                                src="<?= htmlspecialchars(
                                    $avatarUrl,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="">

                        <?php else: ?>

                            <span>
                                <?= htmlspecialchars(
                                    mb_strtoupper(
                                        mb_substr(
                                            $displayUsername,
                                            0,
                                            1
                                        )
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                    <h2 id="profile-preview-username">
                        <?= htmlspecialchars(
                            $displayUsername,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p
                        class="mv-profile-preview-motto"
                        id="profile-preview-motto">

                        <?= htmlspecialchars(
                            (string) ($profile['motto'] ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>

                    <p
                        class="mv-profile-preview-bio"
                        id="profile-preview-bio">

                        <?= htmlspecialchars(
                            trim(
                                (string) ($profile['bio'] ?? '')
                            ) ?: $previewBioEmpty,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </p>

                    <div
                        class="mv-profile-preview-interests"
                        id="profile-preview-interests">

                        <?php foreach (
                            $selectedInterests
                            as $interest
                        ): ?>

                            <?php
                            $interestLabel =
                                $availableInterests[$interest]
                                ?? (string) $interest;
                            ?>

                            <span>
                                <?= htmlspecialchars(
                                    $interestLabel,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                        <?php endforeach; ?>

                    </div>

                    <?php if (
                        $showAliases
                        && $accountAliases !== []
                    ): ?>

                        <div class="mv-profile-preview-aliases">

                            <strong>
                                <?= htmlspecialchars(
                                    $t(
                                        'account.profile.preview.aliases'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                            <div>

                                <?php foreach (
                                    $accountAliases
                                    as $alias
                                ): ?>

                                    <span>
                                        <?= htmlspecialchars(
                                            (string) $alias,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                    <div class="mv-profile-preview-links">

                        <a
                            id="profile-preview-website"
                            href="<?= htmlspecialchars(
                                (string) (
                                    $profile['website'] ?? '#'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            <?= empty($profile['website'])
                                ? 'hidden'
                                : '' ?>>
                            <?= htmlspecialchars(
                                $t(
                                    'account.profile.links.website'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </a>

                        <?php
                        $telegramUsername = ltrim(
                            trim(
                                (string) (
                                    $profile['telegram'] ?? ''
                                )
                            ),
                            '@'
                        );
                        ?>

                        <a
                            id="profile-preview-telegram"
                            href="<?= $telegramUsername !== ''
                                ? 'https://t.me/'
                                    . rawurlencode(
                                        $telegramUsername
                                    )
                                : '#' ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            <?= $telegramUsername === ''
                                ? 'hidden'
                                : '' ?>>
                            <?= htmlspecialchars(
                                $t(
                                    'account.profile.links.telegram'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </a>

                    </div>

                </div>

            </aside>

        </div>

    </form>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const previewBioEmpty = <?= json_encode(
        $previewBioEmpty,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?>;

    const bio = document.getElementById('profile_bio');
    const motto = document.getElementById('profile_motto');
    const website = document.getElementById('profile_website');
    const telegram = document.getElementById('profile_telegram');

    const bioCounter = document.getElementById(
        'profile-bio-counter'
    );

    const previewBio = document.getElementById(
        'profile-preview-bio'
    );

    const previewMotto = document.getElementById(
        'profile-preview-motto'
    );

    const previewWebsite = document.getElementById(
        'profile-preview-website'
    );

    const previewTelegram = document.getElementById(
        'profile-preview-telegram'
    );

    const previewInterests = document.getElementById(
        'profile-preview-interests'
    );

    const interestInputs = document.querySelectorAll(
        '.mv-interest-tile input'
    );

    function updateBio() {
        if (!bio) {
            return;
        }

        if (bioCounter) {
            bioCounter.textContent =
                bio.value.length + ' / 1000';
        }

        if (previewBio) {
            previewBio.textContent =
                bio.value.trim() || previewBioEmpty;
        }
    }

    function updateMotto() {
        if (!previewMotto || !motto) {
            return;
        }

        previewMotto.textContent =
            motto.value.trim();
    }

    function updateWebsite() {
        if (!previewWebsite || !website) {
            return;
        }

        const value = website.value.trim();

        previewWebsite.hidden =
            value === '';

        previewWebsite.href =
            value || '#';
    }

    function updateTelegram() {
        if (!previewTelegram || !telegram) {
            return;
        }

        const value = telegram.value
            .trim()
            .replace(/^@/, '');

        previewTelegram.hidden =
            value === '';

        previewTelegram.href = value !== ''
            ? 'https://t.me/'
                + encodeURIComponent(value)
            : '#';
    }

    function updateInterests() {
        if (!previewInterests) {
            return;
        }

        previewInterests.innerHTML = '';

        interestInputs.forEach(function (input) {
            const tile = input.closest(
                '.mv-interest-tile'
            );

            if (tile) {
                tile.classList.toggle(
                    'is-selected',
                    input.checked
                );

                const mark = tile.querySelector(
                    '.mv-interest-tile-mark'
                );

                if (mark) {
                    mark.textContent =
                        input.checked
                            ? '✓'
                            : '+';
                }
            }

            if (!input.checked) {
                return;
            }

            const tag =
                document.createElement('span');

            tag.textContent =
                input.dataset.interestLabel
                || input.value;

            previewInterests.appendChild(tag);
        });
    }

    if (bio) {
        bio.addEventListener(
            'input',
            updateBio
        );
    }

    if (motto) {
        motto.addEventListener(
            'input',
            updateMotto
        );
    }

    if (website) {
        website.addEventListener(
            'input',
            updateWebsite
        );
    }

    if (telegram) {
        telegram.addEventListener(
            'input',
            updateTelegram
        );
    }

    interestInputs.forEach(function (input) {
        input.addEventListener(
            'change',
            updateInterests
        );
    });

    updateBio();
    updateMotto();
    updateWebsite();
    updateTelegram();
    updateInterests();
});
</script>
