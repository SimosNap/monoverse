<?php
declare(strict_types=1);

$bans = isset($bans) && is_array($bans) ? $bans : [];
$mutes = isset($mutes) && is_array($mutes) ? $mutes : [];

$openReportsCount = (int) ($openReports ?? 0);
$bansCount = count($bans);
$mutesCount = count($mutes);

$formatCounter = static function (int $count): string {
	return $count === 0 ? '✓' : (string) $count;
};
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<?= $component('account-moderation-navigation') ?>

<div class="page-header">
	<h1>
		<?= htmlspecialchars(
			$t('account.moderation.title'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= htmlspecialchars(
			$t('account.moderation.subtitle'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</p>
</div>

<div class="mv-card mv-account-panel mv-moderation-overview">

	<div class="mv-moderation-dashboard">

		<div class="account-card mv-moderation-dashboard-card">

			<div class="account-card-header">

				<div>
					<h2>
						<i class="fa fa-flag" aria-hidden="true"></i>

						<?= htmlspecialchars(
							$t('account.moderation.reports.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p class="text-muted">
						<?= htmlspecialchars(
							$t('account.moderation.reports.text'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>
				</div>

				<span class="badge">
					<?= $formatCounter($openReportsCount) ?>
				</span>

			</div>

			<div class="mv-moderation-dashboard-actions">

				<a
					class="btn btn-primary"
					href="/account/moderation/reports"
				>
					<?= htmlspecialchars(
						$t('account.moderation.reports.open'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

			</div>

		</div>

		<div class="account-card mv-moderation-dashboard-card">

			<div class="account-card-header">

				<div>
					<h2>
						<i class="fa fa-ban" aria-hidden="true"></i>

						<?= htmlspecialchars(
							$t('account.moderation.bans.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p class="text-muted">
						<?= htmlspecialchars(
							$t('account.moderation.bans.text'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>
				</div>

				<span class="badge">
					<?= $formatCounter($bansCount) ?>
				</span>

			</div>

			<div class="mv-moderation-dashboard-actions">

				<a
					class="btn btn-secondary"
					href="/account/moderation/bans"
				>
					<?= htmlspecialchars(
						$t('account.moderation.bans.open'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

			</div>

		</div>

		<div class="account-card mv-moderation-dashboard-card">

			<div class="account-card-header">

				<div>
					<h2>
						<i class="fa fa-volume-off" aria-hidden="true"></i>

						<?= htmlspecialchars(
							$t('account.moderation.mutes.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p class="text-muted">
						<?= htmlspecialchars(
							$t('account.moderation.mutes.text'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>
				</div>

				<span class="badge">
					<?= $formatCounter($mutesCount) ?>
				</span>

			</div>

			<div class="mv-moderation-dashboard-actions">

				<a
					class="btn btn-secondary"
					href="/account/moderation/mutes"
				>
					<?= htmlspecialchars(
						$t('account.moderation.mutes.open'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

			</div>

		</div>

	</div>

</div>
