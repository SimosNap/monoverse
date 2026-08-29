<?php

declare(strict_types=1);

return [
	'main' => [
		'header' => [
			'title' => 'Account preferences',
			'subtitle' => 'Manage your profile, privacy and chat preferences.',
		],
	
		'roles' => [
			'moderator_title' => 'Community moderator',
			'moderator_text' => 'Your account is authorized to access the community moderation tools.',
			'panel_title' => 'Community roles',
		],
	
		'following' => [
			'empty' => 'You are not following anyone yet.',
			'confirm_unfollow' => 'Do you want to stop following this user?',
			'unfollow' => 'Unfollow',
			'panel_title' => 'Following (:count)',
		],
	
		'chat' => [
			'panel_title' => 'Chat preferences',
			'nickname' => 'Nickname',
			'age' => 'Age',
			'profile' => 'Profile',
			'not_specified' => 'Not specified',
			'male' => 'Man',
			'female' => 'Woman',
			'other' => 'Other',
			'city' => 'City',
		],
	
		'storage' => [
			'panel_title' => 'Where to save',
	
			'browser' => [
				'title' => 'Browser only',
				'subtitle' => 'Maximum privacy',
			],
	
			'database' => [
				'title' => 'Site database',
				'subtitle' => 'Across all devices',
			],
		],
	
		'public_profile' => [
			'panel_title' => 'Public profile',
			'enabled' => 'I want a public profile on this site',
			'nickname' => 'Nickname',
			'avatar' => 'Avatar',
			'aliases' => 'Account aliases',
			'age' => 'Age',
			'city' => 'City',
			'sex' => 'Sex',
			'irc_stats' => 'IRC statistics',
	
			'indexing' => [
				'title' => 'Search engine indexing',
				'help' => 'Allow Google, Bing and other search engines to show your profile in search results.',
			],
		],
	
		'doge' => [
			'panel_title' => 'Dogecoin tips',
			'intro' => 'Choose where Monoverse should get your Dogecoin address from to receive tips from other users.',
	
			'mydogemask' => [
				'title' => 'MyDogeMask',
				'help' => 'Use the MyDogeMask wallet address connected to this browser.',
			],
	
			'simosnap' => [
				'title' => 'SimosNap account',
				'help' => 'Use the Dogecoin address configured on your SimosNap account.',
				'address_configured' => 'Dogecoin address configured on SimosNap:',
				'address_missing' => 'No Dogecoin address is configured on your SimosNap account.',
			],
	
			'connect' => 'Connect MyDogeMask',
			'save' => 'Save preference',
		],
	
		'privacy' => [
			'panel_title' => 'Privacy',
			'help' => 'Manage the users you have blocked and restore access to their profiles whenever you want.',
			'blocked_users' => 'Blocked users',
		],
	
		'actions' => [
			'save_preferences' => 'Save preferences',
			'clear_browser' => 'Delete browser data',
			'delete_database' => 'Delete database data',
			'delete_database_confirm' => 'Permanently delete the data saved in the database?',
			'logout' => 'Log out',
		],
		
		'js' => [
			'local_saved' => 'Preferences saved in this browser.',
			'local_cleared' => 'Browser data deleted.',
		
			'doge' => [
				'connecting' => 'Connecting to MyDogeMask…',
				'connected_address' => 'MyDogeMask connected. Address: :address',
				'connect_failed' => 'Unable to connect MyDogeMask.',
				'use_simosnap_address' => 'The Dogecoin address configured on your SimosNap account will be used.',
				'wallet_connected_address' => 'MyDogeMask connected. Address: :address',
				'configured_address' => 'Configured MyDogeMask address: :address',
				'wallet_detected' => 'MyDogeMask detected. Connect the wallet to use its address.',
			],
		],
	],
	
	'profile' => [
		'user' => 'User',
	
		'saved' => [
			'title' => 'Profile saved.',
			'text' => 'Your changes have been saved successfully.',
		],
	
		'about' => [
			'kicker' => 'About me',
			'title' => 'Introduce yourself to the community',
		],
	
		'bio' => [
			'label' => 'Bio',
			'placeholder' => 'Write something about yourself...',
			'max' => 'Maximum 1000 characters',
			'preview_empty' => 'Your bio will appear here.',
		],
	
		'motto' => [
			'label' => 'Personal motto',
			'placeholder' => 'A short phrase that represents you',
			'optional' => 'Optional',
			'max' => 'Maximum 120 characters',
		],
	
		'interests' => [
			'kicker' => 'Interests',
			'title' => 'What do you like talking about?',
	
			'items' => [
				'Music' => 'Music',
				'Cinema' => 'Cinema',
				'TV Series' => 'TV Series',
				'Gaming' => 'Gaming',
				'Anime' => 'Anime',
				'Sport' => 'Sport',
				'Books' => 'Books',
				'Technology' => 'Technology',
				'IRC' => 'IRC',
				'Linux' => 'Linux',
				'Mac' => 'Mac',
				'Windows' => 'Windows',
				'Radio' => 'Radio',
				'Podcast' => 'Podcast',
				'Travel' => 'Travel',
				'Photography' => 'Photography',
				'Cooking' => 'Cooking',
				'Cars and motorcycles' => 'Cars and motorcycles',
			],
		],
	
		'links' => [
			'kicker' => 'Links',
			'title' => 'Where to find you',
			'website' => 'Website',
			'telegram' => 'Telegram',
		],
	
		'save' => [
			'title' => 'Save profile',
			'help' => 'Your changes will be visible after saving.',
			'button' => 'Save profile',
		],
	
		'preview' => [
			'title' => 'Preview',
			'aliases' => 'Aliases',
		],
	],
	
	'navigation' => [
		'aria_label' => 'Account navigation',
		'preferences' => 'Chat preferences',
		'profile' => 'Public profile',
		'saved' => 'Saved content',
		'articles' => 'Submitted articles',
		'privacy' => 'Privacy',
		'moderation' => 'Moderation',
		'logout' => 'Log out',
	],
	
	'moderation' => [
		'title' => 'Moderation',
		'subtitle' => 'Manage reports and active moderation actions in the community.',
	
		'reports' => [
			'title' => 'Reports',
			'text' => 'Review reports submitted by users and take action on content.',
			'open' => 'Open reports',
		],
	
		'bans' => [
			'title' => 'Suspended users',
			'text' => 'Manage users who cannot use community features.',
			'open' => 'Open suspended users',
		],
	
		'mutes' => [
			'title' => 'Muted users',
			'text' => 'Manage users who cannot publish Pings or Pongs.',
			'open' => 'Open muted users',
		],
	],
	
	'moderation_navigation' => [
		'aria_label' => 'Moderation navigation',
		'dashboard' => 'Dashboard',
		'reports' => 'Reports',
		'bans' => 'Suspended',
		'mutes' => 'Muted',
	],
	
	'moderation_reports' => [
		'title' => 'Reports',
		'subtitle' => 'Content reported by users and awaiting review.',
	
		'section_title' => 'Received reports',
	
		'count' => [
			'one' => 'There is one report.',
			'many' => 'There are :count reports.',
		],
	
		'empty' => [
			'title' => 'No reports',
			'text' => 'There is currently no content to review.',
		],
	
		'reasons' => [
			'spam' => 'Spam',
			'harassment' => 'Harassment',
			'hate' => 'Offensive content',
			'violence' => 'Violence or threats',
			'sexual' => 'Sexual content',
			'misinformation' => 'False information',
			'privacy' => 'Privacy violation',
			'other' => 'Other',
		],
	
		'status' => [
			'open' => 'Open',
			'reviewed' => 'Reviewed',
			'closed' => 'Closed',
		],
	
		'target' => [
			'ping' => 'Ping',
			'pong' => 'Pong',
			'content' => 'Content',
		],
	
		'reported_by' => 'Reported by',
		'unknown_user' => 'unknown user',
		'date_unavailable' => 'Date unavailable',
		'id' => 'ID',
	
		'no_description' => 'No additional description provided.',
		'open_report' => 'Open report',
	],
	
	'moderation_report' => [
		'title' => 'Report details',
		'subtitle' => 'View the reported content and report information.',
	
		'status' => 'Status:',
		'reported_by' => 'Reported by:',
		'reviewed_by' => 'Handled by:',
		'reported_content' => 'Reported content',
	
		'status_labels' => [
			'open' => 'Open',
			'reviewed' => 'Reviewed',
			'closed' => 'Closed',
		],
	
		'reasons' => [
			'spam' => 'Spam',
			'harassment' => 'Harassment',
			'privacy' => 'Privacy violation',
			'illegal' => 'Illegal content',
			'copyright' => 'Copyright',
			'other' => 'Other',
		],
	
		'actions' => [
			'mark_reviewed' => 'Mark as reviewed',
			'close' => 'Close report',
			'delete_content' => 'Delete content',
			'delete_confirm' => 'Are you sure you want to permanently delete the reported content?',
		],
	],
	
	'moderation_mutes' => [
		'title' => 'Muted users',
		'subtitle' => 'Manage users who cannot publish Pings or Pongs.',
	
		'section' => [
			'title' => 'Active mutes',
			'help' => 'Muted users can sign in, but they cannot publish Pings or Pongs.',
		],
	
		'empty' => [
			'title' => 'No muted users',
			'text' => 'There are currently no active mutes.',
		],
	
		'user' => [
			'unavailable' => 'Profile unavailable',
			'deleted_or_missing' => 'Profile deleted or not yet created',
			'public_unavailable' => 'Public profile unavailable.',
			'avatar_alt' => 'Avatar of :name',
		],
	
		'status' => 'Muted',
	
		'meta' => [
			'reason' => 'Reason',
			'no_reason' => 'No reason provided',
			'duration' => 'Duration',
			'permanent' => 'Permanent',
			'applied_on' => 'Applied on',
			'moderator' => 'Moderator',
		],
	
		'actions' => [
			'profile' => 'Profile',
			'unmute' => 'Remove mute',
			'unmute_confirm' => 'Remove this mute?',
		],
	],
	
	'moderation_bans' => [
		'title' => 'Suspended users',
		'subtitle' => 'Manage users who cannot use community features.',
	
		'section' => [
			'title' => 'Active suspensions',
			'help' => 'Suspended users cannot use community features.',
		],
	
		'empty' => [
			'title' => 'No suspended users',
			'text' => 'There are currently no active suspensions.',
		],
	
		'user' => [
			'unavailable' => 'Profile unavailable',
			'deleted_or_missing' => 'Profile deleted or not yet created',
			'public_unavailable' => 'Public profile unavailable.',
			'avatar_alt' => 'Avatar of :name',
		],
	
		'status' => 'Suspended',
	
		'meta' => [
			'reason' => 'Reason',
			'no_reason' => 'No reason provided',
			'duration' => 'Duration',
			'permanent' => 'Permanent',
			'applied_on' => 'Applied on',
			'moderator' => 'Moderator',
		],
	
		'actions' => [
			'profile' => 'Profile',
			'unban' => 'Reactivate',
			'unban_confirm' => 'Reactivate this user?',
		],
	],
	
	'articles' => [
		'title' => 'Submitted articles',
		'subtitle' => 'Check the status of the articles you submitted to Chanzine.',
		'submit' => 'Submit an article',
	
		'empty' => 'You have not submitted any articles yet.',
	
		'status' => [
			'submitted' => 'Under review',
			'published' => 'Published',
			'rejected' => 'Rejected',
		],
	
		'submitted_on' => 'Submitted on',
	
		'actions' => [
			'edit' => 'Edit',
			'view' => 'View article',
		],
	
		'rejection' => [
			'title' => 'Reason for rejection',
		],
	],
	
	'article_edit' => [
		'eyebrow' => 'Chanzine',
		'title' => 'Edit proposal',
		'intro' => 'You can edit this proposal while it is still awaiting review by the administrator.',
	
		'article' => [
			'title' => 'Article',
			'help' => 'Edit the title, introduction and content of the proposal.',
		],
	
		'fields' => [
			'title' => 'Title',
			'excerpt' => 'Excerpt',
			'excerpt_help' => 'A short introduction that will accompany the article.',
			'content' => 'Content',
			'content_help' => 'Edit the article content in Markdown.',
			'category' => 'Category',
			'category_placeholder' => 'Select a category',
			'cover' => 'Cover',
			'cover_replace_help' => 'Upload a new image only if you want to replace the current cover.',
			'cover_default_help' => 'JPEG, PNG or WebP. If you do not upload one, the default Chanzine cover will be used.',
		],
	
		'settings' => [
			'title' => 'Settings',
			'help' => 'Update the proposal information.',
		],
	
		'save' => [
			'title' => 'Save changes',
			'help' => 'The proposal will remain pending review.',
			'cancel' => 'Cancel',
			'submit' => 'Save changes',
		],
	],
	
	'saved' => [
		'title' => 'Saved content',
		'subtitle' => 'Find the Pings and articles you have saved.',
		'empty' => 'You have not saved any content yet.',
		'card' => [
			'article' => 'Article',
			'ping' => 'Ping',
			'empty_ping' => 'Ping without content',
			'saved' => 'Saved',
			'remove' => 'Remove',
			'remove_confirm' => 'Remove this content from your saved items?',
		],
	],
	
	'suspended' => [
		'title' => 'Account suspended',
		'subtitle' => 'Access to the Community has been suspended.',

		'intro' => 'Your account cannot currently access Community features because it has been suspended by a moderator.',

		'status' => 'Status',
		'status_value' => 'Suspended',

		'reason' => 'Reason',
		'reason_unspecified' => 'Not specified.',

		'duration' => 'Duration',
		'until' => 'Until',
		'permanent' => 'Permanent',

		'appeal' => 'If you believe this action was applied by mistake, you can contact the staff to request a review.',

		'leave' => [
			'title' => 'Leave the Community',
			'intro' => 'If you no longer wish to be part of the Community, you can permanently delete your public profile.',

			'profile_deleted' => 'Your public profile will be deleted.',
			'removed_from_members' => 'You will no longer appear among Community members.',
			'suspension_retained' => 'The suspension will remain associated with your OAuth account.',

			'confirm' => 'Permanently delete the public profile?',
			'delete' => 'Delete my public profile',
		],

		'logout' => 'Log out',
	],
	'blocked' => [
		'title' => 'Privacy',
		'subtitle' => 'Manage blocked users and your account privacy settings.',
	
		'section_title' => 'Blocked users',
		'section_help' => 'Blocked users cannot interact with you. They will not see your Pings and you will not see theirs.',
	
		'empty_title' => 'No blocked users',
		'empty_text' => 'Your list is empty. Users you block will appear here.',
	
		'user_unavailable' => 'User no longer available',
		'account_missing' => 'SimosNap account no longer exists',
	
		'blocked_on' => 'Blocked on',
		'unblock' => 'Unblock',
	],
];
