<?php
declare(strict_types=1);

$settings = is_array($settings ?? null)
    ? $settings
    : [];

$success = trim(
    (string) ($success ?? '')
);
?>

<section class="mv-admin-page">

    <header class="mv-admin-page-header">

        <div>

            <p class="mv-admin-page-kicker">
                <?= htmlspecialchars(
                    $t('admin.settings.page.kicker'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <h1>
                <?= htmlspecialchars(
                    $t('admin.settings.page.title'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <p>
                <?= htmlspecialchars(
                    $t('admin.settings.page.description'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

    </header>

    <?php if ($success !== ''): ?>

        <div class="mv-alert mv-alert-success">

            <strong>
                <?= htmlspecialchars(
                    $t('admin.settings.success.title'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>

            <p>
                <?= htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

    <?php endif; ?>

    <section class="mv-admin-card">

        <div class="mv-admin-card-heading">

            <div>

                <h2>
                    <?= htmlspecialchars(
                        $t('admin.settings.identity.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>

                <p>
                    <?= htmlspecialchars(
                        $t('admin.settings.identity.description'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </div>

        <form
            method="post"
            action="/admin/settings"
            enctype="multipart/form-data"
            class="mv-admin-settings-form">

            <div class="mv-admin-field">

                <label for="site_name">
                    <?= htmlspecialchars(
                        $t('admin.settings.identity.site_name.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    type="text"
                    id="site_name"
                    name="site_name"
                    value="<?= htmlspecialchars(
                        (string) (
                            $settings['site_name']
                            ?? 'Monoverse'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>

                <span class="mv-admin-field-help">
                    <?= htmlspecialchars(
                        $t('admin.settings.identity.site_name.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

            <div class="mv-admin-field">

                <label for="site_tagline">
                    <?= htmlspecialchars(
                        $t('admin.settings.identity.tagline.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    type="text"
                    id="site_tagline"
                    name="site_tagline"
                    value="<?= htmlspecialchars(
                        (string) (
                            $settings['site_tagline']
                            ?? 'IRC community websites by SimosNap'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

                <span class="mv-admin-field-help">
                    <?= htmlspecialchars(
                        $t('admin.settings.identity.tagline.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.language.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.language.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label for="default_locale">
                        <?= htmlspecialchars(
                            $t('admin.settings.language.default_label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <select
                        id="default_locale"
                        name="default_locale"
                    >

                        <option
                            value="it"
                            <?= (($settings['default_locale'] ?? 'it') === 'it')
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                $t('admin.settings.language.locales.it'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                        <option
                            value="en"
                            <?= (($settings['default_locale'] ?? 'it') === 'en')
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                $t('admin.settings.language.locales.en'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                    </select>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.language.default_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <span class="mv-admin-field-label">
                        <?= htmlspecialchars(
                            $t('admin.settings.language.available_label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="available_locales[]"
                            value="it"
                            <?= in_array(
                                'it',
                                array_filter(
                                    explode(
                                        ',',
                                        (string) (
                                            $settings['available_locales']
                                            ?? 'it'
                                        )
                                    )
                                ),
                                true
                            )
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.language.locales.it'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="available_locales[]"
                            value="en"
                            <?= in_array(
                                'en',
                                array_filter(
                                    explode(
                                        ',',
                                        (string) (
                                            $settings['available_locales']
                                            ?? 'it'
                                        )
                                    )
                                ),
                                true
                            )
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.language.locales.en'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.language.available_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label for="site_logo">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.logo.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <?php if (!empty($settings['site_logo'])): ?>

                        <div class="mv-admin-brand-preview">

                            <img
                                src="/storage/brand/<?= htmlspecialchars(
                                    $settings['site_logo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $t('admin.settings.brand.logo.alt'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>

                        <button
                            type="submit"
                            name="asset"
                            value="logo"
                            formaction="/admin/settings/brand/delete"
                            formmethod="post"
                            class="mv-admin-button is-danger"
                            onclick="return confirm(<?= htmlspecialchars(
                                json_encode(
                                    $t('admin.settings.brand.logo.delete_confirm'),
                                    JSON_UNESCAPED_UNICODE
                                    | JSON_UNESCAPED_SLASHES
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>);">

                            <?= htmlspecialchars(
                                $t('admin.settings.brand.logo.delete'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </button>

                    <?php endif; ?>

                    <input
                        type="file"
                        id="site_logo"
                        name="site_logo"
                        accept=".png,.jpg,.jpeg,.webp,.svg">

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.logo.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label for="site_favicon">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.favicon.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <?php if (!empty($settings['site_favicon'])): ?>

                        <div class="mv-admin-brand-preview">

                            <img
                                src="/storage/brand/<?= htmlspecialchars(
                                    $settings['site_favicon'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $t('admin.settings.brand.favicon.alt'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>

                        <button
                            type="submit"
                            name="asset"
                            value="favicon"
                            formaction="/admin/settings/brand/delete"
                            formmethod="post"
                            class="mv-admin-button is-danger"
                            onclick="return confirm(<?= htmlspecialchars(
                                json_encode(
                                    $t('admin.settings.brand.favicon.delete_confirm'),
                                    JSON_UNESCAPED_UNICODE
                                    | JSON_UNESCAPED_SLASHES
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>);">

                            <?= htmlspecialchars(
                                $t('admin.settings.brand.favicon.delete'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </button>

                    <?php endif; ?>

                    <input
                        type="file"
                        id="site_favicon"
                        name="site_favicon"
                        accept=".ico,.png,.svg">

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.favicon.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label for="site_apple_touch_icon">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.apple_touch_icon.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <?php if (!empty($settings['site_apple_touch_icon'])): ?>

                        <div class="mv-admin-brand-preview">

                            <img
                                src="/storage/brand/<?= htmlspecialchars(
                                    $settings['site_apple_touch_icon'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $t('admin.settings.brand.apple_touch_icon.alt'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>

                        <button
                            type="submit"
                            name="asset"
                            value="apple-touch-icon"
                            formaction="/admin/settings/brand/delete"
                            formmethod="post"
                            class="mv-admin-button is-danger"
                            onclick="return confirm(<?= htmlspecialchars(
                                json_encode(
                                    $t('admin.settings.brand.apple_touch_icon.delete_confirm'),
                                    JSON_UNESCAPED_UNICODE
                                    | JSON_UNESCAPED_SLASHES
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>);">

                            <?= htmlspecialchars(
                                $t('admin.settings.brand.apple_touch_icon.delete'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </button>

                    <?php endif; ?>

                    <input
                        type="file"
                        id="site_apple_touch_icon"
                        name="site_apple_touch_icon"
                        accept=".png">

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.apple_touch_icon.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label for="site_og_image">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.og_image.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <?php if (!empty($settings['site_og_image'])): ?>

                        <div class="mv-admin-brand-preview">

                            <img
                                src="/storage/brand/<?= htmlspecialchars(
                                    $settings['site_og_image'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $t('admin.settings.brand.og_image.alt'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>

                        <button
                            type="submit"
                            name="asset"
                            value="opengraph"
                            formaction="/admin/settings/brand/delete"
                            formmethod="post"
                            class="mv-admin-button is-danger"
                            onclick="return confirm(<?= htmlspecialchars(
                                json_encode(
                                    $t('admin.settings.brand.og_image.delete_confirm'),
                                    JSON_UNESCAPED_UNICODE
                                    | JSON_UNESCAPED_SLASHES
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>);">

                            <?= htmlspecialchars(
                                $t('admin.settings.brand.og_image.delete'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </button>

                    <?php endif; ?>

                    <input
                        type="file"
                        id="site_og_image"
                        name="site_og_image"
                        accept=".jpg,.jpeg,.png,.webp">

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.brand.og_image.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.seo.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.seo.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label for="site_url">
                        <?= htmlspecialchars(
                            $t('admin.settings.seo.site_url.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="url"
                        id="site_url"
                        name="site_url"
                        placeholder="https://community.example.org"
                        value="<?= htmlspecialchars(
                            (string) (
                                $settings['site_url']
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.seo.site_url.help_before'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        <strong>https://</strong>.
                        <?= htmlspecialchars(
                            $t('admin.settings.seo.site_url.help_after'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label for="meta_description">
                        <?= htmlspecialchars(
                            $t('admin.settings.seo.meta_description.label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="4"
                        maxlength="320"
                        placeholder="<?= htmlspecialchars(
                            $t('admin.settings.seo.meta_description.placeholder'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"><?= htmlspecialchars(
                            (string) (
                                $settings['meta_description']
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.seo.meta_description.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.github.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.github.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label for="github_api_token">
                        <?= htmlspecialchars(
                            $t('admin.settings.github.token_label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="password"
                        id="github_api_token"
                        name="github_api_token"
                        value=""
                        autocomplete="new-password"
                        placeholder="<?= htmlspecialchars(
                            !empty($settings['github_api_token'])
                                ? $t('admin.settings.github.token_configured')
                                : $t('admin.settings.github.token_placeholder'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.github.token_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.pages_navigation.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.pages_navigation.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="pages_navigation_main"
                            value="1"
                            <?= (($settings['pages_navigation_main'] ?? '1') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.pages_navigation.enable'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.pages_navigation.help_before'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        <strong><?= htmlspecialchars(
                            $t('admin.settings.pages_navigation.main_menu'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></strong>,
                        <?= htmlspecialchars(
                            $t('admin.settings.pages_navigation.help_after'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.media.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.media.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="media_audio_upload_enabled"
                            value="1"
                            <?= (($settings['media_audio_upload_enabled'] ?? '1') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.media.audio.enable'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.media.audio.enable_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label for="media_audio_max_mb">
                        <?= htmlspecialchars(
                            $t('admin.settings.media.audio.limit_label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="number"
                        id="media_audio_max_mb"
                        name="media_audio_max_mb"
                        min="1"
                        step="1"
                        value="<?= htmlspecialchars(
                            (string) (
                                $settings['media_audio_max_mb']
                                ?? '50'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.media.audio.limit_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="media_video_upload_enabled"
                            value="1"
                            <?= (($settings['media_video_upload_enabled'] ?? '1') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.media.video.enable'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.media.video.enable_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label for="media_video_max_mb">
                        <?= htmlspecialchars(
                            $t('admin.settings.media.video.limit_label'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="number"
                        id="media_video_max_mb"
                        name="media_video_max_mb"
                        min="1"
                        step="1"
                        value="<?= htmlspecialchars(
                            (string) (
                                $settings['media_video_max_mb']
                                ?? '50'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.media.video.limit_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="media_require_text_with_audio_video"
                            value="1"
                            <?= (($settings['media_require_text_with_audio_video'] ?? '1') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.media.require_text.label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.media.require_text.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.chanzine.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.chanzine.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="chanzine_user_submissions_enabled"
                            value="1"
                            <?= (($settings['chanzine_user_submissions_enabled'] ?? '0') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.chanzine.user_submissions'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.chanzine.user_submissions_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-settings-section">

                <div class="mv-admin-settings-section-heading">

                    <h2>
                        <?= htmlspecialchars(
                            $t('admin.settings.crypto.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('admin.settings.crypto.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="crypto_tips_enabled"
                            value="1"
                            <?= (($settings['crypto_tips_enabled'] ?? '0') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.crypto.enable.label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.crypto.enable.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="crypto_tips_profiles_enabled"
                            value="1"
                            <?= (($settings['crypto_tips_profiles_enabled'] ?? '1') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.crypto.profiles.label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.crypto.profiles.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-admin-field">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="crypto_tips_pings_enabled"
                            value="1"
                            <?= (($settings['crypto_tips_pings_enabled'] ?? '1') === '1')
                                ? 'checked'
                                : '' ?>
                        >

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.settings.crypto.pings.label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </label>

                    <span class="mv-admin-field-help">
                        <?= htmlspecialchars(
                            $t('admin.settings.crypto.pings.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

            <div class="mv-admin-form-actions">

                <button
                    type="submit"
                    class="mv-admin-button">

                    <?= htmlspecialchars(
                        $t('admin.settings.save'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </button>

            </div>

        </form>

    </section>

</section>
