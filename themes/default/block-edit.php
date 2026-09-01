<?php
declare(strict_types=1);

/** @var array $block */
/** @var array $blockDefinition */
/** @var string $editor */
/** @var bool $isNew */

$block = is_array($block ?? null)
	? $block
	: [];

$blockDefinition = is_array($blockDefinition ?? null)
	? $blockDefinition
	: [];

$editor = (string) ($editor ?? '');
$isNew = !empty($isNew);

$blockType = trim(
	(string) (
		$block['type']
		?? $blockDefinition['type']
		?? ''
	)
);

$blockTypeKey = str_replace(
	'-',
	'_',
	$blockType
);

$blockLabelKey = 'admin.block_types.'
	. $blockTypeKey
	. '.label';

$translatedBlockLabel = $blockType !== ''
	? $t($blockLabelKey)
	: '';

$blockLabel = (
	$blockType !== ''
	&& $translatedBlockLabel !== $blockLabelKey
)
	? $translatedBlockLabel
	: (string) (
		$blockDefinition['label']
		?? $blockType
		?? $t('admin.block_edit.fallback_label')
	);

$formAction = $isNew
	? '/admin/blocks/store'
	: '/admin/blocks/'
		. (int) ($block['id'] ?? 0)
		. '/update';

$widthOptions = [
	12 => $t('admin.block_edit.width.full'),
	9 => $t('admin.block_edit.width.three_quarters'),
	8 => $t('admin.block_edit.width.two_thirds'),
	6 => $t('admin.block_edit.width.half'),
	4 => $t('admin.block_edit.width.one_third'),
	3 => $t('admin.block_edit.width.one_quarter'),
];
?>

<section class="mv-admin-page mv-widget-editor">

	<header class="mv-admin-page-header">

		<p class="mv-admin-page-kicker">
			<?= htmlspecialchars(
				$isNew
					? $t('admin.block_edit.kicker.new')
					: $t('admin.block_edit.kicker.edit'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

		<h1>
			<?= htmlspecialchars(
				$blockLabel,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h1>

		<p>
			<?= htmlspecialchars(
				$t('admin.block_edit.description'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	</header>

	<form
		method="post"
		action="<?= htmlspecialchars(
			$formAction,
			ENT_QUOTES,
			'UTF-8'
		) ?>"
		class="mv-widget-editor-form"
	>

		<?php if ($isNew): ?>

			<input
				type="hidden"
				name="page"
				value="<?= htmlspecialchars(
					(string) ($block['page'] ?? ''),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

			<input
				type="hidden"
				name="area"
				value="<?= htmlspecialchars(
					(string) ($block['area'] ?? ''),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

			<input
				type="hidden"
				name="type"
				value="<?= htmlspecialchars(
					(string) ($block['type'] ?? ''),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

		<?php endif; ?>

		<div class="mv-widget-editor-main">

			<section class="mv-admin-card">

				<div class="mv-admin-card-heading">

					<div>

						<h2>
							<?= htmlspecialchars(
								$t('admin.block_edit.content.title'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</h2>

						<p>
							<?= htmlspecialchars(
								$t('admin.block_edit.content.description'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					</div>

				</div>

				<?= $editor ?>

			</section>

		</div>

		<aside class="mv-widget-editor-sidebar">

			<section class="mv-admin-card">

				<div class="mv-admin-card-heading">

					<div>

						<h2>
							<?= htmlspecialchars(
								$t('admin.block_edit.settings.title'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</h2>

						<p>
							<?= htmlspecialchars(
								$t('admin.block_edit.settings.description'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					</div>

				</div>

				<div class="mv-admin-field">

					<label for="block-title">
						<?= htmlspecialchars(
							$t('admin.block_edit.public_title.label'),
							ENT_QUOTES,
							'UTF-8'
						) ?>

						<?php if ($defaultLocale !== ''): ?>
							(<?= htmlspecialchars(
								strtoupper($defaultLocale),
								ENT_QUOTES,
								'UTF-8'
							) ?>)
						<?php endif; ?>
					</label>

					<input
						id="block-title"
						type="text"
						name="public_title"
						value="<?= htmlspecialchars(
							(string) ($block['title'] ?? ''),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						placeholder="<?= htmlspecialchars(
							$t('admin.block_edit.public_title.placeholder'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

				</div>

				<?php foreach ($availableLocales as $locale): ?>

					<?php
					$locale = (string) $locale;

					if ($locale === '' || $locale === $defaultLocale) {
						continue;
					}

					$translatedTitle = (string) (
						$titleTranslations[$locale]['title']
						?? ''
					);
					?>

					<div class="mv-admin-field">

						<label
							for="block-title-<?= htmlspecialchars(
								$locale,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>
							<?= htmlspecialchars(
								$t('admin.block_edit.public_title.label'),
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
							id="block-title-<?= htmlspecialchars(
								$locale,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							type="text"
							name="translations[<?= htmlspecialchars(
								$locale,
								ENT_QUOTES,
								'UTF-8'
							) ?>][title]"
							value="<?= htmlspecialchars(
								$translatedTitle,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							placeholder="<?= htmlspecialchars(
								$t('admin.block_edit.public_title.placeholder'),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

					</div>

				<?php endforeach; ?>

				<div class="mv-admin-field">

					<label for="block-width">
						<?= htmlspecialchars(
							$t('admin.block_edit.width.label'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<select
						id="block-width"
						name="width"
					>

						<?php foreach ($widthOptions as $value => $label): ?>

							<option
								value="<?= $value ?>"
								<?= (int) ($block['width'] ?? 12) === $value
									? 'selected'
									: '' ?>
							>
								<?= htmlspecialchars(
									$label,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</option>

						<?php endforeach; ?>

					</select>

				</div>

				<div class="mv-admin-field">

					<label class="mv-admin-switch">

						<input
							type="checkbox"
							name="enabled"
							value="1"
							<?= !empty($block['enabled'])
								? 'checked'
								: '' ?>
						>

						<span
							class="mv-admin-switch-slider"
							aria-hidden="true"
						></span>

						<span class="mv-admin-switch-text">

							<strong>
								<?= htmlspecialchars(
									$t('admin.block_edit.enabled.label'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

							<span>
								<?= htmlspecialchars(
									$t('admin.block_edit.enabled.description'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						</span>

					</label>

				</div>

			</section>

			<div class="mv-widget-editor-actions">

				<a
					href="/admin/blocks/area?<?= http_build_query([
						'page' => (string) ($block['page'] ?? ''),
						'area' => (string) ($block['area'] ?? ''),
					]) ?>"
					class="mv-admin-button is-secondary"
				>
					<?= htmlspecialchars(
						$t('admin.block_edit.actions.cancel'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

				<button
					type="submit"
					class="mv-admin-button is-primary"
				>
					<?= htmlspecialchars(
						$isNew
							? $t('admin.block_edit.actions.create')
							: $t('admin.block_edit.actions.save'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

			</div>

		</aside>

	</form>

</section>