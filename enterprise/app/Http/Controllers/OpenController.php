<?php

// 网页游戏相关

namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller; // 使用open的Helper

use App\Models\DevModel;
use App\Models\OpenModel;
use Config;
use Helper\AccountCenter as Account;
use Helper\Library;
use Illuminate\Http\Request;
use Redirect;

class OpenController extends Controller
{
    /**
     * 每页显示记录数量
     */
    private $pagesize = 20;

    /**
     * open后台保存用户图片、文件地址
     */
    private $devUserImages = "/opt/wwwroot/enterprise/public/dev/user/";

    /**
     * 线上保存用户图片、文件地址
     * 必须要有最后的斜杠
     */
    private $imgUserImages = "dev/user/";

    /**
     * openid后台保存页游上传图片、文件地址
     */
    private $devWebgameImages = "/data/wwwroot/enterprise/public/dev/";

    /**
     * 线上保存页游上传图片、文件地址
     * 必须要有最后的斜杠
     */
    private $imgWebgameImages = "webgame/";

    public function __construct()
    {
        $this->middleware("vrauth:0:dev_admin", ['only' => ["userNeedReview", "userReview", "reviewUserInfo", "appReviewList", "reviewAppInfo"]]);
        $this->middleware("vrauth:json:dev_admin", ['only' => ["goReviewApp", "reviewUser"]]);
        $this->middleware("vrauth:0", ['only' => ["applyHome", "userApply", "openAgreement"]]);
        $this->middleware("vrauth:json", ['only' => ["applyUserInfo", "goReviewUser", "resendActiveEmail"]]);
        $this->middleware("vrauth:0:dev", ['only' => ["getDeveloperInfo"]]);
        $this->middleware("vrauth:json:dev_master", ['only' => ["addSonAccountCheck", "addSonAccount", "getSonPerms", "addSonPerms", "delSonAccount"]]);
    }

    /**
     * 需要审核的用户列表
     */
    public function userNeedReview(Request $request)
    {
        $userInfo = $request->userinfo;

        $stat     = Config::get("status.dev.user.wait_review");
        $devModel = new DevModel;
        $users    = $devModel->getUserByStat($stat, $this->pagesize);
        $outlist  = $users->items();
        return view('open.review.review_user_list', ['users' => $users, "user" => $userInfo, "outlist" => $outlist, "nav" => "review", "tag" => "user"]);
    }

    public function userReview(Request $request, $reviewStat)
    {
        $userInfo = $request->userinfo;

        if (!isset($reviewStat)) {
            $reviewStat = 0;
        }
        $stat = $reviewStat;

        $devModel = new DevModel;
        $users    = $devModel->getUserByStat($stat, $this->pagesize);

        $left       = $reviewStat == 0 ? "online" : "offline";
        $right      = $reviewStat == 0 ? "offline" : "online";
        $offlineNum = $devModel->getUserByStatCount(Config::get("status.dev.user.wait_pass"));
        $onlineNum  = $devModel->getUserByStatCount(Config::get("status.dev.user.review_review"));

        $statArr   = [0, 1, 5, 9];
        $onlineNum = [];
        foreach ($statArr as $v) {
            $onlineNum[$v] = $devModel->getUserByStatCount($v);
        }

        $outlist = $users->items();
        // $category = Config::get("{$gametype}.class");

        return view('open.review.review_user_list', ['left' => $left, 'right' => $right, 'users' => $users, "user" => $userInfo, "offlineNum" => $offlineNum, "onlineNum" => $onlineNum, "outlist" => $outlist, "nav" => "review", "tag" => "user", 'reviewStat' => $reviewStat]);
    }

    /**
     * 需要审核的用户信息
     */
    public function reviewUserInfo(Request $request, $target_uid)
    {
        $userInfo  = $request->userinfo;
        $openModel = new OpenModel;
        $info      = $openModel->getDevUserInfo($target_uid);
        return view('open.review.review_user', ['user' => $userInfo, 'info' => $info, "tag" => "review", "curr" => "user", "nav" => "review", "tag" => "user"]);
    }

