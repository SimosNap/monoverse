<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Webradio;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\AzuraCastService;
use Monoverse\Services\Translator;

final class AzuraCastRequestsBlock implements
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
		return 'azuracast_requests';
	}

	public function label(): string
	{
		return 'Richieste AzuraCast';
	}

	public function category(): string
	{
		return 'webradio';
	}

	public function icon(): string
	{
		return 'fa-music';
	}

	public function description(): string
	{
		return 'Permette agli ascoltatori di cercare e richiedere brani tramite AzuraCast.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'webradio/azuracast-requests';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Richiedi un brano',
			'requests_url' => '',
			'unavailable_behavior' => 'message',
			'unavailable_message' => 'Le richieste musicali non sono disponibili in questo momento.',
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
					?? 'Richiedi un brano'
				),
			],
			[
				'type' => 'url',
				'name' => 'requests_url',
				'label' => 'URL richieste AzuraCast',
				'value' => (string) (
					$settings['requests_url']
					?? ''
				),
				'placeholder' =>
					'https://radio.example.org/api/station/1/requests',
				'help' =>
					'Esempio: https://radio.example.org/api/station/1/requests',
			],
			[
				'type' => 'select',
				'name' => 'unavailable_behavior',
				'label' => 'Quando le richieste non sono disponibili',
				'value' => (string) (
					$settings['unavailable_behavior']
					?? 'message'
				),
				'options' => [
					'message' => 'Mostra un messaggio',
					'hide' => 'Nascondi il blocco',
				],
			],
			[
				'type' => 'text',
				'name' => 'unavailable_message',
				'label' => 'Messaggio richieste non disponibili',
				'value' => (string) (
					$settings['unavailable_message']
					?? 'Le richieste musicali non sono disponibili in questo momento.'
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/azuracast-requests',
		];
	}

	public function scripts(): array
	{
		return [
			'widgets/azuracast-requests',
		];
	}

	public function validateSettings(
		array &$settings
	): array {
		$requestsUrl = rtrim(
			trim(
				(string) (
					$settings['requests_url']
						?? ''
				)
			),
			'/'
		);

		if (
			$requestsUrl !== ''
			&& (
				filter_var(
					$requestsUrl,
					FILTER_VALIDATE_URL
				) === false
				|| preg_match(
					'#/api/station/[^/]+/requests$#',
					(string) parse_url(
						$requestsUrl,
						PHP_URL_PATH
					)
				) !== 1
			)
		) {
			return [
				'requests_url' =>
					$this->translator->translate(
						'blocks.webradio.azuracast_requests.admin.requests_url_error'
					),
			];
		}

		$settings['requests_url'] = $requestsUrl;

		return [];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		$requestsUrl = rtrim(
			trim(
				(string) (
					$settings['requests_url']
						?? ''
				)
			),
			'/'
		);

		$catalog = $requestsUrl !== ''
			? $this->azuraCast->getRequestCatalog(
				$requestsUrl
			)
			: [
				'available' => false,
				'items' => [],
				'message' =>
					'Endpoint delle richieste non configurato.',
			];

		$unavailableBehavior = (string) (
			$settings['unavailable_behavior']
			?? 'message'
		);

		if (!in_array(
			$unavailableBehavior,
			['message', 'hide'],
			true
		)) {
			$unavailableBehavior = 'message';
		}

		return [
			'title' => trim(
				(string) (
					$context['block']['title']
					?? ''
				)
			),
			'requests_url' => $requestsUrl,
			'requests_available' => (bool) (
				$catalog['available']
				?? false
			),
			'request_items' => is_array(
				$catalog['items']
					?? null
			)
				? $catalog['items']
				: [],
			'requests_message' => trim(
				(string) (
					$catalog['message']
					?? ''
				)
			),
			'unavailable_behavior' => $unavailableBehavior,
			'unavailable_message' => trim(
				(string) (
					$settings['unavailable_message']
					?? 'Le richieste musicali non sono disponibili in questo momento.'
				)
			),
		];
	}
}
