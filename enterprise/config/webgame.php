<?php
/**
 * 网页游戏配置
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
     * 分类设置
     */
    'tagName' => "页游",
    'class'   => [
        20100 => [
            'id'   => 20100,
            'name' => "角色扮演",
            'img'  => "http://vronline-10005081.cos.myqcloud.com/webgameimg/dev/1000013/history?1",
        ],
        20200 => [
            'id'   => 20200,
            'name' => "模拟经营",
            'img'  => "http://vronline-10005081.cos.myqcloud.com/webgameimg/dev/1000013/history?1",
        ],
        20300 => [
            'id'   => 20300,
            'name' => "休闲竞技",
            'img'  => "http://vronline-10005081.cos.myqcloud.com/webgameimg/dev/1000013/history?1",
        ],
        20400 => [
            'id'   => 20400,
            'name' => "战争策略",
            'img'  => "http://vronline-10005081.cos.myqcloud.com/webgameimg/dev/1000013/history?1",
        ],
        29900 => [
            'id'   => 29900,
            'name' => "其它",
            'img'  => "http://vronline-10005081.cos.myqcloud.com/webgameimg/dev/1000013/history?1",
        ],
    ],

];
