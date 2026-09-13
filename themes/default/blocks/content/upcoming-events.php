<?php
declare(strict_types=1);

$title = trim(
    (string) (
        $title
        ?? $t('blocks.content.upcoming_events.default_title')
    )
);

$events = is_array($events ?? null)
    ? $events
    : [];

$showDate = (bool) ($show_date ?? true);
$showLocation = (bool) ($show_location ?? true);

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

<div class="mv-widget mv-upcoming-events-widget <?= htmlspecialchars(
    $widthClass,
    ENT_QUOTES,
    'UTF-8'
) ?>">

    <header class="mv-upcoming-events-header">

        <h3>
            <?= htmlspecialchars(
                $title,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h3>

    </header>

    <?php if ($events !== []): ?>

        <ul class="mv-upcoming-events-list">

            <?php foreach ($events as $event): ?>

                <li>

                    <a
                        class="mv-upcoming-events-item"
                        href="/events/<?= rawurlencode(
                            (string) ($event['slug'] ?? '')
                        ) ?>"
                    >

                        <?php
                        $cover = trim(
                            (string) ($event['cover'] ?? '')
                        );
                        ?>

                        <?php if ($cover !== ''): ?>

                            <span class="mv-upcoming-events-thumb">

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

                        <?php endif; ?>

                        <span class="mv-upcoming-events-body">

                            <strong class="mv-upcoming-events-title">
                                <?= htmlspecialchars(
                                    (string) ($event['title'] ?? ''),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>

                            <?php if (
                                $showDate
                                && !empty($event['starts_at'])
                            ): ?>

                                <span class="mv-upcoming-events-date">

                                    <i
                                        class="fa-regular fa-calendar"
                                        aria-hidden="true"
                                    ></i>

                                    <?= htmlspecialchars(
                                        date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                (string) $event['starts_at']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            <?php endif; ?>

                            <?php if (
                                $showLocation
                                && !empty($event['location'])
                            ): ?>

                                <span class="mv-upcoming-events-location">

                                    <i
                                        class="fa-solid fa-location-dot"
                                        aria-hidden="true"
                                    ></i>

                                    <?= htmlspecialchars(
                                        (string) $event['location'],
                                        ENT_QUOTES,
                                        'UTF-8'
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

        <p class="mv-upcoming-events-empty">
            <?= htmlspecialchars(
                $t(
                    'blocks.content.upcoming_events.empty'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    <?php endif; ?>

</div>
