<?php
declare(strict_types=1);

$title = trim(
    (string) (
        $title
        ?? $t(
            'blocks.webradio.azuracast_stats.default_title'
        )
    )
);

$nowPlayingData = is_array($now_playing ?? null)
    ? $now_playing
    : [];

$showListeners = (bool) (
    $show_listeners
    ?? true
);

$showUniqueListeners = (bool) (
    $show_unique_listeners
    ?? true
);

$showBitrate = (bool) (
    $show_bitrate
    ?? true
);

$showCodec = (bool) (
    $show_codec
    ?? true
);

$showMounts = (bool) (
    $show_mounts
    ?? true
);

$station = is_array(
    $nowPlayingData['station'] ?? null
)
    ? $nowPlayingData['station']
    : [];

$listeners = is_array(
    $nowPlayingData['listeners'] ?? null
)
    ? $nowPlayingData['listeners']
    : [];

$mounts = is_array(
    $station['mounts'] ?? null
)
    ? $station['mounts']
    : [];

$isOnline = (bool) (
    $nowPlayingData['is_online']
    ?? false
);

$currentListeners = max(
    0,
    (int) ($listeners['current'] ?? 0)
);

$uniqueListeners = max(
    0,
    (int) ($listeners['unique'] ?? 0)
);

$stationName = trim(
    (string) ($station['name'] ?? '')
);

$activeMounts = [];

foreach ($mounts as $mount) {
    if (!is_array($mount)) {
        continue;
    }

    $mountUrl = trim(
        (string) (
            $mount['url']
            ?? $mount['path']
            ?? ''
        )
    );

    $mountName = trim(
        (string) (
            $mount['name']
            ?? $mountUrl
        )
    );

    $bitrate = max(
        0,
        (int) ($mount['bitrate'] ?? 0)
    );

    $format = trim(
        (string) (
            $mount['format']
            ?? $mount['codec']
            ?? ''
        )
    );

    $mountListeners = is_array(
        $mount['listeners'] ?? null
    )
        ? $mount['listeners']
        : [];

    $mountCurrentListeners = max(
        0,
        (int) (
            $mountListeners['current']
            ?? 0
        )
    );

    if (
        $mountName === ''
        && $mountUrl === ''
    ) {
        continue;
    }

    $activeMounts[] = [
        'name' => $mountName !== ''
            ? $mountName
            : $t(
                'blocks.webradio.azuracast_stats.stream_fallback'
            ),
        'url' => $mountUrl,
        'bitrate' => $bitrate,
        'format' => $format,
        'listeners' => $mountCurrentListeners,
    ];
}

$primaryMount = $activeMounts[0] ?? [];

$primaryBitrate = max(
    0,
    (int) ($primaryMount['bitrate'] ?? 0)
);

$primaryCodec = trim(
    (string) ($primaryMount['format'] ?? '')
);

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

