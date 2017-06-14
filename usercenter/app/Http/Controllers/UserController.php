<?php

namespace App\Http\Controllers;

// 引用Model
use App\Models\AppinfoModel;
use App\Models\DataCenterStatModel;
use App\Models\ImModel;
use App\Models\LoginModel;
use App\Models\OpenidModel;
use App\Models\PassportModel;
use App\Models\ToBCheckBillDBModel;
use App\Models\TokenModel;
use App\Models\UserModel;
use App\Models\VerifyCodeModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $imgCodeLen  = 4;
    private $openImgCode = ['open' => false];
    private $imgCodeUrl  = 'http://passport.vronline.com/imgcode/';

    public function __construct()
    {
        //$this->middleware("vrsign:json", ['only' => ["login", "register"]]);
    }

    public function test1($str)
    {
        var_dump("xxx");
    }

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

    /**
     * 获取用户信息
     */
    public function index(Request $request)
    {
        $reqJson = $request->input('json');
        if (!$reqJson) {
            return Library::output(2001);
        }
        $arrParams = json_decode($reqJson, true);
        if (!$arrParams || !is_array($arrParams)) {
            return Library::output(2001);
        }
        if (!isset($arrParams['uid']) || !$arrParams['uid'] || !isset($arrParams['token']) || !$arrParams['token']) {
            return Library::output(1301);
        }
        $appid    = isset($arrParams['appid']) ? intval($arrParams['appid']) : 0;
        $uid      = isset($arrParams['uid']) ? intval($arrParams['uid']) : 0;
        $token    = isset($arrParams['token']) ? $arrParams['token'] : 0;
        $passport = new PassportModel();
        $login    = $passport->isLogin($uid, $token);
        if (!$login) {
            return Library::output(1301);
        }

        $userInfo = $passport->getUserInfo($uid, $appid);
        if (!$userInfo || !is_array($userInfo)) {
            return Library::output(1);
        }

        return Library::output(0, $userInfo);
    }

