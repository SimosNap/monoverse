<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\EventService;
use Monoverse\Services\NavigationService;

class EventController
{
	public function __construct(
		private View $view,
		private Response $response,
		private Request $request,
		private Session $session,
		private AdminAuthService $auth,
		private EventService $events,
		private NavigationService $navigation
	) {
	}

	public function index(): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$html = $this->view->render('events', [
			'title' => 'Eventi',
			'admin' => $this->auth->user(),
			'events' => $this->events->listAll(),
			'submissions' => $this->events->listSubmitted(),
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

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$html = $this->view->render('event-form', [
			'title' => 'Nuovo evento',
			'admin' => $this->auth->user(),
			'event' => null,
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

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$title = trim((string) $this->request->post('title', ''));
		$slug = trim((string) $this->request->post('slug', ''));
		$description = trim((string) $this->request->post('description', ''));
		$startsAt = trim((string) $this->request->post('starts_at', ''));
		$endsAt = trim((string) $this->request->post('ends_at', ''));
		$location = trim((string) $this->request->post('location', ''));
		$latitude = trim((string) $this->request->post('latitude', ''));
		$longitude = trim((string) $this->request->post('longitude', ''));
		$externalUrl = trim((string) $this->request->post('external_url', ''));

		if (
			$title === ''
			|| $description === ''
			|| $startsAt === ''
		) {
			$this->session->flash(
				'error',
				'Titolo, descrizione e data di inizio sono obbligatori.'
			);

			$this->response->redirect('/admin/events/create');
			return;
		}

		$coordinates = $this->normalizeCoordinates(
			$latitude,
			$longitude
		);

		if ($coordinates === false) {
			$this->session->flash(
				'error',
				'Le coordinate non sono valide. Inserisci latitudine e longitudine entrambe oppure lasciale vuote.'
			);

			$this->response->redirect('/admin/events/create');
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

			$this->response->redirect('/admin/events/create');
			return;
		}

		if ($this->events->slugExists($slug)) {
			$this->session->flash(
				'error',
				'Esiste già un evento con questo slug.'
			);

			$this->response->redirect('/admin/events/create');
			return;
		}

		$startsAt = $this->normalizeDateTime($startsAt);

		if ($startsAt === null) {
			$this->session->flash(
				'error',
				'La data di inizio non è valida.'
			);

			$this->response->redirect('/admin/events/create');
			return;
		}

		$normalizedEndsAt = null;

		if ($endsAt !== '') {
			$normalizedEndsAt = $this->normalizeDateTime($endsAt);

			if ($normalizedEndsAt === null) {
				$this->session->flash(
					'error',
					'La data di fine non è valida.'
				);

				$this->response->redirect('/admin/events/create');
				return;
			}

			if ($normalizedEndsAt < $startsAt) {
				$this->session->flash(
					'error',
					'La data di fine non può precedere quella di inizio.'
				);

				$this->response->redirect('/admin/events/create');
				return;
			}
		}

		if (
			$externalUrl !== ''
			&& filter_var($externalUrl, FILTER_VALIDATE_URL) === false
		) {
			$this->session->flash(
				'error',
				'Il link esterno non è valido.'
			);

			$this->response->redirect('/admin/events/create');
			return;
		}

		$cover = $this->handleCoverUpload(
			null,
			'/admin/events/create'
		);

		if ($cover === false) {
			return;
		}

		$created = $this->events->create([
			'title' => $title,
			'slug' => $slug,
			'description' => $description,
			'starts_at' => $startsAt,
			'ends_at' => $normalizedEndsAt,
			'location' => $location !== '' ? $location : null,
			'latitude' => $coordinates['latitude'],
			'longitude' => $coordinates['longitude'],
			'external_url' => $externalUrl !== '' ? $externalUrl : null,
			'cover' => $cover,
		]);

		if (!$created) {
			$this->session->flash(
				'error',
				'Non è stato possibile salvare l’evento.'
			);

			$this->response->redirect('/admin/events/create');
			return;
		}

		$this->session->flash(
			'success',
			'Bozza salvata.'
		);

		$this->response->redirect('/admin/events');
	}

	public function edit(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$event = $this->events->findByUuid($uuid);

		if (!$event) {
			$this->session->flash(
				'error',
				'Evento non trovato.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		$html = $this->view->render('event-form', [
			'title' => ($event['status'] === 'submitted')
				? 'Revisiona proposta'
				: 'Modifica evento',
			'admin' => $this->auth->user(),
			'event' => $event,
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

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$event = $this->events->findByUuid($uuid);

		if (!$event) {
			$this->session->flash(
				'error',
				'Evento non trovato.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		$title = trim((string) $this->request->post('title', ''));
		$slug = trim((string) $this->request->post('slug', ''));
		$description = trim((string) $this->request->post('description', ''));
		$startsAt = trim((string) $this->request->post('starts_at', ''));
		$endsAt = trim((string) $this->request->post('ends_at', ''));
		$location = trim((string) $this->request->post('location', ''));
		$latitude = trim((string) $this->request->post('latitude', ''));
		$longitude = trim((string) $this->request->post('longitude', ''));
		$externalUrl = trim((string) $this->request->post('external_url', ''));

		if (
			$title === ''
			|| $description === ''
			|| $startsAt === ''
		) {
			$this->session->flash(
				'error',
				'Titolo, descrizione e data di inizio sono obbligatori.'
			);

			$this->response->redirect(
				'/admin/events/' . $uuid . '/edit'
			);
			return;
		}

		$coordinates = $this->normalizeCoordinates(
			$latitude,
			$longitude
		);

		if ($coordinates === false) {
			$this->session->flash(
				'error',
				'Le coordinate non sono valide. Inserisci latitudine e longitudine entrambe oppure lasciale vuote.'
			);

			$this->response->redirect(
				'/admin/events/' . $uuid . '/edit'
			);
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

			$this->response->redirect(
				'/admin/events/' . $uuid . '/edit'
			);
			return;
		}

		if ($this->events->slugExists($slug, $uuid)) {
			$this->session->flash(
				'error',
				'Esiste già un evento con questo slug.'
			);

			$this->response->redirect(
				'/admin/events/' . $uuid . '/edit'
			);
			return;
		}

		$startsAt = $this->normalizeDateTime($startsAt);

		if ($startsAt === null) {
			$this->session->flash(
				'error',
				'La data di inizio non è valida.'
			);

			$this->response->redirect(
				'/admin/events/' . $uuid . '/edit'
			);
			return;
		}

		$normalizedEndsAt = null;

		if ($endsAt !== '') {
			$normalizedEndsAt = $this->normalizeDateTime($endsAt);

			if ($normalizedEndsAt === null) {
				$this->session->flash(
					'error',
					'La data di fine non è valida.'
				);

				$this->response->redirect(
					'/admin/events/' . $uuid . '/edit'
				);
				return;
			}

			if ($normalizedEndsAt < $startsAt) {
				$this->session->flash(
					'error',
					'La data di fine non può precedere quella di inizio.'
				);

				$this->response->redirect(
					'/admin/events/' . $uuid . '/edit'
				);
				return;
			}
		}

		if (
			$externalUrl !== ''
			&& filter_var($externalUrl, FILTER_VALIDATE_URL) === false
		) {
			$this->session->flash(
				'error',
				'Il link esterno non è valido.'
			);

			$this->response->redirect(
				'/admin/events/' . $uuid . '/edit'
			);
			return;
		}

		$cover = $this->handleCoverUpload(
			$event['cover'] ?? null,
			'/admin/events/' . $uuid . '/edit'
		);

		if ($cover === false) {
			return;
		}

		$updated = $this->events->update($uuid, [
			'title' => $title,
			'slug' => $slug,
			'description' => $description,
			'starts_at' => $startsAt,
			'ends_at' => $normalizedEndsAt,
			'location' => $location !== '' ? $location : null,
			'latitude' => $coordinates['latitude'],
			'longitude' => $coordinates['longitude'],
			'external_url' => $externalUrl !== '' ? $externalUrl : null,
			'cover' => $cover,
		]);

		if (!$updated) {
			$this->session->flash(
				'error',
				'Non è stato possibile aggiornare l’evento.'
			);

			$this->response->redirect(
				'/admin/events/' . $uuid . '/edit'
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
			&& (($event['status'] ?? '') === 'submitted')
		) {
			$this->publish($uuid);
			return;
		}

		$this->session->flash(
			'success',
			'Evento aggiornato.'
		);

		$this->response->redirect('/admin/events');
	}

	public function publish(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$event = $this->events->findByUuid($uuid);

		if (!$event) {
			$this->session->flash(
				'error',
				'Evento non trovato.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		if ($event['status'] === 'published') {
			$this->session->flash(
				'error',
				'L’evento è già pubblicato.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		if (!$this->events->publish($uuid)) {
			$this->session->flash(
				'error',
				'Non è stato possibile pubblicare l’evento.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		$this->session->flash(
			'success',
			'Evento pubblicato.'
		);

		$this->response->redirect('/admin/events');
	}

	public function moveToDraft(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$event = $this->events->findByUuid($uuid);

		if (!$event) {
			$this->session->flash(
				'error',
				'Evento non trovato.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		if (!$this->events->moveToDraft($uuid)) {
			$this->session->flash(
				'error',
				'Non è stato possibile riportare l’evento in bozza.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		$this->session->flash(
			'success',
			'Evento riportato in bozza.'
		);

		$this->response->redirect('/admin/events');
	}

	public function reject(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$event = $this->events->findByUuid($uuid);

		if (!$event) {
			$this->response->redirect('/admin/events');
			return;
		}

		if (($event['status'] ?? '') !== 'submitted') {
			$this->response->redirect('/admin/events');
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

			$this->response->redirect('/admin/events');
			return;
		}

		if (!$this->events->reject($uuid, $reason)) {
			$this->session->flash(
				'error',
				'Non è stato possibile rifiutare la proposta.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		$this->session->flash(
			'success',
			'Proposta rifiutata.'
		);

		$this->response->redirect('/admin/events');
	}

	public function delete(string $uuid): void
	{
		if (!$this->auth->check()) {
			$this->response->redirect('/admin/login');
			return;
		}

		if (!$this->auth->can('content')) {
			$this->response->redirect('/admin');
			return;
		}

		$event = $this->events->findByUuid($uuid);

		if (!$event) {
			$this->session->flash(
				'error',
				'Evento non trovato.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		if (!$this->events->delete($uuid)) {
			$this->session->flash(
				'error',
				'Non è stato possibile eliminare l’evento.'
			);

			$this->response->redirect('/admin/events');
			return;
		}

		if (!empty($event['cover'])) {
			$coverFile = __DIR__
				. '/../../'
				. ltrim((string) $event['cover'], '/');

			if (is_file($coverFile)) {
				@unlink($coverFile);
			}
		}

		$this->session->flash(
			'success',
			'Evento eliminato.'
		);

		$this->response->redirect('/admin/events');
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

	private function normalizeDateTime(string $value): ?string
	{
		$value = trim($value);

		if ($value === '') {
			return null;
		}

		$dateTime = \DateTimeImmutable::createFromFormat(
			'Y-m-d\TH:i',
			$value
		);

		if ($dateTime === false) {
			return null;
		}

		$errors = \DateTimeImmutable::getLastErrors();

		if (
			is_array($errors)
			&& (
				$errors['warning_count'] > 0
				|| $errors['error_count'] > 0
			)
		) {
			return null;
		}

		return $dateTime->format('Y-m-d H:i:s');
	}

	private function normalizeCoordinates(
		string $latitude,
		string $longitude
	): array|false {
		$latitude = trim($latitude);
		$longitude = trim($longitude);

		if ($latitude === '' && $longitude === '') {
			return [
				'latitude' => null,
				'longitude' => null,
			];
		}

		if ($latitude === '' || $longitude === '') {
			return false;
		}

		if (
			!is_numeric($latitude)
			|| !is_numeric($longitude)
		) {
			return false;
		}

		$latitudeValue = (float) $latitude;
		$longitudeValue = (float) $longitude;

		if (
			$latitudeValue < -90
			|| $latitudeValue > 90
			|| $longitudeValue < -180
			|| $longitudeValue > 180
		) {
			return false;
		}

		return [
			'latitude' => $latitudeValue,
			'longitude' => $longitudeValue,
		];
	}

	private function handleCoverUpload(
		?string $currentCover,
		string $redirect
	): string|false|null {
		if (
			!isset($_FILES['cover'])
			|| $_FILES['cover']['error'] === UPLOAD_ERR_NO_FILE
		) {
			return $currentCover;
		}

		if ($_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
			$this->session->flash(
				'error',
				'Errore durante il caricamento della cover.'
			);

			$this->response->redirect($redirect);
			return false;
		}

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

			$this->response->redirect($redirect);
			return false;
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

		if (!move_uploaded_file(
			$_FILES['cover']['tmp_name'],
			$destination
		)) {
			$this->session->flash(
				'error',
				'Impossibile salvare la cover.'
			);

			$this->response->redirect($redirect);
			return false;
		}

		if ($currentCover !== null && $currentCover !== '') {
			$oldFile = __DIR__
				. '/../../'
				. ltrim($currentCover, '/');

			if (is_file($oldFile)) {
				@unlink($oldFile);
			}
		}

		return '/storage/events/'
			. date('Y')
			. '/'
			. date('m')
			. '/'
			. $filename;
	}
}
