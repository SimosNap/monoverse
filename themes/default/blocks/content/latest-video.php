<?php
declare(strict_types=1);

/** @var string $title */
/** @var bool $show_author */
/** @var array $posts */

$widgetTitle = trim($title ?? '');
?>

<section class="mv-widget mv-widget-latest-video">

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
				$t('blocks.content.latest_video.empty'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	<?php else: ?>

		<div class="mv-latest-video-list">

			<?php foreach ($posts as $post): ?>

				<?php
				$video = null;

				foreach (($post['media'] ?? []) as $media) {
					if (($media['media_type'] ?? '') === 'video') {
						$video = $media;
						break;
					}
				}

				if (!$video) {
					continue;
				}

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

				$filename = trim(
					(string) (
						$video['original_name']
						?? $t(
							'blocks.content.latest_video.file_fallback'
						)
					)
				);

				$previewUrl = trim(
					(string) (
						$video['preview_url']
						?? ''
					)
				);
				?>

				<article class="mv-latest-video-item">

					<a
						class="mv-latest-video-link"
						href="/ping/<?= rawurlencode($postUuid) ?>"
					>

						<div class="mv-latest-video-preview">

							<?php if ($previewUrl !== ''): ?>

								<img
									src="<?= htmlspecialchars(
										$previewUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									alt=""
									loading="lazy"
								>

							<?php else: ?>

								<i
									class="fa-solid fa-video"
									aria-hidden="true"
								></i>

							<?php endif; ?>

							<span class="mv-latest-video-play">
								<i
									class="fa-solid fa-play"
									aria-hidden="true"
								></i>
							</span>

						</div>

						<div class="mv-latest-video-content">

							<strong class="mv-latest-video-title">
								<?= htmlspecialchars(
									$filename,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

							<div class="mv-latest-video-meta">

								<?php if ($show_author && $author !== ''): ?>

									<span>
										<?= htmlspecialchars(
											$author,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

								<?php if (!empty($video['formatted_size'])): ?>

									<span>
										<?= htmlspecialchars(
											(string) $video['formatted_size'],
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

							</div>

						</div>

					</a>

				</article>

			<?php endforeach; ?>

		</div>

		<a
			class="mv-latest-video-more"
			href="/ping?feed=video"
		>
			<?= htmlspecialchars(
				$t('blocks.content.latest_video.all'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	<?php endif; ?>

</section>
