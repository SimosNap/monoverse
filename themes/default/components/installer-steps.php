<?php
declare(strict_types=1);

$steps = [
    [
        'key' => 'requirements',
        'label' => $t('installer.steps.requirements'),
    ],
    [
        'key' => 'edition',
        'label' => $t('installer.steps.edition'),
    ],
    [
        'key' => 'database',
        'label' => $t('installer.steps.database'),
    ],
    [
        'key' => 'oauth',
        'label' => $t('installer.steps.oauth'),
    ],
    [
        'key' => 'admin',
        'label' => $t('installer.steps.admin'),
    ],
    [
        'key' => 'finish',
        'label' => $t('installer.steps.finish'),
    ],
];

$currentStep = (string) ($currentStep ?? '');
?>

<nav
    class="installer-steps"
    aria-label="<?= htmlspecialchars(
        $t('installer.steps.title'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <?php foreach ($steps as $step): ?>

        <div
            class="installer-step<?= $step['key'] === $currentStep
                ? ' is-active'
                : '' ?>"
        >
            <?= htmlspecialchars(
                $step['label'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endforeach; ?>
</nav>
