<?php

return [

    'default' => env('CURRENCY_DEFAULT', 'USD'),

    'fallback' => env('CURRENCY_FALLBACK', 'USD'),

    'support' => ($currencySupport = env('CURRENCY_SUPPORT', 'USD')) ? explode(',', $currencySupport) : array(),

    'precision' => env('CURRENCY_PRECISION', 6),

];