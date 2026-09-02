<?php
declare(strict_types=1);

$codeBlock = is_array($codeBlock ?? null)
    ? $codeBlock
    : [];

$code = (string) ($codeBlock['code'] ?? '');

$language = trim(
    (string) ($codeBlock['language'] ?? 'text')
);

if ($language === '') {
    $language = 'text';
}

if ($code === '') {
    return;
}
?>

<div
    class="content-code-block"
    data-code-block
>
    <div class="content-code-header">

        <span class="content-code-language">
            <?= htmlspecialchars(
                strtoupper($language),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </span>

    </div>

    <pre class="content-code-pre"><code
            class="language-<?= htmlspecialchars(
                $language,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        ><?= htmlspecialchars(
            $code,
            ENT_QUOTES,
            'UTF-8'
        ) ?></code></pre>
</div>
