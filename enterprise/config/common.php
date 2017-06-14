<?php
return [
    /**
     * 接入用户中心分配的appid
     */
    "uc_appid"           => 1,

    /**
     * 接入用户中心分配的appkey
     */
    "uc_appkey"          => "@eWmmNOcnwLYNNjxnBi@XBeK!A0LiuLR",

    /**
     * 接入用户中心分配的paykey
     */
    "uc_paykey"          => "0QCOoVPFRzgy0KSLnC!jAwLHgFArFGNa",

    /**
     * 环境配置
     */
    "environment"        => env('APP_ENV', "online"),

    /**
     * 支付比例
     */
    "pay_rate"           => "10",

    /**
     * 平台币名称
     */
    "platform_coin_name" => "V币",

    /**
     * 客户端请求接口加密用的key
     */
    'vr_client_key'      => "659H5as6AUtpPfDw=K6BuizHPw8V)qwx",

    /**
     * 线下体验店客户端请求接口加密用的key
     */
    'vr_2bclient_key'    => "5_olI6sz=R8HIn5AYQVUyB(7JFqMRmCT",
    /**
     * 2b版本分成比例
     */
    "2b_plat_rate"       => 0.2 + 0.001,
    "third_party"        => [11 => ['appkey' => 'sdl0jJYth9hg!g6jHMiYJ02B!0k3O', 'paykey' => 'gsFrkl1O7FjFRKkl(u)O5YEx)u3=7Kci']],
];
