<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActModel;
use App\Models\DeepoonModel;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Illuminate\Http\Request;

class Vr3dbbController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:jump", ['only' => ["signUpView", "uploadVideoView"]]);
        $this->middleware("vrauth:json", ['only' => ["load3dbbStat", "signUpSubmit", "signUpCode", "uploadVideoSubmit"]]);
    }

    /**
     * [index 大赛首页]
     * @return [view] [description]
     */
    public function index(Request $request)
    {
        $act  = new ActModel;
        $info = $act->actGetInfoByPosition("3dbb_index", 8);
        return view('act.3dbb', compact("info"));
    }

    /**
     * [load3dbbStat 获取活动状态]
     * @return [view] [description]
     */
    public function load3dbbStat(Request $request)
    {
        $userInfo = $request->userinfo;
        $act      = new ActModel;
        $check    = $act->act3dbbCheck($userInfo['uid']);
        if ($check) {
            $signup = 1;
        } else {
            $signup = 0;
        }
        $uploadRet = $act->act3dbbCheckUpload($userInfo['uid']);
        if ($uploadRet) {
            $upload = 1;
        } else {
            $upload = 0;
        }
        return Library::output(0, ['nick' => $userInfo['nick'], 'signup' => $signup, 'upload' => $upload]);
    }

    /**
     * [signUpView 报名页面]
     * @return [view] [description]
     */
    public function signUpView(Request $request)
    {
        $userInfo = $request->userinfo;
        $act      = new ActModel;
        $check    = $act->act3dbbCheck($userInfo['uid']);
        if ($check) {
            return redirect('/3dbb', 302, [], false);
        }

        $uid          = $userInfo['uid'];
        $token        = $userInfo['token'];
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $userInfoArr  = $accountModel->info($uid, $token);

        if (empty($userInfoArr['data'])) {
            return redirect('/3dbb', 302, [], false);
        }
        $bindMobile = $userInfoArr['data']['bindmobile'] == '' ? '' : $userInfoArr['data']['bindmobile'];

        return view('act.signup', compact('userInfo', 'bindMobile'));
    }

    /**
     * [signUpCode 发送手机验证码]
     * @param [int] $[mobile] [<手机号>]
     * @return [json] [description]
     */
    public function signUpCode(Request $request)
    {
        $userInfo = $request->userinfo;
        $mobile   = $request->input('mobile');

        $uid          = $userInfo['uid'];
        $token        = $userInfo['token'];
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $userInfoArr  = $accountModel->info($uid, $token);
        if (empty($userInfoArr['data'])) {
            return Library::output(1);
        }
        $bindMobile = $userInfoArr['data']['bindmobile'];
        if ($bindMobile) {
            return Library::output(2101);
        }

        if (!$mobile) {
            return Library::output(2001);
        }
        $ret = $accountModel->sendBindMsg($uid, $token, $mobile);
        if (!$ret || $ret['code'] !== 0) {
            return Library::output($ret['code'], $ret['msg']);
        }
        return Library::output(0);
    }

    /**
     * [signUpSubmit 报名]
     * @param [string] $[name] [<名称>]
     * @param [int] $[mobile] [<手机号>]
     * @param [int] $[tp] [<参与组别>]
     * @param [string] $[device] [<设备>]
     * @param [string] $[soft] [<软件>]
     * @param [int] $[code] [<验证码 可选>]
     * @return [json] [description]
     */
    public function signUpSubmit(Request $request)
    {
        $userInfo = $request->userinfo;
        $name     = $request->input('name');
        $mobile   = $request->input('mobile');
        $tp       = $request->input('tp');
        $device   = $request->input('device');
        $soft     = $request->input('soft');
        $district = $request->input('district');
        $code     = $request->input('code');
        if (!$name || !$mobile || !is_numeric($mobile) || ($tp != 1 && $tp != 2 && $tp != 3) || !$device || !$soft || !$district) {
            return Library::output(2001);
        }

        $uid          = $userInfo['uid'];
        $token        = $userInfo['token'];
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new AccountCenter($appid, $appkey);
        $userInfoArr  = $accountModel->info($uid, $token);
        if (empty($userInfoArr['data'])) {
            return Library::output(1);
        }
        $bindMobile = $userInfoArr['data']['bindmobile'];
        if ($bindMobile) {
            $mobile = $bindMobile;
        } else {
            if (!$code || !is_numeric($code)) {
                return Library::output(2005);
            }
            $ret = $accountModel->bindMobile($uid, $token, $mobile, $code);
            if (!$ret || $ret['code'] !== 0) {
                return Library::output($ret['code'], $ret['msg']);
            }
        }

        $act   = new ActModel;
        $check = $act->act3dbbCheck($userInfo['uid']);
        if ($check) {
            return Library::output(3201);
        }

        $is_report    = 1;
        $deepoonModel = new DeepoonModel;
        $ret3d        = $deepoonModel->join($uid, $name, $mobile, $tp, $district, $device, $soft);
        if (!$ret3d) {
            $is_report = 0;
        }

        $data = [
            'uid'       => $uid,
            'name'      => $name,
            'mobile'    => $mobile,
            'tp'        => $tp,
            'device'    => $device,
            'soft'      => $soft,
            'district'  => $district,
            'is_report' => $is_report,
        ];
        $ret = $act->act3dbbSign($data);
        if (!$ret) {
            return Library::output(3202);
        }
        return Library::output(0);
    }

    /**
     * [uploadVideoView 上传视频]
     * @return [view] [description]
     */
    public function uploadVideoView(Request $request)
    {
        $userInfo = $request->userinfo;
        $act      = new ActModel;
        $check    = $act->act3dbbCheck($userInfo['uid']);
        if (!$check) {
            return redirect('/3dbb', 302, [], true);
        }
        return view('act.upload');
    }

    /**
     * [uploadVideoSubmit 上传视频]
     * @return [json] [description]
     */
    public function uploadVideoSubmit(Request $request)
    {
        $userInfo   = $request->userinfo;
        $name       = $request->input('name');
        $source_tp  = $request->input('source_tp');
        $source_url = $request->input('source_url');
        $is_vr      = $request->input('is_vr');
        $intro      = $request->input('intro');
        $group_tp   = $request->input('group_tp');
        $thumb_w    = $request->input('thumb_w');
        $thumb_h    = $request->input('thumb_h');
        if (!$name || !$intro || !$group_tp || !$source_url || ($is_vr != 1 && $is_vr != 2) || ($source_tp != 1 && $source_tp != 2) || !$thumb_w || !$thumb_h) {
            return Library::output(2001);
        }
        $uid   = $userInfo['uid'];
        $act   = new ActModel;
        $check = $act->act3dbbCheck($uid);
        if (!$check) {
            return Library::output(3203);
        }

        $is_report    = 1;
        $thumb_w_r    = static_image($thumb_w);
        $thumb_h_r    = static_image($thumb_h);
        $deepoonModel = new DeepoonModel;
        $ret3d        = $deepoonModel->addVideo($uid, $name, $intro, $group_tp, $thumb_w_r, $thumb_h_r, $is_vr, $source_url);
        if (!$ret3d) {
            $is_report = 0;
        }

        $data = [
            'uid'        => $uid,
            'name'       => $name,
            'source_tp'  => $source_tp,
            'source_url' => $source_url,
            'is_vr'      => $is_vr,
            'intro'      => $intro,
            'thumb_w'    => $thumb_w,
            'thumb_h'    => $thumb_h,
            'group_tp'   => $group_tp,
            'stat'       => 1,
            'is_report'  => $is_report,
        ];
        $ret = $act->act3dbbUpload($data);
        if (!$ret) {
            return Library::output(3204);
        }

        return Library::output(0);
    }

    public function vrplayer(Request $request, $auto, $videoId)
    {
        if (!$videoId || strlen($videoId) < 10) {
            return Library::output(1);
        }
        $videoId        = 'video424/伦敦终(伦敦.南京周)';
        $autoPlay       = $auto == 1 ? "true" : "false";
        $video_url      = 'http://netctvideo.vronline.com/' . $videoId . '.mp4';
        $video_blue_url = 'http://netctvideo.vronline.com/' . $videoId . '_blue.mp4';
        $video_720_url  = 'http://netctvideo.vronline.com/' . $videoId . '_1080.mp4';
        $str            = '<?xml version="1.0" encoding="utf-8"?>
        <krpano>
            <action name="startup" autorun="onstart">if(device.panovideosupport == false,error(\'抱歉，你的浏览器不支持全景播放器！\');,loadscene(vrplayer););</action>
            <scene name="vrplayer" title="">
                <include url="%SWFPATH%/videointerface_novr.xml"/>
                <plugin name="video" url.html5="%SWFPATH%/videoplayer.js" url.flash="%SWFPATH%/videoplayer.swf" pausedonstart="' . $autoPlay . '" loop="false" volume="1,0" onloaded="add_video_sources()"/>
                <image><sphere url="plugin:video"/></image>
                <view hlookat="0" vlookat="0" fovtype="DFOV" fov="130" fovmin="75" fovmax="150" distortion="0.0"/>
                <action name="add_video_sources">
                    videointerface_addsource(\'原画\',\'' . $video_url . '\', \'%SWFPATH%/cover3d-empty.png\');
                    videointerface_addsource(\'超清\', \'' . $video_blue_url . '\', \'%SWFPATH%/cover3d-empty.png\');
                    videointerface_addsource(\'高清\', \'' . $video_720_url . '\', \'%SWFPATH%/cover3d-empty.png\');
                    videointerface_play(\'超清\')
                </action>
            </scene>
        </krpano>';
        return response()->make($str, '200')->header('Content-Type', 'text/xml');
    }
    /**
     * [myVideo 我的视频]
     * @return [view] [description]
     */
    public function myVideo()
    {

    }

    /**
     * [videoList 视频列表]
     * @return [view] [description]
     */
    public function videoList()
    {

    }

    /**
     * [videoInfo 视频信息]
     * @return [view] [description]
     */
    public function videoInfo()
    {

    }

    /**
     * [goVote 投票]
     * @return [json] [description]
     */
    public function goVote()
    {

    }
}
