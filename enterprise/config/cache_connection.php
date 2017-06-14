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
         * 页游
         */
        "webgame" => [

            /**
             * 服务器信息
             */
            "host" => "10.66.132.0",
            "port" => 6379,
            "password" => "crs-q2ja3vko:f@8CmppvGik2Tq)b",
			'database' => 0,
        ],

        /**
         * 数据中心统计
         */
        "datacenterstat" => [

            /**
             * 服务器信息
             */
            "host" => "10.66.181.51",
            "port" => 6379,
            "password" => "crs-i2w9dg9e:f@8CmppvGik2Tq)b",
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
                ["host" => "10.66.139.108", "port" => 9101, "weight" => 100],
            ],

        ],

    ],

    /**
     * udpserver 配置
     */
    'udpserver' => [
        "host" => "10.154.54.37",
        "port" => 8300,
    ],

];
