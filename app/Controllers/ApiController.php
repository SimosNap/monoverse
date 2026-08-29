<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Services\ProfileService;
use Monoverse\Services\AzuraCastService;

class ApiController
{
    private const SIMOSNAP_BASE_URL = 'https://www.simosnap.org';

    private const ENDPOINTS = [
        'nick/check' => [
            'method' => 'GET',
            'path' => '/rest/service.php/checknick',
            'query' => [
                'nickname',
            ],
        ],
    ];

    public function __construct(
        private Response $response,
        private ProfileService $profiles,
        private AzuraCastService $azuraCast
    ) {
    }

    public function mentions(): void
    {
        $query = $_GET['q'] ?? '';

        if (!is_scalar($query)) {
            $query = '';
        }

        $query = trim((string) $query);

        if (strlen($query) < 2) {
            $this->json([
                'success' => true,
                'users' => [],
            ], 200);

            return;
        }

        $query = substr($query, 0, 30);

        $profiles = $this->profiles->searchMentionUsers($query, 5);

        $users = [];

        foreach ($profiles as $profile) {
            $username = trim((string) ($profile['username'] ?? ''));

            if ($username === '') {
                continue;
            }

            $avatarUrl = null;

            if (
                (int) ($profile['show_avatar'] ?? 0) === 1
                && trim((string) ($profile['avatar_url'] ?? '')) !== ''
            ) {
                $avatarUrl = (string) $profile['avatar_url'];
            }

            $users[] = [
                'username' => $username,
                'avatar_url' => $avatarUrl,
            ];
        }

        $this->json([
            'success' => true,
            'users' => $users,
        ], 200);
    }

