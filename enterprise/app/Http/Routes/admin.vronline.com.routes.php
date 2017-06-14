<?php
/**
 * admin后台路由
 */
Route::group(['domain' => 'admin.vronline.com'], function () {

    Route::get('/', ['as' => 'user.login', 'uses' => 'UserController@getLogin']);

    // 查询admin或open所拥有的全部功能列表
    Route::group(['prefix' => 'ajax'], function () {
        Route::get('getMenu', 'UserController@postMenuAjax');
    });

    /**
     * 添加推荐位
     */
    Route::post('/recommend/schedule/add', 'RecommendController@addScheduleItem');

    /**
     * 修改线上的推荐位数据
     */
    Route::post('/recommend/online/set', 'RecommendController@updOneItem');

    /**
     * 删除线上的推荐位数据
     */
    Route::post('/recommend/online/del', 'RecommendController@delOneItem');

    /**
     * 修改排期的的推荐位数据
     */
    Route::post('/recommend/schedule/set', 'RecommendController@updScheduleItem');

    /**
     * 删除排期的的推荐位数据
     */
    Route::get('/recommend/schedule/del/{id}', 'RecommendController@delScheduleItem');

    /**
     * 发布
     */
    Route::get('/recommend/publish/{posid}', 'RecommendController@publish');
    /**
     * 需要验证权限的操作
     */
    Route::group(['middleware' => ['auth', 'menu']], function () {

        // 得到某个组的所有用户 例如 admin = 0
        Route::group(['prefix' => 'ajax'], function () {
            Route::get('showPerm', 'UserController@postShowPerm')->name('getUser');
        });
        Route::get('user/getUser', ['as' => 'getUser', 'uses' => 'UserController@getTypeUser']);
        // 展现某个用户所拥有的权限[admin,open]

        /**
         * 游戏
         */
        Route::get('game', 'GameController@index')->name("game");

        /**
         * 游戏评论
         */
        Route::get('gcomment/{appid}', 'GCommentController@commentList')
            ->where('appid', '[0-9]+')
            ->name("gcomment");
        /**
         * 评论编辑
         */
        Route::group(['as' => 'gcomment/edit'], function () {
            Route::get('gcomment/{gid}/{id}', 'GCommentController@edit')
                ->where('gid', '[0-9]+')->where('id', '[0-9]+');
            Route::put('gcomment/{gid}/{id}', 'GCommentController@update')
                ->where('gid', '[0-9]+')->where('id', '[0-9]+');
        });

        /**
         * 评论删除
         */
        Route::delete('gcomment', 'GCommentController@destroy')->name("gcomment/del");

        /**
         * 游戏类型
         */
        Route::get('gtype', 'GTypeController@index')->name("gtype");
        Route::post('gtype/store', 'GTypeController@store')->name("gtype/add");

        /**
         * 游戏推荐
         */
        Route::get('grecommend', 'GRecommendController@index')->name("grecommend");
        Route::post('grecommend', 'GRecommendController@update')->name("grecommend/edit");

        /**
         * 用户查询
         */
        Route::group(['as' => 'userinfo'], function () {
            Route::get('userinfo', 'UserInfoController@search');
            Route::post('userinfo', 'UserInfoController@search');
        });

        /**
         * 用户封停
         */
        Route::group(['as' => 'user/ban'], function () {
            Route::get('user/ban', function () {
                return view('userinfo.ban');
            });
            Route::post('user/ban', 'UserInfoController@ban');
        });
        /**
         * 用户解封
         */
        Route::group(['as' => 'user/unban'], function () {
            Route::get('user/unban', function () {
                return view('userinfo.unban');
            });

            Route::post('user/unban', 'UserInfoController@unban');
        });

        /**
         * 数据查询
         */
        Route::get('data/all', function () {
            return view('data.all');
        })->name("data/all");

        Route::get('data/platform', function () {
            return view('data.platform');
        })->name("data/platform");

        Route::get('data/webgame', function () {
            return view('data.webgame');
        })->name("data/webgame");

        Route::get('data/video', function () {
            return view('data.video');
        })->name("data/video");

        Route::get('user/game', 'UserInfoController@userGame')->name("user/game");
        Route::post('user/game', 'UserInfoController@userGame')->name("user/game");

        /*
         * 获取视频的列表信息
         */
        Route::get('videos', 'VideoController@getAll')->name("videos");

        /*
         * 获取视频的分类列表
         */
        Route::get('videoSort', 'VideoController@getVideoSort')->name("videoSort");

        /*
         * 删除视频分类
         */
        Route::post('video/videoSortDel', 'VideoController@videoSortDel')->name("video/videoSortDel");

        /*
         * 添加新的分类数据
         */
        Route::post('uploadSort', 'VideoController@uploadSort')->name("uploadSort");

        /*
         * 获取视频的信息
         */
        Route::group(['as' => 'videoInfo'], function () {
            Route::get('videoInfo', 'VideoController@getVideoInfo');
            Route::post('videoInfo', 'VideoController@getVideoInfo');
        });
        /*
         * 广告位路由
         */
        Route::group(['as' => 'videoAd'], function () {
            Route::get('videoAd', 'VideoController@addVideoAd');
            Route::post('videoAd', 'VideoController@addVideoAd');
            Route::get('videoAd/{vtid}', 'VideoController@addVideoAd');
            Route::get('videoAd/', 'VideoController@addVideoAdView');
        });

        /*
         * 广告图片上传和广告删除路由
         */
        Route::post('uploadAd', 'VideoController@uploadAd')->name("uploadAd");
        Route::post('video/videoAdDel', 'VideoController@videoAdDel')->name("video/videoAdDel");

        /*
         * 页游的相关部分
         */
        Route::group(['as' => 'webgameAd'], function () {
            Route::get('webgameAd', 'AdminWebgameController@webgameAd');
            Route::post('webgameAd', 'AdminWebgameController@webgameAd');
            Route::get('webgameAd/{vtid}', 'AdminWebgameController@webgameAd');
            //Route::get('webgameAd', 'AdminWebgameController@webgameAd');
        });

        /*
         * 页游广告图片上传和广告删除路由
         */
        Route::post('uploadWebgameAd', 'AdminWebgameController@uploadWebgameAd')->name("uploadWebgameAd");
        Route::post('webgameAdDel', 'AdminWebgameController@webgameAdDel')->name("webgameAdDel");
        /*
         * 获取搜索页游的信息
         */
        Route::get('webgameSearch/{searchword}', 'AdminWebgameController@getwebgameSearch')->name("webgameSearch");
        /*
         * 获取页游的分类列表
         */
        Route::get('webgameSearch', 'AdminWebgameController@webgameInfo')->name("webgameSearch");

        /*
         * 页游礼包添加
         */
        Route::get('webgameGift', 'AdminWebgameController@webgameGift')->name("webgameGift");
        Route::post('uploadWebgameGift', 'AdminWebgameController@uploadWebgameGift');
        /*
         * 页游的CDkey导入接口
         */
        Route::post('uploadWebgameGiftCode', 'AdminWebgameController@uploadWebgameGiftCode');

        /*
         * 页游的推荐位路由
         */
        Route::get('recommendWebGame/{code}', 'AdminWebgameController@recommendWebGame')->name("recommendWebGame/officialwebgame");

        /*
         * 页游的推荐位路由
         */
        Route::get('bannerRecommend/{code}', 'AdminWebgameController@bannerRecommend')->name("bannerRecommend/game-video-banner");
        /*
         * 推荐位的菜单生成
         */
        Route::get('recommendMenuAdd', 'AdminWebgameController@recommendMenuAdd')->name("recommendMenuAdd");
        /*
         * 添加推荐位信息路由
         */
        Route::post('recommend/sortAdd', 'AdminWebgameController@insPosByCode');
        /*
         * 添加推荐位信息路由
         */
        Route::post('addNewRecommend', 'AdminWebgameController@addNewRecommend');
        /*
         * 按钮发布路由
         */
        Route::get('recommendPublish', 'AdminWebgameController@recommendPublish');
        Route::post('recommendPublish', 'AdminWebgameController@recommendPublish');

        /*
         * 获取搜索视频的信息
         */
        Route::get('videoSearch/{searchword}', 'VideoController@getVideoSearch')->name("videoSearch");
        //Route::post('videoSearch', 'VideoController@getVideoSearch');
        /*
         * 获取视频的分类列表
         */
        Route::get('videoSearch', 'VideoController@getVideoInfo')->name("videoSearch");

        /*
         * 获取搜索视频的信息
         */
        Route::get('videoRecommend', 'VideoController@videoRecommend')->name("videoRecommend");

        Route::post('recommendAdd', 'VideoController@videoRecommendAdd')->name("recommendAdd");

        Route::get('vUserSearch/{vtid}/{uid}', 'VideoController@vUserSearch')->name("vUserSearch");
        Route::get('vUserSearchOne', 'VideoController@vUserSearchOne')->name("vUserSearchOne");

        // 意见反馈列表
        Route::get('adviceList', 'ServiceController@adviceList')->name("adviceList");
        // 意见反馈详情
        Route::get('adviceInfo', 'ServiceController@adviceInfo');

        /*
         *  测试
         */
        Route::get('vrtest', 'AdminWebgameController@test');

        /*
         * 七牛云的上传图片路由
         */
        Route::get('/upload/cosAppSign', 'UploadController@cosAppSign');
        Route::get('/upload/cosAppSignOnce', 'UploadController@cosAppSignOnce');
    });

});
