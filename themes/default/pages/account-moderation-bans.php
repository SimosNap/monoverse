<?php
declare(strict_types=1);

$bans = isset($bans) && is_array($bans) ? $bans : [];

$escape = static function (mixed $value): string {
	return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$formatExpiration = static function (mixed $timestamp) use ($t): string {
	$timestamp = (int) ($timestamp ?? 0);

	if ($timestamp <= 0) {
		return $t('account.moderation_bans.meta.permanent');
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

	return $t('account.moderation_bans.user.unavailable');
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
			$t('account.moderation_bans.title')
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= $escape(
			$t('account.moderation_bans.subtitle')
		) ?>
	</p>
</div>

<div class="account-card mv-moderation-section">

	<div class="account-card-header">
		<div>
			<h2>
				<?= $escape(
					$t('account.moderation_bans.section.title')
				) ?>
			</h2>

			<p class="text-muted">
				<?= $escape(
					$t('account.moderation_bans.section.help')
				) ?>
			</p>
		</div>

		<span class="badge"><?= count($bans) ?></span>
	</div>

	<?php if ($bans === []): ?>

		<div class="mv-moderation-empty">
			<i class="fa fa-check-circle" aria-hidden="true"></i>

			<div>
				<strong>
					<?= $escape(
						$t('account.moderation_bans.empty.title')
					) ?>
				</strong>

				<p>
					<?= $escape(
						$t('account.moderation_bans.empty.text')
					) ?>
				</p>
			</div>
		</div>

	<?php else: ?>

		<div class="mv-moderation-list">

			<?php foreach ($bans as $ban): ?>

				<?php
				$username = trim((string) ($ban['username'] ?? ''));
				$reason = trim((string) ($ban['ban_reason'] ?? ''));
				$avatarUrl = trim((string) ($ban['avatar_url'] ?? ''));
				$moderatorName = trim(
					(string) ($ban['moderator_nickname'] ?? '')
				);

				if ($moderatorName === '') {
					$moderatorName = trim(
						(string) ($ban['moderator_username'] ?? '')
					);
				}

				$profileExists = $hasProfile($ban);
				$profileIsPublic = $isPublicProfile($ban);
				$displayName = $getDisplayName($ban);
				?>

				<article class="mv-moderation-user">

					<div class="mv-moderation-avatar">

						<?php if ($avatarUrl !== ''): ?>

							<img
								src="<?= $escape($avatarUrl) ?>"
								alt="<?= $escape(
									$t(
										'account.moderation_bans.user.avatar_alt',
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
												'account.moderation_bans.user.deleted_or_missing'
											)
										) ?>
									</div>

								<?php endif; ?>

							</div>

							<span
								class="mv-moderation-badge mv-moderation-badge-ban"
							>
								<?= $escape(
									$t('account.moderation_bans.status')
								) ?>
							</span>

						</div>

						<?php if ($profileExists && !$profileIsPublic): ?>

							<div class="mv-moderation-notice">
								<i class="fa fa-eye-slash" aria-hidden="true"></i>

								<?= $escape(
									$t(
										'account.moderation_bans.user.public_unavailable'
									)
								) ?>
							</div>

						<?php endif; ?>

						<dl class="mv-moderation-meta">

							<div>
								<dt>
									<?= $escape(
										$t(
											'account.moderation_bans.meta.reason'
										)
									) ?>
								</dt>

								<dd>
									<?= $reason !== ''
										? $escape($reason)
										: $escape(
											$t(
												'account.moderation_bans.meta.no_reason'
											)
										) ?>
								</dd>
							</div>

							<div>
								<dt>
									<?= $escape(
										$t(
											'account.moderation_bans.meta.duration'
										)
									) ?>
								</dt>

								<dd>
									<?= $escape(
										$formatExpiration(
											$ban['ban_expires_at'] ?? null
										)
									) ?>
								</dd>
							</div>

							<div>
								<dt>
									<?= $escape(
										$t(
											'account.moderation_bans.meta.applied_on'
										)
									) ?>
								</dt>

								<dd>
									<?php if (!empty($ban['updated_at'])): ?>

										<?= date(
											'd/m/Y H:i',
											(int) $ban['updated_at']
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
											'account.moderation_bans.meta.moderator'
										)
									) ?>
								</dt>

								<dd>
									<?= $moderatorName !== ''
										? $escape($moderatorName)
										: $escape(
											$t(
												'account.moderation_bans.user.unavailable'
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
												'account.moderation_bans.actions.profile'
											)
										) ?>
									</a>

								<?php endif; ?>

								<form
									method="post"
									action="/profile/<?= rawurlencode($username) ?>/unban"
									onsubmit="return confirm(<?= htmlspecialchars(
										json_encode(
											$t(
												'account.moderation_bans.actions.unban_confirm'
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
											class="fa fa-check"
											aria-hidden="true"
										></i>

										<?= $escape(
											$t(
												'account.moderation_bans.actions.unban'
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
