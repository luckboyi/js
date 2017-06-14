<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/pay/html/{merchantid}/{terminal_sn}/{appid}', 'ToBStoreController@mobilePay');
Route::get('/payback/{merchantid}/{terminal_sn}/{appid}/{productId}', 'ToBStoreController@payPageBack');

Route::get('/test1', 'TestController@index');

/**
 * 登录
 */
Route::get('user/test/{type}', 'UserController@test');

/**
 * 官网首页
 */
Route::group(['domain' => 'www.vronline.com', "name" => "website"], function () {
    Route::get('/', 'NewsController@index')->name("home");
    Route::get('/pchome', 'WebsiteController@index')->name("home");
    Route::get('charge', 'WebsiteController@charge')->name("charge");
    Route::get('/vronline', 'WebsiteController@vrOnline')->name("vronline");
    Route::get('/down', 'WebsiteController@down')->name("down");
});

/**
 * 官网首页
 */
Route::group(['domain' => 'vronline.com'], function () {
    Route::get('/', 'NewsController@index');
    Route::get('/pchome', 'WebsiteController@index');
});

Route::get('/imgCodeStat', 'UserController@imgCodeStat');
/**
 * 全局登出
 */

Route::get('/upload/netCenterSign', 'UploadController@netCenterSign');
Route::post('/upload/test', 'UploadController@test');

//ajax login
Route::post('api/login', 'UserController@apiLogin');
//ajax account
Route::post('api/account', 'UserController@apiAccountCheck');
//logout
Route::get('logout', 'UserController@loginOut');
//ajax register
Route::post('api/register', 'UserController@apiRegister');

Route::get('bbs', 'UserController@bbsPing');
Route::get('bbs/api/uc', 'UserController@bbsUser');
// 不需要登录验证的接口

Route::get('user/login', ['as' => 'login', 'uses' => 'UserController@getLogin']);
Route::get('user/create', ['as' => 'create', 'uses' => 'UserController@getCreate']);
Route::post('user/login', ['as' => 'login', 'uses' => 'UserController@postLogin']);

// 异步admin后台ajax登录
Route::group(['prefix' => 'ajax'], function () {
    Route::get('login', 'UserController@postLoginAjax');
});

// open后台需要权限
Route::get('/open/needPerm', 'UserController@needPerm');

// 判断账户是否存在
Route::group(['prefix' => 'open'], function () {
    Route::get('isExistAcc', 'UserController@isExistsAccAjax');
});

// 测试用
Route::get('/test/test', 'UserController@test');

// 异步open后台ajax登录
Route::group(['prefix' => 'ajax'], function () {
    Route::get('openLogin', 'UserController@postOpenLoginAjax');
});

// 异步open后台ajax注册
Route::group(['prefix' => 'ajax'], function () {
    Route::get('openReg', 'UserController@postOpenRegisterAjax');
});

// 更新某个用户所拥有的权限[admin,open]
Route::group(['prefix' => 'ajax'], function () {
    Route::get('updatePerm', 'UserController@postUpdatePermAjax');
});

// 管理员创建运营用户
Route::group(['prefix' => 'ajax'], function () {
    Route::get('create', 'UserController@postCreateAjax');
});

// 运营用户修改密码
Route::group(['prefix' => 'ajax'], function () {
    Route::get('changePwd', 'UserController@postChangePwd');
});

// 需要登录验证才能操作的接口
Route::group(array('before' => 'auth'), function () {
    Route::get('user/logout', ['as' => 'logout', 'uses' => 'UserController@getLogout']);
    Route::get('user/dashboard', ['as' => 'dashboard', 'uses' => 'UserController@getDashboard']);
});

/*

 * 后台首页测试路由
 */
Route::get('admin/', function () {
    return view('admin/index');
});

/*
 * 后天框架表单测试路由
 */
Route::get('admin/form', function () {
    return view('admin/form');
});

