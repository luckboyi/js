<?php

/*
 * 特战英雄
 * date:2016/12/6
 */

namespace App\Models\WebGame;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\WebGame\WebgameInterface;

class Webgame1000166Class implements WebgameInterface
{
    /**
     * 游戏分配的pid
     */
    public static $appid = 1000166;

    /**
     * 登录token
     */
    public static $appkey = "282d53e0fa80f848af9d89020ad77999";

    /**
     * 充值token
     */
    public static $paykey = "a130f3d9332b8b199865cf0fa2abd839";

    /**
     * 游戏分配的
     */
    public static $type = "tzyx";

    /**
     * 游戏分配的
     */
    public static $source = "vr";

    /**
     * 登录接口
     */
    public static $loginurl = "http://api3.web.7k7k.com/start.php";

    /**
     * 充值接口
     */
    public static $payurl = "http://pay.web.7k7k.com/tzyx_charge";

    /**
     * 角色查询url
     */
    public static $roleurl = "http://pay.web.7k7k.com/tzyx_check";

    /**
     * 登录游戏，特战要求传入的平台ID必须为整数，所以使用uid
     * @param   int     serverid    服务器ID
     * @param   string  appkey      appkey
     * @param   int     uid
     * @param   string  openid      openid
     * @param   int     isadult     是否是成年人，1:是;0:否;
     * @param   array   uinfo       用户信息数组
     * @param   array   ainfo       app信息数组
     * @return  string  进游戏的链接
     */
    public static function loginGame($serverid, $appkey, $uid, $openid, $isadult, $uinfo = array(), $ainfo = array(), $sinfo = array())
    {
        $ts = time();
        if($isadult == 1) {
            $isAdult = 0;
        }else {
            $isAdult = 1;
        }

        $url_params = "account={$uid}&time={$ts}&server={$serverid}&isAdult={$isAdult}&source=".self::$source."&type=".self::$type;
        $sign = md5(self::$type.$uid.$serverid.self::$source.$ts.self::$appkey);
        $url_params = $url_params . "&sign=" . $sign;
        return self::$loginurl . "?" . $url_params;
    }

    /**
     * 获得游戏角色
     * @param   int     serverid    服务器id
     * @param   string  appkey      加密用的key
     * @param   int     uid         平台uid
     * @param   string  openid      openid
     * @return  array   角色信息
     */
    public static function getRole($serverid, $appkey, $uid, $openid, $ainfo = array(), $sinfo = array(), $extra = null)
    {
        $ts         = time();

        $url_params = "account={$uid}&server={$serverid}&source=".self::$source;
        $sign       = md5($uid.$serverid.self::$source.self::$appkey);
        $url_params = $url_params . "&sign=" . $sign;
        $request_url = self::$roleurl . "?" . $url_params;

        $ret        = HttpRequest::get($request_url);
        $errmsg     = HttpRequest::getError();
        $httpinfo   = HttpRequest::getInfo();
        if(strlen($ret) == 1 && $ret === "1") {
            return ["has" => true, "info" => [], "data" => $ret];
        }else if(strlen($ret) == 1 && $ret === "0") {
            return ["has" => false, "info" => [], "data" => $ret];
        }else {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
    }

    /**
     * 充值回调
     * @param   array   consume     消费订单信息
     * @param   array   paykey      加密用的key
     * @return  array   回调结果 array("result" => "success", "url" => "xxx", "errmsg" => "xxx", "httpinfo" => "xxx", "delivery_time" => xxx)
     */
    public static function payCallBack($consume, $paykey, $ainfo = array(), $sinfo = array(), $extra = null)
    {
        $ts   = time();
        $rate = intval(Config::get("common.plantb_count_per_rmb"));
        if($rate <= 0) {
            return array("result" => "rate error", "url" => "", "delivery_time" => 0, "errmsg" => "get rate config error", "httpinfo" => array());
        }

        $newtradeid = Library::genCPOrderid($consume['tradeid']);

        $money       = $consume['amount'] / $rate;
        $gold        = $money * 10;      // 游戏兑换比例1:10

        $url_params = "account={$consume['to_uid']}&server={$consume['serverid']}&order_id={$newtradeid}&order_amount={$money}&source=".self::$source;
        $sign = md5($consume['to_uid'].$money.$newtradeid.$consume['serverid'].self::$source.self::$paykey);
        $request_url = self::$payurl . "?" . $url_params . "&sign=" . $sign;

        $start_time = microtime(true);
        $result     = HttpRequest::get($request_url);
        $errmsg     = HttpRequest::getError();
        $httpinfo   = HttpRequest::getInfo();
        $end_time   = microtime(true);
        $delivery_time = round($end_time - $start_time, 5);
        if(strval($result) === "1") {
            $ret = "success";
        }else {
            $ret = $result;
        }
        UdpLog::save2("webgame/payCallBack", array("appid" => self::$appid, "return" => $result, "args" => func_get_args(), "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
        $return = array("result" => $ret, "url" => $request_url, "delivery_time" => $delivery_time, "errmsg" => $errmsg, "httpinfo" => $httpinfo);
        return $return;
    }

}