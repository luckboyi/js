<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

// 引用Model
use App\Models\AppinfoModel;
use App\Models\ConsumeModel;
use App\Models\DataCenterStatModel;
use App\Models\LogModel;
use App\Models\OpenidModel;
use App\Models\OrderModel;
use App\Models\PassportModel;
use App\Models\PayModel;
use App\Models\ToBCheckBillDBModel;
use App\Models\ToBCheckBillModel;
use App\Models\ToBOrderModel;
use App\Models\UserModel;
use App\Models\UserOrderModel;
use Config;
use Helper\HttpRequest;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Http\Request;

class PayController extends Controller
{

    /**
     * 生成paytoken
     * 给开发商用
     */
    public function getPayToken(Request $request)
    {
        $pay_openid = trim($request->input('openid')); // 当前用户，也是扣费的用户
        $pay_uid    = intval($request->input('uid')); // 当前用户，也是扣费的用户
        $appid      = intval($request->input('game_type'));
        $token      = trim($request->input('vrkey'));
        $sign       = trim($request->input('sign'));
        $ts         = intval($request->input('ts'));

        if ((!$pay_openid && !$pay_uid) || !$appid || !$sign || !$ts) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "pay_openid && pay_uid || appid || sign || ts is null", "pay_openid" => $pay_openid, "pay_uid" => $pay_uid, "appid" => $appid, "sign" => $sign, "ts" => $ts), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        if ($pay_openid) {
            $check_openid = OpenidModel::getUid($pay_openid);
            if (!$check_openid || !isset($check_openid['uid']) || !$check_openid['uid']) {
                UdpLog::save2("openid/fail", array("function" => "getPayToken", "result" => "false", "log" => "check_openid false", "pay_uid" => $pay_uid, "pay_openid" => $pay_openid, "check_openid" => $check_openid), __METHOD__ . "[" . __LINE__ . "]");
                UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "check_openid false", "check_openid" => $check_openid), __METHOD__ . "[" . __LINE__ . "]");
                return Library::output(2019);
            }
            $check_id = $check_openid['uid'];
            if ($pay_uid && $pay_uid != $check_id) {
                UdpLog::save2("openid/fail", array("function" => "getPayToken", "result" => "false", "log" => "openid check unmatch", "pay_uid" => $pay_uid, "pay_openid" => $pay_openid, "check_openid" => $check_openid), __METHOD__ . "[" . __LINE__ . "]");
                UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "openid check unmatch", "check_openid" => $check_openid), __METHOD__ . "[" . __LINE__ . "]");
                return Library::output(2019);
            }
        } else if ($pay_uid) {
            $check_id   = $pay_uid;
            $pay_openid = OpenidModel::getOpenid($appid, $pay_uid);
        } else {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "uid openid all null"), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        /**
         * 判断登录状态
         */
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($check_id, $token);
        if (!$islogin) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "getPayToken", "result" => "false", "log" => "not login", "uid" => $check_id, "token" => $token), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1301);
        }

        /**
         * 判断是否是开发环境，是否在白名单
         */
        $appModel = new AppinfoModel;
        $appinfo  = $appModel->info($appid);
        if (!$appinfo || !is_array($appinfo)) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "appinfo error", "uid" => $check_id, "appid" => $appid, "appinfo" => $appinfo), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2303);
        }
        $paykey = $appinfo['paykey'];
        if (!$paykey) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "no paykey", "paykey" => $paykey, "appid" => $appid, "appinfo" => $appinfo), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2018);
        }

        $payModel = new PayModel;

        $check_sign = Library::encrypt($_POST, $paykey);
        if (!$check_sign || $check_sign != $sign) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "sign check error", "paykey" => $paykey, "sign" => $sign, "check_sign" => $check_sign), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2002);
        }

        $paytoken = $payModel->genPayToken($check_id, $appid);
        if (!$paytoken) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayToken", "result" => "false", "log" => "set paytoken fail", "appid" => $appid, "check_id" => $check_id, "paytoken" => $paytoken), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2002);
        }

        $data = ["paytoken" => $paytoken];
        return Library::output(0, $data);
    }

    /**
     * 生成paytoken
     * 给自己客户端用
     */
    public function getPayTokenSelf(Request $request)
    {
        $pay_uid = intval($request->input('u')); // 当前用户，也是扣费的用户
        $token   = $request->input('t');
        $sign    = trim($request->input('sign'));
        $ts      = intval($request->input('ts'));
        $appid   = 1;

        if (!$pay_uid || !$sign || !$ts) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayTokenSelf", "result" => "false", "log" => "pay_openid && pay_uid || appid || sign || ts is null", "pay_uid" => $pay_uid, "sign" => $sign, "ts" => $ts), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        /**
         * 判断登录状态
         */
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($pay_uid, $token);
        if (!$islogin) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "getPayTokenSelf", "result" => "false", "log" => "not login", "uid" => $pay_uid, "token" => $token), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1301);
        }

        $pay_openid = OpenidModel::getOpenid($appid, $pay_uid);

        $check_sign = Library::encrypt($_POST, Config::get("common.vr_client_key"));
        if (!$check_sign || $check_sign != $sign) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayTokenSelf", "result" => "false", "log" => "sign check error", "sign" => $sign, "check_sign" => $check_sign), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2002);
        }

        $payModel = new PayModel;

        $paytoken = $payModel->genPayToken($pay_uid, $appid);
        if (!$paytoken) {
            UdpLog::save2("pay.vronline.com/paytoken", array("function" => "getPayTokenSelf", "result" => "false", "log" => "set paytoken fail", "appid" => $appid, "check_id" => $pay_uid, "paytoken" => $paytoken), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2002);
        }

        $data = ["paytoken" => $paytoken, 'openid' => $pay_openid];
        return Library::output(0, $data);
    }

    /**
     * 充值平台币
     * 如果是充值，创建充值订单，不创建消费订单
     * 先不做给他人充值，如果做，就根据to_account计算出to_uid，平台币加到to_uid上
     */
    public function buyPlantb(Request $request)
    {
        Library::accessHeader();
        UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "log" => "buyPlantb:start"), __METHOD__ . "[" . __LINE__ . "]");
//        $pay_uid    = intval($request->input('pay_uid'));               // 当前登录的用户，也是扣费的用户
        $pay_uid = intval($request->input('uid')); // 当前登录的用户，也是扣费的用户
        //        $pay_openid = trim($request->input('openid'));               // 当前登录的用户，也是扣费的用户的openid
        //        $to_account = strtolower(strval(trim($request->input('to_account'))));             // 要充值的账户，我的，也可能是对方的
        $token = trim($request->input('vrkey'));
