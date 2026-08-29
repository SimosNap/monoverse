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
		'assigned' => 'Assigned',
		'milestone' => 'Milestone',
		'reason' => 'Reason',
		'locked' => 'Locked',
		'accepted_answer' => 'Accepted answer',
		'target' => 'Target',
		'assets' => 'Assets',
		'run' => 'Run',
		'attempt' => 'Attempt',
		'updated' => 'Updated',
	],

	'counts' => [
		'file' => 'file',
		'files' => 'files',
		'comment' => 'comment',
		'comments' => 'comments',
		'commit' => 'commit',
		'commits' => 'commits',
		'element' => 'item',
		'elements' => 'items',
		'directory' => 'directory',
		'directories' => 'directories',
		'ahead' => 'ahead',
		'behind' => 'behind',
	],

	'links' => [
		'view_answer' => 'View answer on GitHub',
		'open_gist' => 'Open Gist on GitHub',
		'open_workflow' => 'Open workflow run on GitHub',
		'view_comment' => 'View comment on GitHub',
	],

	'preview' => [
		'file_truncated' => 'File truncated in preview',
	],
];
