<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CdkModel;
use App\Models\CookieModel;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Illuminate\Http\Request;

class CdkController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:json:api", ['only' => ["getGameByCdk", "importCdk"]]);
    }

    public function getGameByCdk(Request $request)
    {
        $userInfo = $request->userinfo;
        $cdk      = $request->input('cdk', "");
        if (!$cdk) {
            return Library::output(2001, null, "请输入CDK");
        }

        $uid = $userInfo['uid'];

        $cdkModel = new CdkModel;
        $ret      = $cdkModel->checkGameCdk($cdk, $uid);
        if (!$ret) {
            return Library::output(1, null, "兑换失败");
        }
        if (is_array($ret) && $ret['code'] == 0) {
            return Library::output(0, ['name' => $ret['appname'], 'appid' => $ret['appid']]);
        }
        switch ($ret) {
            case "notexists":
                return Library::output(1, "", "CDK输入错误");
                break;
            case "used":
                return Library::output(1, "", "CDK已经使用过");
                break;
            case "wrongtype":
                return Library::output(1, "", "CDK无效");
                break;
            case "owned":
                return Library::output(1, "", "已经购买过该游戏");
                break;
            default:break;
        }
        return Library::output(1);
    }

    public function importCdk(Request $request)
    {
        $userInfo = $request->userinfo;
        $itemid   = $request->input('itemid', "");
        $type     = $request->input('type', "");
        $num      = $request->input('num', 0);
        if (!$itemid || !$type || $num <= 0) {
            return Library::output(2001);
        }
        $uid = $userInfo["uid"];

        $cdkModel = new CdkModel;
        $ret      = $cdkModel->importCdk($itemid, $type, $num);
        if (!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

}
