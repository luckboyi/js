<?php

/*
token中心
date:2016/9/1
 */

namespace App\Models;

use App\Helper\Vmemcached;
use App\Models\LoginModel;
use Config;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class TokenModel extends Model
{
    private static $tokenType = [
        "register" => ["pre" => "reg", "expire" => 5 * 60],
    ];
    /**
     * 生成token
     * @param       string  type    token的种类，reg:注册token;
     * @param       string  key             token的key，注册是手机号码;绑定手机是手机号码+id等
     * @param       int             expire  过期时间
     */
    public static function genToken($type, $key, $expire = null)
    {
        $token = Library::genKey(32);

        if (!$key || !$type) {
            return false;
        }
        $prefix = isset(self::$tokenType[$type]['pre']) ? self::$tokenType[$type]['pre'] : "";
        if (!$prefix) {
            return false;
        }
        $newkey = $prefix . "_" . $key;
        if ($expire === null) {
            $expire = isset(self::$tokenType[$type]['expire']) ? self::$tokenType[$type]['expire'] : null;
        }
        try {
            $ret = Vmemcached::set("common_token", $newkey, $token, $expire);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            $ret = false;
        }
        if (!$ret) {
            return false;
        }
        return $token;
    }

    /**
     * 校验token
     * @param       string  type    token的种类，reg:注册token;
     * @param       string  key             token的key，注册是手机号码;绑定手机是手机号码+id等
     */
    public static function checkToken($type, $key, $token)
    {
        if (!$key || !$type || !$token) {
            return false;
        }
        $prefix = isset(self::$tokenType[$type]['pre']) ? self::$tokenType[$type]['pre'] : "";
        if (!$prefix) {
            return false;
        }
        $newkey = $prefix . "_" . $key;

        try {
            $check = Vmemcached::get("common_token", $newkey);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($check != $token) {
            return false;
        }
        return true;
    }

    /**
     * 删除token
     * @param       string  type    token的种类，reg:注册token;
     * @param       string  key             token的key，注册是手机号码;绑定手机是手机号码+id等
     */
    public static function delToken($type, $key, $token)
    {
        if (!$key || !$type || !$token) {
            return false;
        }
        $prefix = isset(self::$tokenType[$type]['pre']) ? self::$tokenType[$type]['pre'] : "";
        if (!$prefix) {
            return false;
        }
        $newkey = $prefix . "_" . $key;

        try {
            $ret = Vmemcached::delete("common_token", $newkey);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return true;
    }

}