/*
 * dislog弹出框测试路由
 */
Route::get('admin/dialog', function () {
    return view('admin/dialog');
});

// webGame网页相关

Route::get('/clientlogin', 'UserController@clientLogin');

// 用户登录注册界面
Route::get('/web/center', function () {
    return view('web.login');
});

// 注册账号
Route::group(['prefix' => 'web'], function () {
    Route::post('regiser', 'WebController@registerAjax');
});

// 账号登录
Route::group(['prefix' => 'web'], function () {
    Route::post('login', 'WebController@loginAjax');
});

// 判断是否登录
Route::group(['prefix' => 'web'], function () {
    Route::get('isLogin', 'WebController@isLoginAjax');
});

// 判断账户是否存在
Route::group(['prefix' => 'web'], function () {
    Route::get('isExistAcc', 'WebController@isExistsAccAjax');
});

// 修改昵称
Route::group(['prefix' => 'web'], function () {
    Route::get('modifyNick', 'WebController@modifyNickAjax');
});

// 绑定平台账号
Route::group(['prefix' => 'web'], function () {
    Route::get('bindAccount', 'WebController@bindAccountAjax');
});

// 发送手机验证码
Route::group(['prefix' => 'web'], function () {
    Route::get('sendMobileMsg', 'WebController@sendBindMsgAjax');
});

// 绑定手机号码
Route::group(['prefix' => 'web'], function () {
    Route::get('bindMobile', 'WebController@bindMobileAjax');
});

// 解绑手机号码
Route::group(['prefix' => 'web'], function () {
    Route::get('unbindMobile', 'WebController@unBindMobileAjax');
});

// 修改密码
Route::group(['prefix' => 'web'], function () {
    Route::get('modifyPwd', 'WebController@modifyPwdAjax');
});

// 修改图象
Route::group(['prefix' => 'web'], function () {
    Route::get('modifyPic', 'WebController@modifyPicAjax');
});

/*
 **头像上传的接口（可剪切的）
 */
Route::post('/cropSubmit', 'WebController@cropSubmit');
Route::post('/cropUpload', 'WebController@cropUpload');
Route::get('/cropPic', function () {
    return view('crop.index');
});
Route::get('/corpImg', function () {
    return view('web.crop');
});

/*
 * 获取所有系统要求的信息
 */
Route::get('getSystemInfo', 'AdminWebgameController@getSystemInfo');

/*
 * 页游的测试页面
 */
Route::get('webGame/index', 'WebGameTController@index');
Route::get('webGame/list/{tp}', 'WebGameTController@getGameList');
Route::post('webGame/list/{tp}', 'WebGameTController@getGameList');

Route::get('webGame/getGameInfo', 'WebGameTController@getGameInfo');
Route::get('webGame/getGameType', 'WebGameTController@getGameType');

Route::get('webGame/detail', function () {
    return view('webgame/detail');
});

Route::get('webGame/cardPackCenter', function () {
    return view('webgame/cardPackCenter');
});

//packageReceive
Route::get('webGame/packageReceive', function () {
    return view('webgame/packageReceive');
});

Route::get('webGame/myPackage', function () {
    return view('webgame/myPackage');
});
/*
 * cookie操作
 */

Route::get('setLoginCookie', 'WebgameController@setLoginCookie');

Route::get('setclient', 'WebgameController@setClientCookie');

Route::get('loginout', 'WebgameController@delLoginCookie');

Route::get('getCookie', 'WebgameController@getCookie');

Route::get('getGift', 'WebgameController@getGift');

Route::get('addGift', 'WebgameController@addGiftCode');

Route::get('test', "GameController@test");

//礼包列表页
Route::get('giftList', 'WebgameController@giftList');

Route::get('getIndex', 'WebgameController@getGameLog');
Route::post('getGiftCode', 'WebgameController@getGiftCode');

Route::get('giftInfo', 'WebgameController@getGiftInfo');

