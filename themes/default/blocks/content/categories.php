<?php
declare(strict_types=1);

$title = trim(
	(string) (
		$title
		?? $t('blocks.content.categories.default_title')
	)
);

$categories = is_array($categories ?? null)
	? $categories
	: [];

$currentCategorySlug = trim(
	(string) ($currentCategorySlug ?? '')
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

<div class="mv-widget mv-categories-widget <?= htmlspecialchars(
	$widthClass,
	ENT_QUOTES,
	'UTF-8'
) ?>">

	<header class="mv-categories-header">

		<h3>
			<?= htmlspecialchars(
				$title,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h3>

	</header>

	<?php if ($categories !== []): ?>

		<ul class="mv-categories-list">

			<?php foreach ($categories as $category): ?>

				<?php
				$name = trim(
					(string) ($category['name'] ?? '')
				);

				$slug = trim(
					(string) ($category['slug'] ?? '')
				);

				$count = (int) (
					$category['articles_count']
					?? $category['count']
					?? 0
				);

				if ($name === '' || $slug === '') {
					continue;
				}

				$isCurrent =
					$currentCategorySlug !== ''
					&& $slug === $currentCategorySlug;
				?>

				<li<?= $isCurrent
					? ' class="is-current"'
					: '' ?>>

					<a href="/chanzine/category/<?= rawurlencode(
						$slug
					) ?>">

						<span>
							<?= htmlspecialchars(
								$name,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

						<?php if ($count > 0): ?>

							<strong>
								<?= $count ?>
							</strong>

						<?php endif; ?>

					</a>

				</li>

			<?php endforeach; ?>

		</ul>

	<?php else: ?>

		<p class="mv-categories-empty">
			<?= htmlspecialchars(
				$t('blocks.content.categories.empty'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	<?php endif; ?>

</div>
