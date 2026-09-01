<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Webradio;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\IcecastService;
use Monoverse\Services\Translator;

final class IcecastBlock implements
	BlockInterface,
	ValidatesSettingsInterface
{
	public function __construct(
		private IcecastService $icecast,
		private Translator $translator
	) {
	}

	public function type(): string
	{
		return 'icecast';
	}

	public function label(): string
	{
		return 'Icecast';
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
		return 'Mostra il player di una stazione Icecast.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'webradio/icecast';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Ascolta la radio',
			'player_style' => 'modern',
			'station_url' => '',
			'mount' => '',
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
				'label' => 'URL server Icecast',
				'value' => (string) (
					$settings['station_url']
					?? ''
				),
				'placeholder' => 'https://radio.example.org:8000',
				'help' => 'URL base del server Icecast, ad esempio https://radio.example.org:8000.',
			],
			[
				'type' => 'text',
				'name' => 'mount',
				'label' => 'Mountpoint',
				'value' => (string) (
					$settings['mount']
					?? ''
				),
				'help' => 'Mountpoint dello stream, ad esempio /radio.',
			],
		];
	}

	public function validateSettings(
		array &$settings
	): array {
		$errors = [];

		$stationUrl = rtrim(
			trim(
				(string) (
					$settings['station_url']
					?? ''
				)
			),
			'/'
		);

		if ($stationUrl !== '') {
			$scheme = strtolower(
				(string) parse_url(
					$stationUrl,
					PHP_URL_SCHEME
				)
			);

			$path = trim(
				(string) parse_url(
					$stationUrl,
					PHP_URL_PATH
				),
				'/'
			);

			if (
				filter_var(
					$stationUrl,
					FILTER_VALIDATE_URL
				) === false
				|| !in_array(
					$scheme,
					['http', 'https'],
					true
				)
				|| $path !== ''
			) {
				$errors[] = $this->translator->translate(
					'blocks.webradio.icecast.admin.station_url_error'
				);
			}
		}

		$mount = trim(
			(string) (
				$settings['mount']
				?? ''
			)
		);

		if ($mount !== '' && !str_starts_with($mount, '/')) {
			$mount = '/' . $mount;
		}

		$settings['station_url'] = $stationUrl;
		$settings['mount'] = $mount;

		return $errors;
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
		$stationUrl = rtrim(
			trim(
				(string) (
					$settings['station_url']
					?? ''
				)
			),
			'/'
		);

		$mount = trim(
			(string) (
				$settings['mount']
				?? ''
			)
		);

		if ($mount !== '' && !str_starts_with($mount, '/')) {
			$mount = '/' . $mount;
		}

		$source = (
			$stationUrl !== ''
			&& $mount !== ''
		)
			? $this->icecast->getSource(
				$stationUrl,
				$mount
			)
			: null;

		return [
			'title' => trim(
				(string) (
					$context['block']['title']
					?? ''
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
			'mount' => $mount,
			'source' => $source,
		];
	}
}
