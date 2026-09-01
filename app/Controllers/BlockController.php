<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Blocks\AreaRegistry;
use Monoverse\Core\Blocks\BlockRegistry;
use Monoverse\Core\Blocks\FormRenderer;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\ContentTranslationService;
use Monoverse\Services\LocaleService;
use Monoverse\Services\Translator;
use Monoverse\Repositories\BlockRepository;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\NavigationService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\PageService;

final class BlockController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications,
		private AdminAuthService $auth,
		private NavigationService $navigation,
		private PageService $pages,
		private BlockRegistry $blocks,
		private AreaRegistry $areas,
		private BlockRepository $repository,
		private FormRenderer $formRenderer,
		private Translator $translator,
		private LocaleService $localeService,
		private ContentTranslationService $contentTranslations
	) {
		parent::__construct(
			$view,
			$response,
			$session,
			$notifications
		);
	}

	public function index(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$definitions = [];

		foreach ($this->blocks->available() as $definition) {
			$type = trim(
				(string) ($definition['type'] ?? '')
			);

			if ($type !== '') {
				$definitions[$type] = $definition;
			}
		}

		$pages = [];

		foreach ($this->areas->all() as $page => $pageAreas) {
			$areas = [];

			foreach ($pageAreas as $area => $label) {
				$configuredBlocks = [];

				foreach (
					$this->repository->findByArea(
						(string) $page,
						(string) $area
					) as $record
				) {
					$configuredBlocks[] = $this->prepareBlockRecord(
						$record,
						$definitions
					);
				}

				$areas[] = [
					'key' => (string) $area,
					'label' => $this->areaLabel(
						(string) $page,
						(string) $area,
						(string) $label
					),
					'blocks' => $configuredBlocks,
				];
			}

			$pages[] = [
				'key' => (string) $page,
				'label' => $this->pageLabel(
					(string) $page
				),
				'areas' => $areas,
			];
		}

		foreach ($this->pages->listAll() as $dynamicPage) {
			$slug = trim(
				(string) ($dynamicPage['slug'] ?? '')
			);

			if ($slug === '') {
				continue;
			}

			$pageKey = 'page:' . $slug;

			$areas = [];

			foreach (
				['content', 'sidebar'] as $area
			) {
				$configuredBlocks = [];

				foreach (
					$this->repository->findByArea(
						$pageKey,
						$area
					) as $record
				) {
					$configuredBlocks[] = $this->prepareBlockRecord(
						$record,
						$definitions
					);
				}

				$areas[] = [
					'key' => $area,
					'label' => $this->translator->translate(
						'admin.blocks.dynamic_areas.' . $area
					),
					'blocks' => $configuredBlocks,
				];
			}

			$pages[] = [
				'key' => $pageKey,
				'label' => trim(
					(string) ($dynamicPage['title'] ?? '')
				) !== ''
					? (string) $dynamicPage['title']
					: ucfirst($slug),
				'areas' => $areas,
			];
		}

		$this->render(
			'blocks',
			[
				'navigation' => $this->navigation->items(),
				'pages' => $pages,
			],
			'admin-layout'
		);
	}

	public function area(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$page = trim(
			(string) ($_GET['page'] ?? '')
		);

		$area = trim(
			(string) ($_GET['area'] ?? '')
		);

		if (!$this->areaExists($page, $area)) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$definitions = [];

		foreach ($this->blocks->available() as $definition) {
			$type = trim(
				(string) ($definition['type'] ?? '')
			);

			if ($type !== '') {
				$definitions[$type] = $definition;
			}
		}

		$configuredBlocks = [];

		foreach (
			$this->repository->findByArea($page, $area) as $record
		) {
			$configuredBlocks[] = $this->prepareBlockRecord(
				$record,
				$definitions
			);
		}

		$pageAreas = $this->areas->page($page);

		$this->render(
			'widgets-area',
			[
				'navigation' => $this->navigation->items(),
				'page' => $page,
				'pageLabel' => $this->pageLabel($page),
				'area' => $area,
				'areaLabel' => $this->areaLabel(
					$page,
					$area,
					(string) ($pageAreas[$area] ?? $area)
				),
				'configuredBlocks' => $configuredBlocks,
			],
			'admin-layout'
		);
	}

	public function library(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$page = trim(
			(string) ($_GET['page'] ?? '')
		);

		$area = trim(
			(string) ($_GET['area'] ?? '')
		);

		if (!$this->areaExists($page, $area)) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$categories = [];

		foreach (
			$this->blocks->categories() as $category => $registeredBlocks
		) {
			$items = [];

			foreach ($registeredBlocks as $block) {
				$items[] = [
					'type' => $block->type(),
					'label' => $block->label(),
					'icon' => $block->icon(),
					'description' => $block->description(),
				];
			}

			$categories[] = [
				'name' => ucfirst((string) $category),
				'blocks' => $items,
			];
		}

		$pageAreas = $this->areas->page($page);

		$this->render(
			'block-library',
			[
				'navigation' => $this->navigation->items(),
				'page' => $page,
				'pageLabel' => $this->pageLabel($page),
				'area' => $area,
				'areaLabel' => $this->areaLabel(
					$page,
					$area,
					(string) ($pageAreas[$area] ?? $area)
				),
				'categories' => $categories,
			],
			'admin-layout'
		);
	}

	public function create(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$page = trim(
			(string) ($_GET['page'] ?? '')
		);

		$area = trim(
			(string) ($_GET['area'] ?? '')
		);

		$type = trim(
			(string) ($_GET['type'] ?? '')
		);

		if (!$this->areaExists($page, $area)) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$block = $this->blocks->get($type);

		if ($block === null) {
			$this->response->redirect(
				$this->areaUrl($page, $area)
			);
			return;
		}

		$defaultSettings = $block->defaultSettings();

		$this->render(
			'block-edit',
			[
				'navigation' => $this->navigation->items(),

				'block' => [
					'id' => 0,
					'page' => $page,
					'area' => $area,
					'type' => $type,
					'name' => null,
					'title' => null,
					'width' => 12,
					'enabled' => 1,
				],

				'blockDefinition' => [
					'label' => $block->label(),
					'description' => $block->description(),
					'icon' => $block->icon(),
				],

				'editor' => $this->formRenderer->render(
					$block,
					$defaultSettings,
					true
				),

				'isNew' => true,
				'availableLocales' => $this->localeService->getAvailableLocales(),
				'defaultLocale' => $this->localeService->getDefaultLocale(),
				'titleTranslations' => [],
			],
			'admin-layout'
		);
	}

	public function store(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$page = trim(
			(string) ($_POST['page'] ?? '')
		);

		$area = trim(
			(string) ($_POST['area'] ?? '')
		);

		$type = trim(
			(string) ($_POST['type'] ?? '')
		);

		if (!$this->areaExists($page, $area)) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$block = $this->blocks->get($type);

		if ($block === null) {
			$this->response->redirect(
				$this->areaUrl($page, $area)
			);
			return;
		}

		$settings = [];

		foreach ($block->settingsForm() as $field) {
			$fieldName = trim(
				(string) ($field['name'] ?? '')
			);

			if ($fieldName === '') {
				continue;
			}

			$settings[$fieldName] = (string) (
				$_POST[$fieldName] ?? ''
			);
		}

		if (
			$block instanceof \Monoverse\Core\Blocks\ValidatesSettingsInterface
		) {
			$errors = $block->validateSettings($settings);

			if ($errors !== []) {
				$this->session->flash(
					'error',
					implode("\n", $errors)
				);

				$this->response->redirect(
					'/admin/blocks/create?'
					. http_build_query([
						'page' => $page,
						'area' => $area,
						'type' => $type,
					])
				);

				return;
			}
		}

		$name = trim(
			(string) ($_POST['name'] ?? '')
		);

		$title = trim(
			(string) ($_POST['public_title'] ?? '')
		);

		$width = (int) (
			$_POST['width'] ?? 12
		);

		if (!in_array($width, [3, 4, 6, 8, 9, 12], true)) {
			$width = 12;
		}

		$encodedSettings = json_encode(
			$settings,
			JSON_UNESCAPED_UNICODE
			| JSON_UNESCAPED_SLASHES
		);

		$blockId = $this->repository->create([
			'page' => $page,
			'area' => $area,
			'type' => $type,
			'name' => $name !== ''
				? $name
				: null,
			'title' => $title !== ''
				? $title
				: null,
			'settings' => $encodedSettings !== false
				? $encodedSettings
				: '{}',
			'width' => $width,
			'position' => $this->repository->nextPosition(
				$page,
				$area
			),
			'enabled' => !empty($_POST['enabled'])
				? 1
				: 0,
		]);

		$translations = $_POST['translations'] ?? [];

		if (!is_array($translations)) {
			$translations = [];
		}

		$defaultLocale = $this->localeService->getDefaultLocale();
		$availableLocales = $this->localeService->getAvailableLocales();

		foreach ($availableLocales as $locale) {
			$locale = (string) $locale;

			if ($locale === '' || $locale === $defaultLocale) {
				continue;
			}

			$translatedTitle = '';

			if (
				isset($translations[$locale])
				&& is_array($translations[$locale])
			) {
				$translatedTitle = trim(
					(string) (
						$translations[$locale]['title']
						?? ''
					)
				);
			}

			$this->contentTranslations->set(
				'block',
				$blockId,
				$locale,
				'title',
				$translatedTitle
			);
		}

		$this->session->flash(
			'success',
			$this->translator->translate(
				'admin.blocks.messages.created'
			)
		);

		$this->response->redirect(
			$this->areaUrl($page, $area)
		);
	}

	public function edit(int $id): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$record = $this->repository->findById($id);

		if ($record === null) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$block = $this->blocks->get(
			(string) ($record['type'] ?? '')
		);

		if ($block === null) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$settings = json_decode(
			(string) ($record['settings'] ?? '{}'),
			true
		);

		if (!is_array($settings)) {
			$settings = [];
		}

		$titleTranslations = $this->contentTranslations->getAllForEntity(
			'block',
			$id
		);

		$this->render(
			'block-edit',
			[
				'navigation' => $this->navigation->items(),
				'block' => $record,

				'blockDefinition' => [
					'label' => $block->label(),
					'description' => $block->description(),
					'icon' => $block->icon(),
				],

				'editor' => $this->formRenderer->render(
					$block,
					$settings
				),

				'availableLocales' => $this->localeService->getAvailableLocales(),
				'defaultLocale' => $this->localeService->getDefaultLocale(),
				'titleTranslations' => $titleTranslations,
			],
			'admin-layout'
		);
	}

	public function update(int $id): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$record = $this->repository->findById($id);

		if ($record === null) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$block = $this->blocks->get(
			(string) ($record['type'] ?? '')
		);

		if ($block === null) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$settings = [];

		foreach ($block->settingsForm() as $field) {
			$fieldName = trim(
				(string) ($field['name'] ?? '')
			);

			if ($fieldName === '') {
				continue;
			}

			$settings[$fieldName] = (string) (
				$_POST[$fieldName] ?? ''
			);
		}

		if (
			$block instanceof \Monoverse\Core\Blocks\ValidatesSettingsInterface
		) {
			$errors = $block->validateSettings($settings);

			if ($errors !== []) {

				$this->session->flash(
					'error',
					implode("\n", $errors)
				);

				$this->response->redirect(
					'/admin/blocks/' . $id . '/edit'
				);

				return;
			}
		}

		$name = trim(
			(string) ($_POST['name'] ?? '')
		);

		$title = trim(
			(string) ($_POST['public_title'] ?? '')
		);

		$width = (int) (
			$_POST['width'] ?? 12
		);

		if (!in_array($width, [3, 4, 6, 8, 9, 12], true)) {
			$width = 12;
		}

		$encodedSettings = json_encode(
			$settings,
			JSON_UNESCAPED_UNICODE
			| JSON_UNESCAPED_SLASHES
		);

		$this->repository->update(
			$id,
			[
				'name' => $name !== ''
					? $name
					: null,
				'title' => $title !== ''
					? $title
					: null,
				'settings' => $encodedSettings !== false
					? $encodedSettings
					: '{}',
				'width' => $width,
				'enabled' => !empty($_POST['enabled'])
					? 1
					: 0,
			]
		);

		$translations = $_POST['translations'] ?? [];

		if (!is_array($translations)) {
			$translations = [];
		}

		$defaultLocale = $this->localeService->getDefaultLocale();
		$availableLocales = $this->localeService->getAvailableLocales();

		foreach ($availableLocales as $locale) {
			$locale = (string) $locale;

			if ($locale === '' || $locale === $defaultLocale) {
				continue;
			}

			$translatedTitle = '';

			if (
				isset($translations[$locale])
				&& is_array($translations[$locale])
			) {
				$translatedTitle = trim(
					(string) (
						$translations[$locale]['title']
						?? ''
					)
				);
			}

			$this->contentTranslations->set(
				'block',
				$id,
				$locale,
				'title',
				$translatedTitle
			);
		}

		$this->session->flash(
			'success',
			$this->translator->translate(
				'admin.blocks.messages.saved'
			)
		);

		$this->response->redirect(
			$this->areaUrl(
				(string) ($record['page'] ?? ''),
				(string) ($record['area'] ?? '')
			)
		);
	}

	public function toggle(int $id): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$record = $this->repository->findById($id);

		if ($record === null) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$this->repository->setEnabled(
			$id,
			empty($record['enabled'])
		);

		$this->session->flash(
			'success',
			empty($record['enabled'])
				? $this->translator->translate(
					'admin.blocks.messages.enabled'
				)
				: $this->translator->translate(
					'admin.blocks.messages.disabled'
				)
		);

		$this->response->redirect(
			$this->areaUrl(
				(string) ($record['page'] ?? ''),
				(string) ($record['area'] ?? '')
			)
		);
	}

	public function reorder(): void
	{
		if (!$this->auth->check()) {
			$this->jsonResponse(
				[
					'ok' => false,
					'message' => $this->translator->translate(
						'admin.blocks.errors.invalid_session'
					),
				],
				401
			);
			return;
		}

		$page = trim(
			(string) ($_POST['page'] ?? '')
		);

		$area = trim(
			(string) ($_POST['area'] ?? '')
		);

		$orderJson = (string) ($_POST['order'] ?? '[]');

		if (!$this->areaExists($page, $area)) {
			$this->jsonResponse(
				[
					'ok' => false,
					'message' => $this->translator->translate(
						'admin.blocks.errors.invalid_area'
					),
				],
				422
			);
			return;
		}

		$order = json_decode(
			$orderJson,
			true
		);

		if (!is_array($order)) {
			$this->jsonResponse(
				[
					'ok' => false,
					'message' => $this->translator->translate(
						'admin.blocks.errors.invalid_order'
					),
				],
				422
			);
			return;
		}

		$ids = [];

		foreach ($order as $id) {
			$id = (int) $id;

			if ($id > 0 && !in_array($id, $ids, true)) {
				$ids[] = $id;
			}
		}

		$configuredBlocks = $this->repository->findByArea(
			$page,
			$area
		);

		$expectedIds = array_map(
			static fn (array $record): int => (int) (
				$record['id'] ?? 0
			),
			$configuredBlocks
		);

		sort($ids);
		sort($expectedIds);

		if ($ids !== $expectedIds) {
			$this->jsonResponse(
				[
					'ok' => false,
					'message' => $this->translator->translate(
						'admin.blocks.errors.order_mismatch'
					),
				],
				422
			);
			return;
		}

		$this->repository->reorder(
			$page,
			$area,
			$order
		);

		$this->jsonResponse([
			'ok' => true,
			'message' => $this->translator->translate(
				'admin.blocks.messages.order_saved'
			),
		]);
	}

	public function delete(int $id): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		$record = $this->repository->findById($id);

		if ($record === null) {
			$this->response->redirect('/admin/blocks');
			return;
		}

		$this->repository->delete($id);

		$this->session->flash(
			'success',
			$this->translator->translate(
				'admin.blocks.messages.deleted'
			)
		);

		$this->response->redirect(
			$this->areaUrl(
				(string) ($record['page'] ?? ''),
				(string) ($record['area'] ?? '')
			)
		);
	}

	private function areaExists(
		string $page,
		string $area
	): bool {
		if ($this->areas->exists($page, $area)) {
			return true;
		}

		if (!str_starts_with($page, 'page:')) {
			return false;
		}

		if (!in_array(
			$area,
			['content', 'sidebar'],
			true
		)) {
			return false;
		}

		$slug = trim(
			substr($page, strlen('page:'))
		);

		if ($slug === '') {
			return false;
		}

		return $this->pages->findBySlug($slug) !== null;
	}

	private function prepareBlockRecord(
		array $record,
		array $definitions
	): array {
		$type = (string) ($record['type'] ?? '');
		$definition = $definitions[$type] ?? [];

		$name = trim(
			(string) ($record['name'] ?? '')
		);

		return [
			'id' => (int) ($record['id'] ?? 0),
			'type' => $type,
			'name' => $name,
			'label' => $name !== ''
				? $name
				: (string) ($definition['label'] ?? $type),
			'type_label' => (string) (
				$definition['label'] ?? $type
			),
			'icon' => (string) (
				$definition['icon'] ?? 'fa-cube'
			),
			'enabled' => !empty($record['enabled']),
			'width' => (int) ($record['width'] ?? 12),
			'position' => (int) ($record['position'] ?? 0),
		];
	}

	private function areaUrl(
		string $page,
		string $area
	): string {
		return '/admin/blocks/area?'
			. http_build_query([
				'page' => $page,
				'area' => $area,
			]);
	}

	private function jsonResponse(
		array $data,
		int $status = 200
	): void {
		http_response_code($status);
		header(
			'Content-Type: application/json; charset=UTF-8'
		);

		echo json_encode(
			$data,
			JSON_UNESCAPED_UNICODE
			| JSON_UNESCAPED_SLASHES
		);

		exit;
	}

	private function areaLabel(
		string $page,
		string $area,
		string $fallback
	): string {
		if (str_starts_with($page, 'page:')) {
			return $this->translator->translate(
				'admin.blocks.dynamic_areas.' . $area
			);
		}

		$pageKey = str_replace('-', '_', $page);
		$areaKey = str_replace('-', '_', $area);

		$key = 'admin.areas.'
			. $pageKey
			. '.'
			. $areaKey;

		$translated = $this->translator->translate($key);

		return $translated !== $key
			? $translated
			: $fallback;
	}

	private function pageLabel(string $page): string
	{
		if (str_starts_with($page, 'page:')) {
			$slug = trim(
				substr($page, strlen('page:'))
			);

			$dynamicPage = $this->pages->findBySlug($slug);

			if ($dynamicPage !== null) {
				$title = trim(
					(string) ($dynamicPage['title'] ?? '')
				);

				if ($title !== '') {
					return $title;
				}
			}

			return ucfirst(
				str_replace(
					['-', '_'],
					' ',
					$slug
				)
			);
		}

		$pageKey = str_replace('-', '_', $page);

		$key = 'admin.blocks.pages.' . $pageKey;

		$translated = $this->translator->translate($key);

		return $translated !== $key
			? $translated
			: ucfirst(
				str_replace(
					['-', '_'],
					' ',
					$page
				)
			);
	}
}
