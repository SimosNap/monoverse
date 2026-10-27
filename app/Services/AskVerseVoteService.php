<?php
declare(strict_types=1);

namespace Monoverse\Services;

use InvalidArgumentException;
use Monoverse\Core\Database;

class AskVerseVoteService
{
    public function __construct(
        private Database $database
    ) {
    }

    public function voteQuestion(
        string $questionUuid,
        string $authorSub,
        int $vote
    ): bool {
        return $this->vote(
            'question',
            $questionUuid,
            $authorSub,
            $vote
        );
    }

    public function voteAnswer(
        string $answerUuid,
        string $authorSub,
        int $vote
    ): bool {
        return $this->vote(
            'answer',
            $answerUuid,
            $authorSub,
            $vote
        );
    }

    private function vote(
        string $type,
        string $uuid,
        string $authorSub,
        int $vote
    ): bool {
        $authorSub = trim($authorSub);

        if ($authorSub === '') {
            return false;
        }

        if (!in_array($vote, [-1, 1], true)) {
            throw new InvalidArgumentException(
                'Vote must be +1 or -1.'
            );
        }

        if ($type === 'question') {
            $table = 'community_question_votes';
            $column = 'question_id';

            $contentSql = '
                SELECT id, author_sub
                FROM community_questions
                WHERE uuid = ?
                  AND status = ?
                  AND deleted_at IS NULL
                LIMIT 1
                FOR UPDATE
            ';

            $contentParams = [
                $uuid,
                'published',
            ];
        } elseif ($type === 'answer') {
            $table = 'community_answer_votes';
            $column = 'answer_id';

            $contentSql = '
                SELECT a.id, a.author_sub
                FROM community_question_answers a
                INNER JOIN community_questions q
                    ON q.id = a.question_id
                WHERE a.uuid = ?
                  AND a.status = ?
                  AND a.deleted_at IS NULL
                  AND q.status = ?
                  AND q.deleted_at IS NULL
                LIMIT 1
                FOR UPDATE
            ';

            $contentParams = [
                $uuid,
                'published',
                'published',
            ];
        } else {
            return false;
        }

        $pdo = $this->database->pdo();

        $pdo->beginTransaction();

        try {
            /*
             * Blocchiamo il contenuto votato.
             *
             * Le richieste concorrenti sullo stesso contenuto
             * vengono serializzate prima di leggere o modificare
             * il voto dell'utente.
             */
            $content = $this->database->fetchOne(
                $contentSql,
                $contentParams
            );

            if (
                !$content
                || $content['author_sub'] === $authorSub
            ) {
                $pdo->rollBack();
                return false;
            }

            $contentId = (int) $content['id'];

            $existing = $this->database->fetchOne(
                "SELECT vote
                 FROM {$table}
                 WHERE {$column} = ?
                   AND author_sub = ?
                 LIMIT 1
                 FOR UPDATE",
                [
                    $contentId,
                    $authorSub,
                ]
            );

            if ($existing !== false) {
                if ((int) $existing['vote'] === $vote) {
                    // Stesso voto: annullamento.
                    $result = $this->database->execute(
                        "DELETE FROM {$table}
                         WHERE {$column} = ?
                           AND author_sub = ?",
                        [
                            $contentId,
                            $authorSub,
                        ]
                    );
                } else {
                    // Voto opposto: aggiornamento.
                    $result = $this->database->execute(
                        "UPDATE {$table}
                         SET vote = ?
                         WHERE {$column} = ?
                           AND author_sub = ?",
                        [
                            $vote,
                            $contentId,
                            $authorSub,
                        ]
                    );
                }
            } else {
                // Primo voto.
                $result = $this->database->execute(
                    "INSERT INTO {$table}
                        ({$column}, author_sub, vote)
                     VALUES (?, ?, ?)",
                    [
                        $contentId,
                        $authorSub,
                        $vote,
                    ]
                );
            }

            if (!$result) {
                $pdo->rollBack();
                return false;
            }

            $pdo->commit();

            return true;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    public function getQuestionScore(
        int $questionId
    ): int {
        $result = $this->database->fetchOne(
            'SELECT COALESCE(SUM(vote), 0) AS score
             FROM community_question_votes
             WHERE question_id = ?',
            [
                $questionId,
            ]
        );

        return (int) ($result['score'] ?? 0);
    }

    public function getAnswerScore(
        int $answerId
    ): int {
        $result = $this->database->fetchOne(
            'SELECT COALESCE(SUM(vote), 0) AS score
             FROM community_answer_votes
             WHERE answer_id = ?',
            [
                $answerId,
            ]
        );

        return (int) ($result['score'] ?? 0);
    }

    public function getQuestionUserVote(
        int $questionId,
        string $authorSub
    ): int {
        $result = $this->database->fetchOne(
            'SELECT vote
             FROM community_question_votes
             WHERE question_id = ?
               AND author_sub = ?
             LIMIT 1',
            [
                $questionId,
                $authorSub,
            ]
        );

        return (int) ($result['vote'] ?? 0);
    }

    public function getAnswerUserVote(
        int $answerId,
        string $authorSub
    ): int {
        $result = $this->database->fetchOne(
            'SELECT vote
             FROM community_answer_votes
             WHERE answer_id = ?
               AND author_sub = ?
             LIMIT 1',
            [
                $answerId,
                $authorSub,
            ]
        );

        return (int) ($result['vote'] ?? 0);
    }
}
