<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Config;
use Monoverse\Core\Session;

class OAuthService
{
	public function __construct(
		private Config $config,
		private Session $session,
		private ModeratorService $moderators
	) {
	}

	public function loginUrl(): string
	{
		$oauth = $this->config->get('oauth', []);

		$state = $this->generateState();

		$this->session->set('oauth.state', $state);

		$query = http_build_query([
			'response_type' => 'code',
			'client_id' => $oauth['client_id'] ?? '',
			'redirect_uri' => rtrim((string) $this->config->get('base_url'), '/') . '/login/callback',
			'scope' => $oauth['scope'] ?? 'openid profile',
			'state' => $state,
		]);

		return ($oauth['authorize_url'] ?? '/') . '?' . $query;
	}

	private function generateState(): string
	{
		return bin2hex(random_bytes(32));
	}

	public function validateCallbackState(?string $state): bool
	{
		$expected = $this->session->get('oauth.state');

		if (!$state || !$expected) {
			return false;
		}

		return hash_equals((string) $expected, (string) $state);
	}

	public function exchangeCodeForToken(string $code): array
	{
		$oauth = $this->config->get('oauth', []);

		$postFields = [
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => rtrim((string) $this->config->get('base_url'), '/') . '/login/callback',
			'client_id' => $oauth['client_id'] ?? '',
			'client_secret' => $oauth['client_secret'] ?? '',
		];

		$ch = curl_init((string) ($oauth['token_url'] ?? ''));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));

		$response = curl_exec($ch);

		if ($response === false) {
			curl_close($ch);
			return [];
		}

		curl_close($ch);

		$token = json_decode($response, true);

		if (!is_array($token)) {
			return [];
		}

		return $token;
	}

	public function fetchUserInfo(string $accessToken): array
	{
		$oauth = $this->config->get('oauth', []);

		$ch = curl_init((string) ($oauth['userinfo_url'] ?? ''));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer ' . $accessToken,
		]);

		$response = curl_exec($ch);

		if ($response === false) {
			curl_close($ch);
			return [];
		}

		curl_close($ch);

		$userinfo = json_decode($response, true);

		if (!is_array($userinfo)) {
			return [];
		}

		return $userinfo;
	}

	public function loginUser(array $userinfo, array $token): void
	{
		$this->session->set('auth.user', [
			'sub' => $userinfo['sub'] ?? '',
			'uid' => $userinfo['uid'] ?? '',
			'nickname' => $userinfo['nickname'] ?? '',
			'preferred_username' => $userinfo['preferred_username'] ?? '',
			'avatar_url' => $userinfo['avatar_url'] ?? '',
			'aliases' => is_array($userinfo['aliases'] ?? null) ? $userinfo['aliases'] : [],
			'is_moderator' => $this->moderators->isModerator((string) ($userinfo['sub'] ?? '')),
		]);

		$this->session->set('auth.token', [
			'access_token' => $token['access_token'] ?? '',
			'token_type' => $token['token_type'] ?? 'Bearer',
			'expires_at' => time() + (int) ($token['expires_in'] ?? 3600),
		]);
	}

    public function check(): bool
    {
        return is_array($this->session->get('auth.user'));
    }

    public function user(): array
    {
        return $this->session->get('auth.user', []);
    }

    public function logout(): void
    {
        $this->session->remove('auth.user');
        $this->session->remove('auth.token');
        $this->session->remove('oauth.state');
    }

}