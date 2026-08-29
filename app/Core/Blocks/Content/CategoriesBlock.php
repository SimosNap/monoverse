<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\CategoryService;

final class CategoriesBlock implements BlockInterface
{
	public function __construct(
		private CategoryService $categories
	) {
	}

	public function type(): string
	{
		return 'categories';
	}

	public function label(): string
	{
		return 'Categorie Chanzine';
	}

	public function category(): string
	{
		return 'content';
	}

	public function icon(): string
	{
		return 'fa-folder-tree';
	}

	public function description(): string
	{
		return 'Mostra le categorie della Chanzine con il numero di articoli pubblicati.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'content/categories';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Categorie',
			'show_count' => true,
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
					?? 'Categorie'
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_count',
				'label' => 'Mostra numero articoli',
				'checked' => (bool) (
					$settings['show_count']
					?? true
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/categories',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		
		$currentCategorySlug = trim(
			(string) (
				$context['currentCategorySlug']
				?? ''
			)
		);
		
		return [
			'title' => trim(
				(string) (
					$settings['title']
					?? 'Categorie'
				)
			),
			'show_count' => filter_var(
				$settings['show_count'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'currentCategorySlug' => $currentCategorySlug,
			'categories' => $this->categories
				->listWithPublishedArticleCount(
					'chanzine'
				),
		];
	}
}
