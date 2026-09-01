<?php
declare(strict_types=1);

$category = is_array($category ?? null)
	? $category
	: [];

$isEdit = !empty($category['uuid']);

$name = trim(
	(string) ($category['name'] ?? '')
);

$slug = trim(
	(string) ($category['slug'] ?? '')
);

$description = trim(
	(string) ($category['description'] ?? '')
);

$sortOrder = max(
	0,
	(int) ($category['sort_order'] ?? 0)
);

$availableLocales = is_array($availableLocales ?? null)
	? $availableLocales
	: [];

$defaultLocale = trim(
	(string) ($defaultLocale ?? 'it')
);

$nameTranslations = is_array($nameTranslations ?? null)
	? $nameTranslations
	: [];

$descriptionTranslations = is_array(
	$descriptionTranslations ?? null
)
	? $descriptionTranslations
	: [];

$formAction = $isEdit
	? '/admin/categories/' . rawurlencode(
		(string) $category['uuid']
	)
	: '/admin/categories';
?>

<div class="admin-page admin-category-form-page">

	<div class="admin-page-header">

		<div>

			<h1>
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.category_form.title.edit')
						: $t('admin.category_form.title.create'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.category_form.description.edit')
						: $t('admin.category_form.description.create'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

		<a
			href="/admin/categories"
			class="mv-admin-button"
		>
			<i
				class="fa fa-arrow-left"
				aria-hidden="true"
			></i>

			<?= htmlspecialchars(
				$t('admin.category_form.back'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	</div>

	<?php if (!empty($error)): ?>

		<div class="alert alert-danger">
			<?= htmlspecialchars(
				(string) $error,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php endif; ?>

	<div class="admin-form-card">

		<form
			method="post"
			action="<?= htmlspecialchars(
				$formAction,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			class="admin-form"
		>

			<div class="form-group">

				<label for="category-name">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.name.label'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
					(<?= htmlspecialchars(
						strtoupper($defaultLocale),
						ENT_QUOTES,
						'UTF-8'
					) ?>)
				</label>

				<input
					id="category-name"
					type="text"
					name="name"
					value="<?= htmlspecialchars(
						$name,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					maxlength="120"
					required
					autocomplete="off"
					placeholder="<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.name.placeholder'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

				<p class="form-help">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.name.help'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

			<?php foreach ($availableLocales as $locale): ?>

				<?php
				$locale = trim((string) $locale);

				if (
					$locale === ''
					|| $locale === $defaultLocale
				) {
					continue;
				}

				$translatedName = trim(
					(string) (
						$nameTranslations[$locale]
						?? ''
					)
				);
				?>

				<div class="form-group">

					<label
						for="category-name-<?= htmlspecialchars(
							$locale,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						<?= htmlspecialchars(
							$t(
								'admin.category_form.fields.name.label'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>
						(<?= htmlspecialchars(
							strtoupper($locale),
							ENT_QUOTES,
							'UTF-8'
						) ?>)
					</label>

					<input
						id="category-name-<?= htmlspecialchars(
							$locale,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						type="text"
						name="translations[<?= htmlspecialchars(
							$locale,
							ENT_QUOTES,
							'UTF-8'
						) ?>][name]"
						value="<?= htmlspecialchars(
							$translatedName,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						maxlength="120"
						autocomplete="off"
						placeholder="<?= htmlspecialchars(
							$t(
								'admin.category_form.fields.name.placeholder'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

				</div>

			<?php endforeach; ?>

			<div class="form-group">

				<label for="category-description">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.description.label'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
					(<?= htmlspecialchars(
						strtoupper($defaultLocale),
						ENT_QUOTES,
						'UTF-8'
					) ?>)
				</label>

				<textarea
					id="category-description"
					name="description"
					rows="4"
					maxlength="1000"
					placeholder="<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.description.placeholder'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				><?= htmlspecialchars(
					$description,
					ENT_QUOTES,
					'UTF-8'
				) ?></textarea>

				<p class="form-help">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.description.help'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

			<?php foreach ($availableLocales as $locale): ?>

				<?php
				$locale = trim((string) $locale);

				if (
					$locale === ''
					|| $locale === $defaultLocale
				) {
					continue;
				}

				$translatedDescription = trim(
					(string) (
						$descriptionTranslations[$locale]
							?? ''
					)
				);
				?>

				<div class="form-group">

					<label
						for="category-description-<?= htmlspecialchars(
							$locale,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						<?= htmlspecialchars(
							$t(
								'admin.category_form.fields.description.label'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>
						(<?= htmlspecialchars(
							strtoupper($locale),
							ENT_QUOTES,
							'UTF-8'
						) ?>)
					</label>

					<textarea
						id="category-description-<?= htmlspecialchars(
							$locale,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						name="translations[<?= htmlspecialchars(
							$locale,
							ENT_QUOTES,
							'UTF-8'
						) ?>][description]"
						rows="4"
						maxlength="1000"
						placeholder="<?= htmlspecialchars(
							$t(
								'admin.category_form.fields.description.placeholder'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					><?= htmlspecialchars(
						$translatedDescription,
						ENT_QUOTES,
						'UTF-8'
					) ?></textarea>

				</div>

			<?php endforeach; ?>

			<div class="form-group">

				<label for="category-slug">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.slug.label'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					id="category-slug"
					type="text"
					name="slug"
					value="<?= htmlspecialchars(
						$slug,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					maxlength="120"
					autocomplete="off"
					placeholder="<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.slug.placeholder'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

				<p class="form-help">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.slug.help'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

			<div class="form-group">

				<label for="category-sort-order">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.sort_order.label'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					id="category-sort-order"
					type="number"
					name="sort_order"
					value="<?= $sortOrder ?>"
					min="0"
					step="1"
				>

				<p class="form-help">
					<?= htmlspecialchars(
						$t(
							'admin.category_form.fields.sort_order.help'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

			<div class="admin-form-actions">

				<a
					href="/admin/categories"
					class="mv-admin-button is-secondary"
				>
					<?= htmlspecialchars(
						$t('admin.category_form.actions.cancel'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

				<button
					type="submit"
					class="mv-admin-button is-primary"
				>
					<i
						class="fa fa-floppy-disk"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$isEdit
							? $t(
								'admin.category_form.actions.save'
							)
							: $t(
								'admin.category_form.actions.create'
							),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

			</div>

		</form>

	</div>

</div>
