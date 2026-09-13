<?php
$isEdit = $event !== null;

$eventTitle = (string)($event['title'] ?? '');
$slug = (string)($event['slug'] ?? '');
$description = (string)($event['description'] ?? '');
$startsAt = (string)($event['starts_at'] ?? '');
$endsAt = (string)($event['ends_at'] ?? '');
$location = (string)($event['location'] ?? '');
$latitude = (string)($event['latitude'] ?? '');
$longitude = (string)($event['longitude'] ?? '');
$externalUrl = (string)($event['external_url'] ?? '');
$cover = (string)($event['cover'] ?? '');

$startsAtInput = $startsAt !== ''
	? date('Y-m-d\TH:i', strtotime($startsAt))
	: '';

$endsAtInput = $endsAt !== ''
	? date('Y-m-d\TH:i', strtotime($endsAt))
	: '';

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
								? $t('admin.event_form.title.edit')
								: $t('admin.event_form.title.create')
						)
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.event_form.description.edit')
						: $t('admin.event_form.description.create'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>
		</div>

		<a href="/admin/events" class="mv-admin-button admin-editor-back">
			<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>

			<span>
				<?= htmlspecialchars(
					$t('admin.event_form.back'),
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
		&& (($event['status'] ?? '') === 'submitted')
	): ?>

		<div class="alert alert-warning">

			<strong>
				<?= htmlspecialchars(
					$t('admin.event_form.submission.from'),
					ENT_QUOTES,
					'UTF-8'
				) ?>

				<?= htmlspecialchars(
					(string) (
						$event['submitted_by_nickname']
							?? $t(
								'admin.event_form.submission.default_user'
							)
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</strong>

			<?php if (!empty($event['submitted_at'])): ?>

				<span>
					<?= htmlspecialchars(
						$t('admin.event_form.submission.on'),
						ENT_QUOTES,
						'UTF-8'
					) ?>

					<?= htmlspecialchars(
						date(
							'd/m/Y H:i',
							strtotime(
								(string) $event['submitted_at']
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
					$t('admin.event_form.submission.pending'),
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
			? '/admin/events/' . htmlspecialchars(
				(string) $event['uuid'],
				ENT_QUOTES,
				'UTF-8'
			)
			: '/admin/events' ?>"
		enctype="multipart/form-data"
	>
		<div class="admin-article-form-layout">

			<main class="admin-article-form-main">

				<section class="admin-editor-panel">

					<div class="form-group admin-title-field">

						<label for="title">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.title.label'
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
									'admin.event_form.fields.title.placeholder'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							value="<?= htmlspecialchars(
								$eventTitle,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

					</div>

				</section>

				<section class="admin-editor-panel">

					<div class="admin-editor-section-heading">
						<div>

							<label for="description">
								<?= htmlspecialchars(
									$t(
										'admin.event_form.fields.description.label'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</label>

							<p class="form-help">
								<?= htmlspecialchars(
									$t(
										'admin.event_form.fields.description.help'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

						</div>
					</div>

					<textarea
						id="description"
						name="description"
						rows="18"
						required
						placeholder="<?= htmlspecialchars(
							$t(
								'admin.event_form.fields.description.placeholder'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					><?= htmlspecialchars(
						$description,
						ENT_QUOTES,
						'UTF-8'
					) ?></textarea>

				</section>

				<section class="admin-editor-panel">

					<h2>
						<?= htmlspecialchars(
							$t('admin.event_form.schedule.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<div class="form-group">

						<label for="starts_at">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.starts_at.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="datetime-local"
							id="starts_at"
							name="starts_at"
							required
							value="<?= htmlspecialchars(
								$startsAtInput,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

					</div>

					<div class="form-group">

						<label for="ends_at">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.ends_at.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="datetime-local"
							id="ends_at"
							name="ends_at"
							value="<?= htmlspecialchars(
								$endsAtInput,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

						<p class="form-help">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.ends_at.help'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					</div>

				</section>

			</main>

			<aside class="admin-article-form-sidebar">

				<section class="admin-editor-panel">

					<h2>
						<?= htmlspecialchars(
							$t('admin.event_form.publication.title'),
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
											'admin.event_form.publication.save_changes'
										)
										: $t(
											'admin.event_form.publication.save_draft'
										),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>
						</button>

						<?php if (
							$isEdit
							&& (($event['status'] ?? '') === 'submitted')
						): ?>

							<button
								type="submit"
								name="publish_after_update"
								value="1"
								class="mv-admin-button is-success"
								onclick="return confirm(<?= htmlspecialchars(
									json_encode(
										$t(
											'admin.event_form.publication.confirm_publish'
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
										'admin.event_form.publication.save_publish'
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</button>

						<?php endif; ?>

						<a
							href="/admin/events"
							class="mv-admin-button is-secondary"
						>
							<?= htmlspecialchars(
								$t(
									'admin.event_form.publication.cancel'
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
							$t('admin.event_form.address.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<div class="form-group">

						<label for="slug">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.slug.label'
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
									'admin.event_form.fields.slug.placeholder'
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
									'admin.event_form.fields.slug.help'
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
							$t('admin.event_form.location.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<div class="form-group">

						<label for="location">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.location.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="text"
							id="location"
							name="location"
							maxlength="255"
							value="<?= htmlspecialchars(
								$location,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

					</div>

					<div class="form-group">

						<label for="latitude">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.latitude.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="number"
							id="latitude"
							name="latitude"
							step="0.0000001"
							min="-90"
							max="90"
							value="<?= htmlspecialchars(
								$latitude,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

					</div>

					<div class="form-group">

						<label for="longitude">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.longitude.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="number"
							id="longitude"
							name="longitude"
							step="0.0000001"
							min="-180"
							max="180"
							value="<?= htmlspecialchars(
								$longitude,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

						<p class="form-help">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.coordinates.help'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					</div>

					<div class="form-group">

						<label for="external_url">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.external_url.label'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<input
							type="url"
							id="external_url"
							name="external_url"
							maxlength="1000"
							value="<?= htmlspecialchars(
								$externalUrl,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

						<p class="form-help">
							<?= htmlspecialchars(
								$t(
									'admin.event_form.fields.external_url.help'
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
							$t('admin.event_form.cover.title'),
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
										'admin.event_form.cover.current_alt'
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
											'admin.event_form.cover.empty'
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
										'admin.event_form.cover.replace'
									)
									: $t(
										'admin.event_form.cover.upload'
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
								$t('admin.event_form.cover.formats'),
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
