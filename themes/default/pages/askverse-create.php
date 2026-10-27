<?php
declare(strict_types=1);

$csrfToken = (string) ($csrfToken ?? '');
$error = (string) ($error ?? '');
$oldTitle = (string) ($oldTitle ?? '');
$oldContent = (string) ($oldContent ?? '');
$oldTags = (string) ($oldTags ?? '');
$oldCode = (string) ($oldCode ?? '');
$oldCodeLanguage = (string) ($oldCodeLanguage ?? 'text');
$hasCode = trim($oldCode) !== '';
$isEditing = !empty($editingQuestion);
$formAction = $isEditing
    ? '/askverse/' . rawurlencode((string) $editingQuestion['uuid'])
    : '/askverse';

$escape = static fn (string $value): string =>
    htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>

<div class="container">
    <section class="askverse-page">

        <header class="askverse-header">
            <div class="askverse-header-content">
                <h1>
                    <?= $escape(
                        $isEditing
                            ? $t('askverse.create.edit_title')
                            : $t('askverse.create.title')
                    ) ?>
                </h1>

                <p>
                    <?= $escape(
                        $isEditing
                            ? $t('askverse.create.edit_description')
                            : $t('askverse.create.description')
                    ) ?>
                </p>
            </div>
        </header>

        <div class="askverse-form-container">

            <?php if ($error !== ''): ?>
                <div class="askverse-form-error" role="alert">
                    <?= $escape($error) ?>
                </div>
            <?php endif; ?>

            <form
                class="askverse-create-form"
                action="<?= $escape($formAction) ?>"
                method="post"
                enctype="multipart/form-data"
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $escape($csrfToken) ?>"
                >

                <div class="askverse-form-group">
                    <label for="askverse-title">
                        <?= $escape($t('askverse.create.question_title')) ?>
                    </label>

                    <input
                        type="text"
                        id="askverse-title"
                        name="title"
                        maxlength="255"
                        required
                        value="<?= $escape($oldTitle) ?>"
                        placeholder="<?= $escape($t('askverse.create.title_placeholder')) ?>"
                    >

                    <small>
                        <?= $escape($t('askverse.create.title_help')) ?>
                    </small>
                </div>

                <div class="askverse-form-group">
                    <label for="askverse-content">
                        <?= $escape($t('askverse.create.content_label')) ?>
                    </label>

                    <textarea
                        id="askverse-content"
                        name="content"
                        rows="10"
                        required
                        placeholder="<?= $escape($t('askverse.create.content_placeholder')) ?>"
                    ><?= $escape($oldContent) ?></textarea>

                    <small>
                        <?= $escape($t('askverse.create.content_help')) ?>
                    </small>
                </div>

                <div class="askverse-code-section">

                    <button
                        type="button"
                        class="askverse-code-toggle"
                        aria-expanded="<?= $hasCode ? 'true' : 'false' ?>"
                        aria-controls="askverse-code-composer"
                        onclick="
                            const composer = document.getElementById('askverse-code-composer');
                            composer.hidden = !composer.hidden;
                            this.setAttribute('aria-expanded', String(!composer.hidden));
                        "
                    >
                        <i class="fa-solid fa-code" aria-hidden="true"></i>
                        <?= $escape($t('ping.composer.code')) ?>
                    </button>

                    <div
                        id="askverse-code-composer"
                        class="askverse-code-composer"
                        <?= $hasCode ? '' : 'hidden' ?>
                    >
                        <div class="askverse-form-group">
                            <label for="askverse-code-language">
                                <?= $escape($t('ping.composer.code_language')) ?>
                            </label>

                            <select
                                id="askverse-code-language"
                                name="code_language"
                            >
                                <?php if ($hasCode): ?>
                                    <option value="<?= $escape($oldCodeLanguage) ?>" selected>
                                        <?= $escape($oldCodeLanguage) ?>
                                    </option>
                                <?php endif; ?>
                                <option value="text">Text</option>
                                <option value="php">PHP</option>
                                <option value="javascript">JavaScript</option>
                                <option value="html">HTML</option>
                                <option value="css">CSS</option>
                                <option value="sql">SQL</option>
                                <option value="bash">Bash</option>
                                <option value="python">Python</option>
                                <option value="c">C</option>
                                <option value="cpp">C++</option>
                                <option value="java">Java</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>

                        <div class="askverse-form-group">
                            <textarea
                                name="code"
                                rows="10"
                                maxlength="10000"
                                spellcheck="false"
                                placeholder="<?= $escape($t('ping.composer.code_placeholder')) ?>"
                            ><?= $escape((string) ($oldCode ?? '')) ?></textarea>

                            <small>0 / 10000</small>
                        </div>
                    </div>

                </div>

                <div class="askverse-form-group">
                    <label for="askverse-tags">
                        <?= $escape($t('askverse.create.tags_label')) ?>
                    </label>

                    <input
                        type="text"
                        id="askverse-tags"
                        name="tags"
                        value="<?= $escape($oldTags) ?>"
                        placeholder="<?= $escape($t('askverse.create.tags_placeholder')) ?>"
                    >

                    <small>
                        <?= $escape($t('askverse.create.tags_help')) ?>
                    </small>
                </div>

                <?php if (!$isEditing): ?>
                    <div class="askverse-form-group">
                        <label for="askverse-attachments">
                            <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                            <?= $escape($t('askverse.attachments.label')) ?>
                        </label>

                        <input
                            type="file"
                            id="askverse-attachments"
                            name="attachments[]"
                            accept="image/jpeg,image/png,image/webp,image/gif,image/avif"
                            multiple
                        >

                        <small>
                            <?= $escape($t('askverse.attachments.help')) ?>
                        </small>
                    </div>
                <?php endif; ?>

                <div class="askverse-form-actions">
                    <a href="/askverse" class="askverse-cancel">
                        <?= $escape($t('askverse.create.cancel')) ?>
                    </a>

                    <button type="submit" class="askverse-submit">
                        <?= $escape(
                            $isEditing
                                ? $t('askverse.create.edit_submit')
                                : $t('askverse.create.submit')
                        ) ?>
                    </button>
                </div>

            </form>

        </div>

    </section>
</div>