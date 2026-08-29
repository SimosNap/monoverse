<?php
declare(strict_types=1);

$profile = is_array($profile ?? null) ? $profile : [];

$following = isset($following) && is_array($following)
    ? $following
    : [];

$isDatabase = !empty($profile);
$isModerator = !empty($isModerator);

$selectedNickname = (string) (
    $profile['nickname']
    ?? $user['nickname']
    ?? ''
);

$selectedSex = (string) ($profile['sex'] ?? 'U');
?>

<section class="mv-account">

    <?= $component('account-navigation', [
        'user' => $user ?? [],
        'settings' => $settings ?? [],
    ]) ?>

    <div class="page-header">

        <h1>
            <?= htmlspecialchars(
                $t('account.main.header.title'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h1>

        <p class="page-subtitle">
            <?= htmlspecialchars(
                $t('account.main.header.subtitle'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    </div>

    <?php if ($isModerator): ?>

        <?php ob_start(); ?>

        <div class="mv-account-roles">

            <div class="mv-account-role">

                <span
                    class="mv-account-role-mark"
                    aria-hidden="true">
                    M
                </span>

                <div>

                    <strong>
                        <?= htmlspecialchars(
                            $t('account.main.roles.moderator_title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                    <p>
                        <?= htmlspecialchars(
                            $t('account.main.roles.moderator_text'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            </div>

        </div>

        <?php

        echo $component('panel', [
            'title' => $t('account.main.roles.panel_title'),
            'class' => 'mv-account-panel mv-account-roles-panel',
            'content' => (string) ob_get_clean(),
        ]);

        ?>

    <?php endif; ?>

    <?php ob_start(); ?>

    <?php if ($following === []): ?>

        <div class="mv-account-empty">

            <p>
                <?= htmlspecialchars(
                    $t('account.main.following.empty'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

    <?php else: ?>

        <div class="mv-following-list">

            <?php foreach ($following as $followed): ?>

                <?php
                $username = trim(
                    (string) ($followed['username'] ?? '')
                );

                $nickname = trim(
                    (string) ($followed['nickname'] ?? '')
                );

                $displayName = $nickname !== ''
                    ? $nickname
                    : $username;

                $showAvatar = !empty($followed['show_avatar']);

                $avatarUrl = trim(
                    (string) ($followed['avatar_url'] ?? '')
                );

                $isPublic = !empty($followed['public_profile']);
                ?>

                <div class="mv-following-user">

                    <a
                        href="/profile/<?= rawurlencode($username) ?>"
                        class="mv-following-link"
                    >

                        <div class="mv-following-avatar">

                            <?php if ($showAvatar && $avatarUrl !== ''): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        $avatarUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $displayName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <span aria-hidden="true">
                                    <i class="fa-solid fa-user"></i>
                                </span>

                            <?php endif; ?>

                        </div>

                        <div class="mv-following-content">

                            <div class="mv-following-name-row">

                                <?= $component('user-presence', [
                                    'presence' => $followed['presence'] ?? [],
                                ]) ?>

                                <strong>
                                    <?= htmlspecialchars(
                                        $displayName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                            </div>

                            <?php if ($username !== ''): ?>

                                <span>
                                    @<?= htmlspecialchars(
                                        $username,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            <?php endif; ?>

                        </div>

                    </a>

                    <?php if ($isPublic && $username !== ''): ?>

                        <form
                            method="post"
                            action="/profile/<?= rawurlencode($username) ?>/unfollow"
                            onsubmit="return confirm(<?= htmlspecialchars(
                                json_encode(
                                    $t(
                                        'account.main.following.confirm_unfollow'
                                    ),
                                    JSON_HEX_APOS
                                    | JSON_HEX_QUOT
                                    | JSON_UNESCAPED_UNICODE
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>);"
                        >

                            <button
                                type="submit"
                                class="mv-link-danger"
                            >
                                <?= htmlspecialchars(
                                    $t('account.main.following.unfollow'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </button>

                        </form>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <?php

    echo $component('panel', [
        'title' => $t(
            'account.main.following.panel_title',
            [
                'count' => count($following),
            ]
        ),
        'class' => 'mv-account-panel mv-following-panel',
        'content' => (string) ob_get_clean(),
    ]);

    ?>

    <form
        id="account-chat-preferences"
        class="mv-account-form"
        method="post"
        action="/account"
        data-i18n-local-saved="<?= htmlspecialchars(
            $t('account.main.js.local_saved'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-local-cleared="<?= htmlspecialchars(
            $t('account.main.js.local_cleared'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-connecting="<?= htmlspecialchars(
            $t('account.main.js.doge.connecting'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-connected-address="<?= htmlspecialchars(
            $t('account.main.js.doge.connected_address'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-connect-failed="<?= htmlspecialchars(
            $t('account.main.js.doge.connect_failed'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-use-simosnap-address="<?= htmlspecialchars(
            $t('account.main.js.doge.use_simosnap_address'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-wallet-connected-address="<?= htmlspecialchars(
            $t('account.main.js.doge.wallet_connected_address'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-configured-address="<?= htmlspecialchars(
            $t('account.main.js.doge.configured_address'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-wallet-detected="<?= htmlspecialchars(
            $t('account.main.js.doge.wallet_detected'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-unavailable="<?= htmlspecialchars(
            $t('crypto.js.errors.mydoge_unavailable'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        data-i18n-doge-not-authorized="<?= htmlspecialchars(
            $t('crypto.js.errors.connection_not_authorized'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        >

        <?php ob_start(); ?>

        <div class="mv-account-grid">

            <p>
                <label for="pref_nick">
                    <?= htmlspecialchars(
                        $t('account.main.chat.nickname'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <select id="pref_nick" name="nickname">

                    <?php foreach (($user['aliases'] ?? []) as $alias): ?>

                        <?php $alias = (string) $alias; ?>

                        <option
                            value="<?= htmlspecialchars(
                                $alias,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= $selectedNickname === $alias
                                ? 'selected'
                                : '' ?>>

                            <?= htmlspecialchars(
                                $alias,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>
            </p>

            <p>

                <label for="pref_age">
                    <?= htmlspecialchars(
                        $t('account.main.chat.age'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    type="text"
                    id="pref_age"
                    name="age"
                    inputmode="numeric"
                    value="<?= htmlspecialchars(
                        (string) ($profile['age'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

            </p>

            <p>

                <label for="pref_sex">
                    <?= htmlspecialchars(
                        $t('account.main.chat.profile'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <select id="pref_sex" name="sex">

                    <option
                        value="U"
                        <?= $selectedSex === 'U'
                            ? 'selected'
                            : '' ?>>
                        <?= htmlspecialchars(
                            $t('account.main.chat.not_specified'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                    <option
                        value="M"
                        <?= $selectedSex === 'M'
                            ? 'selected'
                            : '' ?>>
                        <?= htmlspecialchars(
                            $t('account.main.chat.male'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                    <option
                        value="F"
                        <?= $selectedSex === 'F'
                            ? 'selected'
                            : '' ?>>
                        <?= htmlspecialchars(
                            $t('account.main.chat.female'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                    <option
                        value="O"
                        <?= $selectedSex === 'O'
                            ? 'selected'
                            : '' ?>>
                        <?= htmlspecialchars(
                            $t('account.main.chat.other'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                </select>
            </p>

            <p class="mv-account-full">

                <label for="pref_location">
                    <?= htmlspecialchars(
                        $t('account.main.chat.city'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    type="text"
                    id="pref_location"
                    name="city"
                    value="<?= htmlspecialchars(
                        (string) ($profile['city'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

            </p>

        </div>

        <?php

        echo $component('panel', [
            'title' => $t('account.main.chat.panel_title'),
            'class' => 'mv-account-panel',
            'content' => (string) ob_get_clean(),
        ]);

        ?>

        <?php ob_start(); ?>

        <div class="mv-save-cards">

            <label class="mv-save-card <?= !$isDatabase
                ? 'is-active'
                : '' ?>">

                <input
                    type="radio"
                    name="save_target"
                    value="local"
                    <?= !$isDatabase ? 'checked' : '' ?>>

                <span class="mv-save-card-icon">
                    💻
                </span>

                <span>
                    <strong>
                        <?= htmlspecialchars(
                            $t('account.main.storage.browser.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                    <small>
                        <?= htmlspecialchars(
                            $t('account.main.storage.browser.subtitle'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>
                </span>

            </label>

            <label class="mv-save-card <?= $isDatabase
                ? 'is-active'
                : '' ?>">

                <input
                    type="radio"
                    name="save_target"
                    value="database"
                    <?= $isDatabase ? 'checked' : '' ?>>

                <span class="mv-save-card-icon">
                    ☁️
                </span>

                <span>
                    <strong>
                        <?= htmlspecialchars(
                            $t('account.main.storage.database.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                    <small>
                        <?= htmlspecialchars(
                            $t('account.main.storage.database.subtitle'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>
                </span>

            </label>

        </div>

        <?php

        echo $component('panel', [
            'title' => $t('account.main.storage.panel_title'),
            'class' => 'mv-account-panel',
            'content' => (string) ob_get_clean(),
        ]);

        ?>

        <div
            id="public-profile-options"
            <?= !$isDatabase ? 'hidden' : '' ?>>

            <?php ob_start(); ?>

            <label class="mv-profile-main-toggle">

                <input
                    type="checkbox"
                    id="public_profile"
                    name="public_profile"
                    value="1"
                    <?= !empty($profile['public_profile'])
                        ? 'checked'
                        : '' ?>>

                <span>
                    <?= htmlspecialchars(
                        $t('account.main.public_profile.enabled'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </label>

            <div class="mv-profile-fields">

                <label>
                    <input type="checkbox" checked disabled>

                    <span>
                        <?= htmlspecialchars(
                            $t('account.main.public_profile.nickname'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </label>

                <label>

                    <input
                        type="checkbox"
                        id="public_avatar"
                        name="show_avatar"
                        value="1"
                        <?= !empty($profile['show_avatar'])
                            ? 'checked'
                            : '' ?>>

                    <span>
                        <?= htmlspecialchars(
                            $t('account.main.public_profile.avatar'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </label>

                <label>

                    <input
                        type="checkbox"
                        id="public_aliases"
                        name="show_aliases"
                        value="1"
                        <?= !empty($profile['show_aliases'])
                            ? 'checked'
                            : '' ?>>

                    <span>
                        <?= htmlspecialchars(
                            $t('account.main.public_profile.aliases'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </label>

                <label>

                    <input
                        type="checkbox"
                        id="public_age"
                        name="show_age"
                        value="1"
                        <?= !empty($profile['show_age'])
                            ? 'checked'
                            : '' ?>>

                    <span>
                        <?= htmlspecialchars(
                            $t('account.main.public_profile.age'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </label>

                <label>

                    <input
                        type="checkbox"
                        id="public_location"
                        name="show_city"
                        value="1"
                        <?= !empty($profile['show_city'])
                            ? 'checked'
                            : '' ?>>

                    <span>
                        <?= htmlspecialchars(
                            $t('account.main.public_profile.city'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </label>

                <label>

                    <input
                        type="checkbox"
                        id="public_sex"
                        name="show_sex"
                        value="1"
                        <?= !empty($profile['show_sex'])
                            ? 'checked'
                            : '' ?>>

                    <span>
                        <?= htmlspecialchars(
                            $t('account.main.public_profile.sex'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </label>

                <label>

                    <input
                        type="checkbox"
                        id="public_irc_stats"
                        name="show_irc_stats"
                        value="1"
                        <?= !empty($profile['show_irc_stats'])
                            ? 'checked'
                            : '' ?>>

                    <span>
                        <?= htmlspecialchars(
                            $t('account.main.public_profile.irc_stats'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </label>

                <label class="mv-profile-field-with-note">

                    <input
                        type="checkbox"
                        id="allow_indexing"
                        name="allow_indexing"
                        value="1"
                        <?= !empty($profile['allow_indexing'])
                            ? 'checked'
                            : '' ?>>

                    <span>

                        <strong>
                            <?= htmlspecialchars(
                                $t(
                                    'account.main.public_profile.indexing.title'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <small>
                            <?= htmlspecialchars(
                                $t(
                                    'account.main.public_profile.indexing.help'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </small>

                    </span>

                </label>

            </div>

            <?php

            echo $component('panel', [
                'title' => $t(
                    'account.main.public_profile.panel_title'
                ),
                'class' => 'mv-account-panel',
                'content' => (string) ob_get_clean(),
            ]);

            ?>

        </div>

        <?php if (($settings['crypto_tips_enabled'] ?? '0') === '1'): ?>

            <?php ob_start(); ?>

            <p class="mv-account-section-description">
                <?= htmlspecialchars(
                    $t('account.main.doge.intro'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <div
                id="mv-doge-tips-form"
                class="mv-doge-tips-form"
            >

                <div class="mv-doge-tip-sources">

                    <label class="mv-doge-tip-source">

                        <input
                            type="radio"
                            name="doge_tip_source"
                            value="mydogemask"
                            <?= ($profile['doge_tip_source'] ?? null) === 'mydogemask'
                                ? 'checked'
                                : '' ?>
                        >

                        <span>

                            <strong>
                                <?= htmlspecialchars(
                                    $t('account.main.doge.mydogemask.title'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                            <small>
                                <?= htmlspecialchars(
                                    $t('account.main.doge.mydogemask.help'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </small>

                        </span>

                    </label>

                    <label class="mv-doge-tip-source">

                        <input
                            type="radio"
                            name="doge_tip_source"
                            value="simosnap"
                            <?= ($profile['doge_tip_source'] ?? null) === 'simosnap'
                                ? 'checked'
                                : '' ?>
                        >

                        <span>

                            <strong>
                                <?= htmlspecialchars(
                                    $t('account.main.doge.simosnap.title'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                            <small>
                                <?= htmlspecialchars(
                                    $t('account.main.doge.simosnap.help'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </small>

                        </span>

                    </label>

                </div>

                <?php if (($profile['doge_tip_source'] ?? '') === 'simosnap'): ?>

                    <div class="mv-doge-wallet-status is-info">

                        <?php if (!empty($simosnapDogecoinAddress)): ?>

                            <?= htmlspecialchars(
                                $t(
                                    'account.main.doge.simosnap.address_configured'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            <strong>
                                <?= htmlspecialchars(
                                    (string) $simosnapDogecoinAddress,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                        <?php else: ?>

                            <?= htmlspecialchars(
                                $t(
                                    'account.main.doge.simosnap.address_missing'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

                <input
                    type="hidden"
                    id="doge_tip_address"
                    name="doge_tip_address"
                    value="<?= htmlspecialchars(
                        (string) ($profile['doge_tip_address'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <div
                    id="mv-doge-wallet-status"
                    class="mv-doge-wallet-status"
                ></div>

                <button
                    type="button"
                    id="mv-doge-connect"
                    class="mv-button mv-button-primary"
                >

                    <i
                        class="fa-solid fa-wallet"
                        aria-hidden="true"
                    ></i>

                    <?= htmlspecialchars(
                        $t('account.main.doge.connect'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </button>

                <button
                    type="submit"
                    class="mv-button"
                    formaction="/account/doge-tips"
                    formmethod="post"
                >
                    <?= htmlspecialchars(
                        $t('account.main.doge.save'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </button>

            </div>

            <?php

            echo $component('panel', [
                'title' => $t('account.main.doge.panel_title'),
                'class' => 'mv-account-panel mv-doge-tips-panel',
                'content' => (string) ob_get_clean(),
            ]);

            ?>

        <?php endif; ?>

        <?php ob_start(); ?>

        <p class="mv-account-section-description">
            <?= htmlspecialchars(
                $t('account.main.privacy.help'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

        <p>

            <a
                href="/account/blocked"
                class="mv-button mv-button-primary"
            >
                <?= htmlspecialchars(
                    $t('account.main.privacy.blocked_users'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </a>

        </p>

        <?php

        echo $component('panel', [
            'title' => $t('account.main.privacy.panel_title'),
            'class' => 'mv-account-panel',
            'content' => (string) ob_get_clean(),
        ]);

        ?>

        <?php ob_start(); ?>

        <button type="submit">
            <?= htmlspecialchars(
                $t('account.main.actions.save_preferences'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </button>

        <div class="mv-account-tools">

            <button
                type="button"
                id="clear-local-chat-preferences"
                class="mv-link-danger"
            >
                <?= htmlspecialchars(
                    $t('account.main.actions.clear_browser'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </button>

            <button
                type="submit"
                formaction="/account/delete"
                formmethod="post"
                class="mv-link-danger"
                onclick="return confirm(<?= htmlspecialchars(
                    json_encode(
                        $t(
                            'account.main.actions.delete_database_confirm'
                        ),
                        JSON_HEX_APOS
                        | JSON_HEX_QUOT
                        | JSON_UNESCAPED_UNICODE
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>);"
            >
                <?= htmlspecialchars(
                    $t('account.main.actions.delete_database'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </button>

            <a href="/account/logout">
                <?= htmlspecialchars(
                    $t('account.main.actions.logout'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </a>

        </div>

        <?php

        echo $component('panel', [
            'class' => 'mv-account-panel',
            'content' => (string) ob_get_clean(),
        ]);

        ?>

    </form>

</section>
