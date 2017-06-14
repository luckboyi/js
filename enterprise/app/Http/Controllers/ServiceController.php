<?php

// 客服系统

namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\CookieModel;
use App\Models\ServiceModel;
use Config;
use Helper\AccountCenter as Account;
// 使用open的Helper
use Helper\IPSearch;
use Helper\Library;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Input;

class ServiceController extends Controller
{

    /*public function __construct()
    {
    $this->middleware("vrauth:jump", ['only' => ["advice"]]);
    }*/
    // 默认页
    public function index()
    {
        echo 'ok';
    }

    public function advice()
    {

        return view('service.advice');

    }

    /**
     * 客户端提交意见反馈
     *
     * @param  int  uid     用户uid
     * @param  string  account  账户名称
     * @param  mobile  mobile   手机号码
     * @param  int  qq          用户qq
     * @param  string  pic_json 意见反馈的图片
     * @param  string  ask    反馈的问题内容
     * @return array('code' => 0) 表示成功，code非等于0，后面会有msg错误消息
     */
    public function adviceAjax(Request $request)
    {

        $uid     = CookieModel::getCookie("uid");
        $account = CookieModel::getCookie("account");
        $mobile  = $request->input("mobile", '');
        $qq      = $request->input("qq", '');
        $content = $request->input("content", '');
        $urls    = $request->input('urls');
        if (!$urls || !$content) {
            return Library::output(2001);
        }

        if (!$mobile && !$qq) {
            return Library::output(2001);
        }
        if (!is_array($urls)) {
            $arr = $urls;
            if (!$uid) {
                $uid = 10000;
            }
            $res = ImageHelper::uploadDataUrl("service", $arr, $uid);
        }

        $param['uid']      = $uid;
        $param['account']  = $account;
        $param['mobile']   = $mobile;
        $param['qq']       = $qq;
        $param['pic_json'] = isset($res) ? json_encode($res) : ''; // 作为json存入数据库
        $param['ask']      = $content;

        $serviceModel = new serviceModel();
        $ret          = $serviceModel->insertAdvice($param); // 得到插入的主键id
        unset($param);

        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    // 得到意见反馈列表
    public function adviceList(Request $request)
    {

        $serviceModel = new serviceModel();

        $result = $serviceModel->getAdvice();

        return view('service.adviceList', compact('result'));
    }

    // 得到意见反馈一条具体信息
    public function adviceInfo(Request $request)
    {
        $id  = $request->input("id");
        $uid = $request->input("uid");
        if (!$uid) {
            $uid = 10000;
        }
        $serviceModel = new serviceModel();

        $result = $serviceModel->getAdviceById($id);
        if (!empty($result['pic_json'])) {
            $pic    = json_decode($result['pic_json'], true);
            $res    = ImageHelper::path("service", $uid);
            $picArr = [];
            foreach ($pic as $key => &$value) {
                $picArr[$key] = 'http://www.vronline.com/servers/showimg/' . $id . '/' . $uid . '/' . $value;
            }
            unset($value);
            $result['pic_json'] = $picArr;
            $result['uid']      = $uid;
            $result['id']       = $id;
        }

        return view('service.adviceInfo', compact('result'));
    }

    public function printImg(Request $request, $id, $uid, $name)
    {
        $res      = ImageHelper::path("service", $uid);
        $fileName = $res['dir'] . $name;
        if (file_exists($fileName)) {
            $imageInfo = getimagesize($fileName);
            if (!$imageInfo) {
                return Library::output(1);
            }
            $contentType = $imageInfo['mime'];
            $extArr      = ['image/png', 'image/jpeg', 'image/gif'];
            if (!$contentType || !in_array($contentType, $extArr)) {
                return Library::output(1);
            }
            header('Content-type: ' . $contentType);
            $image = file_get_contents($fileName);
            echo $image;
        } else {
            echo "File no exists!";
        }
    }

}
