<?php
declare(strict_types=1);

$mutes = isset($mutes) && is_array($mutes) ? $mutes : [];

$escape = static function (mixed $value): string {
	return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$formatExpiration = static function (mixed $timestamp) use ($t): string {
	$timestamp = (int) ($timestamp ?? 0);

	if ($timestamp <= 0) {
		return $t('account.moderation_mutes.meta.permanent');
	}

	return date('d/m/Y H:i', $timestamp);
};

$getDisplayName = static function (array $record) use ($t): string {
	$nickname = trim((string) ($record['nickname'] ?? ''));
	$username = trim((string) ($record['username'] ?? ''));

	if ($nickname !== '') {
		return $nickname;
	}

	if ($username !== '') {
		return $username;
	}

	return $t('account.moderation_mutes.user.unavailable');
};

$hasProfile = static function (array $record): bool {
	return trim((string) ($record['username'] ?? '')) !== '';
};

$isPublicProfile = static function (array $record): bool {
	return !empty($record['public_profile']);
};
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<?= $component('account-moderation-navigation') ?>

<div class="page-header">
	<h1>
		<?= $escape(
			$t('account.moderation_mutes.title')
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= $escape(
			$t('account.moderation_mutes.subtitle')
		) ?>
	</p>
</div>

<div class="account-card mv-moderation-section">

	<div class="account-card-header">
		<div>
			<h2>
				<?= $escape(
					$t('account.moderation_mutes.section.title')
				) ?>
			</h2>

			<p class="text-muted">
				<?= $escape(
					$t('account.moderation_mutes.section.help')
				) ?>
			</p>
		</div>

		<span class="badge"><?= count($mutes) ?></span>
	</div>

	<?php if ($mutes === []): ?>

		<div class="mv-moderation-empty">
			<i class="fa fa-check-circle" aria-hidden="true"></i>

			<div>
				<strong>
					<?= $escape(
						$t('account.moderation_mutes.empty.title')
					) ?>
				</strong>

				<p>
					<?= $escape(
						$t('account.moderation_mutes.empty.text')
					) ?>
				</p>
			</div>
		</div>

	<?php else: ?>

		<div class="mv-moderation-list">

			<?php foreach ($mutes as $mute): ?>

				<?php
				$username = trim((string) ($mute['username'] ?? ''));
				$reason = trim((string) ($mute['mute_reason'] ?? ''));
				$avatarUrl = trim((string) ($mute['avatar_url'] ?? ''));
				$moderatorName = trim(
					(string) ($mute['moderator_nickname'] ?? '')
				);

				if ($moderatorName === '') {
					$moderatorName = trim(
						(string) ($mute['moderator_username'] ?? '')
					);
				}

				$profileExists = $hasProfile($mute);
				$profileIsPublic = $isPublicProfile($mute);
				$displayName = $getDisplayName($mute);
				?>

				<article class="mv-moderation-user">

					<div class="mv-moderation-avatar">

						<?php if ($avatarUrl !== ''): ?>

							<img
								src="<?= $escape($avatarUrl) ?>"
								alt="<?= $escape(
									$t(
										'account.moderation_mutes.user.avatar_alt',
										[
											'name' => $displayName,
										]
									)
								) ?>"
								loading="lazy"
							>

						<?php else: ?>

							<div
								class="mv-moderation-avatar-placeholder"
								aria-hidden="true"
							>
								<i class="fa fa-user"></i>
							</div>

						<?php endif; ?>

					</div>

					<div class="mv-moderation-body">

						<div class="mv-moderation-header">

							<div class="mv-moderation-title">

								<h3>
									<?php if ($profileExists && $profileIsPublic): ?>

										<a href="/profile/<?= rawurlencode($username) ?>">
											<?= $escape($displayName) ?>
										</a>

									<?php else: ?>

										<?= $escape($displayName) ?>

									<?php endif; ?>
								</h3>

								<?php if ($profileExists): ?>

									<div class="mv-moderation-username">
										@<?= $escape($username) ?>
									</div>

								<?php else: ?>

									<div class="mv-moderation-profile-status">
										<?= $escape(
											$t(
												'account.moderation_mutes.user.deleted_or_missing'
											)
										) ?>
									</div>

								<?php endif; ?>

							</div>

							<span
								class="mv-moderation-badge mv-moderation-badge-mute"
							>
								<?= $escape(
									$t('account.moderation_mutes.status')
								) ?>
							</span>

						</div>

						<?php if ($profileExists && !$profileIsPublic): ?>

							<div class="mv-moderation-notice">
								<i
									class="fa fa-eye-slash"
									aria-hidden="true"
								></i>

								<?= $escape(
									$t(
										'account.moderation_mutes.user.public_unavailable'
									)
								) ?>
							</div>

						<?php endif; ?>

						<dl class="mv-moderation-meta">

							<div>
								<dt>
									<?= $escape(
										$t(
											'account.moderation_mutes.meta.reason'
										)
									) ?>
								</dt>

								<dd>
									<?= $reason !== ''
										? $escape($reason)
										: $escape(
											$t(
												'account.moderation_mutes.meta.no_reason'
											)
										) ?>
								</dd>
							</div>

							<div>
								<dt>
									<?= $escape(
										$t(
											'account.moderation_mutes.meta.duration'
										)
									) ?>
								</dt>

								<dd>
									<?= $escape(
										$formatExpiration(
											$mute['mute_expires_at'] ?? null
										)
									) ?>
								</dd>
							</div>

							<div>
								<dt>
									<?= $escape(
										$t(
											'account.moderation_mutes.meta.applied_on'
										)
									) ?>
								</dt>

								<dd>
									<?php if (!empty($mute['updated_at'])): ?>

										<?= date(
											'd/m/Y H:i',
											(int) $mute['updated_at']
										) ?>

									<?php else: ?>

										—

									<?php endif; ?>
								</dd>
							</div>

							<div>
								<dt>
									<?= $escape(
										$t(
											'account.moderation_mutes.meta.moderator'
										)
									) ?>
								</dt>

								<dd>
									<?= $moderatorName !== ''
										? $escape($moderatorName)
										: $escape(
											$t(
												'account.moderation_mutes.user.unavailable'
											)
										) ?>
								</dd>
							</div>

						</dl>

						<?php if ($profileExists): ?>

							<div class="mv-moderation-actions">

								<?php if ($profileIsPublic): ?>

									<a
										class="btn btn-secondary btn-small"
										href="/profile/<?= rawurlencode($username) ?>"
									>
										<?= $escape(
											$t(
												'account.moderation_mutes.actions.profile'
											)
										) ?>
									</a>

								<?php endif; ?>

								<form
									method="post"
									action="/profile/<?= rawurlencode($username) ?>/unmute"
									onsubmit="return confirm(<?= htmlspecialchars(
										json_encode(
											$t(
												'account.moderation_mutes.actions.unmute_confirm'
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
										class="btn btn-success btn-small"
									>
										<i
											class="fa fa-volume-up"
											aria-hidden="true"
										></i>

										<?= $escape(
											$t(
												'account.moderation_mutes.actions.unmute'
											)
										) ?>
									</button>
								</form>

							</div>

						<?php endif; ?>

					</div>

				</article>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</div>