    public function azuraCastSongRequest(): void
    {
        if (
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'))
            !== 'POST'
        ) {
            $this->json([
                'success' => false,
                'message' => 'Metodo non consentito.',
            ], 405);

            return;
        }

        $rawBody = file_get_contents('php://input');

        $payload = [];

        if (
            is_string($rawBody)
            && trim($rawBody) !== ''
        ) {
            $decoded = json_decode(
                $rawBody,
                true
            );

            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        if ($payload === []) {
            $payload = $_POST;
        }

        $requestsUrl = $payload['requests_url'] ?? '';
        $requestId = $payload['request_id'] ?? '';

        if (!is_scalar($requestsUrl)) {
            $requestsUrl = '';
        }

        if (!is_scalar($requestId)) {
            $requestId = '';
        }

        $requestsUrl = rtrim(
            trim((string) $requestsUrl),
            '/'
        );

        $requestId = trim(
            (string) $requestId
        );

        if (
            $requestsUrl === ''
            || $requestId === ''
        ) {
            $this->json([
                'success' => false,
                'message' => 'Dati della richiesta mancanti.',
            ], 400);

            return;
        }

        $urlParts = parse_url(
            $requestsUrl
        );

        if (
            !is_array($urlParts)
            || !isset(
                $urlParts['scheme'],
                $urlParts['host'],
                $urlParts['path']
            )
            || strtolower(
                (string) $urlParts['scheme']
            ) !== 'https'
            || !preg_match(
                '#^/api/station/\d+/requests$#',
                (string) $urlParts['path']
            )
        ) {
            $this->json([
                'success' => false,
                'message' => 'Endpoint AzuraCast non valido.',
            ], 400);

            return;
        }

        if (!preg_match(
            '/^[a-f0-9]+$/i',
            $requestId
        )) {
            $this->json([
                'success' => false,
                'message' => 'Identificativo del brano non valido.',
            ], 400);

            return;
        }

        $result = $this->azuraCast->submitSongRequest(
            $requestsUrl,
            $requestId
        );

        $status = (int) (
            $result['status']
            ?? 500
        );

        if (
            $status < 100
            || $status > 599
        ) {
            $status = 500;
        }

        $this->json([
            'success' => (bool) (
                $result['success']
                ?? false
            ),
            'message' => trim(
                (string) (
                    $result['message']
                    ?? 'Impossibile inviare la richiesta.'
                )
            ),
        ], $status);
    }

    public function simosnapProxy(string $endpoint): void
    {
        $endpoint = trim($endpoint, '/');

        if (!isset(self::ENDPOINTS[$endpoint])) {
            $this->json([
                'success' => false,
                'error' => 'Endpoint non autorizzato.',
            ], 404);

            return;
        }

        $definition = self::ENDPOINTS[$endpoint];
        $method = strtoupper((string) ($definition['method'] ?? 'GET'));

        if ($method !== 'GET') {
            $this->json([
                'success' => false,
                'error' => 'Metodo non supportato.',
            ], 405);

            return;
        }

        $query = $this->filterQueryParameters(
            (array) ($definition['query'] ?? [])
        );

        if ($endpoint === 'nick/check') {
            $nickname = trim((string) ($query['nickname'] ?? ''));

            if ($nickname === '') {
                $this->json([
                    'success' => false,
                    'registered' => false,
                    'error' => 'Nickname mancante.',
                ], 400);

                return;
            }

            $query['nickname'] = $nickname;
        }

        if ($endpoint === 'nick/check') {

            $url = self::SIMOSNAP_BASE_URL
                . '/rest/service.php/checknick/'
                . rawurlencode((string) $query['nickname']);

        } else {

            $url = self::SIMOSNAP_BASE_URL
                . (string) $definition['path'];

            if ($query !== []) {
                $url .= '?' . http_build_query(
                    $query,
                    '',
                    '&',
                    PHP_QUERY_RFC3986
                );
            }

        }

        $result = $this->request($url);

        if (!$result['success']) {
            $this->json([
                'success' => false,
                'error' => $result['error'],
                'upstream_status' => $result['status'],
            ], 502);

            return;
        }

        $data = json_decode((string) $result['body'], true);

        if (!is_array($data)) {
            $this->json([
                'success' => false,
                'error' => 'Risposta API SimosNap non valida.',
                'upstream_status' => $result['status'],
            ], 502);

            return;
        }

        $this->json($data, 200);
    }

    private function filterQueryParameters(array $allowed): array
    {
        $query = [];

        foreach ($allowed as $name) {
            if (!is_string($name) || $name === '') {
                continue;
            }

            if (!array_key_exists($name, $_GET)) {
                continue;
            }

            $value = $_GET[$name];

            if (!is_scalar($value)) {
                continue;
            }

            $query[$name] = trim((string) $value);
        }

        return $query;
    }

    private function request(string $url): array
    {
        $ch = curl_init($url);

        if ($ch === false) {
            return [
                'success' => false,
                'status' => 0,
                'body' => '',
                'error' => 'Impossibile inizializzare la richiesta HTTP.',
            ];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: Monoverse-SimosNap-Proxy/1.0',
            ],
        ]);

        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($body === false) {
            return [
                'success' => false,
                'status' => $status,
                'body' => '',
                'error' => $error !== ''
                    ? $error
                    : 'Errore durante la richiesta verso SimosNap.',
            ];
        }

        if ($status < 200 || $status >= 300) {
            return [
                'success' => false,
                'status' => $status,
                'body' => (string) $body,
                'error' => 'L’API SimosNap ha restituito un errore.',
            ];
        }

        return [
            'success' => true,
            'status' => $status,
            'body' => (string) $body,
            'error' => '',
        ];
    }

    private function json(array $data, int $status): void
    {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_INVALID_UTF8_SUBSTITUTE
        );

        if ($json === false) {
            $json = '{"success":false,"error":"Errore di serializzazione JSON."}';
            $status = 500;
        }

        $this->response
            ->status($status)
            ->header('Content-Type', 'application/json; charset=utf-8')
            ->header('Cache-Control', 'no-store')
            ->send($json);
    }
}
