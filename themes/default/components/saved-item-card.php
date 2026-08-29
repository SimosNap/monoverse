<?php
declare(strict_types=1);

/** @var array $item */

$item = is_array($item ?? null)
	? $item
	: [];

$escape = static fn (mixed $value): string => htmlspecialchars(
	(string) $value,
	ENT_QUOTES,
	'UTF-8'
);

$type = (string) ($item['object_type'] ?? 'post');

$title = '';
$excerpt = '';

switch ($type) {

	case 'article':

		$title = trim((string) ($item['title'] ?? ''));

		if ($title === '') {
			$title = $t('account.saved.card.article');
		}

		$excerpt = trim((string) ($item['excerpt'] ?? ''));

		break;

	default:

		$content = trim((string) ($item['content'] ?? ''));

		if ($content === '') {
			$content = $t('account.saved.card.empty_ping');
		}

		$title = $content;

		if (mb_strlen($title) > 60) {
			$title = mb_substr($title, 0, 60) . '…';
		}

		$excerpt = $content;

		if (mb_strlen($excerpt) > 180) {
			$excerpt = mb_substr($excerpt, 0, 180) . '…';
		}

		break;
}

$url = match ($type) {

	'article' => '/chanzine/' . rawurlencode(
		(string) ($item['slug'] ?? '')
	),

	default => '/ping/' . rawurlencode(
		(string) ($item['uuid'] ?? '')
	),
};

$icon = match ($type) {

	'article' => 'fa-newspaper',

	default => 'fa-message',
};

$label = match ($type) {

	'article' => $t('account.saved.card.article'),

	default => $t('account.saved.card.ping'),
};

$removeUrl = match ($type) {

	'article' => '/article/' . rawurlencode(
		(string) ($item['uuid'] ?? '')
	) . '/unsave',

	default => '/ping/' . rawurlencode(
		(string) ($item['uuid'] ?? '')
	) . '/unsave',
};

$removeConfirm = $t(
	'account.saved.card.remove_confirm'
);

?>

<article class="saved-item-card">

	<div class="saved-item-header">

		<div class="saved-item-type">

			<i class="fa-solid <?= $icon ?>"></i>

			<span><?= $escape($label) ?></span>

		</div>

	</div>

	<h3 class="saved-item-title">
		<a href="<?= $escape($url) ?>">
			<?= $escape($title) ?>
		</a>
	</h3>

	<?php if ($excerpt !== ''): ?>

		<p class="saved-item-excerpt">

			<?= $escape($excerpt) ?>

		</p>

	<?php endif; ?>

	<div class="saved-item-footer">

		<div class="saved-item-date">

			<i class="fa-solid fa-bookmark"></i>

			<span>

				<?= $escape(
					$t('account.saved.card.saved')
				) ?>

				<?= \Monoverse\Helpers\DateHelper::timeAgo(
					(string) ($item['saved_at'] ?? ''),
					false,
					(string) ($currentLocale ?? 'it')
				) ?>

			</span>

		</div>

		<form
			method="post"
			action="<?= $escape($removeUrl) ?>"
			onsubmit="return confirm(<?= htmlspecialchars(
				json_encode(
					$removeConfirm,
					JSON_UNESCAPED_UNICODE
					| JSON_HEX_APOS
					| JSON_HEX_QUOT
				),
				ENT_QUOTES,
				'UTF-8'
			) ?>);"
		>

			<button
				type="submit"
				class="mv-link-danger"
			>
				<?= $escape(
					$t('account.saved.card.remove')
				) ?>
			</button>

		</form>

	</div>

</article>
