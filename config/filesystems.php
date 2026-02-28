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

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads',
            // 'url' => env('APP_ENV') === 'local' ? env('APP_URL').'/uploads' : env('APP_URL').'/test/public/uploads',
            'url' =>  env('APP_URL').'/public/uploads',
            'visibility' => 'public',
        ],
        'home_slides' => [
            'driver' => 'local',
            'root'   => storage_path('app/public/uploads/home_slides'),
            // 'url' => env('APP_ENV') === 'local' ? env('APP_URL').'/uploads/home_slides' : env('APP_URL').'/test/public/storage/uploads/home_slides',
            'url' =>  env('APP_URL').'/public/storage/uploads/home_slides',
            'visibility' => 'public',
        ],
        'home_storage' => [
            'driver' => 'local',
            'root' => storage_path('app/public/uploads/home'), // folder for your home section items
            'url' => env('APP_URL') . '/public/storage/uploads/home',
            'visibility' => 'public',
        ],
        'settings_files' => [
            'driver' => 'local',
            'root'   => storage_path('app/public/uploads/settings'),
            // 'url' => env('APP_ENV') === 'local' ? env('APP_URL').'/uploads/settings' : env('APP_URL').'/test/public/storage/uploads/settings',
            'url' => env('APP_URL').'/storage/uploads/settings',
            'visibility' => 'public',
        ],
        'slider' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads/slider',
            'url' => env('APP_URL').'/public',
            'visibility' => 'public',
        ],
        'products_gallery' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads/products',
            // 'url' => env('APP_ENV') === 'local' ? env('APP_URL').'/public' : env('APP_URL').'/test/public/products',
            'url' =>  env('APP_URL').'/public/products',
            'visibility' => 'public',
        ],
        
             'products_features' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads/products_features',
            'url' => env('APP_URL').'/public',
            'visibility' => 'public',
        ],
        'brand_slider' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads/brand_slider',
            // 'url' => env('APP_ENV') === 'local' ? env('APP_URL').'/public/uploads/brand_slider' : env('APP_URL').'/test/public/uploads/brand_slider',
            'url' => env('APP_URL').'/public/uploads/brand_slider',
            'visibility' => 'public',
        ],
        'brands' => [
            'driver' => 'local',
            'root'   => storage_path('app/public/uploads/brands'),
            // 'url' => env('APP_ENV') === 'local' ? env('APP_URL').'/public/uploads/brands' : env('APP_URL').'/test/public/uploads/brands',
            'url' =>  env('APP_URL').'/public/uploads/brands',
            'visibility' => 'public',
        ],
        'posts' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads/posts',
            // 'url' => env('APP_ENV') === 'local' ? env('APP_URL').'/public' : env('APP_URL').'/test/public/uploads/posts',
            'url' =>  env('APP_URL').'/public/uploads/posts',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
        //public_path('uploads') => storage_path('app/public/uploads'),
    ],


];
