<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Services\OAuthService;
use Monoverse\Services\ProfileService;
use Monoverse\Services\AuthorizationService;
use Monoverse\Core\Session;

class OAuthController
{
    public function __construct(
        private Response $response,
        private OAuthService $authService,
        private ProfileService $profiles,
        private AuthorizationService $authorization,
        private Session $session
    ) {
    }

    public function login(): void
    {
        $returnTo = $_GET['return_to'] ?? null;

        if (
            is_string($returnTo)
            && $returnTo !== ''
            && str_starts_with($returnTo, '/')
            && !str_starts_with($returnTo, '//')
        ) {
            $this->session->set(
                'oauth.return_to',
                $returnTo
            );
        } else {
            $referer = $_SERVER['HTTP_REFERER'] ?? '';

            if (is_string($referer) && $referer !== '') {
                $parts = parse_url($referer);

                $refererHost = strtolower(
                    (string) ($parts['host'] ?? '')
                );

                $currentHost = strtolower(
                    preg_replace(
                        '/:\d+$/',
                        '',
                        (string) ($_SERVER['HTTP_HOST'] ?? '')
                    )
                );

                if (
                    $refererHost !== ''
                    && $currentHost !== ''
                    && $refererHost === $currentHost
                ) {
                    $returnTo =
                        (string) ($parts['path'] ?? '/')
                        . (
                            isset($parts['query'])
                                ? '?' . $parts['query']
                                : ''
                        );

                    $this->session->set(
                        'oauth.return_to',
                        $returnTo
                    );
                }
            }
        }

        $this->response->redirect(
            $this->authService->loginUrl()
        );
    }

    public function callback(): void
    {
        $state = $_GET['state'] ?? null;
        $code = $_GET['code'] ?? null;

        if (!$code) {
            echo 'OAuth error: authorization code mancante.';
            return;
        }

        if (
            !$this->authService->validateCallbackState(
                is_string($state) ? $state : null
            )
        ) {
            echo 'OAuth error: state non valido o sessione scaduta.';
            return;
        }

        $token = $this->authService->exchangeCodeForToken(
            (string) $code
        );

        if (empty($token['access_token'])) {
            echo '<pre>';
            echo 'OAuth error: token non ricevuto.' . PHP_EOL;
            print_r($token);
            echo '</pre>';
            return;
        }

        $userinfo = $this->authService->fetchUserInfo(
            (string) $token['access_token']
        );

        if (empty($userinfo['sub'])) {
            echo '<pre>';
            echo 'OAuth error: userinfo non valido.' . PHP_EOL;
            print_r($userinfo);
            echo '</pre>';
            return;
        }

        $this->authService->loginUser($userinfo, $token);

        $user = $this->authService->user();

        if (
            is_array($user) &&
            $this->authorization->isBanned($user)
        ) {
            $this->response->redirect('/account/suspended');
            return;
        }

        $username = trim((string) (
            $userinfo['username']
            ?? $userinfo['nickname']
            ?? $userinfo['preferred_username']
            ?? ''
        ));

        $avatarUrl = trim((string) (
            $userinfo['avatar_url']
            ?? $userinfo['picture']
            ?? ''
        ));

        $this->profiles->syncOAuthIdentity(
            (string) $userinfo['sub'],
            (string) ($userinfo['uid'] ?? ''),
            $username,
            $avatarUrl,
            is_array($userinfo['aliases'] ?? null)
                ? $userinfo['aliases']
                : []
        );

        $returnTo = $this->session->get(
            'oauth.return_to',
            '/'
        );

        $this->session->remove('oauth.return_to');

        if (
            !is_string($returnTo)
            || $returnTo === ''
            || !str_starts_with($returnTo, '/')
            || str_starts_with($returnTo, '//')
        ) {
            $returnTo = '/';
        }

        $this->response->redirect($returnTo);
    }

    public function logout(): void
    {
        $this->authService->logout();

        $this->response->redirect('/');
    }
}