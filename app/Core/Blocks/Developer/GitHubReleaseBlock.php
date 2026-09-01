<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Developer;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\GitHubService;
use Monoverse\Services\Translator;

class GitHubReleaseBlock implements
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
		return 'github-release';
	}

	public function label(): string
	{
		return $this->translator->translate(
			'blocks.developer.github_release.admin.label'
		);
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
		return $this->translator->translate(
			'blocks.developer.github_release.admin.description'
		);
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
				'label' => $this->translator->translate(
					'blocks.developer.github_release.admin.repository'
				),
				'value' => (string) (
					$settings['repository']
						?? ''
				),
				'placeholder' => 'owner/repository',
				'help' => $this->translator->translate(
					'blocks.developer.github_release.admin.repository_help'
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_repository',
				'label' => $this->translator->translate(
					'blocks.developer.github_release.admin.show_repository'
				),
				'checked' => (bool) (
					$settings['show_repository']
						?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_date',
				'label' => $this->translator->translate(
					'blocks.developer.github_release.admin.show_date'
				),
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
					$this->translator->translate(
						'blocks.developer.github_release.admin.repository_error'
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

		$dashboard = $repository !== ''
			? $this->github->getRepositoryDashboard(
				$repository
			)
			: [];

		return [
			'title' => trim(
				(string) (
					$context['block']['title']
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
			'releases' => is_array(
				$dashboard['releases']
					?? null
			)
				? $dashboard['releases']
				: [
					'stable' => [],
					'beta' => [],
					'nightly' => [],
				],
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
