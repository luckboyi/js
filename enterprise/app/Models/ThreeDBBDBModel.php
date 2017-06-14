<?php
/*
 * 3d播播
 * date:2017/3/22
 */

namespace App\Models;

use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class ThreeDBBDBModel extends Model
{
    private $pageSize = 10;
    public function dbbByPage($type = 0, $stat = -1, $words = '')
    {
        $res = DB::connection('db_act')->table('t_vronline_3dbb_game_2017_video');
        if ($stat >= 0) {
            $res->where("stat", $stat);
        }
        if ($words) {
            $res->where("intro", "LIKE", '%' . $words . '%');
        }
        $row = $res->orderBy("ctime", "desc")->paginate($this->pageSize);
        return $row;
    }

    public function updateStat($itemid, $stat, $msg)
    {
        if (!$itemid || !$stat) {
            return false;
        }
        $where = [
            'id' => $itemid,
        ];
        $updateArr = [
            'stat' => $stat,
            'msg'  => $msg,
        ];
        $ret = DB::connection('db_act')->table('t_vronline_3dbb_game_2017_video')->where($where)->update($updateArr);
        return $ret;
    }

    public function get3DBBRegData($start, $end)
    {
        $ret = DB::connection('db_act')->table('t_vronline_3dbb_game_2017')->whereRaw('ctime >= "' . $start . '"')->whereRaw('ctime < "' . $end . '"')->orderBy("ctime", "desc")->paginate($this->pageSize);
        return $ret;
    }
}
