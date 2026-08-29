<?php
declare(strict_types=1);

/** @var array $article */

$article = is_array($article ?? null)
	? $article
	: [];

$articleCover = !empty($article['cover'])
	? (string) $article['cover']
	: '/themes/default/assets/images/chanzine-default.webp';

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
			class="mv-block-area chanzine-article-widget-area chanzine-article-widget-area-before"
			aria-label="<?= htmlspecialchars(
				$t('chanzine.article_page.areas.before'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			<?= $widgetsBeforeContent ?>
		</section>

	<?php endif; ?>

	<div class="chanzine-article-layout">

		<main class="chanzine-article-main">

			<article class="card chanzine-article">

				<img
					class="chanzine-article-cover"
					src="<?= htmlspecialchars(
						$articleCover,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					alt="<?= htmlspecialchars(
						(string) ($article['title'] ?? ''),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>

				<div class="chanzine-article-body">

					<header class="chanzine-article-header">

						<?php if (!empty($article['published_at'])): ?>

							<?php
							$publishedTimestamp = strtotime(
								(string) $article['published_at']
							);
							?>

							<?php if ($publishedTimestamp !== false): ?>

								<div class="chanzine-article-meta">

									<?= htmlspecialchars(
										$t('chanzine.article_page.published_on'),
										ENT_QUOTES,
										'UTF-8'
									) ?>

									<time
										datetime="<?= htmlspecialchars(
											date(
												'Y-m-d\TH:i:s',
												$publishedTimestamp
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>"
									>
										<?= htmlspecialchars(
											date(
												'd/m/Y H:i',
												$publishedTimestamp
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</time>

								</div>

							<?php endif; ?>

						<?php endif; ?>

						<div class="chanzine-article-title-row">

							<h1>
								<?= htmlspecialchars(
									(string) ($article['title'] ?? ''),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</h1>

							<form
								method="post"
								action="/article/<?= rawurlencode(
									(string) ($article['uuid'] ?? '')
								) ?>/<?= !empty($article['is_saved'])
									? 'unsave'
									: 'save' ?>"
							>

								<button
									type="submit"
									class="chanzine-save-button"
								>

									<i
										class="<?= !empty($article['is_saved'])
											? 'fa-solid'
											: 'fa-regular' ?> fa-bookmark"
										aria-hidden="true"
									></i>

									<?= htmlspecialchars(
										!empty($article['is_saved'])
											? $t(
												'chanzine.article_page.save.saved'
											)
											: $t(
												'chanzine.article_page.save.save'
											),
										ENT_QUOTES,
										'UTF-8'
									) ?>

								</button>

							</form>

						</div>

						<?php if (!empty($article['excerpt'])): ?>

							<p class="chanzine-article-excerpt">
								<?= nl2br(
									htmlspecialchars(
										(string) $article['excerpt'],
										ENT_QUOTES,
										'UTF-8'
									)
								) ?>
							</p>

						<?php endif; ?>

					</header>

					<div class="chanzine-content">
						<?= $article['content_html'] ?? '' ?>
					</div>

				</div>

			</article>

		</main>

		<aside
			class="chanzine-article-sidebar"
			aria-label="<?= htmlspecialchars(
				$t('chanzine.article_page.areas.sidebar'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>

			<?php if (!empty($article['ping_uuid'])): ?>

				<section class="chanzine-discussion">

					<div
						class="chanzine-discussion-icon"
						aria-hidden="true"
					>
						<i class="fa-solid fa-comments"></i>
					</div>

					<div class="chanzine-discussion-copy">

						<h2>
							<?= htmlspecialchars(
								$t(
									'chanzine.article_page.discussion.title'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</h2>

						<p>
							<?= htmlspecialchars(
								$t(
									'chanzine.article_page.discussion.text'
								),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</p>

					</div>

					<a
						class="chanzine-discussion-link"
						href="/ping/<?= rawurlencode(
							(string) $article['ping_uuid']
						) ?>"
					>

						<?= htmlspecialchars(
							$t(
								'chanzine.article_page.discussion.join'
							),
							ENT_QUOTES,
							'UTF-8'
						) ?>

						<i
							class="fa-solid fa-arrow-right"
							aria-hidden="true"
						></i>

					</a>

				</section>

			<?php endif; ?>

			<div class="card chanzine-sidebar-card">

				<a
					class="button button-primary chanzine-back-button"
					href="/chanzine"
				>

					<i
						class="fa-solid fa-arrow-left"
						aria-hidden="true"
					></i>

					<?= htmlspecialchars(
						$t('chanzine.article_page.all_articles'),
						ENT_QUOTES,
						'UTF-8'
					) ?>

				</a>

			</div>

			<?php if ($widgetsSidebar !== ''): ?>

				<div
					class="mv-block-area chanzine-article-widget-area chanzine-article-widget-area-sidebar"
					aria-label="<?= htmlspecialchars(
						$t(
							'chanzine.article_page.areas.sidebar_widgets'
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
			class="mv-block-area chanzine-article-widget-area chanzine-article-widget-area-after"
			aria-label="<?= htmlspecialchars(
				$t('chanzine.article_page.areas.after'),
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
			<?= $widgetsAfterContent ?>
		</section>

	<?php endif; ?>

</div>
