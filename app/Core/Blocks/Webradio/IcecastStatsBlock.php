<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Webradio;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\IcecastService;
use Monoverse\Services\Translator;

final class IcecastStatsBlock implements
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
		return 'icecast_stats';
	}

	public function label(): string
	{
		return 'Statistiche Icecast';
	}

	public function category(): string
	{
		return 'webradio';
	}

	public function icon(): string
	{
		return 'fa-chart-simple';
	}

	public function description(): string
	{
		return 'Mostra ascoltatori, picco, bitrate, formato audio e mount attivi.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'webradio/icecast-stats';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Statistiche radio',
			'status_url' => '',
			'mount' => '',
			'show_listeners' => true,
			'show_peak' => true,
			'show_bitrate' => true,
			'show_codec' => true,
			'show_mounts' => true,
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
					?? 'Statistiche radio'
				),
			],
			[
				'type' => 'url',
				'name' => 'status_url',
				'label' => 'URL server Icecast',
				'value' => (string) (
					$settings['status_url']
					?? ''
				),
				'placeholder' => 'https://radio.example.org:8000',
				'help' => 'URL base del server Icecast, ad esempio https://radio.example.org:8000.',
			],
			[
				'type' => 'text',
				'name' => 'mount',
				'label' => 'Mount',
				'value' => (string) (
					$settings['mount']
					?? ''
				),
				'help' => 'Opzionale. Esempio: /radio.mp3',
			],
			[
				'type' => 'checkbox',
				'name' => 'show_listeners',
				'label' => 'Mostra ascoltatori attuali',
				'checked' => (bool) (
					$settings['show_listeners']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_peak',
				'label' => 'Mostra picco ascoltatori',
				'checked' => (bool) (
					$settings['show_peak']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_bitrate',
				'label' => 'Mostra bitrate',
				'checked' => (bool) (
					$settings['show_bitrate']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_codec',
				'label' => 'Mostra formato audio',
				'checked' => (bool) (
					$settings['show_codec']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_mounts',
				'label' => 'Mostra mount attivi',
				'checked' => (bool) (
					$settings['show_mounts']
					?? true
				),
			],
		];
	}

	public function validateSettings(
		array &$settings
	): array {
		$errors = [];

		$baseUrl = rtrim(
			trim(
				(string) (
					$settings['status_url']
					?? ''
				)
			),
			'/'
		);

		if ($baseUrl !== '') {
			$scheme = strtolower(
				(string) parse_url(
					$baseUrl,
					PHP_URL_SCHEME
				)
			);

			$path = trim(
				(string) parse_url(
					$baseUrl,
					PHP_URL_PATH
				),
				'/'
			);

			if (
				filter_var(
					$baseUrl,
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
					'blocks.webradio.icecast_stats.admin.status_url_error'
				);
			}
		}

		$mount = trim(
			(string) (
				$settings['mount']
				?? ''
			)
		);

		if (
			$mount !== ''
			&& !str_starts_with($mount, '/')
		) {
			$mount = '/' . $mount;
		}

		$settings['status_url'] = $baseUrl;
		$settings['mount'] = $mount;

		return $errors;
	}

	public function stylesheets(): array
	{
		return [
			'widgets/azuracast-stats',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		$baseUrl = rtrim(
			trim(
				(string) (
					$settings['status_url']
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

		if (
			$mount !== ''
			&& !str_starts_with($mount, '/')
		) {
			$mount = '/' . $mount;
		}

		$status = $baseUrl !== ''
			? $this->icecast->getStatus($baseUrl)
			: null;

		$sources = $baseUrl !== ''
			? $this->icecast->getSources($baseUrl)
			: [];

		$source = null;

		if ($sources !== []) {
			if ($mount !== '') {
				$source = $this->icecast->getSource(
					$baseUrl,
					$mount
				);
			} elseif (count($sources) === 1) {
				$source = $sources[0];
			}
		}

		return [
			'title' => trim(
				(string) (
					$settings['title']
					?? 'Statistiche radio'
				)
			),
			'status_url' => $baseUrl,
			'mount' => $mount,
			'status' => $status ?? [],
			'sources' => $sources,
			'source' => $source ?? [],
			'show_listeners' => filter_var(
				$settings['show_listeners'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_peak' => filter_var(
				$settings['show_peak'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_bitrate' => filter_var(
				$settings['show_bitrate'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_codec' => filter_var(
				$settings['show_codec'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_mounts' => filter_var(
				$settings['show_mounts'] ?? true,
				FILTER_VALIDATE_BOOL
			),
		];
	}
}
