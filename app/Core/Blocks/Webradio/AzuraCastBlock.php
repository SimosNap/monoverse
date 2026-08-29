<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Webradio;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\AzuraCastService;

final class AzuraCastBlock implements BlockInterface
{
	public function __construct(
		private AzuraCastService $azuraCast
	) {
	}
	
	public function type(): string
	{
		return 'azuracast';
	}

	public function label(): string
	{
		return 'AzuraCast';
	}

	public function category(): string
	{
		return 'webradio';
	}

	public function icon(): string
	{
		return 'fa-radio';
	}

	public function description(): string
	{
		return 'Mostra il player AzuraCast e lo storico degli ultimi brani trasmessi.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'webradio/azuracast';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Ascolta la radio',
			'player_style' => 'modern',
			'station_url' => '',
			'history_limit' => 5,
			'show_history' => true,
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
				'type' => 'select',
				'name' => 'player_style',
				'label' => 'Stile player',
				'value' => (string) (
					$settings['player_style']
					?? 'modern'
				),
				'options' => [
					'modern' => 'Modern',
					'led' => 'LED',
					'analog' => 'Analog',
					'minimal' => 'Minimal',
				],
				'help' => 'Scegli l’aspetto del player mostrato nel sito.',
			],
			[
				'type' => 'url',
				'name' => 'station_url',
				'label' => 'URL stazione AzuraCast',
				'value' => (string) (
					$settings['station_url']
					?? ''
				),
			],
			[
				'type' => 'number',
				'name' => 'history_limit',
				'label' => 'Numero brani nello storico',
				'min' => 5,
				'max' => 10,
				'value' => (int) (
					$settings['history_limit']
					?? 5
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_history',
				'label' => 'Mostra storico brani',
				'checked' => (bool) (
					$settings['show_history']
					?? true
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/azuracast',
		];
	}
	
	public function scripts(): array
	{
		return [
			'widgets/azuracast',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		$historyLimit = (int) (
			$settings['history_limit']
			?? 5
		);
		
		$stationUrl = rtrim(
			trim(
				(string) (
					$settings['station_url']
					?? ''
				)
			),
			'/'
		);
		
		$nowPlaying = $stationUrl !== ''
			? $this->azuraCast->getNowPlaying($stationUrl)
			: [];

		if (!in_array($historyLimit, [5, 10], true)) {
			$historyLimit = 5;
		}

		return [
			'title' => trim(
				(string) (
					$settings['title']
					?? 'Ascolta la radio'
				)
			),
			'player_style' => in_array(
				(string) ($settings['player_style'] ?? 'modern'),
				['modern', 'led', 'analog', 'minimal'],
				true
			)
				? (string) ($settings['player_style'] ?? 'modern')
				: 'modern',
			'station_url' => $stationUrl,
			'now_playing' => $nowPlaying,
			'history_limit' => $historyLimit,
			'show_history' => filter_var(
				$settings['show_history'] ?? true,
				FILTER_VALIDATE_BOOL
			),
		];
	}
}