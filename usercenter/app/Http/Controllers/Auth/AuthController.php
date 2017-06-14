<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Config;
use Cookie;
use Helper\HttpRequest;
use Helper\Library;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Validator;
use \App\Models\PassportModel;

class AuthController extends Controller
{

    // 微博配置信息
    private $wbAppId     = '304960199';
    private $wbAppSecret = '9d56a54203fe5db1c2eacc6dea2f8050';
    private $wbRedirect  = 'http://passport.vronline.com/auth/wbCallback';

    // qq
    private $qqAppId     = '101345341';
    private $qqAppSecret = '7126efe43def7e864f79e7a7e605384f';
    private $qqRedirect  = 'http://passport.vronline.com/auth/qqCallback';

    // wx
    private $wxAppId     = 'wxc8de44d007aa521a';
    private $wxAppSecret = 'a29798e142e2b17d4ab0f197f9d9789a';
    private $wxRedirect  = 'http://passport.vronline.com/auth/wxCallback';

    // 3D播播
    private $boboAppId     = '2536459519';
    private $boboAppSecret = 'dsweR2stDubAs5d2cbea';
    private $boboRedirect  = 'http://passport.vronline.com/auth/boboCallback';

    /**
     * 页游开始游戏地址前缀
     */
    private $webgameStartUrl = '//web.vronline.com/start/';

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
     */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    // 打印数据
    protected function output($code, $data = null)
    {
        if ($code == 0 && $data) {
            $msg = Config::get("errorcode.{$code}");
            return json_encode(array("code" => $code, "data" => $data, "msg" => $msg));
        } else {
            $msg = Config::get("errorcode.{$code}");
            return json_encode(array("code" => $code, "msg" => $msg));
        }
    }

    // 引导用户到新浪微博的登录授权页面
    public function weibo(Request $request)
    {
        $url = $request->input('url', '');
        if (!empty($url)) {
            $this->wbRedirect = $this->wbRedirect . '?url=' . rawurlencode($url);
        } else {
            $referer = $request->headers->get('referer');
            $agent   = $request->header('User-agent');
            if (!$referer && $agent == "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.75 Safari/537.36") {
                $referer = 'vr_windows';
            }
            if ($referer) {
                $this->wbRedirect = $this->wbRedirect . '?url=' . rawurlencode($referer);
            }
        }

        $url = 'https://api.weibo.com/oauth2/authorize?client_id=' . $this->wbAppId . '&response_type=code&redirect_uri=' . rawurlencode($this->wbRedirect);
        return redirect($url);
    }

    // 引导用户到新浪微博的登录授权页面
    public function weibo4tg(Request $request)
    {
        $appid    = (int) $request->input('appid', 0);
        $serverid = (int) $request->input('serverid', 0);
        $adid     = (int) $request->input('adid', 0);

        if (!$appid || !$adid) {
            return "缺少参数";
        }

        $jumpUrl = $this->webgameStartUrl . $appid . "/" . $serverid;

        $this->wbRedirect = $this->wbRedirect . '?url=' . rawurlencode($jumpUrl) . '&appid=' . $appid . "&serverid=" . $serverid . "&adid=" . $adid;

        $url = 'https://api.weibo.com/oauth2/authorize?client_id=' . $this->wbAppId . '&response_type=code&redirect_uri=' . rawurlencode($this->wbRedirect);
        return redirect($url);
    }