//我的礼包页
Route::get('getMyPackage', 'WebgameController@getMyPackage');
Route::get('getMyPackage1', 'WebgameController@getMyPackage1');

//领取礼包页
Route::get('packageReceive/{appId}/', 'WebgameController@packageReceive');

//游戏详情首页
Route::get('getGameDetail/{appId}', 'WebgameController@getGameDetail');

//常见问题
Route::get('qa', function () {
    return view("web.question");
});

//游戏内页充值页面
Route::get('minipay', 'PayController@minipay');

//API：获取游戏区服
Route::get('getServers', 'WebGameTController@getServers');

/*
 * 游戏评论相关路由
 */
Route::get('ctest', 'gameCommentController@test');
/*
 * 添加评论路由
 */
Route::get('comment/add', 'gameCommentController@addComment');
Route::post('comment/add', 'gameCommentController@addComment');
/*
 * 添加支持或不支持路由
 */
Route::get('comment/addSupport', 'gameCommentController@support');
Route::post('comment/addSupport', 'gameCommentController@support');

/*
 * 获取评论的路由
 */
Route::get('comment/getComment', 'gameCommentController@getCommentByGid');
Route::post('comment/getComment', 'gameCommentController@getCommentByGid');

/*
 * 获取评论的数
 */
Route::get('comment/getCommentCount', 'gameCommentController@getCommentCount');
Route::post('comment/getCommentCount', 'gameCommentController@getCommentCount');
/*
 * 删除评论的路由
 */
Route::get('comment/delComment', 'gameCommentController@delComment');
Route::post('comment/delComment', 'gameCommentController@delComment');

/*
 * VR视频的点赞和踩路由
 */
Route::get('video/addSupport', 'VideoController@setVrClickEvent');
Route::post('video/addSupport', 'VideoController@setVrClickEvent');

/*
 * 获取用户的VR视频的点赞和踩值
 */
Route::get('video/getStatus', 'VideoController@getVrClick');
Route::post('video/getStatus', 'VideoController@getVrClick');

/*
 * 用户视频的历史记录相关路由
 */
Route::get('video/addHistory', 'VideoController@addHistory');
Route::post('video/addHistory', 'VideoController@addHistory');

/**
 * VR内的视频分类列表
 */
Route::get('video/halllist', 'VideoController@videoHallList');

/**
 * 获取分类列表信息
 */
Route::get('video/vlist', 'VideoController@videoList');
Route::post('video/vlist', 'VideoController@videoList');

/**
 * 获取分类列表信息
 */
Route::get('video/pagelist', 'VideoController@videoPageList');
Route::post('video/pagelist', 'VideoController@videoPageList');

/**
 * VR内部的历史记录数据
 */
Route::get('video/userhistory', 'VideoController@videoVrHistory');
Route::post('video/userhistory', 'VideoController@videoVrHistory');

Route::get('/test', 'RecommendController@test')->middleware("datecenter:login");
Route::post('/test', 'RecommendController@test')->middleware("datecenter:login");

/**
 * 供flash加载iframe的登入框
 */
Route::get('/flash/login', 'WebsiteController@flashLogin');
Route::post('/flash/api/register', 'UserController@apiRegister4Flash');
Route::post('/flash/api/login', 'UserController@apiLogin4Flash');

Route::get('user_agreement', function () {
    return view('website/user_agreement');
});

// 发送手机验证码
Route::get('web/sendMobileMsgOpen', 'WebController@sendBindMsgOpenAjax');
Route::get('web/bindMobileOpen', 'WebController@bindMobileOpenAjax');

/**
 * 官网路由
 */

Route::group(['domain' => 'partner.vronline.com'], function () {

    /**
     * 登录
     */
    Route::get('/login', 'UserController@webLogin');

    /**
     * 登录
     */
    Route::get('/', function () {
        return redirect("//www.vronline.com/", 302, [], true);
    });

});
