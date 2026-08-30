<?php
declare(strict_types=1);

$title = trim(
	(string) ($title ?? '')
);

$repository = trim(
	(string) ($repository_name ?? '')
);

$releases = is_array($releases ?? null)
	? $releases
	: [];

$repositoryUrl = $repository !== ''
	? 'https://github.com/' . $repository
	: '';

$releaseTypes = [
	'stable' => [
		'label' => $t(
			'blocks.developer.github_release.state.stable'
		),
		'icon' => 'fa-solid fa-tag',
	],
	'beta' => [
		'label' => $t(
			'blocks.developer.github_release.state.beta'
		),
		'icon' => 'fa-solid fa-flask',
	],
	'nightly' => [
		'label' => $t(
			'blocks.developer.github_release.state.nightly'
		),
		'icon' => 'fa-solid fa-moon',
	],
];

$hasReleases = false;

foreach (array_keys($releaseTypes) as $type) {
	if (
		isset($releases[$type])
		&& is_array($releases[$type])
		&& $releases[$type] !== []
	) {
		$hasReleases = true;
		break;
	}
}
?>

<div class="mv-widget mv-github-release-widget">

	<?php if ($title !== ''): ?>

		<header class="mv-github-release-header">

			<h3>
				<?= htmlspecialchars(
					$title,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h3>

		</header>

	<?php endif; ?>

	<?php if ($hasReleases): ?>

		<div class="mv-github-release-list">

			<?php foreach ($releaseTypes as $type => $config): ?>

				<?php
				$release = isset($releases[$type])
					&& is_array($releases[$type])
					? $releases[$type]
					: [];

				if ($release === []) {
					continue;
				}

				$releaseName = trim(
					(string) (
						$release['name']
						?? $release['tag_name']
						?? ''
					)
				);

				$tagName = trim(
					(string) (
						$release['tag_name']
						?? ''
					)
				);

				$releaseUrl = trim(
					(string) (
						$release['html_url']
						?? ''
					)
				);

				$publishedAt = trim(
					(string) (
						$release['published_at']
						?? ''
					)
				);

				$formattedDate = '';

				if ($publishedAt !== '') {
					$timestamp = strtotime(
						$publishedAt
					);

					if ($timestamp !== false) {
						$formattedDate = date(
							'd/m/Y',
							$timestamp
						);
					}
				}
				?>

				<div
					class="mv-github-release-card mv-github-release-card-<?= htmlspecialchars(
						$type,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

					<div class="mv-github-release-icon">

						<i
							class="<?= htmlspecialchars(
								$config['icon'],
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							aria-hidden="true"
						></i>

					</div>

					<div class="mv-github-release-content">

						<div class="mv-github-release-topline">

							<?php if ($releaseName !== ''): ?>

								<strong>
									<?= htmlspecialchars(
										$releaseName,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

							<span
								class="mv-github-release-state mv-github-release-state-<?= htmlspecialchars(
									$type,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
							>
								<?= htmlspecialchars(
									$config['label'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						</div>

						<?php if (
							$tagName !== ''
							|| $formattedDate !== ''
						): ?>

							<div class="mv-github-release-meta">

								<?php if ($tagName !== ''): ?>

									<span>

										<i
											class="fa-solid fa-code-branch"
											aria-hidden="true"
										></i>

										<?= htmlspecialchars(
											$tagName,
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

								<?php if ($releaseUrl !== ''): ?>

									<a
										class="mv-github-release-link"
										href="<?= htmlspecialchars(
											$releaseUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										target="_blank"
										rel="noopener noreferrer"
									>

										<span>
											<?= htmlspecialchars(
												$t(
													'blocks.developer.github_release.view'
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

						<?php endif; ?>

					</div>

				</div>

			<?php endforeach; ?>

		</div>

	<?php else: ?>

		<div class="mv-github-release-empty">

			<i
				class="fa-solid fa-tag"
				aria-hidden="true"
			></i>

			<p>
				<?= htmlspecialchars(
					$t(
						'blocks.developer.github_release.empty'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<?php if ($repositoryUrl !== ''): ?>

				<a
					href="<?= htmlspecialchars(
						$repositoryUrl . '/releases',
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_release.view'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

			<?php endif; ?>

		</div>

	<?php endif; ?>

</div>
