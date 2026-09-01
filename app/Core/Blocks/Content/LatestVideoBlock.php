<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\PostService;

final class LatestVideoBlock implements BlockInterface
{
	public function __construct(
		private PostService $posts
	) {
	}

	public function type(): string
	{
		return 'latest-video';
	}

	public function label(): string
	{
		return 'Ultimi Video Condivisi';
	}

	public function category(): string
	{
		return 'content';
	}

	public function icon(): string
	{
		return 'fa-video';
	}

	public function description(): string
	{
		return 'Mostra gli ultimi video condivisi nei Ping.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'content/latest-video';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Ultimi Video Condivisi',
			'limit' => 5,
			'show_author' => true,
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
					?? 'Ultimi Video Condivisi'
				),
			],
			[
				'type' => 'number',
				'name' => 'limit',
				'label' => 'Numero video',
				'min' => 1,
				'max' => 20,
				'value' => (int) (
					$settings['limit']
					?? 5
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_author',
				'label' => 'Mostra autore',
				'checked' => (bool) (
					$settings['show_author']
					?? true
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/latest-video',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		$limit = max(
			1,
			min(
				20,
				(int) ($settings['limit'] ?? 5)
			)
		);

		return [
			'title' => trim(
				(string) (
					$context['block']['title']
					?? ''
				)
			),
			'show_author' => filter_var(
				$settings['show_author'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'posts' => $this->posts->listPublishedByMediaType(
				'video',
				$limit,
				0,
				null
			),
		];
	}
}
