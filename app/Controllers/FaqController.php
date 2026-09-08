<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\ContentTranslationService;
use Monoverse\Services\FaqService;
use Monoverse\Services\LocaleService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\SettingsService;

final class FaqController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications,
		SettingsService $settings,
		private FaqService $faqs,
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
		$faqs = $this->faqs->published();

		$locale = $this->locales->getCurrentLocale();
		$defaultLocale = $this->locales->getDefaultLocale();

		if ($locale !== $defaultLocale) {
			foreach ($faqs as &$faq) {
				$faqId = (int) ($faq['id'] ?? 0);

				if ($faqId <= 0) {
					continue;
				}

				$question = $this->translations->get(
					'faq',
					$faqId,
					$locale,
					'question'
				);

				if (
					is_string($question)
					&& trim($question) !== ''
				) {
					$faq['question'] = $question;
				}

				$answer = $this->translations->get(
					'faq',
					$faqId,
					$locale,
					'answer'
				);

				if (
					is_string($answer)
					&& trim($answer) !== ''
				) {
					$faq['answer'] = $answer;
				}

				$section = $this->translations->get(
					'faq',
					$faqId,
					$locale,
					'section'
				);

				if (
					is_string($section)
					&& trim($section) !== ''
				) {
					$faq['section'] = $section;
				}
			}

			unset($faq);
		}

		$sections = [];

		foreach ($faqs as $faq) {
			$section = trim((string) ($faq['section'] ?? ''));

			if ($section === '') {
				$section = 'FAQ';
			}

			if (!isset($sections[$section])) {
				$sections[$section] = [];
			}

			$sections[$section][] = $faq;
		}

		$html = $this->view->render(
			'faq',
			[
				'title' => 'FAQ',
				'faqs' => $faqs,
				'sections' => $sections,
			]
		);

		$this->response
			->status(200)
			->header(
				'Content-Type',
				'text/html; charset=utf-8'
			)
			->send($html);
	}
}
