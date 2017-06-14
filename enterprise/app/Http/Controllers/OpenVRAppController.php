<?php
namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use Config;
use Helper\AccountCenter as Account;
use Helper\Library;
use Illuminate\Http\Request;
use Overtrue\Pinyin\Pinyin;
use Validator;
use \App\Models\DevModel;

/**
 * Open后台VR游戏相关
 */
class OpenVRAppController extends Controller
{

    private $vrGamePageSize = 10;

    public function __construct()
    {
        $this->middleware("vrauth:jump:dev", ['only' => ["vrGame", "vrGameCreate", "vrGameDetail"]]);
        $this->middleware("vrauth:json:dev", ['only' => ["vrGameSave"]]);
        $this->middleware("vrauth:0:dev_master", ['only' => ["vrGameCreate"]]);
        $this->middleware("vrauth:json:dev_master", ['only' => ["vrGameCreateSubmit"]]);
    }

    /**
     * VRGame列表
     *
     * @param  Request $request [description]
     * @param  [type]  $tp      [description]
     * @return [type]           [description]
     */
    public function vrGame(Request $request, $tp)
    {
        $userInfo = $request->userinfo;
        $devModel = new DevModel;
        $devInfo  = $devModel->devPerms($userInfo);
        if (!$devInfo) {
            return Library::output(1);
        }
        $uid      = $devInfo['uid'];
        $inIds    = $devInfo['inIds'];
        $gameList = $devModel->getAppsByUid($uid, $tp, $this->vrGamePageSize, 1, $inIds);
        $num      = $devModel->getAppsByUidCount($uid, $tp, 1, $inIds);

        $left  = $tp == "online" ? "online" : "offline";
        $right = $tp == "online" ? "offline" : "online";

        $onlineNum  = $tp == "online" ? $gameList->total() : $num;
        $offlineNum = $tp == "online" ? $num : $gameList->total();

        return view('open.vrgame.vrGameList', ['games' => $gameList, 'user' => $userInfo, 'left' => $left, 'right' => $right, 'onlineNum' => $onlineNum, 'offlineNum' => $offlineNum, 'nav' => 'product', 'tag' => 'vrgame']);
    }

    /**
     * 添加VRGame
     *
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function vrGameCreate(Request $request)
    {
        $userInfo = $request->userinfo;
        $detail   = [
            'new'           => true,
            'appid'         => 0,
            'name'          => '',
            'tags'          => '',
            'first_class'   => '',
            'support'       => '',
            'original_sell' => 0,
            'sell'          => 0,
            'mini_device'   => '',
            'recomm_device' => '',
            'content'       => '',
        ];

        return view('open.vrgame.vrGameBase', ['user' => $userInfo, 'nav' => 'product', 'tag' => 'vrgame', 'detail' => $detail]);
    }

    /**
     * 提交添加VRGame
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function vrGameCreateSubmit(Request $request)
    {
        $userInfo  = $request->userinfo;
        $uid       = $userInfo['uid'];
        $validator = Validator::make($request->all(), [
            'name'          => 'required|min:2|max:20',
            'tags'          => 'required|min:2|max:40',
            'first'         => 'required|min:1|max:200',
            'sell'          => 'required',
            'original_sell' => 'required',
            'support'       => 'required|min:1|max:200',
            'min_device'    => 'required|min:10|max:200',
            'rec_device'    => 'required|min:10|max:200',
            'content'       => 'required|min:10|max:200',
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
        $tags          = trim($request->input("tags"));
        $firstClass    = trim($request->input("first"));
        $original_sell = floatval($request->input("original_sell"));
        $sell          = floatval($request->input("sell"));
        $support       = trim($request->input("support"));
        $mini_device   = trim($request->input("min_device"));
        $recomm_device = trim($request->input("rec_device"));
        $content       = trim($request->input("content"));
        $uinfo         = $devModel->getUser($uid);
        $sdkReq        = $request->input("sdk");
        if ($sdkReq == 0) {
            $sdk = 0;
        } else {
            $sdk = $sdkReq | Config::get("bits_status.is_sdk_conflict");
        }

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
        //$spell = substr($spell, 0, 1) . " " . $spell;
        $spell = substr($spell, 0, 1);
        if (is_numeric($spell)) {
            $spell = Library::num2Pinyin($spell);
            $spell = substr($spell, 0, 1);
        }
        $info = array(
            'uid'           => $uid,
            'name'          => $name,
            'spell_name'    => $spell,
            'tags'          => $tags,
            'first_class'   => $firstClass,
            'sell'          => $sell,
            'original_sell' => $original_sell,
            'content'       => $content,
            'support'       => $support,
            'mini_device'   => $mini_device,
            'recomm_device' => $recomm_device,
            "company"       => $company,
            "bits_status"   => $sdk,
            "contacts"      => $contacts,
            "game_type"     => 1,
        );

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

    /**
     * VRGame详情
     *
     * @param  Request $request [description]
     * @param  string  $tp      [description]
     * @param  [type]  $appid   [description]
     * @return [type]           [description]
     */
    public function vrGameDetail(Request $request, $tp = "all", $appid)
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
        $uid = $userInfo['uid'];

