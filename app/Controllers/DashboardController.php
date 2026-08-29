<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\SettingsService;

class DashboardController
{
    public function __construct(
        private View $view,
        private Response $response,
        private AdminAuthService $auth,
        private \Monoverse\Services\NavigationService $navigation,
        private SettingsService $settings
    ) {
    }

    public function index(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
        }

        $settings = $this->settings->all();

        $html = $this->view->render('dashboard', [
            'title' => 'Dashboard',
            'settings' => $settings,
            'admin' => $this->auth->user(),
            'navigation' => $this->navigation->items(),
        ], 'admin-layout');

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }
}