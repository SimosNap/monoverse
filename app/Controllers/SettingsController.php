<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Request;
use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AdminAuthService;
use Monoverse\Services\SettingsService;
use RuntimeException;

class SettingsController
{
    public function __construct(
        private View $view,
        private Response $response,
        private Request $request,
        private Session $session,
        private AdminAuthService $auth,
        private SettingsService $settings,
        private \Monoverse\Services\NavigationService $navigation
    ) {
    }

    public function index(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
        }

        $html = $this->view->render('settings', [
            'title' => 'Impostazioni',
            'admin' => $this->auth->user(),
            'settings' => $this->settings->all(),
            'errors' => $this->session->getFlash('errors', []),
            'success' => $this->session->getFlash('success'),
            'navigation' => $this->navigation->items(),
        ], 'admin-layout');

        $this->response
            ->status(200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send($html);
    }

    public function save(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
        }

        $this->settings->set('site_name', (string) $this->request->post('site_name', ''));
        $this->settings->set('site_tagline', (string) $this->request->post('site_tagline', ''));

        $siteUrl = rtrim(
            trim((string) $this->request->post('site_url', '')),
            '/'
        );

        $metaDescription = trim(
            (string) $this->request->post('meta_description', '')
        );

        $this->settings->set(
            'pages_navigation_main',
            $this->request->post('pages_navigation_main')
                ? '1'
                : '0'
        );

        $this->settings->set(
            'media_audio_upload_enabled',
            $this->request->post('media_audio_upload_enabled')
                ? '1'
                : '0'
        );

