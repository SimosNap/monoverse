<?php

declare(strict_types=1);

return [
	'user' => 'Utente',

	'roles' => [
		'moderator' => 'Moderatore',
	],

	'shortcuts' => [
		'title' => 'Il tuo account',
		'account' => 'Pannello account',
		'edit_profile' => 'Modifica profilo',
		'saved' => 'Contenuti salvati',
		'blocked' => 'Utenti bloccati',
		'moderation' => 'Moderazione',
	],

	'sex' => [
		'male' => 'Uomo',
		'female' => 'Donna',
		'other' => 'Altro',
	],

	'blocked' => [
		'you_blocked_title' => 'Hai bloccato questo utente',
		'you_blocked_text' => 'Non vedrai più i suoi Ping e non potrai interagire con lui finché non rimuoverai il blocco dalla tua area Account.',

		'blocked_by_title' => 'Interazioni limitate',
		'blocked_by_text' => 'Questo utente ha scelto di non interagire con te. Alcune funzionalità del profilo non sono disponibili.',
	],

	'areas' => [
		'before' => 'Contenuti prima del profilo',
		'sidebar' => 'Contenuti laterali del profilo',
		'after' => 'Contenuti dopo il profilo',
	],

	'pings' => [
		'empty_title' => 'Nessun Ping pubblicato',
		'empty_text' => 'Questo utente non ha ancora pubblicato contenuti.',
		'filters_label' => 'Filtra i Ping del profilo',

		'filters' => [
			'all' => 'Tutti i Ping',
			'audio' => 'Audio',
			'video' => 'Video',
			'interactions' => 'Interazioni',
		],
	],

	'sidebar' => [
		'profile_details' => 'Profilo e dettagli',
	],

	'details' => [
		'years' => 'anni',
		'followers' => 'Follower',
		'following' => 'Seguiti',
		'pings' => 'Ping',
	],

	'actions' => [
		'send_doge' => 'Invia DOGE',
		'follow' => 'Segui',
		'unfollow' => 'Non seguire più',
		'block' => 'Blocca',
		'block_confirm' => 'Vuoi davvero bloccare questo utente?',
	],

	'irc' => [
		'title' => 'Statistiche IRC',
		'connected' => 'Connesso',
		'not_connected' => 'Non connesso',
		'role' => 'Ruolo',
		'operator' => 'Operatore IRC',
		'channels' => 'Canali',
		'messages' => 'Messaggi',
		'words' => 'Parole',
		'characters' => 'Caratteri',
	],

	'sections' => [
		'interests' => 'Interessi',
		'aliases' => 'Alias',
		'links' => 'Collegamenti',
		'website' => 'Sito web',
		'telegram' => 'Telegram',
	],

	'moderation' => [
		'title' => 'Moderazione',
		'clear' => 'Regolare',

		'muted_title' => 'Utente silenziato',
		'muted_text' => 'Non può pubblicare nuovi Ping o Pong.',

		'banned_title' => 'Utente sospeso',
		'banned_text' => 'Non può accedere alle funzionalità della community.',

		'reason' => 'Motivo:',
		'reactivate' => 'Riattiva',

		'mute_user' => 'Silenzia utente',
		'mute' => 'Silenzia',
		'ban_user' => 'Sospendi utente',
		'ban' => 'Sospendi',

		'modal_title' => 'Moderazione',
		'close' => 'Chiudi',
		'reason_label' => 'Motivo',
		'reason_placeholder' => 'Inserisci il motivo...',
		'duration' => 'Durata',

		'duration_options' => [
			'permanent' => 'Permanente',
			'15_minutes' => '15 minuti',
			'30_minutes' => '30 minuti',
			'1_hour' => '1 ora',
			'6_hours' => '6 ore',
			'12_hours' => '12 ore',
			'1_day' => '1 giorno',
			'3_days' => '3 giorni',
			'7_days' => '7 giorni',
			'30_days' => '30 giorni',
		],

		'cancel' => 'Annulla',
		'confirm' => 'Conferma',
	],
];
