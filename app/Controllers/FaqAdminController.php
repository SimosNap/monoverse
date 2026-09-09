<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\FaqService;
use Monoverse\Services\LocaleService;
use Monoverse\Services\ContentTranslationService;
use Monoverse\Services\SettingsService;
use Monoverse\Services\NavigationService;
use Throwable;

final class FaqAdminController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications,
		SettingsService $settings,
		private AdminAuthService $auth,
		private FaqService $faqs,
		private NavigationService $navigation,
		private LocaleService $locales,
		private ContentTranslationService $translations
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
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('site')) {
			$this->response->redirect('/admin');
			return;
		}

		$html = $this->view->render(
			'faqs',
			[
				'title' => 'FAQ',
				'admin' => $this->auth->user(),
				'faqs' => $this->faqs->all(),
				'navigation' => $this->navigation->items(),
			],
			'admin-layout'
		);

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

		if (!$this->auth->can('site')) {
			$this->response->redirect('/admin');
			return;
		}

		$this->renderForm(
			null,
			'/admin/faq',
			'Nuova FAQ'
		);
	}

	public function store(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('site')) {
			$this->response->redirect('/admin');
			return;
		}

		$data = $this->formData();
		$errors = $this->validate($data);

		if (
			$data['anchor'] !== ''
			&& $this->faqs->anchorExists($data['anchor'])
		) {
			$errors['anchor'] =
				'Esiste già una FAQ con questo anchor.';
		}

		if ($errors !== []) {
			$this->renderForm(
				$data,
				'/admin/faq',
				'Nuova FAQ',
				$errors
			);

			return;
		}

		try {
			$faqId = $this->faqs->create($data);

			$this->saveTranslations(
				$faqId,
				$data['translations'] ?? []
			);
		} catch (Throwable) {
			$this->renderForm(
				$data,
				'/admin/faq',
				'Nuova FAQ',
				[
					'general' =>
						'Non è stato possibile creare la FAQ.',
				]
			);

			return;
		}

		$this->response->redirect('/admin/faq');
	}

	public function edit(string $id): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('site')) {
			$this->response->redirect('/admin');
			return;
		}

		$faqId = $this->normalizeId($id);

		if ($faqId === null) {
			$this->notFound();
			return;
		}

		$faq = $this->faqs->find($faqId);

		if ($faq === null) {
			$this->notFound();
			return;
		}

		$this->renderForm(
			$faq,
			'/admin/faq/' . $faqId,
			'Modifica FAQ'
		);
	}

	public function update(string $id): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('site')) {
			$this->response->redirect('/admin');
			return;
		}

		$faqId = $this->normalizeId($id);

		if ($faqId === null) {
			$this->notFound();
			return;
		}

		$faq = $this->faqs->find($faqId);

		if ($faq === null) {
			$this->notFound();
			return;
		}

		$data = $this->formData();
		$errors = $this->validate($data);

		if (
			$data['anchor'] !== ''
			&& $this->faqs->anchorExists(
				$data['anchor'],
				$faqId
			)
		) {
			$errors['anchor'] =
				'Esiste già una FAQ con questo anchor.';
		}

		if ($errors !== []) {
			$this->renderForm(
				array_merge($faq, $data),
				'/admin/faq/' . $faqId,
				'Modifica FAQ',
				$errors
			);

			return;
		}

		try {
			$this->faqs->update(
				$faqId,
				$data
			);

			$this->saveTranslations(
				$faqId,
				$data['translations'] ?? []
			);
		} catch (Throwable) {
			$this->renderForm(
				array_merge($faq, $data),
				'/admin/faq/' . $faqId,
				'Modifica FAQ',
				[
					'general' =>
						'Non è stato possibile aggiornare la FAQ.',
				]
			);

			return;
		}

		$this->response->redirect('/admin/faq');
	}

	public function delete(string $id): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('site')) {
			$this->response->redirect('/admin');
			return;
		}

		$faqId = $this->normalizeId($id);

		if ($faqId === null) {
			$this->notFound();
			return;
		}

		$faq = $this->faqs->find($faqId);

		if ($faq === null) {
			$this->notFound();
			return;
		}

		try {
			$this->translations->deleteEntity(
				'faq',
				$faqId
			);

			$this->faqs->delete($faqId);
		} catch (Throwable) {
			$this->response->redirect('/admin/faq');
			return;
		}

		$this->response->redirect('/admin/faq');
	}

	private function renderForm(
		?array $faq,
		string $formAction,
		string $title,
		array $errors = []
	): void {
		$availableLocales =
			$this->locales->getAvailableLocales();

		$defaultLocale =
			$this->locales->getDefaultLocale();

		$questionTranslations = [];
		$answerTranslations = [];
		$sectionTranslations = [];

		if (
			$faq !== null
			&& !empty($faq['id'])
		) {
			$faqTranslations =
				$this->translations->getAllForEntity(
					'faq',
					(int) $faq['id']
				);

			foreach (
				$faqTranslations as $locale => $fields
			) {
				$questionTranslations[$locale] =
					(string) (
						$fields['question']
						?? ''
					);

				$answerTranslations[$locale] =
					(string) (
						$fields['answer']
						?? ''
					);

				$sectionTranslations[$locale] =
					(string) (
						$fields['section']
						?? ''
					);
			}
		}

		if (
			$faq !== null
			&& isset($faq['translations'])
			&& is_array($faq['translations'])
		) {
			foreach (
				$faq['translations'] as $locale => $fields
			) {
				if (!is_array($fields)) {
					continue;
				}

				$questionTranslations[$locale] =
					trim(
						(string) (
							$fields['question']
							?? ''
						)
					);

				$answerTranslations[$locale] =
					trim(
						(string) (
							$fields['answer']
							?? ''
						)
					);

				$sectionTranslations[$locale] =
					trim(
						(string) (
							$fields['section']
							?? ''
						)
					);
			}
		}

		$html = $this->view->render(
			'faq-form',
			[
				'title' => $title,
				'admin' => $this->auth->user(),
				'faq' => $faq,
				'errors' => $errors,
				'formAction' => $formAction,
				'navigation' => $this->navigation->items(),
				'availableLocales' => $availableLocales,
				'defaultLocale' => $defaultLocale,
				'questionTranslations' =>
					$questionTranslations,
				'answerTranslations' =>
					$answerTranslations,
				'sectionTranslations' =>
					$sectionTranslations,
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

	private function saveTranslations(
		int $faqId,
		mixed $translations
	): void {
		if (!is_array($translations)) {
			$translations = [];
		}

		$defaultLocale =
			$this->locales->getDefaultLocale();

		$availableLocales =
			$this->locales->getAvailableLocales();

		foreach ($availableLocales as $locale) {
			$locale = (string) $locale;

			if (
				$locale === ''
				|| $locale === $defaultLocale
			) {
				continue;
			}

			$fields = [];

			if (
				isset($translations[$locale])
				&& is_array($translations[$locale])
			) {
				$fields = $translations[$locale];
			}

			foreach (
				[
					'question',
					'answer',
					'section',
				] as $field
			) {
				$this->translations->set(
					'faq',
					$faqId,
					$locale,
					$field,
					trim(
						(string) (
							$fields[$field]
							?? ''
						)
					)
				);
			}
		}
	}

	private function formData(): array
	{
		$translations =
			isset($_POST['translations'])
			&& is_array($_POST['translations'])
				? $_POST['translations']
				: [];

		return [
			'question' => trim(
				(string) (
					$_POST['question']
					?? ''
				)
			),

			'anchor' => $this->normalizeAnchor(
				(string) (
					$_POST['anchor']
					?? ''
				)
			),

			'answer' => trim(
				(string) (
					$_POST['answer']
					?? ''
				)
			),

			'section' => trim(
				(string) (
					$_POST['section']
					?? ''
				)
			),

			'status' => $this->normalizeStatus(
				(string) (
					$_POST['status']
					?? 'draft'
				)
			),

			'sort_order' => max(
				0,
				(int) (
					$_POST['sort_order']
					?? 0
				)
			),

			'translations' => $translations,
		];
	}

	private function validate(array $data): array
	{
		$errors = [];

		$question = trim(
			(string) (
				$data['question']
				?? ''
			)
		);

		$anchor = trim(
			(string) (
				$data['anchor']
				?? ''
			)
		);

		$answer = trim(
			(string) (
				$data['answer']
				?? ''
			)
		);

		$section = trim(
			(string) (
				$data['section']
				?? ''
			)
		);

		if ($question === '') {
			$errors['question'] =
				'La domanda è obbligatoria.';
		} elseif (mb_strlen($question) > 255) {
			$errors['question'] =
				'La domanda non può superare 255 caratteri.';
		}

		if ($anchor === '') {
			$errors['anchor'] =
				'L\'anchor è obbligatorio.';
		} elseif (mb_strlen($anchor) > 190) {
			$errors['anchor'] =
				'L\'anchor non può superare 190 caratteri.';
		} elseif (!preg_match(
			'/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
			$anchor
		)) {
			$errors['anchor'] =
				'L\'anchor contiene caratteri non validi.';
		}

		if ($answer === '') {
			$errors['answer'] =
				'La risposta è obbligatoria.';
		}

		if (mb_strlen($section) > 150) {
			$errors['section'] =
				'La sezione non può superare 150 caratteri.';
		}

		return $errors;
	}

	private function normalizeAnchor(
		string $anchor
	): string {
		$anchor = trim(
			mb_strtolower($anchor)
		);

		$anchor = str_replace(
			[
				'à',
				'á',
				'è',
				'é',
				'ì',
				'í',
				'ò',
				'ó',
				'ù',
				'ú',
			],
			[
				'a',
				'a',
				'e',
				'e',
				'i',
				'i',
				'o',
				'o',
				'u',
				'u',
			],
			$anchor
		);

		$anchor = preg_replace(
			'/[^a-z0-9]+/',
			'-',
			$anchor
		) ?? '';

		return trim($anchor, '-');
	}

	private function normalizeStatus(
		string $status
	): string {
		return in_array(
			$status,
			[
				'draft',
				'published',
			],
			true
		)
			? $status
			: 'draft';
	}

	private function normalizeId(
		string $id
	): ?int {
		if (
			$id === ''
			|| !ctype_digit($id)
		) {
			return null;
		}

		$faqId = (int) $id;

		return $faqId > 0
			? $faqId
			: null;
	}
}
