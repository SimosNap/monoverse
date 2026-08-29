<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;
use Monoverse\Services\ModeratorService;
use Monoverse\Services\ReportService;
use Monoverse\Services\SettingsService;

class ReportController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications,
		private ReportService $reports,
		private ModeratorService $moderators,
		SettingsService $settings
	) {
		parent::__construct(
			$view,
			$response,
			$session,
			$notifications,
			$settings
		);
	}

	public function store(): void
	{
		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$targetType = trim((string) ($_POST['target_type'] ?? ''));
		$targetUuid = trim((string) ($_POST['target_uuid'] ?? ''));
		$reason = trim((string) ($_POST['reason'] ?? ''));
		$description = trim((string) ($_POST['description'] ?? ''));

		if (
			!in_array($targetType, ['ping', 'pong'], true) ||
			$targetUuid === '' ||
			$reason === ''
		) {
			$this->response->redirect('/ping');
			return;
		}

		if (
			$this->reports->alreadyReported(
				(string) $user['sub'],
				$targetType,
				$targetUuid
			)
		) {
			$this->session->flash(
				'warning',
				'Hai già segnalato questo contenuto.'
			);

			$this->response->redirect($_SERVER['HTTP_REFERER'] ?? '/ping');
			return;
		}

		$reportUuid = $this->reports->create(
			(string) $user['sub'],
			$targetType,
			$targetUuid,
			$reason,
			$description
		);
		
		if ($reportUuid !== null) {
		
			foreach ($this->moderators->findEnabled() as $moderator) {
		
				$this->notifications->createReportNotification(
					(string) $moderator['oauth_sub'],
					(string) $user['sub'],
					$reportUuid
				);
		
			}
		
		}

		$this->session->flash(
			'success',
			'La segnalazione è stata inviata ai moderatori.'
		);

		$this->response->redirect($_SERVER['HTTP_REFERER'] ?? '/ping');
	}
}
