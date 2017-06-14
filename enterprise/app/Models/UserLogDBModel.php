<?php

/*
用户记录库
date:2016/9/13
 */

namespace App\Models;

use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class UserLogDBModel extends Model
{
    /**
     * 获取礼包库的库、表名后缀
     */
    protected function getOrderDB($uid)
    {
        if (!$uid) {
            return false;
        }
        $tbl_suff = $uid % 32;
        return array('db' => "db_user_log", 'table_order' => "t_buy_order_" . $tbl_suff);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             消 费 记 录 表                                                  |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 添加游戏记录
     * @param   array   info
     * @return  bool
     */
    public function newBuyOrder($uid, $info)
    {
        if (!$info || !is_array($info) || !isset($info['orderid']) || !$info['orderid']) {
            return false;
        }
         try {
            $dbRes = $this->getOrderDB($uid);
            $ret = DB::connection("db_user_log")->table($dbRes['table_order'])->insert($info);
         } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
         }
        return $ret;
    }

    /**
     * 获取页游游戏记录
     */
    public function getOneBuyOrder($uid, $orderid)
    {
        if (!$orderid) {
            return false;
        }

        $clause = ["orderid" => $orderid];
         try {
            $dbRes = $this->getOrderDB($uid);
            $row = DB::connection("db_user_log")->table($dbRes['table_order'])->where($clause)->first();
         } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
         }
        return $row;
    }

}