    // 用户授权后新浪微博回调的页面
    public function wbCallback(Request $request)
    {
        $code    = $request->input('code');
        $jumpUrl = $request->input('url', '');
        $adid    = $request->input("adid", "");

        $referParams = parse_url($jumpUrl);
        /**
         * 判断是不是CP将VRonline做为第三方登录
         * 如果是，需要增加code参数返回
         */
        $third_login_appid = 0;
        if (isset($referParams['host']) && $referParams['host'] == "partner.vronline.com") {
            if (isset($referParams['query']) && $referParams['query']) {
                parse_str($referParams['query'], $tmp);
                if (isset($tmp['appid']) && $tmp['appid']) {
                    $third_login_appid = $tmp['appid'];
                }
            }
        }
        $url    = 'https://api.weibo.com/oauth2/access_token?client_id=' . $this->wbAppId . '&client_secret=' . $this->wbAppSecret . '&grant_type=authorization_code&code=' . $code . '&redirect_uri=' . $this->wbRedirect;
        $resStr = HttpRequest::post($url);
        $res    = json_decode($resStr, true);
        if (!$res || !isset($res['access_token'])) {
            return $this->outMsg(1, $jumpUrl);
        }
        // 得到用户信息
        $userInfo = $this->wbGetUserInfo($res['access_token'], $res['uid']);
        if (!$userInfo || !is_array($userInfo)) {
            return $this->outMsg(2, $jumpUrl);
        }
        // 微博 第三方注册逻辑
        // 1.判断是否已经注册，如果已经注册，就调用登录接口
        // 根据openid 判断是否已经注册
        $passport = new PassportModel();
        $uid      = $passport->isExists($userInfo['id'], 'weibo'); // 用户的唯一uid
        if ($uid === false) {
            return $this->outMsg(1114, $jumpUrl);
        }
        $loginCode = "";
        if ($uid && $uid !== "notexists") {
            $result = $passport->login($uid, '', 'weibo', 0, "", $third_login_appid); // 参数一：uid 参数二：密码(第三方登录没密码) 参数三：qq类型
            if (!is_array($result)) {
                return $this->outMsg(1305, $jumpUrl);
            }

            if (isset($result['code']) && $result['code'] != 0) {
                return $this->outMsg($result['code'], $jumpUrl);
            }

            if (isset($result['loginCode']) && $result['loginCode']) {
                $loginCode = $result['loginCode'];
            }
            if ($loginCode) {
                $jumpUrl = $jumpUrl . "&code=" . $loginCode;
            }
            $display_account = "";
            if ($result['data']['f_account']) {
                $display_account = $result['data']['f_account'];
            } else if ($result['data']['f_mobile']) {
                $display_account = $result['data']['f_mobile'];
            } else if ($result['data']['f_email']) {
                $display_account = $result['data']['f_email'];
            } else if ($result['data']['f_guest']) {
                $display_account = $result['data']['f_guest'];
            }

            $face = $passport->getHeadPicUrl($result['uid'], $result['data']['f_face_ver']);
            $data = array("uid" => $result['uid'], "token" => $result['token'], "account" => $display_account, "nick" => $result['data']['f_nick'], "face" => $face, "type" => 'weibo');
            return $this->outMsg(0, $jumpUrl, '', $data);
        } else {
            // 注册
            // 调用注册接口
            $params                 = array();
            $params['f_nick']       = $userInfo['screen_name']; // 昵称
            $params['f_third_face'] = $userInfo['profile_image_url']; // 用户40X40的图象
            $params['f_type']       = 5; // 注册类型 微博 是5
            $params['f_addip']      = Library::realIp(); // 获取ip
            $params['f_adid']       = $adid;
            /**
             * 注册
             */
            $ret = $passport->register($userInfo['id'], false, 'weibo', $params, 0, $third_login_appid); // 参数一：openid 参数二:密码 参数三：注册类型 参数四:基本信息
            if (!$ret || !is_array($ret)) {
                return $this->outMsg(1114, $jumpUrl);
            }
            if (isset($ret['loginCode']) && $ret['loginCode']) {
                $loginCode = $ret['loginCode'];
            }
            if ($loginCode) {
                $jumpUrl = $jumpUrl . "&code=" . $loginCode;
            }
            return $this->outMsg(0, $jumpUrl, '', $ret);
        }
    }

    // qq登录
    public function qq(Request $request)
    {
        $state = md5(uniqid(rand(), true));
        $url   = $request->input('url', '');
        if (!empty($url)) {
            $this->qqRedirect = $this->qqRedirect . '?url=' . rawurlencode($url);
        } else {
            $referer = $request->headers->get('referer');
            if (!$referer) {
                $referer = 'vr_windows';
            }
            if ($referer) {
                $this->qqRedirect = $this->qqRedirect . '?url=' . rawurlencode($referer);
            }
        }

        $dialog_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
        . $this->qqAppId . "&redirect_uri=" . rawurlencode($this->qqRedirect) . "&state="
            . $state;
        return redirect($dialog_url);
    }

    public function qq4tg(Request $request)
    {
        $appid    = (int) $request->input('appid', 0);
        $serverid = (int) $request->input('serverid', 0);
        $adid     = (int) $request->input('adid', 0);

        if (!$appid || !$adid) {
            return "缺少参数";
        }

        $state = md5(uniqid(rand(), true));

        $jumpUrl = $this->webgameStartUrl . $appid . "/" . $serverid;

        $this->qqRedirect = $this->qqRedirect . '?url=' . rawurlencode($jumpUrl) . '&appid=' . $appid . "&serverid=" . $serverid . "&adid=" . $adid;
        //var_dump($this->qqRedirect);exit;
        $url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
        . $this->qqAppId . "&redirect_uri=" . rawurlencode($this->qqRedirect) . "&state="
            . $state;
        return redirect($url);
    }

