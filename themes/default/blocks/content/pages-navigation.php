<?php
declare(strict_types=1);

$title = trim(
	(string) (
		$title
		?? $t('blocks.content.pages_navigation.default_title')
	)
);

$pages = is_array($pages ?? null)
	? $pages
	: [];

$currentPageSlug = trim(
	(string) ($currentPageSlug ?? '')
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

<div class="mv-widget mv-pages-navigation-widget <?= htmlspecialchars(
	$widthClass,
	ENT_QUOTES,
	'UTF-8'
) ?>">

	<header class="mv-pages-navigation-header">

		<h3>
			<?= htmlspecialchars(
				$title,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h3>

	</header>

	<?php if ($pages !== []): ?>

		<ul class="mv-pages-navigation-list">

			<?php foreach ($pages as $page): ?>

				<?php
				$pageTitle = trim(
					(string) ($page['title'] ?? '')
				);

				$pageSlug = trim(
					(string) ($page['slug'] ?? '')
				);

				$menuLabel = trim(
					(string) ($page['menu_label'] ?? '')
				);

				$pageLabel = $menuLabel !== ''
					? $menuLabel
					: $pageTitle;

				$isCurrent = (
					$currentPageSlug !== ''
					&& $currentPageSlug === $pageSlug
				);
				?>

				<?php if (
					$pageLabel !== ''
					&& $pageSlug !== ''
				): ?>

					<li>

						<a
							class="mv-pages-navigation-item<?= $isCurrent ? ' is-active' : '' ?>"
							href="/<?= rawurlencode(
								$pageSlug
							) ?>"
						>

							<span class="mv-pages-navigation-body">

								<i
									class="fa-regular fa-file-lines"
									aria-hidden="true"
								></i>

								<strong class="mv-pages-navigation-name">
									<?= htmlspecialchars(
										$pageLabel,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							</span>

							<i
								class="fa-solid fa-chevron-right"
								aria-hidden="true"
							></i>

						</a>

					</li>

				<?php endif; ?>

			<?php endforeach; ?>

		</ul>

	<?php else: ?>

		<p class="mv-pages-navigation-empty">
			<?= htmlspecialchars(
				$t(
					'blocks.content.pages_navigation.empty'
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	<?php endif; ?>

</div>
