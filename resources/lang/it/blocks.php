<?php
declare(strict_types=1);

return [
	'community' => [
		'latest_members' => [
			'default_title' => 'Nuovi membri',
			'joined' => 'Iscritto',
			'empty' => 'Nessun membro registrato.',
		],
		'users_in_chat' => [
			'default_title' => 'Adesso in chat',
			'total' => 'nickname in chat',
			'empty' => 'Nessun nickname presente in chat in questo momento.',
		],
		'most_active_users' => [
			'default_title' => 'Utenti più attivi',
			'irc' => 'Messaggi in chat negli ultimi 30 giorni',
			'pings' => 'Ping pubblicati negli ultimi 30 giorni',
			'pongs' => 'Pong pubblicati negli ultimi 30 giorni',
			'upvotes' => 'Upvote espressi negli ultimi 30 giorni',
			'downvotes' => 'Downvote espressi negli ultimi 30 giorni',

			'vote' => 'voto',
			'votes' => 'voti',

			'irc_message' => 'messaggio IRC',
			'irc_messages' => 'messaggi IRC',
			'irc_inactive' => 'IRC inattivo',

			'web_inactive' => 'Nessuna attività web',

			'empty' => 'Nessuna attività disponibile.',
		],
	],
	'content' => [
		'categories' => [
			'default_title' => 'Categorie',
			'empty' => 'Nessuna categoria disponibile.',
		],
		'latest_articles' => [
			'default_title' => 'Ultimi articoli',
			'published' => 'Pubblicato',
			'empty' => 'Nessun articolo pubblicato.',
		],
		'latest_audio' => [
			'empty' => 'Nessun audio condiviso.',
			'file_fallback' => 'File audio',
			'all' => 'Tutti gli audio',
		],
		'latest_video' => [
			'empty' => 'Nessun video condiviso.',
			'file_fallback' => 'Video',
			'all' => 'Tutti i video',
		],
		'pages_navigation' => [
			'default_title' => 'Pagine',
			'empty' => 'Nessuna pagina disponibile.',
		],
		'submit_article' => [
			'default_title' => 'Proponi un articolo',
			'default_button' => 'Proponi un articolo',
		],
	],
	'developer' => [
		'github_pull_requests' => [
			'state' => [
				'open' => 'Aperte',
				'closed' => 'Chiuse',
				'all' => 'Tutte',
			],
			'empty' => 'Nessuna pull request disponibile.',
			'view_all' => 'Vedi tutte le Pull Request',
		],
		'github_release' => [
			'default_title' => 'Ultime release',

			'state' => [
				'stable' => 'Release',
				'beta' => 'Beta',
				'nightly' => 'Nightly',
			],

			'view' => 'Vedi release',
			'empty' => 'Nessuna release disponibile.',

			'admin' => [
				'label' => 'GitHub Release',
				'description' => 'Mostra release stabile, beta e nightly build di un repository GitHub.',
				'repository' => 'Repository GitHub',
				'repository_help' => 'Formato: proprietario/repository',
				'title' => 'Titolo',
				'title_placeholder' => 'Ultime release',
				'show_repository' => 'Mostra repository',
				'show_date' => 'Mostra data di pubblicazione',
				'repository_error' => 'Inserisci un repository GitHub valido.',
			],
		],
		'github_repository' => [
			'default_title' => 'Repository GitHub',
			'unavailable' => 'Repository GitHub non configurato o non disponibile.',
			'branch' => 'Branch',

			'stats' => [
				'stars' => 'Stars',
				'forks' => 'Fork',
				'watchers' => 'Watch',
				'issues' => 'Issues',
			],

			'sections' => [
				'languages' => 'Linguaggi',
				'latest_release' => 'Ultima release',
				'latest_commits' => 'Ultime commit',
				'open_pull_requests' => 'Pull request aperte',
				'open_issues' => 'Issue aperte',
			],

			'files' => [
				'one' => ':count file',
				'many' => ':count file',
			],
		],
	],
	'webradio' => [
		'azuracast_mini_player' => [
			'default_title' => 'Ascolta la radio',
			'track_unavailable' => 'Brano non disponibile',
			'play' => 'Avvia la radio',
			'detach' => 'Apri il player in una finestra separata',
			'js' => [
				'pause' => 'Metti in pausa la radio',
				'play' => 'Avvia la radio',
				'playing' => 'In riproduzione',
				'paused' => 'In pausa',
				'unavailable' => 'Riproduzione non disponibile',
				'error' => 'Errore durante la riproduzione',
				'ready' => 'Pronto',
			],
		],
		'azuracast' => [
			'default_title' => 'Ascolta la radio',
			'track_unavailable' => 'Brano non disponibile',
			'play' => 'Avvia la radio',
			'detach' => 'Apri il player in una finestra separata',
			'detached' => 'Player aperto in una finestra separata',
			'on_air' => 'On Air',
			'now_playing' => 'Adesso in onda',
			'ready' => 'Pronto',
			'mute' => 'Disattiva audio',
			'volume' => 'Volume',
			'stream_unavailable' => 'Stream temporaneamente non disponibile.',
			'history_title' => 'Ultimi brani',
			'radio_unavailable' => 'Radio temporaneamente non disponibile.',
			'pause' => 'Metti in pausa la radio',
			'playing' => 'In riproduzione',
			'paused' => 'In pausa',
			'unmute' => 'Riattiva audio',
			'connecting' => 'Connessione…',
			'start_failed' => 'Impossibile avviare la radio',
			'slow_connection' => 'Connessione lenta…',
			'player_unavailable' => 'Radio non disponibile',
		],
		'azuracast_requests' => [
			'default_title' => 'Richiedi un brano',
			'unavailable_default' => 'Le richieste musicali non sono disponibili in questo momento.',
			'kicker' => 'Dedica musicale',
			'active' => 'Richieste attive',
			'unavailable_title' => 'Richieste non disponibili',
			'empty' => 'Nessun brano è attualmente richiedibile.',

			'search' => [
				'placeholder' => 'Cerca artista, titolo, album o genere',
				'aria_label' => 'Cerca un brano',
				'clear' => 'Cancella ricerca',
				'no_results' => 'Nessun brano corrisponde alla ricerca.',
			],

			'counter' => [
				'one' => ':count brano disponibile',
				'many' => ':count brani disponibili',
			],

			'untitled' => 'Brano senza titolo',
			'request' => 'Richiedi',

			'js' => [
				'no_results' => 'Nessun brano trovato',
				'sent' => 'Richiesta inviata con successo.',
				'failed' => 'Impossibile inviare la richiesta.',
				'sent_label' => 'Inviata',
				'connection_error' => 'Errore di connessione durante l’invio della richiesta.',
				'pagination_label' => 'Paginazione brani disponibili',
				'previous_page' => 'Pagina precedente',
				'next_page' => 'Pagina successiva',
				'results_count' => ':start–:end di :total brani',
				'identify_failed' => 'Impossibile identificare il brano richiesto.',
				'sending' => 'Invio...',
				'sending_status' => 'Invio della richiesta in corso...',
			],
		],
		'azuracast_stats' => [
			'default_title' => 'Statistiche radio',
			'stream_fallback' => 'Stream',
			'kicker' => 'Monitor radio',

			'status' => [
				'online' => 'Online',
				'offline' => 'Offline',
			],

			'unavailable' => [
				'title' => 'Statistiche non disponibili',
				'text' => 'Non è stato possibile recuperare i dati della stazione.',
			],

			'stats' => [
				'current_listeners' => 'Ascoltatori attuali',
				'unique_listeners' => 'Ascoltatori unici',
				'bitrate' => 'Bitrate',
				'codec' => 'Codec',
			],

			'mounts' => [
				'title' => 'Mount attivi',
				'empty' => 'Nessun mount disponibile.',
				'listeners' => [
					'one' => ':count ascoltatore',
					'many' => ':count ascoltatori',
				],
			],
		],
		'icecast_stats' => [
			'default_title' => 'Statistiche radio',
			'stream_fallback' => 'Stream',
			'kicker' => 'Monitor radio',

			'status' => [
				'online' => 'Online',
				'offline' => 'Offline',
			],

			'unavailable' => [
				'title' => 'Statistiche non disponibili',
				'text' => 'Non è stato possibile recuperare i dati del server Icecast.',
			],

			'stats' => [
				'current_listeners' => 'Ascoltatori attuali',
				'listener_peak' => 'Picco ascoltatori',
				'bitrate' => 'Bitrate',
				'codec' => 'Codec',
			],

			'mounts' => [
				'title' => 'Mount attivi',
				'empty' => 'Nessun mount disponibile.',
				'listeners' => [
					'one' => ':count ascoltatore',
					'many' => ':count ascoltatori',
				],
			],
		],
	],
];
