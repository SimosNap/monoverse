<?php
declare(strict_types=1);

namespace Monoverse\Core;

use Monoverse\Services\Translator;

class View
{
    private Config $config;

    private Translator $translator;

    private array $sharedData = [];

    public function __construct(
        Config $config,
        Translator $translator
    ) {
        $this->config = $config;
        $this->translator = $translator;
    }

    public function share(
        string $key,
        mixed $value
    ): void {
        $this->sharedData[$key] = $value;
    }

    public function render(
        string $view,
        array $data = [],
        string $layout = 'layout'
    ): string {
        $themePath = $this->themePath();

        $pageFile = $themePath . '/pages/' . $view . '.php';
        $legacyFile = $themePath . '/' . $view . '.php';
        $layoutFile = $themePath . '/' . $layout . '.php';

        $viewFile = is_file($pageFile)
            ? $pageFile
            : $legacyFile;

        $cssFiles = ['base'];
        $cssFiles[] = 'blocks';

        if (
            $layout === 'admin-layout'
            || $view === 'admin-login'
        ) {
            $cssFiles[] = 'admin';

            if (
                $layout === 'admin-layout'
                && $view === 'article-form'
            ) {
                $cssFiles[] = 'vendor/easymde/easymde.min';
            }
        }

        switch ($view) {

            case 'installer':
            case 'installer-edition':
            case 'installer-database':
            case 'installer-oauth':
            case 'installer-admin':
            case 'installer-summary':
                $cssFiles[] = 'installer';
                break;

            case 'account':
            case 'account-profile':
            case 'account-blocked':
            case 'account-suspended':
            case 'account-moderation':
            case 'account-moderation-reports':
            case 'account-moderation-report':
            case 'account-moderation-bans':
            case 'account-moderation-mutes':
            case 'account-articles':
                $cssFiles[] = 'account';
                break;

            case 'account-article-edit':
                $cssFiles[] = 'account';
                $cssFiles[] = 'chanzine';
                break;

            case 'account-saved':
                $cssFiles[] = 'account';
                $cssFiles[] = 'ping';
                $cssFiles[] = 'autocomplete';
                break;

            case 'profile':
                $cssFiles[] = 'profile';
                $cssFiles[] = 'ping';
                break;

            case 'ping':
            case 'ping-show':
                $cssFiles[] = 'ping';
                $cssFiles[] = 'autocomplete';
                break;

            case 'chanzine':
            case 'chanzine-article':
            case 'chanzine-submit':
                $cssFiles[] = 'chanzine';
                break;

            case 'page':
                $cssFiles[] = 'page';
                break;

            case 'members':
                $cssFiles[] = 'members';
                break;

            case 'notifications':
                $cssFiles[] = 'notifications';
                break;

            case 'landing-chat':
                $cssFiles[] = 'landing-chat';
                break;

            case 'register':
                $cssFiles[] = 'register';
                break;
        }

        $jsFiles = [];
        $jsFiles[] = 'site';

        if (
            $layout === 'admin-layout'
            && $view === 'article-form'
        ) {
            $jsFiles[] = 'vendor/easymde/easymde.min';
            $jsFiles[] = 'article-editor';
        }

        switch ($view) {

            case 'profile':
                $jsFiles[] = 'mydogemask';
                $jsFiles[] = 'doge-tip';
                $jsFiles[] = 'profile';
                break;

            case 'ping':
            case 'ping-show':
                $jsFiles[] = 'ping';
                $jsFiles[] = 'ping-attachments';
                $jsFiles[] = 'autocomplete';
                break;

            case 'members':
                $jsFiles[] = 'members';
                break;

            case 'account':
                $jsFiles[] = 'mydogemask';
                $jsFiles[] = 'account';
                break;

            case 'account-saved':
                $jsFiles[] = 'ping';
                $jsFiles[] = 'ping-attachments';
                $jsFiles[] = 'autocomplete';
                break;

            case 'widgets-area':
                $jsFiles[] = 'widgets-area';
                break;
        }

        $data = array_merge(
            $this->sharedData,
            $data
        );

        $data['cssFiles'] = array_values(
            array_unique($cssFiles)
        );

        $data['jsFiles'] = array_values(
            array_unique($jsFiles)
        );

        extract($data, EXTR_SKIP);

        $component = function (
            string $name,
            array $data = []
        ): string {
            return $this->component(
                $name,
                $data
            );
        };

        $t = function (
            string $key,
            array $replace = []
        ): string {
            return $this->translator->translate(
                $key,
                $replace
            );
        };

        $currentLocale = $this->translator->getLocale();

        ob_start();

        if (is_file($viewFile)) {
            require $viewFile;
        } else {
            echo '<p>View non trovata.</p>';
        }

        $body = (string) ob_get_clean();

        ob_start();

        if (is_file($layoutFile)) {
            require $layoutFile;
        } else {
            echo $body;
        }

        return (string) ob_get_clean();
    }

    public function component(
        string $name,
        array $data = []
    ): string {
        $componentFile = $this->themePath()
            . '/components/'
            . $name
            . '.php';

        extract($data, EXTR_SKIP);

        $component = function (
            string $name,
            array $data = []
        ): string {
            return $this->component(
                $name,
                $data
            );
        };

        $t = function (
            string $key,
            array $replace = []
        ): string {
            return $this->translator->translate(
                $key,
                $replace
            );
        };

        ob_start();

        if (is_file($componentFile)) {
            require $componentFile;
        } else {
            echo '<p>Componente non trovato.</p>';
        }

        return (string) ob_get_clean();
    }

    public function partial(
        string $view,
        array $data = []
    ): string {
        $viewFile = $this->themePath()
            . '/blocks/'
            . $view
            . '.php';

        extract($data, EXTR_SKIP);

        $t = function (
            string $key,
            array $replace = []
        ): string {
            return $this->translator->translate(
                $key,
                $replace
            );
        };

        ob_start();

        if (is_file($viewFile)) {
            require $viewFile;
        } else {
            echo '<p>Block view not found.</p>';
        }

        return (string) ob_get_clean();
    }

    private function themePath(): string
    {
        $theme = (string) $this->config->get(
            'theme',
            'default'
        );

        return __DIR__
            . '/../../themes/'
            . $theme;
    }
}
