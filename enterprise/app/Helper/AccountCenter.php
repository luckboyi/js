<?php

namespace Helper;

use Config;
use Cookie;
use Helper\HttpRequest;
use Helper\Library;

class AccountCenter
{
    private $appid = 0;

    private $appkey = "";

    private $paykey = "";

    private $did = "";

    private $passport_protocal = "http";

    private $pay_protocal = "http";

    /**
     * @param   int     appid
     * @param   string  appkey
     */
    public function __construct($appid = "", $appkey = "", $paykey = "")
    {
        if (!$appid) {
            $appid  = Config::get("common.uc_appid");
            $appkey = Config::get("common.uc_appkey");
        }
        $this->appid  = $appid;
        $this->appkey = $appkey;
        if ($paykey) {
            $this->paykey = $paykey;
        }

        $this->did = strval(Cookie::get("did"));

        if (Library::getCurrEnv() == "product") {
            $this->passport_protocal = "http";
            $this->pay_protocal      = "https";
        }

        if (!$this->appid || !$this->appkey) {
            // 抛出异常
            return false;
        }
    }

    private function addSign($post_string)
    {
        $post_string['appid'] = $this->appid;
        $post_string['time']  = time();
        $post_string['sign']  = md5($post_string['appid'] . $post_string['json'] . $post_string['time'] . $this->appkey);
        return $post_string;
    }

    /**
     * 判断登录状态
     * @param   int     uid     账号
     * @param   string  token   密码
     * @return  array   ['code'=>0]
     */
    public function checkLogin($uid, $token)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/checkLogin";

        if (!$uid || !$token) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 判断用户名是否存在
     * @param   string  account   密码
     * @return  array   ['code'=>0]
     */
    public function isExists($account)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user/existUsername";

