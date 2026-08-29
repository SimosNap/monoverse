<?php
declare(strict_types=1);

$panelTitle = (string) ($title ?? '');
$panelDescription = (string) ($description ?? '');
$panelContent = (string) ($content ?? '');
$panelClass = trim((string) ($class ?? ''));
?>

<article class="mv-card mv-panel<?= $panelClass !== '' ? ' ' . htmlspecialchars($panelClass, ENT_QUOTES, 'UTF-8') : '' ?>">

	<?php if ($panelTitle !== '' || $panelDescription !== ''): ?>

		<header class="mv-panel-header">

			<?php if ($panelTitle !== ''): ?>
				<h2>
					<?= htmlspecialchars($panelTitle, ENT_QUOTES, 'UTF-8') ?>
				</h2>
			<?php endif; ?>

			<?php if ($panelDescription !== ''): ?>
				<p>
					<?= htmlspecialchars($panelDescription, ENT_QUOTES, 'UTF-8') ?>
				</p>
			<?php endif; ?>

		</header>

	<?php endif; ?>

	<div class="mv-panel-content">
		<?= $panelContent ?>
	</div>

</article>