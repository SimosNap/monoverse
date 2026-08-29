<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\NotificationService;

class MediaController extends BaseController
{
	public function __construct(
		View $view,
		Response $response,
		Session $session,
		NotificationService $notifications
	) {
		parent::__construct(
			$view,
			$response,
			$session,
			$notifications
		);
	}

	public function chanzine(string $year, string $month, string $file): void
	{
		$path = __DIR__
			. '/../../storage/chanzine/'
			. $year . '/'
			. $month . '/'
			. basename($file);
	
		if (!is_file($path)) {
			http_response_code(404);
			exit;
		}
	
		$mime = mime_content_type($path);
		
		header('Content-Type: ' . $mime);
		header('Content-Length: ' . filesize($path));
		header('Cache-Control: public, max-age=31536000');
	
		readfile($path);
		exit;
	}
}
