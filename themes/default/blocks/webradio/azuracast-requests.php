<?php
declare(strict_types=1);

$title = trim(
	(string) (
		$title
		?? $t(
			'blocks.webradio.azuracast_requests.default_title'
		)
	)
);

$requestsAvailable = (bool) (
	$requests_available
	?? false
);

$requestItems = is_array(
	$request_items
	?? null
)
	? $request_items
	: [];

$requestsMessage = trim(
	(string) ($requests_message ?? '')
);

$unavailableBehavior = (string) (
	$unavailable_behavior
	?? 'message'
);

$unavailableMessage = trim(
	(string) (
		$unavailable_message
		?? $t(
			'blocks.webradio.azuracast_requests.unavailable_default'
		)
	)
);

if (
	!$requestsAvailable
	&& $unavailableBehavior === 'hide'
) {
	return;
}

$blockWidth = (int) ($block['width'] ?? 12);

if (!in_array(
	$blockWidth,
	[3, 4, 6, 8, 9, 12],
	true
)) {
	$blockWidth = 12;
}

$widthClass = 'mv-block-width-' . $blockWidth;

$requestCount = count($requestItems);

$counterLabel = $t(
	$requestCount === 1
		? 'blocks.webradio.azuracast_requests.counter.one'
		: 'blocks.webradio.azuracast_requests.counter.many',
	[
		'count' => $requestCount,
	]
);
?>

<div
	class="mv-widget mv-azuracast-requests-widget <?= htmlspecialchars(
		$widthClass,
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-requests-url="<?= htmlspecialchars(
		(string) ($requests_url ?? ''),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-no-results="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.no_results'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-sent="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.sent'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-failed="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.failed'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-sent-label="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.sent_label'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-connection-error="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.connection_error'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-pagination-label="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.pagination_label'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-previous-page="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.previous_page'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-next-page="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.next_page'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-results-count="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.results_count'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-identify-failed="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.identify_failed'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-sending="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.sending'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
	data-i18n-sending-status="<?= htmlspecialchars(
		$t(
			'blocks.webradio.azuracast_requests.js.sending_status'
		),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