//        $appid      = intval($request->input('appid'));
        $appid = intval($request->input('game_type'));
        $appid = 1; // 充值一律充到该app下
        //        $total_amount   = floatval($request->input('total_amount'));            // 总充值金额，用户选择的金额，也就是人民币的金额，充值平台币，rmb和total_amount是一样的
        //        $rmb        = floatval($request->input('rmb'));            // 扣人民币数量
        $rmb      = floatval($request->input('pay_rmb')); // 扣人民币数量
        $channel  = strval(strtolower(trim($request->input('channel'))));
        $bank     = strtolower(trim($request->input('bank', "")));
        $paytoken = trim($request->input('paytoken'));
        $source1  = strval($request->input('source1'));
        $source2  = strval($request->input('source2'));
        $macaddr  = strval(trim($request->input('macaddr')));
        $devicesn = strval(trim($request->input('devicesn')));
        $isdev    = intval($request->input('isdev'));

        if (!$pay_uid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "pay_uid is null", "uid" => $pay_uid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "pay_uid 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(1301);
        }
        if (!$appid || !$channel) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "appid or channel is null", "appid" => $appid, "channel" => $channel), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "appid 或 channel 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2001);
        }

        if (!$paytoken) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "paytoken null", "uid" => $pay_uid, "appid" => $appid, "paytoken" => $paytoken), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "pay_uid 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2312);
        }

        /*
        $check_id = OpenidModel::getUid($pay_openid);
        if(!$check_id || !isset($check_id['uid']) || $check_id['uid'] != $pay_uid) {
        UdpLog::save2("openid/fail", array("function"=>"buyPlantb", "result" => "false", "uid" => $pay_uid, "appid" => $appid, "openid" => $pay_openid, "check_id" => $check_id), __METHOD__."[".__LINE__."]");
        UdpLog::save2("pay.vronline.com/pay", array("function"=>"buyPlantb", "result" => "false", "action" => "buyPlantb", "log" => "openid error", "appid" => $appid, "pay_uid" => $pay_uid, "pay_openid" => $pay_openid, "check_id" => $check_id), __METHOD__."[".__LINE__."]");
        $errlog = array("uid"=>$pay_uid, "appid"=>$appid, "action"=>"buyPlantb", "errmsg"=>"openid 和 uid 不一致", "tbl"=>"order", "position"=>__METHOD__."[".__LINE__."]");
        LogModel::addLog($errlog);
        return Library::output(2001);
        }
         */
        if (!$rmb) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "rmb is null", "uid" => $pay_uid, "rmb" => $rmb), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "rmb 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2306);
        }

        $payModel = new PayModel;
        /**
         * 根据支付的人民币，计算获得的平台币是否与要购买的平台币数量一致
         */
        $rate = $payModel->getVbRate($pay_uid, $rmb);
        if (!$rate) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "rate is null", "rate" => $rate), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "rate 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2);
        }
        $get_plantb = intval($rmb * $rate); // 如果有活动，充值返利，这里再加上额外的返的平台币
        if (!$get_plantb) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "get_plantb is null", "get_plantb" => $get_plantb), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "get_plantb 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2307);
        }

        /**
         * 判断登录状态
         */
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($pay_uid, $token);
        if (!$islogin) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "not login", "uid" => $pay_uid, "token" => $token), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "未登录", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(1301);
        }

        /**
         * 只有测试账号才能支付小于1RMB金额
         */
        if ($rmb < 1) {
            $isWhite = $payModel->isTestAccount($pay_uid);
            if (!$isWhite) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "rmb < 1 ", "rmb" => $rmb), __METHOD__ . "[" . __LINE__ . "]");
                return Library::output(2301);
            }
        }

        $check_token = $payModel->getPayToken($pay_uid);
        if ($check_token != $paytoken) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "paytoken error", "uid" => $pay_uid, "paytoken" => $paytoken, "check_token" => $check_token), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "paytoken error", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2312);
        }

        /*
        $loginModel  = new LoginModel;
        $to_uid   = $loginModel->getUid($to_account);
        if(!$to_uid) {
        return Library::output(2308);
        }
        if($to_uid != $pay_uid) {
        return Library::output(2305);                 // 暂时不支持给他人充值
        }
         */
        $to_uid = $pay_uid; // 暂时不支持给他人充值，如果要支持，把上面注释打开，这里删除

        $orderModel = new OrderModel;

        $orderinfo = array(
            "serverid"   => 0,
            "need_platb" => 0,
            "money"      => $rmb,
            "get_plantb" => $get_plantb,
            // "extra_plantb"  => $extra_plantb,              // 如果搞活动，会有额外平台币赠送
            "paychannel" => $channel,
            "bank"       => $bank,
            "paytoken"   => $paytoken,
            "source1"    => $source1,
            "source2"    => $source2,
            "macaddr"    => $macaddr,
            "devicesn"   => $devicesn,
            //"openid"    => $to_openid,
            "pay_uid"    => $pay_uid,
            //"pay_openid" => $pay_openid,
            "isdev"      => $isdev,
            "action"     => "plantb",
        );
        $orderid = $orderModel->newOrder($appid, $to_uid, $orderinfo);
        if (!$orderid) {
            $orderid = $orderModel->newOrder($appid, $to_uid, $orderinfo);
        }

        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "false", "log" => "create order failed", "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $to_uid, "appid" => $appid, "action" => "buyPlantb", "errmsg" => "订单写入错误 [to_uid:{$to_uid}] [orderinfo:" . json_encode($orderinfo) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2);
        }

        $signParam             = $_POST;
        $signParam['wp_pid']   = $orderid;
        $signParam['wp_uid']   = $to_uid . "|1";
        $signParam['uid']      = $to_uid;
        $signParam['jump_url'] = "http://payres.vronline.com/result/plat/{$orderid}";
        $sign                  = $payModel->createPaycenterSign($signParam);

        UdpLog::save2("pay.vronline.com/pay", array("function" => "buyPlantb", "result" => "success", "orderid" => $orderid, "sign" => $sign, "uid" => $to_uid, "wp_pid" => $orderid, "wp_uid" => $to_uid), __METHOD__ . "[" . __LINE__ . "]");
        return Library::output(0, array("orderid" => $orderid, "sign" => $sign, "uid" => $to_uid, "wp_pid" => $orderid, "wp_uid" => $signParam['wp_uid'], "jump_url" => $signParam['jump_url']));
    }

    /**
     * 平台币够，直接使用平台币购买
     */
    /**
     * 总价由开发商传入，不再校验单价、数量是否与总价相符
     * 产品2016/10/26说的
     */
    public function buyGameByPlantb(Request $request)
    {
        Library::accessHeader();
        UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "log" => "buyGameByPlantb:start"), __METHOD__ . "[" . __LINE__ . "]");
//        $pay_uid    = intval($request->input('pay_uid'));               // 当前登录的用户，也是扣费的用户
        $pay_uid    = intval($request->input('uid')); // 当前登录的用户，也是扣费的用户
        $pay_openid = trim($request->input('openid')); // 当前登录的用户，也是扣费的用户的openid
        $from       = trim($request->input('from')); // 充值来源，页游(game)，或平台(plat)，或其他。如果是页游来的，一定要传openid，其他的暂时不用
        // $to_account = strtolower(strval(trim($request->input('to_account'))));             // 要充值的账户，我的，也可能是对方的
        $token = trim($request->input('vrkey'));
//        $appid      = intval($request->input('appid'));
        $appid = intval($request->input('game_type'));
//        $serverid   = intval($request->input('serverid'));
        $serverid     = intval($request->input('sid'));
        $total_amount = round(floatval($request->input('pay_total')), 2); // 总充值金额，用户选择的金额
        $price        = floatval($request->input('price')); // 购买物品单价
        $num          = intval($request->input('num')); // 购买数量
        $item         = trim($request->input('item')); // 商品名称
        $itemid       = trim($request->input('itemid')); // 商品ID
        $gameb        = intval($request->input('gameb')); // 获得总游戏币
        $paytoken     = trim($request->input('paytoken'));
        $source1      = strval($request->input('source1'));
        $source2      = strval($request->input('source2'));
        $macaddr      = strval(trim($request->input('macaddr')));
        $devicesn     = strval(trim($request->input('devicesn')));
        $extra1       = strval($request->input('extra1'));
        $isdev        = intval($request->input('isdev'));

        if (!$appid || !$from || $price <= 0 || $num <= 0 || $total_amount <= 0) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "appid || from || price || num || total_amount is null", "appid" => $appid, "from" => $from, "price" => $price, "num" => $num, "total_amount" => $total_amount), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        if ($from == "plat" && !$pay_openid) {
            $pay_openid = OpenidModel::getOpenid($appid, $pay_uid);
        }

        if (!$paytoken) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "paytoken null", "uid" => $pay_uid, "appid" => $appid, "serverid" => $serverid, "paytoken" => $paytoken), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "pay_uid 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2312);
        }

        if (!$pay_uid || !$token || !$pay_openid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "pay_uid || token || pay_openid is null", "pay_uid" => $pay_uid, "token" => $token, "pay_openid" => $pay_openid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1301);
        }

        $check_id = OpenidModel::getUid($pay_openid);
        if (!$check_id || !isset($check_id['uid']) || $check_id['uid'] != $pay_uid || !isset($check_id['appid']) || $check_id['appid'] != $appid) {
            UdpLog::save2("openid/fail", array("function" => "buyGameByPlantb", "result" => "false", "uid" => $pay_uid, "appid" => $appid, "openid" => $pay_openid, "check_id" => $check_id), __METHOD__ . "[" . __LINE__ . "]");
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "openid error", "appid" => $appid, "pay_uid" => $pay_uid, "pay_openid" => $pay_openid, "check_id" => $check_id), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "openid 和 uid 不一致", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2001);
        }

        $payModel = new PayModel;
        $rate     = $payModel->getVbRate($pay_uid, 0);
        if (!$rate) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "rate is null", "rate" => $rate), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2);
        }
        /**
         * 充值需要扣的平台币数量
         * 不管用户选择是否使用平台币，都是要先充平台币
         * 充成平台币后，实际消费需要扣的平台币数量
         */
        $need_platb = ceil($total_amount * $rate);

        $nowstamp = time();

        /**
         * 判断登录状态
         */
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($pay_uid, $token);
        if (!$islogin) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "not login", "pay_uid" => $pay_uid, "token" => $token), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1301);
        }

        /*
        $loginModel  = new LoginModel;
        $to_uid   = $loginModel->getUid($to_account);
        if(!$to_uid) {
        return Library::output(2308);
        }
        if($to_uid != $pay_uid) {
        return Library::output(2305);                 // 暂时不支持给他人充值
        }
         */
        $to_uid    = $pay_uid;
        $to_openid = $pay_openid;
        /*
        $to_openid = OpenidModel::getOpenid($appid, $to_uid);
        if(!$to_openid) {
        }
         */

        $check_token = $payModel->getPayToken($pay_uid);
        $payModel->delPayToken($pay_uid);
        if ($check_token != $paytoken) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "paytoken error", "uid" => $pay_uid, "paytoken" => $paytoken, "check_token" => $check_token), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "paytoken error", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2312);
        }

        /**
         * 判断appid是否存在
         */
        $appModel = new AppinfoModel;
        $appinfo  = $appModel->info($appid);
        if (!$appinfo || !is_array($appinfo)) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "appinfo is null", "appid" => $appid, "appinfo" => $appinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "appinfo 读取错误", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2303);
        }
        $appkey = $appinfo['appkey'];

        $orderModel = new OrderModel;
        /**
         * 加锁
         */
        $lockid = $pay_uid;

        $ret = $orderModel->addLock($lockid);
        if (!$ret) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "add lock failed", "lockid" => $lockid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "锁未解 {$lockid}", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2304);
        }

        /**
         * 获取消耗平台币用户的信息
         * 判断平台币是否够
         */
        $userModel = new UserModel;
        $userinfo  = $userModel->extInfo($pay_uid);
        if ($userinfo === false) {
            $orderModel->delLock($lockid);
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "get userinfo failed", "pay_uid" => $pay_uid, "userinfo" => $userinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "用户扩展信息读取失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(1308);
        }
        if (!$userinfo || $userinfo['f_money'] < $need_platb || $userinfo['f_money'] <= 0) {
            $orderModel->delLock($lockid);
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "money in userinfo less than need_platb or error", "f_money" => $userinfo['f_money'], "need_platb" => $need_platb, "extinfo" => $userinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "用户扩展信息中money错误[" . json_encode($userinfo) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2311);
        }
        $balance = $userinfo['f_money'] - $need_platb;

        $consumeModel = new ConsumeModel;

        /**
         * 创建订单
         * 先创建订单，充值完成后修改订单状态
         * 原因是创建订单如果失败了，直接提示
         * 如果创建了订单，充值失败了，或者充值成功了，但没有修改订单状态成功，可以有原始订单查询
         */
        $tradeinfo = array(
            "orderid"   => 0,
            "openid"    => $pay_openid,
            "to_uid"    => $to_uid,
            "to_openid" => $to_openid,
            "serverid"  => $serverid,
            "amount"    => $need_platb,
            "price"     => $price,
            "num"       => $num,
            "item"      => $item,
            "itemid"    => $itemid,
            "balance"   => $balance,
            "paytoken"  => $paytoken,
            "source1"   => $source1,
            "source2"   => $source2,
            "macaddr"   => $macaddr,
            "devicesn"  => $devicesn,
            "extra1"    => $extra1,
            "gameb"     => $gameb,
            "stat"      => 0,
            "isdev"     => $isdev,
        );
        $tradeid = $consumeModel->newTrade($appid, $pay_uid, $tradeinfo);
        if (!$tradeid) {
            $tradeid = $consumeModel->newTrade($appid, $pay_uid, $tradeinfo);
        }
        if (!$tradeid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "add consume failed", "appid" => $appid, "pay_uid" => $pay_uid, "consume" => $tradeinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "创建消费记录失败[" . json_encode($tradeinfo) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2, null, "充值失败");
        }

        /**
         * 扣币
         */
        $sub = $userModel->subPlatb($pay_uid, $need_platb);
        UdpLog::save2("pay.vronline.com/trade", array("function" => "buyGameByPlantb", "log" => "[sub]", "result" => $sub, "orderid" => 0, "tradeid" => $tradeid, "uid" => $pay_uid, "subnum" => $need_platb, "appid" => $appid, "serverid" => $serverid, "pay_openid" => $pay_openid), __METHOD__ . "[" . __LINE__ . "]");
        if (!$sub) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "subPlatb failed", "pay_uid" => $pay_uid, "sub_platb" => $need_platb, "orderid" => 0, "tradeid" => $tradeid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "扣币失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            $orderModel->delLock($lockid);
            return Library::output(2);
        }

        /**
         * 再读一次平台币，判断是否有刷的可能
         */
        $userinfo2 = $userModel->extInfo($pay_uid);
        if (!$userinfo2 || !isset($userinfo2['f_money']) || $userinfo2['f_money'] < 0) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "f_money is less than 0 after subPlatb", "need_platb" => $need_platb, "extInfo" => $userinfo2), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "扣币后，money小于0[" . json_encode($userinfo2) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            $orderModel->delLock($lockid);
            return Library::output(2);
        }
