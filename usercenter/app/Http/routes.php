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

/**
 * passport.vronline.com
 */
Route::group(['domain' => 'passport.vronline.com'], function () {
    /**
     * 获取用户信息
     * 必须要有appid、openkey、uid字段
     */
    Route::post('/user', 'UserController@index');
    Route::get('/test', 'Auth\AuthController@test');

    Route::get('/test1', 'UserController@test1');
    Route::get('/imgcode/{token}', 'UserController@showImgCode');
    Route::get('/getImgCode/{token}', 'UserController@getImgCode');
    Route::any('/checkImgCode', 'UserController@checkImgCode');

    /**
     * 获取用户信息，用于后台查询
     * 必须要有appid、openkey、uid字段
     */
    Route::post('/user/id', 'UserController@getUid');

    /**
     * 获取用户信息，用于后台查询
     * 必须要有appid、openkey、uid字段
     */
    Route::post('/user/info', 'UserController@getUserInfo');

    /**
     * 获取用户信息，用于后台查询
     * 必须要有appid、openkey、uid字段
     */
    Route::post('/user/account', 'UserController@getAccountInfo');

    /**
     * 封/解封用户
     * 必须要有appid、openkey、uid字段
     */
    Route::post('/user/disable/{action}', 'UserController@disableUser');

    /**
     * 普通注册
     */

    Route::post('user/register/{type}', 'UserController@register');

    /**
     * 快速注册
     */
    Route::post('/register/guest', 'UserController@guest');

    /**
     * 快速注册
     */
    Route::post('/register/nologin', 'UserController@addNoLoginUser');

    /**
     * 判断用户名是否存在
     */
    Route::post('user/existUsername', 'UserController@existUsername');

    /**
     * 登录
     */
    Route::post('user/login/{type}', 'UserController@login');

    /**
     * 查询登录记录
     */
    Route::post('user/loginRecord', 'UserController@getLoginLog');

    /**
     * 修改t_user_info_0库中某个字段的值
     */
    Route::post('user/updateField', 'UserController@updateField');
    /**
     * 修改密码
     */
    Route::post('user/changePwd', 'UserController@changePwd');

    /**
     * 判断是否登录
     */
    Route::post('/checkLogin', 'UserController@isLogin');

    /**
     * 快速注册后设置账号
     */
    Route::post('/account/set', 'UserController@setAccount');

    /**
     * 快速注册后设置账号
     */
    Route::post('/account/bindthirdaccount', 'UserController@bindThirdAccount');

    // 普通登录注册 RESTful资源控制器
    //Route::get('user/{id}', 'UserController@showProfile');

    //Route::get('user/test/{name}', 'UserController@test');

    // 引导用户到新浪微博的登录授权页面
    Route::get('auth/weibo', 'Auth\AuthController@weibo');
    Route::get('auth/tg/weibo', 'Auth\AuthController@weibo4tg');
    // 用户授权后新浪微博回调的页面
    Route::get('auth/wbCallback', 'Auth\AuthController@wbCallback');

    // 引导用户到qq登录授权页面
    Route::get('auth/qq', 'Auth\AuthController@qq');
    Route::get('auth/tg/qq', 'Auth\AuthController@qq4tg');
    // 用户授权后新浪微博回调的页面
    Route::get('auth/qqCallback', 'Auth\AuthController@qqCallback');

    // 引导用户到wx登录授权页面
    Route::get('auth/wx', 'Auth\AuthController@wx');
    Route::get('auth/tg/wx', 'Auth\AuthController@wx4tg');
    // 用户授权后新浪微博回调的页面
    Route::get('auth/wxCallback', 'Auth\AuthController@wxCallback');

    // 引导用户到3D播播登录授权页面
    Route::get('auth/3Dbobo', 'Auth\AuthController@bobo');
    Route::get('auth/tg/3Dbobo', 'Auth\AuthController@bobo4tg');
    // 用户授权后3D播播回调的页面
    Route::get('auth/boboCallback', 'Auth\AuthController@boboCallback');

    /**
     * 第三方注册、登录
     */
    //Route::post('/login/{type}', 'UserController@thirdLogin');

    /**
     * 第三方注册、登录
     */
    //Route::post('/deny/{type}', 'UserController@thirdLogin');

    /**
     * 绑定手机发送验证码
     */
    Route::post('/sendmsg/bind', 'UserController@sendBindMsg');
    //Route::get('/sendmsg/bind', 'UserController@sendBindMsg');

    /*
     * 解绑手机号
     */
    Route::post('unbind/mobile', 'UserController@unBindMobile');
    //Route::get('unbind/mobile', 'UserController@unBindMobile');

    /**
     * 找回密码发送验证码
     */
    Route::post('/sendmsg/findpwd', 'UserController@sendFindPwdMsg');
    //Route::get('/sendmsg/findpwd', 'UserController@sendFindPwdMsg');

//    Route::get('/authSms', 'UserController@authMsgCode');

    /**
     * 找回密码
     */
    Route::post('/password/find', 'UserController@findPassword');
    //Route::get('/password/find', 'UserController@findPassword');
    /**
     * open后台添加子账号短信验证
     */
    Route::post('/addSonCheckMsg', 'UserController@addSonCheckMsg');
    /**
     * 上传大文件
     */
    Route::post('/face/upload', 'UserController@setFace');
    Route::get('/imgupload', function () {
        return view('uploads.uploads');
    });

    /*
     * 上传头像
     */
    Route::post('/face/uploadFace', 'UserController@uploadFace');

    Route::get('/imgup', function () {
        return view('uploads.upload');
    });
    /*
     **头像上传的接口（可剪切的）
     */
    Route::post('/cropSubmit', 'CropPicController@corpSubmit');
    Route::post('/cropUpload', 'CropPicController@cropUpload');
    Route::get('/cropPic', function () {
        return view('crop.index');
    });
    Route::get('/corpImg', function () {
        return view('crop.crop');
    });

    /**
     * 绑定手机/邮箱
     */
    Route::post('/bind/mobile', 'UserController@bindMobile');

    /**
     * 绑定手机/邮箱
     */
    Route::post('/bind/email', 'UserController@bindEmail');

    Route::get('/sendMail', 'UserController@sendMail');

    /*
     * 获取评论的用户的头像和account信息的路由
     */
    Route::post('/comment/getCommentUserInfo', 'UserController@getCommentUserInfo');

    /**
     * 注册获取验证码
     */
    Route::any('/reg/mobile/getcode', 'UserController@getMobileRegCode');

    /**
     * 注册获取验证码
     */
    Route::any('/reg/mobile/gettoken', 'UserController@getMobileRegToken');

    /**
     * 注册获取验证码
     */
    Route::any('/reg/mobile/reg', 'UserController@registerByMobile');

    /**
     * 获取IMtoken
     */
    Route::post('/imtokoen', 'UserController@getImToken');

    /**
     * 查询是否有设置取款密码
     */
    Route::post('/tob/hasPaypwd', 'ToBCheckBillController@hasPaypwd');

    /**
     * 设置取款密码
     */
    Route::post('/tob/setPaypwd', 'ToBCheckBillController@setPaypwd');

    /**
     * 取款密码登录
     */
    Route::post('/tob/loginPaypwd', 'ToBCheckBillController@loginPaypwd');

    /**
     * 查询余额
     */
    Route::post('/tob/get2bBalance', 'ToBCheckBillController@get2bBalance');

    /**
     * 获取银行卡列表
     */
    Route::post('/tob/get2bCards', 'ToBCheckBillController@get2bCards');
    /**
     * 获取银行卡信息
     */
    Route::post('/tob/get2bCard', 'ToBCheckBillController@get2bCard');
    /**
     * 保存银行卡信息
     */
    Route::post('/tob/save2bCard', 'ToBCheckBillController@save2bCard');

    /**
     * 检查银行卡信息
     */
    Route::post('/tob/check2bCard', 'ToBCheckBillController@check2bCard');

    /**
     * 删除银行卡信息
     */
    Route::post('/tob/del2bCard', 'ToBCheckBillController@del2bCard');

    /**
     * 设置默认银行卡
     */
    Route::post('/tob/default2bCard', 'ToBCheckBillController@default2bCard');

    /**
     * 提取银行卡信息
     */
    Route::post('/tob/extractBankCash', 'ToBCheckBillController@extractBankCash');

});

