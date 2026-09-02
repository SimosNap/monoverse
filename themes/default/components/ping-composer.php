<form
	method="post"
	action="/ping"
	class="ping-composer"
	enctype="multipart/form-data"
>

	<div class="ping-composer-header">

		<div class="ping-avatar">

			<?php if (
				!empty($profile['show_avatar']) &&
				!empty($profile['avatar_url'])
			): ?>

				<img
					src="<?= htmlspecialchars($profile['avatar_url']) ?>"
					alt="@<?= htmlspecialchars($profile['username']) ?>"
				>

			<?php else: ?>

				👤

			<?php endif; ?>

		</div>

		<div class="ping-composer-body">

			<textarea
				class="ping-composer-textarea"
				name="content"
				rows="4"
				maxlength="1000"
				<?= (($settings['media_require_text_with_audio_video'] ?? '1') === '1')
					? 'required'
					: '' ?>
				placeholder="<?= htmlspecialchars(
					$t('ping.composer.placeholder'),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			></textarea>

			<div class="ping-composer-footer">

				<span class="ping-character-counter">
					0 / 1000
				</span>

			</div>

			<?php require __DIR__ . '/ping-media-upload.php'; ?>

			<div
				class="ping-code-composer"
				data-ping-code-composer
				hidden
			>

				<div class="ping-code-composer-header">

					<label for="ping-code-language">
						<?= htmlspecialchars(
							$t('ping.composer.code_language'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</label>

					<select
						id="ping-code-language"
						name="code_language"
						class="ping-code-language"
					>
						<option value="text">Text</option>
						<option value="php">PHP</option>
						<option value="javascript">JavaScript</option>
						<option value="html">HTML</option>
						<option value="css">CSS</option>
						<option value="sql">SQL</option>
						<option value="bash">Bash</option>
						<option value="python">Python</option>
						<option value="c">C</option>
						<option value="cpp">C++</option>
						<option value="java">Java</option>
						<option value="json">JSON</option>
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
				></textarea>

				<div class="ping-code-counter">
					0 / 10000
				</div>

			</div>

			<div class="ping-composer-actions">

				<div class="ping-composer-tools">

					<button
						type="button"
						class="ping-composer-attachments-toggle"
						aria-expanded="false"
						title="<?= htmlspecialchars(
							$t('ping.composer.media.attachments'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						aria-label="<?= htmlspecialchars(
							$t('ping.composer.media.attachments'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						<i
							class="fa-solid fa-upload"
							aria-hidden="true"
						></i>
					</button>

					<button
						type="button"
						class="ping-code-toggle"
						data-ping-code-toggle
						aria-expanded="false"
						title="<?= htmlspecialchars(
							$t('ping.composer.code'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						aria-label="<?= htmlspecialchars(
							$t('ping.composer.code'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						<i
							class="fa-solid fa-code"
							aria-hidden="true"
						></i>
					</button>

				</div>

				<button
					type="submit"
					class="ping-composer-submit"
				>
					<?= htmlspecialchars(
						$t('ping.composer.publish'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</button>

			</div>

		</div>

	</div>

</form>