    // qq登录回调方法
    public function qqCallback(Request $request)
    {
        $code        = $request->input('code');
        $jumpUrl     = $request->input('url', '');
        $adid        = $request->input("adid", "");
        $referParams = parse_url($jumpUrl);
        /**
         * 判断是不是CP将VRonline做为第三方登录
         * 如果是，需要增加code参数返回
         */
        $third_login_appid = 0;
        if (isset($referParams['host']) && $referParams['host'] == "partner.vronline.com") {
            if (isset($referParams['query']) && $referParams['query']) {
                parse_str($referParams['query'], $tmp);
                if (isset($tmp['appid']) && $tmp['appid']) {
                    $third_login_appid = $tmp['appid'];
                }
            }
        }
        //拼接URL
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
        . "client_id=" . $this->qqAppId . "&redirect_uri=" . rawurlencode($this->qqRedirect)
        . "&client_secret=" . $this->qqAppSecret . "&code=" . $code;
        $response = HttpRequest::get($token_url);
        if (strpos($response, "callback") !== false) {
            $lpos     = strpos($response, "(");
            $rpos     = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
            $msg      = json_decode($response);
            if (isset($msg->error)) {
                return $this->outMsg(1, $jumpUrl, $msg->error_description);
            } else {
                return $this->outMsg(1, $jumpUrl);
            }
        }

        //Step3：使用Access Token来获取用户的OpenID
        $params = array();
        parse_str($response, $params);

        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $params['access_token'];
        $openRes   = HttpRequest::get($graph_url);
        if (strpos($openRes, "callback") !== false) {
            $lpos     = strpos($openRes, "(");
            $rpos     = strrpos($openRes, ")");
            $openJson = substr($openRes, $lpos + 1, $rpos - $lpos - 1);
        }
        $user = json_decode($openJson);
        if (isset($user->error)) {
            return $this->outMsg(1, $jumpUrl, $user->error_description);
        }

        $url      = 'https://graph.qq.com/user/get_user_info?access_token=' . $params['access_token'] . '&oauth_consumer_key=' . $this->qqAppId . '&openid=' . $user->openid;
        $userInfo = HttpRequest::get($url);
        $userInfo = json_decode($userInfo, true);
        if (!$userInfo) {
            return $this->outMsg(1, $jumpUrl);
        }

        $userInfo['openid'] = $user->openid;
//        if (strstr($jumpUrl)) {
        //
        //        }

        // 1.判断是否已经注册，如果已经注册，就调用登录接口

        // 根据openid 判断是否已经注册
        $passport = new PassportModel();
        $uid      = $passport->isExists($userInfo['openid'], 'qq');
        $out      = array();
        if ($uid === false) {
            return $this->outMsg(1114, $jumpUrl);
        }

        $loginCode = "";
        if ($uid && $uid !== "notexists") {
            $result = $passport->login($uid, '', 'qq', 0, "", $third_login_appid); // 参数一：uid 参数二：密码(第三方登录没密码) 参数三：qq类型
            if (!is_array($result)) {
                return $this->outMsg(1305, $jumpUrl);
            }
            if (isset($result['code']) && $result['code'] != 0) {
                return $this->outMsg($result['code'], $jumpUrl);
            }
            if (isset($result['loginCode']) && $result['loginCode']) {
                $loginCode = $result['loginCode'];
            }
            if ($loginCode) {
                $jumpUrl = $jumpUrl . "&code=" . $loginCode;
            }

            $display_account = "";
            if ($result['data']['f_account']) {
                $display_account = $result['data']['f_account'];
            } else if ($result['data']['f_mobile']) {
                $display_account = $result['data']['f_mobile'];
            } else if ($result['data']['f_email']) {
                $display_account = $result['data']['f_email'];
            } else if ($result['data']['f_guest']) {
                $display_account = $result['data']['f_guest'];
            }

            $face = $passport->getHeadPicUrl($result['uid'], $result['data']['f_face_ver']);

            $data = array("uid" => $result['uid'], "token" => $result['token'], "account" => $display_account, "nick" => $result['data']['f_nick'], "face" => $face, "type" => 'qq');
            return $this->outMsg(0, $jumpUrl, '', $data);
        } else {
            // 注册
            // 调用注册接口
            unset($params);
            $params['f_nick']       = $userInfo['nickname']; // 昵称
            $params['f_third_face'] = $userInfo['figureurl_qq_1']; // 用户40X40的图象
            $params['f_type']       = 3; // 注册类型 qq 是3
            $params['f_addip']      = Library::realIp(); // 获取ip
            $params['f_adid']       = $adid;
            /**
             * 注册
             */
            $ret = $passport->register($userInfo['openid'], false, 'qq', $params, 0, $third_login_appid); // 参数一：openid 参数二:密码 参数三：注册类型 参数四:基本信息
            if (!$ret || !is_array($ret)) {
                return $this->outMsg(1114, $jumpUrl);
            }
            if (isset($ret['loginCode']) && $ret['loginCode']) {
                $loginCode = $ret['loginCode'];
            }
            if ($loginCode) {
                $jumpUrl = $jumpUrl . "&code=" . $loginCode;
            }
            return $this->outMsg(0, $jumpUrl, '', $ret);
        }
    }

