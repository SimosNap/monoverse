<?php
declare(strict_types=1);

/** @var array $event */

$event = is_array($event ?? null)
	? $event
	: [];

$eventCover = trim(
	(string) ($event['cover'] ?? '')
);

$startsTimestamp = !empty($event['starts_at'])
	? strtotime((string) $event['starts_at'])
	: false;

$endsTimestamp = !empty($event['ends_at'])
	? strtotime((string) $event['ends_at'])
	: false;

$location = trim(
	(string) ($event['location'] ?? '')
);

$latitude = trim(
	(string) ($event['latitude'] ?? '')
);

$longitude = trim(
	(string) ($event['longitude'] ?? '')
);

$hasMap = $latitude !== ''
	&& $longitude !== ''
	&& is_numeric($latitude)
	&& is_numeric($longitude);

$externalUrl = trim(
	(string) ($event['external_url'] ?? '')
);

$widgetAreas = is_array($widgetAreas ?? null)
	? $widgetAreas
	: [];

$widgetsBeforeContent = trim(
	(string) ($widgetAreas['beforeContent'] ?? '')
);

$widgetsSidebar = trim(
	(string) ($widgetAreas['sidebar'] ?? '')
);

$widgetsAfterContent = trim(
	(string) ($widgetAreas['afterContent'] ?? '')
);
?>

<div class="container">

	<?php if ($widgetsBeforeContent !== ''): ?>

		<section
			class="mv-block-area chanzine-article-widget-area events-article-widget-area events-article-widget-area-before"
			aria-label="<?= htmlspecialchars(
				$t('events.event_page.areas.before'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			<?= $widgetsBeforeContent ?>
		</section>

	<?php endif; ?>

	<div class="chanzine-article-layout events-article-layout">

		<main class="chanzine-article-main events-article-main">

			<article class="card chanzine-article events-article">

				<?php if ($eventCover !== ''): ?>

					<img
						class="chanzine-article-cover events-article-cover"
						src="<?= htmlspecialchars(
							$eventCover,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						alt="<?= htmlspecialchars(
							(string) ($event['title'] ?? ''),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

				<?php endif; ?>

				<div class="chanzine-article-body events-article-body">

					<header class="chanzine-article-header events-article-header">

						<h1>
							<?= htmlspecialchars(
								(string) ($event['title'] ?? ''),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</h1>

						<?php if ($startsTimestamp !== false): ?>

							<div class="chanzine-article-meta events-article-meta">

								<i
									class="fa-regular fa-calendar"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$t('events.event_page.starts_at'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<time
									datetime="<?= htmlspecialchars(
										date(
											'Y-m-d\TH:i:s',
											$startsTimestamp
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
								>
									<?= htmlspecialchars(
										date(
											'd/m/Y H:i',
											$startsTimestamp
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</time>

							</div>

						<?php endif; ?>

						<?php if ($endsTimestamp !== false): ?>

							<div class="chanzine-article-meta events-article-meta">

								<i
									class="fa-regular fa-clock"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$t('events.event_page.ends_at'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

								<time
									datetime="<?= htmlspecialchars(
										date(
											'Y-m-d\TH:i:s',
											$endsTimestamp
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
								>
									<?= htmlspecialchars(
										date(
											'd/m/Y H:i',
											$endsTimestamp
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</time>

							</div>

						<?php endif; ?>

						<?php if ($location !== ''): ?>

							<div class="chanzine-article-meta events-article-meta">

								<i
									class="fa-solid fa-location-dot"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$location,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							</div>

						<?php endif; ?>

					</header>

					<div class="chanzine-content events-content">
						<?= $event['description_html'] ?? '' ?>
					</div>

				</div>

			</article>

		</main>

		<aside
			class="chanzine-article-sidebar events-article-sidebar"
			aria-label="<?= htmlspecialchars(
				$t('events.event_page.areas.sidebar'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>

			<div class="card chanzine-sidebar-card events-sidebar-card">

				<?php if ($externalUrl !== ''): ?>

					<a
						class="events-sidebar-action"
						href="<?= htmlspecialchars(
							$externalUrl,
							ENT_QUOTES,
							'UTF-8'
						) ?>"
						target="_blank"
						rel="noopener noreferrer"
					>

						<span>
							<?= htmlspecialchars(
								$t('events.event_page.external_link'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

						<i
							class="fa-solid fa-arrow-up-right-from-square"
							aria-hidden="true"
						></i>

					</a>

				<?php endif; ?>

				<?php if ($hasMap): ?>

					<button
						type="button"
						class="events-sidebar-action events-map-button"
						data-event-map-open
					>

						<span>
							<?= htmlspecialchars(
								$t('events.event_page.show_map'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

						<i
							class="fa-solid fa-map-location-dot"
							aria-hidden="true"
						></i>

					</button>

				<?php endif; ?>

				<a
					class="events-sidebar-action events-sidebar-back"
					href="/events"
				>

					<span>
						<?= htmlspecialchars(
							$t('events.event_page.all_events'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

					<i
						class="fa-solid fa-arrow-left"
						aria-hidden="true"
					></i>

				</a>

			</div>

			<?php if ($widgetsSidebar !== ''): ?>

				<div
					class="mv-block-area chanzine-article-widget-area events-article-widget-area events-article-widget-area-sidebar"
					aria-label="<?= htmlspecialchars(
						$t(
							'events.event_page.areas.sidebar_widgets'
						),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<?= $widgetsSidebar ?>
				</div>

			<?php endif; ?>

		</aside>

	</div>

	<?php if ($widgetsAfterContent !== ''): ?>

		<section
			class="mv-block-area chanzine-article-widget-area events-article-widget-area events-article-widget-area-after"
			aria-label="<?= htmlspecialchars(
				$t('events.event_page.areas.after'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			<?= $widgetsAfterContent ?>
		</section>

	<?php endif; ?>

</div>

<?php if ($hasMap): ?>

	<div
		class="events-map-modal"
		data-event-map-modal
		aria-hidden="true"
	>

		<div
			class="events-map-modal-backdrop"
			data-event-map-close
		></div>

		<div
			class="events-map-modal-dialog"
			role="dialog"
			aria-modal="true"
			aria-labelledby="events-map-modal-title"
		>

			<header class="events-map-modal-header">

				<div>

					<h2 id="events-map-modal-title">
						<?= htmlspecialchars(
							$t('events.event_page.map_title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<?php if ($location !== ''): ?>

						<p>
							<?= htmlspecialchars(
								$location,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					<?php endif; ?>

				</div>

				<button
					type="button"
					class="events-map-modal-close"
					data-event-map-close
					aria-label="<?= htmlspecialchars(
						$t('events.event_page.close_map'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<i
						class="fa-solid fa-xmark"
						aria-hidden="true"
					></i>
				</button>

			</header>

			<div
				class="events-map"
				data-event-map
				data-latitude="<?= htmlspecialchars(
					$latitude,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				data-longitude="<?= htmlspecialchars(
					$longitude,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			></div>

		</div>

	</div>

<?php endif; ?>
