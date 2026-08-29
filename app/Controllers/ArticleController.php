<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\ArticleService;
use Monoverse\Services\CategoryService;
use Monoverse\Services\NavigationService;
use Monoverse\Services\PostService;


class ArticleController
{
	public function __construct(
		private View $view,
		private Response $response,
		private Request $request,
		private Session $session,
		private AdminAuthService $auth,
		private ArticleService $articles,
		private CategoryService $categories,
		private NavigationService $navigation,
		private PostService $posts
	) {
	}

	public function index(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$html = $this->view->render('articles', [
			'title' => 'Articoli',
			'admin' => $this->auth->user(),
			'articles' => $this->articles->listAll(),
			'submissions' => $this->articles->listSubmitted(),
			'success' => $this->session->getFlash('success'),
			'error' => $this->session->getFlash('error'),
			'navigation' => $this->navigation->items(),
		], 'admin-layout');

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
	
		$html = $this->view->render('article-form', [
			'title' => 'Nuovo articolo',
			'admin' => $this->auth->user(),
			'article' => null,
			'categories' => $this->categories->listAll('chanzine'),
			'error' => $this->session->getFlash('error'),
			'navigation' => $this->navigation->items(),
		], 'admin-layout');
	
		$this->response
			->status(200)
			->header('Content-Type', 'text/html; charset=utf-8')
			->send($html);
	}
	
	public function store(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}
	
		$title = trim((string) $this->request->post('title', ''));
		$slug = trim((string) $this->request->post('slug', ''));
		$excerpt = trim((string) $this->request->post('excerpt', ''));
		$content = trim((string) $this->request->post('content', ''));
		
		$categoryId = (int) $this->request->post(
			'category_id',
			0
		);
		
		if (!$this->isValidCategoryId($categoryId)) {
			$this->session->flash(
				'error',
				'Seleziona una categoria valida.'
			);
		
			$this->response->redirect('/admin/articles/create');
			return;
		}
	
		if ($title === '' || $content === '') {
			$this->session->flash(
				'error',
				'Titolo e contenuto sono obbligatori.'
			);
	
			$this->response->redirect('/admin/articles/create');
			return;
		}
	
		if ($slug === '') {
			$slug = $this->slugify($title);
		} else {
			$slug = $this->slugify($slug);
		}
	
		if ($slug === '') {
			$this->session->flash(
				'error',
				'Lo slug non è valido.'
			);
	
			$this->response->redirect('/admin/articles/create');
			return;
		}
	
		if ($this->articles->slugExists($slug)) {
			$this->session->flash(
				'error',
				'Esiste già un articolo con questo slug.'
			);
	
			$this->response->redirect('/admin/articles/create');
			return;
		}
		
		$cover = null;
		
