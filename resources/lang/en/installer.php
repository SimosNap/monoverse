<?php

declare(strict_types=1);

return [
	'title' => [
		'requirements' => 'Monoverse Installation',
		'edition' => 'Choose Edition',
		'database' => 'Database',
		'oauth' => 'SimosNap OAuth',
		'admin' => 'Initial Administrator',
		'summary' => 'Installation Summary',
	],

	'steps' => [
		'requirements' => 'Requirements',
		'edition' => 'Edition',
		'database' => 'Database',
		'oauth' => 'OAuth',
		'admin' => 'Administrator',
		'summary' => 'Summary',
		'install' => 'Installation',
	],

	'common' => [
		'continue' => 'Continue',
		'back' => 'Back',
		'coming_soon' => 'Coming soon',
		'version' => 'Version',
	],

	'language' => [
		'label' => 'Language',
		'italian' => 'Italiano',
		'english' => 'English',
		'change' => 'Change language',
	],

	'requirements' => [
		'heading' => 'Monoverse Installation',
		'welcome' => 'Welcome to the Monoverse installer.',
		'system_requirements' => 'System requirements',

		'loaded' => 'loaded',
		'missing' => 'missing',
		'supported' => 'supported',
		'writable' => 'writable',
		'not_writable' => 'not writable',

		'storage' => 'storage/ writable',
	],

	'edition' => [
		'heading' => 'Choose an Edition',
		'description' => 'Select the Edition you want to install.',
		'install' => 'Install this Edition',
		'unavailable' => 'This Edition is not available yet.',

		'community' => [
			'name' => 'Community',
			'description' => 'Edition for creating an IRC website focused on its community.',
		],

		'hub' => [
			'name' => 'Hub',
			'description' => 'Edition dedicated to creating Monoverse hubs.',
		],
	],

	'database' => [
		'heading' => 'Database Configuration',
		'selected_edition' => 'Selected Edition:',
		'host' => 'Database host',
		'name' => 'Database name',
		'user' => 'Database user',
		'password' => 'Database password',
		'back' => '← Back to Edition selection',
	],

	'oauth' => [
		'heading' => 'SimosNap OAuth',
		'description' => 'Configure login through SimosNap.',
		'client_id' => 'Client ID',
		'client_secret' => 'Client Secret',
		'back' => '← Back',
	],

	'admin' => [
		'heading' => 'Initial Administrator',
		'description' => 'Create the local Monoverse administrator account. This account is used to manage the admin panel and does not depend on SimosNap OAuth.',
		'username' => 'Administrator username',
		'password' => 'Password',
		'password_confirm' => 'Confirm password',
		'back' => '← Back to OAuth configuration',
	],

	'summary' => [
		'heading' => 'Installation Summary',
		'description' => 'Review the information before installing Monoverse.',
		'edition' => 'Edition',
		'database' => 'Database',
		'host' => 'Host',
		'name' => 'Name',
		'user' => 'User',
		'admin' => 'Administrator',
		'username' => 'Username:',
		'install' => 'Install Monoverse',
		'back' => '← Back',
	],

	'validation' => [
		'edition_required' => 'You must select an Edition.',
		'database_host_required' => 'Database host is required.',
		'database_name_required' => 'Database name is required.',
		'database_user_required' => 'Database user is required.',
		'oauth_client_id_required' => 'OAuth Client ID is required.',
		'oauth_client_secret_required' => 'OAuth Client Secret is required.',
		'admin_username_required' => 'Administrator username is required.',
		'admin_password_required' => 'Administrator password is required.',
		'admin_password_confirm_required' => 'Password confirmation is required.',
		'admin_password_mismatch' => 'The passwords do not match.',
	],
];
