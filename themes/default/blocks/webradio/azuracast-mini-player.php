<?php
declare(strict_types=1);

$title = trim(
	(string) (
		$title
		?? $t(
			'blocks.webradio.azuracast_mini_player.default_title'
		)
	)
);

$showCover = (bool) ($show_cover ?? true);

$nowPlayingData = is_array($now_playing ?? null)
	? $now_playing
	: [];

$station = is_array($nowPlayingData['station'] ?? null)
	? $nowPlayingData['station']
	: [];

$nowPlaying = is_array($nowPlayingData['now_playing'] ?? null)
	? $nowPlayingData['now_playing']
	: [];

$currentSong = is_array($nowPlaying['song'] ?? null)
	? $nowPlaying['song']
	: [];

$stationName = trim(
	(string) ($station['name'] ?? '')
);

$streamUrl = trim(
	(string) ($stream_url ?? '')
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
				'blocks.webradio.azuracast_mini_player.track_unavailable'
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
	class="mv-widget mv-azuracast-mini-player <?= htmlspecialchars(
		$widthClass,
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-play="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast_mini_player.js.play'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-pause="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast_mini_player.js.pause'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-playing="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast_mini_player.js.playing'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-paused="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast_mini_player.js.paused'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-unavailable="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast_mini_player.js.unavailable'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-error="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast_mini_player.js.error'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-ready="<?= htmlspecialchars(
		$t('blocks.webradio.azuracast_mini_player.js.ready'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
>

	<audio
		class="mv-azuracast-mini-audio"
		preload="none"
	>
		<?php if ($streamUrl !== ''): ?>

			<source
				src="<?= htmlspecialchars(
					$streamUrl,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

		<?php endif; ?>
	</audio>

	<?php if (
		$showCover
		&& $currentArt !== ''
	): ?>

		<img
			class="mv-azuracast-mini-cover"
			src="<?= htmlspecialchars(
				$currentArt,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			alt=""
			loading="lazy"
		>

	<?php else: ?>

		<span class="mv-azuracast-mini-cover mv-azuracast-mini-cover-placeholder">
			<i
				class="fa-solid fa-radio"
				aria-hidden="true"
			></i>
		</span>

	<?php endif; ?>

	<div class="mv-azuracast-mini-content">

		<div class="mv-azuracast-mini-heading">

			<span class="mv-azuracast-mini-label">
				<?= htmlspecialchars(
					$title,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

			<?php if ($stationName !== ''): ?>

				<span class="mv-azuracast-mini-station">
					<?= htmlspecialchars(
						$stationName,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			<?php endif; ?>

		</div>

		<strong class="mv-azuracast-mini-title">
			<?= htmlspecialchars(
				$currentDisplayTitle,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</strong>

		<?php if ($currentArtist !== ''): ?>

			<span class="mv-azuracast-mini-artist">
				<?= htmlspecialchars(
					$currentArtist,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

		<?php endif; ?>

	</div>

	<div class="mv-azuracast-mini-actions">

		<button
			type="button"
			class="mv-azuracast-mini-play"
			aria-label="<?= htmlspecialchars(
				$t(
					'blocks.webradio.azuracast_mini_player.play'
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			aria-pressed="false"
			<?= $streamUrl === '' ? 'disabled' : '' ?>
		>
			<i
				class="fa-solid fa-play"
				aria-hidden="true"
			></i>
		</button>

		<button
			type="button"
			class="mv-azuracast-mini-detach"
			aria-label="<?= htmlspecialchars(
				$t(
					'blocks.webradio.azuracast_mini_player.detach'
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			<?= $streamUrl === '' ? 'disabled' : '' ?>
		>
			<i
				class="fa-solid fa-up-right-from-square"
				aria-hidden="true"
			></i>
		</button>

	</div>

</div>
