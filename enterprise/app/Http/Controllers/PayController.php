<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Traits\SimpleResponse;
use App\Models\BuyModel;
use App\Models\CookieModel;
use App\Models\OpenidModel;
use App\Models\UserLogDBModel;
use App\Models\WebgameLogicModel;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Helper\UdpLog;
use Helper\UserHelper;
use Illuminate\Http\Request;

/**
 * 支付 后续可以合并至个人中心
 */
class PayController extends Controller
{

    use SimpleResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $channel = $request->input("channel", "alipay");

        $uid     = CookieModel::getCookie("uid");
        $token   = CookieModel::getCookie("token");
        $nick    = CookieModel::getCookie("nick");
        $account = CookieModel::getCookie("account");

        $account = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $ret     = $account->info($uid, $token);
        if (!$ret || $ret["code"] != 0) {
            return view('login');
        }

        $money = $ret["data"]["money"];

        $tokeninfo = $account->getPayToken($uid, $token);
        if (isset($tokeninfo['code']) && $tokeninfo['code'] == 0) {
            $paytoken = $tokeninfo['data']['paytoken'];
        } else {
            $paytoken = "";
        }

        $payRate = Config::get("common.pay_rate");

        $isdev = 0;

        $env = Library::getCurrEnv();

        $webGameModel = App::make('webGame');

        $allgame = $webGameModel->getAllGameInfo();

