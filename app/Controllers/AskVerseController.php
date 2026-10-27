<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

use Monoverse\Core\Response;
use Monoverse\Core\Session;
use Monoverse\Core\View;
use Monoverse\Services\AskVerseService;
use Monoverse\Services\AskVerseAnswerService;
use Monoverse\Services\AskVerseCommentService;
use Monoverse\Services\AskVerseVoteService;
use Monoverse\Services\NotificationService;
use Monoverse\Services\SettingsService;
use Monoverse\Services\AuthorizationService;
use Monoverse\Services\AskVerseAttachmentService;

class AskVerseController extends BaseController
{
    public function __construct(
        View $view,
        Response $response,
        Session $session,
        NotificationService $notifications,
        SettingsService $settings,
        private AskVerseService $questions,
        private AskVerseAnswerService $answers,
        private AskVerseCommentService $comments,
        private AskVerseVoteService $votes,
        private AskVerseAttachmentService $attachments,
        private AuthorizationService $authorization
    ) {
        parent::__construct(
            $view,
            $response,
            $session,
            $notifications,
            $settings
        );
    }

    public function index(): void
    {
        if (!$this->isEnabled()) {
            $this->notFound();
            return;
        }

        $filter = strtolower(
            trim((string) ($_GET['filter'] ?? 'recent'))
        );

        if (!in_array(
            $filter,
            ['recent', 'unanswered', 'solved'],
            true
        )) {
            $filter = 'recent';
        }

        $search = trim(
            (string) ($_GET['q'] ?? '')
        );

        $tag = trim(
            (string) ($_GET['tag'] ?? '')
        );

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $pageSize = 20;
        $offset = ($page - 1) * $pageSize;

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? (string) ($user['sub'] ?? '')
            : null;

        if ($currentSub === '') {
            $currentSub = null;
        }

        $questions = $this->questions->listPublished(
            $filter,
            $pageSize,
            $offset,
            $tag !== '' ? $tag : null,
            $search !== '' ? $search : null,
            $currentSub
        );

        $this->render('askverse', [
            'title' => 'AskVerse',
            'questions' => $questions,
            'filter' => $filter,
            'search' => $search,
            'tag' => $tag,
            'page' => $page,
            'pageSize' => $pageSize,
            'hasMore' => count($questions) === $pageSize,
            'isLogged' => $currentSub !== null,
        ]);
    }

    public function attachment(string $uuid): void
    {
        $attachment = $this->attachments->findByUuid($uuid);

        if (!$attachment) {
            $this->notFound();
            return;
        }

        $question = $this->questions->findByUuid(
            (string) $attachment['question_uuid']
        );

        if (!$question) {
            $this->notFound();
            return;
        }

        $path = $this->attachments->getAbsolutePath($attachment);

        if (!is_file($path) || !is_readable($path)) {
            $this->notFound();
            return;
        }

        header('Content-Type: ' . $attachment['mime_type']);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: inline; filename="attachment"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: public, max-age=86400');

        readfile($path);
        exit;
    }

    public function show(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->notFound();
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? (string) ($user['sub'] ?? '')
            : null;

        if ($currentSub === '') {
            $currentSub = null;
        }

        $question = $this->questions->findByUuid(
            $uuid,
            $currentSub
        );

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->notFound();
            return;
        }

        $questionId = (int) $question['id'];

        $questionAttachments = $this->attachments->findByQuestionId(
            $questionId
        );

        $question['score'] = $this->votes->getQuestionScore(
            $questionId
        );

        $userVote = $currentSub !== null
            ? $this->votes->getQuestionUserVote(
                $questionId,
                $currentSub
            )
            : 0;

        $sort = strtolower(
            trim((string) ($_GET['sort'] ?? 'votes'))
        );

        if (!in_array($sort, ['votes', 'newest'], true)) {
            $sort = 'votes';
        }

        $answers = $this->answers->listByQuestion(
            $questionId,
            $sort,
            $currentSub
        );

        foreach ($answers as &$answer) {
            $answer['attachments'] = $this->attachments->findByAnswerId(
                (int) $answer['id']
            );
        }
        unset($answer);

        $csrfToken = $this->session->get('askverse.csrf_token');

        if ($currentSub !== null && (!is_string($csrfToken) || $csrfToken === '')) {
            $csrfToken = bin2hex(random_bytes(32));
            $this->session->set('askverse.csrf_token', $csrfToken);
        }

