<?php
declare(strict_types=1);

$questions = is_array($questions ?? null) ? $questions : [];

$filter = (string) ($filter ?? 'recent');
$search = (string) ($search ?? '');
$tag = (string) ($tag ?? '');

$page = max(1, (int) ($page ?? 1));
$hasMore = (bool) ($hasMore ?? false);
$isLogged = (bool) ($isLogged ?? false);

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
};

$buildUrl = static function (array $changes = []) use (
    $filter,
    $search,
    $tag
): string {
    $params = array_merge(
        [
            'filter' => $filter,
            'q' => $search,
            'tag' => $tag,
        ],
        $changes
    );

    $params = array_filter(
        $params,
        static fn ($value): bool =>
            $value !== null && $value !== ''
    );

    return '/askverse' . (
        $params !== []
            ? '?' . http_build_query($params)
            : ''
    );
};
?>

<div class="container">

    <section class="askverse-page">

        <header class="askverse-header">

            <div class="askverse-heading">
                <h1>AskVerse</h1>
                <p>
                    <?= $escape($t('askverse.index.tagline')) ?>
                </p>
            </div>

            <a
                href="/askverse/new"
                class="askverse-new-question"
            >
                <i
                    class="fa-solid fa-plus"
                    aria-hidden="true"
                ></i>
                <?= $escape($t('askverse.index.ask_question')) ?>
            </a>

        </header>

        <div class="askverse-toolbar">

            <nav
                class="askverse-filters"
                aria-label="<?= $escape($t('askverse.index.filters_label')) ?>"
            >

                <a
                    href="<?= $escape(
                        $buildUrl([
                            'filter' => 'recent',
                            'page' => null,
                        ])
                    ) ?>"
                    class="<?= $filter === 'recent'
                        ? 'is-active'
                        : '' ?>"
                    <?= $filter === 'recent'
                        ? 'aria-current="page"'
                        : '' ?>
                >
                    <?= $escape($t('askverse.index.recent')) ?>
                </a>

                <a
                    href="<?= $escape(
                        $buildUrl([
                            'filter' => 'unanswered',
                            'page' => null,
                        ])
                    ) ?>"
                    class="<?= $filter === 'unanswered'
                        ? 'is-active'
                        : '' ?>"
                    <?= $filter === 'unanswered'
                        ? 'aria-current="page"'
                        : '' ?>
                >
                    <?= $escape($t('askverse.index.unanswered')) ?>
                </a>

                <a
                    href="<?= $escape(
                        $buildUrl([
                            'filter' => 'solved',
                            'page' => null,
                        ])
                    ) ?>"
                    class="<?= $filter === 'solved'
                        ? 'is-active'
                        : '' ?>"
                    <?= $filter === 'solved'
                        ? 'aria-current="page"'
                        : '' ?>
                >
                    <?= $escape($t('askverse.index.solved')) ?>
                </a>

            </nav>

            <form
                class="askverse-search"
                action="/askverse"
                method="get"
                role="search"
            >

                <input
                    type="hidden"
                    name="filter"
                    value="<?= $escape($filter) ?>"
                >

                <?php if ($tag !== ''): ?>

                    <input
                        type="hidden"
                        name="tag"
                        value="<?= $escape($tag) ?>"
                    >

                <?php endif; ?>

                <input
                    type="search"
                    name="q"
                    value="<?= $escape($search) ?>"
                    placeholder="<?= $escape($t('askverse.index.search_placeholder')) ?>"
                    aria-label="<?= $escape($t('askverse.index.search_label')) ?>"
                >

                <button type="submit">
                    <i
                        class="fa-solid fa-magnifying-glass"
                        aria-hidden="true"
                    ></i>
                    <?= $escape($t('askverse.index.search_submit')) ?>
                </button>

            </form>

        </div>

        <?php if ($tag !== ''): ?>

            <div class="askverse-active-tag">

                <span>
                    <?= $escape($t('askverse.index.tag_label')) ?>:
                    <strong><?= $escape($tag) ?></strong>
                </span>

                <a href="<?= $escape(
                    $buildUrl([
                        'tag' => null,
                        'page' => null,
                    ])
                ) ?>">
                    <?= $escape($t('askverse.index.remove_filter')) ?>
                </a>

            </div>

        <?php endif; ?>

        <div class="askverse-list">

            <?php if ($questions === []): ?>

                <div class="card askverse-empty">

                    <i
                        class="fa-regular fa-comments"
                        aria-hidden="true"
                    ></i>

                    <p>
                        <?= $escape($t('askverse.index.empty')) ?>
                    </p>

                </div>

            <?php else: ?>

                <?php foreach ($questions as $question): ?>

                    <?php
                    $uuid = (string) (
                        $question['uuid'] ?? ''
                    );

                    $title = (string) (
                        $question['title'] ?? ''
                    );

                    $content = trim(
                        (string) ($question['content'] ?? '')
                    );

                    $score = (int) (
                        $question['score']
                        ?? $question['vote_score']
                        ?? 0
                    );

                    $answerCount = (int) (
                        $question['answer_count']
                        ?? $question['answers_count']
                        ?? 0
                    );

                    $isSolved = !empty(
                        $question['accepted_answer_id']
                    );

                    $author = trim(
                        (string) (
                            $question['nickname']
                            ?? $question['username']
                            ?? ''
                        )
                    );

                    if ($author === '') {
                        $author = $t('askverse.index.unknown_user');
                    }

                    $tags = is_array(
                        $question['tags'] ?? null
                    )
                        ? $question['tags']
                        : [];
                    ?>

                    <article class="card askverse-question">

                        <div class="askverse-question-stats">

                            <div class="askverse-stat">

                                <strong>
                                    <?= $score ?>
                                </strong>

                                <span>
                                    <?= $escape($t('askverse.index.votes')) ?>
                                </span>

                            </div>

                            <div class="askverse-stat">

                                <strong>
                                    <?= $answerCount ?>
                                </strong>

                                <span>
                                    <?= $escape(
                                        $answerCount === 1
                                            ? $t('askverse.show.answer')
                                            : $t('askverse.index.answers')
                                    ) ?>
                                </span>

                            </div>

                            <?php if ($isSolved): ?>

                                <div class="askverse-solved">

                                    <i
                                        class="fa-solid fa-check"
                                        aria-hidden="true"
                                    ></i>

                                    <span>
                                        <?= $escape($t('askverse.index.solved_badge')) ?>
                                    </span>

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="askverse-question-content">

                            <h2 class="askverse-question-title">

                                <a href="/askverse/<?= $escape(
                                    rawurlencode($uuid)
                                ) ?>">
                                    <?= $escape($title) ?>
                                </a>

                            </h2>

                            <?php if ($content !== ''): ?>

                                <p class="askverse-question-excerpt">

                                    <?= $escape(
                                        mb_strimwidth(
                                            $content,
                                            0,
                                            220,
                                            '…'
                                        )
                                    ) ?>

                                </p>

                            <?php endif; ?>

                            <?php if ($tags !== []): ?>

                                <div class="askverse-question-tags">

                                    <?php foreach ($tags as $questionTag): ?>

                                        <?php
                                        $tagName = is_array($questionTag)
                                            ? (string) (
                                                $questionTag['tag'] ?? ''
                                            )
                                            : (string) $questionTag;

                                        if ($tagName === '') {
                                            continue;
                                        }
                                        ?>

                                        <a
                                            class="askverse-tag"
                                            href="<?= $escape(
                                                $buildUrl([
                                                    'tag' => $tagName,
                                                    'page' => null,
                                                ])
                                            ) ?>"
                                        >
                                            <?= $escape($tagName) ?>
                                        </a>

                                    <?php endforeach; ?>

                                </div>

                            <?php endif; ?>

                            <div class="askverse-question-meta">

                                <span>
                                    <?= $escape($t('askverse.index.published_by')) ?>
                                    <strong>
                                        <?= $escape($author) ?>
                                    </strong>
                                </span>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <?php if ($page > 1 || $hasMore): ?>

            <nav
                class="askverse-pagination"
                aria-label="<?= $escape($t('askverse.index.pagination_label')) ?>"
            >

                <?php if ($page > 1): ?>

                    <a href="<?= $escape(
                        $buildUrl([
                            'page' => $page - 1,
                        ])
                    ) ?>">
                        <i
                            class="fa-solid fa-chevron-left"
                            aria-hidden="true"
                        ></i>
                        <?= $escape($t('askverse.index.previous')) ?>
                    </a>

                <?php endif; ?>

                <span>
                    <?= $escape(
                        $t('askverse.index.page', [
                            'page' => $page,
                        ])
                    ) ?>
                </span>

                <?php if ($hasMore): ?>

                    <a href="<?= $escape(
                        $buildUrl([
                            'page' => $page + 1,
                        ])
                    ) ?>">
                        <?= $escape($t('askverse.index.next')) ?>
                        <i
                            class="fa-solid fa-chevron-right"
                            aria-hidden="true"
                        ></i>
                    </a>

                <?php endif; ?>

            </nav>

        <?php endif; ?>

    </section>

</div>
