<?php
declare(strict_types=1);

/** @var array $posts */

$posts = is_array($posts ?? null)
	? $posts
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

$query = trim(
	(string) ($query ?? '')
);

$hasSidebar = true;
?>

<div class="container">

	<div class="ping-page-layout <?= $hasSidebar
		? 'has-widget-sidebar'
		: 'is-full-width' ?>">

		<main class="ping-feed">

			<?php if ($widgetsBeforeContent !== ''): ?>

				<section
					class="mv-block-area ping-widget-area ping-widget-area-before"
					aria-label="<?= htmlspecialchars(
						$t('ping.areas.before'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<?= $widgetsBeforeContent ?>
				</section>

			<?php endif; ?>

			<?php if (!empty($isLogged)): ?>

				<?php if ($message = $session->getFlash('success')): ?>

					<div class="alert alert-success">
						<?= htmlspecialchars(
							(string) $message,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</div>

				<?php endif; ?>

				<?php if ($message = $session->getFlash('warning')): ?>

					<div class="alert alert-warning">
						<?= htmlspecialchars(
							(string) $message,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</div>

				<?php endif; ?>

				<?php if ($message = $session->getFlash('error')): ?>

					<div class="alert alert-danger">
						<?= htmlspecialchars(
							(string) $message,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</div>

				<?php endif; ?>

				<?php require __DIR__ . '/../components/ping-composer.php'; ?>

			<?php endif; ?>

			<nav
				class="ping-feed-tabs"
				aria-label="<?= htmlspecialchars(
					$t('ping.feed.label'),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>
				<a
					href="/ping"
					class="ping-feed-tab <?= ($feed ?? 'all') === 'all'
						? 'is-active'
						: '' ?>"
					<?= ($feed ?? 'all') === 'all'
						? 'aria-current="page"'
						: '' ?>
				>
					<i
						class="fa-solid fa-globe"
						aria-hidden="true"
					></i>

					<span>
						<?= htmlspecialchars(
							$t('ping.feed.all'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>
				</a>

				<?php if (!empty($isLogged)): ?>

					<a
						href="/ping?feed=following"
						class="ping-feed-tab <?= ($feed ?? 'all') === 'following'
							? 'is-active'
							: '' ?>"
						<?= ($feed ?? 'all') === 'following'
							? 'aria-current="page"'
							: '' ?>
					>
						<i
							class="fa-solid fa-user-group"
							aria-hidden="true"
						></i>

						<span>
							<?= htmlspecialchars(
								$t('ping.feed.following'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>
					</a>

					<a
						href="/ping?feed=interactions"
						class="ping-feed-tab <?= ($feed ?? 'all') === 'interactions'
							? 'is-active'
							: '' ?>"
						<?= ($feed ?? 'all') === 'interactions'
							? 'aria-current="page"'
							: '' ?>
					>
						<i
							class="fa-solid fa-comments"
							aria-hidden="true"
						></i>

						<span>
							<?= htmlspecialchars(
								$t('ping.feed.interactions'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>
					</a>

				<?php endif; ?>

				<a
					href="/ping?feed=audio"
					class="ping-feed-tab <?= ($feed ?? 'all') === 'audio'
						? 'is-active'
						: '' ?>"
					<?= ($feed ?? 'all') === 'audio'
						? 'aria-current="page"'
						: '' ?>
				>
					<span>
						<?= htmlspecialchars(
							$t('ping.feed.audio'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>
				</a>

				<a
					href="/ping?feed=video"
					class="ping-feed-tab <?= ($feed ?? 'all') === 'video'
						? 'is-active'
						: '' ?>"
					<?= ($feed ?? 'all') === 'video'
						? 'aria-current="page"'
						: '' ?>
				>
					<span>
						<?= htmlspecialchars(
							$t('ping.feed.video'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>
				</a>

			</nav>

			<?php if ($posts === []): ?>

				<div class="card">
					<p>
						<?= htmlspecialchars(
							$t('ping.feed.empty'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>
				</div>

			<?php else: ?>

				<div
					id="ping-infinite-list"
					data-next-offset="<?= count($posts) ?>"
					data-page-size="<?= (int) $pageSize ?>"
					data-feed="<?= htmlspecialchars(
						(string) ($feed ?? 'all'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

					<?php foreach ($posts as $post): ?>

						<?php include __DIR__ . '/../components/ping-card.php'; ?>

					<?php endforeach; ?>

				</div>

				<?php if (count($posts) >= $pageSize): ?>

					<div
						id="ping-infinite-trigger"
						aria-hidden="true"
					></div>

				<?php endif; ?>

			<?php endif; ?>

			<?php if ($widgetsAfterContent !== ''): ?>

				<section
					class="mv-block-area ping-widget-area ping-widget-area-after"
					aria-label="<?= htmlspecialchars(
						$t('ping.areas.after'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<?= $widgetsAfterContent ?>
				</section>

			<?php endif; ?>

			</main>

			<?php if ($hasSidebar): ?>

				<aside
					class="ping-sidebar ping-sidebar-right"
					aria-label="<?= htmlspecialchars(
						$t('ping.areas.sidebar'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

					<details class="ping-sidebar-mobile">

						<summary>
							<span>
								<i
									class="fa-solid fa-sliders"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('ping.sidebar.explore'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

							<i
								class="fa-solid fa-chevron-down ping-sidebar-mobile-chevron"
								aria-hidden="true"
							></i>
						</summary>

						<div class="ping-sidebar-mobile-content">

							<form
								class="ping-search"
								method="get"
								action="/ping"
								role="search"
								autocomplete="off"
							>

								<div class="ping-search-field">

									<i
										class="fa-solid fa-magnifying-glass"
										aria-hidden="true"
									></i>

									<input
										type="search"
										name="q"
										value="<?= htmlspecialchars(
											$query,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										placeholder="<?= htmlspecialchars(
											$t('ping.search.placeholder'),
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										aria-label="<?= htmlspecialchars(
											$t('ping.search.label'),
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										autocomplete="off"
										autocapitalize="none"
										spellcheck="false"
										enterkeyhint="search"
										data-1p-ignore
										data-lpignore="true"
									>

								</div>

								<button type="submit">

									<i
										class="fa-solid fa-magnifying-glass"
										aria-hidden="true"
									></i>

									<span>
										<?= htmlspecialchars(
											$t('ping.search.submit'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								</button>

							</form>

							<div class="mv-block-area ping-widget-area ping-widget-area-sidebar">
								<?= $widgetsSidebar ?>
							</div>

						</div>

					</details>

					<div class="ping-sidebar-desktop">

						<form
							class="ping-search"
							method="get"
							action="/ping"
							role="search"
							autocomplete="off"
						>

							<div class="ping-search-field">

								<i
									class="fa-solid fa-magnifying-glass"
									aria-hidden="true"
								></i>

								<input
									type="search"
									name="q"
									value="<?= htmlspecialchars(
										$query,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									placeholder="<?= htmlspecialchars(
										$t('ping.search.placeholder'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									aria-label="<?= htmlspecialchars(
										$t('ping.search.label'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									autocomplete="off"
									autocapitalize="none"
									spellcheck="false"
									enterkeyhint="search"
									data-1p-ignore
									data-lpignore="true"
								>

							</div>

							<button type="submit">

								<i
									class="fa-solid fa-magnifying-glass"
									aria-hidden="true"
								></i>

								<span>
									<?= htmlspecialchars(
										$t('ping.search.submit'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							</button>

						</form>

						<div class="mv-block-area ping-widget-area ping-widget-area-sidebar">
							<?= $widgetsSidebar ?>
						</div>

					</div>

				</aside>

			<?php endif; ?>

	</div>

	<div
		id="ping-report-modal"
		class="mv-modal"
		hidden
	>

		<div class="mv-modal-backdrop"></div>

		<div
			class="mv-modal-dialog"
			role="dialog"
			aria-modal="true"
			aria-labelledby="report-modal-title"
		>

			<form
				id="ping-report-form"
				method="post"
				action="/report"
			>

				<input
					type="hidden"
					id="report-target-type"
					name="target_type"
				>

				<input
					type="hidden"
					id="report-target-uuid"
					name="target_uuid"
				>

				<div class="mv-modal-header">

					<h3 id="report-modal-title">

						<i
							class="fa-solid fa-flag"
							aria-hidden="true"
						></i>

						<?= htmlspecialchars(
							$t('ping.report.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>

					</h3>

					<button
						type="button"
						class="mv-modal-close"
						aria-label="<?= htmlspecialchars(
							$t('ping.report.close'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>
						&times;
					</button>

				</div>

				<div class="mv-modal-body">

					<p class="report-help">
						<?= htmlspecialchars(
							$t('ping.report.help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

					<div class="report-reasons">

						<label class="report-option">

							<input
								type="radio"
								name="reason"
								value="spam"
								required
							>

							<span>

								<i
									class="fa-solid fa-ban"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('ping.report.reasons.spam'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

							</span>

						</label>

						<label class="report-option">

							<input
								type="radio"
								name="reason"
								value="harassment"
							>

							<span>

								<i
									class="fa-solid fa-comment-slash"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('ping.report.reasons.harassment'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

							</span>

						</label>

						<label class="report-option">

							<input
								type="radio"
								name="reason"
								value="privacy"
							>

							<span>

								<i
									class="fa-solid fa-user-secret"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('ping.report.reasons.privacy'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

							</span>

						</label>

						<label class="report-option">

							<input
								type="radio"
								name="reason"
								value="illegal"
							>

							<span>

								<i
									class="fa-solid fa-scale-balanced"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('ping.report.reasons.illegal'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

							</span>

						</label>

						<label class="report-option">

							<input
								type="radio"
								name="reason"
								value="copyright"
							>

							<span>

								<i
									class="fa-solid fa-copyright"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('ping.report.reasons.copyright'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

							</span>

						</label>

						<label class="report-option">

							<input
								type="radio"
								name="reason"
								value="other"
							>

							<span>

								<i
									class="fa-solid fa-ellipsis"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('ping.report.reasons.other'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

							</span>

						</label>

					</div>

					<div
						id="report-description-group"
						class="mv-form-group"
						hidden
					>

						<label for="report-description">
							<?= htmlspecialchars(
								$t('ping.report.description'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</label>

						<textarea
							id="report-description"
							name="description"
							rows="4"
							placeholder="<?= htmlspecialchars(
								$t('ping.report.description_placeholder'),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						></textarea>

					</div>

				</div>

				<div class="mv-modal-footer">

					<button
						type="button"
						class="mv-button mv-modal-cancel"
					>
						<?= htmlspecialchars(
							$t('ping.report.cancel'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

					<button
						type="submit"
						class="mv-button mv-button-danger"
					>
						<?= htmlspecialchars(
							$t('ping.report.submit'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</button>

				</div>

			</form>

		</div>

	</div>

</div>