    /**
     * 审核
     * 提示审核结果
     * 跳回审核列表页面
     * @param   int     target_uid  用户uid
     * @param   string  action      操作，pass:审核通过，deny:审核拒绝
     * @param   string  msg         审核拒绝的原因
     */
    public function reviewUser(Request $request, $target_uid)
    {
        $target_uid = intval($target_uid);
        $action     = trim($request->input('action'));
        $msg        = trim($request->input('msg'));
        if (!$target_uid || !$action) {
            return Library::output(2001);
        }
        switch ($action) {
            case "pass":
                $newstat = Config::get("status.dev.user.review_pass");
                break;
            case "deny":
                $newstat = Config::get("status.dev.user.review_deny");
                break;
            default:$newstat = 1;
                break;
        }
        if (!$msg) {
            $msg = "";
        }

        /**
         * 读用户信息，判断用户状态
         */
        $devModel = new DevModel;
        $users    = $devModel->getUser($target_uid);
        if (!$users || !is_array($users)) {
            return Library::output(2404);
        }
        if ($users['stat'] != Config::get("status.dev.user.wait_review")) {
            return Library::output(2402);
        }

        /**
         * 审核成功
         * 将用户信息同步到线上
         * 将图片等素材同步到线上
         */
        if ($action == "pass") {
            $cpRet = ImageHelper::openCopyFiles('openuser', $target_uid);
            if (!$cpRet) {
                return Library::output(2407);
            }
        }

        /**
         * 审核，修改信息
         */
        $info = array('stat' => $newstat, "msg" => $msg);
        if ($action == "pass") {
            $info['reviewed'] = 1;
        }
        $ret = $devModel->updUser($target_uid, $info);
        if ($ret === false) {
            // 跳到失败页面
            return Library::output(2403);
        } else {
            return Library::output(0);
        }
    }

    /*
     * 要审核的App
     */
    public function appReviewList(Request $request, $gametype, $reviewStat = 0)
    {
        $userInfo = $request->userinfo;
        if ($gametype == "vrgame") {
            $game_type = 1;
        } else {
            $game_type = 0;
        }
        if ($reviewStat != 0) {
            $stat = Config::get("status.dev.user.review_pass");
        } else {
            $stat = Config::get("status.dev.user.wait_review");
        }

        $devModel = new DevModel;
        $webgames = $devModel->getWebgameByStat($game_type, $stat, $this->pagesize);
        $num      = $devModel->getReviewWebgameCount($game_type, $reviewStat);

        $left  = $reviewStat == 0 ? "online" : "offline";
        $right = $reviewStat == 0 ? "offline" : "online";

        $offlineNum = $reviewStat == 0 ? $webgames->total() : $num;
        $onlineNum  = $reviewStat == 0 ? $num : $webgames->total();

        $outlist  = $webgames->items();
        $category = Config::get("{$gametype}.class");

        return view('open.review.review_webgame_list', ['left' => $left, 'right' => $right, 'webgames' => $webgames, "user" => $userInfo, "offlineNum" => $offlineNum, "onlineNum" => $onlineNum, "outlist" => $outlist, "category" => $category, "nav" => "review", "tag" => $gametype]);
    }

    /**
     * 审核
     * 提示审核结果
     * 跳回审核列表页面
     */
    public function goReviewApp(Request $request, $appid)
    {
        $appid  = intval($appid);
        $action = trim($request->input('action'));
        $msg    = trim($request->input('msg'));
        if (!$appid || !$action) {
            return Library::output(2001);
        }
        switch ($action) {
            case "pass":
                $newstat = Config::get("status.dev.webgame.review_pass");
                break;
            case "deny":
                $newstat = Config::get("status.dev.webgame.review_deny");
                break;
            default:
                return Library::output(2001);
                break;
        }
        $isnew    = false;
        $devModel = new DevModel;
        $webgame  = $devModel->getWebgameInfo($appid);
        if (!$webgame || !is_array($webgame)) {
            return Library::output(2405);
        }
        if ($webgame['stat'] != Config::get("status.dev.webgame.wait_review")) {
            return Library::output(2406);
        }

        if ($action == "pass") {
            //ImageHelper::openCopyFiles('openapp', $appid);
            $openModel = new OpenModel;
            $ret       = $openModel->reviewPassApp($webgame);
            if ($ret === "error:rsync_data") {
                return Library::output(2409);

            } else if ($ret === "error:rsync_pic") {
                return Library::output(2407);
            } else if (!$ret) {
                return Library::output(1);
            }
        }
        /**
         * 审核，修改信息
         */
        $info = array('stat' => $newstat, "msg" => $msg, "isclient" => 0);
        if ($isnew) {
            $info['push_time'] = time();
        }
        $ret = $devModel->updWebgameInfo($appid, $info);
        if ($ret === false) {
            return Library::output(2403);
        }
        $okMsg = $action == "pass" ? "审核成功" : "驳回成功";
        return Library::output(0, '', $okMsg);
    }

