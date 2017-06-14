<?php

/*
充值
date:2016/9/1
 */

namespace App\Models;

use App\Helper\Vmemcached;
use App\Models\AppinfoModel;
use App\Models\DataCenterStatModel;
use App\Models\LogModel;
use App\Models\ToBCheckBillDBModel;
use App\Models\UserOrderModel;
use Config;
use Helper\HttpRequest;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;
use \App\Models\ToBCheckBillModel;
use \App\Models\ToBOrderModel;

class PayModel extends Model
{
    /**
     * 真实发货回调地址
     */
    private $real_payurl = "";

    /**
     * 回调发货错误信息
     */
    private $errmsg = "";

    /**
     * 回调发货执行时间
     */
    private $delivery_time = 0;

    /**
     * 支付token
     */
    private $payTokenPrefix = "pay_token_";

    /**
     * 生成数据中心的签名
     */
    public function getDatacenterPayUrl()
    {
        $app_env = getenv("LARAVEL_APP_ENV");
        if ($app_env && !in_array($app_env, array("preonline"))) {
            return "//dev3.pay.xy.com";
        } else {
            return "//pay3.xy.com";
        }
    }

    /**
     * 生成数据中心的签名
     */
    public function getRefundCallbackUrl()
    {
        $app_env = getenv("LARAVEL_APP_ENV");
        if ($app_env && !in_array($app_env, array("preonline"))) {
            return "//test3.xyzs.com/callBackRefund";
        } else {
            return "//callback.vronline.com/callBackRefund";
        }
    }

    /**
     * 生成数据中心的签名
     */
    public function createPaycenterSign($param)
    {
        $sign_array = $param;
        //过滤不参与签名字段
        $unset_ary = array('sign', 'key', 'paid', 'action', 'resource_id', 'extra_currency', 'cash_type', 'taocan_id', 'role_id', 'user_ip');

        foreach ($unset_ary as $li) {
            if (isset($sign_array[$li])) {
                unset($sign_array[$li]);
            }

        }
        $order = array();
        foreach ($sign_array as $key => $value) {
            $order[$key] = strval($value);
        }
        sort($order, SORT_STRING);
        $str = implode('', $order);
        error_log(date("Y-m-d H:i:s") . " " . $str . "\n", 3, "/tmp/sign.log");
        $order = null;

        $sign = md5('582df15de91b3f12d8e710073e43f4f8' . $str);
        error_log(date("Y-m-d H:i:s") . " " . $sign . "\n", 3, "/tmp/sign.log");
        return $sign;
    }

    /**
     * 生成数据中心发货回调的签名
     */
    public function createPaycenterCallBackSign($uid, $sid, $time, $order_id, $app_order_id, $coins, $money, $merchant_id)
    {
        $paykey = "4d2d351f711ea59375fb1ee4082c6978";
        $sign   = md5($uid . $sid . $time . $order_id . $app_order_id . $coins . $money . $merchant_id . '#' . $paykey);
        error_log(date("Y-m-d H:i:s") . " [callback] " . $sign . "\n", 3, "/tmp/sign.log");
        return $sign;
    }

    /**
     * 生成数据中心退款回调的签名
     */
    public function createPaycenterRefundCallBackSign($order_id, $app_order_id, $money)
    {
        $paykey = "6804a867a9737a1d483150ee96ba085d";
        $sign   = md5($order_id . $app_order_id . $money . $paykey);
        error_log(date("Y-m-d H:i:s") . " [callback] " . $sign . "\n", 3, "/tmp/sign.log");
        return $sign;
    }

