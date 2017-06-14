<?php

namespace App\Models;

// 引用Model
use App\Models\AppinfoModel;
use App\Models\DataCenterStatModel;
use App\Models\LoginModel;
use App\Models\OpenidModel;
use App\Models\UserModel;
use App\Models\VerifyCodeModel;
use Config;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class PassportModel extends Model
{
    /**
     * 设备号
     */
    protected $did;

    /**
     * 设置属性
     */
    public function setProp($key, $val)
    {
        $this->$key = $val;
    }

    /**
     * 设置属性
     */
    public function getProp($key)
    {
        return $this->$key;
    }

    /**
     * 获取头像的相对路径
     */
    public function getUploadFacePath($uid)
    {
        $path1 = $uid % 100;
        $path2 = $uid % 100000;
        return $path1 . "/" . $path2 . "/" . $uid . ".jpg";
    }

    /**
     * 签名校验
     */
    public function checkLoginSign(Request $request)
    {
        $param = json_decode($request->input("json"), true);
        if (!$param || !is_array($param)) {
            return false;
        }
        if (!isset($param['sign']) || !$param['sign']) {
            return false;
        }
        $sign = $param['sign'];
        if (!isset($param['appid'])) {
            return false;
        }
        $appid = intval($param['appid']);
        if (!$appid) {
            return false;
        }
        $app    = new AppinfoModel;
        $info   = $app->info($appid);
        $appkey = $info['appkey'];
        if (!$appkey) {
            return false;
        }
        unset($param['sign']);
        $check = Library::encrypt($param, $appkey);
        if ($check == $sign) {
            return true;
        }
        return false;
    }

    /**
     * 注册
     * 各种注册都用此方法
     * 该方法只处理从生成uid开始到注册结束的过程，判断用户名是否存在需在调用改方法前处理。
     * @param   string  username
     * @param   string  pwd     客户端提交上来的密码，只一次md5后的
     * @param   string  type    注册用户名类型，guest: 游客; qq: qq; weibo: 微博; weixin: 微信; mobile: 手机注册; email: 邮箱注册; account: 普通账号; bobo: 3D播播账号;
     * @param   string  baseinfo    其他基本信息，比如 f_nick/f_third_face等
     * @return  array  成功返回: [uid => xxx, token => xxx]
     */
    public function register($account, $pwd, $type, $baseinfo = array(), $appid = 0, $third_login_appid = 0)
    {
        // 用户名必须要有
        if (empty($account)) {
            return false;
        }
        if ($baseinfo && !is_array($baseinfo)) {
            return false;
        }
        if (!$baseinfo) {
            $baseinfo = array();
        }

        /**
         * 如果是第三方注册，或者游客自动注册，不能有密码，否则密码必须要有，并且是一次MD5后的，32字节。
         */
        if (in_array($type, array("qq", "weixin", "weibo", "ali", "bobo", "guest", "nologin"))) {
            if ($pwd) {
                return false;
            }
            $md5pwd = "";
        } else {
            if (strlen($pwd) != 32) {
                return false;
            }
            $md5pwd = md5($pwd);
        }

        switch ($type) {
            case "qq":
                $account   = "qq:" . $account;
                $md5pwd    = "";
                $userfield = "f_qqopenid";
                break;
            case "weibo":
                $account   = "wb:" . $account;
                $md5pwd    = "";
                $userfield = "f_wbopenid";
                break;
            case "weixin":
                $account   = "wx:" . $account;
                $md5pwd    = "";
                $userfield = "f_wxopenid";
                break;
            case "ali":
                $account   = "ali:" . $account;
                $md5pwd    = "";
                $userfield = "f_aliopenid";
                break;
            case "bobo":
                $account   = "bobo:" . $account;
                $md5pwd    = "";
                $userfield = "f_boboid";
                break;
            case "guest":
                $account   = "guest:" . $account;
                $md5pwd    = "";
                $userfield = "f_guest";
                break;
            case "account":
                $userfield = "f_account";
                break;
            case "mobile":
                $userfield = "f_mobile";
                break;
            case "email":
                $userfield = "f_email";
                break;
            case "nologin":
                $account   = "nologin:" . $account;
                $md5pwd    = "nologin";
                $userfield = "f_guest";
                break;
            default:break;
        }

        $result = array();

        $userModel = new UserModel();
        $uid       = $userModel->tUidInsert($account); // 得到插入最后的主键id

        if (!$uid) {
            return false;
        }

        //3. 插入db_login库t_login_x表
        $loginModel        = new LoginModel();
        $tLogin['f_login'] = $account;
        $tLogin['f_uid']   = $uid;
        $lret              = $loginModel->tLoginInsert($tLogin);
        if (!$lret) {
            return false;
        }

        //4. 插入db_user.t_user_info_x
        $baseinfo['f_uid']    = $uid;
        $baseinfo['f_pwd']    = $md5pwd;
        $baseinfo[$userfield] = $account;
        if (!isset($baseinfo['f_addip']) || !$baseinfo['f_addip']) {
            $baseinfo['f_addip'] = Library::realIp();
        }
        if (in_array($type, array("mobile", "email"))) {
            if (!isset($baseinfo['f_nick'])) {
                $l                  = strlen($account);
                $repeat             = str_repeat("*", $l - 4);
                $baseinfo['f_nick'] = substr($account, 0, 2) . $repeat . substr($account, -2);
            }
        } else {
            $baseinfo['f_nick'] = isset($baseinfo['f_nick']) ? $baseinfo['f_nick'] : $account;
        }

        $uret = $userModel->tUserinfo($baseinfo);
        if (!$uret) {
            return false;
        }
        $token = $this->genToken($uid, "login_token");

        // 向record库中的login_log 表插入一条记录，uid,appid,addip
        $param['uid']   = $uid;
        $param['appid'] = $appid;
        $param['type']  = $type;
        $param['city']  = "";
        $param['ts']    = time();
        $param['ip']    = $baseinfo['f_addip'];

        $loginModel = new LoginModel;
        $lastlogin  = $loginModel->setLastLogin($uid, $param); // 得到插入的主键id
        unset($param);

        $face = isset($baseinfo['f_third_face']) ? $baseinfo['f_third_face'] : $this->getHeadPicUrl($uid, 0);
        $nick = isset($baseinfo['f_nick']) ? $baseinfo['f_nick'] : "";

        /**
         * 合作方通过VRonline
         * 根据appid生成openid
         * 根据appid和openid生成code
         */
        $loginCode = "";
        if ($third_login_appid) {
            $openid     = OpenidModel::getOpenid($third_login_appid, $uid);
            $loginModel = new LoginModel();
            $loginCode  = Library::genCode("str", 32);
            $loginModel->genTmpCode("third_code_" . $loginCode, $openid);
        }

        /**
         * 发送统计
         */
        $properties = [
            "did"           => $this->getProp("did"),
            "_channel"      => isset($baseinfo['f_source1']) ? $baseinfo['f_source1'] : "",
            "from_appid"    => isset($baseinfo['f_from_appid']) ? $baseinfo['f_from_appid'] : $appid,
            "appid"         => $appid,
            "_advid"        => isset($baseinfo['f_adid']) ? $baseinfo['f_adid'] : "",
            "account_ref"   => $type,
            "register_type" => $type == "guest" ? "manual" : "auto",
            "isall"         => 1,
        ];
        DataCenterStatModel::stat("vrplat", "register", $uid, $properties);

        /**
         * 发送登录统计
         */
        $login_type = $this->getLoginType($type);
        $arrlog     = [
            'event'      => 'user_login',
            'uid'        => $uid,
            "mac"        => $this->getProp("did"),
            "channel_id" => isset($baseinfo['f_source1']) ? strval($baseinfo['f_source1']) : "",
            "login_type" => $login_type,
            'ip'         => $baseinfo['f_addip'],
        ];
        DataCenterStatModel::sendUdp(json_encode($arrlog));

        UdpLog::save2("passport.vronline.com/register", array("uid" => $uid, "account" => $account, "type" => $type, "baseinfo" => $baseinfo, "appid" => $appid), "", false);

        $return = ["uid" => $uid, "token" => $token, "account" => $account, "nick" => $nick, "face" => $face, "type" => $type, "lastlogin" => $lastlogin];
        if ($loginCode) {
            $return['loginCode'] = $loginCode;
        }
        return $return;
    }

    /**
     * 普通登录
     * 通过用户名拿到用户ID后，调该方法登录
     * controller里先拿到uid，原因是先判断用户名是否存在，如果不存在，需要单独提示
     * @param   int     uid   账号，或qqopenid、wxopenid、wbopenid等
     * @param   string  pwd   pwd
     * @param   string  type   登录类型，account: 普通账号、手机、邮箱; qq: qq登录; weibo: 微博登录; weixin: 微信登录;
     * @param   int     third_login_appid   用于做为第三方登录的appid，如果是0，需要生成code，否则不需要
     * @return  array  code=0表示登录成功
     */

    public function login($uid, $pwd = "", $type = "account", $appid = 0, $cip = "", $third_login_appid = 0)
    {

        if (!$uid || !is_numeric($uid)) {
            return false;
        }

        if ($type == "account" && !$pwd) {
            return array("code" => 1303);
        }
        if (!$cip) {
            $cip = Library::realIp();
        }

        $user = new UserModel();

        $info = $user->baseInfo($uid);
        if (!$info || !is_array($info)) {
            return false;
        }

        if ($type == "account" && $info['f_pwd'] != md5($pwd)) {
            return array("code" => 1303);
        }

        /**
         * 账号已被锁定，并且还没到解锁时间，不能登录
         */
        if ($info['f_status'] == 1 && time() < $info['f_endlock_time']) {
            return array("code" => 1304);
        }

        // 向record库中的login_log 表插入一条记录，uid,appid,addip
        $param['uid']   = $uid;
        $param['appid'] = $appid;
        $param['type']  = $type;
        $param['city']  = "";
        $param['ts']    = time();
        $param['ip']    = $cip;

        $loginModel = new LoginModel;
        $lastlogin  = $loginModel->setLastLogin($uid, $param); // 得到插入的主键id
        unset($param);

        $extinfo = $user->extInfo($uid);
        if ($extinfo) {
            $info = array_merge($info, $extinfo);
        }

        $token = $this->genToken($uid, "login_token");

        /**
         * 合作方通过VRonline
         * 根据appid生成openid
         * 根据appid和openid生成code
         */
        $loginCode = "";
        if ($third_login_appid) {
            $openid     = OpenidModel::getOpenid($third_login_appid, $uid);
            $loginModel = new LoginModel();
            $loginCode  = Library::genCode("str", 32);
            $loginModel->genTmpCode("third_code_" . $loginCode, $openid);
        }

        /**
         * 发送统计
         */
        $properties = [
            "did"         => $this->getProp("did"),
            "account_ref" => $type,
            "_email"      => isset($info['f_email']) ? $info['f_email'] : "",
            "_mobile"     => isset($info['f_mobile']) ? $info['f_mobile'] : "",
            //"_vip"      => isset($extinfo['f_vip']) ? $extinfo['f_vip'] : 0,
            "isall"       => 1,
        ];
        DataCenterStatModel::stat("vrplat", "login", $uid, $properties);

        /**
         * 发送登录统计
         */
        $login_type = $this->getLoginType($type);
        $arrlog     = [
            'event'      => 'user_login',
            'uid'        => $uid,
            "mac"        => $this->getProp("did"),
            "channel_id" => isset($baseinfo['f_source1']) ? strval($baseinfo['f_source1']) : "",
            "login_type" => $login_type,
            'ip'         => $cip,
        ];
        DataCenterStatModel::sendUdp(json_encode($arrlog));

        UdpLog::save2("passport.vronline.com/login", array("uid" => $uid, "type" => $type, "appid" => $appid, "cip" => $cip), null, false);

        $return = ["code" => 0, "uid" => $uid, "token" => $token, "data" => $info, "lastlogin" => $lastlogin];
        if ($loginCode) {
            $return['loginCode'] = $loginCode;
        }
        return $return;
    }

    /**
     * 判断用户名是否存在
     * @param   string  account
     * @param   string  type
     * @return  mix     存在返回uid; 不存在返回notexists; false:查询失败;
     */
    public function isExists($account, $type)
    {
        if (!$account) {
            return false;
        }
        switch ($type) {
            case "account":
                $account = strtolower(trim($account));
                break;
            case "qq":
                $account = "qq:" . $account;
                break;
            case "weibo":
                $account = "wb:" . $account;
                break;
            case "weixin":
                $account = "wx:" . $account;
                break;
            case "ali":
                $account = "ali:" . $account;
                break;
            case "bobo":
                $account = "bobo:" . $account;
                break;
            default:break;
        }

        $login = new LoginModel();

        // 首先根据用户名，求出uid,如果没有，说明用户名不存在
        $uid = $login->getUid($account);
        if ($uid === false) {
            return false;
        }
        if (!$uid) {
            return "notexists";
        }
        return $uid;
    }

    /**
     * 返回登录类型，用于统计
     */
    public function getLoginType($type)
    {
        $login_type = 0;
        switch ($type) {
            case "qq":
                $login_type = 3;
                break;
            case "weibo":
                $login_type = 4;
                break;
            case "weixin":
                $login_type = 5;
                break;
            case "ali":
                $login_type = 6;
                break;
            case "bobo":
                $login_type = 7;
                break;
            case "guest":
                $login_type = 8;
                break;
            case "account":
                $login_type = 1;
                break;
            case "mobile":
                $login_type = 2;
                break;
            case "email":
                $login_type = 3;
                break;
            case "nologin":
                $login_type = 9;
                break;
            default:break;
        }
        return $login_type;
    }

    /**
     * 修改密码
     * @param   int  uid   uid
     * @param   string  username   username
     * @param   string  pwd   pwd
     * @param   string  newPwd   newPwd
     * @return  int  > 0 所影响的行数，表示修改密码成功，0 表示修改密码失败
     */
    public function changePwd($uid, $pwd, $newPwd)
    {

        $result = array();

        // 判断用户名，密码，新密码是否非空并且是否是32位字符
        if (empty($uid) || empty($pwd) || !isset($pwd{31}) || empty($newPwd) || !isset($newPwd{31})) {

            return false;
        }

        $userModel = new UserModel();

        $tUser['f_uid'] = $uid;
        $tUser['f_pwd'] = $pwd;

        $result = $userModel->changePwd($tUser, $newPwd);
        unset($tUser);

        return $result;
    }

    // 第三方登录(授权:QQ 微信 微博)
    public function authLogin()
    {

    }

    // 第三方注册(授权:QQ 微信 微博) 存入加前缀：比如qq:
    public function authRegister()
    {

    }

    public function getCommentUserInfo($uid, $appid)
    {
        if (!$uid || !$appid) {
            return false;
        }

        $keyCode = 'vrOnline_comment';
        if ($appid != md5($keyCode)) {
            return false;
        }
        $userModel = new UserModel;
        $baseinfo  = $userModel->baseInfo($uid);
        if ($baseinfo === false) {
            return false;
        }
        if (!$baseinfo) {
            return array();
        }
        $ret['uid']     = $baseinfo['f_uid'];
        $ret['account'] = $baseinfo['f_account'];
        $ret['faceUrl'] = self::getHeadPicUrl($uid, $baseinfo['f_face_ver'], $baseinfo);
        return $ret;
    }

    /**
     * 获取用户的基本信息和扩展信息
     * @param int   uid
     * @param int   appid   appid
     * @return mixed
     */
    public function getUserInfo($uid, $appid)
    {
        if (!$uid) {
            return false;
        }
        $userModel = new UserModel;
        $baseinfo  = $userModel->baseInfo($uid);
        if ($baseinfo === false) {
            return false;
        }
        if (!$baseinfo) {
            return array();
        }

        $ret['uid'] = $baseinfo['f_uid'];
        if ($this->checkAuth($appid)) {
            $ret['account']    = $baseinfo['f_account'];
            $ret['bindmobile'] = $baseinfo['f_mobile'];
            $ret['bindemail']  = $baseinfo['f_email'];
            $ret['guest']      = $baseinfo['f_guest'];
            $ret['qqopenid']   = $baseinfo['f_qqopenid'];
            $ret['wxopenid']   = $baseinfo['f_wxopenid'];
            $ret['aliopenid']  = $baseinfo['f_aliopenid'];
            $ret['wbopenid']   = $baseinfo['f_wbopenid'];
            $ret['boboid']     = $baseinfo['f_boboid'];
        }
        $ret['nick']        = $baseinfo['f_nick'];
        $ret['status']      = $baseinfo['f_status'];
        $ret['endlocktime'] = $baseinfo['f_endlock_time'];

        $ret['faceUrl'] = self::getHeadPicUrl($uid, $baseinfo['f_face_ver'], $baseinfo);

        $ret['thirdface'] = $baseinfo['f_third_face'];
        $ret['type']      = $baseinfo['f_type'];
        $ret['source1']   = $baseinfo['f_source1'];
        $ret['source2']   = $baseinfo['f_source2'];
        $ret['ctime']     = $baseinfo['f_ctime'];
        $ret['lastTime']  = $baseinfo['f_ltime'];
        $ret['addip']     = $baseinfo['f_addip'];

        $extinfo = $userModel->extInfo($uid);

        if ($this->checkAuth($appid)) {
            // 账户余额
            $ret['money'] = isset($extinfo['f_money']) ? $extinfo['f_money'] : 0;
            // 充值总额
            $ret['consume'] = isset($extinfo['f_consume']) ? $extinfo['f_consume'] : 0;
        }

        $ret['vip']         = isset($extinfo['f_vip']) ? $extinfo['f_vip'] : 0;
        $ret['extctime']    = isset($extinfo['f_ctime']) ? $extinfo['f_ctime'] : 0;
        $ret['extlasttime'] = isset($extinfo['f_ltime']) ? $extinfo['f_ltime'] : 0;

        return $ret;
    }

    public function sendSms($uid, $mobile, $action, $params = [])
    {
        $ret = array();
        if (empty($mobile) || empty($action) || empty($uid)) {
            return false;
        }
        //判断请求来源
        //        if($msgType === 'bindMobile') {
        //            $sendCodeKey = 'bindMobileCode_' . $mobile;
        //            $sendTimesKey = 'bindMobileCode_num' . $mobile;
        //            $action = "bind_mobile";
        //        } else if($msgType === 'findPwd') {
        //            $sendCodeKey = 'findPwdCode_' . $mobile;
        //            $sendTimesKey = 'findPwdCode_num' . $mobile;
        //            $action = "find_pwd_mobile";
        //        } else {
        //            return false;
        //        }
        $verifyModel = new VerifyCodeModel;
        if ($action == "find_pwd_mobile") {
            $code = $verifyModel->setVerifyCode($uid, $mobile, "mobile", $action);
        } else {
            if ($action == "extract_cash_msg") {
                $code = $verifyModel->setVerifyCode($uid, $mobile, "mobile", $action . $params['card_id']);
            } else {
                $code = $verifyModel->setVerifyCode($uid, $mobile, "mobile", $action);
            }

        }
        if (!$code) {
            return 2022;
        }
        if ($code == "error:expireusermax") {
            return 2020;
        }
        if ($code == "error:expirenummax") {
            return 2021;
        }

        switch ($action) {
            case 'set_cash_pwd':
                $contents = "，您的账号正在修改取款密码";
                break;

            case 'extract_cash_msg':
                $contents = str_replace(['{{ cash }}', '{{ card }}'], [$params['cash'], $params['card']], "，您的账号正在提取余额{{ cash }}元RMB到您尾号为{{ card }}的银行卡");
                break;
            case 'login':
                $contents = "，欢迎登录VR助手。";
                break;
            default:
                $contents = "，欢迎注册体验。";
                break;
        }
        $contents = '您的验证码是：' . $code . $contents;
        $result   = self::sendMsgApi($mobile, $contents);
        if ($result === '') {
            $code = 2010;
            return $code;
        }
        return 0;
    }

    /**
     * 短信发送接口类
     * @param $mobile
     * @param $contents
     */
    public function sendMsgApi($mobile, $contents)
    {

        $phones = $mobile;
        $msg    = $contents;

        $type    = 0; //接口里的发送短信的action标识
        $port    = '*'; //扩展子号标识
        $self    = 0;
        $flownum = 0; //流水号
        $method  = 1; //请求方式，0:soap 1:post 2:get

        $params = array(
            'type'    => $type,
            'method'  => $method,
            'port'    => $port,
            'flownum' => $flownum,
            'msg'     => $msg,
            'self'    => $self,
            'phones'  => $phones,
        );

        return \sms\SmsApi::send($params);

    }

    /**
     * 上传图片
     * @param $uid
     */
    public function uploadApi($uid)
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        Library::accessHeader();
        header('Access-Control-Allow-Methods:POST,GET');
        header('Access-Control-Allow-Credentials:true');
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        if (!empty($_REQUEST['debug'])) {
            $random = rand(0, intval($_REQUEST['debug']));
            if ($random === 0) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }
        // header("HTTP/1.0 500 Internal Server Error");
        // exit;
        // 5 minutes execution time
        @set_time_limit(10 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);

        // Settings

        $targetDir = 'resources' . DIRECTORY_SEPARATOR . 'userPic' . DIRECTORY_SEPARATOR . 'file_material_tmp';
        $uploadDir = 'resources' . DIRECTORY_SEPARATOR . 'userPic' . DIRECTORY_SEPARATOR . $uid;

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge       = 5 * 3600; // Temp file age in seconds
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
        // Create target dir
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $oldName  = $fileName;
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        // $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        // Chunking might be enabled
        $chunk  = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

        // Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done  = true;
        for ($index = 0; $index < $chunks; $index++) {
            if (!file_exists("{$filePath}_{$index}.part")) {
                $done = false;
                break;
            }
        }

        if ($done) {
            $pathInfo = pathinfo($fileName);
            //$hashStr = substr(md5($pathInfo['basename']),8,16);
            $hashStr = substr(md5($uid), 0, 12);
            //$hashName = $hashStr . '.' .$pathInfo['extension'];

            $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $hashStr;
            //$picType = isset($_POST['picname']) ? $_POST['picname'] : '';

            if (!$out = @fopen($uploadPath, "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }
            if (flock($out, LOCK_EX)) {
                for ($index = 0; $index < $chunks; $index++) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            @fclose($out);
            $response = [
                'success'      => true,
                'oldName'      => $oldName,
                'filePaht'     => $uploadPath,
                'fileSize'     => $_FILES["file"]['size'],
                'fileSuffixes' => $pathInfo['extension'],
            ];

            //die(response()->json($response));
            return json_encode($response);die;
        }

        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    /**
     * 更新用户信息自定义呢头像字段
     * @param $uid
     * @param $timeStamp
     * @return array|bool
     */
    public function setFace($uid, $timeStamp)
    {
        $ret = array();
        if (empty($uid) || empty($timeStamp)) {
            $ret['code'] = 1102; // 非法请求--参数缺失
            return $ret;
        }

        $userModel       = new UserModel();
        $user['uid']     = $uid;
        $user['setFact'] = $timeStamp;
        $ret             = $userModel->setFace($user);
        unset($user);
        return $ret;
    }

    /**
     * 获取用户的头像地址
     * @param $uid
     * @return string
     */
    public function getHeadPicUrl($uid, $ver, $baseinfo = array())
    {

        if ($ver < 1000000) {
            // 根据uid 查询出f_type类型，如果是3，4，5，则显示第三方登录本身图象
            if (!$baseinfo) {
                $userModel = new UserModel;
                $baseinfo  = $userModel->baseInfo($uid);
            }

            if (in_array($baseinfo['f_type'], array(3, 4, 5))) {
                return $baseinfo['f_third_face'];
            } else {
                return "//pic.vronline.com/vrhelp/images/default.png";
            }
        }

        $headPicUrl = "//image.vronline.com/userimg/dev/" . $uid . "/120.png?{$ver}";
        return $headPicUrl;
    }

    /**
     * 检查登录状态
     * 如果是登录状态，延长过期时间
     * @param   int     uid
     * @param   string  token   token
     * @param   bool    reset   是否重置缓存时间
     * @return  bool    成功返回true，未登录返回false;
     */
    public function isLogin($uid, $token, $reset = false)
    {
        if (!$uid || !$token) {
            return false;
        }

        $login = new LoginModel;
        $check = $login->getToken($uid, "login_token");
        if (!$check || $check != $token) {
            return false;
        }
        // 如果相等，重置缓存
        if ($reset) {
            $this->genToken($uid, "login_token", $check);
        }
        return true;
    }

    /**
     * 检查登录状态，增强版，检查不同类型的token
     * 如果是登录状态，延长过期时间
     * @param   int     uid
     * @param   string  token   token
     * @param   bool    reset   是否重置缓存时间
     * @return  bool    成功返回true，未登录返回false;
     */
    public function isLoginTurbo($uid, $type, $token, $reset = false)
    {
        if (!$uid || !$type || !$token) {
            return false;
        }

        $login = new LoginModel;
        $check = $login->getToken($uid, $type);
        if (!$check || $check != $token) {
            return false;
        }
        // 如果相等，重置缓存
        if ($reset) {
            $this->genToken($uid, $type, $check);
        }
        return true;
    }

    /**
     * 生成token
     * @param   int     uid     uid
     * @return  string  token   token
     * @return  bool    new     是否强制更新token
     */
    public function genToken($uid, $type, $token = "", $new = false)
    {
        if (!$uid) {
            return false;
        }

        $login = new LoginModel();
        if (!$new) {
            if (!$token) {
                $token = $login->getToken($uid, $type);
            }
        }
        if (!$token || $new) {
            /**
             * 通过id、随机数、时间戳哈希成token
             */
            $rand  = $this->genKey(32);
            $stamp = time();
            $token = md5($uid . "_" . $rand . "_" . $stamp);
            //$info = array('u' => $uid, 'r' => $rand, 't' => $stamp);
        }

        $login->genToken($uid, $type, $token);

        return $token;
    }

    /**
     * 游客修改账号、绑定账号
     * 账号格式必须是游客格式，必须没有密码
     * 登录状态，和判断用户名是否存在，放在controller中，便于返回对应的错误信息给前端
     * @param   int         uid
     * @param   string      newaccount
     * @param   string      newpwd
     * @return  bool        true or false
     */
    public function bindAccount($uid, $newaccount, $newpwd)
    {
        if (!$uid || !$newaccount || !$newpwd || strlen($newpwd) != 32) {
            return false;
        }

        $password = md5($newpwd);
        $user     = new UserModel;

        /**
         * 生成这个游客的用户ID
         */
        $base = $user->baseInfo($uid);

        if (!$base || !is_array($base)) {
            return false;
        }

        /**
         * 已经有账号，不能修改
         * 有密码了也不能修改账号
         */
        if ($base['f_account'] || $base['f_pwd']) {
            return false;
        }

        /**
         * 插入到登录表中
         * 如果重复，无法插入
         */
        $tLogin            = array();
        $tLogin['f_login'] = $newaccount;
        $tLogin['f_uid']   = $uid;
        $login             = new LoginModel;
        $lret              = $login->tLoginInsert($tLogin);
        if (!$lret) {
            return false;
        }
        /**
         * 插入到用户信息表中
         */
        $tUser['f_account'] = $newaccount;
        $tUser['f_pwd']     = $password;
        $uret               = $user->updateBaseinfo($uid, $tUser);
        if (!$uret) {
            return false;
        }
        return true;
    }

    /**
     * 绑定第三方账号
     * 主要用来绑定大朋账号
     * @param   int         uid
     * @param   string      newaccount
     * @param   string      newpwd
     * @return  bool        true or false
     */
    public function bindThirdAccount($uid, $newaccount, $type)
    {
        if (!$uid || !$newaccount || !$type) {
            return false;
        }

        $login   = new LoginModel;
        $existid = $this->isExists($newaccount, $type);
        if (!$existid || $existid !== "notexists") {
            return false;
        }

        /**
         * 生成这个游客的用户ID
         */
        $userModel = new UserModel;
        $base      = $userModel->baseInfo($uid);

        if (!$base || !is_array($base)) {
            return false;
        }

        $accinfo = $this->getAccountByType($newaccount, $type);
        if (!is_array($accinfo)) {
            return false;
        }
        $field   = $accinfo['field'];
        $account = $accinfo['account'];

        $tLogin            = array();
        $tLogin['f_login'] = $account;
        $tLogin['f_uid']   = $uid;
        /**
         * 已经绑定了
         */
        if (isset($base[$field]) && $base[$field]) {
            if ($base[$field] == $account) {
                // 逻辑走到这里，应该是在登录表里没有数据，插入一条
                $reg = $login->tLoginInsert($tLogin);
                return true;
            } else {
                return false;
            }
        }

        /**
         * 插入到登录表中
         * 如果重复，无法插入
         */
        $lret = $login->tLoginInsert($tLogin);
        if (!$lret) {
            return false;
        }
        /**
         * 写入到用户信息表中
         */
        $tUser[$field] = $account;
        $uret          = $userModel->updateBaseinfo($uid, $tUser);
        if (!$uret) {
            return false;
        }
        return true;
    }

    /**
     * 返回真实的帐号名
     */
    public function getAccountByType($account, $type)
    {
        if (!$account || !$type) {
            return false;
        }

        switch ($type) {
            case "qq":
                $field   = "f_qqopenid";
                $account = "qq:" . $account;
                break;
            case "weixin":
                $field   = "f_wxopenid";
                $account = "wx:" . $account;
                break;
            case "weibo":
                $field   = "f_wbopenid";
                $account = "wb:" . $account;
                break;
            case "ali":
                $field   = "f_aliopenid";
                $account = "ali:" . $account;
                break;
            case "bobo":
                $field   = "f_boboid";
                $account = "bobo:" . $account;
                break;
            default:return false;
                break;
        }
        return ["account" => $account, "field" => $field];
    }

    /**
     * 修改基本信息表字段 db_user_0库 t_user_info_0表
     * @param   int        uid
     * @param   array      data
     * @return  bool       true or false
     */
    public function modifyAccount($uid, $data)
    {
        if (!$uid || !$data || !is_array($data) || count($data) < 1) {
            return false;
        }

        // 判断$data里面是否有f_pwd,f_account,f_mobile,f_email,f_guest,f_qqopenid,f_wxopenid,f_aliopenid,f_wbopenid
        $passArr = array('f_pwd', 'f_account', 'f_mobile', 'f_email', 'f_guest', 'f_qqopenid', 'f_wxopenid', 'f_aliopenid', 'f_wbopenid');
        $keys    = array_keys($data);
        // 判断敏感词汇是否和data有交集
        $inter = array_intersect($passArr, $keys);

        if (count($inter) > 0) {
            // 表示包含敏感词汇
            return false;
        }

        $user = new UserModel;

        $res = $user->updateBaseinfo($uid, $data);
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * 检测appid是否有权限
     */
    public function checkAuth($appid)
    {
        $hight_appid = Config::get("common.hight_weight_appid");
        if (!is_array($hight_appid)) {
            $hight_appid = array();
        }
        if (in_array($appid, $hight_appid)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 生成token
     */
    protected function genKey($len)
    {
        $len = intval($len);
        if ($len <= 0) {
            $len = 16;
        }
        $list = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!', '(', ')', '=', '-', '_', '+', '|', ':', '#', '%', '*', '^');
        $num  = count($list);
        $str  = "";
        for ($i = 0; $i < $len; $i++) {
            $r    = mt_rand(0, $num - 1);
            $char = $list[$r];
            $str .= $char;
        }
        return $str;
    }

    /**
     * 获取上传图片的大小
     * @param $size
     * @return string
     */
    public function sizeFormat($size)
    {
        $sizeStr = '';
        if ($size < 1024) {
            return $size . "bytes";
        } else if ($size < (1024 * 1024)) {
            $size = round($size / 1024, 1);
            return $size . "KB";
        } else if ($size < (1024 * 1024 * 1024)) {
            $size = round($size / (1024 * 1024), 1);
            return $size . "MB";
        } else {
            $size = round($size / (1024 * 1024 * 1024), 1);
            return $size . "GB";
        }
    }
}
