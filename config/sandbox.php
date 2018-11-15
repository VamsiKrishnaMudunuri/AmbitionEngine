<?php

return [

    'library' => [
        'type' => 'library',
        'visibility' => 'public',
        'category' => '',
        'field' => '',
        'mainPath' => 'library',
        'subPath' => '',
        'size' => 5000,
        'quality' => 100,
        'mimes' => ['gif', 'png', 'jpg', 'jpeg', 'bmp', 'txt', 'doc', 'docx', 'xlt', 'xlsx', 'pdf', 'ppt', 'pptx', 'pptm', 'zip'],
        'min-dimension'=> [
            'width' => 100, 'height' => 100
        ],
        'dimension' => [
            'sm' => ['slug' => 'sm', 'width' => 100, 'height' => 100]
        ]
    ],

    'image' => [
        'type' => 'image',
        'visibility' => 'public',
        'default' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAYAAACtWK6eAAACF0lEQVR4nO3TMRHAMBDAsCT8sT6FhkDPaztICLx4z8yzgFfn6wD4M4NAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIBINAMAgEg0AwCASDQDAIhAuiFAVZ2U+orwAAAABJRU5ErkJggg==',
        'category' => '',
        'field' => '',
        'mainPath' => 'images',
        'subPath' => '',
        'size' => 5000,
        'quality' => 60,
        'mimes' => ['gif', 'png', 'jpg', 'jpeg', 'bmp'],
        'min-dimension'=> [
            'width' => 100, 'height' => 100
        ],
        'dimension' => [
            'sm' => ['slug' => 'sm', 'width' => 100, 'height' => 100]
        ]
    ],

    'file' => [
        'type' => 'file',
        'visibility' => 'public',
        'default' => '',
        'category' => '',
        'field' => '',
        'mainPath' => 'files',
        'subPath' => '',
        'size' => 5000,
        'quality' => 100,
        'mimes' => ['txt', 'doc', 'docx', 'xlt', 'xlsx', 'pdf', 'ppt', 'pptx', 'pptm', 'zip']
    ],

    'video' => [
        'type' => 'video',
        'visibility' => 'public',
        'default' => '',
        'category' => '',
        'field' => '',
        'mainPath' => 'videos',
        'subPath' => '',
        'size' => 50000,
        'quality' => 100,
        'mimes' => []

    ]

];
