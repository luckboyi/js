<?php

// ToB admin for merchants

namespace App\Http\Controllers;

use Config;
use Helper\AccountCenter;
use Helper\BankHelper;
use Helper\HttpRequest;
use Helper\Library;
use Illuminate\Http\Request;
use \App\Models\CookieModel;
use \App\Models\ImModel;
use \App\Models\SolrModel;
use \App\Models\ToBDBModel;
use \App\Models\WebgameModel;

class ToBStoreAdminController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:json:tobadmin", ['only' => ["allGame", "myGame", "buyGame", "delGame", "addTerminalGame", "addProduct", "editProduct", "delProduct", "setDefaultProduct", "terminalList", 'getGameBySort', 'sendTerminalMsg', 'modifySells', 'transactionRecord', 'getPayPwdCode', 'setPaypwd', "addCard", "getBankCards", "getBankCard", "checkBankCard", "extractCashCode", "extractCash", "terminalListSimple", "get2bBileByDate", "getDataByAll", "getMerchantPhone", "addGamesForTerminals", "delBankCard", "defaultCard", "getPayPwdInfo", "extractCashLog"]]);
    }

    public function sendTerminalMsg(Request $request)
    {
        Library::accessHeader();

        $userInfo        = $request->userinfo;
        $merchantid      = $userInfo['uid'];
        $terminal_sn_arr = $request->input('params');

        if (!$merchantid || !is_array($terminal_sn_arr) || empty($terminal_sn_arr)) {
            return Library::output(2002);
        }

        $toBDBModel = new ToBDBModel;
        $imModel    = new ImModel();
        foreach ($terminal_sn_arr as $k => $v) {
            $terminalInfo = $toBDBModel->get2bTerminal($merchantid, $v);
            if (!$terminalInfo || !isset($terminalInfo['terminal_id'])) {
                $ret[$k]['terminal_sn'] = $v;
                continue;
                // return Library::output(2003);
            }
            $terminal_id = $terminalInfo['terminal_id'];
            $receiver    = [$terminal_id];
            $cont        = ["name" => "sync", "data" => []];
            $msg         = Library::base64Urlsafeencode(json_encode($cont));

            $ret[$k]['result'] = $imModel->sysNotify2b(100, $receiver, $msg);
        }
        $result = [];
        foreach ($ret as $rk => $rv) {
            if (!$rv['result']) {
                $result['msg'][] = $terminal_sn_arr[$rk];
            }
        }
        if (empty($result)) {
            return Library::output(0);
        } else {
            return Library::output(1, $result);
        }
    }

    public function setTobCookie(Request $request)
    {
        Library::accessHeader();
        $uid      = $request->input('uid');
        $account  = $request->input('account');
        $nick     = $request->input('nick');
        $token    = $request->input('token');
        $merchant = $request->input('merchant');
        $face     = $request->input('face');

        if ($uid == '' || $token == '') {
            $code = 1;
            return Library::output($code);
        }
        /**
         * 检查登录状态
         */
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $ret          = $accountModel->checkLogin($uid, $token);

        if (!$ret || !isset($ret['code'])) {
            return Library::output(1);
        }
        if ($ret['code'] != 0) {
            return Library::output($ret['code']);
        }

        $params = array('uid' => $uid, 'token' => $token, 'account' => $account, 'nick' => $nick, 'merchant' => $merchant, 'face' => $face);
        CookieModel::setCookieArr($params);
        return Library::output(0);
    }

    /**
     * 获取终端列表
     * @return  array
     */
    public function terminalList(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];

        $terminals  = [];
        $toBDBModel = new ToBDBModel;
        $rows       = $toBDBModel->get2bTerminal($merchantId);
        $storeInfo  = $toBDBModel->get2bMerchant($merchantId);

        $imModel = new ImModel();
        $stats   = $imModel->terminalStat($rows);

        $store_merchant   = $storeInfo['merchant'];
        $terminal_address = $storeInfo['address'];
        $selected         = false;
        $computer_sort    = '客机';
        $tips_state       = false;
        $sell_state       = false;
        if (is_array($rows) && !empty($rows)) {
            $key = 0;
            foreach ($rows as $row) {
                if ($row['terminal_id'] == $storeInfo['terminal_id']) {
                    continue;
                }
                $time = $row['start'] + $row['playtime'] - time();
                $time = $time < 0 ? 0 : $time;
                $stat = isset($stats[$row['terminal_id']]) ? $stats[$row['terminal_id']] : 6;
                if ($stat == 6) {
                    $state       = '离线';
                    $classObject = 'abnormal_icon';
                } else if ($stat == 7) {
                    $state       = '游戏中';
                    $classObject = 'ingame_icon';
                } else if ($stat == 1) {
                    $state       = '待机';
                    $classObject = 'wait_icon';
                }

                $terminals[$key] = [
                    'state'            => $state,
                    'terminal_no'      => $row['terminal_no'],
                    'terminal_sn'      => $row['terminal_sn'],
                    'terminal_address' => $terminal_address,
                    'store_address'    => $store_merchant,
                    'computer_state'   => $computer_sort,
                    'computer_time'    => date('H:i:s', time()),
                    'classObject'      => $classObject,
                    'selected'         => $key == 0 ? true : $selected,
                    'tips_state'       => $tips_state,
                    'sell_state'       => $sell_state,
                    'stat'             => 0,
                    'time'             => $time,
                    'min'              => '30',
                    'hour'             => '60',

                ];
                $info                    = ['terminal_sn' => $row['terminal_sn']];
                $terminals[$key]['sell'] = $toBDBModel->get2bSell($merchantId, '', $info);
                foreach ($terminals[$key]['sell'] as $k => $v) {
                    if ($v['checked'] === 1) {
                        $terminals[$key]['sell'][$k]['selected'] = true;
                    } else {
                        $terminals[$key]['sell'][$k]['selected'] = false;
                    }
                }
                $key++;
            }
        }
        return Library::output(0, $terminals);
    }

    /**
     * 获取终端列表 只返回设备编号
     * @return  array
     */
    public function terminalListSimple(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];

        $terminals  = [];
        $toBDBModel = new ToBDBModel;
        $rows       = $toBDBModel->get2bTerminal($merchantId);

        return Library::output(0, $rows);
    }

    /**
     * 客户端获取游戏列表详细信息
     * @param   string  terminal_sn 终端编号
     * @return  mix
     */
    public function gameDetailList(Request $request)
    {
        Library::accessHeader();
        $userInfo           = $request->userinfo;
        $merchantId         = $userInfo['uid'];
        $terminal_sn        = $request->input('terminal_sn');
        $terminal_sn        = $terminal_sn ? $terminal_sn : "master";
        $request->addParams = ['merchantid' => $merchantId, 'terminal_sn' => $terminal_sn, 'start' => 0, 'num' => 8];
        $gameOut            = $this->search($request, false);
        $out                = ['num' => $gameOut['num']];
        $out['data']        = $this->gameListFormat($gameOut['data']);
        return Library::output(0, $out);
    }

    /**
     * 获取商家某台电脑上的游戏（按分类和机器sn,已购买和未购买）
     * [getGameBysort description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getGameBySort(Request $request)
    {
        Library::accessHeader();
        $userInfo           = $request->userinfo;
        $merchantId         = $userInfo['uid'];
        $terminal_sn        = $request->input('terminal_sn');
        $request->addParams = ['merchantid' => $merchantId, 'terminal_sn' => $terminal_sn];
        $mark               = intval($request->input('mark')); //判断筛选条件1：已添加 0：未添加
        if ($mark == 0) {
            $request->addParams = ['merchantid' => $merchantId, 'terminal_sn' => 'master', 'nobuy' => $terminal_sn];
        }

        $gameList = $this->search($request, false);

        $outArr = $this->gameListFormat($gameList['data']);
        $out    = [];
        foreach ($outArr as $k => $v) {
            $out['data'][] = $v;
        }
        $out['num'] = $gameList['num'];
        return Library::output(0, $out);
    }
    /**
     * 格式化数据
     * [gameListFormat description]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function gameListFormat($arr)
    {
        if (!is_array($arr) || count($arr) < 1) {
            return [];
        }
        foreach ($arr as $key => $value) {
            $img  = '//image.vronline.com/' . $value['image']['logo'];
            $deep = false;
            $htc  = false;
            $ocu  = false;
            $osvr = false;
            if (isset($value['support']) && count($value['support']) > 0) {
                foreach ($value['support'] as $v) {
                    if ($v === 1) {
                        $deep = true;
                    }
                    if ($v === 2 || $v === 3) {
                        $ocu = true;
                    }
                    if ($v === 4) {
                        $htc = true;
                    }
                    if ($v === 5) {
                        $osvr = true;
                    }
                }
            }
            $out[$key] = ['id' => $value['id'], 'img' => $img, 'name' => $value['name'], 'size' => $value['client_size'], 'deep' => $deep, 'htc' => $htc, 'ocu' => $ocu, 'osvr' => $osvr, 'version' => $value['client_version']];
        }
        return $out;
    }

    /**获取商家拥有的所有游戏
     * [allGame description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function allGame(Request $request)
    {
        Library::accessHeader();
        $userInfo            = $request->userinfo;
        $request->merchantId = $userInfo['uid'];
        $arr                 = $this->search($request, false);
        return Library::output(0, $arr);
    }

    /**
     * 获取游戏列表
     * @param   int  category  分类
     * @param   int  support 支持设备
     * @param   string  name 名称
     * @param   string  spell 字母
     * @param   string  sell 价格
     * @param   string  start 分页start
     * @param   string  num 分页num
     * @return  bool
     */
    private function search(Request $request, $dataOnly = false)
    {
        $category = intval($request->input('category'));
        $support  = intval($request->input('support'));
        $name     = trim($request->input('name'));
        $spell    = trim($request->input('spell'));
        $sell     = $request->input('sell');
        $inids    = $request->input('inids');

        $start = intval($request->input('start', 0));
        $num   = intval($request->input('num', 10));

        if ($start < 0) {
            return Library::output(1);
        }
        if ($num < 1) {
            return Library::output(1);
        }
        $params = [];
        $tp     = 'vrgame';
        if (isset($request->addParams)) {
            $params['merchantid']  = $request->addParams['merchantid'];
            $params['terminal_sn'] = $request->addParams['terminal_sn'];
            $tp                    = 'tob';
        }
        if ($category > 0) {
            $params['category'] = $category;
        }
        if ($support > 0) {
            $params['support'] = $support;
        }
        if ($spell) {
            $params['spell'] = $spell;
        }
        if ($name) {
            $params['name'] = $name;
        }
        if (is_numeric($sell)) {
            $params['sell'] = round($sell, 1);
        }
        if ($inids) {
            $params['gameids'] = $inids;
        }
        $params['limit'] = [$start, $num];
        if ($tp == "vrgame") {
            $params['tob_in'] = 1;
        }
        $solrModel = new SolrModel();
        $numFound  = 0;
        $res       = $solrModel->search($tp, $params, true, $numFound);
        if (!$res) {
            $out = ['data' => [], 'num' => 0];
        } else {
            $out = ['data' => $res, 'num' => $numFound];
        }

        if (isset($request->addParams) && isset($request->addParams['nobuy'])) {
            if (!empty($out['data'])) {
                $gameIds = [];
                foreach ($out['data'] as $game) {
                    $gameIds[] = $game['id'];
                }

                $params  = ['merchantid' => $request->addParams['merchantid'], 'terminal_sn' => $request->addParams['nobuy'], 'gameids' => $gameIds];
                $inGames = $solrModel->search('tob', $params, false);

                if (is_array($inGames) && !empty($inGames)) {
                    $inGameIds = [];
                    foreach ($inGames as $game) {
                        $inGameIds[] = $game['id'];
                    }
                    foreach ($out['data'] as $key => $game) {
                        if (in_array($game['id'], $inGameIds)) {
                            unset($out['data'][$key]);
                        }
                    }

                }
            }
        }
        if ($tp == "vrgame") {
            if (!empty($out['data'])) {
                $gameIds = [];
                foreach ($out['data'] as $game) {
                    $gameIds[] = $game['id'];
                }
                $params  = ['merchantid' => $request->merchantId, 'terminal_sn' => "master", 'gameids' => $gameIds];
                $inGames = $solrModel->search('tob', $params, false);
                if (is_array($inGames) && !empty($inGames)) {
                    $inGameIds = [];
                    foreach ($inGames as $game) {
                        $inGameIds[] = $game['id'];
                    }
                    foreach ($out['data'] as $key => $game) {
                        if (in_array($game['id'], $inGameIds)) {
                            $out['data'][$key]['purchased'] = 1;
                        }
                    }
                }
            }
        }
        if ($dataOnly) {
            return $out['data'];
        }
        return $out;
    }

    /**
     * 客户端获取游戏列表
     * @param   string  terminal_sn 终端编号
     * @return  mix
     */
    public function gameList(Request $request)
    {
        Library::accessHeader();
        $merchantId         = $request->input('merchant_id');
        $terminal_sn        = $request->input('terminal_sn');
        $terminal_sn        = $terminal_sn ? $terminal_sn : "master";
        $request->addParams = ['merchantid' => $merchantId, 'terminal_sn' => $terminal_sn];
        $arr                = $this->search($request, true);
        $out                = [];
        foreach ($arr as $key => $value) {
            $out[$key] = ['id' => $value['id'], 'version' => "1.0.1", 'down' => 'http://down.vrgame.vronline.com/dev/' . $value['id'] . '/' . $value['client_version'] . '/'];
        }
        return Library::output(0, $out);
    }

    /**
     * 获取我的游戏列表
     * @param   string  terminal_sn 终端编号
     * @return  mix
     */
    public function myGame(Request $request)
    {
        Library::accessHeader();
        $userInfo           = $request->userinfo;
        $merchantId         = $userInfo['uid'];
        $terminal_sn        = $request->input('terminal_sn');
        $terminal_sn        = $terminal_sn ? $terminal_sn : "master";
        $request->addParams = ['merchantid' => $merchantId, 'terminal_sn' => $terminal_sn];
        $arr                = $this->search($request);
        return Library::output(0, $arr);
    }

    /**
     * 购买游戏
     * @param   int  appid 游戏APPID
     * @return  mix
     */
    public function buyGame(Request $request)
    {
        Library::accessHeader();
        $userInfo    = $request->userinfo;
        $merchantId  = $userInfo['uid'];
        $appid       = $request->input('appid');
        $terminal_sn = "master";
        $toBDBModel  = new ToBDBModel;
        $ret         = $toBDBModel->add2bGame($merchantId, $terminal_sn, [$appid]);
        if ($ret) {
            $solrModel = new SolrModel();
            $solrModel->updateTerminalGame($merchantId, $terminal_sn, [$appid]);
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 删除游戏
     * @param   int  appid 游戏APPID
     * @return  mix
     */
    public function delGame(Request $request)
    {
        Library::accessHeader();
        $userInfo    = $request->userinfo;
        $merchantid  = $userInfo['uid'];
        $appid       = $request->input('appid');
        $terminal_sn = $request->input('terminal_sn');
        $terminal_sn = $terminal_sn ? $terminal_sn : "master";

        if (!$merchantid || !$appid) {
            return Library::output(1);
        }
        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->del2bGame($merchantid, $terminal_sn, $appid);
        if ($ret) {
            $solrModel = new SolrModel();
            $solrModel->delTerminalGame($merchantid, $terminal_sn, $appid);
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 终端添加游戏
     * @param   int  appid 游戏APPID
     * @param   string  terminal_sn 终端编号
     * @return  mix
     */
    public function addTerminalGame(Request $request)
    {
        Library::accessHeader();
        $userInfo    = $request->userinfo;
        $merchantid  = $userInfo['uid'];
        $appid       = $request->input('appid');
        $terminal_sn = $request->input('terminal_sn');

        if (!$merchantid || !$appid || !$terminal_sn || $terminal_sn == "master") {
            return Library::output(1);
        }

        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->add2bGame($merchantid, $terminal_sn, [$appid]);
        if ($ret) {
            $solrModel = new SolrModel();
            $solrModel->updateTerminalGame($merchantid, $terminal_sn, [$appid]);
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 终端添加游戏
     * @param   int  appid 游戏APPID
     * @param   string  terminal_sn 终端编号
     * @return  mix
     */
    public function addGamesForTerminals(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantid = $userInfo['uid'];
        $appids     = $request->input('appids');
        $terminals  = $request->input('terminals');

        //var_dump($appids, $terminals);
        if (!$merchantid || !$appids || !$terminals) {
            return Library::output(1);
        }

        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->add2bGamesForTerminals($merchantid, $terminals, $appids);
        if (!$ret) {
            return Library::output(1);
        }

        $solrModel = new SolrModel();
        $ret2      = $solrModel->updateTerminalsGame($merchantid, $terminals, $appids);
        if ($ret2) {
            return Library::output(0);
        }

        return Library::output(1);
    }

    /**
     * 添加价格信息
     * @param   string  merchantid  商户ID
     * @param   string  terminal_sn 终端编号
     * @param   enum  type 类型 time or game
     * @param   int  appid 游戏id
     * @param   string  price 价格
     * @param   string  title 描述
     * @param   int  playtime 时间
     * @return  bool
     */
    public function addProduct(Request $request)
    {
        Library::accessHeader();
        $userInfo    = $request->userinfo;
        $merchantId  = $userInfo['uid'];
        $terminal_sn = $request->input('terminal_sn');
        $type        = $request->input('type');
        $appid       = intval($request->input('appid'));
        $price       = round($request->input('price'), 2);
        $title       = $request->input('title');
        $playtime    = intval($request->input('playtime'));
        if (!$terminal_sn || ($type != 'time' && $type != 'game') || $price < 0 || !$title || $playtime < 0) {
            return Library::output(1);
        }

        if ($type == 'game') {
            if (!$appid) {
                return Library::output(1);
            }
        }

        $info = ['terminal_sn' => $terminal_sn, 'price' => $price, 'type' => $type, 'title' => $title, 'playtime' => $playtime];
        if ($appid) {
            $info['appid'] = $appid;
        }
        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->add2bSell($merchantId, $type, $info);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }
    /**
     * 批量修改价格和对应时长
     * [modifySells description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function modifySells(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $pushData   = $request->input('params');
        $merchantId = $userInfo['uid'];
        if (empty($pushData) || !$merchantId) {
            return Library::output(1);
        }
        $toBDBModel    = new ToBDBModel;
        $modifyDataArr = json_decode($pushData, 1);

        foreach ($modifyDataArr as $k => $v) {
            $productId = $v['id'];
            $info      = ['price' => $v['price'], 'playtime' => $v['playtime'], 'checked' => 1];
            if (!$v['selected']) {
                $info['checked'] = 0;
            } else {
                $info['checked'] = 1;
            }

            $ret[$productId] = $toBDBModel->upd2bSell($merchantId, $productId, $info);
        }
        foreach ($ret as $key => $value) {

            if (!$value) {
                $result[] = 'id=' . $key . ',修改失败！';
            }
            $result[] = 'id=' . $key . ',修改成功。';
        }
        return Library::output(0, $result);
    }

    /**
     * 编辑价格信息
     * @param   string  merchantid  商户ID
     * @param   int  product_id 商品Id
     * @param   int  appid 游戏id
     * @param   string  price 价格
     * @param   string  title 描述
     * @param   int  playtime 时间
     * @return  bool
     */
    public function editProduct(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $productId  = intval($request->input('product_id'));
        $appid      = intval($request->input('appid'));
        $price      = round($request->input('price'), 2);
        $title      = $request->input('title');
        $playtime   = intval($request->input('playtime'));

        if (!$product_id || $price < 0 || !$title || $playtime < 0) {
            return Library::output(1);
        }
        $info = ['price' => $price, 'title' => $title, 'playtime' => $playtime];
        if ($appid) {
            $info['appid'] = $appid;
        }
        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->upd2bSell($productId, $info);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 删除价格
     * @param   string  merchantid  商户ID
     * @param   int  product_id 商品Id
     * @return  bool
     */
    public function delProduct(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $productId  = intval($request->input('product_id'));
        if (!$product_id) {
            return Library::output(1);
        }
        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->del2bSell($productId);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 设置默认价格
     * @param   string  merchantid  商户ID
     * @param   string  terminal_sn 终端编号
     * @param   int  product_id 商品Id
     * @return  bool
     */
    public function setDefaultProduct(Request $request)
    {
        Library::accessHeader();
        $userInfo    = $request->userinfo;
        $merchantId  = $userInfo['uid'];
        $terminal_sn = $request->input('terminal_sn');
        $productId   = intval($request->input('product_id'));

        if (!$terminal_sn || !$productId) {
            return Library::output(1);
        }

        $toBDBModel = new ToBDBModel;
        $ret        = $toBDBModel->setDefault2bSell($merchantId, $terminal_sn, $productId);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 获取游戏分类
     *
     * @return  array
     */
    public function gameCategory()
    {
        Library::accessHeader();
        $category = Config::get("category.vrgame");
        return Library::output(0, $category);
    }

    /**
     * 获取交易记录
     * @param   int  page 分页
     * @param   int  tp 记录类型 1交易 2提现
     * @return  json
     */
    public function transactionRecord(Request $request)
    {
        Library::accessHeader();
        $userInfo    = $request->userinfo;
        $merchantId  = $userInfo['uid'];
        $token       = $userInfo['token'];
        $tp          = intval($request->input('tp'));
        $page        = intval($request->input('page'));
        $page        = $page ? $page : 0;
        $terminal_no = intval($request->input('terminal_no', false));
        $status      = $request->input('status', false);
        if ($page < 0 || ($tp != 1 && $tp != 2)) {
            return Library::output(1);
        }

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $balanceInfo  = $accountModel->get2bBalance($merchantId, $token);
        if (!$balanceInfo || $balanceInfo['code'] != 0) {
            return Library::output(1);
        }
        $where = [];
        if ($terminal_no) {
            $where["terminal_no"] = $terminal_no;
        }
        if ($status != "all") {
            $where["status"] = $status;
        }

        $toBDBModel = new ToBDBModel;
        $count      = $toBDBModel->get2bBillCountByMerchantId($merchantId, $tp, $where);
        if ($count == 0) {
            return Library::output(0, array("count" => $count, 'balance' => $balanceInfo['data']));
        }

        $records = $toBDBModel->get2bBillByMerchantId($merchantId, $tp, ($page - 1) * 10, 10, $where);

        if (!$records) {
            return Library::output(0, array("count" => $count, "data" => $records, 'balance' => $balanceInfo['data']));
        }

        foreach ($records as &$record) {
            $appids[] = $record["appid"];
        }
        $appids    = array_unique($appids);
        $gameModel = new WebgameModel;
        $games     = $gameModel->getMultiGameName($appids);

        $gameName = [];
        foreach ($games as $game) {
            $gameName[$game["appid"]] = $game["name"];
        }

        return Library::output(0, array("count" => $count, "data" => $records, 'balance' => $balanceInfo['data'], "name" => $gameName));

    }

    /**
     * 获取提现记录
     * @param   int  page 分页
     * @return  json
     */
    public function extractCashLog(Request $request)
    {
        Library::accessHeader();
        $userInfo     = $request->userinfo;
        $page         = intval($request->input('page'));
        $page         = $page ? $page : 1;
        $stat         = trim($request->input('stat', -1));
        $stat         = $stat;
        $merchantId   = $userInfo['uid'];
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $paykey       = Config::get("common.uc_paykey");
        $accountModel = new AccountCenter($appid, $appkey, $paykey);

        $res = $accountModel->extractCashLog($merchantId, $stat, $page, false);
        if (is_array($res) && $res['code'] == 0) {
            $count = $res['data']['num'];
            $data  = $res['data']['rows'];
            return Library::output(0, array("count" => $count, "data" => $data));
        } else {
            return Library::output(1);
        }
    }
    /**
     * 发起退款
     * @param   string orderid  订单号
     * @return  bool
     */
    public function goRefund(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];
        $orderId    = trim($request->input('orderid'));
        if (!$merchantId || !$token || $orderId) {
            return Library::output(1);
        }

        $toBDBModel = new ToBDBModel;
        $record     = $toBDBModel->get2bBillByOrderid($orderId);
        if (!$record || $record['status'] != 8) {
            return Library::output(1);
        }

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $result       = $accountModel->goRefund($merchantId, $token, $orderId);
        if ($result['code'] == 0) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /**
     * 根据取款密码状态
     * @return  json
     */
    public function getPayPwdInfo(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->hasPaypwd($merchantId, $token);
        if (!isset($res['code'])) {
            return Library::output(1);
        }

        if ($res['code'] == 404) {
            $hasPaypwd = false;
        } else if ($res['code'] == 0) {
            $hasPaypwd = true;
        } else {
            return Library::output(1);
        }
        return Library::output(0, ['hasPayPwd' => $hasPaypwd]);
    }

    /**
     * 设置取款密码时获取验证码
     * @return  array   ['code'=>0]
     */
    public function getPayPwdCode(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $account    = $userInfo['account'];
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];

        $appid  = Config::get("common.uc_appid");
        $appkey = Config::get("common.uc_appkey");

        $accountModel = new AccountCenter($appid, $appkey);
        $accountInfo  = $accountModel->info($merchantId, $token);
        if (empty($accountInfo['data'])) {
            return Library::output(1);
        }
        $mobile = $accountInfo['data']['bindmobile'] == '' ? '' : $accountInfo['data']['bindmobile'];
        if (!$mobile) {
            return Library::output(1);
        }

        $res = $accountModel->sendSmsMsg($account, $mobile, 'set_cash_pwd');

        if (!$res || $res['code'] !== 0) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /**
     * 设置取款密码，或修改取款密码
     * @param   int     merchantid  账号
     * @param   string  token   token
     * @param   string  code   验证码
     * @param   string  oldpwd   老的密码，如果没有，可以不传，或传空
     * @param   string  newpwd   新的密码
     * @return  array   ['code'=>0]
     */
    public function setPaypwd(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];

        $oldpwd = trim($request->input('oldpwd'));
        $newpwd = trim($request->input('newpwd'));
        $code   = trim($request->input('code'));
        if (!$code || !$newpwd) {
            return Library::output(1);
        }

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->hasPaypwd($merchantId, $token);
        if (!isset($res['code'])) {
            return Library::output(1);
        }

        if ($res['code'] == 404) {
            $oldpwd = '';
        } else {
            if (!$oldpwd) {
                return Library::output(1);
            }
        }

        $res = $accountModel->setPaypwd($merchantId, $token, $code, md5($oldpwd), md5($newpwd));
        if (!$res || !isset($res['code'])) {
            return Library::output(1);
        } else {
            if ($res['code'] == 0) {
                return Library::output(0);
            } else {
                return Library::output(1);
            }
        }
    }

    /**
     * 根据银行卡号获取银行卡信息
     * @param   string  card_num   银行卡号
     * @return  json
     */
    public function getBankCards(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];
        $toBDBModel = new ToBDBModel;

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->get2bCards($merchantId, $token);
        if (!$res || $res['code'] != 0) {
            return Library::output(1);
        } else {
            return Library::output(0, $res['data']);
        }
    }

    /**
     * 获取银行卡可选城市
     *
     * @param   string  card_num   银行卡号
     * @return  json
     */
    public function getBankCitys(Request $request)
    {
        Library::accessHeader();

        $request = "https://pay.heepay.com/API/PayTransit/QueryProvincesAndCities.aspx";

        $ret    = HttpRequest::get($request);
        $str    = mb_convert_encoding($ret, "utf-8", "GBK");
        $xmlObj = simplexml_load_string($str);
        $result = json_decode(json_encode($xmlObj), true);

        if (!$result || !$result["province"]) {
            return Library::output(1);
        }

        $provincesArr = [];
        foreach ($result["province"] as $province) {
            $provincesArr[$province["@attributes"]["name"]] = (array) $province["city"];
        }
        return Library::output(0, $provincesArr);

    }

    /**
     * 根据银行卡号获取银行卡信息
     * @param   string  card_num   银行卡号
     * @return  json
     */
    public function getBankCard(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];
        $card_id    = trim($request->input('card_id'));

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->get2bCard($merchantId, $token, $card_id);
        if (!$res || $res['code'] != 0) {
            return Library::output(1);
        } else {
            return Library::output(0, $res['data']);
        }
    }

    /**
     * 添加银行卡
     * @param   string  card_no   银行卡号
     * @param   string  card_name   姓名
     * @param   string  card_province 省
     * @param   string  card_city 市
     * @param   string  card_opener 开户行名称
     * @return  json
     */
    public function addCard(Request $request)
    {
        Library::accessHeader();
        $userInfo      = $request->userinfo;
        $merchantId    = $userInfo['uid'];
        $token         = $userInfo['token'];
        $card_no       = trim($request->input('card_no'));
        $card_name     = trim($request->input('card_name'));
        $card_province = trim($request->input('card_province'));
        $card_city     = trim($request->input('card_city'));
        $card_opener   = trim($request->input('card_opener'));
        $card_owner    = intval($request->input('card_owner'));

        if (!$card_no || !$card_name || !$card_province || !$card_city || !$card_opener) {
            return Library::output(1);
        }
        if (!in_array($card_owner, [0, 1])) {
            return Library::output(1);
        }
        $bankInfo = BankHelper::getBankInfoByCardNo($card_no);
        if (!$bankInfo) {
            return Library::output(1);
        }

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->add2bCard($merchantId, $token, $card_no, $card_name, $card_province, $card_city, $card_opener, $bankInfo['bankname'], $bankInfo['cardtype'], $bankInfo['bankno'], $card_owner);

        if (!$res || $res['code'] != 0) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 根据银行卡号检查银行卡信息
     * @param   string  card_no   银行卡号
     * @return  json
     */
    public function checkBankCard(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];
        $card_no    = trim($request->input('card_no'));
        if (!$card_no) {
            return Library::output(1);
        }

        $bankInfo = BankHelper::getBankInfoByCardNo($card_no);
        if (!$bankInfo) {
            return Library::output(1);
        }

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->check2bCard($merchantId, $token, $card_no);
        if (!$res || $res['code'] != 0) {
            return Library::output(1);
        } else {
            return Library::output(0, $bankInfo);
        }
    }

    /**
     * [defaultCard 设置默认银行卡]
     * @param  id int [银行卡ID]
     * @return JSON
     */
    public function defaultCard(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];
        $card_id    = trim($request->input('card_id'));
        if (!$card_id) {
            return Library::output(1);
        }
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->default2bCard($merchantId, $token, $card_id);
        if (!$res || $res['code'] != 0) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * [delBankCard 删除银行卡]
     * @param  id int [银行卡ID]
     * @return JSON
     */
    public function delBankCard(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];
        $card_id    = trim($request->input('card_id'));
        if (!$card_id) {
            return Library::output(1);
        }
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->del2bCard($merchantId, $token, $card_id);
        if (!$res || $res['code'] != 0) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }
    /**
     *    获取提现验证码
     * @param   string  cash  金额
     * @param   string  card_id  银行卡ID
     * @return  json
     */
    public function extractCashCode(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $account    = $userInfo['account'];
        $token      = $userInfo['token'];
        $cash       = trim($request->input('cash'));
        $card_id    = trim($request->input('card_id'));

        if (!$cash || !$card_id) {
            return false;
        }
        $appid  = Config::get("common.uc_appid");
        $appkey = Config::get("common.uc_appkey");

        $accountModel = new AccountCenter($appid, $appkey);
        $accountInfo  = $accountModel->info($merchantId, $token);
        if (empty($accountInfo['data'])) {
            return Library::output(1);
        }
        $mobile = $accountInfo['data']['bindmobile'] == '' ? '' : $accountInfo['data']['bindmobile'];
        if (!$mobile) {
            return Library::output(1);
        }

        $res = $accountModel->sendSmsMsg($account, $mobile, 'extract_cash_msg', ['cash' => $cash, 'card_id' => $card_id]);
        if (!$res || $res['code'] !== 0) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /**
     *    提现
     * @param   string  card_id   银行卡号
     * @param   string  cash_pwd   取款密码
     * @param   string  code  验证码
     * @param   string  cash  金额
     * @return  json
     */
    public function extractCash(Request $request)
    {
        Library::accessHeader();
        $userInfo = $request->userinfo;

        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];
        $cash       = trim($request->input('cash'));
        $card_id    = trim($request->input('card_id'));
        $cash_pwd   = trim($request->input('cash_pwd'));
        $code       = trim($request->input('code'));

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $res          = $accountModel->extractCash($merchantId, $token, $card_id, $cash, md5($cash_pwd), $code);
        if (!$res || $res['code'] !== 0) {
            return Library::output(1);
        }
        return Library::output(0, $res['data']);
    }

    /**
     * 分成收入的获取数据接口
     * [get2bBileByDate description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get2bBileByDate(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];

        $start = $request->input("start");
        $count = $request->input("count");
        $case  = $request->input("case");

        $toBDBModel = new ToBDBModel;
        if ($case == "day") {
            $ret = $toBDBModel->get2bBillByDay($merchantId, $start, $count, 0);
        }
        if ($case == "month") {
            $ret = $toBDBModel->get2bBillByDay($merchantId, $start, $count, 1);
        }
        $result = [];
        // $result['count']     = $ret['count'];
        $result['totalBill'] = $ret['totalBill'];
        if ($ret['data']) {
            foreach ($ret['data'] as $k => $v) {
                $result['data'][$k]['time']        = $case == "day" ? date("Y-m-d", strtotime($v['day'])) : date("Y-m", strtotime($v['day']));
                $result['data'][$k]['income']      = $v['total_amount'];
                $result['data'][$k]['scale']       = Config::get("common.2b_plat_rate") * 100 . "%";
                $result['data'][$k]['scale_money'] = $v['total_amount'] - $v['net_income'];
                $result['data'][$k]['real_income'] = $v['net_income'];
                $result['data'][$k]['msg']         = $v['status'];
            }
        }
        return Library::output(0, $result);
    }
    /**
     * [getDataByAll description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getDataByAll(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];

        $start      = $request->input("start");
        $count      = $request->input("count");
        $action     = $request->input('case');
        $gameModel  = new WebgameModel;
        $toBDBModel = new ToBDBModel;

        $sum['sum']             = $toBDBModel->getDayAppSum($merchantId);
        $sum['sum']['money']    = isset($sum['sum']['money']) ? $sum['sum']['money'] : '00:00';
        $sum['sum']['start']    = isset($sum['sum']['start']) ? $sum['sum']['start'] : 0 . "次";
        $sum['sum']['playlong'] = isset($sum['sum']['playlong']) ? $sum['sum']['playlong'] : 0;

        if ($action == 'day') {
            $dataListByDay = $toBDBModel->getDayAppDate($merchantId, $start, $count, 0);
        }
        if ($action == 'month') {
            $dataListByDay = $toBDBModel->getDayAppDate($merchantId, $start, $count, 1);
        }

        $out['data'] = [];
        foreach ($dataListByDay['data'] as $k => $v) {
            $gameInfo                    = $gameModel->getOneGameInfo($v['appid']);
            $out['data'][$k]['name']     = isset($gameInfo['name']) ? $gameInfo['name'] : '未知游戏';
            $out['data'][$k]['num']      = $v['start'] . '次';
            $out['data'][$k]['time']     = $action == "day" ? date("Y-m-d", strtotime($v['day'])) : date("Y-m", strtotime($v['day']));
            $out['data'][$k]['playlong'] = timeFormat($v['playlong']);
        }
        for ($i = 0; $i < count($sum['sum']); $i++) {
            if ($i === 0) {
                $out['sum'][0] = [
                    'click_state' => true,
                    'iconobject'  => ['gameplay_icon' => true],
                    'name'        => '游戏启动：',
                    'time'        => $sum['sum']['start'],
                    'iconstate'   => ['up_icon' => true],
                ];
            }
            if ($i === 1) {
                $out['sum'][1] = [
                    'click_state' => false,
                    'iconobject'  => ['gameplay_icon' => true],
                    'name'        => '游戏购买：',
                    'time'        => '￥' . $sum['sum']['money'],
                    'iconstate'   => ['up_icon' => true],
                ];
            }
            if ($i === 2) {
                $out['sum'][2] = [
                    'click_state' => false,
                    'iconobject'  => ['gameplay_icon' => true],
                    'name'        => '游戏时长：',
                    'time'        => timeFormat($sum['sum']['playlong']),
                    'iconstate'   => ['up_icon' => true],
                ];
            }
        }

        return $out;
    }

    /**
     * [getMerchantPhone description]
     * @return [type] [description]
     */
    public function getMerchantPhone(Request $request)
    {
        Library::accessHeader();
        $userInfo   = $request->userinfo;
        $merchantId = $userInfo['uid'];
        $token      = $userInfo['token'];

        $appid  = Config::get("common.uc_appid");
        $appkey = Config::get("common.uc_appkey");

        $accountModel = new AccountCenter($appid, $appkey);
        $accountInfo  = $accountModel->info($merchantId, $token);
        if (empty($accountInfo['data'])) {
            return Library::output(1);
        }
        $mobile = $accountInfo['data']['bindmobile'] == '' ? '' : $accountInfo['data']['bindmobile'];

        return Library::output(0, ["mobile" => $mobile]);
    }

}
