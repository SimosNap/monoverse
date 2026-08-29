<?php
declare(strict_types=1);
?>

<section class="mv-dashboard">

	<header class="mv-hero">

		<h1>
			<?= htmlspecialchars(
				$t('admin.block_library.title'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</h1>

		<p>
			<?= htmlspecialchars(
				$t('admin.block_library.description'),
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</p>

	</header>

	<?php foreach ($categories as $category): ?>

		<div class="mv-card mv-block-library-category">

			<h2>
				<?= htmlspecialchars(
					$category['name'],
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</h2>

			<div class="mv-block-library-grid">

				<?php foreach ($category['blocks'] as $block): ?>

					<?php
					$blockTypeKey = str_replace(
						'-',
						'_',
						(string) $block['type']
					);

					$labelKey = 'admin.block_types.'
						. $blockTypeKey
						. '.label';

					$descriptionKey = 'admin.block_types.'
						. $blockTypeKey
						. '.description';

					$translatedLabel = $t($labelKey);
					$translatedDescription = $t($descriptionKey);

					$blockLabel = $translatedLabel !== $labelKey
						? $translatedLabel
						: (string) $block['label'];

					$blockDescription = $translatedDescription
						!== $descriptionKey
						? $translatedDescription
						: (string) $block['description'];
					?>

					<a
						class="mv-block-library-item"
						href="/admin/blocks/create?<?= http_build_query([
							'page' => $page,
							'area' => $area,
							'type' => $block['type'],
						]) ?>"
					>

						<i
							class="fa-solid <?= htmlspecialchars(
								$block['icon'],
								ENT_QUOTES,
								'UTF-8'
							) ?>"
						></i>

						<strong>
							<?= htmlspecialchars(
								$blockLabel,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</strong>

						<span>
							<?= htmlspecialchars(
								$blockDescription,
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					</a>

				<?php endforeach; ?>

			</div>

		</div>

	<?php endforeach; ?>

</section>
