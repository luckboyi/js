<?php

namespace App\Http\Controllers;

use Config;
use Helper\Library;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\AppinfoModel;

class AppinfoController extends Controller
{
    /**
     * 获取app信息 
     * @param   int  appid
     * @return  array
    */
    public function index($appid)
    {
        if(!$appid) {
            $code = 2001;
            $msg = Config::get("errorcode.{$code}");
            return json_encode(array("code" => $code, "msg" => $msg));
        }
        $app = new AppinfoModel;
        $info = $app->info($appid);
        if(!is_array($info)) {
            return Library::output(2016);
        }
        return Library::output(0, $info);
    }

    /**
     * 设置app信息 
    */
    public function setApp(Request $request, $appid)
    {
        $appid = intval($appid);
        $json = $request->input('json', json_encode(array("payurl"=>"http://passport.vronline.com/delivery", "payurltest"=>"http://passport.vronline.com/delivery")));
        $appinfo = json_decode($json, true);
        if(!$appid || !$appinfo || !is_array($appinfo)) {
            return Library::output(2001);
        }

        $app = new AppinfoModel;
        $appkey = Library::genKey(32);
        $paykey = Library::genKey(32);
        $ret = $app->set($appid, $appinfo, $appkey, $paykey);
        if(!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /**
     * 查一个服的信息 
     * @param   int  appid
     * @param   int  serverid
     * @return  int -2 表示用户名为空 >=1表示用户的uid NULL 表示并未此用户
    */
    public function getOneServer($appid, $serverid)
    {
        if(!$appid || !$serverid) {
            return Library::output(2001);
        }
        $app = new AppinfoModel;
        $info = $app->getPayUrlByServerid($appid, $serverid);
        if(!is_array($info)) {
            return Library::output(2017);
        }
        return Library::output(0, $info);
    }

    /**
     * 设置一个服的信息 
     * @param   int  appid
     * @param   int  serverid
     * @param   array info
     * @return  array
    */
    public function setOneServer(Request $request, $appid, $serverid)
    {
        $appid = intval($appid);
        $serverid = intval($serverid);
        $json = $request->input('json');
        $info = json_decode($json, true);
        if(!$appid || !$serverid || !$info || !is_array($info)) {
            return Library::output(2001);
        }

        $app = new AppinfoModel;
        $ret = $app->setOnePayUrl($appid, $serverid, $info);
        if(!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

}
