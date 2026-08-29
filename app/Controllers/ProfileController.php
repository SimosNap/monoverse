<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Core\Blocks\BlockManager;
use Monoverse\Services\NotificationService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\SimosNapService;
use Monoverse\Core\Config;
use Monoverse\Services\SettingsService;
use Monoverse\Services\FollowService;
use Monoverse\Services\BlockService;
use Monoverse\Services\PostService;
use Monoverse\Services\ModeratorService;
use Monoverse\Services\UserModerationService;

class ProfileController extends BaseController
{
    public function __construct(
        View $view,
        Response $response,
        Session $session,
        NotificationService $notifications,
        private ProfileService $profiles,
        private PostService $posts,
        private SimosNapService $simosnap,
        private FollowService $follows,
        private BlockService $blocks,
        private UserModerationService $moderation,
        private ModeratorService $moderators,
        private BlockManager $blockManager,
        private Config $config,
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

    public function show(string $username): void
    {
        $pageSize = max(
            1,
            (int) $this->config->get(
                'app.infinite_scroll_page_size',
                20
            )
        );

        $username = trim(rawurldecode($username));

        if ($username === '') {
            $this->notFound();
            return;
        }

        $profile = $this->profiles->findPublicByUsername($username);

        if (!$profile) {
            $this->notFound();
            return;
        }

        $dogeTipSource = trim(
            (string) ($profile['doge_tip_source'] ?? '')
        );

        $dogeTipAddress = null;

        if ($dogeTipSource === 'mydogemask') {
            $address = trim(
                (string) ($profile['doge_tip_address'] ?? '')
            );

            $dogeTipAddress = $address !== ''
                ? $address
                : null;
        } elseif ($dogeTipSource === 'simosnap') {
            $dogeTipAddress = $this->simosnap->getDogecoinAddress(
                (string) $profile['username']
            );
        }

        $currentUser = $this->session->get('auth.user');

        $noIndex = empty($profile['allow_indexing']);

        $interests = $this->decodeInterests(
            (string) ($profile['interests'] ?? '')
        );

        $avatarUrl = (string) ($profile['avatar_url'] ?? '');
        $irc = [];

        if ($profile['username'] !== '') {
            $irc = $this->simosnap->getPublicProfile(
                (string) $profile['username']
            );
        }

        $presence = $this->simosnap->getAccountPresence(
            (string) $profile['username']
        );

        $currentSub = is_array($currentUser)
            ? (string) ($currentUser['sub'] ?? '')
            : '';

        $feed = strtolower(
            trim(
                (string) ($_GET['feed'] ?? 'all')
            )
        );

        if (
            !in_array(
                $feed,
                [
                    'all',
                    'audio',
                    'video',
                    'interactions',
                ],
                true
            )
        ) {
            $feed = 'all';
        }

        $posts = $this->posts->listPublishedByAuthor(
            (string) $profile['oauth_sub'],
            $pageSize,
            0,
            $currentSub,
            $feed
        );

        $totalPosts = $this->posts->countPublishedByAuthor(
            (string) $profile['oauth_sub'],
            $currentSub,
            $feed
        );

        foreach ($posts as &$post) {

            $publishedAt = trim(
                (string) ($post['published_at'] ?? '')
            );

            $post['published_at_formatted'] = $publishedAt !== ''
                ? date(
                    'd/m/Y H:i',
                    strtotime($publishedAt)
                )
                : '';

            $post['can_delete'] = false;
            $post['can_edit'] = false;
            $post['edit_expires_at'] = null;

            $post['presence'] = $presence;

            $post['is_saved'] = false;
        }

        unset($post);

        if ($noIndex) {
            $this->response->header('X-Robots-Tag', 'noindex, nofollow');
        }

        $profilePath = '/profile/' . rawurlencode((string) $profile['username']);

        $description = trim((string) ($profile['bio'] ?? ''));

        if ($description === '') {
            $description = sprintf(
                'Profilo pubblico di %s su Monoverse Community.',
                (string) $profile['username']
            );
        }

        $followersCount = $this->follows->followersCount(
            (string) $profile['oauth_sub']
        );

        $followingCount = $this->follows->followingCount(
            (string) $profile['oauth_sub']
        );

        $isFollowing = false;
        $hasBlocked = false;
        $isBlockedBy = false;
        $moderation = $this->moderation->findBySub(
            (string) $profile['oauth_sub']
        );

        $isMuted = !empty($moderation['muted']);
        $isBanned = !empty($moderation['banned']);

        $isProfileModerator = $this->moderators->isModerator(
            (string) ($profile['oauth_sub'] ?? '')
        );

        if (
            is_array($currentUser)
            && !empty($currentUser['sub'])
            && $currentUser['sub'] !== ($profile['oauth_sub'] ?? '')
        ) {
            $isFollowing = $this->follows->isFollowing(
                (string) $currentUser['sub'],
                (string) $profile['oauth_sub']
            );

           if (
               is_array($currentUser)
               && !empty($currentUser['sub'])
               && $currentUser['sub'] !== ($profile['oauth_sub'] ?? '')
           ) {
               $hasBlocked = $this->blocks->isBlocked(
                   (string) $currentUser['sub'],
                   (string) $profile['oauth_sub']
               );

               $isBlockedBy = $this->blocks->isBlocked(
                   (string) $profile['oauth_sub'],
                   (string) $currentUser['sub']
               );
           }
        }

        $widgetAreas = [
            'beforeContent' => $this->blockManager->renderArea(
                'profile',
                'before-content'
            ),
            'sidebar' => $this->blockManager->renderArea(
                'profile',
                'sidebar'
            ),
            'afterContent' => $this->blockManager->renderArea(
                'profile',
                'after-content'
            ),
        ];

        $this->render('profile', [
            'title' => (string) ($profile['username'] ?? 'Profilo'),
            'metaDescription' => $description,
            'canonicalPath' => $profilePath,
            'openGraph' => [
                'type' => 'profile',
                'title' => (string) ($profile['username'] ?? 'Profilo'),
                'description' => $description,
                'path' => $profilePath,
                'image' => $avatarUrl !== '' ? $avatarUrl : null,
            ],
            'noIndex' => $noIndex,
            'user' => $this->session->get('auth.user'),
            'profile' => $profile,
            'dogeTipAddress' => $dogeTipAddress,
            'posts' => $posts,
            'totalPosts' => $totalPosts,
            'pageSize' => $pageSize,
            'feed' => $feed,
            'interests' => $interests,
            'avatarUrl' => $avatarUrl,
            'irc' => $irc,
            'presence' => $presence,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'isFollowing' => $isFollowing,
            'hasBlocked' => $hasBlocked,
            'isBlockedBy' => $isBlockedBy,
            'isMuted' => $isMuted,
            'isBanned' => $isBanned,
            'isProfileModerator' => $isProfileModerator,
            'moderation' => $moderation,
            'widgetAreas' => $widgetAreas,
            'blockCssFiles' => $this->blockManager->stylesheets(),
        ]);
    }

    public function load(string $username): void
    {
        $pageSize = max(
            1,
            (int) $this->config->get(
                'app.infinite_scroll_page_size',
                20
            )
        );

        $username = trim(rawurldecode($username));

        if ($username === '') {
            $this->notFound();
            return;
        }

        $profile = $this->profiles->findPublicByUsername($username);

        if (!$profile) {
            $this->notFound();
            return;
        }

        $currentUser = $this->session->get('auth.user');

        $currentSub = is_array($currentUser)
            ? (string) ($currentUser['sub'] ?? '')
            : '';

        $offset = max(
            0,
            (int) ($_GET['offset'] ?? 0)
        );

        $feed = strtolower(
            trim(
                (string) ($_GET['feed'] ?? 'all')
            )
        );

        if (
            !in_array(
                $feed,
                [
                    'all',
                    'audio',
                    'video',
                    'interactions',
                ],
                true
            )
        ) {
            $feed = 'all';
        }

        $presence = $this->simosnap->getAccountPresence(
            (string) $profile['username']
        );

        $posts = $this->posts->listPublishedByAuthor(
            (string) $profile['oauth_sub'],
            $pageSize,
            $offset,
            $currentSub,
            $feed
        );

        foreach ($posts as &$post) {

            $publishedAt = trim(
                (string) ($post['published_at'] ?? '')
            );

            $post['published_at_formatted'] = $publishedAt !== ''
                ? date(
                    'd/m/Y H:i',
                    strtotime($publishedAt)
                )
                : '';

            $post['can_delete'] = false;
            $post['can_edit'] = false;
            $post['edit_expires_at'] = null;

            $post['presence'] = $presence;

            $post['is_saved'] = false;
        }

        unset($post);

        foreach ($posts as $post) {
            echo $this->view->component(
                'ping-card',
                [
                    'post' => $post,
                    'user' => $currentUser,
                    'session' => $this->session,
                ]
            );
        }
    }

    public function notifyDogeTip(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user || empty($user['sub'])) {
            $this->response->json(
                [
                    'ok' => false,
                    'error' => 'Autenticazione richiesta.',
                ],
                401
            );
            return;
        }

        $username = trim(
            (string) ($_POST['username'] ?? '')
        );

        $amount = trim(
            (string) ($_POST['amount'] ?? '')
        );

        $txId = trim(
            (string) ($_POST['tx_id'] ?? '')
        );

        if (
            $username === ''
            || !preg_match(
                '/^\d+(?:\.\d{1,8})?$/',
                $amount
            )
            || (float) $amount <= 0
            || !preg_match(
                '/^[a-fA-F0-9]{64}$/',
                $txId
            )
        ) {
            $this->response->json(
                [
                    'ok' => false,
                    'error' => 'Dati della mancia non validi.',
                ],
                422
            );
            return;
        }

        $recipient = $this->profiles->findPublicByUsername(
            $username
        );

        if (!$recipient) {
            $this->response->json(
                [
                    'ok' => false,
                    'error' => 'Profilo destinatario non disponibile.',
                ],
                404
            );
            return;
        }

        $created =
            $this->notifications->createDogeTipNotification(
                (string) $recipient['oauth_sub'],
                (string) $user['sub'],
                $txId,
                $amount
            );

        if (!$created) {
            $this->response->json(
                [
                    'ok' => false,
                    'error' => 'Impossibile creare la notifica.',
                ],
                500
            );
            return;
        }

        $this->response->json([
            'ok' => true,
        ]);
    }

