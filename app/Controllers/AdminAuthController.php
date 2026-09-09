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
use Monoverse\Services\Translator;

class AdminAuthController
{
    public function __construct(
        private View $view,
        private Response $response,
        private Request $request,
        private Session $session,
        private AdminAuthService $auth,
        private SettingsService $settings,
        private NavigationService $navigation,
        private Translator $translator
    ) {
    }

    public function login(): void
    {
        if ($this->auth->check()) {
            $this->response->redirect('/admin');
            return;
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
            ->header(
                'Content-Type',
                'text/html; charset=utf-8'
            )
            ->send($html);
    }

    public function authenticate(): void
    {
        $username = trim(
            (string) $this->request->post(
                'username',
                ''
            )
        );

        $password = (string) $this->request->post(
            'password',
            ''
        );

        if (!$this->auth->attempt(
            $username,
            $password
        )) {
            $this->session->flash('errors', [
                'Username o password non validi.',
            ]);

            $this->session->flash('old', [
                'username' => $username,
            ]);

            $this->response->redirect('/admin/login');
            return;
        }

        $this->response->redirect('/admin');
    }

    public function changePassword(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $html = $this->view->render(
            'admin-change-password',
            [
                'title' => $this->translator->translate(
                    'admin.account.password.title'
                ),
                'admin' => $this->auth->user(),
                'navigation' => $this->navigation->items(),
                'errors' => $this->session->getFlash(
                    'errors',
                    []
                ),
                'success' => $this->session->getFlash(
                    'success',
                    []
                ),
            ],
            'admin-layout'
        );

        $this->response
            ->status(200)
            ->header(
                'Content-Type',
                'text/html; charset=utf-8'
            )
            ->send($html);
    }

    public function updatePassword(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $currentPassword = (string) $this->request->post(
            'current_password',
            ''
        );

        $newPassword = (string) $this->request->post(
            'new_password',
            ''
        );

        $newPasswordConfirmation = (string) $this->request->post(
            'new_password_confirmation',
            ''
        );

        if (
            $currentPassword === ''
            || $newPassword === ''
            || $newPasswordConfirmation === ''
        ) {
            $this->session->flash(
                'errors',
                [
                    $this->translator->translate(
                        'admin.account.password.errors.required'
                    ),
                ]
            );

            $this->response->redirect(
                '/admin/change-password'
            );
            return;
        }

        if ($newPassword !== $newPasswordConfirmation) {
            $this->session->flash(
                'errors',
                [
                    $this->translator->translate(
                        'admin.account.password.errors.confirmation'
                    ),
                ]
            );

            $this->response->redirect(
                '/admin/change-password'
            );
            return;
        }

        if (!$this->auth->changePassword(
            $currentPassword,
            $newPassword
        )) {
            $this->session->flash(
                'errors',
                [
                    $this->translator->translate(
                        'admin.account.password.errors.current'
                    ),
                ]
            );

            $this->response->redirect(
                '/admin/change-password'
            );
            return;
        }

        $this->session->flash(
            'success',
            [
                $this->translator->translate(
                    'admin.account.password.success'
                ),
            ]
        );

        $this->response->redirect(
            '/admin/change-password'
        );
    }

    public function logout(): void
    {
        $this->auth->logout();

        $this->response->redirect('/admin/login');
    }
}
