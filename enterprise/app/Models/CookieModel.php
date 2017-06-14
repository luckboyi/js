<?php
namespace App\Models;

use Cookie;
use Illuminate\Database\Eloquent\Model;

class CookieModel extends Model {

	public static function setCookieArr($arr, $expire = null) {
		if (!$expire || $expire < 0) {
			$expire = null;
		} else {
			$expire = intval($expire);
		}
		foreach ($arr as $key => $value) {
			Cookie::queue($key, $value, $expire, "/", ".vronline.com");
		}
	}

	public static function clearCookieArr($arr) {
		foreach ($arr as $value) {
			Cookie::queue($value, null, -1, "/", ".vronline.com");
		}
	}

	/**
	 * 设置用户的登录信息cookie
	 * @param $uid
	 * @param $token
	 * @return Response
	 */
	public static function setLoginCookie($uid, $token, $account, $nick, $face) {
		Cookie::queue('uid', $uid, null, "/", ".vronline.com");
		Cookie::queue('token', $token, null, "/", ".vronline.com");
		Cookie::queue('account', $account, null, "/", ".vronline.com");
		Cookie::queue('nick', $nick, null, "/", ".vronline.com");
		Cookie::queue('face', $face, null, "/", ".vronline.com");
		return true;
	}

	public static function setFace($face) {
		return Cookie::queue('face', $face, 10080, "/", ".vronline.com");
	}

	/**
	 * 设置$k=>$v的cookie键值对
	 * @param $k
	 * @param $v
	 * @param $min  时效单位（分钟）
	 * @return Response
	 */
	public static function setCookie($k, $v, $min = null) {
		return Cookie::queue($k, $v, $min, "/", ".vronline.com");
	}

	/**
	 * 退出登录
	 */
	public static function logOut() {
		Cookie::queue('uid', null, -1, "/", ".vronline.com");
		Cookie::queue('token', null, -1, "/", ".vronline.com");
		Cookie::queue('account', null, -1, "/", ".vronline.com");
		Cookie::queue('nick', null, -1, "/", ".vronline.com");
		Cookie::queue('face', null, -1, "/", ".vronline.com");
		//return true;
	}

	/**
	 * 设置用户的登录信息cookie
	 * @param $uid
	 * @param $token
	 * @return Response
	 */
	public static function getCookie($key) {
		$val = Cookie::get($key);
		if (!isset($val)) {
			$val = "";
		}
		return $val;
	}

	/**
	 * 删除cookie,如果没有$k则，删除登录者的uid和token的cookie值
	 * @param $k
	 * @return Response
	 */
	public static function delCookie($k) {
		return Cookie::forget($k);
	}

    /**
     * 判断登录
     * @param   bool    strict   是否到用户中心严格判断
     * @return  array   登录信息
     */
    public static function checkLogin($strict=false)
    {
        $uid     = Cookie::get("uid");
        $token   = Cookie::get("token");
        $account = Cookie::get("account");
        $nick    = Cookie::get("nick");
        $face    = Cookie::get("face");
        $face    = $face ? $face : "";
        $nick    = $nick ? $nick : $account;
        if(!$uid || !$token || !$nick) {
            return false;
        }
        if($strict) {
        }
        return ["uid" => $uid, "token" => $token, "account" => $account, "nick" => $nick, "face" => $face];
    }

}
