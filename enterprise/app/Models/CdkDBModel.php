<?php
/*
 * cdk
 * date:2017/3/15
 */

namespace App\Models;

use App\Helper\Vredis;
use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class CdkDBModel extends Model
{

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |                                 CDK                                         |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 批量添加生成cdk
     * @param   array   cdks        cdk
     * @param   string  batchno     批号
     * @param   int     itemid      游戏、道具、礼包id
     * @param   string  type        itemid对应的类型，游戏、道具、礼包
     * @return  bool
     */
    public function newCdks($cdks, $batchno, $itemid, $type)
    {
        $count = 1000;
        if (!$cdks || !is_array($cdks) || !$batchno || !$itemid || !$type) {
            return false;
        }
        $info = [];
        for ($i = 0; $i < count($cdks); $i++) {
            $info[] = ['cdk' => $cdks[$i], 'itemid' => $itemid, 'batchno' => $batchno, 'type' => $type];
        }
        $arr = array_chunk($info, $count);
        try {
            for ($i = 0; $i < count($arr); $i++) {
                $ret = DB::connection("db_cdk")->table("t_cdk")->insertIgnore($arr[$i]);
            }
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 添加生成cdk
     * @param   array   info   cdk信息
     * @return  bool
     */
    public function newOneCdk($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        try {
            $ret = DB::connection("db_cdk")->table("t_cdk")->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 兑换cdk，修改cdk信息，针对没有使用的cdk
     * @param   array   cdks   cdk
     * @param   array   info   cdk信息
     * @return  int
     */
    public function useCdk($cdk, $info)
    {
        if (!$cdk || !$info || !is_array($info)) {
            return false;
        }
        $clause = ['cdk' => $cdk, 'userid' => 0];
        try {
            $ret = DB::connection("db_cdk")->table("t_cdk")->where($clause)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 修改cdk信息
     * @param   array   cdks   cdk
     * @param   array   info   cdk信息
     * @return  int
     */
    public function updCdk($cdk, $info)
    {
        if (!$cdk || !$info || !is_array($info)) {
            return false;
        }
        $clause = ['cdk' => $cdk];
        try {
            $ret = DB::connection("db_cdk")->table("t_cdk")->where($clause)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获得同一批的cdk
     * @param   string  batchno     批号
     * @return  int
     */
    public function getCdkByBatch($batchno, $all = false)
    {
        if (!$batchno) {
            return false;
        }
        $clause = ['batchno' => $batchno];
        try {
            $row = DB::connection("db_cdk")->table("t_cdk")->select("cdk", 'itemid', 'ctime')->where($clause)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($all) {
            return $row;
        }
        $result = [];
        if ($row && is_array($row)) {
            for ($i = 0; $i < count($row); $i++) {
                $result[] = $row[$i]['cdk'];
            }
        }
        return $result;
    }

    /**
     * 获得一个cdk信息
     * @param   string  cdk
     * @return  int
     */
    public function getCdkInfo($cdk)
    {
        if (!$cdk) {
            return false;
        }
        $clause = ['cdk' => $cdk];
        try {
            $row = DB::connection("db_cdk")->table("t_cdk")->where($clause)->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $row;
    }

    /**
     * 导入cdk到队列
     * @param   int  itemid
     * @param   array  cdks
     * @return  int
     */
    public function importCdkToQueue($itemid, $cdks)
    {
        if (!$itemid || !$cdks || !is_array($cdks)) {
            return false;
        }
        try {
            $ret = Vredis::rpush("cdk_center", $itemid, $cdks);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 从队列中拿一个cdk
     * @param   int  itemid
     * @return  int
     */
    public function getOneCdkFromQueue($itemid)
    {
        if (!$itemid) {
            return false;
        }
        try {
            $cdk = Vredis::lpop("cdk_center", $itemid);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $cdk;
    }

    /**
     * 剩余CDK数量
     * @param   int  itemid
     * @return  int
     */
    public function getQueueLen($itemid)
    {
        if (!$itemid) {
            return false;
        }
        try {
            $ret = Vredis::llen("cdk_center", $itemid);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    public function getBatch($words = "")
    {
        try {
            $res = DB::connection("db_cdk")->table('t_cdk')->select('batchno', 'itemid', DB::raw('count(cdk) as total'), 'ctime');
            if ($words) {
                $res->where("itemid", "LIKE", '%' . $words . '%');
            }
            $row = $res->groupBy('batchno')->paginate(10);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $row;
    }

}
