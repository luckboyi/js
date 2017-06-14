<?php

/*
 * 线下体验店充值
 * date:2016/9/1
 */

namespace App\Models;

use DB;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class ToBOrderModel extends Model {
	/**
	 * 获取订单库的库、表名后缀
	 */
	protected function getDB($orderid) {
		if (!$orderid) {
			return false;
		}
		$ts = substr($orderid, 0, 14);
		$stamp = strtotime($ts);
		$year = date("Y", $stamp);
		$month = date("m", $stamp);
		return array('db' => "db_2b_store_order_" . $year, 'table_order' => "t_2b_order_" . $month);
	}

	/**
	 * 获取订单库的库、表名后缀
	 */
	protected function getDBByTime($stamp) {
		if (!$stamp) {
			return false;
		}
		$year = date("Y", $stamp);
		$month = date("m", $stamp);
		return array('db' => "db_2b_store_order_" . $year, 'table_order' => "t_2b_order_" . $month);
	}

	/**
	 * 生成订单ID
	 */
	protected function genOrderId($merchantid) {
		if (!$merchantid) {
			return false;
		}
		$ts = date("YmdHis");
		$rand = mt_rand(10000, 99999);
		return "{$ts}{$rand}{$merchantid}";
	}

	/**
	 * 获取订单信息
	 * @param   string  orderid   orderid
	 * @return  array   order info
	 */
	public function getOrderById($orderid) {
		if (!$orderid) {
			return false;
		}
		$tbl = $this->getDB($orderid);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->where("orderid", $orderid)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 插入新订单
	 * @param   string  terminal_sn   终端设备号
	 * @param   string  merchantid    商户ID
	 * @param   array   info    订单信息
	 * @return  bool
	 */
	public function newOrder($terminal_sn, $merchantid, $info) {
		if (!$terminal_sn || !$merchantid || !$info || !is_array($info)) {
			return false;
		}

		$info['terminal_sn'] = $terminal_sn;
		$info['merchantid'] = $merchantid;
		$info['cip'] = Library::realIp();
		$info['orderid'] = $this->genOrderId($merchantid);
		$tbl = $this->getDB($info['orderid']);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->insert($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		if ($ret) {
			return $info['orderid'];
		} else {
			return false;
		}
	}

	/**
	 * 修改订单信息
	 * @param   array  info
	 * @return  bool
	 */
	public function updateOrder($orderid, $info) {
		if (!$orderid || !$info || !is_array($info)) {
			return false;
		}
		$tbl = $this->getDB($orderid);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->where('orderid', $orderid)->update($info);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 统计订单信息，用于对账
	 * @param   array  info
	 * @return  bool
	 */
	public function getOrderCount4Check($day) {
		if (!$day) {
			return false;
		}
        $stamp = strtotime($day);
        $start = date("Y-m-d", $stamp);
        $end = date("Y-m-d", $stamp + 86400);
		$tbl = $this->getDBByTime($stamp);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("status", ">", 0)->count();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 统计订单信息，用于对账
	 * @param   array  info
	 * @return  bool
	 */
	public function getOrderSumBrief4Check($day) {
		if (!$day) {
			return false;
		}
        $stamp = strtotime($day);
        $start = date("Y-m-d", $stamp);
        $end = date("Y-m-d", $stamp + 86400);
		$tbl = $this->getDBByTime($stamp);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->select(DB::raw('SUM(total_rmb) as total_amount'), DB::raw('SUM(pay_rmb) as pay_amount'), DB::raw('SUM(cp_fee) as cp_fee'), DB::raw('SUM(plat_fee) as plat_fee'), DB::raw('SUM(merchant_fee) as merchant_fee'), DB::raw('COUNT(orderid) as total_count'))->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("status", ">", 0)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
        if($ret === null) {
            $ret = ['total_amount'=>0, 'pay_amount'=>0, 'merchant_fee'=>0, 'total_count'=>0];
        }
        $ret['total_amount'] = $ret['total_amount'] === null ? 0 : $ret['total_amount'];
        $ret['pay_amount']   = $ret['pay_amount'] === null ? 0 : $ret['pay_amount'];
        $ret['cp_fee']       = $ret['cp_fee'] === null ? 0 : $ret['cp_fee'];
        $ret['plat_fee']     = $ret['plat_fee'] === null ? 0 : $ret['plat_fee'];
        $ret['merchant_fee'] = $ret['merchant_fee'] === null ? 0 : $ret['merchant_fee'];
		return $ret;
	}

	/**
	 * 统计订单信息，用于对账
	 * @param   array  info
	 * @return  bool
	 */
	public function getOrderSum4Check($day) {
		if (!$day) {
			return false;
		}
        $stamp = strtotime($day);
        $start = date("Y-m-d", $stamp);
        $end = date("Y-m-d", $stamp + 86400);
		$tbl = $this->getDBByTime($stamp);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("status", ">", 0)->sum("total_rmb");
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 统计订单信息，用于对账
	 * @param   array  info
	 * @return  bool
	 */
	public function getOrderPaySum4Check($day) {
		if (!$day) {
			return false;
		}
        $stamp = strtotime($day);
        $start = date("Y-m-d", $stamp);
        $end = date("Y-m-d", $stamp + 86400);
		$tbl = $this->getDBByTime($stamp);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("status", ">", 0)->sum("pay_rmb");
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $ret;
	}

	/**
	 * 统计订单信息，用于对账
	 * @param   array  info
	 * @return  bool
	 */
	public function getOrderBrief4Check($day) {
		if (!$day) {
			return false;
		}
        $stamp = strtotime($day);
        $start = date("Y-m-d", $stamp);
        $end = date("Y-m-d", $stamp + 86400);
		$tbl = $this->getDBByTime($stamp);
		try {
			$ret = DB::connection("db_2b_store_order_2017")->table($tbl['db'] . "." . $tbl['table_order'])->select('orderid', 'paychannel_orderid', 'paycenter_orderid', 'total_rmb', 'pay_rmb', 'cp_fee', 'plat_fee', 'merchant_fee', 'merchantid', 'pay_channel', 'paytype')->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("status", ">", 0)->orderBy('orderid', 'asc')->get();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
        $result = [];
        for($i = 0; $i < count($ret); $i++) {
            $orderid = $ret[$i]['orderid'];
            $result[$orderid] = $ret[$i];
        }
		return $result;
	}

}