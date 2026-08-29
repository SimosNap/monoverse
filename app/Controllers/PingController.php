<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Blocks\BlockManager;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Helpers\DateHelper;
use Monoverse\Services\CommentService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\PostService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\VoteService;
use Monoverse\Services\AuthorizationService;
use Monoverse\Services\SavedItemService;
use Monoverse\Services\SimosNapService;
use Monoverse\Services\SettingsService;
use Monoverse\Services\Translator;
use Monoverse\Core\Config;

class PingController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications,
		private PostService $posts,
		private CommentService $comments,
		private ProfileService $profiles,
		private VoteService $votes,
		private SavedItemService $savedItems,
		private SimosNapService $simosnap,
		private AuthorizationService $authorization,
		private BlockManager $blocks,
		private Config $config,
		private Translator $translator,
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
		$pageSize = max(
			1,
			(int) $this->config->get(
				'app.infinite_scroll_page_size',
				20
			)
		);

		$user = $this->session->get('auth.user');
		$currentSub = $user ? (string) $user['sub'] : null;

		$query = trim(
			(string) ($_GET['q'] ?? '')
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
					'following',
					'interactions',
					'audio',
					'video',
				],
				true
			)
			|| (
				in_array(
					$feed,
					[
						'following',
						'interactions',
					],
					true
				)
				&& $currentSub === null
			)
		) {
			$feed = 'all';
		}

		if ($query !== '') {

			$posts = $this->posts->searchPublished(
				$query,
				$pageSize,
				0,
				$currentSub
			);

			$feed = 'all';

		} elseif ($feed === 'following') {

			$posts = $this->posts->listPublishedFollowing(
				$currentSub,
				$pageSize,
				0
			);

		} elseif ($feed === 'interactions') {

			$posts = $this->posts->listPublishedInteractions(
				$currentSub,
				$pageSize,
				0
			);

		} elseif ($feed === 'audio') {

			$posts = $this->posts->listPublishedByMediaType(
				'audio',
				$pageSize,
				0,
				$currentSub
			);

		} elseif ($feed === 'video') {

			$posts = $this->posts->listPublishedByMediaType(
				'video',
				$pageSize,
				0,
				$currentSub
			);

		} else {

			$posts = $this->posts->listPublished(
				$pageSize,
				0,
				$currentSub
			);

		}

		$profile = null;

		if ($user) {
			$profile = $this->profiles->findBySub($user['sub']);
		}

		foreach ($posts as &$post) {

			$post['is_saved'] = $currentSub !== null
			? $this->savedItems->isSaved(
				$currentSub,
				'post',
				(string) $post['uuid']
			)
			: false;

			$post['published_at_formatted'] = DateHelper::timeAgo(
				$post['published_at'],
				false,
				$this->translator->getLocale()
			);

			$post['presence'] = $this->simosnap->getAccountPresence(
				(string) ($post['username'] ?? '')
			);

			$post['can_delete'] = $this->authorization->canDeletePost(
				$user ?? [],
				$post
			);

			$post['can_edit'] = $this->authorization->canEditPost(
				$user ?? [],
				$post
			);

			$post['edit_expires_at'] = $this->authorization->getPostEditExpiresAt(
				$post
			);

		}

		unset($post);

		$widgetAreas = [
			'beforeContent' => $this->blocks->renderArea(
				'ping',
				'before-content'
			),
			'sidebar' => $this->blocks->renderArea(
				'ping',
				'sidebar'
			),
			'afterContent' => $this->blocks->renderArea(
				'ping',
				'after-content'
			),
		];

		$this->render('ping', [
			'title' => 'Ping',
			'posts' => $posts,
			'feed' => $feed,
			'query' => $query,
			'pageSize' => $pageSize,
			'isLogged' => $this->session->has('auth.user'),
			'profile' => $profile,
			'widgetAreas' => $widgetAreas,
			'blockCssFiles' => $this->blocks->stylesheets(),
		]);
	}

	public function rss(): void
	{
		$siteUrl = rtrim(
			(string) ($this->settings->get('site_url') ?? ''),
			'/'
		);

		if ($siteUrl === '') {
			$this->response
				->status(500)
				->header('Content-Type', 'text/plain; charset=utf-8')
				->send('Site URL not configured.');

			return;
		}

		$siteName = trim(
			(string) ($this->settings->get('site_name') ?? '')
		);

		if ($siteName === '') {
			$siteName = 'Monoverse';
		}

		$posts = $this->posts->listPublishedForRss(50);

		$escape = static fn (string $value): string =>
			htmlspecialchars(
				$value,
				ENT_XML1 | ENT_QUOTES,
				'UTF-8'
			);

		$channelUrl = $siteUrl . '/ping';
		$feedUrl = $siteUrl . '/ping/rss';

		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
		$xml .= '<channel>' . "\n";
		$xml .= '<title>'
			. $escape('Ping - ' . $siteName)
			. '</title>' . "\n";
		$xml .= '<link>'
			. $escape($channelUrl)
			. '</link>' . "\n";
		$xml .= '<description>'
			. $escape('Ultimi Ping pubblicati su ' . $siteName)
			. '</description>' . "\n";
		$xml .= '<language>it</language>' . "\n";
		$xml .= '<atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="'
			. $escape($feedUrl)
			. '" rel="self" type="application/rss+xml" />' . "\n";

		foreach ($posts as $post) {
			$uuid = trim(
				(string) ($post['uuid'] ?? '')
			);

			$content = trim(
				(string) ($post['content'] ?? '')
			);

			$nickname = trim(
				(string) ($post['nickname'] ?? '')
			);

			$username = trim(
				(string) ($post['username'] ?? '')
			);

			$publishedAt = trim(
				(string) ($post['published_at'] ?? '')
			);

			$media = is_array($post['media'] ?? null)
				? $post['media']
				: [];

			$primaryMedia = $media[0] ?? null;
			$primaryMediaUrl = '';

			if (is_array($primaryMedia)) {
				$publicUrl = trim(
					(string) ($primaryMedia['public_url'] ?? '')
				);

				if ($publicUrl !== '') {
					$primaryMediaUrl = str_starts_with(
						$publicUrl,
						'http://'
					) || str_starts_with(
						$publicUrl,
						'https://'
					)
						? $publicUrl
						: $siteUrl . '/' . ltrim($publicUrl, '/');
				}
			}

			if ($uuid === '') {
				continue;
			}

			$author = $nickname !== ''
				? $nickname
				: $username;

			if ($author === '') {
				$author = 'Monoverse';
			}

			$pingUrl = $siteUrl
				. '/ping/'
				. rawurlencode($uuid);

			$title = $author;

			if ($content === '' && is_array($primaryMedia)) {
				$mediaType = trim(
					(string) ($primaryMedia['media_type'] ?? '')
				);

				if ($mediaType !== '') {
					$title .= ': ' . ucfirst($mediaType);
				}
			}

			if ($content !== '') {
				$plainContent = preg_replace(
					'/\s+/u',
					' ',
					strip_tags($content)
				);

				$plainContent = trim(
					(string) $plainContent
				);

				if ($plainContent !== '') {
					$title .= ': ' . mb_substr(
						$plainContent,
						0,
						120
					);
				}
			}

			$xml .= '<item>' . "\n";
			$xml .= '<title>'
				. $escape($title)
				. '</title>' . "\n";
			$xml .= '<link>'
				. $escape($pingUrl)
				. '</link>' . "\n";
			$xml .= '<guid isPermaLink="false">'
				. $escape('urn:uuid:' . $uuid)
				. '</guid>' . "\n";

			$xml .= '<dc:creator>'
				. $escape($author)
				. '</dc:creator>' . "\n";

			if (
				$primaryMediaUrl !== ''
				&& is_array($primaryMedia)
			) {
				$mimeType = trim(
					(string) ($primaryMedia['mime_type'] ?? '')
				);

				$fileSize = max(
					0,
					(int) ($primaryMedia['file_size'] ?? 0)
				);

				if ($mimeType !== '') {
					$xml .= '<enclosure url="'
						. $escape($primaryMediaUrl)
						. '" length="'
						. $fileSize
						. '" type="'
						. $escape($mimeType)
						. '" />' . "\n";
				}
			}

			if ($content !== '') {
				$xml .= '<description>'
					. $escape($content)
					. '</description>' . "\n";
			}

			if ($publishedAt !== '') {
				$timestamp = strtotime($publishedAt);

				if ($timestamp !== false) {
					$xml .= '<pubDate>'
						. gmdate(DATE_RSS, $timestamp)
						. '</pubDate>' . "\n";
				}
			}

			$xml .= '</item>' . "\n";
		}

		$xml .= '</channel>' . "\n";
		$xml .= '</rss>' . "\n";

		$this->response
			->header(
				'Content-Type',
				'application/rss+xml; charset=utf-8'
			)
			->send($xml);
	}

	public function load(): void
	{
		$pageSize = max(
			1,
			(int) $this->config->get(
				'app.infinite_scroll_page_size',
				20
			)
		);

		$user = $this->session->get('auth.user');
		$currentSub = $user ? (string) $user['sub'] : null;

		$query = trim(
			(string) ($_GET['q'] ?? '')
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
					'following',
					'interactions',
					'audio',
					'video',
				],
				true
			)
			|| (
				in_array(
					$feed,
					[
						'following',
						'interactions',
					],
					true
				)
				&& $currentSub === null
			)
		) {
			$feed = 'all';
		}

		$offset = max(
			0,
			(int) ($_GET['offset'] ?? 0)
		);

		if ($query !== '') {

			$posts = $this->posts->searchPublished(
				$query,
				$pageSize,
				$offset,
				$currentSub
			);

			$feed = 'all';

		} elseif ($feed === 'following') {

			$posts = $this->posts->listPublishedFollowing(
				$currentSub,
				$pageSize,
				$offset
			);

		} elseif ($feed === 'interactions') {

			$posts = $this->posts->listPublishedInteractions(
				$currentSub,
				$pageSize,
				$offset
			);

		} elseif ($feed === 'audio') {

			$posts = $this->posts->listPublishedByMediaType(
				'audio',
				$pageSize,
				$offset,
				$currentSub
			);

		} elseif ($feed === 'video') {

			$posts = $this->posts->listPublishedByMediaType(
				'video',
				$pageSize,
				$offset,
				$currentSub
			);

		} else {

			$posts = $this->posts->listPublished(
				$pageSize,
				$offset,
				$currentSub
			);

		}

		foreach ($posts as &$post) {

			$post['is_saved'] = $currentSub !== null
				? $this->savedItems->isSaved(
					$currentSub,
					'post',
					(string) $post['uuid']
				)
				: false;

			$post['published_at_formatted'] = DateHelper::timeAgo(
				$post['published_at'],
				false,
				$this->translator->getLocale()
			);

			$post['presence'] = $this->simosnap->getAccountPresence(
				(string) ($post['username'] ?? '')
			);

			$post['can_delete'] = $this->authorization->canDeletePost(
				$user ?? [],
				$post
			);

			$post['can_edit'] = $this->authorization->canEditPost(
				$user ?? [],
				$post
			);

			$post['edit_expires_at'] = $this->authorization->getPostEditExpiresAt(
				$post
			);
		}

		unset($post);

		foreach ($posts as $post) {
			echo $this->view->component(
				'ping-card',
				[
					'post' => $post,
					'isLogged' => $this->session->has('auth.user'),
					'session' => $this->session,
				]
			);
		}
	}

	public function show(string $uuid): void
	{
		$pageSize = max(
			1,
			(int) $this->config->get(
				'app.infinite_scroll_page_size',
				20
			)
		);

		$user = $this->session->get('auth.user');

		$currentSub = $user ? (string) $user['sub'] : null;

		$post = $this->posts->findByUuid(
			$uuid,
			$currentSub
		);

		if (!$post) {
			$this->notFound();
			return;
		}

		$widgetAreas = [
			'beforeContent' => $this->blocks->renderArea(
				'ping-show',
				'before-content'
			),
			'sidebar' => $this->blocks->renderArea(
				'ping-show',
				'sidebar'
			),
			'afterContent' => $this->blocks->renderArea(
				'ping-show',
				'after-content'
			),
		];

		$post['is_saved'] = $currentSub !== null
		? $this->savedItems->isSaved(
			$currentSub,
			'post',
			(string) $post['uuid']
		)
		: false;

		if (
			!$this->authorization->canIgnoreBlocks($user ?? [])
			&& !empty($post['is_blocked_for_viewer'])
		) {

			$this->render('ping-show', [
				'title' => 'Contenuto non disponibile',
				'post' => null,
				'comments' => [],
				'isLogged' => $this->session->has('auth.user'),
				'blockedMessage' =>
					'Non hai il permesso di visualizzare questo Ping perché tra te e il suo autore esiste un blocco.',
				'widgetAreas' => $widgetAreas,
				'blockCssFiles' => $this->blocks->stylesheets(),
			]);

			return;
		}

		$post['published_at_formatted'] = DateHelper::timeAgo(
			$post['published_at'],
			false,
			$this->translator->getLocale()
		);

		$post['presence'] = $this->simosnap->getAccountPresence(
			(string) ($post['username'] ?? '')
		);

		$post['can_edit'] = $this->authorization->canEditPost(
			$user ?? [],
			$post
		);

		$post['edit_expires_at'] = $this->authorization->getPostEditExpiresAt(
			$post
		);

		$post['can_delete'] = $this->authorization->canDeletePost(
			$user ?? [],
			$post
		);

		$comments = $this->comments->listByPostId(
			(int) $post['id'],
			$currentSub,
			$pageSize,
			0
		);

		foreach ($comments as &$comment) {
			$comment['presence'] = $this->simosnap->getAccountPresence(
				(string) ($comment['username'] ?? '')
			);

			$comment['created_at_formatted'] = DateHelper::timeAgo(
				$comment['created_at'],
				false,
				$this->translator->getLocale()
			);

			$comment['can_edit'] = $this->authorization->canEditComment(
				$user ?? [],
				$comment
			);

			$comment['edit_expires_at'] = $this->authorization->getCommentEditExpiresAt(
				$comment
			);

			$comment['can_delete'] = $this->authorization->canDeleteComment(
				$user ?? [],
				$comment
			);
		}
		unset($comment);

		$pingPath = '/ping/' . rawurlencode((string) $post['uuid']);

		$plainContent = trim(
			preg_replace(
				'/\s+/u',
				' ',
				strip_tags((string) ($post['content'] ?? ''))
			) ?? ''
		);

		$description = $plainContent;

		if (mb_strlen($description) > 200) {
			$description = rtrim(
				mb_substr($description, 0, 197)
			) . '...';
		}

		$authorName = trim((string) (
			$post['username']
			?? $post['author_username']
			?? $post['nickname']
			?? ''
		));

		if ($authorName !== '') {
			$openGraphTitle = 'Ping di @' . $authorName;
		} else {
			$openGraphTitle = 'Ping';
		}

		if ($plainContent !== '') {
			$contentTitle = $plainContent;

			if (mb_strlen($contentTitle) > 70) {
				$contentTitle = rtrim(
					mb_substr($contentTitle, 0, 67)
				) . '...';
			}

			$openGraphTitle .= ' — ' . $contentTitle;
		}

		$openGraphImage = '';

		foreach (($post['media'] ?? []) as $media) {
			if (
				(string) ($media['media_type'] ?? '') === 'image'
				&& trim((string) ($media['public_url'] ?? '')) !== ''
			) {
				$openGraphImage = trim(
					(string) $media['public_url']
				);

				break;
			}
		}

		$showAvatar = (bool) ($post['show_avatar'] ?? false);
		$avatarUrl = trim((string) ($post['avatar_url'] ?? ''));

		if (
			$openGraphImage === ''
			&& $showAvatar
			&& $avatarUrl !== ''
		) {
			$openGraphImage = $avatarUrl;
		}

		$this->render('ping-show', [
			'title' => $openGraphTitle,
			'metaDescription' => $description,
			'canonicalPath' => $pingPath,
			'openGraph' => [
				'type' => 'article',
				'title' => $openGraphTitle,
				'description' => $description,
				'path' => $pingPath,
				'image' => $openGraphImage !== ''
					? $openGraphImage
					: null,
				'publishedTime' => !empty($post['published_at'])
				? date(
					DATE_ATOM,
					strtotime((string) $post['published_at'])
				)
				: null,
			],
			'post' => $post,
			'comments' => $comments,
			'pageSize' => $pageSize,
			'isLogged' => $this->session->has('auth.user'),
			'widgetAreas' => $widgetAreas,
			'blockCssFiles' => $this->blocks->stylesheets(),
		]);
	}

	public function loadComments(string $uuid): void
	{
		$pageSize = max(
			1,
			(int) $this->config->get(
				'app.infinite_scroll_page_size',
				20
			)
		);

		$user = $this->session->get('auth.user');

		$currentSub = $user
			? (string) ($user['sub'] ?? '')
			: null;

		$post = $this->posts->findByUuid(
			$uuid,
			$currentSub
		);

		if (!$post) {
			$this->notFound();
			return;
		}

		if (
			!$this->authorization->canIgnoreBlocks($user ?? [])
			&& !empty($post['is_blocked_for_viewer'])
		) {
			http_response_code(403);
			return;
		}

		$offset = max(
			0,
			(int) ($_GET['offset'] ?? 0)
		);

		$comments = $this->comments->listByPostId(
			(int) $post['id'],
			$currentSub,
			$pageSize,
			$offset
		);

		foreach ($comments as &$comment) {

			$comment['presence'] = $this->simosnap->getAccountPresence(
				(string) ($comment['username'] ?? '')
			);

			$comment['created_at_formatted'] = DateHelper::timeAgo(
				$comment['created_at'],
				false,
				$this->translator->getLocale()
			);

			$comment['can_edit'] = $this->authorization->canEditComment(
				$user ?? [],
				$comment
			);

			$comment['edit_expires_at'] = $this->authorization->getCommentEditExpiresAt(
				$comment
			);

			$comment['can_delete'] = $this->authorization->canDeleteComment(
				$user ?? [],
				$comment
			);
		}

		unset($comment);

		foreach ($comments as $comment) {
			echo $this->view->component(
				'pong-card',
				[
					'comment' => $comment,
					'post' => $post,
					'user' => $user,
					'session' => $this->session,
				]
			);
		}
	}

	public function commentDogeTip(string $uuid): void
	{
		$post = $this->posts->findByUuid($uuid);

		if (!$post) {
			$this->response->json(
				[
					'ok' => false,
					'error' => 'Ping non disponibile.',
				],
				404
			);
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->json(
				[
					'ok' => false,
					'error' => 'Autenticazione richiesta.',
				],
				401
			);
			return;
		}

		if (!$this->authorization->canPost($user)) {
			$this->response->json(
				[
					'ok' => false,
					'error' => 'Non puoi pubblicare Pong in questo momento.',
				],
				403
			);
			return;
		}

		$username = trim(
			(string) ($_POST['username'] ?? '')
		);

		$amount = trim(
			(string) ($_POST['amount'] ?? '')
		);

		$message = trim(
			(string) ($_POST['message'] ?? '')
		);

		if (
			$username === ''
			|| !preg_match(
				'/^\d+(?:\.\d{1,8})?$/',
				$amount
			)
			|| (float) $amount <= 0
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

		$displayAmount = rtrim(
			rtrim(
				$amount,
				'0'
			),
			'.'
		);

		$created = $this->comments->create([
			'post_id' => $post['id'],
			'author_sub' => $user['sub'],
			'content' => $message,
			'metadata' => [
				'source' => 'doge_tip',
				'amount' => $displayAmount,
				'recipient_username' =>
					(string) $recipient['username'],
			],
		]);

		if (!$created) {
			$this->response->json(
				[
					'ok' => false,
					'error' => 'Impossibile pubblicare il Pong.',
				],
				500
			);
			return;
		}

		$this->response->json([
			'ok' => true,
		]);
	}

	public function comment(string $uuid): void
	{
		$post = $this->posts->findByUuid($uuid);

		if (!$post) {
			$this->response->redirect('/ping');
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		if (!$this->authorization->canPost($user)) {
			$this->session->flash(
				'error',
				'Il tuo account è stato temporaneamente silenziato e non può pubblicare contenuti.'
			);

			$this->response->redirect('/ping/' . $uuid);
			return;
		}

		$content = trim((string) ($_POST['content'] ?? ''));

		if ($content === '') {
			$this->response->redirect('/ping/' . $uuid);
			return;
		}

		$this->comments->create([
			'post_id' => $post['id'],
			'author_sub' => $user['sub'],
			'content' => $content,
		]);

		$this->response->redirect('/ping/' . $uuid);
	}

	public function upvote(string $uuid): void
	{
		$post = $this->posts->findByUuid($uuid);

		if (!$post) {
			$this->response->redirect('/ping');
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$this->votes->vote(
			(int) $post['id'],
			(string) $user['sub'],
			1
		);

		$this->response->redirect('/ping/' . $uuid);
	}

	public function downvote(string $uuid): void
	{
		$post = $this->posts->findByUuid($uuid);

		if (!$post) {
			$this->response->redirect('/ping');
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$this->votes->vote(
			(int) $post['id'],
			(string) $user['sub'],
			-1
		);

		$this->response->redirect('/ping/' . $uuid);
	}

	public function save(string $uuid): void
	{
		$post = $this->posts->findByUuid($uuid);

		if (!$post) {
			$this->response->redirect('/ping');
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$this->savedItems->save(
			(string) $user['sub'],
			'post',
			$uuid
		);

		$this->response->redirect('/ping/' . $uuid);
	}

	public function removeSaved(string $uuid): void
	{
		$post = $this->posts->findByUuid($uuid);

		if (!$post) {
			$this->response->redirect('/ping');
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$this->savedItems->remove(
			(string) $user['sub'],
			'post',
			$uuid
		);

		$this->response->redirect('/ping/' . $uuid);
	}

	public function update(string $uuid): void
	{
		$post = $this->posts->findByUuid($uuid);

		if (!$post) {
			$this->notFound();
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$this->authorization->canEditPost($user ?? [], $post)) {
			$this->response->redirect('/ping/' . urlencode($uuid));
			return;
		}

		$content = trim((string) ($_POST['content'] ?? ''));

		if ($content === '') {
			$this->response->redirect('/ping/' . urlencode($uuid));
			return;
		}

		$this->posts->update($uuid, $content);

		$this->response->redirect('/ping/' . urlencode($uuid));
		return;
	}

	public function delete(string $uuid): void
	{
		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$post = $this->posts->findByUuid(
			$uuid,
			(string) ($user['sub'] ?? '')
		);

		if (!$post) {
			$this->response->redirect('/ping');
			return;
		}

		if (!$this->authorization->canDeletePost($user, $post)) {
			http_response_code(403);
			die('Non autorizzato');
		}

		$this->posts->delete($uuid);
		$this->response->redirect('/ping');
	}

	public function updateComment(string $uuid): void
	{
		$comment = $this->comments->findByUuid($uuid);

		if (!$comment) {
			$this->notFound();
			return;
		}

		$user = $this->session->get('auth.user');

		if (
			!$this->authorization->canEditComment(
				$user ?? [],
				$comment
			)
		) {
			$this->response->redirect(
				'/ping/' . urlencode((string) $comment['post_uuid'])
			);
			return;
		}

		$content = trim((string) ($_POST['content'] ?? ''));

		if ($content === '') {
			$this->response->redirect(
				'/ping/' . urlencode((string) $comment['post_uuid'])
			);
			return;
		}

		$this->comments->update($uuid, $content);

		$this->response->redirect(
			'/ping/' . urlencode((string) $comment['post_uuid'])
		);
	}

	public function deleteComment(string $uuid): void
	{
		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$comment = $this->comments->findByUuid($uuid);

		if (!$comment) {
			$this->response->redirect('/ping');
			return;
		}

		if (!$this->authorization->canDeleteComment($user, $comment)) {
			http_response_code(403);
			die('Non autorizzato');
		}

		$postUuid = (string) $comment['post_uuid'];

		$this->comments->delete($uuid);

		$this->response->redirect('/ping/' . urlencode($postUuid));
	}

	public function shareDogeTip(): void
	{
		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->json(
				[
					'ok' => false,
					'error' => 'Autenticazione richiesta.',
				],
				401
			);
			return;
		}

		if (
			$this->settings->get(
				'crypto_tips_enabled',
				'0'
			) !== '1'
		) {
			$this->response->json(
				[
					'ok' => false,
					'error' => 'Le mance Dogecoin non sono abilitate.',
				],
				403
			);
			return;
		}

		if (!$this->authorization->canPost($user)) {
			$this->response->json(
				[
					'ok' => false,
					'error' => 'Non puoi pubblicare Ping in questo momento.',
				],
				403
			);
			return;
		}

		$username = trim(
			(string) ($_POST['username'] ?? '')
		);

		$amount = trim(
			(string) ($_POST['amount'] ?? '')
		);

		if (
			$username === ''
			|| !preg_match(
				'/^\d+(?:\.\d{1,8})?$/',
				$amount
			)
			|| (float) $amount <= 0
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

		$displayAmount = rtrim(
			rtrim(
				$amount,
				'0'
			),
			'.'
		);

		$content = sprintf(
			'Ho inviato una mancia di %s DOGE a @%s.',
			$displayAmount,
			(string) $recipient['username']
		);

		try {

			$this->posts->create(
				[
					'author_sub' => $user['sub'],
					'content' => $content,
					'visibility' => 'public',
					'comments_enabled' => 1,
					'source' => 'doge_tip',
				],
				[]
			);

		} catch (\RuntimeException $e) {

			$this->response->json(
				[
					'ok' => false,
					'error' => $e->getMessage(),
				],
				500
			);
			return;
		}

		$this->response->json([
			'ok' => true,
		]);
	}

	public function store(): void
	{
		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		if (!$this->authorization->canPost($user)) {
			$this->session->flash(
				'error',
				'Il tuo account è stato temporaneamente silenziato e non può pubblicare contenuti.'
			);

			$this->response->redirect('/ping');
			return;
		}

		$content = trim((string) ($_POST['content'] ?? ''));

		$audioTitle = trim(
			(string) ($_POST['audio_title'] ?? '')
		);

		$audioArtist = trim(
			(string) ($_POST['audio_artist'] ?? '')
		);

		$audioTracklist = trim(
			(string) ($_POST['audio_tracklist'] ?? '')
		);

		$mediaRequireText =
			($this->settings->get(
				'media_require_text_with_audio_video',
				'1'
			) === '1');

		$hasAudioVideo = false;

		if (
			isset($_FILES['media']['type'])
			&& is_array($_FILES['media']['type'])
		) {
			foreach ($_FILES['media']['type'] as $mimeType) {

				$mimeType = strtolower(
					trim((string) $mimeType)
				);

				if (
					str_starts_with($mimeType, 'audio/')
					|| str_starts_with($mimeType, 'video/')
				) {
					$hasAudioVideo = true;
					break;
				}
			}
		}

		if (
			$content === ''
			&& (
				$mediaRequireText
				|| !$hasAudioVideo
			)
		) {
			$this->session->flash(
				'error',
				$this->translator->translate(
					'ping.flash.text_required'
				)
			);

			$this->response->redirect('/ping');
			return;
		}

		try {

			$this->posts->create(
				[
					'author_sub' => $user['sub'],
					'content' => $content,
					'visibility' => 'public',
					'comments_enabled' => 1,
					'audio_title' => $audioTitle,
					'audio_artist' => $audioArtist,
					'audio_tracklist' => $audioTracklist,
				],
				$_FILES['media'] ?? []
			);

		} catch (\RuntimeException $e) {

			$this->session->flash(
				'error',
				$e->getMessage()
			);

		}

		$this->response->redirect('/ping');
	}
}
