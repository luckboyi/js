<?php

/*
记录用户登录日志
date:2016/9/14
*/

namespace App\Models;

use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class RecordModel extends Model
{

    //不设置属性，默认连接mysql配置 
    protected $connection = 'record';


    /**
     * 向login_log表插入一条登录信息
     * @param   array data
     * @return  int  primary key id
    */
    public function insertLoginLog($data)
    {
    	if (!$data || !is_array($data) || count($data) < 3) {
    		return false;
    	}
        try{
            $id = DB::connection("record")->table('login_log')->insertGetId($data);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__."[".__LINE__."]");
            return false;
        }

        return $id;
    }

    /**
     * 根据uid和appid查询出最近一次登录的记录，包括time,ip，id倒序，取第二个
     * @param   int uid
     * @param   int appid
     * @return  array  ['last_time' => xxx, 'addip' => xxx]
     */
    public function getLoginLog($uid,$appid){

    	if (!$uid || !$appid) {
    		return false;
    	}

    	$condition['uid'] = $uid;
    	$condition['appid'] = $appid;

        try{
            $res = DB::connection("record")->table('login_log')->where($condition)->select('last_time','addip')
                    ->orderBy('id', 'desc')->skip(1)->take(1)->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__."[".__LINE__."]");
            return false;
        }
    	unset($condition);

    	return $res;
    }






}