    /**
     * 支付成功，调开发商的发货回调地址
     * 先读app信息，判断开发商是否是支持每个服一个发货地址
     * 如果支持，拿到开发商的域名，得到当前服的发货地址
     * 如果不支持，拿默认的发货地址
     * 然后发货
     * 超时2秒，失败记录错误日志
     */
    public function callDeveloper($appid, $consume)
    {

        UdpLog::save2("pay.vronline.com/pay", array("function" => "callDeveloper", "log" => "callDeveloper:start", "appid" => $appid, "order" => $consume), __METHOD__ . "[" . __LINE__ . "]");
        if (!$appid || !$consume || !is_array($consume) || !isset($consume['to_openid']) || !$consume['to_openid']) {

            UdpLog::save2("pay.vronline.com/pay", array("function" => "callDeveloper", "result" => "false", "log" => "params error", "appid" => $appid, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        $app_path   = app_path();
        $class_file = $app_path . "/Models/WebGame/Webgame{$appid}Class.php";
        if (file_exists($class_file)) {
            $appModel = new AppinfoModel;
            $appinfo  = $appModel->info($appid);
            if (!$appinfo || !is_array($appinfo)) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callDeveloper", "result" => "false", "log" => "appinfo get failed", "appid" => $appid, "appinfo" => $appinfo), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("tradeid" => $consume['tradeid'], "uid" => $consume['to_uid'], "openid" => $consume['to_openid'], "appid" => $appid, "action" => "callback", "errmsg" => "app信息读取错误 [appid:{$appid}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
            $paykey = $appinfo['paykey'];

            $urlinfo = $appModel->getPayUrlByServerid($appid, $consume['serverid']);
            if (!$urlinfo || !is_array($urlinfo)) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callDeveloper", "result" => "false", "log" => "urlinfo get failed", "appid" => $appid, "serverid" => $consume['serverid'], "urlinfo" => $urlinfo), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("tradeid" => $consume['tradeid'], "uid" => $consume['to_uid'], "openid" => $consume['to_openid'], "appid" => $appid, "action" => "callback", "errmsg" => "发货回调地址读取错误 [appid:{$appid}][serverid:{$consume['serverid']}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }

            $class  = "App\Models\WebGame\Webgame{$appid}Class";
            $result = $class::payCallBack($consume, $paykey, $appinfo, $urlinfo);

            $this->real_payurl   = isset($result['url']) ? $result['url'] : "";
            $this->delivery_time = isset($result['delivery_time']) ? $result['delivery_time'] : 0;

            if (!isset($result['httpinfo']) || !is_array($result['httpinfo'])) {
                $result['httpinfo'] = array();
            }
            if (!isset($result['errmsg'])) {
                $result['errmsg'] = "";
            }
            $result['httpinfo']['errmsg'] = isset($result['errmsg']) ? $result['errmsg'] : "";
            if (!$result['result']) {
                $this->errmsg = $result['httpinfo'];
            } else {
                $this->errmsg = $result['result'];
            }

            if ($result['result'] === "success") {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callDeveloper", "result" => "success", "http_return" => $result, "appid" => $appid, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]");
                return true;
            } else {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callDeveloper", "result" => "false", "log" => "callback delivery return error", "appid" => $appid, "http_return" => $result, "info" => $result['httpinfo'], "error" => $result['errmsg'], "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("tradeid" => $consume['tradeid'], "uid" => $consume['to_uid'], "openid" => $consume['to_openid'], "appid" => $appid, "action" => "callback", "errmsg" => "发货失败[result:" . json_encode($result) . "][info:" . json_encode($result['httpinfo']) . "][error:" . json_encode($result['errmsg']) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
        } else {
            return $this->callCommonDeveloper($appid, $consume);
        }
    }

    /**
     * 通用发货地址
     * 支付成功，调开发商的发货回调地址
     * 先读app信息，判断开发商是否是支持每个服一个发货地址
     * 如果支持，拿到开发商的域名，得到当前服的发货地址
     * 如果不支持，拿默认的发货地址
     * 然后发货
     * 超时2秒，失败记录错误日志
     */
    public function callCommonDeveloper($appid, $consume)
    {
        UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "log" => "callCommonDeveloper:start", "appid" => $appid, "order" => $consume), __METHOD__ . "[" . __LINE__ . "]");
        if (!$appid || !$consume || !is_array($consume) || !isset($consume['to_openid']) || !$consume['to_openid']) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "result" => "false", "log" => "params error", "appid" => $appid, "order" => $consume), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        $appModel = new AppinfoModel;
        $appinfo  = $appModel->info($appid);
        if (!$appinfo || !is_array($appinfo)) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "result" => "false", "log" => "appinfo get failed", "appid" => $appid, "appinfo" => $appinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("tradeid" => $consume['tradeid'], "uid" => $consume['to_uid'], "openid" => $consume['to_openid'], "appid" => $appid, "action" => "callback", "errmsg" => "app信息读取错误 [appid:{$appid}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }
        $paykey   = $appinfo['paykey'];
        $isdev    = $consume['isdev'];
        $serverid = $consume['serverid'];
		$use_default = intval($appinfo['use_default']);
		$offline_game = intval($appinfo['offline_game']);

		if($offline_game == 1) {
			$this->real_payurl = "offline_game";
			$this->delivery_time = 0;
			$this->errmsg = "offline_game";
			$result = "success";
			UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "result" => "true", "log" => "offline game", "appid" => $appid), __METHOD__ . "[" . __LINE__ . "]");
		}else {

			if (!$serverid || $use_default == 1) {
				if ($isdev) {
					$this->real_payurl = $appinfo['payurltest'];
					//$this->real_payurl = $appinfo['payurl'];
				} else {
					$this->real_payurl = $appinfo['payurl'];
				}
			} else {
				$urlinfo = $appModel->getPayUrlByServerid($appid, $serverid);
				if (!$urlinfo || !is_array($urlinfo)) {
					UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "result" => "false", "log" => "urlinfo get failed", "appid" => $appid, "serverid" => $serverid, "urlinfo" => $urlinfo), __METHOD__ . "[" . __LINE__ . "]");
					$errlog = array("tradeid" => $consume['tradeid'], "uid" => $consume['to_uid'], "openid" => $consume['to_openid'], "appid" => $appid, "action" => "callback", "errmsg" => "发货回调地址读取错误 [appid:{$appid}][serverid:{$serverid}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
					LogModel::addLog($errlog);
					return false;
				}
				if ($isdev) {
					$this->real_payurl = $urlinfo['payurltest'];
					//$this->real_payurl = $urlinfo['payurl'];
				} else {
					$this->real_payurl = $urlinfo['payurl'];
				}
			}

			$rate = Config::get("common.plantb_count_per_rmb");

			$params['tradeid']  = $consume['tradeid'];
			$params['amount']   = round($consume['amount'] / $rate, 2);
			$params['openid']   = $consume['to_openid'];
			$params['appid']    = $appid;
			$params['serverid'] = $consume['serverid'];
			$params['extra1']   = $consume['extra1'];
			$params['itemid']   = $consume['itemid'];
			$params["price"]    = $consume["price"];
			$params["num"]      = $consume["num"];
			$params['paytoken'] = $consume['paytoken'];
			$params['ts']       = time();

			$start_time = microtime(true);

			$http_params       = array();
			$ret               = Library::encrypt($params, $paykey, $http_params);
			$post_string       = http_build_query($http_params);
			$this->real_payurl = $this->real_payurl . "?" . $post_string;
			$result            = HttpRequest::get($this->real_payurl);
			$curlinfo          = HttpRequest::getInfo();
			$err               = HttpRequest::getError();

			$end_time            = microtime(true);
			$this->delivery_time = round($end_time - $start_time, 5);

			if (is_array($curlinfo)) {
				$curlinfo['errmsg'] = $err;
			}

			$this->errmsg = json_encode($curlinfo);
			if (!$result) {
				UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "result" => "false", "log" => "callback delivery return null", "appid" => $appid, "http_return" => $result, "info" => $curlinfo, "error" => $err, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]");
				$errlog = array("tradeid" => $consume['tradeid'], "uid" => $consume['to_uid'], "openid" => $consume['to_openid'], "appid" => $appid, "action" => "callback", "errmsg" => "发货失败[result:" . json_encode($result) . "][info:" . json_encode($curlinfo) . "][error:" . json_encode($err) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
				LogModel::addLog($errlog);
				return false;
			}
		}

        if (strtolower($result) === "success") {
            $this->errmsg = $result;
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "result" => "success", "http_return" => $result, "appid" => $appid, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]");
            return true;
        } else {
            $this->errmsg = $result;
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callCommonDeveloper", "result" => "false", "log" => "callback delivery error", "http_return" => $result, "appid" => $appid, "info" => $curlinfo, "error" => $err, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("tradeid" => $consume['tradeid'], "uid" => $consume['to_uid'], "openid" => $consume['to_openid'], "appid" => $appid, "action" => "callback", "errmsg" => "发货失败[result:" . json_encode($result) . "][info:" . json_encode($curlinfo) . "][error:" . json_encode($err) . "]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }

    }

    /**
     * 获取回调结果信息
     */
    public function getDeliverInfo()
    {
        return array("real_payurl" => $this->real_payurl, "errmsg" => $this->errmsg, "delivery_time" => $this->delivery_time);
    }

    /**
     * 充值回调
     * 第三方渠道扣费后，回调接口
     *
     * @param   string  orderid         订单号
     * @param   string  paycenterid     支付中心的订单号
     * @param   string  paychannelid    第三方支付渠道的订单号
     * @param   float   check_money     用来校验用的人民币，数据中心传过来的
     * @param   bool    addlock         是否有加锁，调用该方法前必须加锁
     * @param   bool    addb_result     加平台币是否成功，如果成功，就返回给支付渠道成功，否则是未支付，等待补单
     * @return  bool
     */
    public function callBack($orderid, $paycenterid, $channel_order, $check_money, $addlock, &$addb_result = false)
    {
        UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "log" => "callBack[model]:start", "orderid" => $orderid, "paycenterid" => $paycenterid, "channel_order" => $channel_order), __METHOD__ . "[" . __LINE__ . "]");
        $addb_result = false;

        if (!$orderid) {
            return false;
        }

        $nowstamp = time();

        if ($addlock !== TRUE) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "add lock failed", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "action" => "callback", "errmsg" => "加锁失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }

        /**
         * 先读订单信息
         * 如果有订单信息的，一定是要充平台币的，至于是否要消费，要看订单信息
         */
        $orderModel = new OrderModel;

        /**
         * 查订单信息
         */
        $orderinfo = $orderModel->getOrderById($orderid);
        if (!$orderinfo || !is_array($orderinfo)) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "getOrderById failed", "orderid" => $orderid, "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "action" => "callback", "errmsg" => "读订单信息错误 [{$orderid}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }
        $uid          = $orderinfo['uid'];
        $openid       = $orderinfo['openid'];
        $appid        = $orderinfo['appid'];
        $tradeid      = $orderinfo['tradeid'];
        $order_status = $orderinfo['stat'];
        $money        = $orderinfo['money'];
        if ($check_money != $money) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "money error", "orderid" => $orderid, "check_money" => $check_money, "money" => $money), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "数据中心传过来的金额错误", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }
        /**
         * 消费订单，判断是否有消费
         */
        $action = $orderinfo['action'];
        if ($action == "game" && !$tradeid) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "action is game,bug no tradeid", "orderid" => $orderid, "tradeid" => $tradeid), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "action is game,bug no tradeid [{$orderid}]", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }

        /**
         * 订单已经发货成功，直接返回成功
         */
        if ($order_status == Config::get("pay.pay_status.success")) {
            $addb_result = true;
            return true;
        } else if ($order_status == Config::get("pay.pay_status.wait_pay")) {
            // 等待付款，可以继续往下处理，剩下的状态都不处理
        } else {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "order status error", "orderid" => $orderid, "status" => $order_status), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "订单状态错误，为 " . $order_status, "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }

        $userModel = new UserModel;

        /**
         * 先处理充平台币的
         * 先修改订单状态，再加币，以免加币成功，改状态失败导致重复加币 ！！！！！！！！！！！！！！！！！！！！
         */
        $get_plantb   = $orderinfo['get_plantb'];
        $extra_plantb = $orderinfo['extra_plantb'];
        if ($get_plantb <= 0) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "get_plantb is null", "orderid" => $orderid, "get_plantb" => $get_plantb), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "get_plantb is " . json_encode($get_plantb), "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }
        $updinfo = array(
            "paycenterid"   => $paycenterid,
            "channel_order" => $channel_order,
            "ftime"         => $nowstamp,
            "stat"          => Config::get("pay.pay_status.success"),
        );
        $upd = $orderModel->updateOrder($orderid, $updinfo);
        if (!$upd) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "updateOrder failed", "orderid" => $orderid, "updinfo" => $updinfo), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "updateOrder 执行失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            return false;
        }

        $paynum = $get_plantb - $extra_plantb;
        $addB   = $userModel->addPlatb($uid, $get_plantb, $paynum);

        UdpLog::save2("pay.vronline.com/trade", array("function" => "callBack[model]", "log" => "[add]", "result" => $addB, "orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "addnum" => $get_plantb, "appid" => $appid, "serverid" => $orderinfo['serverid'], "pay_uid" => $orderinfo['pay_uid'], "pay_openid" => $orderinfo['pay_openid'], "openid" => $orderinfo['openid'], "paynum" => $paynum), __METHOD__ . "[" . __LINE__ . "]");

        if (!$addB) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "addPlatb failed", "orderid" => $orderid, "uid" => $uid, "get_plantb" => $get_plantb, "paynum" => $paynum), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "充值加平台币失败，平台币:{$get_plantb}", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
            $upd = $orderModel->updateOrder($orderid, array("stat" => Config::get("pay.pay_status.add_plantb_failed")));
            if (!$upd) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "updateOrder roll back failed", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "加平台币失败，修改订单状态为充值失败，但订单状态也修改失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
            }
            return false;
        }

        /**
         * 加币成功，就返回成功
         * 消费失败，由平台自己补单
         */
        $addb_result = true;

        $userOrderModel = new UserOrderModel;
        /**
         * 添加用户的充值记录
         * 给用户看的
         */
        $userOrder = array(
            "orderid"       => $orderid,
            "tradeid"       => $tradeid,
            "paycenterid"   => $paycenterid,
            "channel_order" => $channel_order,
            "paychannel"    => isset($orderinfo['paychannel']) ? $orderinfo['paychannel'] : "",
            "uid"           => $uid,
            "openid"        => isset($orderinfo['openid']) ? $orderinfo['openid'] : "",
            "appid"         => $appid,
            "other_uid"     => isset($orderinfo['pay_uid']) ? $orderinfo['pay_uid'] : 0,
            "other_openid"  => isset($orderinfo['pay_openid']) ? $orderinfo['pay_openid'] : "",
            "serverid"      => isset($orderinfo['serverid']) ? $orderinfo['serverid'] : 0,
            "plantb"        => isset($orderinfo['get_plantb']) ? $orderinfo['get_plantb'] : 0,
            "rmb"           => isset($orderinfo['money']) ? $orderinfo['money'] : 0,
            "balance"       => isset($orderinfo['balance']) ? $orderinfo['balance'] : 0,
            "stat"          => 8,
            "action"        => $action,
            "type"          => 0,
        );
        $ret2 = $userOrderModel->newOrder($orderid, $tradeid, $userOrder);
        if (!$ret2) {
            UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "create userorder failed", "orderid" => $orderid, "tradeid" => $tradeid, "userOrder" => $userOrder), __METHOD__ . "[" . __LINE__ . "]");
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "用户充值记录添加错误", "tbl" => "userorder", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
        }

        /**
         * 发送统计
         */
        $properties = [
            "orderid"   => $orderid,
            "tradeid"   => $tradeid,
            "payunit"   => "rmb",
            "_gameid"   => $appid,
            "serverid"  => $orderinfo['serverid'],
            "openid"    => $openid,
            "payamount" => $money,
            "paycoin"   => $get_plantb,
            //"from"      => $from,
            "source"    => $orderinfo['paychannel'],
            "paytype"   => 2,
            "isall"     => 1, // 表示日志数据是全的，不需要再从数据库补数据
        ];
        DataCenterStatModel::stat("vrplat", "recharge", $uid, $properties);

        /**
         * 再判断是否要充游戏，再扣币充游戏
         */
        if ($action == "game") {
            $consumeModel = new ConsumeModel;
            $consumeinfo  = $consumeModel->getTradeById($tradeid);
            if (!$consumeinfo || !is_array($consumeinfo) || $orderid != $consumeinfo['orderid']) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "get consume failed", "orderid" => $orderid, "tradeid" => $tradeid, "consumeinfo" => $consumeinfo), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "消费订单查询错误 " . json_encode($consumeinfo), "tbl" => "consume", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
            $serverid = $consumeinfo['serverid'];
            $amount   = $consumeinfo['amount'];
            $uid      = $consumeinfo['uid'];

            /**
             * 消费，扣平台币
             */
            $subB = $userModel->subPlatb($uid, $amount);
            UdpLog::save2("pay.vronline.com/trade", array("function" => "callBack[model]", "log" => "[sub]", "result" => $subB, "orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "subnum" => $amount, "appid" => $appid, "serverid" => $serverid, "to_uid" => $consumeinfo['to_uid'], "to_openid" => $consumeinfo['to_openid'], "openid" => $consumeinfo['openid'], "consume" => $consumeinfo), __METHOD__ . "[" . __LINE__ . "]");

            if (!$subB) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "subB failed", "tradeid" => $tradeid, "uid" => $uid, "subnum" => $amount), __METHOD__ . "[" . __LINE__ . "]");
                $upd    = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => Config::get("pay.pay_status.pay_failed")));
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "扣平台币失败", "tbl" => "user.ext", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }

            /**
             * 判断扣掉平台币后，剩余的平台币是否是负，如果是，说明之前的平台币数量不对，接下来回滚平台币
             */
            $extinfo = $userModel->extInfo($uid);
            if (!isset($extinfo['f_money']) || $extinfo['f_money'] < 0) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "money is less than 0", "tradeid" => $tradeid, "uid" => $uid, "extinfo" => $extinfo), __METHOD__ . "[" . __LINE__ . "]");
                $upd = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => Config::get("pay.pay_status.plantb_not_enough")));
                if (!$upd) {
                    UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "updateTrade status failed", "tradeid" => $tradeid, "stat" => Config::get("pay.pay_status.plantb_not_enough")), __METHOD__ . "[" . __LINE__ . "]");
                }
                $addB = $userModel->addPlatb($uid, $amount, 0);
                UdpLog::save2("pay.vronline.com/trade", array("function" => "callBack[model]", "log" => "[add][rollsub]", "result" => $addB, "orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "addnum" => $amount, "appid" => $appid, "serverid" => $consumeinfo['serverid'], "to_uid" => $consumeinfo['to_uid'], "to_openid" => $consumeinfo['to_openid'], "openid" => $consumeinfo['openid'], "paynum" => 0, "consume" => $consumeinfo), __METHOD__ . "[" . __LINE__ . "]");

                /**
                 * 回滚加币失败，重试
                 */
                if (!$addB) {
                    $addB = $userModel->addPlatb($uid, $amount, 0);
                    UdpLog::save2("pay.vronline.com/trade", array("function" => "callBack[model]", "log" => "[add][rollsub][again]", "result" => $addB, "orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "addnum" => $amount, "appid" => $appid, "serverid" => $orderinfo['serverid'], "pay_uid" => $orderinfo['pay_uid'], "pay_openid" => $orderinfo['pay_openid'], "openid" => $orderinfo['openid'], "paynum" => 0, "consume" => $consumeinfo), __METHOD__ . "[" . __LINE__ . "]");

                    if (!$addB) {
                        UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "addb roll back again failed", "tradeid" => $tradeid, "uid" => $uid, "addnum" => $amount), __METHOD__ . "[" . __LINE__ . "]");
                        $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "扣平台币后，平台币余额为负，回滚平台币失败", "tbl" => "user.ext", "position" => __METHOD__ . "[" . __LINE__ . "]");
                        LogModel::addLog($errlog);
                    }
                }
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "扣平台币后，平台币余额为负", "tbl" => "user.ext", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
            $balance = $extinfo['f_money'];

            /**
             * 给开发商发货
             */
            $deliver  = $this->callDeveloper($appid, $consumeinfo);
            $callinfo = $this->getDeliverInfo();
            //UdpLog::save2("pay.vronline.com/trade", array("function"=>"callBack[model]", "log" => "delivery", "result" => $deliver, "tradeid" => $tradeid, "orderid" => $orderid, "appid" => $appid, "serverid" => $serverid, "uid" => $uid, "openid" => $consumeinfo['openid'], "to_uid" => $consumeinfo['to_uid'], "to_openid" => $consumeinfo['to_openid'], "amount" => $consumeinfo['amount'], "callinfo" => $callinfo, "consumeinfo" => $consumeinfo), __METHOD__."[".__LINE__."]");

            /**
             * 发货失败，修改状态，结束
             */
            if (!$deliver) {
                $upd = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => Config::get("pay.pay_status.delivery_failed"), "errmsg" => $this->errmsg, "payurl" => $this->real_payurl, "delivery_time" => $this->delivery_time));
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "delivery failed", "tradeid" => $tradeid, "appid" => $appid, "consumeinfo" => $consumeinfo), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "发货失败", "tbl" => "http", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                if (!$upd) {
                    UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "updateTrade status failed", "tradeid" => $tradeid, "stat" => Config::get("pay.pay_status.delivery_failed")), __METHOD__ . "[" . __LINE__ . "]");
                    $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "发货失败、修改订单状态失败", "tbl" => "consume", "position" => __METHOD__ . "[" . __LINE__ . "]");
                    LogModel::addLog($errlog);
                }
                return false;
            }

            /**
             * 发货成功，修改状态
             * 如果状态修改失败，记日志，继续往下执行
             */
            if ($deliver) {
                $upd = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => Config::get("pay.pay_status.success"), "payurl" => $this->real_payurl, "delivery_time" => $this->delivery_time));
                if (!$upd) {
                    UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "delivery success but update trade failed", "tradeid" => $tradeid, "stat" => Config::get("pay.pay_status.success")), __METHOD__ . "[" . __LINE__ . "]");
                    $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "修改消费订单状态失败", "tbl" => "consume", "position" => __METHOD__ . "[" . __LINE__ . "]");
                    LogModel::addLog($errlog);
                }
            }

            /**
             * 添加用户的消费记录
             * 给用户看的
             */
            $userOrder = array(
                "orderid"       => $orderid,
                "tradeid"       => $tradeid,
                "paycenterid"   => $paycenterid,
                "channel_order" => $channel_order,
                "paychannel"    => isset($consumeinfo['paychannel']) ? $consumeinfo['paychannel'] : "",
                "uid"           => $uid,
                "openid"        => isset($consumeinfo['openid']) ? $consumeinfo['openid'] : "",
                "appid"         => $appid,
                "other_uid"     => isset($consumeinfo['to_uid']) ? $consumeinfo['to_uid'] : 0,
                "other_openid"  => isset($consumeinfo['to_openid']) ? $consumeinfo['to_openid'] : "",
                "serverid"      => isset($consumeinfo['serverid']) ? $consumeinfo['serverid'] : 0,
                "plantb"        => isset($consumeinfo['amount']) ? $consumeinfo['amount'] : 0,
                "price"         => isset($consumeinfo['price']) ? $consumeinfo['price'] : 0,
                "num"           => isset($consumeinfo['num']) ? $consumeinfo['num'] : 0,
                "item"          => isset($consumeinfo['item']) ? $consumeinfo['item'] : "",
                "itemid"        => isset($consumeinfo['itemid']) ? $consumeinfo['itemid'] : "",
                //"rmb"       => isset($consumeinfo['pay']) ? $consumeinfo['pay'] : 0,
                "rmb"           => 0,
                "balance"       => $balance,
                "stat"          => 8,
                "action"        => $action,
                "type"          => 9,
            );
            $ret2 = $userOrderModel->newOrder($orderid, $tradeid, $userOrder);
            if (!$ret2) {
                UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "false", "log" => "newUserOrder failed", "orderid" => $orderid, "tradeid" => $tradeid, "userOrder" => $userOrder), __METHOD__ . "[" . __LINE__ . "]");
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "callback", "errmsg" => "用户充值记录添加错误[" . json_encode($orderid) . "]", "tbl" => "userorder", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
            }

            /**
             * 发送统计
             * uid 小于1000的是测试账号，不发统计
             */
            if ($uid > 1000) {
                $rate       = Config::get("common.plantb_count_per_rmb");
                $properties = [
                    "tradeid"   => $tradeid,
                    "orderid"   => $orderid,
                    "payunit"   => "vrb",
                    "_gameid"   => $appid,
                    "serverid"  => $serverid,
                    "openid"    => $openid,
                    "payamount" => round($consumeinfo['amount'] / $rate, 2),
                    "itemid"    => $appid,
                    "item"      => $consumeinfo['item'],
                    "itemprice" => $consumeinfo['price'],
                    "itemnum"   => $consumeinfo['num'],
                    "itemtype"  => "game",
                    "isall"     => 1, // 表示日志数据是全的，不需要再从数据库补数据
                ];
                DataCenterStatModel::stat("vrplat", "buyitem", $uid, $properties);
            }

        }

        UdpLog::save2("pay.vronline.com/pay", array("function" => "callBack[model]", "result" => "success", "log" => "ok", "orderid" => $orderid, "tradeid" => $tradeid, "stat" => Config::get("pay.pay_status.delivery_failed")), __METHOD__ . "[" . __LINE__ . "]");
        return true;
    }

    /**
     * 线下体验店发货
     *
     * @param   string  orderid         订单号
     * @param   string  paycenterid     支付中心的订单号
     * @param   string  paychannelid    第三方支付渠道的订单号
     * @param   float   check_money     用来校验用的人民币，数据中心传过来的
     * @param   bool    addb_result     加平台币是否成功，如果成功，就返回给支付渠道成功，否则是未支付，等待补单
     * @param   string  action          pay:付款;payback:退款;repeat:补发通知
     * @param   bool    islocked        是否已加锁
     * @param   array   orderinfo       订单信息
     * @return  bool
     */
    public function callBack2b($orderid, $paycenterid, $channel_order, $check_money, $action, $islocked, $orderinfo = null)
    {
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "log" => "callBack2b[model]:start", "orderid" => $orderid, "paycenterid" => $paycenterid, "channel_order" => $channel_order), __METHOD__ . "[" . __LINE__ . "]");
        $addb_result = false;

        if (!$orderid || !$islocked) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "result" => "false", "log" => "orderid is null", "orderid" => $orderid, "islocked" => $islocked), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        $nowstamp = time();

        /**
         * 先读订单信息
         */
        $toBOrderModel = new ToBOrderModel;

        /**
         * 查订单信息
         */
        if (!isset($orderinfo) || !$orderinfo) {
            $orderinfo = $toBOrderModel->getOrderById($orderid);
        }
        if (!$orderinfo || !is_array($orderinfo)) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "result" => "false", "log" => "getOrderById failed", "orderid" => $orderid, "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        $merchantid   = $orderinfo['merchantid'];
        $terminal_sn  = $orderinfo['terminal_sn'];
        $appid        = $orderinfo['appid'];
        $total_rmb    = $orderinfo['total_rmb'];
        $pay_rmb      = $orderinfo['pay_rmb'];
        $merchant_fee = $orderinfo['merchant_fee'];
        $paytype      = $orderinfo['paytype'];
        $order_status = $orderinfo['status'];
        if ($check_money != $pay_rmb) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "result" => "false", "log" => "money error", "orderid" => $orderid, "check_money" => $check_money, "pay_rmb" => $pay_rmb, "total_rmb" => $total_rmb), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        if ($paytype == 0 && $action == "pay") {
            /**
             * 正常付款，或渠道补单，如果状态不是待付款，都不处理
             * 如果余额没加上，可以由第二天的自动对账修正
             * 如果终端没通知到，可以由用户或后台补发通知
             */
            if ($order_status != 0) {
                UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "result" => "true", "log" => "pay and status!=0", "orderid" => $orderid, "status" => $order_status), __METHOD__ . "[" . __LINE__ . "]");
                return true;
            }

        } else if ($paytype == 1 && $action == "payback") {
            /**
             * 退款，要判断是否有使用优惠券
             */
        } else if ($paytype == 0 && $action == "repeat") {
            /**
             * 补发通知
             * 如果订单未付款，不补发
             */
            if ($order_status == 0) {
                UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "result" => "false", "log" => "repeat but status=0", "orderid" => $orderid, "status" => $order_status), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }

        } else {
            return false;
        }

        /**
         * 收到回调，先修改订单状态
         * 如果后面发货失败了，可以由后台补发通知
         * 避免重复发货
         * 如果订单状态修改失败了，由渠道补发
         * 补发的请求不修改订单状态
         */
        if ($action != "repeat") {
            $updinfo = array(
                "paycenter_orderid"  => $paycenterid,
                "paychannel_orderid" => $channel_order,
                "paytime"            => date("Y-m-d H:i:s"),
                "status"             => 8,
                "errmsg"             => "",
            );
            $orderinfo['paycenter_orderid']  = $paycenterid;
            $orderinfo['paychannel_orderid'] = $channel_order;
            if ($action == "pay") {
                $updinfo['start']   = $nowstamp;
                $orderinfo['start'] = $nowstamp;
            }
            $upd = $toBOrderModel->updateOrder($orderid, $updinfo);
            if (!$upd) {
                UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "result" => "false", "log" => "update order status failed", "orderid" => $orderid, "stat" => $updinfo['status']), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
        }

        /**
         * 统计收入余额
         * 可以不处理异常情况，因为在每日凌晨会对账并修正余额
         */
        if ($action != "repeat") {
            $toBCheckBillDBModel = new ToBCheckBillDBModel;
            $balance             = $toBCheckBillDBModel->getBalance($merchantid);
            if ($balance === null) {
                $toBCheckBillDBModel->newBalanceInfo($merchantid, ["total_income" => $merchant_fee, "new_net_income" => $merchant_fee]);
            } else {
                if ($action == "pay") {
                    $toBCheckBillDBModel->incNewBalance($merchantid, $merchant_fee);
                } else if ($action == "payback") {
                    $dec_fee = abs($merchant_fee);
                    $toBCheckBillDBModel->decNewBalance($merchantid, $dec_fee);
                    return true;
                }
            }
        }

        /**
         * 发货
         */
        $deliver = $this->call2bDelivery($orderinfo, $action);

        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "callBack2b[model]", "result" => "success", "log" => "ok", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
        return true;
    }

    /**
     * 2b版本发货
     */
    public function call2bDelivery($order, $action)
    {
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "call2bDelivery", "log" => "call2bDelivery:start", "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
        if (!$order || !is_array($order) || !isset($order['merchantid']) || !$order['terminal_sn']) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "call2bDelivery", "result" => "false", "log" => "params error", "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        $url = "http://tob.vronline.com/payfor2bterminal";

        $params['orderid']            = $order['orderid'];
        $params['merchantid']         = $order['merchantid'];
        $params['terminal_sn']        = $order['terminal_sn'];
        $params['total_rmb']          = $order['total_rmb'];
        $params['pay_rmb']            = $order['pay_rmb'];
        $params['cp_fee']             = $order['cp_fee'];
        $params['plat_fee']           = $order['plat_fee'];
        $params['merchant_fee']       = $order['merchant_fee'];
        $params['coupon_code']        = $order['coupon_code'];
        $params["coupon_amount"]      = $order["coupon_amount"];
        $params["pay_channel"]        = $order["pay_channel"];
        $params['paycenter_orderid']  = $order['paycenter_orderid'];
        $params['paychannel_orderid'] = $order['paychannel_orderid'];
        $params['type']               = $order['type'];
        $params['start']              = $order['start'];
        $params['paytype']            = $order['paytype'];
        $params['refund_pay_orderid'] = $order['refund_pay_orderid'];
        $params['sellid']             = $order['sellid'];
        $params['appid']              = $order['appid'];
        $params['appname']            = $order['appname'];
        $params['action']             = $action;

        $start_time        = microtime(true);
        $http_params       = array();
        $ret               = Library::encrypt($params, Config::get("common.uc_paykey"), $http_params);
        $this->real_payurl = $url;
        $result            = HttpRequest::get($this->real_payurl, $http_params);
        $curlinfo          = HttpRequest::getInfo();
        $err               = HttpRequest::getError();

        $end_time            = microtime(true);
        $this->delivery_time = round($end_time - $start_time, 5);
        if (is_array($curlinfo)) {
            $curlinfo['errmsg'] = $err;
        }
        $this->errmsg = $curlinfo;
        if (!$result) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "call2bDelivery", "result" => "false", "log" => "callback delivery return null", "http_return" => $result, "info" => $curlinfo, "error" => $err, "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if (strtolower($result) === "success") {
            $this->errmsg = $result;
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "call2bDelivery", "result" => "success", "http_return" => $result, "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
            return true;
        } else {
            $this->errmsg = $result;
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "call2bDelivery", "result" => "false", "log" => "callback delivery error", "http_return" => $result, "orderid" => $order['orderid'], "info" => $curlinfo, "error" => $err, "order" => $order), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

    }

    /**
     * 2b版本提现
     * 先读审核通过的提现订单，并修改订单状态为正在提现
     * 计算总额，然后创建一笔支付中心订单
     * 发起提现
     */
    public function get2bCash()
    {
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "get2bCash", "log" => "get2bCash:start"), __METHOD__ . "[" . __LINE__ . "]");

        $maxCash = 49999;
        $minCash = 1000;
