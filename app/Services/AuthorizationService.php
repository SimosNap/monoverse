<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Services\UserModerationService;

class AuthorizationService
{
    public function __construct(
        private UserModerationService $moderation
    ) {
    }

    private const POST_EDIT_WINDOW = 1800;      // 30 minuti
    private const COMMENT_EDIT_WINDOW = 900;    // 15 minuti

    public function isModerator(array $user): bool
    {
        return !empty($user['is_moderator']);
    }

    public function canIgnoreBlocks(array $user): bool
    {
        return $this->isModerator($user);
    }

    public function canDeletePost(array $user, array $post): bool
    {
        $sub = (string) ($user['sub'] ?? '');

        if ($sub !== '' && $sub === (string) ($post['author_sub'] ?? '')) {
            return true;
        }

        return $this->isModerator($user);
    }

    public function canDeleteComment(array $user, array $comment): bool
    {
        $sub = (string) ($user['sub'] ?? '');

        if ($sub !== '' && $sub === (string) ($comment['author_sub'] ?? '')) {
            return true;
        }

        return $this->isModerator($user);
    }

    public function canEditPost(array $user, array $post): bool
    {
        $sub = (string) ($user['sub'] ?? '');

        if (
            $sub === ''
            || $sub !== (string) ($post['author_sub'] ?? '')
        ) {
            return false;
        }

        $publishedAt = strtotime((string) ($post['published_at'] ?? ''));

        if ($publishedAt === false) {
            return false;
        }

        return time() <= ($publishedAt + self::POST_EDIT_WINDOW);
    }

    public function canEditComment(array $user, array $comment): bool
    {
        $sub = (string) ($user['sub'] ?? '');

        if (
            $sub === ''
            || $sub !== (string) ($comment['author_sub'] ?? '')
        ) {
            return false;
        }

        $createdAt = strtotime((string) ($comment['created_at'] ?? ''));

        if ($createdAt === false) {
            return false;
        }

        return time() <= ($createdAt + self::COMMENT_EDIT_WINDOW);
    }

    public function getPostEditExpiresAt(array $post): ?int
    {
        $publishedAt = strtotime((string) ($post['published_at'] ?? ''));

        if ($publishedAt === false) {
            return null;
        }

        return $publishedAt + self::POST_EDIT_WINDOW;
    }

    public function getCommentEditExpiresAt(array $comment): ?int
    {
        $createdAt = strtotime((string) ($comment['created_at'] ?? ''));

        if ($createdAt === false) {
            return null;
        }

        return $createdAt + self::COMMENT_EDIT_WINDOW;
    }

    public function isBanned(array $user): bool
    {
        $sub = trim((string) ($user['sub'] ?? ''));

        if ($sub === '') {
            return false;
        }

        return $this->moderation->isBanned($sub);
    }

    public function canUseCommunity(array $user): bool
    {
        return !$this->isBanned($user);
    }

    public function canPost(array $user): bool
    {
        $sub = trim((string) ($user['sub'] ?? ''));

        if ($sub === '') {
            return false;
        }

        return !$this->moderation->isMuted($sub);
    }
}
