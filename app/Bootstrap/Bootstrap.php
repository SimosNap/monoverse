<?php
declare(strict_types=1);

namespace Monoverse\Bootstrap;

use Monoverse\Core\App;
use Monoverse\Core\BootstrapContext;
use Monoverse\Core\Container;
use Monoverse\Providers\CoreServiceProvider;

class Bootstrap
{
    public function boot(): App
    {
        $appConfigFile = __DIR__ . '/../../config/app.php';
        $databaseConfigFile = __DIR__ . '/../../config/database.php';
        $oauthConfigFile = __DIR__ . '/../../config/oauth.php';

        $config = is_file($appConfigFile)
            ? require $appConfigFile
            : [];

        $databaseConfig = is_file($databaseConfigFile)
            ? require $databaseConfigFile
            : [];

        $oauthConfig = is_file($oauthConfigFile)
            ? require $oauthConfigFile
            : [];

        $config['oauth'] = $oauthConfig;

        $context = new BootstrapContext(
            $config,
            $databaseConfig,
            $oauthConfig
        );

        $container = new Container();

        $coreProvider = new CoreServiceProvider(
            $container,
            $context
        );

        $coreProvider->register();

        return new App(
            $config,
            $databaseConfig
        );
    }
}