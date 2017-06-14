<?php
/**
 * admin后台路由
 */
Route::group(['domain' => 'admincp.vronline.com'], function () {
    Route::get('/upload/imgCosAppSign', 'UploadController@adminCosAppSign');

    Route::get('/', 'Admincp\IndexController@index');
    Route::get('/index', 'Admincp\IndexController@index');
    Route::get('/index/help', 'Admincp\IndexController@help');
    Route::get('/service', 'Admincp\ServiceController@index');
    Route::get('/sys', 'Admincp\SysController@index');
    Route::get('/login', 'Admincp\SysController@login');
    Route::get('/loginOut', 'Admincp\SysController@loginOut');
    Route::get('/setpwd', 'Admincp\SysController@setpwd');
    Route::get('/forget', 'Admincp\SysController@forget');
    Route::post('sys/sendMail', 'Admincp\SysController@sendMail');
    Route::post('sys/resetPwd', 'Admincp\SysController@resetPwd');
    Route::post('/sys/loginSubmit', 'Admincp\SysController@loginSubmit');

    Route::get('/audit', 'Admincp\AuditController@index');
    Route::get('/audit/index', 'Admincp\AuditController@index');
    Route::get('/audit/user', 'Admincp\AuditController@user');
    Route::get('/audit/vrgame', 'Admincp\AuditController@vrgame');
    Route::get('/audit/webgame', 'Admincp\AuditController@webgame');
    Route::get('/audit/video', 'Admincp\AuditController@video');
    Route::get('/audit/news', 'Admincp\AuditController@news');

    Route::get('/vrhelp', 'Admincp\VrhelpController@index');
    Route::get('/vrhelp/video', 'Admincp\VrhelpController@video');
    Route::post('/vrhelp/transcoding', 'Admincp\VrhelpController@transcoding');
    Route::post('/vrhelp/transcoding/stat', 'Admincp\VrhelpController@transcodingStat');

    Route::get('/vrhelp/developer', 'Admincp\VrhelpController@developer');

    Route::get('/vrhelp/vrgame', 'Admincp\VrhelpController@vrgame');
    Route::get('/vrhelp/vrgameEdit/{id}', 'Admincp\VrhelpController@vrgameEdit');
    Route::get('/vrhelp/vrgame/version/{appid}', 'Admincp\VrhelpController@vrgameVersion');
    Route::post('/vrhelp/vrgame/subversions', 'Admincp\VrhelpController@vrgameSubVersion');

    Route::get('/vrhelp/webgame', 'Admincp\VrhelpController@webgame');
    Route::get('/vrhelp/webgame/news/{appid}', 'Admincp\VrhelpController@webgameNews');
    Route::get('/vrhelp/top', 'Admincp\VrhelpController@top');
    Route::get('/vrhelp/position', 'Admincp\VrhelpController@position');
    Route::get('/vrhelp/search', 'Admincp\VrhelpController@search');
    Route::post('/vrhelp/switchWeight', 'Admincp\VrhelpController@switchWeight');
    Route::post('/vrhelp/rec/save', 'Admincp\VrhelpController@dataRecSave');
    Route::post('/vrhelp/rec/del', 'Admincp\VrhelpController@dataDelRec');
    Route::get('/vrhelp/client', 'Admincp\VrhelpController@client');
    Route::get('/vrhelp/clientup', 'Admincp\VrhelpController@clientup');
    Route::get('/vrhelp/price', 'Admincp\VrhelpController@price');
    Route::get('/vrhelp/cdk', 'Admincp\VrhelpController@cdk');
    Route::get('/vrhelp/dbb', 'Admincp\VrhelpController@threedbb');
    Route::get('/vrhelp/cdkDown', 'Admincp\VrhelpController@cdkDown');
    Route::get('/vrhelp/dbbinfo', 'Admincp\VrhelpController@dbbinfo');

    Route::get('/vronline', 'Admincp\VronlineController@index');
    Route::get('/vronline/news', 'Admincp\VronlineController@news');
    Route::get('/vronline/newsEdit/{id}', 'Admincp\VronlineController@newsEdit');
    Route::get('/vronline/pc', 'Admincp\VronlineController@pc');
    Route::get('/vronline/pcEdit/{id}', 'Admincp\VronlineController@pcEdit');
    Route::get('/news/top', 'Admincp\NewsController@top');
    Route::get('/vronline/search', 'Admincp\VronlineController@search');
    Route::get('/vronline/video', 'Admincp\VronlineController@video');
    Route::get('/vronline/game', 'Admincp\VronlineController@game');
    Route::get('/vronline/top', 'Admincp\VronlineController@top');
    Route::get('/vronline/position', 'Admincp\VronlineController@position');
    Route::post('/vronline/switchWeight', 'Admincp\VronlineController@switchWeight');

    Route::get('/vronline/comments', 'Admincp\NewsController@comments');
    Route::post('/vronline/game/getgameimg', 'Admincp\VronlineController@getGameImg');
    Route::post('/vronline/game/addgameimg', 'Admincp\VronlineController@addGameImg');
    Route::post('/vronline/game/delgameimg', 'Admincp\VronlineController@delGameImg');

    Route::get('/news', 'Admincp\NewsController@index');
    Route::get('/news/article', 'Admincp\NewsController@article');
    Route::get('/news/articlePreview/{id}', 'Admincp\NewsController@articlePreview');
    Route::get('/news/articleEdit/{id}', 'Admincp\NewsController@articleEdit');
    Route::get('/news/top', 'Admincp\NewsController@top');
    Route::get('/news/position', 'Admincp\NewsController@position');
    Route::get('/news/search', 'Admincp\NewsController@search');
    Route::post('/news/top/switchWeight', 'Admincp\NewsController@switchWeight');
    Route::post('/news/localimg', 'Admincp\NewsController@localImg');

    Route::get('/service', 'Admincp\ServiceController@index');
    Route::get('/service/feedback', 'Admincp\ServiceController@feedback');
    Route::get('/service/feedbackInfo/{id}', 'Admincp\ServiceController@feedbackInfo');
    Route::post('/service/feedback_tps', 'Admincp\ServiceController@feedbackTps');
    Route::post('/service/feedbackDel', 'Admincp\ServiceController@feedbackDel');

    Route::get('/service/qa', 'Admincp\ServiceController@qa');

    Route::get('/stat', 'Admincp\DateCenterController@index');
    Route::get('/stat/index', 'Admincp\DateCenterController@allData');
    Route::get('/stat/vrgame', 'Admincp\DateCenterController@vrGameData');
    Route::get('/stat/vrvideo', 'Admincp\DateCenterController@vrVideoData');
    Route::get('/stat/dbbreginfo', 'Admincp\VrhelpController@dbbRegInfo');

    Route::get('/sys', 'Admincp\SysController@index');
    Route::get('/sys/user', 'Admincp\SysController@user');
    Route::get('/sys/group', 'Admincp\SysController@group');
    Route::get('/sys/top', 'Admincp\SysController@top');
    Route::post('/sys/top/save', 'Admincp\SysController@dataSaveTop');
    Route::post('/sys/top/del', 'Admincp\SysController@dataDelTop');

    Route::get('/tob', 'Admincp\ToBController@index');
    Route::get('/tob/merchants', 'Admincp\ToBController@merchants');
    Route::get('/tob/defaultgame', 'Admincp\ToBController@defaultGame');
    Route::get('/tob/other', 'Admincp\ToBController@other');
    Route::get('/tob/banner', 'Admincp\ToBController@banner');
    Route::post('/tob/banner/switchWeight', 'Admincp\ToBController@switchBannerWeight');
    Route::get('/tob/confirm', 'Admincp\ToBController@extractCashConfirm');
    Route::post('/tob/confirm', 'Admincp\ToBController@extractCash');
    Route::post('/tob/payextract', 'Admincp\ToBController@payExtract');

    Route::post('/json/edit', 'Admincp\JsonController@edit');
    Route::post('/json/save/{name}', 'Admincp\JsonController@save');
    Route::get('/json/save/{name}', 'Admincp\JsonController@save2');
    Route::post('/json/del/{name}', 'Admincp\JsonController@del');
    Route::post('/json/pass/{name}', 'Admincp\JsonController@pass');

    Route::post('/json/update/product_client', 'Admincp\JsonController@updateVersionStatus');

    //读取数据库中的最新版本和稳定版本的数据
    Route::post('client/databasePublic', 'Admincp\JsonController@databasePublic');
    //发布客户端版本=>缓存中
    Route::post('client/versionPublic', 'Admincp\JsonController@versionPublic');
    //获取的发布客户端版本信息=>缓存中
    Route::get('client/alreadyPublic', 'Admincp\JsonController@alreadyPublic');
    //更新在线更新版本的状态
    Route::post('/json/update/online_client', 'Admincp\JsonController@updateOnlineStatus');
    //读取数据库中的发布版本的信息
    Route::post('client/databaseUpPublic', 'Admincp\JsonController@databaseUpPublic');
    //发布客户端版本=>缓存中
    Route::post('client/versionUpPublic', 'Admincp\JsonController@setUpOnlineCache');

    Route::get('/data/devusers', 'Admincp\DataController@devUsers');
});
