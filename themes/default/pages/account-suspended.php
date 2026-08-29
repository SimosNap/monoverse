<?php
declare(strict_types=1);

/** @var array $moderation */

$reason = trim((string) ($moderation['ban_reason'] ?? ''));
$expires = $moderation['banned_until'] ?? null;
?>

<div class="container page-container">

	<div class="page-header">

		<h1>
			<?= htmlspecialchars(
				$t('account.suspended.title'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h1>

		<p class="page-subtitle">
			<?= htmlspecialchars(
				$t('account.suspended.subtitle'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	</div>

	<div class="card">

		<div class="card-body">

			<p>
				<?= htmlspecialchars(
					$t('account.suspended.intro'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<hr>

			<dl class="row mb-4">

				<dt class="col-sm-3">
					<?= htmlspecialchars(
						$t('account.suspended.status'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</dt>

				<dd class="col-sm-9">

					<span class="badge bg-danger">
						<?= htmlspecialchars(
							$t('account.suspended.status_value'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				</dd>

				<dt class="col-sm-3">
					<?= htmlspecialchars(
						$t('account.suspended.reason'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</dt>

				<dd class="col-sm-9">

					<?php if ($reason !== ''): ?>

						<?= htmlspecialchars(
							$reason,
							ENT_QUOTES,
							'UTF-8'
						) ?>

					<?php else: ?>

						<em>
							<?= htmlspecialchars(
								$t('account.suspended.reason_unspecified'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</em>

					<?php endif; ?>

				</dd>

				<dt class="col-sm-3">
					<?= htmlspecialchars(
						$t('account.suspended.duration'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</dt>

				<dd class="col-sm-9">

					<?php if ($expires): ?>

						<?= htmlspecialchars(
							$t('account.suspended.until'),
							ENT_QUOTES,
							'UTF-8'
						) ?>

						<strong>
							<?= date(
								'd/m/Y H:i',
								strtotime((string) $expires)
							) ?>
						</strong>

					<?php else: ?>

						<strong>
							<?= htmlspecialchars(
								$t('account.suspended.permanent'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

					<?php endif; ?>

				</dd>

			</dl>

			<p class="text-muted">
				<?= htmlspecialchars(
					$t('account.suspended.appeal'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<hr class="my-4">

			<h2>
				<?= htmlspecialchars(
					$t('account.suspended.leave.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h2>

			<p>
				<?= htmlspecialchars(
					$t('account.suspended.leave.intro'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<ul>

				<li>
					<?= htmlspecialchars(
						$t('account.suspended.leave.profile_deleted'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</li>

				<li>
					<?= htmlspecialchars(
						$t('account.suspended.leave.removed_from_members'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</li>

				<li>
					<?= htmlspecialchars(
						$t('account.suspended.leave.suspension_retained'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</li>

			</ul>

			<form
				method="post"
				action="/account/delete"
				onsubmit="return confirm(<?= htmlspecialchars(
					json_encode(
						$t('account.suspended.leave.confirm'),
						JSON_HEX_APOS
						| JSON_HEX_QUOT
						| JSON_UNESCAPED_UNICODE
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>);"
			>

				<button
					type="submit"
					class="btn btn-outline-danger"
				>
					<?= htmlspecialchars(
						$t('account.suspended.leave.delete'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

			</form>

			<div class="mt-4">

				<a
					href="/account/logout"
					class="btn btn-danger"
				>
					<?= htmlspecialchars(
						$t('account.suspended.logout'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

			</div>

		</div>

	</div>

</div>
