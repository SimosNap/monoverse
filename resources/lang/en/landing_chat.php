<?php

declare(strict_types=1);

return [
	'presence' => [
		'one_user' => 'user in chat',
		'many_users' => 'users in chat',
	],

	'entry' => [
		'title' => 'Ready to join?',
		'subtitle' => 'Choose how you want to show up and join everyone else in the chat.',
	],

	'nickname' => [
		'label' => 'What should we call you?',
		'placeholder' => 'Choose a nickname',
		'join_as' => 'Join as',
		'change' => 'Change nickname',
		'choose' => 'Choose another nickname',
		'registered' => 'This nickname is already registered.',
		'available' => 'Perfect, this nickname is available.',
		'checking' => 'Checking nickname...',
		'registered_only' => 'You need a nickname registered on SimosNap to join this channel.',
	],

	'profile' => [
		'age' => 'Age',
		'optional' => 'Optional',
		'type' => 'Profile',
		'not_specified' => 'Not specified',
		'male' => 'Man',
		'female' => 'Woman',
		'other' => 'Other',
		'city' => 'City',
		'city_placeholder' => 'Where are you chatting from?',
		'loaded' => 'Profile loaded',
		'years' => 'years old',
		'edit' => 'Edit profile',
	],

	'age_verification' => 'I confirm that I am at least 14 years old',

	'preferences' => [
		'title' => 'How do you like your chat?',
		'hide_join_parts' => 'Hide joins and parts',
	],

	'actions' => [
		'join' => 'Join the chat',
		'login_sync' => 'Sign in and keep your preferences',
		'back' => 'Back',
		'authenticate_join' => 'Sign in and join',
	],

	'auth' => [
		'title' => 'Is this nickname yours?',
		'description_before_nickname' => 'The nickname',
		'description_after_nickname' => 'is registered on SimosNap. Enter your password to use it and join the chat.',
		'password_label' => 'NickServ password',
		'password_placeholder' => 'Nickname password',
	],

	'footer' => [
		'powered_by' => 'Powered by Monoverse',
		'account' => 'Account',
		'login' => 'Sign in',
	],

	'channel_card' => [
		'community' => 'The community',

		'users' => [
			'one' => 'user in chat',
			'many' => 'users in chat',
		],

		'peak_users' => 'peak users',
		'founder' => 'founder',

		'login' => 'Sign in with SimosNap',
		'how_it_works' => 'See how it works',
	],

	'channel_features' => [
		'moderated' => [
			'title' => 'Moderated channel',
			'description' => 'Only users authorized by channel operators can speak.',
		],
		'registered_speak' => [
			'title' => 'Chat reserved for registered users',
			'description' => 'Only registered nicknames can speak in the channel.',
		],
		'registered_access' => [
			'title' => 'Access reserved for registered users',
			'description' => 'Only registered nicknames can join the channel.',
		],
		'invite_only' => [
			'title' => 'Invite-only access',
			'description' => 'You can join the channel only if you have been invited.',
		],
		'auditorium' => [
			'title' => 'Auditorium mode',
			'description' => 'The conversation is managed by the channel operators.',
		],
		'secure' => [
			'title' => 'Secure connection',
			'description' => 'The channel requires a secure IRC connection.',
		],
		'history' => [
			'title' => 'Chat history available',
			'description' => 'The channel provides access to recent conversation history.',
		],
		'slow_mode' => [
			'title' => 'Slow mode',
			'description' => 'The channel limits how frequently messages can be sent.',
		],
	],

	'hero' => [
		'community' => 'The community',
		'identity' => 'People, conversations and content in one place.',
	],

	'registered_only' => [
		'title' => 'Sign in to join the chat',
		'description' => 'This channel is reserved for users registered on SimosNap.',
		'security' => 'Already have an account? Sign in and join everyone else in the chat. Otherwise, you can sign up in just a moment.',
		'login' => 'Sign in with SimosNap',
		'register' => 'Sign up',
	],
];
