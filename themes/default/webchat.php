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
                    $t('admin.webchat.kicker'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <h1>
                <?= htmlspecialchars(
                    $t('admin.webchat.title'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <p>
                <?= htmlspecialchars(
                    $t('admin.webchat.description'),
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
                    $t('admin.webchat.success'),
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
                        $t('admin.webchat.configuration.title'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>

                <p>
                    <?= htmlspecialchars(
                        $t('admin.webchat.configuration.description'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </div>

        <form
            method="post"
            action="/admin/chat"
            class="mv-admin-settings-form">

            <div class="mv-admin-field">

                <label for="chat_default_channel">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.default_channel.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    id="chat_default_channel"
                    type="text"
                    name="chat_default_channel"
                    value="<?= htmlspecialchars(
                        (string) ($settings['chat_default_channel'] ?? '#chat'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>

                <span class="mv-admin-field-help">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.default_channel.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

            <div class="mv-admin-field">

                <label class="mv-admin-switch">

                    <input
                        type="checkbox"
                        name="landing_show_hero"
                        value="1"
                        <?= !empty($settings['landing_show_hero'])
                            ? 'checked'
                            : '' ?>
                    >

                    <span
                        class="mv-admin-switch-slider"
                        aria-hidden="true"
                    ></span>

                    <span class="mv-admin-switch-text">

                        <strong>
                            <?= htmlspecialchars(
                                $t('admin.webchat.fields.show_hero.label'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $t('admin.webchat.fields.show_hero.help'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </span>

                </label>

            </div>

            <div class="mv-admin-field">

                <label class="mv-admin-switch">

                    <input
                        type="checkbox"
                        name="landing_show_channel_card"
                        value="1"
                        <?= !empty($settings['landing_show_channel_card'])
                            ? 'checked'
                            : '' ?>
                    >

                    <span
                        class="mv-admin-switch-slider"
                        aria-hidden="true"
                    ></span>

                    <span class="mv-admin-switch-text">

                        <strong>
                            <?= htmlspecialchars(
                                $t(
                                    'admin.webchat.fields.show_channel_card.label'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $t(
                                    'admin.webchat.fields.show_channel_card.help'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </span>

                </label>

            </div>

            <div class="mv-admin-field">

                <label for="chat_title">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.chat_title.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    id="chat_title"
                    type="text"
                    name="chat_title"
                    value="<?= htmlspecialchars(
                        (string) ($settings['chat_title'] ?? '#chat - Chat'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required>

                <span class="mv-admin-field-help">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.chat_title.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

            <div class="mv-admin-field">

                <label for="chat_theme">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.theme.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    id="chat_theme"
                    type="text"
                    name="chat_theme"
                    value="<?= htmlspecialchars(
                        (string) ($settings['chat_theme'] ?? 'Osprey'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

                <span class="mv-admin-field-help">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.theme.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

            <div class="mv-admin-field">

                <label for="chat_state_key">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.state_key.label'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </label>

                <input
                    id="chat_state_key"
                    type="text"
                    name="chat_state_key"
                    value="<?= htmlspecialchars(
                        (string) ($settings['chat_state_key'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

                <span class="mv-admin-field-help">
                    <?= htmlspecialchars(
                        $t('admin.webchat.fields.state_key.help'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

            <div class="mv-admin-form-actions">

                <button
                    type="submit"
                    class="mv-button">

                    <?= htmlspecialchars(
                        $t('admin.webchat.actions.save'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </button>

            </div>

        </form>

    </section>

</section>
