<?php
declare(strict_types=1);

$administrators = is_array($administrators ?? null)
	? $administrators
	: [];
?>

<section class="mv-admin-page">

	<div class="mv-admin-page-heading">

		<div>

			<h1>
				<?= htmlspecialchars(
					$t('admin.administrators.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('admin.administrators.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

		<a
			href="/admin/administrators/create"
			class="mv-admin-action"
		>
			<?= htmlspecialchars(
				$t('admin.administrators.actions.create'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</a>

	</div>

	<div class="mv-admin-card">

		<?php if ($administrators === []): ?>

			<p>
				<?= htmlspecialchars(
					$t('admin.administrators.empty'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		<?php else: ?>

			<div class="mv-admin-table-wrap">

				<table class="mv-admin-table">

					<thead>
						<tr>
							<th>
								<?= htmlspecialchars(
									$t('admin.administrators.columns.username'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<?= htmlspecialchars(
									$t('admin.administrators.columns.role'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<?= htmlspecialchars(
									$t('admin.administrators.columns.status'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<?= htmlspecialchars(
									$t('admin.administrators.columns.last_login'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th>
								<?= htmlspecialchars(
									$t('admin.administrators.columns.last_login_ip'),
									ENT_QUOTES,
									'UTF-8'
								) ?>
							</th>

							<th></th>
						</tr>
					</thead>

					<tbody>

						<?php foreach ($administrators as $administrator): ?>

							<?php
							$id = (int) (
								$administrator['id']
								?? 0
							);

							$username = (string) (
								$administrator['username']
								?? ''
							);

							$role = (string) (
								$administrator['role']
								?? ''
							);

							$enabled = (int) (
								$administrator['enabled']
								?? 0
							) === 1;

							$lastLoginAt = (int) (
								$administrator['last_login_at']
								?? 0
							);

							$lastLoginIp = trim(
								(string) (
									$administrator['last_login_ip']
									?? ''
								)
							);

							$isOriginal = (
								$role === 'administrator'
							);

							$roleLabel = match ($role) {
								'administrator' => $t(
									'admin.administrators.roles.administrator'
								),
								'siteadmin' => $t(
									'admin.administrators.roles.siteadmin'
								),
								'contentadmin' => $t(
									'admin.administrators.roles.contentadmin'
								),
								default => $role,
							};
							?>

							<tr>

								<td>
									<strong>
										<?= htmlspecialchars(
											$username,
											ENT_QUOTES,
											'UTF-8'
										) ?>
									</strong>
								</td>

								<td>
									<?= htmlspecialchars(
										$roleLabel,
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</td>

								<td>
									<?= htmlspecialchars(
										$enabled
											? $t(
												'admin.administrators.status.enabled'
											)
											: $t(
												'admin.administrators.status.disabled'
											),
										ENT_QUOTES,
										'UTF-8'
									) ?>
								</td>

								<td>
									<?php if ($lastLoginAt > 0): ?>

										<?= htmlspecialchars(
											date(
												'd/m/Y H:i',
												$lastLoginAt
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>

									<?php else: ?>

										<?= htmlspecialchars(
											$t(
												'admin.administrators.last_login.never'
											),
											ENT_QUOTES,
											'UTF-8'
										) ?>

									<?php endif; ?>
								</td>

								<td>
									<?= $lastLoginIp !== ''
										? htmlspecialchars(
											$lastLoginIp,
											ENT_QUOTES,
											'UTF-8'
										)
										: '—' ?>
								</td>

								<td>

									<?php if ($isOriginal): ?>

										<span class="mv-admin-muted">
											<?= htmlspecialchars(
												$t(
													'admin.administrators.status.protected'
												),
												ENT_QUOTES,
												'UTF-8'
											) ?>
										</span>

									<?php else: ?>

										<div class="mv-admin-table-actions">

											<a
												href="/admin/administrators/<?= $id ?>/edit"
											>
												<?= htmlspecialchars(
													$t(
														'admin.administrators.actions.edit'
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>
											</a>

											<form
												method="post"
												action="/admin/administrators/<?= $id ?>/delete"
												onsubmit="return confirm(<?= htmlspecialchars(
													json_encode(
														$t(
															'admin.administrators.delete_confirm'
														),
														JSON_UNESCAPED_UNICODE
														| JSON_UNESCAPED_SLASHES
													),
													ENT_QUOTES,
													'UTF-8'
												) ?>);"
											>
												<button
													type="submit"
													class="mv-admin-link-button"
												>
													<?= htmlspecialchars(
														$t(
															'admin.administrators.actions.delete'
														),
														ENT_QUOTES,
														'UTF-8'
													) ?>
												</button>
											</form>

										</div>

									<?php endif; ?>

								</td>

							</tr>

						<?php endforeach; ?>

					</tbody>

				</table>

			</div>

		<?php endif; ?>

	</div>

</section>
