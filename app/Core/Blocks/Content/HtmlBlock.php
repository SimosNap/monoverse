<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;

final class HtmlBlock implements BlockInterface
{
	public function type(): string
	{
		return 'html';
	}

	public function label(): string
	{
		return 'HTML personalizzato';
	}

	public function category(): string
	{
		return 'content';
	}

	public function icon(): string
	{
		return 'fa-code';
	}

	public function description(): string
	{
		return 'Visualizza codice HTML personalizzato.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'content/html';
	}

	public function defaultSettings(): array
	{
		return [
			'html' => '',
		];
	}

	public function settingsForm(
		array $settings = []
	): array
	{
		return [
			[
				'type' => 'textarea',
				'name' => 'html',
				'label' => 'Codice HTML',
				'rows' => 12,
				'value' => (string) (
					$settings['html'] ?? ''
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/html',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		return [
			'html' => (string) ($settings['html'] ?? ''),
		];
	}
}