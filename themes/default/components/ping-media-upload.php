<div class="ping-composer-actions">

	<button
		type="button"
		class="ping-composer-attachments-toggle"
		aria-expanded="false"
	>
		<i class="fa-solid fa-paperclip" aria-hidden="true"></i>

		<span class="ping-composer-attachments-label">
			<?= htmlspecialchars(
				$t('ping.composer.media.attachments'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</span>
	</button>

</div>

<div
	class="ping-composer-media"
	data-audio-upload-enabled="<?= (($settings['media_audio_upload_enabled'] ?? '1') === '1') ? '1' : '0' ?>"
	data-audio-max-mb="<?= htmlspecialchars(
		(string) ($settings['media_audio_max_mb'] ?? '50'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-video-upload-enabled="<?= (($settings['media_video_upload_enabled'] ?? '1') === '1') ? '1' : '0' ?>"
	data-video-max-mb="<?= htmlspecialchars(
		(string) ($settings['media_video_max_mb'] ?? '50'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	hidden
>

	<div class="ping-composer-media-header">

		<strong>
			<?= htmlspecialchars(
				$t('ping.composer.media.attach_files'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</strong>

		<button
			type="button"
			class="ping-composer-media-close"
			aria-label="<?= htmlspecialchars(
				$t('ping.composer.media.close'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			&times;
		</button>

	</div>

	<div class="ping-composer-media-info">

		<span class="ping-composer-media-allowed">
			<?= htmlspecialchars(
				$t('ping.composer.media.allowed'),
				ENT_QUOTES,
				'UTF-8'
			) ?>

			<?= htmlspecialchars(
				$t('ping.composer.media.images'),
				ENT_QUOTES,
				'UTF-8'
			) ?>,

			<?= htmlspecialchars(
				$t('ping.composer.media.pdf'),
				ENT_QUOTES,
				'UTF-8'
			) ?>

			<?php if (($settings['media_audio_upload_enabled'] ?? '1') === '1'): ?>

				,
				<?= htmlspecialchars(
					$t('ping.composer.media.audio_up_to'),
					ENT_QUOTES,
					'UTF-8'
				) ?>

				<?= htmlspecialchars(
					(string) ($settings['media_audio_max_mb'] ?? '50'),
					ENT_QUOTES,
					'UTF-8'
				) ?> MB

			<?php endif; ?>

			<?php if (($settings['media_video_upload_enabled'] ?? '1') === '1'): ?>

				,
				<?= htmlspecialchars(
					$t('ping.composer.media.video_up_to'),
					ENT_QUOTES,
					'UTF-8'
				) ?>

				<?= htmlspecialchars(
					(string) ($settings['media_video_max_mb'] ?? '50'),
					ENT_QUOTES,
					'UTF-8'
				) ?> MB

			<?php endif; ?>.
		</span>

		<div
			class="ping-composer-media-error"
			data-media-error
			hidden
		></div>

	</div>

	<label class="ping-composer-dropzone">

		<input
			type="file"
			name="media[]"
			accept="image/*<?= (($settings['media_video_upload_enabled'] ?? '1') === '1') ? ',video/*' : '' ?><?= (($settings['media_audio_upload_enabled'] ?? '1') === '1') ? ',audio/*' : '' ?>,.pdf"
			multiple
		>

		<span
			class="ping-composer-dropzone-icon"
			aria-hidden="true"
		>
			📎
		</span>

		<span class="ping-composer-dropzone-title">
			<?= htmlspecialchars(
				$t('ping.composer.media.dropzone_title'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</span>

		<span class="ping-composer-dropzone-help">
			<?= htmlspecialchars(
				$t('ping.composer.media.dropzone_help'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</span>

	</label>

	<div class="ping-media-preview"></div>

	<div
		class="ping-audio-metadata"
		data-audio-metadata
		hidden
	>

		<div class="ping-audio-metadata-header">

			<strong>
				<?= htmlspecialchars(
					$t('ping.composer.media.audio.details'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</strong>

			<span>
				<?= htmlspecialchars(
					$t('ping.composer.media.audio.optional'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

		</div>

		<div class="ping-audio-metadata-fields">

			<label class="ping-audio-metadata-field">

				<span>
					<?= htmlspecialchars(
						$t('ping.composer.media.audio.title'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<input
					type="text"
					name="audio_title"
					maxlength="255"
					autocomplete="off"
					placeholder="<?= htmlspecialchars(
						$t('ping.composer.media.audio.title_placeholder'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

			</label>

			<label class="ping-audio-metadata-field">

				<span>
					<?= htmlspecialchars(
						$t('ping.composer.media.audio.artist'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<input
					type="text"
					name="audio_artist"
					maxlength="255"
					autocomplete="off"
					placeholder="<?= htmlspecialchars(
						$t('ping.composer.media.audio.artist_placeholder'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

			</label>

			<label class="ping-audio-metadata-field ping-audio-metadata-tracklist">

				<span>
					<?= htmlspecialchars(
						$t('ping.composer.media.audio.tracklist'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

				<textarea
					name="audio_tracklist"
					rows="6"
					placeholder="<?= htmlspecialchars(
						$t('ping.composer.media.audio.tracklist_placeholder'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				></textarea>

			</label>

		</div>

	</div>

	<div
		class="ping-upload-progress"
		data-upload-progress
		hidden
	>

		<div class="ping-upload-progress-header">

			<span data-upload-status>
				<?= htmlspecialchars(
					$t('ping.composer.media.upload.uploading'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

			<strong data-upload-percent>
				0%
			</strong>

		</div>

		<div
			class="ping-upload-progress-track"
			role="progressbar"
			aria-label="<?= htmlspecialchars(
				$t('ping.composer.media.upload.progress'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			aria-valuemin="0"
			aria-valuemax="100"
			aria-valuenow="0"
			data-upload-progressbar
		>
			<div
				class="ping-upload-progress-bar"
				data-upload-progress-bar
			></div>
		</div>

		<div class="ping-upload-progress-details">
			<span data-upload-bytes>
				0 MB / 0 MB
			</span>
		</div>

	</div>

</div>
