<?php

/*
 * 线下体验店充值对账
 * 先对商户账单和usercenter账单
 * 再对渠道账单和usercenter账单
 * date:2017/2/9
*/

namespace App\Models;

use Config;
use Helper\UdpLog;
use Helper\Library;
use Helper\HttpRequest;
use App\Models\ToBCheckBillDBModel;
use Illuminate\Database\Eloquent\Model;

class ToBCheckBillModel extends Model
{
    /**
     * 汇付宝商户号
     */
    private $heepay_merchantid = 2075980;

    /**
     * 汇付宝对账接口密钥
     */
    private $heepay_check_bill_key = "50A5037969F94B788F1243CB";

    /**
     * 汇付宝批付接口密钥
     */
    private $heepay_transfer_money_key = "EF09FA6233D44F1A95FE7FB2";

    /**
     * 汇付宝退款订单对账接口密钥
     */
    private $heepay_check_refund_bill_key = "EC228E01529B4934B951F22A";

    /**
     * 汇付宝退款订单对账接口密钥
     */
    private $heepay_cash_3DES_key = "EAB662E952524C28B1E522A3";

    /**
     * 汇付宝对账接口密钥
     */
    private $heepay_check_bill_api = "https://www.heepay.com/API/Check/PaymentCheck.aspx";

    /**
     * 汇付宝退款订单对账接口密钥
     */
    private $heepay_check_refund_bill_api = "https://www.heepay.com/API/Check/PaymentRefundCheck.aspx";

    /**
     * 所有渠道列表，用于检测是否所有渠道的对账单都拉过来了
     */
    private $channel_list = ["heepay"];

    /**
     * 对账
     * 先判断有没有对过
     * 核对2b商家订单和usercenter订单是否一致
     * 提现订单不处理
     * 对账成功，写入对账状态表里channel=usercenter
     * 凌晨1点开始，每小时跑一次
     */
    public function check2BBill($nowstamp)
    {
        $udpLogPath = "pay.vronline.com/checkBill/merchant2Usercenter";
	    UdpLog::save2($udpLogPath, array("result" => "start", "log" => "check2BBill start", "args" => func_get_args()), null, false);

        $yestoday = date("Ymd", $nowstamp - 86400);

        $toBCheckBillDBModel = new ToBCheckBillDBModel;

        /**
         * 先判断有没有获取过账单
         */
        $merchantStatus = $toBCheckBillDBModel->getCheckStatus(["channel"=>"usercenter", "day"=>$yestoday]);
        if($merchantStatus) {
		    UdpLog::save2($udpLogPath, array("result" => "done", "log" => "has done"), null, false);
            return true;
        }
        /**
         * 先比较订单数量和总金额是否相等，如果不相等就认为对账失败
         */
        $param['day']  = $yestoday;
        $param['ts']   = $nowstamp;
        $param['type'] = "getOrderTotal4Check";
        $sign     = Library::encrypt($param, Config::get("common.uc_appkey"), $params);
        $url      = "http://tob.vronline.com/getOrderTotal4Check";
        $http_ret = HttpRequest::get($url, $params);
        if(!$http_ret) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getOrderTotal4Check error", "url" => $url, "params" => $params, "http_ret" => $http_ret, "error" => HttpRequest::getError()), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        $orderTotal = json_decode($http_ret, true);
        if(!$orderTotal || !is_array($orderTotal)) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getOrderTotal4Check json_decode error", "url" => $url, "params" => $params, "http_ret" => $http_ret, "orderTotal" => $orderTotal), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        if(!isset($orderTotal['code']) || $orderTotal['code'] != 0 || !isset($orderTotal['data']) || !$orderTotal['data']) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getOrderTotal4Check code or data error", "url" => $url, "params" => $params, "orderTotal" => $orderTotal), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        $toBOrderModel = new ToBOrderModel;

