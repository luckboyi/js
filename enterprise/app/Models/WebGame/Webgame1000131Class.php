<?php

/*
 * 热血江湖
 * date:2016/11/14
 */

namespace App\Models\WebGame;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\WebGame\WebgameInterface;

class Webgame1000131Class implements WebgameInterface
{
    /**
     * 游戏分配的pid
     */
    public static $appid = 1000131;

    /**
     * 登录token
     */
    public static $appkey = "586f29ead315cf7d2c0b3208f0089eeb";

    /**
     * 充值token
     */
    public static $paykey = "9039e8a39d90fb389dd9df68c042b1a6";

    /**
     * 游戏分配的pid
     */
    public static $platid = 97;

    /**
     * 角色查询url
     */
    public static $roleurl = "iface/userrole.php";

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
        switch($isadult) {
            case 1:
                $cm = 1;
                break;
            case 0:
                $cm = 2;
                break;
            case -1:
                $cm = 0;
                break;
            default:    $cm = 2;break;
        }

        $host = $sinfo['domain'];
        if(substr($host, -1, 1) != "/") {
            $host .= "/";
        }

        $params   = array();
        $params['sid'] = self::getServerid($serverid);
        $params['tm'] = $ts;
        $params['platuid'] = $openid;
        $params['platid'] = self::$platid;
        $params['cm'] = $cm;

        $request_params = self::genSign($params, self::$appkey);
        $url_params = http_build_query($request_params);
        return $host . "check.php?" . $url_params;
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

        $host = $sinfo['domain'];
        if(substr($host, -1, 1) != "/") {
            $host .= "/";
        }
        $roleurl = $host . self::$roleurl;

        $sid = self::getServerid($serverid);
        $url_params = "platid=".self::$platid."&platuid={$openid}&sid={$sid}&tm={$ts}";
        $sign       = md5($url_params.self::$appkey);
        $url_params = $url_params . "&sig=" . $sign;
        $request_url = $roleurl . "?" . $url_params;

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
        if(!$result || !is_array($result) || !isset($result['r']) || !isset($result['data']) || strval($result['r']) !== "1") {
            UdpLog::save2("webgame/getRole", array("appid" => self::$appid, "args" => func_get_args(), "return" => $ret, "errmsg" => $errmsg, "httpinfo" => $httpinfo), __METHOD__."[".__LINE__."]");
            return false;
        }
        if(isset($result['data']) && $result['data'] && is_array($result['data'])) {
            $name  = isset($result['data'][0]['rolename']) ? $result['data'][0]['rolename'] : "";
            $level = isset($result['data'][0]['level']) ? $result['data'][0]['level'] : "";
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

        $params   = array();
        $params['sid'] = self::getServerid($consume['serverid']);
        $params['tm'] = $ts;
        $params['platid'] = self::$platid;
        $params['platuid'] = $consume['to_openid'];
        $params['money'] = $money;
        $params['paypoint'] = $gold;
        $params['orderid'] = $newtradeid;

        $request_params = self::genSign($params, self::$paykey);
        $url_params = http_build_query($request_params);

        $request_url = $sinfo['payurl'] . "?" . $url_params;

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


    private static function genSign($params, $appkey)
    {
        ksort($params);

        $query1 = $request = array();
        foreach($params as $key => $value)
        {
            if($key == "sign") {
                continue;
            }
            array_push($query1, $key."=".$value);
            $request[$key] = $value;
        }
        $query_string   = join("&", $query1);
        $sign           = md5($query_string . $appkey);
        $request['sig'] = $sign;
        return $request;

    }

    private static function getServerid($serverid)
    {
        return 9700000 + $serverid;
    }
}