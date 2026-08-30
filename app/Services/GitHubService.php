<?php
declare(strict_types=1);

namespace Monoverse\Services;

final class GitHubService
{
	private const CACHE_TTL = 600;

	public function __construct(
		private SettingsService $settings
	) {
	}

	public function normalizeRepository(
		string $repository
	): string {
		$repository = trim($repository);

		if ($repository === '') {
			return '';
		}

		$repository = preg_replace(
			'#^https?://(?:www\.)?github\.com/#i',
			'',
			$repository
		) ?? $repository;

		$repository = trim(
			$repository,
			"/ \t\n\r\0\x0B"
		);

		if (
			str_ends_with(
				strtolower($repository),
				'.git'
			)
		) {
			$repository = substr(
				$repository,
				0,
				-4
			);
		}

		if (
			!preg_match(
				'#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#',
				$repository
			)
		) {
			return '';
		}

		return $repository;
	}

	public function isValidRepository(
		string $repository
	): bool {
		return $this->normalizeRepository(
			$repository
		) !== '';
	}

	public function getRepositoryDashboard(
		string $repository,
		?string $branch = null
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if ($repository === '') {
			return [];
		}

		$repositoryData = $this->request(
			'https://api.github.com/repos/'
			. $repository
		);

		if ($repositoryData === []) {
			return [];
		}

		$defaultBranch = trim(
			(string) (
				$repositoryData['default_branch']
				?? 'main'
			)
		);

		$branch = trim(
			(string) (
				$branch
					?? $defaultBranch
			)
		);

		if ($branch === '') {
			$branch = $defaultBranch;
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-repository-'
			. hash(
				'sha256',
				strtolower(
					$repository . '|' . $branch
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$branches = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/branches?per_page=100'
		);

		$commitsUrl =
			'https://api.github.com/repos/'
			. $repository
			. '/commits?sha='
			. rawurlencode($branch)
			. '&per_page=10';

		$commits = $this->request(
			$commitsUrl
		);

		$releases = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/releases?per_page=20'
		);

		$languages = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/languages'
		);

		$pullRequests = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/pulls?state=open&per_page=5'
		);

