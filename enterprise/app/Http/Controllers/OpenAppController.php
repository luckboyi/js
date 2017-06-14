<?php
namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\WebgameModel;
use Config;
use Helper\AccountCenter as Account;
use Helper\Library;
use Illuminate\Http\Request;
use Overtrue\Pinyin\Pinyin;
use Validator;
use \App\Models\DevModel;

// 开发平台产品相关
class OpenAppController extends Controller
{

    private $webGamePageSize       = 10;
    private $webGameServerPageSize = 10;

    public function __construct()
    {
        $this->middleware("vrauth:0:dev", ['only' => ["webGame", "webGameDetail"]]);
        $this->middleware("vrauth:json:dev", ['only' => ["webGameSave", "webGameServerSave", "webgameReview", "webGamePublish"]]);
        $this->middleware("vrauth:0:dev_master", ['only' => ["webGameCreate", "subAccountList", "subAccountEdit", "subAccountPerm"]]);
        $this->middleware("vrauth:json:dev_master", ['only' => ["webGameCreateSubmit"]]);
    }

    //master account start
    public function subAccountList(Request $request)
    {
        $userInfo = $request->userinfo;

        $uid      = $userInfo['uid'];
        $devModel = new DevModel;
        $accounts = $devModel->getSonUser($uid);
        return view('open.account.list', ['accounts' => $accounts, "user" => $userInfo, "nav" => "account", "tag" => "list"]);
    }

    public function subAccountEdit(Request $request)
    {
        $userInfo = $request->userinfo;
        return view('open.account.edit', ["user" => $userInfo, "nav" => "account", "tag" => "list"]);
    }

    public function subAccountPerm(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $devModel = new DevModel;
        $accounts = $devModel->getSonUser($uid);
        $games    = $devModel->getAppsByUid($uid, "all", 20);
        return view('open.account.perm', ['accounts' => $accounts, "user" => $userInfo, "nav" => "account", "tag" => "perm", 'games' => $games]);
    }
    //master account end

    public function webGame(Request $request, $tp)
    {
        $userInfo = $request->userinfo;

        $devModel = new DevModel;
        $devInfo  = $devModel->devPerms($userInfo);
        if (!$devInfo) {
            return Library::output(1);
        }
        $uid   = $devInfo['uid'];
        $inIds = $devInfo['inIds'];

        $gameList = $devModel->getAppsByUid($uid, $tp, $this->webGamePageSize, 0, $inIds);
        $left     = $tp == "online" ? "online" : "offline";
        $right    = $tp == "online" ? "offline" : "online";
        $num      = $devModel->getAppsByUidCount($uid, $tp, 0, $inIds);

        $onlineNum  = $tp == "online" ? $gameList->total() : $num;
        $offlineNum = $tp == "offline" ? $gameList->total() : $num;
        return view('open.product.webGameList', ['games' => $gameList, 'onlineNum' => $onlineNum, 'offlineNum' => $offlineNum, 'user' => $userInfo, 'left' => $left, 'right' => $right, 'nav' => 'product', 'tag' => 'webgame']);
    }

    public function webGameCreate(Request $request)
    {
        $userInfo = $request->userinfo;
        $detail   = [
            'new'         => true,
            'appid'       => 0,
            'name'        => '',
            'tags'        => '',
            'first_class' => '',
            'support'     => '',
            'content'     => '',
            'gameb_name'  => '',
            'rmb_rate'    => 0,
        ];
        return view('open.product.webGameBase', ['user' => $userInfo, 'nav' => 'product', 'tag' => 'webgame', 'detail' => $detail]);
    }

