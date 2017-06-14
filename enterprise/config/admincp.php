<?php

/**
 * admincp 菜单管理
 */

return [

    'hash'               => 'vrauth',
    'menu'               => [
        ['name' => 'index', 'title' => '首页'],
        ['name' => 'vrhelp', 'title' => 'VR助手', 'default' => 'index'],
        ['name' => 'news', 'title' => '资讯中心', 'default' => 'index'],
        ['name' => 'tob', 'title' => '体验店', 'default' => 'index'],
        ['name' => 'vronline', 'title' => 'VRONINE', 'default' => 'index'],
        ['name' => 'stat', 'title' => '数据中心', 'default' => 'index'],
        ['name' => 'service', 'title' => '客服中心', 'default' => 'index'],
        ['name' => 'sys', 'title' => '系统设置', 'default' => 'index'],
    ],
    'menu_sub'           => [
        'index'    => ['help' => '帮助'],
        'vrhelp'   => ['video' => '视频', 'vrgame' => 'VR游戏', 'webgame' => '网页游戏', 'developer' => '开发者', 'top' => '推荐', 'position' => '推荐位管理', 'client' => 'VR客户端', 'price' => 'VR定价', 'cdk' => 'CDK管理', 'dbb' => '3D播播审核', 'dbbinfo' => '3D播播信息管理'],
        'news'     => ['article' => '新闻', 'top' => '推荐', 'position' => '推荐位管理'],
        'vronline' => ['news' => '新闻', 'pc' => '评测', 'video' => '视频', 'game' => '游戏', 'top' => '推荐', 'position' => '推荐位管理', 'comments' => '评论审核'],
        'tob'      => ['merchants' => '商户管理', 'defaultgame' => '游戏管理', 'banner' => '首页轮播', 'confirm' => '提现审核', 'other' => '其他设置'],
        'mp'       => ['index' => '公众号'],
        'service'  => ['feedback' => '玩家反馈', 'qa' => 'QA管理'],
        'stat'     => ['index' => '总数据', 'vrgame' => 'VR游戏数据', 'vrvideo' => '视频数据', 'dbbreginfo' => "3D播播注册"],
        'sys'      => ['user' => '用户管理', 'group' => '权限组管理'],
    ],
    'vrhelp_group'       => ['index' => '首页推荐', 'video' => '视频推荐', 'vrgame' => 'VR推荐', 'webgame' => '页游推荐'],
    'vronline_pos_group' => ['index' => '首页', 'news' => '资讯', 'video' => '视频', 'game' => '游戏'],
    '3dbb_info'          => ['3dbb_index' => '活动页面推荐'],
    'news_group'         => ['index' => '首页推荐', 'list' => '列表页推荐', 'detail' => '内页推荐'],
    'perm'               => [
        'sys'                              => ['id' => 100, 'name' => '系统设置', 'tp' => 'menu'],
        'sys/user'                         => ['id' => 101, 'name' => '用户', 'tp' => 'sub'],
        'json/edit/sys_user'               => ['id' => 102, 'name' => '编辑用户'],
        'json/save/sys_user'               => ['id' => 102, 'name' => '编辑用户'],
        'json/del/sys_user'                => ['id' => 102, 'name' => '编辑用户'],
        'sys/group'                        => ['id' => 111, 'name' => '用户组', 'tp' => 'sub'],
        'json/edit/sys_group'              => ['id' => 112, 'name' => '编辑用户组'],
        'json/save/sys_group'              => ['id' => 112, 'name' => '编辑用户组'],
        'json/del/sys_group'               => ['id' => 112, 'name' => '编辑用户组'],

        'news'                             => ['id' => 200, 'name' => '资讯中心', 'tp' => 'menu'],
        'news/article'                     => ['id' => 201, 'name' => '新闻', 'tp' => 'sub'],
        'news/articlePreview'              => ['id' => 201],
        'json/edit/news_article'           => ['id' => 202, 'name' => '新闻编辑'],
        'json/save/news_article'           => ['id' => 202, 'name' => '新闻编辑'],
        'json/save/news_article_sub'       => ['id' => 202, 'name' => '新闻编辑'],
        'json/del/news_article'            => ['id' => 202, 'name' => '新闻编辑'],
        'json/pass/news_article'           => ['id' => 203, 'name' => '新闻审核'],

        'news/top'                         => ['id' => 211, 'name' => '推荐', 'tp' => 'sub'],
        'news/top/switchWeight'            => ['id' => 212, 'name' => '推荐编辑'],
        'json/edit/news_recommend'         => ['id' => 212, 'name' => '推荐编辑'],
        'json/save/news_recommend'         => ['id' => 212, 'name' => '推荐编辑'],
        'json/del/news_recommend'          => ['id' => 212, 'name' => '推荐编辑'],
        'news/position'                    => ['id' => 213, 'name' => '推荐位查看'],
        'json/save/news_position'          => ['id' => 214, 'name' => '推荐位编辑'],
        'json/edit/news_position'          => ['id' => 214, 'name' => '推荐位编辑'],
        'json/del/news_position'           => ['id' => 214, 'name' => '推荐位编辑'],

        'vrhelp'                           => ['id' => 300, 'name' => 'VR助手', 'tp' => 'menu'],
        'vrhelp/video'                     => ['id' => 301, 'name' => '视频', 'tp' => 'sub'],
        'json/save/vrhelp_video'           => ['id' => 302, 'name' => '视频编辑'],
        'json/edit/vrhelp_video'           => ['id' => 302, 'name' => '视频编辑'],
        'json/del/vrhelp_video'            => ['id' => 302, 'name' => '视频编辑'],
        'json/pass/vrhelp_video'           => ['id' => 303, 'name' => '视频审核'],

        'vrhelp/top'                       => ['id' => 311, 'name' => '推荐', 'tp' => 'sub'],
        'vrhelp/switchWeight'              => ['id' => 312, 'name' => '推荐编辑'],
        'vrhelp/rec/save'                  => ['id' => 312, 'name' => '推荐编辑'],
        'json/save/top_banner'             => ['id' => 312, 'name' => '推荐编辑'],
        'json/edit/top_banner'             => ['id' => 312, 'name' => '推荐编辑'],
        'vrhelp/rec/del'                   => ['id' => 312, 'name' => '推荐编辑'],
        'vrhelp/position'                  => ['id' => 313, 'name' => '推荐位查看'],
        'json/save/vrhelp_position'        => ['id' => 314, 'name' => '推荐位编辑'],
        'json/edit/vrhelp_position'        => ['id' => 314, 'name' => '推荐位编辑'],
        'json/del/vrhelp_position'         => ['id' => 314, 'name' => '推荐位编辑'],

        'vrhelp/client'                    => ['id' => 321, 'name' => '客户端版本', 'tp' => 'sub'],
        'json/edit/product_client'         => ['id' => 322, 'name' => '版本编辑'],
        'json/del/product_client'          => ['id' => 322, 'name' => '版本编辑'],
        'json/update/product_client'       => ['id' => 322, 'name' => '版本编辑'],
        'client/databasePublic'            => ['id' => 323, 'name' => '版本发布'],

        'vrhelp/webgame'                   => ['id' => 331, 'name' => '页游管理', 'tp' => 'sub'],
        'vrhelp/webgame/news'              => ['id' => 331],
        'json/edit/webgame_news'           => ['id' => 332, 'name' => '攻略编辑'],
        'json/del/webgame_news'            => ['id' => 332, 'name' => '攻略编辑'],
        'json/update/webgame_news'         => ['id' => 332, 'name' => '攻略编辑'],

        'vrhelp/vrgame'                    => ['id' => 341, 'name' => 'VR游戏', 'tp' => 'sub'],
        'json/edit/vrhelp_vrgame'          => ['id' => 342, 'name' => '游戏编辑'],
        'json/save/vrhelp_vrgame'          => ['id' => 342, 'name' => '游戏编辑'],
        'json/del/vrhelp_vrgame'           => ['id' => 343, 'name' => '游戏审核'],
        'json/pass/vrhelp_vrgame'          => ['id' => 343, 'name' => '游戏审核'],

        'vrhelp/price'                     => ['id' => 351, 'name' => 'VR定价', 'tp' => 'sub'],
        'json/edit/vrhelp_price'           => ['id' => 351, 'name' => 'VR定价'],
        'json/save/vrhelp_price'           => ['id' => 351, 'name' => 'VR定价'],

        'vrhelp/cdk'                       => ['id' => 361, 'name' => 'CDK管理', 'tp' => 'sub'],
        'json/edit/vrhelp_cdk'             => ['id' => 361, 'name' => 'CDK管理'],
        'json/save/vrhelp_cdk'             => ['id' => 361, 'name' => 'CDK管理'],
        'vrhelp/cdkDown'                   => ['id' => 361, 'name' => 'CDK管理'],

        'vrhelp/dbb'                       => ['id' => 371, 'name' => '3D播播视频审核', 'tp' => 'sub'],
        'vrhelp/dbbreginfo'                => ['id' => 371],
        'json/save/vrhelp_3dbb'            => ['id' => 371, 'name' => '3D播播视频审核'],

        'vrhelp/dbbinfo'                   => ['id' => 381, 'name' => '3D播播首页视频', 'tp' => 'sub'],
        'json/edit/dbb_info'               => ['id' => 381],
        'json/save/dbb_info'               => ['id' => 381],

        'vrhelp/developer'                 => ['id' => 391, 'name' => '开发者审核', 'tp' => 'sub'],
        'json/pass/developer'              => ['id' => 391],

        'stat'                             => ['id' => 400, 'name' => '数据中心', 'tp' => 'menu'],
        'stat/index'                       => ['id' => 401, 'name' => 'VR助手数据', 'tp' => 'sub'],
        'stat/vrgame'                      => ['id' => 402, 'name' => 'VR游戏数据', 'tp' => 'sub'],
        'stat/vrvideo'                     => ['id' => 403, 'name' => '视频数据', 'tp' => 'sub'],

        'service'                          => ['id' => 500, 'name' => '客服中心', 'tp' => 'menu'],
        'service/feedback'                 => ['id' => 501, 'name' => '玩家反馈', 'tp' => 'sub'],
        'service/feedbackInfo'             => ['id' => 501, 'name' => '玩家反馈'],
        'json/save/service_feedback'       => ['id' => 502, 'name' => '反馈编辑'],
        'json/save/service_feedback_reply' => ['id' => 502, 'name' => '反馈编辑'],
        'json/edit/service_feedback'       => ['id' => 502, 'name' => '反馈编辑'],
        'json/del/service_feedback'        => ['id' => 502, 'name' => '反馈编辑'],

        'service/qa'                       => ['id' => 511, 'name' => 'QA', 'tp' => 'sub'],
        'json/save/service_qa'             => ['id' => 512, 'name' => 'QA编辑'],
        'json/edit/service_qa'             => ['id' => 512, 'name' => 'QA编辑'],
        'json/del/service_qa'              => ['id' => 512, 'name' => 'QA编辑'],

        'tob'                              => ['id' => 600, 'name' => '体验店', 'tp' => 'menu'],
        'tob/merchants'                    => ['id' => 601, 'name' => '商户管理', 'tp' => 'sub'],
        'json/save/tob_merchants'          => ['id' => 602, 'name' => '商家编辑'],
        'json/edit/tob_merchants'          => ['id' => 602, 'name' => '商家编辑'],
        'json/del/tob_merchants'           => ['id' => 602, 'name' => '商家编辑'],

        'tob/defaultgame'                  => ['id' => 611, 'name' => '默认游戏', 'tp' => 'sub'],
        'json/save/tob_addgame'            => ['id' => 611, 'name' => '添加游戏'],

        'tob/other'                        => ['id' => 621, 'name' => '其他设置', 'tp' => 'sub'],
        'tob/banner'                       => ['id' => 631, 'name' => '官网banner', 'tp' => 'sub'],

        'tob/confirm'                      => ['id' => 641, 'name' => '提现审核', 'tp' => 'sub'],

        'vronline'                         => ['id' => 700, 'name' => 'VRONLINE', 'tp' => 'menu'],
        'vronline/news'                    => ['id' => 701, 'name' => '新闻查看', 'tp' => 'sub'],
        'json/save/vronline_news'          => ['id' => 702, 'name' => '新闻编辑'],
        'json/edit/vronline_news'          => ['id' => 702, 'name' => '新闻编辑'],
        'json/del/vronline_news'           => ['id' => 702, 'name' => '新闻编辑'],

        'vronline/pc'                      => ['id' => 711, 'name' => '评测查看', 'tp' => 'sub'],
        'json/save/vronline_pc'            => ['id' => 712, 'name' => '评测编辑'],
        'json/edit/vronline_pc'            => ['id' => 712, 'name' => '评测编辑'],
        'json/del/vronline_pc'             => ['id' => 712, 'name' => '评测编辑'],

        'vronline/video'                   => ['id' => 721, 'name' => '视频查看', 'tp' => 'sub'],
        'json/save/vronline_video'         => ['id' => 722, 'name' => '视频编辑'],
        'json/edit/vronline_video'         => ['id' => 722, 'name' => '视频编辑'],
        'json/del/vronline_video'          => ['id' => 722, 'name' => '视频编辑'],

        'vronline/game'                    => ['id' => 731, 'name' => '游戏查看', 'tp' => 'sub'],
        'json/save/vronline_game'          => ['id' => 732, 'name' => '游戏编辑'],
        'json/edit/vronline_game'          => ['id' => 732, 'name' => '游戏编辑'],
        'json/del/vronline_game'           => ['id' => 732, 'name' => '游戏编辑'],

        'vronline/top'                     => ['id' => 741, 'name' => '推荐', 'tp' => 'sub'],
        'vronline/switchWeight'            => ['id' => 742, 'name' => '推荐编辑'],
        'json/save/vronline_top'           => ['id' => 742, 'name' => '推荐编辑'],
        'json/edit/vronline_top'           => ['id' => 742, 'name' => '推荐编辑'],
        'json/del/vronline_top'            => ['id' => 742, 'name' => '推荐编辑'],

        'vronline/position'                => ['id' => 751, 'name' => '推荐位查看', 'tp' => 'sub'],
        'json/save/vronline_position'      => ['id' => 752, 'name' => '推荐位编辑'],
        'json/edit/vronline_position'      => ['id' => 752, 'name' => '推荐位编辑'],
        'json/del/vronline_position'       => ['id' => 752, 'name' => '推荐位编辑'],

        'vronline/comments'                => ['id' => 751, 'name' => '评论审核', 'tp' => 'sub'],
        'json/edit/vronline_comments'      => ['id' => 752, 'name' => '评论审核操作'],
    ],

];
