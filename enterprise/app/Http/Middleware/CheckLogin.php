<?php

namespace App\Http\Middleware;

use App\Http\Traits\SimpleResponse;
use Closure;
use \App\Models\UserModel as User;

class CheckLogin
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

        $check               = true;
        $defaultRedirectType = "jump";
        $defaultPermType     = "common";
        $redirectType        = $defaultRedirectType;
        $permType            = $defaultPermType;
        if ($params) {
            $params = explode(":", $params);

            $redirectType = isset($params[0]) && $params[0] ? $params[0] : $defaultRedirectType;

            $permType = isset($params[1]) && $params[1] ? $params[1] : $defaultPermType;
        }

        $referer = $request->path();
        $host    = $request->getHttpHost();

        $redirectUrl  = '/login?referer=' . $referer;
        $redirectCode = 1301;

        switch ($host) {
            case 'www.vronline.com':
                $checkLoginType = "www";
                break;
            case 'open.vronline.com':
                $checkLoginType = "open";
                break;
            default:
                $checkLoginType = "www";
                break;
        }

        $ret = User::checkLogin($checkLoginType);
        if (!$ret) {
            $check = false;
            if ($permType == "openlogin" || $permType == "login") {
                $check = true;
            }
        } else {
            switch ($permType) {
                case 'admin':
                    if ($ret['perm'] != "all") {
                        $check        = false;
                        $redirectUrl  = 'open/needPerm';
                        $redirectCode = 1301;
                    }
                    break;
                case 'developer':
                    if ($ret['stat'] != 5) {
                        $check        = false;
                        $redirectUrl  = 'applyHome';
                        $redirectCode = 1301;
                    }
                    break;
                case 'login':
                    $check       = false;
                    $redirectUrl = '/';
                    if ($requestReferer = $request->input("referer")) {
                        $redirectUrl = url($requestReferer);
                    }
                    break;
                case 'openlogin':
                    $check       = false;
                    $redirectUrl = 'product/webgamelist/online';
                    break;
                default:
                    break;
            }

            $request->userinfo = $ret;
        }

        if (!$check) {
            if ($redirectType == "jump") {
                return redirect($redirectUrl);
            } else {
                return $this->outputJsonWithCode($redirectCode);
            }
        }

        return $next($request);
    }
}
