<?php

/*
消费
date:2016/9/1
*/

namespace App\Models;

use DB;
use Helper\UdpLog;
use Helper\Library;
use Illuminate\Database\Eloquent\Model;

class ConsumeModel extends Model
{
    /**
     * 获取消费库的库、表名后缀
     */
    protected function getDB($tradeid)
    {
        if(!$tradeid) {
            return false;
        }
        $arr = explode("_", $tradeid);
        if(!$arr) {
            return false;
        }
        if(isset($arr[3]) && $arr[3] > 1470000000 && $arr[3] < 2470000000) {
            $stamp = $arr[3];
        }else {
            return false;
        }
        $year   = date("Y", $stamp);
        $day    = date("md", $stamp);
        return array('db' => "db_consume_".$year, 'table_order' => "t_consume_".$day);
    }

    /**
     * 获取订单库的库、表名后缀
     */
    protected function getDBByTime($stamp)
    {
        if(!$stamp) {
            return false;
        }
        $year   = date("Y", $stamp);
        $day    = date("md", $stamp);
        return array('db' => "db_consume_".$year, 'table_order' => "t_consume_".$day);
    }

    /**
     * 生成订单ID
     */
    public function genTradeid($appid, $uid)
    {
        if(!$appid || !$uid) {
            return false;
        }
        $stamp = time();
        $rand = mt_rand(1000, 9999);
        return "trade_{$appid}_{$uid}_{$stamp}_{$rand}";
    }

    /**
     * 获取订单信息
     * @param   string  tradeid   tradeid
     * @return  array   order info
     */
    public function getTradeById($tradeid)
    {
        if(!$tradeid) {
            return false;
        }
        $tbl = $this->getDB($tradeid);
        try{
            $ret = DB::connection("db_consume_2015")->table($tbl['db'].".".$tbl['table_order'])->where("tradeid", $tradeid)->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__."[".__LINE__."]");
            return false;
        }
        return $ret;
    }

    /**
     * 插入新订单
     * @param   array   info    订单信息
     * @return  bool    
     */
    public function newTrade($appid, $uid, $info, $tradeid="")
    {
        if(!$appid || !$uid || !$info || !is_array($info)) {
            return false;
        }
        $now            = time();
        $info['appid']  = $appid;
        $info['uid']    = $uid;
        $info['ctime']  = $now;
        $info['cip']    = Library::real_ip();
        if(!$tradeid) {
            $tradeid = $this->genTradeid($appid, $uid);
        }
        $info['tradeid'] = $tradeid;
        $tbl = $this->getDB($tradeid);
        try{
            $ret = DB::connection("db_consume_2015")->table($tbl['db'].".".$tbl['table_order'])->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__."[".__LINE__."]");
            return false;
        }
        if($ret) {
            return $tradeid;
        }else {
            return false;
        }
    }

    /**
     * 修改订单信息
     * @param   array  info
     * @return  bool
    */
    public function updateTrade($tradeid, $info)
    {
        if(!$tradeid || !$info || !is_array($info)) {
            return false;
        }
        $tbl = $this->getDB($tradeid);
        try{
            $ret = DB::connection("db_consume_2015")->table($tbl['db'].".".$tbl['table_order'])->where('tradeid', $tradeid)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__."[".__LINE__."]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取未成功的订单信息
     * @param   int     stamp   时间戳
     * @param   int     status  状态
     */
    public function getUnSuccessTrade($stamp, $status, $retrynum=0)
    {
        if(!$stamp) {
            return false;
        }
        $tbl = $this->getDBByTime($stamp);
        try{
            if($retrynum > 0) {
                $ret = DB::connection("db_consume_2015")->table($tbl['db'].".".$tbl['table_order'])->where("stat", '<', $status)->where("retrynum", "<", $retrynum)->orderBy("ctime", "asc")->get();
            }else {
                $ret = DB::connection("db_consume_2015")->table($tbl['db'].".".$tbl['table_order'])->where("stat", '<', $status)->orderBy("ctime", "asc")->get();
            }
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__."[".__LINE__."]");
            return false;
        }
        return $ret;
    }

}