<?php

/*
购买模块
date:2017/1/33
 */

namespace App\Models;

use Helper\UdpLog;
use App\Models\WebgameModel;
use Illuminate\Database\Eloquent\Model;

class BuyModel extends Model {

	/**
	 * 购买VR游戏回调
     * 先查该游戏是不是收费的，价格与扣费的价格是否相同
     * 判断游戏类型是不是vrgame
     * 不判断是否拥有该游戏，因为到这里，都已经扣费了
	 * @param   int  appid  gameid
	 * @return  bool
	 */
	public function buyVrGame($appid, $uid, $rmb) {
		UdpLog::save2("buy/error", array("function" => "buyVrGame", "log" => "buyVrGame:start", "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
		if (!$appid || !$uid) {
			UdpLog::save2("buy/error", array("function" => "buyVrGame", "result" => "false", "log" => "appid or uid miss", "appid" => $appid, "uid" => $uid, "rmb" => $rmb), __METHOD__ . "[" . __LINE__ . "]", false);
			return false;
		}

        $webgameModel = new WebgameModel;
        $ginfo = $webgameModel->getOneGameInfo($appid);
        if(!$ginfo || !is_array($ginfo)) {
			UdpLog::save2("buy/error", array("function" => "buyVrGame", "result" => "false", "log" => "ginfo error", "ginfo" => $ginfo, "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        if($ginfo['sell'] != $rmb) {
			UdpLog::save2("buy/error", array("function" => "buyVrGame", "result" => "false", "log" => "rmb is wrong", "sell" => $ginfo['sell'], "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        if($ginfo['game_type'] != 1) {
			UdpLog::save2("buy/error", array("function" => "buyVrGame", "result" => "false", "log" => "game_type error", "game_type" => $ginfo['game_type'], "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }

        $guinfo = ['appname' => $ginfo['name'], 'game_type' => 1];
        $add    = $webgameModel->addGameLog($uid, $appid, 0, $guinfo);
	    UdpLog::save2("buy/error", array("function" => "buyVrGame", "result" => "false", "log" => "addGameLog fail", "guinfo" => $guinfo, "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
		return $add;
	}

}
