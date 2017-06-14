<?php

// 用户信息中心

namespace App\Http\Controllers;

use App\Helper\Vredis;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Http\Request;
use \App\Models\ImModel;
use \App\Models\SolrModel;
use \App\Models\ToBDBModel;
use \App\Models\ToBStoreModel;

class ToBStoreController extends Controller
{

    /**
     * 展示终端游戏游戏
     * @param   int  merchantid  分类
     * @param   string  terminal_sn 支持设备
     * @return  view
     */
    public function index(Request $request, $merchantid, $terminal_sn)
    {
        $toBDBModel   = new ToBDBModel;
        $terminalInfo = $toBDBModel->get2bTerminal($merchantid, $terminal_sn);
        if (!$terminalInfo) {
            return redirect('/', 302, [], true);
        }

        $storeInfo = $toBDBModel->get2bMerchant($merchantid);
        if (!$storeInfo) {
            return redirect('/', 302, [], true);
        }

        $solrModel = new SolrModel;
        $params    = ['merchantid' => $merchantid];
        if ($storeInfo['terminal_id'] == $terminalInfo['terminal_id']) {
            $params['terminal_sn'] = 'master';
        } else {
            $params['terminal_sn'] = $terminal_sn;
        }

        $games = $solrModel->search('tob', $params);
        if (!is_array($games) || empty($games)) {
            $games = [];
        }
        $toBDBModel = new ToBDBModel;
        $products   = $toBDBModel->get2bSell($merchantid, 'time', ['terminal_sn' => $terminal_sn]);
        //var_dump($products);exit;
        $productPHtml = [];
        $productLHtml = '';
        foreach ($products as $id => $value) {
            $productPHtml[] = '<p class="price" data-val="' . $value['id'] . '">' . number_format($value['price'], 2) . "元/" . Library::handlePlayTime($value["playtime"]) . '</p>';
            $productLHtml .= '<li class="fl" data-val="' . $value['id'] . '"><span>' . number_format($value['price'], 2) . "元体验" . Library::handlePlayTime($value["playtime"]) . '</span></li>';
        }

        $terminal_no = $terminalInfo['terminal_no'];

        return view('tob.index', ['games' => $games, 'merchantid' => $merchantid, 'terminal_sn' => $terminal_sn, 'terminal_no' => $terminal_no, 'productPHtml' => implode('', $productPHtml), 'productLHtml' => $productLHtml]);
    }

