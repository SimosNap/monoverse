<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Services\LocaleService;

class LocaleController
{
	public function __construct(
		private Request $request,
		private Response $response,
		private LocaleService $locales
	) {
	}

	public function update(): void
	{
		$locale = trim(
			(string) $this->request->post(
				'locale',
				''
			)
		);

		$this->locales->setCurrentLocale($locale);

		$referer = (string) $this->request->server(
			'HTTP_REFERER',
			'/'
		);

		$path = parse_url($referer, PHP_URL_PATH);
		$query = parse_url($referer, PHP_URL_QUERY);

		if (
			!is_string($path)
			|| $path === ''
			|| $path[0] !== '/'
		) {
			$path = '/';
		}

		if (
			is_string($query)
			&& $query !== ''
		) {
			$path .= '?' . $query;
		}

		$this->response->redirect($path);
	}
}
