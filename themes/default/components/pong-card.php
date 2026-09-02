<?php
declare(strict_types=1);

$moderationMode = !empty($moderationMode);

$authorUsername = trim(
	(string) ($comment['username'] ?? '')
);

$authorNickname = trim(
	(string) ($comment['nickname'] ?? '')
);

$authorName = $authorUsername !== ''
	? $authorUsername
	: (
		$authorNickname !== ''
			? $authorNickname
			: $t('ping.pong.user')
	);

$authorAccountExists = !array_key_exists(
	'external_account_exists',
	$comment
) || !empty($comment['external_account_exists']);

$authorHasPublicProfile =
	$authorAccountExists
	&& !empty($comment['public_profile'])
	&& $authorUsername !== '';

$authUser = $session->get('auth.user');

$canBlockAuthor =
	!empty($authUser['sub'])
	&& $authorAccountExists
	&& $authorUsername !== ''
	&& !empty($comment['author_sub'])
	&& (string) $authUser['sub']
		!== (string) $comment['author_sub'];

$commentMetadata = [];

if (!empty($comment['metadata'])) {
	$decodedMetadata = json_decode(
		(string) $comment['metadata'],
		true
	);

	if (is_array($decodedMetadata)) {
		$commentMetadata = $decodedMetadata;
	}
}

$isDogeTip = (
	($commentMetadata['source'] ?? '') === 'doge_tip'
);

$dogeTipAmount = trim(
	(string) ($commentMetadata['amount'] ?? '')
);

$dogeTipRecipient = trim(
	(string) ($commentMetadata['recipient_username'] ?? '')
);
?>

