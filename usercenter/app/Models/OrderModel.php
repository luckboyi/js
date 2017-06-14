<?php

/*
充值
date:2016/9/1
 */

namespace App\Models;

use App\Helper\Vmemcached;
use Config;
use DB;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    /**
     * 支付白名单集合key前缀
     */
    private $key_white_list_prefix = "set_pay_white_list_";

    private $key_lock_prefix = "key_pay_lock_";

    /**
     * 获取订单库的库、表名后缀
     */
    protected function getDB($orderid)
    {
        if (!$orderid) {
            return false;
        }
        $arr = explode("_", $orderid);
        if (!$arr) {
            return false;
        }
        if (isset($arr[3]) && $arr[3] > 1470000000 && $arr[3] < 2470000000) {
            $stamp = $arr[3];
        } else {
            return false;
        }
        $year = date("Y", $stamp);
        $day  = date("md", $stamp);
        return array('db' => "db_order_" . $year, 'table_order' => "t_order_" . $day);
    }

    /**
     * 获取订单库的库、表名后缀
     */
    protected function getDBByTime($stamp)
    {
        if (!$stamp) {
            return false;
        }
        $year = date("Y", $stamp);
        $day  = date("md", $stamp);
        return array('db' => "db_order_" . $year, 'table_order' => "t_order_" . $day);
    }

    /**
     * 生成订单ID
     */
    protected function genOrderId($appid, $uid)
    {
        if (!$appid || !$uid) {
            return false;
        }
        $stamp = time();
        $rand  = mt_rand(1000, 9999);
        return "order_{$appid}_{$uid}_{$stamp}_{$rand}";
    }

    /**
     * 获取订单信息
     * @param   string  orderid   orderid
     * @return  array   order info
     */
    public function getOrderById($orderid)
    {
        if (!$orderid) {
            return false;
        }
        $tbl = $this->getDB($orderid);
        try {
            $ret = DB::connection("db_order_2015")->table($tbl['db'] . "." . $tbl['table_order'])->where("orderid", $orderid)->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 插入新订单
     * @param   array   info    订单信息
     * @return  bool
     */
    public function newOrder($appid, $uid, $info)
    {
        if (!$appid || !$uid || !$info || !is_array($info)) {
            return false;
        }
        $now             = time();
        $info['appid']   = $appid;
        $info['uid']     = $uid;
        $info['ctime']   = $now;
        $info['cip']     = Library::realIp();
        $orderid         = $this->genOrderId($appid, $uid);
        $info['orderid'] = $orderid;
        $tbl             = $this->getDB($orderid);
        try {
            $ret = DB::connection("db_order_2015")->table($tbl['db'] . "." . $tbl['table_order'])->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($ret) {
            return $orderid;
        } else {
            return false;
        }
    }

    /**
     * 获取订单信息
     * @param   int     stamp   时间戳
     * @param   int     status  状态
     */
    public function getOrderByStat($stamp, $status, $retrynum = 0)
    {
        if (!$stamp) {
            return false;
        }
        $tbl = $this->getDBByTime($stamp);
        try {
            if ($retrynum > 0) {
                $ret = DB::connection("db_order_2015")->table($tbl['db'] . "." . $tbl['table_order'])->where("stat", $status)->where("retrynum", "<", $retrynum)->orderBy("ctime", "asc")->get();
            } else {
                $ret = DB::connection("db_order_2015")->table($tbl['db'] . "." . $tbl['table_order'])->where("stat", $status)->orderBy("ctime", "asc")->get();
            }
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 修改订单信息
     * @param   array  info
     * @return  bool
     */
    public function updateOrder($orderid, $info)
    {
        if (!$orderid || !$info || !is_array($info)) {
            return false;
        }
        $tbl = $this->getDB($orderid);
        try {
            $ret = DB::connection("db_order_2015")->table($tbl['db'] . "." . $tbl['table_order'])->where('orderid', $orderid)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 加锁
     */
    public function addLock($lockid)
    {
        $key   = "lock_" . $lockid;
        $now   = time();
        $value = $now . "|" . $lockid;
        return Vmemcached::add("lock", $lockid, $value);
    }

    /**
     * 删除锁
     */
    public function delLock($lockid)
    {
        $key   = "lock_" . $lockid;
        $now   = time();
        $value = $now . "|" . $lockid;

        return Vmemcached::delete("lock", $lockid);
    }
}