>

	<header class="mv-azuracast-requests-header">

		<div>

			<span class="mv-azuracast-requests-kicker">
				<i
					class="fa-solid fa-music"
					aria-hidden="true"
				></i>

				<?= htmlspecialchars(
					$t(
						'blocks.webradio.azuracast_requests.kicker'
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

		<?php if ($requestsAvailable): ?>

			<span class="mv-azuracast-requests-status">
				<?= htmlspecialchars(
					$t(
						'blocks.webradio.azuracast_requests.active'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</span>

		<?php endif; ?>

	</header>

	<?php if (!$requestsAvailable): ?>

		<div class="mv-azuracast-requests-unavailable">

			<i
				class="fa-solid fa-clock"
				aria-hidden="true"
			></i>

			<div>

				<strong>
					<?= htmlspecialchars(
						$t(
							'blocks.webradio.azuracast_requests.unavailable_title'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</strong>

				<p>
					<?= htmlspecialchars(
						$unavailableMessage !== ''
							? $unavailableMessage
							: $requestsMessage,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

		</div>

	<?php elseif ($requestItems === []): ?>

		<div class="mv-azuracast-requests-empty">

			<i
				class="fa-solid fa-compact-disc"
				aria-hidden="true"
			></i>

			<p>
				<?= htmlspecialchars(
					$requestsMessage !== ''
						? $requestsMessage
						: $t(
							'blocks.webradio.azuracast_requests.empty'
						),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	<?php else: ?>

		<div class="mv-azuracast-requests-search">

			<i
				class="fa-solid fa-magnifying-glass"
				aria-hidden="true"
			></i>

			<input
				type="search"
				class="mv-azuracast-requests-search-input"
				placeholder="<?= htmlspecialchars(
					$t(
						'blocks.webradio.azuracast_requests.search.placeholder'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				autocomplete="off"
				aria-label="<?= htmlspecialchars(
					$t(
						'blocks.webradio.azuracast_requests.search.aria_label'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

			<button
				type="button"
				class="mv-azuracast-requests-search-clear"
				aria-label="<?= htmlspecialchars(
					$t(
						'blocks.webradio.azuracast_requests.search.clear'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				hidden
			>
				<i
					class="fa-solid fa-xmark"
					aria-hidden="true"
				></i>
			</button>

		</div>

		<p
			class="mv-azuracast-requests-counter"
			aria-live="polite"
		>
			<?= htmlspecialchars(
				$counterLabel,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

		<div class="mv-azuracast-requests-list">

			<?php foreach ($requestItems as $item): ?>

				<?php
				$requestId = trim(
					(string) ($item['request_id'] ?? '')
				);

				$requestUrl = trim(
					(string) ($item['request_url'] ?? '')
				);

				$song = is_array($item['song'] ?? null)
					? $item['song']
					: [];

				$artist = trim(
					(string) ($song['artist'] ?? '')
				);

				$songTitle = trim(
					(string) ($song['title'] ?? '')
				);

				$songText = trim(
					(string) ($song['text'] ?? '')
				);

				$album = trim(
					(string) ($song['album'] ?? '')
				);

				$genre = trim(
					(string) ($song['genre'] ?? '')
				);

				$art = trim(
					(string) ($song['art'] ?? '')
				);

				$displayTitle = $songTitle !== ''
					? $songTitle
					: (
						$songText !== ''
							? $songText
							: $t(
								'blocks.webradio.azuracast_requests.untitled'
							)
					);

				$searchText = strtolower(
					implode(
						' ',
						[
							$artist,
							$displayTitle,
							$album,
							$genre,
						]
					)
				);
				?>

				<article
					class="mv-azuracast-request-card"
					data-search="<?= htmlspecialchars(
						$searchText,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

					<div class="mv-azuracast-request-cover">

						<?php if ($art !== ''): ?>

							<img
								src="<?= htmlspecialchars(
									$art,
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

					</div>

					<div class="mv-azuracast-request-content">

						<strong>
							<?= htmlspecialchars(
								$displayTitle,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<?php if ($artist !== ''): ?>

							<span class="mv-azuracast-request-artist">
								<?= htmlspecialchars(
									$artist,
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

						<?php if (
							$album !== ''
							|| $genre !== ''
						): ?>

							<span class="mv-azuracast-request-meta">

								<?php if ($album !== ''): ?>

									<span>
										<?= htmlspecialchars(
											$album,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

								<?php if (
									$album !== ''
									&& $genre !== ''
								): ?>
									<span aria-hidden="true">·</span>
								<?php endif; ?>

								<?php if ($genre !== ''): ?>

									<span>
										<?= htmlspecialchars(
											$genre,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								<?php endif; ?>

							</span>

						<?php endif; ?>

					</div>

					<button
						type="button"
						class="mv-azuracast-request-button"
						data-request-id="<?= htmlspecialchars(
							$requestId,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						data-request-url="<?= htmlspecialchars(
							$requestUrl,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						<?= (
							$requestId === ''
							|| $requestUrl === ''
						)
							? 'disabled'
							: ''
						?>
					>
						<i
							class="fa-solid fa-paper-plane"
							aria-hidden="true"
						></i>

						<span>
							<?= htmlspecialchars(
								$t(
									'blocks.webradio.azuracast_requests.request'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>
					</button>

				</article>

			<?php endforeach; ?>

		</div>

		<div
			class="mv-azuracast-requests-no-results"
			hidden
		>

			<i
				class="fa-solid fa-magnifying-glass"
				aria-hidden="true"
			></i>

			<p>
				<?= htmlspecialchars(
					$t(
						'blocks.webradio.azuracast_requests.search.no_results'
					),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

		<div
			class="mv-azuracast-requests-feedback"
			aria-live="polite"
			hidden
		></div>

	<?php endif; ?>

</div>
