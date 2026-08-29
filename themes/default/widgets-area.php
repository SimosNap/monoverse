<?php
declare(strict_types=1);

/** @var string $page */
/** @var string $pageLabel */
/** @var string $area */
/** @var string $areaLabel */
/** @var array $configuredBlocks */

$configuredBlocks = isset($configuredBlocks)
	&& is_array($configuredBlocks)
	? $configuredBlocks
	: [];
?>

<section class="mv-admin-page mv-widget-area-page">

	<header class="mv-admin-page-header">

		<p class="mv-admin-page-kicker">
			<?= htmlspecialchars(
				$pageLabel,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

		<h1>
			<?= htmlspecialchars(
				$areaLabel,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h1>

		<p>
			<?= htmlspecialchars(
				$t('admin.widgets_area.description'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

		<div class="mv-widget-area-header-actions">

			<a
				class="mv-admin-button is-secondary"
				href="/admin/blocks"
			>
				<i
					class="fa-solid fa-arrow-left"
					aria-hidden="true"
				></i>

				<?= htmlspecialchars(
					$t('admin.widgets_area.all_areas'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

			<a
				class="mv-admin-button is-primary"
				href="/admin/blocks/library?<?= http_build_query([
					'page' => $page,
					'area' => $area,
				]) ?>"
			>
				<i
					class="fa-solid fa-plus"
					aria-hidden="true"
				></i>

				<?= htmlspecialchars(
					$t('admin.widgets_area.add_widget'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

		</div>

	</header>

	<?php if ($configuredBlocks === []): ?>

		<div class="mv-widget-area-empty">

			<i
				class="fa-regular fa-square-plus"
				aria-hidden="true"
			></i>

			<h2>
				<?= htmlspecialchars(
					$t('admin.widgets_area.empty.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h2>

			<p>
				<?= htmlspecialchars(
					$t('admin.widgets_area.empty.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<a
				class="mv-admin-button is-primary"
				href="/admin/blocks/library?<?= http_build_query([
					'page' => $page,
					'area' => $area,
				]) ?>"
			>
				<?= htmlspecialchars(
					$t('admin.widgets_area.add_widget'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

		</div>

	<?php else: ?>

		<div
			class="mv-widget-area-list"
			data-widget-sortable
			data-page="<?= htmlspecialchars(
				$page,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			data-area="<?= htmlspecialchars(
				$area,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>

			<?php foreach ($configuredBlocks as $block): ?>

				<?php
				$blockId = (int) ($block['id'] ?? 0);
				$isEnabled = !empty($block['enabled']);
				?>

				<article
					class="mv-widget-area-item <?= $isEnabled
						? ''
						: 'is-disabled' ?>"
					data-widget-id="<?= $blockId ?>"
					draggable="true"
				>

					<div
						class="mv-widget-area-drag"
						title="<?= htmlspecialchars(
							$t('admin.widgets_area.drag'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						aria-label="<?= htmlspecialchars(
							$t('admin.widgets_area.drag'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						<i
							class="fa-solid fa-grip-vertical"
							aria-hidden="true"
						></i>
					</div>

					<div class="mv-widget-area-icon">

						<i
							class="fa-solid <?= htmlspecialchars(
								(string) ($block['icon'] ?? 'fa-cube'),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							aria-hidden="true"
						></i>

					</div>

					<div class="mv-widget-area-content">

						<div class="mv-widget-area-title">

							<h2>
								<?= htmlspecialchars(
									(string) ($block['label'] ?? ''),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</h2>

							<span class="mv-admin-badge <?= $isEnabled
								? 'is-active'
								: 'is-disabled' ?>"
							>
								<?= htmlspecialchars(
									$isEnabled
										? $t(
											'admin.widgets_area.status.active'
										)
										: $t(
											'admin.widgets_area.status.disabled'
										),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						</div>

						<p>
							<?= htmlspecialchars(
								(string) ($block['type_label'] ?? ''),
								ENT_QUOTES,
								'UTF-8'
							) ?>

							<span aria-hidden="true">·</span>

							<?= htmlspecialchars(
								$t('admin.widgets_area.width'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
							<?= (int) ($block['width'] ?? 12) ?>/12
						</p>

					</div>

					<div class="mv-widget-area-actions">

						<a
							class="mv-widget-action"
							href="/admin/blocks/<?= $blockId ?>/edit"
						>
							<i
								class="fa-solid fa-pen"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$t(
										'admin.widgets_area.actions.edit'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>
						</a>

						<form
							method="post"
							action="/admin/blocks/<?= $blockId ?>/toggle"
						>
							<button
								type="submit"
								class="mv-widget-action"
							>
								<i
									class="fa-solid <?= $isEnabled
										? 'fa-eye-slash'
										: 'fa-eye' ?>"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$isEnabled
											? $t(
												'admin.widgets_area.actions.disable'
											)
											: $t(
												'admin.widgets_area.actions.enable'
											),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>
							</button>
						</form>

						<form
							method="post"
							action="/admin/blocks/<?= $blockId ?>/delete"
							onsubmit="return confirm(<?= htmlspecialchars(
								json_encode(
									$t(
										'admin.widgets_area.delete_confirm'
									),
									JSON_UNESCAPED_UNICODE
									| JSON_UNESCAPED_SLASHES
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>);"
						>
							<button
								type="submit"
								class="mv-widget-action is-danger"
							>
								<i
									class="fa-regular fa-trash-can"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$t(
											'admin.widgets_area.actions.delete'
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>
							</button>
						</form>

					</div>

				</article>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</section>
