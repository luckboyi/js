<?php
/**
 * 线上开放的支付渠道
 */
return [

    "vrcoin"     => [
        "title_minipay"          => "V币支付",
        "hidden_charge"          => 2, // 平台充值是否隐藏，0: 不隐藏，全部用户可见; 1: 管理员可见; 2: 隐藏;
        "hidden_minipay_webgame" => 0, // 网页游戏内充值是否隐藏，0: 不隐藏，全部用户可见; 1: 管理员可见; 2: 隐藏;
        "hidden_minipay_vrgame"  => 0, // VR游戏内充值是否隐藏，0: 不隐藏，全部用户可见; 1: 管理员可见; 2: 隐藏;
    ],
    /**
     * 支付宝扫码
     */
    "alipay"     => [
        "action"                 => [
            "scan" => "heepayalipaypcvr",           // alipayscanvr
            "form" => "alipayvr",         // alipayvr
        ], // 支付宝扫码
        "title_charge"           => "支付宝充值",
        "title_minipay"          => "支付宝",
        "icon_charge"            => "", // 渠道图标
        "hidden_charge"          => 0, // 平台充值是否隐藏，0: 不隐藏，全部用户可见; 1: 管理员可见; 2: 隐藏;
        "hidden_minipay_webgame" => 0, // 网页游戏内充值是否隐藏，0: 不隐藏，全部用户可见; 1: 管理员可见; 2: 隐藏;
        "hidden_minipay_vrgame"  => 0, // VR游戏内充值是否隐藏，0: 不隐藏，全部用户可见; 1: 管理员可见; 2: 隐藏;
    ],

    /**
     * 神州付微信
     */
    "wxpay"      => [
        "action"                 => [
            "scan" => "heepaywechatscanvr", //wxshenzhoufumergevr  替换新的action
        ],
        "title_charge"           => "微信充值",
        "title_minipay"          => "微信",
        "icon_charge"            => "wx",
        "hidden_charge"          => 0,
        "hidden_minipay_webgame" => 0,
        "hidden_minipay_vrgame"  => 0,
    ],

    /**
     * 京东钱包银行卡
     */
    "chinabank"  => [
        "action"                 => [
            "form" => "chinabankvr",
        ],
        "title_charge"           => "银行卡充值",
        "title_minipay"          => "银行卡",
        "icon_charge"            => "bank",
        "hidden_charge"          => 0,
        "hidden_minipay_webgame" => 0,
        "hidden_minipay_vrgame"  => 0,
    ],

    /**
     * 支付宝银行卡
     */
    "alipaybank" => [
        "action"                 => [
            "form" => "alipayunionvr",
        ],
        "title_charge"           => "网银-支付宝",
        "title_minipay"          => "网银-支付宝",
        "icon_charge"            => "alipaybank",
        "hidden_charge"          => 0,
        "hidden_minipay_webgame" => 0,
        "hidden_minipay_vrgame"  => 0,
    ],
];
