<?php
declare(strict_types=1);

$administrator = is_array($administrator ?? null)
	? $administrator
	: [];

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
	?? 'siteadmin'
);

$enabled = (int) (
	$administrator['enabled']
	?? 1
) === 1;

$isEdit = $id > 0;
?>

<section class="mv-admin-page">

	<div class="mv-admin-page-heading">

		<div>

			<h1>
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.administrators.form.edit_title')
						: $t('admin.administrators.form.create_title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.administrators.form.edit_description')
						: $t('admin.administrators.form.create_description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	</div>

	<?php if (!empty($error)): ?>

		<div class="mv-admin-alert mv-admin-alert-error">
			<?= htmlspecialchars(
				(string) $error,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</div>

	<?php endif; ?>

	<form
		method="post"
		action="<?= htmlspecialchars(
			(string) ($formAction ?? ''),
			ENT_QUOTES,
			'UTF-8'
		) ?>"
		class="mv-admin-form"
	>

		<div class="mv-admin-card">

			<div class="mv-admin-field">

				<label for="username">
					<?= htmlspecialchars(
						$t('admin.administrators.form.fields.username'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					type="text"
					id="username"
					name="username"
					value="<?= htmlspecialchars(
						$username,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					autocomplete="username"
					required
				>

			</div>

			<div class="mv-admin-field">

				<label for="role">
					<?= htmlspecialchars(
						$t('admin.administrators.form.fields.role'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<select
					id="role"
					name="role"
					required
				>

					<option
						value="siteadmin"
						<?= $role === 'siteadmin'
							? 'selected'
							: '' ?>
					>
						<?= htmlspecialchars(
							$t('admin.administrators.roles.siteadmin'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</option>

					<option
						value="contentadmin"
						<?= $role === 'contentadmin'
							? 'selected'
							: '' ?>
					>
						<?= htmlspecialchars(
							$t('admin.administrators.roles.contentadmin'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</option>

				</select>

				<span class="mv-admin-field-help">
					<?= htmlspecialchars(
						$t('admin.administrators.form.role_help'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</span>

			</div>

			<div class="mv-admin-field">

				<label for="password">
					<?= htmlspecialchars(
						$isEdit
							? $t('admin.administrators.form.fields.new_password')
							: $t('admin.administrators.form.fields.password'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					type="password"
					id="password"
					name="password"
					autocomplete="new-password"
					<?= $isEdit
						? ''
						: 'required' ?>
				>

				<?php if ($isEdit): ?>

					<span class="mv-admin-field-help">
						<?= htmlspecialchars(
							$t('admin.administrators.form.password_help'),
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</span>

				<?php endif; ?>

			</div>

			<?php if ($isEdit): ?>

				<div class="mv-admin-field">

					<label>

						<input
							type="checkbox"
							name="enabled"
							value="1"
							<?= $enabled
								? 'checked'
								: '' ?>
						>

						<span>
							<?= htmlspecialchars(
								$t('admin.administrators.form.fields.enabled'),
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</label>

				</div>

			<?php endif; ?>

		</div>

		<div class="mv-admin-form-actions">

			<a href="/admin/administrators">
				<?= htmlspecialchars(
					$t('admin.administrators.form.actions.cancel'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

			<button type="submit">
				<?= htmlspecialchars(
					$isEdit
						? $t('admin.administrators.form.actions.save')
						: $t('admin.administrators.form.actions.create'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</button>

		</div>

	</form>

</section>
