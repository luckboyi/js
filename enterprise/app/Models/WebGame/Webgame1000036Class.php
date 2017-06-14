<?php

/*
 * 九阴绝学
 * date:2016/11/14
 */

namespace App\Models\WebGame;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\WebGame\WebgameInterface;

class Webgame1000036Class implements WebgameInterface
{
    /**
     * 游戏分配的pid
     */
    public static $appid = 1000036;

    /**
     * 游戏分配的pid
     */
    public static $pid = 488;

    /**
     * 登录token
     */
    public static $appkey = "F3yTunu=TXgEJJZR8wfl-lj_NYNfJ6bn";

    /**
     * 充值token
     */
    public static $paykey = "SKqbPxSi0LH9=G5p_kTYeihgU47ZibVq";

    /**
     * 登录url
     */
    public static $loginurl = "http://open.jyjx.game2.com.cn:9001/opengame/unite/g2auth.htm";

    /**
     * 角色查询url
     */
    public static $roleurl = "http://open.jyjx.game2.com.cn:9001/opengame/unite/g2chxlzlaracterList.htm";

    /**
     * 充值接口url
     */
    public static $chargeurl = "http://183.60.41.177:2000/pay/gkey/jyjx/";

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
            $indulge = "n";
        }else {
            $indulge = "y";
        }
        $auth = base64_encode("pid=".self::$pid."&sid={$serverid}&uid={$openid}&time={$ts}&indulge={$indulge}");
        $sign = md5($auth . $appkey);
        $url_params = "auth={$auth}&sign={$sign}";
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
        $ts          = time();
        $sign        = md5(self::$pid."{$serverid}{$openid}{$ts}".$appkey);
        $url_params  = "pid=".self::$pid."&sid={$serverid}&uid={$openid}&time={$ts}&sign={$sign}";
        $request_url = self::$roleurl . "?" . $url_params;
        $ret         = HttpRequest::get($request_url);
        $errmsg      = HttpRequest::getError();
        $httpinfo    = HttpRequest::getInfo();
        if(!$ret) {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), "webgame/", __METHOD__."[".__LINE__."]");
            return false;
        }
        $result = json_decode($ret, true);
        if(!$result || !is_array($result) || !isset($result['result']) || !isset($result['roleinfo']) || strval($result['result']) !== "1" || !is_array($result['roleinfo'])) {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), "webgame/", __METHOD__."[".__LINE__."]");
            return false;
        }
        if($result['roleinfo']) {
            $name  = isset($result['roleinfo']['name']) ? $result['roleinfo']['name'] : "";
            $level = isset($result['roleinfo']['grade']) ? $result['roleinfo']['grade'] : "";
            return ["has" => true, "info" => ["name" => $name, "level" => $level], 'data' => $result];
        }else {
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
        $url_params  = "pid=".self::$pid."&sid={$consume['serverid']}&uid={$consume['to_openid']}&oid={$newtradeid}&money={$money}&gold={$gold}&time={$ts}";
        $sign        = md5($url_params . $paykey);
        $url_params  = $url_params . "&ip={$consume['cip']}&sign={$sign}";
        $request_url = self::$chargeurl . "?" . $url_params;

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