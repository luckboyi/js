<?php

/**
 * 各种服务器配置
 */

return [

    /**
     * udp日志服务器
     */
    'udpserver' => [

        'host' => env('UDP_HOST', ""),
        'port' => env('UDP_PORT', ""),

    ],

    /**
     * 图片服务器
     */
    'imageserver' => [
        //'ip' => '10.154.54.37',
        'ip' => env('IMAGE_SERVER_HOST', ""),
    ],

];