        return view('web.pay', compact("uid", "nick", "token", "allgame",
            "money", "payRate", "channel", "isdev", "paytoken", "env"));
    }

    /**
     * 游戏内弹窗充值
     *
     * @return [type] [description]
     */
    public function minipay(Request $request)
    {
        $openid   = $request->input("openid");
        $appid    = (int) $request->input("appid");
        $serverid = (int) $request->input("serverid");
        $num      = (int) $request->input("num", 1);
        $faceUrl  = $request->input("url", "");
        $extra1   = $request->input("extra1");
        $isdev    = $request->input("isdev", 0);
        $item     = urldecode($request->input("item"));
        $itemid   = trim($request->input("itemid"));
        $paytoken = $request->input("paytoken");
        $price    = (float) $request->input("price");
        $total    = (float) $request->input("total");
        $platform = $request->input("platform", "");
        $from     = $request->input("from", "game");

        if (!$openid || $appid <= 0 || $serverid < 0) {
            return "缺少参数";
        }

        if (!in_array($from, ["game", "vrgame"])) {
            return "参数错误";
        }

        if ($total <= 0) {
            return "金额错误";
        }

        $uid   = CookieModel::getCookie("uid");
        $token = CookieModel::getCookie("token");

        $account = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_key"));
        $ret     = $account->info($uid, $token);
        if (!$ret || $ret["code"] != 0) {
            return view('login');
        }

        $money = $ret["data"]["money"];

        $webGameModel = App::make('webGame');

        if ($from == "vrgame") {
            $game = $webGameModel->getOneGameInfo($appid);
            if (!$game) {
                return "该游戏不存在";
            }
        } else {
            $game = $webGameModel->getOneGameInfo($appid);

            if (!$game) {
                return "该游戏不存在";
            }

            if ($serverid) {
                $server = $webGameModel->getOneServer($appid, $serverid);
                if (!$server) {
                    return "该服务器不存在";
                }
            }
        }

        if ($item) {
            $game["gameb_name"] = $item;
        }

        if (!$price) {
            //  return "金额错误";
            //$price = $game["rmb_rate"] > 0 ? ceil($num / $game["rmb_rate"]) : "0";
        }

        $payRate = Config::get("common.pay_rate");

        if (strpos($faceUrl, "base64") !== false) {
            $find    = array('-', '_');
            $replace = array('+', '/');
            $faceUrl = str_replace($find, $replace, $faceUrl);
        }

        $faceUrl = $faceUrl ? $faceUrl : UserHelper::getUserFace($uid);

        $env = Library::getCurrEnv();

        $isSuperUser = $webGameModel->isTestAccount($uid, 0);

        $role = 0;
        if ($isSuperUser) {
            $role = 1;
        }

        if ($from == "game") {
            $payChannels = Library::getPayChannels("minipay_webgame", $role);
        } elseif ($from == "vrgame") {
            $payChannels = Library::getPayChannels("minipay_vrgame", $role);
        }

        $banks = config::get("bank");

        //var_dump($game);exit;
        return view('web.minipay', compact("faceUrl", "uid", "token", "appid", "price",
            "serverid", "openid", "game", "num", "item", "itemid", "money", "paytoken", "payRate", "extra1", "isdev", "total", "platform", "env", "from", "payChannels", "banks"));

    }

    /**
     * 购买回调
     * @param   string  extra1  格式 uid|type
     * @return [type] [description]
     */
    public function buyCallback(Request $request)
    {
        UdpLog::save2("buy/error", array("function" => "buyCallback", "log" => "buyCallback:start"), __METHOD__ . "[" . __LINE__ . "]");
        $tradeid  = $request->input("tradeid", 0);
        $paytoken = $request->input("paytoken");
        $amount   = $request->input("amount", 0);
        $openid   = $request->input("openid", "");
        $token    = $request->input("vrkey", "");
        $appid    = $request->input("appid", "");
        $serverid = $request->input("serverid", 0);
        $price    = $request->input("price");
        $num      = $request->input("num");
        $itemid   = $request->input("itemid");
        $extra1   = $request->input("extra1");
        $ts       = $request->input("ts", 0);
        $sign     = $request->input("sign");

        $nowstamp = time();
        if (!$tradeid || !$openid || !$sign || !$ts || !$extra1) {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "params error", "tradeid" => $tradeid, "openid" => $openid, "extra1" => $extra1), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        if ($ts + 600 < $nowstamp) {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "time is too long ago", "nowstamp" => $nowstamp, "ts" => $ts), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001, null, "time is too long ago");
        }

        $tmp  = explode("|", $extra1);
        $uid  = isset($tmp[0]) ? $tmp[0] : 0;
        $type = isset($tmp[1]) ? $tmp[1] : "";

        if (isset($_GET['//buy/callback'])) {
            unset($_GET['//buy/callback']);
        }

        $check_sign = Library::encrypt($_GET, Config::get("common.uc_paykey"));
        if ($check_sign != $sign) {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "sign error", "sign" => $sign, "check_sign" => $check_sign), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2002);
        }

        $check_openid = OpenidModel::getOpenid($appid, $uid);
        if ($openid != $check_openid) {
            UdpLog::save2("openid/fail", array("function" => "buyCallback", "result" => "false", "log" => "check_openid false", "uid" => $uid, "openid" => $openid, "check_openid" => $check_openid), __METHOD__ . "[" . __LINE__ . "]");
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "check_openid false", "check_openid" => $check_openid, "openid" => $openid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2019);
        }

        $userLogDBModel = new UserLogDBModel;
        $oinfo          = $userLogDBModel->getOneBuyOrder($uid, $tradeid);
        if ($oinfo === false) {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "getOneBuyOrder error", "oinfo" => $oinfo, "tradeid" => $tradeid, "uid" => $uid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1);
        }
        if ($oinfo && is_array($oinfo)) {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "success", "log" => "success ago"), __METHOD__ . "[" . __LINE__ . "]");
            return "success";
        }
        $newinfo             = [];
        $newinfo['orderid']  = $tradeid;
        $newinfo['uid']      = $uid;
        $newinfo['openid']   = $openid;
        $newinfo['appid']    = $appid;
        $newinfo['serverid'] = $serverid;
        $newinfo['amount']   = $amount;
        $newinfo['price']    = $price;
        $newinfo['itemid']   = $itemid;
        $newinfo['num']      = $num;
        $newinfo['extra1']   = $extra1;
        $newinfo['params']   = json_encode($_GET);
        $new                 = $userLogDBModel->newBuyOrder($uid, $newinfo);
        if (!$new) {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "newBuyOrder error", "newinfo" => $newinfo, "uid" => $uid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1, null, "创建订单失败");
        }

        $buyModel = new BuyModel;
        switch ($type) {
            case "game":
                $ret = $buyModel->buyVrGame($itemid, $extra1, $amount);
                break;
            default:
                UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "type error", "type" => $type), __METHOD__ . "[" . __LINE__ . "]");
                return Library::output(1);
                break;
        }
        if ($ret) {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "success", "log" => "success"), __METHOD__ . "[" . __LINE__ . "]");
            return "success";
        } else {
            UdpLog::save2("buy/error", array("function" => "buyCallback", "result" => "false", "log" => "callback error", "ret" => $ret), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1);
        }

    }

}
