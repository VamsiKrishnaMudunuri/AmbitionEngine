<?php

return [

   'package' => [

       'prime' => [
           'promotion_code_field_name' => '_package_promotion_code',
           'promotion_code_field_length' => 10,
           'promotion_code' => env('SUBSCRIPTION_PRIME_PROMOTION_CODE', null),

        ]

   ]

];
