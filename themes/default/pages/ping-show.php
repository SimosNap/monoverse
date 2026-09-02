<?php
declare(strict_types=1);

/** @var array|null $post */
/** @var array $comments */
/** @var bool $isLogged */
/** @var array|null $user */

$comments = is_array($comments ?? null)
	? $comments
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

$hasSidebar = $widgetsSidebar !== '';
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
						$t('ping.show.areas.before'),
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				>
					<?= $widgetsBeforeContent ?>
				</section>

			<?php endif; ?>

			<?php if (!empty($blockedMessage)): ?>

				<div class="card">

					<h2>
						<?= htmlspecialchars(
							$t('ping.show.blocked.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							(string) $blockedMessage,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>

				</div>

			<?php else: ?>

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

				<?php if (is_array($post)): ?>

					<?php include __DIR__ . '/../components/ping-card.php'; ?>

					<?php if (!empty($isLogged)): ?>

						<div class="ping-composer pong-composer">

							<form
								method="post"
								action="/ping/<?= rawurlencode(
									(string) ($post['uuid'] ?? '')
								) ?>/comment"
							>

								<textarea
									name="content"
									class="ping-composer-textarea"
									rows="4"
									maxlength="1000"
									placeholder="<?= htmlspecialchars(
										$t('ping.show.pong.placeholder'),
										ENT_QUOTES,
										'UTF-8'
									) ?>"
								></textarea>

								<div
									class="ping-code-composer"
									data-pong-code-composer
									hidden
								>

									<div class="ping-code-composer-header">

										<label for="pong-code-language">
											<?= htmlspecialchars(
												$t('ping.composer.code_language'),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</label>

										<select
											id="pong-code-language"
											name="code_language"
											class="ping-code-language"
										>
											<option value="text">Text</option>
											<option value="php">PHP</option>
											<option value="javascript">JavaScript</option>
											<option value="html">HTML</option>
											<option value="css">CSS</option>
											<option value="sql">SQL</option>
											<option value="bash">Bash</option>
											<option value="python">Python</option>
											<option value="c">C</option>
											<option value="cpp">C++</option>
											<option value="java">Java</option>
											<option value="json">JSON</option>
										</select>

									</div>

									<textarea
										name="code"
										class="ping-code-textarea"
										rows="10"
										maxlength="10000"
										spellcheck="false"
										placeholder="<?= htmlspecialchars(
											$t('ping.composer.code_placeholder'),
											ENT_QUOTES,
											'UTF-8'
										) ?>"
									></textarea>

									<div class="ping-code-counter">
										0 / 10000
									</div>

								</div>

								<div class="ping-composer-actions">

									<div class="ping-composer-tools">

										<button
											type="button"
											class="ping-code-toggle"
											data-pong-code-toggle
											aria-expanded="false"
											title="<?= htmlspecialchars(
												$t('ping.composer.code'),
												ENT_QUOTES,
												'UTF-8'
											) ?>"
											aria-label="<?= htmlspecialchars(
												$t('ping.composer.code'),
												ENT_QUOTES,
												'UTF-8'
											) ?>"
										>
											<i
												class="fa-solid fa-code"
												aria-hidden="true"
											></i>
										</button>

									</div>

									<button
										type="submit"
										class="ping-composer-submit"
									>
										<?= htmlspecialchars(
											$t('ping.show.pong.publish'),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</button>

								</div>

							</form>

						</div>

					<?php endif; ?>

					<div class="pong-list">

						<h2 class="pong-list-title">
							<?= htmlspecialchars(
								$t('ping.show.pong.title'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
							(<?= (int) ($post['comments_count'] ?? 0) ?>)
						</h2>

						<?php if ($comments === []): ?>

							<p class="pong-empty">
								<?= htmlspecialchars(
									$t('ping.show.pong.empty'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</p>

						<?php else: ?>

							<div
								id="pong-infinite-list"
								data-next-offset="<?= count($comments) ?>"
								data-page-size="<?= (int) $pageSize ?>"
								data-post-uuid="<?= htmlspecialchars(
									(string) ($post['uuid'] ?? ''),
									ENT_QUOTES,
									'UTF-8'
								) ?>"
							>

								<?php foreach ($comments as $comment): ?>

									<?php include __DIR__ . '/../components/pong-card.php'; ?>

								<?php endforeach; ?>

							</div>

							<?php if (count($comments) >= $pageSize): ?>

								<div
									id="pong-infinite-trigger"
									aria-hidden="true"
								></div>

							<?php endif; ?>

						<?php endif; ?>

					</div>

				<?php endif; ?>

			<?php endif; ?>

			<?php if ($widgetsAfterContent !== ''): ?>

				<section
					class="mv-block-area ping-widget-area ping-widget-area-after"
					aria-label="<?= htmlspecialchars(
						$t('ping.show.areas.after'),
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
					$t('ping.show.areas.sidebar'),
					ENT_QUOTES,
					'UTF-8'
				) ?>"
			>

				<div class="mv-block-area ping-widget-area ping-widget-area-sidebar">
					<?= $widgetsSidebar ?>
				</div>

			</aside>

		<?php endif; ?>

	</div>

</div>

<?php if (empty($blockedMessage)): ?>

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

<?php endif; ?>