    /**
     * 要审核的页游详细信息
     */
    public function reviewAppInfo(Request $request, $gametype, $type, $appid)
    {

        if ($gametype == "vrgame") {
            $game_type = 1;
        } else {
            $game_type = 0;
        }

        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $appid    = intval($appid);
        if (!$appid) {
            Library::output(1);
        }

        /**
         * 读app信息，判断app状态
         */
        $openModel = new OpenModel;
        $webgame   = $openModel->getDevWebgameInfo($appid, true);
        if (!$webgame || !is_array($webgame)) {
            return Library::output(1);
        }

        $webgame['status_dec'] = Config::get("status.dev.webgame_stat.{$webgame['stat']}");

        $category            = Config::get("{$gametype}.class");
        $webgame['category'] = isset($category[$webgame['first_class']]['name']) ? $category[$webgame['first_class']]['name'] : "";

        if ($type == "right") {
            $resInfo         = ImageHelper::path('openapp', $appid);
            $webgame['base'] = $resInfo['base'];
            return view('open.review.review_webgame_right', ['webgame' => $webgame, 'user' => $userInfo, "type" => $type, "appid" => $appid, "nav" => "review", "tag" => $gametype]);
        } else if ($type == "agreement") {
            return view('open.review.review_webgame_agreement', ['webgame' => $webgame, 'user' => $userInfo, "type" => $type, "appid" => $appid, "nav" => "review", "tag" => $gametype]);
        } else {
            $openModel = new OpenModel;
            if ($game_type == 0) {
                $imgtp = 'webgame';
            } else {
                $imgtp = 'vrgame';
            }
            $resInfo = ImageHelper::url($imgtp, $appid, $webgame['img_version'], $webgame['img_slider'], true, $webgame['screenshots']);
            if (is_array($resInfo) && $resInfo) {
                $webgame['logo'] = $resInfo['logo'];
                $webgame['icon'] = $resInfo['icon'];
                $webgame['rank'] = $resInfo['rank'];
                if (isset($resInfo['bg'])) {
                    $webgame['bg'] = $resInfo['bg'];
                }
                if (isset($resInfo['bg2'])) {
                    $webgame['bg2'] = $resInfo['bg2'];
                }
                if (isset($resInfo['card'])) {
                    $webgame['card'] = $resInfo['card'];
                }
                if ($webgame['img_slider']) {
                    $webgame['slider'] = $resInfo['slider'];
                }
                if ($webgame['screenshots']) {
                    $webgame['screenshots'] = $resInfo['screenshots'];
                }
            }

            return view('open.review.review_webgame', ['webgame' => $webgame, 'user' => $userInfo, "type" => $type, "appid" => $appid, "nav" => "review", "tag" => $gametype]);
        }
    }

    public function applyHome(Request $request)
    {
        $userInfo = $request->userinfo;
        $devModel = new DevModel;
        $devInfo  = $devModel->getUser($userInfo['uid']);
        if (empty($devInfo)) {
            return view('open/apply.home', ['user' => $userInfo]);
        } else {
            $action = $devInfo['type'] == 1 ? "company" : "user";
            return redirect('/userApply/' . $action);
        }

    }

