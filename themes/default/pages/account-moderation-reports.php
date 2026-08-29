<?php
declare(strict_types=1);

$reports = isset($reports) && is_array($reports)
	? $reports
	: [];

$escape = static fn (mixed $value): string => htmlspecialchars(
	(string) $value,
	ENT_QUOTES,
	'UTF-8'
);

$reasonLabels = [
	'spam'           => $t('account.moderation_reports.reasons.spam'),
	'harassment'     => $t('account.moderation_reports.reasons.harassment'),
	'hate'           => $t('account.moderation_reports.reasons.hate'),
	'violence'       => $t('account.moderation_reports.reasons.violence'),
	'sexual'         => $t('account.moderation_reports.reasons.sexual'),
	'misinformation' => $t('account.moderation_reports.reasons.misinformation'),
	'privacy'        => $t('account.moderation_reports.reasons.privacy'),
	'other'          => $t('account.moderation_reports.reasons.other'),
];

$statusLabels = [
	'open'     => $t('account.moderation_reports.status.open'),
	'reviewed' => $t('account.moderation_reports.status.reviewed'),
	'closed'   => $t('account.moderation_reports.status.closed'),
];

$dateUnavailable = $t(
	'account.moderation_reports.date_unavailable'
);

$formatDate = static function (mixed $value) use ($dateUnavailable): string {
	$value = trim((string) $value);

	if ($value === '') {
		return $dateUnavailable;
	}

	try {
		return (new DateTimeImmutable($value))->format('d/m/Y · H:i');
	} catch (Throwable) {
		return $value;
	}
};

$reportCount = count($reports);
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<?= $component('account-moderation-navigation') ?>

<div class="page-header">
	<h1>
		<?= $escape(
			$t('account.moderation_reports.title')
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= $escape(
			$t('account.moderation_reports.subtitle')
		) ?>
	</p>
</div>

<div class="account-card mv-moderation-section">

	<div class="account-card-header">
		<div>
			<h2>
				<?= $escape(
					$t('account.moderation_reports.section_title')
				) ?>
			</h2>

			<p class="text-muted">
				<?= $escape(
					$reportCount === 1
						? $t(
							'account.moderation_reports.count.one'
						)
						: $t(
							'account.moderation_reports.count.many',
							[
								'count' => $reportCount,
							]
						)
				) ?>
			</p>
		</div>

		<span class="badge">
			<?= $reportCount ?>
		</span>
	</div>

	<?php if ($reports === []): ?>

		<div class="mv-report-empty">
			<i class="fa-solid fa-shield-check" aria-hidden="true"></i>

			<div>
				<strong>
					<?= $escape(
						$t(
							'account.moderation_reports.empty.title'
						)
					) ?>
				</strong>

				<p>
					<?= $escape(
						$t(
							'account.moderation_reports.empty.text'
						)
					) ?>
				</p>
			</div>
		</div>

	<?php else: ?>

		<div class="mv-report-list">

			<?php foreach ($reports as $report): ?>

				<?php
				$status = (string) ($report['status'] ?? 'open');

				if (!isset($statusLabels[$status])) {
					$status = 'open';
				}

				$reason = (string) ($report['reason'] ?? 'other');

				$reasonLabel = $reasonLabels[$reason]
					?? ucfirst(str_replace('_', ' ', $reason));

				$targetType = (string) ($report['target_type'] ?? '');

				$targetLabel = match ($targetType) {
					'ping' => $t(
						'account.moderation_reports.target.ping'
					),
					'pong' => $t(
						'account.moderation_reports.target.pong'
					),
					default => $t(
						'account.moderation_reports.target.content'
					),
				};

				$description = trim(
					(string) ($report['description'] ?? '')
				);

				$reporterUsername = trim(
					(string) ($report['reporter_username'] ?? '')
				);

				$reporterSub = trim(
					(string) ($report['reporter_sub'] ?? '')
				);

				$uuid = trim(
					(string) ($report['uuid'] ?? '')
				);

				$shortUuid = $uuid !== ''
					? substr($uuid, 0, 8)
					: '—';
				?>

				<article class="mv-report-card">

					<div class="mv-report-header">

						<div>
							<h3 class="mv-report-title">
								<?= $escape($reasonLabel) ?>
							</h3>

							<div class="mv-report-meta">

								<span>
									<i class="fa-solid fa-message"></i>
									<?= $escape($targetLabel) ?>
								</span>

								<span>
									<i class="fa-solid fa-user"></i>

									<?= $escape(
										$t(
											'account.moderation_reports.reported_by'
										)
									) ?>

									<?= $reporterUsername !== ''
										? $escape($reporterUsername)
										: (
											$reporterSub !== ''
												? $escape($reporterSub)
												: $escape(
													$t(
														'account.moderation_reports.unknown_user'
													)
												)
										) ?>
								</span>

								<span>
									<i class="fa-regular fa-clock"></i>

									<?= $escape(
										$formatDate(
											$report['created_at'] ?? ''
										)
									) ?>
								</span>

								<span>
									<?= $escape(
										$t(
											'account.moderation_reports.id'
										)
									) ?>
									<?= $escape($shortUuid) ?>
								</span>

							</div>
						</div>

						<span
							class="mv-report-status mv-report-status-<?= $escape($status) ?>"
						>
							<?= $escape($statusLabels[$status]) ?>
						</span>

					</div>

					<div class="mv-report-description">
						<?php if ($description !== ''): ?>

							<?= nl2br($escape($description)) ?>

						<?php else: ?>

							<?= $escape(
								$t(
									'account.moderation_reports.no_description'
								)
							) ?>

						<?php endif; ?>
					</div>

					<div class="mv-report-actions">

						<a
							class="mv-button mv-button-primary"
							href="/account/moderation/report/<?= rawurlencode($uuid) ?>"
						>
							<?= $escape(
								$t(
									'account.moderation_reports.open_report'
								)
							) ?>
						</a>

					</div>

				</article>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</div>
