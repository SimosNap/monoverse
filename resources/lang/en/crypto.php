<?php

declare(strict_types=1);

return [
	'mydoge' => [
		'wallet_not_connected' => 'Wallet not connected',
		'not_connected' => 'Not connected',
		'balance' => 'Balance',
		'your_doge_address' => 'Your DOGE address',
		'copy' => 'Copy',
		'fallback_help' => 'MyDogeMask is not available in this browser. You can use this address with another Dogecoin wallet.',
		'connect' => 'Connect MyDogeMask',
		'unavailable' => 'MyDogeMask unavailable',
		'connected' => 'Connected',
		'disconnect' => 'Disconnect MyDogeMask',
		'copied' => 'Copied',
		'wallet_connected' => 'Wallet connected',
	],
	'tip_modal' => [
		'title' => 'Send DOGE',
		'close' => 'Close',

		'intro_before_recipient' => 'Send a Dogecoin tip to',

		'fallback' => [
			'address_label' => 'Dogecoin address',
			'qr_label' => 'Dogecoin address QR code',
			'copy' => 'Copy',
			'help' => 'MyDogeMask is not available in this browser. You can use this address with another Dogecoin wallet.',
		],

		'amount' => [
			'label' => 'DOGE amount',
		],

		'share_profile' => [
			'title' => 'Share this tip in a Ping',
			'help' => 'After sending, a Ping will be published to announce the tip to this user.',
		],

		'share_pong' => [
			'title' => 'Send the tip and add a Pong',
			'help' => 'The Pong will automatically include the tip details. You can also add a message.',
			'message_label' => 'Message',
			'message_placeholder' => 'Add a message to the tip (optional)',
		],

		'cancel' => 'Cancel',
		'send' => 'Send DOGE',
	],
	'js' => [
		'cancel' => 'Cancel',
		'close' => 'Close',
		'copy' => 'Copy',
		'copied' => 'Copied',

		'errors' => [
			'notification_failed' => 'Unable to create the notification.',
			'pong_invalid' => 'Invalid tip Pong data.',
			'pong_failed' => 'Unable to publish the Pong.',
			'ping_failed' => 'Unable to publish the Ping.',
			'address_unavailable' => 'Dogecoin address unavailable.',
			'invalid_amount' => 'Enter a valid DOGE amount.',
			'mydoge_unavailable' => 'MyDogeMask is not available in this browser.',
			'missing_txid' => 'MyDogeMask did not return a transaction ID.',
			'send_failed' => 'Send failed:',
			'unknown' => 'unknown error',
			'connection_not_authorized' => 'Connection to MyDogeMask was not authorized.',
		],

		'status' => [
			'confirm_transaction' => 'Confirm the transaction in MyDogeMask…',
			'sent' => 'Tip sent.',
			'view_transaction' => 'View transaction',
			'ping_shared' => 'Also shared in a Ping.',
			'ping_failed' => 'The tip was sent, but the Ping could not be published.',
			'pong_shared' => 'Also added in a Pong.',
			'pong_failed' => 'The tip was sent, but the Pong could not be published.',
		],
	],
];