<?php

namespace App\Models;

use DB;
use Helper\UdpLog;
use App\Models\OpenidModel;
use Illuminate\Database\Eloquent\Model;

class AppinfoModel extends Model
{
	private $key_whitelist_prefix = "key_redis_whitelist_";
	/**
	 * 获取app信息
	 * @param   int     appid   appid
	 * @return  array   appinfo
	 */
	public function info($appid)
    {
		$appid = intval($appid);
		if ($appid <= 0) {
			return false;
		}
		$result = array();
		try {
			$info = DB::connection("db_appinfo")->table('t_appinfo')->where("appid", $appid)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		if (!is_array($info)) {
			return false;
		}
		return $info;
	}

	/**
	 * 设置app信息
	 * @param   int     appid   appid
	 * @return  bool    true or false
	 */
	public function set($appid, $info, $appkey, $paykey)
    {
		if (!$appid || !$info || !is_array($info)) {
			return false;
		}
		if (isset($info['appkey'])) {
			unset($info['appkey']);
		}
		if (isset($info['paykey'])) {
			unset($info['paykey']);
		}
		$info['ltime'] = date("Y-m-d H:i:s");
		try {
			$app = DB::connection("db_appinfo")->table('t_appinfo')->where("appid", $appid)->first();
			if (!$app) {
				$info['appid'] = $appid;
				$info['appkey'] = $appkey;
				$info['paykey'] = $paykey;

				$ret = DB::connection("db_appinfo")->table('t_appinfo')->insert($info);
			} else {
				if (isset($info['appid'])) {
					unset($info['appid']);
				}
				$ret = DB::connection("db_appinfo")->table('t_appinfo')->where("appid", $appid)->update($info);
			}
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 获取指定服的发货地址
	 * 缓存用redis的set
	 * @param   int     appid   appid
	 * @return  int     serverid
	 * @return  array   appinfo
	 */
	public function getPayUrlByServerid($appid, $serverid)
    {
		if (!$appid || !$serverid) {
			return false;
		}
		try {
			$payurl = DB::connection("db_appinfo")->table('t_payurl')->where(array("appid" => $appid, "serverid" => $serverid))->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $payurl;
	}

	/**
	 * 获取指定游戏的所有发货地址
	 * 缓存用redis的set
	 * @param   int     appid   appid
	 * @return  int     serverid
	 * @return  array   appinfo
	 */
	public function getPayUrlByAppid($appid)
    {
		if (!$appid) {
			return false;
		}
		try {
			$info = DB::connection("db_appinfo")->table('t_payurl')->where("appid", $appid)->get();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		if (!is_array($info)) {
			return false;
		}
		return $info;
	}

	/**
	 * 设置某个服的发货地址，有修改，没有插入
	 * @param   int     appid   appid
	 * @return  int     serverid
	 * @return  array   appinfo
	 */
	public function setOnePayUrl($appid, $serverid, $info)
    {
		if (!$appid || !$serverid || !$info) {
			return false;
		}
		$info['ltime'] = date("Y-m-d H:i:s");
		try {
			$payurl = DB::connection("db_appinfo")->table('t_payurl')->where(array("appid" => $appid, "serverid" => $serverid))->first();
			if (!$payurl) {
				$info['appid'] = $appid;
				$info['serverid'] = $serverid;
				$ret = DB::connection("db_appinfo")->table('t_payurl')->insert($info);
			} else {
				if (isset($info['appid'])) {
					unset($info['appid']);
				}

				if (isset($info['serverid'])) {
					unset($info['serverid']);
				}

				$ret = DB::connection("db_appinfo")->table('t_payurl')->where(array("appid" => $appid, "serverid" => $serverid))->update($info);
			}
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 是否在白名单
	 * @param   int     appid
	 * @param   string  openid
	 * @return  bool
	 */
	public function inWhiteList($appid, $openid)
    {
		if (!$appid || !$openid) {
			return false;
		}
        $check_openid = OpenidModel::getUid($openid);
        if(!$check_openid || !isset($check_openid['uid']) || !$check_openid['uid']) {
            return false;
        }
        $uid = intval($check_openid['uid']);
        if($uid >= 101 && $uid <= 120) {
            return true;
        }
        return false;
	}

	/**
	 * 添加白名单
	 * @param   int     appid
	 * @param   string  openid
	 * @return  bool
	 */
	public function addWhiteList($appid, $openid)
    {
        return true;
	}

}
