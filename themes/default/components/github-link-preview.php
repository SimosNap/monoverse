<?php
declare(strict_types=1);

$github = is_array($link['github'] ?? null)
	? $link['github']
	: [];

$url = trim(
	(string) ($link['url'] ?? '')
);

$kind = trim(
	(string) ($github['kind'] ?? 'repository')
);

$fullName = trim(
	(string) ($github['full_name'] ?? '')
);

$ref = trim(
	(string) ($github['ref'] ?? '')
);

$path = trim(
	(string) ($github['path'] ?? '')
);

$file = is_array($github['file'] ?? null)
	? $github['file']
	: [];

$snippet = is_array($file['snippet'] ?? null)
	? $file['snippet']
	: [];

$lineStart = (int) (
	$file['line_start']
	?? $github['line_start']
	?? 0
);

$lineEnd = (int) (
	$file['line_end']
	?? $github['line_end']
	?? 0
);

$kindLabel = match ($kind) {
	'blob' => $t('github.kind.code'),
	'commit' => $t('github.kind.commit'),
	'pull_request' => $t('github.kind.pull_request'),
	'issue' => $t('github.kind.issue'),
	'discussion' => $t('github.kind.discussion'),
	'release' => $t('github.kind.release'),
	'gist' => $t('github.kind.gist'),
	'tree' => $t('github.kind.tree'),
	'compare' => $t('github.kind.compare'),
	'workflow_run' => $t('github.kind.actions'),
	default => $t('github.kind.repository'),
};
?>

