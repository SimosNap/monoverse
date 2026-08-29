<?php
declare(strict_types=1);

namespace Monoverse\Services;

use InvalidArgumentException;
use Monoverse\Core\Database;

class VoteService
{
	public function __construct(
		private Database $database,
		private PostService $posts,
		private NotificationService $notifications
	) {
	}

	public function vote(
		int $postId,
		string $authorSub,
		int $vote
	): void
	{
		if (!in_array($vote, [-1, 1], true)) {
			throw new InvalidArgumentException(
				'Il voto deve essere -1 oppure 1.'
			);
		}

		$currentVote = $this->getUserVote(
			$postId,
			$authorSub
		);

		if ($currentVote === 0) {
			$this->insertVote(
				$postId,
				$authorSub,
				$vote
			);

			if ($vote === 1) {
				$this->createUpvoteNotification(
					$postId,
					$authorSub
				);
			}

			return;
		}

		if ($currentVote === $vote) {
			$this->deleteVote(
				$postId,
				$authorSub
			);

			return;
		}

		$this->updateVote(
			$postId,
			$authorSub,
			$vote
		);

		if ($vote === 1) {
			$this->createUpvoteNotification(
				$postId,
				$authorSub
			);
		}
	}

	public function getUserVote(
		int $postId,
		string $authorSub
	): int
	{
		$row = $this->database->fetchOne(
			'
			SELECT vote
			FROM community_post_votes
			WHERE post_id = ?
			  AND author_sub = ?
			LIMIT 1
			',
			[
				$postId,
				$authorSub,
			]
		);

		if (!$row) {
			return 0;
		}

		return (int) $row['vote'];
	}

	public function getScore(
		int $postId
	): int
	{
		$row = $this->database->fetchOne(
			'
			SELECT COALESCE(SUM(vote), 0) AS score
			FROM community_post_votes
			WHERE post_id = ?
			',
			[
				$postId,
			]
		);

		return (int) ($row['score'] ?? 0);
	}

	public function getScoresForPosts(
		array $postIds
	): array
	{
		$postIds = array_values(
			array_unique(
				array_filter(
					array_map('intval', $postIds),
					static fn (int $postId): bool => $postId > 0
				)
			)
		);

		if ($postIds === []) {
			return [];
		}

		$placeholders = implode(
			', ',
			array_fill(0, count($postIds), '?')
		);

		$rows = $this->database->fetchAll(
			'
			SELECT
				post_id,
				COALESCE(SUM(vote), 0) AS score
			FROM community_post_votes
			WHERE post_id IN (' . $placeholders . ')
			GROUP BY post_id
			',
			$postIds
		);

		$scores = array_fill_keys(
			$postIds,
			0
		);

		foreach ($rows as $row) {
			$scores[(int) $row['post_id']] =
				(int) $row['score'];
		}

		return $scores;
	}

	private function createUpvoteNotification(
		int $postId,
		string $actorSub
	): void
	{
		$post = $this->posts->findById($postId);

		if (!$post) {
			return;
		}

		$recipientSub = (string) ($post['author_sub'] ?? '');
		$postUuid = (string) ($post['uuid'] ?? '');

		if ($recipientSub === '' || $postUuid === '') {
			return;
		}

		$this->notifications->createUpvoteNotification(
			$recipientSub,
			$actorSub,
			$postUuid
		);
	}

	private function insertVote(
		int $postId,
		string $authorSub,
		int $vote
	): void
	{
		$now = date('Y-m-d H:i:s');

		$this->database->execute(
			'
			INSERT INTO community_post_votes
			(
				post_id,
				author_sub,
				vote,
				created_at,
				updated_at
			)
			VALUES (?, ?, ?, ?, ?)
			',
			[
				$postId,
				$authorSub,
				$vote,
				$now,
				$now,
			]
		);
	}

	private function updateVote(
		int $postId,
		string $authorSub,
		int $vote
	): void
	{
		$this->database->execute(
			'
			UPDATE community_post_votes
			SET
				vote = ?,
				updated_at = ?
			WHERE post_id = ?
			  AND author_sub = ?
			',
			[
				$vote,
				date('Y-m-d H:i:s'),
				$postId,
				$authorSub,
			]
		);
	}

	private function deleteVote(
		int $postId,
		string $authorSub
	): void
	{
		$this->database->execute(
			'
			DELETE FROM community_post_votes
			WHERE post_id = ?
			  AND author_sub = ?
			',
			[
				$postId,
				$authorSub,
			]
		);
	}
}