    public function userApply(Request $request, $action)
    {
        $baseInfo         = $request->userinfo;
        $uid              = $baseInfo['uid'];
        $name             = $baseInfo['nick'];
        $baseInfo['name'] = $name;
        $token            = $baseInfo['token'];

        // if ($baseInfo['reviewed'] == 1) {
        //  return redirect('/userApply/' . $action);
        // }

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $userInfoArr  = $accountModel->info($uid, $token);
        //获取用户是否绑定手机号的逻辑
        if (empty($userInfoArr['data'])) {
            return Library::output(1);
        }

        $bindMobile = $userInfoArr['data']['bindmobile'] == '' ? '' : $userInfoArr['data']['bindmobile'];
        $userName   = '';
        $idCard     = '';
        $email      = '';
        $address    = '';
        $province   = "";
        $city       = "";
        $deal       = "";
        $preview    = '<div class="preview" style="display:none"></div>';
        $contacts   = "";
        $devStat    = 0;
        $devModel   = new DevModel;
        $devInfo    = $devModel->getUser($uid);
        if (empty($devInfo)) {
            if ($action == "edit") {
                return Library::output(1);
            }
        } else {
            if ($action == "edit") {
                $action    = $devInfo['type'] == 1 ? "company" : "user";
                $userName  = $devInfo['name'];
                $idCard    = $devInfo['idcard'];
                $email     = $devInfo['email'];
                $address   = $devInfo['address'];
                $tmpAddr   = explode("|", $address);
                $province  = $tmpAddr[0];
                $city      = $tmpAddr[1];
                $address   = str_replace($province . "|" . $city . "|", "", $address);
                $deal      = 'checked=true disabled';
                $contacts  = $devInfo['contacts'];
                $devStat   = $devInfo['stat'];
                $resInfo   = ImageHelper::path('openuser', $uid);
                $idcardUrl = $resInfo['base'] . 'idcard.jpg?v=' . $devInfo['pic_version'];
                $preview   = '<div class="preview"><a href="' . $idcardUrl . '" target="_blank"><img src="' . $idcardUrl . '" width="100%" height="100%"/></a></div>';
            } else {
                if ($devInfo['isactive'] === 1) {
                    $cfg                = Config::get("status.dev.reg_stat");
                    $devInfo['statVal'] = $cfg[$devInfo['stat']];
                    return view('open/apply.regSuccess', ['code' => 0, 'msg' => '激活成功', 'user' => $baseInfo, 'devInfo' => $devInfo]);
                }
                $email = isset($devInfo['email']) ? $devInfo['email'] : '';
                if ($email !== '') {
                    return view('open/apply.authEmail', ['uid' => $uid, 'name' => $name, 'email' => $email, 'user' => $baseInfo]);
                }
            }
        }

        $out = ['uid' => $uid, 'name' => $name, 'user' => $baseInfo, 'bindMobile' => $bindMobile, 'action' => $action, 'userName' => $userName, 'idCard' => $idCard, 'email' => $email, 'address' => $address, 'province' => $province, 'city' => $city, 'deal' => $deal, 'preview' => $preview, 'contacts' => $contacts, 'stat' => $devStat];

        return view('open/apply.info', $out);
    }

