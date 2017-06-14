<?php

/*
 * 主宰西游
 * date:2016/11/14
 */

namespace App\Models\WebGame;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\WebGame\WebgameInterface;

class Webgame1000127Class implements WebgameInterface
{
    /**
     * 游戏分配的pid
     */
    public static $appid = 1000127;

    /**
     * 登录token
     */
    public static $appkey = "aba18772fc70c8cbf79a79f413ef102b";

    /**
     * 充值token
     */
    public static $paykey = "f3e19e21aba9de79daddda8ea73d5248";

    /**
     * 游戏分配的pid
     */
    public static $platform = "vronline";

    /**
     * 登录url
     */
    public static $loginurl = "http://transport.game.cy2009.com/publicinterface/login/vronline/1";

    /**
     * 充值url
     */
    public static $payurl = "http://transport.game.cy2009.com/publicinterface/recharge/vronline/1";

    /**
     * 角色查询url
     */
    public static $roleurl = "http://transport.game.cy2009.com/publicinterface/rolequery/vronline/1";

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
        $url_params = "sid={$serverid}&uid={$openid}&fcm={$isadult}&exts=&time={$ts}&platform=".self::$platform;
        $sign = md5($url_params.self::$appkey);
        $url_params = $url_params . "&type=web&sign=" . $sign;
        return self::$loginurl . "?" . $url_params;
    }

    /**
     * 获得游戏角色
     * @param   int     serverid    服务器id
     * @param   string  appkey      加密用的key
     * @param   int     uid         平台uid
     * @param   string  openid      openid
     * @return  array   角色信息    ['has' => true, 'info' => ['name'=>'xxx', 'level' => xx], 'data' => [游戏返回信息]]
     */
    public static function getRole($serverid, $appkey, $uid, $openid, $ainfo = array(), $sinfo = array(), $extra = null)
    {
        $ts         = time();
        $url_params = "sid={$serverid}&uid={$openid}&time={$ts}&platform=".self::$platform;
        $sign       = md5($url_params.self::$appkey);
        $url_params = $url_params . "&sign=" . $sign;
        $request_url = self::$roleurl . "?" . $url_params;

        $proxy_opt  = array(
                        CURLOPT_PROXYTYPE       => CURLPROXY_HTTP,
                        CURLOPT_PROXY           => "gm-proxy.vronline.com:7777",
                    );
        $ret        = HttpRequest::get($request_url, "", 0, $proxy_opt);
        $errmsg     = HttpRequest::getError();
        $httpinfo   = HttpRequest::getInfo();
        if(!$ret) {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
        $result = json_decode($ret, true);
        if(!$result || !is_array($result)  || !isset($result['status']) ) {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
        if($result['status'] == 200) {
            $name  = isset($result['data']['name']) ? $result['data']['name'] : "";
            $level = isset($result['data']['level']) ? $result['data']['level'] : "";
            return ["has" => true, "info" => ["name" => $name, "level" => $level], 'data' => $result];
        }else if($result['status'] == -1) {
            return ["has" => false];
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
        $url_params  = "sid={$consume['serverid']}&uid={$consume['to_openid']}&oid={$newtradeid}&money={$money}&gold={$gold}&time={$ts}&platform=".self::$platform;
        $sign        = md5($url_params . self::$paykey);
        $url_params  = $url_params . "&sign={$sign}";
        $request_url = self::$payurl . "?" . $url_params;

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