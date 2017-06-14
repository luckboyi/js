<?php

namespace Helper;

class HttpRequest
{
    /**
     * curl 执行信息
     */
    private static $curl_info = "";

    /**
     * curl 错误信息
     */
    private static $curl_error = "";

    /**
     * 连接超时时间
     */
    private static $connect_timeout = 1;

    /**
     * 总执行时间
     */
    private static $curl_timeout = 2;

    /**
     * 普通post请求
     * @param   string  url     请求url
     * @param   array   params  post请求参数数组
     * @param   int     port    端口，0为默认端口
     * @param   array   options curl_setopt信息，关联数组，index是 curl_setopt 常量
     */
    public static function post($url, $params = array(), $port = 0, $options = array(), $headers = array())
    {
        if (!$url) {
            return false;
        }
        if (!function_exists('curl_init')) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($port) {
            curl_setopt($ch, CURLOPT_PORT, $port);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$curl_timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($options && is_array($options)) {
            curl_setopt_array($ch, $options);
        }
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        if (!$result) {
            $errmsg = curl_error($ch);
            self::setError($errmsg);
        }
        self::setInfo(curl_getinfo($ch));
        curl_close($ch);
        return $result;
    }

    /**
     * 普通get请求，参数要么放url里，要么放params里，只能选择其一
     * @param   string  url     请求url，如果params传了，url里就不能带任何参数
     * @param   array   params  get请求参数数组，可以不传，如果传，必须是个数组
     * @param   int     port    端口，0为默认端口
     * @param   array   options curl_setopt信息，关联数组，index是 curl_setopt 常量
     */
    public static function get($url, $params = array(), $port = 0, $options = array())
    {
        if (!$url) {
            return false;
        }
        if (!function_exists('curl_init')) {
            return false;
        }
        if ($params && !is_array($params)) {
            return false;
        }
        if ($params) {
            if (strpos($url, "?") !== false) {
                return false;
            }
            $str_param = http_build_query($params);
            $url       = $url . "?" . $str_param;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($port) {
            curl_setopt($ch, CURLOPT_PORT, $port);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$curl_timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($options && is_array($options)) {
            curl_setopt_array($ch, $options);
        }
        $result = curl_exec($ch);
        if (!$result) {
            $errmsg = curl_error($ch);
            self::setError($errmsg);
        }
        self::setInfo(curl_getinfo($ch));
        curl_close($ch);
        return $result;
    }

    public static function cosPost($url, $sign, $params, $header = [])
    {
        if (!$url) {
            return false;
        }
        $url = $url;
        if (!$header) {
            $header = array('Authorization:QCloud ' . $sign);
        }
        $privateImg = false;
        if (isset($params['privateimg'])) {
            $privateImg = true;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'vronline');
        if (!$privateImg) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        if ($res) {
            if (!$privateImg) {
                return json_decode($res, true);
            } else {
                return $res;
            }
        }
        return false;
    }

    public static function url($method, $url, $headers = false, $params = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'vronline');
        if (strstr($url, "http://search.vronline.com:8889")) {
            curl_setopt($ch, CURLOPT_USERPWD, "solr_95:9xnf#Wp4");
        }
        if ($method == "post") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        if ($res) {
            return json_decode($res, true);
        }
        return false;
    }

    /**
     *
     */
    private static function setInfo($info)
    {
        self::$curl_info = $info;
    }

    /**
     *
     */
    public static function getInfo()
    {
        return self::$curl_info;
    }

    /**
     *
     */
    private static function setError($error)
    {
        self::$curl_error = $error;
    }

    /**
     * 获得错误信息
     */
    public static function getError()
    {
        return self::$curl_error;
    }

    /**
     * 设置超时时间
     */
    public static function setTimeout($connect_timeout, $curl_timeout)
    {
        self::$connect_timeout = $connect_timeout;
        self::$curl_timeout    = $curl_timeout;
    }

}
