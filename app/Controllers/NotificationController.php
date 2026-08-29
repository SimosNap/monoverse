<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;
use Monoverse\Services\ProfileService;

class NotificationController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications,
		private ProfileService $profiles
	) {
		parent::__construct(
			$view,
			$response,
			$session,
			$notifications
		);
	}

	public function index(): void
	{
		$user = $this->session->get('auth.user');

		if (!$user || empty($user['sub'])) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$notifications = $this->notifications->listForUser($user['sub']);
		
		$this->notifications->markAllAsRead($user['sub']);

		$actors = [];

		foreach ($notifications as $notification) {

			$actorSub = $notification['actor_sub'] ?? null;

			if (!$actorSub || isset($actors[$actorSub])) {
				continue;
			}

			$profile = $this->profiles->findBySub($actorSub);

			if ($profile) {
				$actors[$actorSub] = $profile;
			}
		}

		$this->render(
			'notifications',
			[
				'title' => 'Notifiche',
				'notifications' => $notifications,
				'actors' => $actors,
			]
		);
	}
	
	public function delete(string $uuid): void
	{
		$user = $this->session->get('auth.user');
	
		if (!$user || empty($user['sub'])) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		$this->notifications->delete(
			$uuid,
			(string) $user['sub']
		);
	
		$this->response->redirect('/notifications');
	}
	
	public function deleteAll(): void
	{
		$user = $this->session->get('auth.user');
	
		if (!$user || empty($user['sub'])) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		$this->notifications->deleteAll(
			(string) $user['sub']
		);
	
		$this->response->redirect('/notifications');
	}
}
