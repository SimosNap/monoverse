<?php
declare(strict_types=1);

$count = max(0, (int) ($count ?? 0));

$notificationLabel = $t('notifications.label');

$label = $count > 0
	? $t(
		'notifications.unread_label',
		[
			'count' => $count,
		]
	)
	: $notificationLabel;
?>

<a
	href="/notifications"
	class="mv-nav-notifications<?= !empty($active) ? ' is-active' : '' ?><?= $count > 0 ? ' has-notifications' : '' ?>"
	aria-label="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>"
	title="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>"
>
	<i class="fas fa-bell" aria-hidden="true"></i>

	<span class="mv-nav-notifications-label">
		<?= htmlspecialchars($notificationLabel, ENT_QUOTES, 'UTF-8') ?>
	</span>

	<?php if ($count > 0): ?>
		<span class="mv-nav-badge">
			<?= $count > 99 ? '99+' : $count ?>
		</span>
	<?php endif; ?>
</a>
