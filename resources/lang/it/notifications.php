<?php

declare(strict_types=1);

return [
	'label' => 'Notifiche',
	'unread_label' => 'Notifiche non lette: :count',
	'page' => [
		'title' => 'Notifiche',
		'subtitle' => 'Risposte, menzioni e apprezzamenti ricevuti sui tuoi Ping.',
	
		'count' => [
			'one' => ':count notifica',
			'many' => ':count notifiche',
		],
	
		'delete_all' => 'Elimina tutte',
		'delete_all_confirm' => 'Vuoi eliminare tutte le notifiche? Questa azione non può essere annullata.',
	
		'empty' => [
			'title' => 'Nessuna notifica',
			'text' => 'Quando qualcuno risponderà, menzionerà il tuo username o apprezzerà un tuo Ping, troverai qui la notifica.',
			'action' => 'Vai ai Ping',
		],
	
		'actor_fallback' => 'Qualcuno',
		'profile_of' => 'Profilo di :name',
	
		'types' => [
			'default' => 'ha generato una notifica',
			'reply' => 'ha risposto al tuo Ping',
			'mention' => 'ti ha menzionato',
			'upvote' => 'ha apprezzato il tuo Ping',
			'report' => 'ha inviato una nuova segnalazione',
			'doge_tip' => 'ti ha inviato una mancia DOGE',
			'doge_tip_amount' => 'ti ha inviato una mancia di :amount DOGE',
		],
	
		'unread' => 'Non letta',
	
		'actions' => [
			'open' => 'Apri',
			'open_aria' => 'Apri la notifica',
			'delete' => 'Elimina',
			'delete_aria' => 'Elimina la notifica',
		],
	],
];