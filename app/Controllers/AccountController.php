<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\Request;
use Monoverse\Core\View;
use Monoverse\Services\AuthorizationService;
use Monoverse\Services\BlockService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\SettingsService;
use Monoverse\Services\SavedItemService;
use Monoverse\Services\PostService;
use Monoverse\Services\UserModerationService;
use Monoverse\Services\FollowService;
use Monoverse\Services\SimosNapService;
use Monoverse\Services\ArticleService;
use Monoverse\Services\CategoryService;

class AccountController extends BaseController
{
    public function __construct(
        View $view,
        Response $response,
        Session $session,
        private Request $request,
        NotificationService $notifications,
        private ProfileService $profiles,
        private AuthorizationService $authorization,
        private BlockService $blocks,
        private FollowService $follows,
        private SimosNapService $simosnap,
        private SavedItemService $savedItems,
        private PostService $posts,
        private ArticleService $articles,
        private CategoryService $categories,
        private UserModerationService $userModeration,
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
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $profile = !empty($user['sub'])
            ? $this->profiles->findBySub((string) $user['sub'])
            : false;
                
        $simosnapDogecoinAddress = null;
        
        $username = trim((string) (
            $user['username']
            ?? $user['preferred_username']
            ?? $user['nickname']
            ?? ''
        ));
        
        if ($username !== '') {
            $simosnapDogecoinAddress =
                $this->simosnap->getDogecoinAddress($username);
        }

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8');

        $following = [];

        if (!empty($user['sub'])) {

            $following = $this->follows->findFollowing(
                (string) $user['sub']
            );

            foreach ($following as &$followed) {

                $followed['presence'] = $this->simosnap->getAccountPresence(
                    (string) ($followed['username'] ?? '')
                );

            }

            unset($followed);
        }

        $this->render('account', [
            'title' => 'Account',
            'profile' => $profile ?: [],
            'simosnapDogecoinAddress' => $simosnapDogecoinAddress,
            'isModerator' => $this->authorization->isModerator($user),
            'following' => $following,
        ]);
    }

    public function saved(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $userSub = trim((string) ($user['sub'] ?? ''));

        if ($userSub === '') {
            $this->response->redirect('/account');
            return;
        }

        $savedItems = [];

        foreach (
            $this->savedItems->findByUser($userSub) as $item
        ) {
            switch ((string) ($item['object_type'] ?? '')) {

                case 'post':

                    $post = $this->posts->findByUuid(
                        (string) ($item['object_uuid'] ?? '')
                    );

                    if (!$post) {
                        continue 2;
                    }

                    $post['object_type'] = 'post';

                    $post['saved_at'] = (string) ($item['created_at'] ?? '');

                    $savedItems[] = $post;

                    break;
                case 'article':

                    $article = $this->articles->findByUuid(
                        (string) ($item['object_uuid'] ?? '')
                    );

                    if (!$article) {
                        continue 2;
                    }

                    $article['object_type'] = 'article';
                    $article['saved_at'] = (string) ($item['created_at'] ?? '');

                    $savedItems[] = $article;

                    break;
            }
        }

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8');

        $this->render('account-saved', [
            'title'      => 'Contenuti salvati',
            'savedItems' => $savedItems,
        ]);
    }
    
    public function articles(): void
    {
        $user = $this->session->get('auth.user');
    
        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }
    
        $sub = trim((string) ($user['sub'] ?? ''));
    
        if ($sub === '') {
            $this->response->redirect('/account');
            return;
        }
    
        $articles = $this->articles->listSubmittedByUser($sub);
    
        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8');
    