        $audioMaxMb = filter_var(
            $this->request->post('media_audio_max_mb', 50),
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if ($audioMaxMb === false) {
            $this->session->flash('errors', [
                'media_audio_max_mb' => 'Il limite upload audio deve essere almeno 1 MB.',
            ]);

            $this->response->redirect('/admin/settings');
            return;
        }

        $this->settings->set(
            'media_audio_max_mb',
            (string) $audioMaxMb
        );

        $this->settings->set(
            'media_video_upload_enabled',
            $this->request->post('media_video_upload_enabled')
                ? '1'
                : '0'
        );

        $videoMaxMb = filter_var(
            $this->request->post('media_video_max_mb', 50),
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if ($videoMaxMb === false) {
            $this->session->flash('errors', [
                'media_video_max_mb' => 'Il limite upload video deve essere almeno 1 MB.',
            ]);

            $this->response->redirect('/admin/settings');
            return;
        }

        $this->settings->set(
            'media_video_max_mb',
            (string) $videoMaxMb
        );

        $this->settings->set(
            'media_require_text_with_audio_video',
            $this->request->post('media_require_text_with_audio_video')
                ? '1'
                : '0'
        );

        $this->settings->set(
            'chanzine_user_submissions_enabled',
            $this->request->post('chanzine_user_submissions_enabled')
                ? '1'
                : '0'
        );

        $this->settings->set(
            'crypto_tips_enabled',
            $this->request->post('crypto_tips_enabled')
                ? '1'
                : '0'
        );

        $this->settings->set(
            'crypto_tips_profiles_enabled',
            $this->request->post('crypto_tips_profiles_enabled')
                ? '1'
                : '0'
        );

        $this->settings->set(
            'crypto_tips_pings_enabled',
            $this->request->post('crypto_tips_pings_enabled')
                ? '1'
                : '0'
        );

        $allowedLocales = [
            'it',
            'en',
        ];

        $defaultLocale = trim(
            (string) $this->request->post(
                'default_locale',
                'it'
            )
        );

        $availableLocales = $this->request->post(
            'available_locales',
            []
        );

        if (!is_array($availableLocales)) {
            $availableLocales = [];
        }

        $availableLocales = array_values(
            array_unique(
                array_filter(
                    $availableLocales,
                    static fn ($locale): bool =>
                        is_string($locale)
                        && in_array(
                            $locale,
                            $allowedLocales,
                            true
                        )
                )
            )
        );

        if ($availableLocales === []) {
            $this->session->flash('errors', [
                'available_locales' =>
                    'Seleziona almeno una lingua disponibile.',
            ]);

            $this->response->redirect('/admin/settings');
            return;
        }

        if (
            !in_array(
                $defaultLocale,
                $allowedLocales,
                true
            )
            || !in_array(
                $defaultLocale,
                $availableLocales,
                true
            )
        ) {
            $this->session->flash('errors', [
                'default_locale' =>
                    'La lingua predefinita deve essere tra le lingue disponibili.',
            ]);

            $this->response->redirect('/admin/settings');
            return;
        }

        $this->settings->set(
            'default_locale',
            $defaultLocale
        );

        $this->settings->set(
            'available_locales',
            implode(',', $availableLocales)
        );

        $githubApiToken = trim(
            (string) $this->request->post(
                'github_api_token',
                ''
            )
        );

        if ($githubApiToken !== '') {
            $this->settings->set(
                'github_api_token',
                $githubApiToken
            );
        }

        if (
            $siteUrl !== ''
            && filter_var($siteUrl, FILTER_VALIDATE_URL) === false
        ) {
            $this->session->flash('errors', [
                'site_url' => 'Inserisci un URL valido, completo di http:// o https://.',
            ]);

            $this->response->redirect('/admin/settings');
            return;
        }

        $this->settings->set('site_url', $siteUrl);
        $this->settings->set('meta_description', $metaDescription);

        if (
            isset($_FILES['site_logo'])
            && $_FILES['site_logo']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['site_logo'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->session->flash('errors', [
                    'site_logo' => 'Errore durante il caricamento del logo.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $extension = strtolower(
                pathinfo($file['name'], PATHINFO_EXTENSION)
            );

            $allowed = [
                'png',
                'jpg',
                'jpeg',
                'webp',
                'svg',
            ];

            if (!in_array($extension, $allowed, true)) {
                $this->session->flash('errors', [
                    'site_logo' => 'Formato logo non supportato.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $destinationDir = dirname(__DIR__, 2) . '/storage/brand';

            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            foreach (glob($destinationDir . '/logo.*') as $oldFile) {
                @unlink($oldFile);
            }

            $filename = 'logo.' . $extension;

            if (!move_uploaded_file(
                $file['tmp_name'],
                $destinationDir . '/' . $filename
            )) {
                throw new RuntimeException(
                    'Impossibile salvare il logo.'
                );
            }

            $this->settings->set('site_logo', $filename);
        }

        if (
            isset($_FILES['site_favicon'])
            && $_FILES['site_favicon']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['site_favicon'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->session->flash('errors', [
                    'site_favicon' => 'Errore durante il caricamento della favicon.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $extension = strtolower(
                pathinfo($file['name'], PATHINFO_EXTENSION)
            );

            $allowed = [
                'ico',
                'png',
                'svg',
            ];

            if (!in_array($extension, $allowed, true)) {
                $this->session->flash('errors', [
                    'site_favicon' => 'Formato favicon non supportato.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $destinationDir = dirname(__DIR__, 2) . '/storage/brand';

            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            foreach (glob($destinationDir . '/favicon.*') as $oldFile) {
                @unlink($oldFile);
            }

            $filename = 'favicon.' . $extension;

            if (!move_uploaded_file(
                $file['tmp_name'],
                $destinationDir . '/' . $filename
            )) {
                throw new RuntimeException(
                    'Impossibile salvare la favicon.'
                );
            }

            $this->settings->set('site_favicon', $filename);
        }

        if (
            isset($_FILES['site_apple_touch_icon'])
            && $_FILES['site_apple_touch_icon']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['site_apple_touch_icon'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->session->flash('errors', [
                    'site_apple_touch_icon' => 'Errore durante il caricamento della Apple Touch Icon.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $extension = strtolower(
                pathinfo($file['name'], PATHINFO_EXTENSION)
            );

            if ($extension !== 'png') {
                $this->session->flash('errors', [
                    'site_apple_touch_icon' => 'La Apple Touch Icon deve essere in formato PNG.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $destinationDir = dirname(__DIR__, 2) . '/storage/brand';

            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            foreach (glob($destinationDir . '/apple-touch-icon.*') as $oldFile) {
                @unlink($oldFile);
            }

            $filename = 'apple-touch-icon.png';

            if (!move_uploaded_file(
                $file['tmp_name'],
                $destinationDir . '/' . $filename
            )) {
                throw new RuntimeException(
                    'Impossibile salvare la Apple Touch Icon.'
                );
            }

            $this->settings->set('site_apple_touch_icon', $filename);
        }

        if (
            isset($_FILES['site_og_image'])
            && $_FILES['site_og_image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['site_og_image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->session->flash('errors', [
                    'site_og_image' => 'Errore durante il caricamento dell\'immagine OpenGraph.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $extension = strtolower(
                pathinfo($file['name'], PATHINFO_EXTENSION)
            );

            $allowed = [
                'jpg',
                'jpeg',
                'png',
                'webp',
            ];

            if (!in_array($extension, $allowed, true)) {
                $this->session->flash('errors', [
                    'site_og_image' => 'Formato OpenGraph non supportato.',
                ]);

                $this->response->redirect('/admin/settings');
                return;
            }

            $destinationDir = dirname(__DIR__, 2) . '/storage/brand';

            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            foreach (glob($destinationDir . '/opengraph.*') as $oldFile) {
                @unlink($oldFile);
            }

            $filename = 'opengraph.' . $extension;

            if (!move_uploaded_file(
                $file['tmp_name'],
                $destinationDir . '/' . $filename
            )) {
                throw new RuntimeException(
                    'Impossibile salvare l\'immagine OpenGraph.'
                );
            }

            $this->settings->set('site_og_image', $filename);
        }

        $this->session->flash('success', 'Impostazioni salvate.');

        $this->response->redirect('/admin/settings');
    }

    public function deleteBrandAsset(): void
    {
        if (!$this->auth->check()) {
            $this->response->redirect('/admin/login');
            return;
        }

        $asset = (string) $this->request->post('asset', '');

        $assets = [
            'logo' => [
                'setting' => 'site_logo',
                'pattern' => 'logo.*',
                'label' => 'Logo',
            ],
            'favicon' => [
                'setting' => 'site_favicon',
                'pattern' => 'favicon.*',
                'label' => 'Favicon',
            ],
            'apple-touch-icon' => [
                'setting' => 'site_apple_touch_icon',
                'pattern' => 'apple-touch-icon.*',
                'label' => 'Apple Touch Icon',
            ],
            'opengraph' => [
                'setting' => 'site_og_image',
                'pattern' => 'opengraph.*',
                'label' => 'Immagine OpenGraph',
            ],
        ];

        if (!isset($assets[$asset])) {
            $this->response->redirect('/admin/settings');
            return;
        }

        $destinationDir = dirname(__DIR__, 2) . '/storage/brand';

        foreach (glob($destinationDir . '/' . $assets[$asset]['pattern']) as $file) {
            @unlink($file);
        }

        $this->settings->set(
            $assets[$asset]['setting'],
            ''
        );

        $this->session->flash(
            'success',
            $assets[$asset]['label'] . ' eliminato con successo.'
        );

        $this->response->redirect('/admin/settings');
    }
}
