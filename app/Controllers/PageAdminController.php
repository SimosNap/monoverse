<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\PageService;
use Monoverse\Services\SettingsService;
use Monoverse\Services\NavigationService;
use Monoverse\Repositories\BlockRepository;
use Throwable;

final class PageAdminController extends BaseController
{
    public function __construct(
        View $view,
        Response $response,
        Session $session,
        NotificationService $notifications,
        SettingsService $settings,
        private AdminAuthService $auth,
        private PageService $pages,
        private NavigationService $navigation,
        private BlockRepository $blocks
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
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $html = $this->view->render(
            'pages',
            [
                'title' => 'Pagine',
                'admin' => $this->auth->user(),
                'pages' => $this->pages->listForAdmin(),
                'navigation' => $this->navigation->items(),
            ],
            'admin-layout'
        );

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }

    public function create(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $this->renderForm(
            null,
            '/admin/pages',
            'Nuova pagina'
        );
    }

    public function store(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $data = $this->formData();
        $errors = $this->validate($data);

        if (
            $data['slug'] !== ''
            && $this->pages->slugExists($data['slug'])
        ) {
            $errors['slug'] = 'Esiste già una pagina con questo slug.';
        }

        if ($errors !== []) {
            $this->renderForm(
                $data,
                '/admin/pages',
                'Nuova pagina',
                $errors
            );

            return;
        }

        try {
            $this->pages->create($data);
        } catch (Throwable) {
            $this->renderForm(
                $data,
                '/admin/pages',
                'Nuova pagina',
                [
                    'general' => 'Non è stato possibile creare la pagina.',
                ]
            );

            return;
        }

        $this->response->redirect('/admin/pages');
    }

    public function edit(string $id): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $pageId = $this->normalizeId($id);

        if ($pageId === null) {
            $this->notFound();
            return;
        }

        $page = $this->pages->findById($pageId);

        if ($page === null) {
            $this->notFound();
            return;
        }

