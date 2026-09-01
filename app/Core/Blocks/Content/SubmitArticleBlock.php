<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Content;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\SettingsService;

class SubmitArticleBlock implements BlockInterface
{
	public function __construct(
		private SettingsService $settings
	) {
	}

	public function type(): string
	{
		return 'submit-article';
	}

	public function label(): string
	{
		return 'Proponi un articolo';
	}

	public function category(): string
	{
		return 'content';
	}

	public function icon(): string
	{
		return 'fa-solid fa-pen-to-square';
	}

	public function description(): string
	{
		return 'Invita gli utenti a proporre un articolo alla Chanzine.';
	}
	
	public function configurable(): bool
	{
		return true;
	}
	
	public function template(): string
	{
		return 'content/submit-article';
	}
	
	public function stylesheets(): array
	{
		return [
			'widgets/submit-article',
		];
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Proponi un articolo',
			'description' => 'Hai qualcosa da raccontare alla community? Invia la tua proposta alla redazione.',
			'button_label' => 'Proponi un articolo',
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
					?? 'Proponi un articolo'
				),
			],
			[
				'type' => 'textarea',
				'name' => 'description',
				'label' => 'Descrizione',
				'value' => (string) (
					$settings['description']
					?? 'Hai qualcosa da raccontare? Condividi la tua idea con la community.'
				),
			],
			[
				'type' => 'text',
				'name' => 'button_label',
				'label' => 'Testo pulsante',
				'value' => (string) (
					$settings['button_label']
					?? 'Invia la tua proposta'
				),
			],
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array
	{
		return [
			'enabled' => $this->settings->get(
				'chanzine_user_submissions_enabled',
				'0'
			) === '1',
			'title' => trim(
				(string) (
					$context['block']['title']
					?? ''
				)
			),
			'description' => trim(
				(string) (
					$settings['description']
					?? 'Hai qualcosa da raccontare alla community? Invia la tua proposta alla redazione.'
				)
			),
			'button_label' => trim(
				(string) (
					$settings['button_label']
					?? 'Proponi un articolo'
				)
			),
			'url' => '/chanzine/submit',
		];
	}
}
