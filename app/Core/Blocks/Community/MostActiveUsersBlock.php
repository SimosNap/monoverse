<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Community;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Services\CommunityService;

final class MostActiveUsersBlock implements BlockInterface
{
	public function __construct(
		private CommunityService $community
	) {
	}

	public function type(): string
	{
		return 'most-active-users';
	}

	public function label(): string
	{
		return 'Utenti più attivi';
	}

	public function category(): string
	{
		return 'community';
	}

	public function icon(): string
	{
		return 'fa-ranking-star';
	}

	public function description(): string
	{
		return 'Mostra gli utenti più attivi combinando attività nella community e nella chat IRC.';
	}

	public function configurable(): bool
	{
		return true;
	}

	public function template(): string
	{
		return 'community/most-active-users';
	}

	public function defaultSettings(): array
	{
		return [
			'title' => 'Utenti più attivi',
			'limit' => 5,
			'show_avatar' => true,
			'show_stats' => true,
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
					?? 'Utenti più attivi'
				),
			],
			[
				'type' => 'number',
				'name' => 'limit',
				'label' => 'Numero di utenti',
				'min' => 1,
				'max' => 20,
				'value' => (int) (
					$settings['limit']
					?? 5
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_avatar',
				'label' => 'Mostra gli avatar',
				'checked' => (bool) (
					$settings['show_avatar']
					?? true
				),
			],
			[
				'type' => 'checkbox',
				'name' => 'show_stats',
				'label' => 'Mostra le statistiche di attività',
				'checked' => (bool) (
					$settings['show_stats']
					?? true
				),
			],
		];
	}

	public function stylesheets(): array
	{
		return [
			'widgets/most-active-users',
		];
	}

	public function data(
		array $settings = [],
		array $context = []
	): array {
		$limit = max(
			1,
			min(
				20,
				(int) ($settings['limit'] ?? 5)
			)
		);

		return [
			'title' => (string) (
				$settings['title']
				?? 'Utenti più attivi'
			),
			'limit' => $limit,
			'show_avatar' => $this->booleanSetting(
				$settings,
				'show_avatar',
				true
			),
			'show_stats' => $this->booleanSetting(
				$settings,
				'show_stats',
				true
			),
			'users' => $this->community->mostActiveUsers(
				$limit
			),
		];
	}

	private function booleanSetting(
		array $settings,
		string $name,
		bool $default
	): bool {
		if (!array_key_exists($name, $settings)) {
			return $default;
		}

		return filter_var(
			$settings[$name],
			FILTER_VALIDATE_BOOL
		);
	}
}
