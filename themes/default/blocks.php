<?php
declare(strict_types=1);

/** @var array $pages */

$pages = isset($pages) && is_array($pages)
	? $pages
	: [];
?>

<section class="mv-admin-page mv-block-overview">

	<header class="mv-admin-page-header">

		<p class="mv-admin-page-kicker">
			<?= htmlspecialchars(
				$t('admin.blocks_page.kicker'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

		<h1>
			<?= htmlspecialchars(
				$t('admin.blocks_page.title'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h1>

		<p>
			<?= htmlspecialchars(
				$t('admin.blocks_page.description'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	</header>

	<?php if ($pages === []): ?>

		<div class="mv-admin-inline-notice">
			<?= htmlspecialchars(
				$t('admin.blocks_page.empty'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php else: ?>

		<div class="mv-block-builder-pages">

			<?php foreach ($pages as $page): ?>

				<?php
				$pageKey = (string) ($page['key'] ?? '');

				$pageAreas = is_array($page['areas'] ?? null)
					? $page['areas']
					: [];

				$pageBlockCount = 0;

				foreach ($pageAreas as $pageArea) {
					$pageBlockCount += count(
						is_array($pageArea['blocks'] ?? null)
							? $pageArea['blocks']
							: []
					);
				}

				$pageWidgetCountLabel = $t(
					$pageBlockCount === 1
						? 'admin.blocks_page.widget_count.one'
						: 'admin.blocks_page.widget_count.many',
					[
						'count' => $pageBlockCount,
					]
				);
				?>

				<section class="mv-block-builder-page">

					<header class="mv-block-builder-page-header">

						<div>

							<span>
								<?= htmlspecialchars(
									$t('admin.blocks_page.page'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<h2>
								<?= htmlspecialchars(
									(string) ($page['label'] ?? ''),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</h2>

						</div>

						<div class="mv-block-builder-page-total">

							<strong>
								<?= $pageBlockCount ?>
							</strong>

							<span>
								<?= htmlspecialchars(
									str_replace(
										(string) $pageBlockCount,
										'',
										$pageWidgetCountLabel
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						</div>

					</header>

					<div class="mv-block-builder-areas">

						<?php foreach ($pageAreas as $area): ?>

							<?php
							$areaKey = (string) ($area['key'] ?? '');

							$areaBlocks = is_array(
								$area['blocks'] ?? null
							)
								? $area['blocks']
								: [];

							$enabledCount = 0;

							foreach ($areaBlocks as $block) {
								if (!empty($block['enabled'])) {
									$enabledCount++;
								}
							}

							$totalCount = count($areaBlocks);

							$activeLabel = $t(
								$enabledCount === 1
									? 'admin.blocks_page.area.active.one'
									: 'admin.blocks_page.area.active.many',
								[
									'count' => $enabledCount,
								]
							);

							$configuredLabel = $t(
								$totalCount === 1
									? 'admin.blocks_page.area.configured.one'
									: 'admin.blocks_page.area.configured.many',
								[
									'count' => $totalCount,
								]
							);
							?>

							<article class="mv-block-builder-area">

								<div class="mv-block-builder-area-heading">

									<span class="mv-block-builder-area-icon">

										<i
											class="fa-solid fa-table-cells-large"
											aria-hidden="true"
										></i>

									</span>

									<div>

										<h3>
											<?= htmlspecialchars(
												(string) ($area['label'] ?? ''),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</h3>

										<p>

											<?php if ($totalCount === 0): ?>

												<?= htmlspecialchars(
													$t(
														'admin.blocks_page.area.empty'
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>

											<?php else: ?>

												<?= htmlspecialchars(
													$activeLabel,
													ENT_QUOTES,
													'UTF-8'
												) ?>

												<?= htmlspecialchars(
													$t(
														'admin.blocks_page.area.of'
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>

												<?= htmlspecialchars(
													$configuredLabel,
													ENT_QUOTES,
													'UTF-8'
												) ?>.

											<?php endif; ?>

										</p>

									</div>

								</div>

								<a
									class="mv-block-builder-add"
									href="/admin/blocks/area?<?= http_build_query([
										'page' => $pageKey,
										'area' => $areaKey,
									]) ?>"
								>
									<?= htmlspecialchars(
										$t('admin.blocks_page.manage'),
										ENT_QUOTES,
										'UTF-8'
									) ?>

									<i
										class="fa-solid fa-arrow-right"
										aria-hidden="true"
									></i>
								</a>

							</article>

						<?php endforeach; ?>

					</div>

				</section>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</section>
