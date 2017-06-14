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
		 * tob 默认游戏
		 */
		"tob_defaultgame" => [

			"key_prefix" => "tob_defaultgame",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "string",
		],

		/**
		 * tob 默认价格
		 */
		"tob_defaultproduct" => [

			"key_prefix" => "tob_defaultproduct",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "string",
		],

		/**
		 * cos存储文件，已删除可能
		 */
		"cos_file" => [

			"key_prefix" => "cos_img_vronline/",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "string",
		],

		/**
		 * 页游礼包，队列
		 */
		"webgame_gift" => [

			"key_prefix" => "queue_gift_code_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "list",
		],

		/**
		 * 页游领取礼包记录
		 */
		"webgame_user_gift" => [

			"key_prefix" => "get_gift_code_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "hash",
		],

		/**
		 * 需要去重的统计，列表
		 * 比如点赞，按用户统计，每个用户只能点一次，保存点赞的用户列表
		 * 页游玩游戏统计，每个用户只统计一次，保存玩游戏的用户列表
		 */
		"support" => [

			"key_prefix" => "common_support_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "set",
		],

		/**
		 * 用户的单一标志位，记录用户的信息
		 * 每个信息一个字段，可以记录用户的一些状态
		 * 比如: 用户玩的某个游戏的最后一个服的serverid，key是带appid的字符串
		 */
		"user_flags" => [

			"key_prefix" => "user_flags_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "hash",
		],

		/**
		 * 数量统计
		 * 不去重的统计，点击统计
		 * 视频播放次数，VR游戏下载次数等
		 */
		"counting" => [

			"key_prefix" => "counting_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "hash",
		],

		/**
		 * 客户端版本管理
		 */
		"vrclient" => [

			"key_prefix" => "vrclient_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "hash",
		],

		/**
		 * 2b版本统计游戏时长
		 */
		"2bplaylong" => [

			"key_prefix" => "2bplaylong_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "hash",
		],

		/**
		 * 游戏启动次数统计
         * 包括2B版本店铺内统计(game_play_stat_{$merchantid})、2B版本整体统计(game_play_stat_2Ball)
		 */
		"game_play_stat" => [

			"key_prefix" => "game_play_stat_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "hash",
		],

		/**
		 * CDK
		 */
		"cdk_center" => [

			"key_prefix" => "cdk_center_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "list",
		],

		/**
		 * 数量统计缓存，页面浏览数、评论数量、点赞数量
		 */
		"whole_counter" => [

			"key_prefix" => "whole_counter_",
			"expire" => 0,
			"connection" => "webgame",
			"type" => "hash",
		],

		/**
		 * 数据中心统计，队列
		 */
		"datacenterstat" => [

			"key_prefix" => "datacenter_stat_queue_key", // key前缀
			"expire" => 0, // 过期时间
			"connection" => "datacenterstat", // 使用的缓存配置
			"type" => "list", // 缓存类型
		],

	],

	/**
	 * memcached key配置
	 */
	'memcached' => [

		/**
		 * 并发锁
		 */
		"lock" => [
			"key_prefix" => "lock_",
			"expire" => 3,
			"connection" => "template",
		],

		/**
		 * 激活码
		 */
		"active_code" => [

			/**
			 * key的前缀，或key
			 */
			"key_prefix" => "active_code_",

			/**
			 * 过期时间，单位：秒，null为不过期，优先由代码里指定，其次由该配置指定
			 */
			"expire" => 3600,

			/**
			 * 连接的memcached服务器配置
			 */
			"connection" => "template",
		],
		/**
		 * 线下体验中心激活码
		 */
		"merchant_active_code" => [

			/**
			 * key的前缀，或key
			 */
			"key_prefix" => "merchant_active_code_",

			/**
			 * 过期时间，单位：秒，null为不过期，优先由代码里指定，其次由该配置指定
			 */
			"expire" => 3600,

			/**
			 * 连接的memcached服务器配置
			 */
			"connection" => "template",
		],
		"admincp_emailcode" => [

			/**
			 * key的前缀，或key
			 */
			"key_prefix" => "admincp_email_code",

			/**
			 * 过期时间，单位：秒，null为不过期，优先由代码里指定，其次由该配置指定
			 */
			"expire" => 1200,

			/**
			 * 连接的memcached服务器配置
			 */
			"connection" => "template",
		],

	],

];
