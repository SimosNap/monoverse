<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\View;

class ChatController
{
    public function __construct(
        private View $view,
        private Response $response
    ) {
    }

    public function index(): void
    {
        $html = $this->view->render('chat', [
            'title' => 'Chat',
            'settings' => $settings,
        ]);

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }
}
