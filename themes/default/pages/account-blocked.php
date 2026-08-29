<?php
declare(strict_types=1);

$blockedUsers = is_array($blockedUsers ?? null)
	? $blockedUsers
	: [];
?>

<div class="container page-container">

	<div class="page-header">

		<h1>
			<?= htmlspecialchars(
				$t('account.blocked.title'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h1>

		<p class="page-subtitle">
			<?= htmlspecialchars(
				$t('account.blocked.subtitle'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	</div>

	<div class="card">

		<div class="card-body">

			<h2>
				<?= htmlspecialchars(
					$t('account.blocked.section_title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h2>

			<p class="text-muted">
				<?= htmlspecialchars(
					$t('account.blocked.section_help'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<?php if ($blockedUsers === []): ?>

				<div class="mv-empty-state">

					<h3>
						<?= htmlspecialchars(
							$t('account.blocked.empty_title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h3>

					<p>
						<?= htmlspecialchars(
							$t('account.blocked.empty_text'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

			<?php else: ?>

				<div class="account-blocked-list">

					<?php foreach ($blockedUsers as $blocked): ?>

						<?php
						$username = trim(
							(string) ($blocked['username'] ?? '')
						);

						$profileAvailable = !empty(
							$blocked['profile_available']
						);

						$accountExists = array_key_exists(
							'account_exists',
							$blocked
						)
							? !empty($blocked['account_exists'])
							: true;

						$blockedAt = trim(
							(string) ($blocked['blocked_at'] ?? '')
						);
						?>

						<div class="account-blocked-item">

							<div class="account-blocked-user">

								<strong>

									<?php if ($username !== ''): ?>

										<?= htmlspecialchars(
											$username,
											ENT_QUOTES,
											'UTF-8'
										) ?>

									<?php else: ?>

										<?= htmlspecialchars(
											$t(
												'account.blocked.user_unavailable'
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>

									<?php endif; ?>

								</strong>

								<?php if (!$accountExists): ?>

									<span class="text-muted">
										<?= htmlspecialchars(
											$t(
												'account.blocked.account_missing'
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

								<?php if ($blockedAt !== ''): ?>

									<span class="text-muted">

										<?= htmlspecialchars(
											$t('account.blocked.blocked_on'),
											ENT_QUOTES,
											'UTF-8'
										) ?>

										<?= htmlspecialchars(
											$blockedAt,
											ENT_QUOTES,
											'UTF-8'
										) ?>

									</span>

								<?php endif; ?>

							</div>

							<form
								method="post"
								action="/account/blocked/unblock"
							>

								<input
									type="hidden"
									name="blocked_sub"
									value="<?= htmlspecialchars(
										(string) ($blocked['blocked_sub'] ?? ''),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
								>

								<button
									type="submit"
									class="button"
								>
									<?= htmlspecialchars(
										$t('account.blocked.unblock'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</button>

							</form>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	</div>

</div>