        $this->renderForm(
            $page,
            '/admin/pages/' . $pageId,
            'Modifica pagina'
        );
    }

    public function update(string $id): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $pageId = $this->normalizeId($id);

        if ($pageId === null) {
            $this->notFound();
            return;
        }

        $page = $this->pages->findById($pageId);

        if ($page === null) {
            $this->notFound();
            return;
        }

        $data = $this->formData();

        $errors = $this->validate($data);

        if (
            $data['slug'] !== ''
            && $this->pages->slugExists(
                $data['slug'],
                $pageId
            )
        ) {
            $errors['slug'] = 'Esiste già una pagina con questo slug.';
        }

        if ($errors !== []) {
            $this->renderForm(
                array_merge($page, $data),
                '/admin/pages/' . $pageId,
                'Modifica pagina',
                $errors
            );

            return;
        }

        try {
            $oldBlockPageKey = $this->pages->blockPageKey(
                (string) $page['slug']
            );

            $newBlockPageKey = $this->pages->blockPageKey(
                $data['slug']
            );

            if ($oldBlockPageKey !== $newBlockPageKey) {
                $this->blocks->renamePage(
                    $oldBlockPageKey,
                    $newBlockPageKey
                );
            }
            $this->pages->update(
                $pageId,
                $data
            );
        } catch (Throwable) {
            $this->renderForm(
                array_merge($page, $data),
                '/admin/pages/' . $pageId,
                'Modifica pagina',
                [
                    'general' => 'Non è stato possibile aggiornare la pagina.',
                ]
            );

            return;
        }

        $this->response->redirect('/admin/pages');
    }

    public function delete(string $id): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $pageId = $this->normalizeId($id);

        if ($pageId === null) {
            $this->notFound();
            return;
        }

        $page = $this->pages->findById($pageId);

        if ($page === null) {
            $this->notFound();
            return;
        }

        try {
            $this->blocks->deleteByPage(
                $this->pages->blockPageKey(
                    (string) $page['slug']
                )
            );

            $this->pages->delete($pageId);
        } catch (Throwable) {
            $this->response->redirect(
                '/admin/pages'
            );

            return;
        }

        $this->response->redirect('/admin/pages');
    }

    private function renderForm(
        ?array $page,
        string $formAction,
        string $title,
        array $errors = []
    ): void {
        $blockPageKey = '';

        if ($page !== null) {
            $slug = trim(
                (string) ($page['slug'] ?? '')
            );

            if ($slug !== '') {
                $blockPageKey = $this->pages->blockPageKey(
                    $slug
                );
            }
        }

        $html = $this->view->render(
            'page-form',
            [
                'title' => $title,
                'admin' => $this->auth->user(),
                'page' => $page,
                'errors' => $errors,
                'formAction' => $formAction,
                'blockPageKey' => $blockPageKey,
                'navigation' => $this->navigation->items(),
            ],
            'admin-layout'
        );

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }

    private function formData(): array
    {
        return [
            'title' => trim(
                (string) ($_POST['title'] ?? '')
            ),
    
            'slug' => $this->normalizeSlug(
                (string) ($_POST['slug'] ?? '')
            ),
    
            'status' => $this->normalizeStatus(
                (string) ($_POST['status'] ?? 'draft')
            ),
    
            'show_in_navigation' => isset(
                $_POST['show_in_navigation']
            )
                ? 1
                : 0,
    
            'menu_label' => trim(
                (string) ($_POST['menu_label'] ?? '')
            ),
    
            'navigation_group' => trim(
                (string) (
                    $_POST['navigation_group']
                    ?? 'default'
                )
            ),
    
            'sort_order' => max(
                0,
                (int) ($_POST['sort_order'] ?? 0)
            ),
    
            'meta_title' => trim(
                (string) ($_POST['meta_title'] ?? '')
            ),
    
            'meta_description' => trim(
                (string) ($_POST['meta_description'] ?? '')
            ),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        $title = trim(
            (string) ($data['title'] ?? '')
        );

        $slug = trim(
            (string) ($data['slug'] ?? '')
        );

        if ($title === '') {
            $errors['title'] = 'Il titolo è obbligatorio.';
        } elseif (mb_strlen($title) > 190) {
            $errors['title'] = 'Il titolo non può superare 190 caratteri.';
        }

        if ($slug === '') {
            $errors['slug'] = 'Lo slug è obbligatorio.';
        } elseif (mb_strlen($slug) > 190) {
            $errors['slug'] = 'Lo slug non può superare 190 caratteri.';
        } elseif (!preg_match(
            '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            $slug
        )) {
            $errors['slug'] = 'Lo slug contiene caratteri non validi.';
        }

        $metaTitle = trim(
            (string) ($data['meta_title'] ?? '')
        );

        if (mb_strlen($metaTitle) > 190) {
            $errors['meta_title'] =
                'Il titolo SEO non può superare 190 caratteri.';
        }

        return $errors;
    }

    private function normalizeSlug(
        string $slug
    ): string {
        $slug = trim(
            mb_strtolower($slug)
        );

        $slug = str_replace(
            [
                'à',
                'á',
                'è',
                'é',
                'ì',
                'í',
                'ò',
                'ó',
                'ù',
                'ú',
            ],
            [
                'a',
                'a',
                'e',
                'e',
                'i',
                'i',
                'o',
                'o',
                'u',
                'u',
            ],
            $slug
        );

        $slug = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $slug
        ) ?? '';

        return trim($slug, '-');
    }

    private function normalizeStatus(
        string $status
    ): string {
        return in_array(
            $status,
            [
                'draft',
                'published',
                'private',
            ],
            true
        )
            ? $status
            : 'draft';
    }

    private function normalizeId(
        string $id
    ): ?int {
        if (
            $id === ''
            || !ctype_digit($id)
        ) {
            return null;
        }

        $pageId = (int) $id;

        return $pageId > 0
            ? $pageId
            : null;
    }
}
