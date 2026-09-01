<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\PostService;

final class LatestAudioBlock implements BlockInterface
{
	public function __construct(
		private PostService $posts
	) {
	}

	public function type(): string
	{
		return 'latest-audio';
	}

	public function label(): string
	{
		return 'Ultimi Audio Condivisi';
	}

	public function category(): string
	{
		return 'content';
	}

	public function icon(): string
	{
		return 'fa-music';
	}

	public function description(): string
	{
		return 'Mostra gli ultimi audio condivisi nei Ping.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'content/latest-audio';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Ultimi Audio Condivisi',
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
					?? 'Ultimi Audio Condivisi'
				),
			],
			[
				'type' => 'number',
				'name' => 'limit',
				'label' => 'Numero audio',
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
			'widgets/latest-audio',
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
				'audio',
				$limit,
				0,
				null
			),
		];
	}
}
