<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models\WebGame;

interface WebgameInterface
{
    /**
     * 登录游戏
     * @param   int     appid
     * @param   int     serverid    服务器ID
     * @param   string  appkey      appkey
     * @param   int     uid
     * @param   string  openid      openid
     * @param   int     isadult     是否是成年人，1:是;0:否;
     * @param   array   uinfo       用户信息数组
     * @param   array   ainfo       app信息数组
     * @param   array   sinfo       服务器信息数组
     * @return  string  进游戏的链接
     */
    public static function loginGame($serverid, $appkey, $uid, $openid, $isadult, $uinfo = array(), $ainfo = array(), $sinfo = array());

    /**
     * 获得游戏角色
     * @param   int     serverid    服务器id
     * @param   string  appkey      加密用的key
     * @param   int     uid         平台uid
     * @param   string  openid      openid
     * @param   array   ainfo       app信息数组
     * @param   array   sinfo       服务器信息数组
     * @param   mix     extra       额外参数，为特殊参数用
     * @return  array   角色信息
     */
    public static function getRole($serverid, $appkey, $uid, $openid, $ainfo = array(), $sinfo = array(), $extra = null);

    /**
     * 充值回调
     * @param   array   consume     消费订单信息
     * @param   array   paykey      加密用的key
     * @param   array   ainfo       app信息数组
     * @param   array   sinfo       服务器信息数组
     * @param   mix     extra       额外参数，为特殊参数用
     * @return  string  回调结果
     */
    public static function payCallBack($consume, $paykey, $ainfo = array(), $sinfo = array(), $extra = null);
}