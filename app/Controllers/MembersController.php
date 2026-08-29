<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;
use Monoverse\Core\Blocks\BlockManager;
use Monoverse\Services\ProfileService;
use Monoverse\Services\SettingsService;

class MembersController extends BaseController
{
    public function __construct(
        View $view,
        Response $response,
        Session $session,
        NotificationService $notifications,
        private ProfileService $profiles,
        private BlockManager $blocks,
        SettingsService $settings
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
        $perPage = 20;

        $search = trim((string) ($_GET['q'] ?? ''));

        $currentPage = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $totalMembers = $this->profiles->countPublicProfiles(
            $search
        );

        $totalPages = max(
            1,
            (int) ceil($totalMembers / $perPage)
        );

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $perPage;

        $members = $this->profiles->listPublicProfiles(
            $perPage,
            $offset,
            $search
        );

        $widgetAreas = [
            'beforeContent' => $this->blocks->renderArea(
                'members',
                'before-content'
            ),
            'sidebar' => $this->blocks->renderArea(
                'members',
                'sidebar'
            ),
            'afterContent' => $this->blocks->renderArea(
                'members',
                'after-content'
            ),
        ];

        $this->render('members', [
            'title' => 'Community',
            'members' => $members,
            'search' => $search,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalMembers' => $totalMembers,
            'widgetAreas' => $widgetAreas,
            'blockCssFiles' => $this->blocks->stylesheets(),
        ]);
    }
}
