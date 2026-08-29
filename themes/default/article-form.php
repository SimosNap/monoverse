<?php
$isEdit = $article !== null;

$articleTitle = (string)($article['title'] ?? '');
$slug = (string)($article['slug'] ?? '');
$excerpt = (string)($article['excerpt'] ?? '');
$content = (string)($article['content'] ?? '');
$cover = (string)($article['cover'] ?? '');
$categories = is_array($categories ?? null)
	? $categories
	: [];

$categoryId = (int) ($article['category_id'] ?? 0);

$coverUrl = $cover !== ''
	? '/' . ltrim($cover, '/')
	: '';
?>

<div class="admin-page admin-article-editor">

	<header class="admin-page-header">
		<div>
			<h1>
				<?= htmlspecialchars(
					(string) (
						$title
						?? (
							$isEdit
								? $t('admin.article_form.title.edit')
								: $t('admin.article_form.title.create')
						)
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.article_form.description.edit')
						: $t('admin.article_form.description.create'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>
		</div>

		<a href="/admin/articles" class="mv-admin-button admin-editor-back">
			<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>

			<span>
				<?= htmlspecialchars(
					$t('admin.article_form.back'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>
		</a>
	</header>

	<?php if (!empty($error)): ?>
		<div class="alert alert-danger">
			<?= htmlspecialchars(
				(string) $error,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>
	<?php endif; ?>

	<?php if (
		$isEdit
		&& (($article['status'] ?? '') === 'submitted')
	): ?>

		<div class="alert alert-warning">

			<strong>
				<?= htmlspecialchars(
					$t('admin.article_form.submission.from'),
					ENT_QUOTES,
					'UTF-8'
				) ?>

				<?= htmlspecialchars(
					(string) (
						$article['submitted_by_nickname']
						?? $t(
							'admin.article_form.submission.default_user'
						)
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</strong>

			<?php if (!empty($article['submitted_at'])): ?>

				<span>
					<?= htmlspecialchars(
						$t('admin.article_form.submission.on'),
						ENT_QUOTES,
						'UTF-8'
					) ?>

					<?= htmlspecialchars(
						date(
							'd/m/Y H:i',
							strtotime(
								(string) $article['submitted_at']
							)
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			<?php endif; ?>

			<br>

			<span>
				<?= htmlspecialchars(
					$t('admin.article_form.submission.pending'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

		</div>

	<?php endif; ?>

	<form
		class="admin-article-form"
		method="post"
		action="<?= $isEdit
			? '/admin/articles/' . htmlspecialchars(
				(string) $article['uuid'],
				ENT_QUOTES,
				'UTF-8'
			)
			: '/admin/articles' ?>"
		enctype="multipart/form-data"
	>
		<div class="admin-article-form-layout">

			<main class="admin-article-form-main">

				<section class="admin-editor-panel">
					<div class="form-group admin-title-field">

						<label for="title">
							<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.title.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="text"
							id="title"
							name="title"
							maxlength="255"
							required
							placeholder="<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.title.placeholder'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							value="<?= htmlspecialchars(
								$articleTitle,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>
					</div>

					<div class="form-group">

						<label for="excerpt">
							<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.excerpt.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<p class="form-help">
							<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.excerpt.help'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

						<textarea
							id="excerpt"
							name="excerpt"
							rows="4"
							placeholder="<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.excerpt.placeholder'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						><?= htmlspecialchars(
							$excerpt,
							ENT_QUOTES,
							'UTF-8'
						) ?></textarea>
					</div>
				</section>

				<section class="admin-editor-panel admin-content-panel">
					<div class="admin-editor-section-heading">
						<div>

							<label for="content">
								<?= htmlspecialchars(
									$t(
										'admin.article_form.fields.content.label'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</label>

							<p class="form-help">
								<?= htmlspecialchars(
									$t(
										'admin.article_form.fields.content.help'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>
						</div>

						<span class="admin-markdown-label">
							<i
								class="fa-brands fa-markdown"
								aria-hidden="true"
							></i>
							Markdown
						</span>
					</div>

					<textarea
						id="content"
						name="content"
						rows="24"
						placeholder="<?= htmlspecialchars(
							$t(
								'admin.article_form.fields.content.placeholder'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					><?= htmlspecialchars(
						$content,
						ENT_QUOTES,
						'UTF-8'
					) ?></textarea>
				</section>

			</main>

			<aside class="admin-article-form-sidebar">

				<section class="admin-editor-panel">

					<h2>
						<?= htmlspecialchars(
							$t('admin.article_form.publication.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<div class="admin-form-actions">
						<button
							type="submit"
							class="mv-admin-button is-primary"
						>
							<i
								class="fa-solid fa-floppy-disk"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$isEdit
										? $t(
											'admin.article_form.publication.save_changes'
										)
										: $t(
											'admin.article_form.publication.save_draft'
										),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>
						</button>

						<?php if (
							$isEdit
							&& (($article['status'] ?? '') === 'submitted')
						): ?>

							<button
								type="submit"
								name="publish_after_update"
								value="1"
								class="mv-admin-button is-success"
								onclick="return confirm(<?= htmlspecialchars(
									json_encode(
										$t(
											'admin.article_form.publication.confirm_publish'
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
								<i
									class="fa-solid fa-check"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t(
										'admin.article_form.publication.save_publish'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</button>

						<?php endif; ?>

						<a
							href="/admin/articles"
							class="mv-admin-button is-secondary"
						>
							<?= htmlspecialchars(
								$t(
									'admin.article_form.publication.cancel'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</a>
					</div>
				</section>

				<section class="admin-editor-panel">

					<h2>
						<?= htmlspecialchars(
							$t('admin.article_form.address.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<div class="form-group">

						<label for="slug">
							<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.slug.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="text"
							id="slug"
							name="slug"
							maxlength="255"
							required
							placeholder="<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.slug.placeholder'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							value="<?= htmlspecialchars(
								$slug,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

						<p class="form-help">
							<?= htmlspecialchars(
								$t(
									'admin.article_form.fields.slug.help'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>
					</div>
				</section>

				<section class="admin-editor-panel">

					<h2>
						<?= htmlspecialchars(
							$t('admin.article_form.category.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<div class="form-group">

						<label for="category_id">
							<?= htmlspecialchars(
								$t('admin.article_form.category.label'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<select
							id="category_id"
							name="category_id"
							required
						>

							<option value="">
								<?= htmlspecialchars(
									$t(
										'admin.article_form.category.select'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</option>

							<?php foreach ($categories as $category): ?>

								<?php
								$currentCategoryId = (int) (
									$category['id'] ?? 0
								);

								$currentCategoryName = trim(
									(string) ($category['name'] ?? '')
								);

								if (
									$currentCategoryId <= 0
									|| $currentCategoryName === ''
								) {
									continue;
								}
								?>

								<option
									value="<?= $currentCategoryId ?>"
									<?= $currentCategoryId === $categoryId
										? 'selected'
										: '' ?>
								>
									<?= htmlspecialchars(
										$currentCategoryName,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</option>

							<?php endforeach; ?>

						</select>

						<p class="form-help">
							<?= htmlspecialchars(
								$t('admin.article_form.category.help'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					</div>

				</section>

				<section class="admin-editor-panel">

					<h2>
						<?= htmlspecialchars(
							$t('admin.article_form.cover.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<div class="admin-cover-preview<?= $coverUrl === ''
						? ' is-empty'
						: '' ?>">

						<?php if ($coverUrl !== ''): ?>

							<img
								src="<?= htmlspecialchars(
									$coverUrl,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								alt="<?= htmlspecialchars(
									$t(
										'admin.article_form.cover.current_alt'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>"
							>

						<?php else: ?>

							<div class="admin-cover-placeholder">
								<i
									class="fa-regular fa-image"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$t(
											'admin.article_form.cover.empty'
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>
							</div>

						<?php endif; ?>

					</div>

					<div class="form-group admin-cover-upload">

						<label for="cover">
							<?= htmlspecialchars(
								$coverUrl !== ''
									? $t(
										'admin.article_form.cover.replace'
									)
									: $t(
										'admin.article_form.cover.upload'
									),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="file"
							id="cover"
							name="cover"
							accept="image/jpeg,image/png,image/webp"
						>

						<p class="form-help">
							<?= htmlspecialchars(
								$t('admin.article_form.cover.formats'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>
					</div>
				</section>

			</aside>

		</div>

	</form>

</div>
