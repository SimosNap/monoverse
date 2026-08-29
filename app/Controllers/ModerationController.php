<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\SettingsService;
use Monoverse\Services\UserModerationService;
use Monoverse\Services\ReportService;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\AuthorizationService;
use Monoverse\Services\PostService;
use Monoverse\Services\CommentService;

class ModerationController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications,
		SettingsService $settings,
		private ProfileService $profiles,
		private UserModerationService $moderation,
		private ReportService $reports,
		private PostService $posts,
		private CommentService $comments,
		private AuthorizationService $authorization,
		private AdminAuthService $adminAuth
	) {
		parent::__construct(
			$view,
			$response,
			$session,
			$notifications,
			$settings
		);
	}
	
	public function index(): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}

		$this->render('account-moderation', [
			'title'       => 'Moderazione',
			'openReports' => $this->reports->countOpen(),
			'bans'        => $this->moderation->getActiveBans(),
			'mutes'       => $this->moderation->getActiveMutes(),
		]);
	}
	
	public function bans(): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$this->render('account-moderation-bans', [
			'title' => 'Utenti sospesi',
			'bans'  => $this->moderation->getActiveBans(),
		]);
	}
	
	public function mutes(): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$this->render('account-moderation-mutes', [
			'title' => 'Utenti silenziati',
			'mutes' => $this->moderation->getActiveMutes(),
		]);
	}
	
	public function reports(): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$this->render('account-moderation-reports', [
			'title'   => 'Segnalazioni',
			'reports' => $this->reports->findAll(),
		]);
	}
	
	public function report(string $uuid): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$report = $this->reports->findByUuid($uuid);
	
		if (!$report) {
			$this->notFound();
			return;
		}
	
		$content = null;
	
		if (($report['target_type'] ?? '') === 'ping') {
			$content = $this->posts->findByUuid(
				(string) $report['target_uuid']
			);
		}
	
		if (($report['target_type'] ?? '') === 'pong') {
			$content = $this->comments->findByUuid(
				(string) $report['target_uuid']
			);
		}
		
		$authorProfile = null;
		
		if (is_array($content) && !empty($content['author_sub'])) {
			$profile = $this->profiles->findBySub(
				(string) $content['author_sub']
			);
		
			$authorProfile = $profile !== false
				? $profile
				: null;
		}
		
		if (is_array($content)) {
			$content['username'] = trim(
				(string) (
					$authorProfile['username']
					?? $authorProfile['nickname']
					?? 'utente-eliminato'
				)
			);
		
			$content['avatar_url'] = trim(
				(string) ($authorProfile['avatar_url'] ?? '')
			);
		
			$content['show_avatar'] =
				$content['avatar_url'] !== ''
				&& !empty($authorProfile['public_profile']);
		
			$content['can_edit'] = false;
			$content['can_delete'] = false;
		
			if (($report['target_type'] ?? '') === 'ping') {
				$content['published_at_formatted'] = date(
					'd/m/Y H:i',
					strtotime(
						(string) (
							$content['published_at']
							?? $content['created_at']
							?? 'now'
						)
					)
				);
		
				$content['comments_count'] =
					(int) ($content['comments_count'] ?? 0);
		
				$content['user_vote'] = 0;
				$content['score'] = (int) ($content['score'] ?? 0);
			}
		
			if (($report['target_type'] ?? '') === 'pong') {
				$content['created_at_formatted'] = date(
					'd/m/Y H:i',
					strtotime((string) ($content['created_at'] ?? 'now'))
				);
			}
		}
	
		$this->render('account-moderation-report', [
			'title'         => 'Dettaglio segnalazione',
			'report'        => $report,
			'content'       => $content,
			'authorProfile' => $authorProfile,
		]);
	}
	
	public function review(string $uuid): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$this->reports->markReviewed(
			$uuid,
			(string) $currentUser['sub']
		);
	
		$this->response->redirect(
			'/account/moderation/report/' . rawurlencode($uuid)
		);
	}
	
	public function close(string $uuid): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$this->reports->close(
			$uuid,
			(string) $currentUser['sub']
		);
	
		$this->response->redirect(
			'/account/moderation/report/' . rawurlencode($uuid)
		);
	}
	
	public function deleteContent(string $uuid): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$report = $this->reports->findByUuid($uuid);
	
		if (!$report) {
			$this->notFound();
			return;
		}
	
		$targetType = (string) ($report['target_type'] ?? '');
		$targetUuid = (string) ($report['target_uuid'] ?? '');
	
		$deleted = false;
	
		if ($targetType === 'ping') {
			$deleted = $this->posts->delete($targetUuid);
		}
	
		if ($targetType === 'pong') {
			$deleted = $this->comments->delete($targetUuid);
		}
	
		if (!$deleted) {
			$this->session->flash(
				'error',
				'Impossibile eliminare il contenuto segnalato.'
			);
	
			$this->response->redirect(
				'/account/moderation/report/' . rawurlencode($uuid)
			);
			return;
		}
	
		$this->reports->close(
			$uuid,
			(string) $currentUser['sub']
		);
	
		$this->session->flash(
			'success',
			'Contenuto eliminato e segnalazione chiusa.'
		);
	
		$this->response->redirect(
			'/account/moderation/report/' . rawurlencode($uuid)
		);
	}
	
	public function mute(string $username): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$profile = $this->profiles->findByUsername($username);
	
		if (!$profile) {
			http_response_code(404);
			exit('User not found');
		}
	
		if (($profile['oauth_sub'] ?? '') === ($currentUser['sub'] ?? '')) {
			$this->session->flash(
				'error',
				'Non puoi silenziare il tuo stesso account.'
			);
	
			$this->response->redirect('/profile/' . rawurlencode($username));
			return;
		}
	
		$this->moderation->mute(
			$profile['oauth_sub'],
			$currentUser['sub']
		);
	
		$this->session->flash(
			'success',
			'Utente silenziato correttamente.'
		);
	
		$this->response->redirect('/profile/' . rawurlencode($username));
	}
	
	public function ban(string $username): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$profile = $this->profiles->findByUsername($username);
	
		if (!$profile) {
			http_response_code(404);
			exit('User not found');
		}
	
		if (($profile['oauth_sub'] ?? '') === ($currentUser['sub'] ?? '')) {
			$this->session->flash(
				'error',
				'Non puoi bannare il tuo stesso account.'
			);
	
			$this->response->redirect('/profile/' . rawurlencode($username));
			return;
		}
	
		$this->moderation->ban(
			$profile['oauth_sub'],
			$currentUser['sub']
		);
	
		$this->session->flash(
			'success',
			'Utente bannato correttamente.'
		);
	
		$this->response->redirect('/profile/' . rawurlencode($username));
	}
	
	public function unmute(string $username): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$profile = $this->profiles->findByUsername($username);
	
		if (!$profile) {
			http_response_code(404);
			exit('User not found');
		}
	
		$this->moderation->unmute(
			$profile['oauth_sub']
		);
	
		$this->session->flash(
			'success',
			'Utente riattivato correttamente.'
		);
	
		$this->response->redirect('/profile/' . rawurlencode($username));
	}
	
	public function unban(string $username): void
	{
		$currentUser = $this->session->get('auth.user');
	
		if (!$currentUser) {
			$this->response->redirect('/oauth/login');
			return;
		}
	
		if (!$this->authorization->isModerator($currentUser)) {
			http_response_code(403);
			exit('Forbidden');
		}
	
		$profile = $this->profiles->findByUsername($username);
	
		if (!$profile) {
			http_response_code(404);
			exit('User not found');
		}
	
		$this->moderation->unban(
			$profile['oauth_sub']
		);
	
		$this->session->flash(
			'success',
			'Utente riattivato correttamente.'
		);
	
		$this->response->redirect('/profile/' . rawurlencode($username));
	}
}