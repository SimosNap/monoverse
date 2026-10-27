<?php
declare(strict_types=1);

namespace Monoverse\Services;

use InvalidArgumentException;
use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;
use Throwable;

class AskVerseService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function create(
        string $authorSub,
        string $title,
        string $content,
        array $tags = [],
        ?array $codeBlock = null
    ): string|false {
        $authorSub = trim($authorSub);
        $title = trim($title);
        $content = trim($content);
        $tags = $this->normalizeTags($tags);

        if ($authorSub === '' || $title === '' || $content === '') {
            throw new InvalidArgumentException(
                'Author, title and content are required.'
            );
        }

        if (mb_strlen($title) > 255) {
            throw new InvalidArgumentException(
                'Question title is too long.'
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

        $uuid = Uuid::v4();
        $pdo = $this->database->pdo();

        $pdo->beginTransaction();

        try {
            $created = $this->database->execute(
                'INSERT INTO community_questions
                    (uuid, author_sub, title, content, metadata)
                 VALUES (?, ?, ?, ?, ?)',
                [
                    $uuid,
                    $authorSub,
                    $title,
                    $content,
                    $metadata,
                ]
            );

            if (!$created) {
                $pdo->rollBack();
                return false;
            }

            $questionId = (int) $this->database->lastInsertId();

            $this->saveTags($questionId, $tags);

            $pdo->commit();

            return $uuid;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function findByUuid(
        string $uuid,
        ?string $currentSub = null
    ): array|false {
        $question = $this->database->fetchOne(
            'SELECT
                q.*,
                p.username,
                p.nickname,
                p.avatar_url,
                p.show_avatar,
                p.public_profile,
                (
                    SELECT COUNT(*)
                    FROM community_question_answers a
                    WHERE a.question_id = q.id
                      AND a.status = ?
                      AND a.deleted_at IS NULL
                ) AS answers_count,
                (
                    SELECT COALESCE(SUM(v.vote), 0)
                    FROM community_question_votes v
                    WHERE v.question_id = q.id
                ) AS score,
                (
                    SELECT COALESCE(v.vote, 0)
                    FROM community_question_votes v
                    WHERE v.question_id = q.id
                      AND v.author_sub = ?
                    LIMIT 1
                ) AS user_vote
             FROM community_questions q
             LEFT JOIN profiles p
                ON p.oauth_sub = q.author_sub
             WHERE q.uuid = ?
               AND q.status = ?
               AND q.deleted_at IS NULL
             LIMIT 1',
            [
                'published',
                $currentSub ?? '',
                $uuid,
                'published',
            ]
        );

        if ($question === false) {
            return false;
        }

        $question['tags'] = $this->getTags(
            (int) $question['id']
        );

        $question['is_solved'] =
            $question['accepted_answer_id'] !== null;

        return $question;
    }

    public function listPublished(
        string $filter = 'recent',
        int $limit = 20,
        int $offset = 0,
        ?string $tag = null,
        ?string $search = null,
        ?string $currentSub = null
    ): array {
        $filter = strtolower(trim($filter));

        if (!in_array(
            $filter,
            ['recent', 'unanswered', 'solved'],
            true
        )) {
            $filter = 'recent';
        }

        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);

        $conditions = [
            "q.status = 'published'",
            'q.deleted_at IS NULL',
        ];

        $params = [
            $currentSub ?? '',
        ];

        if ($filter === 'unanswered') {
            $conditions[] = 'NOT EXISTS (
                SELECT 1
                FROM community_question_answers a
                WHERE a.question_id = q.id
                  AND a.status = \'published\'
                  AND a.deleted_at IS NULL
            )';
        }

        if ($filter === 'solved') {
            $conditions[] = 'q.accepted_answer_id IS NOT NULL';
        }

        $tag = trim((string) $tag);

        if ($tag !== '') {
            $conditions[] = 'EXISTS (
                SELECT 1
                FROM community_question_tags t
                WHERE t.question_id = q.id
                  AND t.tag = ?
            )';

            $params[] = mb_strtolower($tag);
        }

        $search = trim((string) $search);

        if ($search !== '') {
            $conditions[] = "(
                q.title LIKE ? ESCAPE '!'
                OR q.content LIKE ? ESCAPE '!'
            )";

            $escapedSearch = str_replace(
                ['!', '%', '_'],
                ['!!', '!%', '!_'],
                $search
            );

            $params[] = '%' . $escapedSearch . '%';
            $params[] = '%' . $escapedSearch . '%';
        }

        $where = implode(' AND ', $conditions);

        $questions = $this->database->fetchAll(
            'SELECT
                q.*,
                p.username,
                p.nickname,
                p.avatar_url,
                p.show_avatar,
                p.public_profile,
                (
                    SELECT COUNT(*)
                    FROM community_question_answers a
                    WHERE a.question_id = q.id
                      AND a.status = \'published\'
                      AND a.deleted_at IS NULL
                ) AS answers_count,
                (
                    SELECT COALESCE(SUM(v.vote), 0)
                    FROM community_question_votes v
                    WHERE v.question_id = q.id
                ) AS score,
                (
                    SELECT COALESCE(v.vote, 0)
                    FROM community_question_votes v
                    WHERE v.question_id = q.id
                      AND v.author_sub = ?
                    LIMIT 1
                ) AS user_vote
             FROM community_questions q
             LEFT JOIN profiles p
                ON p.oauth_sub = q.author_sub
             WHERE ' . $where . '
             ORDER BY q.created_at DESC, q.id DESC
             LIMIT ' . $limit . '
             OFFSET ' . $offset,
            $params
        );

        foreach ($questions as &$question) {
            $question['tags'] = $this->getTags(
                (int) $question['id']
            );

            $question['is_solved'] =
                $question['accepted_answer_id'] !== null;
        }

        unset($question);

        return $questions;
    }

    public function update(
        string $uuid,
        string $authorSub,
        string $title,
        string $content,
        array $tags = [],
        ?array $codeBlock = null
    ): bool {
        $question = $this->findByUuid($uuid);

        if (
            !$question
            || $question['author_sub'] !== $authorSub
        ) {
            return false;
        }

        $title = trim($title);
        $content = trim($content);
        $tags = $this->normalizeTags($tags);

        if ($title === '' || $content === '') {
            throw new InvalidArgumentException(
                'Title and content are required.'
            );
        }

        if (mb_strlen($title) > 255) {
            throw new InvalidArgumentException(
                'Question title is too long.'
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

        $pdo = $this->database->pdo();
        $pdo->beginTransaction();

        try {
            $updated = $this->database->execute(
                'UPDATE community_questions
                 SET title = ?,
                     content = ?,
                     metadata = ?
                 WHERE id = ?
                   AND author_sub = ?
                   AND status = ?
                   AND deleted_at IS NULL',
                [
                    $title,
                    $content,
                    $metadata,
                    (int) $question['id'],
                    $authorSub,
                    'published',
                ]
            );

            if (!$updated) {
                $pdo->rollBack();
                return false;
            }

            $this->database->execute(
                'DELETE FROM community_question_tags
                 WHERE question_id = ?',
                [
                    (int) $question['id'],
                ]
            );

            $this->saveTags(
                (int) $question['id'],
                $tags
            );

            $pdo->commit();

            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function delete(
        string $uuid,
        string $authorSub,
        bool $isModerator = false
    ): bool {
        $question = $this->findByUuid($uuid);

        if (
            !$question
            || (
                !$isModerator
                && (
                    $question['author_sub'] !== $authorSub
                    || $this->database->fetchOne(
                        'SELECT id
                         FROM community_question_answers
                         WHERE question_id = ?
                         LIMIT 1',
                        [(int) $question['id']]
                    ) !== false
                )
            )
        ) {
            return false;
        }

        return $this->database->execute(
            'UPDATE community_questions
             SET status = ?,
                 deleted_at = NOW()
             WHERE id = ?
               AND (? = 1 OR author_sub = ?)
               AND status = ?
               AND deleted_at IS NULL
               AND (
                   ? = 1
                   OR NOT EXISTS (
                       SELECT 1
                       FROM community_question_answers a
                       WHERE a.question_id = community_questions.id
                   )
               )',
            [
                'deleted',
                (int) $question['id'],
                $isModerator ? 1 : 0,
                $authorSub,
                'published',
                $isModerator ? 1 : 0,
            ]
        );
    }

    public function getTags(int $questionId): array
    {
        $rows = $this->database->fetchAll(
            'SELECT tag
             FROM community_question_tags
             WHERE question_id = ?
             ORDER BY tag ASC',
            [
                $questionId,
            ]
        );

        return array_column($rows, 'tag');
    }

    private function saveTags(
        int $questionId,
        array $tags
    ): void {
        foreach ($tags as $tag) {
            $this->database->execute(
                'INSERT INTO community_question_tags
                    (question_id, tag)
                 VALUES (?, ?)',
                [
                    $questionId,
                    $tag,
                ]
            );
        }
    }

    private function normalizeTags(array $tags): array
    {
        $normalized = [];

        foreach ($tags as $tag) {
            if (!is_string($tag)) {
                continue;
            }

            $tag = mb_strtolower(trim($tag));

            if ($tag === '') {
                continue;
            }

            if (mb_strlen($tag) > 64) {
                throw new InvalidArgumentException(
                    'Tag is too long.'
                );
            }

            $normalized[] = $tag;
        }

        $normalized = array_values(
            array_unique($normalized)
        );

        if (count($normalized) > 5) {
            throw new InvalidArgumentException(
                'Maximum five tags allowed.'
            );
        }

        return $normalized;
    }
}
