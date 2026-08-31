<?php
declare(strict_types=1);

namespace Monoverse\Services;

final class IcecastService
{
	private const STATUS_PATH = '/status-json.xsl';
	private const CACHE_TTL = 15;
	private const MAX_RESPONSE_BYTES = 1048576;

	/**
	 * @return array<string, mixed>|null
	 */
	public function getStatus(string $baseUrl): ?array
	{
		$baseUrl = $this->normalizeBaseUrl($baseUrl);

		if ($baseUrl === '') {
			return null;
		}

		$url = $baseUrl . self::STATUS_PATH;

		$cacheKey = 'icecast-status-' . sha1($url);
		$cached = $this->readCache($cacheKey);

		if ($cached !== null) {
			return $cached;
		}

		$data = $this->requestJson($url);

		if ($data === null) {
			return null;
		}

		$this->writeCache($cacheKey, $data);

		return $data;
	}

	/**
	 * Icecast può restituire "source" come oggetto singolo o lista.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getSources(string $baseUrl): array
	{
		$status = $this->getStatus($baseUrl);

		if ($status === null) {
			return [];
		}

		$source = $status['icestats']['source'] ?? null;

		if (!is_array($source) || $source === []) {
			return [];
		}

		if (array_is_list($source)) {
			return array_values(
				array_filter(
					$source,
					static fn (mixed $item): bool => is_array($item)
				)
			);
		}

		return [$source];
	}

	/**
	 * Accetta sia "/radio" sia il valore completo di "listenurl".
	 *
	 * @return array<string, mixed>|null
	 */
	public function getSource(string $baseUrl, string $mount): ?array
	{
		$mount = trim($mount);

		if ($mount === '') {
			return null;
		}

		if (!str_starts_with($mount, '/')) {
			$mount = '/' . $mount;
		}

		foreach ($this->getSources($baseUrl) as $source) {
			$listenUrl = trim((string) ($source['listenurl'] ?? ''));

			if ($listenUrl === '') {
				continue;
			}

			$path = parse_url($listenUrl, PHP_URL_PATH);

			if (is_string($path) && $path === $mount) {
				return $source;
			}
		}

		return null;
	}

	private function normalizeBaseUrl(string $baseUrl): string
	{
		$baseUrl = trim($baseUrl);

		if (
			$baseUrl === ''
			|| filter_var(
				$baseUrl,
				FILTER_VALIDATE_URL
			) === false
		) {
			return '';
		}

		$scheme = strtolower(
			(string) parse_url(
				$baseUrl,
				PHP_URL_SCHEME
			)
		);

		if (!in_array(
			$scheme,
			['http', 'https'],
			true
		)) {
			return '';
		}

		$path = trim(
			(string) parse_url(
				$baseUrl,
				PHP_URL_PATH
			),
			'/'
		);

		if ($path !== '') {
			return '';
		}

		return rtrim($baseUrl, '/');
	}

	/**
	 * @return array<string, mixed>|null
	 */
	private function requestJson(string $url): ?array
	{
		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'timeout' => 5,
				'ignore_errors' => true,
				'header' => implode("\r\n", [
					'Accept: application/json',
					'User-Agent: Monoverse/1.0',
				]),
			],
		]);

		$json = @file_get_contents(
			$url,
			false,
			$context,
			0,
			self::MAX_RESPONSE_BYTES
		);

		if ($json === false || trim($json) === '') {
			return null;
		}

		$data = json_decode($json, true);

		if (!is_array($data)) {
			return null;
		}

		if (!isset($data['icestats']) || !is_array($data['icestats'])) {
			return null;
		}

		return $data;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	private function readCache(string $key): ?array
	{
		$file = $this->cacheFile($key);

		if (!is_file($file)) {
			return null;
		}

		$modifiedAt = @filemtime($file);

		if (
			$modifiedAt === false
			|| (time() - $modifiedAt) > self::CACHE_TTL
		) {
			return null;
		}

		$json = @file_get_contents($file);

		if ($json === false || $json === '') {
			return null;
		}

		$data = json_decode($json, true);

		return is_array($data) ? $data : null;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	private function writeCache(string $key, array $data): void
	{
		$file = $this->cacheFile($key);
		$directory = dirname($file);

		if (!is_dir($directory)) {
			@mkdir($directory, 0775, true);
		}

		$json = json_encode(
			$data,
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);

		if ($json === false) {
			return;
		}

		@file_put_contents($file, $json, LOCK_EX);
	}

	private function cacheFile(string $key): string
	{
		return dirname(__DIR__, 2)
			. '/storage/cache/'
			. $key
			. '.json';
	}
}
