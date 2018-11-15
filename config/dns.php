<?php

return [

    'default' => env('DNS_DEFAULT', 'MY'),

    'fallback' => env('DNS_FALLBACK', 'MY'),

    'support' => ($dnsSupport = env('DNS_SUPPORT', 'MY')) ? explode(',', $dnsSupport) : array(),


];