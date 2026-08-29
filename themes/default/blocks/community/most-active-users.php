<?php
declare(strict_types=1);

$title = trim(
	(string) (
		$title
		?? $t(
			'blocks.community.most_active_users.default_title'
		)
	)
);

$users = is_array($users ?? null)
	? $users
	: [];

$showAvatar = (bool) ($show_avatar ?? true);
$showStats = (bool) ($show_stats ?? true);

$blockWidth = (int) ($block['width'] ?? 12);

if (!in_array(
	$blockWidth,
	[3, 4, 6, 8, 9, 12],
	true
)) {
	$blockWidth = 12;
}

$widthClass = 'mv-block-width-' . $blockWidth;
?>

<div
	class="mv-widget mv-most-active-users-widget <?= htmlspecialchars(
		$widthClass,
		ENT_QUOTES,
		'UTF-8'
	) ?>"
>

	<header class="mv-most-active-users-header">

		<h3>
			<?= htmlspecialchars(
				$title,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h3>

	</header>

	<?php if ($users !== []): ?>

		<ul class="mv-most-active-users-list">

			<?php foreach ($users as $user): ?>

				<?php
				if (!is_array($user)) {
					continue;
				}

				$username = trim(
					(string) ($user['username'] ?? '')
				);

				if ($username === '') {
					continue;
				}

				$avatarUrl = trim(
					(string) ($user['avatar_url'] ?? '')
				);

				$hasAvatar =
					$showAvatar
					&& !empty($user['show_avatar'])
					&& $avatarUrl !== '';

				$pingCount = max(
					0,
					(int) ($user['ping_count'] ?? 0)
				);

				$pongCount = max(
					0,
					(int) ($user['pong_count'] ?? 0)
				);

				$upvoteCount = max(
					0,
					(int) ($user['upvote_count'] ?? 0)
				);

				$downvoteCount = max(
					0,
					(int) ($user['downvote_count'] ?? 0)
				);

				$ircLines = max(
					0,
					(int) ($user['irc_lines'] ?? 0)
				);

				$voteCount =
					$upvoteCount
					+ $downvoteCount;

				$siteActivity = [];

				if ($pingCount > 0) {
					$siteActivity[] =
						$pingCount . ' Ping';
				}

				if ($pongCount > 0) {
					$siteActivity[] =
						$pongCount . ' Pong';
				}

				if ($voteCount > 0) {
					$siteActivity[] =
						$voteCount
						. ' '
						. $t(
							$voteCount === 1
								? 'blocks.community.most_active_users.vote'
								: 'blocks.community.most_active_users.votes'
						);
				}

				$siteActivityLabel = $siteActivity !== []
					? implode(
						' · ',
						$siteActivity
					)
					: $t(
						'blocks.community.most_active_users.web_inactive'
					);

				$ircActivityLabel = $ircLines > 0
					? (
						$ircLines
						. ' '
						. $t(
							$ircLines === 1
								? 'blocks.community.most_active_users.irc_message'
								: 'blocks.community.most_active_users.irc_messages'
						)
					)
					: $t(
						'blocks.community.most_active_users.irc_inactive'
					);
				?>

				<li class="mv-most-active-users-item">

					<a
						class="mv-most-active-users-user"
						href="/profile/<?= rawurlencode(
							$username
						) ?>"
					>

						<div class="mv-most-active-users-avatar">

							<?php if ($hasAvatar): ?>

								<img
									src="<?= htmlspecialchars(
										$avatarUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									alt=""
									loading="lazy"
								>

							<?php else: ?>

								<span>
									<?= htmlspecialchars(
										mb_strtoupper(
											mb_substr(
												$username,
												0,
												1
											)
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

						</div>

						<div class="mv-most-active-users-body">

							<div class="mv-most-active-users-name">
								<?= htmlspecialchars(
									$username,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</div>

							<?php if ($showStats): ?>

								<div class="mv-most-active-users-stats">

									<div class="mv-most-active-users-site-stats">
										<?= htmlspecialchars(
											$siteActivityLabel,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</div>

									<div class="mv-most-active-users-irc-stats">
										<?= htmlspecialchars(
											$ircActivityLabel,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</div>

								</div>

							<?php endif; ?>

						</div>

						<i
							class="fa-solid fa-chevron-right"
							aria-hidden="true"
						></i>

					</a>

				</li>

			<?php endforeach; ?>

		</ul>

	<?php else: ?>

		<p class="mv-most-active-users-empty">
			<?= htmlspecialchars(
				$t(
					'blocks.community.most_active_users.empty'
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	<?php endif; ?>

</div>
