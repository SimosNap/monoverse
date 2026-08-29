<?php
declare(strict_types=1);

$currentPath = parse_url(
	(string) ($_SERVER['REQUEST_URI'] ?? '/account/moderation'),
	PHP_URL_PATH
);

$currentPath = is_string($currentPath)
	? rtrim($currentPath, '/')
	: '/account/moderation';

if ($currentPath === '') {
	$currentPath = '/account/moderation';
}

$isDashboard = $currentPath === '/account/moderation';
$isReports = str_starts_with(
	$currentPath,
	'/account/moderation/report'
);
$isBans = str_starts_with(
	$currentPath,
	'/account/moderation/bans'
);
$isMutes = str_starts_with(
	$currentPath,
	'/account/moderation/mutes'
);
?>

<nav
	class="mv-moderation-nav"
	aria-label="<?= htmlspecialchars(
		$t('account.moderation_navigation.aria_label'),
		ENT_QUOTES,
		'UTF-8'
	) ?>"
>

	<a
		href="/account/moderation"
		class="<?= $isDashboard ? 'is-active' : '' ?>"
		<?= $isDashboard ? 'aria-current="page"' : '' ?>
	>
		<?= htmlspecialchars(
			$t('account.moderation_navigation.dashboard'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</a>

	<a
		href="/account/moderation/reports"
		class="<?= $isReports ? 'is-active' : '' ?>"
		<?= $isReports ? 'aria-current="page"' : '' ?>
	>
		<?= htmlspecialchars(
			$t('account.moderation_navigation.reports'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</a>

	<a
		href="/account/moderation/bans"
		class="<?= $isBans ? 'is-active' : '' ?>"
		<?= $isBans ? 'aria-current="page"' : '' ?>
	>
		<?= htmlspecialchars(
			$t('account.moderation_navigation.bans'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</a>

	<a
		href="/account/moderation/mutes"
		class="<?= $isMutes ? 'is-active' : '' ?>"
		<?= $isMutes ? 'aria-current="page"' : '' ?>
	>
		<?= htmlspecialchars(
			$t('account.moderation_navigation.mutes'),
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</a>

</nav>