        $uc_appid     = Config::get("common.uc_appid");
        $uc_appkey    = Config::get("common.uc_appkey");
        $accountModel = new Account($uc_appid, $uc_appkey);

        $keyInfo           = $accountModel->getAppInfo($appid);
        $appInfo['appkey'] = $keyInfo['appkey'];
        $appInfo['paykey'] = $keyInfo['paykey'];

        $viewName = "";
        $data     = [
            'tp'     => $tp,
            'detail' => $appInfo,
            'user'   => $userInfo,
            'nav'    => 'product',
            'tag'    => 'vrgame',
        ];

        if ($appInfo['bits_status'] != 0) {
            $data['detail']['bits_status'] = $appInfo['bits_status'] & Config::get("bits_status.is_sdk_conflict");
        }

        switch ($tp) {
            case 'base':
                $viewName = "open.vrgame.vrGameBase";
                break;
            case "res":
                if ($appInfo['img_version'] > 0) {
                    $resInfo                = ImageHelper::url("vrgame", $appid, $appInfo['img_version'], $appInfo['img_slider']);
                    $data['detail']['logo'] = $resInfo['logo'];
                    $data['detail']['rank'] = $resInfo['rank'];
                    $data['detail']['bg']   = $resInfo['bg'];
                    $data['detail']['icon'] = $resInfo['icon'];
                    if ($appInfo['img_slider']) {
                        $data['detail']['slider'] = $resInfo['slider'];
                    }
                }
                $viewName = "open.vrgame.vrGameRes";
                break;
            case "server":
                $servers         = $devModel->getWebgameServers($appid, $this->webGameServerPageSize);
                $viewName        = "open.vrgame.vrGameVersion";
                $data["servers"] = $servers;
                break;
            case "copyright":
                $viewName               = "open.vrgame.vrGameCopyright";
                $resInfo                = ImageHelper::path("openapp", $appid, 1);
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
                $viewName = "open.vrgame.vrGameDetail";
                break;
        }

