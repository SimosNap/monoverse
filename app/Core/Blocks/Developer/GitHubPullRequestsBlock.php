<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Developer;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\GitHubService;

class GitHubPullRequestsBlock implements
	BlockInterface,
	ValidatesSettingsInterface
{
	public function __construct(
		private GitHubService $github
	) {
	}

	public function type(): string
	{
		return 'github-pull-requests';
	}

	public function label(): string
	{
		return 'GitHub Pull Requests';
	}

	public function category(): string
	{
		return 'developer';
	}

	public function icon(): string
	{
		return 'fa-solid fa-code-pull-request';
	}

	public function description(): string
	{
		return 'Mostra le pull request più recenti di un repository GitHub.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'developer/github-pull-requests';
	}

	public function defaultSettings(): array
	{
		return [
			'repository' => '',
			'custom_title' => '',
			'state' => 'open',
			'limit' => '5',
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
				'placeholder' => 'Pull Requests',
			],
			[
				'type' => 'select',
				'name' => 'state',
				'label' => 'Stato',
				'value' => (string) (
					$settings['state']
					?? 'open'
				),
				'options' => [
					'open' => 'Aperte',
					'closed' => 'Chiuse',
					'all' => 'Tutte',
				],
			],
			[
				'type' => 'select',
				'name' => 'limit',
				'label' => 'Numero di Pull Request',
				'value' => (string) (
					$settings['limit']
					?? '5'
				),
				'options' => [
					'3' => '3',
					'5' => '5',
					'10' => '10',
				],
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/github-pull-requests',
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

		$state = trim(
			(string) (
				$settings['state']
					?? 'open'
			)
		);

		$limit = (int) (
			$settings['limit']
				?? 5
		);

		return [
			'title' => trim(
				(string) (
					$settings['custom_title']
						?? ''
				)
			),
			'repository' => $repository,
			'state' => $state,
			'pullRequests' => $repository !== ''
				? $this->github->getPullRequests(
					$repository,
					$state,
					$limit
				)
				: [],
		];
	}
}
