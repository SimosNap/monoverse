<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\View;
use Monoverse\Services\SettingsService;

class HomeController
{
    public function __construct(
        private View $view,
        private Response $response,
        private SettingsService $settings
    ) {
    }

    public function index(): void
    {
        $settings = $this->settings->all();

        $html = $this->view->render('home', [
            'title' => $settings['site_name'] ?? 'Monoverse',
            'content' => 'Benvenuto in Monoverse.',
            'settings' => $settings,
        ]);

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }
}
