<?php

$username = trim(
	(string) (
		$username
		?? ''
	)
);

$avatarUrl = trim(
	(string) (
		$avatar_url
		?? ''
	)
);

$showAvatar = !empty($show_avatar);

$initial = $username !== ''
	? mb_strtoupper(
		mb_substr(
			$username,
			0,
			1
		)
	)
	: '?';

$letter = mb_strtolower($initial);

$allowedLetters = range('a', 'z');

if (!in_array($letter, $allowedLetters, true)) {
	$letter = 'default';
}

if (
	$showAvatar
	&& $avatarUrl !== ''
): ?>

	<img
		class="mv-avatar-image"
		src="<?= htmlspecialchars(
			$avatarUrl,
			ENT_QUOTES,
			'UTF-8'
		) ?>"
		alt=""
	>

<?php else: ?>

	<span
		class="mv-avatar-fallback mv-avatar-letter-<?= htmlspecialchars(
			$letter,
			ENT_QUOTES,
			'UTF-8'
		) ?>"
		aria-hidden="true"
	>
		<?= htmlspecialchars(
			$initial,
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</span>

<?php endif; ?>
