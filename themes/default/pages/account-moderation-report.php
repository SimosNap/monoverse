<?php
declare(strict_types=1);

$escape = static fn (mixed $value): string => htmlspecialchars(
	(string) $value,
	ENT_QUOTES,
	'UTF-8'
);

$formatDate = static function (?string $value): string {
	if (empty($value)) {
		return '';
	}

	try {
		return (new DateTimeImmutable($value))
			->format('d/m/Y H:i');
	} catch (Throwable) {
		return (string) $value;
	}
};

$statusLabels = [
	'open'     => $t('account.moderation_report.status_labels.open'),
	'reviewed' => $t('account.moderation_report.status_labels.reviewed'),
	'closed'   => $t('account.moderation_report.status_labels.closed'),
];

$reasonLabels = [
	'spam'       => $t('account.moderation_report.reasons.spam'),
	'harassment' => $t('account.moderation_report.reasons.harassment'),
	'privacy'    => $t('account.moderation_report.reasons.privacy'),
	'illegal'    => $t('account.moderation_report.reasons.illegal'),
	'copyright'  => $t('account.moderation_report.reasons.copyright'),
	'other'      => $t('account.moderation_report.reasons.other'),
];
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<?= $component('account-moderation-navigation') ?>

<div class="page-header">
	<h1>
		<?= $escape(
			$t('account.moderation_report.title')
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= $escape(
			$t('account.moderation_report.subtitle')
		) ?>
	</p>
</div>

<div class="account-card mv-account-card">

	<div class="mv-account-card-header">

		<div>

			<h2>
				<?= $escape(
					$reasonLabels[$report['reason']]
					?? ucfirst((string) $report['reason'])
				) ?>
			</h2>

			<p>
				<?= $escape(
					$t('account.moderation_report.status')
				) ?>

				<strong>
					<?= $escape(
						$statusLabels[$report['status']]
						?? $report['status']
					) ?>
				</strong>
			</p>

		</div>

	</div>

	<div class="mv-report-meta">

		<span>
			<i class="fa-solid fa-message"></i>

			<?= $escape(
				strtoupper((string) $report['target_type'])
			) ?>
		</span>

		<span>
			<i class="fa-regular fa-clock"></i>

			<?= $escape(
				$formatDate($report['created_at'] ?? null)
			) ?>
		</span>

		<span>
			<i class="fa-solid fa-user"></i>

			<?= $escape(
				$t('account.moderation_report.reported_by')
			) ?>

			<?= $escape(
				(string) (
					$report['reporter_username']
					?: $report['reporter_sub']
				)
			) ?>
		</span>

		<?php if (!empty($report['reviewed_by'])): ?>

			<span>
				<i class="fa-solid fa-user-shield"></i>

				<?= $escape(
					$t('account.moderation_report.reviewed_by')
				) ?>

				<?= $escape(
					(string) (
						$report['reviewer_username']
						?: $report['reviewed_by']
					)
				) ?>
			</span>

		<?php endif; ?>

		<?php if (!empty($report['reviewed_at'])): ?>

			<span>
				<i class="fa-regular fa-calendar-check"></i>

				<?= $escape(
					$formatDate($report['reviewed_at'] ?? null)
				) ?>
			</span>

		<?php endif; ?>

	</div>

	<?php if (!empty($report['description'])): ?>

		<div class="mv-report-description">
			<?= nl2br(
				$escape($report['description'])
			) ?>
		</div>

	<?php endif; ?>

	<hr class="mv-divider">

	<h3>
		<?= $escape(
			$t('account.moderation_report.reported_content')
		) ?>
	</h3>

	<?= $component(
		'moderation-report-content',
		[
			'report'  => $report,
			'content' => $content,
		]
	) ?>

</div>

<?php if (($report['status'] ?? '') !== 'closed'): ?>

	<div class="mv-report-actions">

		<?php if (($report['status'] ?? '') === 'open'): ?>

			<form
				method="post"
				action="/account/moderation/report/<?= rawurlencode(
					(string) $report['uuid']
				) ?>/review"
			>
				<button
					type="submit"
					class="mv-button mv-button-primary"
				>
					<?= $escape(
						$t(
							'account.moderation_report.actions.mark_reviewed'
						)
					) ?>
				</button>
			</form>

		<?php endif; ?>

		<form
			method="post"
			action="/account/moderation/report/<?= rawurlencode(
				(string) $report['uuid']
			) ?>/close"
		>
			<button
				type="submit"
				class="mv-button"
			>
				<?= $escape(
					$t(
						'account.moderation_report.actions.close'
					)
				) ?>
			</button>
		</form>

		<form
			method="post"
			action="/account/moderation/report/<?= rawurlencode(
				(string) $report['uuid']
			) ?>/delete"
			onsubmit="return confirm(<?= htmlspecialchars(
				json_encode(
					$t(
						'account.moderation_report.actions.delete_confirm'
					),
					JSON_UNESCAPED_UNICODE
					| JSON_HEX_APOS
					| JSON_HEX_QUOT
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>);"
		>
			<button
				type="submit"
				class="mv-button mv-button-danger"
			>
				<?= $escape(
					$t(
						'account.moderation_report.actions.delete_content'
					)
				) ?>
			</button>
		</form>

	</div>

<?php endif; ?>
