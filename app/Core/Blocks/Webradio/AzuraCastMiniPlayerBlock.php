<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Webradio;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\AzuraCastService;

final class AzuraCastMiniPlayerBlock implements BlockInterface
{
	public function __construct(
		private AzuraCastService $azuraCast
	) {
	}

	public function type(): string
	{
		return 'azuracast-mini-player';
	}

	public function label(): string
	{
		return 'Miniplayer AzuraCast';
	}

	public function category(): string
	{
		return 'webradio';
	}

	public function icon(): string
	{
		return 'fa-headphones';
	}

	public function description(): string
	{
		return 'Mostra un player radio compatto apribile anche in una finestra indipendente.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'webradio/azuracast-mini-player';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Ascolta la radio',
			'now_playing_url' => '',
			'stream_url' => '',
			'show_cover' => true,
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
					?? 'Ascolta la radio'
				),
			],
			[
				'type' => 'url',
				'name' => 'now_playing_url',
				'label' => 'URL API Now Playing',
				'value' => (string) (
					$settings['now_playing_url']
					?? ''
				),
			],
			[
				'type' => 'url',
				'name' => 'stream_url',
				'label' => 'URL stream audio',
				'value' => (string) (
					$settings['stream_url']
					?? ''
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_cover',
				'label' => 'Mostra copertina',
				'checked' => (bool) (
					$settings['show_cover']
					?? true
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/azuracast-mini-player',
		];
	}

	public function scripts(): array
	{
		return [
			'widgets/azuracast-mini-player',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		$nowPlayingUrl = trim(
			(string) (
				$settings['now_playing_url']
				?? ''
			)
		);

		$streamUrl = trim(
			(string) (
				$settings['stream_url']
				?? ''
			)
		);

		$nowPlaying = $nowPlayingUrl !== ''
			? $this->azuraCast->getNowPlaying($nowPlayingUrl)
			: [];

		return [
			'title' => trim(
				(string) (
					$settings['title']
						?? 'Ascolta la radio'
				)
			),
			'now_playing_url' => $nowPlayingUrl,
			'stream_url' => $streamUrl,
			'now_playing' => $nowPlaying,
			'show_cover' => filter_var(
				$settings['show_cover'] ?? true,
				FILTER_VALIDATE_BOOL
			),
		];
	}
}
