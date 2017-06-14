<?php

/*
 * 盗墓笔记
 * date:2016/12/6
 */

namespace App\Models\WebGame;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\WebGame\WebgameInterface;

class Webgame1000218Class implements WebgameInterface
{
    /**
     * 游戏分配的pid
     */
    public static $appid = 1000218;

    /**
     * 登录token
     */
    public static $appkey = "NdGlca054iVu91MY";

    /**
     * 充值token
     */
    public static $paykey = "NdGlca054iVu91MY";

    /**
     * 游戏ID编号（游族指定）
     */
    public static $game_id = 146;

    /**
     * 运营商编号
     */
    public static $op_id = 2417;

    /**
     * 基础服务器ID，真实服务器ID在此基础上加
     */
    public static $base_sid = 2452310000;

    /**
     * 登录接口
     */
    public static $loginurl = "http://up.youzu.com/newAPI/Api/login";

    /**
     * 充值接口
     */
    public static $payurl = "http://up.youzu.com/newAPI/commonII/charge";

    /**
     * 角色查询url
     */
    public static $roleurl = "http://up.youzu.com/newAPI/commonII/roleverify";

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

        $sid = self::$base_sid + $serverid;
        $url_params = "op_id=".self::$op_id."&sid={$sid}&game_id=".self::$game_id."&account={$openid}&adult_flag={$isAdult}&time={$ts}";
        $auth       = base64_encode($url_params);
        $verify     = md5($auth . self::$appkey);
        $request_url = self::$loginurl . "?auth={$auth}&verify={$verify}";
        return $request_url;
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

        $sid = self::$base_sid + $serverid;
        $url_params = "op_id=".self::$op_id."&sid={$sid}&game_id=".self::$game_id."&account={$openid}&time={$ts}";
        $auth       = base64_encode($url_params);
        $verify     = md5($auth . self::$appkey);
        $request_url = self::$roleurl . "?auth={$auth}&verify={$verify}";

        $ret        = HttpRequest::get($request_url);
        $errmsg     = HttpRequest::getError();
        $httpinfo   = HttpRequest::getInfo();

        $result = json_decode($ret, true);
        if($result && is_array($result) && isset($result['status']) && $result['status'] == 0) {
            return ["has" => true, "data" => $result];
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

        $money      = $consume['amount'] / $rate;
        $gold       = $money * 10;      // 游戏兑换比例1:10

        $sid = self::$base_sid + $consume['serverid'];
        $url_params = "op_id=".self::$op_id."&sid={$sid}&game_id=".self::$game_id."&account={$consume['to_openid']}&order_id={$newtradeid}&game_money={$gold}&u_money={$money}&time={$ts}";
        $auth       = base64_encode($url_params);
        $verify     = md5($auth . self::$paykey);
        $request_url = self::$payurl . "?auth={$auth}&verify={$verify}";

        $start_time = microtime(true);
        $result     = HttpRequest::get($request_url);
        $errmsg     = HttpRequest::getError();
        $httpinfo   = HttpRequest::getInfo();
        $end_time   = microtime(true);
        $delivery_time = round($end_time - $start_time, 5);

        $ret = json_decode($result, true);
        if($ret && is_array($ret) && isset($ret['status']) && $ret['status'] == 0) {
            $ret = "success";
        }else {
            $ret = $result;
        }
        UdpLog::save2("webgame/payCallBack", array("appid" => self::$appid, "return" => $result, "args" => func_get_args(), "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
        $return = array("result" => $ret, "url" => $request_url, "delivery_time" => $delivery_time, "errmsg" => $errmsg, "httpinfo" => $httpinfo);
        return $return;
    }

}