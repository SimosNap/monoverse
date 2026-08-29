<?php
declare(strict_types=1);

/** @var string $title */
/** @var bool $show_author */
/** @var array $posts */

$widgetTitle = trim($title ?? '');
?>

<section class="mv-widget mv-widget-latest-audio">

	<?php if ($widgetTitle !== ''): ?>

		<div class="mv-widget-header">

			<h3>
				<?= htmlspecialchars(
					$widgetTitle,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h3>

		</div>

	<?php endif; ?>

	<?php if ($posts === []): ?>

		<p class="mv-widget-empty">
			<?= htmlspecialchars(
				$t('blocks.content.latest_audio.empty'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	<?php else: ?>

		<div class="mv-latest-audio-list">

			<?php foreach ($posts as $post): ?>

				<?php
				$audio = null;

				foreach (($post['media'] ?? []) as $media) {
					if (($media['media_type'] ?? '') === 'audio') {
						$audio = $media;
						break;
					}
				}

				if (!$audio) {
					continue;
				}

				$audioTitle = trim(
					(string) (
						$audio['audio_title']
						?? ''
					)
				);

				if ($audioTitle === '') {
					$audioTitle = trim(
						(string) (
							$audio['original_name']
							?? $t(
								'blocks.content.latest_audio.file_fallback'
							)
						)
					);
				}

				$audioArtist = trim(
					(string) (
						$audio['audio_artist']
						?? ''
					)
				);

				$postUuid = (string) (
					$post['uuid']
					?? ''
				);

				$author = trim(
					(string) (
						$post['nickname']
						?? $post['username']
						?? ''
					)
				);
				?>

				<article class="mv-latest-audio-item">

					<a
						class="mv-latest-audio-link"
						href="/ping/<?= rawurlencode($postUuid) ?>"
						<?php if (!empty($audio['waveform_url'])): ?>
							style="--mv-audio-waveform: url('<?= htmlspecialchars(
								(string) $audio['waveform_url'],
								ENT_QUOTES,
								'UTF-8'
							) ?>');"
						<?php endif; ?>
					>

						<div class="mv-latest-audio-icon">
							<i
								class="fa-solid fa-wave-square"
								aria-hidden="true"
							></i>
						</div>

						<div class="mv-latest-audio-content">

							<strong class="mv-latest-audio-title">
								<?= htmlspecialchars(
									$audioTitle,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

							<div class="mv-latest-audio-meta">

								<?php if ($audioArtist !== ''): ?>

									<span>
										<?= htmlspecialchars(
											$audioArtist,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php elseif ($show_author && $author !== ''): ?>

									<span>
										<?= htmlspecialchars(
											$author,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

								<?php if (!empty($audio['formatted_size'])): ?>

									<span>
										<?= htmlspecialchars(
											(string) $audio['formatted_size'],
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

							</div>

						</div>

						<i
							class="fa-solid fa-chevron-right mv-latest-audio-arrow"
							aria-hidden="true"
						></i>

					</a>

				</article>

			<?php endforeach; ?>

		</div>

		<a
			class="mv-latest-audio-more"
			href="/ping?feed=audio"
		>
			<?= htmlspecialchars(
				$t('blocks.content.latest_audio.all'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	<?php endif; ?>

</section>
