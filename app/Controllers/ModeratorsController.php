<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\ModeratorService;
use Monoverse\Services\NavigationService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\SettingsService;

class ModeratorsController
{
    public function __construct(
        private View $view,
        private Response $response,
        private AdminAuthService $auth,
        private NavigationService $navigation,
        private ModeratorService $moderators,
        private ProfileService $profiles,
        private SettingsService $settings
    ) {
    }

    public function index(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $moderators = $this->moderators->findAll();
        $users = $this->profiles->listRegisteredUsers();

        $moderatorSubs = [];

        foreach ($moderators as $moderator) {
            $sub = trim(
                (string) ($moderator['oauth_sub'] ?? '')
            );

            if ($sub !== '') {
                $moderatorSubs[$sub] = true;
            }
        }

        $availableUsers = array_values(
            array_filter(
                $users,
                static function (array $user) use ($moderatorSubs): bool {
                    $sub = trim(
                        (string) ($user['oauth_sub'] ?? '')
                    );

                    return $sub !== ''
                        && !isset($moderatorSubs[$sub]);
                }
            )
        );
        
        $settings = $this->settings->all();

        $html = $this->view->render(
            'admin/moderators',
            [
                'title' => 'Moderatori',
                'settings' => $settings,
                'admin' => $this->auth->user(),
                'navigation' => $this->navigation->items(),
                'moderators' => $moderators,
                'users' => $availableUsers,
            ],
            'admin-layout'
        );

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }

    public function add(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $sub = trim((string) ($_POST['oauth_sub'] ?? ''));

        if ($sub === '') {
            $this->response->redirect('/admin/moderators');
            return;
        }

        $profile = $this->profiles->findBySub($sub);

        if ($profile === false) {
            $this->response->redirect('/admin/moderators');
            return;
        }

        $this->moderators->add($profile);

        $this->response->redirect('/admin/moderators');
    }

    public function remove(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $sub = trim((string) ($_POST['oauth_sub'] ?? ''));

        if ($sub !== '') {
            $this->moderators->remove($sub);
        }

        $this->response->redirect('/admin/moderators');
    }

    public function enable(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $sub = trim((string) ($_POST['oauth_sub'] ?? ''));

        if ($sub !== '') {
            $this->moderators->enable($sub);
        }

        $this->response->redirect('/admin/moderators');
    }

    public function disable(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $sub = trim((string) ($_POST['oauth_sub'] ?? ''));

        if ($sub !== '') {
            $this->moderators->disable($sub);
        }

        $this->response->redirect('/admin/moderators');
    }
}