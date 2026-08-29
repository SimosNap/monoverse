<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;
use Monoverse\Services\SettingsService;

abstract class BaseController
{
	public function __construct(
		protected View $view,
		protected Response $response,
		protected Session $session,
		protected ?NotificationService $notifications = null,
		protected ?SettingsService $settings = null
	) {
	}

	protected function render(
		string $view,
		array $data = [],
		string $layout = 'layout'
	): void {
		$user = $this->session->get('auth.user');

		if (!is_array($user)) {
			$user = [];
		}

		$notificationCount = 0;

		if (
			$this->notifications !== null &&
			!empty($user['sub'])
		) {
			$notificationCount = $this->notifications->countUnread(
				(string) $user['sub']
			);
		}

		$settings = [];

		if ($this->settings !== null) {
			$settings = $this->settings->all();
		}

		$data += [
			'settings'          => $settings,
			'user'              => $user,
			'notificationCount' => $notificationCount,
			'session'           => $this->session,
		];

		$this->response
			->status(200)
			->header('Content-Type', 'text/html; charset=utf-8');

		echo $this->view->render(
			$view,
			$data,
			$layout
		);
	}
	
	protected function notFound(): void
	{
		$this->response
			->status(404)
			->header('Content-Type', 'text/html; charset=utf-8');
	
		echo $this->view->render(
			'404',
			[
				'user' => $this->session->get('auth.user') ?? [],
			]
		);
	
		exit;
	}
}