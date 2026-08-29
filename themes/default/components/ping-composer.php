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

			<div class="ping-composer-actions">

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
