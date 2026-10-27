<?php
declare(strict_types=1);

namespace Monoverse\Services;

use InvalidArgumentException;
use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class AskVerseCommentService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function create(
        string $answerUuid,
        string $authorSub,
        string $content
    ): string|false {
        $authorSub = trim($authorSub);
        $content = trim($content);

        if ($authorSub === '' || $content === '') {
            throw new InvalidArgumentException(
                'Author and content are required.'
            );
        }

        $answer = $this->database->fetchOne(
            'SELECT a.id
             FROM community_question_answers a
             INNER JOIN community_questions q
                ON q.id = a.question_id
             WHERE a.uuid = ?
               AND a.status = ?
               AND a.deleted_at IS NULL
               AND q.status = ?
               AND q.deleted_at IS NULL
             LIMIT 1',
            [
                $answerUuid,
                'published',
                'published',
            ]
        );

        if (!$answer) {
            return false;
        }

        $uuid = Uuid::v4();

        $created = $this->database->execute(
            'INSERT INTO community_answer_comments
                (uuid, answer_id, author_sub, content)
             VALUES (?, ?, ?, ?)',
            [
                $uuid,
                (int) $answer['id'],
                $authorSub,
                $content,
            ]
        );

        return $created ? $uuid : false;
    }

    public function findByUuid(string $uuid): array|false
    {
        return $this->database->fetchOne(
            'SELECT
                c.*,
                a.uuid AS answer_uuid,
                q.uuid AS question_uuid,
                p.username,
                p.nickname,
                p.avatar_url,
                p.show_avatar,
                p.public_profile
             FROM community_answer_comments c
             INNER JOIN community_question_answers a
                ON a.id = c.answer_id
             INNER JOIN community_questions q
                ON q.id = a.question_id
             LEFT JOIN profiles p
                ON p.oauth_sub = c.author_sub
             WHERE c.uuid = ?
               AND c.status = ?
               AND c.deleted_at IS NULL
               AND a.status = ?
               AND a.deleted_at IS NULL
               AND q.status = ?
               AND q.deleted_at IS NULL
             LIMIT 1',
            [
                $uuid,
                'published',
                'published',
                'published',
            ]
        );
    }

    public function listByAnswer(int $answerId): array
    {
        return $this->database->fetchAll(
            'SELECT
                c.*,
                p.username,
                p.nickname,
                p.avatar_url,
                p.show_avatar,
                p.public_profile
             FROM community_answer_comments c
             INNER JOIN community_question_answers a
                ON a.id = c.answer_id
             INNER JOIN community_questions q
                ON q.id = a.question_id
             LEFT JOIN profiles p
                ON p.oauth_sub = c.author_sub
             WHERE c.answer_id = ?
               AND c.status = ?
               AND c.deleted_at IS NULL
               AND a.status = ?
               AND a.deleted_at IS NULL
               AND q.status = ?
               AND q.deleted_at IS NULL
             ORDER BY c.created_at ASC, c.id ASC',
            [
                $answerId,
                'published',
                'published',
                'published',
            ]
        );
    }

    public function update(
        string $uuid,
        string $authorSub,
        string $content
    ): bool {
        $authorSub = trim($authorSub);
        $content = trim($content);

        if ($content === '') {
            throw new InvalidArgumentException(
                'Comment content is required.'
            );
        }

        $comment = $this->findByUuid($uuid);

        if (
            !$comment
            || $authorSub === ''
            || $comment['author_sub'] !== $authorSub
        ) {
            return false;
        }

        return $this->database->execute(
            'UPDATE community_answer_comments
             SET content = ?
             WHERE id = ?
               AND author_sub = ?
               AND status = ?
               AND deleted_at IS NULL',
            [
                $content,
                (int) $comment['id'],
                $authorSub,
                'published',
            ]
        );
    }

    public function delete(
        string $uuid,
        string $authorSub,
        bool $isModerator = false
    ): bool {
        $authorSub = trim($authorSub);

        $comment = $this->findByUuid($uuid);

        if (
            !$comment
            || $authorSub === ''
            || (
                !$isModerator
                && $comment['author_sub'] !== $authorSub
            )
        ) {
            return false;
        }

        return $this->database->execute(
            'UPDATE community_answer_comments
             SET status = ?,
                 deleted_at = NOW()
             WHERE id = ?
               AND (? = 1 OR author_sub = ?)
               AND status = ?
               AND deleted_at IS NULL',
            [
                'deleted',
                (int) $comment['id'],
                $isModerator ? 1 : 0,
                $authorSub,
                'published',
            ]
        );
    }
}