//        $minCash = 0;
        /**
         * 提取提现订单
         */
        $toBCheckBillDBModel = new ToBCheckBillDBModel;
        $cashOrder           = $toBCheckBillDBModel->getExtractOrderByStatus(5);
        if (!is_array($cashOrder)) {
            return false;
        }
        if (!$cashOrder) {
            return [];
        }
        $amount = 0;
        $ids    = [];
        for ($i = 0; $i < count($cashOrder); $i++) {
            if ($cashOrder[$i]['cash'] < $minCash || $cashOrder[$i]['cash'] > $maxCash) {
                continue;
            }
            $amount = bcadd($amount, $cashOrder[$i]['cash'], 2);
            $ids[]  = $cashOrder[$i]['orderid'];
        }

        /**
         * 用户中心创建一笔订单
         */
        $toBOrderModel = new ToBOrderModel;

        $orderinfo = array(
            "total_rmb" => $amount,
            "pay_rmb"   => $amount,
            "cp_fee"    => $amount,
            "paytype"   => 2,
        );

        $orderid = $toBOrderModel->newOrder("cash", "1", $orderinfo);
        if (!$orderid) {
            $orderid = $toBOrderModel->newOrder("cash", "1", $orderinfo);
        }
        if (!$orderid) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "get2bCash", "result" => "false", "log" => "create order failed", "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        /**
         * 状态修改为正在提现
         */

        $ret = $toBCheckBillDBModel->updExtractOrderByIds($ids, ['ucorderid' => $orderid, 'stat' => 6]);
        if (!$ret) {
            return false;
        }
        $toBCheckBillModel = new ToBCheckBillModel;
        $res               = $toBCheckBillModel->payTransfer($orderid, $amount, $cashOrder);
        if (!$res) {
            UdpLog::save2("pay.vronline.com/pay2b", array("function" => "get2bCash", "result" => "false", "res" => $res), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        UdpLog::save2("pay.vronline.com/pay2b", array("function" => "get2bCash", "result" => "success", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
        return $orderid;
    }

    /**
     * 获取支付的token
     * @param   int     uid
     * @return  string  token   token
     */
    public function getPayToken($uid)
    {
        if (!$uid) {
            return false;
        }
        try {
            $token = Vmemcached::get("pay_token", $uid);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        return $token;
    }

    /**
     * 获取平台币的兑换比例
     * 如果是测试账号，并且充值金额是1分钱，按照1:100的比例
     * @param   int     uid
     * @return  int     rate
     */
    public function getVbRate($uid, $rmb)
    {
        if (!$uid) {
            return false;
        }
        $rate = Config::get("common.plantb_count_per_rmb");
        if ($rmb > 0.01) {
            return $rate;
        }
        $testaccount = $this->isTestAccount($uid);
        if ($testaccount && $rmb == 0.01) {
            return 100;
        }
        return $rate;
    }

    /**
     * 删除支付的token
     * @param   int     uid
     */
    public function delPayToken($uid)
    {
        if (!$uid) {
            return false;
        }
        try {
            $ret = Vmemcached::delete("pay_token", $uid);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        return true;
    }

    /**
     * 生成token，缓存5分钟
     * @param   int     uid
     * @return  string  token   token
     */
    public function genPayToken($uid, $appid)
    {
        if (!$uid || !$appid) {
            return false;
        }

        $token = Library::genKey(32);
        try {
            $ret = Vmemcached::set("pay_token", $uid, $token);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        return $token;
    }

    /**
     * 是否是测试账号
     */
    public function isTestAccount($uid)
    {
        if (!$uid) {
            return false;
        }
        if ($uid >= 100 && $uid <= 200) {
            return true;
        }
        return false;
    }

    /**
     * 用户充值记录
	 * @param	int		uid
	 * @param	string	token	登录token
	 * @param	int		page	页数
	 * @param	int		len		每页数量
     */
    public function getUserOrder($uid, $type, $page, $len)
    {
        $userOrderModel = new UserOrderModel;

		$total = $userOrderModel->getOrderCountByUidType($uid, $type);
		if($total === false) {
			return false;
		}
        $orders = $userOrderModel->getOrderByUidType($uid, $type, $page, $len);
        if (!$orders && !is_array($orders)) {
            return false;
        }
		$fields = ['id', 'orderid', 'tradeid', 'uid', 'appid', 'serverid', 'rmb', 'item', 'plantb', 'paychannel', 'ctime', 'stat', 'type'];
		for($i = 0; $i < count($orders); $i++) {
			foreach($orders[$i] as $key => $arr_detail) {
				if(!in_array($key, $fields)) {
					unset($orders[$i][$key]);
				}
			}
		}
        return ['total' => $total, 'orders' => $orders];
    }

    /**
     * 补单充值订单，每分钟跑一次，同时只运行一个该脚本
     * 先读订单信息
     * 如果有订单信息的，一定是要充平台币的，至于是否要消费，要看订单信息
     * 补今天和昨天的订单，再早的不自动补单
     */
    public function retryOrder($timestamp)
    {
        $nowstamp = time();
        $status   = array(Config::get("pay.pay_status.plantb_not_enough"), Config::get("pay.pay_status.pay_failed"), Config::get("pay.pay_status.add_plantb_failed"));
        /**
         * 先读订单信息
         * 如果有订单信息的，一定是要充平台币的，至于是否要消费，要看订单信息
         */
        $orderModel = new OrderModel;

        $orders = array();

        $retry_interval = Config::get("pay.retry_interval");
        $retrytotal     = count($retry_interval) < 10 ? 10 : count($retry_interval);
        /**
         * 先处理昨天的订单
         */
        for ($i = 0; $i < count($status); $i++) {
            $stat = $status[$i];
            $rows = $orderModel->getOrderByStat($timestamp, $stat, $retrytotal);
            if (!$rows) {
                continue;
            }
            for ($j = 0; $j < count($rows); $j++) {
                $retrytime = $rows[$j]['retrytime'];
                $retrynum  = $rows[$j]['retrynum'];
                if ($retrynum > $retrytotal) {
                    continue;
                }
                $interval = $retry_interval[$retrynum];
                if (!$interval) {
                    $interval = 10 * 60;
                }

                /**
                 * 上次补单的时间距离当前时间不够该次补单的间隔，不补单
                 */
                if ($nowstamp - $retrytime < $interval) {
                    continue;
                }
                $orders[] = $rows[$j];
            }
        }

        /**
         * 开始补单
         */
        for ($i = 0; $i < count($orders); $i++) {
            $row = $orders[$i];
            $this->startRetryOrder($row);
        }

    }

    /**
     * 开始补单
     * 判断订单状态，不同的状态，补单逻辑不同
     * 如果是只充值平台币，不会有消费订单
     * 订单表只管加币失败的，只要加币成功，订单状态就是成功的
     * 扣币是由消费表决定的
     */
    public function startRetryOrder($order)
    {
        $orderid = $order['orderid'];
        UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "log" => "startRetryOrder:start", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]", false);

        $nowstamp = time();

        /**
         * 订单状态
         */
        $status = $order['stat'];

        /**
         * 获得的平台币数量
         */
        $get_plantb = $order['get_plantb'];

        /**
         * 活动赠送的平台币
         * 用户总的充值金额，要去掉活动总送的
         */
        $extra_plantb = $order['extra_plantb'];

        /**
         * 重试次数
         */
        $retrynum = $order['retrynum'];

        /**
         * 这次重试跑完后，新的重试次数
         */
        $newretrynum = $retrynum + 1;

        $tradeid = $order['tradeid'];
        $uid     = $order['uid'];
        $appid   = $order['appid'];
        $openid  = $order['openid'];

        $userModel      = new UserModel;
        $orderModel     = new OrderModel;
        $userOrderModel = new UserOrderModel;

        if ($status == Config::get("pay.pay_status.add_plantb_failed")) {
            /**
             * 加平台币失败
             * 补单，重新加币
             */
            if (!$get_plantb || $get_plantb <= 0) {
                UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "result" => "false", "log" => "get_plantb error", "get_plantb" => $get_plantb), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }

            /**
             * 先修改订单状态
             * 然后再加币，避免加币成功，订单状态修改失败，导致重复加币
             */
            $updinfo = array("ftime" => $nowstamp, "stat" => Config::get("pay.pay_status.success"), "retrynum" => $newretrynum, "retrytime" => $nowstamp);
            $upd     = $orderModel->updateOrder($orderid, $updinfo);
            if (!$upd) {
                UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "result" => "false", "log" => "updateOrder failed", "orderid" => $orderid, "updinfo" => $updinfo), __METHOD__ . "[" . __LINE__ . "]", false);
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "updateOrder 执行失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }

            /**
             * 加币
             */
            $paynum = $get_plantb - $extra_plantb;
            $addB   = $userModel->addPlatb($uid, $get_plantb, $paynum);
            UdpLog::save2("pay.vronline.com/trade", array("function" => "startRetryOrder", "log" => "[retry][add]", "result" => $addB, "orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "addnum" => $get_plantb, "appid" => $appid, "serverid" => $order['serverid'], "pay_uid" => $order['pay_uid'], "pay_openid" => $order['pay_openid'], "openid" => $order['openid'], "paynum" => $paynum), __METHOD__ . "[" . __LINE__ . "]", false);

            /**
             * 加币失败，修改订单状态
             */
            if (!$addB) {
                UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "result" => "false", "log" => "addPlatb failed", "orderid" => $orderid, "uid" => $uid, "get_plantb" => $get_plantb, "paynum" => $paynum), __METHOD__ . "[" . __LINE__ . "]", false);
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "补单加平台币失败，平台币:{$get_plantb}", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                $upd = $orderModel->updateOrder($orderid, array("stat" => Config::get("pay.pay_status.add_plantb_failed")));
                if (!$upd) {
                    UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "result" => "false", "log" => "updateOrder roll back failed", "orderid" => $orderid, "stat" => Config::get("pay.pay_status.add_plantb_failed")), __METHOD__ . "[" . __LINE__ . "]", false);
                    $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "加平台币失败，修改订单状态为充值失败，但订单状态也修改失败", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                    LogModel::addLog($errlog);
                }
                return false;
            }

            /**
             * 充值订单补单成功后，将消费订单的重试次数置0，开始补消费订单
             */
            $consumeModel = new ConsumeModel;
            $upd          = $consumeModel->updateTrade($tradeid, array("retrytime" => 0, "retrynum" => 0));
            /**
             * 加币成功后，添加用户订单记录
             */
            $userOrder = array(
                "orderid"       => $orderid,
                "tradeid"       => $tradeid,
                "paycenterid"   => $order['paycenterid'],
                "channel_order" => $order['channel_order'],
                "paychannel"    => isset($order['paychannel']) ? $order['paychannel'] : "",
                "uid"           => $uid,
                "openid"        => isset($order['openid']) ? $order['openid'] : "",
                "appid"         => $appid,
                "other_uid"     => isset($order['pay_uid']) ? $order['pay_uid'] : 0,
                "other_openid"  => isset($order['pay_openid']) ? $order['pay_openid'] : "",
                "serverid"      => isset($order['serverid']) ? $order['serverid'] : 0,
                "plantb"        => isset($order['get_plantb']) ? $order['get_plantb'] : 0,
                "rmb"           => isset($order['money']) ? $order['money'] : 0,
                "balance"       => $order['balance'],
                "stat"          => 8,
                "action"        => $order['action'],
                "type"          => 0,
            );
            $ret2 = $userOrderModel->newOrder($orderid, $tradeid, $userOrder);
            if (!$ret2) {
                UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "result" => "false", "log" => "create userorder failed", "orderid" => $orderid, "tradeid" => $tradeid, "userOrder" => $userOrder), __METHOD__ . "[" . __LINE__ . "]", false);
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "用户充值记录添加失败", "tbl" => "userorder", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
            }

            /**
             * 发送统计
             */
            $properties = [
                "orderid"   => $orderid,
                "tradeid"   => $tradeid,
                "payunit"   => "rmb",
                "_gameid"   => $appid,
                "serverid"  => $order['serverid'],
                "openid"    => $openid,
                "payamount" => $order['money'],
                "paycoin"   => $get_plantb,
                //"from"      => $from,
                "source"    => $order['paychannel'],
                "paytype"   => 2,
                "isall"     => 1, // 表示日志数据是全的，不需要再从数据库补数据
            ];
            DataCenterStatModel::stat("vrplat", "recharge", $uid, $properties);
            UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "result" => "success", "log" => "success", "orderid" => $orderid, "tradeid" => $tradeid), __METHOD__ . "[" . __LINE__ . "]", false);
            return true;

        } else if ($status == Config::get("pay.pay_status.plantb_not_enough") || $status == Config::get("pay.pay_status.pay_failed")) {
            /**
             * 平台币余额不足，导致充值失败
             * 只有在充游戏的时候才会出现
             * 判断现在平台币余额是否够
             * 再判断消费订单是否未发货
             * 扣平台币
             * 发货不处理，等待消费订单补单的时候发货
             */
            UdpLog::save2("pay.vronline.com/retry/order", array("function" => "startRetryOrder", "result" => "false", "log" => "status error, not plantb_not_enough or pay_failed status", "orderid" => $orderid, "tradeid" => $tradeid, "order" => $order), __METHOD__ . "[" . __LINE__ . "]", false);
            $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "状态错误，平台币不足、平台币支付错误都是消费表的状态，不是订单表的", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
            LogModel::addLog($errlog);
        }
    }

    /**
     * 补单消费订单
     * 所有未成功的消费订单都是要补单的，包括未支付平台币的
     */
    public function retryConsume($timestamp)
    {
        $nowstamp = time();

        /**
         * 先读订单信息
         * 如果有订单信息的，一定是要充平台币的，至于是否要消费，要看订单信息
         */
        $consumeModel = new ConsumeModel;

        $consume = array();

        $retry_interval = Config::get("pay.retry_interval");
        $retrytotal     = count($retry_interval) < 10 ? 10 : count($retry_interval);

        $stat = Config::get("pay.pay_status.success");
        /**
         * 先处理昨天的订单
         */
        $rows = $consumeModel->getUnSuccessTrade($timestamp, $stat, $retrytotal);
        if ($rows) {
            for ($j = 0; $j < count($rows); $j++) {
                $retrytime = $rows[$j]['retrytime'];
                $retrynum  = $rows[$j]['retrynum'];
                if ($retrynum > $retrytotal) {
                    continue;
                }
                $interval = $retry_interval[$retrynum];
                if (!$interval) {
                    $interval = 10 * 60;
                }

                /**
                 * 上次补单的时间距离当前时间不够该次补单的间隔，不补单
                 */
                if ($nowstamp - $retrytime < $interval) {
                    continue;
                }
                $consume[] = $rows[$j];
            }
        }
        /**
         * 开始补单
         */
        for ($i = 0; $i < count($consume); $i++) {
            $row = $consume[$i];
            $this->startRetryConsume($row);
        }

    }

    /**
     * 开始补单
     * 根据消费订单，读充值订单，判断充值订单的状态
     * 如果充值订单不是成功状态，消费订单不补单
     * 如果是只充平台币，不会有消费订单
     * 所以，如果有消费订单，查看对应的充值订单是否是成功状态
     * 如果充值订单是成功状态，并且消费订单，则消费订单就要补单
     */
    public function startRetryConsume($consume)
    {
        $orderid = $consume['orderid'];
        $tradeid = $consume['tradeid'];
        UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "log" => "startRetryConsume:start", "orderid" => $orderid, "tradeid" => $tradeid), __METHOD__ . "[" . __LINE__ . "]", false);

        $nowstamp = time();

        /**
         * 重试次数
         */
        $retrynum = $consume['retrynum'];

        /**
         * 订单状态
         */
        $status = $consume['stat'];

        /**
         * 这次重试跑完后，新的重试次数
         */
        $newretrynum = $retrynum + 1;

        /**
         * 如果是true，说明扣币成功，可以开始发货
         */
        $start_delivery = false;

        $orderModel = new OrderModel;

        if ($status == Config::get("pay.pay_status.success")) {
            return true;
        }

        $userModel      = new UserModel;
        $consumeModel   = new ConsumeModel;
        $userOrderModel = new UserOrderModel;

        /**
         * 如果有充值订单，判断充值订单的状态是否是成功
         * 只有充值订单是成功的消费订单裁补单
         */
        if ($orderid) {
            $orderinfo = $orderModel->getOrderById($orderid);
            if (!$orderinfo || !is_array($orderinfo)) {
                UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "get order failed", "orderid" => $orderid, "tradeid" => $tradeid, "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]", false);
                $errlog = array("orderid" => $orderid, "action" => "retry_order", "errmsg" => "读订单信息错误，或订单不存在", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
            $order_stat = $orderinfo['stat'];

            /**
             * 如果充值订单未支付，将消费订单重试次数改大，下次不再处理，等充值订单补成功后，再将重试次数改0，开始补单
             */
            if ($order_stat == Config::get("pay.pay_status.wait_pay")) {
                $upd = $consumeModel->updateTrade($tradeid, array("retrytime" => $nowstamp, "retrynum" => 15));
                return false;
            }
            if ($order_stat != Config::get("pay.pay_status.success")) {
                UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "order stat is not success", "orderid" => $orderid, "tradeid" => $tradeid, "orderinfo" => $orderinfo), __METHOD__ . "[" . __LINE__ . "]", false);
                $errlog = array("orderid" => $orderid, "action" => "retry_order", "errmsg" => "充值订单状态不是成功状态", "tbl" => "order", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
        } else {
            /**
             * 如果没有充值订单，将消费订单次数改大，下次不再处理，等充值订单补成功后，再将重试次数改0，开始补单
             */
            $upd = $consumeModel->updateTrade($tradeid, array("retrytime" => $nowstamp, "retrynum" => 15));
        }
        //UdpLog::save2("pay.vronline.com/retry/consume", "[{$tradeid}] check orderid status success", __METHOD__."[".__LINE__."]", false);

        $uid       = $consume['uid'];
        $openid    = $consume['openid'];
        $to_uid    = $consume['to_uid'];
        $to_openid = $consume['to_openid'];
        $appid     = $consume['appid'];
        $serverid  = $consume['serverid'];
        $amount    = $consume['amount'];
        $status    = $consume['stat'];

        /**
         * 扣平台币失败的订单处理
         * 如果平台币不足、扣币失败，或者没有扣币（充值订单失败导致） 都需要补单
         */
        if ($status == Config::get("pay.pay_status.wait_pay") || $status == Config::get("pay.pay_status.plantb_not_enough") || $status == Config::get("pay.pay_status.pay_failed")) {
            $extinfo = $userModel->extInfo($uid);
            if (!$extinfo || !isset($extinfo['f_money']) || $extinfo['f_money'] < $amount) {
                UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "money is less than {$amount}", "tradeid" => $tradeid, "uid" => $uid, "extinfo" => $extinfo), __METHOD__ . "[" . __LINE__ . "]", false);
                $upd    = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "retrytime" => $nowstamp, "retrynum" => $newretrynum, "stat" => Config::get("pay.pay_status.plantb_not_enough")));
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "平台币不足", "tbl" => "user.ext", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
            $subB = $userModel->subPlatb($uid, $amount);
            UdpLog::save2("pay.vronline.com/trade", array("function" => "startRetryConsume", "log" => "[retry][sub]", "result" => $subB, "orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "subnum" => $amount, "appid" => $appid, "serverid" => $serverid, "to_uid" => $to_uid, "to_openid" => $to_openid, "openid" => $openid, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]", false);

            if (!$subB) {
                UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "subB failed", "tradeid" => $tradeid, "uid" => $uid, "amount" => $amount), __METHOD__ . "[" . __LINE__ . "]", false);
                $upd    = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "retrytime" => $nowstamp, "retrynum" => $newretrynum, "stat" => Config::get("pay.pay_status.pay_failed")));
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "扣平台币失败", "tbl" => "user.ext", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                return false;
            }
            $extinfo2 = $userModel->extInfo($uid);
            if (!isset($extinfo2['f_money']) || $extinfo2['f_money'] < 0) {
                UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "money is less than 0", "tradeid" => $tradeid, "uid" => $uid, "extinfo" => $extinfo), __METHOD__ . "[" . __LINE__ . "]", false);
                $upd    = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "retrytime" => $nowstamp, "retrynum" => $newretrynum, "stat" => Config::get("pay.pay_status.plantb_not_enough")));
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "平台币不足，扣币后平台币为负", "tbl" => "user.ext", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);

                /**
                 * 扣平台币后发现平台币不足，回滚
                 */
                $addB = $userModel->addPlatb($uid, $amount, 0);
                UdpLog::save2("pay.vronline.com/trade", array("function" => "startRetryConsume", "log" => "[retry][add][rollsub]", "ret" => $addB, "orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "addnum" => $amount, "appid" => $appid, "serverid" => $serverid, "to_uid" => $to_uid, "to_openid" => $to_openid, "openid" => $openid, "paynum" => 0, "consume" => $consume), __METHOD__ . "[" . __LINE__ . "]", false);
                if (!$addB) {
                    UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "addb roll back failed", "tradeid" => $tradeid, "uid" => $uid, "amount" => $amount), __METHOD__ . "[" . __LINE__ . "]", false);
                    $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "扣平台币后，平台币余额为负，回滚平台币失败", "tbl" => "user.ext", "position" => __METHOD__ . "[" . __LINE__ . "]");
                    LogModel::addLog($errlog);
                }
                return false;
            }

            $start_delivery = true;
        }

        /**
         * 扣币成功，或者不需要扣币但发货失败的
         */
        if ($start_delivery || $status == Config::get("pay.pay_status.delivery_failed")) {

            $deliver  = $this->callDeveloper($appid, $consume);
            $callinfo = $this->getDeliverInfo();
            //UdpLog::save2("pay.vronline.com/retry/consume", array("function"=>"startRetryConsume", "log" => "retry_delivery", "ret" => $deliver, "tradeid" => $tradeid, "orderid" => $orderid, "appid" => $appid, "serverid" => $serverid, "uid" => $uid, "openid" => $consume['openid'], "to_uid" => $consume['to_uid'], "to_openid" => $consume['to_openid'], "amount" => $consume['amount'], "callinfo" => $callinfo, "consume" => $consume), __METHOD__."[".__LINE__."]");

            /**
             * 发货失败，结束
             */
            if (!$deliver) {
                $upd = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => Config::get("pay.pay_status.delivery_failed"), "retrynum" => $newretrynum, "retrytime" => $nowstamp, "errmsg" => $this->errmsg, "payurl" => $this->real_payurl));
                UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "delivery failed", "tradeid" => $tradeid, "appid" => $appid, "consumeinfo" => $consume), __METHOD__ . "[" . __LINE__ . "]", false);
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "补单发货失败", "tbl" => "http", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
                if (!$upd) {
                    UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "delivery failed and updateTrade failed", "tradeid" => $tradeid, "stat" => Config::get("pay.pay_status.delivery_failed")), __METHOD__ . "[" . __LINE__ . "]", false);
                    $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "补单发货失败、修改订单状态失败", "tbl" => "consume", "position" => __METHOD__ . "[" . __LINE__ . "]");
                    LogModel::addLog($errlog);
                }
                return false;
            }

            if ($deliver) {
                /**
                 * 发货成功，修改订单状态
                 */
                $upd = $consumeModel->updateTrade($tradeid, array("ftime" => $nowstamp, "stat" => Config::get("pay.pay_status.success"), "retrynum" => $newretrynum, "retrytime" => $nowstamp, "payurl" => $this->real_payurl, "delivery_time" => $this->delivery_time));
                if (!$upd) {
                    UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "delivery success but update trade failed", "tradeid" => $tradeid, "stat" => Config::get("pay.pay_status.success")), __METHOD__ . "[" . __LINE__ . "]", false);
                    $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "补单发货成功，修改消费订单状态失败", "tbl" => "consume", "position" => __METHOD__ . "[" . __LINE__ . "]");
                    LogModel::addLog($errlog);
                }
            }
            /**
             * 添加用户的消费记录
             * 给用户看的
             */
            $userOrder = array(
                "orderid"       => $orderid,
                "tradeid"       => $tradeid,
                "paycenterid"   => isset($consume['paycenterid']) ? $consume['paycenterid'] : "",
                "channel_order" => isset($consume['channel_order']) ? $consume['channel_order'] : "",
                "paychannel"    => isset($consume['paychannel']) ? $consume['paychannel'] : "",
                "uid"           => $uid,
                "openid"        => isset($consume['openid']) ? $consume['openid'] : "",
                "appid"         => $appid,
                "other_uid"     => isset($consume['to_uid']) ? $consume['to_uid'] : 0,
                "other_openid"  => isset($consume['to_openid']) ? $consume['to_openid'] : "",
                "serverid"      => isset($consume['serverid']) ? $consume['serverid'] : 0,
                "plantb"        => isset($consume['amount']) ? $consume['amount'] : 0,
                //"rmb"       => isset($consume['pay']) ? $consume['pay'] : 0,
                "rmb"           => 0,
                "price"         => isset($consume['price']) ? $consume['price'] : 0,
                "num"           => isset($consume['num']) ? $consume['num'] : 0,
                "item"          => isset($consume['item']) ? $consume['item'] : "",
                "itemid"        => isset($consume['itemid']) ? $consume['itemid'] : "",
                "balance"       => isset($consume['balance']) ? $consume['balance'] : 0,
                "stat"          => 8,
                "action"        => "game",
                "type"          => 9,
            );
            $ret2 = $userOrderModel->newOrder($orderid, $tradeid, $userOrder);
            if (!$ret2) {
                UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "false", "log" => "newUserOrder failed", "orderid" => $orderid, "tradeid" => $tradeid, "userOrder" => $userOrder), __METHOD__ . "[" . __LINE__ . "]", false);
                $errlog = array("orderid" => $orderid, "tradeid" => $tradeid, "uid" => $uid, "openid" => $openid, "appid" => $appid, "action" => "retry_order", "errmsg" => "用户充值记录添加错误[" . json_encode($orderid) . "]", "tbl" => "userorder", "position" => __METHOD__ . "[" . __LINE__ . "]");
                LogModel::addLog($errlog);
            }

            /**
             * 发送统计
             */
            if ($uid > 1000) {
                $rate       = Config::get("common.plantb_count_per_rmb");
                $properties = [
                    "tradeid"   => $tradeid,
                    "orderid"   => $orderid,
                    "payunit"   => "vrb",
                    "_gameid"   => $appid,
                    "serverid"  => $serverid,
                    "openid"    => $openid,
                    "payamount" => round($consume['amount'] / $rate, 2),
                    "itemid"    => $appid,
                    "item"      => $consume['item'],
                    "itemprice" => $consume['price'],
                    "itemcount" => $consume['num'],
                    "itemtype"  => "game",
                    "isall"     => 1, // 表示日志数据是全的，不需要再从数据库补数据
                ];
                DataCenterStatModel::stat("vrplat", "buyitem", $uid, $properties);
            }

            UdpLog::save2("pay.vronline.com/retry/consume", array("function" => "startRetryConsume", "result" => "success", "log" => "success", "orderid" => $orderid, "tradeid" => $tradeid), __METHOD__ . "[" . __LINE__ . "]", false);
            return true;
        } else {
            return true;
        }
    } // end function

}
