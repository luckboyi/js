<?php

return [

    /**
     * 接入用户中心分配的appid
     */
    "uc_appid"             => 1,

    /**
     * 接入用户中心分配的appkey
     */
    "uc_appkey"            => "@eWmmNOcnwLYNNjxnBi@XBeK!A0LiuLR",

    /**
     * 接入用户中心分配的paykey
     */
    "uc_paykey"            => "0QCOoVPFRzgy0KSLnC!jAwLHgFArFGNa",

    /**
     * 支付资源号
     */
    "pay_source_id"        => 1282160,

    /**
     * 登录状态的有效时间
     * 单位分钟
     * 默认7天
     */
    "expire_token"         => 7 * 24 * 60,

    /**
     * 短信验证码有效时间
     * 单位 分钟
     */
    "expire_sms_code"      => 5,

    /**
     * 邮箱验证码有效时间
     * 单位 分钟
     */
    "expire_email_code"    => 3 * 60,

    /**
     * 普通用户名规则
     */
    "account_pattern"      => "/^[a-z0-9_]{6,18}$/",

    /**
     * 充值人民币对平台币比例
     */
    "plantb_count_per_rmb" => 10,

    /**
     * 有高权限的appid
     */
    "hight_weight_appid"   => [1],

    /**
     * 客户端请求接口加密用的key
     */
    'vr_client_key'        => "659H5as6AUtpPfDw=K6BuizHPw8V)qwx",

    /**
     * 2b版本分成比例
     */
    "2b_plat_rate"         => 0.2 + 0.001,

];
