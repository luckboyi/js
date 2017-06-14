<?php

// 网页游戏相关

namespace App\Http\Controllers;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\CookieModel;
use App\Models\MiddleModel;
use App\Models\OpenidModel;
use App\Models\OperateModel;
use App\Models\SupportModel;
use App\Models\WebgameLogicModel; // 使用open的Helper
use App\Models\WebgameModel;
use Config;
use Helper\AccountCenter as Account;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Http\Request;
use Input;
use Redirect;

// 获取recommend推荐位的model
use \App\Models\DataCenterStatModel;

class WebgameController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:0:webgame", ['only' => ["start"]]);
        $this->middleware("vrauth:0:webgame", ['only' => ["servers"]]);
    }

    /**
     * 给客户端的输出
     * @param $code
     * @param null $data
     * @return mixed
     */
    protected function outputForPc($code, $data = null)
    {
        $passport = new MiddleModel();
        if ($code == 0 && $data) {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("errcode" => $code, "data" => $data, "msg" => $msg));
        } else {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("errcode" => $code, "msg" => $msg));
        }
    }

    protected function output($code, $data = null)
    {
        $passport = new MiddleModel();
        if ($code == 0 && $data) {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("code" => $code, "data" => $data, "msg" => $msg));
        } else {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("code" => $code, "msg" => $msg));
        }
    }

    /**
     * 页游选服页面
     */
    public function servers(Request $request, $appid)
    {
        $islogin  = false;
        $userInfo = $request->userinfo;
        if (isset($userInfo['uid'])) {
            $islogin = true;
            $uid     = $userInfo['uid'];
            $token   = $userInfo['token'];
            $nick    = $userInfo['nick'];
            $account = $userInfo['account'];
        } else {
            $uid = $token = $nick = $account = '';
        }
        $appid = intval($appid);

        $now          = time();
        $webgameModel = new WebgameModel;
        $gameinfo     = $webgameModel->getOneGameInfo($appid);
        if (!$gameinfo || !is_array($gameinfo)) {
            $gameinfo = array();
        }
        if (!isset($gameinfo['uid'])) {
            $gameinfo['uid'] = 0;
        }

        /**
         * 是否有高级权限用户
         */
        $isSuperUser = $webgameModel->isTestAccount($uid, $gameinfo['uid']);

        /**
         * 游戏还未发布，普通用户不能进
         */
        if ($gameinfo['stat'] != 0) {
            if (!$uid || !$isSuperUser) {
                $jump = "http://www.vronline.com/webgame";
                return Redirect::to($jump);
            }
        }

        /**
         * 推荐服
         */
        $recommend = $webgameModel->recommendServer($appid);
        if (!$recommend || !is_array($recommend)) {
            $recommend = array();
        }

        /**
         * 我玩过的服
         */
        $lastserverid = 0;
        $last         = array();
        if ($islogin) {
            $login     = true;
            $myservers = $webgameModel->getGameServerLogByAppid($uid, $appid);
            if (count($myservers) > 0) {
                $last         = $myservers[0];
                $lastserverid = $last['serverid'];
            }
        } else {
            $login     = false;
            $myservers = array();
        }
        if (!$myservers || !is_array($myservers)) {
            $myservers = array();
        }
        if (!$last || !$lastserverid) {
            if ($recommend && count($recommend) > 0) {
                $last         = $recommend[0];
                $lastserverid = $last['serverid'];
            }
        }

        /**
         * 所有服
         */
        $webgameLogic = new WebgameLogicModel;
        $allservers   = $webgameLogic->getAllServer($appid);
        if (!$allservers || !is_array($allservers)) {
            $allservers = array();
        }
        $lastsid = 0;
        foreach ($allservers as $key => $arr_detail) {
            if ($arr_detail['start'] > $now) {
                if (!$isSuperUser) {
                    // 如果是有高级权限用户，可以看到所有服务器
                    unset($allservers[$key]);
                }
                continue;
            }
            $lastsid = $allservers[$key]['serverid'];
        }
        if (!$lastserverid && $lastsid) {
            $lastserverid = $lastsid;
        }

        $images = ImageHelper::url("webgame", $appid, $gameinfo['img_version'], $gameinfo['img_slider'], false);

        return view('website.webgame.servers', ['appid' => $appid, "nick" => $nick, "account" => $account, 'islogin' => $login, 'gameinfo' => $gameinfo, 'recommend' => $recommend, 'myservers' => $myservers, 'allservers' => $allservers, "images" => $images, "lastserverid" => $lastserverid]);
    }
    /**
     * 开始游戏
     * 如果有serverid，并且是正常的大于0的整数，进对应服务器，如果是0，进最新服，如果是其他，先进我的服，再进推荐服
     * @param   int     appid   游戏appid
     * @param   int     serverid    服务器ID，可传0，则进最新服
     */
    public function start(Request $request, $appid, $serverid)
    {
        $appid = intval($appid);
        //$serverid = intval($serverid);

        if (!$appid) {
            $jump = "http://www.vronline.com/webgame";
            return Redirect::to($jump);
        }

        $uid     = CookieModel::getCookie("uid");
        $token   = CookieModel::getCookie("token");
        $vrkey   = CookieModel::getCookie("token");
        $nick    = CookieModel::getCookie("nick");
        $account = CookieModel::getCookie("account");

        /**
         * 判断登录状态
         */
        $webgameLogic = new WebgameLogicModel;
        $islogin      = $webgameLogic->checkLogin(true);
        if (!$islogin) {
            return view('login');
        }
        $login = true;

        $openid   = OpenidModel::getOpenid($appid, $uid);
        $checkuid = OpenidModel::getUid($openid);
        if (!$checkuid || !isset($checkuid['uid']) || $checkuid['uid'] != $uid) {
            UdpLog::save2("openid/fail", array("function" => "start", "result" => "false", "uid" => $uid, "appid" => $appid, "openid" => $openid, "checkuid" => $checkuid), __METHOD__ . "[" . __LINE__ . "]");
            $jump = "/servers/{$appid}?err=openiderr";
            return Redirect::to($jump);
        }

        /**
         * 获取app信息，拿到appkey
         */
        $uc_appid  = Config::get("common.uc_appid");
        $uc_appkey = Config::get("common.uc_appkey");

        $accountModel = new Account($uc_appid, $uc_appkey);
        $appinfo      = $accountModel->getAppInfo($appid);
        if (!$appinfo || !is_array($appinfo)) {
            // 跳转到选服页面
            $jump = "http://www.vronline.com/webgame";
            return Redirect::to($jump);
        }
        $appkey = isset($appinfo['appkey']) ? $appinfo['appkey'] : "";

        /**
         * 获取服务器信息
         */
        $webgame = new WebgameModel;

        if ($serverid && is_numeric($serverid) && $serverid > 0) { // 进指定服
        } else if (!$serverid) {
            // 进最新服
            $serverid = $webgame->maxServeridByAppid($appid);
        } else {
            // 先判断有没有我玩过的服，如果有，进玩过的，没有进推荐服，如果再没有进最新服
            $serverid = $webgame->getLastGameServerid($uid, $appid);
            if (!$serverid) {
                $recommend = $webgame->recommendServer($appid);
                if ($recommend && is_array($recommend)) {
                    $newest = end($recommend);
                    if ($newest && is_array($newest)) {
                        $serverid = $newest['serverid'];
                    }
                }
            }
            if (!$serverid) {
                $serverid = $webgame->maxServeridByAppid($appid);
            }
        }

        $serverinfo = $webgame->getOneServer($appid, $serverid);
        if (!$serverinfo) {
            // 跳转到选服页面
            $jump = "servers/{$appid}?err=noserverinfo";
            return Redirect::to($jump);
        }

        $gameinfo = $webgame->getOneGameInfo($appid);
        if ($gameinfo && isset($gameinfo['name'])) {
            $gamename = $gameinfo['name'];
        }

        /**
         * 游戏还未发布，普通用户不能进
         */
        if ($gameinfo['stat'] != 0) {
            if (!$uid || !$webgame->isTestAccount($uid, $gameinfo['uid'])) {
                $jump = "http://www.vronline.com/webgame";
                return Redirect::to($jump);
            }
        }

        /**
         * 服务器还没开，普通用户不能进
         */
        if ($serverinfo['start'] > time()) {
            if (!$uid || !$webgame->isTestAccount($uid, $gameinfo['uid'])) {
                $jump = "http://www.vronline.com/webgame/{$appid}";
                return Redirect::to($jump);
            }
        }

        /**
         * 服务器正在维护，普通用户不能进
         */
        if ($serverinfo['status'] == 9) {
            if (!$uid || !$webgame->isTestAccount($uid, $gameinfo['uid'])) {
                $msg = "服务器正在维护";
                return view('webgame.gameError', ['appid' => $appid, 'serverid' => $serverid, "servername" => $serverinfo['name'], "nick" => $nick, "uid" => $uid, "account" => $account, 'islogin' => $login, "msg" => $msg]);
            }
        }

        /**
         * 登记玩游戏的用户列表
         * 以后改成只添加，不判断，不修改数据库，数据库放后台修改
         */
        $supportModel = new SupportModel;
        $exists       = $supportModel->isExists($uid, $appid, "webgame", 'play');
        if (!$exists) {
            $supportModel->support($uid, $appid, "webgame", 'play');
            $playcount = $supportModel->getCount($uid, $appid, "webgame", 'play');
            if (isset($gameinfo['play']) && $gameinfo['play'] < $playcount) {
                $webgame->updGameInfo($appid, array("play" => $playcount));
            }
        }

        /**
         * 记录最后进入的服
         */
        $webgame->setLastGameServerid($uid, $appid, $serverid);

        /**
         * 添加到我的登录记录
         */
        $info = array("appname" => $gamename, "servername" => $serverinfo['name']);
        $add  = $webgame->addGameLog($uid, $appid, $serverid, $info);

        $isadult = 0;

        $app_path   = app_path();
        $class_file = $app_path . "/Models/WebGame/Webgame{$appid}Class.php";
        if (file_exists($class_file)) {
            $class   = "App\Models\WebGame\Webgame{$appid}Class";
            $gameurl = $class::loginGame($serverid, $appkey, $uid, $openid, $isadult, array(), $appinfo, $serverinfo);
            if (!$gameurl) {
                $msg = "游戏加载失败，请从服务器列表页面重新进游戏";
                return view('webgame.gameError', ['appid' => $appid, 'serverid' => $serverid, "servername" => $serverinfo['name'], "nick" => $nick, "uid" => $uid, "account" => $account, 'islogin' => $login, "msg" => $msg]);
            }
        } else {

            $domain  = isset($serverinfo['domain']) ? $serverinfo['domain'] : "";
            $params  = array("serverid" => $serverid, "openid" => $openid, "appid" => $appid, "vrkey" => $vrkey, "isadult" => $isadult, "seq" => uniqid("", true), "t" => microtime(true));
            $request = array();
            Library::encrypt($params, $appkey, $request);
            $query_string = http_build_query($request);

            /**
             * 加载游戏的iframe地址
             */
            if (substr($domain, 0, 7) == "http://" || substr($domain, 0, 8) == "https://") {
                $gameurl = "{$domain}?{$query_string}";
            } else {
                $gameurl = "http://{$domain}?{$query_string}";
            }
        }

        /**
         * 发送统计
         */
        $properties = [
            "_gametype" => "webgame",
            "_gameid"   => $appid,
            "_sid"      => $serverid,
            "_systime"  => time(),
            "openid"    => $openid,
            "isall"     => 1, // 表示日志数据是全的，不需要再从数据库补数据
        ];
        DataCenterStatModel::stat("vrgame", "login", $uid, $properties);

        /**
         * 发送广告统计
         */
        $properties = [
            "gameid"   => $appid,
            "serverid" => $serverid,
            "openid"   => $openid,
            "type"     => "webgame",
            "isall"    => 1, // 表示日志数据是全的，不需要再从数据库补数据
        ];
        DataCenterStatModel::stat("vradv", "login", $uid, $properties);

        return view('webgame.game', ['appid' => $appid, 'serverid' => $serverid, "servername" => $serverinfo['name'], "nick" => $nick, "uid" => $uid, "account" => $account, 'islogin' => $login, "gameurl" => $gameurl]);
    }

    /*
     * 设置cookie的页面
     */
    public function setLoginCookie(Request $request)
    {
        $cookie  = new CookieModel();
        $uid     = $request->input('uid');
        $account = $request->input('account');
        $nick    = $request->input('nick');
        $token   = $request->input('token');
        $face    = $request->input('face');
        if ($uid == '' || $token == '') {
            $code = 1;
            return $this->output($code);
        }

        $params = array('uid' => $uid, 'token' => $token, 'account' => $account, 'nick' => $nick, 'face' => $face);
        CookieModel::setCookieArr($params);
        return $this->output(0);
    }

    /*
     * 设置cookie的页面
     */
    public function setClientCookie(Request $request)
    {
        $key  = trim($request->input('key'));
        $val  = trim($request->input('val'));
        $sign = trim($request->input('sign'));
        if (!in_array($key, array("did")) || strlen($val) == 0) {
            return Library::output(2001);
        }

        unset($_GET['//setclient']);
        $client_secret = env("CLIENT_SECRET");
        $check         = Library::encrypt($_GET, $client_secret);
        if ($check != $sign) {
            return Library::output(2002);
        }

        $time = 14400 * 365 * 10;
        $ret  = CookieModel::setCookie($key, $val, $time);
        return $this->output(0);
    }

    /*
     * 查看cookie
     */
    public function getCookie(Request $request)
    {
        $cookie = new CookieModel();
        $token  = CookieModel::getCookie('uid');
        echo $token;
    }

    public function delLoginCookie(Request $request)
    {
        $route = $request->input('route');

        CookieModel::logOut();

        if ($route == '' || $route == 0) {
            $code = 0;
            return $this->output($code);
        }

        return Redirect::to($route);
    }
    /*
     * 添加游戏礼包领取码
     */
    public function addGiftCode(Request $request)
    {
        $webGame  = new WebgameModel();
        $data     = array(20000021, 20000121, 20000221, 20000321, 20000421, 20000521, 20000621, 20000721, 20000821, 20000921);
        $gid      = 1001;
        $appid    = 1002;
        $serverid = 1;
//        echo '<pre>';
        //        print_r($data);
        $ret = $webGame->addGiftCodes($gid, $appid, $serverid, $data);
    }

    /**
     *  礼包的列表
     * @param Request $request
     * @param $appId
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function giftList(Request $request)
    {
        $uid     = CookieModel::getCookie('uid');
        $islogin = true;
        /**
         * 判断登录状态
         */
        $webgameLogic = new WebgameLogicModel;
        $islogin      = $webgameLogic->checkLogin();

        if (!$islogin) {
            $islogin = false;
        }
        $webGame     = new WebgameModel();
        $getGiftList = array();
        $getAllGift  = $webGame->getAllGift('', '');

        foreach ($getAllGift as $k => $v) {
            $gid                        = $v['gid'];
            $getGiftList[$k]['codeNum'] = $webGame->getGiftNum($gid);

            $getGameInfo = $webGame->getOneGameInfo($v['appid']);
            // if ($getGameInfo['hasgift'] == 0) {
            //     continue;
            // }
            $getGiftList[$k]['image']    = ImageHelper::url('webgame', $v['appid'], $getGameInfo['img_version'], $getGameInfo['img_slider'], false);
            $getGiftList[$k]['gid']      = $gid;
            $getGiftList[$k]['appid']    = $v['appid'];
            $getGiftList[$k]['serverid'] = $v['serverid'];
            $getGiftList[$k]['name']     = $v['name'];
            $getGiftList[$k]['content']  = $v['content'];
            $getGiftList[$k]['desc']     = $v['desc'];
            $getGiftList[$k]['gameName'] = $getGameInfo['name'];
            $getGiftList[$k]['uid']      = $uid;
            if ($getGiftList[$k]['codeNum'] == 0) {
                unset($getGiftList[$k]);
            }
        }

        if (strrpos($request->url(), 'www.vronline.com')) {
            return view('website/webgame/cardPackCenter', ['islogin' => $islogin, 'giftListData' => $getGiftList, 'uid' => $uid]);
        }
        return view('webgame/cardPackCenter', ['islogin' => $islogin, 'giftListData' => $getGiftList, 'uid' => $uid]);
    }

    /**
     *
     * @param Request $request
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getGameLog(Request $request)
    {
        $uid = CookieModel::getCookie('uid');
        $num = 3;
        if (!$uid) {
            return false;
        }
        $webGame        = new WebgameModel();
        $getGameLogList = $webGame->getGameLog($uid, $num);
        return view('webgame/index', ['data' => $getGameLogList]);
    }

    /**
     * 用户获取礼包cCode的逻辑
     * @param Request $request
     * @return string
     */
    public function getGiftCode(Request $request)
    {
        $webGame  = new WebgameModel();
        $code     = 0;
        $uid      = $request->input('userid');
        $gid      = $request->input('gid');
        $appname  = $request->input('appname');
        $appid    = $request->input('appid');
        $serverId = $request->input('serverid');

        /**
         * 判断登录状态
         */
        $webgameLogic = new WebgameLogicModel;
        $islogin      = $webgameLogic->checkLogin();

        if (!$islogin) {
            $code = 1301;
            return $this->output($code);
        }

        if ($uid == '' || $gid == '' || $appname == '') {
            $code = 1;
            return $this->output($code);
        }
        $gid = $request->input('gid');

//        $redis->delete($verKey);die;
        //判断礼包是否可以开始领取（服务器当前时间 > 开始时间 或 服务器当前时间 < 当前时间方可领取）
        $tmRet = $webGame->getGiftTm($gid);
        if (!$tmRet) {
            $code = 2306;
            return $this->output($code);
        }
        $result = $webGame->getGiftRedis($uid, $gid);
        if ($result) {
            $code          = 0;
            $date['vCode'] = $result;
            return $this->output($code, $date);
        }
        $data['vCode'] = $webGame->getOneGiftCode($gid);
        if ($data['vCode'] == '') {
            $code = 2302;
            return $this->output($code);
        }

        //获取礼包详细信息
        $giftCodeInfo = $webGame->getGiftInfoById($gid);

        //获取服务器name
        $serverInfo = $webGame->getOneServer($giftCodeInfo[0]['appid'], $giftCodeInfo[0]['serverid']);
        $serverName = $serverInfo['name'] == "" ? '' : $serverInfo['name'];
        //写入领取记录
        $info = array(
            'uid'        => $uid,
            'code'       => $data['vCode'],
            'gid'        => $gid,
            'appid'      => $giftCodeInfo[0]['appid'],
            'appname'    => $appname,
            'serverid'   => $giftCodeInfo[0]['serverid'],
            'servername' => $serverName,
            'giftname'   => $giftCodeInfo[0]['name'],

        );
        //写入缓存
        $setnx = $webGame->setGetGiftRedis($uid, $gid, $data['vCode']);
        if (!$setnx) {
            //写入记录失败
            $code = 2303;
            return $this->output($code);
        }

        $ret = $webGame->addMyGiftCode($uid, $info);
        if (!$ret) {
            //写入记录失败
            $code = 2303;
            return $this->output($code);
        }
        return $this->output($code, $data);
    }

    public function getGiftInfo()
    {
        $webGame      = new WebgameModel();
        $gid          = 1000;
        $giftCodeInfo = $webGame->getGiftInfoById($gid);
        print_r($giftCodeInfo);
    }

    /**
     * 获取用户自己领到的包页面
     * @param Request $request
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMyPackage(Request $request)
    {
        $webGame             = new WebgameModel();
        $uid                 = CookieModel::getCookie('uid');
        $userInfo['uid']     = CookieModel::getCookie('uid');
        $userInfo['nick']    = CookieModel::getCookie('nick');
        $userInfo['account'] = CookieModel::getCookie('account');
        $userInfo['face']    = CookieModel::getCookie('face');
        /**
         * 判断登录状态
         */
        $webgameLogic   = new WebgameLogicModel;
        $islogin        = $webgameLogic->checkLogin();
        $myPackageList  = array();
        $getPageDataUrl = url('getMyPackage1/');

        if (!$islogin) {
            if (strrpos($request->url(), 'www.vronline.com')) {
                return view('website/webgame/myPackage', ['islogin' => $islogin, 'data' => $myPackageList, 'getPageDataUrl' => $getPageDataUrl]);
            }
            return view('webgame/myPackage', ['islogin' => $islogin, 'data' => $myPackageList, 'getPageDataUrl' => $getPageDataUrl]);
        }

        $page    = 1;
        $pagenum = 2;

        if ($uid !== '') {
            $myPackageList = $webGame->getMyGiftCodes($uid, '', '', '', $page, $pagenum);

            foreach ($myPackageList as $k => $v) {
                $gameInfo                  = $webGame->getOneGameInfo($v['appid']);
                $myPackageList[$k]['name'] = $gameInfo['name'];
            }
        }

        if (strrpos($request->url(), 'www.vronline.com')) {
            return view('website/webgame/myPackage', ['islogin' => $islogin, 'data' => $myPackageList, 'getPageDataUrl' => $getPageDataUrl, 'userInfo' => $userInfo]);
        }
        return view('webgame/myPackage', ['islogin' => $islogin, 'data' => $myPackageList, 'getPageDataUrl' => $getPageDataUrl, 'userInfo' => $userInfo]);
    }

    /**
     * ajax获取分页数据的接口
     * @param Request $request
     * @return string
     */
    public function getMyPackage1(Request $request)
    {
        $webGame = new WebgameModel();

        $uid           = CookieModel::getCookie('uid');
        $page          = $request->input('page');
        $pagenum       = 20;
        $myPackageList = $webGame->getMyGiftCodes($uid, '', '', '', $page, $pagenum);

        $data = array();
        if ($uid !== '') {
            $data['content'] = $myPackageList;
            $data['pages']   = ceil(($webGame->getMyGiftCodesNum($uid, '', '')) / $pagenum);
            $data['nums']    = $pagenum;
        }

        return json_encode($data);
    }

    /**
     * 领取礼包页
     * @param Request $request
     * @param $appId
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function packageReceive(Request $request, $appId)
    {
        if ($appId == '') {
            return false;
        }
        $webGame     = new WebgameModel();
        $uid         = CookieModel::getCookie('uid');
        $getGiftList = array();

        /**
         * 判断登录状态
         */
        $webgameLogic = new WebgameLogicModel;
        $islogin      = $webgameLogic->checkLogin();

        if (!$islogin) {
            $islogin = false;
        }

        $getAllGift = $webGame->getAllGift($appId, '');
