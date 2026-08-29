<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

use Monoverse\Core\Container;
use Monoverse\Core\Blocks\Content\HtmlBlock;
use Monoverse\Core\Blocks\Content\LatestArticlesBlock;
use Monoverse\Core\Blocks\Community\UsersInChatBlock;
use Monoverse\Core\Blocks\Community\LatestMembersBlock;
use Monoverse\Core\Blocks\Content\CategoriesBlock;
use Monoverse\Core\Blocks\Webradio\AzuraCastBlock;
use Monoverse\Core\Blocks\Webradio\AzuraCastRequestsBlock;
use Monoverse\Core\Blocks\Webradio\AzuraCastStatsBlock;
use Monoverse\Core\Blocks\Webradio\AzuraCastMiniPlayerBlock;
use Monoverse\Core\Blocks\Webradio\IcecastBlock;
use Monoverse\Core\Blocks\Webradio\IcecastStatsBlock;
use Monoverse\Core\Blocks\Developer\GitHubRepositoryBlock;
use Monoverse\Core\Blocks\Content\PagesNavigationBlock;
use Monoverse\Core\Blocks\Developer\GitHubReleaseBlock;
use Monoverse\Core\Blocks\Developer\GitHubPullRequestsBlock;
use Monoverse\Core\Blocks\Content\LatestAudioBlock;
use Monoverse\Core\Blocks\Content\LatestVideoBlock;
use Monoverse\Core\Blocks\Content\SubmitArticleBlock;
use Monoverse\Core\Blocks\Community\MostActiveUsersBlock;
use Monoverse\Services\PostService;

final class BlockProvider
{
    public function register(
        BlockRegistry $registry,
        Container $container
    ): void {
        $registry->register(
            $container->get(HtmlBlock::class)
        );
        $registry->register(
            $container->get(LatestArticlesBlock::class)
        );
        $registry->register(
            $container->get(SubmitArticleBlock::class)
        );
        $registry->register(
            new LatestAudioBlock(
                $container->get(PostService::class)
            )
        );
        $registry->register(
            new LatestVideoBlock(
                $container->get(PostService::class)
            )
        );
        $registry->register(
            $container->get(UsersInChatBlock::class)
        );
        $registry->register(
            $container->get(MostActiveUsersBlock::class)
        );
        $registry->register(
            $container->get(LatestMembersBlock::class)
        );
        $registry->register(
            $container->get(CategoriesBlock::class)
        );
        $registry->register(
            $container->get(PagesNavigationBlock::class)
        );
        $registry->register(
            $container->get(AzuraCastBlock::class)
        );
        $registry->register(
            $container->get(AzuraCastRequestsBlock::class)
        );
        $registry->register(
            $container->get(AzuraCastStatsBlock::class)
        );
        $registry->register(
            $container->get(AzuraCastMiniPlayerBlock::class)
        );
        $registry->register(
            $container->get(IcecastBlock::class)
        );
        $registry->register(
            $container->get(IcecastStatsBlock::class)
        );
        $registry->register(
            $container->get(GitHubRepositoryBlock::class)
        );
        $registry->register(
            $container->get(GitHubReleaseBlock::class)
        );
        $registry->register(
            $container->get(GitHubPullRequestsBlock::class)
        );
    }
}
