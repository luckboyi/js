<?php

namespace App\Models;

// 引用Model
use Helper\HttpRequest;
use Illuminate\Database\Eloquent\Model;

class ImModel extends Model {
	private $imApiHost = "http://192.168.74.48";

	/**
	 * 签名校验
	 */
	public function getImToken($uid) {
		$uid = intval($uid);
		if (!$uid) {
			return false;
		}
		$params = ["userid" => $uid];
		$json = json_encode($params);
		$url = $this->imApiHost . "/kca/login/generate_auth_secret";
		$ret = HttpRequest::post($url, $json);
		if (!$ret) {
			return false;
		}
		$info = json_decode($ret, true);
		if (!$info) {
			return false;
		}
		return $info;
	}

	/**
	 * 2b系统消息通知
	 * @param   int     fromuid     发通知的来源用户id
	 * @param   array   touids      接收通知的用户id数组
	 * @param   array   params
	$params = [
	"sender" => ["userid" => $uid],
	"usermsg" => ["major_category" => 2, "minor_category" => 2, "text_type" => 1, "content" => "this is test text2"],
	"target" => ["type" => 2, "userids" => [12]],
	"valid_time" => ["time_start" => 0, "duration" => 0],
	"offline" => ["offline_msg" => true],
	];
	 */
	public function sysNotify2b($sender, $receivers, $msg = "", $params = array()) {
		$sender = intval($sender);
		if (!$sender || !$receivers || !is_array($receivers)) {
			return false;
		}
		for ($i = 0; $i < count($receivers); $i++) {
			$receivers[$i] = intval($receivers[$i]);
		}
		$params['sender'] = ["userid" => $sender];
		$params['usermsg'] = isset($params['usermsg']) ? $params['usermsg'] : ["major_category" => 2, "minor_category" => 2, "text_type" => 1, "content" => $msg];
		$params['target'] = isset($params['target']) ? $params['target'] : ["type" => 2, "userids" => $receivers];
		$params['valid_time'] = isset($params['valid_time']) ? $params['valid_time'] : ["time_start" => 0, "duration" => 0];
		$params['offline'] = isset($params['offline']) ? $params['offline'] : ["offline_msg" => true];

		$info = $this->notify($params);
		if (isset($info['errcode']) && $info['errcode'] == 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 消息通知
	 * @param   int     fromuid     发通知的来源用户id
	 * @param   array   touids      接收通知的用户id数组
	 */
	public function notify($params) {
		if (!$params || !is_array($params)) {
			return false;
		}
		$json = json_encode($params);
        if(date("Y-m-d H:i:s") < "2017-03-27") {
            error_log("[".date("Y-m-d H:i:s")."] ".$json."\n\n", 3, "/tmp/im_notify.log");
        }
		$url = $this->imApiHost . "/kca/push/pushusermsg";
		$ret = HttpRequest::post($url, $json);
		if (!$ret) {
			return false;
		}
		$info = json_decode($ret, true);
		if (!$info) {
			return false;
		}
		return $info;
	}

	/**
	 * 获取终端状态
	 * @param   array   terminals     发通知的来源用户id
	 * @param   array   touids      接收通知的用户id数组
	 */
	public function terminalStat($params) {
		if (!$params || !is_array($params)) {
			return false;
		}
		$arr = ['userids' => []];
		foreach ($params as $value) {
			$arr['userids'][] = $value['terminal_id'];
		}
		if (empty($arr['userids'])) {
			return false;
		}
		$json = json_encode($arr);

		$url = $this->imApiHost . "/kca/oss/get_user_onlinestatus";
		$ret = HttpRequest::post($url, $json);
		if (!$ret) {
			return false;
		}

		$info = json_decode($ret, true);
		if (!$info || !isset($info['errcode']) || $info['errcode'] != 0) {
			return false;
		}
		$stats = [];
		foreach ($info['users'] as $user) {
			$stats[$user['userid']] = $user['online_status'];
		}

		return $stats;
	}

}
