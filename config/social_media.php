<?php

return [

    'datetime' => [
        'date' => [
            'format' => ''
        ],
        'datetime' => [
            'full' => [
                'format' => env('SOCIAL_MEDIA_DATETIME_DATETIME_FULL_FORMAT', 'full^|short'),
            ],

            'short' => [
                'format' => env('SOCIAL_MEDIA_DATETIME_DATETIME_SHORT_FORMAT', 'long^|short'),
            ]
        ],
        'time' => [
            'format' => ''
        ]
    ],

];