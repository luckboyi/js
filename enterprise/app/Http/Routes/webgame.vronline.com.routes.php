<?php
/**
 * webgame.vronline.com
 */
Route::group(['domain' => 'webgame.vronline.com'], function () {
    Route::get('login', 'UserController@clientLogin');
    /**
     * 首页
     */
    Route::get('/', 'WebGameTController@index');

    /**
     * 选服页面
     */
    Route::get('/servers/{appid}', 'WebgameController@servers');

    /**
     * 游戏页面
     */
    Route::get('/start/{appid}/{serverid}', 'WebgameController@start');

    /**
     * 获取vr所有游戏列表
     */
    Route::any('/game/getdata', 'WebgameController@getAllVrGameApi');

});
