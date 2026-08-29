<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Blocks\BlockManager;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;
use Monoverse\Services\PageService;
use Monoverse\Services\SettingsService;

final class PageController extends BaseController
{
    public function __construct(
        View $view,
        Response $response,
        Session $session,
        NotificationService $notifications,
        SettingsService $settings,
        private PageService $pages,
        private BlockManager $blockManager
    ) {
        parent::__construct(
            $view,
            $response,
            $session,
            $notifications,
            $settings
        );
    }

    public function show(string $slug): void
    {
        $slug = trim(
            rawurldecode($slug)
        );

        if ($slug === '') {
            $this->notFound();
            return;
        }

        $page = $this->pages->findPublishedBySlug(
            $slug
        );

        if ($page === null) {
            $this->notFound();
            return;
        }

        $pageKey = $this->pages->blockPageKey($slug);

        $contentWidgets = $this->blockManager->renderArea(
            $pageKey,
            'content',
            [
                'page' => $page,
                'pageSlug' => $slug,
            ]
        );

        $sidebarWidgets = $this->blockManager->renderArea(
            $pageKey,
            'sidebar',
            [
                'page' => $page,
                'pageSlug' => $slug,
            ]
        );

        $metaTitle = trim(
            (string) ($page['meta_title'] ?? '')
        );

        $metaDescription = trim(
            (string) ($page['meta_description'] ?? '')
        );

        $this->render('page', [
            'title' => $metaTitle !== ''
                ? $metaTitle
                : (string) ($page['title'] ?? ''),

            'metaDescription' => $metaDescription,

            'page' => $page,

            'contentWidgets' => $contentWidgets,
            'sidebarWidgets' => $sidebarWidgets,

            'blockCssFiles' => $this->blockManager->stylesheets(),
            'blockJsFiles' => $this->blockManager->scripts(),
        ]);
    }
}
