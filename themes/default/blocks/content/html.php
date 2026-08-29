<?php
declare(strict_types=1);

/** @var string $html */
/** @var array $block */

$block = is_array($block ?? null)
	? $block
	: [];

$title = trim(
	(string) ($block['title'] ?? '')
);

$width = (int) ($block['width'] ?? 12);

if (!in_array($width, [3, 4, 6, 8, 9, 12], true)) {
	$width = 12;
}
?>

<article class="mv-widget mv-widget-html mv-block-width-<?= $width ?>">

	<?php if ($title !== ''): ?>

		<h2 class="mv-widget-title">
			<?= htmlspecialchars(
				$title,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h2>

	<?php endif; ?>

	<div class="mv-widget-content">
		<?= $html ?>
	</div>

</article>