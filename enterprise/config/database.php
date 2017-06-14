<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
     */

    'fetch'       => PDO::FETCH_ASSOC,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
     */

    'default'     => env('DB_CONNECTION', 'system'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
     */

    'connections' => [

        'sqlite'      => [
            'driver'   => 'sqlite',
            'database' => storage_path('database.sqlite'),
            'prefix'   => '',
        ],

        'db_operate'  => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_operate',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'system'      => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'system',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_dev'      => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_dev',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_webgame'  => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_webgame',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_user_log' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_user_log',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'record'      => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => 'record',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_operate'  => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_operate',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_2b_store' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_2b_store',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_cdk'      => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_cdk',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_act'      => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_act',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_vronline' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_vronline',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'db_comment'  => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '192.168.10.10'),
            'database'  => 'db_comment',
            'username'  => env('DB_USERNAME', 'homestead'),
            'password'  => env('DB_PASSWORD', 'secret'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'pgsql'       => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],

        'sqlsrv'      => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
     */

    'migrations'  => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
     */

    'redis'       => [

        'cluster'        => false,

        'default'        => [
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 0,
        ],

        'webgame'        => [
            'host'     => env('REDIS_WEBGAME_HOST', ''),
            'port'     => env('REDIS_WEBGAME_PORT', ''),
            'password' => env('REDIS_WEBGAME_PWD', ''),
            'database' => 0,
        ],

        'datacenterstat' => [
            'host'     => env('REDIS_DCSTAT_QUEUE_HOST', ''),
            'port'     => env('REDIS_DCSTAT_QUEUE_PORT', ''),
            'password' => env('REDIS_DCSTAT_QUEUE_PWD', ''),
            'database' => 0,
        ],

    ],

];