/**
 * api.vronline.com
 */
Route::group(['domain' => 'api.vronline.com'], function () {
    /**
     * 判断登录状态
     */
    Route::get('/islogin', 'ApiController@isLogin');

    /**
     * 根据code获取用户信息
     */
    Route::any('/access_token', 'ApiController@getAccessToken');

    /**
     * 根据access_token获取用户信息
     */
    Route::any('/uinfo', 'ApiController@getUserInfoByAccessToken');

    /**
     * 取现记录
     */
    Route::get('/2b/extractCashLog', 'ToBCheckBillController@extractCashLog');

    /**
     * 取现审核
     */
    Route::get('/2b/extractCashConfirm', 'ToBCheckBillController@extractCashConfirm');

    /**
     * 设置app信息
     */
    /*Route::get('/user', 'ApiController@user');*/
});

/**
 * appinfo.vronline.com
 */
Route::group(['domain' => 'appinfo.vronline.com'], function () {
    /**
     * 获取app信息
     */
    Route::get('/get/{appid}', 'AppinfoController@index');

    /**
     * 设置app信息
     */
    Route::post('/set/{appid}', 'AppinfoController@setApp');

    /**
     * 获取一个服务器信息
     */
    Route::get('/server/get/{appid}/{serverid}', 'AppinfoController@getOneServer');

    /**
     * 设置一个服务器信息
     */
    Route::post('/server/set/{appid}/{serverid}', 'AppinfoController@setOneServer');
});

/**
 * pay.vronline.com
 */
