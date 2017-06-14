<?php
/**
 * 大朋活动接口
 */
namespace App\Http\Controllers;

use Config;
use Helper\Library;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DeepoonModel;

class DeepoonController extends Controller
{
    public function __construct()
    {
        $this->middleware("vrauth:json", ['only' => ["join", "joinStat", "addVideo", "videoList"]]);
    }

    /**
     * 报名接口
     * @param  int $appid [appid]
     * @param  string $username [用户名]
     * @param  string $password [密码]
     * @param  string $sign [签名]
     * @return [json] [openid,nick,face,token]
     */
    public function join(Request $request)
    {
        $name    = $request->input('name', "测试");
        $phone = $request->input('phone', "13333333333");
        $group = $request->input('group', "2");
        $city     = $request->input('city', "140700");
        $device = $request->input('device', "device");
        $software     = $request->input('software', "software");
        if (!$name || !$phone || !$group || !$city) {
            return Library::output(2001);
        }

        $userInfo = $request->userinfo;
        if(!isset($userInfo['uid']) || !$userInfo['uid']) {
            return Library::output(1301);
        }
        $uid = $userInfo['uid'];

        $deepoonModel = new DeepoonModel;

        $ret = $deepoonModel->join($uid, $name, $phone, $group, $city, $device, $software);echo "<pre><font color=''> result ==> ";var_dump($ret);echo "</font></pre>";
    }

    /**
     * 报名接口
     * @param  int $appid [appid]
     * @param  string $username [用户名]
     * @param  string $password [密码]
     * @param  string $sign [签名]
     * @return [json] [openid,nick,face,token]
     */
    public function joinStat(Request $request)
    {
        $userInfo = $request->userinfo;
        if(!isset($userInfo['uid']) || !$userInfo['uid']) {
            return Library::output(1301);
        }
        $uid = $userInfo['uid'];

        $deepoonModel = new DeepoonModel;

        $ret = $deepoonModel->joinStat($uid);
        if(!$ret) {
            return Library::output(1, null, "查询失败");
        }
        switch($ret) {
            case "none":
                return Library::output(404, null, "未报名");
                break;
            case "join":
                return Library::output(306, null, "已报名未上传作品");
                break;
            case "done":
                return Library::output(200, null, "已成功参赛");
                break;
            default:    return Library::output(404, null, "未报名");
        }
        return Library::output(1, null, "查询失败");
    }

    /**
     * 上传视频
     * @param  int $appid [appid]
     * @param  string $username [用户名]
     * @param  string $password [密码]
     * @param  string $sign [签名]
     * @return [json] [openid,nick,face,token]
     */
    public function addVideo(Request $request)
    {
        $title    = $request->input('title', "标题");
        $content = $request->input('content', "内容");
        $group = $request->input('group', "2");
        $chl     = $request->input('chl', "1");

        $userInfo = $request->userinfo;
        if(!isset($userInfo['uid']) || !$userInfo['uid']) {
            return Library::output(1301);
        }
        $uid = $userInfo['uid'];

        $deepoonModel = new DeepoonModel;
$img = "https://image.vronline.com/videoimg/10054/100581.jpg";
$imgv = "https://image.vronline.com/vrgameimg/pub/1000031/logo?1/ty466";
$videourl = "http://down.video.vronline.com/dev/chongshangyunxiao.mp4";
        $ret = $deepoonModel->addVideo($uid, $title, $content, $group, $img, $imgv, $chl, $videourl);echo "<pre><font color=''> addVideo ==> ";var_dump($ret);echo "</font></pre>";
    }

    /**
     * 视频列表
     * @param  int $appid [appid]
     * @param  string $username [用户名]
     * @param  string $password [密码]
     * @param  string $sign [签名]
     * @return [json] [openid,nick,face,token]
     */
    public function videoList(Request $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 100);

        $userInfo = $request->userinfo;
        if(!isset($userInfo['uid']) || !$userInfo['uid']) {
            return Library::output(1301);
        }
        $uid = $userInfo['uid'];

        $deepoonModel = new DeepoonModel;

        $ret = $deepoonModel->videoList($uid, $page, $size);
        if(!$ret || !is_array($ret)) {
            return Library::output(1, null, "查询失败");
        }
        return Library::output(0, $ret);
    }

}
