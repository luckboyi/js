<?php
/*
 * act
 * date:2017/3/22
 */

namespace App\Models;

use App\Helper\Vredis;
use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class ActModel extends Model
{
    public function act3dbbSign($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        try {
            $ret = DB::connection("db_act")->table("t_vronline_3dbb_game_2017")->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    public function act3dbbCheck($uid, $mobile = 0)
    {
        if (!$uid) {
            return false;
        }
        try {
            $raw = DB::connection("db_act")->table("t_vronline_3dbb_game_2017")->where('uid', $uid);
            if ($mobile) {
                $raw->orWhere('mobile', $mobile);
            }
            $ret = $raw->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    public function act3dbbCheckUpload($uid)
    {
        if (!$uid) {
            return false;
        }
        try {
            $ret = DB::connection("db_act")->table("t_vronline_3dbb_game_2017_video")->where('uid', $uid)->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    public function act3dbbUpload($info)
    {

        if (!$info || !is_array($info)) {
            return false;
        }
        try {
            $ret = DB::connection("db_act")->table("t_vronline_3dbb_game_2017_video")->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    public function actGetInfoByPosition($position, $limit, $order = ["sort" => "asc"])
    {
        $limit = (int) $limit;
        if (!$position || $limit < 0 || !is_array($order)) {
            return false;
        }

        $model = DB::connection("db_act")->table("t_vronline_3dbb_info");

        if ($position != "all") {
            $model->where('position', $position);
        }

        if ($limit) {
            $model->limit($limit);
        }

        foreach ($order as $orderK => $orderV) {
            $model->orderBy($orderK, $orderV);
        }

        $raw = $model->get();

        if (!$raw || !is_array($raw)) {
            return false;
        }

        foreach ($raw as &$value) {
            $value["detail"] = json_decode($value["detail"], true);
        }

        return $raw;
    }

    public function updateInfo($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection("db_act")->table("t_vronline_3dbb_info")->where("id", $id)->update($info);
        } else {
            $ret = DB::connection("db_act")->table("t_vronline_3dbb_info")->insert($info);
        }
        return $ret;
    }

    public function delInfoById($id)
    {
        $ret = DB::connection('db_act')->table('t_vronline_3dbb_info')->where('id', $id)->delete();
        return $ret;
    }
}
