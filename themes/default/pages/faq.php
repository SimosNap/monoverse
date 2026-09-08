<?php
declare(strict_types=1);

$sections = is_array($sections ?? null)
	? $sections
	: [];
?>

<section class="faq-page">

	<div class="faq-shell">

		<main class="faq-main">

			<header class="faq-header">
				<h1><?= htmlspecialchars(
					$t('faq.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?></h1>

				<p><?= htmlspecialchars(
					$t('faq.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?></p>
			</header>

			<?php if ($sections === []): ?>

				<div class="faq-empty">
					<p><?= htmlspecialchars(
						$t('faq.empty'),
						ENT_QUOTES,
						'UTF-8'
					) ?></p>
				</div>

			<?php else: ?>

				<div class="faq-sections">

					<?php foreach ($sections as $section => $items): ?>

						<section class="faq-section">

							<?php if ((string) $section !== 'FAQ'): ?>
								<h2><?= htmlspecialchars(
									(string) $section,
									ENT_QUOTES,
									'UTF-8'
								) ?></h2>
							<?php endif; ?>

							<div class="faq-list">

								<?php foreach ($items as $faq): ?>

									<?php
									$question = trim(
										(string) ($faq['question'] ?? '')
									);

									$answer = trim(
										(string) ($faq['answer'] ?? '')
									);

									$anchor = trim(
										(string) ($faq['anchor'] ?? '')
									);

									if (
										$question === ''
										|| $anchor === ''
									) {
										continue;
									}
									?>

									<article
										class="faq-item"
										id="<?= htmlspecialchars(
											$anchor,
											ENT_QUOTES,
											'UTF-8'
										) ?>"
									>
										<header class="faq-item-header">

											<h3>
												<span>
													<?= htmlspecialchars(
														$question,
														ENT_QUOTES,
														'UTF-8'
													) ?>
												</span>

												<a
													class="faq-permalink"
													href="#<?= htmlspecialchars(
														$anchor,
														ENT_QUOTES,
														'UTF-8'
													) ?>"
													aria-label="<?= htmlspecialchars(
														$t(
															'faq.permalink',
															[
																'question' =>
																	$question,
															]
														),
														ENT_QUOTES,
														'UTF-8'
													) ?>"
												>
													<i
														class="fa-solid fa-link"
														aria-hidden="true"
													></i>
												</a>
											</h3>

										</header>

										<div class="faq-answer">
											<?= nl2br(
												htmlspecialchars(
													$answer,
													ENT_QUOTES,
													'UTF-8'
												)
											) ?>
										</div>

									</article>

								<?php endforeach; ?>

							</div>

						</section>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</main>

		<?php if ($sections !== []): ?>

			<aside class="faq-sidebar">

				<nav
					class="faq-index"
					aria-label="<?= htmlspecialchars(
						$t('faq.index.label'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<header class="faq-index-header">
						<h2><?= htmlspecialchars(
							$t('faq.index.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?></h2>
					</header>

					<div class="faq-index-body">
						<ul>
							<?php foreach ($sections as $section => $items): ?>

								<?php foreach ($items as $faq): ?>

									<?php
									$question = trim(
										(string) ($faq['question'] ?? '')
									);

									$anchor = trim(
										(string) ($faq['anchor'] ?? '')
									);

									if (
										$question === ''
										|| $anchor === ''
									) {
										continue;
									}
									?>

									<li>
										<a href="#<?= htmlspecialchars(
											$anchor,
											ENT_QUOTES,
											'UTF-8'
										) ?>">
											<i
												class="fa-solid fa-link"
												aria-hidden="true"
											></i>

											<span>
												<?= htmlspecialchars(
													$question,
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</span>
										</a>
									</li>

								<?php endforeach; ?>

							<?php endforeach; ?>
						</ul>
					</div>

				</nav>

			</aside>

		<?php endif; ?>

	</div>

</section>
