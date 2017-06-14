<?php
/*
 * 线下体验店
 * date:2017/1/10
 */

namespace App\Models;

use App\Helper\Vredis;
use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;
use \App\Models\SolrModel;

class ToBDBModel extends Model
{

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             线 下 体 验 店 商 家 表                                         |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 更新店铺信息
     * @param int $id 商户id
     * @param array $info 商户信息
     * @return  mix
     */
    public function updateMerchant($id, $info)
    {
        if (!$id || !$info) {
            return false;
        }
        $row = DB::connection("db_2b_store")->table("t_2b_merchant")->where('merchantid', $id)->update($info);
        return $row;
    }

    /**
     * 店铺列表
     * @return  mix
     */
    public function getMerchats()
    {
        $row = DB::connection("db_2b_store")->table("t_2b_merchant")->paginate(10);
        return $row;
    }

    /**
     * 店铺列表
     * @return  mix
     */
    public function getMerchatids()
    {
        $row = DB::connection("db_2b_store")->table("t_2b_merchant")->select("merchantid")->get();
        return $row;
    }

    /**
     * 店铺列表
     * @return  mix
     */
    public function getMerchantsByStatus($status)
    {
        $row = DB::connection("db_2b_store")->table("t_2b_merchant");
        if (is_array($status)) {
            $row->whereIn("status", $status);
        } elseif (intval($status) > 0) {
            $row->where("status", $status);
        }

        return $row->paginate(10);
    }
    /**
     * 添加店铺
     * @param   string  merchantid  商户ID
     * @param   array   info  商户信息, ["account" => xxx, "terminal_sn"=>xxx, "terminal_mac"=>xxx, "secret"=>xxx, ......]
     * @return  int
     */
    public function add2bMerchant($merchantid, $info)
    {
        if (!$merchantid || !$info || !is_array($info)) {
            return false;
        }
        $info['merchantid'] = $merchantid;
        $info['ts']         = date("Y-m-d H:i:s");
        $ret                = DB::connection("db_2b_store")->table("t_2b_merchant")->insert($info);
        return $ret;
    }

    /**
     * 修改商户信息，不能修改对应的商户绑定信息
     * @param   string  merchantid   商户ID
     * @param   array   info         其他信息,array('merchant'=>xxx, 'pay_pwd'=>xxx, 'terminal_mac'=>xxx, 'serverip'=>xxx, 'serverport'=>xxx, 'secret'=>xxx)
     * @return  bool
     */
    public function upd2bMerchant($merchantid, $info)
    {
        if (!$merchantid || !$info || !is_array($info)) {
            return false;
        }
        if (isset($info['merchantid']) || isset($info['secret'])) {
            // 暂时不允许修改秘钥，以后可开放
            return false;
        }
        $info['ts'] = date("Y-m-d H:i:s");
        $ret        = DB::connection("db_2b_store")->table("t_2b_merchant")->where("merchantid", $merchantid)->update($info);
        return $ret;
    }

    /**
     * 读改商户信息
     * @param   string  merchantid   商户ID
     * @return  array
     */
    public function get2bMerchant($merchantid)
    {
        if (!$merchantid) {
            return false;
        }
        $ret = DB::connection("db_2b_store")->table("t_2b_merchant")->where("merchantid", $merchantid)->first();
        return $ret;
    }

    /**
     * 删除商户信息
     * @param   string  merchantid   商户ID
     * @return  array
     */
    public function del2bMerchant($merchantid)
    {
        if (!$merchantid) {
            return false;
        }
        $ret = DB::connection("db_2b_store")->table("t_2b_merchant")->where("merchantid", $merchantid)->delete();
        return $ret;
    }