        return view($viewName, $data);
    }

    /**
     * VRGame详情
     *
     * @param  Request $request [description]
     * @param  [type]  $tp      [description]
     * @param  [type]  $appid   [description]
     * @return [type]           [description]
     */
    public function vrGameSave(Request $request, $tp, $appid)
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
                    'name'          => 'required|min:2|max:20',
                    'tags'          => 'required|min:2|max:40',
                    'first'         => 'required|min:2|max:40',
                    'sell'          => 'required',
                    'original_sell' => 'required',
                    'support'       => 'required|min:1|max:200',
                    'min_device'    => 'required|min:10|max:200',
                    'rec_device'    => 'required|min:10|max:200',
                    'content'       => 'required|min:10|max:200',
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
                $tags          = trim($request->input("tags"));
                $firstClass    = trim($request->input("first"));
                $sell          = floatval($request->input("sell"));
                $original_sell = floatval($request->input("original_sell"));
                $content       = trim($request->input("content"));
                $support       = trim($request->input("support"));
                $mini_device   = trim($request->input("min_device"));
                $recomm_device = trim($request->input("rec_device"));

                $pinyin = new Pinyin();
                $spell  = strtolower($pinyin->sentence($name));
                $spell  = substr($spell, 0, 1) . " " . $spell;

                $info = array('name' => $name, 'spell_name' => $spell, 'tags' => $tags, 'sell' => $sell, 'original_sell' => $original_sell, 'first_class' => $firstClass, 'content' => $content, 'support' => $support, 'mini_device' => $mini_device, 'recomm_device' => $recomm_device);
                break;
            case "res":
                $validator = Validator::make($request->all(), [
                    'logo'     => 'required|min:20|max:200',
                    'bg'       => 'required|min:20|max:200',
                    'img_icon' => 'required|min:20|max:200',
                    'img_rank' => 'required|min:20|max:200',
                    'slider'   => 'required|min:20|max:1000',
                ]);
                if ($validator->fails()) {
                    return Library::output(1);
                }
                $logo        = trim($request->input("logo"));
                $slider      = trim($request->input("slider"));
                $img_version = intval($appInfo['img_version'] + 1);
                $info        = array('img_version' => $img_version, 'img_slider' => $slider);
                break;
            case "copyright":
                $cpDeal = intval($request->input("cp_deal"));
                if ($cpDeal != 1) {
                    return Library::output(1, "", "必须勾选开发者协议");
                }
                $cpSoft    = json_encode($request->input("soft"));
                $cpRecord  = json_encode($request->input("record"));
                $cpPublish = json_encode($request->input("publish"));
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

    // public function webgameReview(Request $request, $appid)
    // {
    //     $userInfo = $request->userinfo;
    //     $devModel = new DevModel;
    //     $appInfo  = $devModel->getWebgameInfo($appid);
    //     if ($appInfo['uid'] != $userInfo['uid']) {
    //         return Library::output(1);
    //     }
    //     if ($appInfo['is_deal'] != 1 || strlen($appInfo['cp_soft']) < 1 || strlen($appInfo['cp_record']) < 1 || strlen($appInfo['cp_publish']) < 1 || $appInfo['img_version'] < 1 || strlen($appInfo['img_slider']) < 1) {
    //         return Library::output(1, '', "资料未填写完整");
    //     }

    //     $devModel = new DevModel;
    //     $ret      = $devModel->updWebgameInfo($appid, array('stat' => 1));
    //     return Library::output(0, array('appid' => $appid, 'ret' => $ret));
    // }

    // public function webGamePublish(Request $request, $appid)
    // {
    //     $userInfo = $request->userinfo;
    //     $devModel = new DevModel;
    //     $appInfo  = $devModel->getWebgameInfo($appid);
    //     if ($appInfo['uid'] != $userInfo['uid']) {
    //         return Library::output(1);
    //     }
    //     if ($appInfo['stat'] != 5) {
    //         return Library::output(2501);
    //     }
    //     if ($appInfo['send_time'] > 0) {
    //         return Library::output(2502);
    //     }

    //     $info     = array('send_time' => time());
    //     $devModel = new DevModel;
    //     $ret      = $devModel->updWebgameInfo($appid, $info);

    //     $webModel = new WebgameModel;
    //     $ret      = $webModel->setGameInfo($appid, $info);

    //     $ret = $devModel->updWebgameServer($appid, array('is_publish' => 1));
    //     return Library::output(0, array('appid' => $appid, 'ret' => $ret));
    // }

}
