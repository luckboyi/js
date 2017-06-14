<?php
/**
 * vr游戏配置
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
    'tagName'        => "游戏",

    'class'          => [
        10100 => [
            'id'   => 10100,
            'name' => "角色扮演",
            'img'  => "",
        ],
        10200 => [
            'id'   => 10200,
            'name' => "即时战略",
            'img'  => "",
        ],
        10400 => [
            'id'   => 10400,
            'name' => "策略战棋",
            'img'  => "",
        ],
        10500 => [
            'id'   => 10500,
            'name' => "模拟经营",
            'img'  => "",
        ],
        10600 => [
            'id'   => 10600,
            'name' => "模拟格斗",
            'img'  => "",
        ],
        10700 => [
            'id'   => 10700,
            'name' => "冒险",
            'img'  => "",
        ],
        10800 => [
            'id'   => 10800,
            'name' => "飞行模拟",
            'img'  => "",
        ],
        10900 => [
            'id'   => 10900,
            'name' => "赛车竞速",
            'img'  => "",
        ],
        11000 => [
            'id'   => 11000,
            'name' => "动作",
            'img'  => "",
        ],
        11100 => [
            'id'   => 11100,
            'name' => "射击",
            'img'  => "",
        ],
        11300 => [
            'id'   => 11300,
            'name' => "体育",
            'img'  => "",
        ],
        11500 => [
            'id'   => 11500,
            'name' => "益智",
            'img'  => "",
        ],
        11600 => [
            'id'   => 11600,
            'name' => "养成",
            'img'  => "",
        ],
        19900 => [
            'id'   => 19900,
            'name' => "其他",
            'img'  => "",
        ],
    ],

    /**
     * 支持设备列表
     */
    'support_device' => [
        1 => [
            'id'             => 1,
            'name'           => 'DPVR E2',
            'icon'           => 'http://www.kingopr.com/public/source/vrdevice/683d82dcff5113a61936f571519fd8a2.jpg',
            'weight'         => 1,
            'icon-class'     => "deepoon",
            'www_icon_class' => "", // 官网图标
        ],
        2 => [
            'id'             => 2,
            'name'           => 'OCULUS_DK2',
            'icon'           => 'http://www.kingopr.com/public/source/vrdevice/ba45c8f60456a672e003a875e469d0eb.jpg',
            'weight'         => 2,
            'icon-class'     => "oculus",
            'www_icon_class' => "oculcus_icon", // 官网图标
        ],
        3 => [
            'id'             => 3,
            'name'           => 'Oculus Rift',
            'icon'           => 'http://www.kingopr.com/public/source/vrdevice/2b04df3ecc1d94afddff082d139c6f15.jpg',
            'weight'         => 3,
            'icon-class'     => "oculus",
            'www_icon_class' => "oculcus_icon",
        ],
        4 => [
            'id'             => 4,
            'name'           => 'HTC VIVE',
            'icon'           => 'http://www.kingopr.com/public/source/vrdevice/2b04df3ecc1d94afddff082d139c6f151468980054.jpg',
            'weight'         => 4,
            'icon-class'     => "htc",
            'www_icon_class' => "htc_icon",
        ],
        5 => [
            'id'             => 5,
            'name'           => 'OSVR HDK1',
            'icon'           => 'http://www.kingopr.com/public/source/vrdevice/2b04df3ecc1d94afddff082d139c6f151468980054.jpg',
            'weight'         => 5,
            'icon-class'     => "osvr",
            'www_icon_class' => "",
        ],
        6 => [
            'id'             => 6,
            'name'           => 'DPVR E3',
            'icon'           => 'http://www.kingopr.com/public/source/vrdevice/683d82dcff5113a61936f571519fd8a2.jpg',
            'weight'         => 1,
            'icon-class'     => "deepoon",
            'www_icon_class' => "", // 官网图标
        ],
    ],

	/**
	 * 新手引导
	 */
    'newer_guide' => [
		'device_sale' => [
			'start' => 1,					// 开关
			'desc'  => '购买',				// 文字
			'link'  => 'http://www.deepoon.com/',		// 链接
		],
	],

];
