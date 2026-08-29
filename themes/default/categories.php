<?php
declare(strict_types=1);

$categories = is_array($categories ?? null)
	? $categories
	: [];
?>

<div class="admin-page admin-categories-page">

	<div class="admin-page-header">

		<div>
			<h1>
				<?= htmlspecialchars(
					$t('admin.categories.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('admin.categories.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>
		</div>

		<a
			href="/admin/categories/create"
			class="mv-admin-button is-primary"
		>
			<i
				class="fa fa-plus"
				aria-hidden="true"
			></i>

			<?= htmlspecialchars(
				$t('admin.categories.actions.new'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	</div>

	<?php if (!empty($success)): ?>

		<div class="alert alert-success">
			<?= htmlspecialchars(
				(string) $success,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php endif; ?>

	<?php if (!empty($error)): ?>

		<div class="alert alert-danger">
			<?= htmlspecialchars(
				(string) $error,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php endif; ?>

	<?php if ($categories === []): ?>

		<div class="admin-empty-state">

			<div class="admin-empty-state-icon">
				<i
					class="fa fa-folder-open"
					aria-hidden="true"
				></i>
			</div>

			<h2>
				<?= htmlspecialchars(
					$t('admin.categories.empty.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h2>

			<p>
				<?= htmlspecialchars(
					$t('admin.categories.empty.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<a
				href="/admin/categories/create"
				class="mv-admin-button is-primary"
			>
				<?= htmlspecialchars(
					$t('admin.categories.actions.create_first'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

		</div>

	<?php else: ?>

		<div class="admin-categories-list">

			<?php foreach ($categories as $category): ?>

				<?php
				$name = trim(
					(string) ($category['name'] ?? '')
				);

				$slug = trim(
					(string) ($category['slug'] ?? '')
				);

				$uuid = trim(
					(string) ($category['uuid'] ?? '')
				);

				$sortOrder = (int) (
					$category['sort_order'] ?? 0
				);
				?>

				<article class="admin-category-card">

					<div class="admin-category-icon">
						<i
							class="fa fa-folder"
							aria-hidden="true"
						></i>
					</div>

					<div class="admin-category-content">

						<div class="admin-category-heading">

							<div class="admin-category-title-group">

								<h2>
									<?= htmlspecialchars(
										$name,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</h2>

								<div class="admin-category-meta">

									<span>
										<i
											class="fa fa-link"
											aria-hidden="true"
										></i>

										<?= htmlspecialchars(
											$slug,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

									<span>
										<i
											class="fa fa-arrow-down-1-9"
											aria-hidden="true"
										></i>

										<?= htmlspecialchars(
											$t('admin.categories.order'),
											ENT_QUOTES,
											'UTF-8'
										) ?>

										<?= $sortOrder ?>
									</span>

								</div>

							</div>

						</div>

						<div class="admin-category-actions">

							<a
								href="/admin/categories/<?= rawurlencode(
									$uuid
								) ?>/edit"
								class="mv-admin-button is-secondary"
							>
								<i
									class="fa fa-pen"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('admin.categories.actions.edit'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>

							<form
								method="post"
								action="/admin/categories/<?= rawurlencode(
									$uuid
								) ?>/delete"
								onsubmit="return confirm(<?= htmlspecialchars(
									json_encode(
										$t(
											'admin.categories.confirm.delete'
										),
										JSON_HEX_TAG
										| JSON_HEX_AMP
										| JSON_HEX_APOS
										| JSON_HEX_QUOT
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>);"
							>
								<button
									type="submit"
									class="mv-admin-button is-danger"
								>
									<i
										class="fa fa-trash"
										aria-hidden="true"
									></i>

									<?= htmlspecialchars(
										$t('admin.categories.actions.delete'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</button>
							</form>

						</div>

					</div>

				</article>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</div>
