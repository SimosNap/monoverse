<?php
declare(strict_types=1);

/** @var array $article */
/** @var array $categories */

$article = isset($article) && is_array($article)
	? $article
	: [];

$categories = isset($categories) && is_array($categories)
	? $categories
	: [];

$articleTitle = (string) ($article['title'] ?? '');
$excerpt = (string) ($article['excerpt'] ?? '');
$content = (string) ($article['content'] ?? '');
$categoryId = (int) ($article['category_id'] ?? 0);
$cover = trim((string) ($article['cover'] ?? ''));
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<section class="chanzine-submit">

	<header class="chanzine-submit-header">

		<div>

			<span class="chanzine-submit-eyebrow">
				<?= htmlspecialchars(
					$t('account.article_edit.eyebrow'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

			<h1>
				<?= htmlspecialchars(
					$t('account.article_edit.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('account.article_edit.intro'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	</header>

	<?php if (!empty($error)): ?>

		<div class="mv-alert mv-alert-error">
			<?= htmlspecialchars(
				(string) $error,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php endif; ?>

	<form
		method="post"
		action="/account/articles/<?= rawurlencode(
			(string) ($article['uuid'] ?? '')
		) ?>"
		enctype="multipart/form-data"
		class="chanzine-submit-form"
	>

		<div class="chanzine-submit-main">

			<section class="chanzine-submit-card">

				<div class="chanzine-submit-card-header">

					<h2>
						<?= htmlspecialchars(
							$t('account.article_edit.article.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('account.article_edit.article.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="mv-field">

					<label for="title">
						<?= htmlspecialchars(
							$t('account.article_edit.fields.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<input
						type="text"
						id="title"
						name="title"
						maxlength="255"
						value="<?= htmlspecialchars(
							$articleTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						required
					>

				</div>

				<div class="mv-field">

					<label for="excerpt">
						<?= htmlspecialchars(
							$t('account.article_edit.fields.excerpt'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<span class="chanzine-submit-field-help">
						<?= htmlspecialchars(
							$t('account.article_edit.fields.excerpt_help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<textarea
						id="excerpt"
						name="excerpt"
						rows="3"
					><?= htmlspecialchars(
						$excerpt,
						ENT_QUOTES,
						'UTF-8'
					) ?></textarea>

				</div>

				<div class="mv-field">

					<label for="content">
						<?= htmlspecialchars(
							$t('account.article_edit.fields.content'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<span class="chanzine-submit-field-help">
						<?= htmlspecialchars(
							$t('account.article_edit.fields.content_help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<textarea
						id="content"
						name="content"
						rows="18"
						required
					><?= htmlspecialchars(
						$content,
						ENT_QUOTES,
						'UTF-8'
					) ?></textarea>

				</div>

			</section>

		</div>

		<aside class="chanzine-submit-sidebar">

			<section class="chanzine-submit-card">

				<div class="chanzine-submit-card-header">

					<h2>
						<?= htmlspecialchars(
							$t('account.article_edit.settings.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('account.article_edit.settings.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="mv-field">

					<label for="category_id">
						<?= htmlspecialchars(
							$t('account.article_edit.fields.category'),
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
									'account.article_edit.fields.category_placeholder'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</option>

						<?php foreach ($categories as $category): ?>

							<option
								value="<?= (int) $category['id'] ?>"
								<?= ((int) $category['id'] === $categoryId)
									? 'selected'
									: '' ?>
							>
								<?= htmlspecialchars(
									(string) $category['name'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</option>

						<?php endforeach; ?>

					</select>

				</div>

				<div class="mv-field">

					<label for="cover">
						<?= htmlspecialchars(
							$t('account.article_edit.fields.cover'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<?php if ($cover !== ''): ?>

						<div class="chanzine-submit-current-cover">

							<img
								src="<?= htmlspecialchars(
									$cover,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								alt=""
							>

						</div>

					<?php endif; ?>

					<span class="chanzine-submit-field-help">
						<?= htmlspecialchars(
							$cover !== ''
								? $t(
									'account.article_edit.fields.cover_replace_help'
								)
								: $t(
									'account.article_edit.fields.cover_default_help'
								),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<input
						type="file"
						id="cover"
						name="cover"
						accept="image/jpeg,image/png,image/webp"
					>

				</div>

			</section>

			<section class="chanzine-submit-card chanzine-submit-publish">

				<div class="chanzine-submit-card-header">

					<h2>
						<?= htmlspecialchars(
							$t('account.article_edit.save.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('account.article_edit.save.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="chanzine-submit-actions">

					<a
						href="/account/articles"
						class="button"
					>
						<?= htmlspecialchars(
							$t('account.article_edit.save.cancel'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</a>

					<button
						type="submit"
						class="button button-primary"
					>
						<?= htmlspecialchars(
							$t('account.article_edit.save.submit'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

				</div>

			</section>

		</aside>

	</form>

</section>