<div class="ping-github-preview">

	<a
		class="ping-github-header"
		href="<?= htmlspecialchars(
			$url,
			ENT_QUOTES,
			'UTF-8'
		) ?>"
		target="_blank"
		rel="noopener noreferrer"
	>

		<div class="ping-github-provider">

			<i
				class="fa-brands fa-github"
				aria-hidden="true"
			></i>

			<span>
				GitHub
			</span>

			<strong>
				<?= htmlspecialchars(
					$kindLabel,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</strong>

		</div>

		<i
			class="fa-solid fa-arrow-up-right-from-square"
			aria-hidden="true"
		></i>

	</a>

	<?php if ($fullName !== ''): ?>

		<div class="ping-github-repository">

			<i
				class="fa-solid fa-code-branch"
				aria-hidden="true"
			></i>

			<strong>
				<?= htmlspecialchars(
					$fullName,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</strong>

			<?php if ($ref !== ''): ?>

				<span>
					<?= htmlspecialchars(
						$ref,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php if (
		$kind === 'blob'
		&& $file !== []
	): ?>

		<div class="ping-github-file">

			<div class="ping-github-file-heading">

				<div class="ping-github-file-path">

					<i
						class="fa-regular fa-file-code"
						aria-hidden="true"
					></i>

					<span>
						<?= htmlspecialchars(
							(string) (
								$file['path']
								?? $path
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				</div>

				<?php if ($lineStart > 0): ?>

					<span class="ping-github-lines">

						<?= $lineStart === $lineEnd
							? 'L' . $lineStart
							: 'L'
								. $lineStart
								. '–L'
								. $lineEnd ?>

					</span>

				<?php endif; ?>

			</div>

			<?php if ($snippet !== []): ?>

				<div class="ping-github-code">

					<?php foreach ($snippet as $line): ?>

						<?php
						$number = (int) (
							$line['number']
							?? 0
						);

						$content = (string) (
							$line['content']
							?? ''
						);
						?>

						<div class="ping-github-code-line">

							<span class="ping-github-line-number">
								<?= $number ?>
							</span>

							<code><?= htmlspecialchars(
								$content,
								ENT_QUOTES,
								'UTF-8'
							) ?></code>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'commit'
		&& !empty($github['commit'])
	): ?>

		<?php
		$commit = is_array($github['commit'] ?? null)
			? $github['commit']
			: [];

		$commitMessage = trim(
			(string) (
				$commit['message']
				?? ''
			)
		);

		$commitSha = trim(
			(string) (
				$commit['short_sha']
				?? ''
			)
		);

		$commitAuthorLogin = trim(
			(string) (
				$commit['author_login']
				?? ''
			)
		);

		$commitAuthorName = trim(
			(string) (
				$commit['author_name']
				?? ''
			)
		);

		$commitAuthor = $commitAuthorLogin !== ''
			? $commitAuthorLogin
			: $commitAuthorName;

		$commitAvatar = trim(
			(string) (
				$commit['author_avatar']
				?? ''
			)
		);

		$commitDate = trim(
			(string) (
				$commit['author_date']
				?? ''
			)
		);

		$commitStats = is_array(
			$commit['stats']
				?? null
		)
			? $commit['stats']
			: [];

		$commitFiles = is_array(
			$commit['files']
				?? null
		)
			? $commit['files']
			: [];

		$formattedCommitDate = '';

		if ($commitDate !== '') {
			$timestamp = strtotime($commitDate);

			if ($timestamp !== false) {
				$formattedCommitDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}
		?>

		<div class="ping-github-commit">

			<div class="ping-github-commit-heading">

				<div class="ping-github-commit-author">

					<?php if ($commitAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$commitAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-commit-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($commitAuthor !== ''): ?>

							<strong>
								<?= htmlspecialchars(
									$commitAuthor,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

						<?php endif; ?>

						<?php if ($formattedCommitDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedCommitDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<?php if ($commitSha !== ''): ?>

					<span class="ping-github-commit-sha">
						<?= htmlspecialchars(
							$commitSha,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if ($commitMessage !== ''): ?>

				<div class="ping-github-commit-message">
					<?= nl2br(
						htmlspecialchars(
							$commitMessage,
							ENT_QUOTES,
							'UTF-8'
						)
					) ?>
				</div>

			<?php endif; ?>

			<div class="ping-github-commit-stats">

				<?php $commitFileCount = count($commitFiles); ?>

				<span class="is-files">
					<i
						class="fa-regular fa-file-code"
						aria-hidden="true"
					></i>

					<?= $commitFileCount ?>
					<?= htmlspecialchars(
						$t(
							$commitFileCount === 1
								? 'github.counts.file'
								: 'github.counts.files'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span class="is-additions">
					+<?= (int) (
						$commitStats['additions']
						?? 0
					) ?>
				</span>

				<span class="is-deletions">
					-<?= (int) (
						$commitStats['deletions']
						?? 0
					) ?>
				</span>

			</div>

			<?php if ($commitFiles !== []): ?>

				<div class="ping-github-commit-files">

					<?php foreach (
						array_slice(
							$commitFiles,
							0,
							5
						)
						as $commitFile
					): ?>

						<?php
						$filename = trim(
							(string) (
								$commitFile['filename']
								?? ''
							)
						);

						if ($filename === '') {
							continue;
						}
						?>

						<div class="ping-github-commit-file">

							<div class="ping-github-commit-file-heading">

								<span class="ping-github-commit-file-name">
									<?= htmlspecialchars(
										$filename,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<span class="ping-github-commit-file-stats">

									<strong class="is-additions">
										+<?= (int) (
											$commitFile['additions']
											?? 0
										) ?>
									</strong>

									<strong class="is-deletions">
										-<?= (int) (
											$commitFile['deletions']
											?? 0
										) ?>
									</strong>

								</span>

							</div>

							<?php
							$patch = (string) (
								$commitFile['patch']
								?? ''
							);
							?>

							<?php if ($patch !== ''): ?>

								<div class="ping-github-commit-diff">

									<?php foreach (
										preg_split(
											'/\R/u',
											$patch
										) ?: []
										as $patchLine
									): ?>

										<?php
										$patchLine = (string) $patchLine;

										$lineClass = 'is-context';

										if (
											str_starts_with(
												$patchLine,
												'@@'
											)
										) {
											$lineClass = 'is-hunk';
										} elseif (
											str_starts_with(
												$patchLine,
												'+'
											)
											&& !str_starts_with(
												$patchLine,
												'+++'
											)
										) {
											$lineClass = 'is-addition';
										} elseif (
											str_starts_with(
												$patchLine,
												'-'
											)
											&& !str_starts_with(
												$patchLine,
												'---'
											)
										) {
											$lineClass = 'is-deletion';
										}
										?>

										<div class="ping-github-commit-diff-line <?= htmlspecialchars(
											$lineClass,
											ENT_QUOTES,
											'UTF-8'
										) ?>">
											<code><?= htmlspecialchars(
												$patchLine,
												ENT_QUOTES,
												'UTF-8'
											) ?></code>
										</div>

									<?php endforeach; ?>

								</div>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'pull_request'
		&& !empty($github['pull_request'])
	): ?>

		<?php
		$pullRequest = is_array(
			$github['pull_request']
				?? null
		)
			? $github['pull_request']
			: [];

		$prNumber = (int) (
			$pullRequest['number']
				?? 0
		);

		$prTitle = trim(
			(string) (
				$pullRequest['title']
					?? ''
			)
		);

		$prBody = trim(
			(string) (
				$pullRequest['body']
					?? ''
			)
		);

		$prState = trim(
			(string) (
				$pullRequest['state']
					?? 'open'
			)
		);

		$prDraft = (bool) (
			$pullRequest['draft']
				?? false
		);

		$prAuthor = is_array(
			$pullRequest['author']
				?? null
		)
			? $pullRequest['author']
			: [];

		$prAuthorLogin = trim(
			(string) (
				$prAuthor['login']
					?? ''
			)
		);

		$prAuthorAvatar = trim(
			(string) (
				$prAuthor['avatar_url']
					?? ''
			)
		);

		$prAuthorUrl = trim(
			(string) (
				$prAuthor['html_url']
					?? ''
			)
		);

		$prHead = is_array(
			$pullRequest['head']
				?? null
		)
			? $pullRequest['head']
			: [];

		$prBase = is_array(
			$pullRequest['base']
				?? null
		)
			? $pullRequest['base']
			: [];

		$prHeadRef = trim(
			(string) (
				$prHead['ref']
					?? ''
			)
		);

		$prBaseRef = trim(
			(string) (
				$prBase['ref']
					?? ''
			)
		);

		$prCreatedAt = trim(
			(string) (
				$pullRequest['created_at']
					?? ''
			)
		);

		$formattedPrDate = '';

		if ($prCreatedAt !== '') {
			$timestamp = strtotime(
				$prCreatedAt
			);

			if ($timestamp !== false) {
				$formattedPrDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}

		$prLabels = is_array(
			$pullRequest['labels']
				?? null
		)
			? $pullRequest['labels']
			: [];

		$prFiles = is_array(
			$pullRequest['files']
				?? null
		)
			? $pullRequest['files']
			: [];

		$prStateLabel = match (true) {
			$prDraft => $t('github.state.draft'),
			$prState === 'merged' => $t('github.state.merged'),
			$prState === 'closed' => $t('github.state.closed'),
			default => $t('github.state.open'),
		};

		$prStateClass = match (true) {
			$prDraft => 'is-draft',
			$prState === 'merged' => 'is-merged',
			$prState === 'closed' => 'is-closed',
			default => 'is-open',
		};
		?>

		<div class="ping-github-pr">

			<div class="ping-github-pr-heading">

				<div class="ping-github-pr-author">

					<?php if ($prAuthorAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$prAuthorAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-pr-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($prAuthorLogin !== ''): ?>

							<?php if ($prAuthorUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$prAuthorUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$prAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$prAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedPrDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedPrDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<span class="ping-github-pr-state <?= htmlspecialchars(
					$prStateClass,
					ENT_QUOTES,
					'UTF-8'
				) ?>">
					<?= htmlspecialchars(
						$prStateLabel,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($prTitle !== ''): ?>

				<div class="ping-github-pr-title">

					<?php if ($prNumber > 0): ?>

						<span>
							#<?= $prNumber ?>
						</span>

					<?php endif; ?>

					<strong>
						<?= htmlspecialchars(
							$prTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

				</div>

			<?php endif; ?>

			<?php if (
				$prHeadRef !== ''
				|| $prBaseRef !== ''
			): ?>

				<div class="ping-github-pr-branches">

					<i
						class="fa-solid fa-code-branch"
						aria-hidden="true"
					></i>

					<?php if ($prHeadRef !== ''): ?>

						<code>
							<?= htmlspecialchars(
								$prHeadRef,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</code>

					<?php endif; ?>

					<i
						class="fa-solid fa-arrow-right"
						aria-hidden="true"
					></i>

					<?php if ($prBaseRef !== ''): ?>

						<code>
							<?= htmlspecialchars(
								$prBaseRef,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</code>

					<?php endif; ?>

				</div>

			<?php endif; ?>

			<?php if ($prLabels !== []): ?>

				<div class="ping-github-pr-labels">

					<?php foreach ($prLabels as $label): ?>

						<?php
						$labelName = trim(
							(string) (
								$label['name']
									?? ''
							)
						);

						if ($labelName === '') {
							continue;
						}
						?>

						<span>
							<?= htmlspecialchars(
								$labelName,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

			<?php if ($prBody !== ''): ?>

				<div class="ping-github-pr-body">
					<?= nl2br(
						htmlspecialchars(
							$prBody,
							ENT_QUOTES,
							'UTF-8'
						)
					) ?>
				</div>

			<?php endif; ?>

			<div class="ping-github-pr-stats">

				<?php
				$prCommitCount = (int) (
					$pullRequest['commits']
						?? 0
				);

				$prFileCount = (int) (
					$pullRequest['changed_files']
						?? count($prFiles)
				);
				?>

				<span>
					<i
						class="fa-solid fa-code-commit"
						aria-hidden="true"
					></i>

					<?= $prCommitCount ?>
					<?= htmlspecialchars(
						$t(
							$prCommitCount === 1
								? 'github.counts.commit'
								: 'github.counts.commits'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span>
					<i
						class="fa-regular fa-file-code"
						aria-hidden="true"
					></i>

					<?= $prFileCount ?>
					<?= htmlspecialchars(
						$t(
							$prFileCount === 1
								? 'github.counts.file'
								: 'github.counts.files'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span class="is-additions">
					+<?= (int) (
						$pullRequest['additions']
							?? 0
					) ?>
				</span>

				<span class="is-deletions">
					-<?= (int) (
						$pullRequest['deletions']
							?? 0
					) ?>
				</span>

				<?php
				$discussionCount =
					(int) (
						$pullRequest['comments']
							?? 0
					)
					+ (int) (
						$pullRequest['review_comments']
							?? 0
					);
				?>

				<?php if ($discussionCount > 0): ?>

					<span>
						<i
							class="fa-regular fa-comment"
							aria-hidden="true"
						></i>

						<?= $discussionCount ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if ($prFiles !== []): ?>

				<div class="ping-github-pr-files">

					<?php foreach (
						array_slice(
							$prFiles,
							0,
							5
						)
						as $prFile
					): ?>

						<?php
						$filename = trim(
							(string) (
								$prFile['filename']
									?? ''
							)
						);

						if ($filename === '') {
							continue;
						}

						$patch = (string) (
							$prFile['patch']
								?? ''
						);
						?>

						<div class="ping-github-pr-file">

							<div class="ping-github-pr-file-heading">

								<span class="ping-github-pr-file-name">
									<?= htmlspecialchars(
										$filename,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<span class="ping-github-pr-file-stats">

									<strong class="is-additions">
										+<?= (int) (
											$prFile['additions']
												?? 0
										) ?>
									</strong>

									<strong class="is-deletions">
										-<?= (int) (
											$prFile['deletions']
												?? 0
										) ?>
									</strong>

								</span>

							</div>

							<?php if ($patch !== ''): ?>

								<div class="ping-github-commit-diff">

									<?php foreach (
										preg_split(
											'/\R/u',
											$patch
										) ?: []
										as $patchLine
									): ?>

										<?php
										$patchLine = (string) $patchLine;

										$lineClass = 'is-context';

										if (
											str_starts_with(
												$patchLine,
												'@@'
											)
										) {
											$lineClass = 'is-hunk';
										} elseif (
											str_starts_with(
												$patchLine,
												'+'
											)
											&& !str_starts_with(
												$patchLine,
												'+++'
											)
										) {
											$lineClass = 'is-addition';
										} elseif (
											str_starts_with(
												$patchLine,
												'-'
											)
											&& !str_starts_with(
												$patchLine,
												'---'
											)
										) {
											$lineClass = 'is-deletion';
										}
										?>

										<div class="ping-github-commit-diff-line <?= htmlspecialchars(
											$lineClass,
											ENT_QUOTES,
											'UTF-8'
										) ?>">
											<code><?= htmlspecialchars(
												$patchLine,
												ENT_QUOTES,
												'UTF-8'
											) ?></code>
										</div>

									<?php endforeach; ?>

								</div>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'issue'
		&& !empty($github['issue'])
	): ?>

		<?php
		$issue = is_array(
			$github['issue']
				?? null
		)
			? $github['issue']
			: [];

		$issueNumber = (int) (
			$issue['number']
				?? 0
		);

		$issueTitle = trim(
			(string) (
				$issue['title']
					?? ''
			)
		);

		$issueBody = trim(
			(string) (
				$issue['body']
					?? ''
			)
		);

		$issueState = trim(
			(string) (
				$issue['state']
					?? 'open'
			)
		);

		$issueStateReason = trim(
			(string) (
				$issue['state_reason']
					?? ''
			)
		);

		$issueAuthor = is_array(
			$issue['author']
				?? null
		)
			? $issue['author']
			: [];

		$issueAuthorLogin = trim(
			(string) (
				$issueAuthor['login']
					?? ''
			)
		);

		$issueAuthorAvatar = trim(
			(string) (
				$issueAuthor['avatar_url']
					?? ''
			)
		);

		$issueAuthorUrl = trim(
			(string) (
				$issueAuthor['html_url']
					?? ''
			)
		);

		$issueAssignee = is_array(
			$issue['assignee']
				?? null
		)
			? $issue['assignee']
			: [];

		$issueAssigneeLogin = trim(
			(string) (
				$issueAssignee['login']
					?? ''
			)
		);

		$issueAssigneeAvatar = trim(
			(string) (
				$issueAssignee['avatar_url']
					?? ''
			)
		);

		$issueAssigneeUrl = trim(
			(string) (
				$issueAssignee['html_url']
					?? ''
			)
		);

		$issueMilestone = is_array(
			$issue['milestone']
				?? null
		)
			? $issue['milestone']
			: [];

		$issueMilestoneTitle = trim(
			(string) (
				$issueMilestone['title']
					?? ''
			)
		);

		$issueLabels = is_array(
			$issue['labels']
				?? null
		)
			? $issue['labels']
			: [];

		$issueCreatedAt = trim(
			(string) (
				$issue['created_at']
					?? ''
			)
		);

		$formattedIssueDate = '';

		if ($issueCreatedAt !== '') {
			$timestamp = strtotime(
				$issueCreatedAt
			);

			if ($timestamp !== false) {
				$formattedIssueDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}

		$issueStateLabel = $issueState === 'closed'
			? $t('github.state.closed')
			: $t('github.state.open');

		$issueStateClass = $issueState === 'closed'
			? 'is-closed'
			: 'is-open';
		?>

		<div class="ping-github-issue">

			<div class="ping-github-issue-heading">

				<div class="ping-github-issue-author">

					<?php if ($issueAuthorAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$issueAuthorAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-issue-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($issueAuthorLogin !== ''): ?>

							<?php if ($issueAuthorUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$issueAuthorUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$issueAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$issueAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedIssueDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedIssueDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<span class="ping-github-issue-state <?= htmlspecialchars(
					$issueStateClass,
					ENT_QUOTES,
					'UTF-8'
				) ?>">
					<?= htmlspecialchars(
						$issueStateLabel,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($issueTitle !== ''): ?>

				<div class="ping-github-issue-title">

					<?php if ($issueNumber > 0): ?>

						<span>
							#<?= $issueNumber ?>
						</span>

					<?php endif; ?>

					<strong>
						<?= htmlspecialchars(
							$issueTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

				</div>

			<?php endif; ?>

			<?php if ($issueLabels !== []): ?>

				<div class="ping-github-issue-labels">

					<?php foreach ($issueLabels as $label): ?>

						<?php
						$labelName = trim(
							(string) (
								$label['name']
									?? ''
							)
						);

						if ($labelName === '') {
							continue;
						}
						?>

						<span>
							<?= htmlspecialchars(
								$labelName,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

			<?php if ($issueBody !== ''): ?>

				<div class="ping-github-issue-body">
					<?= nl2br(
						htmlspecialchars(
							$issueBody,
							ENT_QUOTES,
							'UTF-8'
						)
					) ?>
				</div>

			<?php endif; ?>

			<?php if (
				$issueAssigneeLogin !== ''
				|| $issueMilestoneTitle !== ''
				|| $issueStateReason !== ''
			): ?>

				<div class="ping-github-issue-meta">

					<?php if ($issueAssigneeLogin !== ''): ?>

						<div class="ping-github-issue-assignee">

							<span>
								<i
									class="fa-regular fa-user"
									aria-hidden="true"
								></i>
								<?= htmlspecialchars(
									$t('github.labels.assigned'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<?php if ($issueAssigneeAvatar !== ''): ?>

								<img
									src="<?= htmlspecialchars(
										$issueAssigneeAvatar,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									alt=""
									loading="lazy"
									referrerpolicy="no-referrer"
								>

							<?php endif; ?>

							<?php if ($issueAssigneeUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$issueAssigneeUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$issueAssigneeLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$issueAssigneeLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						</div>

					<?php endif; ?>

					<?php if ($issueMilestoneTitle !== ''): ?>

						<div>

							<span>
								<i
									class="fa-solid fa-bullseye"
									aria-hidden="true"
								></i>
								<?= htmlspecialchars(
									$t('github.labels.milestone'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<strong>
								<?= htmlspecialchars(
									$issueMilestoneTitle,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

						</div>

					<?php endif; ?>

					<?php if ($issueStateReason !== ''): ?>

						<div>

							<span>
								<i
									class="fa-solid fa-circle-info"
									aria-hidden="true"
								></i>
								<?= htmlspecialchars(
									$t('github.labels.reason'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<strong>
								<?= htmlspecialchars(
									$issueStateReason,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

						</div>

					<?php endif; ?>

				</div>

			<?php endif; ?>

			<div class="ping-github-issue-stats">

				<span>
					<i
						class="fa-regular fa-comment"
						aria-hidden="true"
					></i>

					<?php
					$issueComments = (int) (
						$issue['comments']
							?? 0
					);
					?>

					<?= $issueComments ?>
					<?= htmlspecialchars(
						$t(
							$issueComments === 1
								? 'github.counts.comment'
								: 'github.counts.comments'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

		</div>

	<?php elseif (
		$kind === 'discussion'
		&& !empty($github['discussion'])
	): ?>

		<?php
		$discussion = is_array(
			$github['discussion']
				?? null
		)
			? $github['discussion']
			: [];

		$discussionNumber = (int) (
			$discussion['number']
				?? 0
		);

		$discussionTitle = trim(
			(string) (
				$discussion['title']
					?? ''
			)
		);

		$discussionBody = trim(
			(string) (
				$discussion['body']
					?? ''
			)
		);

		$discussionClosed = (bool) (
			$discussion['closed']
				?? false
		);

		$discussionLocked = (bool) (
			$discussion['locked']
				?? false
		);

		$discussionAuthor = is_array(
			$discussion['author']
				?? null
		)
			? $discussion['author']
			: [];

		$discussionAuthorLogin = trim(
			(string) (
				$discussionAuthor['login']
					?? ''
			)
		);

		$discussionAuthorAvatar = trim(
			(string) (
				$discussionAuthor['avatar_url']
					?? ''
			)
		);

		$discussionAuthorUrl = trim(
			(string) (
				$discussionAuthor['html_url']
					?? ''
			)
		);

		$discussionCategory = is_array(
			$discussion['category']
				?? null
		)
			? $discussion['category']
			: [];

		$discussionCategoryName = trim(
			(string) (
				$discussionCategory['name']
					?? ''
			)
		);

		$discussionCategoryEmoji = trim(
			(string) (
				$discussionCategory['emoji']
					?? ''
			)
		);

		$discussionCreatedAt = trim(
			(string) (
				$discussion['created_at']
					?? ''
			)
		);

		$formattedDiscussionDate = '';

		if ($discussionCreatedAt !== '') {
			$timestamp = strtotime(
				$discussionCreatedAt
			);

			if ($timestamp !== false) {
				$formattedDiscussionDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}

		$discussionComments = (int) (
			$discussion['comments']
				?? 0
		);

		$discussionUpvotes = (int) (
			$discussion['upvotes']
				?? 0
		);

		$discussionAnswer = is_array(
			$discussion['answer']
				?? null
		)
			? $discussion['answer']
			: [];

		$discussionAnswerBody = trim(
			(string) (
				$discussionAnswer['body']
					?? ''
			)
		);

		$discussionAnswerAuthor = trim(
			(string) (
				$discussionAnswer['author']
					?? ''
			)
		);

		$discussionAnswerUrl = trim(
			(string) (
				$discussionAnswer['url']
					?? ''
			)
		);

		$discussionStateLabel = $discussionClosed
			? $t('github.state.closed')
			: $t('github.state.open');

		$discussionStateClass = $discussionClosed
			? 'is-closed'
			: 'is-open';
		?>

		<div class="ping-github-discussion">

			<div class="ping-github-discussion-heading">

				<div class="ping-github-discussion-author">

					<?php if ($discussionAuthorAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$discussionAuthorAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-discussion-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($discussionAuthorLogin !== ''): ?>

							<?php if ($discussionAuthorUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$discussionAuthorUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$discussionAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$discussionAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedDiscussionDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedDiscussionDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<span class="ping-github-discussion-state <?= htmlspecialchars(
					$discussionStateClass,
					ENT_QUOTES,
					'UTF-8'
				) ?>">
					<?= htmlspecialchars(
						$discussionStateLabel,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($discussionTitle !== ''): ?>

				<div class="ping-github-discussion-title">

					<?php if ($discussionNumber > 0): ?>

						<span>
							#<?= $discussionNumber ?>
						</span>

					<?php endif; ?>

					<strong>
						<?= htmlspecialchars(
							$discussionTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

				</div>

			<?php endif; ?>

			<?php if ($discussionCategoryName !== ''): ?>

				<div class="ping-github-discussion-category">

					<?php if ($discussionCategoryEmoji !== ''): ?>

						<span>
							<?= htmlspecialchars(
								$discussionCategoryEmoji,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php endif; ?>

					<strong>
						<?= htmlspecialchars(
							$discussionCategoryName,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

				</div>

			<?php endif; ?>

			<?php if ($discussionBody !== ''): ?>

				<div class="ping-github-discussion-body">
					<?= nl2br(
						htmlspecialchars(
							$discussionBody,
							ENT_QUOTES,
							'UTF-8'
						)
					) ?>
				</div>

			<?php endif; ?>

			<div class="ping-github-discussion-stats">

				<span>
					<i
						class="fa-regular fa-comment"
						aria-hidden="true"
					></i>

					<?= $discussionComments ?>
					<?= htmlspecialchars(
						$t(
							$discussionComments === 1
								? 'github.counts.comment'
								: 'github.counts.comments'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span>
					<i
						class="fa-regular fa-thumbs-up"
						aria-hidden="true"
					></i>

					<?= $discussionUpvotes ?>
				</span>

				<?php if ($discussionLocked): ?>

					<span class="is-locked">
						<i
							class="fa-solid fa-lock"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t('github.labels.locked'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if ($discussionAnswerBody !== ''): ?>

				<div class="ping-github-discussion-answer">

					<div class="ping-github-discussion-answer-heading">

						<i
							class="fa-solid fa-circle-check"
							aria-hidden="true"
						></i>

						<strong>
							<?= htmlspecialchars(
								$t('github.labels.accepted_answer'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<?php if ($discussionAnswerAuthor !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$discussionAnswerAuthor,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

					<div class="ping-github-discussion-answer-body">
						<?= nl2br(
							htmlspecialchars(
								$discussionAnswerBody,
								ENT_QUOTES,
								'UTF-8'
							)
						) ?>
					</div>

					<?php if ($discussionAnswerUrl !== ''): ?>

						<a
							class="ping-github-discussion-answer-link"
							href="<?= htmlspecialchars(
								$discussionAnswerUrl,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<?= htmlspecialchars(
								$t('github.links.view_answer'),
								ENT_QUOTES,
								'UTF-8'
							) ?>

							<i
								class="fa-solid fa-arrow-up-right-from-square"
								aria-hidden="true"
							></i>
						</a>

					<?php endif; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'release'
		&& !empty($github['release'])
	): ?>

		<?php
		$release = is_array(
			$github['release']
				?? null
		)
			? $github['release']
			: [];

		$releaseName = trim(
			(string) (
				$release['name']
					?? ''
			)
		);

		$releaseTag = trim(
			(string) (
				$release['tag_name']
					?? ''
			)
		);

		$releaseBody = trim(
			(string) (
				$release['body']
					?? ''
			)
		);

		$releaseTarget = trim(
			(string) (
				$release['target_commitish']
					?? ''
			)
		);

		$releaseDraft = (bool) (
			$release['draft']
				?? false
		);

		$releasePrerelease = (bool) (
			$release['prerelease']
				?? false
		);

		$releaseAuthor = is_array(
			$release['author']
				?? null
		)
			? $release['author']
			: [];

		$releaseAuthorLogin = trim(
			(string) (
				$releaseAuthor['login']
					?? ''
			)
		);

		$releaseAuthorAvatar = trim(
			(string) (
				$releaseAuthor['avatar_url']
					?? ''
			)
		);

		$releaseAuthorUrl = trim(
			(string) (
				$releaseAuthor['html_url']
					?? ''
			)
		);

		$releasePublishedAt = trim(
			(string) (
				$release['published_at']
					?? $release['created_at']
					?? ''
			)
		);

		$formattedReleaseDate = '';

		if ($releasePublishedAt !== '') {
			$timestamp = strtotime(
				$releasePublishedAt
			);

			if ($timestamp !== false) {
				$formattedReleaseDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}

		$releaseAssets = is_array(
			$release['assets']
				?? null
		)
			? $release['assets']
			: [];

		$releaseStateLabel = match (true) {
			$releaseDraft => $t('github.state.draft'),
			$releasePrerelease => $t('github.state.prerelease'),
			default => $t('github.state.stable'),
		};

		$releaseStateClass = match (true) {
			$releaseDraft => 'is-draft',
			$releasePrerelease => 'is-prerelease',
			default => 'is-stable',
		};
		?>

		<div class="ping-github-release">

			<div class="ping-github-release-heading">

				<div class="ping-github-release-author">

					<?php if ($releaseAuthorAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$releaseAuthorAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-release-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($releaseAuthorLogin !== ''): ?>

							<?php if ($releaseAuthorUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$releaseAuthorUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$releaseAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$releaseAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedReleaseDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedReleaseDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<span class="ping-github-release-state <?= htmlspecialchars(
					$releaseStateClass,
					ENT_QUOTES,
					'UTF-8'
				) ?>">
					<?= htmlspecialchars(
						$releaseStateLabel,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<div class="ping-github-release-title">

				<i
					class="fa-solid fa-tag"
					aria-hidden="true"
				></i>

				<div>

					<?php if ($releaseName !== ''): ?>

						<strong>
							<?= htmlspecialchars(
								$releaseName,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

					<?php endif; ?>

					<?php if ($releaseTag !== ''): ?>

						<code>
							<?= htmlspecialchars(
								$releaseTag,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</code>

					<?php endif; ?>

				</div>

			</div>

			<?php if ($releaseTarget !== ''): ?>

				<div class="ping-github-release-target">

					<i
						class="fa-solid fa-code-branch"
						aria-hidden="true"
					></i>

					<span>
						<?= htmlspecialchars(
							$t('github.labels.target'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<code>
						<?= htmlspecialchars(
							$releaseTarget,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</code>

				</div>

			<?php endif; ?>

			<?php if ($releaseBody !== ''): ?>

				<div class="ping-github-release-body">
					<?= nl2br(
						htmlspecialchars(
							$releaseBody,
							ENT_QUOTES,
							'UTF-8'
						)
					) ?>
				</div>

			<?php endif; ?>

			<?php if ($releaseAssets !== []): ?>

				<div class="ping-github-release-assets">

					<div class="ping-github-release-assets-heading">

						<i
							class="fa-solid fa-box-open"
							aria-hidden="true"
						></i>

						<strong>
							<?= htmlspecialchars(
								$t('github.labels.assets'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<span>
							<?= count($releaseAssets) ?>
						</span>

					</div>

					<?php foreach (
						array_slice(
							$releaseAssets,
							0,
							8
						)
						as $asset
					): ?>

						<?php
						$assetName = trim(
							(string) (
								$asset['name']
									?? ''
							)
						);

						$assetUrl = trim(
							(string) (
								$asset['url']
									?? ''
							)
						);

						$assetSize = (int) (
							$asset['size']
								?? 0
						);

						$assetDownloads = (int) (
							$asset['download_count']
								?? 0
						);

						if ($assetName === '') {
							continue;
						}

						$assetSizeLabel = '';

						if ($assetSize > 0) {
							$assetSizeLabel = $assetSize >= 1048576
								? number_format(
									$assetSize / 1048576,
									1,
									',',
									'.'
								) . ' MB'
								: number_format(
									$assetSize / 1024,
									1,
									',',
									'.'
								) . ' KB';
						}
						?>

						<div class="ping-github-release-asset">

							<div>

								<i
									class="fa-regular fa-file-zipper"
									aria-hidden="true"
								></i>

								<?php if ($assetUrl !== ''): ?>

									<a
										href="<?= htmlspecialchars(
											$assetUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										target="_blank"
										rel="noopener noreferrer"
									>
										<?= htmlspecialchars(
											$assetName,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>

								<?php else: ?>

									<strong>
										<?= htmlspecialchars(
											$assetName,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</strong>

								<?php endif; ?>

							</div>

							<div class="ping-github-release-asset-meta">

								<?php if ($assetSizeLabel !== ''): ?>

									<span>
										<?= htmlspecialchars(
											$assetSizeLabel,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

								<?php if ($assetDownloads > 0): ?>

									<span>
										<i
											class="fa-solid fa-download"
											aria-hidden="true"
										></i>

										<?= $assetDownloads ?>
									</span>

								<?php endif; ?>

							</div>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'gist'
		&& !empty($github['gist'])
	): ?>

		<?php
		$gist = is_array(
			$github['gist']
				?? null
		)
			? $github['gist']
			: [];

		$gistDescription = trim(
			(string) (
				$gist['description']
					?? ''
			)
		);

		$gistUrl = trim(
			(string) (
				$gist['html_url']
					?? $url
			)
		);

		$gistPublic = (bool) (
			$gist['public']
				?? false
		);

		$gistComments = (int) (
			$gist['comments']
				?? 0
		);

		$gistOwner = is_array(
			$gist['owner']
				?? null
		)
			? $gist['owner']
			: [];

		$gistOwnerLogin = trim(
			(string) (
				$gistOwner['login']
					?? $github['owner']
					?? ''
			)
		);

		$gistOwnerAvatar = trim(
			(string) (
				$gistOwner['avatar_url']
					?? ''
			)
		);

		$gistOwnerUrl = trim(
			(string) (
				$gistOwner['html_url']
					?? ''
			)
		);

		$gistCreatedAt = trim(
			(string) (
				$gist['created_at']
					?? ''
			)
		);

		$formattedGistDate = '';

		if ($gistCreatedAt !== '') {
			$timestamp = strtotime(
				$gistCreatedAt
			);

			if ($timestamp !== false) {
				$formattedGistDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}

		$gistFiles = is_array(
			$gist['files']
				?? null
		)
			? $gist['files']
			: [];
		?>

		<div class="ping-github-gist">

			<div class="ping-github-gist-heading">

				<div class="ping-github-gist-author">

					<?php if ($gistOwnerAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$gistOwnerAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-gist-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($gistOwnerLogin !== ''): ?>

							<?php if ($gistOwnerUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$gistOwnerUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$gistOwnerLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$gistOwnerLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedGistDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedGistDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<span class="ping-github-gist-state">
					<?= htmlspecialchars(
						$gistPublic
							? $t('github.state.public')
							: $t('github.state.secret'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($gistDescription !== ''): ?>

				<div class="ping-github-gist-description">
					<?= htmlspecialchars(
						$gistDescription,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</div>

			<?php endif; ?>

			<div class="ping-github-gist-stats">

				<span>
					<i
						class="fa-regular fa-file-code"
						aria-hidden="true"
					></i>

					<?php $gistFileCount = count($gistFiles); ?>

					<?= $gistFileCount ?>
					<?= htmlspecialchars(
						$t(
							$gistFileCount === 1
								? 'github.counts.file'
								: 'github.counts.files'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span>
					<i
						class="fa-regular fa-comment"
						aria-hidden="true"
					></i>

					<?= $gistComments ?>
					<?= htmlspecialchars(
						$t(
							$gistComments === 1
								? 'github.counts.comment'
								: 'github.counts.comments'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($gistFiles !== []): ?>

				<div class="ping-github-gist-files">

					<?php foreach (
						array_slice(
							$gistFiles,
							0,
							5
						)
						as $gistFile
					): ?>

						<?php
						if (!is_array($gistFile)) {
							continue;
						}

						$gistFilename = trim(
							(string) (
								$gistFile['filename']
									?? ''
							)
						);

						$gistLanguage = trim(
							(string) (
								$gistFile['language']
									?? ''
							)
						);

						$gistContent = (string) (
							$gistFile['content']
								?? ''
						);

						$gistSize = (int) (
							$gistFile['size']
								?? 0
						);

						$gistTruncated = (bool) (
							$gistFile['truncated']
								?? false
						);

						$gistSizeLabel = '';

						if ($gistSize > 0) {
							$gistSizeLabel = $gistSize >= 1048576
								? number_format(
									$gistSize / 1048576,
									1,
									',',
									'.'
								) . ' MB'
								: number_format(
									$gistSize / 1024,
									1,
									',',
									'.'
								) . ' KB';
						}
						?>

						<div class="ping-github-gist-file">

							<div class="ping-github-gist-file-heading">

								<div class="ping-github-gist-file-name">

									<i
										class="fa-regular fa-file-code"
										aria-hidden="true"
									></i>

									<strong>
										<?= htmlspecialchars(
											$gistFilename,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</strong>

								</div>

								<div class="ping-github-gist-file-meta">

									<?php if ($gistLanguage !== ''): ?>

										<span>
											<?= htmlspecialchars(
												$gistLanguage,
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

									<?php endif; ?>

									<?php if ($gistSizeLabel !== ''): ?>

										<span>
											<?= htmlspecialchars(
												$gistSizeLabel,
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

									<?php endif; ?>

								</div>

							</div>

							<?php if ($gistContent !== ''): ?>

								<pre class="ping-github-gist-code"><code><?= htmlspecialchars(
									$gistContent,
									ENT_QUOTES,
									'UTF-8'
								) ?></code></pre>

							<?php endif; ?>

							<?php if ($gistTruncated): ?>

								<div class="ping-github-gist-truncated">
									<?= htmlspecialchars(
										$t('github.preview.file_truncated'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</div>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

			<?php if ($gistUrl !== ''): ?>

				<a
					class="ping-github-gist-link"
					href="<?= htmlspecialchars(
						$gistUrl,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?= htmlspecialchars(
						$t('github.links.open_gist'),
						ENT_QUOTES,
						'UTF-8'
					) ?>

					<i
						class="fa-solid fa-arrow-up-right-from-square"
						aria-hidden="true"
					></i>
				</a>

			<?php endif; ?>

		</div>
	<?php elseif (
		$kind === 'tree'
		&& !empty($github['tree'])
	): ?>

		<?php
		$tree = is_array(
			$github['tree']
				?? null
		)
			? $github['tree']
			: [];

		$treeRef = trim(
			(string) (
				$tree['ref']
					?? $ref
			)
		);

		$treePath = trim(
			(string) (
				$tree['path']
					?? $path
			)
		);

		$treeItems = is_array(
			$tree['items']
				?? null
		)
			? $tree['items']
			: [];

		$treeDirectories = 0;
		$treeFiles = 0;

		foreach ($treeItems as $treeItem) {
			if (!is_array($treeItem)) {
				continue;
			}

			if (
				($treeItem['type'] ?? '')
				=== 'dir'
			) {
				$treeDirectories++;
			} else {
				$treeFiles++;
			}
		}
		?>

		<div class="ping-github-tree">

			<div class="ping-github-tree-heading">

				<div>

					<i
						class="fa-solid fa-code-branch"
						aria-hidden="true"
					></i>

					<strong>
						<?= htmlspecialchars(
							$treeRef,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

				</div>

				<?php $treeItemCount = count($treeItems); ?>

				<span>
					<?= $treeItemCount ?>
					<?= htmlspecialchars(
						$t(
							$treeItemCount === 1
								? 'github.counts.element'
								: 'github.counts.elements'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($treePath !== ''): ?>

				<div class="ping-github-tree-path">

					<i
						class="fa-regular fa-folder-open"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$treePath,
						ENT_QUOTES,
						'UTF-8'
					) ?>

				</div>

			<?php endif; ?>

			<div class="ping-github-tree-stats">

				<span>
					<i
						class="fa-regular fa-folder"
						aria-hidden="true"
					></i>

					<?= $treeDirectories ?>
					<?= htmlspecialchars(
						$t(
							$treeDirectories === 1
								? 'github.counts.directory'
								: 'github.counts.directories'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span>
					<i
						class="fa-regular fa-file-code"
						aria-hidden="true"
					></i>

					<?= $treeFiles ?>
					<?= htmlspecialchars(
						$t(
							$treeFiles === 1
								? 'github.counts.file'
								: 'github.counts.files'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($treeItems !== []): ?>

				<div class="ping-github-tree-items">

					<?php foreach (
						array_slice(
							$treeItems,
							0,
							20
						)
						as $treeItem
					): ?>

						<?php
						if (!is_array($treeItem)) {
							continue;
						}

						$treeItemName = trim(
							(string) (
								$treeItem['name']
									?? ''
							)
						);

						$treeItemType = trim(
							(string) (
								$treeItem['type']
									?? ''
							)
						);

						$treeItemUrl = trim(
							(string) (
								$treeItem['html_url']
									?? ''
							)
						);

						$treeItemSize = (int) (
							$treeItem['size']
								?? 0
						);

						if ($treeItemName === '') {
							continue;
						}

						$treeItemIcon = match (
							$treeItemType
						) {
							'dir' => 'fa-regular fa-folder',
							'symlink' => 'fa-solid fa-link',
							'submodule' => 'fa-solid fa-code-branch',
							default => 'fa-regular fa-file-code',
						};

						$treeItemSizeLabel = '';

						if (
							$treeItemType !== 'dir'
							&& $treeItemSize > 0
						) {
							$treeItemSizeLabel =
								$treeItemSize >= 1048576
									? number_format(
										$treeItemSize / 1048576,
										1,
										',',
										'.'
									) . ' MB'
									: number_format(
										$treeItemSize / 1024,
										1,
										',',
										'.'
									) . ' KB';
						}
						?>

						<div class="ping-github-tree-item">

							<div>

								<i
									class="<?= htmlspecialchars(
										$treeItemIcon,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									aria-hidden="true"
								></i>

								<?php if ($treeItemUrl !== ''): ?>

									<a
										href="<?= htmlspecialchars(
											$treeItemUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										target="_blank"
										rel="noopener noreferrer"
									>
										<?= htmlspecialchars(
											$treeItemName,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>

								<?php else: ?>

									<strong>
										<?= htmlspecialchars(
											$treeItemName,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</strong>

								<?php endif; ?>

							</div>

							<?php if ($treeItemSizeLabel !== ''): ?>

								<span>
									<?= htmlspecialchars(
										$treeItemSizeLabel,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'compare'
		&& !empty($github['compare'])
	): ?>

		<?php
		$compare = is_array(
			$github['compare']
				?? null
		)
			? $github['compare']
			: [];

		$compareStatus = trim(
			(string) (
				$compare['status']
					?? ''
			)
		);

		$compareAheadBy = (int) (
			$compare['ahead_by']
				?? 0
		);

		$compareBehindBy = (int) (
			$compare['behind_by']
				?? 0
		);

		$compareTotalCommits = (int) (
			$compare['total_commits']
				?? 0
		);

		$compareBase = trim(
			(string) (
				$github['base']
					?? ''
			)
		);

		$compareHead = trim(
			(string) (
				$github['head']
					?? ''
			)
		);

		$compareCommits = is_array(
			$compare['commits']
				?? null
		)
			? $compare['commits']
			: [];

		$compareFiles = is_array(
			$compare['files']
				?? null
		)
			? $compare['files']
			: [];
		?>

		<div class="ping-github-compare">

			<div class="ping-github-compare-heading">

				<div>

					<i
						class="fa-solid fa-code-compare"
						aria-hidden="true"
					></i>

					<strong>
						<?= htmlspecialchars(
							$compareBase,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

					<i
						class="fa-solid fa-arrow-right"
						aria-hidden="true"
					></i>

					<strong>
						<?= htmlspecialchars(
							$compareHead,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

				</div>

				<?php if ($compareStatus !== ''): ?>

					<span>
						<?= htmlspecialchars(
							strtoupper($compareStatus),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<div class="ping-github-compare-stats">

				<span>
					<i
						class="fa-solid fa-code-commit"
						aria-hidden="true"
					></i>

					<?= $compareTotalCommits ?>
					<?= htmlspecialchars(
						$t(
							$compareTotalCommits === 1
								? 'github.counts.commit'
								: 'github.counts.commits'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span>
					<?= $compareAheadBy ?>
					<?= htmlspecialchars(
						$t('github.counts.ahead'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<span>
					<?= $compareBehindBy ?>
					<?= htmlspecialchars(
						$t('github.counts.behind'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<?php $compareFileCount = count($compareFiles); ?>

				<span>
					<i
						class="fa-regular fa-file-code"
						aria-hidden="true"
					></i>

					<?= $compareFileCount ?>
					<?= htmlspecialchars(
						$t(
							$compareFileCount === 1
								? 'github.counts.file'
								: 'github.counts.files'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<?php if ($compareCommits !== []): ?>

				<div class="ping-github-compare-commits">

					<?php foreach (
						array_slice(
							$compareCommits,
							0,
							5
						)
						as $compareCommit
					): ?>

						<?php
						if (!is_array($compareCommit)) {
							continue;
						}

						$compareCommitSha = trim(
							(string) (
								$compareCommit['short_sha']
									?? ''
							)
						);

						$compareCommitMessage = trim(
							(string) (
								$compareCommit['message']
									?? ''
							)
						);

						$compareCommitUrl = trim(
							(string) (
								$compareCommit['url']
									?? ''
							)
						);

						$compareCommitAuthor = trim(
							(string) (
								$compareCommit['author_login']
									?? $compareCommit['author_name']
									?? ''
							)
						);
						?>

						<div class="ping-github-compare-commit">

							<div>

								<?php if ($compareCommitSha !== ''): ?>

									<code>
										<?= htmlspecialchars(
											$compareCommitSha,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</code>

								<?php endif; ?>

								<?php if ($compareCommitUrl !== ''): ?>

									<a
										href="<?= htmlspecialchars(
											$compareCommitUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										target="_blank"
										rel="noopener noreferrer"
									>
										<?= htmlspecialchars(
											$compareCommitMessage,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>

								<?php else: ?>

									<strong>
										<?= htmlspecialchars(
											$compareCommitMessage,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</strong>

								<?php endif; ?>

							</div>

							<?php if ($compareCommitAuthor !== ''): ?>

								<span>
									<?= htmlspecialchars(
										$compareCommitAuthor,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

			<?php if ($compareFiles !== []): ?>

				<div class="ping-github-compare-files">

					<?php foreach (
						array_slice(
							$compareFiles,
							0,
							5
						)
						as $compareFile
					): ?>

						<?php
						if (!is_array($compareFile)) {
							continue;
						}

						$compareFilename = trim(
							(string) (
								$compareFile['filename']
									?? ''
							)
						);

						$compareFileStatus = trim(
							(string) (
								$compareFile['status']
									?? ''
							)
						);
						?>

						<div class="ping-github-compare-file">

							<strong>
								<?= htmlspecialchars(
									$compareFilename,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

							<div>

								<?php if ($compareFileStatus !== ''): ?>

									<span>
										<?= htmlspecialchars(
											strtoupper($compareFileStatus),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

								<span>
									+<?= (int) (
										$compareFile['additions']
											?? 0
									) ?>
								</span>

								<span>
									-<?= (int) (
										$compareFile['deletions']
											?? 0
									) ?>
								</span>

							</div>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'workflow_run'
		&& !empty($github['workflow_run'])
	): ?>

		<?php
		$workflowRun = is_array(
			$github['workflow_run']
				?? null
		)
			? $github['workflow_run']
			: [];

		$workflowName = trim(
			(string) (
				$workflowRun['name']
					?? ''
			)
		);

		$workflowTitle = trim(
			(string) (
				$workflowRun['display_title']
					?? ''
			)
		);

		$workflowEvent = trim(
			(string) (
				$workflowRun['event']
					?? ''
			)
		);

		$workflowStatus = trim(
			(string) (
				$workflowRun['status']
					?? ''
			)
		);

		$workflowConclusion = trim(
			(string) (
				$workflowRun['conclusion']
					?? ''
			)
		);

		$workflowBranch = trim(
			(string) (
				$workflowRun['head_branch']
					?? ''
			)
		);

		$workflowSha = trim(
			(string) (
				$workflowRun['head_sha']
					?? ''
			)
		);

		$workflowShortSha = $workflowSha !== ''
			? substr(
				$workflowSha,
				0,
				7
			)
			: '';

		$workflowUrl = trim(
			(string) (
				$workflowRun['url']
					?? $url
			)
		);

		$workflowRunNumber = (int) (
			$workflowRun['run_number']
				?? 0
		);

		$workflowRunAttempt = (int) (
			$workflowRun['run_attempt']
				?? 0
		);

		$workflowCreatedAt = trim(
			(string) (
				$workflowRun['run_started_at']
					?? $workflowRun['created_at']
					?? ''
			)
		);

		$formattedWorkflowDate = '';

		if ($workflowCreatedAt !== '') {
			$timestamp = strtotime(
				$workflowCreatedAt
			);

			if ($timestamp !== false) {
				$formattedWorkflowDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}

		$workflowActor = is_array(
			$workflowRun['actor']
				?? null
		)
			? $workflowRun['actor']
			: [];

		$workflowActorLogin = trim(
			(string) (
				$workflowActor['login']
					?? ''
			)
		);

		$workflowActorAvatar = trim(
			(string) (
				$workflowActor['avatar_url']
					?? ''
			)
		);

		$workflowActorUrl = trim(
			(string) (
				$workflowActor['html_url']
					?? ''
			)
		);

		$workflowCommit = is_array(
			$workflowRun['head_commit']
				?? null
		)
			? $workflowRun['head_commit']
			: [];

		$workflowCommitMessage = trim(
			(string) (
				$workflowCommit['message']
					?? ''
			)
		);

		$workflowCommitAuthor = trim(
			(string) (
				$workflowCommit['author_name']
					?? ''
			)
		);

		$workflowStateLabel = $workflowConclusion !== ''
			? strtoupper(
				$workflowConclusion
			)
			: strtoupper(
				$workflowStatus
			);
		?>

		<div class="ping-github-workflow">

			<div class="ping-github-workflow-heading">

				<div class="ping-github-workflow-actor">

					<?php if ($workflowActorAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$workflowActorAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-workflow-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($workflowActorLogin !== ''): ?>

							<?php if ($workflowActorUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$workflowActorUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$workflowActorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$workflowActorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedWorkflowDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedWorkflowDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<?php if ($workflowStateLabel !== ''): ?>

					<span class="ping-github-workflow-state">
						<?= htmlspecialchars(
							$workflowStateLabel,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if (
				$workflowName !== ''
				|| $workflowTitle !== ''
			): ?>

				<div class="ping-github-workflow-title">

					<i
						class="fa-solid fa-gears"
						aria-hidden="true"
					></i>

					<div>

						<?php if ($workflowName !== ''): ?>

							<strong>
								<?= htmlspecialchars(
									$workflowName,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

						<?php endif; ?>

						<?php if ($workflowTitle !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$workflowTitle,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

			<?php endif; ?>

			<div class="ping-github-workflow-meta">

				<?php if ($workflowEvent !== ''): ?>

					<span>
						<i
							class="fa-solid fa-bolt"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$workflowEvent,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

				<?php if ($workflowBranch !== ''): ?>

					<span>
						<i
							class="fa-solid fa-code-branch"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$workflowBranch,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

				<?php if ($workflowShortSha !== ''): ?>

					<span>
						<i
							class="fa-solid fa-code-commit"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$workflowShortSha,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

				<?php if ($workflowRunNumber > 0): ?>

					<span>
						<?= htmlspecialchars(
							$t('github.labels.run'),
							ENT_QUOTES,
							'UTF-8'
						) ?> #<?= $workflowRunNumber ?>
					</span>

				<?php endif; ?>

				<?php if ($workflowRunAttempt > 1): ?>

					<span>
						<?= htmlspecialchars(
							$t('github.labels.attempt'),
							ENT_QUOTES,
							'UTF-8'
						) ?> <?= $workflowRunAttempt ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if ($workflowCommitMessage !== ''): ?>

				<div class="ping-github-workflow-commit">

					<strong>
						<?= htmlspecialchars(
							$workflowCommitMessage,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

					<?php if ($workflowCommitAuthor !== ''): ?>

						<span>
							<?= htmlspecialchars(
								$workflowCommitAuthor,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php endif; ?>

				</div>

			<?php endif; ?>

			<?php if ($workflowUrl !== ''): ?>

				<a
					class="ping-github-workflow-link"
					href="<?= htmlspecialchars(
						$workflowUrl,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?= htmlspecialchars(
						$t('github.links.open_workflow'),
						ENT_QUOTES,
						'UTF-8'
					) ?>

					<i
						class="fa-solid fa-arrow-up-right-from-square"
						aria-hidden="true"
					></i>
				</a>

			<?php endif; ?>

		</div>

	<?php elseif (
		$kind === 'repository'
		&& !empty($github['repository_preview'])
	): ?>

		<?php
		$repositoryPreview = is_array(
			$github['repository_preview']
				?? null
		)
			? $github['repository_preview']
			: [];

		$repositoryData = is_array(
			$repositoryPreview['repository']
				?? null
		)
			? $repositoryPreview['repository']
			: [];

		$repositoryReadme = trim(
			(string) (
				$repositoryPreview['readme']
					?? ''
			)
		);

		$repositoryName = trim(
			(string) (
				$repositoryData['name']
					?? ''
			)
		);

		$repositoryFullName = trim(
			(string) (
				$repositoryData['full_name']
					?? $fullName
			)
		);

		$repositoryDescription = trim(
			(string) (
				$repositoryData['description']
					?? ''
			)
		);

		$repositoryUrl = trim(
			(string) (
				$repositoryData['html_url']
					?? $url
			)
		);

		$repositoryHomepage = trim(
			(string) (
				$repositoryData['homepage']
					?? ''
			)
		);

		$repositoryLanguage = trim(
			(string) (
				$repositoryData['language']
					?? ''
			)
		);

		$repositoryBranch = trim(
			(string) (
				$repositoryData['default_branch']
					?? ''
			)
		);

		$repositoryLicenseData = is_array(
			$repositoryData['license']
				?? null
		)
			? $repositoryData['license']
			: [];

		$repositoryLicense = trim(
			(string) (
				$repositoryLicenseData['spdx_id']
					?? $repositoryLicenseData['name']
					?? (
						is_string(
							$repositoryData['license']
								?? null
						)
							? $repositoryData['license']
							: ''
					)
			)
		);

		$repositoryStars = (int) (
			$repositoryData['stars']
				?? $repositoryData['stargazers_count']
				?? 0
		);

		$repositoryForks = (int) (
			$repositoryData['forks']
				?? $repositoryData['forks_count']
				?? 0
		);

		$repositoryIssues = (int) (
			$repositoryData['open_issues']
				?? $repositoryData['open_issues_count']
				?? 0
		);

		$repositoryUpdatedAt = trim(
			(string) (
				$repositoryData['updated_at']
					?? ''
			)
		);

		$formattedRepositoryDate = '';

		if ($repositoryUpdatedAt !== '') {
			$timestamp = strtotime(
				$repositoryUpdatedAt
			);

			if ($timestamp !== false) {
				$formattedRepositoryDate = date(
					'd/m/Y H:i',
					$timestamp
				);
			}
		}

		$repositoryOwner = is_array(
			$repositoryData['owner']
				?? null
		)
			? $repositoryData['owner']
			: [];

		$repositoryOwnerLogin = trim(
			(string) (
				$repositoryOwner['login']
					?? ''
			)
		);

		$repositoryOwnerAvatar = trim(
			(string) (
				$repositoryOwner['avatar_url']
					?? ''
			)
		);

		$repositoryOwnerUrl = trim(
			(string) (
				$repositoryOwner['html_url']
					?? ''
			)
		);
		?>

		<div class="ping-github-repository-preview">

			<div class="ping-github-repository-heading">

				<div class="ping-github-repository-owner">

					<?php if ($repositoryOwnerAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$repositoryOwnerAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-repository-avatar-fallback">
							<i
								class="fa-brands fa-github"
								aria-hidden="true"
							></i>
						</span>

					<?php endif; ?>

					<div>

						<?php if ($repositoryOwnerLogin !== ''): ?>

							<?php if ($repositoryOwnerUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$repositoryOwnerUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$repositoryOwnerLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$repositoryOwnerLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedRepositoryDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$t('github.labels.updated'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

								<?= htmlspecialchars(
									$formattedRepositoryDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<?php if ($repositoryBranch !== ''): ?>

					<span class="ping-github-repository-branch">
						<i
							class="fa-solid fa-code-branch"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$repositoryBranch,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<div class="ping-github-repository-title">

				<i
					class="fa-solid fa-book-bookmark"
					aria-hidden="true"
				></i>

				<div>

					<a
						href="<?= htmlspecialchars(
							$repositoryUrl,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?= htmlspecialchars(
							$repositoryFullName !== ''
								? $repositoryFullName
								: $repositoryName,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</a>

					<?php if ($repositoryDescription !== ''): ?>

						<p>
							<?= htmlspecialchars(
								$repositoryDescription,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					<?php endif; ?>

				</div>

			</div>

			<div class="ping-github-repository-stats">

				<?php if ($repositoryLanguage !== ''): ?>

					<span>
						<i
							class="fa-solid fa-code"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$repositoryLanguage,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

				<span>
					<i
						class="fa-regular fa-star"
						aria-hidden="true"
					></i>

					<?= $repositoryStars ?>
				</span>

				<span>
					<i
						class="fa-solid fa-code-fork"
						aria-hidden="true"
					></i>

					<?= $repositoryForks ?>
				</span>

				<span>
					<i
						class="fa-regular fa-circle-dot"
						aria-hidden="true"
					></i>

					<?= $repositoryIssues ?>
				</span>

				<?php if ($repositoryLicense !== ''): ?>

					<span>
						<i
							class="fa-regular fa-file-lines"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$repositoryLicense,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if ($repositoryHomepage !== ''): ?>

				<a
					class="ping-github-repository-homepage"
					href="<?= htmlspecialchars(
						$repositoryHomepage,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					<i
						class="fa-solid fa-globe"
						aria-hidden="true"
					></i>

					<span>
						<?= htmlspecialchars(
							$repositoryHomepage,
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

			<?php if ($repositoryReadme !== ''): ?>

				<div class="ping-github-repository-readme">

					<div class="ping-github-repository-readme-heading">

						<i
							class="fa-brands fa-readme"
							aria-hidden="true"
						></i>

						<strong>
							README
						</strong>

					</div>

					<pre><code><?= htmlspecialchars(
						$repositoryReadme,
						ENT_QUOTES,
						'UTF-8'
					) ?></code></pre>

				</div>

			<?php endif; ?>

		</div>

	<?php else: ?>

		<a
			class="ping-github-fallback"
			href="<?= htmlspecialchars(
				$url,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			target="_blank"
			rel="noopener noreferrer"
		>

			<?php if (!empty($link['title'])): ?>

				<strong>
					<?= htmlspecialchars(
						(string) $link['title'],
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</strong>

			<?php endif; ?>

			<?php if (!empty($link['description'])): ?>

				<p>
					<?= htmlspecialchars(
						(string) $link['description'],
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			<?php endif; ?>

		</a>

	<?php endif; ?>

	<?php
	$githubComment = is_array(
		$github['comment']
			?? null
	)
		? $github['comment']
		: [];

	$githubCommentBody = trim(
		(string) (
			$githubComment['body']
				?? ''
		)
	);

	$githubCommentUrl = trim(
		(string) (
			$githubComment['url']
				?? ''
		)
	);

	$githubCommentCreatedAt = trim(
		(string) (
			$githubComment['created_at']
				?? ''
		)
	);

	$githubCommentAssociation = trim(
		(string) (
			$githubComment['author_association']
				?? ''
		)
	);

	$githubCommentAuthor = is_array(
		$githubComment['author']
			?? null
	)
		? $githubComment['author']
		: [];

	$githubCommentAuthorLogin = trim(
		(string) (
			$githubCommentAuthor['login']
				?? ''
		)
	);

	$githubCommentAuthorAvatar = trim(
		(string) (
			$githubCommentAuthor['avatar_url']
				?? ''
		)
	);

	$githubCommentAuthorUrl = trim(
		(string) (
			$githubCommentAuthor['html_url']
				?? ''
		)
	);

	$formattedGithubCommentDate = '';

	if ($githubCommentCreatedAt !== '') {
		$timestamp = strtotime(
			$githubCommentCreatedAt
		);

		if ($timestamp !== false) {
			$formattedGithubCommentDate = date(
				'd/m/Y H:i',
				$timestamp
			);
		}
	}
	?>

	<?php if ($githubComment !== []): ?>

		<div class="ping-github-comment">

			<div class="ping-github-comment-heading">

				<div class="ping-github-comment-author">

					<?php if ($githubCommentAuthorAvatar !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$githubCommentAuthorAvatar,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
							referrerpolicy="no-referrer"
						>

					<?php else: ?>

						<span class="ping-github-comment-avatar-fallback">

							<i
								class="fa-regular fa-user"
								aria-hidden="true"
							></i>

						</span>

					<?php endif; ?>

					<div>

						<?php if ($githubCommentAuthorLogin !== ''): ?>

							<?php if ($githubCommentAuthorUrl !== ''): ?>

								<a
									href="<?= htmlspecialchars(
										$githubCommentAuthorUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?= htmlspecialchars(
										$githubCommentAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

							<?php else: ?>

								<strong>
									<?= htmlspecialchars(
										$githubCommentAuthorLogin,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($formattedGithubCommentDate !== ''): ?>

							<span>
								<?= htmlspecialchars(
									$formattedGithubCommentDate,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				</div>

				<div class="ping-github-comment-label">

					<i
						class="fa-regular fa-comment"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t('github.kind.comment'),
						ENT_QUOTES,
						'UTF-8'
					) ?>

				</div>

			</div>

			<?php if ($githubCommentAssociation !== ''): ?>

				<div class="ping-github-comment-association">
					<?= htmlspecialchars(
						$githubCommentAssociation,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</div>

			<?php endif; ?>

			<?php if ($githubCommentBody !== ''): ?>

				<div class="ping-github-comment-body">
					<?= nl2br(
						htmlspecialchars(
							$githubCommentBody,
							ENT_QUOTES,
							'UTF-8'
						)
					) ?>
				</div>

			<?php endif; ?>

			<?php if ($githubCommentUrl !== ''): ?>

				<a
					class="ping-github-comment-link"
					href="<?= htmlspecialchars(
						$githubCommentUrl,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?= htmlspecialchars(
						$t('github.links.view_comment'),
						ENT_QUOTES,
						'UTF-8'
					) ?>

					<i
						class="fa-solid fa-arrow-up-right-from-square"
						aria-hidden="true"
					></i>
				</a>

			<?php endif; ?>

		</div>

	<?php endif; ?>

</div>
