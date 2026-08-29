<?php
declare(strict_types=1);

$title = trim(
	(string) (
		$title
		?? $t('blocks.webradio.azuracast.default_title')
	)
);

$nowPlayingData = is_array($now_playing ?? null)
	? $now_playing
	: [];

$historyLimit = (int) ($history_limit ?? 5);

if (!in_array($historyLimit, [5, 10], true)) {
	$historyLimit = 5;
}

$showHistory = (bool) ($show_history ?? true);

$playerStyle = trim(
	(string) ($player_style ?? 'modern')
);

if (!in_array(
	$playerStyle,
	['modern', 'led', 'analog', 'minimal'],
	true
)) {
	$playerStyle = 'modern';
}

$station = is_array($nowPlayingData['station'] ?? null)
	? $nowPlayingData['station']
	: [];

$nowPlaying = is_array($nowPlayingData['now_playing'] ?? null)
	? $nowPlayingData['now_playing']
	: [];

$currentSong = is_array($nowPlaying['song'] ?? null)
	? $nowPlaying['song']
	: [];

$songHistory = is_array($nowPlayingData['song_history'] ?? null)
	? array_slice(
		$nowPlayingData['song_history'],
		0,
		$historyLimit
	)
	: [];

$stationName = trim(
	(string) ($station['name'] ?? '')
);

$streamUrl = trim(
	(string) ($station['listen_url'] ?? '')
);

$currentTitle = trim(
	(string) ($currentSong['title'] ?? '')
);

$currentArtist = trim(
	(string) ($currentSong['artist'] ?? '')
);

$currentText = trim(
	(string) ($currentSong['text'] ?? '')
);

$currentArt = trim(
	(string) ($currentSong['art'] ?? '')
);

$currentDisplayTitle = $currentTitle !== ''
	? $currentTitle
	: (
		$currentText !== ''
			? $currentText
			: $t(
				'blocks.webradio.azuracast.track_unavailable'
			)
	);

$blockWidth = (int) ($block['width'] ?? 12);

if (!in_array(
	$blockWidth,
	[3, 4, 6, 8, 9, 12],
	true
)) {
	$blockWidth = 12;
}

$widthClass = 'mv-block-width-' . $blockWidth;
?>

