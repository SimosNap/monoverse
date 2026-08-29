<?php

declare(strict_types=1);

return [
	'label' => 'Notifications',
	'unread_label' => 'Unread notifications: :count',
	'page' => [
		'title' => 'Notifications',
		'subtitle' => 'Replies, mentions and reactions received on your Pings.',
	
		'count' => [
			'one' => ':count notification',
			'many' => ':count notifications',
		],
	
		'delete_all' => 'Delete all',
		'delete_all_confirm' => 'Do you want to delete all notifications? This action cannot be undone.',
	
		'empty' => [
			'title' => 'No notifications',
			'text' => 'When someone replies, mentions your username or reacts to one of your Pings, the notification will appear here.',
			'action' => 'Go to Pings',
		],
	
		'actor_fallback' => 'Someone',
		'profile_of' => 'Profile of :name',
	
		'types' => [
			'default' => 'generated a notification',
			'reply' => 'replied to your Ping',
			'mention' => 'mentioned you',
			'upvote' => 'reacted to your Ping',
			'report' => 'submitted a new report',
			'doge_tip' => 'sent you a DOGE tip',
			'doge_tip_amount' => 'sent you a tip of :amount DOGE',
		],
	
		'unread' => 'Unread',
	
		'actions' => [
			'open' => 'Open',
			'open_aria' => 'Open notification',
			'delete' => 'Delete',
			'delete_aria' => 'Delete notification',
		],
	],
];
