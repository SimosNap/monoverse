<?php
declare(strict_types=1);

$title = trim(
	(string) (
		$title
		?? $t(
			'blocks.webradio.icecast_stats.default_title'
		)
	)
);

$statusData = is_array($status ?? null)
	? $status
	: [];

$sourcesData = is_array($sources ?? null)
	? $sources
	: [];

$sourceData = is_array($source ?? null)
	? $source
	: [];

$showListeners = (bool) (
	$show_listeners
	?? true
);

$showPeak = (bool) (
	$show_peak
	?? true
);

$showBitrate = (bool) (
	$show_bitrate
	?? true
);

$showCodec = (bool) (
	$show_codec
	?? true
);

$showMounts = (bool) (
	$show_mounts
	?? true
);

$currentListeners = max(
	0,
	(int) (
		$sourceData['listeners']
		?? 0
	)
);

$listenerPeak = max(
	0,
	(int) (
		$sourceData['listener_peak']
		?? 0
	)
);

$primaryBitrate = max(
	0,
	(int) (
		$sourceData['bitrate']
		?? 0
	)
);

$primaryCodec = trim(
	(string) (
		$sourceData['server_type']
		?? ''
	)
);

$stationName = trim(
	(string) (
		$sourceData['server_name']
		?? ''
	)
);

$activeMounts = [];

foreach ($sourcesData as $sourceItem) {
	if (!is_array($sourceItem)) {
		continue;
	}

	$listenUrl = trim(
		(string) (
			$sourceItem['listenurl']
			?? ''
		)
	);

	$mountName = '';

	if ($listenUrl !== '') {
		$path = parse_url(
			$listenUrl,
			PHP_URL_PATH
		);

		if (is_string($path)) {
			$mountName = trim($path);
		}
	}

	$mountTitle = trim(
		(string) (
			$sourceItem['server_name']
			?? ''
		)
	);

	if ($mountTitle === '') {
		$mountTitle = $mountName;
	}

	$bitrate = max(
		0,
		(int) (
			$sourceItem['bitrate']
			?? 0
		)
	);

	$format = trim(
		(string) (
			$sourceItem['server_type']
			?? ''
		)
	);

	$mountListeners = max(
		0,
		(int) (
			$sourceItem['listeners']
			?? 0
		)
	);

	if (
		$mountTitle === ''
		&& $listenUrl === ''
	) {
		continue;
	}

	$activeMounts[] = [
		'name' => $mountTitle !== ''
			? $mountTitle
			: $t(
				'blocks.webradio.icecast_stats.stream_fallback'
			),
		'url' => $listenUrl,
		'bitrate' => $bitrate,
		'format' => $format,
		'listeners' => $mountListeners,
	];
}

if ($sourceData === [] && count($activeMounts) === 1) {
	$sourceData = $sourcesData[0] ?? [];

	$currentListeners = max(
		0,
		(int) (
			$sourceData['listeners']
			?? 0
		)
	);

	$listenerPeak = max(
		0,
		(int) (
			$sourceData['listener_peak']
			?? 0
		)
	);

	$primaryBitrate = max(
		0,
		(int) (
			$sourceData['bitrate']
			?? 0
		)
	);

	$primaryCodec = trim(
		(string) (
			$sourceData['server_type']
			?? ''
		)
	);

	$stationName = trim(
		(string) (
			$sourceData['server_name']
			?? ''
		)
	);
}

$isOnline = $statusData !== []
	&& $activeMounts !== [];

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

