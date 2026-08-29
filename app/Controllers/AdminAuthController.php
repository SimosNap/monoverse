<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\SettingsService;

class AdminAuthController
{
    public function __construct(
        private View $view,
        private Response $response,
        private Request $request,
        private Session $session,
        private AdminAuthService $auth,
        private SettingsService $settings
    ) {
    }

    public function login(): void
    {
        if ($this->auth->check()) {
            $this->response->redirect('/admin');
        }
        
        $settings = $this->settings->all();

        $html = $this->view->render('admin-login', [
            'title' => 'Accesso amministrazione',
            'settings' => $settings,
            'errors' => $this->session->getFlash('errors', []),
            'old' => $this->session->getFlash('old', []),
        ]);

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }

    public function authenticate(): void
    {
        $username = trim((string) $this->request->post('username', ''));
        $password = (string) $this->request->post('password', '');

        if (!$this->auth->attempt($username, $password)) {

            $this->session->flash('errors', [
                'Username o password non validi.',
            ]);

            $this->session->flash('old', [
                'username' => $username,
            ]);

            $this->response->redirect('/admin/login');
        }

        $this->response->redirect('/admin');
    }

    public function logout(): void
    {
        $this->auth->logout();

        $this->response->redirect('/admin/login');
    }
}
