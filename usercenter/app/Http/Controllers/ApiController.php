<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;

// 引用Model
use Config;
use Helper\Library;
use Illuminate\Http\Request;
use App\Models\AppinfoModel;
use App\Models\LoginModel;
use App\Models\OpenidModel;
use App\Models\PassportModel;

class ApiController extends Controller
{
    /**
     * access_token 的过期时间
     */
    private $access_token_expire = 3600*12;
    /**
     * 检测登录
     */
    public function isLogin(Request $request)
    {
        $openid = trim($request->input("openid"));
        $appid  = intval($request->input("appid"));
        $token  = trim($request->input("vrkey"));
        $ts     = intval($request->input("ts"));
        $sign   = trim($request->input("sign"));

        if (!$openid || !$appid || !$ts || !$token || !$sign) {
            return Library::output(2001);
        }
        $nowstamp = time();
        if ($nowstamp - $ts > 60 * 5 || $ts - $nowstamp > 60 * 5) {
            //return Library::output(2023);
        }

        $appModel = new AppinfoModel;
        $app      = $appModel->info($appid);
        if (!$app || !is_array($app) || !isset($app['appkey']) || !$app['appkey']) {
            return Library::output(1);
        }
        $check = Library::encrypt($_POST, $app['appkey']);
        if ($check !== $sign) {
            return Library::output(2002);
        }

        $info = OpenidModel::getUid($openid);
        if (!$info || !is_array($info) || !isset($info['uid']) || !$info['uid'] || !isset($info['appid']) || $info['appid'] != $appid) {
            return Library::output(1301);
        }
        $uid = $info['uid'];

        $passport = new PassportModel;
        $ret      = $passport->isLogin($uid, $token, true);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1301);
        }
    }

    /**
     * 获取用户信息
     */
    public function user(Request $request)
    {
        $openid = trim($request->input("openid"));
        $appid  = intval($request->input("appid"));
        $token  = trim($request->input("vrkey"));
        $ts     = intval($request->input("ts"));
        $sign   = trim($request->input("sign"));

        if (!$openid || !$appid || !$ts || !$token || !$sign) {
            return Library::output(2001);
        }
        $nowstamp = time();
        if ($nowstamp - $ts > 60 * 5 || $ts - $nowstamp > 60 * 5) {
            //return Library::output(2023);
        }

        $info = OpenidModel::getUid($openid);
        if (!$info || !is_array($info) || !isset($info['uid']) || !$info['uid'] || !isset($info['appid']) || $info['appid'] != $appid) {
            return Library::output(2019);
        }
        $uid = $info['uid'];

        $passport = new PassportModel();
        $login    = $passport->isLogin($uid, $token);
        if (!$login) {
            return Library::output(1301);
        }

        $appModel = new AppinfoModel;
        $app      = $appModel->info($appid);
        if (!$app || !is_array($app) || !isset($app['appkey']) || !$app['appkey']) {
            return Library::output(1);
        }
        $check = Library::encrypt($_POST, $app['appkey']);
        if ($check !== $sign) {
            return Library::output(2002);
        }

        $userInfo = $passport->getUserInfo($uid, $appid);
        if (!$userInfo || !is_array($userInfo)) {
            return Library::output(1);
        }
        $info         = array();
        $info['nick'] = isset($userInfo['nick']) ? $userInfo['nick'] : "";
        $info['face'] = isset($userInfo['thirdface']) && $userInfo['thirdface'] ? $userInfo['thirdface'] : $userInfo['faceUrl'];

        return Library::output(0, $info);
    }

    /**
     * 获取用户openid
     */
    public function getOpenid(Request $request)
    {
        $uid   = intval($request->input("uid"));
        $appid = intval($request->input("appid"));
        $token = trim($request->input("vrkey"));
        $ts    = intval($request->input("ts"));
        $sign  = trim($request->input("sign"));

        if (!$uid || !$appid || !$ts || !$token || !$sign) {
            return Library::output(2001);
        }
        $nowstamp = time();
        if ($nowstamp - $ts > 60 * 5 || $ts - $nowstamp > 60 * 5) {
            //return Library::output(2023);
        }

        $passport = new PassportModel();
        $login    = $passport->isLogin($uid, $token);
        if (!$login) {
            return Library::output(1301);
        }

        $appModel = new AppinfoModel;
        $app      = $appModel->info($appid);
        if (!$app || !is_array($app) || !isset($app['appkey']) || !$app['appkey']) {
            return Library::output(1);
        }
        $check = Library::encrypt($_POST, $app['appkey']);
        if ($check !== $sign) {
            return Library::output(2002);
        }

        $openid = OpenidModel::getOpenid($appid, $uid);
        if (!$openid) {
            return Library::output(1);
        }

        return Library::output(0, array("openid" => $openid));
    }

    /**
     * 根据code获取用户信息以及accesstoken
     */
    public function getAccessToken(Request $request)
    {
        $appid  = intval($request->input("appid"));
        $code   = trim($request->input("code"));
        $appkey = trim($request->input("appkey"));
        $type   = trim($request->input("type"));

        if (!$appid || !$code || !$appkey || !$type) {
            return Library::output(2001);
        }

        /**
         * 根据code，拿到openid
         */
        $loginModel = new LoginModel();
        $openid     = $loginModel->getTmpCode("third_code_".$code);
        if(!$openid) {
            return Library::output(1309);
        }

        /**
         * 通过openid得到uid，并校验appid
         */
        $info = OpenidModel::getUid($openid);
        if (!$info || !is_array($info) || !isset($info['uid']) || !$info['uid'] || !isset($info['appid']) || $info['appid'] != $appid) {
            return Library::output(1309);
        }
        $uid = $info['uid'];

        /**
         * 查询该app有没有权限登录
         */
        $appModel = new AppinfoModel;
        $app      = $appModel->info($appid);
        if (!$app || !is_array($app) || !isset($app['appkey']) || !$app['appkey'] || $appkey != $app['appkey']) {
            return Library::output(1310);
        }
        if($app['status'] != 6) {
            return Library::output(1311);
        }

        $access_token = Library::genCode("normal", 32);
        $loginModel->genToken("access_token_" . $openid, "login_token", $access_token, $this->access_token_expire);

        $passport = new PassportModel();
        $userInfo = $passport->getUserInfo($uid, $appid);
        if (!$userInfo || !is_array($userInfo)) {
            return Library::output(1);
        }
        $info         = array();
        $info['openid'] = $openid;
        $info['access_token'] = $access_token;
        $info['nick'] = isset($userInfo['nick']) ? $userInfo['nick'] : "";
        $info['face'] = isset($userInfo['thirdface']) && $userInfo['thirdface'] ? $userInfo['thirdface'] : $userInfo['faceUrl'];

        return Library::output(0, $info);
    }

    /**
     * 根据access_token获取用户信息
     */
    public function getUserInfoByAccessToken(Request $request)
    {
        $appid  = intval($request->input("appid"));
        $openid = trim($request->input("openid"));
        $access_token = trim($request->input("access_token"));

        if (!$appid || !$openid || !$access_token) {
            return Library::output(2001);
        }

        /**
         * 根据code，拿到openid
         */
        $loginModel = new LoginModel();
        $check = $loginModel->getToken("access_token_" . $openid, "login_token");
        if(!$check || $access_token != $check) {
            return Library::output(1301, null, "access_token错误");
        }

        /**
         * 通过openid得到uid，并校验appid
         */
        $info = OpenidModel::getUid($openid);
        if (!$info || !is_array($info) || !isset($info['uid']) || !$info['uid'] || !isset($info['appid']) || $info['appid'] != $appid) {
            return Library::output(2019);
        }
        $uid = $info['uid'];

        /**
         * 查询该app有没有权限登录
         */
        $appModel = new AppinfoModel;
        $app      = $appModel->info($appid);
        if (!$app || !is_array($app)) {
            return Library::output(1310);
        }
        if($app['status'] != 6) {
            return Library::output(1311);
        }

        $passport = new PassportModel();
        $userInfo = $passport->getUserInfo($uid, $appid);
        if (!$userInfo || !is_array($userInfo)) {
            return Library::output(1);
        }
        $info         = array();
        $info['openid'] = $openid;
        $info['access_token'] = $access_token;
        $info['nick'] = isset($userInfo['nick']) ? $userInfo['nick'] : "";
        $info['face'] = isset($userInfo['thirdface']) && $userInfo['thirdface'] ? $userInfo['thirdface'] : $userInfo['faceUrl'];

        return Library::output(0, $info);
    }

}
