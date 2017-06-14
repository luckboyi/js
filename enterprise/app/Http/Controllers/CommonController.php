<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/19
 * Time: 9:41
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use \App\Models\CookieModel;

class CommonController extends Controller
{
    /**
     * 设置cookie的页面
     * @param Request $request
     * @param $uid
     * @param $token
     * @return $this|bool
     */
    public function setLoginCookie(Request $request, $uid, $token)
    {
        $cookieModel = new CookieModel();
        if($uid == '' || $token == '') {
            return false;
        }
        $ret = $cookieModel->setLoginCookie($uid, $token);
        return $ret;
    }

    /**
     * @param Request $request
     * @param $k
     * @param $v
     * @param $min 时间（分钟）
     * @return bool|Response
     */
    public function setCookie(Request $request, $k, $v, $min)
    {
        $cookieModel = new CookieModel();
        if($k == '' || $v == '') {
            return false;
        }
        $ret = $cookieModel->setCookie($k, $v, $min);
        return $ret;
    }

    /**
     * 获取key的cookie值
     * @param Request $request
     * @param $key
     * @return array|bool|string
     */
    public function getCookie(Request $request, $key)
    {
        if($key == '') {
            return false;
        }

        $cookies = $request->cookie($key);
        return $cookies;
    }

    /**
     * 清除cookie
     * @param Request $request
     * @param $uid
     * @return $this|bool
     */
    public function delCookie(Request $request, $k)
    {
        $response = new Response();
        $cookieModel = new CookieModel();

        $ret = $cookieModel->delCookie($k);

        return $ret;
    }

    /**
     * 清除登录用户cookie
     * @param Request $request
     * @param $uid
     * @return $this|bool
     */
    public function delLoginCookie(Request $request)
    {
        $cookieModel = new CookieModel();
        $k = '';
        $ret = $cookieModel->delCookie($k);
        return $ret;
    }
}