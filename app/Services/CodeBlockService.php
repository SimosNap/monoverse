<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;

class CodeBlockService
{
    private const MAX_CODE_LENGTH = 10000;

    private const CONTENT_TYPES = [
        'ping',
        'pong',
    ];

    public function __construct(
        private Database $database
    ) {}

    public function validate(
        string $code,
        string $language = 'text'
    ): void
    {
        if ($code === '') {
            return;
        }

        if (mb_strlen($code) > self::MAX_CODE_LENGTH) {
            throw new \RuntimeException(
                sprintf(
                    'Il blocco di codice può contenere al massimo %d caratteri.',
                    self::MAX_CODE_LENGTH
                )
            );
        }

        $this->normalizeLanguage($language);
    }

    public function save(
        string $contentType,
        int $contentId,
        string $code,
        string $language = 'text',
        int $position = 0
    ): void
    {
        $contentType = $this->normalizeContentType(
            $contentType
        );

        $language = $this->normalizeLanguage(
            $language
        );

        if ($contentId <= 0) {
            throw new \InvalidArgumentException(
                'Invalid content ID.'
            );
        }

        if ($position < 0) {
            throw new \InvalidArgumentException(
                'Invalid code block position.'
            );
        }

        if ($code === '') {
            $this->delete(
                $contentType,
                $contentId,
                $position
            );

            return;
        }

        $this->validate($code, $language);

        $existing = $this->database->fetchOne(
            '
            SELECT id
            FROM community_code_blocks
            WHERE content_type = ?
              AND content_id = ?
              AND position = ?
            LIMIT 1
            ',
            [
                $contentType,
                $contentId,
                $position,
            ]
        );

        if (is_array($existing)) {
            $this->database->execute(
                '
                UPDATE community_code_blocks
                SET language = ?,
                    code = ?
                WHERE id = ?
                ',
                [
                    $language,
                    $code,
                    (int) $existing['id'],
                ]
            );

            return;
        }

        $this->database->execute(
            '
            INSERT INTO community_code_blocks
            (
                content_type,
                content_id,
                language,
                code,
                position
            )
            VALUES (?, ?, ?, ?, ?)
            ',
            [
                $contentType,
                $contentId,
                $language,
                $code,
                $position,
            ]
        );
    }

    public function find(
        string $contentType,
        int $contentId,
        int $position = 0
    ): ?array
    {
        $contentType = $this->normalizeContentType(
            $contentType
        );

        $row = $this->database->fetchOne(
            '
            SELECT *
            FROM community_code_blocks
            WHERE content_type = ?
              AND content_id = ?
              AND position = ?
            LIMIT 1
            ',
            [
                $contentType,
                $contentId,
                $position,
            ]
        );

        return is_array($row)
            ? $row
            : null;
    }

    public function delete(
        string $contentType,
        int $contentId,
        int $position = 0
    ): void
    {
        $contentType = $this->normalizeContentType(
            $contentType
        );

        $this->database->execute(
            '
            DELETE FROM community_code_blocks
            WHERE content_type = ?
              AND content_id = ?
              AND position = ?
            ',
            [
                $contentType,
                $contentId,
                $position,
            ]
        );
    }

    public function deleteByContent(
        string $contentType,
        int $contentId
    ): void
    {
        $contentType = $this->normalizeContentType(
            $contentType
        );

        $this->database->execute(
            '
            DELETE FROM community_code_blocks
            WHERE content_type = ?
              AND content_id = ?
            ',
            [
                $contentType,
                $contentId,
            ]
        );
    }

    private function normalizeContentType(
        string $contentType
    ): string
    {
        $contentType = strtolower(
            trim($contentType)
        );

        if (!in_array(
            $contentType,
            self::CONTENT_TYPES,
            true
        )) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unsupported content type "%s".',
                    $contentType
                )
            );
        }

        return $contentType;
    }

    private function normalizeLanguage(
        string $language
    ): string
    {
        $language = strtolower(
            trim($language)
        );

        if ($language === '') {
            return 'text';
        }

        if (!preg_match('/^[a-z0-9_+#.-]{1,32}$/', $language)) {
            throw new \InvalidArgumentException(
                'Invalid code language.'
            );
        }

        return $language;
    }
}
