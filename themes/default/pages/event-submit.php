<?php
declare(strict_types=1);
?>

<section class="chanzine-submit events-submit">

    <header class="chanzine-submit-header">

        <div>

            <span class="chanzine-submit-eyebrow">
                <?= htmlspecialchars(
                    $t('events.submit.eyebrow'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

            <h1>
                <?= htmlspecialchars(
                    $t('events.submit.title'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <p>
                <?= htmlspecialchars(
                    $t('events.submit.intro'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        </div>

    </header>

    <?php if (!empty($error)): ?>

        <div class="mv-alert mv-alert-error">
            <?= htmlspecialchars(
                (string) $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>

    <form
        method="post"
        action="/events/submit"
        enctype="multipart/form-data"
        class="chanzine-submit-form"
    >

        <div class="chanzine-submit-main">

            <section class="chanzine-submit-card">

                <div class="chanzine-submit-card-header">

                    <h2>
                        <?= htmlspecialchars(
                            $t('events.submit.event.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('events.submit.event.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-field">

                    <label for="title">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        maxlength="255"
                        required
                    >

                </div>

                <div class="mv-field">

                    <label for="description">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.description'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <span class="chanzine-submit-field-help">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.description_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <textarea
                        id="description"
                        name="description"
                        rows="18"
                        required
                    ></textarea>

                </div>

            </section>

            <section class="chanzine-submit-card">

                <div class="chanzine-submit-card-header">

                    <h2>
                        <?= htmlspecialchars(
                            $t('events.submit.schedule.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('events.submit.schedule.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-field">

                    <label for="starts_at">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.starts_at'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="datetime-local"
                        id="starts_at"
                        name="starts_at"
                        required
                    >

                </div>

                <div class="mv-field">

                    <label for="ends_at">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.ends_at'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <span class="chanzine-submit-field-help">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.ends_at_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <input
                        type="datetime-local"
                        id="ends_at"
                        name="ends_at"
                    >

                </div>

            </section>

        </div>

        <aside class="chanzine-submit-sidebar">

            <section class="chanzine-submit-card">

                <div class="chanzine-submit-card-header">

                    <h2>
                        <?= htmlspecialchars(
                            $t('events.submit.location.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('events.submit.location.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-field">

                    <label for="location">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.location'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        maxlength="255"
                    >

                </div>

                <div class="mv-field">

                    <label for="latitude">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.latitude'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="number"
                        id="latitude"
                        name="latitude"
                        step="0.0000001"
                        min="-90"
                        max="90"
                    >

                </div>

                <div class="mv-field">

                    <label for="longitude">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.longitude'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <input
                        type="number"
                        id="longitude"
                        name="longitude"
                        step="0.0000001"
                        min="-180"
                        max="180"
                    >

                    <span class="chanzine-submit-field-help">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.coordinates_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="mv-field">

                    <label for="external_url">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.external_url'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <span class="chanzine-submit-field-help">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.external_url_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <input
                        type="url"
                        id="external_url"
                        name="external_url"
                        maxlength="1000"
                    >

                </div>

            </section>

            <section class="chanzine-submit-card">

                <div class="chanzine-submit-card-header">

                    <h2>
                        <?= htmlspecialchars(
                            $t('events.submit.cover.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('events.submit.cover.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="mv-field">

                    <label for="cover">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.cover'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </label>

                    <span class="chanzine-submit-field-help">
                        <?= htmlspecialchars(
                            $t('events.submit.fields.cover_help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <input
                        type="file"
                        id="cover"
                        name="cover"
                        accept="image/jpeg,image/png,image/webp"
                    >

                </div>

            </section>

            <section class="chanzine-submit-card chanzine-submit-publish">

                <div class="chanzine-submit-card-header">

                    <h2>
                        <?= htmlspecialchars(
                            $t('events.submit.publish.title'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $t('events.submit.publish.help'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="chanzine-submit-actions">

                    <a
                        href="/events"
                        class="button"
                    >
                        <?= htmlspecialchars(
                            $t('events.submit.publish.cancel'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>

                    <button
                        type="submit"
                        class="button button-primary"
                    >
                        <?= htmlspecialchars(
                            $t('events.submit.publish.submit'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </button>

                </div>

            </section>

        </aside>

    </form>

</section>
