<?php
/*
 * 线下体验店
 * date:2017/1/10
 */

namespace App\Models;

use App\Helper\Vmemcached;
use App\Models\ImModel;
use App\Models\WebgameModel;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;
use Mail;

class ToBStoreModel extends Model
{

    /**
     * 获取终端状态
     * @param   array  uids 终端ids
     */
    public function genTerminalStat($arr)
    {

    }

    /**
     * 生成设备号
     * @param   string  merchantid  商户ID
     * @return  array   info    数组，游戏信息
     */
    public function genTerminalSn($merchantid, $mac)
    {
        if (!$merchantid || !$mac) {
            return false;
        }
        $private_key = 'lk203,.*+-9f87893)*98hflk;lj!@#%^&';
        $ts          = microtime(true);
        $rand        = rand(10000, 99999);
        $code        = strtoupper(md5($merchantid . $private_key . $ts . $rand . $mac));
        return $code;
    }

    /**
     * 添加主机
     * @param   string  merchantid   商户ID
     * @param   array   info    数组，设备信息 ["terminal_ip"=>xxx, "terminal_mac"=>xxx, "serverip"=>xxx, "serverport"=>xxx]
     * @return  array   设备信息 ["terminal_sn"=>xxx, "terminal_id"=>xxx, "merchantid"=>xxx]
     */
    public function add2bMerchant($merchantid, $info)
    {
        if (!$merchantid || !$info || !is_array($info) || !isset($info['terminal_ip']) || !$info['terminal_ip'] || !isset($info['terminal_mac']) || !$info['terminal_mac']) {
            return false;
        }
        $toBDBModel = new ToBDBModel;

        $minfo = $toBDBModel->get2bMerchant($merchantid);
        if ($minfo === false) {
            return false;
        }

        /**
         * 主机存在
         */
        if ($minfo && is_array($minfo)) {
            return ["merchantid" => $minfo['merchantid'], "terminal_sn" => $minfo['terminal_sn'], "terminal_id" => $minfo['terminal_id']];
        }
        $tinfo    = array("terminal_ip" => $info['terminal_ip'], "terminal_mac" => $info['terminal_mac']);
        $terminal = $this->add2bTerminal($merchantid, $tinfo);
        if (!$terminal || !is_array($terminal)) {
            return false;
        }
        $minfo                                                                          = array();
        $minfo['terminal_sn']                                                           = $terminal['terminal_sn'];
        $minfo['terminal_id']                                                           = $terminal['terminal_id'];
        isset($info['terminal_mac']) && $info['terminal_mac'] && $minfo['terminal_mac'] = $info['terminal_mac'];
        isset($info['serverip']) && $info['serverip'] && $minfo['serverip']             = $info['serverip'];
        isset($info['serverport']) && $info['serverport'] && $minfo['serverport']       = $info['serverport'];
        $ret                                                                            = $toBDBModel->add2bMerchant($merchantid, $minfo);
        if ($ret) {
            return ["merchantid" => $merchantid, "terminal_sn" => $terminal['terminal_sn'], "terminal_id" => $terminal['terminal_id']];
        } else {
            return false;
        }
    }