    /**
     * 初始化商户免费游戏
     * @param   string  merchantid   商户ID
     * @return  bool
     */
    public function addMerchantDefaultGame($merchantid)
    {
        if (!$merchantid) {
            return false;
        }
        $row = DB::connection("db_2b_store")->table("t_2b_merchant")->select('init')->where("merchantid", $merchantid)->first();
        if (!$row) {
            return false;
        }
        if ($row['init'] == 0) {
            $defaultGame = $this->getDefaultGame();
            if ($defaultGame) {
                $terminal_sn = 'master';
                $ret         = $this->add2bGame($merchantid, $terminal_sn, $defaultGame);
                if ($ret) {
                    DB::connection("db_2b_store")->table("t_2b_merchant")->where("merchantid", $merchantid)->update(['init' => 1]);
                    $solrModel = new SolrModel;
                    $solrModel->updateTerminalGame($merchantid, $terminal_sn);
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             线 下 体 验 店 终 端 设 备 表                                   |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获得最大的设备编号
     * @param   string  merchantid  商户ID
     * @return  int
     */
    public function genMax2bTerminalNo($merchantid)
    {
        if (!$merchantid) {
            return false;
        }
        $maxNo = DB::connection("db_2b_store")->table("t_2b_terminal")->where('merchantid', $merchantid)->max("terminal_no");
        return intval($maxNo);
    }

    /**
     * 添加新设备
     * @param   string  merchantid   商户ID
     * @param   string  terminal_sn  终端设备号
     * @param   array   info         其他信息,array('terminal_no'=>xxx, 'terminal_ip'=>xxx, 'terminal_mac'=>xxx)
     * @return  bool
     */
    public function add2bTerminal($merchantid, $terminal_sn, $info)
    {
        if (!$merchantid || !$terminal_sn || !$info || !is_array($info) || !isset($info['terminal_no']) || !$info['terminal_no'] || !isset($info['terminal_mac']) || !$info['terminal_mac']) {
            return false;
        }
        $info['merchantid']  = $merchantid;
        $info['terminal_sn'] = $terminal_sn;
        $info['ts']          = date("Y-m-d H:i:s");
        $ret                 = DB::connection("db_2b_store")->table("t_2b_terminal")->insert($info);
        //$this->clear2bTerminalStatusCache($merchantid, $terminal_sn);
        return $ret;
    }

    /**
     * 修改设备信息，不能修改对应的商户绑定信息
     * @param   string  merchantid   商户ID
     * @param   string  terminal_sn  终端设备号
     * @param   array   info         其他信息,array('terminal_no'=>xxx, 'terminal_ip'=>xxx, 'terminal_mac'=>xxx, 'orderid'=>xxx, 'start'=>xxx, 'playtime'=>xxx, 'payer_id'=>xxx)
     * @return  bool
     */
    public function upd2bTerminal($merchantid, $terminal_sn, $info)
    {
        if (!$merchantid || !$terminal_sn || !$info || !is_array($info)) {
            return false;
        }
        if (isset($info['merchantid']) || isset($info['terminal_sn'])) {
            return false;
        }
        $info['ts'] = date("Y-m-d H:i:s");
        //$this->clear2bTerminalStatusCache($merchantid, $terminal_sn);
        $clause = ["merchantid" => $merchantid, "terminal_sn" => $terminal_sn];
        $ret    = DB::connection("db_2b_store")->table("t_2b_terminal")->where($clause)->update($info);
        return true;
    }

    /**
     * 读设备信息
     * 有terminal_sn，就读单个设备信息
     * 没有terminal_sn，就读商户所有设备
     * @param   string  merchantid   商户ID
     * @param   string  terminal_sn  终端设备号
     * @return  array   info         其他信息,array('terminal_no'=>xxx, 'terminal_ip'=>xxx, 'terminal_mac'=>xxx, 'orderid'=>xxx, 'start'=>xxx, 'playtime'=>xxx, 'payer_id'=>xxx)
     */
    public function get2bTerminal($merchantid, $terminal_sn = "")
    {
        if (!$merchantid) {
            return false;
        }
        $clause = array("merchantid" => $merchantid);
        if ($terminal_sn) {
            $clause['terminal_sn'] = $terminal_sn;
            $ret                   = DB::connection("db_2b_store")->table("t_2b_terminal")->where($clause)->first();
        } else {
            $ret = DB::connection("db_2b_store")->table("t_2b_terminal")->where($clause)->orderBy("terminal_no")->get();
        }
        return $ret;
    }

    /**
     * 根据mac地址读设备信息
     * @param   string  merchantid   商户ID
     * @param   string  terminal_mac  终端设备mac地址
     * @return  array   info         其他信息,array('terminal_no'=>xxx, 'terminal_ip'=>xxx, 'terminal_mac'=>xxx, 'orderid'=>xxx, 'start'=>xxx, 'playtime'=>xxx, 'payer_id'=>xxx)
     */
    public function get2bTerminalByMac($merchantid, $terminal_mac)
    {
        if (!$merchantid || !$terminal_mac) {
            return false;
        }
        $clause = array("merchantid" => $merchantid, "terminal_mac" => $terminal_mac);
        $ret    = DB::connection("db_2b_store")->table("t_2b_terminal")->where($clause)->first();
        return $ret;
    }

    /**
     * 根据orderId读取设备信息
     * @param   string  merchantid   商户ID
     * @param   []string  orderIds  订单ids
     * @return  array   info         其他信息,array('terminal_no'=>xxx, 'terminal_ip'=>xxx, 'terminal_mac'=>xxx, 'orderid'=>xxx, 'start'=>xxx, 'playtime'=>xxx, 'payer_id'=>xxx)
     */
    public function get2bTerminalByOrderIds($merchantid, $orderIds)
    {
        if (!$merchantid || !is_array($orderIds)) {
            return false;
        }

        $row = DB::connection("db_2b_store")->table("t_2b_terminal")->select('orderid', 'terminal_no', 'start', 'playtime', 'terminal_sn', 'merchantid', 'type', 'paychannel_orderid')->where("merchantid", $merchantid)->whereIn('orderid', $orderIds)->orderBy('ltime', 'desc')->get();
        return $row;
    }

    /**
     * 根据orderId更换终端编号
     * @param   string  merchantid   商户ID
     * @param   string  oldSn  旧编号
     * @param   string  newSn  新编号
     * @param   array  info  更新数据
     * @return  bool
     */
    public function swapTerminalSnByOrderId($merchantid, $oldSn, $newSn, $info)
    {
        if (!$merchantid || !$oldSn || !$newSn || !is_array($info) || empty($info)) {
            return false;
        }
        $ret1 = DB::connection("db_2b_store")->table("t_2b_terminal")->where("merchantid", $merchantid)->where('terminal_sn', $oldSn)->update(['orderid' => '', 'paychannel_orderid' => '', 'start' => 0, 'playtime' => 0]);
        $ret2 = DB::connection("db_2b_store")->table("t_2b_terminal")->where("merchantid", $merchantid)->where('terminal_sn', $newSn)->update($info);
        return $ret1 && $ret2;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             线 下 体 验 店 价 格 表                                         |
    |                                                                             |
    |     type=time 按时间收费，playtime是时间，appid=0                           |
    |     type=game 按游戏收费，playtime是0，appid是游戏id                        |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 添加价格信息
     * @param   string  merchantid  商户ID
     * @param   string  type  类型，time:按时长，或game:按游戏
     * @param   array   info  价格信息, ["appid" => xxx, "price"=>xxx, "playtime"=>xxx, ......]
     * @return  int
     */
    public function add2bSell($merchantid, $type, $info, $first = false)
    {
        if (!$merchantid || !$type || !$info || !is_array($info)) {
            return false;
        }
        $info['merchantid'] = $merchantid;
        $info['type']       = $type;
        $info['ts']         = date("Y-m-d H:i:s");
        if ($first) {
            $info['checked'] = 1;
            for ($i = 1; $i <= 4; $i++) {
                $infos[] = $info;
            }
            $info = $infos;
        }

        $ret = DB::connection("db_2b_store")->table("t_2b_sell")->insert($info);
        return $ret;
    }

    /**
     * 修改价格信息
     * @param   string  merchantid   商户ID
     * @param   string  type  类型，time:按时长，或game:按游戏
     * @param   array   info         其他信息, ["appid" => xxx, "price"=>xxx, "playtime"=>xxx, ......]
     * @return  bool
     */
    public function upd2bSell($merchantid, $productId, $info)
    {
        if (!$merchantid || !$productId || !is_numeric($productId) || !$info || !is_array($info)) {
            return false;
        }
        if (isset($info['merchantid'])) {
            // 暂时不允许修改，以后可开放
            return false;
        }
        $info['ts'] = date("Y-m-d H:i:s");
        $clause     = ["merchantid" => $merchantid, 'id' => $productId];
        $ret        = DB::connection("db_2b_store")->table("t_2b_sell")->where($clause)->update($info);
        return $ret;
    }

    /**
     * 读价格信息
     * @param   string  merchantid  商户ID
     * @param   string  type        类型，time:按时长，或game:按游戏
     * @param   array   clause      查询条件 ["playtime" => xxx, "appid" => xxx]
     * @return  array
     */
    public function get2bSell($merchantid, $type = "", $info = array())
    {
        if (!$merchantid) {
            return false;
        }
        $clause            = array();
        $clause            = ["merchantid" => $merchantid];
        $clause['checked'] = 1;
        if ($type) {
            $clause['type'] = $type;
        }
        foreach ($info as $field => $val) {
            if (isset($clause[$field])) {
                continue;
            }
            $clause[$field] = $val;
        }
        $ret = DB::connection("db_2b_store")->table("t_2b_sell")->where($clause);
        if ($type == "time") {
            $row = $ret->orderBy('playtime', 'asc')->get();
        } else {
            $row = $ret->get();
        }
        return $row;
    }

    /**
     * 读价格信息
     * @param   int  sellid  套餐id
     * @return  array
     */
    public function get2bSellById($sellid)
    {
        if (!$sellid) {
            return false;
        }
        $clause = ["id" => $sellid];
        $row    = DB::connection("db_2b_store")->table("t_2b_sell")->where($clause)->first();
        return $row;
    }

    /**
     * 设定默认价格
     * @param   string  merchantid  商户ID
     * @param   string  terminal_sn  终端编号
     * @param   int  productId   商品ID
     * @return  bool
     */
    public function setDefault2bSell($merchantid, $terminal_sn, $productId)
    {
        if (!$merchantid || !$terminal_sn || !$productId || !is_numeric($productId)) {
            return false;
        }
        $clause       = array();
        $clause       = ["merchantid" => $merchantid, 'terminal_sn' => $terminal_sn];
        $ret          = DB::connection("db_2b_store")->table("t_2b_sell")->where($clause)->where('id', "<>", $productId)->update(['checked' => 0]);
        $clause['id'] = $productId;
        $ret          = DB::connection("db_2b_store")->table("t_2b_sell")->where($clause)->update(['checked' => 1]);
        return $ret;
    }

    /**
     * 删除价格
     * @param   string  merchantid  商户ID
     * @param   string  productId   商品ID
     * @return  bool
     */
    public function del2bSell($merchantid, $productId)
    {
        if (!$merchantid || !$productId || !is_numeric($productId)) {
            // 主账号购买的游戏不能删
            return false;
        }
        $clause = ["merchantid" => $merchantid, 'id' => $productId];
        $ret    = DB::connection("db_2b_store")->table("t_2b_sell")->where($clause)->delete();
        return $ret;
    }
    /**
     * 获取商家的日账单分页数据
     * [get2bBillByDay description]
     * @param  [type] $merchantid [description]
     * @param  [type] $start      [description] 开始位置
     * @param  [type] $count      [description] 获取条数
     * @param  [type] $case       [description] 获取的条件月/日
     * @return [type]             [description]
     */
    public function get2bBillByDay($merchantid, $start, $count, $case)
    {
        if (!$merchantid) {
            return false;
        }
        if (!$start) {
            $start = 0;
        }
        if (!$count) {
            $count = 10;
        }
        $clause = [
            "merchantid" => $merchantid,
            "type"       => $case,
        ];
        $sumClause = [
            "merchantid" => $merchantid,
        ];
        $today    = date('Y-m-d 00:00:00', time());
        $toworrow = date('Y-m-d 00:00:00', strtotime("+1 day"));

        $ret = DB::connection("db_2b_store")->table("t_2b_day_bill")->where($clause)->orderBy('day', 'desc')->skip($start)->take($count)->get();
        // $countNum  = DB::connection("db_2b_store")->table("t_2b_day_bill")->where($clause)->count();
        $totalBill = DB::connection("db_2b_store")->table("t_2b_bill")->where($sumClause)->whereRaw('paytime >= "' . $today . '"')->whereRaw('paytime < "' . $toworrow . '"')->sum('merchant_fee');
        $result    = [
            'totalBill' => $totalBill,
            'data'      => $ret,
        ];
        return $result;
    }
    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |                               游 戏 表                                      |
    |                                                                             |
    |       不能修改，只能添加、删除                                              |
    |       免费的游戏保存在系统游戏库中，                                        |
    |       付费的游戏需要商家单独购买，保存在该表中，并且 terminal_sn = 0        |
    |       terminal_sn != 0 的表示已经分配给终端的                               |
    |       分配的时候，如果是收费app，要判断 terminal_sn = 0 的对应app是否存在   |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 添加店铺购买的游戏，或者给终端分配游戏
     * @param   string  merchantid  商户ID
     * @param   string  terminal_sn  终端设备号
     * @param   int     appid
     * @return  int
     */
    public function add2bGame($merchantid, $terminal_sn, $appids)
    {
        if (!$merchantid || !$appids) {
            return false;
        }
        $info = array();
        if (is_array($appids)) {
            for ($i = 0; $i < count($appids); $i++) {
                $info[] = ["merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "appid" => $appids[$i]];
            }
        } else {
            $info = ["merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "appid" => $appids];
        }
        $ret = DB::connection("db_2b_store")->table("t_2b_terminal_games")->insertIgnore($info);
        return $ret;
    }

    /**
     * 批量添加店铺购买的游戏，或者给终端分配游戏
     * @param   string  merchantid  商户ID
     * @param   string  terminal_sn  终端设备号
     * @param   int     appid
     * @return  int
     */
    public function add2bGamesForTerminals($merchantid, $terminals, $appids)
    {
        if (!$merchantid || !is_array($appids) || !is_array($terminals)) {
            return false;
        }

        $info = array();

        for ($i = 0; $i < count($terminals); $i++) {
            for ($a = 0; $a < count($appids); $a++) {
                $info[] = ["merchantid" => $merchantid, "terminal_sn" => $terminals[$i], "appid" => $appids[$a]];
            }
        }

        $ret = DB::connection("db_2b_store")->table("t_2b_terminal_games")->insertIgnore($info);
        return $ret;
    }

    /**
     * 获取购买的，或分配的游戏
     * @param   string  merchantid  商户ID
     * @param   string  terminal_sn 终端设备号
     * @param   array   info        查询条件 ["playtime" => xxx, "appid" => xxx]
     * @return  array
     */
    public function get2bGame($merchantid, $terminal_sn, $info = array())
    {
        if (!$merchantid) {
            return false;
        }
        if (!is_array($clause)) {
            $clause = array();
        }
        $clause = ["merchantid" => $merchantid];
        $clause = ["terminal_sn" => $terminal_sn];
        foreach ($info as $field => $val) {
            if (!isset($clause[$field])) {
                continue;
            }
            $clause[$field] = $val;
        }
        $ret = DB::connection("db_2b_store")->table("t_2b_terminal_games")->where($clause)->get();
        return $ret;
    }

    /**
     * 删除分配的游戏，主账号购买的游戏列表不能删除，所以 $terminal_sn != 0
     * @param   string  merchantid  商户ID
     * @param   string  type        类型，time:按时长，或game:按游戏
     * @param   array   clause      查询条件 ["playtime" => xxx, "appid" => xxx]
     * @return  array
     */
    public function del2bGame($merchantid, $terminal_sn, $appid)
    {
        if (!$merchantid || !$terminal_sn || !$appid) {
            // 主账号购买的游戏不能删
            return false;
        }
        $clause = ["merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "appid" => $appid];
        $ret    = DB::connection("db_2b_store")->table("t_2b_terminal_games")->where($clause)->delete();
        return $ret;
    }

    /**
     * 修改游戏信息
     * @param   string  merchantid  商户ID
     * @param   string  terminal_sn  终端设备号
     * @param   int     appid
     * @param   array   info      修改信息
     * @return  array
     */
    public function upd2bGame($merchantid, $terminal_sn, $appid, $info)
    {
        if (!$merchantid || !$terminal_sn || !$appid || !is_array($info) || !$info) {
            // 主账号购买的游戏不能删
            return false;
        }
        $clause = ["merchantid" => $merchantid, "terminal_sn" => $terminal_sn, "appid" => $appid];

        try {
            $ret = DB::connection("db_2b_store")->table("t_2b_terminal_games")->where($clause)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 添加游玩次数
     * @param   int  merchantid  商户ID
     * @param   int  apoid  商户ID
     * @return  array[int]
     */
    public function addPlay($merchantid, $appid)
    {
        $ret1 = Vredis::hincrby('game_play_stat', $merchantid, $appid, 1);
        $ret2 = Vredis::hincrby('game_play_stat', "2Ball", $appid, 1);
        return $ret1 && $ret2;
    }

    /**
     * 获取 默认产品
     * @return  array[int]
     */
    public function getDefaultProduct()
    {
        $res = Vredis::get("tob_defaultproduct", 'pub');
        $out = [];
        if (!$res) {
            $out = [30, 600, '30元体验10分钟', 1];
        } else {
            $out = explode("|", $res);
        }
        return $out;
    }

    /**
     * 设定 默认产品
     * @return  bool
     */
    public function setDefaultProduct($arr)
    {
        $res = Vredis::get("tob_defaultproduct", 'pub');
        $str = implode('|', $arr);
        $ret = Vredis::set("tob_defaultproduct", 'pub', $str);
        return $ret;

    }

    /**
     * 获取官网banner
     * @param   int  id  默认条数
     * @return  mix
     */
    public function getWwwBanners($num = 10)
    {
        $arr = DB::connection('db_2b_store')->table('t_2b_banner')->orderBy('weight', 'desc')->take($num)->get();
        return $arr;
    }

    /**
     * 获取官网banner
     * @param   int  id  bannerId
     * @param   array    info
     * @return  bool
     */
    public function updateWwwBanner($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection("db_2b_store")->table("t_2b_banner")->where("id", $id)->update($info);
        } else {
            $retId = DB::connection("db_2b_store")->table("t_2b_banner")->insertGetId($info);
            if ($retId) {
                $ret = DB::connection("db_2b_store")->table("t_2b_banner")->where("id", $retId)->update(['weight' => $retId]);
            } else {
                $ret = false;
            }
        }
        return $ret;
    }

    /**
     * 交换banner weight
     * @param   int  drag
     * @param   int   drop
     * @return  bool
     */
    public function wwwBannerWeight($drag, $drop)
    {
        $rowDrag = DB::connection("db_2b_store")->table("t_2b_banner")->select('weight')->where('id', $drag)->first();
        $rowDrop = DB::connection("db_2b_store")->table("t_2b_banner")->select('weight')->where('id', $drop)->first();

        if (!$rowDrag || !$rowDrop) {
            return false;
        }
        $ret1 = DB::connection("db_2b_store")->table("t_2b_banner")->where("id", $drag)->update(['weight' => $rowDrop['weight']]);
        $ret2 = DB::connection("db_2b_store")->table("t_2b_banner")->where("id", $drop)->update(['weight' => $rowDrag['weight']]);

        return $ret1 && $ret2;
    }

    /**
     * 删除官网banner
     * @param   int  id  bannerId
     * @return  bool
     */
    public function delWwwBanner($id)
    {
        $ret = DB::connection('db_2b_store')->table('t_2b_banner')->where('id', $id)->delete();
        return $ret;
    }

    /**
     * 获取默认游戏ID
     * @return  []int gameids
     */
    public function getDefaultGame()
    {
        $rows = DB::connection('db_webgame')->table('t_webgame')->select('appid')->where("tob_in", '2')->get();
        $ids  = [];
        if (is_array($rows) && !empty($rows)) {
            foreach ($rows as $game) {
                $ids[] = $game['appid'];
            }
        }
        return $ids;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |                    店 员 订 单 表                                           |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 添加订单
     * @param   string  merchantid  商户ID
     * @param   array   info        订单信息
     * @return  bool
     */
    public function add2bBill($orderid, $info)
    {
        if (!$orderid || !$info || !is_array($info)) {
            return false;
        }
        $info['orderid'] = $orderid;
        $ret             = DB::connection("db_2b_store")->table("t_2b_bill")->insert($info);
        return $ret;
    }

    /**
     * 修改订单
     * @param   string  merchantid  商户ID
     * @param   array   info        订单信息
     * @return  bool
     */
    public function upd2bBill($orderid, $info)
    {
        if (!$orderid || !$info || !is_array($info)) {
            return false;
        }
        $clause['orderid'] = $orderid;
        $ret               = DB::connection("db_2b_store")->table("t_2b_bill")->where($clause)->update($info);
        return $ret;
    }

    /**
     * 查询订单
     * @param   string  orderid  订单
     * @return  array
     */
    public function get2bBillByOrderid($orderid)
    {
        if (!$orderid) {
            return false;
        }
        $clause = ["orderid" => $orderid];
        $row    = DB::connection("db_2b_store")->table("t_2b_bill")->where($clause)->first();
        return $row;
    }

    /**
     * 查询订单用户显示交易记录
     * @param   string  merchantid  商户id
     * @param   int  start  开始
     * @param   num  merchantid  条数
     * @return  array
     */
    public function get2bBillByMerchantId($merchantid, $tp = 1, $start = 0, $num = 10, $where)
    {
        if (!$merchantid) {
            return false;
        }
        $raw = DB::connection("db_2b_store")->table("t_2b_bill")->select('terminal_no', 'status', 'orderid', 'paytime', 'pay_channel', 'appid', 'pay_rmb', 'merchant_fee', 'playtime')->where("merchantid", $merchantid);
        if ($where && is_array($where)) {
            $raw->where($where);
        }
        if ($tp == 1) {
            $raw->where('paytype', '<>', 9);
        } else {
            $raw->where('paytype', 9);
        }

        $rows = $raw->skip($start)->take($num)->get();
        return $rows;
    }

    /**
     * 查询订单用户显示交易记录
     * @param   string  merchantid  商户id
     * @param   int  start  开始
     * @param   num  merchantid  条数
     * @return  array
     */
    public function get2bBillCountByMerchantId($merchantid, $tp = 1, $where)
    {
        if (!$merchantid) {
            return false;
        }
        $raw = DB::connection("db_2b_store")->table("t_2b_bill")->where("merchantid", $merchantid);
        if ($where && is_array($where)) {
            $raw->where($where);
        }
        if ($tp == 1) {
            $raw->where('paytype', '<>', 9);
        } else {
            $raw->where('paytype', 9);
        }

        $count = $raw->count();
        return $count;
    }

    /**
     * 查询订单，用于对账
     * @param   string  orderid  订单
     * @return  array
     */
    public function get2bBill4Check($start, $end)
    {
        if (!$start || !$end || $start >= $end) {
            return false;
        }
        $row = DB::connection("db_2b_store")->table("t_2b_bill")->select('orderid', 'paychannel_orderid', 'paycenter_orderid', 'total_rmb', 'pay_rmb', 'cp_fee', 'plat_fee', 'merchant_fee', 'merchantid', 'paytype')->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("paytype", "<", 9)->orderBy('orderid', 'asc')->get();
        return $row;
    }

    /**
     * 统计订单信息，用于对账
     * @param   array  info
     * @return  bool
     */
    public function get2bBillSumBrief4Check($start, $end)
    {
        if (!$start || !$end || $start >= $end) {
            return false;
        }
        $row = DB::connection("db_2b_store")->table("t_2b_bill")->select(DB::raw('SUM(total_rmb) as total_amount'), DB::raw('SUM(pay_rmb) as pay_amount'), DB::raw('SUM(cp_fee) as cp_fee'), DB::raw('SUM(plat_fee) as plat_fee'), DB::raw('SUM(merchant_fee) as merchant_fee'), DB::raw('COUNT(orderid) as total_count'))->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("paytype", "<", 9)->first();
        if (!is_array($row)) {
            return false;
        }
        $row['total_amount'] = $row['total_amount'] === null ? 0 : $row['total_amount'];
        $row['pay_amount']   = $row['pay_amount'] === null ? 0 : $row['pay_amount'];
        $row['cp_fee']       = $row['cp_fee'] === null ? 0 : $row['cp_fee'];
        $row['plat_fee']     = $row['plat_fee'] === null ? 0 : $row['plat_fee'];
        $row['merchant_fee'] = $row['merchant_fee'] === null ? 0 : $row['merchant_fee'];
        return $row;
    }

    /**
     * 统计订单信息，用于对账
     * @param   array  info
     * @return  bool
     */
    public function get2bBillMerchantSum($start, $end)
    {
        if (!$start || !$end || $start >= $end) {
            return false;
        }
        $result = array();
        $row    = DB::connection("db_2b_store")->table("t_2b_bill")->select("merchantid", DB::raw('SUM(total_rmb) as total_amount'), DB::raw('SUM(pay_rmb) as pay_amount'), DB::raw('SUM(cp_fee) as cp_fee'), DB::raw('SUM(plat_fee) as plat_fee'), DB::raw('SUM(merchant_fee) as merchant_fee'))->where("paytime", ">=", $start)->where("paytime", "<=", $end)->where("paytype", "<", 9)->groupBy('merchantid')->get();
        if (!is_array($row)) {
            return false;
        }
        for ($i = 0; $i < count($row); $i++) {
            $merchantid          = $row[$i]['merchantid'];
            $result[$merchantid] = $row[$i];
        }
        return $result;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             日 订 单 统 计 表                                               |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 插入某日的账单
     * @param   string  merchantid  商户id
     * @param   string  day         日期
     * @param   array   info    订单信息
     * @return  bool
     */
    public function addOneDayBill($merchantid, $day, $info)
    {
        if (!$merchantid || !$day || !$info || !is_array($info)) {
            return false;
        }
        $info['merchantid'] = $merchantid;
        $info['day']        = $day;
        try {
            $ret = DB::connection("db_2b_store")->table("t_2b_day_bill")->insertUpdate($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 统计月数据
     * @param   string  start   起始日期
     * @param   string  end     结束日期
     * @return  array
     */
    public function statMonthBill($start, $end)
    {
        if (!$start || !$end) {
            return false;
        }
        try {
            $row = DB::connection("db_2b_store")->table("t_2b_day_bill")->select("merchantid", DB::raw('SUM(total_amount) as total_amount'), DB::raw('SUM(pay_amount) as pay_amount'), DB::raw('SUM(net_income) as net_income'))->where("day", ">=", $start)->where("day", "<=", $end)->where("type", 0)->groupBy('merchantid')->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        $rows = [];
        if (is_array($row) && $row) {
            for ($i = 0; $i < count($row); $i++) {
                $rows[$row[$i]['merchantid']] = $row[$i];
            }
        }
        return $rows;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |                   启 动 统 计                                               |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 写某日的统计
     * @param   string  merchantid  商户id
     * @param   string  day         日期
     * @param   int     appid       appid
     * @param   string  type        统计类型 启动:start，时长:long
     * @param   int     num         时长，单位 秒
     * @return  bool
     */
    public function setDayAppStat($merchantid, $day, $appid, $type, $num = 1)
    {
        if (!$merchantid || !$day || strlen($day) != 8 || !$appid || !$type || !$num) {
            return false;
        }

        $month = date("Ym", strtotime($day));
        if ($type == "start") {
            $info = ["merchantid" => $merchantid, "day" => $day, "month" => $month, "appid" => $appid, 'start' => DB::raw("start + {$num}")];
        } else if ($type == "long") {
            $info = ["merchantid" => $merchantid, "day" => $day, "month" => $month, "appid" => $appid, 'playlong' => DB::raw("playlong + {$num}")];
        } else {
            return false;
        }
        try {
            $ret = DB::connection("db_2b_store")->table("t_2b_stat_day")->insertUpdate($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return true;
    }

    /**
     * 写月统计
     * @param   string  merchantid  商户id
     * @param   string  month         日期
     * @param   int     appid       appid
     * @param   string  type        统计类型 启动:start，时长:long
     * @param   int     num         时长，单位 秒
     * @return  bool
     */
    public function setMonthAppStat($merchantid, $month, $appid, $num, $long)
    {
        if (!$merchantid || !$month || !$appid) {
            return false;
        }

        $info = ["merchantid" => $merchantid, "day" => $month, "month" => $month, "appid" => $appid, 'start' => $num, "playlong" => $long, "type" => 1];
        try {
            $ret = DB::connection("db_2b_store")->table("t_2b_stat_day")->insertUpdate($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return true;
    }

    /**
     * 读某日的统计
     * @param   string  merchantid  商户id
     * @param   string  day         日期
     * @return  array
     */
    public function getDayAppStat($merchantid, $day)
    {
        if (!$merchantid || !$day) {
            return false;
        }

        $clause = ["merchantid" => $merchantid, "day" => $day];
        try {
            $ret = DB::connection("db_2b_store")->table("t_2b_stat_day")->where($clause)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取商家的游戏数据总和
     * [getDayAppSum description]
     * @param  [type] $merchantid [description]
     * @return [type]             [description]
     */
    public function getDayAppSum($merchantid)
    {
        if (!$merchantid) {
            return false;
        }

        $clause = [
            "merchantid" => $merchantid,
            "day"        => intval(date('Ymd', time())),
            "type"       => 0,
        ];
        $case = [
            "merchantid" => $merchantid,
        ];
        $today    = date('Y-m-d 00:00:00', time());
        $toworrow = date('Y-m-d 00:00:00', strtotime("+1 day"));

        try {
            $ret['start']    = DB::connection("db_2b_store")->table("t_2b_stat_day")->where($clause)->sum('start');
            $ret['playlong'] = DB::connection("db_2b_store")->table("t_2b_stat_day")->where($clause)->sum('playlong');
            $ret['money']    = DB::connection("db_2b_store")->table("t_2b_bill")->where($case)->whereRaw('paytime >= "' . $today . '"')->whereRaw('paytime < "' . $toworrow . '"')->sum('merchant_fee');
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取商家的游戏数据
     * [getDayAppDate description]
     * @param  [type] $merchantid [description]
     * @return [type]             [description]
     */
    public function getDayAppDate($merchantid, $start, $count, $case)
    {
        if (!$merchantid) {
            return false;
        }
        if (!$start) {
            $start = 0;
        }
        if (!$count) {
            $count = 10;
        }

        $clause = [
            "merchantid" => $merchantid,
            "type"       => $case,
        ];
        try {
            $ret = DB::connection("db_2b_store")->table("t_2b_stat_day")->where($clause)->orderBy('ltime', 'desc')->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        $result = [
            'data' => $ret,
        ];
        return $result;
    }

    /**
     * 商家月统计数据
     * [getDayAppDate description]
     * @param  [type] $merchantid [description]
     * @return [type]             [description]
     */
    public function statMonthData($start, $end)
    {
        if (!$start || !$end) {
            return false;
        }

        try {
            $row = DB::connection("db_2b_store")->table("t_2b_stat_day")->select("merchantid", "appid", DB::raw('SUM(start) as start'), DB::raw('SUM(playlong) as playlong'))->where("day", ">=", $start)->where("day", "<=", $end)->where("type", 0)->groupBy('merchantid')->groupBy('appid')->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $row;
    }

    /**
     * 读店铺内启动次数统计
     * 统计到redis内，定时跑到表中
     * @param   int     merchantid  商户ID
     * @return  bool
     */
    public function getStatMerchantAppStart($merchantid)
    {
        if (!$merchantid) {
            return false;
        }
        $rows = Vredis::hgetall('game_play_stat', $merchantid);
        return $rows;
    }

    /**
     * 游戏启动次数统计，所有店铺
     * 统计到redis内，定时跑到表中
     * @return  bool
     */
    public function getStat2BAppStart()
    {
        $rows = Vredis::hgetall('game_play_stat', "2Ball");
        return $rows;
    }

}
