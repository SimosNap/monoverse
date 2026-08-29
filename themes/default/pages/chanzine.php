<?php
/** @var array $articles */
?>

<?php

$articles = is_array($articles ?? null)
	? $articles
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

$currentCategory = is_array($currentCategory ?? null)
	? $currentCategory
	: null;

$query = trim(
		(string) ($query ?? '')
	);
$searchAction = '/chanzine';

	if ($currentCategory) {
		$categorySlug = trim(
			(string) ($currentCategory['slug'] ?? '')
		);

		if ($categorySlug !== '') {
			$searchAction = '/chanzine/category/'
				. rawurlencode($categorySlug);
		}
	}

?>

<div class="container">

	<div class="chanzine-page-layout <?= $hasSidebar
	? 'has-widget-sidebar'
	: 'is-full-width' ?>">

	<?php if ($widgetsBeforeContent !== ''): ?>

		<section
			class="mv-block-area chanzine-widget-area chanzine-widget-area-before"
			aria-label="<?= htmlspecialchars(
				$t('chanzine.areas.before'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			<?= $widgetsBeforeContent ?>
		</section>

	<?php endif; ?>

		<main class="chanzine-main">

			<header class="card chanzine-header">

				<?php if ($currentCategory): ?>

					<nav
						class="chanzine-breadcrumb"
						aria-label="<?= htmlspecialchars(
							$t('chanzine.breadcrumb'),
							ENT_QUOTES,
							'UTF-8'
						) ?>"
					>

						<a href="/chanzine">
							<?= htmlspecialchars(
								$t('chanzine.header.title'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</a>

						<i
							class="fa-solid fa-chevron-right"
							aria-hidden="true"
						></i>

						<span aria-current="page">
							<?= htmlspecialchars(
								(string) ($currentCategory['name'] ?? ''),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>
					</nav>

					<h1>
						<?= htmlspecialchars(
							(string) ($currentCategory['name'] ?? ''),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h1>

					<?php
					$categoryDescription = trim(
						(string) ($currentCategory['description'] ?? '')
					);
					?>

					<?php if ($categoryDescription !== ''): ?>

						<p>
							<?= nl2br(
								htmlspecialchars(
									$categoryDescription,
									ENT_QUOTES,
									'UTF-8'
								)
							) ?>
						</p>

					<?php else: ?>

						<p>
							<?= htmlspecialchars(
								$t('chanzine.category.articles_of'),
								ENT_QUOTES,
								'UTF-8'
							) ?>

							<strong>
								<?= htmlspecialchars(
									(string) ($currentCategory['name'] ?? ''),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</strong>.
						</p>

					<?php endif; ?>

				<?php else: ?>

					<h1>
						<?= htmlspecialchars(
							$t('chanzine.header.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h1>

					<p>
						<?= htmlspecialchars(
							$t('chanzine.header.subtitle'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

					<?php if (
						!empty($user)
						&& (($settings['chanzine_user_submissions_enabled'] ?? '0') === '1')
					): ?>

						<div class="chanzine-header-actions">
							<a
								class="button button-primary"
								href="/chanzine/submit"
							>
								<?= htmlspecialchars(
									$t('chanzine.header.submit_article'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>
						</div>

					<?php endif; ?>

				<?php endif; ?>

			</header>

			<?php if (empty($articles)): ?>

				<div class="card chanzine-empty">
					<p>
						<?= htmlspecialchars(
							$t('chanzine.empty'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>
				</div>

			<?php else: ?>

				<div class="chanzine-list">

					<?php foreach ($articles as $article): ?>

						<?php
						$cover = !empty($article['cover'])
							? (string) $article['cover']
							: '/themes/default/assets/images/chanzine-default.webp';

						$articleTitle = (string) ($article['title'] ?? '');

						$readAria = str_replace(
							':title',
							$articleTitle,
							$t('chanzine.article.read_aria')
						);
						?>

						<article class="card chanzine-card">

							<a
								class="chanzine-card-cover-link"
								href="/chanzine/<?= rawurlencode($article['slug']) ?>"
								aria-label="<?= htmlspecialchars(
									$readAria,
									ENT_QUOTES,
									'UTF-8'
								) ?>"
							>
								<img
									class="chanzine-card-cover"
									src="<?= htmlspecialchars(
										$cover,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									alt="<?= htmlspecialchars(
										$articleTitle,
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									loading="lazy"
								>
							</a>

							<div class="chanzine-card-content">

								<?php if (!empty($article['published_at'])): ?>

									<div class="chanzine-card-meta">
										<time
											datetime="<?= htmlspecialchars(
												date(
													'Y-m-d',
													strtotime($article['published_at'])
												)
											) ?>"
										>
											<?= htmlspecialchars(
												date(
													'd/m/Y',
													strtotime($article['published_at'])
												)
											) ?>
										</time>
									</div>

								<?php endif; ?>

								<?php if (
									!empty($article['category_name'])
									&& !empty($article['category_slug'])
								): ?>

									<a
										class="chanzine-card-category"
										href="/chanzine/category/<?= rawurlencode(
											(string) $article['category_slug']
										) ?>"
									>
										<?= htmlspecialchars(
											(string) $article['category_name'],
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>

								<?php endif; ?>

								<h2 class="chanzine-card-title">
									<a href="/chanzine/<?= rawurlencode($article['slug']) ?>">
										<?= htmlspecialchars(
											$articleTitle,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</a>
								</h2>

								<?php if (!empty($article['excerpt'])): ?>

									<p class="chanzine-card-excerpt">
										<?= nl2br(
											htmlspecialchars(
												(string) $article['excerpt'],
												ENT_QUOTES,
												'UTF-8'
											)
										) ?>
									</p>

								<?php endif; ?>

								<div class="chanzine-card-actions">
									<a
										class="button button-primary"
										href="/chanzine/<?= rawurlencode($article['slug']) ?>"
									>
										<?= htmlspecialchars(
											$t('chanzine.article.read'),
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
				class="chanzine-sidebar"
				aria-label="<?= htmlspecialchars(
					$t('chanzine.areas.sidebar'),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

				<form
					class="chanzine-search"
					method="get"
					action="<?= htmlspecialchars(
						$searchAction,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					role="search"
					autocomplete="off"
				>

					<div class="chanzine-search-field">

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
								$t('chanzine.search.placeholder'),
								ENT_QUOTES,
								'UTF-8'
							) ?>"
							aria-label="<?= htmlspecialchars(
								$t('chanzine.search.label'),
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
								$t('chanzine.search.submit'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</button>

				</form>

				<div class="mv-block-area chanzine-widget-area chanzine-widget-area-sidebar">

					<?= $widgetsSidebar ?>

				</div>

			</aside>

		<?php endif; ?>

	</div>

	<?php if ($widgetsAfterContent !== ''): ?>

		<section
			class="mv-block-area chanzine-widget-area chanzine-widget-area-after"
			aria-label="<?= htmlspecialchars(
				$t('chanzine.areas.after'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			<?= $widgetsAfterContent ?>
		</section>

	<?php endif; ?>

</div>
