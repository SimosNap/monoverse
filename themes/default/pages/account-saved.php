<?php
declare(strict_types=1);

/** @var array $savedItems */

$savedItems = isset($savedItems) && is_array($savedItems)
	? $savedItems
	: [];
?>

<?= $component('account-navigation', [
	'user' => $user ?? [],
	'settings' => $settings ?? [],
]) ?>

<div class="page-header">

	<h1>
		<?= htmlspecialchars(
			$t('account.saved.title'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</h1>

	<p class="page-subtitle">
		<?= htmlspecialchars(
			$t('account.saved.subtitle'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</p>

</div>

<div class="mv-saved-list">

	<?php if ($savedItems === []): ?>

		<div class="card">

			<p>
				<?= htmlspecialchars(
					$t('account.saved.empty'),
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>

		</div>

	<?php else: ?>

		<?php foreach ($savedItems as $item): ?>

			<?= $component('saved-item-card', [
				'item' => $item,
			]) ?>

		<?php endforeach; ?>

	<?php endif; ?>

</div>
