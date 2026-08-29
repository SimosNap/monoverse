<?php
declare(strict_types=1);

use Monoverse\Core\BootstrapContext;
use Monoverse\Core\Config;
use Monoverse\Core\Container;
use Monoverse\Core\Database;
use Monoverse\Core\Response;
use Monoverse\Core\View;
use Monoverse\Providers\CoreServiceProvider;

$context = new BootstrapContext($config, $databaseConfig);
$container = new Container();

$container->set(Database::class, function () use ($databaseConfig) {
    return new Database($databaseConfig);
});

$container->set(Response::class, function () {
    return new Response();
});

$container->set(View::class, function (Container $container) {
    return new View(
        $container->get(Config::class)
    );
});

$coreProvider = new CoreServiceProvider($container, $context);
$coreProvider->register();

return $container;