//        $balance = $userinfo2['f_money'];

        $consume              = array();
        $consume['tradeid']   = $tradeid;
        $consume['amount']    = $need_platb;
        $consume['to_uid']    = $to_uid;
        $consume['to_openid'] = $to_openid;
        $consume['serverid']  = $serverid;
        $consume["price"]     = $price;
        $consume["num"]       = $num;
        $consume["item"]      = $item;
        $consume["itemid"]    = $itemid;
        $consume['extra1']    = $extra1;
        $consume['paytoken']  = $paytoken;
        $consume['cip']       = Library::real_ip();
        $consume['isdev']     = $isdev;
        $consume['ts']        = time();

        $call_ret = $payModel->callDeveloper($appid, $consume);
        $callinfo = $payModel->getDeliverInfo();
        //UdpLog::save2("pay.vronline.com/trade", array("function"=>"buyGameByPlantb", "result" => $call_ret, "log" => "[delivery]", "ret" => $call_ret, "tradeid" => $tradeid, "orderid" => 0, "appid" => $appid, "serverid" => $serverid, "pay_uid" => $pay_uid, "pay_openid" => $pay_openid, "to_uid" => $to_uid, "to_openid" => $to_openid, "amount" => $need_platb, "callinfo" => $callinfo), __METHOD__."[".__LINE__."]");

        if ($call_ret) {
            $status = Config::get("pay.pay_status.success");
        } else {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "callDeveloper error", "appid" => $appid, "consume" => $consume, "error" => $callinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "发货失败 [" . json_encode($callinfo) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            $status = Config::get("pay.pay_status.delivery_failed");
        }

        $upd = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => $status, "errmsg" => json_encode($callinfo['errmsg']), "payurl" => $callinfo['real_payurl'], "delivery_time" => $callinfo['delivery_time']));
        if (!$upd) {
            $upd = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => $status, "errmsg" => json_encode($callinfo['errmsg']), "payurl" => $callinfo['real_payurl'], "delivery_time" => $callinfo['delivery_time']));
        }

        /**
         * 发货成功，订单写失败，不处理，继续写用户订单
         * 如果发货失败，不写用户订单，提示失败
         */
        if (!$upd) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "update consume status failed", "trade" => $tradeid, "consume" => $tradeinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "消费记录写失败[{$tradeid}][" . json_encode($tradeinfo) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
        }

        /**
         * 解锁
         */
        $orderModel->delLock($lockid);

        if (!$call_ret) {
            return Library::output(2);
        }

        /**
         * 充值失败的不写用户订单
         * 用户订单只看到成功扣币，充值成功的
         * 用户订单写失败了，也不提示用户失败，实际是充值成功的
         */
        if ($call_ret) {
            /**
             * 添加用户的消费记录
             * 给用户看的
             */
            $userOrderModel = new UserOrderModel;
            $userOrder      = array(
                "orderid"      => 0,
                "tradeid"      => $tradeid,
                "uid"          => $pay_uid,
                "openid"       => $pay_openid,
                "appid"        => $appid,
                "other_uid"    => $to_uid,
                "other_openid" => $to_openid,
                "serverid"     => $serverid,
                "plantb"       => $need_platb,
                "price"        => $price,
                "num"          => $num,
                "item"         => $item,
                "itemid"       => $itemid,
                "balance"      => $balance,
                "stat"         => 8,
                "action"       => "game",
                "type"         => 9,
            );
            $ret2 = $userOrderModel->newOrder(0, $tradeid, $userOrder);
            if (!$ret2) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "false", "log" => "add user order failed", "tradeid" => $tradeid, "userorder" => $userOrder), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGameByPlantb", "errmsg" => "用户订单记录写失败[" . json_encode($userOrder) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                $orderModel->delLock($lockid);
                return Library::output(2);
            }
        }

        /**
         * 发送统计
         */
        $properties = [
            "tradeid"               => $tradeid,
            "payunit"               => "vrb",
            "appid"                 => $appid,
            "serverid"              => $serverid,
            "openid"                => $pay_openid,
            "amount"                => $total_amount,
            "item"                  => $item,
            "itemprice"             => $price,
            "itemcount"             => $num,
            "from"                  => $from,
            "_pay_currency_surplus" => $balance,
            "isall"                 => 1, // 表示日志数据是全的，不需要再从数据库补数据
        ];
        DataCenterStatModel::stat("vrplat", "buyitem", $pay_uid, $properties);

        UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGameByPlantb", "result" => "success", "tradeid" => $tradeid), __METHOD__ . "[" . __LINE__ . "]");
        return Library::output(0, array("tradeid" => $tradeid, "itemid" => intval($itemid), "from" => $from), "充值成功");
    }

    /**
     * 创建订单
     * 如果购买，看平台币够不够
     *      如果平台币不够，创建充值订单，先充平台币，在创建消费订单扣平台币
     *      如果平台币够，不创建充值订单，只创建消费订单，对应充值订单号是0
     * 平台币是充给付款方的，然后从付款方购买游戏道具给赠送方
     */
    /**
     * 总价由开发商传入，不再校验单价、数量是否与总价相符
     * 产品2016/10/26说的
     */
    public function buyGame(Request $request)
    {
        Library::accessHeader();
        UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "log" => "buyGame:start"), __METHOD__ . "[" . __LINE__ . "]");
//        $pay_uid    = intval($request->input('pay_uid'));               // 当前登录的用户，也是扣费的用户
        $pay_uid    = intval($request->input('uid')); // 当前登录的用户，也是扣费的用户
        $pay_openid = trim($request->input('openid')); // 当前登录的用户，也是扣费的用户的openid
        $from       = trim($request->input('from')); // 充值来源，页游(game)，或平台(plat)，或其他。如果是页游来的，一定要传openid，其他的暂时不用
        // $to_account = strval(strtolower(trim($request->input('to_account'))));             // 要充值的账户，我的，也可能是对方的
        $token = trim($request->input('vrkey'));
//        $appid      = intval($request->input('appid'));
        $appid = intval($request->input('game_type'));