    // 微信唤醒登录页面
    public function wx(Request $request)
    {

        //state参数用于防止CSRF攻击，成功授权后回调时会原样带回
        $state = md5(uniqid(rand(), true));
        $url   = $request->input('url', '');
        if (!empty($url)) {
            $this->wxRedirect = $this->wxRedirect . '?url=' . rawurlencode($url);
        } else {
            $referer = $request->headers->get('referer');
            $agent   = $request->header('User-agent');
            if (!$referer && $agent == "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.75 Safari/537.36") {
                $referer = 'vr_windows';
            }
            if ($referer) {
                $this->wxRedirect = $this->wxRedirect . '?url=' . rawurlencode($referer);
            }
        }

        // 拼接URL
        $dialog_url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $this->wxAppId .
        "&redirect_uri=" . rawurlencode($this->wxRedirect) . "&response_type=code&scope=snsapi_login&state=" . $state .
            "#wechat_redirect";

        return redirect($dialog_url);
    }

    public function wx4tg(Request $request)
    {
        $appid    = (int) $request->input('appid', 0);
        $serverid = (int) $request->input('serverid', 0);
        $adid     = (int) $request->input('adid', 0);

        if (!$appid || !$adid) {
            return "缺少参数";
        }
        $jumpUrl = $this->webgameStartUrl . $appid . "/" . $serverid;

        $this->wxRedirect = $this->wxRedirect . '?url=' . rawurlencode($jumpUrl) . '&appid=' . $appid . "&serverid=" . $serverid . "&adid=" . $adid;

        $state = md5(uniqid(rand(), true));
        $url   = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $this->wxAppId . "&redirect_uri=" . rawurlencode($this->wxRedirect) . "&response_type=code&scope=snsapi_login&state=" . $state . "#wechat_redirect";

        return redirect($url);
    }

