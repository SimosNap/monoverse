<?php
declare(strict_types=1);

$faq = is_array($faq ?? null)
	? $faq
	: [];

$errors = is_array($errors ?? null)
	? $errors
	: [];

$formAction = trim(
	(string) ($formAction ?? '/admin/faq')
);

$faqId = (int) ($faq['id'] ?? 0);

$isEdit = $faqId > 0;

$question = trim(
	(string) ($faq['question'] ?? '')
);

$anchor = trim(
	(string) ($faq['anchor'] ?? '')
);

$answer = trim(
	(string) ($faq['answer'] ?? '')
);

$section = trim(
	(string) ($faq['section'] ?? '')
);

$status = trim(
	(string) ($faq['status'] ?? 'draft')
);

if (!in_array(
	$status,
	[
		'draft',
		'published',
	],
	true
)) {
	$status = 'draft';
}

$sortOrder = max(
	0,
	(int) ($faq['sort_order'] ?? 0)
);

$availableLocales = is_array(
	$availableLocales ?? null
)
	? $availableLocales
	: [];

$defaultLocale = trim(
	(string) ($defaultLocale ?? 'it')
);

$questionTranslations = is_array(
	$questionTranslations ?? null
)
	? $questionTranslations
	: [];

$answerTranslations = is_array(
	$answerTranslations ?? null
)
	? $answerTranslations
	: [];

$sectionTranslations = is_array(
	$sectionTranslations ?? null
)
	? $sectionTranslations
	: [];
?>

