<?php
declare(strict_types=1);

/** @var array $notifications */
/** @var array $actors */

$notifications = isset($notifications) && is_array($notifications)
	? $notifications
	: [];

$actors = isset($actors) && is_array($actors)
	? $actors
	: [];

$escape = static fn (mixed $value): string => htmlspecialchars(
	(string) $value,
	ENT_QUOTES,
	'UTF-8'
);
?>

<section class="notifications-page">

	<header class="notifications-header">

		<div class="notifications-heading">

			<div class="notifications-heading-icon" aria-hidden="true">
				<i class="fas fa-bell"></i>
			</div>

			<div>
				<h1>
					<?= $escape(
						$t('notifications.page.title')
					) ?>
				</h1>

				<p>
					<?= $escape(
						$t('notifications.page.subtitle')
					) ?>
				</p>
			</div>

		</div>

		<?php if ($notifications !== []): ?>

			<div class="notifications-header-actions">

				<div class="notifications-total">
					<?= $escape(
						count($notifications) === 1
							? $t(
								'notifications.page.count.one',
								[
									'count' => count($notifications),
								]
							)
							: $t(
								'notifications.page.count.many',
								[
									'count' => count($notifications),
								]
							)
					) ?>
				</div>

				<form
					method="post"
					action="/notifications/delete-all"
					onsubmit="return confirm(<?= htmlspecialchars(
						json_encode(
							$t(
								'notifications.page.delete_all_confirm'
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
						class="notifications-delete-all"
					>
						<i class="fas fa-trash" aria-hidden="true"></i>

						<?= $escape(
							$t('notifications.page.delete_all')
						) ?>
					</button>
				</form>

			</div>

		<?php endif; ?>

	</header>

	<?php if ($notifications === []): ?>

		<div class="notifications-empty">

			<div class="notifications-empty-icon" aria-hidden="true">
				<i class="far fa-bell-slash"></i>
			</div>

			<h2>
				<?= $escape(
					$t('notifications.page.empty.title')
				) ?>
			</h2>

			<p>
				<?= $escape(
					$t('notifications.page.empty.text')
				) ?>
			</p>

			<a href="/ping" class="notifications-empty-action">
				<i class="fas fa-stream" aria-hidden="true"></i>

				<?= $escape(
					$t('notifications.page.empty.action')
				) ?>
			</a>

		</div>

	<?php else: ?>

		<div class="notifications-list">

			<?php foreach ($notifications as $notification): ?>

				<?php
				$actorSub = (string) (
					$notification['actor_sub'] ?? ''
				);

				$actor = $actors[$actorSub] ?? null;

				$username = '';
				$displayName = $t(
					'notifications.page.actor_fallback'
				);
				$avatar = null;

				if (is_array($actor)) {
					$username = trim(
						(string) ($actor['username'] ?? '')
					);

					$displayName = trim(
						(string) ($actor['nickname'] ?? '')
					);

					if ($displayName === '') {
						$displayName = $username !== ''
							? $username
							: $t(
								'notifications.page.actor_fallback'
							);
					}

					if (
						!empty($actor['show_avatar'])
						&& !empty($actor['avatar_url'])
					) {
						$avatar = (string) $actor['avatar_url'];
					}
				}

				$isUnread = (int) (
					$notification['is_read'] ?? 0
				) === 0;

				$type = (string) (
					$notification['type'] ?? ''
				);

				$notificationUuid = trim(
					(string) ($notification['uuid'] ?? '')
				);

				$objectUuid = trim(
					(string) ($notification['object_uuid'] ?? '')
				);

				$metadata = [];

				if (!empty($notification['metadata'])) {
					$decodedMetadata = json_decode(
						(string) $notification['metadata'],
						true
					);

					if (is_array($decodedMetadata)) {
						$metadata = $decodedMetadata;
					}
				}

				$notificationIcon = 'fa-bell';
				$notificationClass = 'notification-type-default';
				$notificationText = $t(
					'notifications.page.types.default'
				);

				switch ($type) {
					case 'reply':
						$notificationIcon = 'fa-reply';
						$notificationClass = 'notification-type-reply';
						$notificationText = $t(
							'notifications.page.types.reply'
						);
						break;

					case 'mention':
						$notificationIcon = 'fa-at';
						$notificationClass = 'notification-type-mention';
						$notificationText = $t(
							'notifications.page.types.mention'
						);
						break;

					case 'upvote':
						$notificationIcon = 'fa-arrow-up';
						$notificationClass = 'notification-type-upvote';
						$notificationText = $t(
							'notifications.page.types.upvote'
						);
						break;

					case 'report':
						$notificationIcon = 'fa-flag';
						$notificationClass = 'notification-type-report';
						$notificationText = $t(
							'notifications.page.types.report'
						);
						break;

					case 'doge_tip':
						$notificationIcon = 'fa-coins';
						$notificationClass = 'notification-type-doge-tip';

						$amount = trim(
							(string) ($metadata['amount'] ?? '')
						);

						$notificationText = $amount !== ''
							? $t(
								'notifications.page.types.doge_tip_amount',
								[
									'amount' => $amount,
								]
							)
							: $t(
								'notifications.page.types.doge_tip'
							);
						break;
				}

				$profileUrl = $username !== ''
					? '/profile/' . rawurlencode($username)
					: null;

				if (
					$type === 'doge_tip'
					&& $objectUuid !== ''
				) {
					$notificationUrl =
						'https://dogechain.info/tx/'
						. rawurlencode($objectUuid);
				} elseif (
					$type === 'report'
					&& $objectUuid !== ''
				) {
					$notificationUrl =
						'/account/moderation/report/'
						. rawurlencode($objectUuid);
				} else {
					$notificationUrl = $objectUuid !== ''
						? '/ping/' . rawurlencode($objectUuid)
						: '/notifications';
				}

				$profileAria = $t(
					'notifications.page.profile_of',
					[
						'name' => $displayName,
					]
				);
				?>

				<article
					class="notification-card <?= $isUnread
						? 'is-unread'
						: 'is-read' ?>"
				>

					<div class="notification-avatar-wrap">

						<?php if ($profileUrl !== null): ?>

							<a
								href="<?= $escape($profileUrl) ?>"
								class="notification-avatar"
								aria-label="<?= $escape($profileAria) ?>"
							>

						<?php else: ?>

							<div class="notification-avatar">

						<?php endif; ?>

							<?php if ($avatar !== null): ?>

								<img
									src="<?= $escape($avatar) ?>"
									alt="<?= $escape($displayName) ?>"
								>

							<?php else: ?>

								<span class="notification-avatar-placeholder">
									<i
										class="fas fa-user"
										aria-hidden="true"
									></i>
								</span>

							<?php endif; ?>

						<?php if ($profileUrl !== null): ?>

							</a>

						<?php else: ?>

							</div>

						<?php endif; ?>

						<span
							class="notification-type-icon <?= $escape(
								$notificationClass
							) ?>"
							aria-hidden="true"
						>
							<i class="fas <?= $escape(
								$notificationIcon
							) ?>"></i>
						</span>

					</div>

					<div class="notification-body">

						<div class="notification-message">

							<?php if ($profileUrl !== null): ?>

								<a
									href="<?= $escape($profileUrl) ?>"
									class="notification-actor"
								>
									<?= $escape($displayName) ?>
								</a>

							<?php else: ?>

								<strong class="notification-actor">
									<?= $escape($displayName) ?>
								</strong>

							<?php endif; ?>

							<span>
								<?= $escape($notificationText) ?>
							</span>

						</div>

						<?php if (!empty($notification['post_content'])): ?>

							<p class="notification-preview">
								<?= $escape(
									mb_strimwidth(
										(string) $notification['post_content'],
										0,
										180,
										'…'
									)
								) ?>
							</p>

						<?php endif; ?>

						<div class="notification-meta">

							<time datetime="<?= $escape(
								$notification['created_at'] ?? ''
							) ?>">
								<i
									class="far fa-clock"
									aria-hidden="true"
								></i>

								<?= \Monoverse\Helpers\DateHelper::timeAgo(
									(string) (
										$notification['created_at'] ?? ''
									),
									false,
									(string) ($currentLocale ?? 'it')
								) ?>
							</time>

							<?php if ($isUnread): ?>

								<span class="notification-unread-label">
									<?= $escape(
										$t(
											'notifications.page.unread'
										)
									) ?>
								</span>

							<?php endif; ?>

						</div>

					</div>

					<div class="notification-actions">

						<a
							href="<?= $escape($notificationUrl) ?>"
							class="notification-open"
							aria-label="<?= $escape(
								$t(
									'notifications.page.actions.open_aria'
								)
							) ?>"
						>
							<span>
								<?= $escape(
									$t(
										'notifications.page.actions.open'
									)
								) ?>
							</span>

							<i
								class="fas fa-chevron-right"
								aria-hidden="true"
							></i>
						</a>

						<?php if ($notificationUuid !== ''): ?>

							<form
								method="post"
								action="/notifications/<?= rawurlencode(
									$notificationUuid
								) ?>/delete"
							>
								<button
									type="submit"
									class="notification-delete"
									aria-label="<?= $escape(
										$t(
											'notifications.page.actions.delete_aria'
										)
									) ?>"
								>
									<i
										class="fas fa-trash"
										aria-hidden="true"
									></i>

									<span>
										<?= $escape(
											$t(
												'notifications.page.actions.delete'
											)
										) ?>
									</span>
								</button>
							</form>

						<?php endif; ?>

					</div>

				</article>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</section>
