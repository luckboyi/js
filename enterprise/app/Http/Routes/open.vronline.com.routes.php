<?php
/**
 * open.vronline.com
 */
Route::group(['domain' => 'open.vronline.com'], function () {

    Route::any('/wechat/event', 'Wechat\ServerController@tickEvent');

    Route::get('/', 'Developer\DeveloperController@index');
    Route::get('/login', 'Developer\DeveloperController@index');
    Route::get('/developer/sign', 'Developer\DeveloperController@sign');
    Route::get('/developer/sign/fill', 'Developer\DeveloperController@signFill');
    Route::get('/developer/sign/fill/{tp}', 'Developer\DeveloperController@signFill');
    Route::get('/developer/sign/email', 'Developer\DeveloperController@signEmail');
    Route::get('/developer/sign/active', 'Developer\DeveloperController@activeEmail');
    Route::get('/developer/sign/wait', 'Developer\DeveloperController@signWait');
    Route::get('/developer/sign/reject', 'Developer\DeveloperController@signReject');
    Route::get('/developer/sign/success', 'Developer\DeveloperController@signSuccess');

    Route::get('/developer/vrgame', 'Developer\DeveloperController@vrGame');
    Route::get('/developer/user', 'Developer\DeveloperController@user');
    Route::get('/developer/setting', 'Developer\DeveloperController@setting');
    Route::get('/developer/vrgame/detail/{id}', 'Developer\DeveloperController@vrGameDetail');
    Route::get('/developer/vrgame/agreement/{id}', 'Developer\DeveloperController@vrGameAgreement');
    Route::get('/developer/vrgame/copyright/{id}', 'Developer\DeveloperController@vrGameCopyright');
    Route::get('/developer/vrgame/version/{id}', 'Developer\DeveloperController@vrGameVersion');

    Route::post('/json/edit', 'Developer\JsonController@edit');
    Route::post('/json/save/{name}', 'Developer\JsonController@save');
    Route::post('/json/del/{name}', 'Developer\JsonController@del');
    Route::post('/json/submit/{name}', 'Developer\JsonController@submit');

    Route::get('/upload/imgCosAppSign', 'UploadController@imgCosAppSign');
    Route::post('/upload/start', 'UploadController@uploadPrivate');
    Route::get('/private/{path}', 'UploadController@imagePrivate');

    Route::post('/boboLogin', 'UserController@boboLogin');
    Route::get('/agreement', 'OpenController@openAgreement');
    Route::any('/mytest', 'TestController@test');

//    Route::get('login', 'UserController@openLogin');
    Route::get('logout', 'UserController@loginOut');

    Route::get('register', 'UserController@webRegister');
    Route::get('forgetpwd', 'UserController@forgetPwd');

    // 查询admin或open所拥有的全部功能列表
    Route::group(['prefix' => 'ajax'], function () {
        Route::get('getMenu', 'UserController@postMenuAjax');
    });

    Route::group(['prefix' => 'ajax'], function () {
        Route::get('sendFindCode', 'UserController@sendFindPwdMsg');
        Route::get('resetPwd', 'UserController@findPassword');
    });

    /**
     * webGame列表
     */
    Route::get('/product/webgamelist/{tp}', 'OpenAppController@webGame');
    /**
     * webGame创建
     */
    Route::get('/product/webgame/create', 'OpenAppController@webGameCreate');
    /**
     * webGame创建提交
     */
    Route::post('/product/webgame/submit', 'OpenAppController@webGameCreateSubmit');

    /**
     * webGame 详细信息
     */
    Route::get('/product/webgame/{tp}/{appid}', 'OpenAppController@webGameDetail');
    /**
     * webGame创建提交
     */
    Route::post('/product/webgame/save/{tp}/{appid}', 'OpenAppController@webGameSave');
    /**
     * webGame服务器保存
     */
    Route::post('/product/webgame/serversave/{appid}', 'OpenAppController@webGameServerSave');

    /**
     * webGame提交审核
     */
    Route::post('/product/webgame/review/{appid}', 'OpenAppController@webgameReview');
    /**
     * webGame发布
     */
    Route::post('/product/webgame/publish/{appid}', 'OpenAppController@webGamePublish');

    /**
     * vrGame 列表
     */
    Route::get('/product/vrgamelist/{tp}', 'OpenVRAppController@vrGame');
    /**
     * vrGame 创建
     */
    Route::get('/product/vrgame/create', 'OpenVRAppController@vrGameCreate');
    /**
     * vrGame 创建提交
     */
    Route::post('/product/vrgame/submit', 'OpenVRAppController@vrGameCreateSubmit');
    /**
     * vrGame 详细信息
     */
    Route::get('/product/vrgame/{tp}/{appid}', 'OpenVRAppController@vrGameDetail');
    /**
     * vrGame保存提交
     */
    Route::post('/product/vrgame/save/{tp}/{appid}', 'OpenVRAppController@vrGameSave');

    /**
     * 要审核的用户列表
     */
    Route::get('/review/user', 'OpenController@userNeedReview');

    /**
     * 查看审核未审核的页面
     */
    Route::get('/review/regUser/{reviewStat}', 'OpenController@userReview');

    /**
     * 审核的用户信息
     */
    Route::get('/review/user/info/{target_uid}', 'OpenController@reviewUserInfo');

    /**
     * 审核用户
     */
    Route::post('/review/user/{target_uid}', 'OpenController@reviewUser');

    /**
     * 要审核的游戏列表
     */
    Route::get('/review/{gametype}/{reviewStat}', 'OpenController@appReviewList');

    /**
     * 要审核的游戏信息
     * type:base 游戏基本信息; right: 版权信息;
     */
    Route::get('/review/{gametype}/info/{type}/{appid}', 'OpenController@reviewAppInfo');

    /**
     * 审核游戏
     */
    Route::post('/review/webgame/{appid}', 'OpenController@goReviewApp');

    /*
     * 开放平台开发者注册的信息提交
     */

    Route::post('/applyUserInfo/{action}', 'OpenController@applyUserInfo');

    /*
     * 邮箱激活的操作
     */
    Route::post('AuthEmail', 'OpenController@jumpAuthEmail');
    /*
     * 重新发送验证邮件
     */
    Route::post('resendActiveEmail', 'OpenController@resendActiveEmail');
    /*
     * 注册的第二部操作，邮箱验证界面
     */
    Route::get('activeEmail', 'OpenController@authActiveEmail');

    /*
     * 注册成功页面
     */
    Route::get('open/userRegSuccess', function () {
        return view('open/userRegSuccess');
    });

    /*
     * 开发者申请入口页面
     */
    Route::get('applyHome', 'OpenController@applyHome');

    /*
     * 开发者注册页面（区分个人和公司）
     */
    Route::get('userApply/{action}', 'OpenController@userApply');

    Route::post('apply/goReviewUser', 'OpenController@goReviewUser');

    /*
     * 开发者详细资料页
     */
    Route::get('getDeveloperInfo', 'OpenController@getDeveloperInfo');

    /*
     * 开发者信息编辑页
     */
    Route::get('updateDeveloperInfo', 'OpenController@updateDeveloperInfo');

    /*
     * 更新用户接口
     */
    Route::post('updateUserInfo', 'OpenController@updateUserInfo');

    Route::get('subAccount', 'OpenAppController@subAccountList');
    Route::get('subAccount/edit', 'OpenAppController@subAccountEdit');
    Route::get('subAccount/perm', 'OpenAppController@subAccountPerm');

    //添加子账号
    Route::post('checkAccount', 'OpenController@addSonAccountCheck');
    Route::post('addSonSendMsg', 'OpenController@addSonSendMsg');
    Route::post('addAccount', 'OpenController@addSonAccount');
    Route::post('delAccount', 'OpenController@delSonAccount');
    Route::post('addperms', 'OpenController@addSonPerms');
    Route::post('getSonPerms', 'OpenController@getSonPerms');

    //vrgame版本管理
    Route::any('/dev/devUserLogin', 'UserController@devUserLogin'); //开发者工具登录
    Route::any('/dev/versionInfo', 'VersionController@versionInfo'); //获取游戏版本信息
    Route::any('/dev/addVersion', 'VersionController@addVersion');
    Route::any('/dev/addSubVersion', 'VersionController@addSubVersion');
    Route::any('/dev/completeSubVersion', 'VersionController@completeSubVersion');
    Route::any('/dev/chooseSubVersion', 'VersionController@chooseSubVersion');
    Route::any('/dev/publishVersion', 'VersionController@publishVersion');
    Route::any('/dev/getUploadToken', 'VersionController@getUploadToken');

});
