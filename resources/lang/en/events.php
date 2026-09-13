<?php

declare(strict_types=1);

return [
	'areas' => [
		'before' => 'Content before events',
		'sidebar' => 'Event sidebar content',
		'after' => 'Content after events',
	],

	'header' => [
		'title' => 'Events',
		'subtitle' => 'Discover the community’s upcoming events.',
		'submit_event' => 'Submit an event',
	],

	'empty' => 'There are no upcoming events.',

	'event' => [
		'open_aria' => 'Open :title',
		'open' => 'View event',
	],

	'event_page' => [
		'starts_at' => 'Starts:',
		'ends_at' => 'Ends:',
		'external_link' => 'Visit event website',
		'all_events' => 'All events',
		'show_map' => 'Show map',
		'map_title' => 'Event location',
		'close_map' => 'Close map',

		'areas' => [
			'before' => 'Content before the event',
			'sidebar' => 'Event information',
			'sidebar_widgets' => 'Event sidebar content',
			'after' => 'Content after the event',
		],
	],

	'submit' => [
		'eyebrow' => 'Community',
		'title' => 'Submit an event',
		'intro' => 'Suggest an event to the community. Your submission will be reviewed before publication.',

		'event' => [
			'title' => 'Event',
			'help' => 'Enter the main information about the event.',
		],

		'schedule' => [
			'title' => 'Schedule',
			'help' => 'Specify when the event will take place.',
		],

		'location' => [
			'title' => 'Location',
			'help' => 'You can provide the location and, if available, coordinates to show it on the map.',
		],

		'cover' => [
			'title' => 'Image',
			'help' => 'You can add a cover image for the event.',
		],

		'publish' => [
			'title' => 'Submit proposal',
			'help' => 'Your proposal will be sent to the administrators and may be edited before publication.',
			'cancel' => 'Cancel',
			'submit' => 'Submit event',
		],

		'fields' => [
			'title' => 'Title',
			'description' => 'Description',
			'description_help' => 'You can use Markdown to format the text.',
			'starts_at' => 'Starts',
			'ends_at' => 'Ends',
			'ends_at_help' => 'Optional.',
			'location' => 'Location',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'coordinates_help' => 'Coordinates are optional, but both must be provided.',
			'external_url' => 'External link',
			'external_url_help' => 'Optional. This can be the official website or the event page.',
			'cover' => 'Cover image',
			'cover_help' => 'Supported formats: JPEG, PNG and WebP.',
		],
	],
];
