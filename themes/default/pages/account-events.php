<?php
declare(strict_types=1);

/** @var array $events */

$events = isset($events) && is_array($events)
	? $events
	: [];

$statusLabels = [
	'submitted' => $t('account.events.status.submitted'),
	'published' => $t('account.events.status.published'),
	'rejected' => $t('account.events.status.rejected'),
];
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<div class="page-header">

	<h1>
		<?= htmlspecialchars(
			$t('account.events.title'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= htmlspecialchars(
			$t('account.events.subtitle'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</p>

	<?php if (
		(($settings['events_user_submissions_enabled'] ?? '0') === '1')
	): ?>

		<div class="mv-account-articles-header-actions">

			<a
				href="/events/submit"
				class="mv-button"
			>
				<i
					class="fa-solid fa-plus"
					aria-hidden="true"
				></i>

				<?= htmlspecialchars(
					$t('account.events.submit'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

		</div>

	<?php endif; ?>

</div>

<div class="mv-account-articles-list">

	<?php if ($events === []): ?>

		<div class="card">

			<p>
				<?= htmlspecialchars(
					$t('account.events.empty'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	<?php else: ?>

		<?php foreach ($events as $event): ?>

			<?php
			$status = (string) ($event['status'] ?? '');

			$statusLabel = $statusLabels[$status]
				?? ucfirst($status);
			?>

			<article class="card mv-account-article-card">

				<div class="mv-account-article-content">

					<div class="mv-account-article-heading">

						<h2>
							<?= htmlspecialchars(
								(string) ($event['title'] ?? ''),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</h2>

						<span
							class="mv-account-article-status is-<?= htmlspecialchars(
								$status,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>
							<?= htmlspecialchars(
								$statusLabel,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</div>

					<div class="mv-account-article-meta">

						<?php if (!empty($event['starts_at'])): ?>

							<span>
								<i
									class="fa-regular fa-calendar"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									date(
										'd/m/Y H:i',
										strtotime(
											(string) $event['starts_at']
										)
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

						<?php if (!empty($event['location'])): ?>

							<span>
								<i
									class="fa-solid fa-location-dot"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									(string) $event['location'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

						<?php if (!empty($event['submitted_at'])): ?>

							<span>
								<?= htmlspecialchars(
									$t('account.events.submitted_on'),
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

					</div>

					<?php if ($status === 'submitted'): ?>

						<div class="mv-account-article-actions">

							<a
								href="/account/events/<?= rawurlencode(
									(string) $event['uuid']
								) ?>/edit"
								class="mv-button"
							>
								<i
									class="fa-solid fa-pen"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('account.events.actions.edit'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>

						</div>

					<?php endif; ?>

					<?php if (
						$status === 'rejected'
						&& !empty($event['rejection_reason'])
					): ?>

						<div class="mv-account-article-rejection">

							<div class="mv-account-article-rejection-title">

								<i
									class="fa-solid fa-circle-info"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('account.events.rejection.title'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

							</div>

							<p>
								<?= nl2br(
									htmlspecialchars(
										(string) $event['rejection_reason'],
										ENT_QUOTES,
										'UTF-8'
									)
								) ?>
							</p>

						</div>

					<?php endif; ?>

					<?php if (
						$status === 'published'
						&& !empty($event['slug'])
					): ?>

						<div class="mv-account-article-actions">

							<a
								href="/events/<?= rawurlencode(
									(string) $event['slug']
								) ?>"
								class="mv-button"
							>
								<i
									class="fa-solid fa-arrow-up-right-from-square"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('account.events.actions.view'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>

						</div>

					<?php endif; ?>

				</div>

			</article>

		<?php endforeach; ?>

	<?php endif; ?>

</div>
