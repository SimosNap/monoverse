<?php
declare(strict_types=1);

namespace Monoverse\Core;

class App
{
    private Config $config;
    private array $databaseConfig;
    private Router $router;
    private Database $database;
    private EventDispatcher $events;
    private Container $container;
    private Request $request;
    private Response $response;
    private View $view;
    private Session $session;
    private \Monoverse\Installer\Installer $installer;

    public function __construct(array $config, array $databaseConfig)
    {
        $this->config = new Config($config);

        $this->container = new Container();
        $this->container->instance(Config::class, $this->config);

        $this->databaseConfig = $databaseConfig;

        $this->database = new Database($databaseConfig);
        $this->container->instance(Database::class, $this->database);

        $this->events = new EventDispatcher();
        $this->container->instance(EventDispatcher::class, $this->events);

        $this->request = new Request();
        $this->container->instance(Request::class, $this->request);

        $this->response = new Response();
        $this->container->instance(Response::class, $this->response);

        $this->session = new Session();
        $this->session->start();
        $this->container->instance(Session::class, $this->session);

        $this->container->set(
            \Monoverse\Services\SettingsService::class,
            function (Container $container) {
                return new \Monoverse\Services\SettingsService(
                    $container->get(Database::class)
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\ContentTranslationService::class,
            function (Container $container) {
                return new \Monoverse\Services\ContentTranslationService(
                    $container->get(Database::class)
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\LocaleService::class,
            function (Container $container) {
                $settings = null;

                if (
                    file_exists(
                        dirname(__DIR__, 2)
                        . '/storage/installed.lock'
                    )
                ) {
                    $settings = $container->get(
                        \Monoverse\Services\SettingsService::class
                    );
                }

                return new \Monoverse\Services\LocaleService(
                    $settings,
                    $container->get(Session::class)
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\Translator::class,
            function (Container $container) {
                return new \Monoverse\Services\Translator(
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    ),
                    dirname(__DIR__, 2) . '/resources/lang'
                );
            }
        );

        $this->view = new View(
            $this->config,
            $this->container->get(
                \Monoverse\Services\Translator::class
            )
        );
        $this->container->instance(View::class, $this->view);

        $this->container->set(
            \Monoverse\Core\Blocks\BlockRegistry::class,
            static fn () => new \Monoverse\Core\Blocks\BlockRegistry()
        );

        $this->container->set(
            \Monoverse\Core\Blocks\AreaRegistry::class,
            static fn () => new \Monoverse\Core\Blocks\AreaRegistry()
        );

        $this->container->set(
            \Monoverse\Core\Blocks\AreaProvider::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\AreaProvider(
                    $container->get(
                        \Monoverse\Core\Blocks\AreaRegistry::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\BlockProvider::class,
            static fn () => new \Monoverse\Core\Blocks\BlockProvider()
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Content\HtmlBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Content\HtmlBlock();
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Content\LatestArticlesBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Content\LatestArticlesBlock(
                    $container->get(
                        \Monoverse\Services\ArticleService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Content\SubmitArticleBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Content\SubmitArticleBlock(
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Content\CategoriesBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Content\CategoriesBlock(
                    $container->get(
                        \Monoverse\Services\CategoryService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Content\PagesNavigationBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Content\PagesNavigationBlock(
                    $container->get(
                        \Monoverse\Services\PageService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\AzuraCastService::class,
            static function (): \Monoverse\Services\AzuraCastService {
                return new \Monoverse\Services\AzuraCastService();
            }
        );

        $this->container->set(
            \Monoverse\Services\IcecastService::class,
            static function (): \Monoverse\Services\IcecastService {
                return new \Monoverse\Services\IcecastService();
            }
        );

        $this->container->set(
            \Monoverse\Services\GitHubService::class,
            function (Container $container): \Monoverse\Services\GitHubService {
                return new \Monoverse\Services\GitHubService(
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Webradio\AzuraCastBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Webradio\AzuraCastBlock(
                    $container->get(
                        \Monoverse\Services\AzuraCastService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Webradio\IcecastBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Webradio\IcecastBlock(
                    $container->get(
                        \Monoverse\Services\IcecastService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Webradio\IcecastStatsBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Webradio\IcecastStatsBlock(
                    $container->get(
                        \Monoverse\Services\IcecastService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Webradio\AzuraCastMiniPlayerBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Webradio\AzuraCastMiniPlayerBlock(
                    $container->get(
                        \Monoverse\Services\AzuraCastService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

       $this->container->set(
           \Monoverse\Core\Blocks\Webradio\AzuraCastRequestsBlock::class,
           function (Container $container) {
               return new \Monoverse\Core\Blocks\Webradio\AzuraCastRequestsBlock(
                   $container->get(
                       \Monoverse\Services\AzuraCastService::class
                   ),
                   $container->get(
                       \Monoverse\Services\Translator::class
                   )
               );
           }
       );

        $this->container->set(
            \Monoverse\Core\Blocks\Webradio\AzuraCastStatsBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Webradio\AzuraCastStatsBlock(
                    $container->get(
                        \Monoverse\Services\AzuraCastService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Community\UsersInChatBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Community\UsersInChatBlock(
                    $container->get(
                        \Monoverse\Services\CommunityService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Community\MostActiveUsersBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Community\MostActiveUsersBlock(
                    $container->get(
                        \Monoverse\Services\CommunityService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Community\LatestMembersBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Community\LatestMembersBlock(
                    $container->get(
                        \Monoverse\Services\CommunityService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Developer\GitHubRepositoryBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Developer\GitHubRepositoryBlock(
                    $container->get(
                        \Monoverse\Services\GitHubService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Developer\GitHubReleaseBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Developer\GitHubReleaseBlock(
                    $container->get(
                        \Monoverse\Services\GitHubService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\Developer\GitHubPullRequestsBlock::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\Developer\GitHubPullRequestsBlock(
                    $container->get(
                        \Monoverse\Services\GitHubService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\BlockRenderer::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\BlockRenderer(
                    $container->get(
                        \Monoverse\Core\Blocks\BlockRegistry::class
                    ),
                    $container->get(View::class)
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\FormRenderer::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\FormRenderer(
                    $container->get(
                        View::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Core\Blocks\BlockManager::class,
            function (Container $container) {
                return new \Monoverse\Core\Blocks\BlockManager(
                    $container->get(
                        \Monoverse\Repositories\BlockRepository::class
                    ),
                    $container->get(
                        \Monoverse\Core\Blocks\BlockRenderer::class
                    ),
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ContentTranslationService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\BlockController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\BlockController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Services\NotificationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\AdminAuthService::class
                    ),
                    $container->get(
                        \Monoverse\Services\NavigationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\PageService::class
                    ),
                    $container->get(
                        \Monoverse\Core\Blocks\BlockRegistry::class
                    ),
                    $container->get(
                        \Monoverse\Core\Blocks\AreaRegistry::class
                    ),
                    $container->get(
                        \Monoverse\Repositories\BlockRepository::class
                    ),
                    $container->get(
                        \Monoverse\Core\Blocks\FormRenderer::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    ),
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ContentTranslationService::class
                    )
                );
            }
        );

        $this->installer = new \Monoverse\Installer\Installer();
        $this->container->instance(\Monoverse\Installer\Installer::class, $this->installer);

        $this->container->set(
            \Monoverse\Controllers\InstallController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\InstallController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(
                        \Monoverse\Installer\Installer::class
                    ),
                    $container->get(
                        \Monoverse\Services\EditionService::class
                    ),
                    $container->get(Request::class),
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Services\InstallerService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(\Monoverse\Controllers\HomeController::class, function (Container $container) {
            return new \Monoverse\Controllers\HomeController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(\Monoverse\Services\EditionService::class, function () {
            return new \Monoverse\Services\EditionService();
        });

        $this->container->set(
            \Monoverse\Services\InstallerService::class,
            function (Container $container) {
                return new \Monoverse\Services\InstallerService(
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Validation\Validator::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(\Monoverse\Validation\Validator::class, function () {
            return new \Monoverse\Validation\Validator();
        });

        $this->container->set(\Monoverse\Controllers\DashboardController::class, function (Container $container) {
            return new \Monoverse\Controllers\DashboardController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(\Monoverse\Services\AdminAuthService::class),
                $container->get(\Monoverse\Services\NavigationService::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(\Monoverse\Installer\InstallationRunner::class, function () {
            return new \Monoverse\Installer\InstallationRunner();
        });

        $this->container->set(\Monoverse\Services\OAuthService::class, function (Container $container) {
            return new \Monoverse\Services\OAuthService(
                $container->get(Config::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\ModeratorService::class)
            );
        });

        $this->container->set(\Monoverse\Services\AdminAuthService::class, function (Container $container) {
            return new \Monoverse\Services\AdminAuthService(
                $container->get(Database::class),
                $container->get(Session::class)
            );
        });

        $this->container->set(\Monoverse\Services\ProfileService::class, function (Container $container) {
            return new \Monoverse\Services\ProfileService(
                $container->get(Database::class)
            );
        });

        $this->container->set(\Monoverse\Services\FollowService::class, function (Container $container) {
            return new \Monoverse\Services\FollowService(
                $container->get(Database::class),
                $container->get(\Monoverse\Services\BlockService::class)
            );
        });

        $this->container->set(\Monoverse\Services\SavedItemService::class, function (Container $container) {
            return new \Monoverse\Services\SavedItemService(
                $container->get(Database::class)
            );
        });

        $this->container->set(\Monoverse\Services\BlockService::class, function (Container $container) {
            return new \Monoverse\Services\BlockService(
                $container->get(Database::class)
            );
        });

        $this->container->set(\Monoverse\Services\PostService::class, function (Container $container) {
            return new \Monoverse\Services\PostService(
                $container->get(Database::class),
                $container->get(\Monoverse\Services\MentionService::class),
                $container->get(\Monoverse\Services\LinkService::class),
                $container->get(\Monoverse\Services\MediaService::class),
                $container->get(\Monoverse\Services\BlockService::class),
                $container->get(\Monoverse\Services\DogeTipService::class)
            );
        });

        $this->container->set(\Monoverse\Services\CommentService::class, function (Container $container) {
            return new \Monoverse\Services\CommentService(
                $container->get(Database::class),
                $container->get(\Monoverse\Services\NotificationService::class),
                $container->get(\Monoverse\Services\MentionService::class),
                $container->get(\Monoverse\Services\BlockService::class)
            );
        });

        $this->container->set(\Monoverse\Services\ReportService::class, function (Container $container) {
            return new \Monoverse\Services\ReportService(
                $container->get(Database::class)
            );
        });

        $this->container->set(\Monoverse\Services\VoteService::class, function (Container $container) {
            return new \Monoverse\Services\VoteService(
                $container->get(Database::class),
                $container->get(\Monoverse\Services\PostService::class),
                $container->get(\Monoverse\Services\NotificationService::class)
            );
        });

        $this->container->set(\Monoverse\Services\MentionService::class, function (Container $container) {
            return new \Monoverse\Services\MentionService(
                $container->get(\Monoverse\Services\ProfileService::class),
                $container->get(\Monoverse\Services\NotificationService::class)
            );
        });

        $this->container->set(\Monoverse\Services\NotificationService::class, function (Container $container) {
            return new \Monoverse\Services\NotificationService(
                $container->get(Database::class)
            );
        });

        $this->container->set(
            \Monoverse\Services\LinkService::class,
            function (Container $container) {
                return new \Monoverse\Services\LinkService(
                    $container->get(Database::class),
                    $container->get(
                        \Monoverse\Services\GitHubService::class
                    )
                );
            }
        );

        $this->container->set(\Monoverse\Services\MediaService::class, function (Container $container) {
            return new \Monoverse\Services\MediaService(
                $container->get(Database::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(
            \Monoverse\Services\SimosNapService::class,
            function (Container $container) {
                return new \Monoverse\Services\SimosNapService(
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\DogeTipService::class,
            function (Container $container) {
                return new \Monoverse\Services\DogeTipService(
                    $container->get(
                        \Monoverse\Services\SimosNapService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\CommunityService::class,
            function (Container $container) {
                return new \Monoverse\Services\CommunityService(
                    $container->get(
                        \Monoverse\Services\SimosNapService::class
                    ),
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ProfileService::class
                    )
                );
            }
        );

        $this->container->set(\Monoverse\Controllers\AdminAuthController::class, function (Container $container) {
            return new \Monoverse\Controllers\AdminAuthController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Request::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\AdminAuthService::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(\Monoverse\Controllers\OAuthController::class, function (Container $container) {
            return new \Monoverse\Controllers\OAuthController(
                $container->get(Response::class),
                $container->get(\Monoverse\Services\OAuthService::class),
                $container->get(\Monoverse\Services\ProfileService::class),
                $container->get(\Monoverse\Services\AuthorizationService::class)
            );
        });

        $this->container->set(\Monoverse\Services\ArticleService::class, function (Container $container) {
            return new \Monoverse\Services\ArticleService(
                $container->get(Database::class)
            );
        });

        $this->container->set(
            \Monoverse\Services\CategoryService::class,
            function (Container $container) {
                return new \Monoverse\Services\CategoryService(
                    $container->get(Database::class),
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ContentTranslationService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\PageService::class,
            function (Container $container) {
                return new \Monoverse\Services\PageService(
                    $container->get(Database::class),
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ContentTranslationService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\ExternalAccountService::class,
            function (Container $container) {
                return new \Monoverse\Services\ExternalAccountService(
                    $container->get(Config::class),
                    $container->get(
                        \Monoverse\Services\ProfileService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Repositories\BlockRepository::class,
            function (Container $container) {
                return new \Monoverse\Repositories\BlockRepository(
                    $container->get(Database::class)
                );
            }
        );

        $this->container
            ->get(\Monoverse\Core\Blocks\BlockProvider::class)
            ->register(
                $this->container->get(
                    \Monoverse\Core\Blocks\BlockRegistry::class
                ),
                $this->container
            );

        $themeAreas = require dirname(__DIR__, 2) . '/themes/default/areas.php';

        $this->container
            ->get(\Monoverse\Core\Blocks\AreaProvider::class)
            ->register($themeAreas);

        $this->container->set(
            \Monoverse\Services\MarkdownService::class,
            function () {
                return new \Monoverse\Services\MarkdownService();
            }
        );

        $this->container->set(\Monoverse\Controllers\WebchatController::class, function (Container $container) {
            return new \Monoverse\Controllers\WebchatController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Request::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\AdminAuthService::class),
                $container->get(\Monoverse\Services\NavigationService::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(\Monoverse\Controllers\AccountController::class, function (Container $container) {
            return new \Monoverse\Controllers\AccountController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Session::class),
                $container->get(Request::class),
                $container->get(\Monoverse\Services\NotificationService::class),
                $container->get(\Monoverse\Services\ProfileService::class),
                $container->get(\Monoverse\Services\AuthorizationService::class),
                $container->get(\Monoverse\Services\BlockService::class),
                $container->get(\Monoverse\Services\FollowService::class),
                $container->get(\Monoverse\Services\SimosNapService::class),
                $container->get(\Monoverse\Services\SavedItemService::class),
                $container->get(\Monoverse\Services\PostService::class),
                $container->get(\Monoverse\Services\ArticleService::class),
                $container->get(\Monoverse\Services\CategoryService::class),
                $container->get(\Monoverse\Services\UserModerationService::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(
            \Monoverse\Controllers\ApiController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\ApiController(
                    $container->get(Response::class),
                    $container->get(
                        \Monoverse\Services\ProfileService::class
                    ),
                    $container->get(
                        \Monoverse\Services\AzuraCastService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Services\NavigationService::class,
            function (Container $container) {
                return new \Monoverse\Services\NavigationService(
                    $container->get(
                        \Monoverse\Services\Translator::class
                    )
                );
            }
        );

        $this->container->set(\Monoverse\Controllers\SettingsController::class, function (Container $container) {
            return new \Monoverse\Controllers\SettingsController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Request::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\AdminAuthService::class),
                $container->get(\Monoverse\Services\SettingsService::class),
                $container->get(\Monoverse\Services\NavigationService::class)
            );
        });

        $this->container->set(
            \Monoverse\Controllers\LocaleController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\LocaleController(
                    $container->get(Request::class),
                    $container->get(Response::class),
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\ProfileController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\ProfileController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Services\NotificationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ProfileService::class
                    ),
                    $container->get(
                        \Monoverse\Services\PostService::class
                    ),
                    $container->get(
                        \Monoverse\Services\SimosNapService::class
                    ),
                    $container->get(
                        \Monoverse\Services\FollowService::class
                    ),
                    $container->get(
                        \Monoverse\Services\BlockService::class
                    ),
                    $container->get(
                        \Monoverse\Services\UserModerationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ModeratorService::class
                    ),
                    $container->get(
                        \Monoverse\Core\Blocks\BlockManager::class
                    ),
                    $container->get(
                        Config::class
                    ),
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\PingController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\PingController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Services\NotificationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\PostService::class
                    ),
                    $container->get(
                        \Monoverse\Services\CommentService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ProfileService::class
                    ),
                    $container->get(
                        \Monoverse\Services\VoteService::class
                    ),
                    $container->get(
                        \Monoverse\Services\SavedItemService::class
                    ),
                    $container->get(
                        \Monoverse\Services\SimosNapService::class
                    ),
                    $container->get(
                        \Monoverse\Services\AuthorizationService::class
                    ),
                    $container->get(
                        \Monoverse\Core\Blocks\BlockManager::class
                    ),
                    $container->get(
                        Config::class
                    ),
                    $container->get(
                        \Monoverse\Services\Translator::class
                    ),
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    )
                );
            }
        );

        $this->container->set(\Monoverse\Controllers\ReportController::class, function (Container $container) {
            return new \Monoverse\Controllers\ReportController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\NotificationService::class),
                $container->get(\Monoverse\Services\ReportService::class),
                $container->get(\Monoverse\Services\ModeratorService::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(\Monoverse\Controllers\NotificationController::class, function (Container $container) {
            return new \Monoverse\Controllers\NotificationController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\NotificationService::class),
                $container->get(\Monoverse\Services\ProfileService::class)
            );
        });

        $this->container->set(
            \Monoverse\Controllers\ArticleController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\ArticleController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(Request::class),
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Services\AdminAuthService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ArticleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\CategoryService::class
                    ),
                    $container->get(
                        \Monoverse\Services\NavigationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\PostService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\CategoryController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\CategoryController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(Request::class),
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Services\AdminAuthService::class
                    ),
                    $container->get(
                        \Monoverse\Services\CategoryService::class
                    ),
                    $container->get(
                        \Monoverse\Services\NavigationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ContentTranslationService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\PageAdminController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\PageAdminController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(Session::class),
                    $container->get(
                        \Monoverse\Services\NotificationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    ),
                    $container->get(
                        \Monoverse\Services\AdminAuthService::class
                    ),
                    $container->get(
                        \Monoverse\Services\PageService::class
                    ),
                    $container->get(
                        \Monoverse\Services\NavigationService::class
                    ),
                    $container->get(
                        \Monoverse\Repositories\BlockRepository::class
                    ),
                    $container->get(
                        \Monoverse\Services\LocaleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ContentTranslationService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\ChanzineController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\ChanzineController(
                    $container->get(View::class),
                    $container->get(Response::class),
                    $container->get(Session::class),
                    $container->get(Request::class),
                    $container->get(
                        \Monoverse\Services\NotificationService::class
                    ),
                    $container->get(
                        \Monoverse\Services\ArticleService::class
                    ),
                    $container->get(
                        \Monoverse\Services\CategoryService::class
                    ),
                    $container->get(
                        \Monoverse\Services\MarkdownService::class
                    ),
                    $container->get(
                        \Monoverse\Services\SavedItemService::class
                    ),
                    $container->get(
                        \Monoverse\Core\Blocks\BlockManager::class
                    ),
                    $container->get(
                        \Monoverse\Services\SettingsService::class
                    )
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\SitemapController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\SitemapController(
                    $container->get(Response::class),
                    $container->get(\Monoverse\Services\SettingsService::class),
                    $container->get(\Monoverse\Services\ArticleService::class),
                    $container->get(\Monoverse\Services\PostService::class),
                    $container->get(\Monoverse\Services\ProfileService::class)
                );
            }
        );

        $this->container->set(
            \Monoverse\Controllers\RobotsController::class,
            function (Container $container) {
                return new \Monoverse\Controllers\RobotsController(
                    $container->get(Response::class),
                    $container->get(\Monoverse\Services\SettingsService::class)
                );
            }
        );

        $this->container->set(\Monoverse\Controllers\MediaController::class, function (Container $container) {
            return new \Monoverse\Controllers\MediaController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\NotificationService::class)
            );
        });

        $this->container->set(\Monoverse\Controllers\MembersController::class, function (Container $container) {
            return new \Monoverse\Controllers\MembersController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\NotificationService::class),
                $container->get(\Monoverse\Services\ProfileService::class),
                $container->get(
                    \Monoverse\Core\Blocks\BlockManager::class
                ),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(\Monoverse\Controllers\ModeratorsController::class, function (Container $container) {
            return new \Monoverse\Controllers\ModeratorsController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(\Monoverse\Services\AdminAuthService::class),
                $container->get(\Monoverse\Services\NavigationService::class),
                $container->get(\Monoverse\Services\ModeratorService::class),
                $container->get(\Monoverse\Services\ProfileService::class),
                $container->get(\Monoverse\Services\SettingsService::class)
            );
        });

        $this->container->set(\Monoverse\Controllers\ModerationController::class, function (Container $container) {
            return new \Monoverse\Controllers\ModerationController(
                $container->get(View::class),
                $container->get(Response::class),
                $container->get(Session::class),
                $container->get(\Monoverse\Services\NotificationService::class),
                $container->get(\Monoverse\Services\SettingsService::class),
                $container->get(\Monoverse\Services\ProfileService::class),
                $container->get(\Monoverse\Services\UserModerationService::class),
                $container->get(\Monoverse\Services\ReportService::class),
                $container->get(\Monoverse\Services\PostService::class),
                $container->get(\Monoverse\Services\CommentService::class),
                $container->get(\Monoverse\Services\AuthorizationService::class),
                $container->get(\Monoverse\Services\AdminAuthService::class)
            );
        });

        $this->container->set(\Monoverse\Services\ModeratorService::class, function (Container $container) {
            return new \Monoverse\Services\ModeratorService(
                $container->get(Database::class)
            );
        });

        $this->container->set(\Monoverse\Services\UserModerationService::class, function (Container $container) {
            return new \Monoverse\Services\UserModerationService(
                $container->get(\Monoverse\Core\Database::class)
            );
        });

        $this->container->set(\Monoverse\Services\AuthorizationService::class, function (Container $container) {
            return new \Monoverse\Services\AuthorizationService(
                $container->get(\Monoverse\Services\UserModerationService::class)
            );
        });

       $this->container->set(
           \Monoverse\Controllers\LandingChatController::class,
           function (Container $container) {
               return new \Monoverse\Controllers\LandingChatController(
                   $container->get(View::class),
                   $container->get(Response::class),
                   $container->get(Session::class),
                   $container->get(\Monoverse\Services\NotificationService::class),
                   $container->get(\Monoverse\Services\ProfileService::class),
                   $container->get(\Monoverse\Services\SimosNapService::class),
                   $container->get(
                       \Monoverse\Core\Blocks\BlockManager::class
                   ),
                   $container->get(\Monoverse\Services\SettingsService::class)
               );
           }
       );

       $this->container->set(
           \Monoverse\Controllers\RegisterController::class,
           function (Container $container) {
               return new \Monoverse\Controllers\RegisterController(
                   $container->get(View::class),
                   $container->get(Response::class),
                   $container->get(Session::class),
                   $container->get(
                       \Monoverse\Services\NotificationService::class
                   ),
                   $container->get(
                       \Monoverse\Services\SettingsService::class
                   )
               );
           }
       );

       $this->container->set(
           \Monoverse\Controllers\PageController::class,
           function (Container $container) {
               return new \Monoverse\Controllers\PageController(
                   $container->get(View::class),
                   $container->get(Response::class),
                   $container->get(Session::class),
                   $container->get(
                       \Monoverse\Services\NotificationService::class
                   ),
                   $container->get(
                       \Monoverse\Services\SettingsService::class
                   ),
                   $container->get(
                       \Monoverse\Services\PageService::class
                   ),
                   $container->get(
                       \Monoverse\Core\Blocks\BlockManager::class
                   )
               );
           }
       );

        $this->router = new Router(
            $this->request,
            $this->response
        );
    }

    public function run(): void
    {
        if ($this->installer->shouldRun($this->request)) {
            $this->registerInstallerRoutes();
            $this->router->dispatch();
            return;
        }

        $settingsService = $this->container->get(
            \Monoverse\Services\SettingsService::class
        );

        $pageService = $this->container->get(
            \Monoverse\Services\PageService::class
        );

        $profileService = $this->container->get(
            \Monoverse\Services\ProfileService::class
        );

        $currentProfile = null;

        $user = $this->session->get('auth.user');

        if (
            is_array($user)
            && !empty($user['sub'])
        ) {
            $currentProfile = $profileService->findBySub(
                (string) $user['sub']
            );
        }

        $this->view->share(
            'currentProfile',
            $currentProfile
        );

        $this->view->share(
            'pagesNavigationMain',
            $settingsService->get(
                'pages_navigation_main',
                '1'
            ) === '1'
        );

        $this->view->share(
            'navigationPages',
            $pageService->navigationItems()
        );

        $localeService = $this->container->get(
            \Monoverse\Services\LocaleService::class
        );

        $this->view->share(
            'currentLocale',
            $localeService->getCurrentLocale()
        );

        $this->view->share(
            'availableLocales',
            $localeService->getAvailableLocales()
        );

        $this->view->share(
            'isMultilingual',
            $localeService->isMultilingual()
        );

        $this->container
            ->get(\Monoverse\Services\ExternalAccountService::class)
            ->checkNextDueProfile();

        $this->registerWebRoutes();
        $this->router->dispatch();
    }

    private function registerInstallerRoutes(): void
    {
        $controller = $this->container->get(
            \Monoverse\Controllers\InstallController::class
        );

        $localeController = $this->container->get(
            \Monoverse\Controllers\LocaleController::class
        );

        $this->router->post(
            '/locale',
            [$localeController, 'update']
        );

        $this->router->get('/', [$controller, 'requirements']);
        $this->router->get('/install', [$controller, 'requirements']);
        $this->router->get('/install/edition', [$controller, 'edition']);
        $this->router->post('/install/edition', [$controller, 'saveEdition']);
        $this->router->get('/install/database', [$controller, 'database']);
        $this->router->post('/install/database', [$controller, 'saveDatabase']);
        $this->router->get('/install/oauth', [$controller, 'oauth']);
        $this->router->post('/install/oauth', [$controller, 'saveOAuth']);
        $this->router->get('/install/admin', [$controller, 'admin']);
        $this->router->post('/install/admin', [$controller, 'saveAdmin']);
        $this->router->get('/install/summary', [$controller, 'summary']);
        $this->router->post('/install/run', [$controller, 'runInstall']);
    }

    private function registerWebRoutes(): void
    {
        $homeController = $this->container->get(\Monoverse\Controllers\HomeController::class);
        $dashboardController = $this->container->get(\Monoverse\Controllers\DashboardController::class);
        $adminAuthController = $this->container->get(\Monoverse\Controllers\AdminAuthController::class);
        $settingsController = $this->container->get(\Monoverse\Controllers\SettingsController::class);
        $localeController = $this->container->get(
            \Monoverse\Controllers\LocaleController::class
        );
        $articleController = $this->container->get(
            \Monoverse\Controllers\ArticleController::class
        );
        $categoryController = $this->container->get(
            \Monoverse\Controllers\CategoryController::class
        );
        $pageAdminController = $this->container->get(
            \Monoverse\Controllers\PageAdminController::class
        );
        $chanzineController = $this->container->get(
            \Monoverse\Controllers\ChanzineController::class
        );
        $sitemapController = $this->container->get(
            \Monoverse\Controllers\SitemapController::class
        );
        $robotsController = $this->container->get(
            \Monoverse\Controllers\RobotsController::class
        );
        $mediaController = $this->container->get(
            \Monoverse\Controllers\MediaController::class
        );
        $oauthController = $this->container->get(\Monoverse\Controllers\OAuthController::class);
        $webchatController = $this->container->get(\Monoverse\Controllers\WebchatController::class);
        $blockController = $this->container->get(
            \Monoverse\Controllers\BlockController::class
        );
        $landingChatController = $this->container->get(\Monoverse\Controllers\LandingChatController::class);
        $registerController = $this->container->get(
            \Monoverse\Controllers\RegisterController::class
        );
        $pageController = $this->container->get(
            \Monoverse\Controllers\PageController::class
        );
        $accountController = $this->container->get(\Monoverse\Controllers\AccountController::class);
        $apiController = $this->container->get(\Monoverse\Controllers\ApiController::class);
        $profileController = $this->container->get(
            \Monoverse\Controllers\ProfileController::class
        );
        $membersController = $this->container->get(
            \Monoverse\Controllers\MembersController::class
        );
        $moderatorsController = $this->container->get(
            \Monoverse\Controllers\ModeratorsController::class
        );
        $moderationController = $this->container->get(
            \Monoverse\Controllers\ModerationController::class
        );
        $pingController = $this->container->get(
            \Monoverse\Controllers\PingController::class
        );
        $reportController = $this->container->get(
            \Monoverse\Controllers\ReportController::class
        );

        $notificationController = $this->container->get(
            \Monoverse\Controllers\NotificationController::class
        );

        $this->router->post(
            '/locale',
            [$localeController, 'update']
        );

        $this->router->get(
            '/robots.txt',
            [$robotsController, 'index']
        );

        $this->router->get(
            '/sitemap.xml',
            [$sitemapController, 'index']
        );

        $this->router->get('/', [$landingChatController, 'index']);
        $this->router->get('/chat', [$landingChatController, 'index']);
        $this->router->get(
            '/register',
            [$registerController, 'index']
        );
        $this->router->get(
            '/profile/{username}',
            [$profileController, 'show']
        );
        $this->router->get(
            '/profile/{username}/load',
            [$profileController, 'load']
        );

        $this->router->post(
            '/profile/{username}/follow',
            [$profileController, 'follow']
        );

        $this->router->post(
            '/profile/{username}/unfollow',
            [$profileController, 'unfollow']
        );

        $this->router->post(
            '/profile/{username}/block',
            [$profileController, 'block']
        );

        $this->router->post(
            '/profile/{username}/unblock',
            [$profileController, 'unblock']
        );

        $this->router->post('/profile/{username}/mute', [$moderationController, 'mute']);

        $this->router->post(
            '/profile/{username}/unmute',
            [$moderationController, 'unmute']
        );

        $this->router->post(
            '/profile/{username}/ban',
            [$moderationController, 'ban']
        );

        $this->router->post(
            '/profile/{username}/unban',
            [$moderationController, 'unban']
        );

        $this->router->get(
            '/members',
            [$membersController, 'index']
        );

        $this->router->get('/ping', [$pingController, 'index']);

        $this->router->get(
            '/ping/rss',
            [$pingController, 'rss']
        );

        $this->router->get(
            '/ping/load',
            [$pingController, 'load']
        );

        $this->router->get('/ping/{uuid}', [$pingController, 'show']);
        $this->router->get(
            '/ping/{uuid}/comments/load',
            [$pingController, 'loadComments']
        );
        $this->router->get(
            '/notifications',
            [$notificationController, 'index']
        );
        $this->router->post(
            '/notifications/delete-all',
            [$notificationController, 'deleteAll']
        );

        $this->router->post(
            '/notifications/{uuid}/delete',
            [$notificationController, 'delete']
        );

        $this->router->post('/ping', [$pingController, 'store']);
        $this->router->post(
            '/ping/doge-tip',
            [$pingController, 'shareDogeTip']
        );
        $this->router->post(
            '/doge-tip/notify',
            [$profileController, 'notifyDogeTip']
        );
        $this->router->post('/ping/{uuid}/comment', [$pingController, 'comment']);
        $this->router->post(
            '/ping/{uuid}/doge-tip-comment',
            [$pingController, 'commentDogeTip']
        );
        $this->router->post('/ping/{uuid}/upvote', [$pingController, 'upvote']);
        $this->router->post('/ping/{uuid}/downvote', [$pingController, 'downvote']);
        $this->router->post('/ping/{uuid}/save', [$pingController, 'save']);
        $this->router->post('/ping/{uuid}/unsave', [$pingController, 'removeSaved']);
        $this->router->post('/ping/{uuid}/delete', [$pingController, 'delete']);
        $this->router->post('/pong/{uuid}/delete', [$pingController, 'deleteComment']);
        $this->router->post('/ping/{uuid}/update', [$pingController, 'update']);
        $this->router->post('/pong/{uuid}/update', [$pingController, 'updateComment']);
        $this->router->post(
            '/report',
            [$reportController, 'store']
        );

        $this->router->get(
            '/storage/chanzine/{year}/{month}/{file}',
            [$mediaController, 'chanzine']
        );

        $this->router->get(
            '/chanzine',
            [$chanzineController, 'index']
        );

        $this->router->get(
            '/chanzine/rss',
            [$chanzineController, 'rss']
        );

        $this->router->get(
            '/chanzine/category/{slug}',
            [$chanzineController, 'category']
        );

        $this->router->get(
            '/chanzine/submit',
            [$chanzineController, 'submit']
        );

        $this->router->post(
            '/chanzine/submit',
            [$chanzineController, 'storeSubmission']
        );

        $this->router->get(
            '/chanzine/{slug}',
            [$chanzineController, 'show']
        );

        $this->router->post(
            '/article/{uuid}/save',
            [$chanzineController, 'save']
        );

        $this->router->post(
            '/article/{uuid}/unsave',
            [$chanzineController, 'removeSaved']
        );

        $this->router->get('/admin', [$dashboardController, 'index']);
        $this->router->get(
            '/admin/blocks',
            [$blockController, 'index']
        );
        $this->router->get(
            '/admin/blocks/area',
            [$blockController, 'area']
        );

        $this->router->post(
            '/admin/blocks/{id}/toggle',
            [$blockController, 'toggle']
        );

        $this->router->post(
            '/admin/blocks/{id}/delete',
            [$blockController, 'delete']
        );
        $this->router->post(
            '/admin/blocks/reorder',
            [$blockController, 'reorder']
        );
        $this->router->get(
            '/admin/blocks/library',
            [$blockController, 'library']
        );
        $this->router->get(
            '/admin/blocks/create',
            [$blockController, 'create']
        );
        $this->router->post(
            '/admin/blocks/store',
            [$blockController, 'store']
        );
        $this->router->get(
            '/admin/blocks/{id}/edit',
            [$blockController, 'edit']
        );
        $this->router->post(
            '/admin/blocks/{id}/update',
            [$blockController, 'update']
        );
        $this->router->get(
            '/admin/moderators',
            [$moderatorsController, 'index']
        );
        $this->router->post(
            '/admin/moderators/add',
            [$moderatorsController, 'add']
        );
        $this->router->post(
            '/admin/moderators/remove',
            [$moderatorsController, 'remove']
        );
        $this->router->post(
            '/admin/moderators/enable',
            [$moderatorsController, 'enable']
        );

        $this->router->post(
            '/admin/moderators/disable',
            [$moderatorsController, 'disable']
        );

        $this->router->get(
            '/admin/articles',
            [$articleController, 'index']
        );

        $this->router->post(
            '/admin/articles',
            [$articleController, 'store']
        );

        $this->router->get(
            '/admin/articles/{uuid}/edit',
            [$articleController, 'edit']
        );

        $this->router->post(
            '/admin/articles/{uuid}/publish',
            [$articleController, 'publish']
        );

        $this->router->post(
            '/admin/articles/{uuid}/reject',
            [$articleController, 'reject']
        );

        $this->router->post(
            '/admin/articles/{uuid}/delete',
            [$articleController, 'delete']
        );

        $this->router->post(
            '/admin/articles/{uuid}',
            [$articleController, 'update']
        );

        $this->router->get(
            '/admin/articles/create',
            [$articleController, 'create']
        );

        $this->router->get(
            '/admin/categories',
            [$categoryController, 'index']
        );

        $this->router->get(
            '/admin/categories/create',
            [$categoryController, 'create']
        );

        $this->router->post(
            '/admin/categories',
            [$categoryController, 'store']
        );

        $this->router->get(
            '/admin/categories/{uuid}/edit',
            [$categoryController, 'edit']
        );

        $this->router->post(
            '/admin/categories/{uuid}',
            [$categoryController, 'update']
        );

        $this->router->post(
            '/admin/categories/{uuid}/delete',
            [$categoryController, 'delete']
        );

        $this->router->get(
            '/admin/pages',
            [$pageAdminController, 'index']
        );

        $this->router->get(
            '/admin/pages/create',
            [$pageAdminController, 'create']
        );

        $this->router->post(
            '/admin/pages',
            [$pageAdminController, 'store']
        );

        $this->router->get(
            '/admin/pages/{id}/edit',
            [$pageAdminController, 'edit']
        );

        $this->router->post(
            '/admin/pages/{id}',
            [$pageAdminController, 'update']
        );

        $this->router->post(
            '/admin/pages/{id}/delete',
            [$pageAdminController, 'delete']
        );

        $this->router->get('/admin/login', [$adminAuthController, 'login']);
        $this->router->post('/admin/login', [$adminAuthController, 'authenticate']);
        $this->router->get('/admin/logout', [$adminAuthController, 'logout']);

        $this->router->get('/admin/settings', [$settingsController, 'index']);
        $this->router->post('/admin/settings', [$settingsController, 'save']);

        $this->router->post(
            '/admin/settings/brand/delete',
            [$settingsController, 'deleteBrandAsset']
        );

        $this->router->get('/admin/chat', [$webchatController, 'index']);
        $this->router->post('/admin/chat', [$webchatController, 'save']);

        $this->router->get('/account', [$accountController, 'index']);
        $this->router->post('/account', [$accountController, 'save']);
        $this->router->get('/account/profile', [$accountController, 'publicProfile']);
        $this->router->get(
            '/account/saved',
            [$accountController, 'saved']
        );
        $this->router->post(
            '/account/doge-tips',
            [$accountController, 'saveDogeTips']
        );
        $this->router->get(
            '/account/articles',
            [$accountController, 'articles']
        );
        $this->router->get(
            '/account/articles/{uuid}/edit',
            [$accountController, 'editArticle']
        );
        $this->router->post(
            '/account/articles/{uuid}',
            [$accountController, 'updateArticle']
        );

        $this->router->get(
            '/account/blocked',
            [$accountController, 'blocked']
        );
        $this->router->post('/account/profile', [$accountController, 'savePublicProfile']);
        $this->router->post(
            '/account/blocked/{username}/unblock',
            [$accountController, 'unblockBlockedUser']
        );
        $this->router->get(
            '/account/suspended',
            [$accountController, 'suspended']
        );
        $this->router->post('/account/delete', [$accountController, 'deleteProfile']);
        $this->router->get('/account/logout', [$oauthController, 'logout']);

        $this->router->get(
            '/account/moderation',
            [$moderationController, 'index']
        );

        $this->router->get(
            '/account/moderation/bans',
            [$moderationController, 'bans']
        );

        $this->router->get(
            '/account/moderation/mutes',
            [$moderationController, 'mutes']
        );

        $this->router->get(
            '/account/moderation/reports',
            [$moderationController, 'reports']
        );

        $this->router->get(
            '/account/moderation/report/{uuid}',
            [$moderationController, 'report']
        );

        $this->router->post(
            '/account/moderation/report/{uuid}/review',
            [$moderationController, 'review']
        );

        $this->router->post(
            '/account/moderation/report/{uuid}/close',
            [$moderationController, 'close']
        );

        $this->router->post(
            '/account/moderation/report/{uuid}/delete',
            [$moderationController, 'deleteContent']
        );

        $this->router->get('/oauth/login', [$oauthController, 'login']);
        $this->router->get('/login/callback', [$oauthController, 'callback']);

        $this->router->get(
            '/api/simosnap/nick/check',
            function () use ($apiController): void {
                $apiController->simosnapProxy('nick/check');
            }
        );

        $this->router->get(
            '/api/mentions',
            [$apiController, 'mentions']
        );

        $this->router->post(
            '/api/azuracast/request',
            [$apiController, 'azuraCastSongRequest']
        );

        $this->router->get(
            '/{slug}',
            [$pageController, 'show']
        );
    }

}