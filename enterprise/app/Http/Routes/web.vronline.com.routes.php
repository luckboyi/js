<?php
Route::group(['domain' => 'web.vronline.com'], function () {
    Route::get('/', 'PageGameController@index');
    Route::get('/detail/{appid}', 'PageGameController@detail');
    Route::get('/newslist/{appid}/{sort}', 'PageGameController@getGameNewsList');
    Route::get('/play/{appid}', 'PageGameController@play');

    /**
     * 游戏页面
     */
    Route::get('/start/{appid}/{serverid}', 'WebgameController@start');

    /**
     * minilogin
     */
    Route::get('/user/minilogin', function () {
        $type  = Request::input("type", "login");
        $third = Request::input("third", true);
        return view("pagegame.minilogin", compact("type", "third"));
    });
});
