<?php
declare(strict_types=1);

namespace Monoverse\Services;

class MentionService
{
	public function __construct(
		private ProfileService $profiles,
		private NotificationService $notifications
	) {
	}

	public function notifyMentions(
		string $text,
		string $actorSub,
		string $objectType,
		string $objectUuid
	): void {
		$usernames = $this->extractUsernames($text);

		foreach ($usernames as $username) {
			$profile = $this->profiles->findByUsername($username);

			if (!$profile) {
				continue;
			}

			$recipientSub = (string) ($profile['oauth_sub'] ?? '');

			if ($recipientSub === '') {
				continue;
			}

			$this->notifications->createMentionNotification(
				$recipientSub,
				$actorSub,
				$objectType,
				$objectUuid
			);
		}
	}

	/**
	 * @return string[]
	 */
	private function extractUsernames(string $text): array
	{
		preg_match_all(
			'/(?<!\w)@([A-Za-z0-9_\-]{2,32})/u',
			$text,
			$matches
		);

		if (empty($matches[1])) {
			return [];
		}

		return array_values(
			array_unique($matches[1])
		);
	}
}