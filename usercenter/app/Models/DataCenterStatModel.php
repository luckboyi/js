<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models;

use Cookie;
use Helper\Library;
use Helper\UdpLog;
use App\Helper\Vredis;
use Helper\HttpRequest;
use Illuminate\Database\Eloquent\Model;

class DataCenterStatModel extends Model
{
    //private static $authKey = "ytLNRLFgkdc0lFJ)jYYH7W9-jn0_!naW";
    private static $authKey = "12ced4d084ab93bccaca96db9651803b";
    private static $adauthKey = "743710fe9f28b9bdb4d7f5e3d23dfed5";

    private static $redis;

	private static $properties = [
        //"_birthday" => "",
        //"_gender"   => "",
        //"_ip"       => "",
        //"_vip"      => "",
        //"_appv"     => "",
        //"_os"       => "",
        //"_email"    => "",
        //"_channel"  => "",
        //"_platform_friends"      => 0,
        //"_pay_currency_surplus"  => "",
        //"_free_currency_surplus" => "",
    ];


    private static $strmax = 1024;
    public static $sendData;
    public static $sendLen;

    /**
     * 获得属性
     * @param   string  prop    属性名称
     * @return  string  value
     */
    public static function getProp($prop)
    {
        return self::$$prop;
    }


	/**
	 * 发送日志
	 * @param   string  event   事件名称，日志类型
	 * @param   string  did     设备号
	 * @param   int     uid     uid
	 * @param   array   info    当前日志对应的properties
	 * @return  bool
	 */
	public static function stat($project, $event, $uid, $properties=array())
    {
        if(!$project) {
            $project = "vrplat";
        }
        $data = [
            "project" => $project,
            "event" => $event,
            "did"   => strval(Cookie::get("did")),
            "uid"   => $uid ? $uid : strval(Cookie::get("uid")),
            "timestamp" => time(),
        ];

        /**
         * 这种日志类型要发送ouid，不要uid
         */
        if($project == "vrgame") {
            $data['ouid'] = $data['uid'];
            unset($data['uid']);
        }
        if(isset($properties['did']) && $properties['did']) {
            $data['did'] = $properties['did'];
        }
        if(isset($properties['did'])) {
            unset($properties['did']);
        }

        $data['properties'] = self::$properties;
        $data['properties']['id']  = uniqid();
        $data['properties']['_ip'] = Library::realIp();
        if(isset($_REQUEST['appv'])) $data['properties']['_appv'] = $_REQUEST['appv'];
        if(isset($_REQUEST['os'])) $data['properties']['_os'] = $_REQUEST['os'];

        if(is_array($properties)) {
            foreach($properties as $key => $value) {
                $data['properties'][$key] = $value;
            }
        }
        if(!isset($data['properties']['_termianl']) || !$data['properties']['_termianl']) {
            $data['properties']['_termianl'] = Library::isClient() ? "pc" : "web";
        }
        return self::sendQueue(json_encode($data));
	}

	/**
	 * 将日志写入队列
	 * @param   array   log     日志
	 * @return  bool
	 */
	public static function sendQueue($log)
    {
        if(!$log) {
            return false;
        }
        $ret = Vredis::rpush("datacenterstat", "", $log);
        Vredis::close();
        UdpLog::save2("stat.vronline.com/stat", array("log" => $log), null, false);
        return $ret;
	}

	/**
	 * 从队列中读日志
	 * @param   array   log     日志
	 * @return  bool
	 */
	public static function getStatLog()
    {
        $val = Vredis::blpop("datacenterstat", "", 55);
        $redis->close();
        return $ret;
	}

	/**
	 * 判断登录状态
	 * @param   string  event   事件名称
	 * @param   string  token   密码
	 * @return  array   ['code'=>0]
	 */
	public static function send($data, $type = "vrplat")
    {
        if(in_array($type, array("vradv", "vrgame"))) {
		    $url = "http://stat.vronline.com:82/";
            $authkey = self::$adauthKey;
        }else {
            $url = "http://stat.vronline.com/";
            $authkey = self::$authKey;
        }

        if(is_array($data)) {
            $data = json_encode($data);
        }

        $postLinkedString = "message=" . rawurlencode($data) . "&sign=" . rawurlencode(md5($authkey . $data . $authkey));
        HttpRequest::setTimeout(1, 1);
		$ret = HttpRequest::post($url, $postLinkedString);
        $httpinfo = HttpRequest::getInfo();
        $errinfo  = HttpRequest::getError();
        if($ret === false || trim($ret) != 0) {
            $ret = array("ret"=>$ret, "error"=>$errinfo, "info"=>$httpinfo);
        }
        return $ret;
	}


    public static function writeInt16($str)
    {
        self::$sendData .= pack("n", $str);
        self::$sendLen += 2;
    }

    public static function writeString($str)
    {
        $strLen = strlen($str);
        if ($strLen > self::$strmax) {
            $strLen = self::$strmax;
        }
        self::writeInt16($strLen);
        $format = "a" . $strLen;

        self::$sendData .= pack($format, $str);
        self::$sendLen += $strLen;
    }

    public static function sendUdp($str)
    {
		self::writeString($str);

		$buff = self::$sendData;
		$len  = self::$sendLen;

		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if (!$socket) {
			return false;
		}
		$res = socket_sendto($socket, $buff, $len, 0, "10.105.208.199", 8802);
		socket_close($socket);
		return $res;
    }
}