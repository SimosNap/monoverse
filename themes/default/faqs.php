<?php
declare(strict_types=1);

$faqs = is_array($faqs ?? null)
	? $faqs
	: [];

$statusLabels = [
	'draft' => $t('admin.faq.status.draft'),
	'published' => $t('admin.faq.status.published'),
];
?>

<div class="admin-page admin-faq-page">

	<header class="admin-page-header">

		<div>

			<h1>
				<?= htmlspecialchars(
					$t('admin.faq.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('admin.faq.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

		<a
			class="mv-admin-button"
			href="/admin/faq/create"
		>
			<i
				class="fa-solid fa-plus"
				aria-hidden="true"
			></i>

			<?= htmlspecialchars(
				$t('admin.faq.actions.new'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	</header>

	<?php if ($faqs === []): ?>

		<div class="admin-form-card admin-faq-empty">

			<i
				class="fa-solid fa-circle-question"
				aria-hidden="true"
			></i>

			<div>

				<strong>
					<?= htmlspecialchars(
						$t('admin.faq.empty.title'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</strong>

				<p>
					<?= htmlspecialchars(
						$t('admin.faq.empty.description'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			</div>

		</div>

	<?php else: ?>

		<div class="admin-form-card admin-faq-card">

			<div class="admin-faq-table-wrapper">

				<table class="admin-faq-table">

					<thead>

						<tr>

							<th>
								<?= htmlspecialchars(
									$t('admin.faq.table.question'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<?= htmlspecialchars(
									$t('admin.faq.table.section'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<?= htmlspecialchars(
									$t('admin.faq.table.status'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<?= htmlspecialchars(
									$t('admin.faq.table.order'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<span class="sr-only">
									<?= htmlspecialchars(
										$t('admin.faq.table.actions'),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</span>
							</th>

						</tr>

					</thead>

					<tbody>

						<?php foreach ($faqs as $faq): ?>

							<?php
							$faqId = (int) ($faq['id'] ?? 0);

							$question = trim(
								(string) ($faq['question'] ?? '')
							);

							$anchor = trim(
								(string) ($faq['anchor'] ?? '')
							);

							$section = trim(
								(string) ($faq['section'] ?? '')
							);

							$status = trim(
								(string) ($faq['status'] ?? 'draft')
							);

							$sortOrder = max(
								0,
								(int) ($faq['sort_order'] ?? 0)
							);

							$statusLabel = $statusLabels[$status]
								?? ucfirst($status);
							?>

							<tr>

								<td>

									<div class="admin-faq-identity">

										<strong>
											<?= htmlspecialchars(
												$question,
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</strong>

										<?php if ($anchor !== ''): ?>

											<a
												class="admin-faq-permalink"
												href="/faq#<?= rawurlencode(
													$anchor
												) ?>"
												target="_blank"
												rel="noopener"
												title="/faq#<?= htmlspecialchars(
													$anchor,
													ENT_QUOTES,
													'UTF-8'
												) ?>"
												aria-label="/faq#<?= htmlspecialchars(
													$anchor,
													ENT_QUOTES,
													'UTF-8'
												) ?>"
											>
												<i
													class="fa-solid fa-arrow-up-right-from-square"
													aria-hidden="true"
												></i>
											</a>

										<?php endif; ?>

									</div>

								</td>

								<td>
									<?= $section !== ''
										? htmlspecialchars(
											$section,
											ENT_QUOTES,
											'UTF-8'
										)
										: '—' ?>
								</td>

								<td>

									<span class="admin-faq-status admin-faq-status-<?= htmlspecialchars(
										$status,
										ENT_QUOTES,
										'UTF-8'
									) ?>">
										<?= htmlspecialchars(
											$statusLabel,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</span>

								</td>

								<td>
									<?= $sortOrder ?>
								</td>

								<td>

									<div class="admin-faq-actions">

										<a
											href="/admin/faq/<?= $faqId ?>/edit"
										>
											<?= htmlspecialchars(
												$t(
													'admin.faq.actions.edit'
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</a>

										<form
											action="/admin/faq/<?= $faqId ?>/delete"
											method="post"
											onsubmit="return confirm(<?= htmlspecialchars(
												json_encode(
													$t(
														'admin.faq.confirm.delete'
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
											<button type="submit">
												<?= htmlspecialchars(
													$t(
														'admin.faq.actions.delete'
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</button>
										</form>

									</div>

								</td>

							</tr>

						<?php endforeach; ?>

					</tbody>

				</table>

			</div>

		</div>

	<?php endif; ?>

</div>
