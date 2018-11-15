<?php

return [

    'currency' => env('WALLET_CURRENCY', 'USD'),
    'merchant_id' => env('WALLET_BRAINTREE_MERCHANT_ID', ''),
    'unit' => 0.01,
    'top_up_credit' => array(1000, 2000, 5000, 10000)

];