<?php
/**
 * 官网路由
 */

Route::group(['domain' => 'www.vronline.com', "name" => "website"], function () {
    Route::get('/vrhelp/index', 'VrhelpController@index');
    Route::get('/vrhelp/game', 'VrhelpController@game');
    Route::any('/vrhelp/game/{id}', 'VrhelpController@gameInfo');
    Route::post('/vrhelp/search', 'VrhelpController@search');
    Route::get('/vrhelp/video', 'VrhelpController@video');
    Route::get('/vrhelp/video/list', 'VrhelpController@videoList');
    Route::any('/vrhelp/gameDetail/{id}', 'VrhelpController@gameDetail');
    Route::get('/vrhelp/searchGame', 'VrhelpController@searchGame');
    Route::get('/vrhelp/searchVideo', 'VrhelpController@searchVideo');
    Route::any('/vrhelp/videoDetai/{id}', 'VrhelpController@videoDetail');
    Route::get('/vrhelp/videoRecord/{uid}/{vid}', 'VrhelpController@videoRecord');
    Route::get('/vrhelp/gameRecord/{uid}/{vid}/{type}', 'VrhelpController@gameRecord');
    Route::get('/vrhelp/drive/{did}', 'VrhelpController@drive');
    Route::get('/vrhelp/download', 'VrhelpController@download');
    Route::get('/vrhelp/home', 'VrhelpController@home');

    Route::get('/vrhelp/3dvr', 'VrhelpController@vrfrom3d');
    Route::get('/vrhelp/user', 'VrhelpController@user');
    Route::get('/vrhelp/order', 'VrhelpController@order');
    Route::get('/vrhelp/consume', 'VrhelpController@consume');
    Route::get('/vrhelp/logintop', 'VrhelpController@loginTop');
    Route::post('/vrhelp/gamehistory/add', 'VrhelpController@addGameHistory');

    Route::get('/vrhelp/login', function () {
        return view('vrhelp/loginifame');
    });

    Route::post('/vrhelp/videoListInterface', 'VrhelpController@videoListInterface');
    Route::post('/profile/edit', 'UserController@profileEdit');
    Route::post('/password/edit', 'UserController@passwordEdit');
    Route::post('/mobile/sms', 'UserController@mobileSms');
    Route::post('/mobile/bind', 'UserController@mobileBind');

    Route::get('/upload/imgCosAppSign', 'UploadController@imgCosAppSign');

    Route::get('/testPay', 'TestController@testPay');
    Route::any('/callback/transcoding', 'VersionController@transcodingBack');

    Route::get('/customer/service', 'CustomerServicesController@index');
    Route::get('/customer/service/faq/{tp}', 'CustomerServicesController@faq');
    Route::post('/customer/service/faqpost/{id}', 'CustomerServicesController@faqpost');
    Route::get('/customer/service/faqinfo/{id}', 'CustomerServicesController@faqinfo');
    Route::get('/customer/service/question/{id}', 'CustomerServicesController@question');
    Route::get('/customer/service/myquestion', 'CustomerServicesController@myQuestion');
    Route::get('/customer/service/questioninfo/{id}', 'CustomerServicesController@questionInfo');
    Route::post('/customer/service/submitQuestion', 'CustomerServicesController@submitQuestion');
    Route::post('/customer/service/replyQuestion', 'CustomerServicesController@replyQuestion');
    Route::post('/customer/service/completeQuestion', 'CustomerServicesController@completeQuestion');

    /**
     * 新资讯站
     */
    Route::get('/vronline/index', 'VronlineController@index');

    Route::get('/vronline/article', 'VronlineController@article');
    Route::get('/vronline/game', 'ArticleController@game');
    Route::get('/vronline/video', 'VronlineController@video');

    Route::get('/vronline/pc/search/{words}', 'VronlineController@pcSearch');
    Route::get('/vronline/pc/search/{words}/{page}', 'VronlineController@pcSearch');

    Route::get('/vronline/pc/list/{id}', 'VronlineController@pcList');
    Route::get('/vronline/pc/list/{id}/{page}', 'VronlineController@pcList');

    Route::get('/vronline/search/{words}', 'VronlineController@search');
    Route::get('/vronline/search/{words}/{page}', 'VronlineController@search');

    Route::get('/vronline/article/list/{id}', 'VronlineController@articleList');
    Route::get('/vronline/article/list/{id}/{page}', 'VronlineController@articleList');
    Route::get('/vronline/authorInfo', 'VronlineController@authorInfo');
    Route::get('/vronline/author/{id}', 'VronlineController@author');
    Route::get('/vronline/author/{id}/{page}', 'VronlineController@author');
    Route::get('/vronline/top/{code}', 'VronlineController@top');
    Route::get('/vronline/tag/{tag}', 'VronlineController@tag');

    Route::get('/vronline/game/list', 'ArticleController@gameList');
    Route::get('/vronline/game/search', 'ArticleController@gameSearch');
    Route::get('/vronline/video/list', 'VronlineController@videoList');

    Route::get('/vronline/article/detail/{id}', 'VronlineController@articleDetail');
    Route::get('/vronline/game/detail/{id}', 'ArticleController@gameDetail');
    Route::get('/vronline/video/detail/{id}', 'VronlineController@videoDetail');

    Route::get('/vronline/game/topic/detail', 'ArticleController@gameDetailTopic');
    /**
     * 统计PV
     */
    Route::get('/vronline/stat/pv/{type}/{itemid}', 'ArticleController@addPv');

    /**
     * 统计PV
     */
    Route::get('/vronline/support', 'ArticleController@support');
    /**
     * 资讯站
     */
    Route::get('/', 'NewsController@index', 1200);
    Route::get('/news/list/{id}', 'NewsController@newsList', 1200);
    Route::get('/news/list/more/{id}', 'NewsController@moreList');
    Route::get('/news/detail/{id}.html', 'NewsController@newsDetail', 1200);
    Route::get('/news/support', 'NewsController@support');
    Route::get('/parentintro/down', 'NewsController@parentIntroDown');

    /**
     *    首页
     */
    Route::any('/search', 'WebsiteController@search');
    Route::any('/suggest', 'WebsiteController@suggest');
    Route::any('/switch', 'WebsiteController@switchRecommend');
    Route::get('/index', 'WebsiteController@down');

    /**
     * 登录
     */
    Route::get('/login', 'UserController@webLogin');
    Route::get('/register', 'UserController@webRegister');
    Route::get('/forgetpwd', 'UserController@forgetPwd');

    /**
     * 充值
     */
    Route::get('/pay', 'PayController@index');

    /**
     * 意见反馈 填写页面
     */
    Route::get('/advice', 'ServiceController@advice');

    // 意见反馈ajax交互路由
    Route::group(['prefix' => 'ajax'], function () {
        Route::get('adviceAjax', 'ServiceController@adviceAjax');
        Route::post('adviceAjax', 'ServiceController@adviceAjax');
    });
    Route::get('servers/showimg/{id}/{uid}/{name}', 'ServiceController@printImg');

    /**
     * 忘记密码
     */
    Route::group(['prefix' => 'ajax'], function () {
        Route::get('sendFindCode', 'UserController@sendFindPwdMsg');
    });
    Route::group(['prefix' => 'ajax'], function () {
        Route::get('resetPwd', 'UserController@findPassword');
    });

    /**
     * 静态页面
     */
    Route::get('contact', function () {
        return view('website/contact');
    });
    Route::get('device', function () {
        return view('website/device');
    });
    Route::get('parent_intro', function () {
        return view('news/parentintro');
    });

    /**
     * 页游
     */
    Route::get('webgame', 'WebsiteController@webgame');
    Route::get('webgame/index', 'WebsiteController@webgame');
    /*
     * 页游礼包列表
     */
    Route::get('webgame/giftList', 'WebgameController@giftList');
    Route::post('webgame/sort/{type}', 'WebgameController@getWebgameBySort');
    Route::get('webgame/{appid}', 'WebsiteController@webgameDetail');
    Route::get('webgame/api/list', 'WebgameController@webGameApiList');
    /**
     * 页游->新版
     */
    Route::get('webgamenew', 'WebsiteController@webgamenew');
    Route::get('webgamenew/index', 'WebsiteController@webgamenew');

    /*
     * 多媒体页面
     */
    Route::get('media', 'MediaController@index');
    Route::get('media/list', 'MediaController@mediaList');
    Route::get('mediaHistory', 'VideoController@videoHistory');
    Route::get('media/play/{id}', 'MediaController@mediaPlay');
    Route::get('media/api/list', 'VideoController@videoApiList');

    Route::get('media/history/api', 'VideoController@getHistoryApi');

    /*
     * 我的礼包
     */
    Route::get('websit/getMyPackage', 'WebgameController@websiteMyPackage');

    /*
     * 视频详情-->先放在官网的域名下
     */
    Route::get('videoPlay', 'VideoController@videoPlay');

    /*
     * 添加支持或不支持路由
     */
    Route::get('comment/addSupport', 'gameCommentController@support');
    Route::post('comment/addSupport', 'gameCommentController@support');
    /**
     * 页游
     */
    Route::get('vrgame', 'VrGameController@index');
    Route::get('vrgame/index', 'VrGameController@index');
    Route::get('vrgame/list', 'VrGameController@vrGameList');
    Route::get('vrgame/api/list', 'VrGameController@vrGameApiList');
    Route::get('vrgame/{appid}', 'VrGameController@vrGameDetail');

    /*
     * 获取热门vr游戏的接口
     */
    Route::get('hotvrgame', 'VrGameController@getHotVrGame');

    /*
     * 我的礼包
     */
    Route::get('website/getMyPackage', 'WebgameController@getMyPackage');

    /*
     * 领取礼包页
     */
    Route::get('website/packageReceive/{appId}/', 'WebgameController@PackageReceive');

    /**
     * 需要登入后执行的页面
     */
    Route::get('profile', 'WebsiteController@profile');

    Route::get('profile/video', 'WebsiteController@profileVideo');
    Route::get('profile/problem', 'WebsiteController@profileProblem');
    Route::get('profile/about', 'WebsiteController@profileAbout');
    Route::post('profile/video/save', 'WebsiteController@profileVideoSave');
    /**
     * 选服页面
     */
    Route::get('/servers/{appid}', 'WebgameController@servers');

    /**
     * 游戏页面
     */
    Route::get('/start/{appid}/{serverid}', 'WebgameController@start');

    /**
     * 检测更新
     */
    Route::get('/update/clienttmp', 'CheckUpdateController@clientTemp');
    Route::get('/update/clienttmponline', 'CheckUpdateController@clientTempOnlineUpd');

    Route::get('/vrdevicelus', 'CheckUpdateController@oculusProxy');
    Route::get('/devicedrives', 'CheckUpdateController@deviceDrives');

    /**
     * 新版检测更新
     * type: update:检测版本更新;
    online:在线包下载;
     */
    Route::get('/update/client/{type}', 'CheckUpdateController@client');

    /**
     * 客户端路由=>视频首页
     */
    Route::get('client/video/index', 'VideoController@media');
    Route::get('client/video/index/{sort}', 'VideoController@mediaMore');
    /**
     * 播放器路由
     */
    Route::get('client/video/player', 'VideoController@videoPlay');
    /**
     * 本地播放路由
     */
    Route::get('client/video/playerlocal', 'VideoController@videoPlayLocal');
    /**
     * 用户的播放记录页面
     */
    Route::get('client/video/history', 'VideoController@videoHistory');

    /**
     * 获取视频分类列表
     */
    Route::get('video/page/{sort}', 'VideoController@videoPageDate');
    Route::post('video/page/{sort}', 'VideoController@videoPageDate');
    /**
     * 获取视频分类列表=>给client页面提供的分页数据
     */
    Route::get('video/page2/{sort}', 'VideoController@videoPageDate2');
    Route::post('video/page2/{sort}', 'VideoController@videoPageDate2');

    /**
     * 获取官网首页VR游戏分类列表
     */
    Route::get('vrGame/paging', 'WebsiteController@vrPageDate');
    Route::post('vrGame/paging', 'WebsiteController@vrPageDate');
    /**
     * 获取官网首页网页游戏分类列表
     */
    Route::get('webGame/paging/{type}', 'WebsiteController@webGamePageData');
    Route::post('webGame/paging/{type}', 'WebsiteController@webGamePageData');

    /**
     * 检查页游是否有有效服务器
     */
    Route::get('webgame/validserver/{appid}', 'WebsiteController@isValidServer');

    /**
     * 判断用户在服务器中是否存在角色
     */
    Route::get('hasrole', 'WebsiteController@hasRole');

    /**
     * 协议
     */
    Route::get('license/service', function () {
        return view('website.license.service');
    });
    Route::get('license/video_upload', function () {
        return view('website.license.video_upload');
    });
    Route::get('license/sale', function () {
        return view('website.license.sale');
    });

    /**
     * 商城购买以及cdk
     */
    Route::post('/cdk/getvrgame', 'CdkController@getGameByCdk');

    /**
     * 生成CDK
     */
    Route::get('/cdk/importcdk', 'CdkController@importCdk');

    Route::post('shop/gameStat/{appid}', 'ShopController@gameStat');
    Route::post('shop/myGame', 'ShopController@myGame');
    Route::any('game/xml/{id}', 'ShopController@gameXml');

    /**
     * 购买回调
     */
    Route::get('/buy/callback', 'PayController@buyCallback');

    /**
     * 大朋活动
     */
    Route::any('/deepoon/joinStat', 'DeepoonController@joinStat');
    Route::any('/deepoon/videoList', 'DeepoonController@videoList');
    Route::get('/{auto}/{videoId}/vrplayer.xml', 'Vr3dbbController@vrplayer');
    Route::get('/3dbb', 'Vr3dbbController@index');
    Route::get('/load3dbbStat', 'Vr3dbbController@load3dbbStat');

    Route::get('/3dupload', 'Vr3dbbController@uploadVideoView');
    Route::get('/signup', 'Vr3dbbController@signUpView');
    Route::get('/3dbbprotocol', function () {
        return view('act.protocol');
    });
    Route::get('/3dbbrule', function () {
        return view('act.rule');
    });

    Route::post('/signUpCode', 'Vr3dbbController@signUpCode');
    Route::post('/signUpSubmit', 'Vr3dbbController@signUpSubmit');
    Route::post('/uploadVideoSubmit', 'Vr3dbbController@uploadVideoSubmit');

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |                           评 论 相 关                                       |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */
    /**
     * 发表评论
     */
    Route::any('/newcomment/add', 'CommentController@addComment');

    /**
     * 发表回复
     */
    Route::any('/newcomment/addreply', 'CommentController@addReply');

    /**
     * 获取最新评论
     */
    Route::any('/newcomment/get', 'CommentController@getComments');

    /**
     * 点赞
     */
    Route::any('/newcomment/support', 'CommentController@support');

    Route::any('/newcomment/getUnReviewComments', 'CommentController@getUnReviewComments');
    Route::any('/newcomment/review', 'CommentController@reviewComment');

    /**
     * 新手引导
     */
    Route::any('/newer/top/{tp}', 'VrGameController@newerTop');
    Route::any('/newer/buytips', 'VrGameController@newerBuyTips');

});
