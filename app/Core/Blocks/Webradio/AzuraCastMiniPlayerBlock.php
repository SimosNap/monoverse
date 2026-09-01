<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Webradio;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\AzuraCastService;
use Monoverse\Services\Translator;

final class AzuraCastMiniPlayerBlock implements
	BlockInterface,
	ValidatesSettingsInterface
{
	public function __construct(
		private AzuraCastService $azuraCast,
		private Translator $translator
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
				'placeholder' =>
					'https://radio.example.org/api/nowplaying/1',
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

	public function validateSettings(
		array &$settings
	): array {
		$nowPlayingUrl = rtrim(
			trim(
				(string) (
					$settings['now_playing_url']
						?? ''
				)
			),
			'/'
		);

		if (
			$nowPlayingUrl !== ''
			&& (
				filter_var(
					$nowPlayingUrl,
					FILTER_VALIDATE_URL
				) === false
				|| preg_match(
					'#/api/nowplaying/[^/]+$#',
					(string) parse_url(
						$nowPlayingUrl,
						PHP_URL_PATH
					)
				) !== 1
			)
		) {
			return [
				'now_playing_url' =>
					$this->translator->translate(
						'blocks.webradio.azuracast_mini_player.admin.now_playing_url_error'
					),
			];
		}

		$settings['now_playing_url'] =
			$nowPlayingUrl;

		return [];
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

		$nowPlaying = $nowPlayingUrl !== ''
			? $this->azuraCast->getNowPlaying(
				$nowPlayingUrl
			)
			: [];

		return [
			'title' => trim(
				(string) (
					$context['block']['title']
						?? ''
				)
			),
			'now_playing_url' => $nowPlayingUrl,
			'now_playing' => $nowPlaying,
			'show_cover' => filter_var(
				$settings['show_cover']
					?? true,
				FILTER_VALIDATE_BOOL
			),
		];
	}
}
