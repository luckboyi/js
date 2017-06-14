<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CookieModel;
use App\Models\SolrModel;
use App\Models\SupportModel;
use App\Models\VideoModel;
use Config;
use Illuminate\Http\Request;

class MediaController extends Controller
{

    public function index(Request $request)
    {

        $mediaCateGorys = Config::get("video.class");
        $solrModel      = new SolrModel();
        $posCode        = [
            'hot-video'          => 6,
            'video-rank'         => 7,
            'video-index-banner' => 4,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
        }

        $medias = [];

        foreach ($mediaCateGorys as $cateId => $val) {
            $num = 8;
            if ($val['id'] == 30400) {
                $num = 4;
            }
            $params          = ['category' => $cateId];
            $params['limit'] = [0, $num];
            $medias[$cateId] = $solrModel->search("video", $params);
        }
        return view('website.media.index', compact('mediaCateGorys', 'recommend', 'medias'));
    }

    public function mediaList(Request $request)
    {
        $solrModel      = new SolrModel();
        $class_id       = (int) $request->input("class_id");
        $params         = ['category' => $class_id];
        $medias         = $solrModel->search("video", $params);
        $mediaCateGorys = Config::get("video.class");

        if (!isset($mediaCateGorys[$class_id])) {
            return redirect('/media', 302, [], true);
        }

        $cateGoryName = $mediaCateGorys[$class_id]['name'];

        $posCode = [
            'video-index-banner' => 4,
        ];
        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
        }

        return view('website.media.list', compact('class_id', 'mediaCateGorys', 'recommend', 'medias', "cateGoryName"));
    }

    // 视频详情
    public function mediaPlay(Request $request, $videoId)
    {
        if (!$videoId) {
            return redirect('/media', 302, [], true);
        }
        $videoModel     = new VideoModel();
        $videoInfo      = $videoModel->getVideoById($videoId);
        $mediaCateGorys = Config::get("video.class");
        if (!$videoInfo || $videoInfo['video_stat'] != 0) {
            return redirect('/media', 302, [], true);
        }

        //获取用户的uid=>判断是否登录
        $uid      = CookieModel::getCookie('uid');
        $clickArr = $this->getMediaClick($uid, $videoId);

        if ($videoInfo['video_link_tp'] == 1 && $videoInfo['video_trans'] == '') {
            $fileName = str_replace('http://netctvideo.vronline.com/', '', $videoInfo['video_link']);
            $ret      = ImageHelper::videoTranscoding($fileName);
            if (strlen($ret) > 10) {
                $videoModel->saveDevVideoInfo($video_id, ['video_trans' => $ret]);
                return Library::output(0, ['id' => $ret]);
            }
        }

        if ($videoInfo['video_trans'] == "1") {
            $videoInfo['video_link'] = str_replace(".mp4", "_blue.mp4", $videoInfo['video_link']);
        }

        return view('website.media.play', compact('mediaCateGorys', 'videoInfo', 'clickArr', 'uid'));
    }

    /**
     * 视频的点赞状态获取
     * @param $seconds
     * @return string
     */
    public function getMediaClick($uid, $vid)
    {
        $videoModel = new VideoModel();
        if (!$vid) {
            return false;
        }

        if ($uid == '') {
            $result['alreadyClick'] = '';
            $result['uid']          = $uid;
            return $result;
        }

        $info['loginUid'] = $uid;
        $info['vid']      = $vid;

        $result       = [];
        $supportModel = new SupportModel;
        $supp         = $supportModel->isSupported($uid, $vid, "video");
        if ($supp == "up") {
            $supp = "Y";
        } else if ($supp == "down") {
            $supp = "N";
        } else {
            $supp = "";
        }

        $result['alreadyClick'] = $supp;
        $result['uid']          = $info['loginUid'];
        return $result;
    }

}
