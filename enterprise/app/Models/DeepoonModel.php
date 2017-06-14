<?php

/*
大朋活动model
date:2017/1/33
 */

namespace App\Models;

use Config;
use Helper\AccountCenter;
use Helper\UdpLog;
use Helper\HttpRequest;
use Helper\Library;
use App\Models\OpenidModel;
use Illuminate\Database\Eloquent\Model;

class DeepoonModel extends Model {

    /**
     * 大朋在VRonline上的appid
     */
    public $deepoonAppid = 1000262;

    /**
     * VRonline在大朋上的appid
     */
    public $vronlineAppid = "{ECAF17-8DA2-44AA-9F15-C9D7B28A}";

    /**
     * VRonline在大朋上的secret
     */
    public $vronlineSecret = "H8AaDgUKhzL5QgtBqOG4jiVgC0WnWd1r";

    /**
     * 大朋签名
     */
	public function deepoonSign($params) {
        if (!$params || !is_array($params)) {
            return false;
        }
        $nowstamp = $params['timestamp'];
        ksort($params);

        $query1 = $request = array();
        foreach ($params as $key => $value) {
            if ($value === "" || $key == "appid" || $key == "timestamp" || $key == "sign") {
                continue;
            }
            array_push($query1, $key . "=" . $value);
        }
        $query_string    = join("&", $query1);
        $sign            = md5($nowstamp.$this->vronlineSecret . md5($query_string));
        return $sign;
	}

    /**
     * 大朋签名
     */
	public function deepoonSign2($data, $ts) {
		ksort($data);
		$arr = [];
		foreach ($data as $field => $val)
		{
			if ($val === '')
			{
				continue;
			}
			$enbuf = urlencode((string) $val);
			$arr[] = sprintf('%s=%s', $field, $enbuf);
		}
		$buffer  = md5(implode('&', $arr));
		$strTemp = sprintf('%d%s%s', $ts,	$this->vronlineSecret, $buffer);
		return md5($strTemp);
	}

    /**
     * 大朋报名
     */
	public function join($uid, $name, $phone, $group, $city, $device, $software) {
        if(!$uid) {
            return false;
        }
        $account = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $uinfo = $account->getUserInfoByAdmin($uid);
        if(!is_array($uinfo) || !isset($uinfo['code']) || $uinfo['code'] != 0) {
            return false;
        }
        $boboid = isset($uinfo['data']['boboid']) ? $uinfo['data']['boboid'] : "";
        if($boboid) {
            $boboid = str_replace("bobo:", "", $boboid);
        }

        $url = "http://api.vrbig.com/cli/open/race/join";

        if($boboid) {
            $source = "deepoon";
            $openid = $boboid;
        }else {
            $source = "vronline";
            $openid = OpenidModel::getOpenid($this->deepoonAppid, $uid);
        }

        $nowstamp = time();
        $params = ["openid" => $openid, "source" => $source, "name" => $name, "phone" => $phone, "group" => $group, "city" => $city, "device" => $device, "software" => $software];
        $sign = $this->deepoonSign2($params, $nowstamp);
        $params['sign'] = $sign;
        $params['appid'] = $this->vronlineAppid;
        $params['timestamp'] = $nowstamp;

        HttpRequest::setTimeout(2, 5);
        $resStr = HttpRequest::post($url, $params);
        $res    = json_decode($resStr, true);
        if(is_array($res) && isset($res['code']) && $res['code'] == 100) {
            $boboid = $res['info']['openid'];
            $joinret = $account->bindThridAccount($uid, "", $boboid, "bobo", 0);
            return true;
        }
        if (!$res || !isset($res['code']) || $res['code'] != 0 || !isset($res['info']['openid'])) {
            return false;
        }
        $boboopenid = $res['info']['openid'];
        if($boboid && $boboopenid != $boboid) {
            return false;
        }
        if($boboopenid) {
            $joinret = $account->bindThridAccount($uid, "", $boboopenid, "bobo", 0);
            if(!is_array($joinret) || !isset($joinret['code']) || $joinret['code'] != 0) {
                return false;
            }
        }

		return true;
	}