<div
	class="mv-widget mv-azuracast-widget mv-azuracast-style-<?= htmlspecialchars(
		$playerStyle,
		ENT_QUOTES,
		'UTF-8'
	) ?> <?= htmlspecialchars(
		$widthClass,
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-play="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.play'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-pause="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.pause'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-playing="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.playing'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-paused="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.paused'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-ready="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.ready'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-mute="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.mute'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-unmute="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.unmute'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-unavailable="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.stream_unavailable'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-connecting="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.connecting'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-start-failed="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.start_failed'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-slow-connection="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.slow_connection'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-player-unavailable="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.player_unavailable'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-detach="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.detach'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-detached="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast.detached'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
>

	<header class="mv-azuracast-header">

		<h3>
			<?= htmlspecialchars(
				$title,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h3>

	</header>

	<?php if ($nowPlayingData !== []): ?>

		<?php if ($streamUrl !== ''): ?>

			<div class="mv-azuracast-player">

				<audio
					class="mv-azuracast-audio"
					preload="none"
				>
					<source
						src="<?= htmlspecialchars(
							$streamUrl,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
				</audio>

				<div class="mv-azuracast-deck">

					<div class="mv-azuracast-deck-cover">

						<?php if ($currentArt !== ''): ?>

							<img
								src="<?= htmlspecialchars(
									$currentArt,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								alt=""
								loading="lazy"
							>

						<?php else: ?>

							<span class="mv-azuracast-deck-cover-placeholder">
								<i
									class="fa-solid fa-radio"
									aria-hidden="true"
								></i>
							</span>

						<?php endif; ?>

						<button
							type="button"
							class="mv-azuracast-play"
							aria-label="<?= htmlspecialchars(
								$t('blocks.webradio.azuracast.play'),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							aria-pressed="false"
						>
							<i
								class="fa-solid fa-play"
								aria-hidden="true"
							></i>
						</button>

					</div>

					<div class="mv-azuracast-deck-content">

						<div class="mv-azuracast-deck-top">

							<span class="mv-azuracast-on-air">
								<i
									class="fa-solid fa-circle"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('blocks.webradio.azuracast.on_air'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<?php if ($stationName !== ''): ?>

								<span class="mv-azuracast-deck-station">
									<?= htmlspecialchars(
										$stationName,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

						</div>

						<div class="mv-azuracast-deck-track">

							<span class="mv-azuracast-deck-label">
								<?= htmlspecialchars(
									$t('blocks.webradio.azuracast.now_playing'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<strong class="mv-azuracast-deck-title">
								<?= htmlspecialchars(
									$currentDisplayTitle,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>

							<?php if ($currentArtist !== ''): ?>

								<span class="mv-azuracast-deck-artist">
									<?= htmlspecialchars(
										$currentArtist,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

						</div>

						<div class="mv-azuracast-deck-footer">

							<span class="mv-azuracast-status">
								<?= htmlspecialchars(
									$t('blocks.webradio.azuracast.ready'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<div class="mv-azuracast-controls">

								<div class="mv-azuracast-volume">

									<button
										type="button"
										class="mv-azuracast-mute"
										aria-label="<?= htmlspecialchars(
											$t('blocks.webradio.azuracast.mute'),
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										aria-pressed="false"
									>
										<i
											class="fa-solid fa-volume-high"
											aria-hidden="true"
										></i>
									</button>

									<input
										type="range"
										class="mv-azuracast-volume-range"
										min="0"
										max="1"
										step="0.05"
										value="1"
										aria-label="<?= htmlspecialchars(
											$t('blocks.webradio.azuracast.volume'),
											ENT_QUOTES,
											'UTF-8'
										) ?>"
									>

								</div>

								<button
									type="button"
									class="mv-azuracast-detach"
									aria-label="<?= htmlspecialchars(
										$t('blocks.webradio.azuracast.detach'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									title="<?= htmlspecialchars(
										$t('blocks.webradio.azuracast.detach'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
								>
									<i
										class="fa-solid fa-up-right-from-square"
										aria-hidden="true"
									></i>
								</button>

							</div>

						</div>

					</div>

				</div>

			</div>

		<?php else: ?>

			<div class="mv-azuracast-unavailable">

				<div class="mv-azuracast-unavailable-cover">

					<?php if ($currentArt !== ''): ?>

						<img
							src="<?= htmlspecialchars(
								$currentArt,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							alt=""
							loading="lazy"
						>

					<?php else: ?>

						<i
							class="fa-solid fa-radio"
							aria-hidden="true"
						></i>

					<?php endif; ?>

				</div>

				<div>

					<strong>
						<?= htmlspecialchars(
							$currentDisplayTitle,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

					<p>
						<?= htmlspecialchars(
							$t(
								'blocks.webradio.azuracast.stream_unavailable'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

			</div>

		<?php endif; ?>

		<?php if (
			$showHistory
			&& $songHistory !== []
		): ?>

			<section class="mv-azuracast-history">

				<h4>
					<?= htmlspecialchars(
						$t(
							'blocks.webradio.azuracast.history_title'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</h4>

				<ul>

					<?php foreach ($songHistory as $historyItem): ?>

						<?php
						$historySong = is_array(
							$historyItem['song'] ?? null
						)
							? $historyItem['song']
							: [];

						$historyTitle = trim(
							(string) ($historySong['title'] ?? '')
						);

						$historyArtist = trim(
							(string) ($historySong['artist'] ?? '')
						);

						$historyText = trim(
							(string) ($historySong['text'] ?? '')
						);

						$historyArt = trim(
							(string) ($historySong['art'] ?? '')
						);

						$historyDisplayTitle = $historyTitle !== ''
							? $historyTitle
							: (
								$historyText !== ''
									? $historyText
									: $t(
										'blocks.webradio.azuracast.track_unavailable'
									)
							);
						?>

						<li class="mv-azuracast-history-item">

							<span class="mv-azuracast-history-cover">

								<?php if ($historyArt !== ''): ?>

									<img
										src="<?= htmlspecialchars(
											$historyArt,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										alt=""
										loading="lazy"
									>

								<?php else: ?>

									<i
										class="fa-solid fa-music"
										aria-hidden="true"
									></i>

								<?php endif; ?>

							</span>

							<span class="mv-azuracast-history-body">

								<strong>
									<?= htmlspecialchars(
										$historyDisplayTitle,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</strong>

								<?php if ($historyArtist !== ''): ?>

									<span>
										<?= htmlspecialchars(
											$historyArtist,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

							</span>

						</li>

					<?php endforeach; ?>

				</ul>

			</section>

		<?php endif; ?>

	<?php else: ?>

		<p class="mv-azuracast-empty">
			<?= htmlspecialchars(
				$t(
					'blocks.webradio.azuracast.radio_unavailable'
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	<?php endif; ?>

</div>
