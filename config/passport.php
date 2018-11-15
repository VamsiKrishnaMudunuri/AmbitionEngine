<?php

return [

   'account' => [

       'personal' => [

            'client_id' => env('PASSPORT_ACCOUNT_PERSONAL_CLIENT_ID', null),
             'client_secret' => env('PASSPORT_ACCOUNT_PERSONAL_CLIENT_SECRET', null)
           ],

       'password' => [

           'client_id' => env('PASSPORT_ACCOUNT_PASSWORD_CLIENT_ID', null),
           'client_secret' => env('PASSPORT_ACCOUNT_PASSWORD_CLIENT_SECRET', null)

       ],

   ]

];
