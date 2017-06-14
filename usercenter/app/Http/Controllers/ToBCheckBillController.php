<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

// 引用Model
use App\Models\PassportModel;
use App\Models\ToBCheckBillDBModel;
use App\Models\ToBCheckBillModel;
use App\Models\UserModel;
use App\Models\VerifyCodeModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class ToBCheckBillController extends Controller {

	/**
	 * 生成paytoken
	 */
	public function getHeepayCheckBill() {
		//$nowstamp = strtotime("2017-02-27");
		$nowstamp          = time();
		$toBCheckBillModel = new ToBCheckBillModel;
		//$row = $toBCheckBillModel->getHeepayCheckBill($nowstamp);
		//$row = $toBCheckBillModel->getHeepayCheckRefundBill($nowstamp);
		$row = $toBCheckBillModel->addHeepayCheckBill($nowstamp);
		echo "<pre><font color=''> addHeepayCheckBill ==> ";
		var_dump($row);
		echo "</font></pre>";
		$row = $toBCheckBillModel->check2BBill($nowstamp);
		echo "<pre><font color=''> check2BBill ==> ";
		var_dump($row);
		echo "</font></pre>";
		$row = $toBCheckBillModel->checkUCenterBill($nowstamp);
		echo "<pre><font color=''> checkUCenterBill ==> ";
		var_dump($row);
		echo "</font></pre>";
		$row = $toBCheckBillModel->settlement($nowstamp);
		echo "<pre><font color=''> settlement ==> ";
		var_dump($row);
		echo "</font></pre>";
	}

	/**
	 * 判断是否有支付密码
	 * @param   int     merchantid  商户ID
	 * @param   string  paypwd  支付密码
	 */
	public function hasPaypwd(Request $request) {
		$merchantid = $request->input("merchantid", "");
		$token      = $request->input("token", "");
		if (!$merchantid || !$token) {
			return Library::output(2001);
		}
		$passport = new PassportModel;
		$login    = $passport->isLoginTurbo($merchantid, "login_token", $token);
		if (!$login) {
			return Library::output(1301);
		}

		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$paypwd              = $toBCheckBillDBModel->getCashPwd($merchantid);
		if (!$paypwd || strlen($paypwd) != 32) {
			return Library::output(404, null, "未设置");
		}
		return Library::output(0);
	}

	/**
	 * 判断是否有支付密码
	 * @param   int     merchantid  商户ID
	 * @param   string  paypwd  支付密码
	 */
	public function setPaypwd(Request $request) {
		$merchantid = $request->input("merchantid", "");
		$token      = $request->input("token", "");
		$code       = $request->input("code", "");
		$oldpwd     = $request->input("oldpwd", "");
		$newpwd     = $request->input("newpwd", "");
		if (!$merchantid || !$token || !$newpwd || strlen($newpwd) != 32) {
			return Library::output(2001);
		}

		$passport = new PassportModel;
		$login    = $passport->isLoginTurbo($merchantid, "login_token", $token);
		if (!$login) {
			return Library::output(1301);
		}

		$toBCheckBillDBModel = new ToBCheckBillDBModel;

		$user = new UserModel;
		$base = $user->baseInfo($merchantid);
		if (!$base || !is_array($base)) {
			return Library::output(1);
		}
		if (!$base['f_mobile']) {
			return Library::output(2105);
		}

		$verify     = new VerifyCodeModel;
		$verifyCode = $verify->getVerifyCode($merchantid, $base['f_mobile'], "mobile", 'set_cash_pwd');
		if ($code != $verifyCode) {
			return Library::output(2005);
		}

		$verify->delCode($merchantid, $base['f_mobile'], "mobile", 'set_cash_pwd');
		$paypwd = $toBCheckBillDBModel->getCashPwd($merchantid);

		/**
		 * 未设置密码，添加密码
		 */
		$newpwd = md5($newpwd);
		if ($paypwd === null) {
			if ($oldpwd) {
				return Library::output(2013);
			}
			$ret = $toBCheckBillDBModel->newBalanceInfo($merchantid, ["pay_pwd" => $newpwd]);
		} else {
			if ($paypwd) {
				if (!$oldpwd || md5($oldpwd) != $paypwd) {
					return Library::output(2013);
				}
			}
			$ret = $toBCheckBillDBModel->updCashPwd($merchantid, $newpwd);
		}
		if (!$ret) {
			return Library::output(1, null, "密码设置失败");
		}
		return Library::output(0);
	}

	/**
	 * 支付密码登录
	 * @param   int     merchantid  商户ID
	 * @param   string  paypwd  支付密码
	 */
	public function loginPaypwd(Request $request) {
		$merchantid = $request->input("merchantid", "");
		$token      = $request->input("token", "");
		$paypwd     = $request->input("paypwd", "");
		if (!$merchantid || !$token || !$paypwd || strlen($paypwd) != 32) {
			return Library::output(2001);
		}

		$passport = new PassportModel;
		$login    = $passport->isLoginTurbo($merchantid, "login_token", $token);
		if (!$login) {
			return Library::output(1301);
		}

		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$checkpwd            = $toBCheckBillDBModel->getCashPwd($merchantid);
		if (!$checkpwd || $checkpwd != md5($paypwd)) {
			return Library::output(1303);
		}

		$passport  = new PassportModel;
		$cashtoken = $passport->genToken($merchantid, "2bcash_token");
		return Library::output(0, ['cashtoken' => $cashtoken]);
	}

	/**
	 * 2b版本商家余额
	 * @param   int     merchantid  商户ID
	 * @param   string  paytoken2b  支付密码登录token
	 * @return  array   余额
	 */
	public function get2bBalance(Request $request) {
		$merchantid = $request->input("merchantid", "");
		$token      = $request->input("token", "");
		//$cashtoken = $request->input("cashtoken", "");
		if (!$merchantid || !$token) {
			return Library::output(2001);
		}

		$passport = new PassportModel;
		$login    = $passport->isLoginTurbo($merchantid, "login_token", $token);
		if (!$login) {
			return Library::output(1301);
		}
		// $login = $passport->isLoginTurbo($merchantid, "2bcash_token", $cashtoken);
		// if (!$login) {
		//     return Library::output(1301, null, "请先输入取款密码");
		// }

		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$balance             = $toBCheckBillDBModel->getBalance($merchantid);
		if (!$balance || !is_array($balance)) {
			return Library::output(1, null, "查询失败");
		}
		$info = ["merchantid" => $balance['merchantid'], "total_income" => $balance['total_income'], "net_income" => $balance['net_income'], "new_net_income" => $balance['new_net_income']];
		return Library::output(0, $info);
	}

	/**
	 * 获取银行卡列表
	 * @param  int   merchantid   商户号
	 * @return  json
	 */
	public function get2bCards(Request $request) {
		$merchantId          = trim($request->input('merchantid'));
		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$row                 = $toBCheckBillDBModel->get2bBankCards($merchantId);
		return Library::output(0, $row);
	}

	/**
	 * 获取银行卡信息
	 * @param  int   merchantid   商户号
	 * @param  int   card_id  银行卡id
	 * @return  json
	 */
	public function get2bCard(Request $request) {
		$merchantId = trim($request->input('merchantid'));
		$card_id    = trim($request->input('card_id'));
		if (!$merchantId || !$card_id) {
			return Library::output(1);
		}
		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$row                 = $toBCheckBillDBModel->get2bBankCard($merchantId, $card_id);
		if (!$row) {
			return Library::output(1);
		} else {
			return Library::output(0, $row);
		}
	}

	/**
	 * 检查银行卡信息
	 * @param  int   merchantid   商户号
	 * @param  int   card_no  银行卡号
	 * @param  int   card_id  银行卡id
	 * @return  json
	 */
	public function check2bCard(Request $request) {
		$merchantId = trim($request->input('merchantid'));
		$card_no    = trim($request->input('card_no'));
		$card_id    = trim($request->input('card_id'));
		if (!$merchantId || !$card_no) {
			return Library::output(1);
		}
		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$row                 = $toBCheckBillDBModel->check2bBankCard($card_no, $card_id);
		if ($row) {
			return Library::output(1);
		} else {
			return Library::output(0);
		}
	}

	/**
	 * 添加银行卡
	 * @param   int  merchantid   银行卡号
	 * @param   string  card_name   姓名
	 * @param   string  card_no   银行卡号
	 * @param   string  card_province   银行卡号
	 * @param   string  card_city   银行卡号
	 * @param   string  card_opener   验证码
	 * @param   string  card_bank_name  银行名称
	 * @param   string  card_type_name  卡类型
	 * @param   int  card_pay_num 开户行名称
	 * @return  json
	 */
	public function save2bCard(Request $request) {
		$merchantId     = trim($request->input('merchantid'));
		$card_name      = trim($request->input('card_name'));
		$card_no        = trim($request->input('card_no'));
		$card_province  = trim($request->input('card_province'));
		$card_city      = trim($request->input('card_city'));
		$card_opener    = trim($request->input('card_opener'));
		$card_bank_name = trim($request->input('card_bank_name'));
		$card_type_name = trim($request->input('card_type_name'));
		$card_pay_num   = trim($request->input('card_pay_num'));
		$card_owner     = intval($request->input('card_owner'));
		if (!$merchantId) {
			return Library::output(1);
		}

		$toBCheckBillDBModel = new ToBCheckBillDBModel;

		$cardInfo = $toBCheckBillDBModel->check2bBankCard($card_no);
		if ($cardInfo) {
			return Library::output(1);
		}
		if (!$card_name || !$card_no || !$card_province || !$card_city || !$card_opener || !$card_bank_name || !$card_pay_num) {
			return Library::output(1);
		}
		if (!in_array($card_owner, [0, 1])) {
			return Library::output(1);
		}
		$info = ['merchantid' => $merchantId, 'card_name' => $card_name, 'card_no' => $card_no, 'card_province' => $card_province, 'card_city' => $card_city, 'card_opener' => $card_opener, 'card_bank_name' => $card_bank_name, 'card_type_name' => $card_type_name, 'card_pay_num' => $card_pay_num, "card_owner" => $card_owner];
		$ret  = $toBCheckBillDBModel->save2bBankCard($info);

		if ($ret) {
			return Library::output(0);
		} else {
			return Library::output(1);
		}
	}

	/**
	 * 设置默认银行卡
	 * @param   string  merchantid   商户id
	 * @param   string  card_id   银行卡号
	 * @return  json
	 */
	public function default2bCard(Request $request) {
		$merchantId = trim($request->input('merchantid'));
		$card_id    = intval($request->input('card_id'));
		if (!$merchantId || !$card_id) {
			return Library::output(1);
		}

		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$row                 = $toBCheckBillDBModel->get2bBankCard($merchantId, $card_id);
		if (!$row) {
			return Library::output(1);
		}
		$ret = $toBCheckBillDBModel->defaultCard($merchantId, $card_id);
		if ($ret) {
			return Library::output(0);
		} else {
			return Library::output(1);
		}
	}

	/**
	 * 删除银行卡
	 * @param   string  card_id   银行卡号
	 * @param   string  merchantid   验证码
	 * @return  json
	 */
	public function del2bCard(Request $request) {
		$merchantId = trim($request->input('merchantid'));
		$card_id    = intval($request->input('card_id'));
		if (!$merchantId) {
			return Library::output(1);
		}

		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$row                 = $toBCheckBillDBModel->get2bBankCard($merchantId, $card_id);
		if (!$row) {
			return Library::output(1);
		}
		if ($row['card_stat'] == 1) {
			return Library::output(1);
		}
		$ret = $toBCheckBillDBModel->del2bBankCard($merchantId, $card_id);
		if ($ret) {
			return Library::output(0);
		} else {
			return Library::output(1);
		}
	}

	/**
	 * 2b版本提取现金
	 * @param   int     merchantid  商户ID
	 * @param   string  token  登录token
	 * @param   int  card_id  银行卡ID
	 * @param   string  pwd  取款密码
	 * @param   string  cash  提取金额cash
	 * @param   string  code  验证码
	 * @return  array
	 */
	public function extractBankCash(Request $request) {
		$merchantId = trim($request->input('merchantid'));
		$card_id    = intval($request->input('card_id'));
		$pwd        = trim($request->input('pwd'));
		$cash       = trim($request->input('cash'));
		$code       = trim($request->input('code'));
		if (!$merchantId || !$card_id || !$cash || !$pwd || !$code) {
			return Library::output(2001);
		}

		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$cardInfo            = $toBCheckBillDBModel->get2bBankCard($merchantId, $card_id);
		if (!$cardInfo) {
			return Library::output(2002);
		}

		$user = new UserModel;
		$base = $user->baseInfo($merchantId);
		if (!$base || !is_array($base)) {
			return Library::output(1111);
		}
		if (!$base['f_mobile']) {
			return Library::output(2105);
		}

		$balanceInfo = $toBCheckBillDBModel->getBalanceInfo($merchantId);
		$paypwd      = $balanceInfo['pay_pwd'] ?? null;

		if ($paypwd != md5($pwd)) {
			return Library::output(1303);
		}

		$canExtractCash = $balanceInfo["net_income"] ?? 0;
		if ($cash > $canExtractCash) {
			return Library::output(2401);
		}

		$verify     = new VerifyCodeModel;
		$verifyCode = $verify->getVerifyCode($merchantId, $base['f_mobile'], "mobile", 'extract_cash_msg' . $card_id);
		if ($code != $verifyCode) {
			return Library::output(2005);
		}
		$verify->delCode($merchantId, $base['f_mobile'], "mobile", 'extract_cash_msg' . $card_id);

		$orderId = $this->genOrderId($merchantId);
		$info    = ['orderid' => $orderId, 'merchantid' => $merchantId, 'cash' => $cash, 'card_name' => $cardInfo['card_name'], 'card_no' => $cardInfo['card_no'], 'card_province' => $cardInfo['card_province'], 'card_city' => $cardInfo['card_city'], 'card_opener' => $cardInfo['card_opener'], 'card_pay_no' => $cardInfo['card_pay_num'], 'card_owner' => $cardInfo['card_owner']];
		$ret     = $toBCheckBillDBModel->addExtractOrder($info);
		if ($ret) {
			return Library::output(0, ['orderid' => $orderId]);
		} else {
			return Library::output(1);
		}
	}

	/**
	 * 2b版本提取现金确认
	 * @param   string  orderid  订单id
	 * @param   string  type        pass/deny
	 * @return  array   余额
	 */
	public function extractCashConfirm(Request $request) {
		$orderid  = trim($request->input('orderid'));
		$adminuid = trim($request->input('adminuid'));
		$type     = trim($request->input('type'));
		$time     = trim($request->input('ts'));
		$sign     = trim($request->input('sign'));
		if (!$orderid || !$adminuid || !$type || !$time || !$sign) {
			return Library::output(2001);
		}

		if (isset($_GET['//2b/extractCashConfirm'])) {
			unset($_GET['//2b/extractCashConfirm']);
		}

		$check_sign = Library::encrypt($_GET, Config::get("common.uc_paykey"));
		if ($check_sign != $sign) {
			return Library::output(2002);
		}
		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$status              = 0;
		if ($type == "pass") {
			$status = 5;
		} else if ($type == "deny") {
			$status = 4;
		} else {
			return Library::output(2001);
		}
		$row = $toBCheckBillDBModel->getOneExtractOrder($orderid);
		if (!$row) {
			return Library::output(1, "订单查询失败");
		}
		if (!isset($row['stat']) || $row['stat'] != 0) {
			return Library::output(1, "订单状态错误");
		}
		$ids  = [$orderid];
		$info = ['stat' => $status];
		$res  = $toBCheckBillDBModel->updExtractOrderByIds($ids, $info);
		if ($res === false) {
			return Library::output(1);
		} else {
			return Library::output(0);
		}
	}

	/**
	 * 获取提现记录
	 * @param [type] string 记录类型 [undo:未审核;pass:审核通过，未付款;]
	 */
	public function extractCashLog(Request $request) {
		$adminuid = trim($request->input('adminuid'));
		$status   = trim($request->input('type'));
		$showPage = trim($request->input('showpage'));
		$time     = trim($request->input('ts'));
		$sign     = trim($request->input('sign'));
		if (!$time || !$sign) {
			return Library::output(2003);
		}

		if (isset($_GET['//2b/extractCashLog'])) {
			unset($_GET['//2b/extractCashLog']);
		}

		$check_sign = Library::encrypt($_GET, Config::get("common.uc_paykey"));
		if ($check_sign != $sign) {
			return Library::output(2002);
		}
		$toBCheckBillDBModel = new ToBCheckBillDBModel;
		$rows                = [];
		$where               = [];
		if ($adminuid) {
			$where['merchantid'] = $adminuid;
		}
		if (is_numeric($status) && $status > -1) {
			$where['stat'] = $status;
		}
		$res = $toBCheckBillDBModel->getExtractOrderByStatus($where);
		if ($res === false) {
			return Library::output(1);
		}
		$num  = $res->total();
		$rows = [];
		foreach ($res as $value) {
			$rows[] = $value;
		}
		$data = ['rows' => $rows, 'num' => $num, 'status' => $status];
		if ($showPage == 1) {
			$data['show'] = $res->render();
		}

		return Library::output(0, $data);
	}

	private function genOrderId($merchantid) {
		if (!$merchantid) {
			return false;
		}
		$ts   = date("YmdHis");
		$rand = mt_rand(10000, 99999);
		return "{$ts}{$rand}{$merchantid}";
	}

}
