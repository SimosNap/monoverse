<?php

declare(strict_types=1);

return [
	'areas' => [
		'before' => 'Contenuti prima degli eventi',
		'sidebar' => 'Contenuti laterali degli eventi',
		'after' => 'Contenuti dopo gli eventi',
	],

	'header' => [
		'title' => 'Eventi',
		'subtitle' => 'Scopri i prossimi eventi della community.',
		'submit_event' => 'Proponi un evento',
	],

	'empty' => 'Non ci sono eventi in programma.',

	'event' => [
		'open_aria' => 'Apri :title',
		'open' => 'Vedi evento',
	],

	'event_page' => [
		'starts_at' => 'Inizio:',
		'ends_at' => 'Fine:',
		'external_link' => 'Vai al sito dell’evento',
		'all_events' => 'Tutti gli eventi',
		'show_map' => 'Mostra mappa',
		'map_title' => 'Dove si svolge l’evento',
		'close_map' => 'Chiudi la mappa',

		'areas' => [
			'before' => 'Contenuti prima dell’evento',
			'sidebar' => 'Informazioni sull’evento',
			'sidebar_widgets' => 'Contenuti laterali dell’evento',
			'after' => 'Contenuti dopo l’evento',
		],
	],

	'submit' => [
		'eyebrow' => 'Community',
		'title' => 'Proponi un evento',
		'intro' => 'Segnala un evento alla community. La proposta sarà controllata prima della pubblicazione.',

		'event' => [
			'title' => 'Evento',
			'help' => 'Inserisci le informazioni principali dell’evento.',
		],

		'schedule' => [
			'title' => 'Programmazione',
			'help' => 'Indica quando si svolgerà l’evento.',
		],

		'location' => [
			'title' => 'Luogo',
			'help' => 'Puoi indicare il luogo e, se disponibili, le coordinate per mostrarlo sulla mappa.',
		],

		'cover' => [
			'title' => 'Immagine',
			'help' => 'Puoi aggiungere un’immagine di copertina per l’evento.',
		],

		'publish' => [
			'title' => 'Invia proposta',
			'help' => 'La proposta sarà inviata agli amministratori e potrà essere modificata prima della pubblicazione.',
			'cancel' => 'Annulla',
			'submit' => 'Proponi evento',
		],

		'fields' => [
			'title' => 'Titolo',
			'description' => 'Descrizione',
			'description_help' => 'Puoi usare Markdown per formattare il testo.',
			'starts_at' => 'Inizio',
			'ends_at' => 'Fine',
			'ends_at_help' => 'Facoltativo.',
			'location' => 'Luogo',
			'latitude' => 'Latitudine',
			'longitude' => 'Longitudine',
			'coordinates_help' => 'Le coordinate sono facoltative, ma devono essere inserite entrambe.',
			'external_url' => 'Link esterno',
			'external_url_help' => 'Facoltativo. Può essere il sito ufficiale o la pagina dell’evento.',
			'cover' => 'Immagine di copertina',
			'cover_help' => 'Formati supportati: JPEG, PNG e WebP.',
		],
	],
];
