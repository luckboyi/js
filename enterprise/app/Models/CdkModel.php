<?php
/*
 * cdk
 * date:2017/3/15
 */

namespace App\Models;

use App\Helper\Vredis;
use App\Models\CdkDBModel;
use App\Models\WebgameModel;
use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class CdkModel extends Model
{

    /**
     * 校验兑换游戏的CDK
     * @return  string/bool
     */
    public function checkGameCdk($cdk, $uid)
    {

        if (!$cdk || !$uid) {
            return false;
        }
        $cdk        = strtoupper($cdk);
        $cdkDBModel = new CdkDBModel;
        $cinfo      = $cdkDBModel->getCdkInfo($cdk);
        if ($cinfo === false) {
            return false;
        }

        if (!$cinfo) {
            return "notexists"; // cdk 不存在
        }
        if ($cinfo['userid']) {
            return "used"; // cdk已经使用
        }
        if ($cinfo['type'] != "game") {
            return "wrongtype"; // 类型错误
        }
        $appid = $cinfo['itemid'];
        if (!$appid) {
            return false;
        }

        $webgameModel = new WebgameModel;
        $gamelog      = $webgameModel->getOneGameLog($uid, $appid, 0);
        if ($gamelog === false) {
            return false;
        }
        if ($gamelog) {
            return "owned"; // 已经购买过
        }

        $ginfo = $webgameModel->getOneGameInfo($appid);
        if (!$ginfo || !is_array($ginfo)) {
            return false;
        }
        $uinfo = ['userid' => $uid];
        $ret   = $cdkDBModel->useCdk($cdk, $uinfo);
        if (!$ret) {
            // ret=0或ret=false，都是失败，ret=0表示被领过
            return false;
        }
        $guinfo = ['appname' => $ginfo['name'], 'game_type' => 1];
        $add    = $webgameModel->addGameLog($uid, $appid, 0, $guinfo);
        return ['code' => 0, 'appname' => $ginfo['name'], 'appid' => $appid];
    }

    /**
     * [importCdk 生成CDK]
     * @param [int] [itemid] [<游戏id>]
     * @param [string] [type] [<类型>]
     * @param [int] [num] [<数量>]
     * @return [bool] [结果]
     */
    public function importCdk($itemid, $type, $num)
    {
        if (!$itemid || !$type || !is_numeric($num) || $num < 1) {
            return false;
        }
        $batchno    = date("YmdHis") . rand(1000, 9999);
        $cdks       = $this->genCdk($num);
        $cdks       = array_values($cdks);
        $cdkDBModel = new CdkDBModel;
        $ret        = $cdkDBModel->newCdks($cdks, $batchno, $itemid, $type);
        if (!$ret) {
            return false;
        }
        $records = $cdkDBModel->getCdkByBatch($batchno);
        if (!$records) {
            return false;
        }
        $ret = $cdkDBModel->importCdkToQueue($itemid, $records);
        return $ret;
    }

    /**
     * 生成CDK
     */
    public function genCdk($num)
    {
        $len    = 16;
        $list   = array('2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $area   = count($list);
        $result = [];
        while (count($result) < $num) {
            $tmp = [];
            $str = "";
            for ($i = 0; $i < $len; $i++) {
                $r    = mt_rand(0, $area - 1);
                $char = $list[$r];
                if (count($tmp) % 4 == 3 && count($tmp) < $len - 1) {
                    $char .= "-";
                }
                $tmp[] = $char;
            }
            $str          = strtoupper(implode("", $tmp));
            $result[$str] = $str;
        }
        return $result;
    }
}
