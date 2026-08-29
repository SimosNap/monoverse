<?php

declare(strict_types=1);

return [
	'mydoge' => [
		'wallet_not_connected' => 'Wallet non connesso',
		'not_connected' => 'Non connesso',
		'balance' => 'Saldo',
		'your_doge_address' => 'Il tuo indirizzo DOGE',
		'copy' => 'Copia',
		'fallback_help' => 'MyDogeMask non è disponibile in questo browser. Puoi utilizzare questo indirizzo con un altro wallet Dogecoin.',
		'connect' => 'Collega MyDogeMask',
		'unavailable' => 'MyDogeMask non disponibile',
		'connected' => 'Connesso',
		'disconnect' => 'Scollega MyDogeMask',
		'copied' => 'Copiato',
		'wallet_connected' => 'Wallet connesso',
	],
	'tip_modal' => [
		'title' => 'Invia DOGE',
		'close' => 'Chiudi',

		'intro_before_recipient' => 'Invia una mancia Dogecoin a',

		'fallback' => [
			'address_label' => 'Indirizzo Dogecoin',
			'qr_label' => 'QR code indirizzo Dogecoin',
			'copy' => 'Copia',
			'help' => 'MyDogeMask non è disponibile in questo browser. Puoi utilizzare questo indirizzo con un altro wallet Dogecoin.',
		],

		'amount' => [
			'label' => 'Importo DOGE',
		],

		'share_profile' => [
			'title' => 'Condividi questa mancia in un Ping',
			'help' => "Dopo l'invio verrà pubblicato un Ping che segnala la mancia a questo utente.",
		],

		'share_pong' => [
			'title' => 'Invia la mancia e aggiungi un Pong',
			'help' => 'Il Pong includerà automaticamente i dati della mancia. Puoi aggiungere anche un messaggio.',
			'message_label' => 'Messaggio',
			'message_placeholder' => 'Aggiungi un messaggio alla mancia (facoltativo)',
		],

		'cancel' => 'Annulla',
		'send' => 'Invia DOGE',
	],
	'js' => [
		'cancel' => 'Annulla',
		'close' => 'Chiudi',
		'copy' => 'Copia',
		'copied' => 'Copiato',

		'errors' => [
			'notification_failed' => 'Impossibile creare la notifica.',
			'pong_invalid' => 'Dati del Pong della mancia non validi.',
			'pong_failed' => 'Impossibile pubblicare il Pong.',
			'ping_failed' => 'Impossibile pubblicare il Ping.',
			'address_unavailable' => 'Indirizzo Dogecoin non disponibile.',
			'invalid_amount' => 'Inserisci un importo DOGE valido.',
			'mydoge_unavailable' => 'MyDogeMask non è disponibile in questo browser.',
			'missing_txid' => 'MyDogeMask non ha restituito un transaction ID.',
			'send_failed' => 'Invio non riuscito:',
			'unknown' => 'errore sconosciuto',
			'connection_not_authorized' => 'Connessione a MyDogeMask non autorizzata.',
		],

		'status' => [
			'confirm_transaction' => 'Conferma la transazione in MyDogeMask…',
			'sent' => 'Mancia inviata.',
			'view_transaction' => 'Visualizza transazione',
			'ping_shared' => 'Condivisa anche in un Ping.',
			'ping_failed' => 'La mancia è stata inviata, ma il Ping non è stato pubblicato.',
			'pong_shared' => 'Aggiunta anche in un Pong.',
			'pong_failed' => 'La mancia è stata inviata, ma il Pong non è stato pubblicato.',
		],
	],
];
