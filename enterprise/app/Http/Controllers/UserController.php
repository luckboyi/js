<?php

namespace App\Http\Controllers;

use Auth;
use Config;
use Helper\AccountCenter as Account;
// 使用Model对象
use Helper\Library;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Input;
use Redirect;
use Session;
use \App\Models\CookieModel;
use \App\Models\DevModel;
use \App\Models\ImModel;
use \App\Models\ToBDBModel;
use \App\Models\UserModel as User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware("vrauth:0:weblogin:https", ['only' => ["webLogin", "webRegister"]]);
        $this->middleware("vrauth:0:register", ['only' => ["webRegister"]]);
        $this->middleware("vrauth:0:clientlogin", ['only' => ["clientLogin"]]);
        $this->middleware("vrauth:0:json", ['only' => ["profileEdit", "passwordEdit", "mobileSms", "mobileBind"]]);

    }

    /**
     * [boboLogin 3d播播登录]
     * @param  int $appid [appid]
     * @param  string $username [用户名]
     * @param  string $password [密码]
     * @param  string $sign [签名]
     * @return [json] [openid,nick,face,token]
     */
    public function boboLogin(Request $request)
    {
        $appid    = $request->input('appid');
        $username = $request->input('username');
        $password = $request->input('password');
        $sign     = $request->input('sign');
        if (!$appid || !$username || !$password || !$sign) {
            return Library::output(2001);
        }
        $third_party = Config::get("common.third_party");
        if (!isset($third_party[$appid])) {
            return Library::output(1);
        }
        $appkey = $third_party[$appid]['appkey'];
        $mysign = Library::encrypt(['appid' => $appid, 'username' => $username, 'password' => $password], $appkey);
        if ($sign != $mysign) {
            return Library::output(2002);
        }
        $accountModel = new Account($appid, $appkey);
        $res          = $accountModel->login($username, md5($password), '');
        if (!isset($res['code'])) {
            return Library::output(1);
        }
        if ($res['code'] == 0) {
            $uid     = $res['data']['uid'];
            $token   = $res['data']['token'];
            $nick    = $res['data']['nick'];
            $face    = $res['data']['face'];
            $openRes = $accountModel->getOpenId($uid, $token);
            if (!isset($openRes['code']) || $res['code'] != 0) {
                return Library::output(1);
            }
            $openid = $openRes['data']['openid'];
            $out    = ['openid' => $openid, 'nick' => $nick, 'face' => $face, 'token' => $token];
            return Library::output(0, $out);
        } else {
            return Library::output($res['code'], [], $res['msg']);
        }
    }

    public function bbsPing()
    {
        echo "1";
    }

    public function bbsUser(Request $request)
    {
        $get  = array();
        $code = $request->input('code');
        parse_str(Library::authcode($code, 'DECODE', 'aek19gtklqp'), $get);
        $time = $get['time'];
        if (abs(time() - $get['time']) > 100000) {
            return Library::output(1);
        }
        if ($get['action'] == "synlogin") {
            $uid      = $get['uid'];
            $account  = $get['username'];
            $password = $get['password'];
            return $this->loginCookie($account, $password, 0, ['code' => 'bbsvj&81']);
        } elseif ($get['action'] == "synlogout") {
            $params = array('uid', 'token', 'account', 'nick', 'face');
            CookieModel::clearCookieArr($params);
            Session::flush();
            return Library::output(0);
        }
    }

    public function clientLogin()
    {

        return view('login');
    }

    //www登入
    public function webLogin(Request $request)
    {
        // Library::SSLRedirect($request);

        $referer = $request->input('referer');

        $redirect_uri = $request->input('redirect_uri', "");
        $appid        = intval($request->input('appid', "0"));
        $ispartner    = $_SERVER['HTTP_HOST'] == "partner.vronline.com" ? 1 : 0;
        return view('website.login', ['referer' => $referer, 'nologin' => true, 'redirect_uri' => $redirect_uri, 'appid' => $appid, "ispartner" => $ispartner]);
    }

    // www忘记密码
    public function forgetPwd()
    {
        return view('website.forgetPwd', ['nologin' => 1]);
    }

    // 找回密码发送验证码
    public function sendFindPwdMsg(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        // 得到name 和 password
        $account = Input::get('account');
        $mobile  = Input::get('mobile');

        $accountModel = new Account();
        $result       = $accountModel->sendSmsMsg($account, $mobile);
        if (isset($result['code'])) {
            if ($result['code'] == 0) {
                return Library::output(0);
            } else {
                return Library::output($result['code'], [], $result['msg']);
            }
        } else {
            return Library::output(1);
        }
    }

    // 重置密码
    public function findPassword(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        // 得到账号、密码、验证码
        $account = Input::get('account');
        $pwd     = Input::get('pwd');
        $code    = Input::get('code');

        $accountModel = new Account();
        $result       = $accountModel->findPassword($account, md5($pwd), $code);
        if (isset($result['code'])) {
            if ($result['code'] == 0) {
                return Library::output(0);
            } else {
                return Library::output($result['code'], [], $result['msg']);
            }
        } else {
            return Library::output(1);
        }
    }

    public function imgCodeStat(Request $request)
    {
        $action  = $request->input('action');
        $account = $request->input('account');

        if ($action == "login") {
            if (!$account || strlen($account) < 6) {
                return Library::output(1);
            }
        }

        if (!in_array($action, ["login", "register"])) {
            return Library::output(1);
        }

        $sessionId    = Session::getId();
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $res          = $accountModel->getImgCode($sessionId, $action, $account);

        if (isset($res['code']) && $res['code'] == 0) {
            return Library::output(0, $res['data']);
        } else {
            return Library::output(1107);
        }
    }

    //www open 注册
    public function webRegister(Request $request)
    {
        $referer = $request->input("referer");
        return view('website.register', ['referer' => $referer, 'nologin' => 1]);
    }

    public function apiAccountCheck(Request $request)
    {
        $account = $request->input('account');

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $res          = $accountModel->isExists($account);
        if (isset($res['code']) && $res['code'] == 0) {
            return Library::output(0);
        } else {
            return Library::output(1107);
        }
    }
    /**
     * 登陆
     * @param   string  name  账号
     * @param   string  pwd   密码
     */
    public function apiLogin(Request $request)
    {
        $name       = $request->input('name');
        $pwd        = $request->input('pwd');
        $code       = $request->input('code');
        $thirdpart  = $request->input('thirdpart');
        $thirdappid = $request->input('thirdappid');
        $client     = $request->input('client');
        $isRemeber  = intval($request->input('remember'));
        $ext        = ['code' => $code, 'thirdpart' => $thirdpart, 'thirdappid' => $thirdappid];
        if ($client) {
            $ext['client'] = $client;
        }
        return $this->loginCookie($name, md5($pwd), $isRemeber, $ext);
    }

    //退出
    public function loginOut(Request $request)
    {
        $params = array('uid', 'token', 'account', 'nick', 'face', 'type');
        CookieModel::clearCookieArr($params);
        Session::flush();
        $referer = $request->input('referer');
        if ($referer) {
            $redirectUrl = url($referer);
            return Redirect::to($redirectUrl);
        }
        return Redirect::to('/');
    }

    /**
     * 主机登录
     */
    public function masterLogin(Request $request)
    {
        Library::accessHeader();
        $name = $request->input('name');
        $pwd  = $request->input('pwd');
        $code = $request->input('code');
        return $this->loginCookie($name, md5($pwd), 0, ['code' => $code, 'master' => true]);
    }

    /**
     * 开发者登录
     */
    public function devUserLogin(Request $request)
    {
        $name = $request->input('name');
        $pwd  = $request->input('pwd');
        $code = $request->input('code');
        return $this->loginCookie($name, md5($pwd), 0, ['code' => $code, 'dev_user' => true]);
    }

    /**
     * 注册
     * @param   string  name  账号
     * @param   string  pwd   密码
     * @param   string  confirPwd   密码
     */
    public function apiRegister(Request $request)
    {
        $name       = $request->input('account');
        $pwd        = $request->input('pwd');
        $confirmPwd = $request->input('confirmPwd');
        $code       = $request->input('code');

        // 判断用户名或密码不能为空
        if (!$name || !$pwd || !$confirmPwd) {
            return Library::output(12);
        }

        if ($pwd !== $confirmPwd) {
            return Library::output(13);
        }

        $sid          = Session::getId();
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);

        $res = $accountModel->register($name, md5($pwd), "account", $code, ['sid' => $sid]);
        if (!$res || !isset($res['code'])) {
            return Library::output(1);
        }
        if ($res['code'] != 0) {
            if (!isset($res['data'])) {
                $res['data'] = [];
            }
            return Library::output($res['code'], $res['data']);
        } else {
            $this->setLoginCookie($res['data']);
            return Library::output(0);
        }
    }

    /**
     * flash推广页面注册
     *
     * @param   string  name  账号
     * @param   string  pwd   密码
     * @param   string  confirPwd   密码
     */
    public function apiRegister4Flash(Request $request)
    {
        $name       = $request->input('account');
        $pwd        = $request->input('pwd');
        $confirmPwd = $request->input('confirmPwd');
        $code       = $request->input('code');
        $appid      = (int) $request->input('appid');
        $serverid   = (int) $request->input('serverid', 0);
        $adid       = (int) $request->input('adid');

        // 判断用户名或密码不能为空
        if (!$name || !$pwd || !$confirmPwd || !$appid || !$adid) {
            return Library::output(1110);
        }

        if ($pwd !== $confirmPwd) {
            return Library::output(1106);
        }
        $sid = Session::getId();

        $account_appid  = Config::get("common.uc_appid");
        $account_appkey = Config::get("common.uc_appkey");
        $accountModel   = new Account($account_appid, $account_appkey);

        $ext = [
            "adid"       => $adid,
            "from_appid" => $appid,
            'sid'        => $sid,
            'code'       => $code,
        ];

        $res = $accountModel->register($name, md5($pwd), "account", "", $ext);

        if (!$res || !isset($res['code'])) {
            return Library::output(1);
        }
        if ($res['code'] != 0) {
            if (!isset($res['data'])) {
                $res['data'] = [];
            }
            return Library::output($res['code'], $res['data']);
        }

        $params['uid']     = $res['data']['uid'];
        $params['token']   = $res['data']['token'];
        $params['account'] = $res['data']['account'];
        $params['nick']    = $res['data']['nick'];
        $params['face']    = $res['data']['face'];

        CookieModel::setCookieArr($params, 0);
        return Library::output(0, array("appid" => $appid, "serverid" => $serverid));
    }

    /**
     * flash推广页面登入
     *
     * @param   string  name  账号
     * @param   string  pwd   密码
     */
    public function apiLogin4Flash(Request $request)
    {
        $name = $request->input('name');
        $pwd  = $request->input('pwd');
        $code = $request->input('code');

        $appid    = (int) $request->input('appid');
        $serverid = (int) $request->input('serverid', 0);

        // 判断用户名或密码不能为空
        if (!$appid) {
            return Library::output(1110);
        }

        $ext = [
            "appid"    => $appid,
            "serverid" => $serverid,
            'code'     => $code,
        ];
        return $this->loginCookie($name, md5($pwd), 0, $ext);
    }

    /**
     * 登入设置cookie
     *
     * @param  [type]  $name       [description]
     * @param  [type]  $pwd        [description]
     * @param  integer $isRemember [description]
     * @param  array   $ext        [description]
     * @return [type]              [description]
     */
    private function loginCookie($name, $pwd, $isRemember = 0, $ext = array())
    {
        if (!$name || !$pwd) {
            return Library::output(1);
        }

        $sid          = Session::getId();
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $code         = isset($ext['code']) ? $ext['code'] : '';

        $ext['sid'] = $sid;
        $res        = $accountModel->login($name, $pwd, $code, $ext);

        // return Library::output(1, $res);
        if (!$res || !isset($res['code'])) {
            return Library::output(1, $res);
        }

        if ($res['code'] != 0) {
            if (!isset($res['data'])) {
                $res['data'] = [];
            }
            return Library::output($res['code'], $res['data']);
        }

        if (isset($ext['master']) && $ext['master'] == true) {
            //主机登录
            $uid        = $res['data']['uid'];
            $toBDBModel = new ToBDBModel;
            $tobAccount = $toBDBModel->get2bMerchant($uid);
            if (!$tobAccount) {
                return Library::output(1117);
            } else {
                if ($tobAccount['status'] != 9) {
                    return Library::output(3008);
                }
                $terminalId    = $tobAccount['terminal_id'];
                $terminalStats = [];
                if ($terminalId) {
                    $im            = new ImModel;
                    $terminalStats = $im->terminalStat([['terminal_id' => $terminalId]]);
                }
                if (isset($terminalStats[$terminalId]) && $terminalStats[$terminalId] != 6) {
                    //return Library::output(3007);
                }
                $this->setLoginCookie($res['data'], 0);
                $res['data']['merchant'] = $tobAccount['merchant'];
                return Library::output(0, $res['data']);
            }
        } else if (isset($ext['dev_user']) && $ext['dev_user'] == true) {
            //开发者工具登录
            $uid      = $res['data']['uid'];
            $devModel = new DevModel;
            $devUser  = $devModel->getUser($uid);
            if ($devUser && isset($devUser['stat'])) {
                $stat     = $devUser['stat'];
                $reviewed = $devUser['reviewed'];
                if ($stat == 5 && $reviewed == 1) {
                    $this->setLoginCookie($res['data'], 0);

                    $vrGameList = $devModel->getAppsByUid($uid, 'vr', 100, 1, false);
                    $games      = [];
                    foreach ($vrGameList as $vrGame) {
                        $games[] = ['appid' => $vrGame['appid'], 'name' => $vrGame['name']];
                    }
                    return Library::output(0, $games);
                }
            }
            return Library::output(2501);
        } else if (isset($ext['client'])) {
            $this->setLoginCookie($res['data'], $isRemember);
            $res['data']['systime'] = time();
            return Library::output(0, $res['data']);
        } else {
            if (isset($res['data']['logincode']) && $res['data']['logincode']) {
                $ext['logincode'] = $res['data']['logincode'];
            }
            $this->setLoginCookie($res['data'], $isRemember);

            return Library::output(0, $ext);
        }
    }

    private function setLoginCookie($res, $isRemember = 0)
    {
        $params            = array();
        $params['uid']     = $res['uid'];
        $params['token']   = $res['token'];
        $params['account'] = $res['account'];
        $params['nick']    = $res['nick'];
        $params['face']    = $res['face'];
        if ($isRemember == 1) {
            $expire = 1440 * 7;
        } else {
            $expire = 0;
        }

        CookieModel::setCookieArr($params, $expire);
        return true;
    }

    public function needPerm()
    {
        return view('open.error', ['msg' => "您没有权限查看该页面，请切换账号登录重试"]);
    }

    // 登录页面
    public function getLogin()
    {
        return view('user.login');
    }

    //修改个人资料
    public function profileEdit(Request $request)
    {
        $userInfo       = $request->userinfo;
        $data           = [];
        $data['f_nick'] = $request->input('nick');

        if ($data['f_nick'] == $userInfo['nick']) {
            return Library::output(0);
        }
        $accountModel = new Account();
        $result       = $accountModel->updateField($userInfo['uid'], $userInfo['token'], $data);
        if (isset($result['code'])) {
            if ($result['code'] == 0) {
                CookieModel::setCookie('nick', $data['f_nick']);
                return Library::output(0);
            } else {
                return Library::output($result['code'], [], $result['msg']);
            }
        } else {
            return Library::output(1);
        }
    }

    public function passwordEdit(Request $request)
    {
        $userInfo = $request->userinfo;
        $oldPwd   = $request->input('oldPwd'); // 老密码
        $newPwd   = $request->input('newPwd'); // 新密码

        if (!$oldPwd || !$newPwd) {
            return Library::output(1);
        }

        if ($oldPwd == $newPwd) {
            return Library::output(1);
        }
        $accountModel = new Account();
        $result       = $accountModel->changePwd($userInfo['uid'], $userInfo['token'], md5($oldPwd), md5($newPwd));
        if (isset($result['code'])) {
            if ($result['code'] == 0) {
                return Library::output(0);
            } else {
                return Library::output($result['code'], [], $result['msg']);
            }
        } else {
            return Library::output(1);
        }
    }

    public function mobileSms(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $token    = $userInfo['token'];
        $data     = [];
        $mobile   = $request->input('mobile');
        $action   = $request->input('action');
        if (!$mobile || !is_numeric($mobile) || strlen($mobile) != 11) {
            return Library::output(1);
        }
        $accountModel = new Account();
        $res          = $accountModel->info($uid, $token);
        if (!$res || !isset($res['data']) || !isset($res['data']['bindmobile'])) {
            return Library::output(1);
        }
        $bindMobile = $res['data']['bindmobile'] ? $res['data']['bindmobile'] : "";

        if ($action == 'mobileChange') {
            if (!$bindMobile || $bindMobile != $mobile) {
                return Library::output(1);
            }
        } else if ($bindMobile) {
            return Library::output(1);
        }

        if ($action == 'mobileChange') {

            $result = $accountModel->sendBindMsg($uid, $token, $mobile, $action);
        } else {
            $result = $accountModel->sendBindMsg($uid, $token, $mobile);
        }
        if (isset($result['code'])) {
            if ($result['code'] == 0) {
                return Library::output(0);
            } else {
                return Library::output($result['code'], [], $result['msg']);
            }
        } else {
            return Library::output(1);
        }
    }

    public function mobileBind(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $token    = $userInfo['token'];

        $data   = [];
        $mobile = $request->input('mobile');
        $code   = $request->input('code');
        $action = $request->input('action');
        if (!$mobile || !is_numeric($mobile) || strlen($mobile) != 11) {
            return Library::output(1);
        }
        if (!$code || strlen($code) < 4) {
            return Library::output(1);
        }
        $accountModel = new Account();
        $res          = $accountModel->info($uid, $token);
        if (!$res || !isset($res['data']) || !isset($res['data']['bindmobile'])) {
            return Library::output(1);
        }
        $bindMobile = $res['data']['bindmobile'] ? $res['data']['bindmobile'] : "";

        if ($action == 'mobileChange') {
            if (!$bindMobile || $bindMobile != $mobile) {
                return Library::output(1);
            }
        } else if ($bindMobile) {
            return Library::output(1);
        }

        if ($action == 'mobileChange') {
            $result = $accountModel->unBindMobile($uid, $token, $mobile, $code);
        } else {
            $result = $accountModel->bindMobile($uid, $token, $mobile, $code);
        }
        if (isset($result['code'])) {
            if ($result['code'] == 0) {
                return Library::output(0);
            } else {
                return Library::output($result['code'], [], $result['msg']);
            }
        } else {
            return Library::output(1);
        }
    }
}
