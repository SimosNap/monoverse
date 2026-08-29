<?php
declare(strict_types=1);

$enabled = (bool) ($data['enabled'] ?? false);

if (!$enabled) {
	return;
}

$title = trim(
	(string) (
		$data['title']
		?? $t('blocks.content.submit_article.default_title')
	)
);

$description = trim(
	(string) ($data['description'] ?? '')
);

$buttonLabel = trim(
	(string) (
		$data['button_label']
		?? $t('blocks.content.submit_article.default_button')
	)
);

$url = (string) ($data['url'] ?? '/chanzine/submit');
?>

<section class="mv-submit-article-widget">

	<?php if ($title !== ''): ?>

		<h3 class="mv-submit-article-title">
			<?= htmlspecialchars(
				$title,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h3>

	<?php endif; ?>

	<?php if ($description !== ''): ?>

		<p class="mv-submit-article-description">
			<?= nl2br(
				htmlspecialchars(
					$description,
					ENT_QUOTES,
					'UTF-8'
				)
			) ?>
		</p>

	<?php endif; ?>

	<a
		href="<?= htmlspecialchars(
			$url,
			ENT_QUOTES,
			'UTF-8'
		) ?>"
		class="mv-submit-article-button"
	>
		<i
			class="fa-solid fa-pen-to-square"
			aria-hidden="true"
		></i>

		<?= htmlspecialchars(
			$buttonLabel,
			ENT_QUOTES,
			'UTF-8'
		) ?>
	</a>

</section>