    public function follow(string $username): void
    {
        $user = $this->session->get('auth.user');

        if (!$user || empty($user['sub'])) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $profile = $this->profiles->findPublicByUsername(
            trim(rawurldecode($username))
        );

        if (!$profile) {
            $this->notFound();
            return;
        }

        $this->follows->follow(
            (string) $user['sub'],
            (string) $profile['oauth_sub']
        );

        $this->response->redirect(
            '/profile/' . rawurlencode((string) $profile['username'])
        );
    }

    public function unfollow(string $username): void
    {
        $user = $this->session->get('auth.user');

        if (!$user || empty($user['sub'])) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $profile = $this->profiles->findPublicByUsername(
            trim(rawurldecode($username))
        );

        if (!$profile) {
            $this->notFound();
            return;
        }

        $this->follows->unfollow(
            (string) $user['sub'],
            (string) $profile['oauth_sub']
        );

        $this->response->redirect(
            '/profile/' . rawurlencode((string) $profile['username'])
        );
    }

    public function block(string $username): void
    {
        $user = $this->session->get('auth.user');

        if (!$user || empty($user['sub'])) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $profile = $this->profiles->findByUsername(
            trim(rawurldecode($username))
        );

        if (!$profile) {
            $this->notFound();
            return;
        }

        $this->blocks->block(
            (string) $user['sub'],
            (string) $profile['oauth_sub']
        );

        $this->follows->unfollow(
            (string) $user['sub'],
            (string) $profile['oauth_sub']
        );

        $referer = trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));

        if ($referer !== '') {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            $currentHost = $_SERVER['HTTP_HOST'] ?? '';

            if (
                is_string($refererHost)
                && $refererHost !== ''
                && strcasecmp($refererHost, (string) $currentHost) === 0
            ) {
                $refererPath = parse_url($referer, PHP_URL_PATH);
                $refererQuery = parse_url($referer, PHP_URL_QUERY);

                if (is_string($refererPath) && str_starts_with($refererPath, '/')) {
                    $redirectTo = $refererPath;

                    if (is_string($refererQuery) && $refererQuery !== '') {
                        $redirectTo .= '?' . $refererQuery;
                    }

                    $this->response->redirect($redirectTo);
                    return;
                }
            }
        }

        $this->response->redirect('/ping');
    }

    public function unblock(string $username): void
    {
        $user = $this->session->get('auth.user');

        if (!$user || empty($user['sub'])) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $profile = $this->profiles->findByUsername(
            trim(rawurldecode($username))
        );

        if (!$profile) {
            $this->notFound();
            return;
        }

        $this->blocks->unblock(
            (string) $user['sub'],
            (string) $profile['oauth_sub']
        );

        $this->response->redirect(
            '/profile/' . rawurlencode((string) $profile['username'])
        );
    }

    private function decodeInterests(string $value): array
    {
        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(
            array_filter(
                $decoded,
                static fn ($interest): bool =>
                    is_string($interest) && trim($interest) !== ''
            )
        );
    }

    protected function notFound(): void
    {
        $this->response
            ->status(404)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send('Profilo non trovato.');
    }
}
