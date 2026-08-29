<?php
declare(strict_types=1);

$title = trim(
	(string) ($title ?? '')
);

$dashboard = is_array($dashboard ?? null)
	? $dashboard
	: [];

$repository = is_array($dashboard['repository'] ?? null)
	? $dashboard['repository']
	: [];

$release = is_array($dashboard['release'] ?? null)
	? $dashboard['release']
	: [];

$languages = is_array($dashboard['languages'] ?? null)
	? $dashboard['languages']
	: [];

$commits = is_array($dashboard['commits'] ?? null)
	? $dashboard['commits']
	: [];

$pullRequests = is_array($dashboard['pull_requests'] ?? null)
	? $dashboard['pull_requests']
	: [];

$issues = is_array($dashboard['issues'] ?? null)
	? $dashboard['issues']
	: [];

$showRelease = (bool) ($show_release ?? true);
$showLanguages = (bool) ($show_languages ?? true);
$showCommits = (bool) ($show_commits ?? true);
$showPullRequests = (bool) ($show_pull_requests ?? true);
$showIssues = (bool) ($show_issues ?? true);

$repositoryUrl = trim(
	(string) ($repository['html_url'] ?? '')
);

$repositoryName = trim(
	(string) ($repository['full_name'] ?? '')
);

$branchParameter = $repositoryName !== ''
	? 'github_branch_' . substr(
		hash(
			'sha256',
			strtolower($repositoryName)
		),
		0,
		12
	)
	: 'github_branch';

$description = trim(
	(string) ($repository['description'] ?? '')
);

$branch = trim(
	(string) (
		$dashboard['branch']
		?? $repository['default_branch']
		?? ''
	)
);

$branches = is_array($dashboard['branches'] ?? null)
	? $dashboard['branches']
	: [];

$visibility = trim(
	(string) ($repository['visibility'] ?? '')
);

$license = is_array($repository['license'] ?? null)
	? $repository['license']
	: [];

$licenseName = trim(
	(string) (
		$license['spdx_id']
		?? $license['name']
		?? ''
	)
);

$owner = is_array($repository['owner'] ?? null)
	? $repository['owner']
	: [];

$ownerAvatar = trim(
	(string) ($owner['avatar_url'] ?? '')
);

$displayTitle = $title !== ''
	? $title
	: (
		$repositoryName !== ''
			? $repositoryName
			: $t(
				'blocks.developer.github_repository.default_title'
			)
	);
?>

