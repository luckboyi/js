<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\GameModel;
use App\Models\ToBDBModel;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Illuminate\Http\Request;

class ToBController extends Controller {
	public function __construct() {
		$this->middleware("vrauth:jump:admincp", ['only' => ["index", "merchants", "defaultGame", "other", "banner", "extractCashConfirm", "extractCash", "payExtract"]]);
	}

	public function index(Request $request) {
		return redirect('/tob/merchants', 302, [], true);
	}

	public function merchants(Request $request) {
		$userInfo = $request->userinfo;
		$status = $request->input("choose", -1);
		$statusCfg = Config::get("category.tob_status");
		if (!array_key_exists($status, $statusCfg)) {
			$status = -1;
		}

		$ToBDBModel = new ToBDBModel();
		$data = $ToBDBModel->getMerchantsByStatus($status);

		return view('admincp.tob.merchants', ['cur' => 'tob', 'user' => $userInfo, 'path' => 'merchants', 'data' => $data, 'status' => $status]);
	}

	public function defaultGame(Request $request) {
		$userInfo = $request->userinfo;

		$choose = $request->input('choose');
		$choose = intval($choose);

		$searchText = trim($request->input('search'));
		$searchText = $searchText ? $searchText : '';
		$gameModel = new GameModel();
		$data = $gameModel->gameOnlieByPage(1, $searchText, $choose);
		return view('admincp.tob.defaultgame', ['cur' => 'tob', 'user' => $userInfo, 'path' => 'defaultgame', 'data' => $data, 'choose' => $choose, 'searchText' => $searchText]);
	}

	public function other(Request $request) {
		$userInfo = $request->userinfo;
		$ToBDBModel = new ToBDBModel();
		$defaultProduct = $ToBDBModel->getDefaultProduct();
		return view('admincp.tob.other', ['cur' => 'tob', 'user' => $userInfo, 'path' => 'other', 'product' => $defaultProduct]);
	}

	public function banner(Request $request) {
		$userInfo = $request->userinfo;
		$ToBDBModel = new ToBDBModel();
		$banners = $ToBDBModel->getWwwBanners();
		return view('admincp.tob.banner', ['cur' => 'tob', 'user' => $userInfo, 'path' => 'banner', 'banners' => $banners]);
	}

	public function switchBannerWeight(Request $request) {
		$dragId = intval($request->input('drag'));
		$dropId = intval($request->input('drop'));

		$ToBDBModel = new ToBDBModel;
		$ret = $ToBDBModel->wwwBannerWeight($dragId, $dropId);
		if ($ret) {
			return Library::output(0);
		} else {
			return Library::output(1);
		}
	}

	public function extractCashConfirm(Request $request) {
		$userInfo = $request->userinfo;
		$choose = $request->input('choose', -1);
		$choose = intval($choose);
		$page = $request->input('page', 1);
		$searchText = trim($request->input('search'));
		$searchText = $searchText ? $searchText : '';

		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");
		$paykey = Config::get("common.uc_paykey");
		$AC = new AccountCenter($appid, $appkey, $paykey);
		$result = $AC->extractCashLog("", $choose, $page, true);

		$pageShow = str_replace(
			['api.vronline.com/2b/extractCashLog', '?'], ['admincp.vronline.com/tob/confirm', '?choose=' . $choose . '&'], $result['data']['show']);

		$data = [];
		$error = false;
		if (!$result || !is_array($result) || $result["code"] != 0) {
			$error = "获取数据失败";
		} else {
			$data = $result["data"];
		}
		$rows = $data["rows"] ?: [];
		return view('admincp.tob.confirm', ['cur' => 'tob', 'user' => $userInfo, 'path' => 'confirm', 'choose' => $choose, 'searchText' => $searchText, "error" => $error, "rows" => $rows, "pageview" => $pageShow, 'num' => $data["num"]]);
	}

	/**
	 * 提现审核确认接口
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function extractCash(Request $request) {
		$userInfo = $request->userinfo;
		$adminuid = $userInfo["uid"];

		$orderid = $request->input('order_id');
		$type = $request->input('tp');

		$typeArr = [
			0 => "deny",
			1 => "pass",
		];

		if (!key_exists($type, $typeArr) || !$orderid) {
			return Library::output(1);
		}
		$type = $typeArr[$type];

		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");
		$paykey = Config::get("common.uc_paykey");

		$AC = new AccountCenter($appid, $appkey, $paykey);
		$result = $AC->extractCashConfirm($orderid, $adminuid, $type);

		if (!$result || !is_array($result) || $result["code"] != 0) {
			return Library::output(1);
		}
		return Library::output(0);
	}

	/**
	 * 为审核通过的付款
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function payExtract(Request $request) {
		$userInfo = $request->userinfo;
		$adminuid = $userInfo["uid"];

		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");
		$paykey = Config::get("common.uc_paykey");

		$AC = new AccountCenter($appid, $appkey, $paykey);
		$result = $AC->pay4Merchant($adminuid);

		if (!$result || !is_array($result) || $result["code"] != 0) {
			return Library::output(1);
		}
		return Library::output(0);
	}
}
