<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Request;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Core\Blocks\BlockManager;
use Monoverse\Services\EventService;
use Monoverse\Services\MarkdownService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\SettingsService;

class EventsController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		private Request $request,
		NotificationService $notifications,
		private EventService $events,
		private MarkdownService $markdown,
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
		$events = $this->events->listUpcoming(50);

		$widgetAreas = [
			'beforeContent' => $this->blocks->renderArea(
				'events-public',
				'before-content'
			),
			'sidebar' => $this->blocks->renderArea(
				'events-public',
				'sidebar'
			),
			'afterContent' => $this->blocks->renderArea(
				'events-public',
				'after-content'
			),
		];

		$this->render('events-public', [
			'title' => 'Eventi',
			'events' => $events,
			'success' => $this->session->getFlash('success'),
			'error' => $this->session->getFlash('error'),
			'widgetAreas' => $widgetAreas,
			'blockCssFiles' => $this->blocks->stylesheets(),
			'blockJsFiles' => $this->blocks->scripts(),
		]);
	}

	public function show(string $slug): void
	{
		$slug = trim(rawurldecode($slug));

		if ($slug === '') {
			$this->notFound();
			return;
		}

		$event = $this->events->findPublishedBySlug($slug);

		if (!$event) {
			$this->notFound();
			return;
		}

		$event['description_html'] = $this->markdown->render(
			(string) ($event['description'] ?? '')
		);

		$eventPath = '/events/'
			. rawurlencode((string) $event['slug']);

		$plainDescription = trim(
			preg_replace(
				'/\s+/',
				' ',
				strip_tags(
					(string) ($event['description_html'] ?? '')
				)
			) ?? ''
		);

		$metaDescription = mb_substr(
			$plainDescription,
			0,
			160
		);

		$widgetAreas = [
			'beforeContent' => $this->blocks->renderArea(
				'event',
				'before-content'
			),
			'sidebar' => $this->blocks->renderArea(
				'event',
				'sidebar'
			),
			'afterContent' => $this->blocks->renderArea(
				'event',
				'after-content'
			),
		];

		$this->render('event', [
			'title' => (string) $event['title'],
			'metaDescription' => $metaDescription,
			'canonicalPath' => $eventPath,
			'openGraph' => [
				'type' => 'website',
				'title' => (string) $event['title'],
				'description' => $metaDescription,
				'path' => $eventPath,
				'image' => !empty($event['cover'])
					? (string) $event['cover']
					: null,
			],
			'event' => $event,
			'widgetAreas' => $widgetAreas,
			'blockCssFiles' => $this->blocks->stylesheets(),
			'blockJsFiles' => $this->blocks->scripts(),
		]);
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
				'events_user_submissions_enabled',
				'0'
			) !== '1'
		) {
			$this->response->redirect('/events');
			return;
		}

		$this->render(
			'event-submit',
			[
				'title' => 'Proponi un evento',
				'error' => $this->session->getFlash('error'),
			]
		);
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
				'events_user_submissions_enabled',
				'0'
			) !== '1'
		) {
			$this->response->redirect('/events');
			return;
		}

		$title = trim(
			(string) $this->request->post('title', '')
		);

		$description = trim(
			(string) $this->request->post('description', '')
		);

		$startsAt = $this->normalizeDateTime(
			(string) $this->request->post('starts_at', '')
		);

		$endsAtRaw = trim(
			(string) $this->request->post('ends_at', '')
		);

		$endsAt = $endsAtRaw !== ''
			? $this->normalizeDateTime($endsAtRaw)
			: null;

		$location = trim(
			(string) $this->request->post('location', '')
		);

		$latitude = trim(
			(string) $this->request->post('latitude', '')
		);

		$longitude = trim(
			(string) $this->request->post('longitude', '')
		);

		$externalUrl = trim(
			(string) $this->request->post('external_url', '')
		);

		$coordinates = $this->normalizeCoordinates(
			$latitude,
			$longitude
		);

		if ($coordinates === null) {
			$this->session->flash(
				'error',
				'Latitudine e longitudine devono essere entrambe valide.'
			);

			$this->response->redirect('/events/submit');
			return;
		}

		if (
			$title === ''
			|| $description === ''
			|| $startsAt === null
		) {
			$this->session->flash(
				'error',
				'Titolo, descrizione e data di inizio sono obbligatori.'
			);

			$this->response->redirect('/events/submit');
			return;
		}

		if ($endsAtRaw !== '' && $endsAt === null) {
			$this->session->flash(
				'error',
				'La data di fine non è valida.'
			);

			$this->response->redirect('/events/submit');
			return;
		}

		if (
			$endsAt !== null
			&& strtotime($endsAt) < strtotime($startsAt)
		) {
			$this->session->flash(
				'error',
				'La data di fine non può precedere quella di inizio.'
			);

			$this->response->redirect('/events/submit');
			return;
		}

		if (
			$externalUrl !== ''
			&& filter_var(
				$externalUrl,
				FILTER_VALIDATE_URL
			) === false
		) {
			$this->session->flash(
				'error',
				'Il link esterno non è valido.'
			);

			$this->response->redirect('/events/submit');
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
				'image/png' => 'png',
				'image/webp' => 'webp',
			];

			if (!isset($allowed[$mime])) {
				$this->session->flash(
					'error',
					'La cover deve essere JPEG, PNG o WebP.'
				);

				$this->response->redirect('/events/submit');
				return;
			}

			$directory = __DIR__
				. '/../../storage/events/'
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

			if (
				!move_uploaded_file(
					$_FILES['cover']['tmp_name'],
					$destination
				)
			) {
				$this->session->flash(
					'error',
					'Impossibile salvare la cover.'
				);

				$this->response->redirect('/events/submit');
				return;
			}

			$cover = '/storage/events/'
				. date('Y')
				. '/'
				. date('m')
				. '/'
				. $filename;
		}

		$created = $this->events->createSubmission([
			'title' => $title,
			'slug' => $slug,
			'description' => $description,
			'starts_at' => $startsAt,
			'ends_at' => $endsAt,
			'location' => $location !== ''
				? $location
				: null,
			'latitude' => $coordinates['latitude'],
			'longitude' => $coordinates['longitude'],
			'external_url' => $externalUrl !== ''
				? $externalUrl
				: null,
			'cover' => $cover,
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

			$this->response->redirect('/events/submit');
			return;
		}

		$this->session->flash(
			'success',
			'La tua proposta è stata inviata e sarà revisionata dall’amministratore.'
		);

		$this->response->redirect('/events');
	}

	protected function notFound(): void
	{
		$html = $this->view->render('404', [
			'title' => 'Pagina non trovata',
		]);

		$this->response
			->status(404)
			->header(
				'Content-Type',
				'text/html; charset=utf-8'
			)
			->send($html);
	}

	private function normalizeDateTime(string $value): ?string
	{
		$value = trim($value);

		if ($value === '') {
			return null;
		}

		$date = \DateTimeImmutable::createFromFormat(
			'Y-m-d\TH:i',
			$value
		);

		if (!$date) {
			return null;
		}

		return $date->format('Y-m-d H:i:s');
	}

	private function normalizeCoordinates(
		string $latitude,
		string $longitude
	): ?array {
		$latitude = trim($latitude);
		$longitude = trim($longitude);

		if ($latitude === '' && $longitude === '') {
			return [
				'latitude' => null,
				'longitude' => null,
			];
		}

		if ($latitude === '' || $longitude === '') {
			return null;
		}

		if (
			!is_numeric($latitude)
			|| !is_numeric($longitude)
		) {
			return null;
		}

		$latitudeValue = (float) $latitude;
		$longitudeValue = (float) $longitude;

		if (
			$latitudeValue < -90
			|| $latitudeValue > 90
			|| $longitudeValue < -180
			|| $longitudeValue > 180
		) {
			return null;
		}

		return [
			'latitude' => $latitudeValue,
			'longitude' => $longitudeValue,
		];
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

		$value = trim($value, '-');

		return substr($value, 0, 255);
	}

	private function generateUniqueSlug(string $title): string
	{
		$baseSlug = $this->slugify($title);

		if ($baseSlug === '') {
			$baseSlug = 'evento';
		}

		$slug = $baseSlug;
		$counter = 2;

		while ($this->events->slugExists($slug)) {
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
