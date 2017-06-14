<?php

return [
    'default'      => [
        "connection" => 'db_operate',
        "timestamps" => false,
        "primaryKey" => 'id',
    ],

    'GameType'     => [
        "table"       => 'v_gametype',
        "primaryKey"  => 'gtid',
        "allowMethod" => ["getAllPassedType"],
    ],

    'Game'         => [
        "connection"     => 'db_webgame',
        "table"          => 't_webgame',
        "primaryKey"     => 'appid',
        "transAttribute" => [
            "getSupportAttribute",
        ],
        "allowMethod"    => ["comment"],
        "transJson"      => [
            "getMiniDeviceAttribute",
            "getRecommDeviceAttribute",
        ],
    ],

    'Comment'      => [
        "table" => 't_comment',
        "dates" => ['deleted_at'],
    ],

    'Recommend'    => [
        "table"       => 'v_recommend',
        "allowMethod" => ["game"],
    ],

    'VideoHistory' => [
        "table"          => 'v_history',
        "transAttribute" => ["getPurchasedidAttribute"],
    ],

    'GameHistory'  => [
        "table"          => 'v_ghistory',
        "transAttribute" => [
            "getPurchasedidAttribute",
            "getCookiesgidAttribute",
            "getCookiesgtimeAttribute",
            "getCookiespretimeAttribute",
        ],
    ],

    "Menu"         => [
        "connection"  => 'system',
        "table"       => 'action_menu',
        "allowMethod" => ["getAdminMenuList"],
    ],

    "DeviceType"   => [
        "table"       => 'v_devicetype',
        "primaryKey"  => 'dtid',
        "allowMethod" => ["getAllPassedType"],
    ],

    "Ad"           => [
        "table" => 'v_add_ad',
    ],
];
