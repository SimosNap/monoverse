<?php

declare(strict_types=1);

return [
	'title' => [
		'requirements' => 'Installazione Monoverse',
		'edition' => 'Scelta Edition',
		'database' => 'Database',
		'oauth' => 'OAuth SimosNap',
		'admin' => 'Amministratore iniziale',
		'summary' => 'Riepilogo installazione',
	],

	'steps' => [
		'requirements' => 'Requisiti',
		'edition' => 'Edition',
		'database' => 'Database',
		'oauth' => 'OAuth',
		'admin' => 'Amministratore',
		'summary' => 'Riepilogo',
		'install' => 'Installazione',
	],

	'common' => [
		'continue' => 'Continua',
		'back' => 'Indietro',
		'coming_soon' => 'Coming soon',
		'version' => 'Versione',
	],

	'language' => [
		'label' => 'Lingua',
		'italian' => 'Italiano',
		'english' => 'English',
		'change' => 'Cambia lingua',
	],

	'requirements' => [
		'heading' => 'Installazione Monoverse',
		'welcome' => 'Benvenuto nell\'installer di Monoverse.',
		'system_requirements' => 'Requisiti di sistema',

		'loaded' => 'caricato',
		'missing' => 'mancante',
		'supported' => 'supportato',
		'writable' => 'scrivibile',
		'not_writable' => 'non scrivibile',

		'storage' => 'storage/ scrivibile',
	],

	'edition' => [
		'heading' => 'Scegli la Edition',
		'description' => 'Seleziona la Edition da installare.',
		'install' => 'Installa questa Edition',
		'unavailable' => 'Questa Edition non è ancora disponibile.',

		'community' => [
			'name' => 'Community',
			'description' => 'Edition per creare un sito IRC orientato alla community.',
		],

		'hub' => [
			'name' => 'Hub',
			'description' => 'Edition dedicata alla creazione di hub Monoverse.',
		],
	],

	'database' => [
		'heading' => 'Configurazione Database',
		'selected_edition' => 'Edition selezionata:',
		'host' => 'Host database',
		'name' => 'Nome database',
		'user' => 'Utente database',
		'password' => 'Password database',
		'back' => '← Torna alla scelta della Edition',
	],

	'oauth' => [
		'heading' => 'OAuth SimosNap',
		'description' => 'Configura il login tramite SimosNap.',
		'client_id' => 'Client ID',
		'client_secret' => 'Client Secret',
		'back' => '← Indietro',
	],

	'admin' => [
		'heading' => 'Amministratore iniziale',
		'description' => 'Crea l\'account amministratore locale di Monoverse. Questo accesso serve per gestire il pannello admin e non dipende da OAuth SimosNap.',
		'username' => 'Username amministratore',
		'password' => 'Password',
		'password_confirm' => 'Conferma password',
		'back' => '← Torna alla configurazione OAuth',
	],

	'summary' => [
		'heading' => 'Riepilogo installazione',
		'description' => 'Controlla i dati prima di installare Monoverse.',
		'edition' => 'Edition',
		'database' => 'Database',
		'host' => 'Host',
		'name' => 'Nome',
		'user' => 'Utente',
		'admin' => 'Amministratore',
		'username' => 'Username:',
		'install' => 'Installa Monoverse',
		'back' => '← Indietro',
	],

	'validation' => [
		'edition_required' => 'Devi selezionare una Edition.',
		'database_host_required' => 'Host database obbligatorio.',
		'database_name_required' => 'Nome database obbligatorio.',
		'database_user_required' => 'Utente database obbligatorio.',
		'oauth_client_id_required' => 'Client ID OAuth obbligatorio.',
		'oauth_client_secret_required' => 'Client Secret OAuth obbligatorio.',
		'admin_username_required' => 'Username amministratore obbligatorio.',
		'admin_password_required' => 'Password amministratore obbligatoria.',
		'admin_password_confirm_required' => 'Conferma password obbligatoria.',
		'admin_password_mismatch' => 'Le password non coincidono.',
	],
];
