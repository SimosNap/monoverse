<?php
declare(strict_types=1);

namespace Monoverse\Services;

use InvalidArgumentException;
use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;
use Throwable;

class AskVerseAnswerService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function create(
        string $questionUuid,
        string $authorSub,
        string $content,
        ?array $codeBlock = null
    ): string|false {
        $authorSub = trim($authorSub);
        $content = trim($content);

        if ($authorSub === '' || $content === '') {
            throw new InvalidArgumentException(
                'Author and content are required.'
            );
        }

        $metadata = null;

        if ($codeBlock !== null) {
            $language = trim((string) ($codeBlock['language'] ?? ''));
            $code = (string) ($codeBlock['code'] ?? '');

            if (trim($code) !== '') {
                $metadata = json_encode(
                    [
                        'code_block' => [
                            'language' => $language !== '' ? $language : 'plaintext',
                            'code' => $code,
                        ],
                    ],
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
                );
            }
        }

        $question = $this->database->fetchOne(
            'SELECT id
             FROM community_questions
             WHERE uuid = ?
               AND status = ?
               AND deleted_at IS NULL
             LIMIT 1',
            [
                $questionUuid,
                'published',
            ]
        );

        if (!$question) {
            return false;
        }

        $uuid = Uuid::v4();

        $created = $this->database->execute(
            'INSERT INTO community_question_answers
                (uuid, question_id, author_sub, content, metadata)
             VALUES (?, ?, ?, ?, ?)',
            [
                $uuid,
                (int) $question['id'],
                $authorSub,
                $content,
                $metadata,
            ]
        );

        return $created ? $uuid : false;
    }

    public function findByUuid(
        string $uuid,
        ?string $currentSub = null
    ): array|false {
        return $this->database->fetchOne(
            'SELECT
                a.*,
                q.uuid AS question_uuid,
                q.author_sub AS question_author_sub,
                q.accepted_answer_id,
                p.username,
                p.nickname,
                p.avatar_url,
                p.show_avatar,
                p.public_profile,
                (
                    SELECT COALESCE(SUM(v.vote), 0)
                    FROM community_answer_votes v
                    WHERE v.answer_id = a.id
                ) AS score,
                (
                    SELECT COALESCE(v.vote, 0)
                    FROM community_answer_votes v
                    WHERE v.answer_id = a.id
                      AND v.author_sub = ?
                    LIMIT 1
                ) AS user_vote,
                (
                    SELECT COUNT(*)
                    FROM community_answer_comments c
                    WHERE c.answer_id = a.id
                      AND c.status = ?
                      AND c.deleted_at IS NULL
                ) AS comments_count
             FROM community_question_answers a
             INNER JOIN community_questions q
                ON q.id = a.question_id
             LEFT JOIN profiles p
                ON p.oauth_sub = a.author_sub
             WHERE a.uuid = ?
               AND a.status = ?
               AND a.deleted_at IS NULL
               AND q.status = ?
               AND q.deleted_at IS NULL
             LIMIT 1',
            [
                $currentSub ?? '',
                'published',
                $uuid,
                'published',
                'published',
            ]
        );
    }

    public function listByQuestion(
        int $questionId,
        string $sort = 'votes',
        ?string $currentSub = null
    ): array {
        $sort = strtolower(trim($sort));

        if (!in_array($sort, ['votes', 'newest'], true)) {
            $sort = 'votes';
        }

        $order = $sort === 'newest'
            ? 'a.created_at DESC, a.id DESC'
            : 'score DESC, a.created_at ASC, a.id ASC';

        return $this->database->fetchAll(
            'SELECT
                a.*,
                q.accepted_answer_id,
                p.username,
                p.nickname,
                p.avatar_url,
                p.show_avatar,
                p.public_profile,
                (
                    SELECT COALESCE(SUM(v.vote), 0)
                    FROM community_answer_votes v
                    WHERE v.answer_id = a.id
                ) AS score,
                (
                    SELECT COALESCE(v.vote, 0)
                    FROM community_answer_votes v
                    WHERE v.answer_id = a.id
                      AND v.author_sub = ?
                    LIMIT 1
                ) AS user_vote,
                (
                    SELECT COUNT(*)
                    FROM community_answer_comments c
                    WHERE c.answer_id = a.id
                      AND c.status = ?
                      AND c.deleted_at IS NULL
                ) AS comments_count
             FROM community_question_answers a
             INNER JOIN community_questions q
                ON q.id = a.question_id
             LEFT JOIN profiles p
                ON p.oauth_sub = a.author_sub
             WHERE a.question_id = ?
               AND a.status = ?
               AND a.deleted_at IS NULL
               AND q.status = ?
               AND q.deleted_at IS NULL
             ORDER BY
                (a.id = q.accepted_answer_id) DESC,
                ' . $order,
            [
                $currentSub ?? '',
                'published',
                $questionId,
                'published',
                'published',
            ]
        );
    }

    public function update(
        string $uuid,
        string $authorSub,
        string $content,
        ?array $codeBlock = null
    ): bool {
        $answer = $this->findByUuid($uuid);

        if (
            !$answer
            || $answer['author_sub'] !== $authorSub
        ) {
            return false;
        }

        $content = trim($content);

        if ($content === '') {
            throw new InvalidArgumentException(
                'Answer content is required.'
            );
        }

        $metadata = null;

        if ($codeBlock !== null) {
            $language = trim((string) ($codeBlock['language'] ?? ''));
            $code = (string) ($codeBlock['code'] ?? '');

            if (trim($code) !== '') {
                $metadata = json_encode(
                    [
                        'code_block' => [
                            'language' => $language !== '' ? $language : 'plaintext',
                            'code' => $code,
                        ],
                    ],
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
                );
            }
        }

        return $this->database->execute(
            'UPDATE community_question_answers
             SET content = ?,
                 metadata = ?
             WHERE id = ?
               AND author_sub = ?
               AND status = ?
               AND deleted_at IS NULL',
            [
                $content,
                $metadata,
                (int) $answer['id'],
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
        $answer = $this->findByUuid($uuid);

        if (
            !$answer
            || (
                !$isModerator
                && $answer['author_sub'] !== $authorSub
            )
        ) {
            return false;
        }

        $pdo = $this->database->pdo();
        $pdo->beginTransaction();

        try {
            if (
                (int) ($answer['accepted_answer_id'] ?? 0)
                === (int) $answer['id']
            ) {
                $this->database->execute(
                    'UPDATE community_questions
                     SET accepted_answer_id = NULL
                     WHERE id = ?
                       AND accepted_answer_id = ?',
                    [
                        (int) $answer['question_id'],
                        (int) $answer['id'],
                    ]
                );
            }

            $deleted = $this->database->execute(
                'UPDATE community_question_answers
                 SET status = ?,
                     deleted_at = NOW()
                 WHERE id = ?
                   AND (? = 1 OR author_sub = ?)
                   AND status = ?
                   AND deleted_at IS NULL',
                [
                    'deleted',
                    (int) $answer['id'],
                    $isModerator ? 1 : 0,
                    $authorSub,
                    'published',
                ]
            );

            if (!$deleted) {
                $pdo->rollBack();
                return false;
            }

            $pdo->commit();

            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function accept(
        string $questionUuid,
        string $answerUuid,
        string $authorSub
    ): bool {
        $question = $this->database->fetchOne(
            'SELECT id, author_sub
             FROM community_questions
             WHERE uuid = ?
               AND status = ?
               AND deleted_at IS NULL
             LIMIT 1',
            [
                $questionUuid,
                'published',
            ]
        );

        if (
            !$question
            || $question['author_sub'] !== $authorSub
        ) {
            return false;
        }

        $answer = $this->database->fetchOne(
            'SELECT id
             FROM community_question_answers
             WHERE uuid = ?
               AND question_id = ?
               AND status = ?
               AND deleted_at IS NULL
             LIMIT 1',
            [
                $answerUuid,
                (int) $question['id'],
                'published',
            ]
        );

        if (!$answer) {
            return false;
        }

        return $this->database->execute(
            'UPDATE community_questions
             SET accepted_answer_id = ?
             WHERE id = ?
               AND author_sub = ?
               AND status = ?
               AND deleted_at IS NULL',
            [
                (int) $answer['id'],
                (int) $question['id'],
                $authorSub,
                'published',
            ]
        );
    }

    public function unaccept(
        string $questionUuid,
        string $authorSub
    ): bool {
        return $this->database->execute(
            'UPDATE community_questions
             SET accepted_answer_id = NULL
             WHERE uuid = ?
               AND author_sub = ?
               AND status = ?
               AND deleted_at IS NULL',
            [
                $questionUuid,
                $authorSub,
                'published',
            ]
        );
    }
}
