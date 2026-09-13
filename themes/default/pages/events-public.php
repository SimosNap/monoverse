<?php
/** @var array $events */
?>

<?php

$events = is_array($events ?? null)
	? $events
	: [];

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

$hasSidebar = true;

?>

<div class="container">

	<div class="chanzine-page-layout events-page-layout <?= $hasSidebar
		? 'has-widget-sidebar'
		: 'is-full-width' ?>">

		<?php if ($widgetsBeforeContent !== ''): ?>

			<section
				class="mv-block-area chanzine-widget-area events-widget-area events-widget-area-before"
				aria-label="<?= htmlspecialchars(
					$t('events.areas.before'),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>
				<?= $widgetsBeforeContent ?>
			</section>

		<?php endif; ?>

		<main class="chanzine-main events-main">

			<header class="card chanzine-header events-header">

				<h1>
					<?= htmlspecialchars(
						$t('events.header.title'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</h1>

				<p>
					<?= htmlspecialchars(
						$t('events.header.subtitle'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

				<?php if (
					!empty($user)
					&& (($settings['events_user_submissions_enabled'] ?? '0') === '1')
				): ?>

					<div class="chanzine-header-actions events-header-actions">

						<a
							class="button button-primary"
							href="/events/submit"
						>
							<?= htmlspecialchars(
								$t('events.header.submit_event'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</a>

					</div>

				<?php endif; ?>

			</header>

			<?php if (!empty($success)): ?>

				<div class="alert alert-success">
					<?= htmlspecialchars(
						(string) $success,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</div>

			<?php endif; ?>

			<?php if (!empty($error)): ?>

				<div class="alert alert-error">
					<?= htmlspecialchars(
						(string) $error,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</div>

			<?php endif; ?>

			<?php if (empty($events)): ?>

				<div class="card chanzine-empty events-empty">

					<p>
						<?= htmlspecialchars(
							$t('events.empty'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

			<?php else: ?>

				<div class="chanzine-list events-list">

					<?php foreach ($events as $event): ?>

						<?php

						$eventTitle = trim(
							(string) ($event['title'] ?? '')
						);

						$eventSlug = trim(
							(string) ($event['slug'] ?? '')
						);

						$eventUrl = '/events/'
							. rawurlencode($eventSlug);

						$cover = trim(
							(string) ($event['cover'] ?? '')
						);

						$location = trim(
							(string) ($event['location'] ?? '')
						);

						$description = trim(
							(string) ($event['description'] ?? '')
						);

						$descriptionPreview = preg_replace(
							'/\s+/',
							' ',
							strip_tags($description)
						) ?? '';

						if (mb_strlen($descriptionPreview) > 220) {
							$descriptionPreview = rtrim(
								mb_substr(
									$descriptionPreview,
									0,
									220
								)
							) . '…';
						}

						$startsAt = !empty($event['starts_at'])
							? strtotime((string) $event['starts_at'])
							: false;

						$endsAt = !empty($event['ends_at'])
							? strtotime((string) $event['ends_at'])
							: false;

						$readAria = str_replace(
							':title',
							$eventTitle,
							$t('events.event.open_aria')
						);

						?>

						<article class="card chanzine-card events-card">

							<?php if ($cover !== ''): ?>

								<a
									class="chanzine-card-cover-link events-card-cover-link"
									href="<?= htmlspecialchars(
										$eventUrl,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									aria-label="<?= htmlspecialchars(
										$readAria,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
								>

									<img
										class="chanzine-card-cover events-card-cover"
										src="<?= htmlspecialchars(
											$cover,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										alt="<?= htmlspecialchars(
											$eventTitle,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										loading="lazy"
									>

								</a>

							<?php endif; ?>

							<div class="chanzine-card-content events-card-content">

								<?php if ($startsAt !== false): ?>

									<div class="chanzine-card-meta events-card-meta">

										<time
											datetime="<?= htmlspecialchars(
												date(
													'Y-m-d\TH:i',
													$startsAt
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>"
										>
											<i
												class="fa-regular fa-calendar"
												aria-hidden="true"
											></i>

											<?= htmlspecialchars(
												date(
													'd/m/Y H:i',
													$startsAt
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</time>

										<?php if ($endsAt !== false): ?>

											<span aria-hidden="true">
												–
											</span>

											<time
												datetime="<?= htmlspecialchars(
													date(
														'Y-m-d\TH:i',
														$endsAt
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>"
											>
												<?= htmlspecialchars(
													date(
														'd/m/Y H:i',
														$endsAt
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</time>

										<?php endif; ?>

									</div>

								<?php endif; ?>

								<h2 class="chanzine-card-title events-card-title">

									<a
										href="<?= htmlspecialchars(
											$eventUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
									>
										<?= htmlspecialchars(
											$eventTitle,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>

								</h2>

								<?php if ($location !== ''): ?>

									<div class="events-card-location">

										<i
											class="fa-solid fa-location-dot"
											aria-hidden="true"
										></i>

										<?= htmlspecialchars(
											$location,
											ENT_QUOTES,
											'UTF-8'
										) ?>

									</div>

								<?php endif; ?>

								<?php if ($descriptionPreview !== ''): ?>

									<p class="chanzine-card-excerpt events-card-excerpt">
										<?= htmlspecialchars(
											$descriptionPreview,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</p>

								<?php endif; ?>

								<div class="chanzine-card-actions events-card-actions">

									<a
										class="button button-primary"
										href="<?= htmlspecialchars(
											$eventUrl,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
									>
										<?= htmlspecialchars(
											$t('events.event.open'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>

								</div>

							</div>

						</article>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</main>

		<?php if ($hasSidebar): ?>

			<aside
				class="chanzine-sidebar events-sidebar"
				aria-label="<?= htmlspecialchars(
					$t('events.areas.sidebar'),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

				<div class="mv-block-area chanzine-widget-area events-widget-area events-widget-area-sidebar">

					<?= $widgetsSidebar ?>

				</div>

			</aside>

		<?php endif; ?>

	</div>

	<?php if ($widgetsAfterContent !== ''): ?>

		<section
			class="mv-block-area chanzine-widget-area events-widget-area events-widget-area-after"
			aria-label="<?= htmlspecialchars(
				$t('events.areas.after'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			<?= $widgetsAfterContent ?>
		</section>

	<?php endif; ?>

</div>