    public function openAgreement(Request $request)
    {
        $baseInfo         = $request->userinfo;
        $uid              = $baseInfo['uid'];
        $name             = $baseInfo['nick'];
        $baseInfo['name'] = $name;
        $token            = $baseInfo['token'];
        $out              = ['uid' => $uid, 'name' => $name, 'user' => $baseInfo];
        return view('open.dev', $out);
    }
    /**
     * 上传申请信息的表单
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function applyUserInfo(Request $request, $action)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];

        $name     = $request->input('userName');
        $idCard   = $request->input('idCard');
        $email    = $request->input('email');
        $province = $request->input('province');
        $city     = $request->input('city');
        $address  = $request->input('address');
        $contacts = $request->input('connector');

        $addressAll = $province . '|' . $city . '|' . $address;
        if (!$uid || !$name || strlen($idCard) < 10 || strlen($email) < 4 || strlen($addressAll) < 5) {
            return Library::output(1);
        }

        $type = $action == "company" ? 1 : 2;

        $userInfoArr = array(
            'name'        => $name,
            'idcard'      => $idCard,
            'email'       => $email,
            'address'     => $addressAll,
            'pic_version' => time(),
            'type'        => $type,
        );

        if ($type === 1) {
            $userInfoArr['contacts'] = $contacts;
        } else {
            $userInfoArr['contacts'] = $name;
        }
        $sendMail = false;
        $devModel = new DevModel();
        $devInfo  = $devModel->getUser($uid);
        if (empty($devInfo)) {
            $sendMail = true;
            $result   = $devModel->addUser($uid, $userInfoArr);
        } else {
            if ($devInfo['stat'] == 1 || $devInfo['stat'] == 5) {
                return Library::output(2505);
            }
            if ($devInfo['email'] != $email) {
                $userInfoArr['isactive'] = 0;
                $sendMail                = true;
            }
            $userInfoArr['stat'] = 0;
            unset($userInfoArr['type']);
            $result = $devModel->updUser($uid, $userInfoArr);
        }

        if ($result) {
            if ($sendMail) {
                $openModel = new OpenModel;
                $openModel->setActiveEmailCode($uid);
                $activeCode = $openModel->getActiveEmailCode($uid);
                $msgDataArr = array(
                    'uid'        => $uid,
                    'title'      => '请点击邮件里激活链接，激活您的账号！',
                    'activeCode' => $activeCode,
                );
                $openModel->sendVerifyMail($email, $name, $msgDataArr);
            }
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 重新发送激活邮件
     * @param Request $request
     * @return string
     */
    public function resendActiveEmail(Request $request)
    {

        $openModel = new OpenModel;
        $name      = $request->input('userName');
        $email     = $request->input('email');

        $baseInfo = $request->userinfo;
        $uid      = $baseInfo['uid'];

        if (!$uid || !$name || !$email) {
            return Library::output(1);
        }

        $openModel->setActiveEmailCode($uid);
        $code       = $openModel->getActiveEmailCode($uid);
        $msgDataArr = array(
            'uid'        => $uid,
            'title'      => '请点击邮件里激活链接，激活您的账号！',
            'activeCode' => $code,
        );
        $openModel->sendVerifyMail($email, $name, $msgDataArr);
        return Library::output(0);
    }

    // 激活邮件
    public function authActiveEmail(Request $request)
    {
        $requestUid = $request->input('uid');

        //$baseInfo = $request->userinfo;
        $uid = $requestUid;

        /**
         * 如果当前登录用户不是验证用户
         * 退出当前登录用户
         */

//      if ($uid != $requestUid) {
        //          $params = array('uid', 'token', 'account', 'nick', 'face', 'type');
        //          CookieModel::clearCookieArr($params);
        //          Session::flush();
        //      }

        $openModel = new OpenModel;
        $devModel  = new DevModel();
        $devInfo   = $devModel->getUser($requestUid);
        if (empty($devInfo)) {
            $baseInfo['name'] = "";
            return view('open/apply.authEmail', ['code' => 1, 'msg' => '验证失败', 'code' => "code=1", 'user' => $baseInfo, 'name' => "", "email" => "", "uid" => $requestUid, "cont" => 2, "nologin" => 1]);
        }
        $baseInfo['name'] = $devInfo['name'];
        $name             = $devInfo['name'];
        $email            = $devInfo['email'];

        $activeCode = $request->input('activeCode');
        if (!$activeCode) {
            return view('open/apply.authEmail', ['code' => 1, 'msg' => '验证失败', 'code' => "code=3", 'user' => $baseInfo, 'uid' => $uid, 'name' => $name, "email" => $email]);
        }
        $cfg                = Config::get("status.dev.reg_stat");
        $devInfo['statVal'] = $cfg[$devInfo['stat']];
        if ($devInfo['isactive'] === 1) {
            // 直接提交审核
            $flag = $this->commReviewUser($uid);
            if ($flag) {
                $devInfo['stat'] = 1;
            }
            return view('open/apply.regSuccess', ['code' => 0, 'msg' => '激活成功', 'code' => "code=4", 'user' => $baseInfo, 'devInfo' => $devInfo]);
        }
        $code = $openModel->getActiveEmailCode($uid);

        if ($code === $activeCode) {
            $infoArr = array('isactive' => 1);
            $ret     = $devModel->updUser($uid, $infoArr);
            if (!$ret) {
                return view('open/apply.authEmail', ['uid' => $uid, 'name' => $name, 'email' => $email, 'user' => $baseInfo, 'msg' => '验证链接已经失效，请重新发送邮件']);
            }
            $openModel->delActiveEmailCode($uid);
            // 直接提交审核
            $flag = $this->commReviewUser($uid);
            if ($flag) {
                $devInfo['stat'] = 1;
            }
            return view('open/apply.regSuccess', ['code' => 0, 'msg' => '激活成功', 'user' => $baseInfo, 'devInfo' => $devInfo]);
        } else {
            return view('open/apply.authEmail', ['uid' => $uid, 'name' => $name, 'email' => $email, 'user' => $baseInfo, 'msg' => '验证链接已经失效，请重新发送邮件']);
        }
    }

