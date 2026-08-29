<?php
declare(strict_types=1);

/** @var array $fields */

$fields = is_array($fields ?? null)
	? $fields
	: [];
?>

<?php foreach ($fields as $field): ?>

	<?php
	$type = trim(
		(string) ($field['type'] ?? 'text')
	);

	$name = trim(
		(string) ($field['name'] ?? '')
	);

	$label = trim(
		(string) ($field['label'] ?? '')
	);

	$value = $field['value'] ?? '';

	if ($name === '') {
		continue;
	}

	$fieldId = 'widget-field-' . preg_replace(
		'/[^a-zA-Z0-9_-]+/',
		'-',
		$name
	);
	?>

	<?php if ($type === 'textarea'): ?>

		<div class="mv-admin-field">

			<label for="<?= htmlspecialchars(
				$fieldId,
				ENT_QUOTES,
				'UTF-8'
			) ?>">
				<?= htmlspecialchars(
					$label,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</label>

			<textarea
				id="<?= htmlspecialchars(
					$fieldId,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				name="<?= htmlspecialchars(
					$name,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				rows="<?= max(
					3,
					(int) ($field['rows'] ?? 12)
				) ?>"
				class="mv-widget-code-field"
			><?= htmlspecialchars(
				(string) $value,
				ENT_QUOTES,
				'UTF-8'
			) ?></textarea>

		</div>

	<?php elseif ($type === 'number'): ?>

		<div class="mv-admin-field">

			<label for="<?= htmlspecialchars(
				$fieldId,
				ENT_QUOTES,
				'UTF-8'
			) ?>">
				<?= htmlspecialchars(
					$label,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</label>

			<input
				id="<?= htmlspecialchars(
					$fieldId,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				type="number"
				name="<?= htmlspecialchars(
					$name,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				value="<?= htmlspecialchars(
					(string) $value,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				<?php if (isset($field['min'])): ?>
					min="<?= (int) $field['min'] ?>"
				<?php endif; ?>
				<?php if (isset($field['max'])): ?>
					max="<?= (int) $field['max'] ?>"
				<?php endif; ?>
				<?php if (isset($field['step'])): ?>
					step="<?= htmlspecialchars(
						(string) $field['step'],
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				<?php endif; ?>
			>

		</div>
		
	<?php elseif ($type === 'select'): ?>
	
	<div class="mv-admin-field">
	
		<label for="<?= htmlspecialchars(
			$fieldId,
			ENT_QUOTES,
			'UTF-8'
		) ?>">
			<?= htmlspecialchars(
				$label,
				ENT_QUOTES,
				'UTF-8'
			) ?>
		</label>
	
		<select
			id="<?= htmlspecialchars(
				$fieldId,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
			name="<?= htmlspecialchars(
				$name,
				ENT_QUOTES,
				'UTF-8'
			) ?>"
		>
	
			<?php foreach (
				(array) ($field['options'] ?? [])
				as $optionValue => $optionLabel
			): ?>
	
				<option
					value="<?= htmlspecialchars(
						(string) $optionValue,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					<?= (string) $value === (string) $optionValue
						? 'selected'
						: '' ?>
				>
					<?= htmlspecialchars(
						(string) $optionLabel,
						ENT_QUOTES,
						'UTF-8'
					) ?>
				</option>
	
			<?php endforeach; ?>
	
		</select>
	
		<?php if (!empty($field['help'])): ?>
	
			<p class="mv-admin-field-help">
				<?= htmlspecialchars(
					(string) $field['help'],
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</p>
	
		<?php endif; ?>
	
	</div>

	<?php elseif ($type === 'checkbox'): ?>

		<div class="mv-admin-field">

			<label class="mv-admin-switch">

				<input
					type="checkbox"
					name="<?= htmlspecialchars(
						$name,
						ENT_QUOTES,
						'UTF-8'
					) ?>"
					value="1"
					<?= !empty($field['checked'])
						? 'checked'
						: '' ?>
				>

				<span
					class="mv-admin-switch-slider"
					aria-hidden="true"
				></span>

				<span class="mv-admin-switch-text">

					<strong>
						<?= htmlspecialchars(
							$label,
							ENT_QUOTES,
							'UTF-8'
						) ?>
					</strong>

					<?php if (!empty($field['help'])): ?>

						<span>
							<?= htmlspecialchars(
								(string) $field['help'],
								ENT_QUOTES,
								'UTF-8'
							) ?>
						</span>

					<?php endif; ?>

				</span>

			</label>

		</div>

	<?php else: ?>

		<div class="mv-admin-field">

			<label for="<?= htmlspecialchars(
				$fieldId,
				ENT_QUOTES,
				'UTF-8'
			) ?>">
				<?= htmlspecialchars(
					$label,
					ENT_QUOTES,
					'UTF-8'
				) ?>
			</label>

			<input
				id="<?= htmlspecialchars(
					$fieldId,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				type="text"
				name="<?= htmlspecialchars(
					$name,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				value="<?= htmlspecialchars(
					(string) $value,
					ENT_QUOTES,
					'UTF-8'
				) ?>"
				<?php if (!empty($field['placeholder'])): ?>
					placeholder="<?= htmlspecialchars(
						(string) $field['placeholder'],
						ENT_QUOTES,
						'UTF-8'
					) ?>"
				<?php endif; ?>
			>

		</div>

	<?php endif; ?>

<?php endforeach; ?>