    /**
     * 添加设备，自动生成设备号
     * @param   string  merchantid   商户ID
     * @param   array   info    数组，设备信息 ["terminal_ip"=>xxx, "terminal_mac"=>xxx]
     * @return  array   设备信息 ["terminal_sn"=>xxx, "terminal_id"=>xxx, "terminal_no"=>xxx, "merchantid"=>xxx]
     */
    public function add2bTerminal($merchantid, $info, $isMaster = false)
    {
        if (!$merchantid || !$info || !is_array($info) || !isset($info['terminal_mac']) || !$info['terminal_mac']) {
            return false;
        }

        $toBDBModel = new ToBDBModel;

        /**
         * 判断该设备号是否存在，如果存在，返回该设备号信息
         */
        $tinfo = $toBDBModel->get2bTerminalByMac($merchantid, $info['terminal_mac']);

        if ($tinfo) {
            if ($isMaster) {
                $toBDBModel->updateMerchant($merchantid, ['terminal_id' => $tinfo['terminal_id'], "terminal_sn" => $tinfo['terminal_sn']]);
            }
            return ["merchantid" => $merchantid, "terminal_sn" => $tinfo['terminal_sn'], "terminal_id" => $tinfo['terminal_id'], "terminal_no" => $tinfo['terminal_no']];
        }

        $terminal_sn = $this->genTerminalSn($merchantid, $info['terminal_mac']);
        if (!$terminal_sn) {
            return false;
        }

        /**
         * 如果设备号不存在，生成一个，并注册一个账号，拿到id
         */
        $accountModel = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $uinfo        = $accountModel->addNoLoginUser($terminal_sn);

        if (!$uinfo || !is_array($uinfo) || !isset($uinfo['code']) || $uinfo['code'] != 0 || !isset($uinfo['data']['uid']) || !$uinfo['data']['uid']) {
            return false;
        }
        $maxNo               = $toBDBModel->genMax2bTerminalNo($merchantid);
        $info['terminal_id'] = $uinfo['data']['uid'];
        $info['terminal_no'] = $maxNo + 1;
        $ret                 = $toBDBModel->add2bTerminal($merchantid, $terminal_sn, $info);

        if ($ret) {
            if ($isMaster) {
                $toBDBModel->updateMerchant($merchantid, ['terminal_id' => $info['terminal_id'], "terminal_sn" => $terminal_sn]);
            }
            $defaultProduct = $toBDBModel->getDefaultProduct();
            if (!empty($defaultProduct)) {
                $pInfo = ['terminal_sn' => $terminal_sn, 'price' => $defaultProduct[0], 'type' => 'time', 'playtime' => $defaultProduct[1], 'title' => $defaultProduct[2]];
                #初始化5个套餐
                $toBDBModel->add2bSell($merchantid, 'time', $pInfo, true);
            }
            return ["merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "terminal_id" => $info['terminal_id'], "terminal_no" => $info['terminal_no']];
        }
        return false;
    }

    /**
     * 修改设备状态，用于付费后调用
     * 先插入订单，发货成功后修改状态
     * @param   string  merchantid   商户ID
     * @param   string  terminal_sn  设备号
     * @param   array   info    数组，设备信息 ["type"=>"time", "appid"=>123, "playtime"=>600, "orderid"=>xxx, "paychannel_orderid"=>xxx, "payer_id"=>xxx]
     * @return  string  mix     true:成功; false:失败; startfailed:启动失败; duplicate:订单重复;
     */
    public function payFor2bTerminal($merchantid, $terminal_sn, $orderid, $request)
    {
        if (!$merchantid || !$terminal_sn || !$orderid) {
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "params error", "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        $nowstamp = time();
        $paytype  = $request->input("paytype", 0);
        $type     = $request->input("type", "time");
        $start    = $request->input("start", "");
        if ($type == "time" && $start < $nowstamp - 3600) {
            // 开始时间是1小时以前的，不处理
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "one hour ago", "terminal_sn" => $terminal_sn, "start" => $start), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        /**
         * 要插入的订单信息
         */
        $addOinfo['orderid']            = $orderid;
        $addOinfo['merchantid']         = $merchantid;
        $addOinfo['terminal_sn']        = $terminal_sn;
        $addOinfo['total_rmb']          = $request->input("total_rmb");
        $addOinfo['pay_rmb']            = $request->input("pay_rmb");
        $addOinfo['cp_fee']             = $request->input("cp_fee");
        $addOinfo['plat_fee']           = $request->input("plat_fee");
        $addOinfo['merchant_fee']       = $request->input("merchant_fee");
        $addOinfo['pay_channel']        = $request->input("pay_channel", "");
        $addOinfo['coupon_code']        = $request->input("coupon_code", "");
        $addOinfo['coupon_amount']      = $request->input("coupon_amount", 0);
        $addOinfo['payer_id']           = $request->input("payer_id", "");
        $addOinfo['paycenter_orderid']  = $request->input("paycenter_orderid", "");
        $addOinfo['paychannel_orderid'] = $request->input("paychannel_orderid", "");
        $addOinfo['type']               = $type;
        $addOinfo['paytype']            = $paytype;
        $addOinfo['refund_pay_orderid'] = $request->input("refund_pay_orderid", "");
        $addOinfo['sellid']             = $request->input("sellid");
        $addOinfo['appid']              = $request->input("appid", 0);
        $addOinfo['appname']            = $request->input("appname", "");
        $addOinfo['start']              = $start;
        $addOinfo['paytime']            = date("Y-m-d H:i:s");

        $imModel    = new ImModel;
        $toBDBModel = new ToBDBModel;

        $old = $toBDBModel->get2bTerminal($merchantid, $terminal_sn);
        if (!$old || !is_array($old)) {
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "terminal info error", "terminal_sn" => $terminal_sn, "old" => $old), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        $addOinfo['terminal_no'] = $old['terminal_no'];

        /**
         * 先查订单是否存在
         */
        $oinfo = $toBDBModel->get2bBillByOrderid($orderid);

        /**
         * 如果订单存在，并且是通知成功的
         * 判断下开始时间，如果超过了10分钟，不处理，交给店长在后台处理
         * 如果在10分钟内，判断该终端当前的游戏状态，是这笔订单的，启动游戏，不是这笔订单，不处理
         */
        if ($oinfo && is_array($oinfo) && isset($oinfo['status']) && $oinfo['status'] == 8) {
            if ($nowstamp - $oinfo['start'] >= 10 * 60) {
                UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false/true", "log" => "order exists and interval large than 5 minutes", "orderid" => $orderid, "oinfo" => $oinfo), __METHOD__ . "[" . __LINE__ . "]");
                return "duplicate";
            }
            if (!isset($old['orderid']) || $old['orderid'] != $orderid) {
                UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "orderid error", "orderid" => $orderid, "terminal_sn" => $terminal_sn, "old" => $old), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }

            /**
             * 通知终端
             */
            $terminal_id = $old['terminal_id'];
            $receiver    = [$terminal_id];
            $left        = $old['playtime'] - ($nowstamp - $old['start']);
            if ($left <= 0) {
                $left = 0;
            }
            $cont = ["name" => "buy", "data" => ["tp" => "time", "appid" => $old['appid'], "val" => $old['playtime'], "start" => true, "now" => $nowstamp, "left" => $left, "flag" => 1]];
            $msg  = Library::base64Urlsafeencode(json_encode($cont));
            $send = $imModel->sysNotify2b($terminal_id, $receiver, $msg);
            if (!$send) {
                $send = $imModel->sysNotify2b($terminal_id, $receiver, $msg);
            }
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false/true", "log" => "repeat ok", "orderid" => $orderid, "oinfo" => $oinfo), __METHOD__ . "[" . __LINE__ . "]");
            return "duplicate";
        }

        /**
         * 未创建订单，或者订单发货失败，开始补发
         * 判断有没有该套餐
         *
         */
        $sellinfo = $toBDBModel->get2bSellById($addOinfo['sellid']);
        if (!$sellinfo) {
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "sell info error", "orderid" => $orderid, "sellid" => $addOinfo['sellid']), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($addOinfo['total_rmb'] != $sellinfo['price'] || $merchantid != $sellinfo['merchantid'] || ($sellinfo['terminal_sn'] && $terminal_sn != $sellinfo['terminal_sn']) || $addOinfo['type'] != $sellinfo['type']) {
            // 请求的订单信息错误
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "sell info error2", "orderid" => $orderid, "sellinfo" => $sellinfo), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($addOinfo['total_rmb'] != $addOinfo['pay_rmb'] + $addOinfo['coupon_amount']) {
            // 价格不对
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "total_rmb != pay_rmb + coupon_amount", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        /**
         * 如果订单不存在，创建订单，状态为未发货
         * 先修改终端设备状态，成功后修改订单状态，如果失败，等待补单，补单由用户手动发起
         * 避免刷订单，订单添加就直接是发货成功状态
         * 如果有发货失败，由店长在后台手动补发通知
         */
        if (!$oinfo) {
            $addOinfo['playtime'] = $sellinfo['playtime'];
            $addOinfo['status']   = 8;
            $ret                  = $toBDBModel->add2bBill($orderid, $addOinfo);
            if ($ret === false) {
                UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "add bill fail", "orderid" => $orderid), __METHOD__ . "[" . __LINE__ . "]");
                return false;
            }
        }

        /**
         * 如果设备状态里的订单号与请求里发来的订单号相同，不修改设备状态，只通知终端，并且是在正常游戏的状态
         * 如果是按时间付费
         * 判断下这台设备现有的状态，及游戏时间
         * 如果现有是按时间的，并且时间还没到，在现有基础上延长时间
         * 否则从购买时间开始计
         * 不是按时间付费，时间都置0
         */
        $updTinfo = [];

        /**
         * 是否启动终端
         */
        $isStartTerminal = false;

        /**
         * 设备状态里的订单号与请求的订单号不同时，才有机会更新设备状态
         */
        $notify_appid = $notify_start = $notify_playtime = "";
        if ($old['orderid'] != $orderid) {
            if ($type == "time") {
                if ($old['type'] == "time") {
                    if ($old['start'] + $old['playtime'] > $nowstamp) {
                        $updTinfo['orderid']            = $orderid;
                        $updTinfo['paychannel_orderid'] = $addOinfo['paychannel_orderid'];
                        $updTinfo['type']               = $type;
                        $updTinfo['playtime']           = $old['playtime'] + $sellinfo['playtime'];
                        $updTinfo['appid']              = $addOinfo['appid'];
                    } else {
                        $updTinfo['orderid']            = $orderid;
                        $updTinfo['paychannel_orderid'] = $addOinfo['paychannel_orderid'];
                        $updTinfo['type']               = $type;
                        $updTinfo['start']              = $addOinfo['start'];
                        $updTinfo['playtime']           = $sellinfo['playtime'];
                        $updTinfo['appid']              = $addOinfo['appid'];
                    }
                } else {
                    $updTinfo['orderid']            = $orderid;
                    $updTinfo['paychannel_orderid'] = $addOinfo['paychannel_orderid'];
                    $updTinfo['type']               = $type;
                    $updTinfo['start']              = $addOinfo['start'];
                    $updTinfo['playtime']           = $sellinfo['playtime'];
                    $updTinfo['appid']              = $addOinfo['appid'];
                }
            } else {
                $updTinfo['orderid']            = $orderid;
                $updTinfo['paychannel_orderid'] = $addOinfo['paychannel_orderid'];
                $updTinfo['start']              = $updTinfo['playtime']              = 0;
                $updTinfo['appid']              = $addOinfo['appid'];
                $updTinfo['type']               = $addOinfo['type'];
            }
            $ret = $toBDBModel->upd2bTerminal($merchantid, $terminal_sn, $updTinfo);
            if (!$ret) {
                UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "update terminal info fail", "merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "updTinfo" => $updTinfo), __METHOD__ . "[" . __LINE__ . "]");
                return "startfailed";
            }
        }

        $terminal_id = $old['terminal_id'];
        $receiver    = [$terminal_id];

        $notify_appid    = isset($updTinfo['appid']) ? $updTinfo['appid'] : $old['appid'];
        $notify_start    = isset($updTinfo['start']) ? $updTinfo['start'] : $old['start'];
        $notify_playtime = isset($updTinfo['playtime']) ? $updTinfo['playtime'] : $old['playtime'];
        $left            = $notify_playtime - ($nowstamp - $notify_start);
        if ($left <= 0) {
            $left = 0;
        }
        $cont = ["name" => "buy", "data" => ["tp" => "time", "appid" => $notify_appid, "val" => $notify_playtime, "start" => true, "now" => $nowstamp, "left" => $left, "flag" => 2]];
        $msg  = Library::base64Urlsafeencode(json_encode($cont));
        $send = $imModel->sysNotify2b($terminal_id, $receiver, $msg);
        if (!$send) {
            $send = $imModel->sysNotify2b($terminal_id, $receiver, $msg);
        }
        if (!$send) {
            UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "false", "log" => "notify failed", "orderid" => $orderid, "terminal_id" => $terminal_id), __METHOD__ . "[" . __LINE__ . "]");
            return "startfailed";
        }
        UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "success", "orderid" => $orderid, "terminal_id" => $terminal_id), __METHOD__ . "[" . __LINE__ . "]");
        return true;
    }

    /**
     * 添加价格
     * @param   string  merchantid  商户ID
     * @param   string  type  类型，time:按时长，或game:按游戏
     * @param   array   info  价格信息, ["appid" => xxx, "price"=>xxx, "playtime"=>xxx, ......]  playtime 单位:分
     * @return  -1 参数错误; -2 已经存在了; -3 失败; 0 成功;
     */
    public function addSell($merchantid, $type, $info)
    {
        if (!$merchantid || !$type) {
            return -1;
        }
        if ($type == "time") {
            if (!isset($info['playtime']) || $info['playtime'] < 1) {
                return -1;
            }
            $info['appid']    = 0;
            $info['playtime'] = $info['playtime'] * 60;
        }
        if ($type == "game") {
            if (!isset($info['appid']) || !$info['appid']) {
                return -1;
            }
            $info['playtime'] = 0;
        }
        $toBDBModel = new ToBDBModel;
        $clause     = array("appid" => $info['appid'], "playtime" => $info['playtime']);
        $sells      = $toBDBModel->get2bSell($merchantid, $type, $clause);
        if ($sells) {
            return -2;
        }
        $ret = $toBDBModel->add2bSell($merchantid, $type, $info);
        if ($ret) {
            return 0;
        } else {
            return -3;
        }
    }

    /**
     * 设置价格，有就修改，没有就添加
     * 如果是按游戏收费，默认的收费是 type=game & appid=0
     * @param   string  merchantid  商户ID
     * @param   string  type  类型，time:按时长，或game:按游戏
     * @param   array   info  价格信息, ["appid" => xxx, "price"=>xxx, "playtime"=>xxx, ......]  playtime 单位:分
     * @return  bool
     */
    public function setSell($merchantid, $type, $info)
    {
        if (!$merchantid || !$type) {
            return false;
        }
        if ($type == "time") {
            if (!isset($info['playtime']) || $info['playtime'] < 1) {
                return false;
            }
            $info['appid']    = 0;
            $info['playtime'] = $info['playtime'] * 60;
            $clause           = array("playtime" => $info['playtime']);
        }
        if ($type == "game") {
            $info['playtime'] = 0;
            $clause           = array("appid" => $info['appid']);
        }
        $toBDBModel = new ToBDBModel;
        $sell       = $toBDBModel->get2bSell($merchantid, $type, $clause);
        if ($sell) {
            $ret = $toBDBModel->upd2bSell($merchantid, $type, $info);
        } else {
            $ret = $toBDBModel->add2bSell($merchantid, $type, $info);
        }
        return $ret;
    }

    /**
     * 设置激活码的缓存
     * @param $uid
     * @return bool
     */
    public function setActiveEmailCode($uid)
    {
        if (!$uid) {
            return false;
        }
        $code = md5(md5($uid) . time() . mt_rand(1111, 9999));
        $ret  = Vmemcached::set("merchant_active_code", $uid, $code);
        return $ret;
    }

    /**
     * 获取邮件链接中的激活code是否有效
     * @param $uid
     * @return bool
     */
    public function getActiveEmailCode($uid)
    {
        if (!$uid) {
            return false;
        }
        return Vmemcached::get("merchant_active_code", $uid);
    }

    /**
     * 删除激活码缓存
     * @param $uid
     * @return bool
     */
    public function delActiveEmailCode($uid)
    {
        if (!$uid) {
            return false;
        }
        return Vmemcached::delete("merchant_active_code", $uid);
    }

    /**
     * 发送邮件方法
     * @param $email
     * @param $name
     * @param $msgDataArr
     */
    public function sendVerifyMail($email, $name, $msgDataArr)
    {
        $data = ['email' => $email, 'name' => $name, 'uid' => $msgDataArr['uid'], 'title' => $msgDataArr['title'], 'activeCode' => $msgDataArr['activeCode']];
        Mail::queue('tob.activeEmail', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject($data['title']);
        });
        return true;
    }

    /**
     * 统计启动次数
     * 定时跑脚本
     * @return  bool
     */
    public function statPlayTimes2DB()
    {
        $toBDBModel = new ToBDBModel;

        /**
         * 先统计店铺的游戏启动次数
         */
        $merchantids = $toBDBModel->getMerchatids();
        if($merchantids && is_array($merchantids)) {
            for($j = 0; $j < count($merchantids); $j++) {
                $merchantid = $merchantids[$j]['merchantid'];
                $rows = $toBDBModel->getStatMerchantAppStart($merchantid);
                if($rows && is_array($rows)) {
                    foreach($rows as $appid => $num) {
                        if(!$num || $num <= 0) {
                            continue;
                        }
                        $info = ['play' => $num];
                        $r = $toBDBModel->upd2bGame($merchantid, "master", $appid, $info);
                    }
                }
            }
        }

        /**
         * 再统计所有店铺的游戏启动次数
         */
        $rows = $toBDBModel->getStat2BAppStart();
        if($rows && is_array($rows)) {
            $webgameModel = new WebgameModel;
            foreach($rows as $appid => $num) {
                if(!$num || $num <= 0) {
                    continue;
                }
                $info = ['tob_play' => $num];
                $s = $webgameModel->updGameInfo($appid, $info);
            }
        }
        return true;
    }

}
