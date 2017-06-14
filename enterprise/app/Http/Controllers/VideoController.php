<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2016/9/5
 * Time: 15:40
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

// 引用Model
use App\Models\CookieModel;
use App\Models\DataCenterStatModel;

// 获取recommend推荐位的model
use App\Models\MiddleModel;
use App\Models\OperateModel;
use App\Models\SupportModel;
use App\Models\VideoModel;
use Config;
use Helper\UdpLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Input;
use Log;
use Redirect;

class VideoController extends Controller
{

    public function __construct()
    {
        // $this->middleware("vrauth:json", ['only' => ["setVrClickEvent", "getVrClick"]]);
    }

    protected function output($code, $data = null)
    {
        $passport = new MiddleModel();
        if ($code == 0 && $data) {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("code" => $code, "data" => $data, "msg" => $msg));
        } else {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("code" => $code, "msg" => $msg));
        }
    }

    protected function outputPc($code, $data = null)
    {
        $passport = new MiddleModel();
        if ($code == 0 && $data) {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("code" => $code, "data" => $data, "msg" => $msg));
        } else {
            $msg = Config::get("errorcode.{$code}");
            return $passport->jsonEncode(array("code" => $code, "msg" => $msg));
        }
    }

    /**
     * 处理一些带有标签的字符串->数组，例如：$string = "{&quot;loginMobile&quot;:&quot;13641673610&quot;}"
     * @param $str
     * @return string
     */
    public function tagStrToArr($str)
    {
        $sec = html_entity_decode($str);
        $ret = stripslashes($sec);
        return $ret;
    }

    public function index(Request $request)
    {
        return view('website/media/indexv2');
    }

    /*
     * 获取所有视频相关信息
     */
    public function getAll()
    {
        $MiddleModel = new MiddleModel();
        $videosInfo  = $MiddleModel->getAll();
        echo '<pre>';
        print_r($videosInfo);
    }

    /*
     * 获取所有视频相关分类
     */
    public function getVideoSort()
    {
        $MiddleModel = new MiddleModel();
        $videoSort   = $MiddleModel->getVideoSort();
        return view('admin/video/videoSort', ['data' => $videoSort]);
    }

    /*
     * 获取所有视频相关信息
     */
    public function getVideoInfo()
    {
        $MiddleModel = new MiddleModel();
        $where       = array();
        $videoInfo   = $MiddleModel->getVideoInfo($where);
        return view('admin/video/videoSearch', ['data' => $videoInfo]);
    }

    /*
     * 获取所有视频相关信息
     */
    public function getVideoSearch(Request $request, $searchword)
    {
        $MiddleModel = new MiddleModel();
        //$searchword = $request->input('searchword');
        //$vuid = $request->input('userid');
        $where = array(
            'searchword' => $searchword,
        );
        $videoInfo = $MiddleModel->getVideoInfo($where);
        return view('admin/video/videoSearch', ['data' => $videoInfo, 'searchword' => $searchword]);
    }

    /*
     * 视频广告管理
     */
    public function addVideoAd(Request $request, $vtid)
    {
        $MiddleModel = new MiddleModel();
        //$searchword = $request->input('searchword');
        //$vuid = $request->input('userid');
        $where = array(
            'vtid' => $vtid,
        );

        $videoInfo = $MiddleModel->getVideoAdInfo($where);
        $videoSort = $MiddleModel->getVideoSort();
        return view('admin/video/videoAd', ['data' => $videoInfo, 'vtid' => $vtid, 'sort' => $videoSort]);
    }
    /*
     * 视频广告管理
     */
    public function addVideoAdView(Request $request)
    {
        // 先判断有没有session,没有表示没有登录
        if ($request->session()->has('user') && $request->session()->has('perm')) {

            $perm = $request->session()->get('perm');

            $MiddleModel = new MiddleModel();
            //$searchword = $request->input('searchword');
            //$vuid = $request->input('userid');
            $where = array(
                'vtid' => 1,
            );

            $videoInfo = $MiddleModel->getVideoAdInfo($where);
            $videoSort = $MiddleModel->getVideoSort();
            return view('admin/video/videoAD', ['data' => $videoInfo, 'vtid' => 1, 'sort' => $videoSort, 'perm' => $perm]);

        } else {

            return Redirect::to('user/login'); // 跳到登录页面或其他
        }

    }

    /*
     * 上传视频分类信息
     */

    public function uploadSort(Request $request)
    {
        $passport = new MiddleModel();
        $vtid     = $request->input('vtid');
        $submit   = $request->input('submit');
        $sortName = $request->input('typename');
        $sortDesc = $request->input('typedesc');
        $userId   = $request->input('userid');

        $uploadDir     = "/opt/wwwroot/enterprise/public" . DIRECTORY_SEPARATOR . "videoType" . DIRECTORY_SEPARATOR . $vtid . DIRECTORY_SEPARATOR;
        $uploadDirTure = "/videoType" . DIRECTORY_SEPARATOR . $vtid . DIRECTORY_SEPARATOR;

        // 创建用户的头像目录
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }
        $fileAllowExt  = 'gif|jpg|jpeg|png|gif'; //限制上传图片的格式
        $fileAllowSize = 2 * 1024 * 1024; //限制最大尺寸是2MB
        //$submit = isset($_POST['submit']) ? $_POST['submit'] : 123;
        if ($submit == 'sureBtn') {
            if (is_uploaded_file($_FILES['typelogo']['tmp_name'])) {
                $fileName         = $_FILES['typelogo']['name'];
                $fileError        = $_FILES['typelogo']['error'];
                $fileType         = $_FILES['typelogo']['type'];
                $fileTmpName      = $_FILES['typelogo']['tmp_name'];
                $fileSize         = $_FILES['typelogo']['size'];
                $fileExt          = substr($fileName, strrpos($fileName, '.') + 1);
                $data['oldName']  = $fileName;
                $data['fileExt']  = $fileExt;
                $data['fileType'] = $fileType;
                switch ($fileError) {
                    case 0:
                        $code        = 0;
                        $data['msg'] = "文件上传成功!";
                        break;

                    case 1:
                        $code        = 2202;
                        $data['msg'] = "文件上传失败，文件大小" . $fileSize . "超过限制,允许上传大小" . $passport->sizeFormat($fileAllowSize) . "!";
                        return $this->output($code, $data);
                        break;

                    case 3:
                        $code        = 2203;
                        $data['msg'] = "上传失败，文件只有部份上传!";
                        return $this->output($code, $data);
                        break;

                    case 4:
                        $code        = 2204;
                        $data['msg'] = "上传失败，文件没有被上传!";
                        return $this->output($code, $data);
                        break;

                    case 5:
                        $code        = 2205;
                        $data['msg'] = "文件上传失败，文件大小为0!";
                        return $this->output($code, $data);
                        break;
                }
                if (stripos($fileAllowExt, $fileExt) === false) {
                    $code        = 2206;
                    $data['msg'] = "该文件扩展名不允许上传";
                    return $this->output($code, $data);
                }
                if ($fileSize > $fileAllowSize) {
                    $code        = 2202;
                    $data['msg'] = "文件大小超过限制,只能上传" . $passport->sizeFormat($fileAllowSize) . "的文件!";
                    return $this->output($code, $data);
                }
                if ($code !== 0) {
                    $data['msg'] = $data['msg'];
                    return $this->output($code, $data);
                }
                if ($code !== 0) {
                    return $this->output($code, $data);
                }
                if (!file_exists($uploadDir)) {
                    return $this->output($code, $data);
                }
                $fileNewName  = substr(md5($vtid), 0, 12);
                $fileSavePath = $uploadDir . $fileNewName;
                move_uploaded_file($fileTmpName, $fileSavePath);
                $fileSavePathTure = $uploadDirTure . $fileNewName;
                $timeStamp        = time();
                $addData          = array(
                    'vtid'            => $vtid,
                    'typename'        => $sortName,
                    'typedescription' => $sortDesc,
                    'typelogo'        => url($fileSavePathTure),
                    'ispassed'        => 'Y',
                    'userid'          => $userId,
                    'sequence'        => 1,
                    'tmcreate'        => $timeStamp,
                );
                $result = $passport->addVideoSort($addData);
                $code   = 1; //更新状态失败
                if ($result) {
                    $code        = 0; //图片上传成功
                    $data['url'] = url($fileSavePathTure) . '?' . $timeStamp;
                }
                return $this->output($code, $data);
            }
        }
    }

    /*
     * 视频分类删除
     */

    public function videoSortDel(Request $request)
    {
        $passport = new MiddleModel();
        $action   = $request->input('typename');
        $vtid     = $request->input('vtid');
        $userId   = $request->input('userid');
        if ($action === '' || $vtid === '' || $userId === '') {
            return $this->output(2011);
        }
        $dataArr = array(
            'vtid'   => $vtid,
            'userid' => $userId,
        );
        $result = $passport->videoSortDel($dataArr);
        $code   = 1;
        if ($result) {
            $code = 0;
        }
        return $this->output($code);
    }

    /*
     * 上传广告位
     */

    public function uploadAd(Request $request)
    {
        $passport = new MiddleModel();
        $vtid     = $request->input('vtid');
        $vid      = $request->input('videoId');
        $url      = $request->input('videoUrl');
        $userId   = $request->input('userid');
        $tmBegin  = $request->input('tmBegin');
        $tmEnd    = $request->input('tmEnd');

        $uploadDir = "resources" . DIRECTORY_SEPARATOR . "videoAd" . DIRECTORY_SEPARATOR . $vid . DIRECTORY_SEPARATOR;

        if ($vid == '') {
            $extDir    = substr(md5($url), 0, 8);
            $uploadDir = "resources" . DIRECTORY_SEPARATOR . "videoAd" . DIRECTORY_SEPARATOR . $extDir . DIRECTORY_SEPARATOR;
        }
        // 创建用户的头像目录
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        $fileAllowExt  = 'gif|jpg|jpeg|png|gif'; //限制上传图片的格式
        $fileAllowSize = 2 * 1024 * 1024; //限制最大尺寸是2MB
        //$submit = isset($_POST['submit']) ? $_POST['submit'] : 123;
        if (is_uploaded_file($_FILES['typelogo']['tmp_name'])) {
            $fileName         = $_FILES['typelogo']['name'];
            $fileError        = $_FILES['typelogo']['error'];
            $fileType         = $_FILES['typelogo']['type'];
            $fileTmpName      = $_FILES['typelogo']['tmp_name'];
            $fileSize         = $_FILES['typelogo']['size'];
            $fileExt          = substr($fileName, strrpos($fileName, '.') + 1);
            $data['oldName']  = $fileName;
            $data['fileExt']  = $fileExt;
            $data['fileType'] = $fileType;
            switch ($fileError) {
                case 0:
                    $code        = 0;
                    $data['msg'] = "文件上传成功!";
                    break;

                case 1:
                    $code        = 2202;
                    $data['msg'] = "文件上传失败，文件大小" . $fileSize . "超过限制,允许上传大小" . $passport->sizeFormat($fileAllowSize) . "!";
                    return $this->output($code, $data);
                    break;

                case 3:
                    $code        = 2203;
                    $data['msg'] = "上传失败，文件只有部份上传!";
                    return $this->output($code, $data);
                    break;

                case 4:
                    $code        = 2204;
                    $data['msg'] = "上传失败，文件没有被上传!";
                    return $this->output($code, $data);
                    break;

                case 5:
                    $code        = 2205;
                    $data['msg'] = "文件上传失败，文件大小为0!";
                    return $this->output($code, $data);
                    break;
            }
            if (stripos($fileAllowExt, $fileExt) === false) {
                $code        = 2206;
                $data['msg'] = "该文件扩展名不允许上传";
                return $this->output($code, $data);
            }
            if ($fileSize > $fileAllowSize) {
                $code        = 2202;
                $data['msg'] = "文件大小超过限制,只能上传" . $passport->sizeFormat($fileAllowSize) . "的文件!";
                return $this->output($code, $data);
            }
            if ($code !== 0) {
                $data['msg'] = $data['msg'];
                return $this->output($code, $data);
            }
            if ($code !== 0) {
                return $this->output($code, $data);
            }
            if (!file_exists($uploadDir)) {
                return $this->output($code, $data);
            }
            $fileNewName = substr(md5($vid), 0, 12);
            if ($vid == '' || $vid == 0) {
                $fileNewName = substr(md5($url), 0, 12);
            }
            $fileSavePath = $uploadDir . $fileNewName;
            move_uploaded_file($fileTmpName, $fileSavePath);
            $timeStamp = time();
            $addData   = array(
                'content_sortid' => $vtid,
                'ad_id'          => 'video_1',
                'content_id'     => $vid,
                'content_url'    => $url,
                'create_userid'  => 0,
                'content_type'   => 1,
                'opening_time'   => $tmBegin,
                'end_time'       => $tmEnd,
            );
            $result = $passport->addVideoAd($addData);
            $code   = 1; //更新状态失败
            if ($result) {
                $code        = 0; //图片上传成功
                $data['url'] = url($fileSavePath) . '?' . $timeStamp;
            }
            return $this->output($code, $data);
        }
    }

    /*
     * 广告位删除接口
     */

    public function videoAdDel(Request $request)
    {
        $passport = new MiddleModel();
        $action   = $request->input('action');
        $id       = $request->input('id');
        //$userId = $request->input('userid');
        $userId = 0;
        if ($action === '' || $id === '' || $userId === '') {
            return $this->output(2011);
        }
        $dataArr = array(
            'id' => $id,
        );
        $result = $passport->videoAdDel($dataArr);
        $code   = 1;
        if ($result) {
            $code = 0;
        }
        return $this->output($code);
    }

    /*
     * 视频的推荐位管理
     */
    public function videoRecommend(Request $request)
    {
        $MiddleModel = new MiddleModel();

        $videoSort = $MiddleModel->getVideoSort();

        $recommendArr = array();
        $vSort        = array();
        foreach ($videoSort as $sk => $sv) {
            $vtid = $sv['vtid'];
            $case = array(
                'vtid' => $vtid,
            );
            $vSort[$vtid] = array(
                'sortName' => $sv['typename'],
            );
            $recommendArr[$vtid] = $MiddleModel->getVideoRecommend($case);
            foreach ($recommendArr[$vtid] as $k => $v) {
                $where = array(
                    'searchword' => $v['content_id'],
                );
                $recommendArr[$vtid][$k]['videoInfo'] = $MiddleModel->getVideoInfoGet($where);
            }
        }

        return view('admin/video/videoRecommend', ['data' => $recommendArr, 'sort' => $vSort]);
    }
    /*
     * 添加或更新推荐位
     */
    public function videoRecommendAdd(Request $request)
    {
        $videoId = $request->input('videoId');
        $tmBegin = $request->input('tmBegin');
        $tmEnd   = $request->input('tmEnd');
        $uniqId  = $request->input('uniqId');
        $userid  = $request->input('userid');
        $sortId  = $request->input('sortId');
        $data    = array(
            'id'           => $uniqId,
            'content_id'   => $videoId,
            'opening_time' => $tmBegin,
            'end_time'     => $tmEnd,
            'sortId'       => $sortId,
        );
        $MiddleModel = new MiddleModel();
        $result      = $MiddleModel->videoRecommendAdd($data);
        $code        = 1;
        if ($result) {
            $code = 0;
        }
        return $this->output($code);
    }

    /*
     * 添加用户查询视频的页面
     */
    public function vUserSearch(Request $request, $sort, $uid)
    {
        $sort        = intval($sort);
        $uid         = intval($uid);
        $MiddleModel = new MiddleModel();
        $videoSort   = $MiddleModel->getVideoSort();

        $data = $MiddleModel->getVideoHistory($uid, $sort);

        $perPage = 3;
        if ($request->has('page')) {
            $currentPage = $request->input('page');
            $currentPage = $currentPage <= 0 ? 1 : $currentPage;
        } else {
            $currentPage = 1;
        }

        $item  = array_slice($data, ($currentPage - 1) * $perPage, $perPage); //注释1
        $total = count($data);

        $paginator = new LengthAwarePaginator($item, $total, $perPage, $currentPage, [
            'path'     => Paginator::resolveCurrentPath(), //注释2
            'pageName' => 'page',
        ]);

        $videoList = $paginator->toArray()['data'];

        return view('admin/user/vUserSearch', ['data' => $videoList, 'paginator' => $paginator, 'selType' => $sort, 'uid' => $uid, 'sort' => $videoSort]);
    }

    /*
     * 添加用户查询视频的页面
     */
    public function vUserSearchOne(Request $request)
    {

        $MiddleModel = new MiddleModel();
        $videoSort   = $MiddleModel->getVideoSort();

        $data = array();

        return view('admin/user/vUserSearch', ['data' => $data, 'selType' => 'all', 'uid' => '', 'sort' => $videoSort]);
    }

    public function media(Request $request)
    {
        $MiddleModel = new MiddleModel();
        $videoModel  = new VideoModel();
        $operate     = new OperateModel();

        //获取video中的推荐视频的信息
        $recommendArr = $operate->getByType('video');
        foreach ($recommendArr as $k => $v) {
            if ($v['code'] == 'hot-video') {
                $videoSort[0] = $recommendArr[$k];
            }
        }
        //获取配置中的视频分类信息
        $videoSort[] = $videoModel->getVideoSortByConfig();
        //获取视频的推荐位的信息
        $videoInfo = [];

        //获取精选视频的信息
        foreach ($videoSort as $vk => $vv) {
            if ($vk == 0) {
                $vidArr = $operate->getItemsByPosid($vv['posid']);
                foreach ($vidArr as $ak => $av) {
                    $videoArr = $MiddleModel->getVideoInfoByVid($av['itemid'], 7);
                    if (!$videoArr || !is_array($videoArr) || !isset($videoArr[0])) {
                        continue;
                    }
                    $videoInfo[$vk]['info'][$ak] = $videoArr[0];
                    foreach ($videoArr as $mk => $mv) {
                        $videoInfo[$vk]['info'][$ak]['video_times'] = $this->timeFormat($mv['video_times']);
                    }
                }
                continue;
            }
            foreach ($vv as $sk => $sv) {
                $videoArrBySort = $videoModel->getAllBySort($sv['id'], 7);
                if (!$videoArrBySort) {
                    continue;
                }

                $videoInfo[$vk]['info'][$sk]        = $videoArrBySort;
                $videoInfo[$vk]['info'][$sk]['num'] = $videoModel->getVideoNumBySort($sv['id']);
                foreach ($videoArrBySort as $tk => $tv) {
                    $videoInfo[$vk]['info'][$sk][$tk]['video_times'] = $this->timeFormat($tv['video_times']);
                }
            }

        }

        if (strrpos($request->url(), 'client')) {
            return view('client/video/index', ['recommendSort' => $videoSort, 'videoInfo' => $videoInfo]);
        }
        return view('website/media/index', ['recommendSort' => $videoSort, 'videoInfo' => $videoInfo]);
    }

    public function mediaMore(Request $request, $sort)
    {
        $sort        = intval($sort);
        $MiddleModel = new MiddleModel();
        $videoModel  = new VideoModel();
        $operate     = new OperateModel();

        //获取video中的推荐视频的信息
        $recommendArr = $operate->getByType('video');
        foreach ($recommendArr as $k => $v) {
            if ($v['code'] == 'hot-video') {
                $videoSort[0] = $recommendArr[$k];
            }
        }
        //获取配置中的视频分类信息
        $videoSort[] = $videoModel->getVideoSortByConfig();

        //获取展示区的banner推荐
        $codeArr     = config::get('video.class');
        $code        = $codeArr[$sort]['code'];
        $bannerPosId = $operate->getPosIdBySort($code);

        $vidArr = $operate->getItemsByPosid($bannerPosId['posid']);

        $videoArrBySort = $videoModel->getAllBySort($sort);
        if (strrpos($request->url(), 'client')) {
            return view('client/video/mediaMore', ['sort' => $sort, 'recommendSort' => $videoSort, 'bannerDate' => $vidArr, 'videoInfo' => $videoArrBySort]);
        }
        return view('website/media/mediaMore', ['sort' => $sort, 'recommendSort' => $videoSort, 'bannerDate' => $vidArr, 'videoInfo' => $videoArrBySort]);
    }

    /**
     * 获取分页的接口
     * @param $array
     * @param $keys
     * @param string $type
     */
    public function videoPageDate(Request $request, $sort)
    {
        $sort           = intval($sort);
        $videoModel     = new VideoModel();
        $page           = $request->input('page');
        $pageSize       = 12;
        $videoArrBySort = $videoModel->getAllBySortPage($sort, $page, $pageSize);

        $htmlTags = '';
        $url      = 'mediaPlayer?vid=';
        if (strrpos($request->url(), 'client')) {
            $url = 'client/video/mediaPlayer?vid=';
        }
        if ($videoArrBySort) {
            foreach ($videoArrBySort as $k => $v) {
                $htmlTags .= '<li class="fl pr"><a href="' . url($url . $v['video_id'] . '&vsort=' . $sort) . '"><img src=' . static_image($v['video_cover'], 226) . '><div class="play_mask"></div><div class="play_video"></div><p class="pa"><span class="fl look">' . $v['video_view'] . '</span><span class="fr">' . $this->timeFormat($v['video_times']) . '</span></p></a><div class="clearfix"><span class="fl">' . $v['video_name'] . '</span></div></li>';
            }
        }
        return $htmlTags;
    }

    /**
     * 视频列表页
     *
     * @return \Illuminate\Http\Response
     */
    public function videoApiList(Request $request)
    {
        $page     = (int) $request->input("page", 1);
        $class_id = (int) $request->input("class_id", 0);

        $filter = [];
        if ($class_id) {
            $filter["class_id"] = $class_id;
        }

        $videoModel = new VideoModel();
        $videos     = $videoModel->videoCategoryPage($page, 3, $filter);

        if (!$videos) {
            return $this->output(0, ["data" => false]);
        }

        return $this->output(0, ["data" => $videos]);
    }

    /**
     * 获取分页的接口=>给client下面提供的分页数据接口
     * @param $array
     * @param $keys
     * @param string $type
     */
    public function videoPageDate2(Request $request, $sort)
    {
        $sort           = intval($sort);
        $videoModel     = new VideoModel();
        $page           = $request->input('page');
        $pageSize       = 12;
        $videoArrBySort = $videoModel->getAllBySortPage($sort, $page, $pageSize);

        $htmlTags = '';
        $url      = 'client/video/mediaPlayer?vid=';
        if ($videoArrBySort) {
            foreach ($videoArrBySort as $k => $v) {
                $htmlTags .= '<li class="fl pr"><a href="' . url($url . $v['video_id'] . '&vsort=' . $sort) . '"><img src=' . static_image($v['video_cover'], 226) . '><div class="play_mask"></div><div class="play_video"></div><p class="pa"><span class="fl look">' . $v['video_view'] . '</span><span class="fr">' . $this->timeFormat($v['video_times']) . '</span></p></a><div class="clearfix"><span class="fl">' . $v['video_name'] . '</span></div></li>';
            }
        }
        return $htmlTags;
    }

    public function arraySort($array, $keys, $type = 'asc')
    {
        //$array为要排序的数组,$keys为要用来排序的键名,$type默认为升序排序
        $keysvalue = $new_array = array();
        foreach ($array as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    /*
     * 视频播放器的播放器
     */
    public function mediaPlayer(Request $request)
    {
        $MiddleModel = new MiddleModel();
        $videoId     = $request->input('vid');
        if (!$videoId) {
            return $this->output(1102);
        }

        $videoInfoArr = $MiddleModel->getVideoInfoByVid($videoId);
        return view('website/media/mediaPlayer', ['videoInfo' => $videoInfoArr]);
    }

    /**
     * 本地视频播放器
     * @param Request $request
     * @return mixed
     */
    public function videoPlayLocal(Request $request)
    {
        $videoModel = new VideoModel();
        $operate    = new OperateModel();

        //获取video中的推荐视频的信息
        $recommendArr = $operate->getByType('video');
        foreach ($recommendArr as $k => $v) {
            if ($v['code'] == 'hot-video') {
                $recommendSort[0] = $recommendArr[$k];
            }
        }
        //获取配置中的视频分类信息
        $recommendSort[] = $videoModel->getVideoSortByConfig();
        $sort            = 'no';
        return view('client/video/videoPlayLocal', compact('recommendSort', 'sort'));
    }

    /*
     * VR眼睛内的视频点赞和踩的接口，获取点赞的状态Y或N
     */
    public function getVrClick(Request $request)
    {
        $videoModel = new VideoModel();
        $reqJson    = $request->input('json');
        $param      = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param)) {
            return $this->outputPc(2001);
        }
        $loginUid = isset($param['uid']) ? $param['uid'] : '';
        if ($loginUid == '') {
            $userInfo         = CookieModel::getCookie('uid');
            $info['loginUid'] = $userInfo['uid'];
        } else {
            $info['loginUid'] = $loginUid;
        }

        $info['vid'] = $param['vid'];

        $supportModel = new SupportModel;
        $supp         = $supportModel->isSupported($info['loginUid'], $param['vid'], "video");
        if ($supp == "up") {
            $supp = "Y";
        } else if ($supp == "down") {
            $supp = "N";
        } else {
            $supp = "";
        }

        $result['alreadyClick'] = $supp;
        $result['uid']          = intval($info['loginUid']);
        $result['vid']          = $info['vid'];
        return $this->outputPc(0, $result);
    }

    /*
     * 设置用户赞和踩的redis缓存和计数
     */
    public function setVrClickEvent(Request $request)
    {
        $videoModel = new VideoModel();
        $reqJson    = $request->input('json');
        $param      = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param)) {
            return $this->outputPc(2001);
        }
        $loginUid = isset($param['uid']) ? $param['uid'] : '';
        if ($loginUid == '') {
            $info['loginUid'] = CookieModel::getCookie('uid');
        } else {
            $info['loginUid'] = $loginUid;
        }

        if ($info['loginUid'] == '') {
            return $this->outputPc(1301);
        }
        $info['vid']     = $param['vid'];
        $info['support'] = $param['support'];

        $supportModel = new SupportModel;
        $supp         = $supportModel->isSupported($info['loginUid'], $param['vid'], "video");
        if ($supp) {
            return $this->outputPc(2701);
        }

        if (isset($param['support']) && $param['support'] == 1) {
            $action = "up";
        } else {
            $action = "down";
        }
        $addRet = $supportModel->support($info['loginUid'], $info['vid'], "video", $action);
        $addNum = $videoModel->addAgreeNum($info);
        if (!$addNum) {
            return $this->outputPc(1);
        }
        return $this->outputPc(0);
    }

    /**
     * 添加用户的历史记录
     * @param Request $request
     * @return mixed
     */
    public function addHistory(Request $request)
    {
        $videoModel = new VideoModel();
        $appid      = $request->input('appid');
        $timelen    = $request->input('timelen');
        $uid        = CookieModel::getCookie('uid');
        if ($appid == '') {
            $reqJson = $request->input('json');
            $param   = json_decode(self::tagStrToArr($reqJson), true);
            if (!$param || !is_array($param)) {
                return $this->outputPc(2001);
            }
            $uid     = isset($param['uid']) ? $param['uid'] : '';
            $appid   = $param['videoid'];
            $timelen = $param['timelen'];
        }

        //添加用户的历史记录到UdpLog日志中=>/opt/tcplog/videolog/userLog
        //UdpLog::save2("videolog/userLog", array("function" => "addHistory", "uid" => $uid, "appid" => intval($appid), "timelen" => intval($timelen), "viewtm" => date("Ymd", time())), "");

        /**
         * 发送统计
         */
        $properties = [
            "_gameid"   => $appid,
            "timecount" => intval($timelen),
            "isall"     => 1, // 表示日志数据是全的，不需要再从数据库补数据
        ];
        DataCenterStatModel::stat("vrplat", "videoplay", $uid == '' ? 0 : $uid, $properties);

        if ($uid == '' || $appid == '' || $timelen == '') {
            return $this->output(1102);
        }

        $info = [
            'appid'   => intval($appid),
            'timelen' => intval($timelen),
        ];

        $ret = $videoModel->addVideoHistory($uid, $info);
        if (!$ret) {
            return $this->output(1);
        }
        return $this->output(0);
    }
    /**
     * VR内部的视频分类数据其中包括精彩推荐的数据
     */
    public function videoList(Request $request)
    {
        $videoModel  = new VideoModel();
        $operate     = new OperateModel();
        $MiddleModel = new MiddleModel();

        $videoSort = [];
        //获取video中的推荐视频的信息
        // $recommendArr = $operate->getByType('video');
        // foreach ($recommendArr as $k => $v) {
        //     if ($v['code'] == 'hot-video') {
        //         //$videoSort[0] = $recommendArr[$k];
        //         $videoSort[0]['vtid']       = strval(1);
        //         $videoSort[0]['vtname']     = $recommendArr[$k]['name'];
        //         $videoSort[0]['vtdesc']     = $recommendArr[$k]['desc'];
        //         $videoSort[0]['vtimg']      = $recommendArr[$k]['show_pic'];
        //         $videoSort[0]['vrtypelogo'] = 'http://pic.vronline.com/video/images/30000-VR.png';
        //     }
        // }
        //获取配置中的视频分类信息
        $videoSortArr = array_values($videoModel->getVideoSortByConfig());
        foreach ($videoSortArr as $k => $v) {
            $videoSort[$k]['vtid']       = strval($v['id']);
            $videoSort[$k]['vtname']     = $v['name'];
            $videoSort[$k]['vtdesc']     = $v['desc'];
            $videoSort[$k]['vtimg']      = $v['img'];
            $videoSort[$k]['vrtypelogo'] = $v['vrimg'];
        }

        $result         = [];
        $result['code'] = 0;
        $result['msg']  = '获取数据成功';
        $result['data'] = $videoSort;
        return $MiddleModel->jsonEncode($result);
    }

    /**
     * VR内部的视频分类列表
     */
    public function videoHallList(Request $request)
    {
        $videoModel  = new VideoModel();
        $operate     = new OperateModel();
        $MiddleModel = new MiddleModel();

        $videoSort = [];
        //获取video中的推荐视频的信息
        $recommendArr = $operate->getByType('video');
        foreach ($recommendArr as $k => $v) {
            if ($v['code'] == 'hot-video') {
                $videoSort[0] = $recommendArr[$k];
            }
        }
        //获取配置中的视频分类信息
        $videoSortArr = array_values($videoModel->getVideoSortByConfig());

        for ($i = 1; $i <= count($videoSortArr); $i++) {
            $videoSort[$i] = $videoSortArr[$i - 1];
        }

        //获取视频的推荐位的信息
        $videoInfo = [];

        //获取精选视频的信息
        foreach ($videoSort as $vk => $vv) {
            if ($vk == 0) {
                $vidArr = $operate->getItemsByPosid($vv['posid']);
                foreach ($vidArr as $ak => $av) {
                    $videoArr = $videoModel->getInfoByIdForVr($av['itemid']);
                    if (!$videoArr || !is_array($videoArr) || !isset($videoArr[0])) {
                        continue;
                    }
                    $videoInfo[$vk]['vtid']      = strval(1);
                    $videoInfo[$vk]['vtname']    = $vv['desc'];
                    $videoInfo[$vk]['info'][$ak] = $videoArr[0];
                    foreach ($videoArr as $mk => $mv) {
                        $videoInfo[$vk]['info'][$ak]['videotimes'] = $this->timeFormat($mv['videotimes']);
                    }
                }
                continue;
            }

            $videoArrBySort = $videoModel->getAllBySortForVr($vv['id'], 50);
            if (!$videoArrBySort) {
                continue;
            }
            $videoInfo[$vk]['vtid']   = strval($vv['id']);
            $videoInfo[$vk]['vtname'] = $vv['name'];

            $videoInfo[$vk]['info'] = $videoArrBySort;
            $videoInfo[$vk]['num']  = $videoModel->getVideoNumBySort($vv['id']);
            foreach ($videoArrBySort as $tk => $tv) {
                $videoInfo[$vk]['info'][$tk]['videotimes'] = $this->timeFormat($tv['videotimes']);
            }
        }

        $result         = [];
        $result['code'] = 0;
        $result['msg']  = '获取数据成功';
        $result['data'] = $videoInfo;
        return $MiddleModel->jsonEncode($result);
    }

    public function videoVrHistory(Request $request)
    {
        $videoModel  = new VideoModel();
        $MiddleModel = new MiddleModel();
        $operate     = new OperateModel();
        $reqJson     = $request->input('json');
        $param       = json_decode(self::tagStrToArr($reqJson), true);
        $uid         = $param['uid'];
//      if(!$uid) {
        //          $uid = CookieModel::getCookie('uid');
        //      }

        if (!$uid) {
            $result['code'] = 1301;
            $result['msg']  = '请登录账户！';
            $result['data'] = [];
            return $MiddleModel->jsonEncode($result);
        }

        //获取video中的推荐视频的信息
        $recommendArr = $operate->getByType('video');
        foreach ($recommendArr as $k => $v) {
            if ($v['code'] == 'hot-video') {
                $videoSort[0] = $recommendArr[$k];
            }
        }
        //获取配置中的视频分类信息
        $videoSort[] = $videoModel->getVideoSortByConfig();

        $ret = '';
        if ($uid) {
            $ret = $videoModel->getVideoHistoryNew($uid);
        }
        $videoInfoDet = [];
        //获取当天的年份
        $y = date("Y");

        //获取当天的月份
        $m = date("m");

        //获取当天的号数
        $d = date("d");

        $todayStamp = mktime(0, 0, 0, $m, $d, $y);
        if (!$ret) {
            $videoInfoDet = [];
        } else {
            foreach ($ret as $k => $v) {
                if ($v['ltime'] > $todayStamp) {
                    $data = 'todayData';
                    if (isset($videoInfoDet[$data]) && count($videoInfoDet[$data]) > 7) {
                        continue;
                    }
                    $videoInfoDetArr                     = $MiddleModel->getVideoInfoByVid($v['appid'])[0];
                    $videoInfoDet[$data][$k]             = $this->historyInfoFormat($data, $k, $videoInfoDetArr);
                    $videoInfoDet[$data][$k]['viewtm']   = strval($v['ltime']);
                    $videoInfoDet[$data][$k]['viewCost'] = strval($v['timelen']);
                } else if ($todayStamp >= $v['ltime'] && $v['ltime'] > $todayStamp - 7 * 24 * 3600) {
                    $data = 'weekData';
                    if (isset($videoInfoDet[$data]) && count($videoInfoDet[$data]) > 7) {
                        continue;
                    }
                    $videoInfoDetArr                     = $MiddleModel->getVideoInfoByVid($v['appid'])[0];
                    $videoInfoDet[$data][$k]             = $this->historyInfoFormat($data, $k, $videoInfoDetArr);
                    $videoInfoDet[$data][$k]['viewtm']   = strval($v['ltime']);
                    $videoInfoDet[$data][$k]['viewCost'] = strval($v['timelen']);
                } else {
                    $data = 'weekAgoData';
                    if (isset($videoInfoDet[$data]) && count($videoInfoDet[$data]) > 7) {
                        continue;
                    }
                    $videoInfoDetArr                     = $MiddleModel->getVideoInfoByVid($v['appid'])[0];
                    $videoInfoDet[$data][$k]             = $this->historyInfoFormat($data, $k, $videoInfoDetArr);
                    $videoInfoDet[$data][$k]['viewtm']   = strval($v['ltime']);
                    $videoInfoDet[$data][$k]['viewCost'] = strval($v['timelen']);
                }
            }
        }

        $result['code'] = 0;
        $result['msg']  = '获取数据成功';
        if (empty($videoInfoDet)) {
            $result['code'] = 1;
            $result['msg']  = '无历史记录数据';
        }
        $result['data'] = $videoInfoDet;
        return $MiddleModel->jsonEncode($result);
    }

    /**
     * 格式化历史记录数组
     * @param $arr
     */
    public function historyInfoFormat($data, $k, $arr)
    {
        $videoInfoDet[$data][$k]['vid']         = strval($arr['video_id']);
        $videoInfoDet[$data][$k]['banner']      = strval(0);
        $videoInfoDet[$data][$k]['vindex']      = strval(1);
        $videoInfoDet[$data][$k]['vindexone']   = strval(1);
        $videoInfoDet[$data][$k]['agreenum']    = strval($arr['agreenum']);
        $videoInfoDet[$data][$k]['disagreenum'] = strval($arr['disagreenum']);
        $videoInfoDet[$data][$k]['vpurl']       = $arr['video_link'];
        $videoInfoDet[$data][$k]['vname']       = $arr['video_name'];
        $videoInfoDet[$data][$k]['vtid']        = strval($arr['video_class']);
        $videoInfoDet[$data][$k]['vdesc']       = $arr['video_intro'];
        $videoInfoDet[$data][$k]['upfacility']  = $arr['video_upfacility'];
        $videoInfoDet[$data][$k]['vsmallimg']   = static_image($arr['video_cover'], 226);
        $videoInfoDet[$data][$k]['vbigimg']     = static_image($arr['video_cover'], 226);
        $videoInfoDet[$data][$k]['viewtimes']   = strval($arr['video_view']);
        $videoInfoDet[$data][$k]['videotimes']  = strval($arr['video_times']);
        return $videoInfoDet[$data][$k];
    }

    /**
     * 获取视频分页数据
     * @param Request $request
     */
    public function videoPageList(Request $request)
    {
        $reqJson = $request->input('json');
        $param   = json_decode(self::tagStrToArr($reqJson), true);
        if (!$param || !is_array($param)) {
            return $this->output(2001);
        }
        $sort        = $param['vtypeid'];
        $MiddleModel = new MiddleModel();
        $videoModel  = new VideoModel();
        $operate     = new OperateModel();

        $start          = $param['start'];
        $pageSize       = $param['pieces'];
        $videoArrBySort = [];
        if ($sort == 1) {
            $videoSort = [];
            //获取video中的推荐视频的信息
            $recommendArr = $operate->getByType('video');
            foreach ($recommendArr as $k => $v) {
                if ($v['code'] == 'hot-video') {
                    $videoSort[0] = $recommendArr[$k];
                }
            }
            //获取精选视频的信息
            foreach ($videoSort as $vk => $vv) {
                $vidArr = $operate->getItemsByPosid($vv['posid']);
                foreach ($vidArr as $ak => $av) {
                    $videoArr            = $videoModel->getInfoByIdForVr($av['itemid']);
                    $videoArrBySort[$ak] = $videoArr[0];
                    foreach ($videoArr as $mk => $mv) {
                        $videoArrBySort[$ak]['vsmallimg']  = static_image($mv['vsmallimg'], 226);
                        $videoArrBySort[$ak]['vbigimg']    = static_image($mv['vbigimg'], 226);
                        $videoArrBySort[$ak]['vid']        = strval($mv['video_id']);
                        $videoArrBySort[$ak]['vindexone']  = strval(1);
                        $videoArrBySort[$ak]['vindex']     = strval(1);
                        $videoArrBySort[$ak]['banner']     = strval(0);
                        $videoArrBySort[$ak]['videotimes'] = $this->timeFormat($mv['videotimes']);
                    }
                }
            }
        } else {
            $videoArrBySort = $videoModel->getBySortPageForVr($sort, $start, $pageSize);
            foreach ($videoArrBySort as $mk => $mv) {
                $videoArrBySort[$mk]['vsmallimg']   = static_image($mv['vsmallimg'], 226);
                $videoArrBySort[$mk]['vbigimg']     = static_image($mv['vbigimg'], 226);
                $videoArrBySort[$mk]['vid']         = strval($mv['video_id']);
                $videoArrBySort[$mk]['vindexone']   = strval(1);
                $videoArrBySort[$mk]['vindex']      = strval(1);
                $videoArrBySort[$mk]['banner']      = strval(0);
                $videoArrBySort[$mk]['viewtimes']   = strval($mv['viewtimes']);
                $videoArrBySort[$mk]['agreenum']    = strval($mv['agreenum']);
                $videoArrBySort[$mk]['disagreenum'] = strval($mv['disagreenum']);
                $videoArrBySort[$mk]['videotimes']  = $this->timeFormat($mv['videotimes']);
            }
        }

        $result['code'] = 0;
        $result['msg']  = '获取数据成功';
        if (empty($videoArrBySort)) {
            $result['code'] = 1;
            $result['msg']  = '无历史记录数据';
        }
        $result['data'] = $videoArrBySort;
        return $MiddleModel->jsonEncode($result);
    }

    /**
     * 用户历史记录的页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function videoHistory(Request $request)
    {
        $videoModel  = new VideoModel();
        $MiddleModel = new MiddleModel();
        $operate     = new OperateModel();

        $uid = CookieModel::getCookie('uid');

        //获取video中的推荐视频的信息
        $recommendArr = $operate->getByType('video');
        foreach ($recommendArr as $k => $v) {
            if ($v['code'] == 'hot-video') {
                $videoSort[0] = $recommendArr[$k];
            }
        }
        //获取配置中的视频分类信息
        $videoSort[] = $videoModel->getVideoSortByConfig();

        $ret = '';
        if ($uid) {
            $ret = $videoModel->getVideoHistoryNew($uid);
        }

        $videoInfoDet = [];
        //获取当天的年份
        $y = date("Y");

        //获取当天的月份
        $m = date("m");

        //获取当天的号数
        $d = date("d");

        $todayStamp = mktime(0, 0, 0, $m, $d, $y);
        if (!$ret) {
            $videoInfoDet = [];
        } else {
            foreach ($ret as $k => $v) {
                if ($v['ltime'] > $todayStamp) {
                    $data = 'today';
                    if (isset($videoInfoDet[$data]) && count($videoInfoDet[$data]) > 7) {
                        continue;
                    }
                    $videoInfoDet[$data][$k]                 = $MiddleModel->getVideoInfoByVid($v['appid'])[0];
                    $videoInfoDet[$data][$k]['videotimes']   = $this->timeFormat($videoInfoDet[$data][$k]['video_times']);
                    $videoInfoDet[$data][$k]['viewtimes']    = $v['ltime'];
                    $videoInfoDet[$data][$k]['viewtimeslen'] = $v['timelen'];
                } else if ($todayStamp >= $v['ltime'] && $v['ltime'] > $todayStamp - 7 * 24 * 3600) {
                    $data = 'week';
                    if (isset($videoInfoDet[$data]) && count($videoInfoDet[$data]) > 7) {
                        continue;
                    }
                    $videoInfoDet[$data][$k]                 = $MiddleModel->getVideoInfoByVid($v['appid'])[0];
                    $videoInfoDet[$data][$k]['videotimes']   = $this->timeFormat($videoInfoDet[$data][$k]['video_times']);
                    $videoInfoDet[$data][$k]['viewtimes']    = $v['ltime'];
                    $videoInfoDet[$data][$k]['viewtimeslen'] = $v['timelen'];
                } else {
                    $data = 'earlier';
                    if (isset($videoInfoDet[$data]) && count($videoInfoDet[$data]) > 7) {
                        continue;
                    }
                    $videoInfoDet[$data][$k]                 = $MiddleModel->getVideoInfoByVid($v['appid'])[0];
                    $videoInfoDet[$data][$k]['videotimes']   = $this->timeFormat($videoInfoDet[$data][$k]['video_times']);
                    $videoInfoDet[$data][$k]['viewtimes']    = $v['ltime'];
                    $videoInfoDet[$data][$k]['viewtimeslen'] = $v['timelen'];
                }
            }
        }
        //获取视频的分类
        $mediaCateGorys = Config::get("video.class");

        if (strrpos($request->url(), 'client')) {
            return view('client/video/history', ['recommendSort' => $videoSort, 'videoInfo' => $videoInfoDet, 'sort' => 'no']);
        }
        return view('website/media/history', ['recommendSort' => $videoSort, 'mediaCateGorys' => $mediaCateGorys, 'videoInfo' => $videoInfoDet, 'sort' => 'no', 'userId' => $uid]);
    }

    /**
     * [getAdminVideoDate description] admin管理后台的页面数据展示
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getAdminVideoDate(Request $request)
    {
        $date           = $request->input('date');
        $videoModel     = new VideoModel();
        $adminVideoInfo = $videoModel->getAdminVideoDate($date);
        return view('data.video', ['adminVideoInfo' => $adminVideoInfo]);
    }

    public function getHistoryApi(Request $request)
    {
        $videoModel = new VideoModel();

        $startNum = $request->input('start');
        $pageNum  = $request->input('num');
        if (!$pageNum) {
            $pageNum = 30;
        }
        if (!$startNum) {
            $startNum = 0;
        }
        $uid = CookieModel::getCookie('uid');

        $ret = '';
        if ($uid) {
            $ret = $videoModel->videoHistoryApi($uid, $startNum, $pageNum);
        }

        if (!$ret) {
            $videoInfoDet['data'] = [];
        } else {
            $videoInfoDet['data'] = $ret;
        }
        return $this->output(0, $videoInfoDet);
    }

    /*
     * 格式化视频时长的方法
     */
    public function timeFormat($seconds)
    {
        $h = 0;
        if ($seconds >= 3600) {
            $h = intval($seconds / 3600);
        }
        if ($h < 10) {
            $h = '0' . $h;
        }
        $min = intval(($seconds - $h * 3600) / 60);
        if ($seconds < 60) {
            $min = 0;
        }
        if ($min < 10) {
            $min = '0' . $min;
        }
        $sec = $seconds - $h * 3600 - $min * 60;
        if ($sec < 10) {
            $sec = '0' . $sec;
        }
        $ret = $h . ':' . $min . ':' . $sec;
        return $ret;
    }

}
