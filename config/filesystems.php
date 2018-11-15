<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
        ],

        'wsdl' => [
            'driver' => 'local',
            'root' => storage_path('app/wsdl'),
            'visibility' => 'public',
        ],

        'tmp' => [
            'driver' => 'local',
            'root' => storage_path('app/tmp'),
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'root' => 'public',
            'scheme'  => env('AWS_S3_SCHEME', null),
            'http'    => [
                'verify' => base_path() . '/' . env('AWS_S3_SSL_PEM', null)
            ],

            'key' => env('AWS_S3_ACCESS_KEY_ID', null),
            'secret' => env('AWS_S3_SECRET_ACCESS_KEY', null),
            'region' => env('AWS_S3_REGION_ID', null),
            'bucket' => env('AWS_S3_BUCKET', null),
            'bucket_endpoint' => env('AWS_S3_BUCKET_ENDPOINT', false),
            'endpoint' => env('AWS_S3_ENDPOINT', null),
            'expired' => env('AWS_S3_SIGNED_LINK_EXPIRED', null)
        ],

        's3_private' => [
            'driver' => 's3',
            'root' => 'private',
            'scheme'  => env('AWS_S3_SCHEME', null),
            'http'    => [
                'verify' => base_path() . '/' . env('AWS_S3_SSL_PEM', null)
            ],
            'key' => env('AWS_S3_ACCESS_KEY_ID', null),
            'secret' => env('AWS_S3_SECRET_ACCESS_KEY', null),
            'region' => env('AWS_S3_REGION_ID', null),
            'bucket' => env('AWS_S3_BUCKET', null),
            'bucket_endpoint' => env('AWS_S3_BUCKET_ENDPOINT', false),
            'endpoint' => env('AWS_S3_ENDPOINT', null),
            'expired' => env('AWS_S3_SIGNED_LINK_EXPIRED', null)
        ]

    ],

];
