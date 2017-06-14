<?php

namespace Helper;

use App\Helper\Vmemcached;
use Config;

class Library {

	/**
	 * 输出结果
	 */
	public static function output($code, $data = null, $msg = null) {
		$return = array();
		if (!$msg) {
			$msg = Config::get("errorcode.{$code}");
		}
		if (!$msg) {
			$msg = "未知错误";
		}
		$return['code'] = $code;
		if ($data !== null) {
			$return['data'] = $data;
		}

		$return['msg'] = $msg;
		return json_encode($return);
	}

	/**
	 * 获取当前环境
	 * @return  product: 正式环境
	 */
	public static function getCurrEnv() {
		$env = getenv("LARAVEL_APP_ENV");
		if ($env === "dev") {
			return "develop"; // 开发环境
		} else if ($env === "test") {
			return "test"; // 测试环境
		} else if ($env === "local") {
			return "local"; // 本地环境
		} else {
			return "product"; // 正式环境
		}
	}

	/**
	 * 生成key
	 */
	public static function genKey($len = 32) {
		$len = intval($len);
		if ($len <= 0) {
			$len = 32;
		}
		$list = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '_', '-', '=');
		$num = count($list);
		$str = "";
		for ($i = 0; $i < $len; $i++) {
			$r = mt_rand(0, $num - 1);
			$char = $list[$r];
			$str .= $char;
		}
		return $str;
	}

	/**
	 * 生成纯数字的验证码
	 * @param   string  type    生成的验证码类型，str: 包含字母; num:只数字;
	 * @param   int     len     验证码长度
	 */
	public static function genCode($type = "num", $len = 6) {
		$len = intval($len);
		switch ($type) {
		case "str":
			$list = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
			break;
		case "num":
			$list = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
			break;
		case "normal":
		    $list = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '_');
			break;
		default:
			$list = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
			break;
		}
		$num = count($list);
		$str = "";
		for ($i = 0; $i < $len; $i++) {
			$r = mt_rand(0, $num - 1);
			$char = $list[$r];
			$str .= $char;
		}
		return $str;
	}

	/**
	 * 生成签名
	 * 用于调用开发商接口和用户中心接口
	 */
	public static function encrypt($params, $appkey, &$request = null) {
		if (!$params || !is_array($params) || !$appkey) {
			return false;
		}
		ksort($params);

		$query1 = $request = array();
		foreach ($params as $key => $value) {
			if ($key == "sign") {
				continue;
			}
			array_push($query1, $key . "=" . $value);
			$request[$key] = $value;
		}
		$query_string = join("&", $query1);
		$sign = md5($appkey . $query_string);
		$request['sign'] = $sign;
		return $sign;
	}

	/**
	 * 获得用户的真实IP地址
	 */
	public static function realIp() {
		static $realip = NULL;

		if ($realip !== NULL) {
			return $realip;
		}

		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

				/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
				foreach ($arr AS $ip) {
					$ip = trim($ip);

					if ($ip != 'unknown') {
						$realip = $ip;

						break;
					}
				}
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$realip = $_SERVER['REMOTE_ADDR'];
				} else {
					$realip = '0.0.0.0';
				}
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$realip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_CLIENT_IP')) {
				$realip = getenv('HTTP_CLIENT_IP');
			} else {
				$realip = getenv('REMOTE_ADDR');
			}
		}

		preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
		$realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

		return $realip;
	}

	/**
	 * 获得用户的真实IP地址
	 */
	public static function real_ip() {
		return self::realIp();
	}

	/**
	 * 允许跨域访问的header
	 */
	public static function accessHeader() {
		$referer = "http://www.vronline.com";
		if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
			$referer = $_SERVER['HTTP_ORIGIN'];
		} else if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
			$referer = $_SERVER['HTTP_REFERER'];
		}
		$info = parse_url($referer);

		$host = $info['host'];
		if (isset($info['port'])) {
			$host = $info['host'] . ":" . $info['port'];
		}
		if (!$host) {
			return false;
		}
		$allows = Config::get("access_control_allow_origin");
		if (isset($allows[$host]) && $allows[$host]) {
			$origin = $info['scheme'] . "://" . $host;
			header("Access-Control-Allow-Origin:{$origin}");
			header("Access-Control-Allow-Credentials:true");

		}
		return true;
	}

	/**
	 * 系统进程数量
	 */
	public static function osProcessNum($name) {
		if (!$name) {
			return false;
		}
        $cmd = "ps ax|grep {$name}|grep -v grep|grep -v '/bin/sh -c'|wc -l";
		$ret = shell_exec($cmd);
		$ret = intval(trim($ret));
		return json_encode($ret);
	}

	/**
	 * 是否在客户端中运行
	 *
	 * @return boolean
	 */
	public static function isClient() {
		if (!isset($_SERVER['HTTP_USER_AGENT']) || !$_SERVER['HTTP_USER_AGENT']) {
			return false;
		}
		$pos = strpos($_SERVER['HTTP_USER_AGENT'], "VRonlinePlat");
		if ($pos === false) {
			return false;
		}
		return true;
	}

	/**
	 * 生成给开发商的订单号
	 */
	public static function genCPOrderid($orderid) {
		if (!$orderid) {
			return false;
		}
		$info = explode("_", $orderid);
		if (!$info || !is_array($info) || count($info) != 5) {
			return $orderid;
		}
		$new = "";
		if ($info[0] == "order") {
			$new = "O";
		} else if ($info[0] == "trade") {
			$new = "T";
		}
		$new = $new . $info[3] . $info[4] . $info[2];
		return $new;
	}

	/**
	 * 加锁
	 */
	public static function addLock($lockid) {
		$key = "lock_" . $lockid;
		$now = time();
		$value = $now . "|" . $lockid;
		return Vmemcached::add("lock", $lockid, $value);
	}

	/**
	 * 删除锁
	 */
	public static function delLock($lockid) {
		$key = "lock_" . $lockid;
		return Vmemcached::delete("lock", $lockid);
	}

    /**
     * XML转数组
     */
    public static function xmlToArray($xml)
    {
        $xml = trim($xml);
        libxml_disable_entity_loader(true); 
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
        $val = json_decode(json_encode($xmlstring),true); 
        return $val; 
    }
}