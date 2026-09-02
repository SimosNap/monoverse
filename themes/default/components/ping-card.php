<?php
declare(strict_types=1);

$isChanzine = ($post['source'] ?? 'user') === 'chanzine';
$isDogeTip = ($post['source'] ?? 'user') === 'doge_tip';
$moderationMode = !empty($moderationMode);

$authorUsername = trim(
	(string) ($post['username'] ?? '')
);

$authorNickname = trim(
	(string) ($post['nickname'] ?? '')
);

$authorName = $authorUsername !== ''
	? $authorUsername
	: (
		$authorNickname !== ''
			? $authorNickname
			: $t('ping.card.user')
	);

$authorAccountExists = !array_key_exists(
	'external_account_exists',
	$post
) || !empty($post['external_account_exists']);

$authorHasPublicProfile =
	$authorAccountExists
	&& !empty($post['public_profile'])
	&& $authorUsername !== '';

$currentUser = $session->get('auth.user');

$currentSub = is_array($currentUser)
	? trim((string) ($currentUser['sub'] ?? ''))
	: '';

$canBlockAuthor =
	!$isChanzine
	&& $authorAccountExists
	&& $authorUsername !== ''
	&& $currentSub !== ''
	&& $currentSub !== (string) ($post['author_sub'] ?? '');

$cryptoTipsEnabled = (
	($settings['crypto_tips_enabled'] ?? '0') === '1'
);

$cryptoTipsPingsEnabled = (
	($settings['crypto_tips_pings_enabled'] ?? '1') === '1'
);

$dogeTipAddress = trim(
	(string) ($post['doge_tip_resolved_address'] ?? '')
);

$canDogeTipAuthor =
	$cryptoTipsEnabled
	&& $cryptoTipsPingsEnabled
	&& !$isChanzine
	&& $authorAccountExists
	&& $authorUsername !== ''
	&& $dogeTipAddress !== ''
	&& $currentSub !== ''
	&& $currentSub !== (string) ($post['author_sub'] ?? '');

?>

<div
	class="ping-card<?= $isDogeTip ? ' ping-card-doge-tip' : '' ?>"
	data-post-id="<?= (int) ($post['id'] ?? 0) ?>"
