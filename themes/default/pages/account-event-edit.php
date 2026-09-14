<?php
declare(strict_types=1);

/** @var array $event */

$event = isset($event) && is_array($event)
	? $event
	: [];

$eventTitle = (string) ($event['title'] ?? '');
$description = (string) ($event['description'] ?? '');
$location = (string) ($event['location'] ?? '');
$latitude = (string) ($event['latitude'] ?? '');
$longitude = (string) ($event['longitude'] ?? '');
$externalUrl = (string) ($event['external_url'] ?? '');
$cover = trim((string) ($event['cover'] ?? ''));

$startsAt = '';

if (!empty($event['starts_at'])) {
	$timestamp = strtotime((string) $event['starts_at']);

	if ($timestamp !== false) {
		$startsAt = date('Y-m-d\TH:i', $timestamp);
	}
}

$endsAt = '';

if (!empty($event['ends_at'])) {
	$timestamp = strtotime((string) $event['ends_at']);

	if ($timestamp !== false) {
		$endsAt = date('Y-m-d\TH:i', $timestamp);
	}
}
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
					$t('account.event_edit.eyebrow'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

			<h1>
				<?= htmlspecialchars(
					$t('account.event_edit.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('account.event_edit.intro'),
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
		action="/account/events/<?= rawurlencode(
			(string) ($event['uuid'] ?? '')
		) ?>"
		enctype="multipart/form-data"
		class="chanzine-submit-form"
	>

		<div class="chanzine-submit-main">

			<section class="chanzine-submit-card">

				<div class="chanzine-submit-card-header">

					<h2>
						<?= htmlspecialchars(
							$t('account.event_edit.event.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('account.event_edit.event.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="mv-field">

					<label for="title">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.title'),
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
							$eventTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						required
					>

				</div>

				<div class="mv-field">

					<label for="description">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.description'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<textarea
						id="description"
						name="description"
						rows="12"
						required
					><?= htmlspecialchars(
						$description,
						ENT_QUOTES,
						'UTF-8'
					) ?></textarea>

				</div>

				<div class="mv-field">

					<label for="location">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.location'),
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

				<div class="mv-field">

					<label for="external_url">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.external_url'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<input
						type="url"
						id="external_url"
						name="external_url"
						value="<?= htmlspecialchars(
							$externalUrl,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

				</div>

			</section>

		</div>

		<aside class="chanzine-submit-sidebar">

			<section class="chanzine-submit-card">

				<div class="chanzine-submit-card-header">

					<h2>
						<?= htmlspecialchars(
							$t('account.event_edit.details.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('account.event_edit.details.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="mv-field">

					<label for="starts_at">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.starts_at'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<input
						type="datetime-local"
						id="starts_at"
						name="starts_at"
						value="<?= htmlspecialchars(
							$startsAt,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						required
					>

				</div>

				<div class="mv-field">

					<label for="ends_at">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.ends_at'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<input
						type="datetime-local"
						id="ends_at"
						name="ends_at"
						value="<?= htmlspecialchars(
							$endsAt,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

				</div>

				<div class="mv-field">

					<label for="latitude">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.latitude'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<input
						type="text"
						id="latitude"
						name="latitude"
						value="<?= htmlspecialchars(
							$latitude,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

				</div>

				<div class="mv-field">

					<label for="longitude">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.longitude'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<input
						type="text"
						id="longitude"
						name="longitude"
						value="<?= htmlspecialchars(
							$longitude,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

				</div>

				<div class="mv-field">

					<label for="cover">
						<?= htmlspecialchars(
							$t('account.event_edit.fields.cover'),
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
									'account.event_edit.fields.cover_replace_help'
								)
								: $t(
									'account.event_edit.fields.cover_default_help'
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
							$t('account.event_edit.save.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('account.event_edit.save.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

				<div class="chanzine-submit-actions">

					<a
						href="/account/events"
						class="button"
					>
						<?= htmlspecialchars(
							$t('account.event_edit.save.cancel'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</a>

					<button
						type="submit"
						class="button button-primary"
					>
						<?= htmlspecialchars(
							$t('account.event_edit.save.submit'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

				</div>

			</section>

		</aside>

	</form>

</section>
