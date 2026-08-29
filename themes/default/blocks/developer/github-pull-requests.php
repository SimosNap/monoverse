<?php
declare(strict_types=1);

$title = trim(
	(string) ($title ?? '')
);

$repository = trim(
	(string) ($repository ?? '')
);

$state = trim(
	(string) ($state ?? 'open')
);

$pullRequests = is_array(
	$pullRequests ?? null
)
	? $pullRequests
	: [];

$stateLabel = match ($state) {
	'closed' => $t(
		'blocks.developer.github_pull_requests.state.closed'
	),
	'all' => $t(
		'blocks.developer.github_pull_requests.state.all'
	),
	default => $t(
		'blocks.developer.github_pull_requests.state.open'
	),
};

$repositoryUrl = $repository !== ''
	? 'https://github.com/' . $repository
	: '';

$pullRequestsUrl = $repositoryUrl !== ''
	? $repositoryUrl
		. '/pulls'
		. ($state !== 'open'
			? '?q=is%3Apr+is%3A' . rawurlencode($state)
			: '')
	: '';
?>

<div class="mv-widget mv-github-pull-requests-widget">

	<?php if ($title !== ''): ?>

		<header class="mv-github-pull-requests-header">

			<h3>
				<?= htmlspecialchars(
					$title,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h3>

		</header>

	<?php endif; ?>

	<div class="mv-github-pull-requests-summary">

		<div class="mv-github-pull-requests-summary-icon">

			<i
				class="fa-solid fa-code-pull-request"
				aria-hidden="true"
			></i>

		</div>

		<div>

			<strong>
				<?= count($pullRequests) ?>
			</strong>

			<span>
				<?= htmlspecialchars(
					$stateLabel,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

		</div>

	</div>

	<?php if ($pullRequests !== []): ?>

		<div class="mv-github-pull-requests-list">

			<?php foreach ($pullRequests as $pullRequest): ?>

				<?php
				$number = (int) (
					$pullRequest['number']
						?? 0
				);

				$pullTitle = trim(
					(string) (
						$pullRequest['title']
							?? ''
					)
				);

				$url = trim(
					(string) (
						$pullRequest['url']
							?? ''
					)
				);

				$user = trim(
					(string) (
						$pullRequest['user']
							?? ''
					)
				);

				$updatedAt = trim(
					(string) (
						$pullRequest['updated_at']
							?? ''
					)
				);

				$formattedDate = '';

				if ($updatedAt !== '') {
					$timestamp = strtotime(
						$updatedAt
					);

					if ($timestamp !== false) {
						$formattedDate = date(
							'd/m/Y',
							$timestamp
						);
					}
				}
				?>

				<article class="mv-github-pull-request">

					<div class="mv-github-pull-request-title">

						<?php if ($number > 0): ?>

							<span>
								#<?= $number ?>
							</span>

						<?php endif; ?>

						<?php if ($url !== ''): ?>

							<a
								href="<?= htmlspecialchars(
									$url,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								target="_blank"
								rel="noopener noreferrer"
							>
								<?= htmlspecialchars(
									$pullTitle,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>

						<?php else: ?>

							<strong>
								<?= htmlspecialchars(
									$pullTitle,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

						<?php endif; ?>

					</div>

					<?php if (
						$user !== ''
						|| $formattedDate !== ''
					): ?>

						<div class="mv-github-pull-request-meta">

							<?php if ($user !== ''): ?>

								<span>
									<i
										class="fa-regular fa-user"
										aria-hidden="true"
									></i>

									<?= htmlspecialchars(
										$user,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

							<?php if ($formattedDate !== ''): ?>

								<span>
									<i
										class="fa-regular fa-calendar"
										aria-hidden="true"
									></i>

									<?= htmlspecialchars(
										$formattedDate,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

						</div>

					<?php endif; ?>

				</article>

			<?php endforeach; ?>

		</div>

	<?php else: ?>

		<div class="mv-github-pull-requests-empty">

			<i
				class="fa-solid fa-code-pull-request"
				aria-hidden="true"
			></i>

			<p>
				<?= htmlspecialchars(
					$t(
						'blocks.developer.github_pull_requests.empty'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	<?php endif; ?>

	<?php if ($pullRequestsUrl !== ''): ?>

		<a
			class="mv-github-pull-requests-link"
			href="<?= htmlspecialchars(
				$pullRequestsUrl,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			target="_blank"
			rel="noopener noreferrer"
		>
			<span>
				<?= htmlspecialchars(
					$t(
						'blocks.developer.github_pull_requests.view_all'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

			<i
				class="fa-solid fa-arrow-up-right-from-square"
				aria-hidden="true"
			></i>
		</a>

	<?php endif; ?>

</div>
