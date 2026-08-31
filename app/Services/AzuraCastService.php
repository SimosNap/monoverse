<?php
declare(strict_types=1);

namespace Monoverse\Services;

final class AzuraCastService
{
	private const CACHE_TTL = 15;

	private const REQUESTS_CACHE_TTL = 60;
	private const MAX_RESPONSE_BYTES = 1048576;

	public function getNowPlaying(
		string $stationUrl
	): array {
		$stationUrl = rtrim(
			trim($stationUrl),
			'/'
		);

		if ($stationUrl === '') {
			return [];
		}

		if (!$this->isValidNowPlayingUrl($stationUrl)) {
			return [];
		}

		$cacheDirectory = __DIR__ . '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/azuracast-now-playing-'
			. hash('sha256', strtolower($stationUrl))
			. '.json';

		if (
			is_file($cacheFile)
			&& (time() - (int) filemtime($cacheFile))
				< self::CACHE_TTL
		) {
			$cachedJson = file_get_contents($cacheFile);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (is_array($cachedData)) {
					return $cachedData;
				}
			}
		}

		$data = $this->request($stationUrl);

		if ($data !== []) {
			$encodedData = json_encode(
				$data,
				JSON_UNESCAPED_UNICODE
				| JSON_UNESCAPED_SLASHES
			);

			if ($encodedData !== false) {
				$temporaryFile = $cacheFile . '.tmp';

				if (
					file_put_contents(
						$temporaryFile,
						$encodedData,
						LOCK_EX
					) !== false
				) {
					rename(
						$temporaryFile,
						$cacheFile
					);
				}
			}
		}

