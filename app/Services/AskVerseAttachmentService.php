<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class AskVerseAttachmentService
{
    private const MAX_FILE_SIZE = 50 * 1024 * 1024;

    private const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/avif' => 'avif',
    ];

    private string $storagePath;

    public function __construct(
        private Database $database
    ) {
        $this->storagePath = dirname(__DIR__, 2)
            . '/storage/media/askverse';
    }

    public function validate(array $files): void
    {
        foreach ($this->normalizeFiles($files) as $file) {
            if (
                (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)
                === UPLOAD_ERR_NO_FILE
            ) {
                continue;
            }

            $this->validateUpload($file);
        }
    }

    public function store(
        int $questionId,
        ?int $answerId,
        string $authorSub,
        array $files
    ): void {
        foreach ($this->normalizeFiles($files) as $file) {
            if (
                (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)
                === UPLOAD_ERR_NO_FILE
            ) {
                continue;
            }

            $mimeType = $this->validateUpload($file);
            $uuid = Uuid::v4();

            $relativeDirectory = date('Y/m');
            $filename = $uuid . '.' . self::EXTENSIONS[$mimeType];

            $directory = $this->storagePath
                . '/' . $relativeDirectory;

            if (
                !is_dir($directory)
                && !mkdir($directory, 0775, true)
                && !is_dir($directory)
            ) {
                throw new \RuntimeException(
                    'Unable to create attachment directory.'
                );
            }

            $destination = $directory . '/' . $filename;

            if (!move_uploaded_file(
                (string) $file['tmp_name'],
                $destination
            )) {
                throw new \RuntimeException(
                    'Unable to save uploaded attachment.'
                );
            }

            $relativePath = $relativeDirectory . '/' . $filename;

            try {
                $inserted = $this->database->insert(
                    'community_askverse_attachments',
                    [
                        'uuid'          => $uuid,
                        'question_id'   => $questionId,
                        'answer_id'     => $answerId,
                        'author_sub'    => $authorSub,
                        'storage_path'  => $relativePath,
                        'original_name' => mb_substr(
                            basename((string) $file['name']),
                            0,
                            255
                        ),
                        'mime_type'     => $mimeType,
                        'file_size'     => (int) $file['size'],
                    ]
                );

                if (!$inserted) {
                    throw new \RuntimeException(
                        'Unable to register uploaded attachment.'
                    );
                }
            } catch (\Throwable $e) {
                @unlink($destination);
                throw $e;
            }
        }
    }

    public function findByQuestionId(int $questionId): array
    {
        return $this->database->fetchAll(
            'SELECT *
             FROM community_askverse_attachments
             WHERE question_id = :question_id
               AND answer_id IS NULL
             ORDER BY id ASC',
            ['question_id' => $questionId]
        );
    }

    public function findByAnswerId(int $answerId): array
    {
        return $this->database->fetchAll(
            'SELECT *
             FROM community_askverse_attachments
             WHERE answer_id = :answer_id
             ORDER BY id ASC',
            ['answer_id' => $answerId]
        );
    }

    public function findByUuid(string $uuid): array|false
    {
        return $this->database->fetchOne(
            'SELECT
                a.*,
                q.uuid AS question_uuid
             FROM community_askverse_attachments a
             INNER JOIN community_questions q
                ON q.id = a.question_id
             WHERE a.uuid = :uuid
             LIMIT 1',
            ['uuid' => $uuid]
        );
    }

    public function getAbsolutePath(array $attachment): string
    {
        return $this->storagePath
            . '/' . ltrim(
                (string) $attachment['storage_path'],
                '/'
            );
    }

    private function normalizeFiles(array $files): array
    {
        if (
            !isset($files['name'])
            || !is_array($files['name'])
        ) {
            return [];
        }

        $normalized = [];

        foreach (array_keys($files['name']) as $index) {
            $normalized[] = [
                'name'     => $files['name'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error'    => $files['error'][$index]
                    ?? UPLOAD_ERR_NO_FILE,
                'size'     => $files['size'][$index] ?? 0,
            ];
        }

        return $normalized;
    }

    private function validateUpload(array $file): string
    {
        if (
            !isset(
                $file['error'],
                $file['tmp_name'],
                $file['size']
            )
        ) {
            throw new \RuntimeException('Invalid upload.');
        }

        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(
                sprintf(
                    'Upload failed with error code %d.',
                    (int) $file['error']
                )
            );
        }

        if (!is_uploaded_file((string) $file['tmp_name'])) {
            throw new \RuntimeException(
                'Invalid uploaded file.'
            );
        }

        if ((int) $file['size'] <= 0) {
            throw new \RuntimeException(
                'The uploaded file is empty.'
            );
        }

        if ((int) $file['size'] > self::MAX_FILE_SIZE) {
            throw new \RuntimeException(
                'Il file supera la dimensione massima consentita (50 MB).'
            );
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        $mimeType = $finfo->file(
            (string) $file['tmp_name']
        );

        if (
            !is_string($mimeType)
            || $mimeType === ''
        ) {
            throw new \RuntimeException(
                'Unable to determine file type.'
            );
        }

        $mimeType = strtolower(trim($mimeType));

        if (!isset(self::EXTENSIONS[$mimeType])) {
            throw new \RuntimeException(
                sprintf(
                    'Unsupported MIME type "%s".',
                    $mimeType
                )
            );
        }

        return $mimeType;
    }
}
