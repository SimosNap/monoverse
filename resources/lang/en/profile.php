<?php

declare(strict_types=1);

return [
	'user' => 'User',

	'roles' => [
		'moderator' => 'Moderator',
	],

	'shortcuts' => [
		'title' => 'Your account',
		'account' => 'Account panel',
		'edit_profile' => 'Edit profile',
		'saved' => 'Saved content',
		'blocked' => 'Blocked users',
		'moderation' => 'Moderation',
	],

	'sex' => [
		'male' => 'Man',
		'female' => 'Woman',
		'other' => 'Other',
	],

	'blocked' => [
		'you_blocked_title' => 'You blocked this user',
		'you_blocked_text' => 'You will no longer see their Pings or be able to interact with them until you remove the block from your Account area.',

		'blocked_by_title' => 'Limited interactions',
		'blocked_by_text' => 'This user has chosen not to interact with you. Some profile features are unavailable.',
	],

	'areas' => [
		'before' => 'Content before the profile',
		'sidebar' => 'Profile sidebar content',
		'after' => 'Content after the profile',
	],

	'pings' => [
		'empty_title' => 'No Pings published',
		'empty_text' => 'This user has not published any content yet.',
		'filters_label' => 'Filter profile Pings',

		'filters' => [
			'all' => 'All Pings',
			'audio' => 'Audio',
			'video' => 'Video',
			'interactions' => 'Interactions',
		],
	],

	'sidebar' => [
		'profile_details' => 'Profile and details',
	],

	'details' => [
		'years' => 'years old',
		'followers' => 'Followers',
		'following' => 'Following',
		'pings' => 'Pings',
	],

	'actions' => [
		'send_doge' => 'Send DOGE',
		'follow' => 'Follow',
		'unfollow' => 'Unfollow',
		'block' => 'Block',
		'block_confirm' => 'Are you sure you want to block this user?',
	],

	'irc' => [
		'title' => 'IRC statistics',
		'connected' => 'Connected',
		'not_connected' => 'Not connected',
		'role' => 'Role',
		'operator' => 'IRC operator',
		'channels' => 'Channels',
		'messages' => 'Messages',
		'words' => 'Words',
		'characters' => 'Characters',
	],

	'sections' => [
		'interests' => 'Interests',
		'aliases' => 'Aliases',
		'links' => 'Links',
		'website' => 'Website',
		'telegram' => 'Telegram',
	],

	'moderation' => [
		'title' => 'Moderation',
		'clear' => 'Clear',

		'muted_title' => 'User muted',
		'muted_text' => 'They cannot publish new Pings or Pongs.',

		'banned_title' => 'User suspended',
		'banned_text' => 'They cannot access community features.',

		'reason' => 'Reason:',
		'reactivate' => 'Reactivate',

		'mute_user' => 'Mute user',
		'mute' => 'Mute',
		'ban_user' => 'Suspend user',
		'ban' => 'Suspend',

		'modal_title' => 'Moderation',
		'close' => 'Close',
		'reason_label' => 'Reason',
		'reason_placeholder' => 'Enter the reason...',
		'duration' => 'Duration',

		'duration_options' => [
			'permanent' => 'Permanent',
			'15_minutes' => '15 minutes',
			'30_minutes' => '30 minutes',
			'1_hour' => '1 hour',
			'6_hours' => '6 hours',
			'12_hours' => '12 hours',
			'1_day' => '1 day',
			'3_days' => '3 days',
			'7_days' => '7 days',
			'30_days' => '30 days',
		],

		'cancel' => 'Cancel',
		'confirm' => 'Confirm',
	],
];
