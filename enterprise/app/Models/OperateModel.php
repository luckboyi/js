<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class OperateModel extends Model
{

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             位 置 码                                                        |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 根据位置码，获取某个推荐位的位置ID
     * @param   string  code    位置码
     * @return  int     posid   位置id
     */
    public function getPosId($code)
    {
        if (!$code) {
            return false;
        }
        $row = DB::connection("db_operate")->table("top_postion")->where("code", $code)->first();
        return $row;
    }

    /**
     * 根据分类id查询推荐banner位的ID
     * @param $sort
     */
    public function getPosIdBySort($code)
    {
        if (!$code) {
            return false;
        }
        $row = DB::connection("db_operate")->table("top_postion")->where("code", $code)->first();
        return $row;
    }

    /**
     * 通过type获取信息
     * @param $type
     * @return array|bool|\Illuminate\Support\Collection|static[]
     */
    public function getByType($type)
    {
        if (!$type) {
            return false;
        }
        $where = [
            'content_tp' => $type,
        ];
        $row = DB::connection("db_operate")->table("top_postion")->where($where)->orderBy('posid', 'asc')->get();
        return $row;
    }

    /**
     * 获取最大的位置id，用于添加位置码
     * @return  int     posid   位置id
     */
    public function getMaxPosId()
    {
        $row = DB::connection("db_operate")->table("top_postion")->orderBy("posid", "desc")->first();
        return $row;
    }

    /**
     * 获取所有位置信息
     * @return  int     posid   位置id
     * @return  int     stat    状态
     */
    public function getPosIdsByStat($stat)
    {
        $stat = intval($stat);
        $row  = DB::connection("db_operate")->table("top_postion")->where("stat", $stat)->get();
        return $row;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             推 荐 位                                                        |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取某个推荐位的内容ID
     * @param   int     posid   posid
     * @return  array   info    数组，游戏信息
     */
    public function getItemsByPosid($posid, $num = false)
    {
        if (!$posid) {
            return false;
        }
        $row = DB::connection("db_operate")->table("top_recommend")->where("posid", $posid)->orderBy("weight", "desc");
        $num = (int) $num;
        if ($num > 0) {
            $row = $row->take($num);
        }

        return $row->get();
    }

    /**
     * 获取推荐位的某个内容ID信息
     * @param   int     posid   posid
     * @param   int     itemid   itemid
     * @return  array   info    数组，游戏信息
     */
    public function getItem($posid, $itemid)
    {
        if (!$posid || !$itemid) {
            return false;
        }

        $row = DB::connection("db_operate")->table("top_recommend")->where(array("posid" => $posid, "itemid" => $itemid, "stat" => 0))->first();
        return $row;
    }

    /**
     * 根据权重获取推荐位的某个内容ID信息
     * @param   int     posid   posid
     * @param   int     itemid   weight
     * @return  array   info    数组，游戏信息
     */
    public function getItemByWeight($posid, $weight)
    {
        if (!$posid || !$weight) {
            return false;
        }
        $row = DB::connection("db_operate")->table("top_recommend")->where(array("posid" => $posid, "weight" => $weight, "stat" => 0))->first();
        return $row;
    }

    /**
     * 查询某个itemId的视频或游戏是否存在
     * @param   string  type        类别，video:视频;vrgame:vr游戏;webgame:页游;
     * @param   int     itemId        添加推荐位的itemId
     */
    public function ifExistItemId($type, $itemId)
    {
        if (!$type || !$itemId) {
            return false;
        }

        if ($type == 'video') {
            $row = DB::connection("db_operate")->table("t_video")->where("video_id", $itemId)->first();
            return $row;
        } else {
            $row = DB::connection("db_webgame")->table("t_webgame")->where("appid", $itemId)->first();
            return $row;
        }
    }
}
