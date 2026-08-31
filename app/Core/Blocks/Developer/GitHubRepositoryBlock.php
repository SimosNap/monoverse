<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Developer;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\GitHubService;
use Monoverse\Services\Translator;

class GitHubRepositoryBlock implements
BlockInterface,
ValidatesSettingsInterface
{
	public function __construct(
		private GitHubService $github,
		private Translator $translator
	) {
	}

	public function type(): string
	{
		return 'github-repository';
	}

	public function label(): string
	{
		return $this->translator->translate(
			'blocks.developer.github_repository.admin.label'
		);
	}

	public function category(): string
	{
		return 'developer';
	}

	public function icon(): string
	{
		return 'fa-brands fa-github';
	}

	public function description(): string
	{
		return $this->translator->translate(
			'blocks.developer.github_repository.admin.description'
		);
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'developer/github-repository';
	}

	public function defaultSettings(): array
	{
		return [
			'repository' => '',
			'branch' => '',
			'title' => '',
			'show_release' => true,
			'show_languages' => true,
			'show_commits' => true,
			'show_pull_requests' => true,
			'show_issues' => true,
			'commit_limit' => 10,
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
				'name' => 'branch',
				'label' => 'Branch',
				'value' => (string) (
					$settings['branch']
					?? ''
				),
				'placeholder' => 'Lascia vuoto per usare il branch predefinito',
			],
			[
				'type' => 'text',
				'name' => 'title',
				'label' => 'Titolo personalizzato',
				'value' => (string) (
					$settings['title']
					?? ''
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_release',
				'label' => 'Mostra ultima release',
				'checked' => (bool) (
					$settings['show_release']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_languages',
				'label' => 'Mostra linguaggi',
				'checked' => (bool) (
					$settings['show_languages']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_commits',
				'label' => 'Mostra ultime commit',
				'checked' => (bool) (
					$settings['show_commits']
					?? true
				),
			],
			[
				'type' => 'number',
				'name' => 'commit_limit',
				'label' => 'Numero commit',
				'value' => (int) (
					$settings['commit_limit']
					?? 10
				),
				'min' => 1,
				'max' => 10,
			],
			[
				'type' => 'checkbox',
				'name' => 'show_pull_requests',
				'label' => 'Mostra pull request aperte',
				'checked' => (bool) (
					$settings['show_pull_requests']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_issues',
				'label' => 'Mostra issue aperte',
				'checked' => (bool) (
					$settings['show_issues']
					?? true
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/github-repository',
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
					$this->translator->translate(
						'blocks.developer.github_repository.admin.repository_error'
					),
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
		
		$defaultBranch = trim(
			(string) (
				$settings['branch']
					?? ''
			)
		);
		
		$branchParameter = $repository !== ''
			? 'github_branch_' . substr(
				hash(
					'sha256',
					strtolower($repository)
				),
				0,
				12
			)
			: 'github_branch';
		
		$branch = trim(
			(string) (
				$_GET[$branchParameter]
					?? $defaultBranch
			)
		);

		$dashboard = $repository !== ''
			? $this->github->getRepositoryDashboard(
				$repository,
				$branch !== '' ? $branch : null
			)
			: [];

		$commitLimit = (int) (
			$settings['commit_limit']
				?? 10
		);

		if ($commitLimit < 1) {
			$commitLimit = 1;
		}

		if ($commitLimit > 10) {
			$commitLimit = 10;
		}

		if (
			isset($dashboard['commits'])
			&& is_array($dashboard['commits'])
		) {
			$dashboard['commits'] = array_slice(
				$dashboard['commits'],
				0,
				$commitLimit
			);
		}

		return [
			'title' => trim(
				(string) (
					$settings['title']
						?? ''
				)
			),
			'repository_name' => $repository,
			'dashboard' => $dashboard,
			'show_release' => filter_var(
				$settings['show_release'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_languages' => filter_var(
				$settings['show_languages'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_commits' => filter_var(
				$settings['show_commits'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_pull_requests' => filter_var(
				$settings['show_pull_requests'] ?? true,
				FILTER_VALIDATE_BOOL
			),
			'show_issues' => filter_var(
				$settings['show_issues'] ?? true,
				FILTER_VALIDATE_BOOL
			),
		];
	}
}