        $this->render('account-articles', [
            'title' => 'Articoli proposti',
            'articles' => $articles,
        ]);
    }
    
    public function editArticle(string $uuid): void
    {
        $user = $this->session->get('auth.user');
    
        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }
    
        $sub = trim((string) ($user['sub'] ?? ''));
    
        if ($sub === '') {
            $this->response->redirect('/account/articles');
            return;
        }
    
        $article = $this->articles->findEditableSubmissionByUser(
            $uuid,
            $sub
        );
    
        if (!$article) {
            $this->response->redirect('/account/articles');
            return;
        }
        
       $categories = $this->categories->listAll('chanzine');
    
        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8');
    
        $this->render('account-article-edit', [
            'title' => 'Modifica proposta',
            'article' => $article,
            'categories' => $categories,
            'error' => $this->session->getFlash('error'),
        ]);
    }
    
    public function updateArticle(string $uuid): void
    {
        $user = $this->session->get('auth.user');
    
        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }
    
        $sub = trim((string) ($user['sub'] ?? ''));
    
        if ($sub === '') {
            $this->response->redirect('/account/articles');
            return;
        }
    
        $article = $this->articles->findEditableSubmissionByUser(
            $uuid,
            $sub
        );
    
        if (!$article) {
            $this->response->redirect('/account/articles');
            return;
        }
    
        $title = trim(
            (string) $this->request->post('title', '')
        );
    
        $excerpt = trim(
            (string) $this->request->post('excerpt', '')
        );
    
        $content = trim(
            (string) $this->request->post('content', '')
        );
    
        $categoryId = (int) $this->request->post(
            'category_id',
            0
        );
    
        $editPath = '/account/articles/'
            . rawurlencode($uuid)
            . '/edit';
    
        $isValidCategory = false;
    
        if ($categoryId > 0) {
            foreach (
                $this->categories->listAll('chanzine')
                as $category
            ) {
                if (
                    (int) ($category['id'] ?? 0)
                    === $categoryId
                ) {
                    $isValidCategory = true;
                    break;
                }
            }
        }
    
        if (!$isValidCategory) {
            $this->session->flash(
                'error',
                'Seleziona una categoria valida.'
            );
    
            $this->response->redirect($editPath);
            return;
        }
    
        if ($title === '' || $content === '') {
            $this->session->flash(
                'error',
                'Titolo e contenuto sono obbligatori.'
            );
    
            $this->response->redirect($editPath);
            return;
        }
    
        $slug = $this->generateUniqueArticleSlug(
            $title,
            $uuid
        );
    
        $cover = !empty($article['cover'])
            ? (string) $article['cover']
            : null;
    
        $newCover = null;
    
        if (
            isset($_FILES['cover'])
            && $_FILES['cover']['error'] === UPLOAD_ERR_OK
        ) {
            $mime = mime_content_type(
                $_FILES['cover']['tmp_name']
            );
    
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];
    
            if (!isset($allowed[$mime])) {
                $this->session->flash(
                    'error',
                    'La cover deve essere JPEG, PNG o WebP.'
                );
    
                $this->response->redirect($editPath);
                return;
            }
    
            $directory = __DIR__
                . '/../../storage/chanzine/'
                . date('Y')
                . '/'
                . date('m');
    
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
    
            $filename = bin2hex(random_bytes(16))
                . '.'
                . $allowed[$mime];
    
            $destination =
                $directory . '/' . $filename;
    
            if (!move_uploaded_file(
                $_FILES['cover']['tmp_name'],
                $destination
            )) {
                $this->session->flash(
                    'error',
                    'Impossibile salvare la cover.'
                );
    
                $this->response->redirect($editPath);
                return;
            }
    
            $newCover =
                '/storage/chanzine/'
                . date('Y')
                . '/'
                . date('m')
                . '/'
                . $filename;
    
            $cover = $newCover;
        }
    
        $updated = $this->articles->updateSubmissionByUser(
            $uuid,
            $sub,
            [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt !== ''
                    ? $excerpt
                    : null,
                'content' => $content,
                'cover' => $cover,
                'category_id' => $categoryId,
            ]
        );
    
        if (!$updated) {
            if ($newCover !== null) {
                $newCoverFile = __DIR__
                    . '/../../'
                    . ltrim($newCover, '/');
    
                if (is_file($newCoverFile)) {
                    @unlink($newCoverFile);
                }
            }
    
            $this->session->flash(
                'error',
                'Non è stato possibile salvare le modifiche.'
            );
    
            $this->response->redirect($editPath);
            return;
        }
    
        if (
            $newCover !== null
            && !empty($article['cover'])
            && $article['cover'] !== $newCover
        ) {
            $oldCoverFile = __DIR__
                . '/../../'
                . ltrim(
                    (string) $article['cover'],
                    '/'
                );
    
            if (is_file($oldCoverFile)) {
                @unlink($oldCoverFile);
            }
        }
    
        $this->session->flash(
            'success',
            'Le modifiche alla proposta sono state salvate.'
        );
    
        $this->response->redirect('/account/articles');
    }

    public function save(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $saveTarget = (string) ($_POST['save_target'] ?? 'local');

        if ($saveTarget !== 'database') {
            $this->response->redirect('/account');
            return;
        }

        $username = trim((string) (
            $user['username']
            ?? $user['preferred_username']
            ?? $user['nickname']
            ?? ''
        ));

        $aliases = $user['aliases'] ?? [];

        if (!is_array($aliases)) {
            $aliases = [];
        }

        $data = [
            'oauth_sub' => (string) ($user['sub'] ?? ''),
            'oauth_uid' => (string) ($user['uid'] ?? ''),
            'username' => $username,
            'nickname' => trim((string) ($_POST['nickname'] ?? '')),
            'avatar_url' => (string) ($user['avatar_url'] ?? ''),
            'aliases' => $aliases,
            'age' => trim((string) ($_POST['age'] ?? '')),
            'city' => trim((string) ($_POST['city'] ?? '')),
            'sex' => (string) ($_POST['sex'] ?? 'U'),
            'public_profile' => isset($_POST['public_profile']) ? 1 : 0,
            'allow_indexing' => isset($_POST['allow_indexing']) ? 1 : 0,
            'show_avatar' => isset($_POST['show_avatar']) ? 1 : 0,
            'show_aliases' => isset($_POST['show_aliases']) ? 1 : 0,
            'show_age' => isset($_POST['show_age']) ? 1 : 0,
            'show_city' => isset($_POST['show_city']) ? 1 : 0,
            'show_sex' => isset($_POST['show_sex']) ? 1 : 0,
            'show_irc_stats' => isset($_POST['show_irc_stats']) ? 1 : 0,
        ];

        $this->profiles->upsert($data);

        $this->response->redirect('/account?saved=1');
    }
    
    public function saveDogeTips(): void
    {
        $user = $this->session->get('auth.user');
    
        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }
    
        $sub = trim(
            (string) ($user['sub'] ?? '')
        );
    
        if ($sub === '') {
            $this->response->redirect('/account');
            return;
        }
    
        $profile = $this->profiles->findBySub($sub);
    
        if (!$profile) {
            $this->response->redirect('/account');
            return;
        }
    
        $source = trim(
            (string) $this->request->post(
                'doge_tip_source',
                ''
            )
        );
    
        if (
            !in_array(
                $source,
                [
                    'mydogemask',
                    'simosnap',
                ],
                true
            )
        ) {
            $source = null;
        }
    
        $address = null;
    
        if ($source === 'mydogemask') {
            $address = trim(
                (string) $this->request->post(
                    'doge_tip_address',
                    ''
                )
            );
    
            if ($address === '') {
                $address = null;
            }
        }
    
        if ($source === 'simosnap') {
            $username = trim((string) (
                $user['username']
                ?? $user['preferred_username']
                ?? $user['nickname']
                ?? ''
            ));
    
            $simosnapAddress = $username !== ''
                ? $this->simosnap->getDogecoinAddress($username)
                : null;
    
            if ($simosnapAddress === null) {
                $this->session->flash(
                    'error',
                    'Non hai configurato un indirizzo Dogecoin sul tuo account SimosNap.'
                );
    
                $this->response->redirect('/account');
                return;
            }
        }
    
        $this->profiles->updateDogeTipSettings(
            $sub,
            $source,
            $address
        );
    
        $this->response->redirect(
            '/account?doge_saved=1'
        );
    }

    public function publicProfile(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $profile = !empty($user['sub'])
            ? $this->profiles->findBySub((string) $user['sub'])
            : false;

        if (!$profile || empty($profile['public_profile'])) {
            $this->response->redirect('/account');
            return;
        }

        $this->render('account-profile', [
            'title' => 'Profilo pubblico',
            'profile' => $profile,
        ]);
    }

    public function savePublicProfile(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $sub = (string) ($user['sub'] ?? '');

        if ($sub === '') {
            $this->response->redirect('/account');
            return;
        }

        $profile = $this->profiles->findBySub($sub);

        if (!$profile || empty($profile['public_profile'])) {
            $this->response->redirect('/account');
            return;
        }

        $bio = trim((string) ($_POST['bio'] ?? ''));
        $motto = trim((string) ($_POST['motto'] ?? ''));
        $website = trim((string) ($_POST['website'] ?? ''));
        $telegram = trim((string) ($_POST['telegram'] ?? ''));

        $interests = $_POST['interests'] ?? [];

        if (!is_array($interests)) {
            $interests = [];
        }

        $interests = array_values(
            array_filter(
                array_map(
                    static fn ($value): string => trim((string) $value),
                    $interests
                ),
                static fn (string $value): bool => $value !== ''
            )
        );

        if (mb_strlen($bio) > 1000) {
            $bio = mb_substr($bio, 0, 1000);
        }

        if (mb_strlen($motto) > 120) {
            $motto = mb_substr($motto, 0, 120);
        }

        if (mb_strlen($website) > 255) {
            $website = mb_substr($website, 0, 255);
        }

        if (mb_strlen($telegram) > 100) {
            $telegram = mb_substr($telegram, 0, 100);
        }

        $this->profiles->updatePublicProfile(
            $sub,
            $bio,
            $motto,
            $interests,
            $website,
            $telegram
        );

        $this->response->redirect('/account/profile?saved=1');
    }

    public function blocked(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $sub = trim((string) ($user['sub'] ?? ''));

        if ($sub === '') {
            $this->response->redirect('/account');
            return;
        }

        $blockedUsers = $this->blocks->listBlocked($sub);

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8');

        $this->render('account-blocked', [
            'title' => 'Utenti bloccati',
            'blockedUsers' => $blockedUsers,
        ]);
    }

    public function unblockBlockedUser(string $username): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $sub = trim((string) ($user['sub'] ?? ''));

        if ($sub === '') {
            $this->response->redirect('/account');
            return;
        }

        $username = trim(rawurldecode($username));

        if ($username === '') {
            $this->response->redirect('/account/blocked');
            return;
        }

        $blockedUser = $this->blocks->findBlockedByUsername(
            $sub,
            $username
        );

        if ($blockedUser === false) {
            $this->response->redirect('/account/blocked');
            return;
        }

        $blockedSub = trim((string) ($blockedUser['blocked_sub'] ?? ''));

        if ($blockedSub !== '') {
            $this->blocks->unblock(
                $sub,
                $blockedSub
            );
        }

        $this->response->redirect('/account/blocked?unblocked=1');
    }

    public function deleteProfile(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $sub = (string) ($user['sub'] ?? '');

        if ($sub !== '') {
            $this->profiles->deleteBySub($sub);

            if ($this->authorization->isBanned($user)) {
                $this->response->redirect('/account/suspended?deleted=1');
                return;
            }
        }

        $this->response->redirect('/account');
    }

    public function suspended(): void
    {
        $user = $this->session->get('auth.user');

        if (!$user) {
            $this->response->redirect('/oauth/login');
            return;
        }

        $moderation = $this->userModeration->findBySub(
            (string) $user['sub']
        );

        $this->render('account-suspended', [
            'title'      => 'Account sospeso',
            'moderation' => $moderation,
        ]);
    }
    
    private function slugify(string $value): string
    {
        $value = trim($value);
    
        $converted = iconv(
            'UTF-8',
            'ASCII//TRANSLIT//IGNORE',
            $value
        );
    
        if ($converted !== false) {
            $value = $converted;
        }
    
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');
    
        return substr($value, 0, 255);
    }
    
    private function generateUniqueArticleSlug(
        string $title,
        string $uuid
    ): string {
        $baseSlug = $this->slugify($title);
    
        if ($baseSlug === '') {
            $baseSlug = 'articolo';
        }
    
        $slug = $baseSlug;
        $counter = 2;
    
        while ($this->articles->slugExists($slug, $uuid)) {
            $suffix = '-' . $counter;
    
            $slug = substr(
                $baseSlug,
                0,
                255 - strlen($suffix)
            ) . $suffix;
    
            $counter++;
        }
    
        return $slug;
    }
}
