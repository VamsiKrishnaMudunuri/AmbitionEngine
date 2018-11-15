<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Enviroment
    |--------------------------------------------------------------------------
    |
    | Please provide the enviroment you would like to use for braintree.
    | This can be either 'sandbox' or 'production'.
    |
    */
	'environment' => env('BRAINTREE_ENVIRONMENT', 'sandbox'),

	/*
    |--------------------------------------------------------------------------
    | Merchant ID
    |--------------------------------------------------------------------------
    |
    | Please provide your Merchant ID.
    |
    */
	'merchantId' => env('BRAINTREE_MERCHANT_ID', 'my_merchant_id'),

	/*
    |--------------------------------------------------------------------------
    | Public Key
    |--------------------------------------------------------------------------
    |
    | Please provide your Public Key.
    |
    */
	'publicKey' => env('BRAINTREE_PUBLIC_KEY', 'my_public_key'),

	/*
    |--------------------------------------------------------------------------
    | Private Key
    |--------------------------------------------------------------------------
    |
    | Please provide your Private Key.
    |
    */
	'privateKey' => env('BRAINTREE_PRIVATE_KEY', 'my_private_key'),

	/*
    |--------------------------------------------------------------------------
    | Client Side Encryption Key
    |--------------------------------------------------------------------------
    |
    | Please provide your CSE Key.
    |
    */
	'clientSideEncryptionKey' => 'my_client_side_encryption_key',

    'rules' => [
        'payment_method_nonce' => 'required'
    ]


];