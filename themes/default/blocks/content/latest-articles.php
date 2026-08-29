<?php
declare(strict_types=1);

$title = trim(
    (string) (
        $title
        ?? $t('blocks.content.latest_articles.default_title')
    )
);

$articles = is_array($articles ?? null)
    ? $articles
    : [];

$showDate = (bool) ($show_date ?? true);

$blockWidth = (int) ($block['width'] ?? 12);

if (!in_array(
    $blockWidth,
    [3, 4, 6, 8, 9, 12],
    true
)) {
    $blockWidth = 12;
}

$widthClass = 'mv-block-width-' . $blockWidth;
?>

<div class="mv-widget mv-latest-articles-widget <?= htmlspecialchars(
    $widthClass,
    ENT_QUOTES,
    'UTF-8'
) ?>">

    <header class="mv-latest-articles-header">

        <h3>
            <?= htmlspecialchars(
                $title,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h3>

    </header>

    <?php if ($articles !== []): ?>

        <ul class="mv-latest-articles-list">

            <?php foreach ($articles as $article): ?>

                <li>

                    <a
                        class="mv-latest-articles-item"
                        href="/chanzine/<?= rawurlencode(
                            (string) ($article['slug'] ?? '')
                        ) ?>"
                    >

                        <?php
                        $cover = trim(
                            (string) ($article['cover'] ?? '')
                        );

                        if ($cover === '') {
                            $cover = '/themes/default/assets/images/chanzine-default.webp';
                        }
                        ?>

                        <span class="mv-latest-articles-thumb">

                            <img
                                src="<?= htmlspecialchars(
                                    $cover,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt=""
                                loading="lazy"
                            >

                        </span>

                        <span class="mv-latest-articles-body">

                            <strong class="mv-latest-articles-title">
                                <?= htmlspecialchars(
                                    (string) ($article['title'] ?? ''),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                            <?php if (
                                $showDate
                                && !empty($article['published_at'])
                            ): ?>

                                <span class="mv-latest-articles-date">

                                    <?= htmlspecialchars(
                                        $t(
                                            'blocks.content.latest_articles.published'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                    <?= \Monoverse\Helpers\DateHelper::timeAgo(
                                        (string) $article['published_at'],
                                        true,
                                        (string) ($currentLocale ?? 'it')
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </span>

                        <i
                            class="fa-solid fa-chevron-right"
                            aria-hidden="true"
                        ></i>

                    </a>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php else: ?>

        <p class="mv-latest-articles-empty">
            <?= htmlspecialchars(
                $t(
                    'blocks.content.latest_articles.empty'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    <?php endif; ?>

</div>
