<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Traits\SimpleResponse;
use App\Models\CommonModel;
use Helper\AccountCenter;
use Illuminate\Http\Request;
use Config;
use Helper\AccountCenter as Account;

class UserInfoController extends Controller {

	use SimpleResponse;

	public $accountCenter = "";

	/**
	 * 用户信息查询
	 *
	 * @return view('userinfo.search')
	 */
	public function search(Request $request) {

		$userinfo = [];

		if ($request->isMethod("POST")) {

			$url = url("userinfo");

			$type  = $request->input("type");
			$value = $request->input("value");

			$ret = $this->getUserId($value, $type, $url);

			if (!is_numeric($ret)) {
				return $ret;
			}

			$id = $ret;

			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			$result = $accountModel->getUserInfoByAdmin($id);

			if (isset($result["code"]) && $result["code"] == 0) {
				$userinfo       = $result["data"];
				$gamehistory    = CommonModel::set("GameHistory")->where("uid", $userinfo["uid"])->first();
				$gamecount      = $gamehistory ? count($gamehistory->purchasedid) : 0;
				$videohistory   = CommonModel::set("VideoHistory")->where("uid", $userinfo["uid"])->first();
				$videototaltime = $videohistory ? $videohistory->totaltime : 0;
			} else {
				return $this->errorRedirect($url, "用户不存在");
			}
		}

		return view('userinfo.search', [
			"userinfo"       => $userinfo,
			"gamecount"      => isset($gamecount) ? $gamecount : 0,
			"videototaltime" => isset($videototaltime) ? $videototaltime : 0,
		]);
	}

	/**
	 * 账号封禁
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function ban(Request $request) {
		$redirect_url = url("user/ban");

		$uids = $request->input("uids");
		if (!$uids) {
			return $this->errorRedirect($redirect_url, "缺少参数");
		}

		$uidArr = explode(";", $uids);
		foreach ($uidArr as &$uid) {
			if (!is_numeric($uid) || !$uid > 0) {
				return $this->errorRedirect($redirect_url, "uid：{$uid}错误");
			}
			$uid = (int) $uid;
		}

		$uids = join(";", $uidArr);

		$accountCenter = new AccountCenter(1, "aaa");

		$ret = $accountCenter->disableUsers("disable", $uidArr);

		if (!$ret) {
			return $this->errorRedirect($redirect_url, "封停提交失败");
		}

		if (!isset($ret["code"]) || $ret["code"] != 0) {
			$msg = isset($ret["msg"]) ? $ret["msg"] : "封停提交失败";
			return $this->errorRedirect($redirect_url, $msg);
		}

		return redirect($redirect_url)
			->with('status', "封停{$uids}成功")
			->withInput();
	}

	/**
	 * 账号解封
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function unban(Request $request) {
		$redirect_url = url("user/unban");

		$uids = $request->input("uids");
		if (!$uids) {
			return $this->errorRedirect($redirect_url, "缺少参数");
		}

		$uidArr = explode(";", $uids);
		foreach ($uidArr as &$uid) {
			foreach ($uidArr as &$uid) {
				if (!is_numeric($uid) || !$uid > 0) {
					return $this->errorRedirect($redirect_url, "uid：{$uid}错误");
				}
				$uid = (int) $uid;
			}
		}

		$uids = join(";", $uidArr);

		$accountCenter = new AccountCenter(1, "aaa");

		$ret = $accountCenter->disableUsers("enable", $uidArr);

		if (!$ret) {
			return $this->errorRedirect($redirect_url, "解封提交失败");
		}

		if (!isset($ret["code"]) || $ret["code"] != 0) {
			$msg = isset($ret["msg"]) ? $ret["msg"] : "解封提交失败";
			return $this->errorRedirect($redirect_url, $msg);
		}

		return redirect($redirect_url)
			->with('status', "解封{$uids}成功")
			->withInput();
	}

	/**
	 * 用户游戏查询
	 *
	 * @return [type] [description]
	 */
	public function userGame(Request $request) {

		if ($request->isMethod("POST")) {

			$gameArr      = [];
			$redirect_url = url("user/game");

			$type  = $request->input("type");
			$value = $request->input("value");

			$ret = $this->getUserId($value, $type, $redirect_url);

			if (!is_numeric($ret)) {
				return $ret;
			}

			$id = $ret;

			if (!$this->accountCenter) {
				$this->accountCenter = new AccountCenter(1, "aaa");
			}

			$result = $this->accountCenter->getUserInfoByAdmin($id);

			if (!isset($result["code"]) || $result["code"] != 0) {

				return $this->errorRedirect($redirect_url, "用户不存在");

			}

			$gHistory = CommonModel::set("GameHistory")->where("uid", $id)->first();

			$gameIds = $gHistory->purchasedid;

			if ($gameIds && is_array($gameIds)) {

				$games = CommonModel::set("Game")->whereIn('gid', $gameIds)->get();

				foreach ($games as $game) {
					$gameArr[$game->gid] = $game;
				}

				$cookiesgids = $gHistory->cookiesgid;
				$totaltime   = $gHistory->cookiespretime;
				$buytime     = $gHistory->cookiesgtime;

				if ($cookiesgids && is_array($cookiesgids)) {
					foreach ($cookiesgids as $key => $gid) {
						$gameArr[$gid]->totaltime = isset($totaltime[$key]) ? $totaltime[$key] : "0";
						$gameArr[$gid]->buytime   = isset($buytime[$key]) ? $buytime[$key] : "0";
					}
				}
				//var_dump($gameArr);exit;
			}

			return view('userinfo.game', [
				"gameArr" => $gameArr,
			]);
		}

		return view('userinfo.game');
	}

	/**
	 * 根据类型获取用户信息
	 *
	 * @param  [type] $value [description]
	 * @param  [type] $type  [description]
	 * @return [type]        [description]
	 */
	public function getUserId($value, $type, $redirect_url) {

		$url = url("userinfo");

		if (!in_array($type, [1, 2, 3])) {
			return $this->errorRedirect($redirect_url, "搜索类型错误");
		}

		if (!$value) {
			return $this->errorRedirect($redirect_url, "请填写搜索内容");
		}

		if (!$this->accountCenter) {
			$this->accountCenter = new AccountCenter(1, "aaa");
		}

		switch ($type) {

		case 2:
			$id = (int) $value;

			if ($id < 0) {
				return $this->errorRedirect($redirect_url, "UID错误");
			}

			break;

		default:

			$result = $this->accountCenter->getUidByAdmin($value);

			if (!isset($result["code"]) || $result["code"] != 0) {
				return $this->errorRedirect($redirect_url, "用户不存在");
			}

			$id = $result["data"]["uid"];

			break;
		}

		return $id;

	}
}
