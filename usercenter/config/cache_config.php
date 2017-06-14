<?php
/**
 * 所有缓存key前缀的配置
 * 以及数据所在机器配置
 * 单个key，不要前缀，前缀就设置未空字符串
 */
return [

    /**
     * redis key配置
     */
    'redis' => [

        /**
         * 临时数据，用户最后登录状态等
         */
        "user_last_login" => [

                "key_prefix" => "user_last_login_",
                "expire"     => 0,
                "connection" => "user",
                "type"       => "hash",
        ],

        /**
         * 数据中心统计
         */
        "datacenterstat" => [

                "key_prefix" => "datacenter_stat_queue_key",
                "expire"     => 0,
                "connection" => "datacenterstat",
                "type"       => "list",
        ],

    ],

    /**
     * memcached key配置
     */
    'memcached' => [

        /**
         * token
         */
        "common_token" => [

            /**
             * key的前缀，或key
             */
            "key_prefix" => "common_token_",

            /**
             * 过期时间，单位：秒，null为不过期，优先由代码里指定，其次由该配置指定
             */
            "expire"     => 5*60,

            /**
             * 连接的memcached服务器配置
             */
            "connection" => "template",
        ],

        /**
         * 登录token
         */
        "login_token" => [
            "key_prefix" => "login_token_",
            "expire"     => 86400 * 7,
            "connection" => "template",
        ],

        /**
         * 支付token
         */
        "pay_token" => [
            "key_prefix" => "pay_token_",
            "expire"     => 15 * 60,
            "connection" => "template",
        ],

        /**
         * 临时校验用的code
         * 第三方登录等使用
         */
        "tmp_code" => [
            "key_prefix" => "tmp_code_",
            "expire"     => 15 * 60,
            "connection" => "template",
        ],

        /**
         * 2b版本取款密码登录token
         */
        "2bcash_token" => [
            "key_prefix" => "2bcash_token_",
            "expire"     => 15 * 60,
            "connection" => "template",
        ],

        /**
         * 并发锁
         */
        "lock" => [
            "key_prefix" => "lock_",
            "expire"     => 3,
            "connection" => "template",
        ],

        /**
         * 验证码
         */
        "verfy_code" => [
            "key_prefix" => "verfy_code_",
            "expire"     => 15 * 60,
            "connection" => "template",
        ],

    ],

];
