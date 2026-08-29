<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Developer;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\GitHubService;

class GitHubReleaseBlock implements
	BlockInterface,
	ValidatesSettingsInterface
{
	public function __construct(
		private GitHubService $github
	) {
	}

	public function type(): string
	{
		return 'github-release';
	}

	public function label(): string
	{
		return 'GitHub Release';
	}

	public function category(): string
	{
		return 'developer';
	}

	public function icon(): string
	{
		return 'fa-solid fa-tag';
	}

	public function description(): string
	{
		return 'Mostra in formato compatto l\'ultima release disponibile di un repository GitHub.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'developer/github-release';
	}

	public function defaultSettings(): array
	{
		return [
			'repository' => '',
			'custom_title' => '',
			'show_repository' => true,
			'show_date' => true,
		];
	}

	public function settingsForm(
		array $settings = []
	): array {
		return [
			[
				'type' => 'text',
				'name' => 'repository',
				'label' => 'Repository',
				'value' => (string) (
					$settings['repository']
					?? ''
				),
				'placeholder' => 'owner/repository',
				'help' => 'Puoi inserire sia owner/repository sia l\'URL completo del repository GitHub.',
			],
			[
				'type' => 'text',
				'name' => 'custom_title',
				'label' => 'Titolo personalizzato',
				'value' => (string) (
					$settings['custom_title']
					?? ''
				),
				'placeholder' => 'Ultima release',
			],
			[
				'type' => 'checkbox',
				'name' => 'show_repository',
				'label' => 'Mostra nome repository',
				'checked' => (bool) (
					$settings['show_repository']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_date',
				'label' => 'Mostra data pubblicazione',
				'checked' => (bool) (
					$settings['show_date']
					?? true
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/github-release',
		];
	}

	public function scripts(): array
	{
		return [];
	}

	public function validateSettings(
		array &$settings
	): array {
		$repository = $this->github->normalizeRepository(
			(string) (
				$settings['repository']
					?? ''
			)
		);

		if ($repository === '') {
			return [
				'repository' =>
					'Inserisci owner/repository oppure l\'URL completo GitHub.',
			];
		}

		$settings['repository'] = $repository;

		return [];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		$repository = trim(
			(string) (
				$settings['repository']
					?? ''
			)
		);

		$dashboard = $repository !== ''
			? $this->github->getRepositoryDashboard(
				$repository
			)
			: [];

		return [
			'title' => trim(
				(string) (
					$settings['custom_title']
						?? ''
				)
			),
			'repository_name' => $repository,
			'repository' => is_array(
				$dashboard['repository']
					?? null
			)
				? $dashboard['repository']
				: [],
			'release' => is_array(
				$dashboard['release']
					?? null
			)
				? $dashboard['release']
				: [],
			'show_repository' => filter_var(
				$settings['show_repository']
					?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_date' => filter_var(
				$settings['show_date']
					?? true,
				FILTER_VALIDATE_BOOL
			),
		];
	}
}
