<?php

/*
充值
date:2016/9/1
 */

namespace App\Models;

use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class UserOrderModel extends Model
{
    /**
     * 获取订单库的库、表名后缀
     */
    protected function getDB($uid)
    {
        if (!$uid) {
            return false;
        }
		$suff = $uid % 32;
        return array('db' => "db_user_order", 'table_order' => "t_user_order_" . $suff);
    }

    /**
     * 获取订单信息
     * @param   int     uid   uid
     * @return  array   order info
     */
    public function getOrderById($orderId)
    {
        if (!$orderId) {
            return false;
        }
		$arr   = explode("_", $orderId);
		$uid = $arr[2];
        $tbl = $this->getDB($uid);
        try {
            $ret = DB::connection($tbl['db'])->table($tbl['table_order'])->where("orderid", $orderId)->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取订单信息
     * @param   int     uid   uid
     * @return  array   order info
     */
    public function getOrderByUid($uid, $page, $perPage = 20)
    {
        if (!$uid) {
            return false;
        }
        $tbl = $this->getDB($uid);
        try {
            $ret = DB::connection($tbl['db'])->table($tbl['table_order'])->where("uid", $uid)->orderBy("id", "desc")->forPage($page, $perPage)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取订单信息
     * @param   int     uid   uid
     * @return  array   order info
     */
    public function getOrderByUidType($uid, $type, $page, $perPage = 20)
    {
		$type = intval($type);
        if (!$uid) {
            return false;
        }
        $tbl = $this->getDB($uid);
        try {
            $ret = DB::connection($tbl['db'])->table($tbl['table_order'])->where(["uid" => $uid, "type" => $type])->orderBy("id", "desc")->forPage($page, $perPage)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取订单数量
     * @param   int     uid   uid
     * @return  array   order info
     */
    public function getOrderCountByUidType($uid, $type)
    {
		$type = intval($type);
        if (!$uid) {
            return false;
        }
        $tbl = $this->getDB($uid);
        try {
            $ret = DB::connection($tbl['db'])->table($tbl['table_order'])->where(["uid" => $uid, "type" => $type])->count();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 插入新订单
     * @param   string  orderid     充值订单号
     * @param   string  orderid     消费订单号
     * @param   int     appid       appid
     * @param   int     uid         uid
     * @param   array   info        订单信息
     * @return  bool
     */
    public function newOrder($orderid, $tradeid, $info)
    {
        if (!$info || !is_array($info) || !isset($info['uid']) || !$info['uid']) {
            return false;
        }
        if ($orderid) {
            $info['orderid'] = $orderid;
        }

        if ($tradeid) {
            $info['tradeid'] = $tradeid;
        }

        $info['ctime'] = time();
        $tbl           = $this->getDB($info['uid']);
        try {
            $ret = DB::connection($tbl['db'])->table($tbl['table_order'])->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($ret) {
            return $ret;
        } else {
            return false;
        }
    }

}