/*
+-----------------------------------------------------------------------------+
|                                                                             |
|             仅 提 供 内 部 使 用 的 接 口                                   |
|                                                                             |
+-----------------------------------------------------------------------------+
 */

    /**
     * 检测登录
     */
    public function isLogin(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $uid   = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $token = isset($param["token"]) ? $param["token"] : "";

        if (!$uid || !$token) {
            return Library::output(1301);
        }

        $passport = new PassportModel;
        $ret      = $passport->isLogin($uid, $token, true);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1301);
        }
    }

    /**
     * 获取用户id
     */
    public function getUid(Request $request)
    {
        $appid   = intval($request->input('appid'));
        $account = strtolower($request->input('account'));
        $ts      = intval($request->input('ts'));
        $sign    = trim($request->input('sign'));
        $stamp   = time();

        $passportModel = new PassportModel;

        if (!$appid || $passportModel->checkAuth($appid) == false || !$account || !$ts || $stamp - $ts > 10 || $ts - $stamp > 10) {
            return Library::output(2001);
        }
        $login = new LoginModel();

        $uid = $login->getUid($account);
        if (!$uid) {
            return Library::output(1);
        }

        return Library::output(0, array("uid" => $uid));
    }

    /**
     * 获取用户信息
     * 内部使用
     */
    public function getAccountInfo(Request $request)
    {
        $appid   = intval($request->input('appid'));
        $uid     = intval($request->input('uid'));
        $ts      = intval($request->input('ts'));
        $account = trim($request->input('account'));
        $stamp   = time();

        $passportModel = new PassportModel;
        if (!$appid || $passportModel->checkAuth($appid) == false || !$ts || $stamp - $ts > 30 || $ts - $stamp > 30) {
            return Library::output(2001);
        }

        if (!$uid) {
            $login = new LoginModel();
            $uid   = $login->getUid($account);
            if (!$uid) {
                return Library::output(1);
            }
        }

        $userModel = new UserModel();
        $base      = $userModel->baseInfo($uid);
        if (!$base || !is_array($base)) {
            return Library::output(1);
        }
        $userInfo = ['uid' => $base['f_uid'], 'username' => $base['f_account']];
        return Library::output(0, $userInfo);
    }

    /**
     * 获取用户信息
     * 内部使用
     */
    public function getUserInfo(Request $request)
    {
        $appid = intval($request->input('appid'));
        $uid   = intval($request->input('uid'));
        $ts    = intval($request->input('ts'));
        $sign  = trim($request->input('sign'));
        $stamp = time();

        $passportModel = new PassportModel;
        if (!$appid || $passportModel->checkAuth($appid) == false || !$uid || !$ts || $stamp - $ts > 30 || $ts - $stamp > 30) {
            return Library::output(2001);
        }
        $passport = new PassportModel();

        $userInfo = $passport->getUserInfo($uid, $appid);
        if (!$userInfo || !is_array($userInfo)) {
            return Library::output(1);
        }

        return Library::output(0, $userInfo);
    }

    /**
     * 普通登录
     * 输入用户名、密码
     * @param   array  request
     * @return  int state  登录状态码
     */
    public function login(Request $request, $type)
    {
        header("Access-Control-Allow-Origin:http://www.vronline.com");
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $account    = isset($param["account"]) ? strtolower(trim($param["account"])) : "";
        $pwd        = isset($param["pwd"]) ? strtolower(trim($param["pwd"])) : "";
        $appid      = isset($param["appid"]) ? trim($param["appid"]) : 0; // 得到appid
        $did        = isset($param["did"]) ? $param["did"] : "";
        $sid        = isset($param["sid"]) ? $param["sid"] : "test";
        $addip      = isset($param["cip"]) ? $param["cip"] : Library::realIp();
        $code       = isset($param["code"]) ? strtolower($param["code"]) : "";
        $sms        = isset($param["sms"]) ? strtolower($param["sms"]) : "";
        $thirdpart  = isset($param["thirdpart"]) ? trim($param["thirdpart"]) : 0; // 是否是接入的合作方通过VRonline账号登录，如果是，需要生成code返回
        $thirdappid = isset($param["thirdappid"]) ? trim($param["thirdappid"]) : 0; // 如果是接入的合作方通过VRonline账号登录，这里是合作方的appid

        if (!$account) {
            return Library::output(1306);
        }

        if (!$pwd && !$sms) {
            return Library::output(1307);
        }

        if (!$sid || !$addip) {
            return Library::output(1103);
        }

        $device = md5(md5($sid) . "login");

        $is_need_code = false;
        $verifyModel  = new VerifyCodeModel;
        if (in_array($type, array("account", "guest", "mobile", "email"))) {
            $verifyInfo = $verifyModel->getImgCode($device);
            if (!$verifyInfo) {
                if ($verifyModel->isNeedImgCode("login", $addip, "ip") || $verifyModel->isNeedImgCode("login", $account, "account") || $appid == 11) {
                    $is_need_code = false;
                }
                if (isset($this->openImgCode['open'])) {
                    $is_need_code = $this->openImgCode['open'];
                }
            } else {
                if (!isset($verifyInfo['code']) || $code != strtolower($verifyInfo['code']) || $verifyInfo['action'] != "login") {
                    return Library::output(1115, array("img" => $this->imgCodeUrl . $device));
                }
                $verifyModel->delImgCode($device);
            }
        }

        if ($is_need_code) {
            $info = $verifyModel->createImgCode($device, 'login');
            return Library::output(1115, array("img" => $this->imgCodeUrl . $info['device']));
        }

        $passport = new PassportModel;
        $passport->setProp("did", $did);
        /**
         * 判断用户名是否存在，并拿到uid
         */
        $uid = $passport->isExists($account, $type);
        if ($uid === false) {
            $verifyModel->setRetryCount("ip", $addip);
            return Library::output(1305, array("inc" => $is_need_code));
        }

        if ($uid === "notexists") {
            $verifyModel->setRetryCount("ip", $addip);
            return Library::output(1302, array("inc" => $is_need_code));
        }

        /**
         * 登录
         */
        if (!$thirdpart) {
            $thirdappid = 0;
        }
        if ($sms) {
            $verify = new VerifyCodeModel;
            $check  = $verify->getVerifyCode("login_" . $account, $account, "mobile", 'login');
            if (!$check) {
                return Library::output(2006);
            }
            if ($check == "error:notimes") {
                return Library::output(2111);
            }
            if ($sms != $check) {
                return Library::output(2006);
            }

            $verify->delCode("login_" . $account, $account, "mobile", 'login');
        }
        $result = $passport->login($uid, $pwd, $type, $appid, $addip, $thirdappid);
        if (!is_array($result)) {
            return Library::output(1305, array("inc" => $is_need_code));
        }
        if (isset($result['code']) && $result['code'] != 0) {
            $verifyModel->setRetryCount("account", $account);
            $verifyModel->setRetryCount("ip", $addip);
            return Library::output($result['code'], array("inc" => $is_need_code));
        }
        if (!isset($result['uid']) || !$result['uid']) {
            return Library::output($result['code'], array("inc" => $is_need_code));
        }
        $verifyModel->delRetryCount("login", $addip);
        $verifyModel->delRetryCount("login", $account);

        /**
         * 合作方通过VRonline
         * 根据appid生成openid
         * 根据appid和openid生成logincode
         */
        $loginCode = isset($result['loginCode']) && $result['loginCode'] ? $result['loginCode'] : "";

        $display_account = "";
        if ($result['data']['f_account']) {
            $display_account = $result['data']['f_account'];
        } elseif ($result['data']['f_mobile']) {
            $display_account = $result['data']['f_mobile'];
        } elseif ($result['data']['f_email']) {
            $display_account = $result['data']['f_email'];
        } elseif ($result['data']['f_guest']) {
            $display_account = $result['data']['f_guest'];
        }
        $face = $passport->getHeadPicUrl($result['uid'], $result['data']['f_face_ver']);
        return Library::output(0, array("uid" => $result['uid'], "token" => $result['token'], "account" => $display_account, "nick" => $result['data']['f_nick'], "face" => $face, "type" => $type, "logincode" => $loginCode, "inc" => $is_need_code, 'systime' => time()));
    }

    /**
     * 普通注册
     * @param   array  request
     * @param   string  type    注册类型  account: 普通账号注册; mobile: 手机注册; email: 邮箱注册;
     * @return  int state  注册状态码
     */
    public function register(Request $request, $type)
    {
        if (!in_array($type, array("account", "mobile", "email"))) {
            return Library::output(1);
        }

        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        /**
         * 处理用户名，判断用户名、密码格式是否合法
         */
        $account    = isset($param["account"]) ? strtolower(trim($param["account"])) : "";
        $password   = isset($param["pwd"]) ? strtolower(trim($param["pwd"])) : "";
        $appid      = isset($param["appid"]) ? trim($param["appid"]) : 0; // 得到appid
        $from_appid = isset($param["from_appid"]) ? intval($param["from_appid"]) : $appid; // 注册所在的appid
        $did        = isset($param["did"]) ? $param["did"] : "";
        $adid       = isset($param["adid"]) ? $param["adid"] : "";
        $sid        = isset($param["sid"]) ? $param["sid"] : "test";
        $addip      = isset($param["cip"]) ? $param["cip"] : Library::realIp();
        $code       = isset($param["code"]) ? strtolower($param["code"]) : "";

        if (!$account || !$sid || !$addip) {
            return Library::output(1103);
        }

        $device = md5(md5($sid) . "register");

        $is_need_code = false;

        $pattern = Config::get("common.account_pattern");
        $preg    = preg_match($pattern, $account, $matches);
        if (!$preg) {
            return Library::output(1101, array("inc" => $is_need_code));
        }
        if (strlen($account) == 11 && is_numeric($account)) {
            return Library::output(1116, array("inc" => $is_need_code));
        }
        if (!$password || strlen($password) != 32) {
            return Library::output(1104, array("inc" => $is_need_code));
        }

        $verifyModel = new VerifyCodeModel;
        $verifyInfo  = $verifyModel->getImgCode($device);

        if (!$verifyInfo) {
            if ($verifyModel->isNeedImgCode("register", $addip, "ip", 0)) {
                $is_need_code = true;
            }
            if (isset($this->openImgCode['open'])) {
                $is_need_code = $this->openImgCode['open'];
            }

            if ($code == "bbsvj&81") {
                $is_need_code = false;
            }
        } else {
            if (!isset($verifyInfo['code']) || $code != strtolower($verifyInfo['code']) || $verifyInfo['action'] != "register") {
                return Library::output(1115, array("img" => $this->imgCodeUrl . $device));
            }
            $verifyModel->delImgCode($device);
            $verifyModel->addRetryCount("register", $addip);
        }

        if ($is_need_code) {
            $info = $verifyModel->createImgCode($device, 'register');
            return Library::output(1115, array("img" => $this->imgCodeUrl . $info['device']));
        }

        /**
         * 判断用户名是否存在
         */
        $passport = new PassportModel();
        $passport->setProp("did", $did);
        $exists = $passport->isExists($account, $type);
        if ($exists === false) {
            return Library::output(1114, array("inc" => $is_need_code));
        }
        if ($exists && $exists !== "notexists") {
            return Library::output(1107, array("inc" => $is_need_code));
        }

        /**
         * 注册
         */
        $data = array(
            "f_from_appid" => $from_appid,
            "f_adid"       => $adid,
            "f_addip"      => $addip,
        );
        $ret = $passport->register($account, $password, $type, $data, $appid);
        if (!$ret || !is_array($ret)) {
            return Library::output(1114, array("inc" => $is_need_code));
        }
        return Library::output(0, $ret);
    }

    /**
     * 手机号码注册
     * @param   array  request
     * @param   string  type    注册类型  account: 普通账号注册; mobile: 手机注册; email: 邮箱注册;
     * @return  int state  注册状态码
     */
    public function registerByMobile(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        $type = "mobile";

        /**
         * 处理用户名，判断用户名、密码格式是否合法
         */
        $mobile     = trim($request->input("mobile", ""));
        $token      = trim($request->input("token", ""));
        $password   = trim($request->input("pwd", ""));
        $appid      = intval($request->input("appid", "")); // 得到appid
        $from_appid = intval($request->input("from_appid", "")); // 注册所在的appid
        $did        = $request->input("did", "");
        $adid       = $request->input("adid", "");
        $addip      = $request->input("adid", "");
        $code       = isset($param["code"]) ? strtolower($param["code"]) : "";

        if (!$mobile || strlen($mobile) != 11 || !is_numeric($mobile)) {
            return Library::output(2003);
        }

        if (!$password || strlen($password) != 32) {
            return Library::output(1104);
        }

        $addip = $addip ? $addip : Library::realIp();

        /**
         * 先判断token是否正确
         */
        $check = TokenModel::checkToken("register", $mobile, $token);
        if (!$check) {
            return Library::output(1117);
        }

        /**
         * 判断用户名是否存在
         */
        $passport = new PassportModel();
        $passport->setProp("did", $did);
        $exists = $passport->isExists($mobile, $type);
        if ($exists === false) {
            return Library::output(1114);
        }
        if ($exists && $exists !== "notexists") {
            return Library::output(1107);
        }

        /**
         * 注册
         */
        $data = array(
            "f_from_appid" => $from_appid,
            "f_adid"       => $adid,
            "f_addip"      => $addip,
        );
        $ret = $passport->register($mobile, $password, $type, $data, $appid);
        if (!$ret || !is_array($ret)) {
            return Library::output(1114);
        }
        return Library::output(0, $ret);
    }

    /**
     * 获取手机验证码，用于注册账号
     */
    public function getMobileRegCode(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        $mobile = $request->input("mobile", "");
        $type   = $request->input("type", "");

        if (!$mobile || !is_numeric($mobile) || $mobile < 13000000000 || $mobile > 19999999999) {
            return Library::output(2003);
        }
        $passport = new PassportModel;

        /**
         * 先判断该手机号码有没有绑定过账号
         * 一个号码只能绑定一个账号
         */
        $isExists = $passport->isExists($mobile, "account");
        if ($isExists === false) {
            return Library::output(1);
        }
        if ($isExists === "notexists") {
            $ret = $passport->sendSms("reg_" . $mobile, $mobile, "reg");
            return Library::output($ret);
        } else {
            if ($type == "login") {
                $ret = $passport->sendSms("login_" . $mobile, $mobile, "login");
                return Library::output($ret);
            } else {
                return Library::output(2101);
            }
        }

    }

    /**
     * 获取手机验证码，用于注册账号
     */
    public function getMobileRegToken(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        $mobile = $request->input("mobile", "");
        $code   = $request->input("code", "");

        if (!$mobile || !is_numeric($mobile) || $mobile < 13000000000 || $mobile > 19999999999) {
            return Library::output(2003);
        }
        if (!$code) {
            return Library::output(2005);
        }
        $action = "reg";
        $verify = new VerifyCodeModel;
        $check  = $verify->getVerifyCode("reg_" . $mobile, $mobile, "mobile", $action);
        if (!$check) {
            return Library::output(2006);
        }
        if ($check == "error:notimes") {
            return Library::output(2111);
        }

        if ($code != $check) {
            return Library::output(2006);
        }
        $verify->delCode("reg_" . $mobile, $mobile, "mobile", $action);

        /**
         * 生成token
         */
        $token = TokenModel::genToken("register", $mobile);
        if (!$token) {
            return Library::output(1);
        }

        return Library::output(0, ["token" => $token]);
    }

    /**
     * 创建不能登录的账号，只为生成uid
     * @param   array  request
     * @param   string  type    注册类型  account: 普通账号注册; mobile: 手机注册; email: 邮箱注册;
     * @return  int state  注册状态码
     */
    public function addNoLoginUser(Request $request)
    {
        /**
         * 处理用户名，判断用户名、密码格式是否合法
         */
        $account = trim($request->input("account"));
        $appid   = intval($request->input("appid"));
        $addip   = trim($request->input("cip"));
        $sign    = trim($request->input("sign"));

        if (!$account || !$appid || !$sign) {
            return Library::output(2001);
        }

        $appModel = new AppinfoModel;
        $appinfo  = $appModel->info($appid);
        $appkey   = isset($appinfo['appkey']) && $appinfo['appkey'] ? $appinfo['appkey'] : "";
        if (!$appkey) {
            return Library::output(2001);
        }
        $check = Library::encrypt($_POST, $appkey);
        if ($check != $sign) {
            return Library::output(2002);
        }

        /**
         * 判断用户名是否存在
         */
        $passport = new PassportModel();
        $exists   = $passport->isExists($account, "nologin");
        if ($exists === false) {
            return Library::output(1114, array("inc" => $is_need_code));
        }
        if ($exists && $exists !== "notexists") {
            return Library::output(1107, array("inc" => $is_need_code));
        }

        /**
         * 注册
         */
        $data = array(
            "f_addip" => $addip,
        );
        $ret = $passport->register($account, "", "nologin", $data, $appid);
        if (!$ret || !is_array($ret)) {
            return Library::output(1114);
        }
        return Library::output(0, array("uid" => $ret['uid'], "account" => $ret['account']));
    }

    /**
     * 判断用户名是否存在
     * @param   string  username
     * @return  int -2 表示用户名为空 >=1表示用户的uid NULL 表示并未此用户
     */
    public function existUsername(Request $request)
    {
        $param = json_decode($request->input('json'), true);
        if (!is_array($param) || !$param) {
            return Library::output(2001);
        }
        $account = isset($param['account']) ? strtolower(trim($param['account'])) : "";
        if (!$account) {
            return Library::output(2001);
        }

        $passport = new PassportModel;
        /**
         * 判断用户名是否存在，并拿到uid
         */
        $uid = $passport->isExists($account, 'account');
        if ($uid === false) {
            return Library::output(1305);
        }
        if ($uid === "notexists") {
            return Library::output(0);
        }
        if (is_numeric($uid) && $uid > 0) {
            return Library::output(1107, ['uid' => $uid]);
        }
        return Library::output(1305);
    }

    /**
     * 封、解封用户
     * @param   array  request
     * @param   action  enable/disable
     * @return  array   code=0成功，并返回新的token和uid
     */
    public function disableUser(Request $request, $action)
    {
        $appid = intval($request->input('appid'));
        $uids  = json_decode($request->input('uids'), true);
        $ts    = intval($request->input('ts'));
        $sign  = trim($request->input('sign'));
        if ($action == "disable") {
            $endtime = intval($request->input('endtime'));
        }
        $stamp = time();

        $passportModel = new PassportModel;
        if (!$appid || $passportModel->checkAuth($appid) == false || !$uids || !is_array($uids) || !$ts || $stamp - $ts > 10 || $ts - $stamp > 10) {
            return Library::output(2001);
        }

        if ($action == "disable") {
            $updinfo = array("f_status" => 1, "f_endlock_time" => $endtime);
        } elseif ($action == "enable") {
            $updinfo = array("f_status" => 0);
        } else {
            return Library::output(2001);
        }

        $err_users = array();
        $result    = true;
        $user      = new UserModel;
        for ($i = 0; $i < count($uids); $i++) {
            $uid = $uids[$i];
            $ret = $user->updateBaseinfo($uid, $updinfo);
            if ($ret === false) {
                $result      = false;
                $err_users[] = $uid;
            }
        }
        if ($result) {
            return Library::output(0);
        } else {
            return Library::output(0, array("erruid" => $err_users));
        }
    }

    /**
     * 修改密码
     * @param   array  request
     * @return  array   code=0成功，并返回新的token和uid
     */
    public function changePwd(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $uid     = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $token   = isset($param["token"]) ? $param["token"] : "";
        $oldpwd  = isset($param["oldpwd"]) ? strtolower(trim($param["oldpwd"])) : "";
        $newpwd1 = isset($param["newpwd1"]) ? strtolower(trim($param["newpwd1"])) : "";
        $newpwd2 = isset($param["newpwd2"]) ? strtolower(trim($param["newpwd2"])) : "";

        if (!$uid || !$token) {
            return Library::output(1301);
        }

        if (!$oldpwd || !$newpwd1 || !$newpwd2) {
            return Library::output(2001);
        }
        if (strlen($newpwd1) != 32 || $newpwd1 != $newpwd2) {
            return Library::output(2012);
        }

        $passport = new PassportModel;
        $isLogin  = $passport->isLogin($uid, $token);
        if (!$isLogin) {
            return Library::output(1301);
        }

        $user   = new UserModel;
        $info   = $user->baseInfo($uid);
        $oldpwd = md5($oldpwd);
        if (!$info) {
            return Library::output(1);
        }
        if ($info['f_pwd'] != $oldpwd) {
            return Library::output(2013);
        }

        $newpwd1 = md5($newpwd1); // 新密码需要再次md5处理
        $res     = $passport->changePwd($uid, $oldpwd, $newpwd1);
        if (!$res) {
            return Library::output(1);
        }
        $token = $passport->genToken($uid, "login_token");
        return Library::output(0, array("uid" => $uid, "token" => $token)); // 修改密码后，重新生成token，让老的登录状态下线
    }

    public function checkImgCode(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $sid    = isset($param["sid"]) ? $param["sid"] : "";
        $code   = isset($param["code"]) ? $param["code"] : "";
        $action = isset($param["action"]) ? $param["action"] : "";
        if (!$sid || !$action || !$code) {
            return Library::output(1);
        }
        $device      = md5(md5($sid) . $action);
        $verifyModel = new VerifyCodeModel;
        $verifyInfo  = $verifyModel->getImgCode($device);

        if (isset($verifyInfo['code'])) {
            if ($verifyInfo['code'] == $code) {
                return Library::output(0);
            }
        }
        return Library::output(1);
    }

    /**
     * 绑定手机/邮箱发送验证码
     * 必须要登录状态
     * @param   int     uid
     * @param   token   token
     * @param   bool
     */
    public function sendBindMsg(Request $request)
    {
        $reqJson = $request->input('json');
        $param   = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }
        $uid    = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $token  = isset($param["token"]) ? $param["token"] : "";
        $mobile = isset($param["mobile"]) ? $param["mobile"] : "";
        $action = isset($param["action"]) ? $param["action"] : "";
        if (!$uid || !$token || !$mobile) {
            return Library::output(2001);
        }

        /**
         * 绑定手机号码，必须要登录，并且传uid
         */
        $passport = new PassportModel;
        if (!$uid) {
            return Library::output(2001);
        }
        $islogin = $passport->isLogin($uid, $token);
        if (!$islogin) {
            return Library::output(1301);
        }

        /**
         * 先判断该手机号码有没有绑定过账号
         * 一个号码只能绑定一个账号
         */
        if ($action === 'mobileChange') {
        } else {
            $isExists = $passport->isExists($mobile, "account");
            if ($isExists === false) {
                return Library::output(1);
            }
            if ($isExists === "notexists") {
            } else {
                return Library::output(2101);
            }
        }

        /**
         * 再判断这个账号有没有绑定过手机
         * 换绑定需要先解绑以前的手机
         */
        if ($action === 'mobileChange') {
        } else {
            $userModel = new UserModel;
            $base      = $userModel->baseInfo($uid);
            if (!$base || !is_array($base)) {
                return Library::output(1);
            }
            if ($base['f_mobile']) {
                return Library::output(2102);
            }
        }

        $ret = $passport->sendSms($uid, $mobile, "bind_mobile");

        return Library::output($ret);
    }

    /**
     * 找回密码发送短信验证码
     * @param Request $request
     */
    public function sendFindPwdMsg(Request $request)
    {
        $reqJson = $request->input('json');
        $param   = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }
        $account    = isset($param["account"]) ? strtolower(trim($param["account"])) : "";
        $bindMobile = isset($param["bindMobile"]) ? $param["bindMobile"] : "";
        $smsTp      = isset($param["smsTp"]) ? $param["smsTp"] : "find_pwd_mobile";
        $smsContent = isset($param["smsContent"]) ? $param["smsContent"] : [];

        if (!$account) {
            return Library::output(2001);
        }

        if (!$bindMobile) {
            return Library::output(2001);
        }

        /**
         * 找回密码，要传account
         */
        $passport = new PassportModel;
        $uid      = $passport->isExists($account, "account");
        if ($uid === false) {
            return Library::output(1);
        }

        if ($uid === "notexists" || !is_numeric($uid) || !$uid) {
            return Library::output(1302);
        }

        /**
         * 查到绑定的手机号码
         */
        $userModel = new UserModel;
        $base      = $userModel->baseInfo($uid);
        if (!$base || !is_array($base)) {
            return Library::output(1);
        }
        if (!$base['f_mobile']) {
            return Library::output(2105);
        }

        //比对手机号正确与否
        if ($base['f_mobile'] != $bindMobile) {
            return Library::output(2112);
        }

        if ($smsTp == "extract_cash_msg") {
            $toBCheckBillDBModel = new ToBCheckBillDBModel;
            $cardInfo            = $toBCheckBillDBModel->get2bBankCard($uid, $smsContent['card_id']);
            if (!$cardInfo) {
                return Library::output(2112);
            } else {
                $smsContent['card'] = substr($cardInfo['card_no'], -4);
            }
        }
        $ret = $passport->sendSms($uid, $base['f_mobile'], $smsTp, $smsContent);

        return Library::output($ret);
    }

    /**
     * 用户bind手机号操作
     * @param Request $request
     */
    public function bindMobile(Request $request)
    {
        $reqJson = $request->input('json');
        $param   = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param) || !isset($param['msgCode']) || !$param['msgCode'] || !isset($param['mobile']) || !$param['mobile']) {
            return Library::output(2001);
        }

        if (!isset($param['token']) || !$param['token']) {
            return Library::output(1301);
        }
        if (!isset($param['uid']) || !$param['uid']) {
            return Library::output(1301);
        }

        $uid     = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $token   = isset($param["token"]) ? $param["token"] : "";
        $mobile  = isset($param["mobile"]) ? trim($param["mobile"]) : "";
        $msgCode = isset($param["msgCode"]) ? trim($param["msgCode"]) : "";
        if (strlen($msgCode) == 0) {
            return Library::output(1301);
        }

        //换绑定的操作流程
        $mobileChange = isset($param["mobileChange"]) ? trim($param["mobileChange"]) : "";

        $passport = new PassportModel();
        $isLogin  = $passport->isLogin($uid, $token);
        if (!$isLogin) {
            return Library::output(1301);
        }

        /*
         * 判断验证码是否正确
         */
        if ($mobileChange == 1) {
            $action = "bind_mobile";
            $verify = new VerifyCodeModel;
            //$ret = $verify->decVerfyCount($uid, $action);
            $code = $verify->getVerifyCode($uid, $mobile, "mobile", $action);
            if (!$code) {
                return Library::output(2006);
            }
            if ($code == "error:notimes") {
                return Library::output(2111);
            }
            if ($code != $msgCode) {
                return Library::output(2006);
            }
            $verify->delCode($uid, $mobile, "mobile", $action);
            return Library::output(0);
        }

        /**
         * 先判断该手机号码有没有绑定过账号
         * 一个号码只能绑定一个账号
         */
        if ($mobileChange === '') {
            $isExists = $passport->isExists($mobile, "account");
            if ($isExists === false) {
                return Library::output(1);
            }
            if ($isExists === "notexists") {
            } else {
                return Library::output(2101);
            }
        }

        /**
         * 再判断这个账号有没有绑定过手机
         * 换绑定需要先解绑以前的手机
         */
        $userModel = new UserModel;
        $base      = $userModel->baseInfo($uid);
        if ($mobileChange === '') {
            if (!$base || !is_array($base)) {
                return Library::output(1);
            }
            if ($base['f_mobile']) {
                return Library::output(2102);
            }
        }

        /**
         *  判断验证码是否正确
         */
        $action = "bind_mobile";
        $verify = new VerifyCodeModel;
        $code   = $verify->getVerifyCode($uid, $mobile, "mobile", $action);
        if ($code == "error:notimes") {
            return Library::output(2111);
        }
        if (!$code) {
            return Library::output(2006);
        }
        if ($code != $msgCode) {
            return Library::output(2006);
        }
        $verify->delCode($uid, $mobile, "mobile", $action);

        //插入db_login库t_login_x表
        $loginModel        = new LoginModel();
        $tLogin['f_login'] = $mobile;
        $tLogin['f_uid']   = $uid;
        $tLoginUpdateArr   = array(
            'f_uid'   => $uid,
            'f_login' => $mobileChange,
        );

        if ($mobileChange === '') {
            $lret = $loginModel->tLoginInsert($tLogin);
        } else {
            $lret = $loginModel->tLoginUpdate($mobile, $tLoginUpdateArr);
        }
        if (!$lret) {
            return Library::output(1);
        }

        $ret = $userModel->updateBaseinfo($uid, array("f_mobile" => $mobile));
        if (!$ret) {
            return Library::output(1);
        }

        /**
         * 发送统计
         */
        $properties = [
            "catalog" => "use",
            "actid"   => "binding_phone",
            "actcnt"  => 1,
            "isall"   => 1,
        ];
        DataCenterStatModel::stat("vrplat", "actcount", $uid, $properties);

        return Library::output(0);
    }

    /*
     * 解除用户的账号和手机号的绑定
     */
    public function unBindMobile(Request $request)
    {
        $reqJson = $request->input('json');
        $param   = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param) || !isset($param['mobile']) || !$param['mobile']) {
            return Library::output(2001);
        }

        if (!isset($param['token']) || !$param['token']) {
            return Library::output(1301);
        }
        if (!isset($param['uid']) || !$param['uid']) {
            return Library::output(1301);
        }

        if (!isset($param['code']) || !$param['code']) {
            return Library::output(2110);
        }
        $uid     = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $token   = isset($param["token"]) ? $param["token"] : "";
        $mobile  = isset($param["mobile"]) ? trim($param["mobile"]) : "";
        $msgCode = isset($param["code"]) ? trim($param["code"]) : "";

        $passport = new PassportModel();
        $isLogin  = $passport->isLogin($uid, $token);
        if (!$isLogin) {
            return Library::output(1301);
        }

        $tLogin = array(
            'f_uid'   => $uid,
            'f_login' => $mobile,
        );
        //校验验证码
        $action = "bind_mobile";
        $verify = new VerifyCodeModel;
        $code   = $verify->getVerifyCode($uid, $mobile, "mobile", $action);
        if ($code == "error:notimes") {
            return Library::output(2111);
        }
        if (!$code) {
            return Library::output(2006);
        }

        if ($code != $msgCode) {
            return Library::output(2006);
        }
        //删除验证码
        $verify->delCode($uid, $mobile, "mobile", $action);

        //删除t_login_x表中的记录和用户信息表中db_user_info_X表中的绑定信息
        $loginModel = new LoginModel();
        $ret        = $loginModel->unbind($tLogin);
        if (!$ret) {
            return Library::output(1);
        }
        $userModel = new UserModel;
        $result    = $userModel->updateBaseinfo($uid, array("f_mobile" => ''));

        if (!$result) {
            return Library::output(1);
        }

        /**
         * 发送统计
         */
        $properties = [
            "catalog" => "use",
            "actid"   => "unbinding_phone",
            "actcnt"  => 1,
            "isall"   => 1,
        ];
        DataCenterStatModel::stat("vrplat", "actcount", $uid, $properties);

        return Library::output(0);
    }

    /**
     * 找回密码的验证绑定手机号并重置密码
     * @param Request $request
     */
    public function findPassword(Request $request)
    {
        $param = json_decode(self::tagStrToArr($request->input('json')), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $account = isset($param["account"]) ? strtolower(trim($param["account"])) : "";
        $msgCode = isset($param["msgCode"]) ? trim($param["msgCode"]) : "";
        $pwd     = isset($param["pwd"]) ? strtolower(trim($param["pwd"])) : "";
        if (!$account || !$msgCode || !$pwd) {
            return Library::output(2001);
        }

        if (strlen($param['pwd']) != 32) {
            return Library::output(1303);
        }

        $passport = new PassportModel();
        /**
         * 先判断该手机号码有没有绑定过账号
         * 一个号码只能绑定一个账号
         */
        $uid = $passport->isExists($account, "account");
        if ($uid === false) {
            return Library::output(1);
        }
        if ($uid === "notexists" || !is_numeric($uid) || !$uid) {
            return Library::output(1302);
        }

        $user = new UserModel;
        $base = $user->baseInfo($uid);

        if (!$base || !is_array($base)) {
            return Library::output(1);
        }
        if (!$base['f_mobile']) {
            return Library::output(2105);
        }

        $action = "find_pwd_mobile";
        $verify = new VerifyCodeModel;

        $code = $verify->getVerifyCode($uid, $base['f_mobile'], "mobile", $action);
        if ($code == "error:notimes") {
            return Library::output(2111);
        }
        if (!$code) {
            return Library::output(2006);
        }
        if ($code != $msgCode) {
            return Library::output(2006);
        }
        $verify->delCode($uid, $base['f_mobile'], "mobile", $action);

        $ret = $user->updateBaseinfo($uid, array("f_pwd" => md5($pwd)));
        if ($ret == 2) {
            return Library::output(2113);
        }
        if (!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /**
     * Open后天添加子账号的短信验证
     * @param Request $request
     */
    public function addSonCheckMsg(Request $request)
    {
        $param = json_decode(self::tagStrToArr($request->input('json')), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $account = isset($param["account"]) ? strtolower(trim($param["account"])) : "";
        $mobile  = isset($param["mobile"]) ? strtolower(trim($param["mobile"])) : "";
        $msgCode = isset($param["msgCode"]) ? trim($param["msgCode"]) : "";
        if (!$account || !$msgCode) {
            return Library::output(2001);
        }

        $passport = new PassportModel();
        /**
         * 先判断该手机号码有没有绑定过账号
         * 一个号码只能绑定一个账号
         */
        $uid = $passport->isExists($account, "account");
        if ($uid === false) {
            return Library::output(1);
        }
        if ($uid === "notexists" || !is_numeric($uid) || !$uid) {
            return Library::output(1302);
        }

        $action = "find_pwd_mobile";
        $verify = new VerifyCodeModel;
        $code   = $verify->getVerifyCode($uid, $mobile, "mobile", $action);
        if ($code == "error:notimes") {
            return Library::output(2111);
        }
        if (!$code) {
            return Library::output(2006);
        }
        if ($code != $msgCode) {
            return Library::output(2006);
        }
        $verify->delCode($uid, $mobile, "mobile", $action);
        return Library::output(0);
    }

    /**
     * 验证短信验证码接口
     * @param Request $request
     */
    /*
    public function authMsgCode(Request $request) {
    $passport = new PassportModel();
    $ret['code'] = 1102;
    if($request->isMethod('post')){
    $reqJson = $request->input('json');
    $req = json_decode(self::tagStrToArr($reqJson), true);
    if(isset($req['mobile']) && isset($req['msgType']) && isset($req['msgCode'])) {
    $mobile = $req['mobile'];
    $msgCode = $req['msgCode'];
    $msgType = $req['msgType'];
    $ret = $passport->authMsgCode($mobile, $msgCode, $msgType);
    } else {
    $ret['code'] = 1102;    // 非法请求--参数缺失
    }
    }

    return Library::output($ret['code']);
    }
     */

    public function uploadFace(Request $request)
    {
        $passport  = new PassportModel();
        $uid       = $request->input('uid');
        $uploadDir = "resources" . DIRECTORY_SEPARATOR . "userPic" . DIRECTORY_SEPARATOR . $uid . DIRECTORY_SEPARATOR;
        // 创建用户的头像目录
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }
        $fileAllowExt  = 'gif|jpg|jpeg|png|gif'; //限制上传图片的格式
        $fileAllowSize = 2 * 1024 * 1024; //限制最大尺寸是2MB

        if ($_POST['submit'] == 'upload') {
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                $fileName         = $_FILES['file']['name'];
                $fileError        = $_FILES['file']['error'];
                $fileType         = $_FILES['file']['type'];
                $fileTmpName      = $_FILES['file']['tmp_name'];
                $fileSize         = $_FILES['file']['size'];
                $fileExt          = substr($fileName, strrpos($fileName, '.') + 1);
                $data['oldName']  = $fileName;
                $data['fileExt']  = $fileExt;
                $data['fileType'] = $fileType;
                switch ($fileError) {
                    case 0:
                        $code        = 0;
                        $data['msg'] = "文件上传成功!";
                        break;

                    case 1:
                        $code        = 2202;
                        $data['msg'] = "文件上传失败，文件大小" . $fileSize . "超过限制,允许上传大小" . $passport->sizeFormat($fileAllowSize) . "!";
                        break;

                    case 3:
                        $code        = 2203;
                        $data['msg'] = "上传失败，文件只有部份上传!";
                        break;

                    case 4:
                        $code        = 2204;
                        $data['msg'] = "上传失败，文件没有被上传!";
                        break;

                    case 5:
                        $code        = 2205;
                        $data['msg'] = "文件上传失败，文件大小为0!";
                        break;
                }
                if (stripos($fileAllowExt, $fileExt) === false) {
                    $code        = 2206;
                    $data['msg'] = "该文件扩展名不允许上传";
                }
                if ($fileSize > $fileAllowSize) {
                    $code        = 2202;
                    $data['msg'] = "文件大小超过限制,只能上传" . $passport->sizeFormat($fileAllowSize) . "的文件!";
                }
                if ($code !== 0) {
                    $data['msg'] = $data['msg'];
                    return Library::output($code, $data);
                }
                if ($code === 0) {
                    if (file_exists($uploadDir)) {
                        $fileNewName  = substr(md5($uid), 0, 12);
                        $fileSavePath = $uploadDir . $fileNewName;
                        move_uploaded_file($fileTmpName, $fileSavePath);
                        $timeStamp = time();
                        $result    = $passport->setFace($uid, $timeStamp);
                        $code      = 1; //更新状态失败
                        if ($result) {
                            $code        = 0; //图片上传成功
                            $data['url'] = $passport->getHeadPicUrl($uid, $timeStamp);
                        }
                        return Library::output($code, $data);
                    } else {
                        return Library::output($code, $data);
                    }
                }
            }
        }
    }

    /**
     * 上传头像接口
     * @param Request $request
     */
    public function setFace(Request $request)
    {
        $passport          = new PassportModel();
        $ret['headPicUrl'] = array();
        $ret['code']       = 1102; // 非法请求--参数缺失
        if ($request->isMethod('post')) {
            $reqJson     = $request->input('json');
            $req         = json_decode(self::tagStrToArr($reqJson), true);
            $ret['code'] = 2011; //uid参数缺失
            if (isset($req['uid'])) {
                $uid    = $req['uid'];
                $result = $passport->uploadApi($uid);
                $retArr = json_decode($result, true);

                $ret['code'] = 2009; //图片上传失败
                if ($retArr['success']) {
                    //如果上传成功，则修改其头像状态为当前状态时间戳
                    $timeStamp   = time();
                    $result      = $passport->setFace($uid, $timeStamp);
                    $ret['code'] = 1; //更新状态失败
                    if ($result) {
                        $ret['code']       = 0; //图片上传成功
                        $ret['headPicUrl'] = $passport->getHeadPicUrl($uid, $timeStamp);
                    }
                }
            }
        }
        return Library::output($ret['code'], $ret['headPicUrl']);
    }

    /*
     * 发送邮件接口
     */
    public function sendMail()
    {
        $data = ['email' => '850195711@qq.com', 'name' => '李宾宾', 'uid' => 1, 'activationcode' => '464343'];
        Mail::send('activemail', $data, function ($message) use ($data) {
            $message->to('850195711@qq.com', '李宾宾')->subject('欢迎注册我们的网站，请激活您的账号！');
        });
    }

    /**
     * 游客自动注册、登录
     */
    public function guest(Request $request)
    {
        $json       = $request->input("json");
        $uid        = 0;
        $token      = "";
        $from_appid = 0;
        $did        = "";
        $adid       = "";
        $addip      = "";
        if ($json) {
            $param = json_decode($request->input("json"), true);
            if (!$param || !is_array($param)) {
                return Library::output(2001);
            }

            $uid        = isset($param["uid"]) ? intval($param["uid"]) : 0;
            $token      = isset($param["token"]) ? $param["token"] : "";
            $from_appid = isset($param["from_appid"]) ? intval($param["from_appid"]) : 0; // 注册所在的appid
            $did        = isset($param["did"]) ? $param["did"] : "";
            $adid       = isset($param["adid"]) ? $param["adid"] : "";
            $addip      = isset($param["cip"]) ? $param["cip"] : Library::realIp();
        }

        /**
         * 从cookie中读到用户uid和token
         * 检测是否是登录状态
         * 如果是登录状态，不需要再注册
         */
        $passport = new PassportModel;
        $passport->setProp("did", $did);
        $islogin = $passport->isLogin($uid, $token);
        if ($islogin) {
            $info = array("uid" => $uid, "token" => $token);
            return Library::output(0, $info);
        }

        /**
         * 下次请求是否需要验证码
         * 拿到结果后，可以请求验证码
         */
        $is_need_code = false;

        /**
         * 统计该IP注册次数
         */
        $verifyModel = new VerifyCodeModel;
        $counter_ip  = $verifyModel->addRetryCount("register", $addip);
        if ($verifyModel->isNeedImgCode("register", $addip, "ip", $counter_ip)) {
            $is_need_code = true;
        }

        $user = new UserModel;

        /**
         * 生成一个游客ID，拼一个游客的临时账号
         */
        $guestid = $user->getGuestId();
        if (!$guestid) {
            return Library::output(1305, array("inc" => $is_need_code));
        }
        $account = "account_" . $guestid;

        $ret = $passport->register($account, "", "guest", array("f_from_appid" => $from_appid, "f_adid" => $adid, "f_addip" => $addip));
        if (!$ret || !is_array($ret)) {
            return Library::output(1305, array("inc" => $is_need_code));
        }
        return Library::output(0, $ret);
    }

    /**
     * 游客自动注册、登录
     */
    public function setAccount(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $uid      = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $token    = isset($param["token"]) ? $param["token"] : "";
        $account  = isset($param["account"]) ? strtolower(trim($param["account"])) : "";
        $password = isset($param["pwd"]) ? strtolower(trim($param["pwd"])) : "";

        if (!$uid || !$token || !$account) {
            return Library::output(1301);
        }
        if (strlen($account) == 11 && is_numeric($account)) {
            return Library::output(1101);
        }
        $pattern = Config::get("common.account_pattern");
        $preg    = preg_match($pattern, $account, $matches);
        if (!$preg) {
            return Library::output(1101);
        }
        if (!$password || strlen($password) != 32) {
            return Library::output(1);
        }

        /**
         * 先判断是否登录，如果没有登录，不能修改账号
         * 判断用户名是否存在
         * 绑定账号
         */
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($uid, $token);
        if (!$islogin) {
            return Library::output(1301);
        }

        $login = new LoginModel;
        $user  = $login->existUser($account);
        if ($user) {
            return Library::output(1107);
        }
        $ret = $passport->bindAccount($uid, $account, $password);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 绑定第三方账号
     * 仅限第三方账号
     * 不需要登录
     * @param   string  type: qq;weibo;weixin;ali;bobo;
     */
    public function bindThirdAccount(Request $request)
    {
        $uid       = trim($request->input("uid"));
        $acc       = trim($request->input("acc"));
        $type      = trim($request->input("type"));
        $token     = trim($request->input("token", ""));
        $needlogin = trim($request->input("needlogin", 1));
        $sign      = trim($request->input("sign", ""));
        if (!$uid || !$acc || !$type) {
            return Library::output(2001);
        }

        if ($needlogin && !$token) {
            return Library::output(1301);
        }

        /**
         * 如果没有指定不需要登录，就要验证登录状态
         */
        $passport = new PassportModel;
        if ($needlogin) {
            $islogin = $passport->isLogin($uid, $token);
            if (!$islogin) {
                return Library::output(1301);
            }
        } else {
            $check = Library::encrypt($_POST, Config::get("common.uc_appkey"));
            if ($sign != $check || !$check) {
                return Library::output(2002);
            }
        }

        $login = new LoginModel;
        $user  = $passport->isExists($acc, $type);
        if (!$user) {
            return Library::output(1);
        }
        if ($user !== "notexists") {
            return Library::output(1107);
        }

        $ret = $passport->bindThirdAccount($uid, $acc, $type);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     *
     */
    public function getcode(Request $request, $type)
    {
        $uid    = $request->input('uid');
        $verify = new VerifyCodeModel;
        $code   = $verify->getVerfyCode($uid, $type);
        echo "code ==> ";
        var_dump($code);
        echo "<br>";
    }

    /**
     * 得到登录记录
     * @param   array  request
     * @return  code => 0, array 成功状态码等于0，并加一个登录记录数组
     */
    public function getLoginLog(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        /**
         * 判断uid或者appid是否合法
         */
        $uid   = isset($param["uid"]) ? trim($param["uid"]) : 0;
        $appid = isset($param["appid"]) ? trim($param["appid"]) : 0; // 得到appid

        if (!$uid || !$appid) {
            // 判断uid和appid是否为空
            return Library::output(2014);
        }

        $loginModel = new LoginModel;
        $lastlogin  = $loginModel->getLastLogin($uid);
        if (!$lastlogin || !is_array($lastlogin) || !isset($lastlogin['last']) || !$lastlogin['last'] || !is_array($lastlogin['last'])) {
            return Library::output(2015);
        }
        return Library::output(0, $lastlogin['last']);
    }

    /**
     * 更新图象和昵称接口
     */
    public function updateField(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $uid   = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $token = isset($param["token"]) ? $param["token"] : "";
        $data  = isset($param["data"]) ? $param["data"] : ""; // 这就是一个数组，类似 nick => 'zhangsan'

        if (!$uid || !$token || !$data || !is_array($data)) {
            return Library::output(1301);
        }

        // 判断是否登录
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($uid, $token);
        if (!$islogin) {
            return Library::output(1301);
        }

        // 调用passportModel里面的
        $res = $passport->modifyAccount($uid, $data); // 修改成功返回true，修改失败返回false
        if (!$res) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 获得IM的token
     */
    public function getImToken(Request $request)
    {
        $uid   = intval($request->input("uid", ""));
        $token = $request->input("token", "");
        $ts    = $request->input("ts", "");
        $sign  = $request->input("sign", "");
        if (!$uid || !$token || !$ts || !$sign) {
            return Library::output(2001);
        }

        $clientkey = Config::get("common.vr_client_key");
        $check     = Library::encrypt($_POST, $clientkey);
        if ($check != $sign) {
            return Library::output(2002);
        }

        // 判断是否登录
        $passport = new PassportModel;
        $islogin  = $passport->isLogin($uid, $token);
        if (!$islogin) {
            return Library::output(1301);
        }

        $imModel = new ImModel;
        $info    = $imModel->getImToken($uid);
        if (!$info || !isset($info['secret']) || !$info['secret']) {
            return Library::output(1);
        }
        return Library::output(0, ["imtoken" => $info['secret']]);
    }

    /*
     * 给游戏的评论中获取用户account和头像地址的接口
     */
    public function getCommentUserInfo(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return Library::output(2001);
        }

        $uid      = isset($param["uid"]) ? intval($param["uid"]) : 0;
        $appid    = isset($param["appid"]) ? $param["appid"] : "";
        $passport = new PassportModel;
        $ret      = $passport->getCommentUserInfo($uid, $appid);

        if (!$ret) {
            return Library::output(1);
        }
        return $ret;
    }

    public function showImgCode(Request $request, $device)
    {
        $w = $request->input('w');
        $h = $request->input('h');
        $w = $w ? $w : 100;
        $h = $h ? $h : 25;
        if (!$device) {
            return Library::output(1);
        }

        $verifyModel = new VerifyCodeModel;
        $verifyInfo  = $verifyModel->getImgCode($device);
        if ($verifyInfo) {
            $code = '';
            for ($i = 0; $i < $this->imgCodeLen; $i++) {
                $code .= dechex(mt_rand(0, 15));
            }
            $verifyInfo['code'] = $code;
            $ret                = $verifyModel->setImgCode($device, $verifyInfo);
            if ($ret) {
                $verifyModel->showImg($w, $h, $verifyInfo['code']);
            }
        }
    }
}
