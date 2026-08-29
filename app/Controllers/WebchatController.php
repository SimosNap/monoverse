<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\NavigationService;
use Monoverse\Services\SettingsService;

class WebchatController
{
    public function __construct(
        private View $view,
        private Response $response,
        private Request $request,
        private Session $session,
        private AdminAuthService $auth,
        private NavigationService $navigation,
        private SettingsService $settings
    ) {
    }

    public function index(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
        }

        $html = $this->view->render('webchat', [
            'title' => 'Landing Chat',
            'admin' => $this->auth->user(),
            'navigation' => $this->navigation->items(),
            'settings' => $this->settings->all(),
            'success' => $this->session->getFlash('success'),
        ], 'admin-layout');

        $this->sendHtml($html);
    }

    public function save(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
        }

        $this->settings->set('chat_default_channel', (string) $this->request->post('chat_default_channel', '#chat'));
        $this->settings->set('chat_title', (string) $this->request->post('chat_title', '#chat - Chat'));
        $this->settings->set('chat_theme', (string) $this->request->post('chat_theme', 'Osprey'));
        $this->settings->set('chat_state_key', (string) $this->request->post('chat_state_key', ''));

        $this->settings->set(
            'landing_show_hero',
            $this->request->post('landing_show_hero') ? '1' : '0'
        );

        $this->settings->set(
            'landing_show_channel_card',
            $this->request->post('landing_show_channel_card') ? '1' : '0'
        );

        $this->session->flash('success', 'Landing Chat salvata.');

        $this->response->redirect('/admin/chat');
    }

    private function sendHtml(string $html): void
    {
        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }
}