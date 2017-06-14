<?php
/**
 * 所有缓存key前缀的配置
 * 以及数据所在机器配置
 */
return [

    /**
     * redis key配置
     */
    'redis' => [

        /**
         * 登录token
         */
        "template" => [

            /**
             * 服务器信息
             */
            "host" => "192.168.78.95",
            "port" => 6379,
            "password" => "",
			'database' => 0,
        ],

        /**
         * 页游
         */
        "webgame" => [

            /**
             * 服务器信息
             */
            "host" => "192.168.78.95",
            "port" => 6379,
            "password" => "",
			'database' => 0,
        ],

        /**
         * 数据中心统计
         */
        "datacenterstat" => [

            /**
             * 服务器信息
             */
            "host" => "192.168.78.95",
            "port" => 6379,
            "password" => "",
			'database' => 0,
        ],

    ],

    /**
     * memcached 配置
     */
    'memcached' => [

        /**
         * 登录token
         */
        "template" => [

            /**
             * 服务器信息
             */
            "servers" => [
                ["host" => "192.168.78.95", "port" => 11556, "weight" => 100],
            ],

        ],

    ],

    /**
     * udpserver 配置
     */
    'udpserver' => [
        "host" => "192.168.78.95",
        "port" => 8300,
    ],

];