        $total_brief = $toBOrderModel->getOrderSumBrief4Check($yestoday);
        if(!$total_brief || !is_array($total_brief)) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "getOrderSumBrief4Check error", "yestoday" => $yestoday, "total_brief" => $total_brief), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }
        $totalcount = $total_brief['total_count'];
        $totalsum   = $total_brief['total_amount'];
        $paysum     = $total_brief['pay_amount'];
        $cpfee      = $total_brief['cp_fee'];
        $platfee    = $total_brief['plat_fee'];
        $merchantfee = $total_brief['merchant_fee'];

        /**
         * 比较总金额与总数量，商家订单里包括优惠券的金额
         */
        if($totalcount != $orderTotal['data']['total_count'] || $totalsum != $orderTotal['data']['total_amount'] || $paysum != $orderTotal['data']['pay_amount'] || $merchantfee != $orderTotal['data']['merchant_fee']) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "total unmatch", "yestoday" => $yestoday, "totalcount" => $totalcount, "total_count" => $orderTotal['data']['total_count'], "totalsum" => $totalsum, "total_amount" => $orderTotal['data']['total_amount'], "paysum" => $paysum, "pay_amount" => $orderTotal['data']['pay_amount'], "merchantfee" => $merchantfee, "merchant_fee" => $orderTotal['data']['merchant_fee']), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }

        /**
         * 如果有订单，核对订单
         * 如果没有订单，直接写状态
         */
        if($totalcount > 0)
        {
            /**
             * 拉商家订单
             */
            $param['day']  = $yestoday;
            $param['ts']   = $nowstamp;
            $param['type'] = "getOrder4Check";
            $sign     = Library::encrypt($param, Config::get("common.uc_appkey"), $params);
            $url      = "http://tob.vronline.com/getOrder4Check";
            $http_ret = HttpRequest::get($url, $params);
            if(!$http_ret) {
                UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getOrder4Check error", "url" => $url, "params" => $params, "http_ret" => $http_ret, "error" => HttpRequest::getError()), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }
            $orderTotal = json_decode($http_ret, true);
            if(!$orderTotal || !is_array($orderTotal)) {
                UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getOrder4Check json_decode error", "url" => $url, "params" => $params, "http_ret" => $http_ret, "orderTotal" => $orderTotal), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }
            if(!isset($orderTotal['code']) || $orderTotal['code'] != 0 || !isset($orderTotal['data'])) {
		        UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getOrder4Check code or data error", "url" => $url, "params" => $params, "orderTotal" => $orderTotal), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }
            $merchantOrder = $orderTotal['data'];

            /**
             * 读用户中心订单
             */
            $ucenterOrder  = $toBOrderModel->getOrderBrief4Check($yestoday);
            if(!$merchantOrder || !is_array($merchantOrder) || !$ucenterOrder || !is_array($ucenterOrder) || count($merchantOrder) != count($ucenterOrder) || count($ucenterOrder) != $totalcount) {
		        UdpLog::save2($udpLogPath, array("result" => "false", "log" => "getOrderBrief4Check error", "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }

            /**
             * 核对订单
             */
            $checktotal = $checkpay = $checkmerchant = $checkcp = $checkplat = 0;
            foreach($merchantOrder as $orderid => $arr_detail)
            {
                $amount = $arr_detail['total_rmb'];
                $checktotal = bcadd($checktotal, $amount, 2);
                $checkpay = bcadd($checkpay, $arr_detail['pay_rmb'], 2);
                $checkmerchant = bcadd($checkmerchant, $arr_detail['merchant_fee'], 2);
                $checkcp = bcadd($checkcp, $arr_detail['cp_fee'], 2);
                $checkplat = bcadd($checkplat, $arr_detail['plat_fee'], 2);
                if(!isset($ucenterOrder[$orderid])) {
		            UdpLog::save2($udpLogPath, array("result" => "false", "log" => "can't find order in usercenter", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]", false);
                    return false;
                }
                if($ucenterOrder[$orderid]['total_rmb'] != $amount || $ucenterOrder[$orderid]['pay_rmb'] != $arr_detail['pay_rmb'] || $ucenterOrder[$orderid]['merchant_fee'] != $arr_detail['merchant_fee'] || $ucenterOrder[$orderid]['merchantid'] != $arr_detail['merchantid'] || $ucenterOrder[$orderid]['cp_fee'] != $arr_detail['cp_fee'] || $ucenterOrder[$orderid]['plat_fee'] != $arr_detail['plat_fee']) {
		            UdpLog::save2($udpLogPath, array("result" => "false", "log" => "order unmatch", "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
                    return false;
                }else {
                }
            }
            if($checktotal != $totalsum || $paysum != $checkpay || $checkmerchant != $merchantfee || $cpfee != $checkcp || $platfee != $checkplat) {
		        UdpLog::save2($udpLogPath, array("result" => "false", "log" => "total unmatch2", "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }else {
            }
        }

        /**
         * 对账成功，写入状态
         */
        $statInfo = ["channel"=>"usercenter", "day"=>$yestoday, "num"=>$totalcount, "total_amount"=>$totalsum, "pay_amount"=>$paysum, "merchant_fee"=>$merchantfee, "cp_fee"=>$cpfee, "plat_fee"=>$platfee];
        $ret = $toBCheckBillDBModel->addCheckStatus($statInfo);
        if(!$ret) {
            UdpLog::save2($udpLogPath, array("result" => "false", "log" => "addCheckStatus error", "statInfo" => $statInfo, "ret" => $ret), __METHOD__ . "[" . __LINE__ . "]", false);
            /**
             * 这里要报警
             */
        }
        UdpLog::save2($udpLogPath, array("result" => "success", "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
        return true;
    }


    /**
     * 渠道订单与用户中心订单对账
     * 读取拉过来的渠道订单
     * 与用户中心的订单比对订单号与金额
     * 完全一致，修改订单对账状态 channel=channel_check
     * 最后修改商家余额
     * 每天凌晨4点钟开始跑，每小时跑一次
     */
    public function checkUCenterBill($nowstamp)
    {
        $udpLogPath = "pay.vronline.com/checkBill/usercenter2Channel";
	    UdpLog::save2($udpLogPath, array("result" => "start", "log" => "checkUCenterBill start", "args" => func_get_args()), null, false);

        $yestoday = date("Ymd", $nowstamp - 86400);

        $toBCheckBillDBModel = new ToBCheckBillDBModel;

        /**
         * 检查渠道订单与用户中心订单是否对账完成，对账完成了，就不处理了
         */
        $channelStatus = $toBCheckBillDBModel->getCheckStatus(["channel"=>"channel_check", "day"=>$yestoday]);
        if($channelStatus) {
		    UdpLog::save2($udpLogPath, array("result" => "done", "log" => "has done"), null, false);
            return true;
        }

        /**
         * 拿到所有的对账状态，及渠道订单状态
         * 先检查商家订单是否与用户中心订单对账完成，对账完成，才开始对渠道订单与用户中心订单
         * 再检查商家与用户中心订单是否对账完成，如果没有对账完成，不继续处理，先等他们对账完成
         * 再检查渠道订单是否都拉过来了，如果有一个渠道的没拉过来，就不处理
         * 最后再比对渠道订单总数量与总金额是否与用户中心订单相符
         */
        $checkStatus = $toBCheckBillDBModel->getCheckStatus(["day"=>$yestoday]);
        if(!$checkStatus) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "getCheckStatus error", "checkStatus" => $checkStatus, "param" => ["day"=>$yestoday]), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }

        /**
         * 商家与用户中心未对账
         */
        if(!isset($checkStatus['usercenter']) || !$checkStatus['usercenter']) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "usercenter to channel undeal"), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }

        /**
         * 用户中心订单总数量
         */
        $ucenter_total_count = $checkStatus['usercenter']['num'];

        /**
         * 用户中心订单总金额，不包括优惠券
         */
        $ucenter_pay_amount = $checkStatus['usercenter']['pay_amount'];

        /**
         * 用户中心订单总金额，包括优惠券
         */
        $ucenter_total_amount = $checkStatus['usercenter']['total_amount'];

        /**
         * 用户中心商家总收入
         */
        $ucenter_merchant = $checkStatus['usercenter']['merchant_fee'];

        /**
         * 渠道订单总数量
         */
        $channel_total_count = 0;

        /**
         * 渠道订单总金额
         */
        $channel_total_amount = 0;

        /**
         * 再检查渠道订单是否都拉过来了
         * 如果有一个渠道的没拉过来，就不处理
         * 并统计渠道订单总数量与总金额
         */
        for($i = 0; $i < count($this->channel_list); $i++) {
            $channel_name = $this->channel_list[$i];
            if(!isset($checkStatus[$channel_name]) || !$checkStatus[$channel_name]) {
		        UdpLog::save2($udpLogPath, array("result" => "false", "log" => "miss one channel's bill", "channel_name" => $channel_name), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }
            $channel_total_count  += $checkStatus[$channel_name]['num'];
            $channel_total_amount = bcadd($channel_total_amount, $checkStatus[$channel_name]['pay_amount'], 2);
        }

        /**
         * 总额不对
         */
        if($ucenter_total_count != $channel_total_count || $ucenter_pay_amount != $channel_total_amount) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "total not match", "ucenter_total_count" => $ucenter_total_count, "channel_total_count" => $channel_total_count, "ucenter_pay_amount" => $ucenter_pay_amount, "channel_total_amount" => $channel_total_amount), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }

        /**
         * 如果总数是对的，开始对详细订单
         */
        if($ucenter_total_count > 0)
        {
            /**
             * 用户中心订单
             */
            $toBOrderModel = new ToBOrderModel;
            $ucenterOrder  = $toBOrderModel->getOrderBrief4Check($yestoday);
            if(!$ucenterOrder || !is_array($ucenterOrder)) {
		        UdpLog::save2($udpLogPath, array("result" => "false", "log" => "getOrderBrief4Check error", "yestoday" => $yestoday, "ucenterOrder" => $ucenterOrder), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }

            /**
             * 渠道订单
             */
            $start = date("Y-m-d", strtotime($yestoday));
            $end = date("Y-m-d", $nowstamp);
            $channelOrder = $toBCheckBillDBModel->getHeepayBillByTime($start, $end);
            if(!$channelOrder || !is_array($channelOrder)) {
		        UdpLog::save2($udpLogPath, array("result" => "false", "log" => "getHeepayBillByTime error", "start" => $start, "end" => $end, "channelOrder" => $channelOrder), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }

            /**
             * 核对订单
             */
            $checktotal = $checkpay = $checkmerchant = $checkcp = $checkplat = 0;
            foreach($ucenterOrder as $field => $arr_detail)
            {
                $amount     = $arr_detail['total_rmb'];
                $checktotal = bcadd($amount, $checktotal, 2);
                $checkpay   = bcadd($arr_detail['pay_rmb'], $checkpay, 2);
                $checkmerchant = bcadd($arr_detail['merchant_fee'], $checkmerchant, 2);
                $checkcp    = bcadd($arr_detail['cp_fee'], $checkcp, 2);
                $checkplat  = bcadd($arr_detail['plat_fee'], $checkplat, 2);

                $c = $this->getRealChannel($arr_detail['pay_channel']);
                $o = $arr_detail['paychannel_orderid'];
                if(!$c || !$o) {
                    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "get detail error", "c" => $c, "o" => $o, "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
                    return false;
                }
                $neworder = $c."_".$o;
                if(!isset($channelOrder[$neworder]) || !$channelOrder[$neworder]) {
                    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "can't find channel order", "neworder" => $neworder, "start" => $start, "end" => $end), __METHOD__ . "[" . __LINE__ . "]", false);
                    return false;
                }
                if($channelOrder[$neworder]['pay_rmb'] != $arr_detail['pay_rmb']) {
                    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "pay_rmb not match", "channel" => $channelOrder[$neworder]['pay_rmb'], "ucenter" => $arr_detail['pay_rmb']), __METHOD__ . "[" . __LINE__ . "]", false);
                    return false;
                }
            } // end foreach

            /**
             * 再次校验
             */
            if($checkpay != $ucenter_pay_amount || $ucenter_merchant != $checkmerchant || $checktotal != $ucenter_total_amount || $ucenter_total_count != count($channelOrder)) {
                UdpLog::save2($udpLogPath, array("result" => "false", "log" => "total unmatch2", "checkpay" => $checkpay, "ucenter_pay_amount" => $ucenter_pay_amount, "ucenter_merchant" => $ucenter_merchant, "checkmerchant" => $checkmerchant, "checktotal" => $checktotal, "ucenter_total_amount" => $ucenter_total_amount, "ucenter_total_count" => $ucenter_total_count, "count(channelOrder)" => count($channelOrder)), __METHOD__ . "[" . __LINE__ . "]", false);
                return false;
            }
        } // end if

        /**
         * 对账成功，写入记录
         */
        $statInfo = ["channel"=>"channel_check", "day"=>$yestoday, "num"=>$ucenter_total_count, "pay_amount"=>$ucenter_pay_amount, "total_amount"=>$ucenter_total_amount, "merchant_fee"=>$ucenter_merchant, "cp_fee"=>$checkStatus['usercenter']['cp_fee'], "plat_fee"=>$checkStatus['usercenter']['plat_fee']];
        $ret = $toBCheckBillDBModel->addCheckStatus($statInfo);
        if(!$ret) {
            UdpLog::save2($udpLogPath, array("result" => "false", "log" => "addCheckStatus error", "statInfo" => $statInfo, "ret" => $ret), __METHOD__ . "[" . __LINE__ . "]", false);
            /**
             * 这里要报警
             */
        }

        UdpLog::save2($udpLogPath, array("result" => "success", "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
        return true;
    }


    /**
     * 余额结算
     * 凌晨5点跑，每小时跑一次
     */
    public function settlement($nowstamp)
    {
        $udpLogPath = "pay.vronline.com/checkBill/settlement";
	    UdpLog::save2($udpLogPath, array("result" => "start", "log" => "settlement start", "args" => func_get_args()), null, false);

        $yestoday = date("Ymd", $nowstamp - 86400);

        $toBCheckBillDBModel = new ToBCheckBillDBModel;

        /**
         * 检查是否结算过
         * 如果已结算，不再处理
         */
        $settlementStatus = $toBCheckBillDBModel->getCheckStatus(["channel"=>"settlement", "day"=>$yestoday]);
        if($settlementStatus) {
		    UdpLog::save2($udpLogPath, array("result" => "done", "log" => "has done"), null, false);
            return true;
        }

        /**
         * 再判断是否对账完成
         * 如果没对账完成，先对账
         */
        $channelStatus = $toBCheckBillDBModel->getCheckStatus(["channel"=>"channel_check", "day"=>$yestoday]);
        if(!$channelStatus) {
            UdpLog::save2($udpLogPath, array("result" => "false", "log" => "getCheckStatus error", "channelStatus" => $channelStatus, "param" => ["channel"=>"channel_check", "day"=>$yestoday]), __METHOD__ . "[" . __LINE__ . "]", false);
            return false;
        }

        /**
         * 统计每个商家的余额，结算前一天的收入  http://tob.vronline.com/getMerchantIncome?day=20170210&ts=1&sign=1
         * 从未结算的余额中，减去这部分，加到已结算余额中
         * 最后再修改状态
         */

        $param['day']  = $yestoday;
        $param['ts']   = $nowstamp;
        $param['type'] = "getMerchantIncome";
        $sign     = Library::encrypt($param, Config::get("common.uc_appkey"), $params);
        $url      = "http://tob.vronline.com/getMerchantIncome";
        $http_ret = HttpRequest::get($url, $params);
        if(!$http_ret) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getMerchantIncome error", "url" => $url, "params" => $params, "http_ret" => $http_ret, "error" => HttpRequest::getError()), null, false);
            return false;
        }
        $return = json_decode($http_ret, true);
        if(!$return || !is_array($return)) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getMerchantIncome json_decode error", "url" => $url, "params" => $params, "http_ret" => $http_ret, "return" => $return), null, false);
            return false;
        }
        if(!isset($return['code']) || $return['code'] != 0 || !isset($return['data']) || !$return['data'] || !is_array($return['data'])) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "HttpRequest::get getMerchantIncome code or data error", "url" => $url, "params" => $params, "return" => $return), null, false);
            return false;
        }
        $brief = $return['data'];
        /**
         * 获得商家列表，获得商家结算统计
         * 获得已结算的商家列表，从 t_2b_day_bill 表中
         * 循环判断是否有未结算的
         * 如果未结算，先判断商家余额与结算金额是否足够结算，再插入、修改结算状态，如果不需要结算的，直接修改状态。如果余额不足，修改状态为结算失败
         * 全部结算完成后，修改 t_2b_check_bill_status 表结算状态，全部完成指明确结算成功和结算失败的，下个整点不再跑该脚本，忽略开始结算未知结果的
         */
        /**
         * t_2b_day_bill 表处理
         * 防止结算失败重复结算
         * 结算前，先插入一条记录，状态是0，下次结算时有记录了，就不结算了，如果失败，就手动结算，结算完成修改状态，
         * 开始结算，结算成功、失败都修改状态
         * 下次结算的时候，如果状态是0，就表示上一次结算中途报错退出，需要人工干预。
         * 正常情况下要么是成功状态，要么是失败状态，不可能是0
         */

        $dayBills = $toBCheckBillDBModel->getAllBillByDay($yestoday);
        if(!is_array($dayBills)) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "getAllBillByDay error", "yestoday" => $yestoday, "dayBills" => $dayBills), null, false);
            return false;
        }

        /**
         * 是否全部结算完成的状态
         * 全部结算完成了，修改当天总的结算状态
         */
        $isAllSettlemented = true;
        foreach($brief as $merchantid => $arr_detail)
        {
            /**
             * 结算成功的
             */
            if(isset($dayBills[$merchantid])) {
                /**
                 * 结算成功的，跳过
                 */
                if($dayBills[$merchantid]['status'] == 8) {
                    continue;
                }
            }

            /**
             * 未结算过的，开始结算
             * 先插入状态是0的结算记录
             */
            $balance = $toBCheckBillDBModel->getBalance($merchantid);
            if($balance === null) {
                $balance = ['net_income' => 0, 'new_net_income' => 0];
            }
            if(!is_array($balance)) {       // 结算失败
                $info = ['status' => 4];
                $add = $toBCheckBillDBModel->addOneDayBill($merchantid, $yestoday, $info);
                continue;
            }

            /**
             * 如果当天没有收入
             * 直接结算成功，修改状态
             * 未插入状态是0的初始记录，不需要判断状态
             */
            if($arr_detail['merchant_fee'] == 0) {
                $info = [];
                $info['total_amount'] = $arr_detail['total_amount'];
                $info['pay_amount'] = $arr_detail['pay_amount'];
                $info['net_income'] = 0;
                $info['pre_net_income'] = $balance['net_income'];
                $info['done_net_income'] = $balance['net_income'];
                $info['status'] = 8;
                $add = $toBCheckBillDBModel->addOneDayBill($merchantid, $yestoday, $info);
                if($add) {
                    $r = $toBCheckBillDBModel->updBalance($merchantid, ['last_settlement_time' => date("Y-m-d H:i:s")]);
                }
                $this->addMerchantDayOrder($merchantid, $yestoday, $info);
                continue;
            }else {
            }

            /**
             * 开始结算
             * 先判断未结算的余额是否够，不够不能结算
             */
            if($balance['new_net_income'] < $arr_detail['merchant_fee']) {
                if(isset($dayBills[$merchantid])) {
                    if($dayBills[$merchantid]['status'] == 3) {
                        continue;
                    }else {
                        $r = $toBCheckBillDBModel->updOneDayBill($merchantid, $yestoday, ['status' => 3]);
                    }
                    continue;
                }else {
                    $r = $toBCheckBillDBModel->addOneDayBill($merchantid, $yestoday, ['net_income' => $balance['new_net_income'], 'status' => 3]);
                    continue;
                }
            }
            /**
             * 结算过，但失败的，改为未结算状态，重新结算
             * 未结算的，插入一条记录，开始结算
             * 其他的先不处理
             */
            if(isset($dayBills[$merchantid]) && ($dayBills[$merchantid]['status'] == 4 || $dayBills[$merchantid]['status'] == 3)) {
                $info = [];
                $info['total_amount']      = $arr_detail['total_amount'];
                $info['pay_amount']        = $arr_detail['pay_amount'];
                $info['net_income']        = $arr_detail['merchant_fee'];
                $info['pre_net_income']    = $balance['net_income'];
                $info['done_net_income']   = 0;
                $info['status']            = 0;
                $set = $toBCheckBillDBModel->updOneDayBill($merchantid, $yestoday, $info);
            }else if(!isset($dayBills[$merchantid])) {
                $info = [];
                $info['total_amount']      = $arr_detail['total_amount'];
                $info['pay_amount']        = $arr_detail['pay_amount'];
                $info['net_income']        = $arr_detail['merchant_fee'];
                $info['pre_net_income']    = $balance['net_income'];
                $info['done_net_income']   = 0;
                $info['status']            = 0;
                $set = $toBCheckBillDBModel->addOneDayBill($merchantid, $yestoday, $info);
            }else {
                continue;
            }

            if(!$set) {
                $isAllSettlemented = false;
                UdpLog::save2($udpLogPath, array("result" => "false", "log" => "updOneDayBill or addOneDayBill error", "merchantid" => $merchantid, "yestoday" => $yestoday, "info" => $info), __METHOD__ . "[" . __LINE__ . "]", false);
                continue;
            }

            /**
             * 开始结算
             */
            $set = $toBCheckBillDBModel->settlementBalance($merchantid, $arr_detail['merchant_fee'], md5($merchantid.$arr_detail['merchant_fee']));
            if(!$set) {     // 结算失败
                $r = $toBCheckBillDBModel->updOneDayBill($merchantid, $yestoday, ['status' => 4]);
                UdpLog::save2($udpLogPath, array("result" => "false", "log" => "settlementBalance error", "merchantid" => $merchantid, "merchant_fee" => $arr_detail['merchant_fee']), __METHOD__ . "[" . __LINE__ . "]", false);
                continue;
            }else {
                /**
                 * 结算成功
                 * 查最新的余额，并修改状态
                 */
                $balance2 = $toBCheckBillDBModel->getBalance($merchantid);
                if(!is_array($balance2)) {
                    $r = $toBCheckBillDBModel->updOneDayBill($merchantid, $yestoday, ['done_net_income' => -999999, 'status' => 8]);     // 结算成功，但读最新的余额失败，就写该值
                }else {
                    $r = $toBCheckBillDBModel->updOneDayBill($merchantid, $yestoday, ['done_net_income' => $balance2['net_income'], 'status' => 8]);
                }
                $info['done_net_income'] = isset($balance2['net_income']) ? $balance2['net_income'] : -999999;
                $info['status']          = 8;
                $this->addMerchantDayOrder($merchantid, $yestoday, $info);
                continue;
            }
        }

        /**
         * 结算成功
         */
        $statInfo = ["channel"=>"settlement", "day"=>$yestoday, "num"=>$channelStatus['num'], "total_amount" => $channelStatus['total_amount'], "pay_amount"=>$channelStatus['pay_amount'], "cp_fee"=>$channelStatus['cp_fee'], "plat_fee"=>$channelStatus['plat_fee'], "merchant_fee"=>$channelStatus['merchant_fee']];
        $ret = $toBCheckBillDBModel->addCheckStatus($statInfo);
        if(!$ret) {
            UdpLog::save2($udpLogPath, array("result" => "false", "log" => "addCheckStatus error", "statInfo" => $statInfo, "ret" => $ret), __METHOD__ . "[" . __LINE__ . "]", false);
            /**
             * 这里要报警
             */
        }
        UdpLog::save2($udpLogPath, array("result" => "success", "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
        return true;
    }

    /**
     * 添加商户的日统计订单
     * 在 db_2b_store 库中的
     */
    public function addMerchantDayOrder($merchantid, $day, $info)
    {
        if(!$merchantid || !$day || !$info || !is_array($info)) {
            return false;
        }
        $param  = base64_encode(json_encode($info));
        $sign   = Library::encrypt(["merchantid" => $merchantid, "day" => $day, "param" => $param], Config::get("common.uc_paykey"), $params);
        $url    = "http://tob.vronline.com/addDayBill";
        $return = HttpRequest::post($url, $params);
        if($return) {
            $a = json_decode($return, true);
            if($a && isset($a) && isset($a['code']) && $a['code'] == 0) {
                return true;
            }
        }
        return false;
    }


    /**
     * 获取真实的渠道标示
     */
    public function getRealChannel($action)
    {
        if(!$action) {
            return false;
        }
        $channel = [
            "wechath5vr" => "heepay",
            "alipayh5vr" => "heepay",
            "vrrefundquery" => "heepay",            // 汇付宝的退款订单
        ];
        if(!isset($channel[$action])) {
            return false;
        }
        return $channel[$action];
    }


    /**
     * 添加汇付宝的对账单
     * 根据订单渠道，读取渠道订单信息
     * 如果没有、或金额不正确，报错，提醒
     * 从拉过来的订单中，统计订单条数，以及总金额，在插入完成后，从数据库中再统计数量和金额，对比是否一致，不一致表示不成功
     * 凌晨3点开始，每小时跑一次
     */
    public function addHeepayCheckBill($nowstamp)
    {
        $udpLogPath = "pay.vronline.com/checkBill/getHeepayBill";
	    UdpLog::save2($udpLogPath, array("result" => "start", "log" => "addHeepayCheckBill start", "args" => func_get_args()), null, false);

        $yestoday = date("Ymd", $nowstamp - 86400);
        $today = date("Ymd", $nowstamp);
        $channel = "heepay";

        $toBCheckBillDBModel = new ToBCheckBillDBModel;

        /**
         * 先判断有没有获取过账单
         */
        $heepayStatus = $toBCheckBillDBModel->getCheckStatus(["channel"=>$channel, "day"=>$yestoday]);
        if($heepayStatus) {
		    UdpLog::save2($udpLogPath, array("result" => "done", "log" => "has done"), null, false);
            return true;
        }
        $pay = $this->getHeepayCheckBill($yestoday);
        $refund = $this->getHeepayCheckRefundBill($yestoday);
        if(!is_array($pay) || !is_array($refund)) {
            return false;
        }
        $rows = array_merge($pay, $refund);
        $counter = 0;
        $amount = 0;
        if($rows && is_array($rows) && count($rows) > 0) {
            for($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                $res = $toBCheckBillDBModel->newHeepayBill($row);
                if(!$res) {
		        UdpLog::save2($udpLogPath, array("result" => "false", "log" => "newHeepayBill error", "res" => $res, "row" => $row), __METHOD__ . "[" . __LINE__ . "]", false);
                    /**
                     * 这里要报警
                     */
                    return false;
                }
                $counter++;
                $amount = bcadd($row['pay_rmb'], $amount, 2);
            }
            $total_count = $toBCheckBillDBModel->getHeepayBillNumByTime($yestoday, $today);
            $total_amount = $toBCheckBillDBModel->getHeepayBillAmountByTime($yestoday, $today);
            if($total_count != $counter || $total_amount != $amount) {
		    UdpLog::save2($udpLogPath, array("result" => "false", "log" => "total unmatch", "total_count" => $total_count, "counter" => $counter, "total_amount" => $total_amount, "amount" => $amount, "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
                /**
                 * 这里要报警
                 */
                return false;
            }
        }
        $statInfo = ["channel"=>$channel, "day"=>$yestoday, "num"=>$counter, "pay_amount"=>$amount];
        $ret = $toBCheckBillDBModel->addCheckStatus($statInfo);
        if(!$ret) {
            UdpLog::save2($udpLogPath, array("result" => "false", "log" => "addCheckStatus error", "statInfo" => $statInfo, "ret" => $ret), __METHOD__ . "[" . __LINE__ . "]", false);
            /**
             * 这里要报警
             */
        }
        UdpLog::save2($udpLogPath, array("result" => "success", "yestoday" => $yestoday), __METHOD__ . "[" . __LINE__ . "]", false);
        return true;
    }


    /**
     * 对账
     * 根据订单渠道，读取渠道订单信息
     * 比较金额、订单号等是否正确
     * 如果没有、或金额不正确，报错，提醒
     * 对支付订单，用户订单，渠道订单
     * 用户订单中的取现订单不处理
     * 从拉过来的订单中，统计订单条数，以及总金额，在插入完成后，从数据库中再统计数量和金额，对比是否一致，不一致表示不成功
     */
    public function getHeepayCheckBill($day)
    {
        if(!$day) {
            return false;
        }
        $d = date("Ymd", strtotime($day));
        $end = $d . "235959";
        $start = $d . "000000";
        //$end = date("Ymd", strtotime($day) + 86400);
        //$end .= "000000";
        $page = 1;
        $result = $this->getHeepayCheckBillByPage($start, $end, $page);
        if(!$result || !is_array($result)) {
            return false;
        }
        $rows = array();
        $rows = $rows + $result['data'];
        if($result['total_page'] > $page) {
            for($i = 2; $i <= $result['total_page']; $i++) {
                $result = $this->getHeepayCheckBillByPage($start, $end, $i);
                if(!$result || !is_array($result)) {
                    return false;
                }
                $rows = array_merge($rows, $result['data']);
            }
        }
        return $rows;
    }


    /**
     * 对账，退款对账单
     * 根据订单渠道，读取渠道订单信息
     * 比较金额、订单号等是否正确
     * 如果没有、或金额不正确，报错，提醒
     * 对支付订单，用户订单，渠道订单
     * 用户订单中的取现订单不处理
     * 从拉过来的订单中，统计订单条数，以及总金额，在插入完成后，从数据库中再统计数量和金额，对比是否一致，不一致表示不成功
     */
    public function getHeepayCheckRefundBill($day)
    {
        if(!$day) {
            return false;
        }
        $d = date("Ymd", strtotime($day));
        $page = 1;
        $result = $this->getHeepayCheckRefundBillByPage($d, $d, $page);
        if(!$result || !is_array($result)) {
            return false;
        }
        $rows = array();
        $rows = $rows + $result['data'];
        if($result['total_page'] > $page) {
            for($i = 2; $i <= $result['total_page']; $i++) {
                $result = $this->getHeepayCheckRefundBillByPage($d, $d, $i);
                if(!$result || !is_array($result)) {
                    return false;
                }
                $rows = array_merge($rows, $result['data']);
            }
        }
        return $rows;
    }

    /**
     * 获取汇付宝的对账单，分页区
     * @param   string  start   开始时间
     * @param   string  end     结束时间
     * @return  array   order info
     */
    public function getHeepayCheckBillByPage($start, $end, $page)
    {
        if(!$start || !$end || $start > $end || $page <= 0) {
            return false;
        }
        $page_count = 500;
        $params = [
            "agent_id"   => $this->heepay_merchantid,
            "begin_time" => $start,
            "end_time"   => $end,
            "page_index" => $page,
            "page_size"  => $page_count,
            "pay_type"   => 0,
            "version"    => 2
        ];
        $sign     = $this->heepayEncrypt($params, $this->heepay_check_bill_key);
        $params['sign_type'] = "MD5";
        $params['sign'] = $sign;
        $http_ret = HttpRequest::get($this->heepay_check_bill_api, $params);
        $errmsg   = HttpRequest::getError();
        $httpinfo = HttpRequest::getInfo();
        if(!$http_ret) {
            return false;
        }

        $pattern_code  = "/<ret_code>([^<]*)<\/ret_code>/";
        $pattern_agent = "/<agent_id>(\d*)<\/agent_id>/";
        $pattern_data  = "/<detail_data>([^<]*)<\/detail_data>/";
        $pattern_page  = "/<total_page>([^<]*)<\/total_page>/";
        $pattern_count = "/<total_count>([^<]*)<\/total_count>/";

        $preg_ret_code = preg_match_all($pattern_code, $http_ret, $matches);
        if(!$preg_ret_code || !isset($matches[1][0]) || $matches[1][0] != "0000") {
            return false;
        }
        $preg_ret_agent = preg_match_all($pattern_agent, $http_ret, $matches);
        if(!$preg_ret_agent || !isset($matches[1][0]) || $matches[1][0] != $this->heepay_merchantid) {
            return false;
        }
        $preg_ret_page = preg_match_all($pattern_page, $http_ret, $matches);
        if(!$preg_ret_page || !isset($matches[1][0]) || strlen($matches[1][0]) == 0) {
            return false;
        }
        $total_page = intval($matches[1][0]);
        $preg_ret_count = preg_match_all($pattern_count, $http_ret, $matches);
        if(!$preg_ret_count || !isset($matches[1][0]) || strlen($matches[1][0]) == 0) {
            return false;
        }
        $total_count = intval($matches[1][0]);

        if($total_page == 0 && $total_count == 0) {
            $data_content = array();
        }else {
            $preg_ret_data = preg_match_all($pattern_data, $http_ret, $matches);
            if(!$preg_ret_data || !isset($matches[1][0])) {
                return false;
            }
            $detail_data = trim($matches[1][0]);
            $data_content = explode("\r\n", $detail_data);
        }

        $row = array();
        for($i = 0; $i < count($data_content); $i++) {
            $tmp = explode(",", $data_content[$i]);
if(substr($tmp[1], 0, 7) == "1228139") {        // 啪啪的订单
    continue;
}
            $new['paychannel_orderid'] = $tmp[0];
            $new['paycenter_orderid'] = $tmp[1];
            $new['paychannel_type'] = "heepay";
            $new['paycenter_ts'] = date("Y-m-d H:i:s", strtotime($tmp[2]));
            $new['paychannel_ts'] = date("Y-m-d H:i:s", strtotime($tmp[3]));
            $new['pay_rmb'] = $tmp[4];
            $new['subchannel'] = iconv("GB2312", "UTF-8", $tmp[5]);
            $new['paychannel_fee'] = is_numeric($tmp[6]) ? $tmp[6] : 0;
            $row[] = $new;
        }
        return ['data' => $row, 'total_page' => $total_page];
    }

    /**
     * 获取汇付宝的对账单，分页区
     * @param   string  start   开始时间
     * @param   string  end     结束时间
     * @return  array   order info
     */
    public function getHeepayCheckRefundBillByPage($start, $end, $page)
    {
        if(!$start || !$end || $start > $end || $page <= 0) {
            return false;
        }
        $page_count = 2000;
        $params = [
            "agent_id"   => $this->heepay_merchantid,
            "begin_time" => $start,
            "end_time"   => $end,
            "page_index" => $page,
            "page_size"  => $page_count,
            "version"    => 1
        ];
        $sign     = $this->heepayEncrypt($params, $this->heepay_check_refund_bill_key);
        $params['sign'] = $sign;
        $http_ret = HttpRequest::get($this->heepay_check_refund_bill_api, $params);
        if(!$http_ret) {
            return false;
        }

        $pattern_code  = "/<ret_code>([^<]*)<\/ret_code>/";
        $pattern_agent = "/<agent_id>(\d*)<\/agent_id>/";
        $pattern_data  = "/<detail_data>([^<]*)<\/detail_data>/";
        $pattern_page  = "/<total_page>([^<]*)<\/total_page>/";
        $pattern_count = "/<total_count>([^<]*)<\/total_count>/";

        $preg_ret_code = preg_match_all($pattern_code, $http_ret, $matches);
        if(!$preg_ret_code || !isset($matches[1][0]) || $matches[1][0] != "0000") {
            return false;
        }
        $preg_ret_agent = preg_match_all($pattern_agent, $http_ret, $matches);
        if(!$preg_ret_agent || !isset($matches[1][0]) || $matches[1][0] != $this->heepay_merchantid) {
            return false;
        }
        $preg_ret_page = preg_match_all($pattern_page, $http_ret, $matches);
        if(!$preg_ret_page || !isset($matches[1][0]) || strlen($matches[1][0]) == 0) {
            return false;
        }
        $total_page = intval($matches[1][0]);
        $preg_ret_count = preg_match_all($pattern_count, $http_ret, $matches);
        if(!$preg_ret_count || !isset($matches[1][0]) || strlen($matches[1][0]) == 0) {
            return false;
        }
        $total_count = intval($matches[1][0]);

        if($total_page == 0 && $total_count == 0) {
            $data_content = array();
        }else {
            $preg_ret_data = preg_match_all($pattern_data, $http_ret, $matches);
            if(!$preg_ret_data || !isset($matches[1][0])) {
                return false;
            }
            $detail_data = trim(trim($matches[1][0]), "|");
            $data_content = explode("|", $detail_data);
        }
        $row = array();
        for($i = 0; $i < count($data_content); $i++) {
            $tmp = explode(",", $data_content[$i]);

            $new['paycenter_orderid']  = $tmp[0];
            $new['paychannel_orderid'] = $tmp[1] . "#" . $tmp[3];
            $new['paychannel_type']    = "heepay";
            $new['paycenter_ts']       = date("Y-m-d H:i:s", strtotime($tmp[8]));
            $new['paychannel_ts']      = date("Y-m-d H:i:s", strtotime($tmp[9]));
            $new['pay_rmb']            = 0 - $tmp[5];
            $new['subchannel']         = iconv("GB2312", "UTF-8", $tmp[7]);
            $new['paychannel_fee']     = is_numeric($tmp[6]) ? $tmp[6] : 0;
            $new['paytype']            = 1;
            $row[] = $new;
        }
        return ['data' => $row, 'total_page' => $total_page];
    }

    /**
     * 汇付宝签名
     */
    private function heepayEncrypt($params, $appkey, &$request = null) {
        if (!$params || !is_array($params) || !$appkey) {
            return false;
        }
        $params['key'] = $appkey;
        ksort($params);

        $query1 = $request = array();
        foreach ($params as $key => $value) {
            if ($key == "sign") {
                continue;
            }
            array_push($query1, $key . "=" . $value);
            $request[$key] = $value;
        }
        $query_string = strtolower(join("&", $query1));
        $sign = md5($query_string);
        $request['sign'] = $sign;
        return $sign;
    }


    /**
     * 开始发起提现
     */
    public function cashStart($merchantid, $paypwd, $amount)
    {
        $min_amount = 1000;
        if($amount < $min_amount) {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashStart", "result" => "false", "log" => "amount is less than {$min_amount}", "merchantid" => $merchantid, "amount" => $amount), __METHOD__ . "[" . __LINE__ . "]");
			return "less";
        }

        $toBCheckBillDBModel = new ToBCheckBillDBModel;
        $balanceInfo = $toBCheckBillDBModel->getBalanceInfo($merchantid);
        if(!$balanceInfo || !is_array($balanceInfo)) {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashStart", "result" => "false", "log" => "get balance error", "merchantid" => $merchantid), __METHOD__ . "[" . __LINE__ . "]");
			return false;
        }

        /**
         * 判断提现密码
         */
        if(md5($paypwd) != $balanceInfo['pay_pwd']) {
			return "pwd_error";
        }

        /**
         * 判断余额是否足够
         */
        if($balanceInfo['net_income'] < $amount) {
			return "balance_not_enough";         // 余额不足
        }
        $dec = $toBCheckBillDBModel->decNetBalance($merchantid, $amount);
        if(!$dec) {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashStart", "result" => "false", "log" => "decNetBalance error", "merchantid" => $merchantid, "amount" => $amount), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
		UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashStart", "result" => "true", "log" => "cash", "merchantid" => $merchantid, "amount" => $amount), __METHOD__ . "[" . __LINE__ . "]");

        /**
         * 扣完后，检测一次余额是否为付
         */
        $balanceInfo2 = $toBCheckBillDBModel->getBalanceInfo($merchantid);
        if(!$balanceInfo2 || !is_array($balanceInfo2)) {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashStart", "result" => "false", "log" => "get balance2 error", "merchantid" => $merchantid), __METHOD__ . "[" . __LINE__ . "]");
			return false;
        }
        if($balanceInfo2['net_income'] < 0) {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "cashStart", "result" => "false", "log" => "check balance error", "merchantid" => $merchantid, "balanceInfo2" => $balanceInfo2), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return true;
    }

    /**
     * 汇付宝批付接口
     * @param   string  orderid     订单号
     * @param   float   money       总金额
     * @param   array   accounts    账户列表 []
     */
    public function payTransfer($orderid, $money, $accounts)
    {
		UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "log" => "payTransfer:start"), __METHOD__ . "[" . __LINE__ . "]");

        if(!$orderid || $money <= 0 || !$accounts || !is_array($accounts)) {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "params error", "orderinfo" => $orderinfo, "money" => $money), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        $this_env = Library::getCurrEnv();
        if($this_env == "product") {
            $notify_url = "http://callback.vronline.com/callBackCash";
        }else {
            $notify_url = "http://test3.xyzs.com/callBackCash";
        }
        $small_url = "https://Pay.heepay.com/API/PayTransit/PayTransferWithSmallAll.aspx";
        $large_url = "https://Pay.heepay.com/API/PayTransit/PayTransferWithLargeWork.aspx";

        if($money >= 50000) {       // 以单笔子订单区分
            $heepay_api_url = $small_url;
        }else {
            $heepay_api_url = $small_url;
        }

        $params = [];
        $params['version'] = 3;
        $params['agent_id'] = $this->heepay_merchantid;
        $params['batch_no'] = $orderid;
        $params['batch_amt'] = $money;
        $params['batch_num'] = count($accounts);

        $params['notify_url'] = $notify_url;
        $params['ext_param1'] = $orderid;
        $detail2 = [];
        for($i = 0; $i < count($accounts); $i++) {
            $tmp2 = [];

            // 子订单号
            if(!isset($accounts[$i]['orderid']) || !$accounts[$i]['orderid']) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "sub orderid error", "i" => $i, "accounts" => $accounts), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['orderid'];

            // 银行编号
            if(!isset($accounts[$i]['card_pay_no']) || !$accounts[$i]['card_pay_no']) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "bankno error", "bankno" => $bankno, "card_no" => $accounts[$i]['card_no']), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['card_pay_no'];

            // 收款账号类型，对公/对私
            if(isset($accounts[$i]['card_owner'])) {
                $tmp2[] = $accounts[$i]['card_owner'];
            }else {
                $tmp2[] = 0;
            }

            // 收款账号
            if(!isset($accounts[$i]['card_no']) || !$accounts[$i]['card_no']) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "card_no error", "i" => $i, "accounts" => $accounts), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['card_no'];

            // 收款人姓名
            if(!isset($accounts[$i]['card_name']) || !$accounts[$i]['card_name']) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "card_name error", "i" => $i, "accounts" => $accounts), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['card_name'];

            // 付费金额
            if(!isset($accounts[$i]['cash']) || $accounts[$i]['cash'] <= 0) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "cash error", "i" => $i, "accounts" => $accounts), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['cash'];

            // 付款理由
            $tmp2[] = "上游结算款";

            // 省份
            if(!isset($accounts[$i]['card_province']) || !$accounts[$i]['card_province']) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "card_province error", "i" => $i, "accounts" => $accounts), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['card_province'];

            // 城市
            if(!isset($accounts[$i]['card_city']) || !$accounts[$i]['card_city']) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "card_city error", "i" => $i, "accounts" => $accounts), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['card_city'];

            // 收款支行名称
            if(!isset($accounts[$i]['card_opener']) || !$accounts[$i]['card_opener']) {
			    UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "card_opener error", "i" => $i, "accounts" => $accounts), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
            $tmp2[] = $accounts[$i]['card_opener'];
            $detail2[] = implode("^", $tmp2);
        }
        $detail_data2 = implode("|", $detail2);
        $params['detail_data'] = $detail_data2;

        $sign     = $this->heepayEncrypt($params, $this->heepay_transfer_money_key);
        $params['detail_data'] = $this->encrypt3DES($this->heepay_cash_3DES_key, mb_convert_encoding($detail_data2, "GB2312", "UTF-8"));
        $params['sign_type'] = "MD5";
        $params['sign'] = $sign;
        $http_ret = HttpRequest::get($heepay_api_url, $params);
        $errmsg   = HttpRequest::getError();
        $httpinfo = HttpRequest::getInfo();
        if(!$http_ret) {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "http request error", "http_ret" => mb_convert_encoding($http_ret, "UTF-8", "GB2312"), "errmsg" => $errmsg, "httpinfo" => $httpinfo, "url" => $heepay_api_url, "params" => $params), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        $pattern_code  = "/<ret_code>([^<]*)<\/ret_code>/";
        $preg_ret_code = preg_match_all($pattern_code, $http_ret, $matches);
        if(!$preg_ret_code || !isset($matches[1][0]) || $matches[1][0] != "0000") {
			UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "false", "log" => "http request error2", "http_ret" => mb_convert_encoding($http_ret, "UTF-8", "GB2312"), "errmsg" => $errmsg, "httpinfo" => $httpinfo, "url" => $heepay_api_url, "params" => $params), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

		UdpLog::save2("pay.vronline.com/pay2b", array("function" => "payTransfer", "result" => "success", "log" => "success", "orderid" => $orderid, "money" => $money), __METHOD__ . "[" . __LINE__ . "]");
        return true;
    }

    public function encrypt3DES($key, $input)
    {
        if (empty($input)){
            return null;
        }
        $size = mcrypt_get_block_size ( MCRYPT_3DES, 'ecb' );
        $pad = $size - (strlen ( $input ) % $size);
        $input = $input . str_repeat ( chr ( $pad ), $pad );
        $key = str_pad ( $key, 24, '0' );
        $td = mcrypt_module_open ( MCRYPT_3DES, '', 'ecb', '' );
        $iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
        @mcrypt_generic_init ( $td, $key, $iv );
        $data = mcrypt_generic ( $td, $input );
        mcrypt_generic_deinit ( $td );
        mcrypt_module_close ( $td );
//        $data = base64_encode ( $data );
        $data = strtoupper(bin2hex ( $data ));
        return $data;
    }

    public function getAgentid()
    {
        return $this->heepay_merchantid;
    }
}