<div class="mv-widget mv-azuracast-stats-widget <?= htmlspecialchars(
    $widthClass,
    ENT_QUOTES,
    'UTF-8'
) ?>">

    <header class="mv-azuracast-stats-header">

        <div>

            <span class="mv-azuracast-stats-kicker">
                <i
                    class="fa-solid fa-chart-simple"
                    aria-hidden="true"
                ></i>

                <?= htmlspecialchars(
                    $t(
                        'blocks.webradio.azuracast_stats.kicker'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

            <h3>
                <?= htmlspecialchars(
                    $title,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h3>

        </div>

        <span class="mv-azuracast-stats-status <?= $isOnline
            ? 'is-online'
            : 'is-offline'
        ?>">

            <i
                class="fa-solid fa-circle"
                aria-hidden="true"
            ></i>

            <?= htmlspecialchars(
                $t(
                    $isOnline
                        ? 'blocks.webradio.azuracast_stats.status.online'
                        : 'blocks.webradio.azuracast_stats.status.offline'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </span>

    </header>

    <?php if ($nowPlayingData === []): ?>

        <div class="mv-azuracast-stats-unavailable">

            <i
                class="fa-solid fa-triangle-exclamation"
                aria-hidden="true"
            ></i>

            <div>

                <strong>
                    <?= htmlspecialchars(
                        $t(
                            'blocks.webradio.azuracast_stats.unavailable.title'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <p>
                    <?= htmlspecialchars(
                        $t(
                            'blocks.webradio.azuracast_stats.unavailable.text'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        </div>

    <?php else: ?>

        <?php if ($stationName !== ''): ?>

            <p class="mv-azuracast-stats-station">
                <?= htmlspecialchars(
                    $stationName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

        <?php endif; ?>

        <div class="mv-azuracast-stats-grid">

            <?php if ($showListeners): ?>

                <div class="mv-azuracast-stat">

                    <span class="mv-azuracast-stat-icon">
                        <i
                            class="fa-solid fa-headphones"
                            aria-hidden="true"
                        ></i>
                    </span>

                    <div>

                        <strong>
                            <?= $currentListeners ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $t(
                                    'blocks.webradio.azuracast_stats.stats.current_listeners'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>

            <?php if ($showUniqueListeners): ?>

                <div class="mv-azuracast-stat">

                    <span class="mv-azuracast-stat-icon">
                        <i
                            class="fa-solid fa-users"
                            aria-hidden="true"
                        ></i>
                    </span>

                    <div>

                        <strong>
                            <?= $uniqueListeners ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $t(
                                    'blocks.webradio.azuracast_stats.stats.unique_listeners'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>

            <?php if ($showBitrate): ?>

                <div class="mv-azuracast-stat">

                    <span class="mv-azuracast-stat-icon">
                        <i
                            class="fa-solid fa-gauge-high"
                            aria-hidden="true"
                        ></i>
                    </span>

                    <div>

                        <strong>
                            <?= $primaryBitrate > 0
                                ? $primaryBitrate . ' kbps'
                                : '—'
                            ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $t(
                                    'blocks.webradio.azuracast_stats.stats.bitrate'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>

            <?php if ($showCodec): ?>

                <div class="mv-azuracast-stat">

                    <span class="mv-azuracast-stat-icon">
                        <i
                            class="fa-solid fa-wave-square"
                            aria-hidden="true"
                        ></i>
                    </span>

                    <div>

                        <strong>
                            <?= htmlspecialchars(
                                $primaryCodec !== ''
                                    ? strtoupper($primaryCodec)
                                    : '—',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $t(
                                    'blocks.webradio.azuracast_stats.stats.codec'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>

        </div>

        <?php if ($showMounts): ?>

            <section class="mv-azuracast-mounts">

                <header>

                    <h4>
                        <?= htmlspecialchars(
                            $t(
                                'blocks.webradio.azuracast_stats.mounts.title'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h4>

                    <span>
                        <?= count($activeMounts) ?>
                    </span>

                </header>

                <?php if ($activeMounts === []): ?>

                    <p class="mv-azuracast-mounts-empty">
                        <?= htmlspecialchars(
                            $t(
                                'blocks.webradio.azuracast_stats.mounts.empty'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php else: ?>

                    <ul>

                        <?php foreach ($activeMounts as $mount): ?>

                            <li>

                                <span class="mv-azuracast-mount-icon">
                                    <i
                                        class="fa-solid fa-tower-broadcast"
                                        aria-hidden="true"
                                    ></i>
                                </span>

                                <div class="mv-azuracast-mount-content">

                                    <strong>
                                        <?= htmlspecialchars(
                                            (string) $mount['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>

                                    <span>

                                        <?php if (
                                            (string) $mount['format'] !== ''
                                        ): ?>

                                            <?= htmlspecialchars(
                                                strtoupper(
                                                    (string) $mount['format']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        <?php endif; ?>

                                        <?php if (
                                            (int) $mount['bitrate'] > 0
                                        ): ?>

                                            <?php if (
                                                (string) $mount['format'] !== ''
                                            ): ?>
                                                ·
                                            <?php endif; ?>

                                            <?= (int) $mount['bitrate'] ?>
                                            kbps

                                        <?php endif; ?>

                                        <?php if (
                                            (int) $mount['listeners'] > 0
                                        ): ?>

                                            ·

                                            <?= htmlspecialchars(
                                                $t(
                                                    (int) $mount['listeners'] === 1
                                                        ? 'blocks.webradio.azuracast_stats.mounts.listeners.one'
                                                        : 'blocks.webradio.azuracast_stats.mounts.listeners.many',
                                                    [
                                                        'count' => (int) $mount['listeners'],
                                                    ]
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        <?php endif; ?>

                                    </span>

                                </div>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>

            </section>

        <?php endif; ?>

    <?php endif; ?>

</div>
