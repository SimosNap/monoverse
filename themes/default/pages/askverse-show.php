<?php
declare(strict_types=1);

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$question = $question ?? [];
$questionAttachments = $questionAttachments ?? [];
$answers = $answers ?? [];
$sort = $sort ?? 'votes';
$isLogged = (bool) ($isLogged ?? false);
$isAuthor = (bool) ($isAuthor ?? false);
$isModerator = (bool) ($isModerator ?? false);

$author = trim((string) (
    $question['nickname']
    ?? $question['username']
    ?? ''
));

if ($author === '') {
    $author = $t('askverse.index.unknown_user');
}

$tags = $question['tags'] ?? [];
$tags = is_array($tags) ? $tags : [];
?>

<div class="container">
    <section class="askverse-page">

        <nav class="askverse-detail-navigation">
            <a href="/askverse">
                &larr; AskVerse
            </a>

            <?php if ($isAuthor): ?>
                <a href="/askverse/<?= rawurlencode((string) $question['uuid']) ?>/edit">
                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                    <?= $escape($t('askverse.show.edit_question')) ?>
                </a>
            <?php endif; ?>

            <?php if ($isModerator || ($isAuthor && (int) ($question['answers_count'] ?? 0) === 0)): ?>
                <button
                    type="button"
                    class="askverse-delete-question"
                    data-delete-url="/askverse/<?= rawurlencode((string) $question['uuid']) ?>/delete"
                >
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    <?= $escape($t('askverse.show.delete_question')) ?>
                </button>
            <?php endif; ?>
        </nav>

        <article class="askverse-detail-question">

            <header class="askverse-detail-header">
                <h1><?= $escape($question['title'] ?? '') ?></h1>

                <div class="askverse-question-meta">
                    <?= $escape($t('askverse.index.published_by')) ?>
                    <strong><?= $escape($author) ?></strong>

                    <?php if (!empty($question['created_at'])): ?>
                        <time datetime="<?= $escape($question['created_at']) ?>">
                            <?= $escape($question['created_at']) ?>
                        </time>
                    <?php endif; ?>
                </div>
            </header>

            <div class="askverse-detail-content">
                <?= nl2br($escape($question['content'] ?? '')) ?>
            </div>

            <?php if ($questionAttachments !== []): ?>
                <div class="askverse-attachments">
                    <?php foreach ($questionAttachments as $attachment): ?>
                        <a
                            class="askverse-attachment"
                            href="/askverse/attachments/<?= rawurlencode((string) $attachment['uuid']) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                            <?= $escape($attachment['original_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php
            $metadata = json_decode(
                (string) ($question['metadata'] ?? ''),
                true
            );

            $codeBlock = is_array($metadata)
                ? ($metadata['code_block'] ?? null)
                : null;
            ?>

            <?php if (is_array($codeBlock) && trim((string) ($codeBlock['code'] ?? '')) !== ''): ?>
                <div class="askverse-detail-code">
                    <?php
                    $code = (string) $codeBlock['code'];
                    $language = (string) ($codeBlock['language'] ?? 'text');

                    require __DIR__ . '/../components/code-block.php';
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($tags !== []): ?>
                <div class="askverse-question-tags">
                    <?php foreach ($tags as $tag): ?>
                        <?php
                        $tagName = is_array($tag)
                            ? (string) ($tag['tag'] ?? '')
                            : (string) $tag;
                        ?>

                        <?php if ($tagName !== ''): ?>
                            <a
                                class="askverse-tag"
                                href="/askverse?tag=<?= rawurlencode($tagName) ?>"
                            >
                                <?= $escape($tagName) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($question['accepted_answer_id'])): ?>
                <div class="askverse-detail-solved">
                    <?= $escape($t('askverse.index.solved_badge')) ?>
                </div>
            <?php endif; ?>

            <div
                class="askverse-voting"
                data-vote-url="/askverse/<?= rawurlencode((string) ($question['uuid'] ?? '')) ?>/vote"
            >
                <button
                    type="button"
                    class="askverse-vote-button"
                    data-vote="1"
                    aria-label="<?= $escape($t('askverse.show.vote_up')) ?>"
                    <?= !$isLogged || $isAuthor ? 'disabled' : '' ?>
                >
                    <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
                </button>

                <span class="askverse-vote-score" aria-live="polite">
                    <?= (int) ($question['score'] ?? 0) ?>
                </span>

                <button
                    type="button"
                    class="askverse-vote-button"
                    data-vote="-1"
                    aria-label="<?= $escape($t('askverse.show.vote_down')) ?>"
                    <?= !$isLogged || $isAuthor ? 'disabled' : '' ?>
                >
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </div>

        </article>

        <section class="askverse-answers">

            <header class="askverse-answers-header">
                <h2>
                    <?= count($answers) ?>
                    <?= $escape(
                        count($answers) === 1
                            ? $t('askverse.show.answer')
                            : $t('askverse.index.answers')
                    ) ?>
                </h2>

                <?php if (count($answers) > 1): ?>
                    <div class="askverse-answers-sort">
                        <a
                            href="?sort=votes"
                            <?= $sort === 'votes' ? 'aria-current="page"' : '' ?>
                        >
                            <?= $escape($t('askverse.index.votes')) ?>
                        </a>

                        <a
                            href="?sort=newest"
                            <?= $sort === 'newest' ? 'aria-current="page"' : '' ?>
                        >
                            <?= $escape($t('askverse.index.recent')) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </header>

            <?php if ($answers === []): ?>

                <div class="askverse-empty">
                    <?= $escape($t('askverse.show.no_answers')) ?>
                </div>

            <?php else: ?>

                <div class="askverse-answers-list">

                    <?php foreach ($answers as $answer): ?>
                        <?php
                        $answerAuthor = trim((string) (
                            $answer['nickname']
                            ?? $answer['username']
                            ?? ''
                        ));

                        if ($answerAuthor === '') {
                            $answerAuthor = $t('askverse.index.unknown_user');
                        }

                        $isAccepted = (int) (
                            $question['accepted_answer_id'] ?? 0
                        ) === (int) ($answer['id'] ?? 0);
                        ?>

                        <article
                            class="askverse-answer<?= $isAccepted ? ' is-accepted' : '' ?>"
                            id="answer-<?= $escape($answer['uuid'] ?? '') ?>"
                        >

                            <div class="askverse-answer-body">
                                <?= nl2br($escape($answer['content'] ?? '')) ?>
                            </div>

                            <?php if (!empty($answer['attachments'])): ?>
                                <div class="askverse-attachments">
                                    <?php foreach ($answer['attachments'] as $attachment): ?>
                                        <a
                                            class="askverse-attachment"
                                            href="/askverse/attachments/<?= rawurlencode((string) $attachment['uuid']) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                                            <?= $escape($attachment['original_name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (
                                $isLogged
                                && (string) ($answer['author_sub'] ?? '') === (string) $currentSub
                            ): ?>
                                <form
                                    class="askverse-edit-answer-form"
                                    action="/askverse/answers/<?= rawurlencode((string) $answer['uuid']) ?>/update"
                                    method="post"
                                    hidden
                                >
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= $escape((string) ($csrfToken ?? '')) ?>"
                                    >

                                    <div class="askverse-form-group">
                                        <textarea
                                            name="content"
                                            rows="7"
                                            required
                                        ><?= $escape($answer['content'] ?? '') ?></textarea>
                                    </div>

                                    <?php
                                    $editMetadata = json_decode(
                                        (string) ($answer['metadata'] ?? ''),
                                        true
                                    );

                                    $editCodeBlock = is_array($editMetadata)
                                        ? ($editMetadata['code_block'] ?? null)
                                        : null;

                                    $editCode = is_array($editCodeBlock)
                                        ? (string) ($editCodeBlock['code'] ?? '')
                                        : '';

                                    $editLanguage = is_array($editCodeBlock)
                                        ? (string) ($editCodeBlock['language'] ?? 'text')
                                        : 'text';
                                    ?>

                                    <div class="askverse-code-section">
                                        <button
                                            type="button"
                                            class="askverse-edit-code-toggle"
                                            aria-expanded="<?= $editCode !== '' ? 'true' : 'false' ?>"
                                        >
                                            <i class="fa-solid fa-code" aria-hidden="true"></i>
                                            <?= $escape($t('ping.composer.code')) ?>
                                        </button>

                                        <div class="askverse-code-composer" <?= $editCode === '' ? 'hidden' : '' ?>>
                                            <div class="askverse-form-group">
                                                <label>
                                                    <?= $escape($t('ping.composer.code_language')) ?>
                                                </label>

                                                <select name="code_language">
                                                    <?php foreach ([
                                                        'text' => 'Text',
                                                        'php' => 'PHP',
                                                        'javascript' => 'JavaScript',
                                                        'html' => 'HTML',
                                                        'css' => 'CSS',
                                                        'sql' => 'SQL',
                                                        'bash' => 'Bash',
                                                        'python' => 'Python',
                                                        'c' => 'C',
                                                        'cpp' => 'C++',
                                                        'java' => 'Java',
                                                        'json' => 'JSON',
                                                    ] as $value => $label): ?>
                                                        <option
                                                            value="<?= $escape($value) ?>"
                                                            <?= $editLanguage === $value ? 'selected' : '' ?>
                                                        >
                                                            <?= $escape($label) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="askverse-form-group">
                                                <textarea
                                                    name="code"
                                                    rows="8"
                                                    maxlength="10000"
                                                    spellcheck="false"
                                                    placeholder="<?= $escape($t('ping.composer.code_placeholder')) ?>"
                                                ><?= $escape($editCode) ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="askverse-form-actions">
                                        <button type="submit" class="askverse-submit">
                                            <?= $escape($t('askverse.show.save_changes')) ?>
                                        </button>

                                        <button type="button" class="askverse-cancel-edit-answer">
                                            <?= $escape($t('askverse.show.cancel')) ?>
                                        </button>
                                    </div>

                                    <div
                                        class="askverse-edit-answer-error"
                                        role="alert"
                                        hidden
                                    ></div>
                                </form>
                            <?php endif; ?>

                            <?php
                            $answerMetadata = json_decode(
                                (string) ($answer['metadata'] ?? ''),
                                true
                            );

                            $answerCodeBlock = is_array($answerMetadata)
                                ? ($answerMetadata['code_block'] ?? null)
                                : null;
                            ?>

                            <?php if (is_array($answerCodeBlock) && trim((string) ($answerCodeBlock['code'] ?? '')) !== ''): ?>
                                <div class="askverse-detail-code">
                                    <?php
                                    $code = (string) $answerCodeBlock['code'];
                                    $language = (string) ($answerCodeBlock['language'] ?? 'text');

                                    require __DIR__ . '/../components/code-block.php';
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div
                                class="askverse-voting"
                                data-vote-url="/askverse/answers/<?= rawurlencode((string) ($answer['uuid'] ?? '')) ?>/vote"
                                data-user-vote="<?= (int) ($answer['user_vote'] ?? 0) ?>"
                            >
                                <button
                                    type="button"
                                    class="askverse-vote-button"
                                    data-vote="1"
                                    aria-label="<?= $escape($t('askverse.show.vote_up')) ?>"
                                    <?= !$isLogged || (string) ($answer['author_sub'] ?? '') === (string) $currentSub ? 'disabled' : '' ?>
                                >
                                    <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
                                </button>

                                <span class="askverse-vote-score" aria-live="polite">
                                    <?= (int) ($answer['score'] ?? 0) ?>
                                </span>

                                <button
                                    type="button"
                                    class="askverse-vote-button"
                                    data-vote="-1"
                                    aria-label="<?= $escape($t('askverse.show.vote_down')) ?>"
                                    <?= !$isLogged || (string) ($answer['author_sub'] ?? '') === (string) $currentSub ? 'disabled' : '' ?>
                                >
                                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                                </button>
                            </div>

                            <footer class="askverse-answer-footer">

                                <div class="askverse-answer-author">
                                    <?= $escape($answerAuthor) ?>

                                    <?php if (!empty($answer['created_at'])): ?>
                                        <time datetime="<?= $escape($answer['created_at']) ?>">
                                            <?= $escape($answer['created_at']) ?>
                                        </time>
                                    <?php endif; ?>
                                </div>

                                <?php if ($isAccepted): ?>
                                    <span class="askverse-accepted-badge">
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                        <?= $escape($t('askverse.show.accepted')) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (
                                    $isLogged
                                    && (string) ($answer['author_sub'] ?? '') === (string) $currentSub
                                ): ?>
                                    <button
                                        type="button"
                                        class="askverse-edit-answer"
                                        data-edit-url="/askverse/answers/<?= rawurlencode((string) $answer['uuid']) ?>/update"
                                    >
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                        <?= $escape($t('askverse.show.edit_answer')) ?>
                                    </button>
                                <?php endif; ?>

                                <?php if (
                                    $isModerator
                                    || (
                                        $isLogged
                                        && (string) ($answer['author_sub'] ?? '') === (string) $currentSub
                                    )
                                ): ?>
                                    <button
                                        type="button"
                                        class="askverse-delete-answer"
                                        data-delete-url="/askverse/answers/<?= rawurlencode((string) $answer['uuid']) ?>/delete"
                                    >
                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        <?= $escape($t('askverse.show.delete_answer')) ?>
                                    </button>
                                <?php endif; ?>

                                <?php if ($isAuthor): ?>
                                    <button
                                        type="button"
                                        class="askverse-accept-button<?= $isAccepted ? ' is-active' : '' ?>"
                                        data-accept-url="/askverse/answers/<?= rawurlencode((string) $answer['uuid']) ?>/accept"
                                        aria-pressed="<?= $isAccepted ? 'true' : 'false' ?>"
                                    >
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                        <?= $escape($isAccepted
                                            ? $t('askverse.show.unaccept_answer')
                                            : $t('askverse.show.accept_answer')) ?>
                                    </button>
                                <?php endif; ?>

                            </footer>

                            <section
                                class="askverse-comments"
                                data-comments-url="/askverse/answers/<?= rawurlencode((string) $answer['uuid']) ?>/comments"
                            >
                                <button
                                    type="button"
                                    class="askverse-comments-toggle"
                                    aria-expanded="false"
                                >
                                    <i class="fa-regular fa-comment" aria-hidden="true"></i>
                                    <?= $escape($t('askverse.show.comments')) ?>
                                </button>

                                <div class="askverse-comments-panel" hidden>

                                    <div class="askverse-comments-list"></div>

                                    <?php if ($isLogged): ?>
                                        <form
                                            class="askverse-comment-form"
                                            action="/askverse/answers/<?= rawurlencode((string) $answer['uuid']) ?>/comments"
                                            method="post"
                                        >
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= $escape((string) ($csrfToken ?? '')) ?>"
                                            >

                                            <textarea
                                                name="content"
                                                rows="3"
                                                maxlength="2000"
                                                required
                                                placeholder="<?= $escape($t('askverse.show.comment_placeholder')) ?>"
                                            ></textarea>

                                            <div
                                                class="askverse-comment-error"
                                                role="alert"
                                                hidden
                                            ></div>

                                            <button
                                                type="submit"
                                                class="askverse-comment-submit"
                                            >
                                                <?= $escape($t('askverse.show.submit_comment')) ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </div>
                            </section>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>
        <?php if ($isLogged): ?>
            <section class="askverse-reply">
                <h2><?= $escape($t('askverse.show.your_answer')) ?></h2>

                <form
                    id="askverse-answer-form"
                    action="/askverse/<?= rawurlencode((string) $question['uuid']) ?>/answers"
                    method="post"
                    enctype="multipart/form-data"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $escape((string) ($csrfToken ?? '')) ?>"
                    >

                    <div class="askverse-form-group">
                        <textarea
                            name="content"
                            rows="7"
                            required
                            placeholder="<?= $escape($t('askverse.show.answer_placeholder')) ?>"
                        ></textarea>
                    </div>

                    <div class="askverse-code-section">
                        <button
                            type="button"
                            class="askverse-code-toggle"
                            aria-expanded="false"
                            aria-controls="askverse-answer-code"
                        >
                            <i class="fa-solid fa-code" aria-hidden="true"></i>
                            <?= $escape($t('ping.composer.code')) ?>
                        </button>

                        <div
                            id="askverse-answer-code"
                            class="askverse-code-composer"
                            hidden
                        >
                            <div class="askverse-form-group">
                                <label for="askverse-answer-language">
                                    <?= $escape($t('ping.composer.code_language')) ?>
                                </label>

                                <select
                                    id="askverse-answer-language"
                                    name="code_language"
                                >
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
                                    rows="8"
                                    maxlength="10000"
                                    spellcheck="false"
                                    placeholder="<?= $escape($t('ping.composer.code_placeholder')) ?>"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="askverse-form-group">
                        <label for="askverse-answer-attachments">
                            <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                            <?= $escape($t('askverse.attachments.label')) ?>
                        </label>

                        <input
                            type="file"
                            id="askverse-answer-attachments"
                            name="attachments[]"
                            accept="image/jpeg,image/png,image/webp,image/gif,image/avif"
                            multiple
                        >

                        <small>
                            <?= $escape($t('askverse.attachments.help')) ?>
                        </small>
                    </div>

                    <div
                        id="askverse-answer-error"
                        class="askverse-form-error"
                        role="alert"
                        hidden
                    ></div>

                    <div class="askverse-form-actions">
                        <button type="submit" class="askverse-submit">
                            <?= $escape($t('askverse.show.submit_answer')) ?>
                        </button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = <?= json_encode(
                (string) ($csrfToken ?? ''),
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ) ?>;

            const acceptLabel = <?= json_encode(
                (string) $t('askverse.show.accept_answer'),
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ) ?>;

            const unacceptLabel = <?= json_encode(
                (string) $t('askverse.show.unaccept_answer'),
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ) ?>;

            const acceptedLabel = <?= json_encode(
                (string) $t('askverse.show.accepted'),
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ) ?>;

            const solvedLabel = <?= json_encode(
                (string) $t('askverse.index.solved_badge'),
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ) ?>;

            const showLabels = <?= json_encode([
                'edit' => (string) $t('askverse.show.edit'),
                'delete' => (string) $t('askverse.show.delete'),
                'saveChanges' => (string) $t('askverse.show.save_changes'),
                'cancel' => (string) $t('askverse.show.cancel'),
                'deleteComment' => (string) $t('askverse.show.delete_comment'),
                'confirmDeleteQuestion' => (string) $t('askverse.show.confirm_delete_question'),
                'confirmDeleteAnswer' => (string) $t('askverse.show.confirm_delete_answer'),
                'confirmDeleteComment' => (string) $t('askverse.show.confirm_delete_comment'),
                'deleteQuestionError' => (string) $t('askverse.show.delete_question_error'),
                'editAnswerError' => (string) $t('askverse.show.edit_answer_error'),
                'deleteAnswerError' => (string) $t('askverse.show.delete_answer_error'),
                'voteError' => (string) $t('askverse.show.vote_error'),
                'acceptAnswerError' => (string) $t('askverse.show.accept_answer_error'),
                'editCommentError' => (string) $t('askverse.show.edit_comment_error'),
                'deleteCommentError' => (string) $t('askverse.show.delete_comment_error'),
                'publishAnswerError' => (string) $t('askverse.show.publish_answer_error'),
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

            const commentMessages = <?= json_encode([
                'loading' => (string) $t('askverse.show.comments_loading'),
                'empty' => (string) $t('askverse.show.comments_empty'),
                'loadError' => (string) $t('askverse.show.comments_load_error'),
                'publishError' => (string) $t('askverse.show.comments_publish_error'),
                'unknownUser' => (string) $t('askverse.index.unknown_user'),
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

            const deleteQuestionButton = document.querySelector('.askverse-delete-question');

            if (deleteQuestionButton) {
                deleteQuestionButton.addEventListener('click', async () => {
                    if (!confirm(showLabels.confirmDeleteQuestion)) {
                        return;
                    }

                    deleteQuestionButton.disabled = true;

                    const data = new FormData();
                    data.append('csrf_token', csrfToken);

                    try {
                        const response = await fetch(deleteQuestionButton.dataset.deleteUrl, {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(
                                result.error || showLabels.deleteQuestionError
                            );
                        }

                        window.location.href = result.redirect;

                    } catch (error) {
                        alert(error.message);
                        deleteQuestionButton.disabled = false;
                    }
                });
            }

            document.querySelectorAll('.askverse-edit-answer').forEach((button) => {
                const answer = button.closest('.askverse-answer');
                const form = answer.querySelector('.askverse-edit-answer-form');
                const body = answer.querySelector('.askverse-answer-body');
                const cancel = form.querySelector('.askverse-cancel-edit-answer');
                const toggle = form.querySelector('.askverse-edit-code-toggle');
                const composer = form.querySelector('.askverse-code-composer');
                const errorBox = form.querySelector('.askverse-edit-answer-error');
                const submit = form.querySelector('[type="submit"]');

                button.addEventListener('click', () => {
                    form.hidden = false;
                    body.hidden = true;
                    button.hidden = true;

                    form.querySelector('textarea[name="content"]').focus();
                });

                cancel.addEventListener('click', () => {
                    form.reset();
                    form.hidden = true;
                    body.hidden = false;
                    button.hidden = false;
                    errorBox.hidden = true;
                });

                toggle.addEventListener('click', () => {
                    composer.hidden = !composer.hidden;
                    toggle.setAttribute('aria-expanded', String(!composer.hidden));
                });

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (submit.disabled) return;

                    errorBox.hidden = true;
                    submit.disabled = true;

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(
                                result.error || showLabels.editAnswerError
                            );
                        }

                        window.location.href = result.redirect;
                        window.location.reload();

                    } catch (error) {
                        errorBox.textContent = error.message;
                        errorBox.hidden = false;
                        submit.disabled = false;
                    }
                });
            });

            document.querySelectorAll('.askverse-delete-answer').forEach((button) => {
                button.addEventListener('click', async () => {
                    if (button.disabled) return;

                    if (!confirm(showLabels.confirmDeleteAnswer)) {
                        return;
                    }

                    button.disabled = true;

                    const data = new FormData();
                    data.append('csrf_token', csrfToken);

                    try {
                        const response = await fetch(button.dataset.deleteUrl, {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(
                                result.error || showLabels.deleteAnswerError
                            );
                        }

                        window.location.href = result.redirect;

                    } catch (error) {
                        alert(error.message);
                        button.disabled = false;
                    }
                });
            });

            document.querySelectorAll('.askverse-voting').forEach((voting) => {
                const buttons = voting.querySelectorAll('.askverse-vote-button');
                const score = voting.querySelector('.askverse-vote-score');

                let currentVote = Number(
                    voting.dataset.userVote ?? <?= (int) ($userVote ?? 0) ?>
                );

                let pending = false;

                const updateButtons = () => {
                    buttons.forEach((button) => {
                        const selected = Number(button.dataset.vote) === currentVote;

                        button.classList.toggle('is-active', selected);
                        button.setAttribute('aria-pressed', String(selected));
                    });
                };

                updateButtons();

                buttons.forEach((button) => {
                    button.addEventListener('click', async () => {
                        if (pending || button.disabled) return;

                        pending = true;

                        const data = new FormData();
                        data.append('csrf_token', csrfToken);
                        data.append('vote', button.dataset.vote);

                        try {
                            const response = await fetch(voting.dataset.voteUrl, {
                                method: 'POST',
                                body: data,
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });

                            const result = await response.json();

                            if (!response.ok || !result.success) {
                                throw new Error(
                                    result.error || showLabels.voteError
                                );
                            }

                            currentVote = Number(result.user_vote);
                            score.textContent = result.score;

                            updateButtons();
                        } catch (error) {
                            alert(error.message);
                        } finally {
                            pending = false;
                        }
                    });
                });
            });

            const answersList = document.querySelector('.askverse-answers-list');
            const acceptButtons = document.querySelectorAll('.askverse-accept-button');

            let acceptPending = false;

            acceptButtons.forEach((button) => {
                button.addEventListener('click', async () => {
                    if (acceptPending || button.disabled) return;

                    acceptPending = true;

                    acceptButtons.forEach((item) => {
                        item.disabled = true;
                    });

                    const data = new FormData();
                    data.append('csrf_token', csrfToken);

                    try {
                        const response = await fetch(button.dataset.acceptUrl, {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(
                                result.error || showLabels.acceptAnswerError
                            );
                        }

                        document.querySelectorAll('.askverse-answer').forEach((answer) => {
                            const isAccepted =
                                result.accepted &&
                                answer.id === 'answer-' + result.answer_uuid;

                            answer.classList.toggle('is-accepted', isAccepted);

                            const badge = answer.querySelector('.askverse-accepted-badge');

                            if (isAccepted) {
                                if (!badge) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'askverse-accepted-badge';

                                    const icon = document.createElement('i');
                                    icon.className = 'fa-solid fa-check';
                                    icon.setAttribute('aria-hidden', 'true');

                                    newBadge.append(
                                        icon,
                                        document.createTextNode(' ' + acceptedLabel)
                                    );

                                    answer.querySelector('.askverse-answer-footer')
                                        .appendChild(newBadge);
                                }
                            } else if (badge) {
                                badge.remove();
                            }

                            const acceptButton = answer.querySelector('.askverse-accept-button');

                            if (acceptButton) {
                                acceptButton.classList.toggle('is-active', isAccepted);
                                acceptButton.setAttribute('aria-pressed', String(isAccepted));

                                const icon = acceptButton.querySelector('i');

                                acceptButton.textContent = '';

                                if (icon) {
                                    acceptButton.appendChild(icon);
                                }

                                acceptButton.appendChild(
                                    document.createTextNode(
                                        ' ' + (isAccepted ? unacceptLabel : acceptLabel)
                                    )
                                );
                            }
                        });

                        const question = document.querySelector('.askverse-detail-question');
                        let solvedBadge = question?.querySelector('.askverse-detail-solved');

                        if (result.accepted) {
                            if (!solvedBadge && question) {
                                solvedBadge = document.createElement('div');
                                solvedBadge.className = 'askverse-detail-solved';
                                solvedBadge.textContent = solvedLabel;

                                const voting = question.querySelector('.askverse-voting');

                                if (voting) {
                                    voting.before(solvedBadge);
                                } else {
                                    question.appendChild(solvedBadge);
                                }
                            }

                            const acceptedAnswer = document.getElementById(
                                'answer-' + result.answer_uuid
                            );

                            if (answersList && acceptedAnswer) {
                                answersList.prepend(acceptedAnswer);
                            }
                        } else if (solvedBadge) {
                            solvedBadge.remove();
                        }
                    } catch (error) {
                        alert(error.message);
                    } finally {
                        acceptPending = false;

                        acceptButtons.forEach((item) => {
                            item.disabled = false;
                        });
                    }
                });
            });

            const renderComments = (list, comments) => {
                list.replaceChildren();

                if (!comments.length) {
                    const empty = document.createElement('div');
                    empty.className = 'askverse-comments-empty';
                    empty.textContent = commentMessages.empty;
                    list.appendChild(empty);
                    return;
                }

                const currentSub = <?= json_encode(
                    (string) ($currentSub ?? ''),
                    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                ) ?>;

                const isModerator = <?= $isModerator ? 'true' : 'false' ?>;

                comments.forEach((comment) => {
                    const item = document.createElement('div');
                    item.className = 'askverse-comment';

                    const meta = document.createElement('div');
                    meta.className = 'askverse-comment-meta';

                    const author = document.createElement('strong');
                    author.textContent =
                        comment.nickname ||
                        comment.username ||
                        commentMessages.unknownUser;

                    meta.appendChild(author);

                    if (comment.created_at) {
                        const time = document.createElement('time');
                        time.dateTime = comment.created_at;
                        time.textContent = comment.created_at;
                        meta.appendChild(time);
                    }

                    const body = document.createElement('div');
                    body.className = 'askverse-comment-body';
                    body.textContent = comment.content || '';

                    item.append(meta, body);

                    const isCommentAuthor =
                        currentSub !== '' &&
                        comment.author_sub === currentSub;

                    if (comment.uuid && isCommentAuthor) {
                        const editButton = document.createElement('button');
                        editButton.type = 'button';
                        editButton.className = 'askverse-edit-comment';
                        editButton.textContent = showLabels.edit;

                        const editForm = document.createElement('form');
                        editForm.className = 'askverse-edit-comment-form';
                        editForm.hidden = true;

                        const textarea = document.createElement('textarea');
                        textarea.name = 'content';
                        textarea.rows = 3;
                        textarea.maxLength = 2000;
                        textarea.required = true;
                        textarea.value = comment.content || '';

                        const saveButton = document.createElement('button');
                        saveButton.type = 'submit';
                        saveButton.className = 'askverse-save-comment';
                        saveButton.textContent = showLabels.saveChanges;

                        const cancelButton = document.createElement('button');
                        cancelButton.type = 'button';
                        cancelButton.className = 'askverse-cancel-edit-comment';
                        cancelButton.textContent = showLabels.cancel;

                        const errorBox = document.createElement('div');
                        errorBox.className = 'askverse-edit-comment-error';
                        errorBox.setAttribute('role', 'alert');
                        errorBox.hidden = true;

                        editForm.append(
                            textarea,
                            saveButton,
                            cancelButton,
                            errorBox
                        );

                        editButton.addEventListener('click', () => {
                            textarea.value = comment.content || '';
                            errorBox.hidden = true;

                            body.hidden = true;
                            editButton.hidden = true;
                            editForm.hidden = false;

                            textarea.focus();
                        });

                        cancelButton.addEventListener('click', () => {
                            editForm.hidden = true;
                            body.hidden = false;
                            editButton.hidden = false;
                            errorBox.hidden = true;
                        });

                        editForm.addEventListener('submit', async (event) => {
                            event.preventDefault();

                            if (saveButton.disabled) return;

                            errorBox.hidden = true;
                            saveButton.disabled = true;

                            const data = new FormData();
                            data.append('csrf_token', csrfToken);
                            data.append('content', textarea.value);

                            try {
                                const response = await fetch(
                                    '/askverse/comments/' +
                                    encodeURIComponent(comment.uuid) +
                                    '/update',
                                    {
                                        method: 'POST',
                                        body: data,
                                        credentials: 'same-origin',
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    }
                                );

                                const result = await response.json();

                                if (!response.ok || !result.success) {
                                    throw new Error(
                                        result.error ||
                                        showLabels.editCommentError
                                    );
                                }

                                comment.content = result.content;
                                body.textContent = result.content;

                                editForm.hidden = true;
                                body.hidden = false;
                                editButton.hidden = false;

                            } catch (error) {
                                errorBox.textContent = error.message;
                                errorBox.hidden = false;
                            } finally {
                                saveButton.disabled = false;
                            }
                        });

                        item.append(editButton, editForm);
                    }

                    if (
                        comment.uuid &&
                        (isModerator || isCommentAuthor)
                    ) {
                        const deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.className = 'askverse-delete-comment';
                        deleteButton.textContent = showLabels.delete;
                        deleteButton.setAttribute('aria-label', showLabels.deleteComment);

                        deleteButton.addEventListener('click', async () => {
                            if (deleteButton.disabled) return;

                            if (!confirm(showLabels.confirmDeleteComment)) {
                                return;
                            }

                            deleteButton.disabled = true;

                            const data = new FormData();
                            data.append('csrf_token', csrfToken);

                            try {
                                const response = await fetch(
                                    '/askverse/comments/' +
                                    encodeURIComponent(comment.uuid) +
                                    '/delete',
                                    {
                                        method: 'POST',
                                        body: data,
                                        credentials: 'same-origin',
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    }
                                );

                                const result = await response.json();

                                if (!response.ok || !result.success) {
                                    throw new Error(
                                        result.error ||
                                        showLabels.deleteCommentError
                                    );
                                }

                                item.remove();

                                if (!list.querySelector('.askverse-comment')) {
                                    const empty = document.createElement('div');
                                    empty.className = 'askverse-comments-empty';
                                    empty.textContent = commentMessages.empty;
                                    list.appendChild(empty);
                                }

                            } catch (error) {
                                alert(error.message);
                                deleteButton.disabled = false;
                            }
                        });

                        item.appendChild(deleteButton);
                    }

                    list.appendChild(item);
                });
            };

            document.querySelectorAll('.askverse-comments').forEach((section) => {
                const toggle = section.querySelector('.askverse-comments-toggle');
                const panel = section.querySelector('.askverse-comments-panel');
                const list = section.querySelector('.askverse-comments-list');
                const commentForm = section.querySelector('.askverse-comment-form');

                let loaded = false;
                let loading = false;

                const loadComments = async () => {
                    if (loading) return;

                    loading = true;
                    list.textContent = commentMessages.loading;

                    try {
                        const response = await fetch(section.dataset.commentsUrl, {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(
                                result.error || commentMessages.loadError
                            );
                        }

                        renderComments(
                            list,
                            Array.isArray(result.comments) ? result.comments : []
                        );

                        loaded = true;
                    } catch (error) {
                        list.textContent = error.message;
                    } finally {
                        loading = false;
                    }
                };

                toggle.addEventListener('click', async () => {
                    const opening = panel.hidden;

                    panel.hidden = !opening;
                    toggle.setAttribute('aria-expanded', String(opening));

                    if (opening && !loaded) {
                        await loadComments();
                    }
                });

                if (commentForm) {
                    const textarea = commentForm.querySelector('textarea[name="content"]');
                    const errorBox = commentForm.querySelector('.askverse-comment-error');
                    const submit = commentForm.querySelector('[type="submit"]');

                    commentForm.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        if (submit.disabled) return;

                        errorBox.hidden = true;
                        submit.disabled = true;

                        try {
                            const response = await fetch(commentForm.action, {
                                method: 'POST',
                                body: new FormData(commentForm),
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });

                            const result = await response.json();

                            if (!response.ok || !result.success) {
                                throw new Error(
                                    result.error || commentMessages.publishError
                                );
                            }

                            textarea.value = '';
                            loaded = false;

                            await loadComments();
                        } catch (error) {
                            errorBox.textContent = error.message;
                            errorBox.hidden = false;
                        } finally {
                            submit.disabled = false;
                        }
                    });
                }
            });

            const form = document.getElementById('askverse-answer-form');
            if (!form) return;

            const toggle = form.querySelector('.askverse-code-toggle');
            const composer = document.getElementById('askverse-answer-code');
            const errorBox = document.getElementById('askverse-answer-error');
            const submit = form.querySelector('[type="submit"]');

            toggle.addEventListener('click', () => {
                composer.hidden = !composer.hidden;
                toggle.setAttribute('aria-expanded', String(!composer.hidden));
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                errorBox.hidden = true;
                submit.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(
                            result.error || showLabels.publishAnswerError
                        );
                    }

                    window.location.href = result.redirect;

                    if (window.location.hash === '#answer-' + result.uuid) {
                        window.location.reload();
                    }
                } catch (error) {
                    errorBox.textContent = error.message;
                    errorBox.hidden = false;
                    submit.disabled = false;
                }
            });
        });
        </script>
    </section>
</div>