<div class="mv-widget mv-azuracast-stats-widget <?= htmlspecialchars(
	$widthClass,
	ENT_QUOTES,
	'UTF-8'
) ?>">

	<header class="mv-azuracast-stats-header">

		<div>

			<span class="mv-azuracast-stats-kicker">
				<i
					class="fa-solid fa-chart-simple"
					aria-hidden="true"
				></i>

				<?= htmlspecialchars(
					$t(
						'blocks.webradio.icecast_stats.kicker'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

			<h3>
				<?= htmlspecialchars(
					$title,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h3>

		</div>

		<span class="mv-azuracast-stats-status <?= $isOnline
			? 'is-online'
			: 'is-offline'
		?>">

			<i
				class="fa-solid fa-circle"
				aria-hidden="true"
			></i>

			<?= htmlspecialchars(
				$t(
					$isOnline
						? 'blocks.webradio.icecast_stats.status.online'
						: 'blocks.webradio.icecast_stats.status.offline'
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>

		</span>

	</header>

	<?php if ($statusData === []): ?>

		<div class="mv-azuracast-stats-unavailable">

			<i
				class="fa-solid fa-triangle-exclamation"
				aria-hidden="true"
			></i>

			<div>

				<strong>
					<?= htmlspecialchars(
						$t(
							'blocks.webradio.icecast_stats.unavailable.title'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</strong>

				<p>
					<?= htmlspecialchars(
						$t(
							'blocks.webradio.icecast_stats.unavailable.text'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

		</div>

	<?php else: ?>

		<?php if ($stationName !== ''): ?>

			<p class="mv-azuracast-stats-station">
				<?= htmlspecialchars(
					$stationName,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		<?php endif; ?>

		<div class="mv-azuracast-stats-grid">

			<?php if ($showListeners): ?>

				<div class="mv-azuracast-stat">

					<span class="mv-azuracast-stat-icon">
						<i
							class="fa-solid fa-headphones"
							aria-hidden="true"
						></i>
					</span>

					<div>

						<strong>
							<?= $currentListeners ?>
						</strong>

						<span>
							<?= htmlspecialchars(
								$t(
									'blocks.webradio.icecast_stats.stats.current_listeners'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</div>

				</div>

			<?php endif; ?>

			<?php if ($showPeak): ?>

				<div class="mv-azuracast-stat">

					<span class="mv-azuracast-stat-icon">
						<i
							class="fa-solid fa-arrow-trend-up"
							aria-hidden="true"
						></i>
					</span>

					<div>

						<strong>
							<?= $listenerPeak ?>
						</strong>

						<span>
							<?= htmlspecialchars(
								$t(
									'blocks.webradio.icecast_stats.stats.listener_peak'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</div>

				</div>

			<?php endif; ?>

			<?php if ($showBitrate): ?>

				<div class="mv-azuracast-stat">

					<span class="mv-azuracast-stat-icon">
						<i
							class="fa-solid fa-gauge-high"
							aria-hidden="true"
						></i>
					</span>

					<div>

						<strong>
							<?= $primaryBitrate > 0
								? $primaryBitrate . ' kbps'
								: '—'
							?>
						</strong>

						<span>
							<?= htmlspecialchars(
								$t(
									'blocks.webradio.icecast_stats.stats.bitrate'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</div>

				</div>

			<?php endif; ?>

			<?php if ($showCodec): ?>

				<div class="mv-azuracast-stat">

					<span class="mv-azuracast-stat-icon">
						<i
							class="fa-solid fa-wave-square"
							aria-hidden="true"
						></i>
					</span>

					<div>

						<strong>
							<?= htmlspecialchars(
								$primaryCodec !== ''
									? strtoupper($primaryCodec)
									: '—',
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<span>
							<?= htmlspecialchars(
								$t(
									'blocks.webradio.icecast_stats.stats.codec'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</div>

				</div>

			<?php endif; ?>

		</div>

		<?php if ($showMounts): ?>

			<section class="mv-azuracast-mounts">

				<header>

					<h4>
						<?= htmlspecialchars(
							$t(
								'blocks.webradio.icecast_stats.mounts.title'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h4>

					<span>
						<?= count($activeMounts) ?>
					</span>

				</header>

				<?php if ($activeMounts === []): ?>

					<p class="mv-azuracast-mounts-empty">
						<?= htmlspecialchars(
							$t(
								'blocks.webradio.icecast_stats.mounts.empty'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				<?php else: ?>

					<ul>

						<?php foreach ($activeMounts as $mountItem): ?>

							<li>

								<span class="mv-azuracast-mount-icon">
									<i
										class="fa-solid fa-tower-broadcast"
										aria-hidden="true"
									></i>
								</span>

								<div class="mv-azuracast-mount-content">

									<strong>
										<?= htmlspecialchars(
											(string) $mountItem['name'],
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</strong>

									<span>

										<?php if (
											(string) $mountItem['format'] !== ''
										): ?>

											<?= htmlspecialchars(
												strtoupper(
													(string) $mountItem['format']
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>

										<?php endif; ?>

										<?php if (
											(int) $mountItem['bitrate'] > 0
										): ?>

											<?php if (
												(string) $mountItem['format'] !== ''
											): ?>
												·
											<?php endif; ?>

											<?= (int) $mountItem['bitrate'] ?>
											kbps

										<?php endif; ?>

										<?php if (
											(int) $mountItem['listeners'] > 0
										): ?>

											·

											<?= htmlspecialchars(
												$t(
													(int) $mountItem['listeners'] === 1
														? 'blocks.webradio.icecast_stats.mounts.listeners.one'
														: 'blocks.webradio.icecast_stats.mounts.listeners.many',
													[
														'count' => (int) $mountItem['listeners'],
													]
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>

										<?php endif; ?>

									</span>

								</div>

							</li>

						<?php endforeach; ?>

					</ul>

				<?php endif; ?>

			</section>

		<?php endif; ?>

	<?php endif; ?>

</div>
