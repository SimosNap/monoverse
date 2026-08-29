<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Installer\InstallationRunner;
use Monoverse\Installer\Installer;
use Monoverse\Services\EditionService;
use Monoverse\Services\InstallerService;
use Monoverse\Services\Translator;

class InstallController
{
    public function __construct(
        private View $view,
        private Response $response,
        private Installer $installer,
        private EditionService $editionService,
        private Request $request,
        private Session $session,
        private InstallerService $installerService,
        private Translator $translator
    ) {
    }

    public function requirements(): void
    {
        $html = $this->view->render('installer', [
            'title' => $this->translator->translate(
                'installer.title.requirements'
            ),
            'settings' => $this->defaultSettings(),
            'requirements' => $this->installer->requirements(),
            'installerStep' => 'requirements',
        ]);

        $this->sendHtml($html);
    }

    public function edition(): void
    {
        $html = $this->view->render('installer-edition', [
            'title' => $this->translator->translate(
                'installer.title.edition'
            ),
            'settings' => $this->defaultSettings(),
            'installerStep' => 'edition',
            'editions' => $this->editionService->all(),
            'errors' => $this->session->getFlash('errors', []),
        ]);

        $this->sendHtml($html);
    }

    public function saveEdition(): void
    {
        $edition = (string) $this->request->post(
            'edition',
            ''
        );

        if (
            !$this->installerService->saveEdition(
                $edition
            )
        ) {
            $this->response->redirect(
                '/install/edition'
            );
        }

        $this->response->redirect(
            '/install/database'
        );
    }

    public function database(): void
    {
        if (
            !$this->installerService->canAccessStep(
                'database'
            )
        ) {
            $this->response->redirect(
                $this->installerService
                    ->firstIncompleteStep()
            );
        }

        $html = $this->view->render(
            'installer-database',
            [
                'title' => $this->translator->translate(
                    'installer.title.database'
                ),
                'settings' => $this->defaultSettings(),
                'installerStep' => 'database',
                'selectedEdition' =>
                    $this->installerService
                        ->getEdition(),
                'errors' => $this->session->getFlash(
                    'errors',
                    []
                ),
                'old' => $this->session->getFlash(
                    'old',
                    []
                ),
            ]
        );

        $this->sendHtml($html);
    }

    public function saveDatabase(): void
    {
        $database = [
            'host' => (string) $this->request->post(
                'db_host',
                ''
            ),
            'name' => (string) $this->request->post(
                'db_name',
                ''
            ),
            'user' => (string) $this->request->post(
                'db_user',
                ''
            ),
            'pass' => (string) $this->request->post(
                'db_pass',
                ''
            ),
        ];

        if (
            !$this->installerService->saveDatabase(
                $database
            )
        ) {
            $this->response->redirect(
                '/install/database'
            );
        }

        $this->response->redirect(
            '/install/oauth'
        );
    }

    public function oauth(): void
    {
        if (
            !$this->installerService->canAccessStep(
                'oauth'
            )
        ) {
            $this->response->redirect(
                $this->installerService
                    ->firstIncompleteStep()
            );
        }

        $html = $this->view->render(
            'installer-oauth',
            [
                'title' => $this->translator->translate(
                    'installer.title.oauth'
                ),
                'settings' => $this->defaultSettings(),
                'installerStep' => 'oauth',
                'errors' => $this->session->getFlash(
                    'errors',
                    []
                ),
                'old' => $this->session->getFlash(
                    'old',
                    []
                ),
            ]
        );

        $this->sendHtml($html);
    }

    public function saveOAuth(): void
    {
        $oauth = [
            'client_id' => (string) $this->request->post(
                'oauth_client_id',
                ''
            ),
            'client_secret' => (string) $this->request->post(
                'oauth_client_secret',
                ''
            ),
        ];

        if (
            !$this->installerService->saveOAuth(
                $oauth
            )
        ) {
            $this->response->redirect(
                '/install/oauth'
            );
        }

        $this->response->redirect(
            '/install/admin'
        );
    }

    public function admin(): void
    {
        if (
            !$this->installerService->canAccessStep(
                'admin'
            )
        ) {
            $this->response->redirect(
                $this->installerService
                    ->firstIncompleteStep()
            );
        }

        $html = $this->view->render(
            'installer-admin',
            [
                'title' => $this->translator->translate(
                    'installer.title.admin'
                ),
                'settings' => $this->defaultSettings(),
                'installerStep' => 'admin',
                'errors' => $this->session->getFlash(
                    'errors',
                    []
                ),
                'old' => $this->session->getFlash(
                    'old',
                    []
                ),
            ]
        );

        $this->sendHtml($html);
    }

    public function saveAdmin(): void
    {
        $admin = [
            'username' => (string) $this->request->post(
                'admin_username',
                ''
            ),
            'password' => (string) $this->request->post(
                'admin_password',
                ''
            ),
            'password_confirm' =>
                (string) $this->request->post(
                    'admin_password_confirm',
                    ''
                ),
        ];

        if (
            !$this->installerService->saveAdmin(
                $admin
            )
        ) {
            $this->response->redirect(
                '/install/admin'
            );
        }

        $this->response->redirect(
            '/install/summary'
        );
    }

    public function summary(): void
    {
        if (
            !$this->installerService->canAccessStep(
                'summary'
            )
        ) {
            $this->response->redirect(
                $this->installerService
                    ->firstIncompleteStep()
            );
        }

        $html = $this->view->render(
            'installer-summary',
            [
                'title' => $this->translator->translate(
                    'installer.title.summary'
                ),
                'settings' => $this->defaultSettings(),
                'installerStep' => 'summary',
                'edition' =>
                    $this->installerService
                        ->getEdition(),
                'database' =>
                    $this->installerService
                        ->getDatabase(),
                'admin' =>
                    $this->installerService
                        ->getAdmin(),
            ]
        );

        $this->sendHtml($html);
    }

    public function runInstall(): void
    {
        if (
            !$this->installerService->canAccessStep(
                'run'
            )
        ) {
            $this->response->redirect(
                $this->installerService
                    ->firstIncompleteStep()
            );
        }

        $runner = new InstallationRunner();

        if (
            !$runner->run(
                $this->installerService
                    ->installationData()
            )
        ) {
            $this->response->redirect(
                '/install/summary'
            );
        }

        $this->response->redirect('/admin');
    }

    private function sendHtml(string $html): void
    {
        $this->response
            ->status(200)
            ->header(
                'Content-Type',
                'text/html; charset=utf-8'
            )
            ->send($html);
    }

    private function defaultSettings(): array
    {
        return [
            'site_name' => 'Monoverse',
            'site_tagline' =>
                'IRC community websites by SimosNap',
        ];
    }
}
