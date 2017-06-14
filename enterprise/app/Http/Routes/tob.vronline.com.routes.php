<?php
Route::group(['domain' => 'tob.vronline.com'], function () {

    Route::post('/login/master', 'UserController@masterLogin');

    Route::post('/terminal/active', 'ToBStoreController@terminalActive');
    Route::get('/index/{merchantid}/{terminal_sn}', 'ToBStoreController@index');

    Route::post('/search/{merchantid}/{terminal_sn}', 'ToBStoreController@search');
    Route::get('/terminal/check', 'ToBStoreController@checkTerminal');
    Route::get('/update/paypwd', 'ToBStoreController@changePayPwd');

    Route::get('/pay/product/{merchantid}/{terminal_sn}/{appid}', 'ToBStoreController@products');
    Route::get('/pay/productinfo/{merchantid}/{terminal_sn}/{productid}', 'ToBStoreController@productInfo');
    Route::post('/pay/swapterminalinfo/{merchantid}/{terminal_sn}/{appid}', 'ToBStoreController@swapTerminalInfo'); //更换机器信息
    Route::post('/pay/swapterminal/{merchantid}/{terminal_sn}/{appid}/{orderid}', 'ToBStoreController@swapTerminal'); //更换机器
    Route::post('/play/add/{merchantid}/{appid}', 'ToBStoreController@addPlay'); //更换机器

    /**
     * 平台账号申请体验店账号
     */
    Route::get("/login", "ToBController@login");
    Route::get("/", "ToBController@index");
    Route::get("/enter", "ToBController@enter");
    Route::post("/apply", "ToBController@apply");

    Route::get("/validateEmail", "ToBController@validateEmail");
    Route::post("/sendEmail", "ToBController@sendEmail");
    Route::get("/activeEmail", "ToBController@activeEmail");

    Route::post("/submit", "ToBController@submit");
    Route::get("/wait", "ToBController@wait");

    /**
     * admin for merchants
     */
    Route::any('/admin/login', 'ToBStoreAdminController@setTobCookie');
    Route::post("/admin/search", "ToBStoreAdminController@allGame");
    Route::post("/admin/game", "ToBStoreAdminController@myGame");
    Route::post("/admin/game/other", "ToBStoreAdminController@otherGame");
    Route::post("/admin/game/bysort", "ToBStoreAdminController@getGameBySort");
    Route::get("/admin/game/category", "ToBStoreAdminController@gameCategory");
    Route::post("/admin/game/buy", "ToBStoreAdminController@buyGame");
    Route::post("/admin/game/add", "ToBStoreAdminController@addTerminalGame");
    Route::post("/admin/game/add/games", "ToBStoreAdminController@addGamesForTerminals");
    Route::post("/admin/game/del", "ToBStoreAdminController@delGame");
    Route::post("/admin/product/add", "ToBStoreAdminController@addProduct");
    Route::post("/admin/product/edit", "ToBStoreAdminController@editProduct");
    Route::post("/admin/product/modify", "ToBStoreAdminController@modifySells");
    Route::post("/admin/product/default", "ToBStoreAdminController@setDefaultProduct");
    Route::post("/admin/product/del", "ToBStoreAdminController@delProduct");
    Route::post('/admin/terminallist', 'ToBStoreAdminController@terminalList');
    Route::post('/admin/terminallist/simple', 'ToBStoreAdminController@terminalListSimple');
    Route::post('/admin/gameDetailList', 'ToBStoreAdminController@gameDetailList');
    Route::post("/admin/game/list", "ToBStoreAdminController@gameList");
    Route::post("/admin/terminalmsg", "ToBStoreAdminController@sendTerminalMsg");
    Route::post("/admin/record", "ToBStoreAdminController@transactionRecord"); //交易记录
    Route::post("/admin/extractCashLog", "ToBStoreAdminController@extractCashLog"); //提现记录
    Route::post("/admin/phone", "ToBStoreAdminController@getMerchantPhone"); //获取用户绑定手机号
    Route::post("/admin/getPayPwdInfo", "ToBStoreAdminController@getPayPwdInfo"); //检查密码状态
    Route::post("/admin/getPayPwdCode", "ToBStoreAdminController@getPayPwdCode"); //设置交易密码时获取验证码
    Route::post("/admin/setpaypwd", "ToBStoreAdminController@setPaypwd"); //设置交易密码
    Route::post("/admin/getbillbydate", "ToBStoreAdminController@get2bBileByDate"); //分成收入的账单

    Route::post("/admin/getBankCards", "ToBStoreAdminController@getBankCards"); //查看银行卡
    Route::get("/admin/getBankCitys", "ToBStoreAdminController@getBankCitys"); //银行可选城市
    Route::post("/admin/getBankCard", "ToBStoreAdminController@getBankCard"); //查看银行卡信息
    Route::post("/admin/addCard", "ToBStoreAdminController@addCard"); //添加银行卡
    Route::post("/admin/defaultCard", "ToBStoreAdminController@defaultCard"); //设置默认银行卡
    Route::post("/admin/checkBankCard", "ToBStoreAdminController@checkBankCard"); //检查银行卡
    Route::post("/admin/delBankCard", "ToBStoreAdminController@delBankCard"); //检查银行卡

    Route::post("/admin/extractCashCode", "ToBStoreAdminController@extractCashCode"); //提现
    Route::post("/admin/extractCash", "ToBStoreAdminController@extractCash"); //提现

    /**
     * 购买发货启动游戏
     */
    Route::get('/payfor2bterminal', 'ToBStoreController@payFor2bTerminal');

    /**
     * im登录
     */
    Route::post('/imlogin', 'ToBStoreController@getImToken2b');

    /**
     * 获取对账订单
     */
    Route::get('/getOrder4Check', 'ToBStoreController@getOrder4Check');
    Route::get('/getOrderTotal4Check', 'ToBStoreController@getOrderTotal4Check');
    Route::get('/getMerchantIncome', 'ToBStoreController@getMerchantIncome');
    Route::post('/addRefundOrder', 'ToBStoreController@addRefundOrder');
    Route::post('/confirmRefundOrder', 'ToBStoreController@confirmRefundOrder');
    Route::post('/addDayBill', 'ToBStoreController@addDayBill');

    /**
     * 游戏启动统计
     */
    Route::post('/stat/appStart2bStat', 'ToBStoreController@appStart2bStat');

    /**
     * 获取商家数据
     */
    Route::post('/getDayAppDate', 'ToBStoreAdminController@getDataByAll');
});