    // 公用的验证邮箱成功后，直接提交审核
    public function commReviewUser($uid)
    {
        if (!$uid) {
            return Library::output(1);
        }
        $devModel = new DevModel();
        $devInfo  = $devModel->getUser($uid);
        if (empty($devInfo)) {
            return Library::output(1);
        }
        if ($devInfo['isactive'] != 1) {
            return Library::output(1);
        }
        if ($devInfo['stat'] == 1 || $devInfo['stat'] == 5) {
            return Library::output(1);
        }
        $userInfoArr['stat'] = 1;
        $result              = $devModel->updUser($uid, $userInfoArr);
        if ($result) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function goReviewUser(Request $request)
    {
        $baseInfo = $request->userinfo;
        $uid      = $baseInfo['uid'];
        $devModel = new DevModel();
        $devInfo  = $devModel->getUser($uid);
        if (empty($devInfo)) {
            return Library::output(1);
        }
        if ($devInfo['isactive'] != 1) {
            return Library::output(1);
        }
        if ($devInfo['stat'] == 1 || $devInfo['stat'] == 5) {
            return Library::output(1);
        }
        $userInfoArr['stat'] = 1;
        $result              = $devModel->updUser($uid, $userInfoArr);
        if ($result) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 开发者详情资料页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getDeveloperInfo(Request $request)
    {
        $baseInfo = $request->userinfo;
        $uid      = $baseInfo['uid'];

        $devModel = new DevModel;
        $devInfo  = $devModel->getUser($uid);
        if (empty($devInfo)) {
            return redirect()->route('/', 302, [], true);
        }
        $devInfo['url'] = ImageHelper::path('openuser', $uid);
        return view('open/developerDetail', ['data' => $devInfo, 'type' => $devInfo['type'], 'user' => $baseInfo, 'nav' => 'my']);
    }

    /**
     * 验证添加open后台的子账号是否合法
     * [addSonAccount description]
     * @param Request $request [description]
     */
    public function addSonAccountCheck(Request $request)
    {
        $userInfo   = $request->userinfo;
        $sonAccount = $request->input('account');
        if ($sonAccount == '') {
            return Library::output(1102);
        }

        $account = isset($userInfo['account']) ? $userInfo['account'] : '';

        if ($account == $sonAccount) {
            return Library::output(2015);
        }

        $check = $this->checkSonAccount($sonAccount);
        if (isset($check['code']) && $check['code'] !== 0) {
            return $check;
        }

        $sonInfo = isset($check['data']) ? $check['data'] : '';

        $retArr = [
            'mobile' => isset($sonInfo['bindmobile']) ? $sonInfo['bindmobile'] : '',
        ];

        return Library::output(0, $retArr);
    }

    public function addSonSendMsg(Request $request)
    {
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);

        $account = $request->input('account');
        $mobile  = $request->input('mobile');

        if (!$account || !$mobile) {
            return Library::output(2001);
        }
        $ret = $accountModel->sendSmsMsg($account, $mobile);
        if (!$ret || $ret['code'] !== 0) {
            return $ret;
        }

        return Library::output(0);
    }

    public function addSonCheckMsg($account, $mobile, $code)
    {
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);

        if (!$account || !$mobile || !$code) {
            return Library::output(2001);
        }
        $ret = $accountModel->addSonCheckMsg($account, $mobile, $code);
        if (isset($ret['code']) && $ret['code'] !== 0) {
            return $ret;
        }
        $result['code'] = 0;
        $result['msg']  = '验证码正确';
        return $result;
    }

