<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Services\SettingsService;

class RobotsController
{
	public function __construct(
		private Response $response,
		private SettingsService $settings,
	) {
	}

	public function index(): void
	{
		
		$siteUrl = rtrim(
			(string) ($this->settings->get('site_url') ?? ''),
			'/'
		);

		if ($siteUrl === '') {
			$this->response->text(
				'Site URL not configured.',
				500
			);

			return;
		}

		$content = implode("\n", [
			'User-agent: *',
			'Allow: /',
			'',
			'Disallow: /admin/',
			'Disallow: /account/',
			'Disallow: /oauth/',
			'Disallow: /install/',
			'',
			'Sitemap: ' . $siteUrl . '/sitemap.xml',
			'',
		]);

		$this->response->text($content);
	}
}