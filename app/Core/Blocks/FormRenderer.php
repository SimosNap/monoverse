<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

use Monoverse\Core\View;
use Monoverse\Services\Translator;

final class FormRenderer
{
	public function __construct(
		private View $view,
		private Translator $translator
	) {
	}

	public function render(
		BlockInterface $block,
		array $settings = [],
		bool $useTranslatedDefaults = false
	): string {

		$fields = $block->settingsForm(
			$settings
		);

		$typeKey = str_replace(
			'-',
			'_',
			$block->type()
		);

		foreach ($fields as &$field) {
			if (!is_array($field)) {
				continue;
			}

			$name = trim(
				(string) ($field['name'] ?? '')
			);

			if ($name === '') {
				continue;
			}

			$baseKey = 'admin.block_settings.'
				. $typeKey
				. '.'
				. $name;

			$field['label'] = $this->translatedValue(
				$baseKey . '.label',
				(string) ($field['label'] ?? '')
			);

			if (array_key_exists('help', $field)) {
				$field['help'] = $this->translatedValue(
					$baseKey . '.help',
					(string) $field['help']
				);
			}

			if (array_key_exists('placeholder', $field)) {
				$field['placeholder'] = $this->translatedValue(
					$baseKey . '.placeholder',
					(string) $field['placeholder']
				);
			}

			if (
				isset($field['options'])
				&& is_array($field['options'])
			) {
				foreach ($field['options'] as $value => $label) {
					$optionKey = $baseKey
						. '.options.'
						. (string) $value;

					$field['options'][$value]
						= $this->translatedValue(
							$optionKey,
							(string) $label
						);
				}
			}

			if (
				$useTranslatedDefaults
				&& array_key_exists('value', $field)
				&& is_string($field['value'])
			) {
				$defaultKey = $baseKey . '.default';

				$translatedDefault = $this->translator->translate(
					$defaultKey
				);

				if ($translatedDefault !== $defaultKey) {
					$field['value'] = $translatedDefault;
				}
			}
		}

		unset($field);

		return $this->view->partial(
			'editor/form',
			[
				'fields' => $fields,
			]
		);
	}

	private function translatedValue(
		string $key,
		string $fallback
	): string {
		$translated = $this->translator->translate(
			$key
		);

		return $translated !== $key
			? $translated
			: $fallback;
	}
}
