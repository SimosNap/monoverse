<?php
declare(strict_types=1);

/** @var array $articles */

$articles = isset($articles) && is_array($articles)
	? $articles
	: [];

$statusLabels = [
	'submitted' => $t('account.articles.status.submitted'),
	'published' => $t('account.articles.status.published'),
	'rejected' => $t('account.articles.status.rejected'),
];
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<div class="page-header">

	<h1>
		<?= htmlspecialchars(
			$t('account.articles.title'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= htmlspecialchars(
			$t('account.articles.subtitle'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</p>
	
	<?php if (
		(($settings['chanzine_user_submissions_enabled'] ?? '0') === '1')
	): ?>
	
		<div class="mv-account-articles-header-actions">
	
			<a
				href="/chanzine/submit"
				class="mv-button"
			>
				<i
					class="fa-solid fa-plus"
					aria-hidden="true"
				></i>
	
				<?= htmlspecialchars(
					$t('account.articles.submit'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>
	
		</div>
	
	<?php endif; ?>

</div>

<div class="mv-account-articles-list">

	<?php if ($articles === []): ?>

		<div class="card">

			<p>
				<?= htmlspecialchars(
					$t('account.articles.empty'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	<?php else: ?>

		<?php foreach ($articles as $article): ?>

			<?php
			$status = (string) ($article['status'] ?? '');

			$statusLabel = $statusLabels[$status]
				?? ucfirst($status);
			?>

			<article class="card mv-account-article-card">

				<div class="mv-account-article-content">

					<div class="mv-account-article-heading">

						<h2>
							<?= htmlspecialchars(
								(string) ($article['title'] ?? ''),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</h2>

						<span
							class="mv-account-article-status is-<?= htmlspecialchars(
								$status,
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						>
							<?= htmlspecialchars(
								$statusLabel,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</div>

					<div class="mv-account-article-meta">

						<?php if (!empty($article['category_name'])): ?>

							<span>
								<?= htmlspecialchars(
									(string) $article['category_name'],
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

						<?php if (!empty($article['submitted_at'])): ?>

							<span>
								<?= htmlspecialchars(
									$t('account.articles.submitted_on'),
									ENT_QUOTES,
									'UTF-8'
								) ?>

								<?= htmlspecialchars(
									date(
										'd/m/Y H:i',
										strtotime(
											(string) $article['submitted_at']
										)
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</span>

						<?php endif; ?>

					</div>

					<?php if ($status === 'submitted'): ?>

						<div class="mv-account-article-actions">

							<a
								href="/account/articles/<?= rawurlencode(
									(string) $article['uuid']
								) ?>/edit"
								class="mv-button"
							>
								<i
									class="fa-solid fa-pen"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('account.articles.actions.edit'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>

						</div>

					<?php endif; ?>
					
					<?php if (
						$status === 'rejected'
						&& !empty($article['rejection_reason'])
					): ?>
					
						<div class="mv-account-article-rejection">
					
							<div class="mv-account-article-rejection-title">
								<i
									class="fa-solid fa-circle-info"
									aria-hidden="true"
								></i>
					
								<?= htmlspecialchars(
									$t('account.articles.rejection.title'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</div>
					
							<p>
								<?= nl2br(
									htmlspecialchars(
										(string) $article['rejection_reason'],
										ENT_QUOTES,
										'UTF-8'
									)
								) ?>
							</p>
					
						</div>
					
					<?php endif; ?>
					
					<?php if (
						$status === 'published'
						&& !empty($article['slug'])
					): ?>
					
						<div class="mv-account-article-actions">
					
							<a
								href="/chanzine/<?= rawurlencode(
									(string) $article['slug']
								) ?>"
								class="mv-button"
							>
								<i
									class="fa-solid fa-arrow-up-right-from-square"
									aria-hidden="true"
								></i>
					
								<?= htmlspecialchars(
									$t('account.articles.actions.view'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>
					
						</div>
					
					<?php endif; ?>

				</div>

			</article>

		<?php endforeach; ?>

	<?php endif; ?>

</div>