		if (
			isset($_FILES['cover']) &&
			$_FILES['cover']['error'] === UPLOAD_ERR_OK
		) {
			$mime = mime_content_type($_FILES['cover']['tmp_name']);
		
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
		
				$this->response->redirect('/admin/articles/create');
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
		
			$destination = $directory . '/' . $filename;
		
			if (!move_uploaded_file(
				$_FILES['cover']['tmp_name'],
				$destination
			)) {
				$this->session->flash(
					'error',
					'Impossibile salvare la cover.'
				);
			
				$this->response->redirect('/admin/articles/create');
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
	
		$created = $this->articles->create([
			'title' => $title,
			'slug' => $slug,
			'excerpt' => $excerpt !== '' ? $excerpt : null,
			'content' => $content,
			'cover' => $cover,
			'category_id' => $categoryId,
		]);
	
		if (!$created) {
			$this->session->flash(
				'error',
				'Non è stato possibile salvare l’articolo.'
			);
	
			$this->response->redirect('/admin/articles/create');
			return;
		}
	
		$this->session->flash(
			'success',
			'Bozza salvata.'
		);
	
		$this->response->redirect('/admin/articles');
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
	
	public function edit(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}
	
		$article = $this->articles->findByUuid($uuid);
	
		if (!$article) {
			$this->session->flash(
				'error',
				'Articolo non trovato.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$html = $this->view->render('article-form', [
			'title' => ($article['status'] === 'submitted')
				? 'Revisiona proposta'
				: 'Modifica articolo',
			'admin' => $this->auth->user(),
			'article' => $article,
			'categories' => $this->categories->listAll('chanzine'),
			'error' => $this->session->getFlash('error'),
			'navigation' => $this->navigation->items(),
		], 'admin-layout');
	
		$this->response
			->status(200)
			->header('Content-Type', 'text/html; charset=utf-8')
			->send($html);
	}
	
	public function update(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}
	
		$article = $this->articles->findByUuid($uuid);
	
		if (!$article) {
			$this->session->flash(
				'error',
				'Articolo non trovato.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$title = trim((string) $this->request->post('title', ''));
		$slug = trim((string) $this->request->post('slug', ''));
		$excerpt = trim((string) $this->request->post('excerpt', ''));
		$content = trim((string) $this->request->post('content', ''));
		$categoryId = (int) $this->request->post(
			'category_id',
			0
		);
		$cover = $article['cover'] ?? null;
		
		if (
			isset($_FILES['cover']) &&
			$_FILES['cover']['error'] === UPLOAD_ERR_OK
		) {
			$mime = mime_content_type($_FILES['cover']['tmp_name']);
		
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
		
				$this->response->redirect('/admin/articles/' . $uuid . '/edit');
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
		
			$destination = $directory . '/' . $filename;
		
			if (!move_uploaded_file(
				$_FILES['cover']['tmp_name'],
				$destination
			)) {
				$this->session->flash(
					'error',
					'Impossibile salvare la cover.'
				);
			
				$this->response->redirect(
					'/admin/articles/' . $uuid . '/edit'
				);
				return;
			}
			
			if (!empty($article['cover'])) {
				$oldFile = __DIR__
					. '/../../'
					. ltrim((string) $article['cover'], '/');
			
				if (is_file($oldFile)) {
					@unlink($oldFile);
				}
			}
			
			$cover =
				'/storage/chanzine/'
				. date('Y')
				. '/'
				. date('m')
				. '/'
				. $filename;
		}
		
		if (!$this->isValidCategoryId($categoryId)) {
			$this->session->flash(
				'error',
				'Seleziona una categoria valida.'
			);
		
			$this->response->redirect(
				'/admin/articles/' . $uuid . '/edit'
			);
			return;
		}
	
		if ($title === '' || $content === '') {
			$this->session->flash(
				'error',
				'Titolo e contenuto sono obbligatori.'
			);
	
			$this->response->redirect('/admin/articles/' . $uuid . '/edit');
			return;
		}
	
		$slug = $this->slugify(
			$slug !== '' ? $slug : $title
		);
	
		if ($slug === '') {
			$this->session->flash(
				'error',
				'Lo slug non è valido.'
			);
	
			$this->response->redirect('/admin/articles/' . $uuid . '/edit');
			return;
		}
	
		if ($this->articles->slugExists($slug, $uuid)) {
			$this->session->flash(
				'error',
				'Esiste già un articolo con questo slug.'
			);
	
			$this->response->redirect(
				'/admin/articles/' . $uuid . '/edit'
			);
			return;
		}
	
		$updated = $this->articles->update($uuid, [
			'title' => $title,
			'slug' => $slug,
			'excerpt' => $excerpt !== '' ? $excerpt : null,
			'content' => $content,
			'cover' => $cover,
			'category_id' => $categoryId,
		]);
	
		if (!$updated) {
			$this->session->flash(
				'error',
				'Non è stato possibile aggiornare l’articolo.'
			);
	
			$this->response->redirect(
				'/admin/articles/' . $uuid . '/edit'
			);
			return;
		}
		
		$publishAfterUpdate = (
			(string) $this->request->post(
				'publish_after_update',
				''
			) === '1'
		);
		
		if (
			$publishAfterUpdate
			&& (($article['status'] ?? '') === 'submitted')
		) {
			$this->publish($uuid);
			return;
		}
	
		$this->session->flash(
			'success',
			'Articolo aggiornato.'
		);
	
		$this->response->redirect('/admin/articles');
	}
	
	public function publish(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}
	
		$article = $this->articles->findByUuid($uuid);
	
		if (!$article) {
			$this->session->flash(
				'error',
				'Articolo non trovato.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		if ($article['status'] === 'published') {
			$this->session->flash(
				'error',
				'L’articolo è già pubblicato.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$published = $this->articles->publish($uuid);
	
		if (!$published) {
			$this->session->flash(
				'error',
				'Non è stato possibile pubblicare l’articolo.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$pingUuid = $article['ping_uuid'] ?? null;
	
		if (!$pingUuid) {
			$pingUuid = $this->posts->createChanzinePing(
				(string) $article['title'],
				(string) ($article['excerpt'] ?? ''),
				(string) $article['slug']
			);
	
			if (!$pingUuid) {
				$this->session->flash(
					'error',
					'L’articolo è stato pubblicato, ma non è stato possibile creare il Ping.'
				);
	
				$this->response->redirect('/admin/articles');
				return;
			}
	
			if (!$this->articles->setPingUuid($uuid, $pingUuid)) {
				$this->session->flash(
					'error',
					'L’articolo e il Ping sono stati pubblicati, ma non è stato possibile collegarli.'
				);
	
				$this->response->redirect('/admin/articles');
				return;
			}
		}
	
		$this->session->flash(
			'success',
			'Articolo pubblicato e Ping creato.'
		);
	
		$this->response->redirect('/admin/articles');
	}
	
	public function moveToDraft(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}
	
		$article = $this->articles->findByUuid($uuid);
	
		if (!$article) {
			$this->session->flash(
				'error',
				'Articolo non trovato.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		if (!$this->articles->moveToDraft($uuid)) {
			$this->session->flash(
				'error',
				'Non è stato possibile riportare l’articolo in bozza.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$this->session->flash(
			'success',
			'Articolo riportato in bozza.'
		);
	
		$this->response->redirect('/admin/articles');
	}
	
	public function reject(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}
	
		$article = $this->articles->findByUuid($uuid);
	
		if (!$article) {
			$this->response->redirect('/admin/articles');
			return;
		}
	
		if (($article['status'] ?? '') !== 'submitted') {
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$reason = trim(
			(string) $this->request->post(
				'rejection_reason',
				''
			)
		);
	
		if ($reason === '') {
			$this->session->flash(
				'error',
				'Indica il motivo del rifiuto.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		if (!$this->articles->reject($uuid, $reason)) {
			$this->session->flash(
				'error',
				'Non è stato possibile rifiutare la proposta.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$this->session->flash(
			'success',
			'Proposta rifiutata.'
		);
	
		$this->response->redirect('/admin/articles');
	}
	
	public function delete(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}
	
		$article = $this->articles->findByUuid($uuid);
	
		if (!$article) {
			$this->session->flash(
				'error',
				'Articolo non trovato.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
		
		$pingUuid = trim(
			(string) ($article['ping_uuid'] ?? '')
		);
		
		if ($pingUuid !== '') {
			$this->posts->delete($pingUuid);
		}
	
		if (!$this->articles->delete($uuid)) {
			$this->session->flash(
				'error',
				'Non è stato possibile eliminare l’articolo.'
			);
	
			$this->response->redirect('/admin/articles');
			return;
		}
	
		$this->session->flash(
			'success',
			'Articolo eliminato.'
		);
	
		$this->response->redirect('/admin/articles');
	}
	
	private function isValidCategoryId(int $categoryId): bool
	{
		if ($categoryId <= 0) {
			return false;
		}
	
		foreach (
			$this->categories->listAll('chanzine')
			as $category
		) {
			if ((int) ($category['id'] ?? 0) === $categoryId) {
				return true;
			}
		}
	
		return false;
	}
}
