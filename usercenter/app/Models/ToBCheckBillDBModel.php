<?php

/*
 * 线下体验店充值
 * date:2016/9/1
 */

namespace App\Models;

use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class ToBCheckBillDBModel extends Model {

	/*
		      +-----------------------------------------------------------------------------+
		      |                                                                             |
		      |                           每 日 统 计 账 单                                 |
		      |                                                                             |
		      |     # 2b版本商家每日对账，保存前一天对账收入总额，并统计余额                |
		      |     # 每日凌晨统计出前一天的账单，并和数据中心对账通过后，写入该表，        |
		      |     # 每天都要有记录，如果没有收入，收入为0                                 |
		      |     # 对账完成后，重新统计商户的余额                                        |
		      |                                                                             |
		      +-----------------------------------------------------------------------------+
	*/
	/**
	 * 获取某日的账单
	 * @param   string  merchantid  商户id
	 * @param   string  day         日期
	 * @return  array   order info
	 */
	public function getOneDayBill($merchantid, $day) {
		if (!$merchantid || !$day) {
			return false;
		}
		$clause = ["merchantid" => $merchantid, "day" => $day];
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_day_bill")->where($clause)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 获取某日的账单
	 * @param   string  day         日期
	 * @return  array   order info
	 */
	public function getAllBillByDay($day) {
		if (!$day) {
			return false;
		}
		$clause = ["day" => $day];
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_day_bill")->where($clause)->get();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		if (!is_array($ret)) {
			return false;
		}
		$result = [];
		for ($i = 0; $i < count($ret); $i++) {
			$merchantid = $ret[$i]['merchantid'];
			$result[$merchantid] = $ret[$i];
		}
		return $result;
	}

	/**
	 * 插入某日的账单
	 * @param   string  merchantid  商户id
	 * @param   string  day         日期
	 * @param   array   info    订单信息
	 * @return  bool
	 */
	public function addOneDayBill($merchantid, $day, $info) {
		if (!$merchantid || !$day || !$info || !is_array($info)) {
			return false;
		}
		$info['merchantid'] = $merchantid;
		$info['day'] = $day;
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_day_bill")->insert($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 修改某日的账单
	 * @param   string  merchantid  商户id
	 * @param   string  day         日期
	 * @param   array   info    订单信息
	 * @return  bool
	 */
	public function updOneDayBill($merchantid, $day, $info) {
		if (!$merchantid || !$day || !$info || !is_array($info)) {
			return false;
		}
		$clause = ['merchantid' => $merchantid, 'day' => $day];
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_day_bill")->where($clause)->update($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/*
		      +-----------------------------------------------------------------------------+
		      |                                                                             |
		      |                           商 家 余 额                                       |
		      |                                                                             |
		      +-----------------------------------------------------------------------------+
	*/

	/**
	 * 获取余额信息
	 * @param   string  merchantid  商户id
	 * @return  array   order info
	 */
	public function getBalance($merchantid) {
		if (!$merchantid) {
			return false;
		}
		$info = $this->getBalanceInfo($merchantid);
		if ($info && isset($info['pay_pwd'])) {
			unset($info['pay_pwd']);
		}
		return $info;
	}

	/**
	 * 获取支付密码
	 * @param   string  merchantid  商户id
	 * @return  array   order info
	 */
	public function getCashPwd($merchantid) {
		if (!$merchantid) {
			return false;
		}
		$info = $this->getBalanceInfo($merchantid);
		if (isset($info['pay_pwd'])) {
			return $info['pay_pwd'];
		} else {
			return null;
		}
	}

	/**
	 * 获取余额信息
	 * @param   string  merchantid  商户id
	 * @return  array   order info
	 */
	public function getBalanceInfo($merchantid) {
		if (!$merchantid) {
			return false;
		}
		$clause = ["merchantid" => $merchantid];
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_balance")->where($clause)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 修改支付密码
	 * @param   string  merchantid  商户id
	 * @param   string  pwd
	 * @return  bool
	 */
	public function updCashPwd($merchantid, $pwd) {
		if (!$merchantid || !$pwd || strlen($pwd) != 32) {
			return false;
		}
		$ret = $this->updBalanceInfo($merchantid, ["pay_pwd" => $pwd]);
		return $ret;
	}

	/**
	 * 修改支付密码
	 * @param   string  merchantid  商户id
	 * @param   string  pwd
	 * @return  bool
	 */
	public function updBalance($merchantid, $info) {
		if (!$merchantid || !$info || !is_array($info)) {
			return false;
		}
		if (isset($info['pay_pwd'])) {
			return flase;
		}
		$ret = $this->updBalanceInfo($merchantid, $info);
		return $ret;
	}

	/**
	 * 修改余额信息
	 * @param   string  merchantid  商户id
	 * @param   array    info
	 * @return  bool
	 */
	private function updBalanceInfo($merchantid, $info) {
		if (!$merchantid || !$info || !is_array($info)) {
			return false;
		}
		$clause = ["merchantid" => $merchantid];
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_balance")->where($clause)->update($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 创建记录
	 * @param   string  merchantid  商户id
	 * @param   array    info
	 * @return  bool
	 */
	public function newBalanceInfo($merchantid, $info) {
		if (!$merchantid || !$info || !is_array($info)) {
			return false;
		}
		$info['merchantid'] = $merchantid;
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_balance")->insert($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 增加余额，用户付费用，同时增加总收入
	 * @param   string  merchantid  商户id
	 * @param   float   num         加余额数量
	 * @return  bool
	 */
	public function incNewBalance($merchantid, $num) {
		$num = floatval($num);
		if (!$merchantid || !$num || $num <= 0) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_balance")->where("merchantid", $merchantid)->update(array(
				'total_income' => DB::raw("total_income + {$num}"),
				'new_net_income' => DB::raw("new_net_income + {$num}"),
			));
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 扣余额，退款用，同时扣总收入
	 * @param   string  merchantid  商户id
	 * @param   float   num         加余额数量
	 * @return  bool
	 */
	public function decNewBalance($merchantid, $num) {
		$num = floatval($num);
		if (!$merchantid || !$num || $num <= 0) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_balance")->where("merchantid", $merchantid)->update(array(
				'total_income' => DB::raw("total_income - {$num}"),
				'new_net_income' => DB::raw("new_net_income - {$num}"),
			));
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 扣已对账余额，提现用，不扣总收入
	 * @param   string  merchantid  商户id
	 * @param   float   num         加余额数量
	 * @return  bool
	 */
	public function decNetBalance($merchantid, $num) {
		$num = floatval($num);
		if (!$merchantid || !$num || $num <= 0) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_balance")->where("merchantid", $merchantid)->decrement("net_income", $num);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 结算，增加已结算金额，减少未结算金额
	 * 只能在结算时候调该方法，其他时候不能调
	 * 不扣总收入
	 * @param   string  merchantid  商户id
	 * @param   float   num         减余额数量
	 * @return  bool
	 */
	public function settlementBalance($merchantid, $net_num, $k) {
		if (!$merchantid || !is_numeric($net_num)) {
			return false;
		}
		if ($k !== md5($merchantid . $net_num)) {
			// 这个接口不要调，只在结算时使用
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_balance")->where("merchantid", $merchantid)->update(array(
				'net_income' => DB::raw("net_income + {$net_num}"),
				'new_net_income' => DB::raw("new_net_income - {$net_num}"),
				'last_settlement_time' => date("Y-m-d H:i:s"),
			));
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/*
		      +-----------------------------------------------------------------------------+
		      |                                                                             |
		      |                           游 戏 分 成 设 定                                 |
		      |                                                                             |
		      +-----------------------------------------------------------------------------+
	*/
	/**
	 * 获取某个游戏的分成
	 */
	public function getAppFee($appid) {
		if ($appid <= 0) {
			return false;
		}
		$clause = ["appid" => $appid];
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_appid_fee")->where($clause)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 设置某个游戏的分成
	 */
	public function setAppFee($appid, $info) {
		if ($appid <= 0 || !$info || !is_array($info)) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_appid_fee")->insertUpdate($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/*
		      +-----------------------------------------------------------------------------+
		      |                                                                             |
		      |                           渠 道 对 账 单                                    |
		      |                                                                             |
		      +-----------------------------------------------------------------------------+
	*/

	/**
	 * 添加对账单获取状态
	 * @param   array    info ['channel'=>'heepay', 'day'=>'20170209', 'num'=>2, 'amount'=>0.02]
	 * @return  bool
	 */
	public function addCheckStatus($info) {
		if (!$info || !is_array($info)) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_check_bill_status")->insert($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 读取对账单状态
	 * @param   array    clause ['channel'=>'heepay', 'day'=>'20170209']
	 * @return  bool
	 */
	public function getCheckStatus($clause) {
		if (!$clause || !is_array($clause)) {
			return false;
		}
		try {
			if (isset($clause['channel']) && $clause['channel'] && isset($clause['day']) && $clause['day']) {
				$result = DB::connection("db_2b_store_check_bill")->table("t_2b_check_bill_status")->where($clause)->first();
				if ($result === null) {
					$result = array();
				}
			} else {
				$result = [];
				$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_check_bill_status")->where($clause)->get();
				if ($ret && is_array($ret)) {
					for ($i = 0; $i < count($ret); $i++) {
						$c = $ret[$i]['channel'];
						$result[$c] = $ret[$i];
					}
				} else {
					$result = $ret;
				}
			}
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $result;
	}

	/**
	 * 添加汇付宝对账单
	 * @param   array    info
	 * @return  bool
	 */
	public function newHeepayBill($info) {
		if (!$info || !is_array($info)) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_check_bill_heepay")->insert($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 获取汇付宝对账单
	 * @param   string  start   开始时间
	 * @param   string  end     结束时间
	 * @return  bool
	 */
	public function getHeepayBillByTime($start, $end) {
		if (!$start || !$end || $start >= $end) {
			return false;
		}
		try {
			$result = [];
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_check_bill_heepay")->where("paychannel_ts", ">=", $start)->where("paychannel_ts", "<=", $end)->get();
			if ($ret && is_array($ret)) {
				for ($i = 0; $i < count($ret); $i++) {
					$o = $ret[$i]['paychannel_orderid'];
					$c = $ret[$i]['paychannel_type'];
					$result[$c . "_" . $o] = $ret[$i];
				}
			} else {
				$result = $ret;
			}
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $result;
	}

	/**
	 * 获取汇付宝对账单记录数量
	 * @param   string  start   开始时间
	 * @param   string  end     结束时间
	 * @return  bool
	 */
	public function getHeepayBillNumByTime($start, $end) {
		if (!$start || !$end || $start >= $end) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_check_bill_heepay")->where("paychannel_ts", ">=", $start)->where("paychannel_ts", "<=", $end)->count();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 获取汇付宝对账单总金额
	 * @param   string  start   开始时间
	 * @param   string  end     结束时间
	 * @return  bool
	 */
	public function getHeepayBillAmountByTime($start, $end) {
		if (!$start || !$end || $start >= $end) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_check_bill_heepay")->where("paychannel_ts", ">=", $start)->where("paychannel_ts", "<=", $end)->sum("pay_rmb");
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 根据商户获取银行卡信息
	 * @param   int  merchantid   商户ID
	 * @return  bool
	 */
	public function get2bBankCards($merchantid) {
		if (!$merchantid) {
			return false;
		}
		try {
			$row = DB::connection("db_2b_store_check_bill")->table("t_2b_bank_cards")->where('merchantid', $merchantid)->get();

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $row;
	}

	/**
	 * 设置默认银行卡
	 * @param   int  merchantid     商户id
	 * @param   int  card_id     银行卡id
	 * @return  bool
	 */
	public function defaultCard($merchantid, $card_id = 0) {
		if (!$merchantid || !$card_id) {
			return false;
		}
		try {
			DB::connection("db_2b_store_check_bill")->table("t_2b_bank_cards")->where('merchantid', $merchantid)->where('id', '<>', $card_id)->update(['card_default' => 0]);
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_bank_cards")->where('merchantid', $merchantid)->where('id', $card_id)->update(['card_default' => 1]);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 获取银行卡信息
	 * @param   int  card_id     银行卡id
	 * @return  bool
	 */
	public function check2bBankCard($card_no, $card_id = 0) {
		if (!$card_no) {
			return false;
		}
		try {
			$raw = DB::connection("db_2b_store_check_bill")->table("t_2b_bank_cards")->where('card_no', $card_no);
			if ($card_id) {
				$raw->where('card_id', '<>', $card_id);
			}
			$row = $raw->first();

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $row;
	}

	/**
	 * 获取银行卡信息
	 * @param   int  card_id   银行卡id
	 * @return  bool
	 */
	public function get2bBankCard($merchantid, $card_id) {
		if (!$merchantid || !$card_id) {
			return false;
		}
		try {
			$row = DB::connection("db_2b_store_check_bill")->table("t_2b_bank_cards")->where('merchantid', $merchantid)->where('id', $card_id)->first();

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $row;
	}

	/**
	 * 删除银行卡信息
	 * @param   int  merchantid   商户id
	 * @param   int  card_id   银行卡id
	 * @return  bool
	 */
	public function del2bBankCard($merchantid, $card_id) {
		if (!$merchantid || !$card_id) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_bank_cards")->where('merchantid', $merchantid)->where('id', $card_id)->delete();

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 保存银行卡信息
	 * @param   array  info   信息
	 * @return  bool
	 */
	public function save2bBankCard($info) {
		if (!$info) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_bank_cards")->insert($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 保存提取订单
	 * @param   array  info   信息
	 * @param   array  where     条件
	 * @return  bool
	 */
	public function addExtractOrder($info) {
		if (!$info) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_extract_cash")->insert($info);

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 根据状态查询提取订单
	 * @param   int     status  状态
	 * @return  array
	 */
	public function getExtractOrderByStatus($clause) {

		if (!is_array($clause)) {
			return false;
		}

		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_extract_cash")->where($clause)->paginate(10);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 批量修改订单
	 * @param   array   ids     orderids
	 * @param   int     status  状态
	 * @return  bool
	 */
	public function updExtractOrderByIds($ids, $info) {

		if (!$ids || !is_array($ids)) {
			return false;
		}
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_extract_cash")->whereIn("orderid", $ids)->update($info);

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 根据orderid查询提取订单
	 * @param   string  orderid     订单号
	 * @return  array
	 */
	public function getOneExtractOrder($orderid) {
		if (!$orderid) {
			return false;
		}
		$clause = ['orderid' => $orderid];
		try {
			$ret = DB::connection("db_2b_store_check_bill")->table("t_2b_extract_cash")->where($clause)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

}