    /**
     * 添加游戏游玩次数
     * @param   int  merchantid  分类
     * @param   int  appid 游戏id
     * @return  bool
     */
    public function addPlay(Request $request, $merchantid, $appid)
    {
        $tobModel = new ToBDBModel;
        $ret      = $tobModel->addPlay($merchantid, $appid);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function rank(Request $request, $merchantid)
    {
        $tp        = $request->input('type');
        $solrModel = new SolrModel;
        $params    = ['merchantid' => $merchantid, 'terminal_sn' => 'master'];
        $games     = $solrModel->search('tob', $params);
        return $games;
    }

    /**
     * 搜索终端游戏
     * @param   int  merchantid  分类
     * @param   string  terminal_sn 支持设备
     * @return  json
     */
    public function search(Request $request, $merchantid, $terminal_sn)
    {
        $spells   = strtolower($request->input('spells'));
        $spellLen = strlen($spells);

        $hotType = $request->input('hot', false);

        $toBDBModel = new ToBDBModel;
        $storeInfo  = $toBDBModel->get2bMerchant($merchantid);
        if (!$storeInfo) {
            return rLibrary::output(1);
        }

        $terminalInfo = $toBDBModel->get2bTerminal($merchantid, $terminal_sn);
        if (!$terminalInfo) {
            return rLibrary::output(1);
        }

        $solrModel = new SolrModel;

        $searchTp = "tob";
        switch ($hotType) {
            case 'hot':
                $searchTp          = "vrgame";
                $params['orderBy'] = 'tob_play desc';
                break;
            case 'rank':
                $params["merchantid"]  = $merchantid;
                $params['terminal_sn'] = 'master';
                $params['orderBy']     = 'tob_play desc';
                break;
            default:
                $params = ['merchantid' => $merchantid];
                if ($storeInfo['terminal_id'] == $terminalInfo['terminal_id']) {
                    $params['terminal_sn'] = 'master';
                } else {
                    $params['terminal_sn'] = $terminal_sn;
                }
                $params['orderBy'] = 'tob_play desc';
                break;
        }

        if ($spellLen > 0) {
            for ($i = 0; $i < strlen($spells); $i++) {
                $spellArr[$i] = $spells{$i};
            }
            $params['spell'] = implode(' ', $spellArr);
        }

        $games = $solrModel->search($searchTp, $params);
        if (!is_array($games) || empty($games)) {
            $games = [];
        }
        return Library::output(0, $games);
    }

    /**
     * 查看终端所有商品
     * @param   int  merchantid  分类
     * @param   string  terminal_sn 支持设备
     * @param   int  appid 游戏id
     * @return  json
     */
    public function products(Request $request, $merchantid, $terminal_sn, $appid)
    {
        Library::accessHeader();
        $toBDBModel   = new ToBDBModel;
        $terminalInfo = $toBDBModel->get2bTerminal($merchantid, $terminal_sn);
        $merchantInfo = $toBDBModel->get2bMerchant($merchantid);
        $products     = $toBDBModel->get2bSell($merchantid, 'time', ['terminal_sn' => $terminal_sn]);
        $productArr   = [];
        foreach ($products as $i => $product) {
            $productArr[$i] = ['label' => number_format($product['price'], 2) . "元体验" . Library::handlePlayTime($product["playtime"]), 'price' => floatval($product['price']), 'value' => strval($i), 'id' => $product['id']];
        }
        $title = $merchantInfo['merchant'];
        $out   = [
            'title'       => $title,
            'terminal_no' => $terminalInfo['terminal_no'],
            'value'       => "0",
            'product'     => $productArr,
            'actionUrl'   => '//dev3.pay.xy.com/index.php?resource_id=1300160&action=',
            'orderUrl'    => '//test3.vronline.com',
        ];
        $env = Library::getCurrEnv();
        if ($env == "product" || $env == "preonline") {
            $out['actionUrl'] = '//pay3.xy.com/index.php?resource_id=1300160&action=';
            $out['orderUrl']  = 'https://pay.vronline.com';
        }
        return Library::output(0, $out);
    }

    /**
     * 查看终端单个商品信息
     * @param   int  merchantid  分类
     * @param   string  terminal_sn 支持设备
     * @param   int  productid 商品id
     * @return  json
     */
    public function productInfo(Request $request, $merchantid, $terminal_sn, $productid)
    {
        Library::accessHeader();
        $toBDBModel   = new ToBDBModel;
        $merchantInfo = $toBDBModel->get2bMerchant($merchantid);
        $products     = $toBDBModel->get2bSell($merchantid, 'time');
        foreach ($products as $product) {
            if ($productid == strval($product['id'])) {
                $productDesc = $product['title'];
                break;
            }
        }
        $title = $merchantInfo['merchant'];
        $out   = [
            'title' => $title,
            'desc'  => $productDesc,
        ];
        return Library::output(0, $out);
    }

    /**
     * 更换终端信息
     * @param   int  merchantid  分类
     * @param   string  terminal_sn 支持设备
     * @param   int  productid 商品id
     * @return  json
     */
    public function swapTerminalInfo(Request $request, $merchantid, $terminal_sn, $appid)
    {
        Library::accessHeader();
        $orderids = $request->input('orderids');
        if (!is_array($orderids)) {
            return Library::output(1);
        }

        $toBDBModel   = new ToBDBModel;
        $merchantInfo = $toBDBModel->get2bMerchant($merchantid);
        $terminalInfo = $toBDBModel->get2bTerminal($merchantid, $terminal_sn);

        $orders       = $toBDBModel->get2bTerminalByOrderIds($merchantid, $orderids);
        $op           = [];
        $defaultValue = '';
        if (!empty($orders)) {
            $now = time();
            foreach ($orders as $index => $order) {
                if (($order['start'] + $order['playtime'] > $now) && ($order['terminal_sn'] != $terminal_sn)) {
                    $retime = intval(($order['start'] + $order['playtime'] - $now) / 60);
                    $retime = $retime < 0 ? 0 : $retime;
                    $op[]   = ['label' => $order['terminal_no'] . "号机 剩余" . $retime . "分钟", 'value' => $order['orderid']];
                    if (!$defaultValue) {
                        $defaultValue = $order['orderid'];
                    }
                }
            }
        }
        $out = ['title' => $merchantInfo['merchant'], 'terminal_no' => $terminalInfo['terminal_no'], 'options' => $op, 'value' => $defaultValue];
        return Library::output(0, $out);
    }

    /**
     * 更换终端
     * @param   int  merchantid  分类
     * @param   string  terminal_sn 支持设备
     * @param   int  productid 商品id
     * @return  json
     */
    public function swapTerminal(Request $request, $merchantid, $terminal_sn, $appid, $orderId)
    {
        Library::accessHeader();
        $toBDBModel = new ToBDBModel;
        $orders     = $toBDBModel->get2bTerminalByOrderIds($merchantid, [$orderId]);
        if (!$orders) {
            return Library::output(1);
        }
        $order = $orders[0];
        if ($order['merchantid'] != $merchantid) {
            return Library::output(1);
        }
        if ($order['terminal_sn'] == $terminal_sn) {
            return Library::output(1);
        }
        $now = time();
        if ($order['start'] + $order['playtime'] < $now) {
            return Library::output(1);
        }
        $info = ['orderid' => $orderId, 'start' => $order['start'], 'playtime' => $order['playtime'], 'type' => $order['type'], 'paychannel_orderid' => $order['paychannel_orderid']];
        $ret  = $toBDBModel->swapTerminalSnByOrderId($merchantid, $order['terminal_sn'], $terminal_sn, $info);
        if ($ret) {
            return Library::output(0);
        }
    }

    /**
     * 终端激活
     * @param   int  merchantid  商户id
     * @param   string  mac 终端mac 地址
     * @param   int  is_master 是否为主机
     * @return  json
     */
    public function terminalActive(Request $request)
    {
        $merchantid = $request->input('merchantid');
        $mac        = $request->input('mac');
        $isMaster   = $request->input('is_master', 0);
        $toBDBModel = new ToBStoreModel;
        $info       = ['terminal_mac' => $mac];
        $minfo      = $toBDBModel->add2bTerminal($merchantid, $info, (bool) $isMaster);
        if (!$minfo) {
            return Library::output(1);
        } else {
            return Library::output(0, $minfo);
        }
    }

    /**
     * 获得IM的token
     * @param   int  merchantid  商户id
     * @param   string  terminal_sn 终端编号
     * @param   int  terminal_id 终端id
     * @param   int  terminal_mac 终端mac
     * @param   int  ts 时间戳
     * @param   string  sign 签名
     * @return  json
     */
    public function getImToken2b(Request $request)
    {
        $merchantid   = $request->input("merchantid", 0);
        $terminal_sn  = $request->input("terminal_sn", "");
        $terminal_id  = $request->input("terminal_id", 0);
        $terminal_mac = $request->input("terminal_mac", "");
        $ts           = $request->input("ts", "");
        $sign         = $request->input("sign", "");
        if (!$merchantid || !$terminal_sn || !$terminal_id || !$terminal_mac || !$ts || !$sign) {
            return Library::output(2001);
        }

        $clientkey = Config::get("common.vr_2bclient_key");
        $check     = Library::encrypt($_POST, $clientkey);
        if ($check != $sign) {
            return Library::output(2002);
        }

        $toBDBModel = new ToBDBModel;
        $tinfo      = $toBDBModel->get2bTerminal($merchantid, $terminal_sn);
        if (!$tinfo || !isset($tinfo['merchantid']) || !isset($tinfo['terminal_sn']) || !isset($tinfo['terminal_id'])) {
            return Library::output(1);
        }
        if ($tinfo['merchantid'] != $merchantid || $tinfo['terminal_sn'] != $terminal_sn || $tinfo['terminal_id'] != $terminal_id) {
            return Library::output(2002);
        }

        $imModel = new ImModel;
        $info    = $imModel->getImToken($terminal_id);
        if (!$info || !isset($info['secret']) || !$info['secret']) {
            return Library::output(1);
        }
        return Library::output(0, ["imtoken" => $info['secret']]);
    }

    /**
     * 付款会发货
     */
    public function payFor2bTerminal(Request $request)
    {

        UdpLog::save2("pay.vronline.com/payFor2bTerminal", array("function" => "payFor2bTerminal", "result" => "start"), __METHOD__ . "[" . __LINE__ . "]");
        $merchantid    = $request->input("merchantid", 0);
        $terminal_sn   = $request->input("terminal_sn", "");
        $orderid       = $request->input("orderid", "");
        $paytype       = $request->input("paytype", 0);
        $action        = $request->input("action", "");
        $sign          = $request->input("sign", "");
        $total_rmb     = $request->input("total_rmb", 0);
        $pay_rmb       = $request->input("pay_rmb", 0);
        $cp_fee        = $request->input("cp_fee", 0);
        $plat_fee      = $request->input("plat_fee", 0);
        $merchant_fee  = $request->input("merchant_fee", 0);
        $coupon_amount = $request->input("coupon_amount", 0);
        $sellid        = $request->input("sellid");

        if (!$merchantid || !$terminal_sn || !$orderid || !$action || !$total_rmb || $total_rmb <= 0 || !$pay_rmb || $pay_rmb <= 0 || !$sellid) {
            return Library::output(2001, null, "参数错误1");
        }
        if ($total_rmb * 100 != $cp_fee * 100 + $plat_fee * 100 + $merchant_fee * 100 || $coupon_amount * 100 + $pay_rmb * 100 != $total_rmb * 100) {
            return Library::output(2001, null, "金额错误");
        }

        /**
         * 是退款订单，但两个参数不匹配
         */
        if ($action == "repeat" && $paytype != 0) {
            return Library::output(2001, null, "参数错误3");
        }

        if (isset($_GET['//payfor2bterminal'])) {
            unset($_GET['//payfor2bterminal']);
        }
        $check_sign = Library::encrypt($_GET, Config::get("common.uc_paykey"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }

        $toBStoreModel = new ToBStoreModel;

        /**
         * 加锁
         */
        $lock = Library::addLock($terminal_sn);
        if (!$lock) {
            return Library::output(2024);
        }
        $ret = $toBStoreModel->payFor2bTerminal($merchantid, $terminal_sn, $orderid, $request);
        Library::delLock($terminal_sn);

        if (!$ret) {
            return Library::output(1, $ret);
        } else {
            return "success";
        }
    }

    /**
     * 修改取款密码
     */
    public function changePayPwd(Request $request)
    {
        $merchantid = $request->input("merchantid", 0);
        $token      = $request->input("token", "");
        $oldpwd     = $request->input("oldpwd", "");
        $newpwd1    = $request->input("newpwd1", "");
        $newpwd2    = $request->input("newpwd2", "");

        if (!$merchantid || !$token) {
            return Library::output(1301);
        }

        if (!$oldpwd || !$newpwd1 || !$newpwd2) {
            return Library::output(2001);
        }

        if ($newpwd1 != $newpwd2) {
            return Library::output(1106);
        }
        $newpwd = md5($newpwd1);

        /**
         * 检查登录状态
         */
        $accountModel = new AccountCenter;
        $ret          = $accountModel->checkLogin($merchantid, $token);
        if (!$ret || !isset($ret['code'])) {
            return Library::output(1);
        }
        if ($ret['code'] != 0) {
            return Library::output($ret['code']);
        }

        $toBDBModel = new ToBDBModel;
        $minfo      = $toBDBModel->get2bMerchant($merchantid);
        if (!$minfo || !is_array($minfo) || !isset($minfo['pay_pwd'])) {
            return Library::output(1, "", "修改失败");
        }
        if ($minfo['pay_pwd'] && $minfo['pay_pwd'] != md5($oldpwd)) {
            return Library::output(2013);
        }
        $info['pay_pwd'] = $newpwd;
        $upd             = $toBDBModel->upd2bMerchant($merchantid, $info);
        if ($upd) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 检查终端设备状态
     */
    public function checkTerminal(Request $request)
    {
        $merchantid  = $request->input("merchantid", 0);
        $terminal_sn = $request->input("terminal_sn", "");
        if (!$merchantid || !$terminal_sn) {
            return Library::output(2001);
        }

        $toBDBModel = new ToBDBModel;
        $info       = $toBDBModel->get2bTerminal($merchantid, $terminal_sn);

        if (!is_array($info) || !isset($info['type'])) {
            return Library::output(1);
        }
        if (!$info) {
            return Library::output(2017);
        }

        if ($info['type'] == "time") {
            $now  = time();
            $left = $info['start'] + $info['playtime'] - $now;

            if ($left <= 0) {
                return Library::output(2401);
            }
            $arr = array("type" => $info['type'], "start" => $info['start'], "playtime" => $info['playtime'], "left" => $left);
            return Library::output(0, $arr);
        } else {
            return Library::output(2401);
        }
    }

    /**
     * 获取商家订单用于对账
     */
    public function getOrder4Check(Request $request)
    {
        $day  = $request->input("day", 0);
        $ts   = $request->input("ts", 0);
        $type = $request->input("type", 0);
        $sign = $request->input("sign", 0);
        if (!$day || !$ts || !$sign || $type != "getOrder4Check") {
            return Library::output(2001);
        }

        if (isset($_GET['//getOrder4Check'])) {
            unset($_GET['//getOrder4Check']);
        }
        $check_sign = Library::encrypt($_GET, Config::get("common.uc_appkey"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }
        $nowstamp = time();
        if ($nowstamp - 30 > $ts || $nowstamp + 30 < $ts) {
//            return Library::output(2002, null, "超时了");
        }

        $start      = date("Y-m-d", strtotime($day));
        $end        = date("Y-m-d", strtotime($day) + 86400);
        $toBDBModel = new ToBDBModel;
        $row        = $toBDBModel->get2bBill4Check($start, $end);
        $result     = [];
        for ($i = 0; $i < count($row); $i++) {
            $orderid          = $row[$i]['orderid'];
            $result[$orderid] = $row[$i];
        }
        return Library::output(0, $result);
    }

    /**
     * 获取商家订单用于对账
     */
    public function getOrderTotal4Check(Request $request)
    {

        $day  = $request->input("day", 0);
        $ts   = $request->input("ts", 0);
        $type = $request->input("type", 0);
        $sign = $request->input("sign", 0);
        if (!$day || !$ts || !$sign || $type != "getOrderTotal4Check") {
            return Library::output(2001);
        }

        if (isset($_GET['//getOrderTotal4Check'])) {
            unset($_GET['//getOrderTotal4Check']);
        }
        $check_sign = Library::encrypt($_GET, Config::get("common.uc_appkey"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }
        $nowstamp = time();
        if ($nowstamp - 30 > $ts || $nowstamp + 30 < $ts) {
//            return Library::output(2002, null, "超时了");
        }

        $start      = date("Y-m-d", strtotime($day));
        $end        = date("Y-m-d", strtotime($day) + 86400);
        $toBDBModel = new ToBDBModel;
        $brief      = $toBDBModel->get2bBillSumBrief4Check($start, $end);
        if (!$brief) {
            return Library::output(1);
        }
        return Library::output(0, $brief);
    }

    /**
     * 获取商家收入
     */
    public function getMerchantIncome(Request $request)
    {

        $day  = $request->input("day", 0);
        $ts   = $request->input("ts", 0);
        $type = $request->input("type", 0);
        $sign = $request->input("sign", 0);
        if (!$day || !$ts || !$sign || $type != "getMerchantIncome") {
            return Library::output(2001);
        }

        if (isset($_GET['//getMerchantIncome'])) {
            unset($_GET['//getMerchantIncome']);
        }
        $check_sign = Library::encrypt($_GET, Config::get("common.uc_appkey"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }
        $nowstamp = time();
        if ($nowstamp - 30 > $ts || $nowstamp + 30 < $ts) {
//            return Library::output(2002);
        }

        $start      = date("Y-m-d", strtotime($day));
        $end        = date("Y-m-d", strtotime($day) + 86400);
        $toBDBModel = new ToBDBModel;
        $brief      = $toBDBModel->get2bBillMerchantSum($start, $end);
        if (!is_array($brief)) {
            return Library::output(1);
        }
        $merchantids = $toBDBModel->getMerchatids();
        if (!is_array($merchantids)) {
            return Library::output(1);
        }
        $result = [];
        for ($i = 0; $i < count($merchantids); $i++) {
            $merchantid          = $merchantids[$i]['merchantid'];
            $result[$merchantid] = isset($brief[$merchantid]) ? $brief[$merchantid] : ['merchantid' => $merchantid, 'total_amount' => 0, 'pay_amount' => 0, 'cp_fee' => 0, 'plat_fee' => 0, 'merchant_fee' => 0];
        }
        return Library::output(0, $result);
    }

    /**
     * 添加退款订单
     */
    public function addRefundOrder(Request $request)
    {

        $order   = json_decode(base64_decode($request->input("param", "")), true);
        $orderid = $request->input("orderid", "");
        $sign    = $request->input("sign", "");
        if (!$orderid || !$order || !is_array($order) || !$sign) {
            return Library::output(2001);
        }

        $check_sign = Library::encrypt($_POST, Config::get("common.uc_paykey"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }

        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->add2bBill($orderid, $order);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 确认退款订单
     */
    public function confirmRefundOrder(Request $request)
    {

        $order   = json_decode(base64_decode($request->input("param", "")), true);
        $orderid = $request->input("orderid", "");
        $sign    = $request->input("sign", "");
        if (!$orderid || !$order || !is_array($order) || !$sign) {
            return Library::output(2001);
        }

        $check_sign = Library::encrypt($_POST, Config::get("common.uc_paykey"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }

        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->upd2bBill($orderid, $order);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 添加日统计订单
     */
    public function addDayBill(Request $request)
    {

        $info       = json_decode(base64_decode($request->input("param", "")), true);
        $merchantid = $request->input("merchantid", "");
        $day        = $request->input("day", "");
        $sign       = $request->input("sign", "");
        if (!$merchantid || !$day || !$info || !is_array($info) || !$sign) {
            return Library::output(2001);
        }

        $check_sign = Library::encrypt($_POST, Config::get("common.uc_paykey"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }

        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->addOneDayBill($merchantid, $day, $info);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 游戏启动统计
     * @param   string  merchantid
     * @param   string  terminal_sn
     * @param   int     appid
     * @param   string  type            操作类型， start:启动游戏;close:关闭游戏;beat:统计启动游戏时长，避免未发关闭，无法统计时长
     * @param   string  sign            签名
     */
    public function appStart2bStat(Request $request)
    {

        $merchantid  = $request->input("merchantid", "");
        $terminal_sn = $request->input("terminal_sn", "");
        $appid       = $request->input("appid", "");
        $type        = $request->input("type", "");
        $sign        = $request->input("sign", "");
        if (!$merchantid || !$terminal_sn || !$appid || !$type || !in_array($type, ["start", "close", "beat"]) || !$sign) {
            return Library::output(2001);
        }

        $check_sign = Library::encrypt($_POST, Config::get("common.vr_2bclient_key"));
        if ($sign != $check_sign) {
            return Library::output(2002);
        }

        $nowstamp   = time();
        $day        = date("Ymd", $nowstamp);
        $toBDBModel = new ToBDBModel;

        $ret = true;
        /**
         * 读出时长统计，判断上次的是否未结束
         * 如果有记录，没有发送结束，并且时长不为0，则统计，为0不统计
         * 正常请求在启动的时候，redis里是没有数据的
         */
        $old = Vredis::hget("2bplaylong", $merchantid, $terminal_sn);
        if ($old) {
            $old = json_decode($old, true);
        }
        /**
         * 启动游戏，增加启动次数
         * 同时记录
         */
        if ($type == "start") {
            $toBDBModel->addPlay($merchantid, $appid);
            $ret = $toBDBModel->setDayAppStat($merchantid, $day, $appid, "start", 1);

            if ($old && is_array($old) && isset($old['start']) && $old['start'] && isset($old['appid']) && $old['appid'] && isset($old['long']) && $old['long'] > 0) {
                /**
                 * 有没有结束的时长统计
                 * 统计到表里
                 */
                $oldday = date("Ymd", $old['start']);
                $ret    = $toBDBModel->setDayAppStat($merchantid, $oldday, $old['appid'], "long", $old['long']);
            }

            /**
             * 这次游戏开始，开始统计
             */
            $info = ["appid" => $appid, "start" => $nowstamp, "long" => 0]; // 其中 long用于每次心跳时，统计时长
            Vredis::hset("2bplaylong", $merchantid, $terminal_sn, json_encode($info));
        } else if ($type == "close") {
            /**
             * 关闭游戏，统计时长
             */
            if ($old && is_array($old) && isset($old['start']) && $old['start'] && isset($old['appid']) && $old['appid'] && $old['appid'] == $appid) {
                /**
                 * 有没有结束的时长统计
                 * 统计到表里
                 */
                $oldday = date("Ymd", $old['start']);
                $long   = $nowstamp - $old['start'];
                $ret    = $toBDBModel->setDayAppStat($merchantid, $oldday, $appid, "long", $long);
            }
            Vredis::hdel("2bplaylong", $merchantid, $terminal_sn);

        } else if ($type == "beat") {
            /**
             * 心跳，统计时长，防止未发送结束统计
             */
            if ($old && is_array($old) && isset($old['start']) && $old['start'] && isset($old['appid']) && $old['appid'] && $old['appid'] == $appid) {
                /**
                 * 有没有结束的时长统计
                 * 统计到表里
                 */
                $old['long'] = $nowstamp - $old['start'];
                Vredis::hset("2bplaylong", $merchantid, $terminal_sn, json_encode($old));
            }
        } else {
            return Library::output(2001);
        }
        Vredis::close();

        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

}
