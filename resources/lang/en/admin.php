<?php
declare(strict_types=1);

return [
	'layout' => [
		'logout' => 'Log out',
	],
	'navigation' => [
		'dashboard' => 'Dashboard',
		'settings' => 'Settings',
		'blocks' => 'Blocks',
		'pages' => 'Pages',
		'chat' => 'Chat',
		'moderators' => 'Moderators',
		'chanzine' => 'Chanzine',
		'categories' => 'Categories',
	],
	'settings' => [
		'page' => [
			'kicker' => 'System',
			'title' => 'Settings',
			'description' => 'Configure the main site settings.',
		],
	
		'success' => [
			'title' => 'Settings saved.',
		],
	
		'identity' => [
			'title' => 'Site identity',
			'description' => 'These details are used in the title and header of public pages.',
	
			'site_name' => [
				'label' => 'Site name',
				'help' => 'The main name displayed on the site.',
			],
	
			'tagline' => [
				'label' => 'Tagline',
				'help' => 'A short description of the project or community.',
			],
		],
	
		'language' => [
			'title' => 'Language',
			'description' => 'Configure the default interface language and the languages available on the site.',
			'default_label' => 'Default site language',
			'default_help' => 'This language is used as the default for the site interface.',
			'available_label' => 'Available languages',
			'available_help' => 'If only one language is available, Monoverse automatically works as a monolingual site and does not display a language selector.',
	
			'locales' => [
				'it' => 'Italiano',
				'en' => 'English',
			],
		],
	
		'brand' => [
			'title' => 'Brand',
			'description' => 'Configure the images used to identify the site in browsers, mobile devices, and social sharing.',
	
			'logo' => [
				'label' => 'Site logo',
				'alt' => 'Logo',
				'delete' => 'Delete logo',
				'delete_confirm' => 'Delete the logo?',
				'help' => 'Main site logo.',
			],
	
			'favicon' => [
				'label' => 'Favicon',
				'alt' => 'Favicon',
				'delete' => 'Delete favicon',
				'delete_confirm' => 'Delete the favicon?',
				'help' => 'Icon displayed in browser tabs. ICO, PNG and SVG are supported.',
			],
	
			'apple_touch_icon' => [
				'label' => 'Apple Touch Icon',
				'alt' => 'Apple Touch Icon',
				'delete' => 'Delete Apple Touch Icon',
				'delete_confirm' => 'Delete the Apple Touch Icon?',
				'help' => 'Icon used when the site is added to the Home Screen on iPhone and iPad. Recommended size: 180×180 pixels.',
			],
	
			'og_image' => [
				'label' => 'Default OpenGraph image',
				'alt' => 'OpenGraph',
				'delete' => 'Delete OpenGraph image',
				'delete_confirm' => 'Delete the OpenGraph image?',
				'help' => 'Image used for social sharing when a page does not have its own image. Recommended size: 1200×630 pixels.',
			],
		],
	
		'seo' => [
			'title' => 'SEO and URLs',
			'description' => 'Configure the main site address and the default description used by search engines.',
	
			'site_url' => [
				'label' => 'Site URL',
				'help_before' => 'Enter the full address, including',
				'help_after' => 'It will be used for canonical URLs, sitemap, OpenGraph and absolute URLs.',
			],
	
			'meta_description' => [
				'label' => 'Default meta description',
				'placeholder' => 'Briefly describe the site or community.',
				'help' => 'Used when a page does not have its own specific description.',
			],
		],
	
		'github' => [
			'title' => 'GitHub',
			'description' => 'Configure GitHub API access used by Developer widgets.',
			'token_label' => 'GitHub API Token',
			'token_configured' => 'Token configured — leave blank to keep it',
			'token_placeholder' => 'ghp_...',
			'token_help' => 'Optional GitHub token used to increase the API request limit. If a token is already configured, leave the field blank to keep it unchanged.',
		],
	
		'pages_navigation' => [
			'title' => 'Page navigation',
			'description' => 'Choose how published dynamic pages are made accessible.',
			'enable' => 'Show dynamic pages in the main menu',
			'help_before' => 'If you choose',
			'main_menu' => 'Main menu',
			'help_after' => 'a single page will be shown as a direct link; with two or more pages, a dropdown menu will be created automatically.',
		],
	
		'media' => [
			'title' => 'Audio and video attachments',
			'description' => 'Configure which media attachment types can be uploaded to Pings and the maximum allowed size for each file.',
	
			'audio' => [
				'enable' => 'Allow audio attachment uploads',
				'enable_help' => 'Disabling this option prevents only new audio uploads. Existing published attachments will remain available.',
				'limit_label' => 'Audio upload limit (MB)',
				'limit_help' => 'Maximum allowed size for each audio attachment.',
			],
	
			'video' => [
				'enable' => 'Allow video attachment uploads',
				'enable_help' => 'Disabling this option prevents only new video uploads. Existing published videos will remain available.',
				'limit_label' => 'Video upload limit (MB)',
				'limit_help' => 'Maximum allowed size for each video attachment.',
			],
	
			'require_text' => [
				'label' => 'Require text in Pings with audio or video attachments',
				'help' => 'When disabled, users may publish a Ping containing only one or more audio or video attachments. A Ping without text or attachments will still not be allowed.',
			],
		],
	
		'chanzine' => [
			'title' => 'Chanzine',
			'description' => 'Configure editorial features available to users.',
			'user_submissions' => 'Allow users to submit articles',
			'user_submissions_help' => 'Articles submitted by users are not published automatically. They remain pending review until an administrator approves, edits or rejects them.',
		],
	
		'crypto' => [
			'title' => 'Crypto Tips',
			'description' => 'Configure Dogecoin tips between community users.',
	
			'enable' => [
				'label' => 'Enable Dogecoin tips',
				'help' => 'Allows users to configure a Dogecoin address to receive tips through MyDogeMask or their SimosNap account. Monoverse never holds funds or private keys.',
			],
	
			'profiles' => [
				'label' => 'Show tip button on profiles',
				'help' => 'Shows the action to send a Dogecoin tip on public profiles of users who configured a receiving address.',
			],
	
			'pings' => [
				'label' => 'Show tip button on Pings',
				'help' => 'Shows the Ð Tip action on Pings published by users who configured a Dogecoin receiving address.',
			],
		],
	
		'save' => 'Save settings',
	],
	'widgets_area' => [
		'description' => 'Organize the widgets displayed in this area.',
		'all_areas' => 'All areas',
		'add_widget' => 'Add widget',
	
		'empty' => [
			'title' => 'This area is empty',
			'description' => 'Add the first widget by choosing it from the library.',
		],
	
		'drag' => 'Drag to reorder',
	
		'status' => [
			'active' => 'Active',
			'disabled' => 'Disabled',
		],
	
		'width' => 'Width',
	
		'actions' => [
			'edit' => 'Edit',
			'enable' => 'Enable',
			'disable' => 'Disable',
			'delete' => 'Delete',
		],
	
		'delete_confirm' => 'Do you really want to delete this widget?',
	],
	'areas' => [
		'landing_chat' => [
			'before_entry' => 'Before the access section',
			'entry_left_before' => 'Left column — before content',
			'entry_left_after' => 'Left column — after content',
			'after_entry' => 'After the access section',
			'before_footer' => 'Before the footer',
		],
	
		'members' => [
			'before_content' => 'Before the members list',
			'sidebar' => 'Sidebar',
			'after_content' => 'After the members list',
		],
	
		'ping' => [
			'before_content' => 'Before Pings',
			'sidebar' => 'Sidebar',
			'after_content' => 'After Pings',
		],
	
		'ping_show' => [
			'before_content' => 'Before the Ping',
			'sidebar' => 'Sidebar',
			'after_content' => 'After the Ping',
		],
	
		'profile' => [
			'before_content' => 'Before the profile',
			'sidebar' => 'Sidebar',
			'after_content' => 'After the profile',
		],
	
		'chanzine' => [
			'before_content' => 'Before articles',
			'sidebar' => 'Sidebar',
			'after_content' => 'After articles',
		],
	
		'chanzine_article' => [
			'before_content' => 'Before the article',
			'sidebar' => 'Sidebar',
			'after_content' => 'After the article',
		],
	
		'account' => [
			'sidebar' => 'Sidebar',
		],
	],
	'blocks' => [
		'pages' => [
			'landing_chat' => 'Landing Chat',
			'members' => 'Members list',
			'ping' => 'Timeline',
			'ping_show' => 'Ping details',
			'profile' => 'Public profile',
			'account' => 'Personal area',
			'chanzine' => 'Chanzine',
			'chanzine_article' => 'Chanzine article',
		],
	
		'dynamic_areas' => [
			'content' => 'Content',
			'sidebar' => 'Sidebar',
		],
	
		'messages' => [
			'created' => 'Widget created.',
			'saved' => 'Widget saved.',
			'enabled' => 'Widget enabled.',
			'disabled' => 'Widget disabled.',
			'deleted' => 'Widget deleted.',
			'order_saved' => 'Widget order saved.',
		],
	
		'errors' => [
			'invalid_session' => 'Invalid administrator session.',
			'invalid_area' => 'Invalid widget area.',
			'invalid_order' => 'Invalid widget order.',
			'order_mismatch' => 'The widget list does not match the selected area.',
		],
	],
	'blocks_page' => [
		'kicker' => 'Site composition',
		'title' => 'Widgets',
		'description' => 'Choose a page and manage the widgets available in the different theme areas.',
	
		'empty' => 'The active theme has no configurable areas.',
	
		'page' => 'Page',
	
		'widget_count' => [
			'one' => ':count widget',
			'many' => ':count widgets',
		],
	
		'area' => [
			'empty' => 'No widgets configured.',
	
			'active' => [
				'one' => ':count active',
				'many' => ':count active',
			],
	
			'configured' => [
				'one' => ':count configured',
				'many' => ':count configured',
			],
	
			'of' => 'of',
		],
	
		'manage' => 'Manage',
	],
	'block_library' => [
		'title' => 'Block Library',
		'description' => 'Choose the type of block to add.',
	],
	'block_edit' => [
		'fallback_label' => 'Widget',
	
		'kicker' => [
			'new' => 'New widget',
			'edit' => 'Widget configuration',
		],
	
		'description' => 'Edit the widget content, width and visibility.',
	
		'content' => [
			'title' => 'Content',
			'description' => 'Configure the information displayed by the widget.',
		],
	
		'settings' => [
			'title' => 'Settings',
			'description' => 'Name, title and dimensions of the widget.',
		],
	
		'name' => [
			'label' => 'Internal name',
			'placeholder' => 'E.g. Summer banner',
			'help' => 'Visible only in the administration panel.',
		],
	
		'public_title' => [
			'label' => 'Public title',
			'placeholder' => 'Optional',
		],
	
		'width' => [
			'label' => 'Width',
			'full' => 'Full width',
			'three_quarters' => 'Three quarters',
			'two_thirds' => 'Two thirds',
			'half' => 'Half',
			'one_third' => 'One third',
			'one_quarter' => 'One quarter',
		],
	
		'enabled' => [
			'label' => 'Widget enabled',
			'description' => 'The widget is displayed in its assigned area.',
		],
	
		'actions' => [
			'cancel' => 'Cancel',
			'create' => 'Create widget',
			'save' => 'Save widget',
		],
	],
	'login' => [
		'title' => 'Administration login',
		'description' => 'Enter your administrator credentials to continue.',
		'username' => 'Username',
		'password' => 'Password',
		'submit' => 'Sign in',
	],
	'articles' => [
		'title' => 'Articles',
		'description' => 'Manage Chanzine articles.',
	
		'actions' => [
			'new' => 'New article',
			'review' => 'Review',
			'reject' => 'Reject',
			'edit' => 'Edit',
			'publish' => 'Publish',
			'delete' => 'Delete',
			'create_first' => 'Create the first article',
		],
	
		'submissions' => [
			'title' => 'Pending submissions',
			'description' => 'Articles submitted by users and awaiting review.',
			'default_user' => 'User',
			'status' => 'Under review',
			'rejection_reason' => 'Reason for rejection',
		],
	
		'empty' => [
			'title' => 'No articles',
			'description' => 'There are no articles in the Chanzine yet.',
		],
	
		'publication' => [
			'not_published' => 'Not published yet',
		],
	
		'status' => [
			'published' => 'Published',
			'draft' => 'Draft',
		],
	
		'confirm' => [
			'delete' => 'Permanently delete this article?',
		],
	],
	'article_form' => [
		'title' => [
			'create' => 'New article',
			'edit' => 'Edit article',
		],
	
		'description' => [
			'create' => 'Create a new draft for Chanzine.',
			'edit' => 'Edit the article content and settings.',
		],
	
		'back' => 'Back to articles',
	
		'submission' => [
			'from' => 'Submission from',
			'default_user' => 'User',
			'on' => 'on',
			'pending' => 'The article is awaiting review and is not public yet.',
		],
	
		'fields' => [
			'title' => [
				'label' => 'Title',
				'placeholder' => 'Enter the article title',
			],
	
			'excerpt' => [
				'label' => 'Excerpt',
				'help' => 'A short introduction that will be displayed in the article list.',
				'placeholder' => 'Write a short description of the article',
			],
	
			'content' => [
				'label' => 'Content',
				'help' => 'Write the article content in Markdown.',
				'placeholder' => 'Start writing your article...',
			],
	
			'slug' => [
				'label' => 'Slug',
				'placeholder' => 'my-article-title',
				'help' => 'It will be used in the article’s public address.',
			],
		],
	
		'publication' => [
			'title' => 'Publication',
			'save_changes' => 'Save changes',
			'save_draft' => 'Save draft',
			'save_publish' => 'Save and publish',
			'confirm_publish' => 'Save the changes and publish this submission?',
			'cancel' => 'Cancel',
		],
	
		'address' => [
			'title' => 'Article address',
		],
	
		'category' => [
			'title' => 'Category',
			'label' => 'Article category',
			'select' => 'Select a category',
			'help' => 'Every article must belong to a category.',
		],
	
		'cover' => [
			'title' => 'Cover',
			'current_alt' => 'Current article cover',
			'empty' => 'No cover selected',
			'replace' => 'Replace cover',
			'upload' => 'Upload cover',
			'formats' => 'Accepted formats: JPG, PNG and WebP.',
		],
	],
	'categories' => [
		'title' => 'Chanzine Categories',
		'description' => 'Manage the categories available for Chanzine articles.',
	
		'actions' => [
			'new' => 'New category',
			'create_first' => 'Create the first category',
			'edit' => 'Edit',
			'delete' => 'Delete',
		],
	
		'empty' => [
			'title' => 'No categories',
			'description' => 'There are no categories available for the Chanzine yet.',
		],
	
		'order' => 'Order:',
	
		'confirm' => [
			'delete' => 'Delete this category? Associated articles will remain without a category.',
		],
	],
	'category_form' => [
		'title' => [
			'create' => 'New category',
			'edit' => 'Edit category',
		],
	
		'description' => [
			'create' => 'Create a new category for Chanzine articles.',
			'edit' => 'Edit the category name, description, slug and order.',
		],
	
		'back' => 'Back to categories',
	
		'fields' => [
			'name' => [
				'label' => 'Name',
				'placeholder' => 'E.g. Community',
				'help' => 'The name displayed in the administration panel and articles.',
			],
	
			'description' => [
				'label' => 'Description',
				'placeholder' => 'Briefly describe the contents of the category.',
				'help' => 'It will be displayed on the public category page.',
			],
	
			'slug' => [
				'label' => 'Slug',
				'placeholder' => 'E.g. community',
				'help' => 'Used in URLs. If left empty, it is automatically generated from the name.',
			],
	
			'sort_order' => [
				'label' => 'Order',
				'help' => 'Categories with lower values are displayed first.',
			],
		],
	
		'actions' => [
			'cancel' => 'Cancel',
			'create' => 'Create category',
			'save' => 'Save changes',
		],
	],
	'pages' => [
		'title' => 'Pages',
		'description' => 'Create public pages composed using widgets.',
	
		'actions' => [
			'new' => 'New page',
			'edit' => 'Edit',
			'delete' => 'Delete',
		],
	
		'empty' => [
			'title' => 'No pages created',
			'description' => 'Create a page and compose it using the Content and Sidebar areas.',
		],
	
		'table' => [
			'page' => 'Page',
			'status' => 'Status',
			'widgets' => 'Widgets',
			'actions' => 'Actions',
		],
	
		'status' => [
			'draft' => 'Draft',
			'published' => 'Published',
			'private' => 'Private',
		],
	
		'areas' => [
			'content' => 'Content',
			'sidebar' => 'Sidebar',
		],
	
		'confirm' => [
			'delete' => 'Permanently delete this page?',
		],
	],
	'page_form' => [
		'title' => [
			'create' => 'New page',
			'edit' => 'Edit page',
		],
	
		'description' => 'Configure the page and compose its content using widgets.',
		'back' => 'Back to pages',
	
		'fields' => [
			'title' => 'Title',
	
			'slug' => [
				'label' => 'Slug',
				'help' => 'Public page address, for example :example.',
			],
	
			'status' => [
				'label' => 'Status',
				'help' => 'Only published pages are publicly accessible.',
			],
	
			'menu_label' => [
				'label' => 'Menu label',
				'placeholder' => 'Leave empty to use the title',
				'help' => 'Short text displayed in navigation.',
			],
	
			'navigation_group' => [
				'label' => 'Group',
				'help' => 'Group used by navigation widgets. Leave default unless you need a specific separation.',
			],
	
			'sort_order' => [
				'label' => 'Order',
				'help' => 'Lower values are displayed first.',
			],
	
			'meta_title' => [
				'label' => 'SEO title',
				'help' => 'If empty, the page title will be used.',
			],
	
			'meta_description' => [
				'label' => 'Meta description',
				'help' => 'Short description used by search engines.',
			],
		],
	
		'status' => [
			'draft' => 'Draft',
			'published' => 'Published',
			'private' => 'Private',
		],
	
		'navigation' => [
			'title' => 'Navigation',
			'description' => 'Configure how this page is presented in menus and navigation widgets.',
			'show' => 'Show in navigation',
			'show_help' => 'When disabled, the page remains accessible by URL but is not included in menus or the Pages widget.',
		],
	
		'actions' => [
			'cancel' => 'Cancel',
			'create' => 'Create page',
			'save' => 'Save changes',
			'manage_widgets' => 'Manage widgets',
			'open' => 'Open page',
			'delete' => 'Delete page',
		],
	
		'composition' => [
			'title' => 'Page composition',
			'description' => 'Manage the main content and sidebar separately. If the sidebar is empty, it will not be displayed.',
	
			'content' => [
				'title' => 'Content',
				'description' => 'Compose the main section of the page using the available widgets.',
			],
	
			'sidebar' => [
				'title' => 'Sidebar',
				'description' => 'Add widgets to the side column or leave it empty to use the full width.',
			],
		],
	
		'unsaved' => [
			'title' => 'Widgets will be available after saving',
			'description' => 'Create the page first; you can then configure the Content and Sidebar areas.',
		],
	
		'confirm' => [
			'delete' => 'Permanently delete this page?',
		],
	],
	'webchat' => [
		'kicker' => 'Community',
		'title' => 'Landing Chat',
		'description' => 'Configure the chat access page and the default KiwiIRC settings.',
	
		'success' => 'Settings saved.',
	
		'configuration' => [
			'title' => 'Landing Chat configuration',
			'description' => 'These settings are used by the chat landing page.',
		],
	
		'fields' => [
			'default_channel' => [
				'label' => 'Default channel',
				'help' => 'The channel automatically opened when entering the chat.',
			],
	
			'show_hero' => [
				'label' => 'Introductory hero',
				'help' => 'Show the introductory section at the top of the chat landing page.',
			],
	
			'show_channel_card' => [
				'label' => 'Channel information card',
				'help' => 'Show community statistics and features on the chat landing page.',
			],
	
			'chat_title' => [
				'label' => 'Window title',
				'help' => 'Title displayed in the browser and on the landing page.',
			],
	
			'theme' => [
				'label' => 'KiwiIRC theme',
				'help' => 'Name of the theme to use.',
			],
	
			'state_key' => [
				'label' => 'State Key',
				'help' => 'Key used to preserve the client state.',
			],
		],
	
		'actions' => [
			'save' => 'Save configuration',
		],
	],
	'block_types' => [
		'github_repository' => [
			'label' => 'GitHub Repository',
			'description' => 'Displays a complete GitHub repository dashboard with activity, commits, releases, issues and pull requests.',
		],
	
		'github_pull_requests' => [
			'label' => 'GitHub Pull Requests',
			'description' => 'Displays the most recent pull requests from a GitHub repository.',
		],
	
		'github_release' => [
			'label' => 'GitHub Release',
			'description' => 'Displays the latest available release from a GitHub repository in a compact format.',
		],
	
		'azuracast_mini_player' => [
			'label' => 'AzuraCast Miniplayer',
			'description' => 'Displays a compact radio player that can also be opened in a separate window.',
		],
	
		'azuracast_requests' => [
			'label' => 'AzuraCast Requests',
			'description' => 'Allows listeners to search for and request tracks through AzuraCast.',
		],
	
		'azuracast' => [
			'label' => 'AzuraCast',
			'description' => 'Displays the AzuraCast player and the history of recently played tracks.',
		],
	
		'azuracast_stats' => [
			'label' => 'AzuraCast Statistics',
			'description' => 'Displays radio status, listeners, bitrate, codec and active mounts.',
		],
	
		'users_in_chat' => [
			'label' => 'Now in chat',
			'description' => 'Displays the people currently connected to the IRC chat.',
		],
	
		'latest_members' => [
			'label' => 'New members',
			'description' => 'Displays the latest members registered in the community.',
		],
	
		'latest_audio' => [
			'label' => 'Latest shared audio',
			'description' => 'Displays the latest audio shared in Pings.',
		],
	
		'submit_article' => [
			'label' => 'Submit an article',
			'description' => 'Invites users to submit an article to the Chanzine.',
		],
	
		'html' => [
			'label' => 'Custom HTML',
			'description' => 'Displays custom HTML code.',
		],
	
		'latest_video' => [
			'label' => 'Latest shared videos',
			'description' => 'Displays the latest videos shared in Pings.',
		],
	
		'latest_articles' => [
			'label' => 'Latest articles',
			'description' => 'Displays the latest articles published in the Chanzine.',
		],
	
		'categories' => [
			'label' => 'Chanzine categories',
			'description' => 'Displays Chanzine categories with the number of published articles.',
		],
	
		'pages_navigation' => [
			'label' => 'Page navigation',
			'description' => 'Displays published dynamic pages in a navigation menu.',
		],
	],
	'block_settings' => [
		'latest_members' => [
			'title' => [
				'label' => 'Title',
				'default' => 'New members',
			],
			'limit' => [
				'label' => 'Number of members',
			],
			'show_avatar' => [
				'label' => 'Show avatars',
			],
		],
	
		'users_in_chat' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Now in chat',
			],
			'limit' => [
				'label' => 'Number of people',
			],
			'show_total' => [
				'label' => 'Show total number of people in chat',
			],
			'show_avatar' => [
				'label' => 'Show registered member avatars',
			],
			'show_join_link' => [
				'label' => 'Show the "Join chat" link',
			],
		],
	
		'categories' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Categories',
			],
			'show_count' => [
				'label' => 'Show article count',
			],
		],
	
		'html' => [
			'html' => [
				'label' => 'HTML code',
			],
		],
	
		'latest_articles' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Latest articles',
			],
			'limit' => [
				'label' => 'Number of articles',
			],
			'show_date' => [
				'label' => 'Show date',
			],
		],
	
		'latest_audio' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Latest shared audio',
			],
			'limit' => [
				'label' => 'Number of audio items',
			],
			'show_author' => [
				'label' => 'Show author',
			],
		],
	
		'latest_video' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Latest shared videos',
			],
			'limit' => [
				'label' => 'Number of videos',
			],
			'show_author' => [
				'label' => 'Show author',
			],
		],
	
		'pages_navigation' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Pages',
			],
			'navigation_group' => [
				'label' => 'Group',
				'placeholder' => 'default',
			],
		],
	
		'submit_article' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Submit an article',
			],
			'description' => [
				'label' => 'Description',
				'default' => 'Have something to share? Tell the community about your idea.',
			],
			'button_label' => [
				'label' => 'Button text',
				'default' => 'Submit your proposal',
			],
		],
	
		'github_pull_requests' => [
			'repository' => [
				'label' => 'Repository',
				'placeholder' => 'owner/repository',
				'help' => 'You can enter either owner/repository or the full GitHub repository URL.',
			],
			'custom_title' => [
				'label' => 'Custom title',
				'placeholder' => 'Pull Requests',
			],
			'state' => [
				'label' => 'Status',
				'options' => [
					'open' => 'Open',
					'closed' => 'Closed',
					'all' => 'All',
				],
			],
			'limit' => [
				'label' => 'Number of Pull Requests',
			],
		],
	
		'github_release' => [
			'repository' => [
				'label' => 'Repository',
				'placeholder' => 'owner/repository',
				'help' => 'You can enter either owner/repository or the full GitHub repository URL.',
			],
			'custom_title' => [
				'label' => 'Custom title',
				'placeholder' => 'Latest release',
			],
			'show_repository' => [
				'label' => 'Show repository name',
			],
			'show_date' => [
				'label' => 'Show publication date',
			],
		],
	
		'github_repository' => [
			'repository' => [
				'label' => 'Repository',
				'placeholder' => 'owner/repository',
				'help' => 'You can enter either owner/repository or the full GitHub repository URL.',
			],
			'branch' => [
				'label' => 'Branch',
				'placeholder' => 'Leave empty to use the default branch',
			],
			'title' => [
				'label' => 'Custom title',
			],
			'show_release' => [
				'label' => 'Show latest release',
			],
			'show_languages' => [
				'label' => 'Show languages',
			],
			'show_commits' => [
				'label' => 'Show latest commits',
			],
			'commit_limit' => [
				'label' => 'Number of commits',
			],
			'show_pull_requests' => [
				'label' => 'Show open pull requests',
			],
			'show_issues' => [
				'label' => 'Show open issues',
			],
		],
	
		'azuracast' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Listen to the radio',
			],
			'player_style' => [
				'label' => 'Player style',
				'help' => 'Choose the appearance of the player displayed on the site.',
				'options' => [
					'modern' => 'Modern',
					'led' => 'LED',
					'analog' => 'Analog',
					'minimal' => 'Minimal',
				],
			],
			'station_url' => [
				'label' => 'AzuraCast station URL',
			],
			'history_limit' => [
				'label' => 'Number of tracks in history',
			],
			'show_history' => [
				'label' => 'Show track history',
			],
		],
	
		'azuracast_mini_player' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Listen to the radio',
			],
			'now_playing_url' => [
				'label' => 'Now Playing API URL',
			],
			'stream_url' => [
				'label' => 'Audio stream URL',
			],
			'show_cover' => [
				'label' => 'Show cover',
			],
		],
	
		'azuracast_requests' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Request a track',
			],
			'requests_url' => [
				'label' => 'AzuraCast requests URL',
				'help' => 'Example: https://radio.example.org/api/station/1/requests',
			],
			'unavailable_behavior' => [
				'label' => 'When requests are unavailable',
				'options' => [
					'message' => 'Show a message',
					'hide' => 'Hide the block',
				],
			],
			'unavailable_message' => [
				'label' => 'Unavailable requests message',
				'default' => 'Music requests are not available at the moment.',
			],
		],
	
		'azuracast_stats' => [
			'title' => [
				'label' => 'Title',
				'default' => 'Radio statistics',
			],
			'station_url' => [
				'label' => 'AzuraCast Now Playing URL',
				'help' => 'Example: https://radio.example.org/api/nowplaying/1',
			],
			'show_listeners' => [
				'label' => 'Show current listeners',
			],
			'show_unique_listeners' => [
				'label' => 'Show unique listeners',
			],
			'show_bitrate' => [
				'label' => 'Show bitrate',
			],
			'show_codec' => [
				'label' => 'Show codec',
			],
			'show_mounts' => [
				'label' => 'Show active mounts',
			],
		],
	],
];