<div class="mv-widget mv-github-widget">

	<?php if ($repository === []): ?>

		<div class="mv-widget-empty">
			<?= htmlspecialchars(
				$t(
					'blocks.developer.github_repository.unavailable'
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php else: ?>

		<header class="mv-github-header">

			<div class="mv-github-title">

				<?php if ($ownerAvatar !== ''): ?>

					<img
						class="mv-github-owner-avatar"
						src="<?= htmlspecialchars(
							$ownerAvatar,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						alt=""
						loading="lazy"
					>

				<?php else: ?>

					<i
						class="fa-brands fa-github"
						aria-hidden="true"
					></i>

				<?php endif; ?>

				<div>

					<h3>
						<?= htmlspecialchars(
							$displayTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h3>

					<?php if ($description !== ''): ?>

						<p>
							<?= htmlspecialchars(
								$description,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					<?php endif; ?>

					<div class="mv-github-meta">

						<?php if ($branch !== ''): ?>

							<span>
								<i
									class="fa-solid fa-code-branch"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$branch,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

						<?php if ($visibility !== ''): ?>

							<span>
								<?= htmlspecialchars(
									ucfirst($visibility),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

						<?php if ($licenseName !== ''): ?>

							<span>
								<i
									class="fa-regular fa-file-lines"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$licenseName,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

			</div>

			<?php if ($repositoryUrl !== ''): ?>

				<a
					href="<?= htmlspecialchars(
						$repositoryUrl,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					target="_blank"
					rel="noopener"
					class="mv-github-open"
				>
					<i
						class="fa-brands fa-github"
						aria-hidden="true"
					></i>

					GitHub
				</a>

			<?php endif; ?>

			<?php if ($branches !== []): ?>

				<form
					class="mv-github-branch-form"
					method="get"
				>

					<?php foreach ($_GET as $key => $value): ?>

						<?php if ($key === $branchParameter): ?>
							<?php continue; ?>
						<?php endif; ?>

						<input
							type="hidden"
							name="<?= htmlspecialchars(
								(string) $key,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							value="<?= htmlspecialchars(
								(string) $value,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

					<?php endforeach; ?>

					<label
						class="mv-github-branch-label"
						for="mv-github-branch"
					>
						<i
							class="fa-solid fa-code-branch"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t(
								'blocks.developer.github_repository.branch'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<div class="mv-github-header-actions">

						<select
							id="mv-github-branch"
							name="<?= htmlspecialchars(
								$branchParameter,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							class="mv-github-branch-select"
							onchange="this.form.submit()"
						>

							<?php foreach ($branches as $branchItem): ?>

								<?php
								$branchName = trim(
									(string) (
										$branchItem['name']
										?? ''
									)
								);

								if ($branchName === '') {
									continue;
								}
								?>

								<option
									value="<?= htmlspecialchars(
										$branchName,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									<?= $branchName === $branch
										? 'selected'
										: '' ?>
								>
									<?= htmlspecialchars(
										$branchName,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</option>

							<?php endforeach; ?>

						</select>

					</div>

				</form>

			<?php endif; ?>

		</header>

		<div class="mv-github-stats">

			<div>
				<strong>
					<?= (int) ($repository['stars'] ?? 0) ?>
				</strong>

				<span>
					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.stats.stars'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>
			</div>

			<div>
				<strong>
					<?= (int) ($repository['forks'] ?? 0) ?>
				</strong>

				<span>
					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.stats.forks'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>
			</div>

			<div>
				<strong>
					<?= (int) ($repository['watchers'] ?? 0) ?>
				</strong>

				<span>
					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.stats.watchers'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>
			</div>

			<div>
				<strong>
					<?= (int) ($repository['open_issues'] ?? 0) ?>
				</strong>

				<span>
					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.stats.issues'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>
			</div>

		</div>

		<?php if (
			$showLanguages
			&& $languages !== []
		): ?>

			<section class="mv-github-section">

				<h4>
					<i
						class="fa-solid fa-code"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.sections.languages'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</h4>

				<div class="mv-github-languages">

					<?php foreach ($languages as $item): ?>

						<?php
						$languageName = trim(
							(string) ($item['name'] ?? '')
						);

						$percent = (float) (
							$item['percent']
							?? 0
						);
						?>

						<div class="mv-github-language">

							<div class="mv-github-language-head">

								<span>
									<?= htmlspecialchars(
										$languageName,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<strong>
									<?= number_format(
										$percent,
										1,
										',',
										'.'
									) ?>%
								</strong>

							</div>

							<div class="mv-github-language-bar">

								<span
									style="width: <?= max(
										0,
										min(
											100,
											$percent
										)
									) ?>%;"
								></span>

							</div>

						</div>

					<?php endforeach; ?>

				</div>

			</section>

		<?php endif; ?>

		<?php if (
			$showRelease
			&& $release !== []
		): ?>

			<section class="mv-github-section">

				<h4>
					<i
						class="fa-solid fa-tag"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.sections.latest_release'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</h4>

				<a
					class="mv-github-release"
					href="<?= htmlspecialchars(
						(string) ($release['html_url'] ?? ''),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					target="_blank"
					rel="noopener"
				>

					<div>

						<strong>
							<?= htmlspecialchars(
								(string) (
									$release['name']
									?? $release['tag_name']
									?? ''
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<?php if (
							!empty($release['tag_name'])
						): ?>

							<span>
								<?= htmlspecialchars(
									(string) $release['tag_name'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

					<i
						class="fa-solid fa-chevron-right"
						aria-hidden="true"
					></i>

				</a>

			</section>

		<?php endif; ?>

		<?php if (
			$showCommits
			&& $commits !== []
		): ?>

			<section class="mv-github-section">

				<h4>
					<i
						class="fa-solid fa-code-commit"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.sections.latest_commits'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</h4>

				<div class="mv-github-commits">

					<?php foreach ($commits as $commit): ?>

						<?php
						$commitUrl = trim(
							(string) ($commit['url'] ?? '')
						);

						$authorName = trim(
							(string) (
								$commit['author_login']
								?? $commit['author_name']
								?? ''
							)
						);

						$avatar = trim(
							(string) (
								$commit['author_avatar']
								?? ''
							)
						);

						$filesChanged = (int) (
							$commit['files_changed']
							?? 0
						);

						$additions = (int) (
							$commit['additions']
							?? 0
						);

						$deletions = (int) (
							$commit['deletions']
							?? 0
						);
						?>

						<article class="mv-github-commit">

							<div class="mv-github-commit-avatar">

								<?php if ($avatar !== ''): ?>

									<img
										src="<?= htmlspecialchars(
											$avatar,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										alt=""
										loading="lazy"
									>

								<?php else: ?>

									<i
										class="fa-solid fa-user"
										aria-hidden="true"
									></i>

								<?php endif; ?>

							</div>

							<div class="mv-github-commit-body">

								<?php if ($commitUrl !== ''): ?>

									<a
										class="mv-github-commit-message"
										href="<?= htmlspecialchars(
											$commitUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										target="_blank"
										rel="noopener"
									>
										<?= htmlspecialchars(
											(string) (
												$commit['message']
												?? ''
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>

								<?php else: ?>

									<strong>
										<?= htmlspecialchars(
											(string) (
												$commit['message']
												?? ''
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</strong>

								<?php endif; ?>

								<small>

									<?php if ($authorName !== ''): ?>

										<?= htmlspecialchars(
											$authorName,
											ENT_QUOTES,
											'UTF-8'
										) ?>

										·

									<?php endif; ?>

									<?= htmlspecialchars(
										(string) (
											$commit['short_sha']
											?? ''
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>

									<?php if (
										!empty($commit['author_date'])
									): ?>

										·

										<?= htmlspecialchars(
											(string) $commit['author_date'],
											ENT_QUOTES,
											'UTF-8'
										) ?>

									<?php endif; ?>

								</small>

								<?php if (
									$filesChanged > 0
									|| $additions > 0
									|| $deletions > 0
								): ?>

									<div class="mv-github-commit-stats">

										<?php if ($filesChanged > 0): ?>

											<span>
												<i
													class="fa-regular fa-file-code"
													aria-hidden="true"
												></i>

												<?= htmlspecialchars(
													$t(
														$filesChanged === 1
															? 'blocks.developer.github_repository.files.one'
															: 'blocks.developer.github_repository.files.many',
														[
															'count' => $filesChanged,
														]
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</span>

										<?php endif; ?>

										<?php if ($additions > 0): ?>

											<span class="is-additions">
												+<?= $additions ?>
											</span>

										<?php endif; ?>

										<?php if ($deletions > 0): ?>

											<span class="is-deletions">
												-<?= $deletions ?>
											</span>

										<?php endif; ?>

									</div>

								<?php endif; ?>

							</div>

						</article>

					<?php endforeach; ?>

				</div>

			</section>

		<?php endif; ?>

		<?php if (
			$showPullRequests
			&& $pullRequests !== []
		): ?>

			<section class="mv-github-section">

				<h4>
					<i
						class="fa-solid fa-code-pull-request"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.sections.open_pull_requests'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</h4>

				<div class="mv-github-items">

					<?php foreach ($pullRequests as $pullRequest): ?>

						<a
							class="mv-github-item"
							href="<?= htmlspecialchars(
								(string) (
									$pullRequest['url']
									?? ''
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							target="_blank"
							rel="noopener"
						>

							<span class="mv-github-item-number">
								#<?= (int) (
									$pullRequest['number']
									?? 0
								) ?>
							</span>

							<strong>
								<?= htmlspecialchars(
									(string) (
										$pullRequest['title']
										?? ''
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

							<i
								class="fa-solid fa-chevron-right"
								aria-hidden="true"
							></i>

						</a>

					<?php endforeach; ?>

				</div>

			</section>

		<?php endif; ?>

		<?php if (
			$showIssues
			&& $issues !== []
		): ?>

			<section class="mv-github-section">

				<h4>
					<i
						class="fa-regular fa-circle-dot"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t(
							'blocks.developer.github_repository.sections.open_issues'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</h4>

				<div class="mv-github-items">

					<?php foreach ($issues as $issue): ?>

						<a
							class="mv-github-item"
							href="<?= htmlspecialchars(
								(string) (
									$issue['url']
									?? ''
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							target="_blank"
							rel="noopener"
						>

							<span class="mv-github-item-number">
								#<?= (int) (
									$issue['number']
									?? 0
								) ?>
							</span>

							<strong>
								<?= htmlspecialchars(
									(string) (
										$issue['title']
										?? ''
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

							<i
								class="fa-solid fa-chevron-right"
								aria-hidden="true"
							></i>

						</a>

					<?php endforeach; ?>

				</div>

			</section>

		<?php endif; ?>

	<?php endif; ?>

</div>
