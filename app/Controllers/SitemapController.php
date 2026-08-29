<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Services\ArticleService;
use Monoverse\Services\PostService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\SettingsService;

class SitemapController
{
	public function __construct(
		private Response $response,
		private SettingsService $settings,
		private ArticleService $articles,
		private PostService $posts,
		private ProfileService $profiles,
	) {
	}

	public function index(): void
	{
		$siteUrl = rtrim(
			(string) ($this->settings->get('site_url') ?? ''),
			'/'
		);

		if ($siteUrl === '') {
			http_response_code(500);
			echo 'Site URL not configured.';
			return;
		}

		$articles = $this->articles->listPublishedForSitemap();
		$posts = $this->posts->listPublishedForSitemap();
		$profiles = $this->profiles->listIndexableProfilesForSitemap();

		header('Content-Type: application/xml; charset=UTF-8');

		echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php

		$this->writeUrl(
			$siteUrl,
			'/',
			null,
			'daily',
			'1.0'
		);

		foreach ($articles as $article) {
			$this->writeUrl(
				$siteUrl,
				'/chanzine/' . rawurlencode((string) $article['slug']),
				$article['updated_at'] ?: $article['published_at'],
				'weekly',
				'0.9'
			);
		}

		foreach ($posts as $post) {
			$this->writeUrl(
				$siteUrl,
				'/ping/' . rawurlencode((string) $post['uuid']),
				$post['updated_at'] ?: $post['published_at'],
				'weekly',
				'0.8'
			);
		}

		foreach ($profiles as $profile) {
			$this->writeUrl(
				$siteUrl,
				'/profile/' . rawurlencode((string) $profile['username']),
				$profile['updated_at'] ?? null,
				'weekly',
				'0.7'
			);
		}

?>
</urlset>
<?php
	}

	private function writeUrl(
		string $siteUrl,
		string $path,
		string|int|null $lastMod,
		string $changeFreq,
		string $priority
	): void {
		$formattedLastMod = $this->formatLastMod($lastMod);
		?>
	<url>
		<loc><?= htmlspecialchars(
			$siteUrl . $path,
			ENT_XML1 | ENT_QUOTES,
			'UTF-8'
		) ?></loc>
<?php if ($formattedLastMod !== null) : ?>
		<lastmod><?= htmlspecialchars(
			$formattedLastMod,
			ENT_XML1 | ENT_QUOTES,
			'UTF-8'
		) ?></lastmod>
<?php endif; ?>
		<changefreq><?= htmlspecialchars(
			$changeFreq,
			ENT_XML1 | ENT_QUOTES,
			'UTF-8'
		) ?></changefreq>
		<priority><?= htmlspecialchars(
			$priority,
			ENT_XML1 | ENT_QUOTES,
			'UTF-8'
		) ?></priority>
	</url>
<?php
	}

	private function formatLastMod(string|int|null $lastMod): ?string
	{
		if ($lastMod === null || $lastMod === '') {
			return null;
		}

		if (is_int($lastMod) || ctype_digit((string) $lastMod)) {
			$timestamp = (int) $lastMod;
		} else {
			$timestamp = strtotime((string) $lastMod);
		}

		if ($timestamp === false || $timestamp <= 0) {
			return null;
		}

		return date(DATE_ATOM, $timestamp);
	}
}