        $this->render('askverse-show', [
            'title' => (string) $question['title'],
            'question' => $question,
            'questionAttachments' => $questionAttachments,
            'answers' => $answers,
            'sort' => $sort,
            'csrfToken' => $csrfToken,
            'userVote' => (int) $userVote,
            'currentSub' => $currentSub,
            'isLogged' => $currentSub !== null,
            'isAuthor' => $currentSub !== null
                && $currentSub === (string) $question['author_sub'],
            'isModerator' => is_array($user)
                && $this->authorization->isModerator($user),
        ]);
    }

    public function comments(string $uuid): void
    {
        $this->response->header(
            'Content-Type',
            'application/json; charset=utf-8'
        );

        if (!$this->isEnabled()) {
            $this->response->status(404);

            echo json_encode([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ]);

            return;
        }

        $answer = $this->answers->findByUuid($uuid);

        if (!$answer) {
            $this->response->status(404);

            echo json_encode([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_not_found'
                ),
            ]);

            return;
        }

        $question = $this->questions->findByUuid(
            (string) $answer['question_uuid']
        );

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->status(404);

            echo json_encode([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_unavailable'
                ),
            ]);

            return;
        }

        $comments = $this->comments->listByAnswer(
            (int) $answer['id']
        );

        echo json_encode([
            'success' => true,
            'comments' => $comments,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function storeComment(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $content = $_POST['content'] ?? '';

        if (!is_string($content)) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_data'
                ),
            ], 400);
            return;
        }

        $content = trim($content);

        if ($content === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.comment_required'
                ),
            ], 422);
            return;
        }

        if (mb_strlen($content) > 2000) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.comment_too_long'
                ),
            ], 422);
            return;
        }

        $answer = $this->answers->findByUuid($uuid);

        if (!$answer) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_not_found'
                ),
            ], 404);
            return;
        }

        $question = $this->questions->findByUuid(
            (string) $answer['question_uuid']
        );

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_unavailable'
                ),
            ], 404);
            return;
        }

        try {
            $commentUuid = $this->comments->create(
                $uuid,
                $currentSub,
                $content
            );

            if ($commentUuid === false) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.publish_comment_failed'
                    ),
                ], 422);
                return;
            }

            $this->response->json([
                'success' => true,
                'uuid' => $commentUuid,
            ], 201);

        } catch (\InvalidArgumentException $e) {
            $this->response->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Comment publication failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.publish_comment_failed'
                ),
            ], 500);
        }
    }

    public function updateComment(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $comment = $this->comments->findByUuid($uuid);

        if (!$comment) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.comment_not_found'
                ),
            ], 404);
            return;
        }

        if ((string) $comment['author_sub'] !== $currentSub) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.cannot_edit_comment'
                ),
            ], 403);
            return;
        }

        $content = $_POST['content'] ?? '';

        if (!is_string($content)) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_data'
                ),
            ], 400);
            return;
        }

        $content = trim($content);

        if ($content === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.comment_required'
                ),
            ], 422);
            return;
        }

        if (mb_strlen($content) > 2000) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.comment_too_long'
                ),
            ], 422);
            return;
        }

        try {
            $updated = $this->comments->update(
                $uuid,
                $currentSub,
                $content
            );

            if (!$updated) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.update_comment_failed'
                    ),
                ], 422);
                return;
            }

            $this->response->json([
                'success' => true,
                'content' => $content,
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->response->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Comment update failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.update_comment_failed'
                ),
            ], 500);
        }
    }

    public function deleteComment(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $comment = $this->comments->findByUuid($uuid);

        if (!$comment) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.comment_not_found'
                ),
            ], 404);
            return;
        }

        $isModerator = $this->authorization->isModerator($user);

        if (
            (string) $comment['author_sub'] !== $currentSub
            && !$isModerator
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.cannot_delete_comment'
                ),
            ], 403);
            return;
        }

        try {
            $deleted = $this->comments->delete(
                $uuid,
                $currentSub,
                $isModerator
            );

            if (!$deleted) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.delete_comment_failed'
                    ),
                ], 422);
                return;
            }

            $this->response->json([
                'success' => true,
            ]);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Comment deletion failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.delete_comment_failed'
                ),
            ], 500);
        }
    }

    public function create(): void
    {
        if (!$this->isEnabled()) {
            $this->notFound();
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->redirect('/oauth/login');
            return;
        }

        $csrfToken = bin2hex(random_bytes(32));

        $this->session->set(
            'askverse.csrf_token',
            $csrfToken
        );

        $this->render('askverse-create', [
            'title' => $this->translator->translate('askverse.create.title') . ' - AskVerse',
            'csrfToken' => $csrfToken,
        ]);
    }

    public function edit(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->notFound();
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->redirect('/oauth/login');
            return;
        }

        $question = $this->questions->findByUuid($uuid, $currentSub);

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
            || (string) $question['author_sub'] !== $currentSub
        ) {
            $this->notFound();
            return;
        }

        $csrfToken = bin2hex(random_bytes(32));

        $this->session->set(
            'askverse.csrf_token',
            $csrfToken
        );

        $metadata = json_decode(
            (string) ($question['metadata'] ?? ''),
            true
        );

        $codeBlock = is_array($metadata)
            ? ($metadata['code_block'] ?? null)
            : null;

        $tags = $this->questions->getTags((int) $question['id']);

        $this->render('askverse-create', [
            'title' => $this->translator->translate('askverse.create.edit_title') . ' - AskVerse',
            'csrfToken' => $csrfToken,
            'editingQuestion' => $question,
            'oldTitle' => (string) $question['title'],
            'oldContent' => (string) $question['content'],
            'oldTags' => implode(', ', $tags),
            'oldCode' => is_array($codeBlock)
                ? (string) ($codeBlock['code'] ?? '')
                : '',
            'oldCodeLanguage' => is_array($codeBlock)
                ? (string) ($codeBlock['language'] ?? 'text')
                : 'text',
        ]);
    }

    public function store(): void
    {
        if (!$this->isEnabled()) {
            $this->notFound();
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->redirect('/oauth/login');
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get(
            'askverse.csrf_token'
        );

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->text(
                $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
                403
            );
            return;
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $tagsInput = $_POST['tags'] ?? '';

        if (
            !is_string($title)
            || !is_string($content)
            || !is_string($tagsInput)
        ) {
            $this->response->text(
                $this->translator->translate(
                    'askverse.errors.invalid_data'
                ),
                400
            );
            return;
        }

        $title = trim($title);
        $content = trim($content);

        $tags = array_map(
            'trim',
            explode(',', $tagsInput)
        );

        $tags = array_values(
            array_filter(
                $tags,
                static fn (string $tag): bool => $tag !== ''
            )
        );

        try {
            $code = $_POST['code'] ?? '';
            $language = $_POST['code_language'] ?? 'text';

            if (!is_string($code) || !is_string($language)) {
                throw new \InvalidArgumentException(
                    $this->translator->translate(
                        'askverse.errors.invalid_code_block'
                    )
                );
            }

            if (mb_strlen($code) > 10000) {
                throw new \InvalidArgumentException(
                    $this->translator->translate(
                        'askverse.errors.code_block_too_long'
                    )
                );
            }

            $codeBlock = trim($code) !== ''
                ? [
                    'language' => $language,
                    'code' => $code,
                ]
                : null;


            $attachments = $_FILES['attachments'] ?? [];

            if (!is_array($attachments)) {
                throw new \InvalidArgumentException(
                    $this->translator->translate(
                        'askverse.errors.invalid_attachments'
                    )
                );
            }

            $this->attachments->validate($attachments);

            $uuid = $this->questions->create(
                $currentSub,
                $title,
                $content,
                $tags,
                $codeBlock
            );

            if ($uuid === false) {
                throw new \RuntimeException(
                    $this->translator->translate(
                        'askverse.errors.publish_question_failed'
                    )
                );
            }

            $question = $this->questions->findByUuid(
                $uuid,
                $currentSub
            );

            if (!$question) {
                throw new \RuntimeException(
                    $this->translator->translate(
                        'askverse.errors.published_question_not_found'
                    )
                );
            }

            $this->attachments->store(
                (int) $question['id'],
                null,
                $currentSub,
                $attachments
            );

            $this->session->remove(
                'askverse.csrf_token'
            );

            $this->response->redirect(
                '/askverse/' . rawurlencode($uuid),
                303
            );
            return;

        } catch (\InvalidArgumentException $e) {
            $error = $e->getMessage();

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Question publication failed: '
                . $e->getMessage()
            );

            $error = $this->translator->translate(
                'askverse.errors.publish_question_failed'
            );
        }

        $this->response->status(422);

        $this->render('askverse-create', [
            'title' => $this->translator->translate('askverse.create.title') . ' - AskVerse',
            'csrfToken' => $sessionToken,
            'error' => $error,
            'oldTitle' => $title,
            'oldContent' => $content,
            'oldTags' => $tagsInput,
            'oldCode' => is_string($code ?? null) ? $code : '',
            'oldCodeLanguage' => is_string($language ?? null) ? $language : 'text',
        ]);
    }

    public function update(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->notFound();
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->redirect('/oauth/login');
            return;
        }

        $question = $this->questions->findByUuid($uuid, $currentSub);

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
            || (string) $question['author_sub'] !== $currentSub
        ) {
            $this->notFound();
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->text(
                $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
                403
            );
            return;
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $tagsInput = $_POST['tags'] ?? '';

        if (
            !is_string($title)
            || !is_string($content)
            || !is_string($tagsInput)
        ) {
            $this->response->text(
                $this->translator->translate(
                    'askverse.errors.invalid_data'
                ),
                400
            );
            return;
        }

        $title = trim($title);
        $content = trim($content);

        $tags = array_map(
            'trim',
            explode(',', $tagsInput)
        );

        $tags = array_values(
            array_filter(
                $tags,
                static fn (string $tag): bool => $tag !== ''
            )
        );

        try {
            $code = $_POST['code'] ?? '';
            $language = $_POST['code_language'] ?? 'text';

            if (!is_string($code) || !is_string($language)) {
                throw new \InvalidArgumentException(
                    $this->translator->translate(
                        'askverse.errors.invalid_code_block'
                    )
                );
            }

            if (mb_strlen($code) > 10000) {
                throw new \InvalidArgumentException(
                    $this->translator->translate(
                        'askverse.errors.code_block_too_long'
                    )
                );
            }

            $codeBlock = trim($code) !== ''
                ? [
                    'language' => $language,
                    'code' => $code,
                ]
                : null;

            $updated = $this->questions->update(
                $uuid,
                $currentSub,
                $title,
                $content,
                $tags,
                $codeBlock
            );

            if (!$updated) {
                throw new \RuntimeException(
                    $this->translator->translate(
                        'askverse.errors.update_question_failed'
                    )
                );
            }

            $this->session->remove('askverse.csrf_token');

            $this->response->redirect(
                '/askverse/' . rawurlencode($uuid),
                303
            );
            return;

        } catch (\InvalidArgumentException $e) {
            $error = $e->getMessage();

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Question update failed: '
                . $e->getMessage()
            );

            $error = $this->translator->translate(
                'askverse.errors.update_question_failed'
            );
        }

        $this->response->status(422);

        $this->render('askverse-create', [
            'title' => $this->translator->translate('askverse.create.edit_title') . ' - AskVerse',
            'csrfToken' => $sessionToken,
            'editingQuestion' => $question,
            'error' => $error,
            'oldTitle' => $title,
            'oldContent' => $content,
            'oldTags' => $tagsInput,
            'oldCode' => is_string($code ?? null) ? $code : '',
            'oldCodeLanguage' => is_string($language ?? null)
                ? $language
                : 'text',
        ]);
    }

    public function delete(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $question = $this->questions->findByUuid($uuid, $currentSub);

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_not_found'
                ),
            ], 404);
            return;
        }

        $isModerator = $this->authorization->isModerator($user);

        if (
            (string) $question['author_sub'] !== $currentSub
            && !$isModerator
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.cannot_delete_question'
                ),
            ], 403);
            return;
        }

        try {
            $deleted = $this->questions->delete($uuid, $currentSub, $isModerator);

            if (!$deleted) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.delete_question_with_answers_failed'
                    ),
                ], 422);
                return;
            }

            $this->response->json([
                'success' => true,
                'redirect' => '/askverse',
            ]);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Question deletion failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.delete_question_failed'
                ),
            ], 500);
        }
    }

    public function storeAnswer(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $content = $_POST['content'] ?? '';
        $code = $_POST['code'] ?? '';
        $language = $_POST['code_language'] ?? 'text';

        if (
            !is_string($content)
            || !is_string($code)
            || !is_string($language)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_data'
                ),
            ], 400);
            return;
        }

        $content = trim($content);

        if ($content === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_required'
                ),
            ], 422);
            return;
        }

        if (mb_strlen($code) > 10000) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.code_block_too_long'
                ),
            ], 422);
            return;
        }

        $question = $this->questions->findByUuid($uuid);

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_not_found'
                ),
            ], 404);
            return;
        }

        $codeBlock = trim($code) !== ''
            ? [
                'language' => $language,
                'code' => $code,
            ]
            : null;

        try {
            $attachments = $_FILES['attachments'] ?? [];

            if (!is_array($attachments)) {
                throw new \InvalidArgumentException(
                    $this->translator->translate(
                        'askverse.errors.invalid_attachments'
                    )
                );
            }

            $this->attachments->validate($attachments);

            $answerUuid = $this->answers->create(
                $uuid,
                $currentSub,
                $content,
                $codeBlock
            );

            if ($answerUuid === false) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.publish_answer_failed'
                    ),
                ], 422);
                return;
            }

            $answer = $this->answers->findByUuid($answerUuid);

            if (!$answer) {
                throw new \RuntimeException(
                    $this->translator->translate(
                        'askverse.errors.published_answer_not_found'
                    )
                );
            }

            $this->attachments->store(
                (int) $question['id'],
                (int) $answer['id'],
                $currentSub,
                $attachments
            );

            $this->response->json([
                'success' => true,
                'uuid' => $answerUuid,
                'redirect' => '/askverse/' . rawurlencode($uuid)
                    . '#answer-' . rawurlencode($answerUuid),
            ], 201);

        } catch (\InvalidArgumentException $e) {
            $this->response->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Answer publication failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.publish_answer_failed'
                ),
            ], 500);
        }
    }

    public function updateAnswer(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $answer = $this->answers->findByUuid($uuid);

        if (!$answer) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_not_found'
                ),
            ], 404);
            return;
        }

        if ((string) $answer['author_sub'] !== $currentSub) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.cannot_edit_answer'
                ),
            ], 403);
            return;
        }

        $question = $this->questions->findByUuid(
            (string) $answer['question_uuid']
        );

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_unavailable'
                ),
            ], 404);
            return;
        }

        $content = $_POST['content'] ?? '';
        $code = $_POST['code'] ?? '';
        $language = $_POST['code_language'] ?? 'text';

        if (
            !is_string($content)
            || !is_string($code)
            || !is_string($language)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_data'
                ),
            ], 400);
            return;
        }

        $content = trim($content);

        if ($content === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_required'
                ),
            ], 422);
            return;
        }

        if (mb_strlen($code) > 10000) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.code_block_too_long'
                ),
            ], 422);
            return;
        }

        $codeBlock = trim($code) !== ''
            ? [
                'language' => $language,
                'code' => $code,
            ]
            : null;

        try {
            $updated = $this->answers->update(
                $uuid,
                $currentSub,
                $content,
                $codeBlock
            );

            if (!$updated) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.update_answer_failed'
                    ),
                ], 422);
                return;
            }

            $this->response->json([
                'success' => true,
                'redirect' => '/askverse/'
                    . rawurlencode((string) $question['uuid'])
                    . '#answer-' . rawurlencode($uuid),
            ]);

        } catch (\InvalidArgumentException $e) {
            $this->response->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Answer update failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.update_answer_failed'
                ),
            ], 500);
        }
    }

    public function deleteAnswer(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $answer = $this->answers->findByUuid($uuid);

        if (!$answer) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_not_found'
                ),
            ], 404);
            return;
        }

        $question = $this->questions->findByUuid(
            (string) $answer['question_uuid']
        );

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_unavailable'
                ),
            ], 404);
            return;
        }

        $isModerator = $this->authorization->isModerator($user);

        if (
            (string) $answer['author_sub'] !== $currentSub
            && !$isModerator
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.cannot_delete_answer'
                ),
            ], 403);
            return;
        }

        try {
            $deleted = $this->answers->delete(
                $uuid,
                $currentSub,
                $isModerator
            );

            if (!$deleted) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.delete_answer_failed'
                    ),
                ], 422);
                return;
            }

            $this->response->json([
                'success' => true,
                'redirect' => '/askverse/' . rawurlencode(
                    (string) $question['uuid']
                ),
            ]);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Answer deletion failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.delete_answer_failed'
                ),
            ], 500);
        }
    }

    public function voteQuestion(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $vote = filter_var(
            $_POST['vote'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!in_array($vote, [-1, 1], true)) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_vote'
                ),
            ], 400);
            return;
        }

        $question = $this->questions->findByUuid($uuid);

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_not_found'
                ),
            ], 404);
            return;
        }

        if ($currentSub === (string) $question['author_sub']) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.cannot_vote_own_question'
                ),
            ], 403);
            return;
        }

        try {
            $result = $this->votes->voteQuestion(
                $uuid,
                $currentSub,
                $vote
            );

            if (!$result) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.register_vote_failed'
                    ),
                ], 422);
                return;
            }

            $questionId = (int) $question['id'];

            $this->response->json([
                'success' => true,
                'score' => $this->votes->getQuestionScore($questionId),
                'user_vote' => $this->votes->getQuestionUserVote(
                    $questionId,
                    $currentSub
                ),
            ]);

        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Question vote failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.register_vote_failed'
                ),
            ], 500);
        }
    }

    public function voteAnswer(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $vote = filter_var(
            $_POST['vote'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!in_array($vote, [-1, 1], true)) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_vote'
                ),
            ], 400);
            return;
        }

        $answer = $this->answers->findByUuid($uuid);

        if (!$answer) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_not_found'
                ),
            ], 404);
            return;
        }

        if ($currentSub === (string) $answer['author_sub']) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.cannot_vote_own_answer'
                ),
            ], 403);
            return;
        }

        try {
            $result = $this->votes->voteAnswer(
                $uuid,
                $currentSub,
                $vote
            );

            if (!$result) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.register_vote_failed'
                    ),
                ], 422);
                return;
            }

            $answerId = (int) $answer['id'];

            $this->response->json([
                'success' => true,
                'score' => $this->votes->getAnswerScore($answerId),
                'user_vote' => $this->votes->getAnswerUserVote(
                    $answerId,
                    $currentSub
                ),
            ]);
        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Answer vote failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.register_vote_failed'
                ),
            ], 500);
        }
    }

    public function acceptAnswer(string $uuid): void
    {
        if (!$this->isEnabled()) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.not_available'
                ),
            ], 404);
            return;
        }

        $user = $this->session->get('auth.user');

        $currentSub = is_array($user)
            ? trim((string) ($user['sub'] ?? ''))
            : '';

        if ($currentSub === '') {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.authentication_required'
                ),
            ], 401);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->get('askverse.csrf_token');

        if (
            !is_string($submittedToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $submittedToken)
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.invalid_csrf'
                ),
            ], 403);
            return;
        }

        $answer = $this->answers->findByUuid($uuid);

        if (!$answer) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.answer_not_found'
                ),
            ], 404);
            return;
        }

        $question = $this->questions->findByUuid(
            (string) $answer['question_uuid']
        );

        if (
            !$question
            || ($question['status'] ?? '') !== 'published'
            || !empty($question['deleted_at'])
        ) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.question_unavailable'
                ),
            ], 404);
            return;
        }

        if ($currentSub !== (string) $question['author_sub']) {
            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.only_author_can_accept'
                ),
            ], 403);
            return;
        }

        $isAccepted = (int) ($question['accepted_answer_id'] ?? 0)
            === (int) $answer['id'];

        try {
            $result = $isAccepted
                ? $this->answers->unaccept(
                    (string) $question['uuid'],
                    $currentSub
                )
                : $this->answers->accept(
                    (string) $question['uuid'],
                    $uuid,
                    $currentSub
                );

            if (!$result) {
                $this->response->json([
                    'success' => false,
                    'error' => $this->translator->translate(
                        'askverse.errors.update_accepted_answer_failed'
                    ),
                ], 422);
                return;
            }

            $this->response->json([
                'success' => true,
                'accepted' => !$isAccepted,
                'answer_uuid' => $uuid,
                'question_uuid' => (string) $question['uuid'],
            ]);
        } catch (\Throwable $e) {
            error_log(
                '[AskVerse] Answer acceptance failed: '
                . $e->getMessage()
            );

            $this->response->json([
                'success' => false,
                'error' => $this->translator->translate(
                    'askverse.errors.update_accepted_answer_failed'
                ),
            ], 500);
        }
    }

    private function isEnabled(): bool
    {
        $value = $this->settings?->get(
            'askverse_enabled',
            '0'
        );

        return in_array(
            strtolower(trim((string) $value)),
            ['1', 'true', 'yes', 'on'],
            true
        );
    }
}