    public function addSonAccount(Request $request)
    {
        $userInfo = $request->userinfo;

        $parentId = isset($userInfo['uid']) ? $userInfo['uid'] : '';

        if (!$parentId) {
            return Library::output(2410);
        }
        $devModel   = new DevModel;
        $sonAccount = $request->input('account');
        $mobile     = $request->input('mobile');
        $code       = $request->input('code');

        $checkCode = $this->addSonCheckMsg($sonAccount, $mobile, $code);

        if (isset($checkCode['code']) && $checkCode['code'] !== 0) {
            return json_encode($checkCode);
        }

        $checkAccount = $this->checkSonAccount($sonAccount);
        if (isset($checkAccount['code']) && $checkAccount['code'] !== 0) {
            return $checkAccount;
        }

        $sonInfo = isset($checkAccount['data']) ? $checkAccount['data'] : '';

        $sonUid  = $sonInfo['uid'];
        $sonName = $sonInfo['account'];

        $sonAccountInfo = $devModel->getSonUser($parentId);
        if (!empty($sonAccountInfo) && count($sonAccountInfo) > 2) {
            return Library::output(2016);
        }
        $devInfo = $devModel->getUser($parentId);

        $addSonInfo = [
            'uid'      => $sonUid,
            'name'     => $devInfo['name'],
            'contacts' => $sonName,
            'reviewed' => 1,
            'stat'     => 5,
            'type'     => $devInfo['type'],
            'isactive' => 1,
            'parentId' => $parentId,
        ];

        $addRet = $devModel->addUser($sonUid, $addSonInfo);

        if (!$addRet) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    public function delSonAccount(Request $request)
    {
        $userInfo = $request->userinfo;

        $parentId = isset($userInfo['uid']) ? $userInfo['uid'] : '';
        if (!$parentId) {
            return Library::output(2410);
        }
        $sonUid = $request->input('uid');
        if (!$sonUid) {
            return Library::output(2001);
        }

        $devModel = new DevModel;

        $ret = $devModel->delSonAccount($sonUid);
        if (!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    public function getSonPerms(Request $request)
    {
        $userInfo = $request->userinfo;
        $parentId = isset($userInfo['uid']) ? $userInfo['uid'] : '';
        if (!$parentId) {
            return Library::output(2410);
        }
        $sonUid = $request->input('uid');

        if (!$sonUid) {
            return Library::output(2001);
        }
        $devModel = new DevModel;
        $accounts = $devModel->getSonUser($parentId);
        $out      = [];
        foreach ($accounts as $value) {
            if ($value['uid'] == $sonUid) {
                if (!$value['perms']) {
                    $perms = [];
                } else {
                    $perms = json_decode($value['perms'], true);
                }
                $out = ['perms' => $perms];
                break;
            }
        }
        if (!$out) {
            return Library::output(1);
        }
        return Library::output(0, $out);
    }

    public function addSonPerms(Request $request)
    {
        $userInfo = $request->userinfo;

        $parentId = isset($userInfo['uid']) ? $userInfo['uid'] : '';
        if (!$parentId) {
            return Library::output(2410);
        }
        $sonUid = $request->input('uid');
        $perms  = $request->input('perms');

        if (!$sonUid || !$perms) {
            return Library::output(2001);
        }
        $devModel = new DevModel;
        $info     = [
            'perms' => $perms,
        ];
        $ret = $devModel->updUser($sonUid, $info);
        if (!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    private function checkSonAccount($sonAccount)
    {
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);

        //先判断是否是open后台的账号
        $isExists = $accountModel->isExists($sonAccount);
        if (isset($isExists['code']) && $isExists['code'] === 0) {
            return Library::output(1302);
        }

        $sonUid = '';
        if (isset($isExists['data']['uid'])) {
            $sonUid = $isExists['data']['uid'];
        }

        $devModel = new DevModel;
        $devInfo  = $devModel->getUser($sonUid);

        if ($devInfo) {
            return Library::output(2014);
        }

        // $keyCode     = 'vrOnline_comment';
        $userInfoArr = $accountModel->getUserInfoByAdmin($sonUid);

        return $userInfoArr;
    }

}
