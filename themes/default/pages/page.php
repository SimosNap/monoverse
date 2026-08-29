<?php
declare(strict_types=1);

$pageTitle = trim(
    (string) ($page['title'] ?? '')
);

$contentWidgets = trim(
    (string) ($contentWidgets ?? '')
);

$sidebarWidgets = trim(
    (string) ($sidebarWidgets ?? '')
);

$hasSidebar = $sidebarWidgets !== '';
?>

<section class="mv-page">

    <div class="mv-page-layout<?= $hasSidebar
        ? ' has-sidebar'
        : ''
    ?>">

        <main class="mv-page-content">

            <?= $contentWidgets ?>

        </main>

        <?php if ($hasSidebar): ?>

            <aside class="mv-page-sidebar">

                <?= $sidebarWidgets ?>

            </aside>

        <?php endif; ?>

    </div>

</section>