    public function webGameDetail(Request $request, $tp = "all", $appid)
    {
        $userInfo = $request->userinfo;

        $devModel = new DevModel;
        $devInfo  = $devModel->devPerms($userInfo);
        if (!$devInfo) {
            return redirect('open/needPerm', 302, [], true);
        }
        $uid   = $devInfo['uid'];
        $inIds = $devInfo['inIds'];

        $appInfo = $devModel->getWebgameInfo($appid);
        if ($appInfo['uid'] != $uid || ($devInfo['sub'] && (!$inIds || !in_array($appid, $inIds)))) {
            return redirect('open/needPerm', 302, [], true);
        }

        $uc_appid     = Config::get("common.uc_appid");
        $uc_appkey    = Config::get("common.uc_appkey");
        $accountModel = new Account($uc_appid, $uc_appkey);
        $keyInfo      = $accountModel->getAppInfo($appid);

        $appInfo['appkey'] = $keyInfo['appkey'];
        $appInfo['paykey'] = $keyInfo['paykey'];
        $viewName          = "";
        $data              = ['tp' => $tp, 'detail' => $appInfo, 'user' => $userInfo, 'nav' => 'product', 'tag' => 'webgame'];
        switch ($tp) {
            case 'base':
                $viewName = "open.product.webGameBase";
                break;
            case "res":
                if ($appInfo['img_version'] > 0) {
                    $resInfo                 = ImageHelper::url('webgame', $appid, $appInfo['img_version'], $appInfo['img_slider'], true, $appInfo['screenshots']);
                    $data['detail']['logo']  = $resInfo['logo'];
                    $data['detail']['slogo'] = $resInfo['slogo'];
                    $data['detail']['icon']  = $resInfo['icon'];
                    $data['detail']['rank']  = $resInfo['rank'];
                    $data['detail']['bg']    = $resInfo['bg'];
                    $data['detail']['bg2']   = $resInfo['bg2'];
                    $data['detail']['ico']   = $resInfo['ico'];
                    $data['detail']['card']  = $resInfo['card'];
                    if ($appInfo['img_slider']) {
                        $data['detail']['slider'] = $resInfo['slider'];
                    }
                    if ($appInfo['screenshots']) {
                        $data['detail']['screenshots'] = $resInfo['screenshots'];
                    }
                }
                $viewName = "open.product.webGameRes";
                break;
            case "server":
                $servers         = $devModel->getWebgameServers($appid, $this->webGameServerPageSize);
                $viewName        = "open.product.webGameServer";
                $data["servers"] = $servers;
                break;
            case "copyright":
                $viewName               = "open.product.webGameCopyright";
                $resInfo                = ImageHelper::path('openapp', $appid);
                $data['detail']['base'] = $resInfo['base'];
                if ($appInfo['cp_soft']) {
                    $data['detail']['cp_soft'] = json_decode($appInfo['cp_soft'], true);
                }
                if ($appInfo['cp_record']) {
                    $data['detail']['cp_record'] = json_decode($appInfo['cp_record'], true);
                }
                if ($appInfo['cp_publish']) {
                    $data['detail']['cp_publish'] = json_decode($appInfo['cp_publish'], true);
                }
                if ($appInfo['is_deal'] == 1) {
                    $data['detail']['agreement_type'] = "";
                } else {
                    $data['detail']['agreement_type'] = "input";
                }
                break;
            default:
                $viewName = "open.product.webGameDetail";
                break;
        }
        return view($viewName, $data);
    }

