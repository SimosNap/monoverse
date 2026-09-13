<div class="admin-page admin-articles-page">

	<div class="admin-page-header">

		<div>
			<h1>
				<?= htmlspecialchars(
					$t('admin.events.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('admin.events.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>
		</div>

		<a
			href="/admin/events/create"
			class="mv-admin-button is-primary"
		>
			<i class="fa fa-plus" aria-hidden="true"></i>

			<?= htmlspecialchars(
				$t('admin.events.actions.new'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	</div>

	<?php if (!empty($success)): ?>

		<div class="alert alert-success">
			<?= htmlspecialchars(
				$success,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php endif; ?>

	<?php if (!empty($error)): ?>

		<div class="alert alert-danger">
			<?= htmlspecialchars(
				$error,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php endif; ?>

	<?php if (!empty($submissions)): ?>

		<section class="admin-article-submissions">

			<div class="admin-page-section-header">

				<div>
					<h2>
						<?= htmlspecialchars(
							$t('admin.events.submissions.title'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</h2>

					<p>
						<?= htmlspecialchars(
							$t('admin.events.submissions.description'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</p>
				</div>

			</div>

			<div class="admin-articles-list">

				<?php foreach ($submissions as $event): ?>

					<article class="admin-article-card">

						<div class="admin-article-cover">

							<?php if (!empty($event['cover'])): ?>

								<img
									src="<?= htmlspecialchars(
										$event['cover'],
										ENT_QUOTES,
										'UTF-8'
									) ?>"
									alt=""
									loading="lazy"
								>

							<?php else: ?>

								<div class="admin-article-cover-placeholder">
									<i
										class="fa fa-calendar-days"
										aria-hidden="true"
									></i>
								</div>

							<?php endif; ?>

						</div>

						<div class="admin-article-content">

							<div class="admin-article-heading">

								<div class="admin-article-title-group">

									<h2>
										<?= htmlspecialchars(
											$event['title'],
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</h2>

									<div class="admin-article-meta">

										<span>
											<i
												class="fa fa-user"
												aria-hidden="true"
											></i>

											<?= htmlspecialchars(
												(string) (
													$event[
														'submitted_by_nickname'
													]
													?? $t(
														'admin.events.submissions.default_user'
													)
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

										<?php if (!empty($event['starts_at'])): ?>

											<span>
												<i
													class="fa fa-calendar"
													aria-hidden="true"
												></i>

												<?= htmlspecialchars(
													date(
														'd/m/Y H:i',
														strtotime(
															$event['starts_at']
														)
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</span>

										<?php endif; ?>

										<?php if (!empty($event['location'])): ?>

											<span>
												<i
													class="fa fa-location-dot"
													aria-hidden="true"
												></i>

												<?= htmlspecialchars(
													$event['location'],
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</span>

										<?php endif; ?>

									</div>

								</div>

								<span class="admin-status admin-status-draft">
									<?= htmlspecialchars(
										$t(
											'admin.events.submissions.status'
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							</div>

							<div class="admin-article-actions">

								<a
									href="/admin/events/<?= rawurlencode(
										$event['uuid']
									) ?>/edit"
									class="mv-admin-button is-secondary"
								>
									<i
										class="fa fa-pen"
										aria-hidden="true"
									></i>

									<?= htmlspecialchars(
										$t(
											'admin.events.actions.review'
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</a>

								<form
									method="post"
									action="/admin/events/<?= rawurlencode(
										$event['uuid']
									) ?>/reject"
									class="admin-article-reject-form"
								>
									<input
										type="text"
										name="rejection_reason"
										class="admin-article-reject-reason"
										placeholder="<?= htmlspecialchars(
											$t(
												'admin.events.submissions.rejection_reason'
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>"
										required
									>

									<button
										type="submit"
										class="mv-admin-button is-danger"
									>
										<i
											class="fa fa-xmark"
											aria-hidden="true"
										></i>

										<?= htmlspecialchars(
											$t(
												'admin.events.actions.reject'
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</button>
								</form>

							</div>

						</div>

					</article>

				<?php endforeach; ?>

			</div>

		</section>

	<?php endif; ?>

	<?php if (empty($events)): ?>

		<div class="admin-empty-state">

			<div class="admin-empty-state-icon">
				<i
					class="fa fa-calendar-days"
					aria-hidden="true"
				></i>
			</div>

			<h2>
				<?= htmlspecialchars(
					$t('admin.events.empty.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h2>

			<p>
				<?= htmlspecialchars(
					$t('admin.events.empty.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

			<a
				href="/admin/events/create"
				class="mv-admin-button is-primary"
			>
				<?= htmlspecialchars(
					$t('admin.events.actions.create_first'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

		</div>

	<?php else: ?>

		<div class="admin-articles-list">

			<?php foreach ($events as $event): ?>

				<article class="admin-article-card">

					<div class="admin-article-cover">

						<?php if (!empty($event['cover'])): ?>

							<img
								src="<?= htmlspecialchars(
									$event['cover'],
									ENT_QUOTES,
									'UTF-8'
								) ?>"
								alt=""
								loading="lazy"
							>

						<?php else: ?>

							<div class="admin-article-cover-placeholder">
								<i
									class="fa fa-calendar-days"
									aria-hidden="true"
								></i>
							</div>

						<?php endif; ?>

					</div>

					<div class="admin-article-content">

						<div class="admin-article-heading">

							<div class="admin-article-title-group">

								<h2>
									<?= htmlspecialchars(
										$event['title'],
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</h2>

								<div class="admin-article-meta">

									<?php if (!empty($event['starts_at'])): ?>

										<span>
											<i
												class="fa fa-calendar"
												aria-hidden="true"
											></i>

											<?= htmlspecialchars(
												date(
													'd/m/Y H:i',
													strtotime(
														$event['starts_at']
													)
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

									<?php endif; ?>

									<?php if (!empty($event['ends_at'])): ?>

										<span>
											<i
												class="fa fa-clock"
												aria-hidden="true"
											></i>

											<?= htmlspecialchars(
												date(
													'd/m/Y H:i',
													strtotime(
														$event['ends_at']
													)
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

									<?php endif; ?>

									<?php if (!empty($event['location'])): ?>

										<span>
											<i
												class="fa fa-location-dot"
												aria-hidden="true"
											></i>

											<?= htmlspecialchars(
												$event['location'],
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

									<?php endif; ?>

								</div>

							</div>

							<?php if ($event['status'] === 'published'): ?>

								<span class="admin-status admin-status-published">
									<?= htmlspecialchars(
										$t(
											'admin.events.status.published'
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php else: ?>

								<span class="admin-status admin-status-draft">
									<?= htmlspecialchars(
										$t(
											'admin.events.status.draft'
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>

							<?php endif; ?>

						</div>

						<div class="admin-article-actions">

							<a
								href="/admin/events/<?= rawurlencode(
									$event['uuid']
								) ?>/edit"
								class="mv-admin-button is-secondary"
							>
								<i
									class="fa fa-pen"
									aria-hidden="true"
								></i>

								<?= htmlspecialchars(
									$t('admin.events.actions.edit'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</a>

							<?php if ($event['status'] === 'draft'): ?>

								<form
									method="post"
									action="/admin/events/<?= rawurlencode(
										$event['uuid']
									) ?>/publish"
								>
									<button
										type="submit"
										class="mv-admin-button is-primary"
									>
										<i
											class="fa fa-upload"
											aria-hidden="true"
										></i>

										<?= htmlspecialchars(
											$t(
												'admin.events.actions.publish'
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</button>
								</form>

							<?php endif; ?>

							<form
								method="post"
								action="/admin/events/<?= rawurlencode(
									$event['uuid']
								) ?>/delete"
								onsubmit="return confirm(<?= htmlspecialchars(
									json_encode(
										$t(
											'admin.events.confirm.delete'
										),
										JSON_HEX_TAG
										| JSON_HEX_AMP
										| JSON_HEX_APOS
										| JSON_HEX_QUOT
									),
									ENT_QUOTES,
									'UTF-8'
								) ?>);"
							>
								<button
									type="submit"
									class="mv-admin-button is-danger"
								>
									<i
										class="fa fa-trash"
										aria-hidden="true"
									></i>

									<?= htmlspecialchars(
										$t(
											'admin.events.actions.delete'
										),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</button>
							</form>

						</div>

					</div>

				</article>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</div>
