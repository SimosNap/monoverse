<?php
declare(strict_types=1);

/** @var array $presence */
/** @var bool $showLabel */

$presence = is_array($presence ?? null)
    ? $presence
    : [];

$showLabel = (bool) ($showLabel ?? false);

$status = (string) ($presence['status'] ?? 'offline');

$states = [
    'online' => [
        'label' => $t('common.presence.online'),
        'icon' => 'fa-circle',
    ],
    'away' => [
        'label' => $t('common.presence.away'),
        'icon' => 'fa-circle',
    ],
    'offline' => [
        'label' => $t('common.presence.offline'),
        'icon' => 'fa-circle',
    ],
];

$current = $states[$status]
    ?? $states['offline'];
?>

<span
    class="mv-user-presence is-<?= htmlspecialchars(
        $status,
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
    title="<?= htmlspecialchars(
        $current['label'],
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
    aria-label="<?= htmlspecialchars(
        $current['label'],
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <i
        class="fa-solid <?= htmlspecialchars(
            $current['icon'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        aria-hidden="true"
    ></i>

    <?php if ($showLabel): ?>

        <span class="mv-user-presence-label">
            <?= htmlspecialchars(
                $current['label'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </span>

    <?php endif; ?>
</span>
