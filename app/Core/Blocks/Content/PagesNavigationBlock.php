<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\PageService;

final class PagesNavigationBlock implements BlockInterface
{
	public function __construct(
		private PageService $pages
	) {
	}

	public function type(): string
	{
		return 'pages-navigation';
	}

	public function label(): string
	{
		return 'Navigazione pagine';
	}

	public function category(): string
	{
		return 'content';
	}

	public function icon(): string
	{
		return 'fa-list';
	}

	public function description(): string
	{
		return 'Mostra le pagine dinamiche pubblicate in un menu di navigazione.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'content/pages-navigation';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Pagine',
			'navigation_group' => 'default',
		];
	}

	public function settingsForm(
		array $settings = []
	): array {
		return [
			[
				'type' => 'text',
				'name' => 'title',
				'label' => 'Titolo',
				'value' => (string) (
					$settings['title']
					?? 'Pagine'
				),
			],
			[
				'type' => 'text',
				'name' => 'navigation_group',
				'label' => 'Gruppo',
				'value' => (string) (
					$settings['navigation_group']
					?? 'default'
				),
				'placeholder' => 'default',
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/pages-navigation',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {

		$currentPageSlug = trim(
			(string) (
				$context['pageSlug']
				?? ''
			)
		);

		$navigationGroup = trim(
			(string) (
				$settings['navigation_group']
					?? 'default'
			)
		);

		if ($navigationGroup === '') {
			$navigationGroup = 'default';
		}

		return [
			'title' => trim(
				(string) (
					$context['block']['title']
						?? ''
				)
			),
			'currentPageSlug' => $currentPageSlug,
			'pages' => $this->pages
				->navigationItems(
					$navigationGroup
				),
		];
	}
}
