<?php
/*
 *uid、openid转换
 */

namespace App\Models;

use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class OpenidModel extends Model
{
    /**
     * 这个数组一定不能改 ！！！！！！！！！！！！！
     */
    const BASE_CHARS = ['i', 'v', 't', 'f', '2', '3', 'Z', 'y', 'k', '6', 'n', 'W', 'U', '9', 'u', '7', 'r', 'Q', 'M', 'c', 'z', 'j', 'J', 'S', '_', 'H', 'G', '1', 'K', 'R', '0', 'D', 'L', 's', 'P', '8', 'C', 'N', 'g', 'e', 'A', 'B', '5', 'Y', 'T', 'q', 'w', 'p', 'V', 'b', 'h', '4', 'X', 'x', 'd', 'F', 'E', 'a', 'm'];
    const BASE_UID_DIVIDE = 8385;
    const BASE_APPID_DIVIDE = 9237;
    private static $count = 59;

    /**
     * 根据appid、uid得到openid
     * 
     */
    public static function getOpenid($appid, $uid)
    {
        if(!$appid || !$uid) {
            return false;
        }

        /**
         * 计算出最后一部分，也做校验位
         * uid、appid的余数之和
         */
        $remainder_uid   = $uid % self::BASE_UID_DIVIDE;
        $remainder_appid = $appid % self::BASE_APPID_DIVIDE;
        $last        = $remainder_uid * 37 + $remainder_appid * 192;
        $last_59     = self::get59($last);
        $last_len    = strlen($last_59);
        $last_len_59 = self::get59($last_len);

        /**
         * 倒数第二部分 uid + appid + last
         */
        $second        = $uid + $appid + $last;
        $second_59     = self::get59($second);
        $second_len    = strlen($second_59);
        $second_len_59 = self::get59($second_len);

        /**
         * 倒数第三部分 appid . (uid % 10) + second
         */
        $first        = intval($appid . ($uid % 10)) + $second;
        $first_59     = self::get59($first);
        $first_len    = strlen($first_59);
        $first_len_59 = self::get59($first_len);

        $openid = $first_len_59 . $first_59 . $second_len_59 . $second_59 . $last_len_59 . $last_59;

        /**
         * 校验
         */
        $check = self::getUid($openid);
        if(!$check || !is_array($check) || !isset($check['uid']) || !isset($check['appid']) || $check['appid'] != $appid || $check['uid'] != $uid) {
            UdpLog::save2("openid/fail", array("function" => "getOpenid", "result" => "false", "log" => "gen openid error", "appid" => $appid, "uid" => $uid, "openid" => $openid, "check" => $check), __METHOD__."[".__LINE__."]");
            return false;
        }
        return $openid;
    }

    /**
     * openid转成uid
     */
    public static function getUid($openid)
    {
        $first_len_59 = substr($openid, 0, 1);
        $first_len    = self::getDec($first_len_59);
        $first_59     = substr($openid, 1, $first_len);
        $first        = self::getDec($first_59);

        $second_start = $first_len + 1;

        $second_len_59 = substr($openid, $second_start, 1);
        $second_len    = self::getDec($second_len_59);
        $second_59     = substr($openid, $second_start + 1, $second_len);
        $second        = self::getDec($second_59);

        $last_start = $second_start + 1 + $second_len;

        $last_len_59 = substr($openid, $last_start, 1);
        $last_len    = self::getDec($last_len_59);
        $last_59     = substr($openid, $last_start + 1, $last_len);
        $last        = self::getDec($last_59);


        $first_appid   = $first - $second;
        $appid = substr($first_appid, 0, -1);

        $uid = $second - $appid - $last;

        if(!$appid || !is_numeric($appid) || $appid <= 0 || !$uid || !is_numeric($uid) || $uid <= 0) {
            return false;
        }

        $remainder_uid   = $uid % self::BASE_UID_DIVIDE;
        $remainder_appid = $appid % self::BASE_APPID_DIVIDE;
        if($last != $remainder_uid * 37 + $remainder_appid * 192) {
            return false;
        }

        return array("appid" => $appid, "uid" => $uid);
    }

    /**
     * 检测转换的字符串数组是否正确
     */
    private static function check()
    {
        $count1 = count(self::BASE_CHARS);
        $count2 = count(array_flip(self::BASE_CHARS));
        if($count1 != $count2 || $count1 != self::$count || self::BASE_UID_DIVIDE !== 8385 || self::BASE_APPID_DIVIDE !== 9237) {
            UdpLog::save2("openid/fail", array("function"=>"check", "result"=>"false", "log" => "openidModel check false", "count1" => $count1, "count2" => $count2, "BASE_UID_DIVIDE" => self::BASE_UID_DIVIDE, "BASE_APPID_DIVIDE" => self::BASE_APPID_DIVIDE), __METHOD__."[".__LINE__."]");
            return false;
        }
        return true;
    }

    /**
     * 10进制转59进制
     */
    private static function get59($num)
    {
        if(!self::check()) {
            return false;
        }
        $str = '';
        while ($num > 0) {
            $key = ($num ) % self::$count;
            $str = self::BASE_CHARS[$key] . $str;
            $num = floor(($num - $key) / self::$count);
        }
        return $str;
    }

    /**
     * 59进制转10进制
     */
    private static function getDec($str)
    {
        if(!self::check()) {
            return false;
        }
        $new = array_flip(self::BASE_CHARS);
        $num = 0;
        for($i=0;$i<=strlen($str)-1;$i++)
        {
            //从末尾依次取得字符串
            $char = substr($str,-($i+1),1);
            //取得字符串的位置作为值
            $val = $new[$char];
            //取得字符所代表的数值
            $num = $num + $val*(pow(self::$count,$i));
        }
        return $num;
    }

}