<div class="pong-card">

	<div class="pong-header">

		<?php if ($authorHasPublicProfile): ?>

			<a
				class="pong-avatar"
				href="/profile/<?= rawurlencode(
					$authorUsername
				) ?>"
			>

				<?php if (
					!empty($comment['show_avatar'])
					&& !empty($comment['avatar_url'])
				): ?>

					<img
						src="<?= htmlspecialchars(
							(string) $comment['avatar_url'],
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

			<span class="pong-avatar pong-avatar-static">

				<?php if (
					$authorAccountExists
					&& !empty($comment['show_avatar'])
					&& !empty($comment['avatar_url'])
				): ?>

					<img
						src="<?= htmlspecialchars(
							(string) $comment['avatar_url'],
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

		<div class="pong-author">

			<div class="pong-author-name">

				<?php if ($authorAccountExists): ?>

					<?= $component('user-presence', [
						'presence' => $comment['presence'] ?? [],
					]) ?>

				<?php endif; ?>

				<?php if ($authorHasPublicProfile): ?>

					<a
						class="pong-username"
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

					<span class="pong-username pong-username-static">
						@<?= htmlspecialchars(
							$authorName,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php else: ?>

					<span class="pong-username pong-username-missing">
						<?= htmlspecialchars(
							$t('ping.pong.author.unavailable'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if (!$authorHasPublicProfile): ?>

				<p class="pong-author-status">

					<?php if ($authorAccountExists): ?>

						<i
							class="fa-solid fa-lock"
							aria-hidden="true"
						></i>

						<span>
							<?= htmlspecialchars(
								$t('ping.pong.author.private_profile'),
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
								$t('ping.pong.author.account_missing'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php endif; ?>

				</p>

			<?php endif; ?>

			<div class="pong-date">
				<?= htmlspecialchars(
					(string) (
						$comment['created_at_formatted']
						?? ''
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</div>

		</div>

	</div>

	<div
		class="pong-content"
		data-content
	>

		<?php if (
			$isDogeTip
			&& $dogeTipAmount !== ''
			&& $dogeTipRecipient !== ''
		): ?>

			<div class="pong-doge-tip">

				<span
					class="pong-doge-tip-icon"
					aria-hidden="true"
				>
					Ð
				</span>

				<div class="pong-doge-tip-info">

					<span class="pong-doge-tip-label">
						<?= htmlspecialchars(
							$t('ping.pong.doge.tip_label'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<div class="pong-doge-tip-value">

						<strong>
							<?= htmlspecialchars(
								$dogeTipAmount,
								ENT_QUOTES,
								'UTF-8'
							) ?> DOGE
						</strong>

						<span aria-hidden="true">
							→
						</span>

						<a
							href="/profile/<?= rawurlencode(
								$dogeTipRecipient
							) ?>"
						>
							@<?= htmlspecialchars(
								$dogeTipRecipient,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</a>

					</div>

				</div>

			</div>

		<?php endif; ?>

		<?php if (
			trim(
				(string) ($comment['content'] ?? '')
			) !== ''
		): ?>

			<div class="<?= $isDogeTip
				? 'pong-doge-tip-message'
				: ''
			?>">

				<?= nl2br(
					\Monoverse\Helpers\TextHelper::linkMentions(
						(string) ($comment['content'] ?? '')
					)
				) ?>

			</div>

		<?php endif; ?>

		<?php if (!empty($comment['code_block'])): ?>

			<?php
			$codeBlock = $comment['code_block'];

			require __DIR__ . '/code-block.php';
			?>

		<?php endif; ?>
	</div>

	<?php if (!empty($comment['can_edit'])): ?>

		<div
			class="pong-editor"
			style="display:none;"
		>

			<form
				method="post"
				action="/pong/<?= rawurlencode(
					(string) ($comment['uuid'] ?? '')
				) ?>/update"
			>

				<textarea
					name="content"
					rows="4"
				><?= htmlspecialchars(
					(string) ($comment['content'] ?? ''),
					ENT_QUOTES,
					'UTF-8'
				) ?></textarea>

				<?php
				$pongEditCodeBlock = is_array(
					$comment['code_block'] ?? null
				)
					? $comment['code_block']
					: [];

				$pongEditCode = (string) (
					$pongEditCodeBlock['code']
					?? ''
				);

				$pongEditCodeLanguage = (string) (
					$pongEditCodeBlock['language']
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
									<?= $pongEditCodeLanguage === $value
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
						$pongEditCode,
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
							$t('ping.pong.editor.save'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

					<button
						type="button"
						class="btn btn-secondary pong-edit-cancel"
					>
						<?= htmlspecialchars(
							$t('ping.pong.editor.cancel'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

				</div>

			</form>

		</div>

	<?php endif; ?>

	<?php if (!$moderationMode): ?>

		<div class="ping-actions">

			<?php if (!empty($comment['can_edit'])): ?>

				<button
					type="button"
					class="ping-action pong-edit-button"
				>
					<i
						class="fa-solid fa-pen"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t('ping.pong.actions.edit'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

				<span
					class="ping-edit-timer"
					data-edit-expires-at="<?= (int) (
						$comment['edit_expires_at']
						?? 0
					) ?>"
				>
				</span>

			<?php endif; ?>

			<?php if (!empty($comment['can_delete'])): ?>

				<form
					method="post"
					action="/pong/<?= rawurlencode(
						(string) ($comment['uuid'] ?? '')
					) ?>/delete"
					class="pong-delete-form"
					onsubmit="return confirm(<?= htmlspecialchars(
						json_encode(
							$t('ping.pong.actions.delete_confirm'),
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
						class="ping-action ping-delete-button"
					>
						<i
							class="fa-solid fa-trash"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t('ping.pong.actions.delete'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>
				</form>

			<?php endif; ?>

			<?php if (!empty($session->get('auth.user'))): ?>

				<button
					type="button"
					class="ping-action js-open-report-modal"
					data-report-type="pong"
					data-report-uuid="<?= htmlspecialchars(
						(string) ($comment['uuid'] ?? ''),
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

					<?= htmlspecialchars(
						$t('ping.pong.actions.report'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

				<?php if ($canBlockAuthor): ?>

					<form
						method="post"
						action="/profile/<?= rawurlencode(
							$authorUsername
						) ?>/block"
						onsubmit="return confirm(<?= htmlspecialchars(
							json_encode(
								$t('ping.pong.actions.block_confirm'),
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
							class="ping-action"
						>
							<i
								class="fa-solid fa-ban"
								aria-hidden="true"
							></i>

							<?= htmlspecialchars(
								$t('ping.pong.actions.block_user'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</button>
					</form>

				<?php endif; ?>

			<?php endif; ?>

		</div>

	<?php endif; ?>

</div>
