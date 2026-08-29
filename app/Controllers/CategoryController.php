<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\CategoryService;
use Monoverse\Services\NavigationService;

final class CategoryController
{
	private const TYPE = 'chanzine';

	public function __construct(
		private View $view,
		private Response $response,
		private Request $request,
		private Session $session,
		private AdminAuthService $auth,
		private CategoryService $categories,
		private NavigationService $navigation
	) {
	}

	public function index(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$html = $this->view->render('categories', [
			'title' => 'Categorie Chanzine',
			'admin' => $this->auth->user(),
			'categories' => $this->categories->listAll(
				self::TYPE
			),
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

		$html = $this->view->render('category-form', [
			'title' => 'Nuova categoria',
			'admin' => $this->auth->user(),
			'category' => null,
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

		$name = trim(
			(string) $this->request->post('name', '')
		);
		
		$description = trim(
			(string) $this->request->post('description', '')
		);

		$slug = trim(
			(string) $this->request->post('slug', '')
		);

		$sortOrder = max(
			0,
			(int) $this->request->post('sort_order', 0)
		);

		if ($name === '') {
			$this->session->flash(
				'error',
				'Il nome della categoria è obbligatorio.'
			);

			$this->response->redirect(
				'/admin/categories/create'
			);
			return;
		}

		$slug = $this->slugify(
			$slug !== '' ? $slug : $name
		);

		if ($slug === '') {
			$this->session->flash(
				'error',
				'Lo slug non è valido.'
			);

			$this->response->redirect(
				'/admin/categories/create'
			);
			return;
		}

		if ($this->categories->slugExists(
			self::TYPE,
			$slug
		)) {
			$this->session->flash(
				'error',
				'Esiste già una categoria con questo slug.'
			);

			$this->response->redirect(
				'/admin/categories/create'
			);
			return;
		}

		$this->categories->create(
			self::TYPE,
			[
				'name' => $name,
				'description' => $description,
				'slug' => $slug,
				'sort_order' => $sortOrder,
			]
		);

		$this->session->flash(
			'success',
			'Categoria creata.'
		);

		$this->response->redirect('/admin/categories');
	}

	public function edit(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$category = $this->categories->findByUuid($uuid);

		if (
			$category === null
			|| ($category['type'] ?? '') !== self::TYPE
		) {
			$this->session->flash(
				'error',
				'Categoria non trovata.'
			);

			$this->response->redirect('/admin/categories');
			return;
		}

		$html = $this->view->render('category-form', [
			'title' => 'Modifica categoria',
			'admin' => $this->auth->user(),
			'category' => $category,
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

		$category = $this->categories->findByUuid($uuid);

		if (
			$category === null
			|| ($category['type'] ?? '') !== self::TYPE
		) {
			$this->session->flash(
				'error',
				'Categoria non trovata.'
			);

			$this->response->redirect('/admin/categories');
			return;
		}

		$name = trim(
			(string) $this->request->post('name', '')
		);
		
		$description = trim(
			(string) $this->request->post('description', '')
		);

		$slug = trim(
			(string) $this->request->post('slug', '')
		);

		$sortOrder = max(
			0,
			(int) $this->request->post('sort_order', 0)
		);

		if ($name === '') {
			$this->session->flash(
				'error',
				'Il nome della categoria è obbligatorio.'
			);

			$this->response->redirect(
				'/admin/categories/' . $uuid . '/edit'
			);
			return;
		}

		$slug = $this->slugify(
			$slug !== '' ? $slug : $name
		);

		if ($slug === '') {
			$this->session->flash(
				'error',
				'Lo slug non è valido.'
			);

			$this->response->redirect(
				'/admin/categories/' . $uuid . '/edit'
			);
			return;
		}

		if ($this->categories->slugExists(
			self::TYPE,
			$slug,
			$uuid
		)) {
			$this->session->flash(
				'error',
				'Esiste già una categoria con questo slug.'
			);

			$this->response->redirect(
				'/admin/categories/' . $uuid . '/edit'
			);
			return;
		}

		$this->categories->update(
			$uuid,
			[
				'name' => $name,
				'description' => $description,
				'slug' => $slug,
				'sort_order' => $sortOrder,
			]
		);

		$this->session->flash(
			'success',
			'Categoria aggiornata.'
		);

		$this->response->redirect('/admin/categories');
	}

	public function delete(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$category = $this->categories->findByUuid($uuid);

		if (
			$category === null
			|| ($category['type'] ?? '') !== self::TYPE
		) {
			$this->session->flash(
				'error',
				'Categoria non trovata.'
			);

			$this->response->redirect('/admin/categories');
			return;
		}

		$this->categories->delete($uuid);

		$this->session->flash(
			'success',
			'Categoria eliminata.'
		);

		$this->response->redirect('/admin/categories');
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
		$value = preg_replace(
			'/[^a-z0-9]+/',
			'-',
			$value
		) ?? '';

		return substr(
			trim($value, '-'),
			0,
			120
		);
	}
}
