<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class LinkService
{
	public function __construct(
		private Database $database,
		private GitHubService $github
	) {
	}
	
	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function extract(string $text): array
	{
		if (!preg_match_all('~https?://[^\s<>"\'()]+~i', $text, $matches)) {
			return [];
		}

		$links = [];

		foreach (array_unique($matches[0]) as $url) {
			$provider = $this->detectProvider($url);

			$links[] = [
				'url'         => $url,
				'provider'    => $provider,
				'type'        => $this->detectType($provider),
				'title'       => null,
				'description' => null,
				'image'       => null,
				'embed'       => null,
				'site_name'   => null,
				'favicon'     => null,
			];
		}

		return $links;
	}

	/**
	 * @param array<int, array<string, mixed>> $links
	 * @return array<int, array<string, mixed>>
	 */
	public function enrich(array $links): array
	{
		foreach ($links as &$link) {
			$cached = $this->loadFromCache($link['url']);
			
			if (
				$cached !== null
				&& ($link['provider'] ?? '') !== 'github'
			) {
				$link = $this->decorateProvider(
					array_merge($link, $cached)
				);
				continue;
			}
			
			switch ($link['provider']) {

				case 'youtube':
					$link = $this->enrichYouTube($link);
					$link = $this->decorateProvider($link);
					$this->saveToCache($link);
					break;
				
				case 'github':
					$link = $this->enrichGitHub($link);
					break;
				
				default:
					$link = $this->enrichWebsite($link);
					$link = $this->decorateProvider($link);
					$this->saveToCache($link);
					break;
			}
		}

		unset($link);

		return $links;
	}

	private function detectProvider(string $url): string
	{
		$host = strtolower((string) parse_url($url, PHP_URL_HOST));

		$host = preg_replace('/^www\./', '', $host);

		return match (true) {
			str_contains($host, 'youtube.com'),
			str_contains($host, 'youtu.be')      => 'youtube',

			str_contains($host, 'vimeo.com')     => 'vimeo',

			str_contains($host, 'github.com'),
			str_contains($host, 'gist.github.com') => 'github',

			str_contains($host, 'gitlab.com')    => 'gitlab',

			str_contains($host, 'x.com'),
			str_contains($host, 'twitter.com')   => 'x',

			str_contains($host, 'bsky.app')      => 'bluesky',

			str_contains($host, 'mastodon')      => 'mastodon',

			str_contains($host, 'spotify.com')   => 'spotify',

			str_contains($host, 'twitch.tv')     => 'twitch',

			default                              => 'website',
		};
	}

	private function detectType(string $provider): string
	{
		return match ($provider) {
			'youtube',
			'vimeo'      => 'video',

			'spotify'    => 'audio',

			default      => 'link',
		};
	}
	
	private function enrichGitHubGist(
		array $link,
		array $parts
	): array {
		$segments = array_values(
			array_filter(
				explode(
					'/',
					trim(
						(string) (
							$parts['path']
							?? ''
						),
						'/'
					)
				),
				static fn (string $segment): bool =>
					$segment !== ''
			)
		);
	
		if (count($segments) < 2) {
			return $this->enrichWebsite($link);
		}
	
		$owner = rawurldecode(
			(string) $segments[0]
		);
	
		$gistId = rawurldecode(
			(string) $segments[1]
		);
	
		$gist = $this->github->getGist(
			$gistId
		);
	
		if ($gist === []) {
			return $this->enrichWebsite($link);
		}
	
		$link['github'] = [
			'kind' => 'gist',
			'owner' => $owner,
			'gist_id' => $gistId,
			'full_name' => $owner . '/' . $gistId,
			'gist' => $gist,
		];
	
		$link = $this->enrichWebsite($link);
		$link = $this->decorateProvider($link);
	
		return $link;
	}
	
	private function enrichGitHub(array $link): array
	{
		$url = (string) ($link['url'] ?? '');
	
		$parts = parse_url($url);
	
		if (
			!is_array($parts)
			|| empty($parts['host'])
			|| empty($parts['path'])
		) {
			return $link;
		}
	
		$host = strtolower(
			(string) $parts['host']
		);
		
		if (
			$host === 'gist.github.com'
			|| $host === 'www.gist.github.com'
		) {
			return $this->enrichGitHubGist($link, $parts);
		}
	
		if (
			$host !== 'github.com'
			&& $host !== 'www.github.com'
		) {
			return $link;
		}
	
		$segments = array_values(
			array_filter(
				explode(
					'/',
					trim(
						(string) $parts['path'],
						'/'
					)
				),
				static fn (string $segment): bool =>
					$segment !== ''
			)
		);
	
		if (count($segments) < 2) {
			return $this->enrichWebsite($link);
		}
	
		$owner = rawurldecode(
			$segments[0]
		);
	
		$repository = rawurldecode(
			$segments[1]
		);
	
		$github = [
			'kind' => 'repository',
			'owner' => $owner,
			'repository' => $repository,
			'full_name' => $owner . '/' . $repository,
			'ref' => null,
			'path' => null,
			'line_start' => null,
			'line_end' => null,
			'number' => null,
			'sha' => null,
		];
	
		$resource = strtolower(
			(string) ($segments[2] ?? '')
		);
	
		switch ($resource) {
	
			case 'blob':
				$github['kind'] = 'blob';
	
				$github['ref'] = isset($segments[3])
					? rawurldecode($segments[3])
					: null;
	
				if (count($segments) > 4) {
					$github['path'] = implode(
						'/',
						array_map(
							'rawurldecode',
							array_slice(
								$segments,
								4
							)
						)
					);
				}
	
				break;
			case 'compare':
			$github['kind'] = 'compare';
			
			$compareSpec = isset($segments[3])
				? rawurldecode(
					(string) $segments[3]
				)
				: '';
			
			if (
				$compareSpec !== ''
				&& str_contains(
					$compareSpec,
					'...'
				)
			) {
				[
					$github['base'],
					$github['head']
				] = array_pad(
					explode(
						'...',
						$compareSpec,
						2
					),
					2,
					null
				);
			}
			
			break;
			
			case 'tree':
			$github['kind'] = 'tree';
			
			$github['ref'] = isset($segments[3])
				? rawurldecode($segments[3])
				: null;
			
			if (count($segments) > 4) {
				$github['path'] = implode(
					'/',
					array_map(
						'rawurldecode',
						array_slice(
							$segments,
							4
						)
					)
				);
			}
			
			$tree = $this->github->getTree(
			$github['full_name'],
				(string) (
					$github['ref']
						?? ''
				),
				(string) (
					$github['path']
						?? ''
				)
			);
			
			if ($tree !== []) {
				$github['tree'] = $tree;
			}
			
			break;
			case 'actions':
				$actionResource = strtolower(
					(string) ($segments[3] ?? '')
				);
				
				if (
					$actionResource === 'runs'
					&& isset($segments[4])
					&& ctype_digit(
						(string) $segments[4]
					)
				) {
					$github['kind'] = 'workflow_run';
					$github['run_id'] = (int) $segments[4];
				}
				
				break;
			case 'commit':
				$github['kind'] = 'commit';
				$github['sha'] = isset($segments[3])
					? rawurldecode($segments[3])
					: null;
				break;
	
			case 'pull':
				$github['kind'] = 'pull_request';
				$github['number'] = isset($segments[3])
					? (int) $segments[3]
					: null;
				break;
	
			case 'issues':
				$github['kind'] = 'issue';
				$github['number'] = isset($segments[3])
					? (int) $segments[3]
					: null;
				break;
			case 'discussions':
				$github['kind'] = 'discussion';
				$github['number'] = isset($segments[3])
					? (int) $segments[3]
					: null;
				break;
			
			case 'releases':
				$github['kind'] = 'release';
				
				if (
					strtolower(
						(string) ($segments[3] ?? '')
					) === 'tag'
					&& isset($segments[4])
				) {
					$github['tag'] = rawurldecode(
						$segments[4]
					);
				} else {
					$releaseFragment = trim(
						(string) ($parts['fragment'] ?? '')
					);
				
					if (
						str_starts_with(
							$releaseFragment,
							'release-'
						)
					) {
						$github['tag'] = rawurldecode(
							substr(
								$releaseFragment,
								strlen('release-')
							)
						);
					}
				}
				
				break;
		}
		
		if (
			$github['kind'] === 'workflow_run'
			&& !empty($github['run_id'])
			&& (int) $github['run_id'] > 0
		) {
			$workflowRun = $this->github->getWorkflowRun(
				$github['full_name'],
				(int) $github['run_id']
			);
		
			if ($workflowRun !== []) {
				$github['workflow_run'] = $workflowRun;
			}
		}
		
		if (
			$github['kind'] === 'compare'
			&& !empty($github['base'])
			&& !empty($github['head'])
		) {
			$compare = $this->github->getCompare(
				$github['full_name'],
				(string) $github['base'],
				(string) $github['head']
			);
		
			if ($compare !== []) {
				$github['compare'] = $compare;
			}
		}
		
		if (
			$github['kind'] === 'commit'
			&& is_string($github['sha'])
			&& $github['sha'] !== ''
		) {
			$commit = $this->github->getCommit(
				$github['full_name'],
				$github['sha']
			);
		
			if ($commit !== []) {
				$github['commit'] = $commit;
			}
		}
		
		if (
			$github['kind'] === 'pull_request'
			&& is_int($github['number'])
			&& $github['number'] > 0
		) {
			$pullRequest = $this->github->getPullRequest(
				$github['full_name'],
				$github['number']
			);
		
			if ($pullRequest !== []) {
				$github['pull_request'] = $pullRequest;
			}
		}
		
		if (
			$github['kind'] === 'issue'
			&& is_int($github['number'])
			&& $github['number'] > 0
		) {
			$issue = $this->github->getIssue(
				$github['full_name'],
				$github['number']
			);
		
			if ($issue !== []) {
				$github['issue'] = $issue;
			}
		}
		
		if (
			$github['kind'] === 'discussion'
			&& is_int($github['number'])
			&& $github['number'] > 0
		) {
			$discussion = $this->github->getDiscussion(
				$github['full_name'],
				$github['number']
			);
		
			if ($discussion !== []) {
				$github['discussion'] = $discussion;
			}
		}
		
		if (
			$github['kind'] === 'release'
			&& !empty($github['tag'])
		) {
			$release = $this->github->getRelease(
				$github['full_name'],
				(string) $github['tag']
			);
		
			if ($release !== []) {
				$github['release'] = $release;
			}
		}
		
		if (
			$github['kind'] === 'repository'
		) {
			$repositoryPreview = $this->github->getRepositoryPreview(
				$github['full_name']
			);
		
			if ($repositoryPreview !== []) {
				$github['repository_preview'] = $repositoryPreview;
			}
		}

		$fragment = trim(
			(string) ($parts['fragment'] ?? '')
		);
		
		if (
			in_array(
				$github['kind'],
				[
					'issue',
					'pull_request',
					'discussion',
				],
				true
			)
			&& $fragment !== ''
		) {
			if (
				preg_match(
					'/^(?:issuecomment|discussioncomment)-(\d+)$/i',
					$fragment,
					$matches
				)
			) {
				$github['comment_id'] = (int) $matches[1];
			}
		}
		
		if (
			!empty($github['comment_id'])
			&& (int) $github['comment_id'] > 0
		) {
			$comment = [];
		
			if (
				$github['kind'] === 'issue'
				|| $github['kind'] === 'pull_request'
			) {
				$comment = $this->github->getIssueComment(
					$github['full_name'],
					(int) $github['comment_id']
				);
			} elseif (
				$github['kind'] === 'discussion'
			) {
				$comment = $this->github->getDiscussionComment(
					$github['full_name'],
					(int) $github['comment_id']
				);
			}
		
			if ($comment !== []) {
				$github['comment'] = $comment;
			}
		}
		
		if (
			$github['kind'] === 'blob'
			&& preg_match(
				'/^L(\d+)(?:-L(\d+))?$/i',
				$fragment,
				$matches
			)
		) {
			$github['line_start'] = (int) $matches[1];
			$github['line_end'] = isset($matches[2])
				? (int) $matches[2]
				: (int) $matches[1];
		}
		
		if (
			$github['kind'] === 'blob'
			&& is_string($github['ref'])
			&& $github['ref'] !== ''
			&& is_string($github['path'])
			&& $github['path'] !== ''
		) {
			$file = $this->github->getFileContent(
				$github['full_name'],
				$github['ref'],
				$github['path']
			);
		
			if ($file !== []) {
				$content = (string) (
					$file['content']
					?? ''
				);
		
				$lines = preg_split(
					'/\R/u',
					$content
				);
		
				if (!is_array($lines)) {
					$lines = [];
				}
		
				$totalLines = count($lines);
		
				$lineStart = (int) (
					$github['line_start']
					?? 0
				);
		
				$lineEnd = (int) (
					$github['line_end']
					?? 0
				);
		
				if ($totalLines > 0) {
					if ($lineStart <= 0) {
						$lineStart = 1;
					}
		
					if ($lineEnd <= 0) {
						$lineEnd = min(
							$lineStart + 14,
							$totalLines
						);
					}
		
					$lineStart = min(
						$lineStart,
						$totalLines
					);
		
					$lineEnd = max(
						$lineStart,
						min(
							$lineEnd,
							$totalLines
						)
					);
		
					// Massimo 50 righe.
					if (($lineEnd - $lineStart) > 49) {
						$lineEnd = $lineStart + 49;
					}
		
					$snippet = [];
		
					for (
						$lineNumber = $lineStart;
						$lineNumber <= $lineEnd;
						$lineNumber++
					) {
						$snippet[] = [
							'number' => $lineNumber,
							'content' => (string) (
								$lines[$lineNumber - 1]
								?? ''
							),
						];
					}
		
					$github['file'] = [
						'name' => (string) (
							$file['name']
							?? ''
						),
						'path' => (string) (
							$file['path']
							?? $github['path']
						),
						'sha' => (string) (
							$file['sha']
							?? ''
						),
						'size' => (int) (
							$file['size']
							?? 0
						),
						'html_url' => (string) (
							$file['html_url']
							?? ''
						),
						'total_lines' => $totalLines,
						'line_start' => $lineStart,
						'line_end' => $lineEnd,
						'snippet' => $snippet,
					];
				}
			}
		}
	
		$link['github'] = $github;
	
		$link = $this->enrichWebsite($link);
		$link = $this->decorateProvider($link);
	
		return $link;
	}

	 
	private function enrichWebsite(array $link): array
	{
		$html = $this->fetch($link['url']);
	
		if ($html === null) {
			return $link;
		}
	
		libxml_use_internal_errors(true);
	
		$dom = new \DOMDocument();
	
		if (!$dom->loadHTML($html)) {
			return $link;
		}
	
		$xpath = new \DOMXPath($dom);
	
		$link['title'] =
			$this->findMeta($xpath, 'og:title')
			?? $this->findMeta($xpath, 'twitter:title')
			?? $this->findTitle($xpath);
	
		$link['description'] =
			$this->findMeta($xpath, 'og:description')
			?? $this->findMeta($xpath, 'description');
	
		$image = $this->findMeta($xpath, 'og:image');
	
		if ($image !== null) {
			$link['image'] = $this->absoluteUrl($link['url'], $image);
		}
	
		$link['site_name'] = $this->findMeta($xpath, 'og:site_name');
	
		if (empty($link['site_name'])) {
			$host = parse_url($link['url'], PHP_URL_HOST);
	
			if (is_string($host)) {
				$link['site_name'] = preg_replace('/^www\./i', '', $host);
			}
		}
	
		$host = parse_url($link['url'], PHP_URL_HOST);
	
		if (is_string($host)) {
			$link['favicon'] = sprintf(
				'https://www.google.com/s2/favicons?domain=%s&sz=64',
				$host
			);
		}
		
		return $link;
		
	}

	private function enrichYouTube(array $link): array
	{
		$videoId = $this->extractYouTubeId($link['url']);
	
		if ($videoId === null) {
			return $link;
		}
		
		$link = $this->enrichWebsite($link);
	
		$link['image'] = sprintf(
			'https://i.ytimg.com/vi/%s/hqdefault.jpg',
			$videoId
		);
	
		$link['embed'] = sprintf(
			'https://www.youtube-nocookie.com/embed/%s',
			$videoId
		);
		
		return $link;

	}
	private function extractYouTubeId(string $url): ?string
	{
		$parts = parse_url($url);
	
		if (!$parts) {
			return null;
		}
	
		$host = strtolower($parts['host'] ?? '');
	
		// https://youtu.be/VIDEOID
		if (str_contains($host, 'youtu.be')) {
			return trim($parts['path'] ?? '/', '/');
		}
	
		// https://youtube.com/watch?v=VIDEOID
		if (!empty($parts['query'])) {
			parse_str($parts['query'], $query);
	
			if (!empty($query['v'])) {
				return $query['v'];
			}
		}
	
		// https://youtube.com/embed/VIDEOID
		if (!empty($parts['path']) &&
			preg_match('~^/embed/([^/]+)~', $parts['path'], $m)) {
			return $m[1];
		}
	
		return null;
	}
	private function findMeta(\DOMXPath $xpath, string $property): ?string
	{
		$nodes = $xpath->query(
			sprintf(
				'//meta[@property="%1$s"] | //meta[@name="%1$s"]',
				$property
			)
		);
	
		if (!$nodes || $nodes->length === 0) {
			return null;
		}
	
		$content = trim($nodes->item(0)?->getAttribute('content') ?? '');
	
		return $content !== '' ? $content : null;
	}
	
	private function findTitle(\DOMXPath $xpath): ?string
	{
		$nodes = $xpath->query('//title');
	
		if (!$nodes || $nodes->length === 0) {
			return null;
		}
	
		$title = trim($nodes->item(0)?->textContent ?? '');
	
		return $title !== '' ? $title : null;
	}
	private function fetch(string $url): ?string
	{
		
		$context = stream_context_create([
			'http' => [
				'method'        => 'GET',
				'timeout'       => 5,
				'user_agent'    => 'Monoverse Link Preview/1.0',
				'follow_location' => 1,
				'max_redirects' => 5,
			],
		]);
	
		$html = @file_get_contents($url, false, $context);
	
		if ($html === false) {
			return null;
		}
	
		return $html;
	}
	private function absoluteUrl(string $baseUrl, string $url): string
	{
		if ($url === '') {
			return '';
		}
	
		if (preg_match('~^https?://~i', $url)) {
			return $url;
		}
	
		$parts = parse_url($baseUrl);
	
		if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
			return $url;
		}
	
		$root = $parts['scheme'] . '://' . $parts['host'];
	
		if (str_starts_with($url, '/')) {
			return $root . $url;
		}
	
		return rtrim(dirname($baseUrl), '/') . '/' . ltrim($url, '/');
	}
	
	private function loadFromCache(string $url): ?array
	{
		$sql = "
			SELECT
				provider,
				title,
				description,
				image,
				site_name,
				favicon,
				embed
			FROM community_link_cache
			WHERE url_hash = :hash
			  AND (
					expires_at IS NULL
					OR expires_at > NOW()
			  )
			LIMIT 1
		";
	
		$stmt = $this->database->pdo()->prepare($sql);
	
		$stmt->execute([
			':hash' => hash('sha256', $url),
		]);
	
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
	
		return $row ?: null;
	}
	
	/**
	 * @param array<string, mixed> $link
	 */
	private function saveToCache(array $link): void
	{
		$sql = "
			INSERT INTO community_link_cache (
				url,
				url_hash,
				provider,
				title,
				description,
				image,
				site_name,
				favicon,
				embed,
				fetched_at,
				expires_at
			) VALUES (
				:url,
				:url_hash,
				:provider,
				:title,
				:description,
				:image,
				:site_name,
				:favicon,
				:embed,
				NOW(),
				DATE_ADD(NOW(), INTERVAL 7 DAY)
			)
			ON DUPLICATE KEY UPDATE
				provider = VALUES(provider),
				title = VALUES(title),
				description = VALUES(description),
				image = VALUES(image),
				site_name = VALUES(site_name),
				favicon = VALUES(favicon),
				embed = VALUES(embed),
				fetched_at = NOW(),
				expires_at = DATE_ADD(NOW(), INTERVAL 7 DAY)
		";
	
		$stmt = $this->database->pdo()->prepare($sql);
	
		$stmt->execute([
			':url'         => $link['url'],
			':url_hash'    => hash('sha256', $link['url']),
			':provider'    => $link['provider'],
			':title'       => $link['title'],
			':description' => $link['description'],
			':image'       => $link['image'],
			':site_name'   => $link['site_name'],
			':favicon' 	   => $link['favicon'],
			':embed'       => $link['embed'],
		]);
	}
	private function decorateProvider(array $link): array
	{
		[$label, $icon] = match ($link['provider']) {
	
			'youtube' => ['YouTube', 'fa-brands fa-youtube'],
			'github'  => ['GitHub', 'fa-brands fa-github'],
			'gitlab'  => ['GitLab', 'fa-brands fa-gitlab'],
			'spotify' => ['Spotify', 'fa-brands fa-spotify'],
			'reddit'  => ['Reddit', 'fa-brands fa-reddit'],
			'x'       => ['X', 'fa-brands fa-x-twitter'],
			'bluesky' => ['Bluesky', 'fa-brands fa-bluesky'],
			'mastodon'=> ['Mastodon', 'fa-brands fa-mastodon'],
	
			default   => match ($link['type']) {
				'video' => ['Video', 'fa-solid fa-circle-play'],
				'audio' => ['Music', 'fa-solid fa-music'],
				default => ['Website', 'fa-solid fa-link'],
			}
		};
	
		$link['provider_label'] = $label;
		$link['provider_icon']  = $icon;
	
		return $link;
	}
}
