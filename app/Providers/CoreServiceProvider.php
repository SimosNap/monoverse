<?php
declare(strict_types=1);

namespace Monoverse\Providers;

use Monoverse\Core\Config;
use Monoverse\Core\Container;
use Monoverse\Core\Request;
use Monoverse\Core\BootstrapContext;
use Monoverse\Core\Database;
use Monoverse\Services\PostService;
use Monoverse\Services\CommentService;
use Monoverse\Services\VoteService;
use Monoverse\Services\NotificationService;

class CoreServiceProvider
{
    public function __construct(
        private Container $container,
        private BootstrapContext $context
    ) {
    }

    public function register(): void
    {
        $this->container->set(Config::class, function () {
            return new Config($this->context->config);
        });

        $this->container->set(Request::class, function () {
            return new Request();
        });

        $this->container->set(Logger::class, function () {
            return new Logger(__DIR__ . '/../../storage/logs/monoverse.log');
        });

        $this->container->set(Database::class, function () {
            return new Database($this->context->databaseConfig());
        });

        $this->container->set(PostService::class, function () {
            return new PostService(
                $this->container->get(Database::class)
            );
        });
        
        $this->container->set(CommentService::class, function () {
            return new CommentService(
                $this->container->get(Database::class),
                $this->container->get(NotificationService::class)
            );
        });
        
        $this->container->set(VoteService::class, function () {
            return new VoteService(
                $this->container->get(Database::class)
            );
        });
        
        $this->container->set(NotificationService::class, function () {
            return new NotificationService(
                $this->container->get(Database::class)
            );
        });
    }
}