Route::group(['domain' => 'pay.vronline.com'], function () {
    /**
     * 生成paytoken
     */
    Route::post('/paytoken', 'PayController@getPayToken');

    /**
     * 生成paytoken
     */
    Route::post('/paytoken2self', 'PayController@getPayTokenSelf');

    /**
     * 充值平台币
     */
    Route::post('/create/buyplantb', 'PayController@buyPlantb');

    /**
     * 充值游戏
     */
    Route::post('/create/buygame', 'PayController@buyGame');

    /**
     * 平台币直接充值游戏
     */
    Route::post('/buy/buygame', 'PayController@buyGameByPlantb');

    /**
     * 创建2B版本订单
     */
    Route::post('/create/create2bOrder', 'PayController@create2bOrder');

    /**
     * 支付结果
     */
    Route::get('/result2b/{orderid}', 'PayController@payresult2b');

    /**
     * 创建2B版本退款订单
     */
    Route::post('/create/create2bRefundOrder', 'PayController@create2bRefundOrder');

    /**
     * 充值结果
     */
    Route::get('/result/{from}/{orderid}', 'PayController@payresult');

    /**
     * 2b版本补发通知
     */
    Route::get('/repeat2b', 'PayController@repeat2b');

    /**
     * 取现扣余额
     */
    Route::post('/cashSubbalance', 'PayController@cashSubbalance');

    /**
     * 取现付款
     */
    Route::get('/create/create2bCashOrder', 'PayController@create2bCashOrder');

    /**
     * 充值回调
     */
    Route::get('/callback', 'PayController@callBack');

    /**
     * 充值回调
     */
    Route::get('/callback2b', 'PayController@callBack2b');

    /**
     * 充值回调
     */
    Route::get('/callBackRefund', 'PayController@callBackRefund');

    /**
     * 取现付款回调
     */
    Route::get('/callBackCash', 'PayController@callBackCash');

    /**
     * 支付结果api
     */
    Route::get('/resultapi/{orderid}', 'PayController@payResultApi');
    /**
     * 用户订单记录
     */
    Route::get('/userOrders', 'PayController@userOrders');
    /**
     * 发货回调
     */
    Route::get('/delivery', function () {
        sleep(1);
        return "success";
    });
});

/**
 * paydev.vronline.com
 */
Route::group(['domain' => 'callback.vronline.com'], function () {

    /**
     * 充值回调
     */
    Route::get('/callback', 'PayController@callBack');

    /**
     * 充值回调
     */
    Route::get('/callback2b', 'PayController@callBack2b');

    /**
     * 充值回调
     */
    Route::get('/callBackRefund', 'PayController@callBackRefund');

    /**
     * 取现付款回调
     */
    Route::get('/callBackCash', 'PayController@callBackCash');

});

/**
 * paydev.vronline.com
 */
Route::group(['domain' => 'payol.vronline.com'], function () {

    /**
     * 充值回调
     */
    Route::get('/callback', 'PayController@callBack');

    /**
     * 充值回调
     */
    Route::get('/callback2b', 'PayController@callBack2b');

    /**
     * 充值回调
     */
    Route::get('/callBackRefund', 'PayController@callBackRefund');

    /**
     * 取现付款回调
     */
    Route::get('/callBackCash', 'PayController@callBackCash');

});

/**
 * paydev.vronline.com
 */
Route::group(['domain' => 'test3.vronline.com'], function () {

    /**
     * 充值回调
     */
    Route::get('/callback2b', 'PayController@callBack2b');
    Route::any('/create/create2bOrder', 'PayController@create2bOrder');
    Route::get('/result2b/{orderid}', 'PayController@payresult2b');
    Route::get('/repeat2b', 'PayController@repeat2b');

});

/**
 * paydev.vronline.com
 */
Route::group(['domain' => 'payres.vronline.com'], function () {

    /**
     * 充值结果
     */
    Route::get('/result/{from}/{orderid}', 'PayController@payresult');

    /**
     * 充值结果
     */
    Route::get('/result2b/{orderid}', 'PayController@payresult2b');

    Route::get('/checkbill', 'ToBCheckBillController@getHeepayCheckBill');
});

/**
 * paydev.vronline.com
 */
Route::group(['domain' => 'test3.xyzs.com'], function () {
    /**
     * 充值回调
     */
    Route::get('/callback', 'PayController@callBack');
    Route::get('/callback2b', 'PayController@callBack2b');
    Route::get('/callBackRefund', 'PayController@callBackRefund');
    Route::get('/callBackCash', 'PayController@callBackCash');

    /**
     * 充值回调
     */
    Route::get('/delivery', function () {
        sleep(1);
        return "success";
    });

});

/**
 * test.vronline.com
 */
Route::group(['domain' => 'openapi.vronline.com'], function () {
    /**
     * 判断是否登录
     */
    Route::post('/islogin', 'ApiController@isLogin');

    /**
     * 生成openid
     */
    Route::post('/openid', 'ApiController@getOpenid');

    /**
     * 用户资料
     */
    Route::post('/user', 'ApiController@user');

});
