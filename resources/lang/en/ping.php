<?php

declare(strict_types=1);

return [
	'areas' => [
		'before' => 'Content before Pings',
		'after' => 'Content after Pings',
		'sidebar' => 'Sidebar content',
	],

	'feed' => [
		'label' => 'Filter Pings',
		'all' => 'All Pings',
		'following' => 'Following',
		'interactions' => 'Interactions',
		'audio' => 'Audio',
		'video' => 'Video',
		'empty' => 'There are no Pings yet.',
	],

	'search' => [
		'label' => 'Search Pings',
		'placeholder' => 'Search...',
		'submit' => 'Search',
	],

	'sidebar' => [
		'explore' => 'Explore',
	],

	'report' => [
		'title' => 'Report content',
		'close' => 'Close',
		'help' => 'Why are you reporting this content?',

		'reasons' => [
			'spam' => 'Spam',
			'harassment' => 'Harassment or insults',
			'privacy' => 'Personal information',
			'illegal' => 'Illegal content',
			'copyright' => 'Copyright',
			'other' => 'Other',
		],

		'description' => 'Description',
		'description_placeholder' => 'Provide some useful details for the moderators...',

		'cancel' => 'Cancel',
		'submit' => 'Submit report',
	],
	'composer' => [
		'placeholder' => 'What are you thinking about?',
		'publish' => 'Publish',
		'media' => [
			'attachments' => 'Attachments',
			'attach_files' => 'Attach files',
			'close' => 'Close attachments',

			'allowed' => 'Allowed:',
			'images' => 'images',
			'pdf' => 'PDF',
			'audio_up_to' => 'audio up to',
			'video_up_to' => 'video up to',

			'dropzone_title' => 'Drop files here',
			'dropzone_help' => 'or click to select them',

			'audio' => [
				'details' => 'Audio details',
				'optional' => 'Optional',
				'title' => 'Title',
				'title_placeholder' => 'Track or mix title',
				'artist' => 'Artist / author',
				'artist_placeholder' => 'Artist, DJ or author',
				'tracklist' => 'Tracklist',
				'tracklist_placeholder' => "00:00 Artist – Track\n04:32 Artist – Track",
			],

			'upload' => [
				'uploading' => 'Uploading…',
				'progress' => 'Upload progress',
			],
		],
	],
	'card' => [
		'user' => 'User',

		'author' => [
			'unavailable' => 'User no longer available',
			'private_profile' => 'Has chosen not to have a public profile',
			'account_missing' => 'SimosNap account no longer exists',
		],

		'doge' => [
			'tip_label' => 'Dogecoin tip',
			'tip' => 'Tip',
		],

		'editor' => [
			'save' => 'Save',
			'cancel' => 'Cancel',
		],

		'comments' => 'Pong',

		'actions' => [
			'more' => 'More actions',
			'save' => 'Save',
			'unsave' => 'Remove from saved',
			'edit' => 'Edit',
			'block_user' => 'Block user',
			'report' => 'Report',
			'delete' => 'Delete',
			'delete_confirm' => 'Are you sure you want to delete this Ping?',
			'upvote' => 'Upvote',
			'downvote' => 'Downvote',
		],

		'link' => [
			'video' => 'VIDEO',
			'audio' => 'MUSIC',
			'default' => 'LINK',
		],

		'media' => [
			'video_unsupported' => 'Your browser does not support video playback.',
			'audio_unsupported' => 'Your browser does not support audio playback.',

			'audio_file' => 'Audio file',
			'audio' => 'Audio',
			'waveform_unavailable' => 'Waveform unavailable',

			'play' => 'Play',
			'mute' => 'Mute audio',
			'volume' => 'Volume',
			'tracklist' => 'Tracklist',
		],
	],

	'pong' => [
		'user' => 'User',

		'author' => [
			'unavailable' => 'User no longer available',
			'private_profile' => 'Has chosen not to have a public profile',
			'account_missing' => 'SimosNap account no longer exists',
		],

		'doge' => [
			'tip_label' => 'Dogecoin tip',
		],

		'editor' => [
			'save' => 'Save',
			'cancel' => 'Cancel',
		],

		'actions' => [
			'edit' => 'Edit',
			'delete' => 'Delete',
			'report' => 'Report',
			'block_user' => 'Block user',
			'delete_confirm' => 'Are you sure you want to delete this Pong?',
			'block_confirm' => 'Do you want to block this user? You will no longer see their Pings and Pongs.',
		],
	],

	'show' => [
		'areas' => [
			'before' => 'Content before the Ping',
			'after' => 'Content after the Ping',
			'sidebar' => 'Sidebar content',
		],

		'blocked' => [
			'title' => 'Content unavailable',
		],

		'pong' => [
			'placeholder' => 'Write a Pong...',
			'publish' => 'Publish Pong',
			'title' => 'Pong',
			'empty' => 'No Pongs yet.',
		],
	],

	'js' => [
		'upload' => [
			'processing' => 'Processing…',
			'uploading' => 'Uploading…',
			'failed' => 'Upload failed',
		],

		'lightbox' => [
			'previous_image' => 'Previous image',
			'next_image' => 'Next image',
		],

		'audio' => [
			'pause' => 'Pause',
			'play' => 'Play',
			'unmute' => 'Unmute audio',
			'mute' => 'Mute audio',
		],

		'attachments' => [
			'label' => 'Attachments',
			'one' => '1 attachment',
			'count' => ':count attachments',
			'remove' => 'Remove :file',

			'audio_not_allowed' => 'Audio attachments are not allowed.',
			'video_not_allowed' => 'Video attachments are not allowed.',

			'audio_too_large' => 'The audio file exceeds the :max MB limit.',
			'video_too_large' => 'The video file exceeds the :max MB limit.',
		],
	],

	'flash' => [
		'text_required' => 'Write some text before publishing the Ping.',
	],
];