    // 微信回调页面
    public function wxCallback(Request $request)
    {
        $code        = $request->input('code');
        $jumpUrl     = $request->input('url', '');
        $adid        = $request->input("adid", "");
        $state       = $request->input('state');
        $referParams = parse_url($jumpUrl);
        /**
         * 判断是不是CP将VRonline做为第三方登录
         * 如果是，需要增加code参数返回
         */
        $third_login_appid = 0;
        if (isset($referParams['host']) && $referParams['host'] == "partner.vronline.com") {
            if (isset($referParams['query']) && $referParams['query']) {
                parse_str($referParams['query'], $tmp);
                if (isset($tmp['appid']) && $tmp['appid']) {
                    $third_login_appid = $tmp['appid'];
                }
            }
        }

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->wxAppId .
        "&secret=" . $this->wxAppSecret . "&code=" . $code . "&grant_type=authorization_code";
        $resStr = HttpRequest::post($url);
        $res    = json_decode($resStr, true);
        if (!$res || !isset($res['access_token'])) {
            return $this->outMsg(1, $jumpUrl);
        }
        $access_token = $res['access_token'];
        $url          = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $res['openid'];
        $resStr       = HttpRequest::post($url);
        $userInfo     = json_decode($resStr, true);
        if (!$userInfo || !isset($userInfo['unionid'])) {
            return $this->outMsg(1, $jumpUrl);
        }

        $passport = new PassportModel();
        $uid      = $passport->isExists($userInfo['unionid'], 'weixin');
        if ($uid === false) {
            return $this->outMsg(1114, $jumpUrl);
        }
        $loginCode = "";
        if ($uid && $uid !== "notexists") {
            $result = $passport->login($uid, '', 'weixin', 0, "", $third_login_appid); // 参数一：uid 参数二：密码(第三方登录没密码) 参数三：wx类型
            if (!is_array($result)) {
                return $this->outMsg(1305, $jumpUrl);
            }

            if (isset($result['code']) && $result['code'] != 0) {
                return $this->outMsg($result['code'], $jumpUrl);
            }

            if (isset($result['loginCode']) && $result['loginCode']) {
                $loginCode = $result['loginCode'];
            }
            if ($loginCode) {
                $jumpUrl = $jumpUrl . "&code=" . $loginCode;
            }

            $display_account = "";
            if ($result['data']['f_account']) {
                $display_account = $result['data']['f_account'];
            } else if ($result['data']['f_mobile']) {
                $display_account = $result['data']['f_mobile'];
            } else if ($result['data']['f_email']) {
                $display_account = $result['data']['f_email'];
            } else if ($result['data']['f_guest']) {
                $display_account = $result['data']['f_guest'];
            }

            $face = $passport->getHeadPicUrl($result['uid'], $result['data']['f_face_ver']);

            $data = array("uid" => $result['uid'], "token" => $result['token'], "account" => $display_account, "nick" => $result['data']['f_nick'], "face" => $face, "type" => 'weixin');
            return $this->outMsg(0, $jumpUrl, '', $data);
        } else {
            // 注册
            // 调用注册接口
            $params                 = array();
            $params['f_nick']       = $userInfo['nickname']; // 昵称
            $params['f_third_face'] = $userInfo['headimgurl']; // 用户40X40的图象
            $params['f_type']       = 4; // 注册类型 wx 是4
            $params['f_addip']      = Library::realIp(); // 获取ip
            $params["f_adid"]       = $adid;
            /**
             * 注册
             */
            $ret = $passport->register($userInfo['unionid'], false, 'weixin', $params, 0, $third_login_appid); // 参数一：openid 参数二:密码 参数三：注册类型 参数四:基本信息
            if (!$ret || !is_array($ret)) {
                return $this->outMsg(1114, $jumpUrl);
            }
            if (isset($ret['loginCode']) && $ret['loginCode']) {
                $loginCode = $ret['loginCode'];
            }
            if ($loginCode) {
                $jumpUrl = $jumpUrl . "&code=" . $loginCode;
            }
            return $this->outMsg(0, $jumpUrl, '', $ret);
        }
    }

    /**
     * 微博[string] $token [授权码]
     * @return mixed
     */
    private function wbGetUserInfo($token, $uid)
    {
        $url    = 'https://api.weibo.com/2/users/show.json?access_token=' . $token . '&uid=' . $uid;
        $resStr = HttpRequest::get($url);
        $res    = json_decode($resStr, true);
        return $res;
    }

    /**
     * 3D播播用户登录
     */
    public function bobo(Request $request)
    {

        $url = $request->input('url', '');
        if (!empty($url)) {
            $this->boboRedirect = $this->boboRedirect . '?url=' . base64_encode($url);
        } else {
            $referer = $request->headers->get('referer');
            $agent   = $request->header('User-agent');
            if (!$referer && $agent == "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.75 Safari/537.36") {
                $referer = 'vr_windows';
            }
            if ($referer) {
                $this->boboRedirect = $this->boboRedirect . '?url=' . base64_encode($referer);
            }
        }

        // 拼接URL
        $dialog_url = "https://passport.dpvr.cn/auth/login?appid=" . $this->boboAppId .
        "&redirect_uri=" . rawurlencode($this->boboRedirect);

        return redirect($dialog_url);
    }

    public function bobo4tg(Request $request)
    {
        $appid    = (int) $request->input('appid', 0);
        $serverid = (int) $request->input('serverid', 0);
        $adid     = (int) $request->input('adid', 0);

        if (!$appid || !$adid) {
            return "缺少参数";
        }
        $jumpUrl = $this->webgameStartUrl . $appid . "/" . $serverid;

        $this->boboRedirect = $this->boboRedirect . '?url=' . rawurlencode($jumpUrl) . '&appid=' . $appid . "&serverid=" . $serverid . "&adid=" . $adid;

        $state = md5(uniqid(rand(), true));
        $url   = "https://passport.dpvr.cn/auth/login?appid=" . $this->boboAppId . "&redirect_uri=" . rawurlencode($this->boboRedirect);

        return redirect($url);
    }

