<?php

namespace App\Http\Middleware;

use App\Http\Traits\SimpleResponse;
use Closure;
use Config;
use Cookie;
use Helper\AccountCenter;
use Session;
use \App\Models\DevModel;
use \App\Models\UserModel;

class VrAuth
{

    use SimpleResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  params 使用:分割参数 第一位:处理方式，jump/json 第二位:权限类型
     * @return mixed
     */
    public function handle($request, Closure $next, $params = null)
    {
        $redirect            = false;
        $defaultRedirectType = "jump";
        $defaultPermType     = "common";
        $redirectType        = $defaultRedirectType;
        $permType            = $defaultPermType;
        if ($params) {
            $params       = explode(":", $params);
            $redirectType = isset($params[0]) && $params[0] ? $params[0] : $defaultRedirectType;
            $permType     = isset($params[1]) && $params[1] ? $params[1] : $defaultPermType;
        }

        if (isset($params[2]) && $params[2] == "https") {
            if ($request->server('HTTP_PROTOCOL') != "https") {
                return redirect('https://' . $request->server('HTTP_HOST') . $request->server('REQUEST_URI'), 302, [], true);
            }
        }
        if (strstr($permType, "admincp")) {
            return $this->loadAdminAuth($request, $next, $permType, $redirectType);
        }

        if (strstr($permType, "api")) {
            return $this->loadApiAuth($request, $next, $permType, $redirectType);
        }

        $referer = $request->path();
        $domain  = $request->getHttpHost();
        $isLogin = true;
        $uid     = Cookie::get("uid");
        $token   = Cookie::get("token");
        $account = Cookie::get("account");
        $nick    = Cookie::get("nick");
        $face    = Cookie::get("face");
        $face    = $face ? $face : "";
        $auth    = array();

        if (!$uid || !$token || !$nick) {
            $isLogin = false;
        } else {
            $auth = ['uid' => $uid, 'token' => $token, 'account' => $account, 'nick' => $nick, 'face' => $face];
            switch ($domain) {
                case 'open.vronline.com':
                    $addAuth = $this->loadOpenAuth($uid, $account);
                    break;
                case 'admin.vronline.com':
                    $addAuth = $this->loadDefaultAuth($uid);
                    break;
                default:
                    $addAuth = $this->loadDefaultAuth($uid);
                    break;
            }
            $auth = array_merge($auth, $addAuth);
        }
        $redirectUrl  = '/login?referer=' . $referer;
        $redirectCode = 1301;

        $expectLogin = ["login", "weblogin", "register", "webindex", "openlogin", 'clientlogin', 'webgame'];
        if (!$isLogin) {
            if (in_array($permType, $expectLogin)) {
                $redirect = false;
            } else {
                if ($permType == 'clientindex') {
                    $redirectUrl = 'clientlogin';
                }
                $redirect = true;
            }
        } else {
            switch ($permType) {
                case 'dev_admin':
                    if (!in_array(1, $auth['perm'])) {
                        $redirect     = true;
                        $redirectUrl  = 'open/needPerm';
                        $redirectCode = 1308;
                    }
                    break;
                case 'dev':
                    if ($auth['stat'] != 5) {
                        $redirect     = true;
                        $redirectUrl  = '/developer/sign';
                        $redirectCode = 1308;
                    }
                    break;
                case 'dev_master':
                    if ($auth['stat'] != 5) {
                        $redirect     = true;
                        $redirectUrl  = '/developer/sign';
                        $redirectCode = 1308;
                    }
                    if (isset($auth['parentid'])) {
                        $redirect     = true;
                        $redirectUrl  = 'open/needPerm';
                        $redirectCode = 1308;
                    }
                    break;
                // case 'login':
                //     $redirect = true;
                //     $redirectUrl = '/';
                //     if ($requestReferer = $request->input("referer")) {
                //         $redirectUrl = url($requestReferer);
                //     }
                //     break;
                case 'register':
                    $redirect    = true;
                    $redirectUrl = '/';
                    break;
                case 'openlogin':
                    $redirect = false;
                    break;
                case 'weblogin':
                    $refererUrl   = $request->input("referer", "");
                    $redirect_uri = $request->input("redirect_uri", "");
                    $code         = $request->input("code", "");
                    $noredirect   = $request->input("noredirect", "");
                    $redirect     = true;
                    if ($noredirect) {
                        $redirect = false;
                    }
                    if ($refererUrl) {
                        $redirectUrl = $refererUrl;
                    } else if ($redirect_uri) {
                        // 支持VRonline做为第三方登录

                        /**
                         * VRonline做为第三方登录，传指定参数，则不需要判断登录状态自动跳转
                         */
                        if ($code && isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == "partner.vronline.com") {
                            $arr = parse_url($redirect_uri);
                            if (isset($arr['query']) && $arr['query']) {
                                $redirectUrl = $redirect_uri . "&code=" . $code;
                            } else {
                                $redirectUrl = $redirect_uri . "?code=" . $code;
                            }
                        } else {
                            $redirectUrl = $redirect_uri;
                        }
                    } else {
                        $redirectUrl = '/';
                    }
                    break;
                case 'clientlogin':
                    $redirect    = true;
                    $redirectUrl = '/web';
                    break;
                case 'toblogin':
                    $redirect    = true;
                    $redirectUrl = '/enter';
                    break;
                default:
                    break;
            }
        }

        if ($redirect) {
            if ($redirectType == "jump") {
                return redirect($redirectUrl, 302, [], false);
            } else {
                return $this->outputJsonWithCode($redirectCode);
            }
        } else {
            $request->userinfo = $auth;
        }
        return $next($request);
    }

