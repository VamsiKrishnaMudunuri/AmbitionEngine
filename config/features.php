<?php

return [

    'username' => env('FEATURE_USERNAME', 1),

    'payment' => array(
        'method' => array(
            'credit-card' => env('FEATURE_PAYMENT_METHOD_CREDIT_CARD', 1),
        )
    ),
	'subscription' => array(
		'batch-upload' => env('FEATURE_SUBSCRIPTION_BATCH_UPLOAD', 1),
		'invoice' => env('FEATURE_SUBSCRIPTION_INVOICE', 1)
	),

    'admin' => array(
        'event' => array('timezone' => env('FEATURE_ADMIN_EVENT_TIMEZONE', 1))
    ),

    'member' => array(
        'auth' => array('sign-up-with-payment' => env('FEATURE_MEMBER_AUTH_SIGN_UP_WITH_PAYMENT', 1) ),
        'feed' => array('location' => env('FEATURE_MEMBER_FEED_LOCATION', 1)),
        'event' => array('timezone' => env('FEATURE_MEMBER_EVENT_TIMEZONE', 1)),
        'wallet' => array(
            'all' => env('FEATURE_MEMBER_WALLET_ALL', 1),
            'top-up' => env('FEATURE_MEMBER_WALLET_TOP_UP', 1)
        ),
        'credit_card' => array('all' => env('FEATURE_MEMBER_CREDIT_CARD_ALL', 1)),
    ),

];
