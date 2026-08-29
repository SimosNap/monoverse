<?php
declare(strict_types=1);

$report = is_array($report ?? null)
	? $report
	: [];

$content = is_array($content ?? null)
	? $content
	: [];

if ($report === [] || $content === []) {
	return;
}

$moderationMode = true;
$targetType = (string) ($report['target_type'] ?? '');

if ($targetType === 'ping') {
	$post = $content;

	require __DIR__ . '/ping-card.php';

	return;
}

if ($targetType === 'pong') {
	$comment = $content;

	require __DIR__ . '/pong-card.php';

	return;
}