//        $serverid   = intval($request->input('serverid'));
        $serverid     = intval($request->input('sid'));
        $total_amount = round(floatval($request->input('pay_total')), 2); // 总充值金额，总共要充给游戏的金额，
        //        $rmb        = floatval($request->input('rmb'));                 // 扣人民币的金额
        $price     = floatval($request->input('price')); // 购买物品单价
        $num       = intval($request->input('num')); // 购买数量
        $item      = trim($request->input('item')); // 商品名称
        $itemid    = trim($request->input('itemid')); // 商品ID
        $rmb       = floatval($request->input('pay_rmb')); // 扣人民币的金额，在总共充值的金额中，去掉由平台币抵扣的部分，剩下实际要充值到平台币的数量
        $channel   = strval(strtolower(trim($request->input('channel'))));
        $bank      = strtolower(trim($request->input('bank', "")));
        $paytoken  = trim($request->input('paytoken'));
        $source1   = strval($request->input('source1'));
        $source2   = strval($request->input('source2'));
        $macaddr   = strval(trim($request->input('macaddr')));
        $devicesn  = strval(trim($request->input('devicesn')));
        $extra1    = strval($request->input('extra1'));
        $gameb     = intval($request->input('gameb')); // 充值获得的游戏币数量
        $isdev     = intval($request->input('isdev'));
        $useplantb = intval($request->input('pay_vr')); // 是否使用平台币

        if (!$appid || !$from || !$channel || $price <= 0 || $num <= 0 || $total_amount <= 0) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "appid || from || channel || price || num || total_amount is null", "appid" => $appid, "from" => $from, "channel" => $channel, "price" => $price, "num" => $num, "total_amount" => $total_amount), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        if (!$paytoken) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "paytoken null", "uid" => $pay_uid, "appid" => $appid, "serverid" => $serverid, "paytoken" => $paytoken), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyGame", "errmsg" => "pay_uid 为空", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2312);
        }

        if ($from == "plat" && !$pay_openid) {
            $pay_openid = OpenidModel::getOpenid($appid, $pay_uid);
        }

        if (!$pay_uid || !$token || !$pay_openid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "pay_uid || token || pay_openid is null", "pay_uid" => $pay_uid, "token" => $token, "pay_openid" => $pay_openid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1301);
        }

        $check_id = OpenidModel::getUid($pay_openid);
        if (!$check_id || !isset($check_id['uid']) || $check_id['uid'] != $pay_uid || !isset($check_id['appid']) || $check_id['appid'] != $appid) {
            UdpLog::save2("openid/fail", array("function" => "buyGame", "result" => "false", "uid" => $pay_uid, "appid" => $appid, "openid" => $pay_openid, "check_id" => $check_id), __METHOD__ . "[" . __LINE__ . "]");
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "openid error", "appid" => $appid, "pay_uid" => $pay_uid, "pay_openid" => $pay_openid, "check_id" => $check_id), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buygame", "errmsg" => "openid 和 uid 不一致", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2001);
        }

        $payModel = new PayModel;
        $rate     = $payModel->getVbRate($pay_uid, $rmb);
        if (!$rate) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "rate is null", "rate" => $rate), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2);
        }
        /**
         * 充值后，能获得的平台币数量
         * 如果做活动，还要加上活动赠送的数量
         */
        $get_plantb = intval($rmb * $rate);
        /**
         * 充值需要扣的平台币数量
         * 不管用户选择是否使用平台币，都是要先充平台币
         * 充成平台币后，实际消费需要扣的平台币数量
         */
        $need_platb = ceil($total_amount * $rate);
        if ($need_platb <= 0) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "need_platb error", "need_platb" => $need_platb), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2307);
        }

        /**
         * 判断登录状态
         */
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($pay_uid, $token);
        if (!$islogin) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "not login", "pay_uid" => $pay_uid, "token" => $token), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1301);
        }

        /*
        $loginModel  = new LoginModel;
        $to_uid   = $loginModel->getUid($to_account);
        if(!$to_uid) {
        return Library::output(2308);
        }
        if($to_uid != $pay_uid) {
        return Library::output(2305);                 // 暂时不支持给他人充值
        }
         */
        $to_uid    = $pay_uid;
        $to_openid = $pay_openid;
        /*
        $to_openid = OpenidModel::getOpenid($appid, $to_uid);
        if(!$to_openid) {
        }
         */

        $check_token = $payModel->getPayToken($pay_uid);
        if ($check_token != $paytoken) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "paytoken error", "uid" => $pay_uid, "paytoken" => $paytoken, "check_token" => $check_token), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("uid" => $pay_uid, "appid" => $appid, "action" => "buyGame", "errmsg" => "paytoken error", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2312);
        }

        /**
         * 判断appid是否存在
         */
        $appModel = new AppinfoModel;
        $appinfo  = $appModel->info($appid);
        if (!$appinfo || !is_array($appinfo)) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "appinfo error", "appid" => $appid, "appinfo" => $appinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGame", "errmsg" => "app信息读失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2303);
        }
        $appkey = $appinfo['appkey'];
        $paykey = $appinfo['paykey'];

        $orderModel = new OrderModel;
        /**
         * 加锁
         */
        $lockid = $pay_uid;
        $ret    = $orderModel->addLock($lockid);
        if (!$ret) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "add lock failed", "lockid" => $lockid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGame", "errmsg" => "未解锁", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2304);
        }

        /**
         * 获取加平台币的用户信息
         * 如果是消费用户，判断平台币 + rmb 是否等于总充值额
         */
        $userModel = new UserModel;
        $extInfo   = $userModel->extInfo($pay_uid);
        if (!$extInfo || !isset($extInfo['f_money']) || !$extInfo['f_money']) {
            $has_plantb = 0;
        } else {
            $has_plantb = $extInfo['f_money'];
        }

        /**
         * 充值后，平台币余额，未扣前的
         */
        $balance1 = $get_plantb + $has_plantb;

        /**
         * 充值并消费，购买游戏币后，平台币余额
         */
        $balance2 = $get_plantb + $has_plantb - $need_platb;

        /**
         * 如果使用平台币，计算rmb+平台币总额够不够付款
         * 如果不使用平台币，计算rmb够不够付款
         */
        if ($useplantb == 1) {
            if ($get_plantb + $has_plantb < $need_platb) {
                $orderModel->delLock($lockid);
                UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "useplantb=1, get_plantb + has_plantb < need_plantb", "get_plantb" => $get_plantb, "has_plantb" => $has_plantb, "need_platb" => $need_platb), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGame", "errmsg" => "使用平台币，充值总额小于消费总额", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return Library::output(2310);
            }
        } else {
            if ($get_plantb < $need_platb) {
                $orderModel->delLock($lockid);
                UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "useplantb=0, get_plantb < need_plantb", "get_plantb" => $get_plantb, "need_platb" => $need_platb), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGame", "errmsg" => "不使用平台币，充值总额小于消费总额", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return Library::output(2310);
            }
        }

        $consumeModel = new ConsumeModel;
        $tradeid      = $consumeModel->genTradeid($appid, $pay_uid);

        $orderinfo = array(
            "tradeid"    => $tradeid,
            "serverid"   => $serverid,
            "openid"     => $pay_openid,
            "pay_uid"    => $pay_uid, // 付款方和接受方都是同一人，在消费订单里，是发货给接收方的
            "pay_openid" => $pay_openid, // 付款方和接受方都是同一人
            "need_platb" => $need_platb,
            "money"      => $rmb,
            "get_plantb" => $get_plantb,
            // "extra_plantb"  => $extra_plantb,                // 如果搞活动，会有额外平台币赠送
            "balance"    => $balance1, // 这次充值成功后，余额应该是这么多
            "paychannel" => $channel,
            "bank"       => $bank,
            "paytoken"   => $paytoken,
            "source1"    => $source1,
            "source2"    => $source2,
            "macaddr"    => $macaddr,
            "devicesn"   => $devicesn,
            "extra1"     => $extra1,
            "isdev"      => $isdev,
            "action"     => "game",
        );
        $orderid = $orderModel->newOrder($appid, $pay_uid, $orderinfo); // 平台币是充给付款方的
        if (!$orderid) {
            $orderid = $orderModel->newOrder($appid, $pay_uid, $orderinfo);
        }
        if (!$orderid) {
            $orderModel->delLock($lockid);
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "create order failed", "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGame", "errmsg" => "充值订单写失败[" . json_encode($orderinfo) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2);
        }

        $tradeinfo = array(
            "orderid"    => $orderid,
            "openid"     => $pay_openid,
            "serverid"   => $serverid,
            "amount"     => $need_platb,
            "price"      => $price,
            "num"        => $num,
            "item"       => $item,
            "itemid"     => $itemid,
            "balance"    => $balance2, // 这次充值成功后，余额应该是这么多
            "pay"        => $rmb,
            "paychannel" => $channel,
            "gameb"      => $gameb,
            "paytoken"   => $paytoken,
            "source1"    => $source1,
            "source2"    => $source2,
            "macaddr"    => $macaddr,
            "devicesn"   => $devicesn,
            "extra1"     => $extra1,
            "to_uid"     => $to_uid,
            "to_openid"  => $to_openid,
            "isdev"      => $isdev,
        );
        $tradeid = $consumeModel->newTrade($appid, $pay_uid, $tradeinfo, $tradeid);
        if (!$tradeid) {
            $tradeid = $consumeModel->newTrade($appid, $pay_uid, $tradeinfo, $tradeid);
            if (!$tradeid) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "create consume failed", "appid" => $appid, "pay_uid" => $pay_uid, "tradeid" => $tradeid, "tradeinfo" => $tradeinfo), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGame", "errmsg" => "消费订单写失败[" . json_encode($tradeinfo) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
            }
        }

        /**
         * 解锁
         */
        $orderModel->delLock($lockid);

        if (!$orderid || !$tradeid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "false", "log" => "add order failed", "orderid" => $orderid, "tradeid" => $tradeid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => "", "uid" => $pay_uid, "openid" => $pay_openid, "appid" => $appid, "action" => "buyGame", "errmsg" => "订单记录写失败[{$orderid}][{$tradeid}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2);
        }

        $signParam             = $_POST;
        $signParam['wp_pid']   = $orderid;
        $signParam['wp_uid']   = $pay_uid . "|1";
        $signParam['uid']      = $pay_uid;
        $signParam['jump_url'] = "http://payres.vronline.com/result/{$from}/{$orderid}";
        $sign                  = $payModel->createPaycenterSign($signParam);

        UdpLog::save2("pay.vronline.com/pay", array("function" => "buyGame", "result" => "success", "log" => "success", "orderid" => $orderid, "tradeid" => $tradeid, "sign" => $sign, "uid" => $pay_uid, "wp_pid" => $orderid, "wp_uid" => $pay_uid), __METHOD__ . "[" . __LINE__ . "]");
        return Library::output(0, array("orderid" => $orderid, "tradeid" => $tradeid, "sign" => $sign, "uid" => $pay_uid, "wp_pid" => $orderid, "wp_uid" => $signParam['wp_uid'], "jump_url" => $signParam['jump_url']));
    }

    /**
     * 回调
     * 分充值平台币，还是充游戏
     * 充值完后，肯定要先充平台币，再判断是不是要再充游戏
     * 先读订单信息，判断充值类型，判断是否已经充值成功
     * 如果充值成功，判断是否需要消费，如果没有，就结束
    如果有，再读消费表，看消费是否成功，如果成功，就结束，如果不成功，等待处理消费数据
     * 如果充值未成功，判断是否需要消费，如果不需要消费，则加平台币
    如果需要消费，平台币不加，直接扣，并修改充值状态为成功，然后修改消费表为已扣币，之后处理消费数据
     *
     * 处理消费数据如果是未处理，则先扣币（未处理，是不是上次修改消费表失败了？因为币是没有加回去，直接扣的）
     * @param   array  request
     * @return  int state  注册状态码
     */
    public function callBack(Request $request)
    {
        UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack", "log" => "callBack:start"), __METHOD__ . "[" . __LINE__ . "]");
        $orderid       = trim($request->input('app_order_id'));
        $paycenterid   = trim($request->input('order_id'));
        $channel_order = trim($request->input('merchant_id'));
        $uid           = trim($request->input('uid'));
        $sid           = trim($request->input('sid'));
        $time          = trim($request->input('time'));
        $coins         = trim($request->input('coins'));
        $money         = trim($request->input('money'));
        $sign          = trim($request->input('sign'));

        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack", "result" => "false", "log" => "orderid is null", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"2","error_msg":"参数不全"}';
        }
        $payModel = new PayModel;
        $check    = $payModel->createPaycenterCallBackSign($uid, $sid, $time, $paycenterid, $orderid, $coins, $money, $channel_order);
        if ($sign != $check) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack", "result" => "false", "log" => "sign check error", "sign" => $sign, "check" => $check), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "uid" => $uid, "action" => "callback", "errmsg" => "签名错误", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return '{"error_no":"3","error_msg":"验证失败"}';
        }
        $addb_result = false;
        $lockid      = $orderid;
        $addlock     = Library::addLock($lockid);
        $ret         = $payModel->callBack($orderid, $paycenterid, $channel_order, $money, $addlock, $addb_result);
        Library::delLock($lockid);

        /**
         * 平台币添加成功，就返回成功
         * 消费失败，由平台自己补单
         */
        if ($addb_result) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack", "result" => "success"), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"1","error_msg":"成功"}';
        } else {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack", "result" => "false", "log" => "callBack failed", "orderid" => $orderid, "paycenterid" => $paycenterid, "channel_order" => $channel_order, "money" => $money, "addb_result" => $addb_result), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"-6","error_msg":"充值失败"}';
        }
    }

    /**
     * 充值结果页面
     */
    public function payresult($from, $orderid)
    {
        $from    = trim($from);
        $orderid = trim($orderid);
        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/payres", array("result" => "false", "log" => "orderid is null", "from" => $from, "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return view('pay.fail');
        }
        $neworderid = Library::genCPOrderid($orderid);

        $itemid     = 0;
        $orderModel = new OrderModel;
        $order      = $orderModel->getOrderById($orderid);
        if (!$order || !is_array($order) || !isset($order['stat'])) {
            UdpLog::save2("pay.vronline.com/payres", array("result" => "false", "log" => "order error", "from" => $from, "orderid" => $orderid, "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
            return view('pay.fail', ["from" => $from, "orderid" => $neworderid, 'queryorderid' => $orderid]);
        }
        if ($order['stat'] != Config::get("pay.pay_status.success")) {
            return view('pay.success', ["from" => $from, "orderid" => $neworderid, "itemid" => $itemid, 'wait' => 1, 'queryorderid' => $orderid]);
        }
        if (isset($order['tradeid']) && $order['tradeid']) {
            $consumeModel = new ConsumeModel;
            $consume      = $consumeModel->getTradeById($order['tradeid']);
            if (!$consume || !is_array($consume) || !isset($consume['stat']) || $consume['stat'] != Config::get("pay.pay_status.success")) {
                UdpLog::save2("pay.vronline.com/payres", array("result" => "false", "log" => "consume error", "from" => $from, "orderid" => $orderid, "order" => $order, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]");
                return view('pay.fail', ["from" => $from, "orderid" => $neworderid, 'queryorderid' => $orderid]);
            }
            $itemid = $consume['itemid'];
        }
        return view('pay.success', ["from" => $from, "orderid" => $neworderid, "itemid" => $itemid, 'queryorderid' => $orderid]);
    }

    /**
     * 用户订单记录
     */
    public function userOrders(Request $request)
    {
        $uid	= intval($request->input('uid'));
        $token	= trim($request->input('token'));
        $type	= intval($request->input('type', 0));
        $page	= intval($request->input('page', 1));
        $len    = intval($request->input('len', 20));

		if(!$uid || !$token) {
			return Library::output(1301);
		}

        $passport = new PassportModel;
        $islogin  = $passport->isLogin($uid, $token);
        if (!$islogin) {
            return Library::output(1301);
        }

        $payModel = new PayModel;
        $order	  = $payModel->getUserOrder($uid, $type, $page, $len);
		if(!is_array($order)) {
            return Library::output(1);
		}
        return Library::output(0, $order);
    }

    /**
     * 创建线下体验店订单
     */
    public function create2bOrder(Request $request)
    {
        Library::accessHeader();
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bOrder", "log" => "create2bOrder:start"), __METHOD__ . "[" . __LINE__ . "]");
        $params = json_decode($request->input('params', '[]'), true);
//        $params = json_decode($request->input('params', json_encode(["uid" => 199, "try_uid" => "", "callback_url" => "", "jump_url" => "", "surl" => "", "game_type" => 1000017, "sid" => "s1", "wp_pid" => "", "wp_uid" => "2CDA0B1491CCF222E6799C2A8B6056BF", "pay_rmb" => 0.01, "login_openid" => "", "login_type" => 0, "attach" => "", "game_owner" => "", "is_contract" => "", "action" => "alipayh5vr", "product_id" => 6, "user_ip" => "192.168.0.1", "type" => "game"])), true);
        $merchantid  = isset($params['uid']) ? $params['uid'] : ""; // 当前登录的用户，也是扣费的用户
        $terminal_sn = isset($params['wp_uid']) ? $params['wp_uid'] : "";
        $appid       = isset($params['game_type']) ? $params['game_type'] : "";
        $appname     = isset($params['appname']) ? $params['appname'] : "";
        $total_rmb   = isset($params['pay_rmb']) ? $params['pay_rmb'] : ""; // 总充值金额，用户选择的金额，包括人民币+优惠券
        $pay_rmb     = isset($params['pay_rmb']) ? $params['pay_rmb'] : ""; // 扣人民币数量
        $channel     = isset($params['action']) ? $params['action'] : "";
        $sellid      = isset($params['product_id']) ? $params['product_id'] : "";
        $type        = isset($params['type']) ? $params['type'] : "time"; // 付费类型，time:按时间付费;game:按游戏付费;
        if (!$merchantid || !$terminal_sn || !$appid || !$channel || $total_rmb <= 0 || $pay_rmb <= 0) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bOrder", "result" => "false", "log" => "merchantid or terminal_sn or appid or channel is null", "merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "appid" => $appid, "channel" => $channel), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        $toBOrderModel       = new ToBOrderModel;
        $toBCheckBillDBModel = new ToBCheckBillDBModel;

        $orderinfo = array(
            "total_rmb"   => $total_rmb,
            "pay_rmb"     => $pay_rmb,
            "pay_channel" => $channel,
            "sellid"      => $sellid,
            "type"        => $type,
            "appid"       => $appid,
        );
        if ($type == "game") {
            $appidFee = $toBCheckBillDBModel->getAppFee($appid);
            if ($appidFee === null) {
                $orderinfo['cp_fee'] = 0;
            } else {
                $orderinfo['cp_fee'] = isset($appidFee['rmb']) && $appidFee['rmb'] > 0 ? $appidFee['rmb'] : 0;
            }
        } else {
            $orderinfo['cp_fee'] = 0;
        }

        /**
         * 计算平台的分成，不能为负
         */
        if ($total_rmb - $orderinfo['cp_fee'] <= 0) {
            $orderinfo['plat_fee'] = 0;
        } else {
            $orderinfo['plat_fee'] = ceil(($total_rmb - $orderinfo['cp_fee']) * 100 * Config::get("common.2b_plat_rate")) / 100;
        }
        $orderinfo['merchant_fee'] = $total_rmb - $orderinfo['cp_fee'] - $orderinfo['plat_fee'];

        $orderid = $toBOrderModel->newOrder($terminal_sn, $merchantid, $orderinfo);
        if (!$orderid) {
            $orderid = $toBOrderModel->newOrder($terminal_sn, $merchantid, $orderinfo);
        }

        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bOrder", "result" => "false", "log" => "create order failed", "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2);
        }
        $userIp                  = Library::real_ip();
        $payModel                = new PayModel;
        $signParam               = [];
        $signParam['uid']        = $terminal_sn;
        $signParam['sid']        = 1;
        $signParam['game_type']  = $appid;
        $signParam['pay_rmb']    = $pay_rmb;
        $signParam['action']     = $channel;
        $signParam['product_id'] = $sellid;
        $signParam['wp_pid']     = $orderid;
        $signParam['wp_uid']     = $terminal_sn . "|1";
        $signParam['user_ip']    = $userIp;
        $signParam['jump_url']   = 'http://tob.vronline.com/pay/#/back/' . $merchantid . '/' . $terminal_sn . '/' . $appid . '/' . $sellid . "/" . $orderid;
        $sign                    = $payModel->createPaycenterSign($signParam);

        $repeatParams = ["orderid" => $orderid, "merchantid" => $merchantid, "merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "sellid" => $sellid, "ts" => time()];
        Library::encrypt($repeatParams, Config::get("common.uc_paykey"), $postParam);
        $p         = http_build_query($postParam);
        $repeaturl = "http://pay.vronline.com/repeat2b?" . $p;

        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bOrder", "result" => "success", "orderid" => $orderid, "sign" => $sign, "merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "appid" => $appid, "wp_pid" => $orderid, "wp_uid" => $merchantid, "repeaturl" => $repeaturl), __METHOD__ . "[" . __LINE__ . "]");
        return Library::output(0, array("orderid" => $orderid, "sign" => $sign, "uid" => $signParam['uid'], "wp_pid" => $orderid, "wp_uid" => $signParam['wp_uid'], "jump_url" => $signParam['jump_url'], 'user_ip' => $userIp, "repeaturl" => $repeaturl, "sid" => $signParam['sid']));
    }

    /**
     * 回调发货
     * @param   array  request
     * @return  int state  注册状态码
     */
    public function callBack2b(Request $request)
    {
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b", "log" => "callBack2b:start"), __METHOD__ . "[" . __LINE__ . "]");
        $orderid       = trim($request->input('app_order_id'));
        $paycenterid   = trim($request->input('order_id'));
        $channel_order = trim($request->input('merchant_id'));
        $uid           = trim($request->input('uid'));
        $sid           = trim($request->input('sid'));
        $time          = trim($request->input('time'));
        $coins         = trim($request->input('coins'));
        $money         = trim($request->input('money'));
        $paytype       = trim($request->input('paytype', "pay"));
        $sign          = trim($request->input('sign'));

        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b", "result" => "false", "log" => "orderid is null", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"2","error_msg":"参数不全"}';
        }
        $payModel = new PayModel;
        $check    = $payModel->createPaycenterCallBackSign($uid, $sid, $time, $paycenterid, $orderid, $coins, $money, $channel_order);
        if ($sign != $check) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b", "result" => "false", "log" => "pay center sign check error", "sign" => $sign, "check" => $check), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"3","error_msg":"验证失败"}';
        }

        /**
         * 先加锁
         */
        $orderModel = new OrderModel;
        $ret        = Library::addLock($orderid);
        if (!$ret) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b", "result" => "false", "log" => "add lock failed", "lockid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2024);
        }

        $ret = $payModel->callBack2b($orderid, $paycenterid, $channel_order, $money, $paytype, true);

        Library::delLock($orderid);
        /**
         * 平台币添加成功，就返回成功
         * 消费失败，由平台自己补单
         */
        if ($ret) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b", "result" => "success"), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"1","error_msg":"成功"}';
        } else {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b", "result" => "false", "log" => "callBack2b failed", "orderid" => $orderid, "paycenterid" => $paycenterid, "channel_order" => $channel_order, "money" => $money), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"-6","error_msg":"充值失败"}';
        }
    }

    /**
     * 重复发货通知，只通知，不加商家余额
     * 要校验签名是否正确
     * 读订单信息，回调补发。如果订单不是成功状态，修改状态，否则不修改
     * 链接由创建订单接口生成
     * @param   array  request
     * @return  json
     */
    public function repeat2b(Request $request)
    {
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "repeat2b", "log" => "repeat2b:start"), __METHOD__ . "[" . __LINE__ . "]");
        $orderid     = trim($request->input('orderid'));
        $merchantid  = trim($request->input('merchantid'));
        $terminal_sn = trim($request->input('terminal_sn'));
        $sellid      = trim($request->input('sellid'));
        $ts          = trim($request->input('ts'));
        $sign        = trim($request->input('sign'));

        if (!$orderid || !$merchantid || !$terminal_sn || !$sellid || !$ts || !$sign) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "repeat2b", "result" => "false", "log" => "params error", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }
        if (isset($_GET['//repeat2b'])) {
            unset($_GET['//repeat2b']);
        }
        $check_sign = Library::encrypt($_GET, Config::get("common.uc_paykey"));
        if ($check_sign != $sign) {
            return Library::output(2002);
        }

        /**
         * 先读订单信息
         */
        $toBOrderModel = new ToBOrderModel;

        /**
         * 查订单信息
         */
        $orderinfo = $toBOrderModel->getOrderById($orderid);
        if (!$orderinfo || !is_array($orderinfo)) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "repeat2b", "result" => "false", "log" => "getOrderById failed", "orderid" => $orderid, "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1);
        }
        if ($orderinfo['merchantid'] != $merchantid || $orderinfo['terminal_sn'] != $terminal_sn || $orderinfo['sellid'] != $sellid) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "repeat2b", "result" => "false", "log" => "params unmatch to orderinfo", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2002);
        }
        /**
         * 先加锁
         */
        $orderModel = new OrderModel;
        $ret        = $orderModel->addLock($orderid);
        if (!$ret) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "repeat2b", "result" => "false", "log" => "add lock failed", "lockid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "action" => "callBack2b", "errmsg" => "加锁失败 [{$orderid}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return Library::output(2024);
        }

        $payModel = new PayModel;
        $ret      = $payModel->callBack2b($orderid, $orderinfo['paycenter_orderid'], $orderinfo['paychannel_orderid'], $orderinfo['pay_rmb'], "repeat", true, $orderinfo);

        $orderModel->delLock($orderid);
        return Library::output(0);
    }

    /**
     * 创建线下体验店退款订单
     * @param   int     merchantid  商家ID
     * @param   string  token       登录token
     * @param   string  orderid     原订单号
     */
    public function create2bRefundOrder(Request $request)
    {
        Library::accessHeader();
        return Library::output(2);

        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "log" => "create2bRefundOrder:start"), __METHOD__ . "[" . __LINE__ . "]");
        $merchantid = $request->input('merchantid');
        $token      = $request->input('token');
        $oldorderid = $request->input('orderid');
        if (!$merchantid || !$token || !$oldorderid) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "merchantid or orderid or token is null", "merchantid" => $merchantid, "orderid" => $orderid, "token" => $token), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        /**
         * 付款订单渠道对应的退款订单渠道
         */
        $refundChannel = [
            "alipayh5vr" => "vrrefund",
            "wechath5vr" => "vrrefund",
        ];

        $nowstamp = time();

        /**
         * 判断是否登录
         */
        $passport = new PassportModel;
        $login    = $passport->isLoginTurbo($merchantid, "login_token", $token);
        if (!$login) {
            return Library::output(1301);
        }

        /**
         * 查要退款的订单信息，核对信息
         */
        $toBOrderModel = new ToBOrderModel;
        $oInfo         = $toBOrderModel->getOrderById($oldorderid);
        if (!$oInfo || !is_array($oInfo)) {
            return Library::output(1, null, "订单信息查询失败");
        }
        if ($oInfo['status'] == 0) {
            return Library::output(1, null, "订单未付款");
        }
        if ($oInfo['refund_pay_orderid']) {
            return Library::output(1, null, "订单已退款");
        }
        $paytime    = $oInfo['paytime'] ? strtotime($oInfo['paytime']) : 0;
        $paychannel = $oInfo['pay_channel'];

        $channel = isset($refundChannel[$paychannel]) && $refundChannel[$paychannel] ? $refundChannel[$paychannel] : "";
        if (!$channel) {
            return Library::output(1, null, "订单渠道信息查询失败");
        }
        $paycenter_orderid = $oInfo['paycenter_orderid'];

        if ($merchantid != $oInfo['merchantid']) {
            return Library::output(1, null, "订单信息查询失败");
        }

        /**
         * 2个小时以前的订单不能退款
         */
        if ($nowstamp - $paytime > 3600 * 2) {
            return Library::output(502, null, "超过2个小时的订单不能退款"); // *********************************************************************************************
        }

        /**
         * 生成退款订单
         */
        $refundFee = $oInfo['merchant_fee'];
        $orderinfo = array(
            "total_rmb"          => 0 - $oInfo['total_rmb'],
            "pay_rmb"            => 0 - $oInfo['pay_rmb'],
            "cp_fee"             => 0 - $oInfo['cp_fee'],
            "plat_fee"           => 0 - $oInfo['plat_fee'],
            "merchant_fee"       => 0 - $oInfo['merchant_fee'],
            "coupon_code"        => $oInfo['coupon_code'],
            "coupon_amount"      => $oInfo['coupon_amount'],
            "type"               => $oInfo['type'],
            "sellid"             => $oInfo['sellid'],
            "appid"              => $oInfo['appid'],
            "appname"            => $oInfo['appname'],
            "pay_channel"        => $channel,
            "paytype"            => 1,
            "refund_pay_orderid" => $oldorderid,
        );

        $terminal_sn = $oInfo['terminal_sn'];
        $merchantid  = $oInfo['merchantid'];
        $orderid     = $toBOrderModel->newOrder($terminal_sn, $merchantid, $orderinfo);
        if (!$orderid) {
            $orderid = $toBOrderModel->newOrder($terminal_sn, $merchantid, $orderinfo);
        }

        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "create order failed", "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2);
        }

        /**
         * 商家订单中插入一个退款订单记录，退款单号 paycenter_orderid 和 paychannel_orderid 置空，状态是成功，等退款成功后回调修改 paycenter_orderid 和 paychannel_orderid
         */
        $orderinfo["merchantid"]  = $merchantid;
        $orderinfo["terminal_sn"] = $terminal_sn;
        $orderinfo["status"]      = 8;
        $param                    = base64_encode(json_encode($orderinfo));
        $sign                     = Library::encrypt(["orderid" => $orderid, "param" => $param], Config::get("common.uc_paykey"), $params);
        $url                      = "http://tob.vronline.com/addRefundOrder";
        $return                   = HttpRequest::post($url, $params);
        if (!$return) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "addRefundOrder failed", "return" => $return, "curl_info" => HttpRequest::getInfo()), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1);
        }
        $return = json_decode($return, true);
        if (!$return || !is_array($return) || !isset($return['code']) || $return['code'] != 0) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "addRefundOrder failed", "return" => $return), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1);
        }

        /**
         * 扣商家余额
         * 由于是退款，在扣余额失败时，上面两个订单可以重复添加，扣款不能重复扣
         */
        if ($refundFee != 0) {
            $toBCheckBillDBModel = new ToBCheckBillDBModel;
            $dec                 = $toBCheckBillDBModel->decNewBalance($merchantid, $refundFee);
            if (!$dec) {
                UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "decNewBalance failed", "dec" => $dec), __METHOD__ . "[" . __LINE__ . "]");
                return Library::output(1, null, "退款失败");
            }
        }

        /**
         * 调接口退款
         */
        $payModel     = new PayModel;
        $callback_url = $payModel->getRefundCallbackUrl();
        $payurl       = $payModel->getDatacenterPayUrl();
        $money        = 0;
        $sign         = md5($paycenter_orderid . $callback_url . $money . $orderid . '6804a867a9737a1d483150ee96ba085d');
        $refundUrl    = $payurl . "/index.php?action={$channel}&resource_id=1300160&order_id={$paycenter_orderid}&callback_url=" . rawurlencode($callback_url) . "&money={$money}&vr_order={$orderid}&sign={$sign}";
        $ret          = HttpRequest::get($refundUrl);
        $refundRet    = json_decode(trim($ret), true);
        if (!$ret || !$refundRet || !is_array($refundRet) || !isset($refundRet['status']) || $refundRet['status'] != "success") {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "call datacenter failed", "ret" => $ret, "action" => $channel, "paycenter_orderid" => $paycenter_orderid), __METHOD__ . "[" . __LINE__ . "]");
            $msg = isset($refundRet['msg']) && $refundRet['msg'] ? $refundRet['msg'] : "3";
            return Library::output(1, null, "退款失败({$msg})");
        }

        /**
         * 成功后调接口，修改原订单状态为已退款，下次不能再退
         * refund_pay_orderid 不为空，即为已退款
         * 修改新、老订单信息及状态
         */

        $ret = $toBOrderModel->updateOrder($oldorderid, ['refund_pay_orderid' => $orderid]);
        if (!$ret) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "updateOrder fail", "orderid" => $oldorderid, "info" => ['refund_pay_orderid' => $orderid]), __METHOD__ . "[" . __LINE__ . "]");
        }

        $info   = ['refund_pay_orderid' => $orderid];
        $param  = base64_encode(json_encode($info));
        $sign   = Library::encrypt(["orderid" => $oldorderid, "param" => $param], Config::get("common.uc_paykey"), $params);
        $url    = "http://tob.vronline.com/confirmRefundOrder";
        $return = HttpRequest::post($url, $params);
        $return = json_decode($return, true);
        if (!isset($return['code']) || $return['code'] != 0) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "false", "log" => "update old order refund_pay_orderid error", "orderid" => $oldorderid, "info" => $info), __METHOD__ . "[" . __LINE__ . "]");
        }

        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bRefundOrder", "result" => "success", "orderid" => $orderid, "sign" => $sign, "paycenter_orderid" => $paycenter_orderid), __METHOD__ . "[" . __LINE__ . "]");
        return Library::output(0);
    }

    /**
     * 回调退款
     * @param   array  request
     * @return  int state  注册状态码
     */
    public function callBackRefund(Request $request)
    {
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "log" => "callBackRefund:start"), __METHOD__ . "[" . __LINE__ . "]");
        $orderid       = trim($request->input('vr_order'));
        $paycenterid   = trim($request->input('order_id'));
        $channel_order = trim($request->input('refund_bill_no'));
        $money         = $request->input('refund_amt');
        $refund_status = trim($request->input('refund_status'));
        $sign          = trim($request->input('sign'));

        if (!$orderid || !$paycenterid || !$channel_order || $money <= 0 || !$sign) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "orderids or money is null", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"2","error_msg":"参数不全"}';
        }
        if (strtolower($refund_status) != "success") {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "refund_status error", "orderid" => $orderid, "channel_order" => $channel_order, "refund_status" => $refund_status), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"-6","error_msg":"状态错误"}';
        }

        $channel_order .= "#refund";
        $payModel = new PayModel;
        if (isset($_GET['//callBackRefund'])) {
            unset($_GET['//callBackRefund']);
        }
        $check = $payModel->createPaycenterSign($_GET);
        if ($check != $sign) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "sign check error", "sign" => $sign, "check" => $check), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"3","error_msg":"验证失败"}';
        }

        $toBOrderModel = new ToBOrderModel;
        $oInfo         = $toBOrderModel->getOrderById($orderid);
        if (!$oInfo || !is_array($oInfo)) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "getOrderById error", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"-6","error_msg":"订单查询错误"}';
        }
        if ($oInfo['paytype'] != 1) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "not refund order", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"-6","error_msg":"不是退款订单"}';
        }

        /**
         * 修改新、老订单信息及状态
         */
        $info                       = [];
        $info['paycenter_orderid']  = $paycenterid;
        $info['paychannel_orderid'] = $channel_order;
        $info['paytime']            = date("Y-m-d H:i:s");
        $info['status']             = 8;
        $toBOrderModel              = new ToBOrderModel;
        $ret                        = $toBOrderModel->updateOrder($orderid, $info);
        if (!$ret) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "updateOrder fail", "orderid" => $orderid, "info" => $info), __METHOD__ . "[" . __LINE__ . "]");
        }

        $param  = base64_encode(json_encode($info));
        $sign   = Library::encrypt(["orderid" => $orderid, "param" => $param], Config::get("common.uc_paykey"), $params);
        $url    = "http://tob.vronline.com/confirmRefundOrder";
        $return = HttpRequest::post($url, $params);
        if (!$return) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "call confirmRefundOrder fail", "orderid" => $orderid, "param" => $params, "return" => $return), __METHOD__ . "[" . __LINE__ . "]");
        }
        $return = json_decode($return, true);
        if (!$return || !is_array($return) || !isset($return['code']) || $return['code'] != 0) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackRefund", "result" => "false", "log" => "call confirmRefundOrder fail", "orderid" => $orderid, "param" => $params, "return" => $return), __METHOD__ . "[" . __LINE__ . "]");
            return '{"error_no":"-6","error_msg":"退款失败"}';
        }

        return '{"error_no":"1","error_msg":"退款成功"}';
    }

    /**
     * 充值结果页面
     */
    public function payresult2b($orderid)
    {
        Library::accessHeader();
        $orderid = trim($orderid);
        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/payres2b", array("result" => "false", "log" => "orderid is null", "from" => $from, "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }
        $toBOrderModel = new ToBOrderModel;
        $order         = $toBOrderModel->getOrderById($orderid);
        if (!$order || !is_array($order) || !isset($order['status'])) {
            UdpLog::save2("pay.vronline.com/payres2b", array("result" => "false", "log" => "order error", "orderid" => $orderid, "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1, "", "付款失败");
        }
        if ($order['status'] == 0) {
            return Library::output(2, "", "等待支付");
        }
        return Library::output(0, ['time' => strtotime($order['ltime']), 'merchantid' => $order['merchantid']]);
    }

    /**
     * 充值结果
     */
    public function payResultApi($orderid)
    {
        Library::accessHeader();
        $orderid = trim($orderid);
        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/payres", array("result" => "false", "log" => "orderid is null", "from" => $from, "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }
        $orderModel = new OrderModel;
        $order      = $orderModel->getOrderById($orderid);
        if (!$order || !is_array($order) || !isset($order['stat'])) {
            UdpLog::save2("pay.vronline.com/payres", array("result" => "false", "log" => "order error", "orderid" => $orderid, "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1, null, "付款失败1");
        }
        if ($order['stat'] == 0) {
            return Library::output(2, null, "等待支付");
        }
        if ($order['stat'] != 8) {
            return Library::output(1, null, "付款失败2");
        }
        if ($order['tradeid']) {
            $consumeModel = new ConsumeModel;
            $consume      = $consumeModel->getTradeById($order['tradeid']);
            if (!$consume || !is_array($consume) || !isset($consume['stat']) || $consume['stat'] != 8) {
                UdpLog::save2("pay.vronline.com/payres", array("result" => "false", "log" => "order error", "tradeid" => $order['tradeid']), __METHOD__ . "[" . __LINE__ . "]");
                return Library::output(1, null, "付款失败3");
            }
        }
        return Library::output(0);
    }

    /**
     * 提现前扣余额
     * @param   int     merchantid  商家ID
     * @param   string  token       登录token
     * @param   string  paypwd      提现密码
     * @param   float   amount      提现金额，单位 元
     */
    public function cashSubbalance(Request $request)
    {
        Library::accessHeader();
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashSubbalance", "log" => "cashSubbalance:start"), __METHOD__ . "[" . __LINE__ . "]");
        $merchantid = $request->input('merchantid');
        $token      = $request->input('token');
        $paypwd     = $request->input('paypwd');
        $amount     = $request->input('amount');
        if (!$merchantid || !$token || !$paypwd || $amount <= 0) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashSubbalance", "result" => "false", "log" => "merchantid or token or paypwd or amount is null", "merchantid" => $merchantid, "paypwd" => $paypwd, "token" => $token, "amount" => $amount), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2001);
        }

        $nowstamp = time();

        /**
         * 判断是否登录
         */
        $passport = new PassportModel;
        $login    = $passport->isLoginTurbo($merchantid, "login_token", $token);
        if (!$login) {
            return Library::output(1301);
        }

        /**
         * 加锁
         */
        $lockid = "cash_" . $merchantid;
        $ret    = Library::addLock($lockid);
        if (!$ret) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashSubbalance", "result" => "false", "log" => "add lock failed", "lockid" => $lockid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2024);
        }

        $toBCheckBillModel = new ToBCheckBillModel;
        $ret               = $toBCheckBillModel->cashStart($merchantid, $paypwd, $amount);

        Library::delLock($lockid);
        if (!$ret) {
            return Library::output(1);
        }
        if ($ret === true) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashSubbalance", "result" => "success", "merchantid" => $merchantid, "amount" => $amount), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(0);
        }

        switch ($ret) {
            case "less":
                return Library::output(2001, null, "取现金额不能少于1000元");
            case "pwd_error":
                return Library::output(1303);
            case "balance_not_enough":
                return Library::output(2311, null, "取现余额不足");
            default:return Library::output(1);
        }
    }

    /**
     * 提现创建订单
     * @param   int     adminid     登录用户id
     * @param   string  token
     * @return  array
     */
    public function create2bCashOrder(Request $request)
    {
        Library::accessHeader();
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bCashOrder", "log" => "create2bCashOrder:start"), __METHOD__ . "[" . __LINE__ . "]");

        $adminid = $request->input('adminid');
        $time    = $request->input('ts');
        $sign    = $request->input('sign');
        if (!$adminid || !$time) {
            return Library::output(2001);
        }
        if ($time + 30 < time() || $time - 30 > time()) {
            return Library::output(2001, null, "参数错误2");
        }

        if (isset($_GET['//create/create2bCashOrder'])) {
            unset($_GET['//create/create2bCashOrder']);
        }

        $check_sign = Library::encrypt($_GET, Config::get("common.uc_paykey"));
        if ($check_sign != $sign) {
            return Library::output(2002);
        }
        $payModel = new PayModel;

        $lockid = "cash_start";
        $lock   = Library::addLock($lockid);
        if (!$lock) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bCashOrder", "result" => "false", "log" => "add lock failed", "lockid" => $lockid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(2024);
        }

        $orderid = $payModel->get2bCash();
        Library::delLock($lockid);
        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bCashOrder", "result" => "false", "log" => "get2bCash failed", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return Library::output(1);
        }

        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "create2bCashOrder", "result" => "success", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
        return Library::output(0);
    }

    /**
     * 提现付款回调
     */
    public function callBackCash(Request $request)
    {
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "log" => "callBackCash:start", "get" => base64_encode($_GET), "post" => base64_encode($_POST)), __METHOD__ . "[" . __LINE__ . "]", false);
        $ret_code    = $request->input('ret_code');
        $ret_msg     = $request->input('ret_msg');
        $agent_id    = $request->input('agent_id');
        $hy_bill_no  = $request->input('hy_bill_no');
        $status      = $request->input('status');
        $batch_no    = $request->input('batch_no');
        $batch_amt   = $request->input('batch_amt');
        $batch_num   = $request->input('batch_num');
        $ext_param1  = $request->input('ext_param1');
        $detail_data = $request->input('detail_data');
        $sign        = $request->input('sign');

        $toBCheckBillModel = new ToBCheckBillModel;
        if (!$hy_bill_no || !$batch_no || $batch_amt <= 0 || $batch_num <= 0 || !$detail_data || !$sign) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "false", "log" => "miss one of params", "batch_no" => $batch_no, "hy_bill_no" => $hy_bill_no), __METHOD__ . "[" . __LINE__ . "]");
            return 'error';
        }

        if ($agent_id != $toBCheckBillModel->getAgentid() || $status != 1) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "false", "log" => "params error", "batch_no" => $batch_no, "hy_bill_no" => $hy_bill_no), __METHOD__ . "[" . __LINE__ . "]");
            return 'error';
        }

        $toBOrderModel = new ToBOrderModel;
        $oinfo         = $toBOrderModel->getOrderById($batch_no);
        if (!$oinfo || !is_array($oinfo)) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "false", "log" => "get order info failed", "orderid" => $batch_no), __METHOD__ . "[" . __LINE__ . "]");
            return 'error';
        }
        if ($oinfo['status'] == 8) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "success before", "orderid" => $batch_no), __METHOD__ . "[" . __LINE__ . "]");
            return 'ok';
        }
        if ($ret_code != "0000") {
            $errmsg = ['ret_code' => $ret_code, 'ret_msg' => $ret_msg];
            if ($oinfo['errmsg']) {
                $errmsg['old'] = $oinfo['errmsg'];
            }
            $uinfo = ['errmsg' => json_encode($errmsg)];
            $toBOrderModel->updateOrder($batch_no, $uinfo);
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "false", "log" => "get code error", "ret_code" => $ret_code, "ret_msg" => $ret_msg), __METHOD__ . "[" . __LINE__ . "]");
            return "ok";
        }

        $detail = explode("|", $detail_data);
        if (!$detail || !is_array($detail)) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "false", "log" => "detail_data error", "detail_data" => base64_encode($detail_data)), __METHOD__ . "[" . __LINE__ . "]");
            return "error";
        }

        $result              = true;
        $toBCheckBillDBModel = new ToBCheckBillDBModel;
        for ($i = 0; $i < count($detail); $i++) {
            $detail = iconv("GB2312", "UTF-8", $detail[$i]);
            $info   = explode("^", $detail);
            if (!$info || !is_array($info)) {
                continue;
            }
            if (!isset($info[0]) || !$info[0]) {
                continue;
            }
            $oid = $info[0];
            if (!isset($info[4]) || !$info[4]) {
                continue;
            }
            $sta = strtolower($info[4]);
            if ($sta === "s") {
                $uinfo = ['stat' => 8];
            } else {
                $uinfo = ['stat' => 7];
            }
            $uinfo['channelorderid'] = $hy_bill_no;
            $uinfo['msg']            = $detail;
            $ret                     = $toBCheckBillDBModel->updExtractOrderByIds([$oid], $uinfo);
            if (!$ret) {
                $result = false;
            }
        }
        if (!$result) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "false", "orderid" => $batch_no), __METHOD__ . "[" . __LINE__ . "]");
            return "error";
        }
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBackCash", "result" => "success", "orderid" => $batch_no), __METHOD__ . "[" . __LINE__ . "]");
        return "ok";
    }

}
