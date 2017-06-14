<?php

/*
日志
date:2016/9/1
*/

namespace App\Models;

use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class LogModel extends Model
{
    /**
     * 获取订单库的库、表名后缀
     */
    protected static function getDB()
    {
        $stamp  = time();
        $year   = date("Y", $stamp);
        $month  = date("m", $stamp);
        return array('db' => "db_log_".$year, 'table_error' => "t_error_log_".$month);
    }

    /**
     * 插入错误日志
     */
    public static function addLog($info)
    {
        if(!$info || !is_array($info)) {
            return false;
        }
        if(isset($info['errmsg']) && is_array($info['errmsg'])) {
            $info['errmsg'] = json_encode($info['errmsg']);
        }
        $info['get']  = json_encode($_GET);
        $info['post'] = json_encode($_POST);
        $tbl = self::getDB();
        try{
            $ret = DB::connection("db_log_2016")->table($tbl['db'].".".$tbl['table_error'])->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__."[".__LINE__."]");
            return false;
        }
        if($ret) {
            return true;
        }else {
            return false;
        }
    }

}