    private function loadAdminAuth($request, Closure $next, $permType, $redirectType)
    {
        $uid     = Cookie::get("admin_uid");
        $name    = Cookie::get("admin_name");
        $account = Cookie::get("admin_account");

        $isLogin = true;
        if (!$uid || !$name || !$account) {
            $isLogin = false;
        }

        $expectLogin  = ["admincp_login"];
        $redirectUrl  = "/login";
        $redirectCode = 1301;

        if (!$isLogin) {
            if (in_array($permType, $expectLogin)) {
                $redirect = false;
            } else {
                $redirect = true;
            }
        } else {
            if ($permType == "admincp_login") {
                $redirect    = true;
                $redirectUrl = '/';
            } else {
                $redirect = false;
            }
        }

        $wwwUid = Cookie::get("uid");
        if (!$wwwUid) {
            $wwwUid = '';
        }
        $auth = ['uid' => $uid, 'name' => $name, 'account' => $account, 'wwwUid' => $wwwUid];

        $permRedirect = false;
        if ($uid) {
            $UserModel = new UserModel;
            $permInfo  = $UserModel->getAdmincpPerms($uid);
            if (isset($permInfo['perms'])) {
                $perms       = $permInfo['perms'];
                $permId      = 0;
                $permCfgPath = Config::get("admincp.perm");
                $permPath    = $request->path();
                if (isset($permCfgPath[$permPath])) {
                    $permId = $permCfgPath[$permPath]['id'];
                }

                if ($permId) {
                    if (!in_array($permId, $perms)) {
                        $permRedirect = true;
                    }
                }

                foreach ($perms as $nextPerm) {
                    if ($nextPerm > $permId) {
                        break;
                    }
                }
                $nextPath = '';
                if ($nextPerm) {

                    $permPaths = [];
                    foreach ($permCfgPath as $key => $value) {
                        if (!isset($permPaths[$value['id']])) {
                            $value['path']           = $key;
                            $permPaths[$value['id']] = $value;
                        }
                    }

                    if (isset($permPaths[$nextPerm])) {
                        $nextPath = $permPaths[$nextPerm]['path'];
                    }
                }
                $auth['perms']    = $perms;
                $auth['index']    = $permInfo['group_path'];
                $auth['permId']   = $permId;
                $auth['nextPath'] = $nextPath;
            } else {
                $permRedirect = true;
            }
        }

        if ($permRedirect) {
            if ($redirectType == "jump") {
                return redirect('/index/help', 302, [], true);
            } else {
                return $this->outputJsonWithCode(1308);
            }
        }
        if ($redirect) {
            if ($redirectType == "jump") {
                return redirect($redirectUrl, 302, [], true);
            } else {
                return $this->outputJsonWithCode($redirectCode);
            }
        } else {
            $request->userinfo = $auth;
        }

        return $next($request);
    }

    private function loadOpenAuth($uid, $account)
    {
        $sessionUid = Session::get('uid');
        $stat       = Session::get('stat');
        $perm       = Session::get('perm');

        $devModel = new DevModel;
        $devUser  = $devModel->getUser($uid);
        $stat     = 0;
        $perm     = [];
        if ($devUser && isset($devUser['stat'])) {
            $stat = $devUser['stat'];
        }

        $user = new UserModel();
        $res  = $user->openLogin($uid, $account);
        $perm = $res['perm'] ? $res['perm'] : array();

        Session::set('uid', $uid);
        Session::set('stat', $devUser['stat']);
        Session::set('perm', $perm);

        $outAuth = ['stat' => $stat, 'perm' => $perm];
        if ($devUser['parentid']) {
            $outAuth['parentid'] = $devUser['parentid'];
            $tmpPerm             = json_decode($devUser['perms'], true);
            if (!$tmpPerm) {
                $outAuth['gameperms'] = [];
            } else {
                $outAuth['gameperms'] = $tmpPerm;
            }

        }
        return $outAuth;
    }

    private function loadDefaultAuth($uid)
    {
        Session::set('uid', $uid);
        $outAuth = ["uid" => $uid];
        return $outAuth;
    }

    private function loadApiAuth($request, Closure $next, $permType, $redirectType)
    {
        $uid          = $request->input('uid');
        $token        = $request->input('token');
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $ret          = $accountModel->checkLogin($uid, $token);
        if (!$ret || !isset($ret['code']) || $ret['code'] != 0) {
            return $this->outputJsonWithCode(1301);
        } else {
            $request->userinfo = ['uid' => $uid, 'token' => $token];
            return $next($request);
        }
    }

}
