<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class PostService
{
	public function __construct(
		private Database $database,
		private MentionService $mentions,
		private LinkService $linkService,
		private MediaService $mediaService,
		private BlockService $blocks,
		private DogeTipService $dogeTips
	) {
	}

	private function enrichDogeTip(array &$post): void
	{
		$post['doge_tip_resolved_address'] = null;

		$source = trim(
			(string) ($post['doge_tip_source'] ?? '')
		);

		if (
			!in_array(
				$source,
				[
					'mydogemask',
					'simosnap',
				],
				true
			)
		) {
			return;
		}

		$address = $this->dogeTips->resolveAddress(
			[
				'username' => $post['username'] ?? null,
				'doge_tip_source' => $source,
				'doge_tip_address' =>
					$post['doge_tip_address'] ?? null,
			]
		);

		if ($address !== null) {
			$post['doge_tip_resolved_address'] = $address;
		}
	}

	public function findById(int $id): array|false
	{
		return $this->database->fetchOne(
			'
			SELECT *
			FROM community_posts
			WHERE id = ?
			  AND status = ?
			  AND deleted_at IS NULL
			LIMIT 1
			',
			[
				$id,
				'published',
			]
		);
	}

	public function findByUuid(string $uuid, ?string $currentSub = null): array|false
	{
		$post = $this->database->fetchOne(
			'
			SELECT
				p.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.doge_tip_source,
				pr.doge_tip_address,
				pr.external_account_exists,
				COUNT(c.id) AS comments_count,
				(
					SELECT COALESCE(SUM(v.vote), 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
				) AS score,
				(
					SELECT COALESCE(v.vote, 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
					  AND v.author_sub = ?
					LIMIT 1
				) AS user_vote
			FROM community_posts p

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			LEFT JOIN community_post_comments c
				ON c.post_id = p.id
				AND c.status = ?
				AND c.deleted_at IS NULL

			WHERE p.uuid = ?
			  AND p.status = ?
			  AND p.deleted_at IS NULL

			GROUP BY p.id

			LIMIT 1
			',
			[
				$currentSub ?? '',
				'published',
				$uuid,
				'published'
			]
		);

		if ($post !== false) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);

			$this->enrichDogeTip($post);

			$post['is_blocked_for_viewer'] = false;

			if (
				$currentSub !== null
				&& trim($currentSub) !== ''
				&& (string) $post['author_sub'] !== $currentSub
			) {
				$post['is_blocked_for_viewer'] = $this->blocks->isEitherBlocked(
					$currentSub,
					(string) $post['author_sub']
				);
			}
		}

		return $post;
	}

	public function delete(string $uuid): bool

	{

		$post = $this->findByUuid($uuid);
		if (!$post) {
			return false;
		}
		$this->mediaService->deleteByPostId(
			(int) $post['id']
		);
		return $this->database->execute(
			'
			DELETE FROM community_posts
			WHERE id = ?
			',
			[
				(int) $post['id']
			]
		);

	}

	public function update(string $uuid, string $content): bool
	{
		return $this->database->execute(
			'
			UPDATE community_posts
			SET content = ?,
				updated_at = ?
			WHERE uuid = ?
			',
			[
				$content,
				date('Y-m-d H:i:s'),
				$uuid,
			]
		);
	}

	public function listPublished(
		int $limit = 20,
		int $offset = 0,
		?string $currentSub = null
	): array {
		$limit = max(1, $limit);
		$offset = max(0, $offset);

		$currentSub = trim((string) $currentSub);

		$blockFilter = '';
		$params = [
			$currentSub,
			'published',
			'published',
		];

		if ($currentSub !== '') {
			$blockFilter = '
				AND NOT EXISTS (
					SELECT 1
					FROM user_blocks ub
					WHERE (
						ub.blocker_sub = ?
						AND ub.blocked_sub = p.author_sub
					)
					OR (
						ub.blocker_sub = p.author_sub
						AND ub.blocked_sub = ?
					)
				)
			';

			$params[] = $currentSub;
			$params[] = $currentSub;
		}

		$posts = $this->database->fetchAll(
			'
			SELECT
				p.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.doge_tip_source,
				pr.doge_tip_address,
				pr.external_account_exists,
				COUNT(c.id) AS comments_count,
				(
					SELECT COALESCE(SUM(v.vote), 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
				) AS score,
				(
					SELECT COALESCE(v.vote, 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
					  AND v.author_sub = ?
					LIMIT 1
				) AS user_vote
			FROM community_posts p

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			LEFT JOIN community_post_comments c
				ON c.post_id = p.id
				AND c.status = ?
				AND c.deleted_at IS NULL

			WHERE p.status = ?
			  AND p.deleted_at IS NULL
			  ' . $blockFilter . '

			GROUP BY p.id

			ORDER BY p.published_at DESC

			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			$params
		);

		foreach ($posts as &$post) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);

			$this->enrichDogeTip($post);
		}

		unset($post);

		return $posts;
	}

	public function searchPublished(
		string $query,
		int $limit = 20,
		int $offset = 0,
		?string $currentSub = null
	): array {
		$query = trim($query);

		if ($query === '') {
			return [];
		}

		$limit = max(1, $limit);
		$offset = max(0, $offset);

		$currentSub = trim(
			(string) $currentSub
		);

		$blockFilter = '';

		$params = [
			$currentSub,
			'published',
			'published',
			'%' . $query . '%',
		];

		if ($currentSub !== '') {
			$blockFilter = '
				AND NOT EXISTS (
					SELECT 1
					FROM user_blocks ub
					WHERE (
						ub.blocker_sub = ?
						AND ub.blocked_sub = p.author_sub
					)
					OR (
						ub.blocker_sub = p.author_sub
						AND ub.blocked_sub = ?
					)
				)
			';

			$params[] = $currentSub;
			$params[] = $currentSub;
		}

		$posts = $this->database->fetchAll(
			'
			SELECT
				p.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.doge_tip_source,
				pr.doge_tip_address,
				pr.external_account_exists,
				COUNT(c.id) AS comments_count,
				(
					SELECT COALESCE(SUM(v.vote), 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
				) AS score,
				(
					SELECT COALESCE(v.vote, 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
					  AND v.author_sub = ?
					LIMIT 1
				) AS user_vote
			FROM community_posts p

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			LEFT JOIN community_post_comments c
				ON c.post_id = p.id
				AND c.status = ?
				AND c.deleted_at IS NULL

			WHERE p.status = ?
			  AND p.deleted_at IS NULL
			  AND p.content LIKE ?
			  ' . $blockFilter . '

			GROUP BY p.id

			ORDER BY p.published_at DESC

			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			$params
		);

		foreach ($posts as &$post) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);

			$this->enrichDogeTip($post);
		}

		unset($post);

		return $posts;
	}

	public function listPublishedByMediaType(
		string $mediaType,
		int $limit = 20,
		int $offset = 0,
		?string $currentSub = null
	): array {
		$mediaType = strtolower(
			trim($mediaType)
		);

		if (
			!in_array(
				$mediaType,
				[
					'audio',
					'video',
				],
				true
			)
		) {
			return [];
		}

		$limit = max(1, $limit);
		$offset = max(0, $offset);

		$currentSub = trim(
			(string) $currentSub
		);

		$blockFilter = '';

		$params = [
			$currentSub,
			'published',
			'published',
			$mediaType,
		];

		if ($currentSub !== '') {
			$blockFilter = '
				AND NOT EXISTS (
					SELECT 1
					FROM user_blocks ub
					WHERE (
						ub.blocker_sub = ?
						AND ub.blocked_sub = p.author_sub
					)
					OR (
						ub.blocker_sub = p.author_sub
						AND ub.blocked_sub = ?
					)
				)
			';

			$params[] = $currentSub;
			$params[] = $currentSub;
		}

		$posts = $this->database->fetchAll(
			'
			SELECT
				p.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.doge_tip_source,
				pr.doge_tip_address,
				pr.external_account_exists,
				COUNT(c.id) AS comments_count,
				(
					SELECT COALESCE(SUM(v.vote), 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
				) AS score,
				(
					SELECT COALESCE(v.vote, 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
					  AND v.author_sub = ?
					LIMIT 1
				) AS user_vote
			FROM community_posts p

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			LEFT JOIN community_post_comments c
				ON c.post_id = p.id
				AND c.status = ?
				AND c.deleted_at IS NULL

			WHERE p.status = ?
			  AND p.deleted_at IS NULL

			  AND EXISTS (
					SELECT 1
					FROM community_post_media m
					WHERE m.post_id = p.id
					  AND m.media_type = ?
					  AND m.status = \'active\'
					  AND m.deleted_at IS NULL
			  )

			  ' . $blockFilter . '

			GROUP BY p.id

			ORDER BY p.published_at DESC

			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			$params
		);

		foreach ($posts as &$post) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);

			$this->enrichDogeTip($post);
		}

		unset($post);

		return $posts;
	}

	public function listPublishedFollowing(
		string $followerSub,
		int $limit = 20,
		int $offset = 0
	): array {
		$followerSub = trim($followerSub);

		if ($followerSub === '') {
			return [];
		}

		$limit = max(1, $limit);
		$offset = max(0, $offset);

		$blockFilter = '
			AND NOT EXISTS (
				SELECT 1
				FROM user_blocks ub
				WHERE (
					ub.blocker_sub = ?
					AND ub.blocked_sub = p.author_sub
				)
				OR (
					ub.blocker_sub = p.author_sub
					AND ub.blocked_sub = ?
				)
			)
		';

		$params = [
			$followerSub,
			'published',
			'published',
			$followerSub,
			$followerSub,
			$followerSub,
		];

		$posts = $this->database->fetchAll(
			'
			SELECT
				p.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.doge_tip_source,
				pr.doge_tip_address,
				pr.external_account_exists,
				COUNT(c.id) AS comments_count,
				(
					SELECT COALESCE(SUM(v.vote), 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
				) AS score,
				(
					SELECT COALESCE(v.vote, 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
					  AND v.author_sub = ?
					LIMIT 1
				) AS user_vote
			FROM community_posts p

			INNER JOIN user_follows uf
				ON uf.followed_sub = p.author_sub
				AND uf.follower_sub = ?

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			LEFT JOIN community_post_comments c
				ON c.post_id = p.id
				AND c.status = ?
				AND c.deleted_at IS NULL

			WHERE p.status = ?
			  AND p.deleted_at IS NULL
			  ' . $blockFilter . '

			GROUP BY p.id

			ORDER BY p.published_at DESC

			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			$params
		);

		foreach ($posts as &$post) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);

			$this->enrichDogeTip($post);
		}

		unset($post);

		return $posts;
	}

	public function listPublishedInteractions(
		string $userSub,
		int $limit = 20,
		int $offset = 0
	): array {
		$userSub = trim($userSub);

		if ($userSub === '') {
			return [];
		}

		$limit = max(1, $limit);
		$offset = max(0, $offset);

		$blockFilter = '
			AND NOT EXISTS (
				SELECT 1
				FROM user_blocks ub
				WHERE (
					ub.blocker_sub = ?
					AND ub.blocked_sub = p.author_sub
				)
				OR (
					ub.blocker_sub = p.author_sub
					AND ub.blocked_sub = ?
				)
			)
		';

		$params = [
			$userSub,
			'published',
			'published',

			$userSub,
			$userSub,
			'published',
			$userSub,
			'post',

			$userSub,
			$userSub,
		];

		$posts = $this->database->fetchAll(
			'
			SELECT
				p.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.doge_tip_source,
				pr.doge_tip_address,
				pr.external_account_exists,
				COUNT(c.id) AS comments_count,
				(
					SELECT COALESCE(SUM(v.vote), 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
				) AS score,
				(
					SELECT COALESCE(v.vote, 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
					  AND v.author_sub = ?
					LIMIT 1
				) AS user_vote
			FROM community_posts p

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			LEFT JOIN community_post_comments c
				ON c.post_id = p.id
				AND c.status = ?
				AND c.deleted_at IS NULL

			WHERE p.status = ?
			  AND p.deleted_at IS NULL

			  AND (
					EXISTS (
						SELECT 1
						FROM community_post_votes iv
						WHERE iv.post_id = p.id
						  AND iv.author_sub = ?
					)

					OR EXISTS (
						SELECT 1
						FROM community_post_comments ic
						WHERE ic.post_id = p.id
						  AND ic.author_sub = ?
						  AND ic.status = ?
						  AND ic.deleted_at IS NULL
					)

					OR EXISTS (
						SELECT 1
						FROM community_saved_items si
						WHERE si.user_sub = ?
						  AND si.object_type = ?
						  AND si.object_uuid = p.uuid
					)
			  )

			  ' . $blockFilter . '

			GROUP BY p.id

			ORDER BY p.published_at DESC

			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			$params
		);

		foreach ($posts as &$post) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);

			$this->enrichDogeTip($post);
		}

		unset($post);

		return $posts;
	}

	public function listPublishedByAuthor(
		string $authorSub,
		int $limit = 20,
		int $offset = 0,
		?string $currentSub = null,
		string $feed = 'all'
	): array {
		$authorSub = trim($authorSub);
		$currentSub = trim((string) $currentSub);
		$feed = strtolower(trim($feed));

		if ($authorSub === '') {
			return [];
		}

		if (
			!in_array(
				$feed,
				[
					'all',
					'audio',
					'video',
					'interactions',
				],
				true
			)
		) {
			$feed = 'all';
		}

		if (
			$feed === 'interactions'
			&& (
				$currentSub === ''
				|| $currentSub === $authorSub
			)
		) {
			return [];
		}

		$limit = max(1, $limit);
		$offset = max(0, $offset);

		$feedFilter = '';
		$blockFilter = '';

		$params = [
			$currentSub,
			'published',
			$authorSub,
			'published',
		];

		if (
			$feed === 'audio'
			|| $feed === 'video'
		) {
			$feedFilter = '
				AND EXISTS (
					SELECT 1
					FROM community_post_media m
					WHERE m.post_id = p.id
					  AND m.media_type = ?
					  AND m.status = \'active\'
					  AND m.deleted_at IS NULL
				)
			';

			$params[] = $feed;
		}

		if ($feed === 'interactions') {
			$feedFilter = '
				AND (
					EXISTS (
						SELECT 1
						FROM community_post_votes iv
						WHERE iv.post_id = p.id
						  AND iv.author_sub = ?
					)

					OR EXISTS (
						SELECT 1
						FROM community_post_comments ic
						WHERE ic.post_id = p.id
						  AND ic.author_sub = ?
						  AND ic.status = ?
						  AND ic.deleted_at IS NULL
					)

					OR EXISTS (
						SELECT 1
						FROM community_saved_items si
						WHERE si.user_sub = ?
						  AND si.object_type = ?
						  AND si.object_uuid = p.uuid
					)
				)
			';

			$params[] = $currentSub;
			$params[] = $currentSub;
			$params[] = 'published';
			$params[] = $currentSub;
			$params[] = 'post';
		}

		if ($currentSub !== '') {
			$blockFilter = '
				AND NOT EXISTS (
					SELECT 1
					FROM user_blocks ub
					WHERE (
						ub.blocker_sub = ?
						AND ub.blocked_sub = p.author_sub
					)
					OR (
						ub.blocker_sub = p.author_sub
						AND ub.blocked_sub = ?
					)
				)
			';

			$params[] = $currentSub;
			$params[] = $currentSub;
		}

		$posts = $this->database->fetchAll(
			'
			SELECT
				p.*,
				pr.username,
				pr.nickname,
				pr.avatar_url,
				pr.show_avatar,
				pr.public_profile,
				pr.doge_tip_source,
				pr.doge_tip_address,
				pr.external_account_exists,
				COUNT(c.id) AS comments_count,
				(
					SELECT COALESCE(SUM(v.vote), 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
				) AS score,
				(
					SELECT COALESCE(v.vote, 0)
					FROM community_post_votes v
					WHERE v.post_id = p.id
					  AND v.author_sub = ?
					LIMIT 1
				) AS user_vote
			FROM community_posts p

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			LEFT JOIN community_post_comments c
				ON c.post_id = p.id
				AND c.status = ?
				AND c.deleted_at IS NULL

			WHERE p.author_sub = ?
			  AND p.status = ?
			  AND p.deleted_at IS NULL
			  ' . $feedFilter . '
			  ' . $blockFilter . '

			GROUP BY p.id

			ORDER BY p.published_at DESC

			LIMIT ' . $limit . '
			OFFSET ' . $offset,
			$params
		);

		foreach ($posts as &$post) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);

			$this->enrichDogeTip($post);
		}

		unset($post);

		return $posts;
	}

	public function countPublishedByAuthor(
		string $authorSub,
		?string $currentSub = null,
		string $feed = 'all'
	): int {
		$authorSub = trim($authorSub);
		$currentSub = trim((string) $currentSub);
		$feed = strtolower(trim($feed));

		if ($authorSub === '') {
			return 0;
		}

		if (
			!in_array(
				$feed,
				[
					'all',
					'audio',
					'video',
					'interactions',
				],
				true
			)
		) {
			$feed = 'all';
		}

		if (
			$feed === 'interactions'
			&& (
				$currentSub === ''
				|| $currentSub === $authorSub
			)
		) {
			return 0;
		}

		$feedFilter = '';
		$blockFilter = '';

		$params = [
			$authorSub,
			'published',
		];

		if (
			$feed === 'audio'
			|| $feed === 'video'
		) {
			$feedFilter = '
				AND EXISTS (
					SELECT 1
					FROM community_post_media m
					WHERE m.post_id = p.id
					  AND m.media_type = ?
					  AND m.status = \'active\'
					  AND m.deleted_at IS NULL
				)
			';

			$params[] = $feed;
		}

		if ($feed === 'interactions') {
			$feedFilter = '
				AND (
					EXISTS (
						SELECT 1
						FROM community_post_votes iv
						WHERE iv.post_id = p.id
						  AND iv.author_sub = ?
					)

					OR EXISTS (
						SELECT 1
						FROM community_post_comments ic
						WHERE ic.post_id = p.id
						  AND ic.author_sub = ?
						  AND ic.status = ?
						  AND ic.deleted_at IS NULL
					)

					OR EXISTS (
						SELECT 1
						FROM community_saved_items si
						WHERE si.user_sub = ?
						  AND si.object_type = ?
						  AND si.object_uuid = p.uuid
					)
				)
			';

			$params[] = $currentSub;
			$params[] = $currentSub;
			$params[] = 'published';
			$params[] = $currentSub;
			$params[] = 'post';
		}

		if ($currentSub !== '') {
			$blockFilter = '
				AND NOT EXISTS (
					SELECT 1
					FROM user_blocks ub
					WHERE (
						ub.blocker_sub = ?
						AND ub.blocked_sub = p.author_sub
					)
					OR (
						ub.blocker_sub = p.author_sub
						AND ub.blocked_sub = ?
					)
				)
			';

			$params[] = $currentSub;
			$params[] = $currentSub;
		}

		$result = $this->database->fetchOne(
			'
			SELECT COUNT(*) AS total
			FROM community_posts p
			WHERE p.author_sub = ?
			  AND p.status = ?
			  AND p.deleted_at IS NULL
			  ' . $feedFilter . '
			  ' . $blockFilter,
			$params
		);

		if ($result === false) {
			return 0;
		}

		return (int) (
			$result['total']
				?? 0
		);
	}

	public function listPublishedForRss(
		int $limit = 50
	): array {
		$limit = max(1, min(100, $limit));

		$posts = $this->database->fetchAll(
			'
			SELECT
				p.id,
				p.uuid,
				p.content,
				p.published_at,
				pr.username,
				pr.nickname
			FROM community_posts p

			LEFT JOIN profiles pr
				ON pr.oauth_sub = p.author_sub

			WHERE p.status = ?
			  AND p.visibility = ?
			  AND p.deleted_at IS NULL

			ORDER BY
				p.published_at DESC,
				p.id DESC

			LIMIT ' . $limit,
			[
				'published',
				'public',
			]
		);

		foreach ($posts as &$post) {
			$post['media'] = $this->mediaService->findByPostId(
				(int) $post['id']
			);
		}

		unset($post);

		return $posts;
	}

	public function listPublishedForSitemap(): array
	{
		return $this->database->fetchAll(
			'
			SELECT
				uuid,
				published_at,
				updated_at
			FROM community_posts
			WHERE status = ?
			  AND visibility = ?
			  AND deleted_at IS NULL
			ORDER BY published_at DESC
			',
			[
				'published',
				'public',
			]
		);
	}

	public function create(
		array $data,
		array $files = []
	): bool
	{
		$uuid = Uuid::v4();

		$links = $this->linkService->enrich(
			$this->linkService->extract($data['content'])
		);

		$metadata = json_encode(
			['links' => $links],
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);

		if (!empty($files)) {
			$this->mediaService->validate($files);
		}

		$created = $this->database->execute(
			'
			INSERT INTO community_posts
			(
				uuid,
				author_sub,
				source,
				content,
				visibility,
				status,
				comments_enabled,
				published_at,
				created_at,
				updated_at,
				metadata
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			',
			[
				$uuid,
				$data['author_sub'],
				$data['source'] ?? 'user',
				$data['content'],
				$data['visibility'] ?? 'public',
				'published',
				!empty($data['comments_enabled']) ? 1 : 0,
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				$metadata,
			]
		);

		$postId = null;

		if ($created) {
			$postId = (int) $this->database->lastInsertId();
		}

		if ($created) {

			if (!empty($files)) {
				$this->mediaService->store(
					$postId,
					$data['author_sub'],
					$files,
					isset($data['audio_title'])
						? (string) $data['audio_title']
						: null,
					isset($data['audio_artist'])
						? (string) $data['audio_artist']
						: null,
					isset($data['audio_tracklist'])
						? (string) $data['audio_tracklist']
						: null
				);
			}

			$this->mentions->notifyMentions(
				$data['content'],
				$data['author_sub'],
				'post',
				$uuid
			);
		}

		return $created;
	}

	public function createChanzinePing(
		string $title,
		string $excerpt,
		string $slug
	): string|false {
		$uuid = Uuid::v4();
		$now = date('Y-m-d H:i:s');

		$scheme = (
			(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https'
		)
			? 'https'
			: 'http';

		$host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));

		$path = '/chanzine/' . rawurlencode($slug);

		$url = $host !== ''
			? $scheme . '://' . $host . $path
			: $path;

		$content = $title;

		if (trim($excerpt) !== '') {
			$content .= "\n\n" . trim($excerpt);
		}

		$content .= "\n\n" . $url;

		$links = $this->linkService->enrich(
			$this->linkService->extract($content)
		);

		$metadata = json_encode(
			[
				'links' => $links,
				'type' => 'chanzine',
				'article_slug' => $slug,
			],
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);

		$created = $this->database->execute(
			'
			INSERT INTO community_posts
			(
				uuid,
				author_sub,
				source,
				content,
				visibility,
				status,
				comments_enabled,
				published_at,
				created_at,
				updated_at,
				metadata
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			',
			[
				$uuid,
				'',
				'chanzine',
				$content,
				'public',
				'published',
				1,
				$now,
				$now,
				$now,
				$metadata,
			]
		);

		return $created ? $uuid : false;
	}
}