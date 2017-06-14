<?php
/**
 * VR客户端配置
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
     */

    /**
     * 最新版本号
     */
    'latest_client'       => [

        /**
         * 客户端普通版
         */
        'client'   => [

            /**
             * 版本号
             */
            'version'      => '1.0.1.7',
            /**
             * 版本更新的时间
             */
            'update_time'  => '2017-03-24',

            /**
             * 下载地址
             */
            'address'      => 'http://down.client.vronline.com/client/VRonline/1.0.1.7/VRassistant_FullInstaller_1.0.1.7.exe',

            /**
             * 新版本升级说明
             */
            'release_note' => '',
            /**
             * 包大小
             */
            'size'         => '172M',
        ],

        /**
         * 在线安装版本版本号
         */
        'updateol' => [

            /**
             * 版本号
             */
            'version'      => '1.0.1.7',
            /**
             * 版本更新的时间
             */
            'update_time'  => '2017-03-24',

            /**
             * 下载地址
             */
            'address'      => 'http://down.client.vronline.com/client/VRonline_Installer/VRassistant_OnlineInstaller_2017032401.exe',

            /**
             * 新版本升级说明
             */
            'release_note' => '',
            /**
             * 包大小
             */
            'size'         => '2.5M',
        ],

        /**
         * 静默安装版本版本号
         */
        'silence'  => [

            /**
             * 版本号
             */
            'version'      => '1.0.1',
            /**
             * 版本更新的时间
             */
            'update_time'  => '2016-12-08',

            /**
             * 下载地址
             */
            'address'      => 'http://www.vronline.com/',

            /**
             * 新版本升级说明
             */
            'release_note' => '',
            /**
             * 包大小
             */
            'size'         => '10.68M',
        ],
    ],

    /**
     * 最大的强制更新版本
     * 版本小于此版本的都强制更新
     */
    'max_forceup_version' => '1.0',

];
