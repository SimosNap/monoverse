<?php

declare(strict_types=1);

return [
	'main' => [
		'header' => [
			'title' => 'Preferenze account',
			'subtitle' => 'Gestisci il tuo profilo, la privacy e le preferenze della chat.',
		],
	
		'roles' => [
			'moderator_title' => 'Moderatore della community',
			'moderator_text' => 'Il tuo account è autorizzato ad accedere agli strumenti di moderazione della community.',
			'panel_title' => 'Ruoli nella community',
		],
	
		'following' => [
			'empty' => 'Non stai ancora seguendo nessun utente.',
			'confirm_unfollow' => 'Vuoi smettere di seguire questo utente?',
			'unfollow' => 'Non seguire',
			'panel_title' => 'Utenti seguiti (:count)',
		],
	
		'chat' => [
			'panel_title' => 'Preferenze chat',
			'nickname' => 'Nickname',
			'age' => 'Età',
			'profile' => 'Profilo',
			'not_specified' => 'Non specificato',
			'male' => 'Uomo',
			'female' => 'Donna',
			'other' => 'Altro',
			'city' => 'Città',
		],
	
		'storage' => [
			'panel_title' => 'Dove salvare',
	
			'browser' => [
				'title' => 'Solo browser',
				'subtitle' => 'Privacy massima',
			],
	
			'database' => [
				'title' => 'Database sito',
				'subtitle' => 'Su tutti i dispositivi',
			],
		],
	
		'public_profile' => [
			'panel_title' => 'Profilo pubblico',
			'enabled' => 'Voglio un profilo pubblico su questo sito',
			'nickname' => 'Nickname',
			'avatar' => 'Avatar',
			'aliases' => 'Alias dell’account',
			'age' => 'Età',
			'city' => 'Città',
			'sex' => 'Sesso',
			'irc_stats' => 'Statistiche IRC',
	
			'indexing' => [
				'title' => 'Indicizzazione sui motori di ricerca',
				'help' => 'Consente a Google, Bing e agli altri motori di ricerca di mostrare il tuo profilo nei risultati.',
			],
		],
	
		'doge' => [
			'panel_title' => 'Mance Dogecoin',
			'intro' => 'Scegli da dove Monoverse deve ottenere il tuo indirizzo Dogecoin per ricevere mance dagli altri utenti.',
	
			'mydogemask' => [
				'title' => 'MyDogeMask',
				'help' => "Usa l'indirizzo del wallet MyDogeMask collegato a questo browser.",
			],
	
			'simosnap' => [
				'title' => 'Account SimosNap',
				'help' => "Usa l'indirizzo Dogecoin configurato sul tuo account SimosNap.",
				'address_configured' => 'Indirizzo Dogecoin configurato su SimosNap:',
				'address_missing' => 'Nessun indirizzo Dogecoin configurato sul tuo account SimosNap.',
			],
	
			'connect' => 'Collega MyDogeMask',
			'save' => 'Salva preferenza',
		],
	
		'privacy' => [
			'panel_title' => 'Privacy',
			'help' => "Gestisci gli utenti che hai bloccato e ripristina l'accesso ai loro profili quando lo desideri.",
			'blocked_users' => 'Utenti bloccati',
		],
	
		'actions' => [
			'save_preferences' => 'Salva preferenze',
			'clear_browser' => 'Elimina dati browser',
			'delete_database' => 'Elimina dati dal database',
			'delete_database_confirm' => 'Eliminare definitivamente i dati salvati nel database?',
			'logout' => 'Esci',
		],
		
		'js' => [
			'local_saved' => 'Preferenze salvate in questo browser.',
			'local_cleared' => 'Dati salvati nel browser eliminati.',
		
			'doge' => [
				'connecting' => 'Connessione a MyDogeMask in corso…',
				'connected_address' => 'MyDogeMask collegato. Indirizzo: :address',
				'connect_failed' => 'Impossibile collegare MyDogeMask.',
				'use_simosnap_address' => 'Verrà utilizzato l’indirizzo Dogecoin configurato sul tuo account SimosNap.',
				'wallet_connected_address' => 'MyDogeMask connesso. Indirizzo: :address',
				'configured_address' => 'Indirizzo MyDogeMask configurato: :address',
				'wallet_detected' => 'MyDogeMask rilevato. Collega il wallet per usare il suo indirizzo.',
			],
		],
	],
	
	'profile' => [
		'user' => 'Utente',
	
		'saved' => [
			'title' => 'Profilo salvato.',
			'text' => 'Le modifiche sono state memorizzate correttamente.',
		],
	
		'about' => [
			'kicker' => 'Chi sono',
			'title' => 'Presentati alla community',
		],
	
		'bio' => [
			'label' => 'Bio',
			'placeholder' => 'Scrivi qualcosa su di te...',
			'max' => 'Massimo 1000 caratteri',
			'preview_empty' => 'La tua bio comparirà qui.',
		],
	
		'motto' => [
			'label' => 'Frase personale',
			'placeholder' => 'Una frase breve che ti rappresenta',
			'optional' => 'Facoltativa',
			'max' => 'Massimo 120 caratteri',
		],
	
		'interests' => [
			'kicker' => 'Interessi',
			'title' => 'Di cosa ti piace parlare?',
	
			'items' => [
				'Music' => 'Musica',
				'Cinema' => 'Cinema',
				'TV Series' => 'Serie TV',
				'Gaming' => 'Gaming',
				'Anime' => 'Anime',
				'Sport' => 'Sport',
				'Books' => 'Libri',
				'Technology' => 'Tecnologia',
				'IRC' => 'IRC',
				'Linux' => 'Linux',
				'Mac' => 'Mac',
				'Windows' => 'Windows',
				'Radio' => 'Radio',
				'Podcast' => 'Podcast',
				'Travel' => 'Viaggi',
				'Photography' => 'Fotografia',
				'Cooking' => 'Cucina',
				'Cars and motorcycles' => 'Auto e moto',
			],
		],
	
		'links' => [
			'kicker' => 'Collegamenti',
			'title' => 'Dove trovarti',
			'website' => 'Sito web',
			'telegram' => 'Telegram',
		],
	
		'save' => [
			'title' => 'Salva il profilo',
			'help' => 'Le modifiche saranno visibili dopo il salvataggio.',
			'button' => 'Salva profilo',
		],
	
		'preview' => [
			'title' => 'Anteprima',
			'aliases' => 'Alias',
		],
	],
	
	'navigation' => [
		'aria_label' => 'Navigazione account',
		'preferences' => 'Preferenze chat',
		'profile' => 'Profilo pubblico',
		'saved' => 'Contenuti salvati',
		'articles' => 'Articoli proposti',
		'privacy' => 'Privacy',
		'moderation' => 'Moderazione',
		'logout' => 'Esci',
	],
	
	'moderation' => [
		'title' => 'Moderazione',
		'subtitle' => 'Gestisci le segnalazioni e i provvedimenti attivi nella community.',
	
		'reports' => [
			'title' => 'Segnalazioni',
			'text' => 'Esamina le segnalazioni inviate dagli utenti e intervieni sui contenuti.',
			'open' => 'Apri segnalazioni',
		],
	
		'bans' => [
			'title' => 'Utenti sospesi',
			'text' => 'Gestisci gli utenti che non possono utilizzare le funzioni della community.',
			'open' => 'Apri sospesi',
		],
	
		'mutes' => [
			'title' => 'Utenti silenziati',
			'text' => 'Gestisci gli utenti che non possono pubblicare Ping o Pong.',
			'open' => 'Apri silenziati',
		],
	],
	
	'moderation_navigation' => [
		'aria_label' => 'Navigazione moderazione',
		'dashboard' => 'Dashboard',
		'reports' => 'Segnalazioni',
		'bans' => 'Sospesi',
		'mutes' => 'Silenziati',
	],
	
	'moderation_reports' => [
		'title' => 'Segnalazioni',
		'subtitle' => 'Contenuti segnalati dagli utenti e in attesa di verifica.',
	
		'section_title' => 'Segnalazioni ricevute',
	
		'count' => [
			'one' => 'È presente una segnalazione.',
			'many' => 'Sono presenti :count segnalazioni.',
		],
	
		'empty' => [
			'title' => 'Nessuna segnalazione',
			'text' => 'Al momento non ci sono contenuti da esaminare.',
		],
	
		'reasons' => [
			'spam' => 'Spam',
			'harassment' => 'Molestie',
			'hate' => 'Contenuto offensivo',
			'violence' => 'Violenza o minacce',
			'sexual' => 'Contenuto sessuale',
			'misinformation' => 'Informazioni false',
			'privacy' => 'Violazione della privacy',
			'other' => 'Altro',
		],
	
		'status' => [
			'open' => 'Aperta',
			'reviewed' => 'Esaminata',
			'closed' => 'Chiusa',
		],
	
		'target' => [
			'ping' => 'Ping',
			'pong' => 'Pong',
			'content' => 'Contenuto',
		],
	
		'reported_by' => 'Segnalata da',
		'unknown_user' => 'utente sconosciuto',
		'date_unavailable' => 'Data non disponibile',
		'id' => 'ID',
	
		'no_description' => 'Nessuna descrizione aggiuntiva fornita.',
		'open_report' => 'Apri segnalazione',
	],
	
	'moderation_report' => [
		'title' => 'Dettaglio segnalazione',
		'subtitle' => 'Visualizza il contenuto segnalato e le informazioni della segnalazione.',
	
		'status' => 'Stato:',
		'reported_by' => 'Segnalata da:',
		'reviewed_by' => 'Gestita da:',
		'reported_content' => 'Contenuto segnalato',
	
		'status_labels' => [
			'open' => 'Aperta',
			'reviewed' => 'Esaminata',
			'closed' => 'Chiusa',
		],
	
		'reasons' => [
			'spam' => 'Spam',
			'harassment' => 'Molestie',
			'privacy' => 'Violazione della privacy',
			'illegal' => 'Contenuto illegale',
			'copyright' => 'Copyright',
			'other' => 'Altro',
		],
	
		'actions' => [
			'mark_reviewed' => 'Segna come esaminata',
			'close' => 'Chiudi segnalazione',
			'delete_content' => 'Elimina contenuto',
			'delete_confirm' => 'Vuoi davvero eliminare definitivamente il contenuto segnalato?',
		],
	],
	
	'moderation_mutes' => [
		'title' => 'Utenti silenziati',
		'subtitle' => 'Gestisci gli utenti che non possono pubblicare Ping o Pong.',
	
		'section' => [
			'title' => 'Silenziamenti attivi',
			'help' => 'Gli utenti silenziati possono accedere, ma non possono pubblicare Ping o Pong.',
		],
	
		'empty' => [
			'title' => 'Nessun utente silenziato',
			'text' => 'Non risultano silenziamenti attivi.',
		],
	
		'user' => [
			'unavailable' => 'Profilo non disponibile',
			'deleted_or_missing' => 'Profilo eliminato o non ancora creato',
			'public_unavailable' => 'Profilo pubblico non disponibile.',
			'avatar_alt' => 'Avatar di :name',
		],
	
		'status' => 'Silenziato',
	
		'meta' => [
			'reason' => 'Motivo',
			'no_reason' => 'Nessun motivo indicato',
			'duration' => 'Durata',
			'permanent' => 'Permanente',
			'applied_on' => 'Applicato il',
			'moderator' => 'Moderatore',
		],
	
		'actions' => [
			'profile' => 'Profilo',
			'unmute' => 'Rimuovi mute',
			'unmute_confirm' => 'Rimuovere il silenziamento?',
		],
	],
	
	'moderation_bans' => [
		'title' => 'Utenti sospesi',
		'subtitle' => 'Gestisci gli utenti che non possono utilizzare le funzioni della community.',
	
		'section' => [
			'title' => 'Sospensioni attive',
			'help' => 'Gli utenti sospesi non possono utilizzare le funzioni della community.',
		],
	
		'empty' => [
			'title' => 'Nessun utente sospeso',
			'text' => 'Non risultano sospensioni attive.',
		],
	
		'user' => [
			'unavailable' => 'Profilo non disponibile',
			'deleted_or_missing' => 'Profilo eliminato o non ancora creato',
			'public_unavailable' => 'Profilo pubblico non disponibile.',
			'avatar_alt' => 'Avatar di :name',
		],
	
		'status' => 'Sospeso',
	
		'meta' => [
			'reason' => 'Motivo',
			'no_reason' => 'Nessun motivo indicato',
			'duration' => 'Durata',
			'permanent' => 'Permanente',
			'applied_on' => 'Applicato il',
			'moderator' => 'Moderatore',
		],
	
		'actions' => [
			'profile' => 'Profilo',
			'unban' => 'Riattiva',
			'unban_confirm' => 'Riattivare questo utente?',
		],
	],
	
	'articles' => [
		'title' => 'Articoli proposti',
		'subtitle' => 'Controlla lo stato degli articoli che hai inviato alla Chanzine.',
		'submit' => 'Proponi un articolo',
	
		'empty' => 'Non hai ancora proposto nessun articolo.',
	
		'status' => [
			'submitted' => 'In revisione',
			'published' => 'Pubblicato',
			'rejected' => 'Rifiutato',
		],
	
		'submitted_on' => 'Inviato il',
	
		'actions' => [
			'edit' => 'Modifica',
			'view' => 'Visualizza articolo',
		],
	
		'rejection' => [
			'title' => 'Motivo del rifiuto',
		],
	],
	
	'article_edit' => [
		'eyebrow' => 'Chanzine',
		'title' => 'Modifica proposta',
		'intro' => "Puoi modificare questa proposta finché è ancora in attesa di revisione da parte dell'amministratore.",
	
		'article' => [
			'title' => 'Articolo',
			'help' => 'Modifica titolo, introduzione e contenuto della proposta.',
		],
	
		'fields' => [
			'title' => 'Titolo',
			'excerpt' => 'Excerpt',
			'excerpt_help' => "Una breve introduzione che accompagnerà l'articolo.",
			'content' => 'Contenuto',
			'content_help' => "Modifica il contenuto dell'articolo in Markdown.",
			'category' => 'Categoria',
			'category_placeholder' => 'Seleziona una categoria',
			'cover' => 'Cover',
			'cover_replace_help' => 'Carica una nuova immagine solo se vuoi sostituire la cover attuale.',
			'cover_default_help' => 'JPEG, PNG o WebP. Se non ne carichi una verrà utilizzata la cover predefinita della Chanzine.',
		],
	
		'settings' => [
			'title' => 'Impostazioni',
			'help' => 'Aggiorna le informazioni della proposta.',
		],
	
		'save' => [
			'title' => 'Salva modifiche',
			'help' => 'La proposta resterà in attesa di revisione.',
			'cancel' => 'Annulla',
			'submit' => 'Salva modifiche',
		],
	],
	
	'saved' => [
		'title' => 'Contenuti salvati',
		'subtitle' => 'Ritrova i Ping e gli articoli che hai salvato.',
		'empty' => 'Non hai ancora salvato nessun contenuto.',
	
		'card' => [
			'article' => 'Articolo',
			'ping' => 'Ping',
			'empty_ping' => 'Ping senza contenuto',
			'saved' => 'Salvato',
			'remove' => 'Rimuovi',
			'remove_confirm' => 'Rimuovere questo contenuto dai salvati?',
		],
	],
	
	'suspended' => [
		'title' => 'Account sospeso',
		'subtitle' => "L'accesso alla Community è stato sospeso.",

		'intro' => 'Il tuo account non può attualmente accedere alle funzionalità della Community in quanto è stato sospeso da un moderatore.',

		'status' => 'Stato',
		'status_value' => 'Sospeso',

		'reason' => 'Motivazione',
		'reason_unspecified' => 'Non specificata.',

		'duration' => 'Durata',
		'until' => 'Fino al',
		'permanent' => 'Permanente',

		'appeal' => 'Se ritieni che il provvedimento sia stato applicato per errore, puoi contattare lo staff per richiedere una verifica.',

		'leave' => [
			'title' => 'Lascia la Community',
			'intro' => 'Se non desideri più far parte della Community puoi eliminare definitivamente il tuo profilo pubblico.',

			'profile_deleted' => 'Il tuo profilo pubblico verrà eliminato.',
			'removed_from_members' => 'Non comparirai più tra gli utenti della Community.',
			'suspension_retained' => 'Il provvedimento di sospensione rimarrà associato al tuo account OAuth.',

			'confirm' => 'Eliminare definitivamente il profilo pubblico?',
			'delete' => 'Elimina il mio profilo pubblico',
		],

		'logout' => 'Esci',
	],
	
	'blocked' => [
		'title' => 'Privacy',
		'subtitle' => 'Gestisci gli utenti bloccati e le impostazioni sulla privacy del tuo account.',
	
		'section_title' => 'Utenti bloccati',
		'section_help' => 'Gli utenti bloccati non possono interagire con te. Non vedranno i tuoi Ping e tu non vedrai i loro.',
	
		'empty_title' => 'Nessun utente bloccato',
		'empty_text' => 'La tua lista è vuota. Gli utenti che bloccherai compariranno qui.',
	
		'user_unavailable' => 'Utente non più disponibile',
		'account_missing' => 'Account SimosNap non più esistente',
	
		'blocked_on' => 'Bloccato il',
		'unblock' => 'Sblocca',
	],
];
