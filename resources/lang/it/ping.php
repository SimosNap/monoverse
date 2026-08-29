<?php

declare(strict_types=1);

return [
	'areas' => [
		'before' => 'Contenuti prima dei Ping',
		'after' => 'Contenuti dopo i Ping',
		'sidebar' => 'Contenuti laterali',
	],

	'feed' => [
		'label' => 'Filtra Ping',
		'all' => 'Tutti i Ping',
		'following' => 'Seguiti',
		'interactions' => 'Interazioni',
		'audio' => 'Audio',
		'video' => 'Video',
		'empty' => 'Non ci sono ancora Ping.',
	],

	'search' => [
		'label' => 'Cerca nei Ping',
		'placeholder' => 'Cerca...',
		'submit' => 'Cerca',
	],

	'sidebar' => [
		'explore' => 'Esplora',
	],

	'report' => [
		'title' => 'Segnala contenuto',
		'close' => 'Chiudi',
		'help' => 'Perché stai segnalando questo contenuto?',

		'reasons' => [
			'spam' => 'Spam',
			'harassment' => 'Molestie o insulti',
			'privacy' => 'Dati personali',
			'illegal' => 'Contenuto illegale',
			'copyright' => 'Copyright',
			'other' => 'Altro',
		],

		'description' => 'Descrizione',
		'description_placeholder' => 'Fornisci qualche dettaglio utile ai moderatori...',

		'cancel' => 'Annulla',
		'submit' => 'Invia segnalazione',
	],
	'composer' => [
		'placeholder' => 'A cosa stai pensando?',
		'publish' => 'Pubblica',
		'media' => [
			'attachments' => 'Allegati',
			'attach_files' => 'Allega file',
			'close' => 'Chiudi allegati',

			'allowed' => 'Consentiti:',
			'images' => 'immagini',
			'pdf' => 'PDF',
			'audio_up_to' => 'audio fino a',
			'video_up_to' => 'video fino a',

			'dropzone_title' => 'Trascina qui i file',
			'dropzone_help' => 'oppure clicca per selezionarli',

			'audio' => [
				'details' => 'Dettagli audio',
				'optional' => 'Opzionali',
				'title' => 'Titolo',
				'title_placeholder' => 'Titolo del brano o del mix',
				'artist' => 'Artista / autore',
				'artist_placeholder' => 'Artista, DJ o autore',
				'tracklist' => 'Tracklist',
				'tracklist_placeholder' => "00:00 Artista – Brano\n04:32 Artista – Brano",
			],

			'upload' => [
				'uploading' => 'Caricamento…',
				'progress' => 'Avanzamento caricamento',
			],
		],
	],
	'card' => [
		'user' => 'Utente',

		'author' => [
			'unavailable' => 'Utente non più disponibile',
			'private_profile' => 'Ha scelto di non avere un profilo pubblico',
			'account_missing' => 'Account SimosNap non più esistente',
		],

		'doge' => [
			'tip_label' => 'Mancia Dogecoin',
			'tip' => 'Mancia',
		],

		'editor' => [
			'save' => 'Salva',
			'cancel' => 'Annulla',
		],

		'comments' => 'Pong',

		'actions' => [
			'more' => 'Altre azioni',
			'save' => 'Salva',
			'unsave' => 'Rimuovi dai salvati',
			'edit' => 'Modifica',
			'block_user' => 'Blocca utente',
			'report' => 'Segnala',
			'delete' => 'Elimina',
			'delete_confirm' => 'Vuoi davvero eliminare questo Ping?',
			'upvote' => 'Voto positivo',
			'downvote' => 'Voto negativo',
		],

		'link' => [
			'video' => 'VIDEO',
			'audio' => 'MUSICA',
			'default' => 'LINK',
		],

		'media' => [
			'video_unsupported' => 'Il tuo browser non supporta la riproduzione video.',
			'audio_unsupported' => 'Il tuo browser non supporta la riproduzione audio.',

			'audio_file' => 'File audio',
			'audio' => 'Audio',
			'waveform_unavailable' => 'Waveform non disponibile',

			'play' => 'Riproduci',
			'mute' => 'Disattiva audio',
			'volume' => 'Volume',
			'tracklist' => 'Tracklist',
		],
	],

	'pong' => [
		'user' => 'Utente',

		'author' => [
			'unavailable' => 'Utente non più disponibile',
			'private_profile' => 'Ha scelto di non avere un profilo pubblico',
			'account_missing' => 'Account SimosNap non più esistente',
		],

		'doge' => [
			'tip_label' => 'Mancia Dogecoin',
		],

		'editor' => [
			'save' => 'Salva',
			'cancel' => 'Annulla',
		],

		'actions' => [
			'edit' => 'Modifica',
			'delete' => 'Elimina',
			'report' => 'Segnala',
			'block_user' => 'Blocca utente',
			'delete_confirm' => 'Vuoi davvero eliminare questo Pong?',
			'block_confirm' => 'Vuoi bloccare questo utente? Non vedrai più i suoi Ping e Pong.',
		],
	],

	'show' => [
		'areas' => [
			'before' => 'Contenuti prima del Ping',
			'after' => 'Contenuti dopo il Ping',
			'sidebar' => 'Contenuti laterali',
		],

		'blocked' => [
			'title' => 'Contenuto non disponibile',
		],

		'pong' => [
			'placeholder' => 'Scrivi un Pong...',
			'publish' => 'Pubblica Pong',
			'title' => 'Pong',
			'empty' => 'Ancora nessun Pong.',
		],
	],

	'js' => [
		'upload' => [
			'processing' => 'Elaborazione…',
			'uploading' => 'Caricamento…',
			'failed' => 'Caricamento non riuscito',
		],

		'lightbox' => [
			'previous_image' => 'Immagine precedente',
			'next_image' => 'Immagine successiva',
		],

		'audio' => [
			'pause' => 'Pausa',
			'play' => 'Riproduci',
			'unmute' => 'Attiva audio',
			'mute' => 'Disattiva audio',
		],

		'attachments' => [
			'label' => 'Allegati',
			'one' => '1 allegato',
			'count' => ':count allegati',
			'remove' => 'Rimuovi :file',

			'audio_not_allowed' => 'Il caricamento di allegati audio non è consentito.',
			'video_not_allowed' => 'Il caricamento di allegati video non è consentito.',

			'audio_too_large' => 'Il file audio supera il limite di :max MB.',
			'video_too_large' => 'Il file video supera il limite di :max MB.',
		],

	],

	'flash' => [
		'text_required' => 'Scrivi un testo prima di pubblicare il Ping.',
	],

];
