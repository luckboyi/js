<?php

namespace Helper;

use Agent;
use App\Helper\Vmemcached;
use Config;

class Library
{

    /**
     * 输出结果
     */
    public static function output($code, $data = null, $msg = null)
    {
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

        return response()->json($return);
        // return json_encode($return);
    }

    /**
     * 获取当前环境
     * @return  product: 正式环境
     */
    public static function getCurrEnv()
    {
        $env = strtolower(getenv("LARAVEL_APP_ENV"));
        if ($env === "dev") {
            return "develop"; // 开发环境
        } else if ($env === "test") {
            return "test"; // 测试环境
        } else if ($env === "local") {
            return "local"; // 本地环境
        } else if ($env === "preonline") {
            return "preonline"; // 本地环境
        } else {
            return "product"; // 正式环境
        }
    }

    /**
     * 生成key
     */
    public static function genKey($len = 32)
    {
        $len = intval($len);
        if ($len <= 0) {
            $len = 32;
        }
        $list = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '_', '-', '=');
        $num  = count($list);
        $str  = "";
        for ($i = 0; $i < $len; $i++) {
            $r    = mt_rand(0, $num - 1);
            $char = $list[$r];
            $str .= $char;
        }
        return $str;
    }

    /**
     * 生成签名
     * 用于调用开发商接口和用户中心接口
     */
    public static function encrypt($params, $appkey, &$request = null)
    {
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
        $query_string    = join("&", $query1);
        $sign            = md5($appkey . $query_string);
        $request['sign'] = $sign;
        return $sign;
    }

    /**
     * 获得用户的真实IP地址
     */
    public static function realIp()
    {
        static $realip = null;

        if ($realip !== null) {
            return $realip;
        }

        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr as $ip) {
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

    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $key         = md5($key);
        $keya        = md5(substr($key, 0, 16));
        $keyb        = md5(substr($key, 16, 16));
        $keyc        = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey   = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string        = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box    = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }

    /**
     * 数字转拼音
     */
    public static function num2Pinyin($num)
    {
        if (!is_numeric($num)) {
            return false;
        }
        $num = substr($num, 0, 1);
        switch ($num) {
            case 0:
                return "ling";
            case 1:
                return "yi";
            case 2:
                return "er";
            case 3:
                return "san";
            case 4:
                return "si";
            case 5:
                return "wu";
            case 6:
                return "liu";
            case 7:
                return "qi";
            case 8:
                return "ba";
            case 9:
                return "jiu";
                break;
            default:break;
        }
        return false;
    }

    public static function accessHeader()
    {
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
    public static function osProcessNum($name)
    {
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
    public static function isClient()
    {
        return Agent::match('VRonlinePlat');
    }

    public static function getCurrentCategory($type, $category)
    {
        $arr = explode(',', $category);
        if ($type == "news") {
            $cates = Config::get("category.vronline_news");
        } else if ($type == "pc") {
            $cates = Config::get("category.vronline_pc");
        }

        $cateId = current($arr);
        return $cates[$cateId];
    }

    public static function execCategory($category)
    {
        $arr = explode(',', $category);
        foreach ($arr as $value) {
            $out[] = intval($value);
        }
        return $out;
    }

    /**
     * 获取支付渠道配置
     *
     * @param  string  $type 支付平台类型,"charge", "minipay_webgame", "minipay_vrgame" 目前三种类型
     * @param  integer $role 获取隐藏级别 0: 显示不隐藏渠道; 1:不隐藏+管理员可见渠道; 2: 全部渠道;
     * @return array
     */
    public static function getPayChannels($type, $hidden = 0)
    {
        $ret = [];

        if (!in_array($type, ["charge", "minipay_webgame", "minipay_vrgame"])) {
            return $ret;
        }

        $typeArr = explode("_", $type);

        $platform = $typeArr[0];

        $channels = Config::get("pay_channel");

        foreach ($channels as $key => $channel) {
            if (isset($channel["hidden_" . $type]) && $channel["hidden_" . $type] > $hidden) {
                continue;
            }
            $ret[$key] = [
                "action" => $channel["action"] ?? "",
                "title"  => $channel["title_" . $platform] ?? "",
                "icon"   => $channel["icon_" . $platform] ?? "",
            ];
        }

        return $ret;
    }

    /**
     * 生成给开发商的订单号
     */
    public static function genCPOrderid($orderid)
    {
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
     * 对提供的数据进行urlsafe的base64编码。
     *
     * @param string $data 待编码的数据，一般为字符串
     *
     * @return string 编码后的字符串
     */
    public static function base64Urlsafeencode($data)
    {
        $find    = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    /**
     * 对提供的urlsafe的base64编码的数据进行解码
     *
     * @param string $str 待解码的数据，一般为字符串
     *
     * @return string 解码后的字符串
     */
    public static function base64Urlsafedecode($str)
    {
        $find    = array('-', '_');
        $replace = array('+', '/');
        return base64_decode(str_replace($find, $replace, $str));
    }

    /**
     * 加锁
     */
    public static function addLock($lockid)
    {
        $key   = "lock_" . $lockid;
        $now   = time();
        $value = $now . "|" . $lockid;
        return Vmemcached::add("lock", $lockid, $value);
    }

    /**
     * 删除锁
     */
    public static function delLock($lockid)
    {
        $key = "lock_" . $lockid;
        return Vmemcached::delete("lock", $lockid);
    }

    /**
     * 转换游玩时间
     * @param  [type] $playTime [description]
     * @return [type]           [description]
     */
    public static function handlePlayTime($playTime)
    {
        if ($playTime < 60) {
            return number_format($playTime, 2) . '秒';
        }
        if ($playTime == 60 || (60 < $playTime && $playTime < 3600)) {
            return number_format($playTime / 60, 2) . '分钟';
        }

        if ($playTime >= 3600) {
            $hours  = floor($playTime / 3600);
            $hasTmp = $playTime - $hours * 3600;
            if ($hasTmp < 1) {
                return $hours . '小时';
            }
            $min    = ceil($hasTmp / 60);
            $format = $hours . '小时' . $min . '分钟';
            return $format;
        }
    }
}