>

	<div class="ping-header">

		<?php if ($isChanzine): ?>

			<a
				class="ping-avatar ping-avatar-chanzine"
				href="/chanzine"
				aria-label="Chanzine"
			>
				<i
					class="fa-solid fa-newspaper"
					aria-hidden="true"
				></i>
			</a>

			<div class="ping-author">

				<a
					class="ping-username ping-username-chanzine"
					href="/chanzine"
				>
					Chanzine
				</a>

				<a
					class="ping-date"
					href="/ping/<?= rawurlencode(
						(string) ($post['uuid'] ?? '')
					) ?>"
				>
					<?= htmlspecialchars(
						(string) (
							$post['published_at_formatted']
							?? ''
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

			</div>

		<?php else: ?>

			<?php if ($authorHasPublicProfile): ?>

				<a
					class="ping-avatar"
					href="/profile/<?= rawurlencode(
						$authorUsername
					) ?>"
				>

					<?php if (
						!empty($post['show_avatar'])
						&& !empty($post['avatar_url'])
					): ?>

						<img
							src="<?= htmlspecialchars(
								(string) $post['avatar_url'],
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt="@<?= htmlspecialchars(
								$authorName,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>

					<?php else: ?>

						<i
							class="fa-solid fa-user"
							aria-hidden="true"
						></i>

					<?php endif; ?>

				</a>

			<?php else: ?>

				<span class="ping-avatar ping-avatar-static">

					<?php if (
						$authorAccountExists
						&& !empty($post['show_avatar'])
						&& !empty($post['avatar_url'])
					): ?>

						<img
							src="<?= htmlspecialchars(
								(string) $post['avatar_url'],
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
						>

					<?php elseif ($authorAccountExists): ?>

						<i
							class="fa-solid fa-user"
							aria-hidden="true"
						></i>

					<?php else: ?>

						<i
							class="fa-solid fa-user-slash"
							aria-hidden="true"
						></i>

					<?php endif; ?>

				</span>

			<?php endif; ?>

			<div class="ping-author">

				<div class="ping-author-name">

					<?php if ($authorAccountExists): ?>

						<?= $this->component(
							'user-presence',
							[
								'presence' =>
									$post['presence'] ?? [],
							]
						) ?>

					<?php endif; ?>

					<?php if ($authorHasPublicProfile): ?>

						<a
							class="ping-username"
							href="/profile/<?= rawurlencode(
								$authorUsername
							) ?>"
						>
							@<?= htmlspecialchars(
								$authorName,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</a>

					<?php elseif ($authorAccountExists): ?>

						<span class="ping-username ping-username-static">
							@<?= htmlspecialchars(
								$authorName,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php else: ?>

						<span class="ping-username ping-username-missing">
							<?= htmlspecialchars(
								$t('ping.card.author.unavailable'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php endif; ?>

				</div>

				<?php if (!$authorHasPublicProfile): ?>

					<p class="ping-author-status">

						<?php if ($authorAccountExists): ?>

							<i
								class="fa-solid fa-lock"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$t('ping.card.author.private_profile'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php else: ?>

							<i
								class="fa-solid fa-user-slash"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$t('ping.card.author.account_missing'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</p>

				<?php endif; ?>

				<a
					class="ping-date"
					href="/ping/<?= rawurlencode(
						(string) ($post['uuid'] ?? '')
					) ?>"
				>
					<?= htmlspecialchars(
						(string) (
							$post['published_at_formatted']
							?? ''
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</a>

			</div>

		<?php endif; ?>

	</div>

	<?php if ($isDogeTip): ?>

		<div class="ping-doge-tip-label">
			<span class="ping-doge-tip-icon">
				Ð
			</span>

			<span>
				<?= htmlspecialchars(
					$t('ping.card.doge.tip_label'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>
		</div>

	<?php endif; ?>

	<div
		class="ping-content"
		data-content
	>
		<?= nl2br(
			\Monoverse\Helpers\TextHelper::linkMentions(
				(string) ($post['content'] ?? '')
			)
		) ?>
	</div>

	<?php if (!empty($post['code_block'])): ?>
		<?php
		$codeBlock = $post['code_block'];

		require __DIR__ . '/code-block.php';
		?>
	<?php endif; ?>

	<?php if (!empty($post['can_edit'])): ?>

		<div
			class="ping-editor"
			style="display:none;"
		>

			<form
				method="post"
				action="/ping/<?= rawurlencode(
					(string) ($post['uuid'] ?? '')
				) ?>/update"
			>

				<textarea
					name="content"
					rows="5"
				><?= htmlspecialchars(
					(string) ($post['content'] ?? ''),
					ENT_QUOTES,
					'UTF-8'
				) ?></textarea>

				<?php
				$editCodeBlock = is_array($post['code_block'] ?? null)
					? $post['code_block']
					: [];

				$editCode = (string) (
					$editCodeBlock['code']
					?? ''
				);

				$editCodeLanguage = (string) (
					$editCodeBlock['language']
					?? 'text'
				);
				?>

				<div class="ping-code-composer">

					<div class="ping-code-composer-header">

						<label>
							<?= htmlspecialchars(
								$t('ping.composer.code_language'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<select
							name="code_language"
							class="ping-code-language"
						>
							<?php
							$codeLanguages = [
								'text' => 'Text',
								'php' => 'PHP',
								'javascript' => 'JavaScript',
								'html' => 'HTML',
								'css' => 'CSS',
								'sql' => 'SQL',
								'bash' => 'Bash',
								'python' => 'Python',
								'c' => 'C',
								'cpp' => 'C++',
								'java' => 'Java',
								'json' => 'JSON',
							];

							foreach ($codeLanguages as $value => $label):
							?>
								<option
									value="<?= htmlspecialchars(
										$value,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									<?= $editCodeLanguage === $value
										? 'selected'
										: '' ?>
								>
									<?= htmlspecialchars(
										$label,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</option>
							<?php endforeach; ?>
						</select>

					</div>

					<textarea
						name="code"
						class="ping-code-textarea"
						rows="10"
						maxlength="10000"
						spellcheck="false"
						placeholder="<?= htmlspecialchars(
							$t('ping.composer.code_placeholder'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					><?= htmlspecialchars(
						$editCode,
						ENT_QUOTES,
						'UTF-8'
					) ?></textarea>

				</div>

				<div class="ping-editor-actions">

					<button
						type="submit"
						class="btn btn-primary"
					>
						<?= htmlspecialchars(
							$t('ping.card.editor.save'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

					<button
						type="button"
						class="btn btn-secondary ping-edit-cancel"
					>
						<?= htmlspecialchars(
							$t('ping.card.editor.cancel'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

				</div>

			</form>

		</div>

	<?php endif; ?>

	<?php require __DIR__ . '/../components/ping-media.php'; ?>

	<?php
	$metadata = [];

	if (!empty($post['metadata'])) {
		$metadata = json_decode(
			(string) $post['metadata'],
			true
		) ?? [];
	}

	if (!empty($metadata['links'][0])):

		$link = $metadata['links'][0];

		$isGitHubPreview = (
			($link['provider'] ?? '') === 'github'
			&& !empty($link['github'])
		);

		if ($isGitHubPreview) {

			require __DIR__
				. '/../components/github-link-preview.php';

		} else {
	?>

		<a
			class="ping-link-preview"
			href="<?= htmlspecialchars(
				(string) ($link['url'] ?? ''),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			target="_blank"
			rel="noopener noreferrer"
		>

			<?php if (!empty($link['image'])): ?>

				<div class="ping-link-image-wrapper">

					<img
						class="ping-link-image"
						src="<?= htmlspecialchars(
							(string) $link['image'],
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						alt="<?= htmlspecialchars(
							(string) ($link['title'] ?? ''),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

					<?php if (
						($link['provider'] ?? '') === 'youtube'
					): ?>

						<div class="ping-link-play">
							<i
								class="fa fa-play"
								aria-hidden="true"
							></i>
						</div>

					<?php endif; ?>

				</div>

			<?php endif; ?>

			<div class="ping-link-body">

				<?php
				$badge = match ($link['type'] ?? 'link') {
					'video' => $t('ping.card.link.video'),
					'audio' => $t('ping.card.link.audio'),
					default => !empty($link['provider'])
						? strtoupper(
							(string) $link['provider']
						)
						: $t('ping.card.link.default'),
				};
				?>

				<div class="ping-link-badge">

					<i
						class="fa <?= ($link['type'] ?? '') === 'video'
							? 'fa-play-circle'
							: (
								($link['type'] ?? '') === 'audio'
									? 'fa-music'
									: 'fa-link'
							) ?>"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$badge,
						ENT_QUOTES,
						'UTF-8'
					) ?>

				</div>

				<?php if (
					!empty($link['site_name'])
					|| !empty($link['favicon'])
				): ?>

					<div class="ping-link-site">

						<?php if (!empty($link['favicon'])): ?>

							<img
								class="ping-link-favicon"
								src="<?= htmlspecialchars(
									(string) $link['favicon'],
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								alt=""
								loading="lazy"
								referrerpolicy="no-referrer"
							>

						<?php endif; ?>

						<?php if (!empty($link['site_name'])): ?>

							<span>
								<?= htmlspecialchars(
									(string) $link['site_name'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

				<?php endif; ?>

				<?php if (!empty($link['title'])): ?>

					<div class="ping-link-title">
						<?= htmlspecialchars(
							(string) $link['title'],
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</div>

				<?php endif; ?>

				<?php if (!empty($link['description'])): ?>

					<div class="ping-link-description">
						<?= htmlspecialchars(
							(string) $link['description'],
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</div>

				<?php endif; ?>

			</div>

		</a>

	<?php
		}

	endif;
	?>

	<?php if (!$moderationMode): ?>

		<div class="ping-actions">

			<a
				class="ping-action"
				href="/ping/<?= rawurlencode(
					(string) ($post['uuid'] ?? '')
				) ?>"
			>
				<i
					class="fa-regular fa-comments"
					aria-hidden="true"
				></i>

				<?= (int) ($post['comments_count'] ?? 0) ?>
				<?= htmlspecialchars(
					$t('ping.card.comments'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

			<?php if ($canDogeTipAuthor): ?>

				<button
					type="button"
					class="ping-action ping-doge-tip-action js-doge-tip"
					data-doge-address="<?= htmlspecialchars(
						$dogeTipAddress,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					data-doge-username="<?= htmlspecialchars(
						$authorUsername,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					data-doge-context="ping"
					data-doge-post-uuid="<?= htmlspecialchars(
						(string) ($post['uuid'] ?? ''),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<span
						class="ping-doge-tip-action-icon"
						aria-hidden="true"
					>
						Ð
					</span>

					<span>
						<?= htmlspecialchars(
							$t('ping.card.doge.tip'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>
				</button>

			<?php endif; ?>

			<details class="ping-more-menu">

				<summary
					class="ping-action ping-more-toggle"
					aria-label="<?= htmlspecialchars(
						$t('ping.card.actions.more'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					title="<?= htmlspecialchars(
						$t('ping.card.actions.more'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<i
						class="fa-solid fa-ellipsis"
						aria-hidden="true"
					></i>
				</summary>

				<div class="ping-more-dropdown">

					<form
						method="post"
						action="/ping/<?= rawurlencode(
							(string) ($post['uuid'] ?? '')
						) ?>/<?= !empty($post['is_saved'])
							? 'unsave'
							: 'save' ?>"
						class="ping-more-form"
					>
						<button
							type="submit"
							class="ping-more-action <?= !empty(
								$post['is_saved']
							)
								? 'is-active'
								: '' ?>"
						>
							<i
								class="<?= !empty($post['is_saved'])
									? 'fa-solid'
									: 'fa-regular' ?> fa-bookmark"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									!empty($post['is_saved'])
										? $t('ping.card.actions.unsave')
										: $t('ping.card.actions.save'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>
						</button>
					</form>

					<?php if (!empty($post['can_edit'])): ?>

						<button
							type="button"
							class="ping-more-action ping-edit-button"
						>
							<i
								class="fa-solid fa-pen"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$t('ping.card.actions.edit'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>
						</button>

						<span
							class="ping-edit-timer ping-more-timer"
							data-edit-expires-at="<?= (int) (
								$post['edit_expires_at']
								?? 0
							) ?>"
						>
						</span>

					<?php endif; ?>

					<?php if (!$isChanzine): ?>

						<?php if ($canBlockAuthor): ?>

							<div class="ping-more-separator"></div>

							<form
								method="post"
								action="/profile/<?= rawurlencode(
									$authorUsername
								) ?>/block"
								class="ping-more-form"
							>

								<button
									type="submit"
									class="ping-more-action"
								>

									<i
										class="fa-solid fa-user-slash"
										aria-hidden="true"
									></i>

									<span>
										<?= htmlspecialchars(
											$t('ping.card.actions.block_user'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								</button>

							</form>

						<?php endif; ?>

						<button
							type="button"
							class="ping-more-action ping-report-button js-open-report-modal"
							data-report-type="ping"
							data-report-uuid="<?= htmlspecialchars(
								(string) ($post['uuid'] ?? ''),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							data-report-author="<?= htmlspecialchars(
								$authorName,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>
							<i
								class="fa-regular fa-flag"
								aria-hidden="true"
							></i>

							<span>
								<?= htmlspecialchars(
									$t('ping.card.actions.report'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>
						</button>

					<?php endif; ?>

					<?php if (!empty($post['can_delete'])): ?>

						<div class="ping-more-separator"></div>

						<form
							method="post"
							action="/ping/<?= rawurlencode(
								(string) ($post['uuid'] ?? '')
							) ?>/delete"
							class="ping-more-form ping-delete-form"
							onsubmit="return confirm(<?= htmlspecialchars(
								json_encode(
									$t('ping.card.actions.delete_confirm'),
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
								class="ping-more-action ping-more-danger ping-delete-button"
							>
								<i
									class="fa-regular fa-trash-can"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$t('ping.card.actions.delete'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>
							</button>
						</form>

					<?php endif; ?>

				</div>

			</details>

			<div class="ping-votes">

				<form
					method="post"
					action="/ping/<?= rawurlencode(
						(string) ($post['uuid'] ?? '')
					) ?>/upvote"
				>
					<button
						type="submit"
						class="ping-vote ping-vote-up <?= (
							($post['user_vote'] ?? 0) == 1
						)
							? 'active'
							: '' ?>"
						aria-label="<?= htmlspecialchars(
							$t('ping.card.actions.upvote'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						▲
					</button>
				</form>

				<span class="ping-score">
					<?= (int) ($post['score'] ?? 0) ?>
				</span>

				<form
					method="post"
					action="/ping/<?= rawurlencode(
						(string) ($post['uuid'] ?? '')
					) ?>/downvote"
				>
					<button
						type="submit"
						class="ping-vote ping-vote-down <?= (
							($post['user_vote'] ?? 0) == -1
						)
							? 'active'
							: '' ?>"
						aria-label="<?= htmlspecialchars(
							$t('ping.card.actions.downvote'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						▼
					</button>
				</form>

			</div>

		</div>

	<?php endif; ?>

</div>
