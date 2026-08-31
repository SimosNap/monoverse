<?php
declare(strict_types=1);

return [
	'community' => [
		'latest_members' => [
			'default_title' => 'New members',
			'joined' => 'Joined',
			'empty' => 'No registered members.',
		],
		'users_in_chat' => [
			'default_title' => 'Now in chat',
			'total' => 'nicknames in chat',
			'empty' => 'No nicknames are in chat right now.',
		],
		'most_active_users' => [
			'default_title' => 'Most active users',
			'irc' => 'Chat messages in the last 30 days',
			'pings' => 'Pings published in the last 30 days',
			'pongs' => 'Pongs published in the last 30 days',
			'upvotes' => 'Upvotes given in the last 30 days',
			'downvotes' => 'Downvotes given in the last 30 days',

			'vote' => 'vote',
			'votes' => 'votes',

			'irc_message' => 'IRC message',
			'irc_messages' => 'IRC messages',
			'irc_inactive' => 'IRC inactive',

			'web_inactive' => 'No web activity',

			'empty' => 'No activity available.',
		],
	],
	'content' => [
		'categories' => [
			'default_title' => 'Categories',
			'empty' => 'No categories available.',
		],
		'latest_articles' => [
			'default_title' => 'Latest articles',
			'published' => 'Published',
			'empty' => 'No articles published.',
		],
		'latest_audio' => [
			'empty' => 'No audio shared.',
			'file_fallback' => 'Audio file',
			'all' => 'All audio',
		],
		'latest_video' => [
			'empty' => 'No video shared.',
			'file_fallback' => 'Video',
			'all' => 'All videos',
		],
		'pages_navigation' => [
			'default_title' => 'Pages',
			'empty' => 'No pages available.',
		],
		'submit_article' => [
			'default_title' => 'Submit an article',
			'default_button' => 'Submit an article',
		],
	],
	'developer' => [
		'github_pull_requests' => [
			'state' => [
				'open' => 'Open',
				'closed' => 'Closed',
				'all' => 'All',
			],
			'empty' => 'No pull requests available.',
			'view_all' => 'View all Pull Requests',
			'admin' => [
				'label' => 'GitHub Pull Requests',
				'description' => 'Shows the most recent pull requests from a GitHub repository.',
				'repository_error' => 'Enter a valid GitHub repository.',
			],
		],
		'github_release' => [
			'default_title' => 'Latest releases',

			'state' => [
				'stable' => 'Release',
				'beta' => 'Beta',
				'nightly' => 'Nightly',
			],

			'view' => 'View release',
			'empty' => 'No releases available.',

			'admin' => [
				'label' => 'GitHub Release',
				'description' => 'Shows the stable release, beta and nightly build of a GitHub repository.',
				'repository' => 'GitHub repository',
				'repository_help' => 'Format: owner/repository',
				'title' => 'Title',
				'title_placeholder' => 'Latest releases',
				'show_repository' => 'Show repository',
				'show_date' => 'Show publication date',
				'repository_error' => 'Enter a valid GitHub repository.',
			],
		],
		'github_repository' => [
			'default_title' => 'GitHub Repository',
			'unavailable' => 'GitHub repository not configured or unavailable.',
			'branch' => 'Branch',

			'stats' => [
				'stars' => 'Stars',
				'forks' => 'Forks',
				'watchers' => 'Watchers',
				'issues' => 'Issues',
			],

			'sections' => [
				'languages' => 'Languages',
				'latest_release' => 'Latest release',
				'latest_commits' => 'Latest commits',
				'open_pull_requests' => 'Open pull requests',
				'open_issues' => 'Open issues',
			],

			'files' => [
				'one' => ':count file',
				'many' => ':count files',
			],

			'admin' => [
				'label' => 'GitHub Repository',
				'description' => 'Shows a complete GitHub repository dashboard with activity, commits, releases, issues and pull requests.',
				'repository_error' => 'Enter a valid GitHub repository.',
			],
		],
	],
	'webradio' => [
		'azuracast_mini_player' => [
			'default_title' => 'Listen to the radio',
			'track_unavailable' => 'Track unavailable',
			'play' => 'Play radio',
			'detach' => 'Open the player in a separate window',
			'js' => [
				'pause' => 'Pause radio',
				'play' => 'Play radio',
				'playing' => 'Playing',
				'paused' => 'Paused',
				'unavailable' => 'Playback unavailable',
				'error' => 'Playback error',
				'ready' => 'Ready',
			],
		],
		'azuracast' => [
			'default_title' => 'Listen to the radio',
			'track_unavailable' => 'Track unavailable',
			'play' => 'Play radio',
			'detach' => 'Open the player in a separate window',
			'detached' => 'Player opened in a separate window',
			'on_air' => 'On Air',
			'now_playing' => 'Now playing',
			'ready' => 'Ready',
			'mute' => 'Mute audio',
			'volume' => 'Volume',
			'stream_unavailable' => 'Stream temporarily unavailable.',
			'history_title' => 'Recently played',
			'radio_unavailable' => 'Radio temporarily unavailable.',
			'pause' => 'Pause radio',
			'playing' => 'Playing',
			'paused' => 'Paused',
			'unmute' => 'Unmute audio',
			'connecting' => 'Connecting…',
			'start_failed' => 'Unable to start the radio',
			'slow_connection' => 'Slow connection…',
			'player_unavailable' => 'Radio unavailable',
		],
		'azuracast_requests' => [
			'default_title' => 'Request a song',
			'unavailable_default' => 'Music requests are not available right now.',
			'kicker' => 'Song request',
			'active' => 'Requests active',
			'unavailable_title' => 'Requests unavailable',
			'empty' => 'No songs are currently available for requests.',

			'search' => [
				'placeholder' => 'Search artist, title, album or genre',
				'aria_label' => 'Search for a song',
				'clear' => 'Clear search',
				'no_results' => 'No songs match your search.',
			],

			'counter' => [
				'one' => ':count song available',
				'many' => ':count songs available',
			],

			'untitled' => 'Untitled track',
			'request' => 'Request',

			'js' => [
				'no_results' => 'No songs found',
				'sent' => 'Request sent successfully.',
				'failed' => 'Unable to send the request.',
				'sent_label' => 'Sent',
				'connection_error' => 'Connection error while sending the request.',
				'pagination_label' => 'Available songs pagination',
				'previous_page' => 'Previous page',
				'next_page' => 'Next page',
				'results_count' => ':start–:end of :total songs',
				'identify_failed' => 'Unable to identify the requested song.',
				'sending' => 'Sending...',
				'sending_status' => 'Sending request...',
			],
		],
		'azuracast_stats' => [
			'default_title' => 'Radio statistics',
			'stream_fallback' => 'Stream',
			'kicker' => 'Radio monitor',

			'status' => [
				'online' => 'Online',
				'offline' => 'Offline',
			],

			'unavailable' => [
				'title' => 'Statistics unavailable',
				'text' => 'Unable to retrieve station data.',
			],

			'stats' => [
				'current_listeners' => 'Current listeners',
				'unique_listeners' => 'Unique listeners',
				'bitrate' => 'Bitrate',
				'codec' => 'Codec',
			],

			'mounts' => [
				'title' => 'Active mounts',
				'empty' => 'No mounts available.',
				'listeners' => [
					'one' => ':count listener',
					'many' => ':count listeners',
				],
			],
		],
		'icecast_stats' => [
			'default_title' => 'Radio statistics',
			'stream_fallback' => 'Stream',
			'kicker' => 'Radio monitor',

			'status' => [
				'online' => 'Online',
				'offline' => 'Offline',
			],

			'unavailable' => [
				'title' => 'Statistics unavailable',
				'text' => 'Unable to retrieve data from the Icecast server.',
			],

			'stats' => [
				'current_listeners' => 'Current listeners',
				'listener_peak' => 'Listener peak',
				'bitrate' => 'Bitrate',
				'codec' => 'Codec',
			],

			'mounts' => [
				'title' => 'Active mounts',
				'empty' => 'No mounts available.',
				'listeners' => [
					'one' => ':count listener',
					'many' => ':count listeners',
				],
			],
		],
	],
];