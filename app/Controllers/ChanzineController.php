<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Request;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Core\Blocks\BlockManager;
use Monoverse\Services\ArticleService;
use Monoverse\Services\CategoryService;
use Monoverse\Services\MarkdownService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\SavedItemService;
use Monoverse\Services\SettingsService;

class ChanzineController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		private Request $request,
		NotificationService $notifications,
		private ArticleService $articles,
		private CategoryService $categories,
		private MarkdownService $markdown,
		private SavedItemService $savedItems,
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
		$query = trim(
			(string) ($_GET['q'] ?? '')
		);

		$articles = $query !== ''
			? $this->articles->searchPublished(
				$query,
				null,
				20,
				0
			)
			: $this->articles->listPublished(20);

		$widgetAreas = [
			'beforeContent' => $this->blocks->renderArea(
				'chanzine',
				'before-content'
			),
			'sidebar' => $this->blocks->renderArea(
				'chanzine',
				'sidebar'
			),
			'afterContent' => $this->blocks->renderArea(
				'chanzine',
				'after-content'
			),
		];

		$this->render('chanzine', [
			'title' => 'Chanzine',
			'articles' => $articles,
			'query' => $query,
			'widgetAreas' => $widgetAreas,
			'blockCssFiles' => $this->blocks->stylesheets(),
			'blockJsFiles' => $this->blocks->scripts(),
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

		$articles = $this->articles->listPublished(20);

		$escape = static fn (string $value): string =>
			htmlspecialchars(
				$value,
				ENT_XML1 | ENT_QUOTES,
				'UTF-8'
			);

		$channelUrl = $siteUrl . '/chanzine';
		$feedUrl = $siteUrl . '/chanzine/rss';

		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<rss version="2.0">' . "\n";
		$xml .= '<channel>' . "\n";
		$xml .= '<title>'
			. $escape('Chanzine - ' . $siteName)
			. '</title>' . "\n";
		$xml .= '<link>'
			. $escape($channelUrl)
			. '</link>' . "\n";
		$xml .= '<description>'
			. $escape('Ultimi articoli pubblicati su ' . $siteName)
			. '</description>' . "\n";
		$xml .= '<language>it</language>' . "\n";
		$xml .= '<atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="'
			. $escape($feedUrl)
			. '" rel="self" type="application/rss+xml" />' . "\n";

		foreach ($articles as $article) {
			$title = trim(
				(string) ($article['title'] ?? '')
			);

			$slug = trim(
				(string) ($article['slug'] ?? '')
			);

			$uuid = trim(
				(string) ($article['uuid'] ?? '')
			);

			$excerpt = trim(
				(string) ($article['excerpt'] ?? '')
			);

			$publishedAt = trim(
				(string) ($article['published_at'] ?? '')
			);

			if ($title === '' || $slug === '' || $uuid === '') {
				continue;
			}

			$articleUrl = $siteUrl
				. '/chanzine/'
				. rawurlencode($slug);

			$xml .= '<item>' . "\n";
			$xml .= '<title>'
				. $escape($title)
				. '</title>' . "\n";
			$xml .= '<link>'
				. $escape($articleUrl)
				. '</link>' . "\n";
			$xml .= '<guid isPermaLink="false">'
				. $escape('urn:uuid:' . $uuid)
				. '</guid>' . "\n";

			if ($excerpt !== '') {
				$xml .= '<description>'
					. $escape($excerpt)
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

			if (!empty($article['category_name'])) {
				$xml .= '<category>'
					. $escape(
						(string) $article['category_name']
					)
					. '</category>' . "\n";
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

	public function storeSubmission(): void
	{
		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		if (
			$this->settings->get(
				'chanzine_user_submissions_enabled',
				'0'
			) !== '1'
		) {
			$this->response->redirect('/chanzine');
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

			$this->response->redirect('/chanzine/submit');
			return;
		}

		if ($title === '' || $content === '') {
			$this->session->flash(
				'error',
				'Titolo e contenuto sono obbligatori.'
			);

			$this->response->redirect('/chanzine/submit');
			return;
		}

		$slug = $this->generateUniqueSlug($title);

		$cover = null;

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

				$this->response->redirect('/chanzine/submit');
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

				$this->response->redirect('/chanzine/submit');
				return;
			}

			$cover =
				'/storage/chanzine/'
				. date('Y')
				. '/'
				. date('m')
				. '/'
				. $filename;
		}

		$created = $this->articles->createSubmission([
			'title' => $title,
			'slug' => $slug,
			'excerpt' => $excerpt !== ''
				? $excerpt
				: null,
			'content' => $content,
			'cover' => $cover,
			'category_id' => $categoryId,
			'submitted_by_sub' => (string) (
				$user['sub'] ?? ''
			),
			'submitted_by_nickname' => (string) (
				$user['nickname']
				?? $user['preferred_username']
				?? ''
			),
		]);

		if (!$created) {
			if ($cover !== null) {
				$coverFile = __DIR__
					. '/../../'
					. ltrim($cover, '/');

				if (is_file($coverFile)) {
					@unlink($coverFile);
				}
			}

			$this->session->flash(
				'error',
				'Non è stato possibile inviare la proposta.'
			);

			$this->response->redirect('/chanzine/submit');
			return;
		}

		$this->session->flash(
			'success',
			'La tua proposta è stata inviata e sarà revisionata dall’amministratore.'
		);

		$this->response->redirect('/chanzine');
	}

	public function submit(): void
	{
		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		if (
			$this->settings->get(
				'chanzine_user_submissions_enabled',
				'0'
			) !== '1'
		) {
			$this->response->redirect('/chanzine');
			return;
		}

		$this->render(
			'chanzine-submit',
			[
				'title' => 'Proponi un articolo',
				'categories' => $this->categories->listAll('chanzine'),
				'error' => $this->session->getFlash('error'),
			]
		);
	}

	public function show(string $slug): void
	{
		$slug = trim(rawurldecode($slug));

		if ($slug === '') {
			$this->notFound();
			return;
		}

		$article = $this->articles->findPublishedBySlug($slug);

		if (!$article) {
			$this->notFound();
			return;
		}

		$user = $this->session->get('auth.user');

		$article['is_saved'] = $user
			? $this->savedItems->isSaved(
				(string) $user['sub'],
				'article',
				(string) $article['uuid']
			)
			: false;

		$article['content_html'] = $this->markdown->render(
			(string) ($article['content'] ?? '')
		);

		$articlePath = '/chanzine/' . rawurlencode((string) $article['slug']);

		$description = trim((string) ($article['excerpt'] ?? ''));

		$widgetAreas = [
			'beforeContent' => $this->blocks->renderArea(
				'chanzine-article',
				'before-content'
			),
			'sidebar' => $this->blocks->renderArea(
				'chanzine-article',
				'sidebar'
			),
			'afterContent' => $this->blocks->renderArea(
				'chanzine-article',
				'after-content'
			),
		];

		$this->render('chanzine-article', [
			'title' => (string) $article['title'],
			'metaDescription' => $description,
			'canonicalPath' => $articlePath,
			'openGraph' => [
				'type' => 'article',
				'title' => (string) $article['title'],
				'description' => $description,
				'path' => $articlePath,
				'image' => !empty($article['cover'])
					? (string) $article['cover']
					: '/themes/default/assets/images/chanzine-default.webp',
				'publishedTime' => !empty($article['published_at'])
					? (string) $article['published_at']
					: null,
			],
			'article' => $article,
			'widgetAreas' => $widgetAreas,
			'blockCssFiles' => $this->blocks->stylesheets(),
			'blockJsFiles' => $this->blocks->scripts(),
		]);
	}

	public function save(string $uuid): void
	{
		$article = $this->articles->findByUuid($uuid);

		if (!$article) {
			$this->response->redirect('/chanzine');
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$this->savedItems->save(
			(string) $user['sub'],
			'article',
			$uuid
		);

		$this->response->redirect(
			'/chanzine/' . rawurlencode((string) $article['slug'])
		);
	}

	public function removeSaved(string $uuid): void
	{
		$article = $this->articles->findByUuid($uuid);

		if (!$article) {
			$this->response->redirect('/chanzine');
			return;
		}

		$user = $this->session->get('auth.user');

		if (!$user) {
			$this->response->redirect('/oauth/login');
			return;
		}

		$this->savedItems->remove(
			(string) $user['sub'],
			'article',
			$uuid
		);

		$this->response->redirect(
			'/chanzine/' . rawurlencode((string) $article['slug'])
		);
	}

	public function category(string $slug): void
	{
		$slug = trim(rawurldecode($slug));

		if ($slug === '') {
			$this->notFound();
			return;
		}

		$category = $this->categories->findBySlug(
			'chanzine',
			$slug
		);

		if (!$category) {
			$this->notFound();
			return;
		}

		$query = trim(
			(string) ($_GET['q'] ?? '')
		);

		$articles = $query !== ''
			? $this->articles->searchPublished(
				$query,
				(int) $category['id'],
				20,
				0
			)
			: $this->articles->listPublishedByCategory(
				(int) $category['id'],
				20
			);

		$blockContext = [
			'currentCategorySlug' => (string) $category['slug'],
		];

		$widgetAreas = [
			'beforeContent' => $this->blocks->renderArea(
				'chanzine',
				'before-content',
				$blockContext
			),
			'sidebar' => $this->blocks->renderArea(
				'chanzine',
				'sidebar',
				$blockContext
			),
			'afterContent' => $this->blocks->renderArea(
				'chanzine',
				'after-content',
				$blockContext
			),
		];

		$this->render('chanzine', [
			'title' => (string) $category['name'] . ' - Chanzine',
			'articles' => $articles,
			'query' => $query,
			'currentCategory' => $category,
			'widgetAreas' => $widgetAreas,
			'blockCssFiles' => $this->blocks->stylesheets(),
			'blockJsFiles' => $this->blocks->scripts(),
		]);
	}

	protected function notFound(): void
	{
		$html = $this->view->render('404', [
			'title' => 'Pagina non trovata',
		]);

		$this->response
			->status(404)
			->header('Content-Type', 'text/html; charset=utf-8')
			->send($html);
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

	private function generateUniqueSlug(string $title): string
	{
		$baseSlug = $this->slugify($title);

		if ($baseSlug === '') {
			$baseSlug = 'articolo';
		}

		$slug = $baseSlug;
		$counter = 2;

		while ($this->articles->slugExists($slug)) {
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