    public function webGameCreateSubmit(Request $request)
    {
        $userInfo  = $request->userinfo;
        $uid       = $userInfo['uid'];
        $validator = Validator::make($request->all(), [
            'name'     => 'required|min:2|max:20',
            'tags'     => 'required|min:2|max:40',
            'first'    => 'required|min:2|max:20',
            'content'  => 'required|min:10|max:200',
            'rmb_name' => 'required|min:1|max:20',
            'rmb_rate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return Library::output(1, null, "input error");
        }

        $name       = trim($request->input("name"));
        $devModel   = new DevModel;
        $nameExists = $devModel->checkWebgameName($name);
        if ($nameExists) {
            return Library::output(2, null, "name repeat");
        }
        $firstClass = trim($request->input("first"));
        $tags       = trim($request->input("tags"));
        $content    = trim($request->input("content"));
        $rmb_name   = trim($request->input("rmb_name"));
        $rmb_rate   = intval($request->input("rmb_rate"));
        if ($rmb_rate < 1) {
            return Library::output(1, null, "input error");
        }
        $uinfo = $devModel->getUser($uid);
        if (!$uinfo || !is_array($uinfo)) {
            return Library::output(2, null, "user info error");
        }
        if ($uinfo['type'] == 1) {
            $company  = $uinfo['name'];
            $contacts = $uinfo['contacts'];
        } else {
            $company  = "";
            $contacts = $uinfo['name'];
        }

        $pinyin = new Pinyin();
        $spell  = strtolower($pinyin->sentence($name));
        $spell  = substr($spell, 0, 1) . " " . $spell;

        $info  = array('uid' => $uid, 'name' => $name, 'spell_name' => $spell, 'tags' => $tags, 'first_class' => $firstClass, 'content' => $content, "company" => $company, "contacts" => $contacts, 'gameb_name' => $rmb_name, 'rmb_rate' => $rmb_rate);
        $appId = $devModel->addWebgameInfo($info);
        if ($appId) {
            //同步appinfo
            $uc_appid     = Config::get("common.uc_appid");
            $uc_appkey    = Config::get("common.uc_appkey");
            $accountModel = new Account($uc_appid, $uc_appkey);
            $appRet       = $accountModel->setAppInfo($appId, array('appid' => $appId));
            //log appRet
            return Library::output(0, array('appid' => $appId));
        } else {
            return Library::output(3);
        }
    }

    public function webGameSave(Request $request, $tp, $appid)
    {
        $userInfo = $request->userinfo;

        $devModel = new DevModel;
        $devInfo  = $devModel->devPerms($userInfo);
        if (!$devInfo) {
            return redirect('open/needPerm', 302, [], true);
        }
        $uid   = $devInfo['uid'];
        $inIds = $devInfo['inIds'];

        $appInfo = $devModel->getWebgameInfo($appid);
        if ($appInfo['uid'] != $uid || ($devInfo['sub'] && (!$inIds || !in_array($appid, $inIds)))) {
            return Library::output(1);
        }
        if ($appInfo['stat'] == 1) {
            return Library::output(1, '', "资料审核中 无法修改");
        }
        switch ($tp) {
            case 'base':
                $validator = Validator::make($request->all(), [
                    'name'     => 'required|min:2|max:20',
                    'first'    => 'required|min:2|max:20',
                    'tags'     => 'required|min:2|max:40',
                    'content'  => 'required|min:10|max:200',
                    'rmb_name' => 'required|min:1|max:20',
                    'rmb_rate' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    return Library::output(1);
                }
                $name       = trim($request->input("name"));
                $devModel   = new DevModel;
                $nameExists = $devModel->checkWebgameName($name, $appid);
                if ($nameExists) {
                    return Library::output(2, null, "name repeat");
                }
                $tags       = trim($request->input("tags"));
                $firstClass = trim($request->input("first"));
                $content    = trim($request->input("content"));
                $rmb_name   = trim($request->input("rmb_name"));
                $rmb_rate   = intval($request->input("rmb_rate"));
                if ($rmb_rate < 1) {
                    return Library::output(1, null, "input error");
                }
                $pinyin = new Pinyin();
                $spell  = strtolower($pinyin->sentence($name));
                //$spell = substr($spell, 0, 1) . " " . $spell;
                $spell = substr($spell, 0, 1);
                if (is_numeric($spell)) {
                    $spell = Library::num2Pinyin($spell);
                    $spell = substr($spell, 0, 1);
                }
                $info = array('name' => $name, 'spell_name' => $spell, 'tags' => $tags, 'first_class' => $firstClass, 'content' => $content, 'gameb_name' => $rmb_name, 'rmb_rate' => $rmb_rate);
                break;
            case "res":
                $validator = Validator::make($request->all(), [
                    'logo'     => 'required|min:20|max:200',
                    'slogo'    => 'required|min:20|max:200',
                    'img_icon' => 'required|min:20|max:200',
                    'img_rank' => 'required|min:20|max:200',
                    'bg'       => 'required|min:20|max:200',
                    'slider'   => 'required|min:20|max:1000',
                ]);
                if ($validator->fails()) {
                    return Library::output(1);
                }
                $logo        = trim($request->input("logo"));
                $slogo       = trim($request->input("slogo"));
                $slider      = trim($request->input("slider"));
                $screenshots = trim($request->input("screenshots"));
                $img_version = intval($appInfo['img_version'] + 1);
                $info        = array('img_version' => $img_version, 'img_slider' => $slider, 'screenshots' => $screenshots);
                break;
            case "copyright":
                $cpDeal = intval($request->input("cp_deal"));
                if ($cpDeal != 1) {
                    return Library::output(1, "", "必须勾选开发者协议");
                }

                $cpSoft    = json_encode(explode(",,", $request->input("soft", "")));
                $cpRecord  = json_encode(explode(",,", $request->input("record", "")));
                $cpPublish = json_encode(explode(",,", $request->input("publish", "")));

                $info      = array('cp_soft' => $cpSoft, 'cp_record' => $cpRecord, 'cp_publish' => $cpPublish, 'is_deal' => 1);
                $agreement = $_POST;
                if (isset($agreement['soft'])) {
                    unset($agreement['soft']);
                }

                if (isset($agreement['record'])) {
                    unset($agreement['record']);
                }

                if (isset($agreement['publish'])) {
                    unset($agreement['publish']);
                }

                if ($agreement && is_array($agreement) && count($agreement) > 20) {
                    $info['agreement'] = json_encode($agreement);
                }
                break;
            default:
                return Library::output(1);
                break;
        }

        $devModel = new DevModel;
        $ret      = $devModel->updWebgameInfo($appid, $info);
        return Library::output(0, array('appid' => $appid, 'ret' => $ret));
    }

    public function webGameServerSave(Request $request, $appid)
    {
        $userInfo = $request->userinfo;

        $devModel = new DevModel;
        $devInfo  = $devModel->devPerms($userInfo);
        if (!$devInfo) {
            return redirect('open/needPerm', 302, [], true);
        }
        $uid   = $devInfo['uid'];
        $inIds = $devInfo['inIds'];

        $appInfo = $devModel->getWebgameInfo($appid);
        if ($appInfo['uid'] != $uid || ($devInfo['sub'] && (!$inIds || !in_array($appid, $inIds)))) {
            return Library::output(1);
        }

        $validator = Validator::make($request->all(), [
            'serverid'     => 'required|numeric',
            'name'         => 'required|min:2|max:30',
            'domain'       => 'required|min:5|max:100',
            'payurl'       => 'required|min:5|max:100',
            'status'       => 'required|numeric',
            'is_new'       => 'required|numeric',
            'is_recommend' => 'required|numeric',
            'start'        => 'required|min:10|max:20',
        ]);
        if ($validator->fails()) {
            return Library::output(11);
        }
        $serverid = intval($request->input("serverid"));
        if ($serverid < 1) {
            return Library::output(1);
        }
        $oldserverid = intval($request->input("oldserverid"));
        $name        = trim($request->input("name"));
        $domain      = trim($request->input("domain"));
        $payUrl      = trim($request->input("payurl"));
        $status      = trim($request->input("status"));
        $isRecommend = intval($request->input("is_recommend"));
        $isNew       = intval($request->input("is_new"));
        $start       = strtotime($request->input("start"));

        $devModel  = new DevModel;
        $nameCheck = $devModel->checkWebGameServerName($appid, $serverid, $oldserverid, $name);
        if ($nameCheck !== 1) {
            return Library::output($nameCheck);
        }

        $uc_appid     = Config::get("common.uc_appid");
        $uc_appkey    = Config::get("common.uc_appkey");
        $accountModel = new Account($uc_appid, $uc_appkey);
        $serverRet    = $accountModel->setServerInfo($appid, $serverid, array('payurl' => $payUrl));
        if (!isset($serverRet['code']) || $serverRet['code'] != 0) {
            return Library::output(1);
        }

        $is_publish = $appInfo['send_time'] > 0 ? 1 : 0;
        $info       = ['appid' => $appid, 'serverid' => $serverid, 'name' => $name, 'domain' => $domain, 'payurl' => $payUrl, 'status' => $status, 'recommend' => $isRecommend, 'isnew' => $isNew, 'start' => $start, 'is_publish' => $is_publish];
        if ($oldserverid > 0) {
            unset($info['appid']);
            $ret = $devModel->updWebgameServerInfo($appid, $oldserverid, $info);
        } else {
            $ret = $devModel->addWebgameServers($info);
        }

        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function webgameReview(Request $request, $appid)
    {
        $userInfo = $request->userinfo;
        $devModel = new DevModel;
        $devInfo  = $devModel->devPerms($userInfo);
        if (!$devInfo) {
            return redirect('open/needPerm', 302, [], true);
        }
        $uid   = $devInfo['uid'];
        $inIds = $devInfo['inIds'];

        $appInfo = $devModel->getWebgameInfo($appid);
        if ($appInfo['uid'] != $uid || ($devInfo['sub'] && (!$inIds || !in_array($appid, $inIds)))) {
            return Library::output(1);
        }
        if ($appInfo['is_deal'] != 1 || strlen($appInfo['cp_soft']) < 1 || strlen($appInfo['cp_record']) < 1 || strlen($appInfo['cp_publish']) < 1 || $appInfo['img_version'] < 1 || strlen($appInfo['img_slider']) < 1) {
            return Library::output(1, '', "资料未填写完整");
        }

        $devModel = new DevModel;
        $ret      = $devModel->updWebgameInfo($appid, array('stat' => 1));
        return Library::output(0, array('appid' => $appid, 'ret' => $ret));
    }

    public function webGamePublish(Request $request, $appid)
    {
        $userInfo = $request->userinfo;
        $devModel = new DevModel;
        $devInfo  = $devModel->devPerms($userInfo);
        if (!$devInfo) {
            return redirect('open/needPerm', 302, [], true);
        }
        $uid     = $devInfo['uid'];
        $inIds   = $devInfo['inIds'];
        $appInfo = $devModel->getWebgameInfo($appid);
        if ($appInfo['uid'] != $uid || ($devInfo['sub'] && (!$inIds || !in_array($appid, $inIds)))) {
            return Library::output(1);
        }
        if ($appInfo['stat'] != 5) {
            return Library::output(2501);
        }
        if ($appInfo['send_time'] > 0) {
            return Library::output(2502);
        }

        $info     = array('send_time' => time());
        $devModel = new DevModel;
        $ret      = $devModel->updWebgameInfo($appid, $info);

        $webModel     = new WebgameModel;
        $info['stat'] = 0;
        $ret          = $webModel->setGameInfo($appid, $info);

        $ret = $devModel->updWebgameServer($appid, array('is_publish' => 1));
        return Library::output(0, array('appid' => $appid, 'ret' => $ret));
    }

}