//        $getAllGift = $webGame->getGiftInfoById($gid);
        $getGameInfo = $webGame->getOneGameInfo($appId);
        $getGiftOne  = array();
        foreach ($getAllGift as $k => $v) {
            $gid                         = $v['gid'];
            $getGiftList[$k]['codeNum']  = $webGame->getGiftNum($v['gid']);
            $getGiftList[$k]['gid']      = $v['gid'];
            $getGiftList[$k]['appid']    = $appId;
            $getGiftList[$k]['serverid'] = $v['serverid'];
            $getGiftList[$k]['name']     = $v['name'];
            $getGiftList[$k]['content']  = $v['content'];
            $getGiftList[$k]['start']    = date('Y-m-d H:i', $v['start']);
            $getGiftList[$k]['end']      = date('Y-m-d H:i', $v['end']);
            $getGiftList[$k]['desc']     = $v['desc'];
            $getGiftList[$k]['gameName'] = $getGameInfo['name'];
            $getGiftList[$k]['uid']      = $uid;
            if (empty($getGiftOne)) {
                $getGiftOne[$k] = $getGiftList[$k];
            }
        }

        //echo $this->jsonEncode($getGiftList);
        // echo '<pre>';
        // print_r($getGiftList);
        if (strrpos($request->url(), 'www.vronline.com')) {
            return view('website/webgame/packageReceive', ['giftListData' => $getGiftOne, 'islogin' => $islogin, 'appId' => $appId, 'selectData' => $getGiftList, 'selectJsonData' => json_encode($getGiftList)]);
        }
        return view('webgame/packageReceive', ['giftListData' => $getGiftOne, 'islogin' => $islogin, 'appId' => $appId, 'selectData' => $getGiftList, 'selectJsonData' => json_encode($getGiftList)]);
    }

    /**
     * 获取游戏的详情页
     * @param Request $request
     * @param $appId
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getGameDetail(Request $request, $appId)
    {
        if ($appId == '') {
            return false;
        }
        $uid            = CookieModel::getCookie('uid');
        $webGame        = new WebgameModel();
        $getGiftList    = array();
        $getAllGift     = $webGame->getAllGift($appId, "");
        $getGameLogList = $webGame->getGameLog($uid, 3);
        $getGameInfo    = $webGame->getOneGameInfo($appId);
        foreach ($getAllGift as $k => $v) {
            $gid                         = $v['gid'];
            $getGiftList[$k]['codeNum']  = $webGame->getGiftNum($gid);
            $getGiftList[$k]['gid']      = $gid;
            $getGiftList[$k]['appid']    = $appId;
            $getGiftList[$k]['name']     = $v['name'];
            $getGiftList[$k]['content']  = $v['content'];
            $getGiftList[$k]['start']    = $v['start'];
            $getGiftList[$k]['end']      = $v['end'];
            $getGiftList[$k]['desc']     = $v['desc'];
            $getGiftList[$k]['gameName'] = $getGameInfo['name'];
            $getGiftList[$k]['uid']      = $uid;

        }
        return view('webgame/detail', ['logData' => $getGameLogList, 'uid' => $uid, 'gid' => $gid, 'appId' => $appId]);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             VR 游 戏 的 方 法                                                |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */
    /**
     * 处理一些带有标签的字符串->数组，例如：$string = "{&quot;loginMobile&quot;:&quot;13641673610&quot;}"
     * @param $str
     * @return string
     */
    public function tagStrToArr($str)
    {
        $sec = html_entity_decode($str);
        $ret = stripslashes($sec);
        return $ret;
    }

    public function getAllVrGameApi(Request $request)
    {
        $reqJson = $request->input('json');
        $param   = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param)) {
            return $this->output(2001);
        }
        $actionType = isset($param["actiontype"]) ? $param["actiontype"] : '';
        $uid        = isset($param["uid"]) ? intval($param["uid"]) : '';
        $appid      = isset($param["gid"]) ? intval($param["gid"]) : '';
        $times      = isset($param["times"]) ? intval($param["times"]) : '';

        switch ($actionType) {
            case 'gameslist':
                $ret = $this->getVrGameList();
                return $ret;
            case 'gameinfo':
                $res = $this->getVrGameInfo($appid);
                return $res;
            case 'historyadd':
                $ret = $this->addVrGameHistory($uid, $appid);
                return $ret;
            case 'historylist':
                $ret = $this->getVrGameHistory($uid);
                return $ret;
            case 'delhistorylist':
                $ret = $this->delVrGameHistory($uid, $appid);
                return $ret;
            case 'addtimes':
                $ret = $this->addVrGameTimes($uid, $appid, $times);
                return $ret;
            case 'vrinnerrecommend':
                $ret = $this->getVrGameSix();
                return $ret;
            default:
                $ret = $this->outputForPc(1102);
                return $ret;
        }
    }

    /**
     * 获取VR列表
     */
    public function getVrGameInfo($appid)
    {

        if (!is_numeric($appid)) {
            return Library::output(1);
        }
        $gameModel = new WebgameModel;
        $gameInfo  = $gameModel->getOneGameInfo($appid);
        if (!$gameInfo) {
            return Library::output(2304);
        }

        $imgs         = ImageHelper::getUrl('vrgameimg', ['id' => $appid, 'version' => $gameInfo['img_version'], 'publish' => true]);
        $sdk          = isset($gameInfo['bits_status']) && $gameInfo['bits_status'] != 0 ? $gameInfo['bits_status'] & Config::get("bits_status.is_sdk_conflict") : 0;
        $recommendArr = $this->getVrGameSix();
        $show_flag    = "0";
        if (isset($recommendArr) && is_array($recommendArr)) {
            foreach ($recommendArr as $rk => $rv) {
                if ($rv['game_id'] == $appid) {
                    $show_flag = strval($rv['show_flag']);
                }
            }
        }

        if (isset($gameInfo['recomm_device']["操作系统"])) {
            $recomm_device = [
                "system"   => $gameInfo['recomm_device']['操作系统'],
                "cpu"      => isset($gameInfo['recomm_device']['处理器']) ? trim($gameInfo['recomm_device']['处理器']) : "",
                "memory"   => isset($gameInfo['recomm_device']['内存']) ? trim($gameInfo['recomm_device']['内存']) : "",
                "directx"  => isset($gameInfo['recomm_device']['DX版本']) ? trim($gameInfo['recomm_device']['DX版本']) : "",
                "graphics" => isset($gameInfo['recomm_device']['显卡']) ? trim($gameInfo['recomm_device']['显卡']) : "",
            ];
        } else {
            $recomm_device = $gameInfo['recomm_device'];
        }

        $out                     = [];
        $out['game_id']          = $appid;
        $out['game_name']        = $gameInfo['name'];
        $out['game_ver']         = $gameInfo['version_code'];
        $out['game_flag']        = "0";
        $out['show_flag']        = $show_flag;
        $out['res_url']          = 'http://down.vrgame.vronline.com/dev/' . $appid . '/' . $gameInfo['version_code'];
        $out['logo']             = static_image($imgs['logo']);
        $out['game_sdk']         = $sdk;
        $out['support_device']   = $gameInfo['support'];
        $out['recomm_device']    = $recomm_device;
        $out['ocruntimeversion'] = $gameInfo['ocruntimeversion'];

        return $this->outputForPc(0, $out);
    }

    /**
     * 获取VR列表
     */
    public function getVrGameList()
    {
        $webGame      = new WebgameModel();
        $allVrGameArr = $webGame->getAllVrGameInfo();
        $allVrGame    = [];
        $recommendArr = $this->getVrGameSix();

        foreach ($allVrGameArr as $k => $v) {
            $allVrGame[$k]['game_id']        = strval($v['appid']);
            $allVrGame[$k]['game_name']      = $v['name'];
            $allVrGame[$k]['game_ver']       = strval($v['version_code']);
            $allVrGame[$k]['game_flag']      = strval($v['icon_corner']);
            $allVrGame[$k]['show_flag']      = "0";
            $allVrGame[$k]['res_url']        = 'http://down.vrgame.vronline.com/dev/' . $v['appid'] . '/' . $v['version_code'];
            $logo                            = ImageHelper::url("vrgame", $v['appid'], $v['img_version'], $v['img_slider'], false);
            $allVrGame[$k]['logo']           = static_image($logo['logo']);
            $sdk                             = isset($v['bits_status']) && $v['bits_status'] != 0 ? $v['bits_status'] & Config::get("bits_status.is_sdk_conflict") : 0;
            $allVrGame[$k]['game_sdk']       = $sdk;
            $allVrGame[$k]['support_device'] = $v['support'];
            $allVrGame[$k]['mini_device']    = $v['mini_device'];
            $allVrGame[$k]['recomm_device']  = $v['recomm_device'];
            $allVrGame[$k]['network_type']   = strval($v['network_type']);
            if (isset($recommendArr) && is_array($recommendArr)) {
                foreach ($recommendArr as $rk => $rv) {
                    if ($rv['game_id'] == $v['appid']) {
                        $allVrGame[$k]['show_flag'] = strval($rv['show_flag']);
                    }
                }
            }
        }

        return $this->outputForPc(0, $allVrGame);
    }
    /*
     * 添加用户的购买和下载的历史记录
     */
    public function addVrGameHistory($uid, $appid)
    {
        $webGame = new WebgameModel();

        if ($uid == '' || $appid == '') {
            return $this->output(1102);
        }
        $vrGameArr = $webGame->getOneVrGameInfo($appid);

        $name = isset($vrGameArr['name']) ? $vrGameArr['name'] : '';

        if ($name == '') {
            return $this->outputForPc(2304);
        }
        $info = [
            'uid'       => $uid,
            'appid'     => $appid,
            'appname'   => $name,
            'ltime'     => time(),
            'game_type' => 1,
        ];

        $ret = $webGame->addVrGameHistory($info);
        if (!$ret) {
            return $this->outputForPc(1);
        }
        return $this->outputForPc(0);
    }

    /*
     * 获取用户的vr游戏历史记录
     */
    public function getVrGameHistory($uid)
    {
        $webGame = new WebgameModel();

        if ($uid == '') {
            return $this->outputForPc(1102);
        }

        $ret = $webGame->getVrGameHistory($uid);

        $allVrHistory = [];
        foreach ($ret as $k => $v) {
            $allVrHistory[$k]['gid']        = strval($v['appid']);
            $allVrHistory[$k]['game_name']  = strval($v['appname']);
            $allVrHistory[$k]['totalTimes'] = strval($v['timelen']);
            $allVrHistory[$k]['gtimestamp'] = strval($v['ltime']);
        }

        return $this->outputForPc(0, $allVrHistory);
    }

    /**
     * 删除历史列表
     */
    public function delVrGameHistory($uid, $appid)
    {
        $webGame = new WebgameModel();

        if ($uid == '' || $appid == '') {
            return $this->output(1102);
        }
        $info = [
            'uid'   => $uid,
            'appid' => $appid,
        ];

        $ret = $webGame->delVrGameHistory($info);
        if (!$ret) {
            return $this->outputForPc(1);
        }
        return $this->outputForPc(0);
    }

    /**
     * 添加游戏时长
     */
    public function addVrGameTimes($uid, $appid, $times)
    {
        $webGame = new WebgameModel();

        if ($uid == '' || $appid == '') {
            return $this->outputForPc(1102);
        }
        $info = [
            'uid'   => $uid,
            'appid' => $appid,
            'times' => $times,
        ];
        $ret = $webGame->addVrGameTimes($info);

        if (!$ret) {
            return $this->outputForPc(1);
        }
        return $this->outputForPc(0);
    }
    /**
     * VR头戴设备里的推荐位6个
     */
    public function getVrGameSix()
    {
        $recommendModel = new OperateModel();
        $webGame        = new WebgameModel();
        $code           = 'vrgame-recommend-6';
        $startPosidArr  = $recommendModel->getPosId($code);
        $posid          = isset($startPosidArr['posid']) ? $startPosidArr['posid'] : '';

        if ($posid == '') {
            return $this->outputForPc(1102);
        }

        //获取正式表中VR眼睛内部推荐位数据
        $recommendArrAll = $recommendModel->getItemsByPosid($posid);

        $recommendInfo    = [];
        $vrGameInfoArrTmp = [];
        foreach ($recommendArrAll as $k => $v) {
            $vrGameInfoArrTmp[$k]           = $webGame->getOneGameInfo($v['itemid']);
            $recommendInfo[$k]['game_id']   = $vrGameInfoArrTmp[$k]['appid'];
            $recommendInfo[$k]['show_flag'] = $k + 3; //由于对应的位置是从3~8标识的6个位置，前边是预留位置
        }
        return $recommendInfo;
    }

    /**
     * 获取页游首页的分类筛选的数据
     */
    public function getWebgameBySort(Request $request, $type)
    {
        if (!$type) {
            return $this->output(1);
        }

        $webGame = new WebgameModel();
        $page    = $request->input('page');
        if ($page) {
            $gameType              = 0;
            $pagenum               = 12;
            $getAllGameArr['data'] = $webGame->webPageDate($type, $gameType, $page, $pagenum);
            foreach ($getAllGameArr['data'] as $k => $v) {
                $getAllGameArr['data'][$k]['image'] = ImageHelper::url('webgame', $v['appid'], $v['img_version'], $v['img_slider'], false);
            }
            return $this->output(0, $getAllGameArr);
        }

        $getAllGameArr['data'] = $webGame->getAllGameInfo($type, '', 0);
        foreach ($getAllGameArr['data'] as $k => $v) {
            $getAllGameArr['data'][$k]['image'] = ImageHelper::url('webgame', $v['appid'], $v['img_version'], $v['img_slider'], false);
        }
        return $this->output(0, $getAllGameArr);
    }

    /**
     * 页游列表页
     *
     * @return \Illuminate\Http\Response
     */
    public function webGameApiList(Request $request)
    {
        $page     = (int) $request->input("page", 1);
        $class_id = (int) $request->input("class_id", 0);

        $filter = [];
        if ($class_id) {
            $filter["class_id"] = $class_id;
        }

        $webGameModel = App::make('webGame');
        $webgames     = $webGameModel->webGameCategoryPage($page, 7, $filter);

        if (!$webgames) {
            return $this->output(0, ["data" => false]);
        }

        foreach ($webgames as &$game) {
            $game["logo"]  = static_image($game["image"]["logo"], 226);
            $game["icon"]  = static_image($game["image"]["icon"]);
            $game["score"] = number_format($game["score"], 1);
            // $game["device-icon"] = BladeHelper::handleDeviceIcon($game["support"]);
            // $game["type-span"]   = "<span>" . BladeHelper::transConetentClass($game["first_class"], "vrgame", "</span><span>") . "</span>";
            //$game["date"] = date("Y年m月d日", strtotime($game["publish_date"]));
        }
        return $this->output(0, ["data" => $webgames]);
    }
}
