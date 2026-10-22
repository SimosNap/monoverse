<?php if (!empty($post['media'])): ?>

	<?php $mediaCount = count($post['media']); ?>

	<div
		class="ping-media ping-layout-<?= $mediaCount ?>"
	>

		<?php foreach ($post['media'] as $loopIndex => $media): ?>

			<?php if ($media['media_type'] === 'image'): ?>

				<div class="ping-media-item">

					<a
						class="ping-image-link"
						href="<?= htmlspecialchars($media['public_url']) ?>"
						data-full="<?= htmlspecialchars(
							preg_replace(
								'/\.webp$/i',
								'.' . pathinfo($media['storage_path'], PATHINFO_EXTENSION),
								$media['public_url']
							)
						) ?>"
						data-post="<?= (int) $post['id'] ?>"
						data-index="<?= $loopIndex ?? 0 ?>"
					>

						<img
							class="ping-image"
							src="<?= htmlspecialchars($media['public_url']) ?>"
							alt="<?= htmlspecialchars($media['original_name'] ?? '') ?>"
							loading="lazy"
						>

					</a>

				</div>

			<?php elseif ($media['media_type'] === 'video'): ?>

				<div class="ping-media-item ping-video-item">

					<video
						class="ping-video"
						controls
						preload="metadata"
					>
						<source
							src="<?= htmlspecialchars($media['public_url']) ?>"
							type="<?= htmlspecialchars($media['mime_type'] ?? '') ?>"
						>

						<?= htmlspecialchars(
							$t('ping.card.media.video_unsupported'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</video>

				</div>

			<?php elseif ($media['media_type'] === 'audio'): ?>

				<div class="ping-media-item ping-audio-item">

					<div
						class="ping-audio-player"
						data-audio-player
					>

						<audio
							class="ping-audio-engine"
							preload="metadata"
						>
							<source
								src="<?= htmlspecialchars($media['public_url']) ?>"
								type="<?= htmlspecialchars($media['mime_type'] ?? '') ?>"
							>

							<?= htmlspecialchars(
								$t('ping.card.media.audio_unsupported'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</audio>

						<div class="ping-audio-top">

							<button
								type="button"
								class="ping-audio-play"
								aria-label="<?= htmlspecialchars(
									$t('ping.card.media.play'),
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								data-audio-play
							>
								<i
									class="fa-solid fa-play"
									aria-hidden="true"
								></i>
							</button>

							<div class="ping-audio-info">

								<div class="ping-audio-title">
									<?= htmlspecialchars(
										!empty($media['audio_title'])
											? $media['audio_title']
											: (
												$media['original_name']
												?? $t('ping.card.media.audio_file')
											)
									) ?>
								</div>

								<div class="ping-audio-meta">

									<?php if (!empty($media['audio_artist'])): ?>

										<span class="ping-audio-artist">
											<?= htmlspecialchars(
												$media['audio_artist']
											) ?>
										</span>

									<?php else: ?>

										<span>
											<?= htmlspecialchars(
												$t('ping.card.media.audio'),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

									<?php endif; ?>

									<?php if (!empty($media['formatted_size'])): ?>

										<span>
											<?= htmlspecialchars(
												$media['formatted_size']
											) ?>
										</span>

									<?php endif; ?>

								</div>

							</div>

						</div>

						<div
							class="ping-audio-waveform"
							data-audio-waveform
							<?php if (!empty($media['waveform_url'])): ?>
								data-waveform-url="<?= htmlspecialchars(
									$media['waveform_url'],
									ENT_QUOTES,
									'UTF-8'
								) ?>"
							<?php endif; ?>
						>

							<?php if (!empty($media['waveform_url'])): ?>

								<img
									class="ping-audio-waveform-image"
									src="<?= htmlspecialchars(
										$media['waveform_url'],
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									alt=""
									loading="lazy"
								>

							<?php else: ?>

								<div class="ping-audio-waveform-fallback">

									<i
										class="fa-solid fa-wave-square"
										aria-hidden="true"
									></i>

									<span>
										<?= htmlspecialchars(
											$t('ping.card.media.waveform_unavailable'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								</div>

							<?php endif; ?>

							<div
								class="ping-audio-waveform-progress"
								data-audio-progress
							>

								<?php if (!empty($media['waveform_url'])): ?>

									<img
										class="ping-audio-waveform-image ping-audio-waveform-image-progress"
										src="<?= htmlspecialchars(
											$media['waveform_url'],
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										alt=""
										loading="lazy"
									>

								<?php endif; ?>

							</div>

						</div>

						<div class="ping-audio-bottom">

							<button
								type="button"
								class="ping-audio-volume"
								aria-label="<?= htmlspecialchars(
									$t('ping.card.media.mute'),
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								data-audio-volume
							>
								<i
									class="fa-solid fa-volume-high"
									aria-hidden="true"
								></i>
							</button>

							<input
								type="range"
								class="ping-audio-volume-range"
								min="0"
								max="1"
								step="0.01"
								value="1"
								aria-label="<?= htmlspecialchars(
									$t('ping.card.media.volume'),
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								data-audio-volume-range
							>

						</div>

						<?php if (!empty($media['audio_tracklist'])): ?>

							<details class="ping-audio-tracklist">

								<summary class="ping-audio-tracklist-toggle">

									<span>
										<i
											class="fa-solid fa-list-music"
											aria-hidden="true"
										></i>

										<?= htmlspecialchars(
											$t('ping.card.media.tracklist'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

									<i
										class="fa-solid fa-chevron-down ping-audio-tracklist-chevron"
										aria-hidden="true"
									></i>

								</summary>

								<div class="ping-audio-tracklist-content">
									<?= nl2br(
										htmlspecialchars(
											$media['audio_tracklist'],
											ENT_QUOTES,
											'UTF-8'
										)
									) ?>
								</div>

							</details>

						<?php endif; ?>

					</div>

				</div>

			<?php elseif ($media['media_type'] === 'document'): ?>

				<div class="ping-media-item ping-document-item">

					<a
						class="ping-document-link"
						href="<?= htmlspecialchars($media['public_url']) ?>"
						target="_blank"
						rel="noopener"
					>

						<div class="ping-document-icon">
							<i class="fa-solid fa-file-pdf"></i>
						</div>

						<div class="ping-document-body">

							<div class="ping-document-title">
								<?= htmlspecialchars($media['original_name']) ?>
							</div>

							<div class="ping-document-meta">
								PDF • <?= htmlspecialchars($media['formatted_size']) ?>
							</div>

						</div>

					</a>

				</div>

			<?php endif; ?>

		<?php endforeach; ?>

	</div>

<?php endif; ?>