<div class="admin-page admin-page-editor admin-faq-editor">

	<header class="admin-page-header">

		<div>

			<h1>
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.faq_form.title.edit')
						: $t('admin.faq_form.title.create'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('admin.faq_form.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

		<a
			class="mv-admin-button"
			href="/admin/faq"
		>
			<i
				class="fa-solid fa-arrow-left"
				aria-hidden="true"
			></i>

			<?= htmlspecialchars(
				$t('admin.faq_form.back'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	</header>

	<?php if (!empty($errors['general'])): ?>

		<div class="admin-alert admin-alert-error">

			<i
				class="fa-solid fa-circle-exclamation"
				aria-hidden="true"
			></i>

			<span>
				<?= htmlspecialchars(
					(string) $errors['general'],
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

		</div>

	<?php endif; ?>

	<div class="admin-form-card">

		<form
			class="admin-form admin-faq-form"
			action="<?= htmlspecialchars(
				$formAction,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			method="post"
		>

			<section class="admin-faq-form-block">

				<div class="admin-form-section-header">

					<h2>
						<i
							class="fa-solid fa-circle-question"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t('admin.faq_form.fields.question'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

				</div>

				<div class="admin-faq-language-grid">

					<div class="form-group">

						<label for="faq-question">
							<?= htmlspecialchars(
								$t('admin.faq_form.fields.question'),
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
							id="faq-question"
							name="question"
							type="text"
							value="<?= htmlspecialchars(
								$question,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							maxlength="255"
							required
							autofocus
						>

						<?php if (!empty($errors['question'])): ?>

							<p class="form-error">
								<?= htmlspecialchars(
									(string) $errors['question'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

						<?php endif; ?>

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

						$translatedQuestion = trim(
							(string) (
								$questionTranslations[$locale]
								?? ''
							)
						);
						?>

						<div class="form-group">

							<label for="faq-question-<?= htmlspecialchars(
								$locale,
								ENT_QUOTES,
								'UTF-8'
							) ?>">
								<?= htmlspecialchars(
									$t('admin.faq_form.fields.question'),
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
								id="faq-question-<?= htmlspecialchars(
									$locale,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								name="translations[<?= htmlspecialchars(
									$locale,
									ENT_QUOTES,
									'UTF-8'
								) ?>][question]"
								type="text"
								value="<?= htmlspecialchars(
									$translatedQuestion,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								maxlength="255"
							>

						</div>

					<?php endforeach; ?>

				</div>

			</section>

			<section class="admin-faq-form-block">

				<div class="admin-form-section-header">

					<h2>
						<i
							class="fa-solid fa-message"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t('admin.faq_form.fields.answer'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

				</div>

				<div class="admin-faq-language-grid">

					<div class="form-group">

						<label for="faq-answer">
							<?= htmlspecialchars(
								$t('admin.faq_form.fields.answer'),
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
							id="faq-answer"
							name="answer"
							rows="10"
							required
						><?= htmlspecialchars(
							$answer,
							ENT_QUOTES,
							'UTF-8'
						) ?></textarea>

						<?php if (!empty($errors['answer'])): ?>

							<p class="form-error">
								<?= htmlspecialchars(
									(string) $errors['answer'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

						<?php endif; ?>

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

						$translatedAnswer = trim(
							(string) (
								$answerTranslations[$locale]
								?? ''
							)
						);
						?>

						<div class="form-group">

							<label for="faq-answer-<?= htmlspecialchars(
								$locale,
								ENT_QUOTES,
								'UTF-8'
							) ?>">
								<?= htmlspecialchars(
									$t('admin.faq_form.fields.answer'),
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
								id="faq-answer-<?= htmlspecialchars(
									$locale,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								name="translations[<?= htmlspecialchars(
									$locale,
									ENT_QUOTES,
									'UTF-8'
								) ?>][answer]"
								rows="10"
							><?= htmlspecialchars(
								$translatedAnswer,
								ENT_QUOTES,
								'UTF-8'
							) ?></textarea>

						</div>

					<?php endforeach; ?>

				</div>

			</section>

			<section class="admin-faq-form-block">

				<div class="admin-form-section-header">

					<h2>
						<i
							class="fa-solid fa-link"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t('admin.faq_form.permalink.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('admin.faq_form.permalink.description'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="form-group">

					<label for="faq-anchor">
						<?= htmlspecialchars(
							$t('admin.faq_form.fields.anchor.label'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<div class="admin-input-prefix">

						<span aria-hidden="true">
							/faq#
						</span>

						<input
							id="faq-anchor"
							name="anchor"
							type="text"
							value="<?= htmlspecialchars(
								$anchor,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							maxlength="190"
							pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
							placeholder="come-entrare-in-chat"
							required
						>

					</div>

					<p class="form-help">
						<?= htmlspecialchars(
							$t('admin.faq_form.fields.anchor.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

					<?php if (!empty($errors['anchor'])): ?>

						<p class="form-error">
							<?= htmlspecialchars(
								(string) $errors['anchor'],
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					<?php endif; ?>

				</div>

			</section>

			<section class="admin-faq-form-block">

				<div class="admin-form-section-header">

					<h2>
						<i
							class="fa-solid fa-layer-group"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t('admin.faq_form.organization.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('admin.faq_form.organization.description'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="admin-faq-language-grid">

					<div class="form-group">

						<label for="faq-section">
							<?= htmlspecialchars(
								$t('admin.faq_form.fields.section'),
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
							id="faq-section"
							name="section"
							type="text"
							value="<?= htmlspecialchars(
								$section,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							maxlength="150"
						>

						<?php if (!empty($errors['section'])): ?>

							<p class="form-error">
								<?= htmlspecialchars(
									(string) $errors['section'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

						<?php endif; ?>

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

						$translatedSection = trim(
							(string) (
								$sectionTranslations[$locale]
								?? ''
							)
						);
						?>

						<div class="form-group">

							<label for="faq-section-<?= htmlspecialchars(
								$locale,
								ENT_QUOTES,
								'UTF-8'
							) ?>">
								<?= htmlspecialchars(
									$t('admin.faq_form.fields.section'),
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
								id="faq-section-<?= htmlspecialchars(
									$locale,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								name="translations[<?= htmlspecialchars(
									$locale,
									ENT_QUOTES,
									'UTF-8'
								) ?>][section]"
								type="text"
								value="<?= htmlspecialchars(
									$translatedSection,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								maxlength="150"
							>

						</div>

					<?php endforeach; ?>

				</div>

				<div class="admin-faq-options-grid">

					<div class="form-group">

						<label for="faq-status">
							<?= htmlspecialchars(
								$t('admin.faq_form.fields.status.label'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<select
							id="faq-status"
							name="status"
						>

							<option
								value="draft"
								<?= $status === 'draft'
									? 'selected'
									: ''
								?>
							>
								<?= htmlspecialchars(
									$t('admin.faq_form.status.draft'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</option>

							<option
								value="published"
								<?= $status === 'published'
									? 'selected'
									: ''
								?>
							>
								<?= htmlspecialchars(
									$t('admin.faq_form.status.published'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</option>

						</select>

					</div>

					<div class="form-group">

						<label for="faq-sort-order">
							<?= htmlspecialchars(
								$t(
									'admin.faq_form.fields.sort_order.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							id="faq-sort-order"
							name="sort_order"
							type="number"
							value="<?= $sortOrder ?>"
							min="0"
							step="1"
						>

						<p class="form-help">
							<?= htmlspecialchars(
								$t(
									'admin.faq_form.fields.sort_order.help'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					</div>

				</div>

			</section>

			<div class="admin-form-actions">

				<a
					class="mv-admin-button"
					href="/admin/faq"
				>
					<?= htmlspecialchars(
						$t('admin.faq_form.actions.cancel'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

				<button
					class="mv-admin-button"
					type="submit"
				>
					<i
						class="fa-solid fa-floppy-disk"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$isEdit
							? $t('admin.faq_form.actions.save')
							: $t('admin.faq_form.actions.create'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

			</div>

		</form>

	</div>

	<?php if ($isEdit): ?>

		<div class="admin-page-public-actions">

			<a
				class="mv-admin-button"
				href="/faq#<?= rawurlencode($anchor) ?>"
				target="_blank"
				rel="noopener"
			>
				<i
					class="fa-solid fa-arrow-up-right-from-square"
					aria-hidden="true"
				></i>

				<?= htmlspecialchars(
					$t('admin.faq_form.actions.open'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

			<form
				action="/admin/faq/<?= $faqId ?>/delete"
				method="post"
				onsubmit="return confirm(<?= htmlspecialchars(
					json_encode(
						$t('admin.faq_form.confirm.delete'),
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
					class="mv-admin-button is-danger"
					type="submit"
				>
					<i
						class="fa-solid fa-trash"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t('admin.faq_form.actions.delete'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

			</form>

		</div>

	<?php endif; ?>

</div>
