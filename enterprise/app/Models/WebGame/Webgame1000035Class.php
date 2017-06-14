<?php

/*
 * 攻城掠地
 * date:2016/11/10
 */

namespace App\Models\WebGame;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\WebGame\WebgameInterface;

class Webgame1000035Class implements WebgameInterface
{
    /**
     * 游戏分配的pid
     */
    public static $appid = 1000035;

    /**
     * 游戏分配的pid
     */
    public static $yx = "vronline";

    /**
     * 人民币对游戏币的比例
     */
    public static $game_rate = 10;

    /**
     * 登录token
     */
    public static $appkey = "$#*#@@(vronline::LOGIN)@@4GAbde8OLGYvVD8Ftp";

    /**
     * 充值token
     */
    public static $paykey = "*#~^@@(vronline::PAY)@@Oz39tcOn9ceoxi6x";

    /**
     * 查询数据key
     */
    public static $querykey = "*#~^@@(vronline::PAY)@@Oz39tcOn9ceoxi6x";

    /**
     * 预登录url
     */
    public static $preloginurl = "root/preLogin.action";

    /**
     * 登录url
     */
    public static $loginurl = "root/login.action";

    /**
     * 角色查询url
     */
    public static $roleurl = "root/playerInfo.action";

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
        /**
         * additionalkey，开发商需要的参数，预登录接口和登录接口的要一致
         */
        $additionalKey = md5(rand(10000000, 999999999));
        $ts = time();
        $ticket = md5(self::$yx."{$openid}{$ts}".self::$appkey);
        $url_params = "yx=".self::$yx."&userId={$openid}&tp={$ts}&additionalKey={$additionalKey}&ticket={$ticket}";

        $host = $sinfo['domain'];
        if(substr($host, -1, 1) != "/") {
            $host .= "/";
        }

        $request_url = $host . self::$preloginurl . "?" . $url_params;
        $proxy_opt   = array(
                        CURLOPT_PROXYTYPE       => CURLPROXY_HTTP,
                        CURLOPT_PROXY           => "gm-proxy.vronline.com:7777",
                    );
        $ret      = HttpRequest::get($request_url, "", 0, $proxy_opt);
        $errmsg   = HttpRequest::getError();
        $httpinfo = HttpRequest::getInfo();
        if(!$ret) {
            UdpLog::save2("webgame/loginGame", array("action" => "prelogin", "return" => $ret, "appid" => self::$appid, "args" => func_get_args(), "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
        $result = json_decode($ret, true);
        if(!$result || !is_array($result) || !isset($result['state']) || $result['state'] != "1") {
            UdpLog::save2("webgame/loginGame", array("action" => "prelogin", "return" => $ret, "appid" => self::$appid, "args" => func_get_args(), "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }

        $ticket = md5(self::$yx."{$openid}{$ts}{$additionalKey}".self::$appkey);
        $url_params = "yx=".self::$yx."&userId={$openid}&tp={$ts}&sfid=&adult={$isadult}&yxSource=&ticket={$ticket}";
        $login_url = $host . self::$loginurl . "?" . $url_params;
        return $login_url;
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
        $host = $sinfo['domain'];
        if(substr($host, -1, 1) != "/") {
            $host .= "/";
        }

        $ts          = time();
        $url_params  = "yx=".self::$yx."&userId={$openid}";
        $request_url = $host . self::$roleurl . "?" . $url_params;
        $proxy_opt   = array(
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
        if(!$result || !is_array($result) || !isset($result['state']) || strval($result['state']) !== "1") {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
        if(isset($result['data']['players'][0]['playerName'])) {
            $name  = isset($result['data']['players'][0]['playerName']) ? $result['data']['players'][0]['playerName'] : "";
            $level = isset($result['data']['players'][0]['playerLv']) ? $result['data']['players'][0]['playerLv'] : "";
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
        $ts     = time();
        $rate   = intval(Config::get("common.plantb_count_per_rmb"));
        if($rate <= 0) {
            return false;
        }

        $newtradeid = Library::genCPOrderid($consume['tradeid']);

        $money = $consume['amount'] / $rate;
        $gold  = $money * self::$game_rate;      // 游戏兑换比例1:10

        $ticket      = md5(self::$yx."{$consume['to_openid']}{$newtradeid}{$gold}{$ts}".self::$paykey);
        $url_params  = "yx=".self::$yx."&userId={$consume['to_openid']}&playerId=&orderId={$newtradeid}&gold={$gold}&tp={$ts}&ticket={$ticket}";
        $request_url = $sinfo['payurl'] . "?" . $url_params;

        $start_time = microtime(true);
        $result     = HttpRequest::get($request_url);
        $errmsg     = HttpRequest::getError();
        $httpinfo   = HttpRequest::getInfo();
        $end_time   = microtime(true);
        $delivery_time = round($end_time - $start_time, 5);
        if($result) {
            $ret = json_decode($result, true);
            if(is_array($ret) && isset($ret['state']) && $ret['state'] == 1) {
                $result = "success";
            }
        }
        UdpLog::save2("webgame/payCallBack", array("appid" => self::$appid, "return" => $result, "args" => func_get_args(), "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
        $return = array("result" => $result, "url" => $request_url, "errmsg" => $errmsg, "httpinfo" => $httpinfo, "delivery_time" => $delivery_time);
        return $return;
    }
}