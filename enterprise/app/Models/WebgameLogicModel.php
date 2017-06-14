<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models;

use App\Models\CookieModel;
use Config;
use Helper\AccountCenter;
use Illuminate\Database\Eloquent\Model;

class WebgameLogicModel extends Model
{
    /**
     * 判断登录状态
     * 普通页面登录不需要严格判断
     * 不需要调用用户中心的登录状态判断
     * 只判断cookie即可
     * 如果有涉及到信息修改的，比如绑定手机，修改用户信息，修改密码，充值等，需要严格判断
     * @param   bool    strict  是否严格判断，如果严格判断，需要调用用户中心的登录状态接口验证
     * @return  bool    登录状态，true:登录状态;false:非登录状态
     */
    public function checkLogin($strict = false)
    {
        $uid   = CookieModel::getCookie("uid");
        $token = CookieModel::getCookie("token");

        if (!$uid || !$token) {
            return false;
        }

        if ($strict) {
            $appid  = Config::get("common.uc_appid");
            $appkey = Config::get("common.uc_appkey");

            $accountModel = new AccountCenter($appid, $appkey);
            $islogin      = $accountModel->checkLogin($uid, $token);
            if ($islogin['code'] == 0) {
                return array("uid" => $uid, "token" => $token);
            } else {
                return false;
            }
        }
        $account = CookieModel::getCookie("account");
        $nick    = CookieModel::getCookie("nick");
        $face    = CookieModel::getCookie("face");
        $power   = CookieModel::getCookie("power");
        return array("uid" => $uid, "token" => $token, "account" => $account, "nick" => $nick, "face" => $face, "power" => $power);
    }

    /**
     * 某个游戏的全部服务器列表
     * 以serverid为key
     */
    public function getAllServer($appid)
    {
        $webgameModel = new WebgameModel;
        $games        = $webgameModel->getAllServer($appid);
        if ($games === false) {
            return false;
        }
        if (!$games || !is_array($games)) {
            return array();
        }
        $result = array();
        for ($i = 0; $i < count($games); $i++) {
            $serverid          = $games[$i]['serverid'];
            $result[$serverid] = $games[$i];
        }
        return $result;
    }

    /**
     * 某个游戏的全部服务器列表
     * 以serverid为key
     */
    public function getEnableServers($appid)
    {
        $webgameModel = new WebgameModel;
        $games        = $webgameModel->getEnableServers($appid);
        if ($games === false) {
            return false;
        }
        if (!$games || !is_array($games)) {
            return array();
        }
        $result = array();
        for ($i = 0; $i < count($games); $i++) {
            $serverid          = $games[$i]['serverid'];
            $result[$serverid] = $games[$i];
        }
        return $result;
    }

    /**
     * 获取webgame图片保存的相对路径
     */
    public static function getWebgameImagePath($appid)
    {
        if (!$appid || !is_numeric($appid) || $appid <= 0) {
            return false;
        }
        $path1 = sprintf("%02s", $appid % 100);
        return "webgame/{$appid}/";
    }

    /**
     * 检查一个游戏是否有可用服务器
     * 临时用的接口
     */
    public function checkServer($appid)
    {
        $webgameModel = new WebgameModel;
        $serverid     = $webgameModel->maxServeridByAppid($appid);
        return $serverid;
    }
}
