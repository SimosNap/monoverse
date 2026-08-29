<?php

declare(strict_types=1);

return [
	'kind' => [
		'code' => 'CODE',
		'commit' => 'COMMIT',
		'pull_request' => 'PULL REQUEST',
		'issue' => 'ISSUE',
		'discussion' => 'DISCUSSION',
		'release' => 'RELEASE',
		'gist' => 'GIST',
		'tree' => 'TREE',
		'compare' => 'COMPARE',
		'actions' => 'ACTIONS',
		'repository' => 'REPOSITORY',
		'comment' => 'COMMENT',
	],

	'state' => [
		'open' => 'OPEN',
		'closed' => 'CLOSED',
		'draft' => 'DRAFT',
		'merged' => 'MERGED',
		'prerelease' => 'PRERELEASE',
		'stable' => 'STABLE',
		'public' => 'PUBLIC',
		'secret' => 'SECRET',
	],

	'labels' => [
		'assigned' => 'Assegnato',
		'milestone' => 'Milestone',
		'reason' => 'Motivo',
		'locked' => 'Bloccata',
		'accepted_answer' => 'Risposta accettata',
		'target' => 'Target',
		'assets' => 'Asset',
		'run' => 'Run',
		'attempt' => 'Tentativo',
		'updated' => 'Aggiornato',
	],

	'counts' => [
		'file' => 'file',
		'files' => 'file',
		'comment' => 'commento',
		'comments' => 'commenti',
		'commit' => 'commit',
		'commits' => 'commit',
		'element' => 'elemento',
		'elements' => 'elementi',
		'directory' => 'directory',
		'directories' => 'directory',
		'ahead' => 'avanti',
		'behind' => 'indietro',
	],

	'links' => [
		'view_answer' => 'Vedi risposta su GitHub',
		'open_gist' => 'Apri Gist su GitHub',
		'open_workflow' => 'Apri workflow run su GitHub',
		'view_comment' => 'Vedi commento su GitHub',
	],

	'preview' => [
		'file_truncated' => 'File troncato nella preview',
	],
];
