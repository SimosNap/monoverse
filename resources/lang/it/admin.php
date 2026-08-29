<?php
declare(strict_types=1);

return [
	'layout' => [
		'logout' => 'Esci',
	],
	'navigation' => [
		'dashboard' => 'Dashboard',
		'settings' => 'Impostazioni',
		'blocks' => 'Blocchi',
		'pages' => 'Pagine',
		'chat' => 'Chat',
		'moderators' => 'Moderatori',
		'chanzine' => 'Chanzine',
		'categories' => 'Categorie',
	],
	'settings' => [
		'page' => [
			'kicker' => 'Sistema',
			'title' => 'Impostazioni',
			'description' => 'Configura le impostazioni principali del sito.',
		],
	
		'success' => [
			'title' => 'Impostazioni salvate.',
		],
	
		'identity' => [
			'title' => 'Identità del sito',
			'description' => 'Questi dati vengono utilizzati nel titolo e nell’intestazione delle pagine pubbliche.',
	
			'site_name' => [
				'label' => 'Nome sito',
				'help' => 'Il nome principale mostrato nel sito.',
			],
	
			'tagline' => [
				'label' => 'Slogan',
				'help' => 'Una breve descrizione del progetto o della community.',
			],
		],
	
		'language' => [
			'title' => 'Lingua',
			'description' => 'Configura la lingua predefinita dell’interfaccia e le lingue disponibili per il sito.',
			'default_label' => 'Lingua predefinita del sito',
			'default_help' => 'Questa lingua viene utilizzata come predefinita per l’interfaccia del sito.',
			'available_label' => 'Lingue disponibili',
			'available_help' => 'Se è disponibile una sola lingua, Monoverse funziona automaticamente come sito monolingua e non mostra alcun selettore di lingua.',
	
			'locales' => [
				'it' => 'Italiano',
				'en' => 'English',
			],
		],
	
		'brand' => [
			'title' => 'Brand',
			'description' => 'Configura le immagini utilizzate per identificare il sito nei browser, nei dispositivi mobili e nelle condivisioni social.',
	
			'logo' => [
				'label' => 'Logo del sito',
				'alt' => 'Logo',
				'delete' => 'Elimina logo',
				'delete_confirm' => 'Eliminare il logo?',
				'help' => 'Logo principale del sito.',
			],
	
			'favicon' => [
				'label' => 'Favicon',
				'alt' => 'Favicon',
				'delete' => 'Elimina favicon',
				'delete_confirm' => 'Eliminare la favicon?',
				'help' => 'Icona mostrata nelle schede del browser. Sono supportati ICO, PNG e SVG.',
			],
	
			'apple_touch_icon' => [
				'label' => 'Apple Touch Icon',
				'alt' => 'Apple Touch Icon',
				'delete' => 'Elimina Apple Touch Icon',
				'delete_confirm' => 'Eliminare la Apple Touch Icon?',
				'help' => 'Icona utilizzata quando il sito viene aggiunto alla schermata Home di iPhone e iPad. Dimensione consigliata: 180×180 pixel.',
			],
	
			'og_image' => [
				'label' => 'Immagine OpenGraph predefinita',
				'alt' => 'OpenGraph',
				'delete' => 'Elimina immagine OpenGraph',
				'delete_confirm' => 'Eliminare l’immagine OpenGraph?',
				'help' => 'Immagine utilizzata nelle condivisioni social quando una pagina non dispone di una propria immagine. Dimensione consigliata: 1200×630 pixel.',
			],
		],
	
		'seo' => [
			'title' => 'SEO e URL',
			'description' => 'Configura l’indirizzo principale del sito e la descrizione predefinita utilizzata dai motori di ricerca.',
	
			'site_url' => [
				'label' => 'URL del sito',
				'help_before' => 'Inserisci l’indirizzo completo, comprensivo di',
				'help_after' => 'Verrà utilizzato per canonical, sitemap, OpenGraph e URL assoluti.',
			],
	
			'meta_description' => [
				'label' => 'Meta description predefinita',
				'placeholder' => 'Descrivi brevemente il sito o la community.',
				'help' => 'Utilizzata quando una pagina non dispone di una descrizione specifica.',
			],
		],
	
		'github' => [
			'title' => 'GitHub',
			'description' => 'Configura l’accesso alle API GitHub utilizzato dai widget Developer.',
			'token_label' => 'GitHub API Token',
			'token_configured' => 'Token configurato — lascia vuoto per mantenerlo',
			'token_placeholder' => 'ghp_...',
			'token_help' => 'Token GitHub opzionale utilizzato per aumentare il limite delle richieste API. Se un token è già configurato, lascia il campo vuoto per mantenerlo invariato.',
		],
	
		'pages_navigation' => [
			'title' => 'Navigazione pagine',
			'description' => 'Scegli come rendere accessibili le pagine dinamiche pubblicate.',
			'enable' => 'Mostra le pagine dinamiche nel menu principale',
			'help_before' => 'Se scegli',
			'main_menu' => 'Menu principale',
			'help_after' => 'una sola pagina verrà mostrata come link diretto; con due o più pagine verrà creato automaticamente un menu a discesa.',
		],
	
		'media' => [
			'title' => 'Allegati audio e video',
			'description' => 'Configura quali tipi di allegati multimediali possono essere caricati nei Ping e il limite massimo consentito per ciascun file.',
	
			'audio' => [
				'enable' => 'Consenti upload allegati audio',
				'enable_help' => 'Disabilitando questa opzione verranno impediti soltanto i nuovi upload audio. Gli allegati già pubblicati continueranno a essere disponibili.',
				'limit_label' => 'Limite upload audio (MB)',
				'limit_help' => 'Dimensione massima consentita per ogni allegato audio.',
			],
	
			'video' => [
				'enable' => 'Consenti upload allegati video',
				'enable_help' => 'Disabilitando questa opzione verranno impediti soltanto i nuovi upload video. I video già pubblicati continueranno a essere disponibili.',
				'limit_label' => 'Limite upload video (MB)',
				'limit_help' => 'Dimensione massima consentita per ogni allegato video.',
			],
	
			'require_text' => [
				'label' => 'Richiedi testo nei Ping con allegati audio o video',
				'help' => 'Se disabilitata, gli utenti potranno pubblicare un Ping composto soltanto da uno o più allegati audio o video. Un Ping senza testo e senza allegati continuerà a non essere consentito.',
			],
		],
	
		'chanzine' => [
			'title' => 'Chanzine',
			'description' => 'Configura le funzionalità editoriali disponibili agli utenti.',
			'user_submissions' => 'Consenti agli utenti di proporre articoli',
			'user_submissions_help' => 'Gli articoli proposti dagli utenti non vengono pubblicati automaticamente. Restano in attesa di revisione finché un amministratore non li approva, modifica o rifiuta.',
		],
	
		'crypto' => [
			'title' => 'Crypto Tips',
			'description' => 'Configura le mance in Dogecoin tra gli utenti della community.',
	
			'enable' => [
				'label' => 'Abilita mance Dogecoin',
				'help' => 'Consente agli utenti di configurare un indirizzo Dogecoin per ricevere mance tramite MyDogeMask o il proprio account SimosNap. Monoverse non custodisce fondi né chiavi private.',
			],
	
			'profiles' => [
				'label' => 'Mostra pulsante mance nei profili',
				'help' => 'Mostra l’azione per inviare una mancia Dogecoin nei profili pubblici degli utenti che hanno configurato un indirizzo di ricezione.',
			],
	
			'pings' => [
				'label' => 'Mostra pulsante mance nei Ping',
				'help' => 'Mostra l’azione Ð Mancia nei Ping pubblicati dagli utenti che hanno configurato un indirizzo Dogecoin di ricezione.',
			],
		],
	
		'save' => 'Salva impostazioni',
	],
	'widgets_area' => [
		'description' => 'Organizza i widget visualizzati in questa area.',
		'all_areas' => 'Tutte le aree',
		'add_widget' => 'Aggiungi widget',
	
		'empty' => [
			'title' => 'Questa area è vuota',
			'description' => 'Aggiungi il primo widget scegliendolo dalla libreria.',
		],
	
		'drag' => 'Trascina per riordinare',
	
		'status' => [
			'active' => 'Attivo',
			'disabled' => 'Disattivato',
		],
	
		'width' => 'Larghezza',
	
		'actions' => [
			'edit' => 'Modifica',
			'enable' => 'Attiva',
			'disable' => 'Disattiva',
			'delete' => 'Elimina',
		],
	
		'delete_confirm' => 'Vuoi davvero eliminare questo widget?',
	],
	'areas' => [
		'landing_chat' => [
			'before_entry' => 'Prima della sezione di accesso',
			'entry_left_before' => 'Colonna sinistra — prima del contenuto',
			'entry_left_after' => 'Colonna sinistra — dopo il contenuto',
			'after_entry' => 'Dopo la sezione di accesso',
			'before_footer' => 'Prima del footer',
		],
	
		'members' => [
			'before_content' => 'Prima dell’elenco membri',
			'sidebar' => 'Sidebar',
			'after_content' => 'Dopo l’elenco membri',
		],
	
		'ping' => [
			'before_content' => 'Prima dei Ping',
			'sidebar' => 'Sidebar',
			'after_content' => 'Dopo i Ping',
		],
	
		'ping_show' => [
			'before_content' => 'Prima del Ping',
			'sidebar' => 'Sidebar',
			'after_content' => 'Dopo il Ping',
		],
	
		'profile' => [
			'before_content' => 'Prima del profilo',
			'sidebar' => 'Sidebar',
			'after_content' => 'Dopo il profilo',
		],
	
		'chanzine' => [
			'before_content' => 'Prima degli articoli',
			'sidebar' => 'Sidebar',
			'after_content' => 'Dopo gli articoli',
		],
	
		'chanzine_article' => [
			'before_content' => 'Prima dell’articolo',
			'sidebar' => 'Sidebar',
			'after_content' => 'Dopo l’articolo',
		],
	
		'account' => [
			'sidebar' => 'Sidebar',
		],
	],
	'blocks' => [
		'pages' => [
			'landing_chat' => 'Landing Chat',
			'members' => 'Elenco membri',
			'ping' => 'Timeline',
			'ping_show' => 'Dettaglio Ping',
			'profile' => 'Profilo pubblico',
			'account' => 'Area personale',
			'chanzine' => 'Chanzine',
			'chanzine_article' => 'Articolo Chanzine',
		],
	
		'dynamic_areas' => [
			'content' => 'Contenuto',
			'sidebar' => 'Sidebar',
		],
	
		'messages' => [
			'created' => 'Widget creato.',
			'saved' => 'Widget salvato.',
			'enabled' => 'Widget attivato.',
			'disabled' => 'Widget disattivato.',
			'deleted' => 'Widget eliminato.',
			'order_saved' => 'Ordine widget salvato.',
		],
	
		'errors' => [
			'invalid_session' => 'Sessione amministrativa non valida.',
			'invalid_area' => 'Area widget non valida.',
			'invalid_order' => 'Ordine widget non valido.',
			'order_mismatch' => 'L’elenco dei widget non corrisponde all’area.',
		],
	],
	'blocks_page' => [
		'kicker' => 'Composizione del sito',
		'title' => 'Widget',
		'description' => 'Scegli una pagina e gestisci i widget disponibili nelle diverse aree del tema.',
	
		'empty' => 'Il tema attivo non dispone di aree configurabili.',
	
		'page' => 'Pagina',
	
		'widget_count' => [
			'one' => ':count widget',
			'many' => ':count widget',
		],
	
		'area' => [
			'empty' => 'Nessun widget configurato.',
	
			'active' => [
				'one' => ':count attivo',
				'many' => ':count attivi',
			],
	
			'configured' => [
				'one' => ':count configurato',
				'many' => ':count configurati',
			],
	
			'of' => 'su',
		],
	
		'manage' => 'Gestisci',
	],
	'block_library' => [
		'title' => 'Libreria Blocchi',
		'description' => 'Scegli il tipo di blocco da aggiungere.',
	],
	'block_edit' => [
		'fallback_label' => 'Widget',
	
		'kicker' => [
			'new' => 'Nuovo widget',
			'edit' => 'Configurazione widget',
		],
	
		'description' => 'Modifica contenuto, larghezza e visibilità del widget.',
	
		'content' => [
			'title' => 'Contenuto',
			'description' => 'Configura le informazioni mostrate dal widget.',
		],
	
		'settings' => [
			'title' => 'Impostazioni',
			'description' => 'Nome, titolo e dimensioni del widget.',
		],
	
		'name' => [
			'label' => 'Nome interno',
			'placeholder' => 'Es. Banner estate',
			'help' => 'Visibile soltanto nel pannello amministrativo.',
		],
	
		'public_title' => [
			'label' => 'Titolo pubblico',
			'placeholder' => 'Facoltativo',
		],
	
		'width' => [
			'label' => 'Larghezza',
			'full' => 'Intera larghezza',
			'three_quarters' => 'Tre quarti',
			'two_thirds' => 'Due terzi',
			'half' => 'Metà',
			'one_third' => 'Un terzo',
			'one_quarter' => 'Un quarto',
		],
	
		'enabled' => [
			'label' => 'Widget attivo',
			'description' => 'Il widget viene mostrato nell’area assegnata.',
		],
	
		'actions' => [
			'cancel' => 'Annulla',
			'create' => 'Crea widget',
			'save' => 'Salva widget',
		],
	],
	'login' => [
		'title' => 'Accesso amministrazione',
		'description' => 'Inserisci le credenziali amministrative per continuare.',
		'username' => 'Username',
		'password' => 'Password',
		'submit' => 'Accedi',
	],
	'articles' => [
		'title' => 'Articoli',
		'description' => 'Gestisci gli articoli della Chanzine.',
	
		'actions' => [
			'new' => 'Nuovo articolo',
			'review' => 'Revisiona',
			'reject' => 'Rifiuta',
			'edit' => 'Modifica',
			'publish' => 'Pubblica',
			'delete' => 'Elimina',
			'create_first' => 'Crea il primo articolo',
		],
	
		'submissions' => [
			'title' => 'Proposte in attesa',
			'description' => 'Articoli inviati dagli utenti e in attesa di revisione.',
			'default_user' => 'Utente',
			'status' => 'In revisione',
			'rejection_reason' => 'Motivo del rifiuto',
		],
	
		'empty' => [
			'title' => 'Nessun articolo',
			'description' => 'Non ci sono ancora articoli nella Chanzine.',
		],
	
		'publication' => [
			'not_published' => 'Non ancora pubblicato',
		],
	
		'status' => [
			'published' => 'Pubblicato',
			'draft' => 'Bozza',
		],
	
		'confirm' => [
			'delete' => 'Eliminare definitivamente questo articolo?',
		],
	],
	'article_form' => [
		'title' => [
			'create' => 'Nuovo articolo',
			'edit' => 'Modifica articolo',
		],
	
		'description' => [
			'create' => 'Crea una nuova bozza per Chanzine.',
			'edit' => 'Modifica il contenuto e le impostazioni dell’articolo.',
		],
	
		'back' => 'Torna agli articoli',
	
		'submission' => [
			'from' => 'Proposta inviata da',
			'default_user' => 'Utente',
			'on' => 'il',
			'pending' => 'L’articolo è in attesa di revisione e non è ancora pubblico.',
		],
	
		'fields' => [
			'title' => [
				'label' => 'Titolo',
				'placeholder' => 'Inserisci il titolo dell’articolo',
			],
	
			'excerpt' => [
				'label' => 'Excerpt',
				'help' => 'Una breve introduzione che verrà mostrata nell’elenco degli articoli.',
				'placeholder' => 'Scrivi una breve descrizione dell’articolo',
			],
	
			'content' => [
				'label' => 'Contenuto',
				'help' => 'Scrivi il contenuto dell’articolo in Markdown.',
				'placeholder' => 'Inizia a scrivere il tuo articolo...',
			],
	
			'slug' => [
				'label' => 'Slug',
				'placeholder' => 'titolo-del-mio-articolo',
				'help' => 'Verrà utilizzato nell’indirizzo pubblico dell’articolo.',
			],
		],
	
		'publication' => [
			'title' => 'Pubblicazione',
			'save_changes' => 'Salva modifiche',
			'save_draft' => 'Salva bozza',
			'save_publish' => 'Salva e pubblica',
			'confirm_publish' => 'Salvare le modifiche e pubblicare questa proposta?',
			'cancel' => 'Annulla',
		],
	
		'address' => [
			'title' => 'Indirizzo articolo',
		],
	
		'category' => [
			'title' => 'Categoria',
			'label' => 'Categoria articolo',
			'select' => 'Seleziona una categoria',
			'help' => 'Ogni articolo deve appartenere a una categoria.',
		],
	
		'cover' => [
			'title' => 'Cover',
			'current_alt' => 'Cover attuale dell’articolo',
			'empty' => 'Nessuna cover selezionata',
			'replace' => 'Sostituisci cover',
			'upload' => 'Carica cover',
			'formats' => 'Formati accettati: JPG, PNG e WebP.',
		],
	],
	'categories' => [
		'title' => 'Categorie Chanzine',
		'description' => 'Gestisci le categorie disponibili per gli articoli della Chanzine.',
	
		'actions' => [
			'new' => 'Nuova categoria',
			'create_first' => 'Crea la prima categoria',
			'edit' => 'Modifica',
			'delete' => 'Elimina',
		],
	
		'empty' => [
			'title' => 'Nessuna categoria',
			'description' => 'Non ci sono ancora categorie disponibili per la Chanzine.',
		],
	
		'order' => 'Ordine:',
	
		'confirm' => [
			'delete' => 'Eliminare questa categoria? Gli articoli associati resteranno senza categoria.',
		],
	],
	'category_form' => [
		'title' => [
			'create' => 'Nuova categoria',
			'edit' => 'Modifica categoria',
		],
	
		'description' => [
			'create' => 'Crea una nuova categoria per gli articoli della Chanzine.',
			'edit' => 'Modifica nome, descrizione, slug e ordine della categoria.',
		],
	
		'back' => 'Torna alle categorie',
	
		'fields' => [
			'name' => [
				'label' => 'Nome',
				'placeholder' => 'Es. Community',
				'help' => 'Il nome visibile nel pannello e negli articoli.',
			],
	
			'description' => [
				'label' => 'Descrizione',
				'placeholder' => 'Descrivi brevemente i contenuti della categoria.',
				'help' => 'Verrà mostrata nella pagina pubblica della categoria.',
			],
	
			'slug' => [
				'label' => 'Slug',
				'placeholder' => 'Es. community',
				'help' => 'Usato negli URL. Se lasciato vuoto, viene generato automaticamente dal nome.',
			],
	
			'sort_order' => [
				'label' => 'Ordine',
				'help' => 'Le categorie con valore più basso vengono mostrate prima.',
			],
		],
	
		'actions' => [
			'cancel' => 'Annulla',
			'create' => 'Crea categoria',
			'save' => 'Salva modifiche',
		],
	],
	'pages' => [
		'title' => 'Pagine',
		'description' => 'Crea pagine pubbliche composte tramite widget.',
	
		'actions' => [
			'new' => 'Nuova pagina',
			'edit' => 'Modifica',
			'delete' => 'Elimina',
		],
	
		'empty' => [
			'title' => 'Nessuna pagina creata',
			'description' => 'Crea una pagina e componila usando le aree Contenuto e Sidebar.',
		],
	
		'table' => [
			'page' => 'Pagina',
			'status' => 'Stato',
			'widgets' => 'Widget',
			'actions' => 'Azioni',
		],
	
		'status' => [
			'draft' => 'Bozza',
			'published' => 'Pubblicata',
			'private' => 'Privata',
		],
	
		'areas' => [
			'content' => 'Contenuto',
			'sidebar' => 'Sidebar',
		],
	
		'confirm' => [
			'delete' => 'Eliminare definitivamente questa pagina?',
		],
	],
	'page_form' => [
		'title' => [
			'create' => 'Nuova pagina',
			'edit' => 'Modifica pagina',
		],
	
		'description' => 'Configura la pagina e componi il contenuto tramite i widget.',
		'back' => 'Torna alle pagine',
	
		'fields' => [
			'title' => 'Titolo',
	
			'slug' => [
				'label' => 'Slug',
				'help' => 'Indirizzo pubblico della pagina, ad esempio :example.',
			],
	
			'status' => [
				'label' => 'Stato',
				'help' => 'Solo le pagine pubblicate sono accessibili pubblicamente.',
			],
	
			'menu_label' => [
				'label' => 'Etichetta menu',
				'placeholder' => 'Lascia vuoto per usare il titolo',
				'help' => 'Testo breve da mostrare nella navigazione.',
			],
	
			'navigation_group' => [
				'label' => 'Gruppo',
				'help' => 'Gruppo utilizzato dai widget di navigazione. Lascia default se non serve una separazione specifica.',
			],
	
			'sort_order' => [
				'label' => 'Ordine',
				'help' => 'Valori più bassi vengono mostrati per primi.',
			],
	
			'meta_title' => [
				'label' => 'Titolo SEO',
				'help' => 'Se vuoto, verrà utilizzato il titolo della pagina.',
			],
	
			'meta_description' => [
				'label' => 'Meta description',
				'help' => 'Breve descrizione utilizzata dai motori di ricerca.',
			],
		],
	
		'status' => [
			'draft' => 'Bozza',
			'published' => 'Pubblicata',
			'private' => 'Privata',
		],
	
		'navigation' => [
			'title' => 'Navigazione',
			'description' => 'Configura come questa pagina viene presentata nei menu e nei widget di navigazione.',
			'show' => 'Mostra nella navigazione',
			'show_help' => 'Se disattivata, la pagina resta raggiungibile tramite URL ma non viene proposta nei menu o nel widget Pagine.',
		],
	
		'actions' => [
			'cancel' => 'Annulla',
			'create' => 'Crea pagina',
			'save' => 'Salva modifiche',
			'manage_widgets' => 'Gestisci widget',
			'open' => 'Apri pagina',
			'delete' => 'Elimina pagina',
		],
	
		'composition' => [
			'title' => 'Composizione della pagina',
			'description' => 'Gestisci separatamente il contenuto principale e la sidebar. Se la sidebar è vuota, non verrà mostrata.',
	
			'content' => [
				'title' => 'Contenuto',
				'description' => 'Componi la parte principale della pagina con i widget disponibili.',
			],
	
			'sidebar' => [
				'title' => 'Sidebar',
				'description' => 'Aggiungi i widget della colonna laterale oppure lasciala vuota per utilizzare tutta la larghezza.',
			],
		],
	
		'unsaved' => [
			'title' => 'I widget saranno disponibili dopo il salvataggio',
			'description' => 'Crea prima la pagina; potrai poi configurare le aree Contenuto e Sidebar.',
		],
	
		'confirm' => [
			'delete' => 'Eliminare definitivamente questa pagina?',
		],
	],
	'webchat' => [
		'kicker' => 'Community',
		'title' => 'Landing Chat',
		'description' => 'Configura la pagina di accesso alla chat e i parametri predefiniti di KiwiIRC.',
	
		'success' => 'Impostazioni salvate.',
	
		'configuration' => [
			'title' => 'Configurazione Landing Chat',
			'description' => 'Queste impostazioni vengono utilizzate dalla landing page della chat.',
		],
	
		'fields' => [
			'default_channel' => [
				'label' => 'Canale predefinito',
				'help' => 'Il canale aperto automaticamente all’ingresso.',
			],
	
			'show_hero' => [
				'label' => 'Hero introduttiva',
				'help' => 'Mostra il blocco introduttivo nella parte alta della landing chat.',
			],
	
			'show_channel_card' => [
				'label' => 'Scheda informazioni del canale',
				'help' => 'Mostra statistiche e caratteristiche della community nella landing chat.',
			],
	
			'chat_title' => [
				'label' => 'Titolo finestra',
				'help' => 'Titolo mostrato nel browser e nella landing page.',
			],
	
			'theme' => [
				'label' => 'Tema KiwiIRC',
				'help' => 'Nome del tema da utilizzare.',
			],
	
			'state_key' => [
				'label' => 'State Key',
				'help' => 'Chiave utilizzata per mantenere lo stato del client.',
			],
		],
	
		'actions' => [
			'save' => 'Salva configurazione',
		],
	],
	'block_types' => [
		'github_repository' => [
			'label' => 'Repository GitHub',
			'description' => 'Dashboard completa di un repository GitHub con attività, commit, release, issue e pull request.',
		],
	
		'github_pull_requests' => [
			'label' => 'Pull request GitHub',
			'description' => 'Mostra le pull request più recenti di un repository GitHub.',
		],
	
		'github_release' => [
			'label' => 'Release GitHub',
			'description' => 'Mostra in formato compatto l’ultima release disponibile di un repository GitHub.',
		],
	
		'azuracast_mini_player' => [
			'label' => 'Miniplayer AzuraCast',
			'description' => 'Mostra un player radio compatto apribile anche in una finestra indipendente.',
		],
	
		'azuracast_requests' => [
			'label' => 'Richieste AzuraCast',
			'description' => 'Permette agli ascoltatori di cercare e richiedere brani tramite AzuraCast.',
		],
	
		'azuracast' => [
			'label' => 'AzuraCast',
			'description' => 'Mostra il player AzuraCast e lo storico degli ultimi brani trasmessi.',
		],
	
		'azuracast_stats' => [
			'label' => 'Statistiche AzuraCast',
			'description' => 'Mostra stato della radio, ascoltatori, bitrate, codec e mount attivi.',
		],
	
		'users_in_chat' => [
			'label' => 'Adesso in chat',
			'description' => 'Mostra le persone presenti in questo momento nella chat IRC.',
		],
	
		'latest_members' => [
			'label' => 'Nuovi membri',
			'description' => 'Mostra gli ultimi membri registrati nella community.',
		],
	
		'latest_audio' => [
			'label' => 'Ultimi audio condivisi',
			'description' => 'Mostra gli ultimi audio condivisi nei Ping.',
		],
	
		'submit_article' => [
			'label' => 'Proponi un articolo',
			'description' => 'Invita gli utenti a proporre un articolo alla Chanzine.',
		],
	
		'html' => [
			'label' => 'HTML personalizzato',
			'description' => 'Visualizza codice HTML personalizzato.',
		],
	
		'latest_video' => [
			'label' => 'Ultimi video condivisi',
			'description' => 'Mostra gli ultimi video condivisi nei Ping.',
		],
	
		'latest_articles' => [
			'label' => 'Ultimi articoli',
			'description' => 'Mostra gli ultimi articoli pubblicati nella Chanzine.',
		],
	
		'categories' => [
			'label' => 'Categorie Chanzine',
			'description' => 'Mostra le categorie della Chanzine con il numero di articoli pubblicati.',
		],
	
		'pages_navigation' => [
			'label' => 'Navigazione pagine',
			'description' => 'Mostra le pagine dinamiche pubblicate in un menu di navigazione.',
		],
	],
	'block_settings' => [
		'latest_members' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Nuovi membri',
			],
			'limit' => [
				'label' => 'Numero di membri',
			],
			'show_avatar' => [
				'label' => 'Mostra avatar',
			],
		],
	
		'users_in_chat' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Adesso in chat',
			],
			'limit' => [
				'label' => 'Numero di persone',
			],
			'show_total' => [
				'label' => 'Mostra il totale delle persone in chat',
			],
			'show_avatar' => [
				'label' => 'Mostra gli avatar dei membri registrati',
			],
			'show_join_link' => [
				'label' => 'Mostra il collegamento "Entra in chat"',
			],
		],
	
		'categories' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Categorie',
			],
			'show_count' => [
				'label' => 'Mostra numero articoli',
			],
		],
	
		'html' => [
			'html' => [
				'label' => 'Codice HTML',
			],
		],
	
		'latest_articles' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Ultimi articoli',
			],
			'limit' => [
				'label' => 'Numero articoli',
			],
			'show_date' => [
				'label' => 'Mostra data',
			],
		],
	
		'latest_audio' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Ultimi audio condivisi',
			],
			'limit' => [
				'label' => 'Numero audio',
			],
			'show_author' => [
				'label' => 'Mostra autore',
			],
		],
	
		'latest_video' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Ultimi video condivisi',
			],
			'limit' => [
				'label' => 'Numero video',
			],
			'show_author' => [
				'label' => 'Mostra autore',
			],
		],
	
		'pages_navigation' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Pagine',
			],
			'navigation_group' => [
				'label' => 'Gruppo',
				'placeholder' => 'default',
			],
		],
	
		'submit_article' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Proponi un articolo',
			],
			'description' => [
				'label' => 'Descrizione',
				'default' => 'Hai qualcosa da raccontare? Condividi la tua idea con la community.',
			],
			'button_label' => [
				'label' => 'Testo pulsante',
				'default' => 'Invia la tua proposta',
			],
		],
	
		'github_pull_requests' => [
			'repository' => [
				'label' => 'Repository',
				'placeholder' => 'owner/repository',
				'help' => 'Puoi inserire sia owner/repository sia l’URL completo del repository GitHub.',
			],
			'custom_title' => [
				'label' => 'Titolo personalizzato',
				'placeholder' => 'Pull Requests',
			],
			'state' => [
				'label' => 'Stato',
				'options' => [
					'open' => 'Aperte',
					'closed' => 'Chiuse',
					'all' => 'Tutte',
				],
			],
			'limit' => [
				'label' => 'Numero di Pull Request',
			],
		],
	
		'github_release' => [
			'repository' => [
				'label' => 'Repository',
				'placeholder' => 'owner/repository',
				'help' => 'Puoi inserire sia owner/repository sia l’URL completo del repository GitHub.',
			],
			'custom_title' => [
				'label' => 'Titolo personalizzato',
				'placeholder' => 'Ultima release',
			],
			'show_repository' => [
				'label' => 'Mostra nome repository',
			],
			'show_date' => [
				'label' => 'Mostra data pubblicazione',
			],
		],
	
		'github_repository' => [
			'repository' => [
				'label' => 'Repository',
				'placeholder' => 'owner/repository',
				'help' => 'Puoi inserire sia owner/repository sia l’URL completo del repository GitHub.',
			],
			'branch' => [
				'label' => 'Branch',
				'placeholder' => 'Lascia vuoto per usare il branch predefinito',
			],
			'title' => [
				'label' => 'Titolo personalizzato',
			],
			'show_release' => [
				'label' => 'Mostra ultima release',
			],
			'show_languages' => [
				'label' => 'Mostra linguaggi',
			],
			'show_commits' => [
				'label' => 'Mostra ultime commit',
			],
			'commit_limit' => [
				'label' => 'Numero commit',
			],
			'show_pull_requests' => [
				'label' => 'Mostra pull request aperte',
			],
			'show_issues' => [
				'label' => 'Mostra issue aperte',
			],
		],
	
		'azuracast' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Ascolta la radio',
			],
			'player_style' => [
				'label' => 'Stile player',
				'help' => 'Scegli l’aspetto del player mostrato nel sito.',
				'options' => [
					'modern' => 'Modern',
					'led' => 'LED',
					'analog' => 'Analog',
					'minimal' => 'Minimal',
				],
			],
			'station_url' => [
				'label' => 'URL stazione AzuraCast',
			],
			'history_limit' => [
				'label' => 'Numero brani nello storico',
			],
			'show_history' => [
				'label' => 'Mostra storico brani',
			],
		],
	
		'azuracast_mini_player' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Ascolta la radio',
			],
			'now_playing_url' => [
				'label' => 'URL API Now Playing',
			],
			'stream_url' => [
				'label' => 'URL stream audio',
			],
			'show_cover' => [
				'label' => 'Mostra copertina',
			],
		],
	
		'azuracast_requests' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Richiedi un brano',
			],
			'requests_url' => [
				'label' => 'URL richieste AzuraCast',
				'help' => 'Esempio: https://radio.example.org/api/station/1/requests',
			],
			'unavailable_behavior' => [
				'label' => 'Quando le richieste non sono disponibili',
				'options' => [
					'message' => 'Mostra un messaggio',
					'hide' => 'Nascondi il blocco',
				],
			],
			'unavailable_message' => [
				'label' => 'Messaggio richieste non disponibili',
				'default' => 'Le richieste musicali non sono disponibili in questo momento.',
			],
		],
	
		'azuracast_stats' => [
			'title' => [
				'label' => 'Titolo',
				'default' => 'Statistiche radio',
			],
			'station_url' => [
				'label' => 'URL Now Playing AzuraCast',
				'help' => 'Esempio: https://radio.example.org/api/nowplaying/1',
			],
			'show_listeners' => [
				'label' => 'Mostra ascoltatori attuali',
			],
			'show_unique_listeners' => [
				'label' => 'Mostra ascoltatori unici',
			],
			'show_bitrate' => [
				'label' => 'Mostra bitrate',
			],
			'show_codec' => [
				'label' => 'Mostra codec',
			],
			'show_mounts' => [
				'label' => 'Mostra mount attivi',
			],
		],
	],
];