		return $data;
	}

	public function getRequestCatalog(
		string $requestsUrl
	): array {
		$requestsUrl = rtrim(
			trim($requestsUrl),
			'/'
		);

		if ($requestsUrl === '') {
			return [
				'available' => false,
				'items' => [],
				'message' => 'Endpoint delle richieste non configurato.',
			];
		}

		if (!$this->isValidRequestsUrl($requestsUrl)) {
			return [
				'available' => false,
				'items' => [],
				'message' => 'Endpoint delle richieste AzuraCast non valido.',
			];
		}

		$cacheDirectory = __DIR__ . '/../../storage/cache';

		$cacheFile = $cacheDirectory
			. '/azuracast-requests-'
			. hash('sha256', strtolower($requestsUrl))
			. '.json';

		if (
			is_file($cacheFile)
			&& (time() - (int) filemtime($cacheFile))
				< self::REQUESTS_CACHE_TTL
		) {
			$cachedJson = file_get_contents($cacheFile);

			if (
				$cachedJson !== false
				&& $cachedJson !== ''
			) {
				$cachedData = json_decode(
					$cachedJson,
					true
				);

				if (
					is_array($cachedData)
					&& array_key_exists(
						'available',
						$cachedData
					)
					&& isset($cachedData['items'])
					&& is_array($cachedData['items'])
				) {
					return $cachedData;
				}
			}
		}

		$responseBody = $this->requestBody(
			$requestsUrl
		);

		if ($responseBody === '') {
			$result = [
				'available' => false,
				'items' => [],
				'message' => 'Il servizio delle richieste non è raggiungibile.',
			];

			$this->writeRequestsCache(
				$cacheFile,
				$result
			);

			return $result;
		}

		$decoded = json_decode(
			$responseBody,
			true
		);

		if (
			!is_array($decoded)
			|| !array_is_list($decoded)
		) {
			$result = [
				'available' => false,
				'items' => [],
				'message' => 'La stazione non accetta richieste in questo momento.',
			];

			$this->writeRequestsCache(
				$cacheFile,
				$result
			);

			return $result;
		}

		$items = [];

		foreach ($decoded as $item) {
			if (!is_array($item)) {
				continue;
			}

			$requestId = trim(
				(string) ($item['request_id'] ?? '')
			);

			$requestUrl = trim(
				(string) ($item['request_url'] ?? '')
			);

			$song = is_array($item['song'] ?? null)
				? $item['song']
				: [];

			if (
				$requestId === ''
				|| $requestUrl === ''
				|| $song === []
			) {
				continue;
			}

			$items[] = [
				'request_id' => $requestId,
				'request_url' => $requestUrl,
				'song' => [
					'id' => trim(
						(string) ($song['id'] ?? '')
					),
					'art' => trim(
						(string) ($song['art'] ?? '')
					),
					'text' => trim(
						(string) ($song['text'] ?? '')
					),
					'artist' => trim(
						(string) ($song['artist'] ?? '')
					),
					'title' => trim(
						(string) ($song['title'] ?? '')
					),
					'album' => trim(
						(string) ($song['album'] ?? '')
					),
					'genre' => trim(
						(string) ($song['genre'] ?? '')
					),
				],
			];
		}

		$result = [
			'available' => true,
			'items' => $items,
			'message' => $items === []
				? 'Nessun brano è attualmente richiedibile.'
				: '',
		];

		$this->writeRequestsCache(
			$cacheFile,
			$result
		);

		return $result;
	}
	
	public function submitSongRequest(
		string $requestsUrl,
		string $requestId
	): array {
		$requestsUrl = rtrim(
			trim($requestsUrl),
			'/'
		);
	
		$requestId = trim($requestId);
	
		if (
			$requestsUrl === ''
			|| $requestId === ''
			|| !preg_match('/^[a-f0-9]+$/i', $requestId)
		) {
			return [
				'success' => false,
				'message' => 'Richiesta non valida.',
				'status' => 400,
			];
		}
	
		$catalog = $this->getRequestCatalog(
			$requestsUrl
		);
	
		if (!(bool) ($catalog['available'] ?? false)) {
			return [
				'success' => false,
				'message' => trim(
					(string) (
						$catalog['message']
						?? 'Le richieste non sono disponibili.'
					)
				),
				'status' => 409,
			];
		}
	
		$requestPath = '';
	
		foreach ((array) ($catalog['items'] ?? []) as $item) {
			if (!is_array($item)) {
				continue;
			}
	
			if (
				hash_equals(
					(string) ($item['request_id'] ?? ''),
					$requestId
				)
			) {
				$requestPath = trim(
					(string) ($item['request_url'] ?? '')
				);
	
				break;
			}
		}
	
		if ($requestPath === '') {
			return [
				'success' => false,
				'message' => 'Il brano non è più disponibile per la richiesta.',
				'status' => 404,
			];
		}
	
		$parts = parse_url($requestsUrl);
	
		if (
			!is_array($parts)
			|| !isset($parts['scheme'], $parts['host'])
			|| !in_array(
				strtolower((string) $parts['scheme']),
				['http', 'https'],
				true
			)
		) {
			return [
				'success' => false,
				'message' => 'Configurazione AzuraCast non valida.',
				'status' => 500,
			];
		}
	
		$requestUrl = (string) $parts['scheme']
			. '://'
			. (string) $parts['host'];
	
		if (isset($parts['port'])) {
			$requestUrl .= ':' . (int) $parts['port'];
		}
	
		$requestUrl .= '/'
			. ltrim($requestPath, '/');
	
		return $this->postRequest(
			$requestUrl
		);
	}
	
	private function translateResponseMessage(
		string $message
	): string {
		$message = trim($message);
	
		if ($message === '') {
			return '';
		}
	
		$normalized = strtolower($message);
	
		$translations = [
			'request submitted successfully'
				=> 'Richiesta inviata con successo.',
	
			'your request has been submitted'
				=> 'La tua richiesta è stata inviata.',
	
			'you have already requested a song recently'
				=> 'Hai già richiesto un brano recentemente. Attendi prima di inviarne un altro.',
				
			'cannot submit request: you have submitted a request too recently'
				=> 'Hai inviato una richiesta troppo recentemente.',
	
			'you have already requested this song'
				=> 'Hai già richiesto questo brano.',
	
			'this song has already been requested'
				=> 'Questo brano è già stato richiesto.',
				
			'cannot submit request: this song was already requested and will play soon'
				=> 'Questo brano è già stato richiesto e verrà riprodotto a breve.',
	
			'this song was played too recently'
				=> 'Questo brano è stato trasmesso troppo recentemente.',
	
			'this artist was played too recently'
				=> 'Un brano di questo artista è stato trasmesso troppo recentemente.',
	
			'requests are not currently accepted'
				=> 'La stazione non accetta richieste in questo momento.',
	
			'this station does not currently accept requests'
				=> 'La stazione non accetta richieste in questo momento.',
	
			'request not found'
				=> 'Il brano richiesto non è più disponibile.',
	
			'rate limit exceeded'
				=> 'Hai effettuato troppe richieste. Riprova più tardi.',
	
			'too many requests'
				=> 'Hai effettuato troppe richieste. Riprova più tardi.',
		];
	
		foreach ($translations as $english => $italian) {
			if (str_contains($normalized, $english)) {
				return $italian;
			}
		}
	
		return $message;
	}
	
	private function postRequest(
		string $url
	): array {
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'timeout' => 8,
				'ignore_errors' => true,
				'header' => [
					'Accept: application/json',
					'Content-Type: application/json',
					'User-Agent: Monoverse/1.0',
				],
				'content' => '{}',
			],
		]);
	
		$responseBody = @file_get_contents(
			$url,
			false,
			$context,
			0,
			self::MAX_RESPONSE_BYTES
		);
	
		$status = 0;
	
		if (isset($http_response_header[0])) {
			if (preg_match(
				'/\s(\d{3})\s/',
				(string) $http_response_header[0],
				$matches
			)) {
				$status = (int) $matches[1];
			}
		}
	
		$body = $responseBody !== false
			? trim($responseBody)
			: '';
	
		$decoded = $body !== ''
			? json_decode($body, true)
			: null;
	
		$message = '';
	
		if (is_array($decoded)) {
			$message = $this->translateResponseMessage(
				trim(
					(string) (
						$decoded['message']
							?? $decoded['error']
							?? ''
					)
				)
			);
		}
	
		if (
			$status >= 200
			&& $status < 300
		) {
			return [
				'success' => true,
				'message' => $message !== ''
					? $message
					: 'Richiesta inviata con successo.',
				'status' => $status,
			];
		}
	
		if ($message === '' && $body !== '') {
			$message = $this->translateResponseMessage(
				strip_tags($body)
			);
		}
	
		return [
			'success' => false,
			'message' => $message !== ''
				? $message
				: 'AzuraCast non ha accettato la richiesta.',
			'status' => $status > 0
				? $status
				: 502,
		];
	}

	private function writeRequestsCache(
		string $cacheFile,
		array $data
	): void {
		$encodedData = json_encode(
			$data,
			JSON_UNESCAPED_UNICODE
				| JSON_UNESCAPED_SLASHES
		);

		if ($encodedData === false) {
			return;
		}

		$temporaryFile = $cacheFile . '.tmp';

		if (
			file_put_contents(
				$temporaryFile,
				$encodedData,
				LOCK_EX
			) === false
		) {
			return;
		}

		rename(
			$temporaryFile,
			$cacheFile
		);
	}

	private function isValidRequestsUrl(
		string $url
	): bool {
		if (
			$url === ''
			|| filter_var(
				$url,
				FILTER_VALIDATE_URL
			) === false
		) {
			return false;
		}

		$parts = parse_url($url);

		if (
			!is_array($parts)
			|| !isset(
				$parts['scheme'],
				$parts['host'],
				$parts['path']
			)
		) {
			return false;
		}

		if (
			!in_array(
				strtolower((string) $parts['scheme']),
				['http', 'https'],
				true
			)
		) {
			return false;
		}

		$path = rtrim(
			(string) $parts['path'],
			'/'
		);

		return preg_match(
			'#/api/station/[^/]+/requests$#',
			$path
		) === 1;
	}

	private function isValidNowPlayingUrl(
		string $url
	): bool {
		if (
			$url === ''
			|| filter_var(
				$url,
				FILTER_VALIDATE_URL
			) === false
		) {
			return false;
		}

		$parts = parse_url($url);

		if (
			!is_array($parts)
			|| !isset(
				$parts['scheme'],
				$parts['host'],
				$parts['path']
			)
		) {
			return false;
		}

		if (
			!in_array(
				strtolower((string) $parts['scheme']),
				['http', 'https'],
				true
			)
		) {
			return false;
		}

		$path = rtrim(
			(string) $parts['path'],
			'/'
		);

		return preg_match(
			'#/api/nowplaying/[^/]+$#',
			$path
		) === 1;
	}

	private function requestBody(
		string $url
	): string {
		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'timeout' => 5,
				'ignore_errors' => true,
				'header' => [
					'Accept: application/json',
					'User-Agent: Monoverse/1.0',
				],
			],
		]);

		$responseBody = @file_get_contents(
			$url,
			false,
			$context
		);

		if (
			$responseBody === false
			|| $responseBody === ''
		) {
			return '';
		}

		return trim($responseBody);
	}

	private function request(
		string $stationUrl
	): array {
		$json = $this->requestBody(
			$stationUrl
		);

		if ($json === '') {
			return [];
		}

		$decoded = json_decode(
			$json,
			true
		);

		return is_array($decoded)
			? $decoded
			: [];
	}
}