    /**
     * 查询参赛状态
     */
	public function joinStat($uid) {
        if(!$uid) {
            return false;
        }

        $account = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $uinfo = $account->getUserInfoByAdmin($uid);
        if(!is_array($uinfo) || !isset($uinfo['code']) || $uinfo['code'] != 0) {
            return false;
        }
        $boboid = $uinfo['data']['boboid'];
        if($boboid) {
            $boboid = str_replace("bobo:", "", $boboid);
        }
        if(!$boboid) {
            return "none";
        }

        $url = "http://api.vrbig.com/cli/open/race/stat";

        $params = ["openid" => $boboid, "appid" => $this->vronlineAppid, "timestamp" => time()];
        $sign = $this->deepoonSign($params);
        $params['sign'] = $sign;
        HttpRequest::setTimeout(2, 5);
        $resStr = HttpRequest::post($url, $params);
        $res    = json_decode($resStr, true);
        if (!$res || !isset($res['code']) || $res['code'] != 0) {
            return false;
        }
        if(!isset($res['info']['stat'])) {
            return "none";
        }
        $stat = $res['info']['stat'];
        switch($stat) {
            case 1:
                return "none";
            case 2:
                return "join";
            case 3:
                return "done";
            default:    return false;
        }
    }
    /**
     * 视频资料上传
     */
	public function addVideo($uid, $title, $content, $group, $img, $imgv, $chl, $videourl) {
        if(!$uid) {
            return false;
        }
        $account = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $uinfo = $account->getUserInfoByAdmin($uid);
        if(!is_array($uinfo) || !isset($uinfo['code']) || $uinfo['code'] != 0) {
            return false;
        }
        $boboid = $uinfo['data']['boboid'];
        if($boboid) {
            $boboid = str_replace("bobo:", "", $boboid);
        }
        if(!$boboid) {
            return false;
        }

        $url = "http://api.vrbig.com/cli/open/race/add";

        $nowstamp = time();
        $params = ["openid" => $boboid, "title" => $title, "content" => $content, 'group' => $group, "img" => $img, "imgv" => $imgv, "chl" => $chl, "url" => $videourl, "file" => basename($videourl)];
        $sign = $this->deepoonSign2($params, $nowstamp);
        $params['appid'] = $this->vronlineAppid;
        $params['timestamp'] = $nowstamp;
        $params['sign'] = $sign;

        HttpRequest::setTimeout(2, 5);
        $resStr = HttpRequest::post($url, $params);
        $res    = json_decode($resStr, true);
        if (!$res || !isset($res['code']) || $res['code'] != 0) {
            return false;
        }
        return true;
	}

    /**
     * 视频资料上传
     */
	public function videoList($uid, $page=1, $size=100) {
        if(!$uid) {
            return false;
        }

        $account = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $uinfo = $account->getUserInfoByAdmin($uid);
        if(!is_array($uinfo) || !isset($uinfo['code']) || $uinfo['code'] != 0) {
            return false;
        }
        $boboid = $uinfo['data']['boboid'];
        if($boboid) {
            $boboid = str_replace("bobo:", "", $boboid);
        }
        if(!$boboid) {
            return ["list" => [], "total" => 0];
        }

        $url = "http://api.vrbig.com/cli/open/race/list";

        $params = ["openid" => $boboid, "appid" => $this->vronlineAppid, "timestamp" => time(), "page" => $page, "size" => $size];
        $sign = $this->deepoonSign($params);
        $params['sign'] = $sign;
        HttpRequest::setTimeout(2, 5);
        $resStr = HttpRequest::post($url, $params);
        $res    = json_decode($resStr, true);
        if (!$res || !isset($res['code']) || $res['code'] != 0) {
            return false;
        }
        return $res['info'];
	}

}
