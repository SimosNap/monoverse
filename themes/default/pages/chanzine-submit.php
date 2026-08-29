<?php
declare(strict_types=1);
?>

<section class="chanzine-submit">

	<header class="chanzine-submit-header">

		<div>

			<span class="chanzine-submit-eyebrow">
				<?= htmlspecialchars(
					$t('chanzine.submit.eyebrow'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

			<h1>
				<?= htmlspecialchars(
					$t('chanzine.submit.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('chanzine.submit.intro'),
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
		action="/chanzine/submit"
		enctype="multipart/form-data"
		class="chanzine-submit-form"
	>

		<div class="chanzine-submit-main">

			<section class="chanzine-submit-card">

				<div class="chanzine-submit-card-header">

					<h2>
						<?= htmlspecialchars(
							$t('chanzine.submit.article.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('chanzine.submit.article.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="mv-field">

					<label for="title">
						<?= htmlspecialchars(
							$t('chanzine.submit.fields.title'),
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
					>

				</div>

				<div class="mv-field">

					<label for="excerpt">
						<?= htmlspecialchars(
							$t('chanzine.submit.fields.excerpt'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<span class="chanzine-submit-field-help">
						<?= htmlspecialchars(
							$t('chanzine.submit.fields.excerpt_help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<textarea
						id="excerpt"
						name="excerpt"
						rows="3"
					></textarea>

				</div>

				<div class="mv-field">

					<label for="content">
						<?= htmlspecialchars(
							$t('chanzine.submit.fields.content'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<span class="chanzine-submit-field-help">
						<?= htmlspecialchars(
							$t('chanzine.submit.fields.content_help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<textarea
						id="content"
						name="content"
						rows="18"
						required
					></textarea>

				</div>

			</section>

		</div>

		<aside class="chanzine-submit-sidebar">

			<section class="chanzine-submit-card">

				<div class="chanzine-submit-card-header">

					<h2>
						<?= htmlspecialchars(
							$t('chanzine.submit.settings.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('chanzine.submit.settings.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="mv-field">

					<label for="category_id">
						<?= htmlspecialchars(
							$t('chanzine.submit.fields.category'),
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
									'chanzine.submit.fields.category_placeholder'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</option>

						<?php foreach ($categories as $category): ?>

							<option
								value="<?= (int) $category['id'] ?>"
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
							$t('chanzine.submit.fields.cover'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<span class="chanzine-submit-field-help">
						<?= htmlspecialchars(
							$t('chanzine.submit.fields.cover_help'),
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
							$t('chanzine.submit.publish.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('chanzine.submit.publish.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="chanzine-submit-actions">

					<a
						href="/chanzine"
						class="button"
					>
						<?= htmlspecialchars(
							$t('chanzine.submit.publish.cancel'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</a>

					<button
						type="submit"
						class="button button-primary"
					>
						<?= htmlspecialchars(
							$t('chanzine.submit.publish.submit'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

				</div>

			</section>

		</aside>

	</form>

</section>
