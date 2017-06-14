<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */
    //'default' => env('CACHE_DRIVER', 'memcached'),
    'default' => env('CACHE_DRIVER', 'file'),
    //'default' => 'redis',

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
        ],

        'database' => [
            'driver' => 'database',
            'table'  => 'cache',
            'connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache'),
        ],

        /**
         * 临时数据，可以丢失数据
         * 保存token
         */
        'template' => [
            'driver'  => 'memcached',
            'prefix'  => 'tmp_',
            'servers' => [
                [
                    'host' => env('MC_TEMP_HOST', ''), 'port' => env('MC_TEMP_PORT', ''), 'weight' => 100,
                ],
            ],
        ],

        /**
         * 用户基本信息
         */
        'userbase' => [
            'driver'  => 'memcached',
            'prefix'  => 'ub_',
            'servers' => [
                [
                    'host' => env('MC_USER_HOST', ''), 'port' => env('MC_USER_PORT', ''), 'weight' => 100,
                ],
            ],
        ],

        /**
         * 用户扩展信息
         */
        'userext' => [
            'driver'  => 'memcached',
            'prefix'  => 'ue_',                 // key的前缀
            'expire'  => 90*24*60,              // 过期时间，单位 分钟
            'servers' => [
                [
                    'host' => env('MC_USER_HOST', ''), 'port' => env('MC_USER_PORT', ''), 'weight' => 100,
                ],
            ],
        ],

        /**
         * 登录信息
         */
        'login' => [
            'driver'  => 'memcached',
            'prefix'  => 'login_',
            'expire'  => 90*24*60,              // 过期时间，单位 分钟
            'servers' => [
                [
                    'host' => env('MC_LOGIN_HOST', ''), 'port' => env('MC_LOGIN_PORT', ''), 'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => '',

];