		$issues = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/issues?state=open&per_page=5'
		);

		$result = [
			'repository' => $this->normalizeRepositoryData(
				$repositoryData
			),
			'branch' => $branch,
			'branches' => $this->normalizeBranches(
				$branches,
				$branch
			),
			'commits' => $this->normalizeCommits(
				$commits
			),
			'releases' => $this->normalizeReleases(
				$releases
			),
			'languages' => $this->normalizeLanguages(
				$languages
			),
			'pull_requests' => $this->normalizePullRequests(
				$pullRequests
			),
			'issues' => $this->normalizeIssues(
				$issues
			),
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getRepositoryPreview(
		string $repository
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if ($repository === '') {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-repository-preview-'
			. hash(
				'sha256',
				strtolower($repository)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$repositoryData = $this->request(
			'https://api.github.com/repos/'
			. $repository
		);

		if ($repositoryData === []) {
			return [];
		}

		$readmeData = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/readme'
		);

		$readme = '';

		if (
			($readmeData['encoding'] ?? '') === 'base64'
			&& !empty($readmeData['content'])
		) {
			$decodedReadme = base64_decode(
				preg_replace(
					'/\s+/',
					'',
					(string) $readmeData['content']
				) ?? '',
				true
			);

			if (
				$decodedReadme !== false
				&& !str_contains(
					$decodedReadme,
					"\0"
				)
			) {
				$readme = trim(
					$decodedReadme
				);
			}
		}

		if (strlen($readme) > 20000) {
			$readme = substr(
				$readme,
				0,
				20000
			);
		}

		$result = [
			'repository' =>
				$this->normalizeRepositoryData(
					$repositoryData
				),
			'readme' => $readme,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getDiscussion(
		string $repository,
		int $number
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if (
			$repository === ''
			|| $number <= 0
		) {
			return [];
		}

		[$owner, $name] = explode(
			'/',
			$repository,
			2
		);

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-discussion-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $number
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$query = <<<'GRAPHQL'
	query(
		$owner: String!,
		$name: String!,
		$number: Int!
	) {
		repository(
			owner: $owner,
			name: $name
		) {
			discussion(number: $number) {
				number
				title
				body
				url
				createdAt
				updatedAt
				closed
				locked
				upvoteCount

				author {
					login
					avatarUrl
					url
				}

				category {
					name
					emoji
					description
				}

				comments {
					totalCount
				}

				answer {
					url
					createdAt

					author {
						login
					}

					body
				}
			}
		}
	}
	GRAPHQL;

		$response = $this->graphqlRequest(
			$query,
			[
				'owner' => $owner,
				'name' => $name,
				'number' => $number,
			]
		);

		$discussion = $response['data']['repository']['discussion']
			?? null;

		if (!is_array($discussion)) {
			return [];
		}

		$author = is_array(
			$discussion['author']
				?? null
		)
			? $discussion['author']
			: [];

		$category = is_array(
			$discussion['category']
				?? null
		)
			? $discussion['category']
			: [];

		$answer = is_array(
			$discussion['answer']
				?? null
		)
			? $discussion['answer']
			: [];

		$answerAuthor = is_array(
			$answer['author']
				?? null
		)
			? $answer['author']
			: [];

		$result = [
			'number' => (int) (
				$discussion['number']
					?? $number
			),
			'title' => trim(
				(string) (
					$discussion['title']
						?? ''
				)
			),
			'body' => trim(
				(string) (
					$discussion['body']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$discussion['url']
						?? ''
				)
			),
			'created_at' => trim(
				(string) (
					$discussion['createdAt']
						?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$discussion['updatedAt']
						?? ''
				)
			),
			'closed' => (bool) (
				$discussion['closed']
					?? false
			),
			'locked' => (bool) (
				$discussion['locked']
					?? false
			),
			'upvotes' => (int) (
				$discussion['upvoteCount']
					?? 0
			),
			'comments' => (int) (
				$discussion['comments']['totalCount']
					?? 0
			),
			'author' => [
				'login' => trim(
					(string) (
						$author['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$author['avatarUrl']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$author['url']
							?? ''
					)
				),
			],
			'category' => [
				'name' => trim(
					(string) (
						$category['name']
							?? ''
					)
				),
				'emoji' => trim(
					(string) (
						$category['emoji']
							?? ''
					)
				),
				'description' => trim(
					(string) (
						$category['description']
							?? ''
					)
				),
			],
			'answer' => [
				'url' => trim(
					(string) (
						$answer['url']
							?? ''
					)
				),
				'created_at' => trim(
					(string) (
						$answer['createdAt']
							?? ''
					)
				),
				'body' => trim(
					(string) (
						$answer['body']
							?? ''
					)
				),
				'author' => trim(
					(string) (
						$answerAuthor['login']
							?? ''
					)
				),
			],
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getPullRequests(
		string $repository,
		string $state = 'open',
		int $limit = 5
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if ($repository === '') {
			return [];
		}

		if (!in_array(
			$state,
			[
				'open',
				'closed',
				'all',
			],
			true
		)) {
			$state = 'open';
		}

		$limit = in_array(
			$limit,
			[
				3,
				5,
				10,
			],
			true
		)
			? $limit
			: 5;

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-pull-requests-'
			. hash(
				'sha256',
				strtolower(
					$repository
					. '|'
					. $state
					. '|'
					. $limit
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$pullRequests = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/pulls?state='
			. rawurlencode($state)
			. '&per_page='
			. $limit
		);

		$result = $this->normalizePullRequests(
			$pullRequests
		);

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getPullRequest(
		string $repository,
		int $number
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if (
			$repository === ''
			|| $number <= 0
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-pull-request-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $number
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/pulls/'
			. $number
		);

		if ($response === []) {
			return [];
		}

		$filesResponse = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/pulls/'
			. $number
			. '/files?per_page=100'
		);

		$user = is_array(
			$response['user']
				?? null
		)
			? $response['user']
			: [];

		$head = is_array(
			$response['head']
				?? null
		)
			? $response['head']
			: [];

		$base = is_array(
			$response['base']
				?? null
		)
			? $response['base']
			: [];

		$labels = [];

		foreach (
			(array) (
				$response['labels']
					?? []
			)
			as $label
		) {
			if (!is_array($label)) {
				continue;
			}

			$name = trim(
				(string) (
					$label['name']
						?? ''
				)
			);

			if ($name === '') {
				continue;
			}

			$labels[] = [
				'name' => $name,
				'color' => trim(
					(string) (
						$label['color']
							?? ''
					)
				),
			];
		}

		$files = [];

		if (array_is_list($filesResponse)) {
			foreach ($filesResponse as $file) {
				if (!is_array($file)) {
					continue;
				}

				$filename = trim(
					(string) (
						$file['filename']
							?? ''
					)
				);

				if ($filename === '') {
					continue;
				}

				$files[] = [
					'filename' => $filename,
					'status' => trim(
						(string) (
							$file['status']
								?? ''
						)
					),
					'additions' => (int) (
						$file['additions']
							?? 0
					),
					'deletions' => (int) (
						$file['deletions']
							?? 0
					),
					'changes' => (int) (
						$file['changes']
							?? 0
					),
					'blob_url' => trim(
						(string) (
							$file['blob_url']
								?? ''
						)
					),
					'raw_url' => trim(
						(string) (
							$file['raw_url']
								?? ''
						)
					),
					'patch' => (string) (
						$file['patch']
							?? ''
					),
				];
			}
		}

		$state = trim(
			(string) (
				$response['state']
					?? ''
			)
		);

		$merged = (bool) (
			$response['merged']
				?? false
		);

		if ($merged) {
			$displayState = 'merged';
		} elseif ($state === 'closed') {
			$displayState = 'closed';
		} else {
			$displayState = 'open';
		}

		$result = [
			'number' => (int) (
				$response['number']
					?? $number
			),
			'title' => trim(
				(string) (
					$response['title']
						?? ''
				)
			),
			'body' => trim(
				(string) (
					$response['body']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'state' => $displayState,
			'draft' => (bool) (
				$response['draft']
					?? false
			),
			'merged' => $merged,
			'mergeable' => array_key_exists(
				'mergeable',
				$response
			)
				? $response['mergeable']
				: null,
			'created_at' => trim(
				(string) (
					$response['created_at']
						?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$response['updated_at']
						?? ''
				)
			),
			'merged_at' => trim(
				(string) (
					$response['merged_at']
						?? ''
				)
			),
			'closed_at' => trim(
				(string) (
					$response['closed_at']
						?? ''
				)
			),
			'author' => [
				'login' => trim(
					(string) (
						$user['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$user['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$user['html_url']
							?? ''
					)
				),
			],
			'head' => [
				'ref' => trim(
					(string) (
						$head['ref']
							?? ''
					)
				),
				'sha' => trim(
					(string) (
						$head['sha']
							?? ''
					)
				),
			],
			'base' => [
				'ref' => trim(
					(string) (
						$base['ref']
							?? ''
					)
				),
				'sha' => trim(
					(string) (
						$base['sha']
							?? ''
					)
				),
			],
			'commits' => (int) (
				$response['commits']
					?? 0
			),
			'changed_files' => (int) (
				$response['changed_files']
					?? count($files)
			),
			'additions' => (int) (
				$response['additions']
					?? 0
			),
			'deletions' => (int) (
				$response['deletions']
					?? 0
			),
			'comments' => (int) (
				$response['comments']
					?? 0
			),
			'review_comments' => (int) (
				$response['review_comments']
					?? 0
			),
			'labels' => $labels,
			'files' => $files,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getIssue(
		string $repository,
		int $number
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if (
			$repository === ''
			|| $number <= 0
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-issue-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $number
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/issues/'
			. $number
		);

		if (
			$response === []
			|| isset($response['pull_request'])
		) {
			return [];
		}

		$user = is_array(
			$response['user']
				?? null
		)
			? $response['user']
			: [];

		$assignee = is_array(
			$response['assignee']
				?? null
		)
			? $response['assignee']
			: [];

		$milestone = is_array(
			$response['milestone']
				?? null
		)
			? $response['milestone']
			: [];

		$labels = [];

		foreach (
			(array) (
				$response['labels']
					?? []
			)
			as $label
		) {
			if (!is_array($label)) {
				continue;
			}

			$name = trim(
				(string) (
					$label['name']
						?? ''
				)
			);

			if ($name === '') {
				continue;
			}

			$labels[] = [
				'name' => $name,
				'color' => trim(
					(string) (
						$label['color']
							?? ''
					)
				),
			];
		}

		$state = trim(
			(string) (
				$response['state']
					?? 'open'
			)
		);

		$result = [
			'number' => (int) (
				$response['number']
					?? $number
			),
			'title' => trim(
				(string) (
					$response['title']
						?? ''
				)
			),
			'body' => trim(
				(string) (
					$response['body']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'state' => $state === 'closed'
				? 'closed'
				: 'open',
			'state_reason' => trim(
				(string) (
					$response['state_reason']
						?? ''
				)
			),
			'created_at' => trim(
				(string) (
					$response['created_at']
						?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$response['updated_at']
						?? ''
				)
			),
			'closed_at' => trim(
				(string) (
					$response['closed_at']
						?? ''
				)
			),
			'comments' => (int) (
				$response['comments']
					?? 0
			),
			'author' => [
				'login' => trim(
					(string) (
						$user['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$user['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$user['html_url']
							?? ''
					)
				),
			],
			'assignee' => [
				'login' => trim(
					(string) (
						$assignee['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$assignee['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$assignee['html_url']
							?? ''
					)
				),
			],
			'milestone' => [
				'title' => trim(
					(string) (
						$milestone['title']
							?? ''
					)
				),
				'number' => (int) (
					$milestone['number']
						?? 0
				),
				'state' => trim(
					(string) (
						$milestone['state']
							?? ''
					)
				),
			],
			'labels' => $labels,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getRelease(
		string $repository,
		string $tag
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		$tag = trim($tag);

		if (
			$repository === ''
			|| $tag === ''
			|| strlen($tag) > 255
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-release-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $tag
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/releases/tags/'
			. rawurlencode($tag)
		);

		if ($response === []) {
			return [];
		}

		$author = is_array(
			$response['author']
				?? null
		)
			? $response['author']
			: [];

		$assets = [];

		foreach (
			(array) (
				$response['assets']
					?? []
			)
			as $asset
		) {
			if (!is_array($asset)) {
				continue;
			}

			$name = trim(
				(string) (
					$asset['name']
						?? ''
				)
			);

			if ($name === '') {
				continue;
			}

			$assets[] = [
				'name' => $name,
				'url' => trim(
					(string) (
						$asset['browser_download_url']
							?? ''
					)
				),
				'content_type' => trim(
					(string) (
						$asset['content_type']
							?? ''
					)
				),
				'size' => (int) (
					$asset['size']
						?? 0
				),
				'download_count' => (int) (
					$asset['download_count']
						?? 0
				),
				'created_at' => trim(
					(string) (
						$asset['created_at']
							?? ''
					)
				),
				'updated_at' => trim(
					(string) (
						$asset['updated_at']
							?? ''
					)
				),
			];
		}

		$result = [
			'id' => (int) (
				$response['id']
					?? 0
			),
			'name' => trim(
				(string) (
					$response['name']
						?? ''
				)
			),
			'tag_name' => trim(
				(string) (
					$response['tag_name']
						?? $tag
				)
			),
			'target_commitish' => trim(
				(string) (
					$response['target_commitish']
						?? ''
				)
			),
			'body' => trim(
				(string) (
					$response['body']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'draft' => (bool) (
				$response['draft']
					?? false
			),
			'prerelease' => (bool) (
				$response['prerelease']
					?? false
			),
			'created_at' => trim(
				(string) (
					$response['created_at']
						?? ''
				)
			),
			'published_at' => trim(
				(string) (
					$response['published_at']
						?? ''
				)
			),
			'author' => [
				'login' => trim(
					(string) (
						$author['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$author['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$author['html_url']
							?? ''
					)
				),
			],
			'assets' => $assets,
			'tarball_url' => trim(
				(string) (
					$response['tarball_url']
						?? ''
				)
			),
			'zipball_url' => trim(
				(string) (
					$response['zipball_url']
						?? ''
				)
			),
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getGist(
		string $gistId
	): array {
		$gistId = trim($gistId);

		if (
			$gistId === ''
			|| strlen($gistId) > 128
			|| !preg_match(
				'/^[A-Fa-f0-9]+$/',
				$gistId
			)
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-gist-'
			. hash(
				'sha256',
				strtolower($gistId)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/gists/'
			. rawurlencode($gistId)
		);

		if ($response === []) {
			return [];
		}

		$owner = is_array(
			$response['owner']
				?? null
		)
			? $response['owner']
			: [];

		$files = [];

		foreach (
			(array) (
				$response['files']
					?? []
			)
			as $filename => $file
		) {
			if (!is_array($file)) {
				continue;
			}

			$name = trim(
				(string) (
					$file['filename']
						?? $filename
				)
			);

			if ($name === '') {
				continue;
			}

			$content = (string) (
				$file['content']
					?? ''
			);

			if (
				str_contains(
					$content,
					"\0"
				)
			) {
				continue;
			}

			if (strlen($content) > 524288) {
				$content = substr(
					$content,
					0,
					524288
				);
			}

			$files[] = [
				'filename' => $name,
				'type' => trim(
					(string) (
						$file['type']
							?? ''
					)
				),
				'language' => trim(
					(string) (
						$file['language']
							?? ''
					)
				),
				'raw_url' => trim(
					(string) (
						$file['raw_url']
							?? ''
					)
				),
				'size' => (int) (
					$file['size']
						?? strlen($content)
				),
				'truncated' => (bool) (
					$file['truncated']
						?? false
				),
				'content' => $content,
			];
		}

		$result = [
			'id' => trim(
				(string) (
					$response['id']
						?? $gistId
				)
			),
			'description' => trim(
				(string) (
					$response['description']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'public' => (bool) (
				$response['public']
					?? true
			),
			'created_at' => trim(
				(string) (
					$response['created_at']
						?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$response['updated_at']
						?? ''
				)
			),
			'comments' => (int) (
				$response['comments']
					?? 0
			),
			'owner' => [
				'login' => trim(
					(string) (
						$owner['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$owner['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$owner['html_url']
							?? ''
					)
				),
			],
			'files' => $files,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getTree(
		string $repository,
		string $ref,
		string $path = ''
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		$ref = trim($ref);

		$path = trim(
			$path,
			"/ \t\n\r\0\x0B"
		);

		if (
			$repository === ''
			|| $ref === ''
			|| strlen($ref) > 255
			|| strlen($path) > 4096
			|| str_contains($path, "\0")
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-tree-'
			. hash(
				'sha256',
				$repository
					. '|'
					. $ref
					. '|'
					. $path
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$url =
			'https://api.github.com/repos/'
			. $repository
			. '/contents';

		if ($path !== '') {
			$url .= '/'
				. implode(
					'/',
					array_map(
						'rawurlencode',
						explode(
							'/',
							$path
						)
					)
				);
		}

		$url .= '?ref='
			. rawurlencode($ref);

		$response = $this->request(
			$url
		);

		if (
			$response === []
			|| !array_is_list($response)
		) {
			return [];
		}

		$items = [];

		foreach ($response as $item) {
			if (!is_array($item)) {
				continue;
			}

			$type = trim(
				(string) (
					$item['type']
						?? ''
				)
			);

			if (
				$type !== 'dir'
				&& $type !== 'file'
				&& $type !== 'symlink'
				&& $type !== 'submodule'
			) {
				continue;
			}

			$name = trim(
				(string) (
					$item['name']
						?? ''
				)
			);

			if ($name === '') {
				continue;
			}

			$items[] = [
				'name' => $name,
				'path' => trim(
					(string) (
						$item['path']
							?? ''
					)
				),
				'type' => $type,
				'sha' => trim(
					(string) (
						$item['sha']
							?? ''
					)
				),
				'size' => (int) (
					$item['size']
						?? 0
				),
				'html_url' => trim(
					(string) (
						$item['html_url']
							?? ''
					)
				),
				'download_url' => trim(
					(string) (
						$item['download_url']
							?? ''
					)
				),
			];
		}

		usort(
			$items,
			static function (
				array $a,
				array $b
			): int {
				$aDirectory = $a['type'] === 'dir';
				$bDirectory = $b['type'] === 'dir';

				if ($aDirectory !== $bDirectory) {
					return $aDirectory
						? -1
						: 1;
				}

				return strcasecmp(
					(string) $a['name'],
					(string) $b['name']
				);
			}
		);

		$result = [
			'ref' => $ref,
			'path' => $path,
			'items' => $items,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getCompare(
		string $repository,
		string $base,
		string $head
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		$base = trim($base);
		$head = trim($head);

		if (
			$repository === ''
			|| $base === ''
			|| $head === ''
			|| strlen($base) > 255
			|| strlen($head) > 255
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-compare-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $base
						. '|'
						. $head
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
				. $repository
				. '/compare/'
				. rawurlencode($base)
				. '...'
				. rawurlencode($head)
		);

		if ($response === []) {
			return [];
		}

		$files = [];

		foreach (
			(array) (
				$response['files']
					?? []
			)
			as $file
		) {
			if (!is_array($file)) {
				continue;
			}

			$filename = trim(
				(string) (
					$file['filename']
						?? ''
				)
			);

			if ($filename === '') {
				continue;
			}

			$files[] = [
				'filename' => $filename,
				'status' => trim(
					(string) (
						$file['status']
							?? ''
					)
				),
				'additions' => (int) (
					$file['additions']
						?? 0
				),
				'deletions' => (int) (
					$file['deletions']
						?? 0
				),
				'changes' => (int) (
					$file['changes']
						?? 0
				),
				'blob_url' => trim(
					(string) (
						$file['blob_url']
							?? ''
					)
				),
				'raw_url' => trim(
					(string) (
						$file['raw_url']
							?? ''
					)
				),
				'patch' => (string) (
					$file['patch']
						?? ''
				),
			];
		}

		$commits = [];

		foreach (
			(array) (
				$response['commits']
					?? []
			)
			as $commitItem
		) {
			if (!is_array($commitItem)) {
				continue;
			}

			$commitData = is_array(
				$commitItem['commit']
					?? null
			)
				? $commitItem['commit']
				: [];

			$authorData = is_array(
				$commitData['author']
					?? null
			)
				? $commitData['author']
				: [];

			$githubAuthor = is_array(
				$commitItem['author']
					?? null
			)
				? $commitItem['author']
				: [];

			$sha = trim(
				(string) (
					$commitItem['sha']
						?? ''
				)
			);

			$message = trim(
				(string) (
					$commitData['message']
						?? ''
				)
			);

			$messageParts = preg_split(
				'/\R/',
				$message,
				2
			);

			if (
				is_array($messageParts)
				&& isset($messageParts[0])
			) {
				$message = trim(
					(string) $messageParts[0]
				);
			}

			$commits[] = [
				'sha' => $sha,
				'short_sha' => substr(
					$sha,
					0,
					7
				),
				'message' => $message,
				'url' => trim(
					(string) (
						$commitItem['html_url']
							?? ''
					)
				),
				'author_name' => trim(
					(string) (
						$authorData['name']
							?? ''
					)
				),
				'author_date' => trim(
					(string) (
						$authorData['date']
							?? ''
					)
				),
				'author_login' => trim(
					(string) (
						$githubAuthor['login']
							?? ''
					)
				),
				'author_avatar' => trim(
					(string) (
						$githubAuthor['avatar_url']
							?? ''
					)
				),
			];
		}

		$result = [
			'status' => trim(
				(string) (
					$response['status']
						?? ''
				)
			),
			'ahead_by' => (int) (
				$response['ahead_by']
					?? 0
			),
			'behind_by' => (int) (
				$response['behind_by']
					?? 0
			),
			'total_commits' => (int) (
				$response['total_commits']
					?? count($commits)
			),
			'base_commit' => trim(
				(string) (
					$response['base_commit']['sha']
						?? ''
				)
			),
			'merge_base_commit' => trim(
				(string) (
					$response['merge_base_commit']['sha']
						?? ''
				)
			),
			'html_url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'diff_url' => trim(
				(string) (
					$response['diff_url']
						?? ''
				)
			),
			'patch_url' => trim(
				(string) (
					$response['patch_url']
						?? ''
				)
			),
			'commits' => $commits,
			'files' => $files,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getIssueComment(
		string $repository,
		int $commentId
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if (
			$repository === ''
			|| $commentId <= 0
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-issue-comment-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $commentId
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
				. $repository
				. '/issues/comments/'
				. $commentId
		);

		if ($response === []) {
			return [];
		}

		$user = is_array(
			$response['user']
				?? null
		)
			? $response['user']
			: [];

		$result = [
			'id' => (int) (
				$response['id']
					?? $commentId
			),
			'body' => trim(
				(string) (
					$response['body']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'created_at' => trim(
				(string) (
					$response['created_at']
						?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$response['updated_at']
						?? ''
				)
			),
			'author_association' => trim(
				(string) (
					$response['author_association']
						?? ''
				)
			),
			'author' => [
				'login' => trim(
					(string) (
						$user['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$user['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$user['html_url']
							?? ''
					)
				),
			],
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getDiscussionComment(
		string $repository,
		int $commentId
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if (
			$repository === ''
			|| $commentId <= 0
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-discussion-comment-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $commentId
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$query = <<<'GRAPHQL'
		query(
			$id: ID!
		) {
			node(id: $id) {
				... on DiscussionComment {
					id
					body
					url
					createdAt
					updatedAt
					isAnswer

					author {
						login
						avatarUrl
						url
					}
				}
			}
		}
		GRAPHQL;

		// REST espone l'ID numerico, GraphQL richiede il node ID.
		$response = $this->request(
			'https://api.github.com/repos/'
				. $repository
				. '/discussions/comments/'
				. $commentId
		);

		if ($response === []) {
			return [];
		}

		$user = is_array(
			$response['user']
				?? null
		)
			? $response['user']
			: [];

		$result = [
			'id' => (int) (
				$response['id']
					?? $commentId
			),
			'body' => trim(
				(string) (
					$response['body']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'created_at' => trim(
				(string) (
					$response['created_at']
						?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$response['updated_at']
						?? ''
				)
			),
			'author_association' => trim(
				(string) (
					$response['author_association']
						?? ''
				)
			),
			'author' => [
				'login' => trim(
					(string) (
						$user['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$user['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$user['html_url']
							?? ''
					)
				),
			],
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getWorkflowRun(
		string $repository,
		int $runId
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		if (
			$repository === ''
			|| $runId <= 0
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-workflow-run-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $runId
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
				. $repository
				. '/actions/runs/'
				. $runId
		);

		if ($response === []) {
			return [];
		}

		$actor = is_array(
			$response['actor']
				?? null
		)
			? $response['actor']
			: [];

		$headCommit = is_array(
			$response['head_commit']
				?? null
		)
			? $response['head_commit']
			: [];

		$result = [
			'id' => (int) (
				$response['id']
					?? $runId
			),
			'name' => trim(
				(string) (
					$response['name']
						?? ''
				)
			),
			'display_title' => trim(
				(string) (
					$response['display_title']
						?? ''
				)
			),
			'event' => trim(
				(string) (
					$response['event']
						?? ''
				)
			),
			'status' => trim(
				(string) (
					$response['status']
						?? ''
				)
			),
			'conclusion' => trim(
				(string) (
					$response['conclusion']
						?? ''
				)
			),
			'workflow_id' => (int) (
				$response['workflow_id']
					?? 0
			),
			'run_number' => (int) (
				$response['run_number']
					?? 0
			),
			'run_attempt' => (int) (
				$response['run_attempt']
					?? 0
			),
			'head_branch' => trim(
				(string) (
					$response['head_branch']
						?? ''
				)
			),
			'head_sha' => trim(
				(string) (
					$response['head_sha']
						?? ''
				)
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'created_at' => trim(
				(string) (
					$response['created_at']
						?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$response['updated_at']
						?? ''
				)
			),
			'run_started_at' => trim(
				(string) (
					$response['run_started_at']
						?? ''
				)
			),
			'actor' => [
				'login' => trim(
					(string) (
						$actor['login']
							?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$actor['avatar_url']
							?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$actor['html_url']
							?? ''
					)
				),
			],
			'head_commit' => [
				'id' => trim(
					(string) (
						$headCommit['id']
							?? ''
					)
				),
				'message' => trim(
					(string) (
						$headCommit['message']
							?? ''
					)
				),
				'timestamp' => trim(
					(string) (
						$headCommit['timestamp']
							?? ''
					)
				),
				'author_name' => trim(
					(string) (
						$headCommit['author']['name']
							?? ''
					)
				),
			],
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getFileContent(
		string $repository,
		string $ref,
		string $path
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		$ref = trim($ref);
		$path = trim(
			$path,
			"/ \t\n\r\0\x0B"
		);

		if (
			$repository === ''
			|| $ref === ''
			|| $path === ''
		) {
			return [];
		}

		// Limiti sui parametri usati nelle richieste GitHub.
		if (
			str_contains($path, "\0")
			|| strlen($path) > 4096
			|| strlen($ref) > 255
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-file-'
			. hash(
				'sha256',
				$repository
					. '|'
					. $ref
					. '|'
					. $path
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/contents/'
			. implode(
				'/',
				array_map(
					'rawurlencode',
					explode(
						'/',
						$path
					)
				)
			)
			. '?ref='
			. rawurlencode($ref)
		);

		if (
			$response === []
			|| ($response['type'] ?? '') !== 'file'
		) {
			return [];
		}

		$encoding = strtolower(
			trim(
				(string) (
					$response['encoding']
					?? ''
				)
			)
		);

		$encodedContent = (string) (
			$response['content']
				?? ''
		);

		if (
			$encoding !== 'base64'
			|| $encodedContent === ''
		) {
			return [];
		}

		$decodedContent = base64_decode(
			preg_replace(
				'/\s+/',
				'',
				$encodedContent
			) ?? '',
			true
		);

		if ($decodedContent === false) {
			return [];
		}

		// Limite massimo per la preview: 512 KiB.
		if (strlen($decodedContent) > 524288) {
			return [];
		}

		// Esclude contenuti binari.
		if (str_contains($decodedContent, "\0")) {
			return [];
		}

		$result = [
			'name' => trim(
				(string) (
					$response['name']
						?? basename($path)
				)
			),
			'path' => trim(
				(string) (
					$response['path']
						?? $path
				)
			),
			'sha' => trim(
				(string) (
					$response['sha']
						?? ''
				)
			),
			'size' => (int) (
				$response['size']
					?? strlen($decodedContent)
			),
			'html_url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'content' => $decodedContent,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	public function getCommit(
		string $repository,
		string $sha
	): array {
		$repository = $this->normalizeRepository(
			$repository
		);

		$sha = trim($sha);

		if (
			$repository === ''
			|| $sha === ''
			|| strlen($sha) > 64
			|| !preg_match(
				'/^[A-Fa-f0-9]+$/',
				$sha
			)
		) {
			return [];
		}

		$cacheDirectory = __DIR__
			. '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/github-commit-'
			. hash(
				'sha256',
				strtolower(
					$repository
						. '|'
						. $sha
				)
			)
			. '.json';

		if (
			is_file($cacheFile)
			&& (
				time()
					- (int) filemtime($cacheFile)
			) < self::CACHE_TTL
		) {
			$cachedJson = file_get_contents(
				$cacheFile
			);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$response = $this->request(
			'https://api.github.com/repos/'
			. $repository
			. '/commits/'
			. rawurlencode($sha)
		);

		if ($response === []) {
			return [];
		}

		$commit = is_array(
			$response['commit']
				?? null
		)
			? $response['commit']
			: [];

		$author = is_array(
			$commit['author']
				?? null
		)
			? $commit['author']
			: [];

		$githubAuthor = is_array(
			$response['author']
				?? null
		)
			? $response['author']
			: [];

		$stats = is_array(
			$response['stats']
				?? null
		)
			? $response['stats']
			: [];

		$files = [];

		foreach (
			(array) (
				$response['files']
					?? []
			)
			as $file
		) {
			if (!is_array($file)) {
				continue;
			}

			$filename = trim(
				(string) (
					$file['filename']
						?? ''
				)
			);

			if ($filename === '') {
				continue;
			}

			$files[] = [
				'filename' => $filename,
				'status' => trim(
					(string) (
						$file['status']
							?? ''
					)
				),
				'additions' => (int) (
					$file['additions']
						?? 0
				),
				'deletions' => (int) (
					$file['deletions']
						?? 0
				),
				'changes' => (int) (
					$file['changes']
						?? 0
				),
				'blob_url' => trim(
					(string) (
						$file['blob_url']
							?? ''
					)
				),
				'raw_url' => trim(
					(string) (
						$file['raw_url']
							?? ''
					)
				),
				'patch' => (string) (
					$file['patch']
						?? ''
				),
			];
		}

		$result = [
			'sha' => trim(
				(string) (
					$response['sha']
						?? $sha
				)
			),
			'short_sha' => substr(
				trim(
					(string) (
						$response['sha']
							?? $sha
					)
				),
				0,
				7
			),
			'url' => trim(
				(string) (
					$response['html_url']
						?? ''
				)
			),
			'message' => trim(
				(string) (
					$commit['message']
						?? ''
				)
			),
			'author_name' => trim(
				(string) (
					$author['name']
						?? ''
				)
			),
			'author_date' => trim(
				(string) (
					$author['date']
						?? ''
				)
			),
			'author_login' => trim(
				(string) (
					$githubAuthor['login']
						?? ''
				)
			),
			'author_avatar' => trim(
				(string) (
					$githubAuthor['avatar_url']
						?? ''
				)
			),
			'stats' => [
				'total' => (int) (
					$stats['total']
						?? 0
				),
				'additions' => (int) (
					$stats['additions']
						?? 0
				),
				'deletions' => (int) (
					$stats['deletions']
						?? 0
				),
			],
			'files' => $files,
		];

		$this->writeCache(
			$cacheFile,
			$result
		);

		return $result;
	}

	private function normalizeRepositoryData(
		array $repository
	): array {
		$owner = is_array(
			$repository['owner'] ?? null
		)
			? $repository['owner']
			: [];

		$license = is_array(
			$repository['license'] ?? null
		)
			? $repository['license']
			: [];

		return [
			'name' => trim(
				(string) (
					$repository['name']
					?? ''
				)
			),
			'full_name' => trim(
				(string) (
					$repository['full_name']
					?? ''
				)
			),
			'description' => trim(
				(string) (
					$repository['description']
					?? ''
				)
			),
			'html_url' => trim(
				(string) (
					$repository['html_url']
					?? ''
				)
			),
			'homepage' => trim(
				(string) (
					$repository['homepage']
					?? ''
				)
			),
			'visibility' => trim(
				(string) (
					$repository['visibility']
					?? ''
				)
			),
			'private' => (bool) (
				$repository['private']
				?? false
			),
			'fork' => (bool) (
				$repository['fork']
				?? false
			),
			'archived' => (bool) (
				$repository['archived']
				?? false
			),
			'disabled' => (bool) (
				$repository['disabled']
				?? false
			),
			'language' => trim(
				(string) (
					$repository['language']
					?? ''
				)
			),
			'default_branch' => trim(
				(string) (
					$repository['default_branch']
					?? ''
				)
			),
			'stars' => (int) (
				$repository['stargazers_count']
				?? 0
			),
			'watchers' => (int) (
				$repository['subscribers_count']
				?? $repository['watchers_count']
				?? 0
			),
			'forks' => (int) (
				$repository['forks_count']
				?? 0
			),
			'open_issues' => (int) (
				$repository['open_issues_count']
				?? 0
			),
			'size' => (int) (
				$repository['size']
				?? 0
			),
			'created_at' => trim(
				(string) (
					$repository['created_at']
					?? ''
				)
			),
			'updated_at' => trim(
				(string) (
					$repository['updated_at']
					?? ''
				)
			),
			'pushed_at' => trim(
				(string) (
					$repository['pushed_at']
					?? ''
				)
			),
			'topics' => is_array(
				$repository['topics'] ?? null
			)
				? array_values(
					$repository['topics']
				)
				: [],
			'license' => [
				'name' => trim(
					(string) (
						$license['name']
						?? ''
					)
				),
				'spdx_id' => trim(
					(string) (
						$license['spdx_id']
						?? ''
					)
				),
			],
			'owner' => [
				'login' => trim(
					(string) (
						$owner['login']
						?? ''
					)
				),
				'avatar_url' => trim(
					(string) (
						$owner['avatar_url']
						?? ''
					)
				),
				'html_url' => trim(
					(string) (
						$owner['html_url']
						?? ''
					)
				),
			],
		];
	}

	private function normalizeBranches(
		array $branches,
		string $currentBranch
	): array {
		if (!array_is_list($branches)) {
			return [];
		}

		$result = [];

		foreach ($branches as $branch) {

			if (!is_array($branch)) {
				continue;
			}

			$name = trim(
				(string) (
					$branch['name']
					?? ''
				)
			);

			if ($name === '') {
				continue;
			}

			$result[] = [
				'name' => $name,
				'protected' => (bool) (
					$branch['protected']
					?? false
				),
				'current' => (
					$name === $currentBranch
				),
			];
		}

		usort(
			$result,
			static function (
				array $a,
				array $b
			): int {

				if ($a['current']) {
					return -1;
				}

				if ($b['current']) {
					return 1;
				}

				return strcasecmp(
					$a['name'],
					$b['name']
				);
			}
		);

		return $result;
	}

	private function normalizeCommits(
		array $commits
	): array {
		if (!array_is_list($commits)) {
			return [];
		}

		$result = [];

		foreach ($commits as $item) {
			if (!is_array($item)) {
				continue;
			}

			$commit = is_array(
				$item['commit'] ?? null
			)
				? $item['commit']
				: [];

			$author = is_array(
				$commit['author'] ?? null
			)
				? $commit['author']
				: [];

			$githubAuthor = is_array(
				$item['author'] ?? null
			)
				? $item['author']
				: [];

			$message = trim(
				(string) (
					$commit['message']
					?? ''
				)
			);

			$messageParts = preg_split(
				'/\R/',
				$message,
				2
			);

			if (
				is_array($messageParts)
				&& isset($messageParts[0])
			) {
				$message = trim(
					(string) $messageParts[0]
				);
			}

			$sha = trim(
				(string) (
					$item['sha']
					?? ''
				)
			);

			$result[] = [
				'sha' => $sha,
				'short_sha' => substr(
					$sha,
					0,
					7
				),
				'message' => $message,
				'url' => trim(
					(string) (
						$item['html_url']
						?? ''
					)
				),
				'author_name' => trim(
					(string) (
						$author['name']
						?? ''
					)
				),
				'author_date' => trim(
					(string) (
						$author['date']
						?? ''
					)
				),
				'author_login' => trim(
					(string) (
						$githubAuthor['login']
						?? ''
					)
				),
				'author_avatar' => trim(
					(string) (
						$githubAuthor['avatar_url']
						?? ''
					)
				),
			];
		}

		return $result;
	}

	private function normalizeReleases(
		array $releases
	): array {
		$result = [
			'stable' => [],
			'beta' => [],
			'nightly' => [],
		];

		if (!array_is_list($releases)) {
			return $result;
		}

		foreach ($releases as $release) {
			if (!is_array($release)) {
				continue;
			}

			if (
				(bool) (
					$release['draft']
						?? false
				)
			) {
				continue;
			}

			$tagName = trim(
				(string) (
					$release['tag_name']
						?? ''
				)
			);

			if ($tagName === '') {
				continue;
			}

			$normalizedRelease = [
				'name' => trim(
					(string) (
						$release['name']
							?? ''
					)
				),
				'tag_name' => $tagName,
				'html_url' => trim(
					(string) (
						$release['html_url']
							?? ''
					)
				),
				'published_at' => trim(
					(string) (
						$release['published_at']
							?? ''
					)
				),
				'prerelease' => (bool) (
					$release['prerelease']
						?? false
				),
				'draft' => false,
			];

			if (
				strtolower($tagName) === 'nightly'
			) {
				if ($result['nightly'] === []) {
					$result['nightly'] =
						$normalizedRelease;
				}

				continue;
			}

			if (
				(bool) (
					$release['prerelease']
						?? false
				)
			) {
				if ($result['beta'] === []) {
					$result['beta'] =
						$normalizedRelease;
				}

				continue;
			}

			if ($result['stable'] === []) {
				$result['stable'] =
					$normalizedRelease;
			}

			if (
				$result['stable'] !== []
				&& $result['beta'] !== []
				&& $result['nightly'] !== []
			) {
				break;
			}
		}

		return $result;
	}

	private function normalizeLanguages(
		array $languages
	): array {
		if ($languages === []) {
			return [];
		}

		$total = array_sum(
			array_map(
				'intval',
				$languages
			)
		);

		if ($total <= 0) {
			return [];
		}

		$result = [];

		foreach ($languages as $language => $bytes) {
			$result[] = [
				'name' => (string) $language,
				'bytes' => (int) $bytes,
				'percent' => round(
					(
						(int) $bytes
						/ $total
					) * 100,
					1
				),
			];
		}

		usort(
			$result,
			static fn(
				array $a,
				array $b
			): int =>
				$b['bytes']
				<=> $a['bytes']
		);

		return array_slice(
			$result,
			0,
			5
		);
	}

	private function normalizePullRequests(
		array $pullRequests
	): array {
		if (!array_is_list($pullRequests)) {
			return [];
		}

		$result = [];

		foreach ($pullRequests as $item) {
			if (!is_array($item)) {
				continue;
			}

			$user = is_array(
				$item['user'] ?? null
			)
				? $item['user']
				: [];

			$result[] = [
				'number' => (int) (
					$item['number']
					?? 0
				),
				'title' => trim(
					(string) (
						$item['title']
						?? ''
					)
				),
				'url' => trim(
					(string) (
						$item['html_url']
						?? ''
					)
				),
				'created_at' => trim(
					(string) (
						$item['created_at']
						?? ''
					)
				),
				'updated_at' => trim(
					(string) (
						$item['updated_at']
						?? ''
					)
				),
				'user' => trim(
					(string) (
						$user['login']
						?? ''
					)
				),
			];
		}

		return $result;
	}

	private function normalizeIssues(
		array $issues
	): array {
		if (!array_is_list($issues)) {
			return [];
		}

		$result = [];

		foreach ($issues as $item) {
			if (
				!is_array($item)
				|| isset($item['pull_request'])
			) {
				continue;
			}

			$user = is_array(
				$item['user'] ?? null
			)
				? $item['user']
				: [];

			$labels = [];

			foreach (
				(array) (
					$item['labels']
					?? []
				)
				as $label
			) {
				if (!is_array($label)) {
					continue;
				}

				$name = trim(
					(string) (
						$label['name']
						?? ''
					)
				);

				if ($name !== '') {
					$labels[] = $name;
				}
			}

			$result[] = [
				'number' => (int) (
					$item['number']
					?? 0
				),
				'title' => trim(
					(string) (
						$item['title']
						?? ''
					)
				),
				'url' => trim(
					(string) (
						$item['html_url']
						?? ''
					)
				),
				'created_at' => trim(
					(string) (
						$item['created_at']
						?? ''
					)
				),
				'updated_at' => trim(
					(string) (
						$item['updated_at']
						?? ''
					)
				),
				'user' => trim(
					(string) (
						$user['login']
						?? ''
					)
				),
				'labels' => $labels,
			];
		}

		return $result;
	}

	private function writeCache(
		string $cacheFile,
		array $data
	): void {
		$encodedData = json_encode(
			$data,
			JSON_UNESCAPED_UNICODE
				| JSON_UNESCAPED_SLASHES
		);

		if ($encodedData === false) {
			return;
		}

		$temporaryFile = $cacheFile
			. '.tmp';

		if (
			file_put_contents(
				$temporaryFile,
				$encodedData,
				LOCK_EX
			) === false
		) {
			return;
		}

		rename(
			$temporaryFile,
			$cacheFile
		);
	}

	private function graphqlRequest(
		string $query,
		array $variables = []
	): array {
		$token = trim(
			(string) $this->settings->get(
				'github_api_token',
				''
			)
		);

		if ($token === '') {
			return [];
		}

		$payload = json_encode(
			[
				'query' => $query,
				'variables' => $variables,
			],
			JSON_UNESCAPED_UNICODE
				| JSON_UNESCAPED_SLASHES
		);

		if ($payload === false) {
			return [];
		}

		$headers = [
			'Accept: application/vnd.github+json',
			'Content-Type: application/json',
			'Authorization: Bearer ' . $token,
			'User-Agent: Monoverse/1.0',
		];

		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'timeout' => 8,
				'ignore_errors' => true,
				'header' => $headers,
				'content' => $payload,
			],
		]);

		$responseBody = @file_get_contents(
			'https://api.github.com/graphql',
			false,
			$context
		);

		if (
			$responseBody === false
			|| $responseBody === ''
		) {
			return [];
		}

		$decoded = json_decode(
			$responseBody,
			true
		);

		return is_array($decoded)
			? $decoded
			: [];
	}

	private function request(
		string $url
	): array {
		$headers = [
			'Accept: application/vnd.github+json',
			'X-GitHub-Api-Version: 2022-11-28',
			'User-Agent: Monoverse/1.0',
		];

		$token = trim(
			(string) $this->settings->get(
				'github_api_token',
				''
			)
		);

		if ($token !== '') {
			$headers[] = 'Authorization: Bearer '
				. $token;
		}

		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'timeout' => 8,
				'ignore_errors' => true,
				'header' => $headers,
			],
		]);

		$responseBody = @file_get_contents(
			$url,
			false,
			$context
		);

		if (
			$responseBody === false
			|| $responseBody === ''
		) {
			return [];
		}

		$decoded = json_decode(
			$responseBody,
			true
		);

		return is_array($decoded)
			? $decoded
			: [];
	}
}
