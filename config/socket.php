<?php

return [

   'url' => env('SOCKET_URL', 'http://localhost'),

    'channels' => array(
        'online' => env('SOCKET_ONLINE_CHANNEL', 'online'),
    )

];
