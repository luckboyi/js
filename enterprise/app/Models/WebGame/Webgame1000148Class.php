<?php

/*
 * 完美漂移
 * date:2016/11/14
 */

namespace App\Models\WebGame;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\WebGame\WebgameInterface;

class Webgame1000148Class implements WebgameInterface
{
    /**
     * 游戏分配的pid
     */
    public static $appid = 1000148;

    /**
     * 登录token
     */
    public static $appkey = "9e206d0092cdfdbf162481863e062e68";

    /**
     * 充值token
     */
    public static $paykey = "e1c69e3b5d6184b09171d06563ef3c1d";

    /**
     * 游戏分配的
     */
    public static $type = "wmpy";

    /**
     * 游戏分配的
     */
    public static $source = "vr";

    /**
     * 登录接口
     */
    public static $loginurl = "http://web.7k7k.com/hfgames/api/start.php";

    /**
     * 充值接口
     */
    public static $payurl = "http://web.7k7k.com/hfgames/api/charge.php";

    /**
     * 角色查询url
     */
    public static $roleurl = "http://web.7k7k.com/hfgames/api/getrole.php";

    /**
     * 登录游戏
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

        $url_params = "uid={$openid}&username={$openid}&time={$ts}&server={$serverid}&isAdult={$isAdult}&source=".self::$source."&type=".self::$type;
        $sign = md5(self::$type.$openid.$serverid.self::$source.$ts.self::$appkey);
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

        $url_params = "uid={$openid}&server={$serverid}&source=".self::$source."&type=".self::$type;
        $sign       = md5($openid.$serverid.self::$source.self::$type.self::$appkey);
        $url_params = $url_params . "&sign=" . $sign;
        $request_url = self::$roleurl . "?" . $url_params;

        $ret        = HttpRequest::get($request_url);
        $errmsg     = HttpRequest::getError();
        $httpinfo   = HttpRequest::getInfo();
        if(!$ret) {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
        $result = json_decode($ret, true);
        if(!$result || !is_array($result) || !isset($result['status'])) {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
        if($result['status'] == 0) {
            return ["has" => false];
        }
        if(isset($result['rolename'])) {
            $name  = isset($result['rolename']) ? $result['rolename'] : "";
            $level = isset($result['level']) ? $result['level'] : "";
            return ["has" => true, "info" => ["name" => $name, "level" => $level], 'data' => $result];
        }else {
            return ["has" => false];
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

        $url_params = "uid={$consume['to_openid']}&username={$consume['to_openid']}&time={$ts}&server={$consume['serverid']}&orderid={$newtradeid}&order_amount={$money}&source=".self::$source."&type=".self::$type;
        $sign = md5($consume['to_openid'].$money.self::$source.self::$type.$consume['serverid'].$newtradeid.$ts.self::$paykey);
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