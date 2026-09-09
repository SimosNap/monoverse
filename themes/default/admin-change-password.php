<?php
declare(strict_types=1);

$errors = is_array($errors ?? null)
	? $errors
	: [];

$success = is_array($success ?? null)
	? $success
	: [];
?>

<section class="mv-admin-page">

	<div class="mv-admin-page-heading">

		<div>

			<h1>
				<?= htmlspecialchars(
					$t('admin.account.password.title'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h1>

			<p>
				<?= htmlspecialchars(
					$t('admin.account.password.description'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	</div>

	<?php if ($errors !== []): ?>

		<div class="mv-admin-alert mv-admin-alert-error">

			<?php foreach ($errors as $error): ?>

				<p>
					<?= htmlspecialchars(
						(string) $error,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

	<?php if ($success !== []): ?>

		<div class="mv-admin-alert mv-admin-alert-success">

			<?php foreach ($success as $message): ?>

				<p>
					<?= htmlspecialchars(
						(string) $message,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</p>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

	<form
		method="post"
		action="/admin/change-password"
		class="mv-admin-form"
	>

		<div class="mv-admin-card">

			<div class="mv-admin-field">

				<label for="current_password">
					<?= htmlspecialchars(
						$t('admin.account.password.fields.current'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					type="password"
					id="current_password"
					name="current_password"
					autocomplete="current-password"
					required
				>

			</div>

			<div class="mv-admin-field">

				<label for="new_password">
					<?= htmlspecialchars(
						$t('admin.account.password.fields.new'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					type="password"
					id="new_password"
					name="new_password"
					autocomplete="new-password"
					required
				>

			</div>

			<div class="mv-admin-field">

				<label for="new_password_confirmation">
					<?= htmlspecialchars(
						$t('admin.account.password.fields.confirm'),
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</label>

				<input
					type="password"
					id="new_password_confirmation"
					name="new_password_confirmation"
					autocomplete="new-password"
					required
				>

			</div>

		</div>

		<div class="mv-admin-form-actions">

			<a href="/admin">
				<?= htmlspecialchars(
					$t('admin.account.password.actions.cancel'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</a>

			<button type="submit">
				<?= htmlspecialchars(
					$t('admin.account.password.actions.save'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</button>

		</div>

	</form>

</section>