        $account = strtolower(trim($account));
        if (!$account) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("account" => $account);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 添加不用于登录的账号
     * @param   string  account     账号
     * @param   string  pwd         密码
     * @param   string  type        账号类型，account:普通账号; mobile:手机注册; email:邮箱注册;
     * @param   string  code        验证码，手机注册、邮箱注册需要
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function addNoLoginUser($account, $cip = "")
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/register/nologin";
        if (!$account) {
            return $this->output(2001, "参数错误(1)");
        }
        $cip                 = $cip ? $cip : Library::realIp();
        $post_string         = array("account" => $account, "appid" => $this->appid, "cip" => $cip);
        $sign                = Library::encrypt($post_string, $this->appkey);
        $post_string['sign'] = $sign;
        $ret                 = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "注册失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "注册失败");
        }
        return $res;
    }

    /**
     * 注册
     * @param   string  account     账号
     * @param   string  pwd         密码
     * @param   string  type        账号类型，account:普通账号; mobile:手机注册; email:邮箱注册;
     * @param   string  code        验证码，手机注册、邮箱注册需要
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function register($account, $pwd, $type, $code = "", $ext = array())
    {
        if (!in_array($type, array("account", "mobile", "email"))) {
            return $this->output(1102, "参数错误(3)");
        }

        $request = "{$this->passport_protocal}://passport.vronline.com/user/register/{$type}";
        if (!$account || !$pwd || strlen($pwd) != 32) {
            return $this->output(2001, "参数错误(1)");
        }
        $param = array("account" => strtolower(trim($account)), "pwd" => $pwd, "appid" => $this->appid, "did" => $this->did, 'cip' => Library::realIp());
        if ($code) {
            $param['code'] = $code;
        }
        if ($ext) {
            $param = $param + $ext;
        }
        $post_string = array("json" => json_encode($param));
        $post_string = $this->addSign($post_string);
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "注册失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "注册失败");
        }
        return $res;
    }

    /**
     * 登录
     * @param   string  account     账号
     * @param   string  pwd         密码
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function login($account, $pwd, $code = '', $ext = array())
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user/login/account";

        if (!$account || !$pwd || strlen($pwd) != 32) {
            return $this->output(2001, "参数错误");
        }
        $param = array("account" => strtolower(trim($account)), "pwd" => $pwd, "appid" => $this->appid, "did" => $this->did, 'cip' => Library::realIp());
        if ($code) {
            $param['code'] = $code;
        }
        if ($ext) {
            $param = $param + $ext;
        }
        $post_string = array("json" => json_encode($param));
        $post_string = $this->addSign($post_string);
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "登录失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res)) {
            return $this->output(1, "登录失败");
        }
        return $res;
    }

    /**
     * 游客自动注册，如果传了，并且是登录状态，不创建账号，否则创建新的账号，密码为空
     * @param   int     uid     账号，可选
     * @param   string  token   token，可选
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function guest($uid = "", $token = "")
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/register/guest";

        $param                    = array();
        $uid && $param['uid']     = $uid;
        $token && $param['token'] = $token;
        $param["did"]             = $this->did;

        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "注册失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "注册失败");
        }
        return $res;
    }

    /**
     * 获取用户信息
     * @param   int     uid     账号
     * @param   string  token   密码
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function info($uid, $token)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user";

        if (!$uid || !$token) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token, "appid" => $this->appid);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 获取登录记录
     * @param   int     uid     账号
     * @return  array   {"code":0,"data":{"last_time":"2016-09-14 15:56:02","addip":"127.0.0.1"},"msg":"操作成功"}
     */
    public function getLoginRecord($uid)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user/loginRecord";

        if (!$uid) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "appid" => $this->appid);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 获取评论的用户的信息
     * @param   int     uid     账号
     * @return  array   {"code":0,"data":{"last_time":"2016-09-14 15:56:02","addip":"127.0.0.1"},"msg":"操作成功"}
     */
    public function getCommentUserInfo($uid, $appid)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/comment/getCommentUserInfo";

        if (!$uid) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "appid" => $appid);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 更新数据库某个字段信息
     * @param   int     uid     账号
     * @param   string  token   密码
     * @return  array   {"code":0,"msg":"\u64cd\u4f5c\u6210\u529f"}
     */
    public function updateField($uid, $token, $data)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user/updateField";

        if (!$uid || !$token || !$data) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token, 'data' => $data);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 获取用户信息，用于后台查询
     * @param   int     uid     账号
     * @param   string  token   密码
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function getUserInfoByAdmin($uid)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user/info";

        if (!$uid) {
            return $this->output(2001, "参数错误");
        }
        $param = array("appid" => $this->appid, "uid" => intval($uid), "ts" => time());
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 获取用户id，用于后台查询
     * @param   int     uid     账号
     * @param   string  token   密码
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function getUidByAdmin($account)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user/id";

        if (!$account) {
            return $this->output(2001, "参数错误");
        }
        $param = array("appid" => $this->appid, "account" => strtolower($account), "ts" => time());
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 封/解封用户
     * @param   int     uid     账号
     * @param   string  token   密码
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function disableUsers($type, $uids, $endtime = 0)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user";

        if (!$type || !$uids || !is_array($uids)) {
            return $this->output(2001, "参数错误");
        }
        if ($type == "disable") {
            $request = "{$this->passport_protocal}://passport.vronline.com/user/disable/disable";
            $param   = array("appid" => $this->appid, "uids" => json_encode($uids), "endtime" => $endtime, "ts" => time());
        } else {
            $request = "{$this->passport_protocal}://passport.vronline.com/user/disable/enable";
            $param   = array("appid" => $this->appid, "uids" => json_encode($uids), "ts" => time());
        }
        $ret = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 修改密码
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  oldpwd  老密码
     * @param   string  newpwd1 新密码
     * @return  array   ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]
     */
    public function changePwd($uid, $token, $oldpwd, $newpwd1)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/user/changePwd";

        if (!$uid || !$token || !$oldpwd || strlen($oldpwd) != 32 || !$newpwd1 || strlen($newpwd1) != 32) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token, "oldpwd" => $oldpwd, "newpwd1" => $newpwd1, "newpwd2" => $newpwd1);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "修改失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "修改失败");
        }
        return $res;
    }

    /**
     * 绑定账号
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  account 账号
     * @param   string  pwd     密码
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function bindAccount($uid, $token, $account, $pwd)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/account/set";

        if (!$uid || !$token || !$account || !$pwd || strlen($pwd) != 32) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token, "account" => strtolower(trim($account)), "pwd" => $pwd);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "修改失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "修改失败");
        }
        return $res;
    }

    /**
     * 绑定第三方账号
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  account 账号
     * @param   string  pwd     密码
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function bindThridAccount($uid, $token, $account, $type, $needlogin = 1)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/account/bindthirdaccount";

        if (!$uid || !$type || !$account) {
            return $this->output(2001, "参数错误");
        }
        $param         = array("uid" => intval($uid), "token" => $token, "acc" => strtolower(trim($account)), "type" => $type, "needlogin" => $needlogin);
        $sign          = Library::encrypt($param, $this->appkey);
        $param['sign'] = $sign;
        $ret           = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "修改失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "修改失败");
        }
        return $res;
    }

    /**
     * 绑定手机号码发送短信验证码
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  mobile  手机号码
     * @param   string  action  (只有换绑手机发送验证码时用)（mobileChange）
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function sendBindMsg($uid, $token, $mobile, $action = null)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/sendmsg/bind";

        if (!$uid || !$token || !$mobile) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token, "mobile" => $mobile, "action" => $action);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "发送失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "发送失败");
        }
        return $res;
    }

    /**
     * 发送验证码
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  mobile  手机号码
     * @param   string  action  发送类型
     * @param   array  content  短信参数
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function sendSmsMsg($account, $mobile, $action = 'find_pwd_mobile', $content = [])
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/sendmsg/findpwd";

        $account    = strtolower(trim($account));
        $bindMobile = $mobile;
        if (!$account) {
            return $this->output(2001, "参数错误");
        }

        if (!$bindMobile) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("account" => $account, 'bindMobile' => $bindMobile, 'smsTp' => $action, 'smsContent' => $content);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "发送失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "发送失败");
        }
        return $res;
    }

    /**
     * 绑定手机号码
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  mobile  手机号码 -- (如果是换绑手机号，这个是账号原来绑定的手机号，用于删除t_login_x表中记录)
     * @param   string  code    验证码
     * @param   string  mobileChange    换绑的手机号（换绑的时候必须传，其他可以不传）
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function bindMobile($uid, $token, $mobile, $code, $mobileChange = null)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/bind/mobile";

        if (!$uid || !$token || !$mobile || !$code) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token, "mobile" => $mobile, "msgCode" => $code, "mobileChange" => $mobileChange);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "绑定失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "绑定失败");
        }
        return $res;
    }

    /**
     * 解绑手机号码
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  mobile  手机号码
     * @param   string  code    验证码
     * @param   string  mobileChange    换绑的手机号（换绑的时候必须传，其他可以不传）
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function unBindMobile($uid, $token, $mobile, $code)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/unbind/mobile";

        if (!$uid || !$token || !$mobile) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("uid" => intval($uid), "token" => $token, "mobile" => $mobile, "code" => $code);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "解绑失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "解绑失败");
        }
        return $res;
    }

    /**
     * 找回密码
     * @param   string  account 账号
     * @param   string  pwd     密码
     * @param   string  code    验证码
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function findPassword($account, $pwd, $code)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/password/find";

        $account = strtolower(trim($account));
        $pwd     = strtolower(trim($pwd));
        if (!$account || !$pwd || strlen($pwd) != 32 || !$code) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("account" => $account, "pwd" => $pwd, "msgCode" => $code);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "设置失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "设置失败");
        }
        return $res;
    }

    /**
     * open添加子管理账号
     * @param   string  account 账号
     * @param   string  code    验证码
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function addSonCheckMsg($account, $mobile, $code)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/addSonCheckMsg";

        $account = strtolower(trim($account));
        $mobile  = strtolower(trim($mobile));
        if (!$account || !$mobile || !$code) {
            return $this->output(2001, "参数错误");
        }
        $param       = array("account" => $account, "mobile" => $mobile, "msgCode" => $code);
        $post_string = array("json" => json_encode($param));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "设置失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "设置失败");
        }
        return $res;
    }

    /**
     * 获取app信息
     * @param   int     appid
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function getAppInfo($appid)
    {
        $appid = intval($appid);
        if (!$appid) {
            return $this->output(2001, "参数错误");
        }
        $request = "http://appinfo.vronline.com/get/{$appid}";

        $ret = HttpRequest::get($request);
        if (!$ret) {
            return $this->output(1, "读取失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "读取失败");
        }
        if (!isset($res['code']) || $res['code'] != 0) {
            return array();
        }
        return $res['data'];
    }

    /**
     * 设置app信息
     * @param   int     appid
     * @param   arrat   info
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function setAppInfo($appid, $appinfo)
    {
        $appid = intval($appid);
        if (!$appid || !$appinfo || !is_array($appinfo)) {
            return $this->output(2001, "参数错误");
        }
        $request = "http://appinfo.vronline.com/set/{$appid}";

        $post_string = array("json" => json_encode($appinfo));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "设置失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "设置失败");
        }
        return $res;
    }

    /**
     * 读一个服信息
     * @param   int     appid
     * @param   int     serverid
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function getOneServerInfo($appid, $serverid)
    {
        $appid    = intval($appid);
        $serverid = intval($serverid);
        if (!$appid || !$serverid) {
            return $this->output(2001, "参数错误");
        }
        $request = "http://appinfo.vronline.com/server/get/{$appid}/{$serverid}";

        $ret = HttpRequest::get($request);
        if (!$ret) {
            return $this->output(1, "读取失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "读取失败");
        }
        return $res;
    }

    /**
     * 设置一个服信息
     * @param   int     appid
     * @param   int     serverid
     * @param   array   info
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function setServerInfo($appid, $serverid, $info)
    {
        $appid    = intval($appid);
        $serverid = intval($serverid);
        if (!$appid || !$serverid || !$info || !is_array($info)) {
            return $this->output(2001, "参数错误");
        }
        $request = "http://appinfo.vronline.com/server/set/{$appid}/{$serverid}";

        $post_string = array("json" => json_encode($info));
        $ret         = HttpRequest::post($request, $post_string);
        if (!$ret) {
            return $this->output(1, "设置失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "设置失败");
        }
        return $res;
    }

    /**
     * 获得token
     * @param   int     appid
     * @return  array   ['code'=>0, 'data'=>xxxxx]
     */
    public function getPayToken($uid, $token, $needopenid = "")
    {

        $uid = intval($uid);
        if (!$uid || !$this->paykey || !$token) {
            return $this->output(2001, "参数错误");
        }
        $request = "{$this->pay_protocal}://pay.vronline.com/paytoken";

        $param         = array("game_type" => $this->appid, "uid" => $uid, "vrkey" => $token, "needopenid" => $needopenid, "ts" => time());
        $sign          = Library::encrypt($param, $this->paykey);
        $param['sign'] = $sign;
        $ret           = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "获取失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "获取失败");
        }
        return $res;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             2b 版 本 接 口                                                  |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 检查是否有设置取款密码
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @return  array   ['code'=>0] 有设置，['code' => 404] 未设置
     */
    public function hasPaypwd($merchantid, $token)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/hasPaypwd";

        if (!$merchantid || !$token) {
            return $this->output(2001, "参数错误");
        }
        $param = array("merchantid" => intval($merchantid), "token" => $token);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 设置取款密码，或修改取款密码
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  oldpwd   老的密码，如果没有，可以不传，或传空
     * @param   string  newpwd   新的密码
     * @return  array   ['code'=>0]
     */
    public function setPaypwd($merchantid, $token, $code, $oldpwd, $newpwd)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/setPaypwd";

        if (!$merchantid || !$token || !$newpwd) {
            return $this->output(2001, "参数错误");
        }

        $param = array("merchantid" => intval($merchantid), "token" => $token, "code" => $code, "newpwd" => $newpwd);
        if ($oldpwd) {
            $param["oldpwd"] = $oldpwd;
        }

        $ret = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 登录取款密码
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  paypwd   取款密码
     * @return  array   ['code'=>0]
     */
    public function loginPaypwd($merchantid, $token, $paypwd)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/loginPaypwd";

        if (!$merchantid || !$token || !$paypwd) {
            return $this->output(2001, "参数错误");
        }
        $param = array("merchantid" => intval($merchantid), "token" => $token, "paypwd" => $paypwd);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 发起退款
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  cashtoken   取款密码登录token
     * @return  array   ['code'=>0]
     */
    public function goRefund($merchantid, $token, $orderId)
    {
        $request = "{$this->passport_protocal}://pay.vronline.com/create/create2bRefundOrder";

        if (!$merchantid || !$token || !$orderId) {
            return $this->output(2001, "参数错误");
        }
        $param = array("merchantid" => intval($merchantid), "token" => $token, "orderid" => $orderId);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 查询余额
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @return  array   ['code'=>0]
     */
    public function get2bBalance($merchantid, $token)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/get2bBalance";
        if (!$merchantid || !$token) {
            return $this->output(2001, "参数错误");
        }

        $param = array("merchantid" => intval($merchantid), "token" => $token);
        $ret   = HttpRequest::post($request, $param);

        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 取现扣余额
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  paypwd   取款密码
     * @param   float  amount   取款金额，单位 元
     * @return  array   ['code'=>0]
     */
    public function cashSubbalance($merchantid, $token, $paypwd, $amount)
    {
        $request = "{$this->pay_protocal}://pay.vronline.com/cashSubbalance";

        if (!$merchantid || !$token || !$paypwd || !$amount) {
            return $this->output(2001, "参数错误");
        }
        $param = array("merchantid" => intval($merchantid), "token" => $token, "paypwd" => $paypwd, "amount" => $amount);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 查询银行卡列表
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @return  array   ['code'=>0]
     */
    public function get2bCards($merchantid, $token)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/get2bCards";

        if (!$merchantid || !$token) {
            return $this->output(2001, "参数错误");
        }
        $param = array("merchantid" => intval($merchantid), "token" => $token);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 查询银行卡信息
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  card_id   银行id
     * @param   string  card_no   银行卡号
     * @return  array   ['code'=>0]
     */
    public function get2bCard($merchantid, $token, $card_id = 0)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/get2bCard";

        if (!$merchantid || !$token || !$card_id) {
            return $this->output(2001, "参数错误");
        }
        $param = array("merchantid" => intval($merchantid), "token" => $token, 'card_id' => $card_id);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 添加银行卡列表
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  card_no   银行卡号
     * @param   string  card_name   姓名
     * @param   string  card_province 省
     * @param   string  card_city 市
     * @param   string  card_opener 开户行名称
     * @param   string  card_bank_name 银行名称
     * @param   string  card_type_name 银行卡类型名称
     * @param   string  card_pay_num 汇付宝编号
     * @param   string  card_owner 银行卡类型
     * @return  array   ['code'=>0]
     */
    public function add2bCard($merchantid, $token, $card_no, $card_name, $card_province, $card_city, $card_opener, $card_bank_name, $card_type_name, $card_pay_num, $card_owner = 0)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/save2bCard";

        if (!$merchantid || !$token || !$card_name || !$card_no || !$card_province || !$card_city || !$card_opener || !$card_bank_name || !$card_type_name || !$card_pay_num) {
            return $this->output(2001, "参数错误");
        }
        if (!in_array($card_owner, [0, 1])) {
            return $this->output(2001, "参数错误");
        }

        $param = array("merchantid" => intval($merchantid), "token" => $token, 'card_no' => $card_no, 'card_name' => $card_name, 'card_province' => $card_province, 'card_city' => $card_city, 'card_opener' => $card_opener, 'card_bank_name' => $card_bank_name, 'card_type_name' => $card_type_name, 'card_pay_num' => $card_pay_num, "card_owner" => $card_owner);
        $ret   = HttpRequest::post($request, $param);

        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 检查银行卡信息
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  card_no   银行卡号
     * @return  array   ['code'=>0]
     */
    public function check2bCard($merchantid, $token, $card_no)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/check2bCard";

        if (!$merchantid || !$token || !$card_no) {
            return $this->output(2001, "参数错误");
        }
        $param = array("merchantid" => intval($merchantid), "token" => $token, 'card_no' => $card_no);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 设置默认银行卡
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  card_id   银行id
     * @return  array   ['code'=>0]
     */
    public function default2bCard($merchantid, $token, $card_id)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/default2bCard";

        if (!$merchantid || !$token || !$card_id) {
            return $this->output(2001, "参数错误");
        }

        $param = array("merchantid" => intval($merchantid), "token" => $token, 'card_id' => $card_id);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 删除银行卡信息
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   string  card_id   银行id
     * @return  array   ['code'=>0]
     */
    public function del2bCard($merchantid, $token, $card_id)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/del2bCard";

        if (!$merchantid || !$token || !$card_id) {
            return $this->output(2001, "参数错误");
        }

        $param = array("merchantid" => intval($merchantid), "token" => $token, 'card_id' => $card_id);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 提取余额
     * @param   int     merchantid     账号
     * @param   string  token   token
     * @param   int  card_id   银行id
     * @param   double   cash   金额
     * @param   string  pwd   密码
     * @param   string  code   验证码
     * @return  array   ['code'=>0]
     */
    public function extractCash($merchantid, $token, $card_id, $cash, $pwd, $code)
    {
        $request = "{$this->passport_protocal}://passport.vronline.com/tob/extractBankCash";

        if (!$merchantid || !$token || !$card_id || !$cash || !$pwd || !$code) {
            return $this->output(2001, "参数错误");
        }

        $param = array("merchantid" => intval($merchantid), "token" => $token, 'card_id' => $card_id, 'cash' => $cash, 'pwd' => $pwd, 'code' => $code);
        $ret   = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 获取提现记录
     * @param   int     adminuid     账号
     * @param   string  token   token
     * @param   string  type   记录类型 0:待审核，未处理的;4:审核失败;5:审核通过，未付款的;6:正在付款，未处理完成;7:付款失败;8:付款成功
     * @return  array   ['code'=>0]
     */
    public function extractCashLog($adminuid = '', $type = -1, $page = 1, $show = false)
    {
        $request = "{$this->passport_protocal}://api.vronline.com/2b/extractCashLog";
        $param   = array("ts" => time(), 'page' => $page);
        if ($adminuid) {
            $param['adminuid'] = $adminuid;
        }
        if ($type > -1) {
            $param['type'] = $type;
        }
        if ($show) {
            $param['showpage'] = 1;
        }

        $sign          = Library::encrypt($param, $this->paykey);
        $param['sign'] = $sign;
        $ret           = HttpRequest::get($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 审核提现记录
     * @param   string  orderid   订单号
     * @param   int     adminuid     账号
     * @param   string  token   token
     * @param   string  type   操作类型，pass:审核通过;deny:拒绝;
     * @return  array   ['code'=>0]
     */
    public function extractCashConfirm($orderid, $adminuid, $type)
    {
        $request = "{$this->passport_protocal}://api.vronline.com/2b/extractCashConfirm";

        if (!$orderid || !$type) {
            return $this->output(2001, "参数错误");
        }

        $param         = array("orderid" => $orderid, "adminuid" => $adminuid, "type" => $type, "ts" => time());
        $sign          = Library::encrypt($param, $this->paykey);
        $param['sign'] = $sign;
        $ret           = HttpRequest::get($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 付款
     * @param   int     adminuid     admin后台登录id
     * @return  array   ['code'=>0]
     */
    public function pay4Merchant($adminuid)
    {
        $request = "{$this->pay_protocal}://pay.vronline.com/create/create2bCashOrder";

        if (!$adminuid) {
            return $this->output(2001, "参数错误");
        }

        $param         = array("adminid" => $adminuid, "ts" => time());
        $sign          = Library::encrypt($param, $this->paykey);
        $param['sign'] = $sign;
        $ret           = HttpRequest::get($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 获取openid
     * @param   int     uid     账号
     * @param   string  token   token
     * @return  array   ['code'=>0]
     */
    public function getOpenId($uid, $token)
    {
        $request       = "{$this->passport_protocal}://openapi.vronline.com/openid";
        $param         = array("ts" => time(), 'uid' => $uid, 'vrkey' => $token, 'appid' => $this->appid);
        $sign          = Library::encrypt($param, $this->appkey);
        $param['sign'] = $sign;
        $ret           = HttpRequest::post($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    /**
     * 获取用户订单
     * @param   int     uid     账号
     * @param   string  token   token
     * @param   string  type    订单类型 0:充值订单;9:消费订单;
     * @param   int        page    页数
     * @param   int        len     每页记录数
     * @return  array   ['code'=>0]
     */
    public function getUserOrders($uid, $token, $type, $page, $len)
    {
        $request = "{$this->pay_protocal}://pay.vronline.com/userOrders";
        $param   = array('uid' => $uid, 'token' => $token, 'type' => $type, 'page' => $page, 'len' => $len);
        $ret     = HttpRequest::get($request, $param);
        if (!$ret) {
            return $this->output(1, "请求失败");
        }
        $res = json_decode($ret, true);
        if (!is_array($res) || !$res) {
            return $this->output(1, "请求失败");
        }
        return $res;
    }

    private function output($code, $msg, $data = "")
    {
        $return = array("code" => $code, "msg" => $msg);
        if ($data) {
            $return['data'] = $data;
        }
        return $return;
    }
}
/*
$account = new AccountCenter(1,1);
//$ret = $account->register("testabcdf1", md5(111111), "account");
$ret = $account->login("13482779535", md5(333333));
//$ret = $account->guest();
//$ret = $account->info(10018, "23f09bf2c2fcfffc56b874727918c44a");
//$ret = $account->changePwd(10017, "b4e2aed6a08de9c8b8d10afed8da17d9", md5(111111), md5(222222));
//$ret = $account->bindAccount(10018, "23f09bf2c2fcfffc56b874727918c44a", "testab", md5(111111));
//$ret = $account->sendBindMsg(10017, "ed294a44756ca1dfc1cddb744ade9d31", 13482779535);
//$ret = $account->bindMobile(10017, "ed294a44756ca1dfc1cddb744ade9d31", 13482779535, "377261");
//$ret = $account->sendFindPwdMsg("testa");
//$ret = $account->findPassword("testa", md5(333333), "244166");
//$ret = $account->checkLogin(10017, "03ca61b100c141f54baad5a4e15288ac");
//$ret = $account->isExists("testa");
echo "ret ==> ";var_dump($ret);echo "\n";
 */