    // 微信回调页面
    public function boboCallback(Request $request)
    {
        $code    = $request->input('code');
        $jumpUrl = base64_decode($request->input('url', ''));
        $adid    = $request->input("adid", "");
        $url = "https://passport.dpvr.cn/auth/accessToken?appid=" . $this->boboAppId .
        "&appkey=" . $this->boboAppSecret . "&code=" . $code;
        $resStr = HttpRequest::get($url);
        $res = json_decode($resStr, true);
        if (!$res || !isset($res['code']) || $res['code'] != 1 || !isset($res['data']['openid']) || !$res['data']['openid']) {
            return $this->outMsg(1, $jumpUrl);
        }

        $account = $res['data']['openid'];
        $nick    = isset($res['data']['nick']) ? $res['data']['nick'] : "";
        $face    = isset($res['data']['face']) ? $res['data']['face'] : "";

        $passport = new PassportModel();
        $uid      = $passport->isExists($account, 'bobo');
        if ($uid === false) {
            return $this->outMsg(1114, $jumpUrl);
        }
        if ($uid && $uid !== "notexists") {
            $result = $passport->login($uid, '', 'bobo'); // 参数一：uid 参数二：密码(第三方登录没密码) 参数三：wx类型
            if (!is_array($result)) {
                return $this->outMsg(1305, $jumpUrl);
            }
            if (isset($result['code']) && $result['code'] != 0) {
                return $this->outMsg($result['code'], $jumpUrl);
            }

            $display_account = "";
            if ($result['data']['f_account']) {
                $display_account = $result['data']['f_account'];
            } else if ($result['data']['f_mobile']) {
                $display_account = $result['data']['f_mobile'];
            } else if ($result['data']['f_email']) {
                $display_account = $result['data']['f_email'];
            } else if ($result['data']['f_guest']) {
                $display_account = $result['data']['f_guest'];
            }

            $data = array("uid" => $result['uid'], "token" => $result['token'], "account" => $display_account, "nick" => $result['data']['f_nick'], "face" => $face, "type" => 'weixin');
            return $this->outMsg(0, $jumpUrl, '', $data);
        } else {
            // 注册
            // 调用注册接口
            $params                 = array();
            $params['f_nick']       = $nick; // 昵称
            $params['f_third_face'] = $face; // 用户40X40的图象
            $params['f_type']       = 7; // 注册类型 bobo 是7
            $params['f_addip']      = Library::realIp(); // 获取ip
            $params["f_adid"]       = $adid;
            /**
             * 注册
             */
            $ret = $passport->register($account, false, 'bobo', $params); // 参数一：openid 参数二:密码 参数三：注册类型 参数四:基本信息
            if (!$ret || !is_array($ret)) {
                return $this->outMsg(1114, $jumpUrl);
            }
            return $this->outMsg(0, $jumpUrl, '', $ret);
        }
    }

    private function outMsg($code, $jumpUrl, $msg = '', $data = array())
    {
        $isClient = false;
        if ($jumpUrl == "vr_windows") {
            $isClient = true;
        }

        $out         = array();
        $out['code'] = $code;
        if ($code == 0) {
            $out['data'] = $data;
        } else {
            if (!$msg) {
                $msg = "登录失败";
            }
            if ($msg) {
                $out['msg'] = $msg;
            } else {
                $out['msg'] = '登录失败';
            }
        }

        if ($isClient) {
            $json = json_encode($out, JSON_UNESCAPED_SLASHES);
            return '<script>window.CppCall("loginframe", "trdloginres", \'' . $json . '\')</script>';
        } else {
            if ($code == 0) {
                $this->setCookieArr($out['data']);
                return redirect($jumpUrl);
            } else {
                return redirect($jumpUrl);
                return Library::output($code, '', $out['msg']);
            }
        }
    }

    private function setCookieArr($arr)
    {
        foreach ($arr as $key => $value) {
            Cookie::queue($key, $value, null, "/", ".vronline.com");
        }
    }

    private function setLoginCookie($uid, $token, $account, $nick, $face)
    {
        Cookie::queue('uid', $uid, null, "/", ".vronline.com");
        Cookie::queue('token', $token, null, "/", ".vronline.com");
        Cookie::queue('account', $account, null, "/", ".vronline.com");
        Cookie::queue('nick', $nick, null, "/", ".vronline.com");
        Cookie::queue('face', $face, null, "/", ".vronline.com");
        return true;
    }
}
