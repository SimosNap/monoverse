<header class="mv-hero">

	<?php if (!empty($avatar)): ?>

		<img
			class="mv-hero-avatar"
			src="<?= htmlspecialchars((string) $avatar, ENT_QUOTES, 'UTF-8') ?>"
			alt="">

	<?php endif; ?>

	<?php if (!empty($kicker)): ?>

		<p class="mv-kicker">
			<?= htmlspecialchars((string) $kicker, ENT_QUOTES, 'UTF-8') ?>
		</p>

	<?php endif; ?>

	<h1>
		<?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?>
	</h1>

	<?php if (!empty($description)): ?>

		<p class="mv-hero-description">
			<?= htmlspecialchars((string) $description, ENT_QUOTES, 'UTF-8') ?>
		</p>

	<?php endif; ?>

</header>
