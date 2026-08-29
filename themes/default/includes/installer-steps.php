<?php

$steps = [
    'requirements' => 'Requisiti',
    'edition'      => 'Edition',
    'database'     => 'Database',
    'oauth'        => 'OAuth',
    'admin'        => 'Amministratore',
    'summary'      => 'Riepilogo',
    'install'      => 'Installazione',
];

$currentReached = true;

?>

<nav class="installer-steps">

    <ol>

        <?php foreach ($steps as $key => $label): ?>

            <?php

            if ($key === $installerStep) {
                $state = 'current';
                $currentReached = false;
            } elseif ($currentReached) {
                $state = 'done';
            } else {
                $state = 'pending';
            }

            ?>

            <li class="<?= $state ?>">

                <span class="step-circle"></span>

                <span class="step-label">
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </span>

            </li>

        <?php endforeach; ?>

    </ol>

</nav>