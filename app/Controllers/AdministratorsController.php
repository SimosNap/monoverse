<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\AdministratorService;
use Monoverse\Services\NavigationService;
use Monoverse\Services\Translator;

class AdministratorsController
{
	public function __construct(
		private View $view,
		private Response $response,
		private Request $request,
		private AdminAuthService $auth,
		private AdministratorService $administrators,
		private NavigationService $navigation,
		private Translator $translator
	) {
	}

	public function index(): void
	{
		if (!$this->authorize()) {
			return;
		}

		$html = $this->view->render(
			'administrators',
			[
				'title' => $this->translator->translate(
					'admin.administrators.title'
				),
				'admin' => $this->auth->user(),
				'navigation' => $this->navigation->items(),
				'administrators' => $this->administrators->findAll(),
			],
			'admin-layout'
		);

		$this->response
			->status(200)
			->header(
				'Content-Type',
				'text/html; charset=utf-8'
			)
			->send($html);
	}

	public function create(): void
	{
		if (!$this->authorize()) {
			return;
		}

		$html = $this->view->render(
			'administrator-form',
			[
				'title' => $this->translator->translate(
					'admin.administrators.form.create_title'
				),
				'admin' => $this->auth->user(),
				'navigation' => $this->navigation->items(),
				'administrator' => null,
				'formAction' => '/admin/administrators',
				'formMethod' => 'post',
			],
			'admin-layout'
		);

		$this->response
			->status(200)
			->header(
				'Content-Type',
				'text/html; charset=utf-8'
			)
			->send($html);
	}

	public function store(): void
	{
		if (!$this->authorize()) {
			return;
		}

		$username = trim(
			(string) $this->request->post(
				'username',
				''
			)
		);

		$password = (string) $this->request->post(
			'password',
			''
		);

		$role = trim(
			(string) $this->request->post(
				'role',
				''
			)
		);

		try {
			$this->administrators->create(
				$username,
				$password,
				$role
			);
		} catch (\Throwable $exception) {
			$this->renderFormError(
				'/admin/administrators',
				$this->translator->translate(
					'admin.administrators.form.create_title'
				),
				$this->administratorError(
					$exception->getMessage()
				),
				[
					'username' => $username,
					'role' => $role,
					'enabled' => 1,
				]
			);

			return;
		}

		$this->response->redirect(
			'/admin/administrators'
		);
	}

	public function edit(int $id): void
	{
		if (!$this->authorize()) {
			return;
		}

		$administrator = $this->administrators->findById(
			$id
		);

		if ($administrator === null) {
			$this->response
				->status(404)
				->send(
					$this->translator->translate(
						'admin.administrators.errors.not_found'
					)
				);

			return;
		}

		if (
			($administrator['role'] ?? '')
			=== 'administrator'
		) {
			$this->response->redirect(
				'/admin/administrators'
			);

			return;
		}

		$html = $this->view->render(
			'administrator-form',
			[
				'title' => $this->translator->translate(
					'admin.administrators.form.edit_title'
				),
				'admin' => $this->auth->user(),
				'navigation' => $this->navigation->items(),
				'administrator' => $administrator,
				'formAction' => '/admin/administrators/'
					. $id,
				'formMethod' => 'post',
			],
			'admin-layout'
		);

		$this->response
			->status(200)
			->header(
				'Content-Type',
				'text/html; charset=utf-8'
			)
			->send($html);
	}

	public function update(int $id): void
	{
		if (!$this->authorize()) {
			return;
		}

		$administrator = $this->administrators->findById(
			$id
		);

		if ($administrator === null) {
			$this->response
				->status(404)
				->send(
					$this->translator->translate(
						'admin.administrators.errors.not_found'
					)
				);

			return;
		}

		if (
			($administrator['role'] ?? '')
			=== 'administrator'
		) {
			$this->response->redirect(
				'/admin/administrators'
			);

			return;
		}

		$username = trim(
			(string) $this->request->post(
				'username',
				''
			)
		);

		$role = trim(
			(string) $this->request->post(
				'role',
				''
			)
		);

		$password = trim(
			(string) $this->request->post(
				'password',
				''
			)
		);

		$enabled = (
			(string) $this->request->post(
				'enabled',
				''
			)
		) === '1';

		try {
			$this->administrators->update(
				$id,
				$username,
				$role,
				$enabled,
				$password !== ''
					? $password
					: null
			);
		} catch (\Throwable $exception) {
			$this->renderFormError(
				'/admin/administrators/' . $id,
				$this->translator->translate(
					'admin.administrators.form.edit_title'
				),
				$this->administratorError(
					$exception->getMessage()
				),
				[
					'id' => $id,
					'username' => $username,
					'role' => $role,
					'enabled' => $enabled ? 1 : 0,
				]
			);

			return;
		}

		$this->response->redirect(
			'/admin/administrators'
		);
	}

	public function delete(int $id): void
	{
		if (!$this->authorize()) {
			return;
		}

		try {
			$this->administrators->remove($id);
		} catch (\Throwable) {
			$this->response->redirect(
				'/admin/administrators'
			);

			return;
		}

		$this->response->redirect(
			'/admin/administrators'
		);
	}

	private function authorize(): bool
	{
		if (!$this->auth->check()) {
			$this->response->redirect(
				'/admin/login'
			);

			return false;
		}

		if (!$this->auth->can('administrators')) {
			$this->response->redirect(
				'/admin'
			);

			return false;
		}

		return true;
	}

	private function renderFormError(
		string $formAction,
		string $title,
		string $error,
		array $administrator
	): void {
		$html = $this->view->render(
			'administrator-form',
			[
				'title' => $title,
				'admin' => $this->auth->user(),
				'navigation' => $this->navigation->items(),
				'administrator' => $administrator,
				'formAction' => $formAction,
				'formMethod' => 'post',
				'error' => $error,
			],
			'admin-layout'
		);

		$this->response
			->status(422)
			->header(
				'Content-Type',
				'text/html; charset=utf-8'
			)
			->send($html);
	}

	private function administratorError(
		string $error
	): string {
		$allowedErrors = [
			'username_required',
			'password_required',
			'invalid_role',
			'username_exists',
			'not_found',
			'original_cannot_modify',
			'original_cannot_delete',
		];

		if (!in_array(
			$error,
			$allowedErrors,
			true
		)) {
			return $this->translator->translate(
				'admin.administrators.errors.generic'
			);
		}

		return $this->translator->translate(
			'admin.administrators.errors.' . $error
		);
	}
}
