<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 15:44
 */
namespace App\Models;

// 引用Model
use Config;
use DB;
use Illuminate\Database\Eloquent\Model;

class VideoModel extends Model
{

    private $videoHistorySize = 1;
    private $videoDevSize     = 8;
    /*
     * 用户点击视频下的顶和踩后redis缓存key前缀
     */
    private $key_click_video_agree = "click_video_agree_mark_";

    /**
     * 获取礼包库的库、表名后缀
     */
    protected function getlogDB($uid)
    {
        if (!$uid) {
            return false;
        }
        $tbl_suff = $uid % 32;
        return array('db' => "db_user_log", 'table_log' => "t_video_log_" . $tbl_suff);
    }

    //dev video start
    public function getDevVideoClassPage($class_id, $search = '')
    {
        $res = DB::connection("db_dev")->table("t_video");
        if ($class_id > 0) {
            $findCase = 'FIND_IN_SET(' . $class_id . ', video_class)';
            $res->whereRaw($findCase);
        }
        if ($search) {
            $res->where('video_name', 'like', '%' . $search . '%');
        }
        $row = $res->orderBy('video_id', 'desc')->paginate($this->videoDevSize);
        return $row;
    }

    public function getDevVideoPage()
    {
        $row = DB::connection('db_dev')->table('t_video')->where('video_stat', 1)->orderBy('ltime', 'desc')->paginate($this->videoDevSize);
        return $row;
    }

    public function getDevVideoByUid($uid, $stat)
    {
        if (!$uid) {
            return false;
        }
        if ($stat == "all") {
            $row = DB::connection('db_dev')->table('t_video')->where('video_uid', $uid)->orderBy('ltime', 'desc')->get(); //->paginate($this->videoDevSize);
        } else if ($stat == "pass") {
            $row = DB::connection('db_dev')->table('t_video')->where('video_uid', $uid)->where('video_stat', 0)->orderBy('ltime', 'desc')->get(); //->paginate($this->videoDevSize);
        } else if ($stat == "deny") {
            $row = DB::connection('db_dev')->table('t_video')->where('video_uid', $uid)->where('video_stat', 3)->orderBy('ltime', 'desc')->get(); //->paginate($this->videoDevSize);
        } else if ($stat == "wait") {
            $row = DB::connection('db_dev')->table('t_video')->where('video_uid', $uid)->where('video_stat', 1)->orderBy('ltime', 'desc')->get(); //->paginate($this->videoDevSize);
        }

        return $row;
    }

    public function getDevVideoById($id)
    {
        if (!$id) {
            return false;
        }
        $row = DB::connection('db_dev')->table('t_video')->where('video_id', $id)->first();
        return $row;
    }

    public function updateTrans($persistentId)
    {
        if (!$persistentId) {
            return false;
        }

        $ret = DB::connection('db_dev')->table('t_video')->where('video_trans', $persistentId)->update(['video_trans' => 1]);

        return $ret;
    }

    public function saveDevVideoInfo($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($id > 0) {
            $row = DB::connection('db_dev')->table('t_video')->where('video_id', $id)->update($info);
        } else {
            $row = DB::connection('db_dev')->table('t_video')->insert($info);
        }
        return $row;
    }

    public function offlineDevVideoInfo($id)
    {
        $info = ['video_stat' => 9];
        $ret  = DB::connection('db_dev')->table('t_video')->where('video_id', $id)->update($info);
        $row  = DB::connection('db_operate')->table('t_video')->where('video_id', $id)->first();
        if ($row && $ret) {
            $ret = DB::connection('db_operate')->table('t_video')->where('video_id', $id)->update($info);
        }

        return $ret;
    }

    public function passDevVideoInfo($id)
    {
        $row = DB::connection('db_dev')->table('t_video')->where('video_id', $id)->first();
        if ($row) {
            $row['video_stat'] = 0;
            $ret               = DB::connection('db_operate')->table('t_video')->replace($row);
            if ($ret) {
                $ret = DB::connection('db_dev')->table('t_video')->where('video_id', $id)->update(['video_stat' => 0]);
            }
        }

        return $ret;
    }
    //dev video end

    //pub video start
    /**
     * 通过视频分类获取视频信息
     * @return array
     */
    public function getVideoByTop($sort, $limit = 10)
    {
        $findCase  = 'FIND_IN_SET(' . $sort . ', video_class)';
        $videoInfo = DB::connection('db_operate')->table('t_video')->select('video_id', 'video_name', 'video_cover', 'video_times', 'video_view')->whereRaw($findCase)->orderBy('ltime', 'desc')->limit($limit)->get();
        return $videoInfo;
    }

    public function getVideoById($id)
    {
        $row = DB::connection('db_operate')->table('t_video')->where("video_id", $id)->first();
        return $row;
    }

    /*
     * 获取视频的分类信息
     */
    public function getVideoSortByConfig()
    {
        $sort = config::get("video.class");
        return $sort;
    }

    /*
     * 通过分类获取视频的数目
     */
    public function getVideoNumBySort($sort)
    {
        $findCase   = 'FIND_IN_SET(' . $sort . ', video_class)';
        $videoCount = DB::connection('db_operate')->table('t_video')->whereRaw($findCase)->count();
        return $videoCount;
    }

    /**
     * 通过视频分类获取视频信息
     * @return array
     */
    public function getAllBySort($sort, $limit = null)
    {
        $findCase = 'FIND_IN_SET(' . $sort . ', video_class)';
        if ($limit == '') {
            $videoInfo = DB::connection('db_operate')->table('t_video')->select('video_id', 'video_name', 'video_cover', 'video_times', 'video_view')->whereRaw($findCase)->paginate(12);
        } else {
            $videoInfo = DB::connection('db_operate')->table('t_video')->select('video_id', 'video_name', 'video_cover', 'video_times', 'video_view')->whereRaw($findCase)->limit($limit)->get();
        }
        return $videoInfo;
    }

    /**
     * 通过视频分类获取视频信息=>VR客户端的
     * @return array
     */
    public function getAllBySortForVr($sort, $limit)
    {
        $findCase = 'FIND_IN_SET(' . $sort . ', video_class)';

        $videoInfo = DB::connection('db_operate')->table('t_video')->select('video_id', 'video_name as vname', 'video_class as vtid', 'video_intro as vdesc', 'video_cover as vsmallimg', 'video_cover as vbigimg', 'video_link as vpurl', 'video_times as videotimes')->whereRaw($findCase)->limit($limit)->get();
        return $videoInfo;
    }

    /**
     * 通过视频分类获取视频信息
     * 支持下拉刷新接口
     * @return array
     */
    public function getAllBySortPage($sort, $page = "", $pagenum = "")
    {
        if ($page == '' || $pagenum == '') {
            return false;
        }
        $findCase  = 'FIND_IN_SET(' . $sort . ', video_class)';
        $startNum  = ($page - 1) * $pagenum;
        $videoInfo = DB::connection('db_operate')->table('t_video')->select('video_id', 'video_name', 'video_cover', 'video_times', 'video_view')->whereRaw($findCase)->skip($startNum)->take($pagenum)->get();
        return $videoInfo;
    }

    /**
     * 通过视频分类获取视频信息=>VR
     * 支持下拉刷新接口
     * @return array
     */
    public function getBySortPageForVr($sort, $start = "", $pagenum = "")
    {
        if ($start === '' || $pagenum == '') {
            return false;
        }
        $findCase = 'FIND_IN_SET(' . $sort . ', video_class)';
        //$startNum = ($page - 1) * $pagenum;
        $videoInfo = DB::connection('db_operate')->table('t_video')->select('video_id', 'video_name as vname', 'video_class as vtid', 'video_intro as vdesc', 'video_cover as vsmallimg', 'video_cover as vbigimg', 'video_link as vpurl', 'video_times as videotimes', 'video_view as viewtimes', 'agreenum', 'disagreenum')->where('video_vr', 1)->whereRaw($findCase)->skip($start)->take($pagenum)->get();

        return $videoInfo;
    }

    /*
     * 获取所有的视频相关信息
     */
    public function getAll()
    {
        $info = DB::connection('db_operate')->table('t_video')->get();
        return $info;
    }

    /*
     * 获取所有视频相关分类
     */
    public function getVideoSort()
    {
        $where = array(
            'video_stat' => '0',
        );
        $sortInfo = DB::connection('db_operate')->table('v_videotype')->where($where)->get();
        return $sortInfo;
    }

    /*
     * 获取所有视频信息
     */
    public function getVideoInfo($data)
    {
        if (!empty($data)) {
            $case = array(
                'video_stat' => '0',
            );

            $videoInfo = '';
            if (intval($data['searchword'])) {
                $case1 = array(
                    'video_id' => $data['searchword'],
                );
                $videoInfo = DB::connection('db_operate')->table('t_video')->where($case)->where($case1)->paginate(15);
                //return $videoInfo;
            }
            if ($videoInfo === '') {
                $videoInfo = DB::connection('db_operate')->table('t_video')->where($case)->where('video_name', 'like', '%' . $data['searchword'] . '%')->paginate(15);
            }
            return $videoInfo;
        }
        $where = array(
            'video_stat' => '0',
        );
        $videoInfo = DB::connection('db_operate')->table('t_video')->where($where)->paginate(15);
        return $videoInfo;
    }

    /*
     * 获取所有视频信息
     */
    public function getVideoInfoGet($data)
    {
        if (!empty($data)) {
            $case = array(
                'video_stat' => '0',
            );

            $videoInfo = '';
            if (intval($data['searchword'])) {
                $case1 = array(
                    'video_id' => $data['searchword'],
                );
                $videoInfo = DB::connection('db_operate')->table('t_video')->where($case)->where($case1)->get();
                //return $videoInfo;
            }
            if ($videoInfo === '') {
                $videoInfo = DB::connection('db_operate')->table('t_video')->where($case)->where('video_name', 'like', '%' . $data['searchword'] . '%')->get();
            }
            return $videoInfo;
        }
        $where = array(
            'video_stat' => '0',
        );
        $videoInfo = DB::connection('db_operate')->table('t_video')->where($where)->get();
        return $videoInfo;
    }

    /*
     * 获取所有广告信息
     */
    public function getVideoAdInfo($data)
    {
        $videoInfo = '';
        if (!empty($data)) {
            $vtid  = $data['vtid'];
            $where = array(
                'content_sortid' => $vtid,
                'ad_id'          => 'video_1',
            );
            //$sortInfo = DB::connection('db_operate')->table('v_videotype')->where($where)->get();

            $adInfo = DB::connection('db_operate')->table('v_add_ad')->where($where)->paginate(15);

            //$case = "SELECT * FROM v_videos WHERE find_in_set('" . $data['vtid'] . "', videotypeid) AND ispassed='Y' AND recommend=1";
            if (empty($adInfo)) {
                return $videoInfo;
            }
            $videoInfo = $adInfo;
        }
        return $videoInfo;
    }

    /*
     * 获取视频推荐位信息
     */
    public function getVideoRecommend($data)
    {
        $where = array(
            'content_type' => 1,
        );
        $vtid          = $data['vtid'];
        $RecommendInfo = DB::connection('db_operate')->table('v_recommend')->where($where)->where('position_id', 'like', 'video\_' . $vtid . '\_%')->get();
        return $RecommendInfo;
    }

    /*
     * 添加视频推荐信息
     */
    public function videoRecommendAdd($data)
    {
        if (empty($data)) {
            return false;
        }
        $id    = $data['id'];
        $where = array(
            'id' => $id,
        );

        $update = array(
            'content_id'   => $data['content_id'],
            'opening_time' => $data['opening_time'],
            'end_time'     => $data['end_time'],
        );
        $ret = DB::connection('db_operate')->table('v_recommend')->where($where)->update($update);
        return $ret;
    }

    /*
     * 添加视频分类
     */
    public function addVideoSort($data)
    {
        if (empty($data)) {
            return false;
        }
        $ret = DB::connection('db_operate')->table('v_videotype')->insert($data);
        if ($ret) {
            $vtid = $data['vtid'];
            //添加视屏推荐位的数据id
            $numberArr = array(0, 1, 2, 3, 4, 5, 6, 7, 8);
            $where     = array(
                'content_type' => 1,
            );
            $RecommendInfo = DB::connection('db_operate')->table('v_recommend')->where($where)->where('position_id', 'like', 'video\_' . $vtid . '\_%')->get();
            if (empty($RecommendInfo)) {
                foreach ($numberArr as $v) {
                    $insertArr = array(
                        'position_id'  => 'video_' . $vtid . '_' . $v,
                        'content_type' => 1,
                    );
                    $ret = DB::connection('db_operate')->table('v_recommend')->insert($insertArr);
                }
            }
        }
        return $ret;
    }

    /*
     * 删除视频分类
     */

    public function videoSortDel($data)
    {
        if (empty($data)) {
            return false;
        }
        $ret = DB::connection('db_operate')->table('v_videotype')->where($data)->delete();
        if (!$ret) {
            return false;
        }
        if ($ret) {
            $result = DB::connection('db_operate')->table('v_recommend')->where('position_id', 'like', 'video\_' . $data['vtid'] . '\_%')->delete();
        }
        return $result;
    }

    /*
     * 删除视频分类
     */

    public function videoAdDel($data)
    {
        if (empty($data)) {
            return false;
        }
        $ret = DB::connection('db_operate')->table('v_add_ad')->where($data)->delete();
        return $ret;
    }

    /*
     * 添加视频分类
     */
    public function addVideoAd($data)
    {
        if (empty($data)) {
            return false;
        }

        $ret = DB::connection('db_operate')->table('v_add_ad')->insert($data);
        return $ret;
    }

    /*
     * 查询某部视频的信息
     */
    public function getVideoInfoById($vid)
    {
        $case = [
            'video_id' => $vid,
        ];
        $videoInfo = DB::connection('db_operate')->table('t_video')->where($case)->get();
        return $videoInfo;
    }

    /*
     * 查询某部视频的信息
     */
    public function getInfoByIdForVr($vid)
    {
        $case = [
            'video_id' => $vid,
        ];
        $videoInfo = DB::connection('db_operate')->table('t_video')->select('video_id', 'video_name as vname', 'video_class as vtid', 'video_intro as vdesc', 'video_cover as vsmallimg', 'video_cover as vbigimg', 'video_link as vpurl', 'video_times as videotimes')->where($case)->get();
        return $videoInfo;
    }

    /*
     * 查询多部视频的信息
     */
    public function getMultiVideoInfoByIds($vid)
    {
        $videoInfo = DB::connection('db_operate')->table('t_video')->whereIn("video_id", $vid)->get();
        return $videoInfo;
    }

    /*
     * 获取视频分类视频的数目
     */
    public function getVideoNum($sort)
    {

        $case = [
            'video_stat' => '0',
        ];
        $findCase   = 'FIND_IN_SET(' . $sort . ', video_class)';
        $videoCount = DB::connection('db_operate')->table('t_video')->where($case)->whereRaw($findCase)->count();
        return $videoCount;
    }

    /**
     * 添加视频的播放次数记录
     * @param $tmp
     * @return string
     */
    public function addVideoView($vid)
    {
        $videoInfo = DB::connection('db_operate')->table('t_video')->where("video_id", $vid)->first();

        if ($videoInfo) {
            $videoViewNum = $videoInfo['video_view'] + 1;
            $update       = [
                'video_view' => $videoViewNum,
            ];
            $ret = DB::connection('db_operate')->table('t_video')->where("video_id", $vid)->update($update);
        }
        return true;
    }

    /**
     * 添加视频video的支持还是不支持的num数据
     * @param $tmp
     * @return string
     */
    public function addAgreeNum($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $case      = ['video_id' => $info['vid']];
        $videoInfo = DB::connection('db_operate')->table('t_video')->where($case)->get(['agreenum', 'disagreenum']);
        $setMark   = false;
        $update    = [];

        if ($info['support'] === 1) {
            $update = ['agreenum' => $videoInfo[0]['agreenum'] + 1];
        } else if ($info['support'] === 0) {
            $update = ['disagreenum' => $videoInfo[0]['disagreenum'] + 1];
        }
        $ret = DB::connection('db_operate')->table('t_video')->where($case)->update($update);
        return $ret;
    }

    /**
     * [videoCategoryPage description]
     * @param  integer $page    [页数]
     * @param  integer $pageNum [每页多少条]
     * @param  array   $search  [搜索的关键字]
     * @return [type]           [description]
     */
    public function videoCategoryPage($page = 1, $pageNum = 10, $search = [])
    {
        $startNum = ($page - 1) * $pageNum;
        if (isset($search['class_id'])) {
            $class_id = $search['class_id'];
            $findCase = 'FIND_IN_SET(' . $class_id . ', video_class)';
            $videoRow = DB::connection('db_operate')->table('t_video')->whereRaw($findCase)->skip($startNum)->take($pageNum)->get();
        } else {
            $videoRow = DB::connection('db_operate')->table('t_video')->skip($startNum)->take($pageNum)->get();
        }

        $videos = [];
        if ($videoRow) {
            foreach ($videoRow as $k => $v) {
                $videoArr = [
                    'id'       => $v['video_id'],
                    'tp'       => 'video',
                    'name'     => $v['video_name'],
                    // 'spell'    => $v['video_spell'],
                    'desc'     => $v['video_intro'],
                    'category' => $v['video_class'],
                    'support'  => $v['video_upfacility'],
                    'play'     => $v['video_view'],
                    'score'    => $v['agreenum'],
                    'cover'    => static_image($v['video_cover'], 226),
                    "url"      => $v['video_link'],
                ];
                $videos[] = $videoArr;
            }
        }
        return $videos;
    }

    /**
     * 视频历史记录添加
     * @param $uid
     * @param $info
     * @return array|bool
     */
    public function addVideoHistory($uid, $info)
    {
        if (!$uid || !$info || empty($info)) {
            return false;
        }
        $ret         = [];
        $info['uid'] = $uid;
        $appid       = $info['appid'];

        $result  = $this->getVideoHistoryOne($uid, $appid);
        $addView = $this->addVideoView($appid);
        $dbRes   = $this->getlogDB($uid);
        if (!$result) {
            $videoInfo = DB::connection('db_operate')->table('t_video')->where("video_id", $appid)->first();
            if (empty($videoInfo)) {
                return false;
            }

            $info['ltime']   = time();
            $info['appname'] = $videoInfo['video_name'];
            $ret             = DB::connection('db_user_log')->table($dbRes['table_log'])->insert($info);
        } else {
            $where = [
                'uid'   => $uid,
                'appid' => $appid,
            ];
            $timelen = $result['timelen'] > $info['timelen'] ? $result['timelen'] : $info['timelen'];
            $update  = [
                'ltime'   => time(),
                'timelen' => $timelen,
            ];
            $ret = DB::connection('db_user_log')->table($dbRes['table_log'])->where($where)->update($update);
        }

        return $ret;
    }

    /**
     * 获取一条用户的历史记录
     * @param $uid
     * @param $appid
     * @return array|bool|mixed|null|\stdClass|static
     */
    public function getVideoHistoryOne($uid, $appid)
    {
        if (!$uid || !$appid) {
            return false;
        }
        $case = [
            'uid'   => $uid,
            'appid' => $appid,
        ];
        $dbRes = $this->getlogDB($uid);
        $ret   = DB::connection('db_user_log')->table($dbRes['table_log'])->where($case)->first();
        return $ret;
    }

    /**
     * 获取用户的所有历史记录
     * @param $uid
     * @param $appid
     * @return array|bool|mixed|null|\stdClass|static
     */
    public function getVideoHistoryNew($uid)
    {
        if (!$uid) {
            return false;
        }
        $case = [
            'uid' => $uid,
        ];
        $dbRes = $this->getlogDB($uid);
        $ret   = DB::connection('db_user_log')->table($dbRes['table_log'])->where($case)->orderBy('ltime', 'desc')->get();
        return $ret;
    }

    public function getVideoHistoryPage($uid)
    {
        if (!$uid) {
            return false;
        }
        $case = [
            'uid' => $uid,
        ];
        $dbRes = $this->getlogDB($uid);
        $row   = DB::connection('db_user_log')->table($dbRes['table_log'])->where($case)->orderBy('ltime', 'desc')->paginate($this->videoHistorySize);
        return $row;
    }

    public function videoHistoryApi($uid, $startNum = 0, $pageNum = 10)
    {
        if (!$uid) {
            return false;
        }
        $case = [
            'uid' => $uid,
        ];

        $dbRes  = $this->getlogDB($uid);
        $row    = DB::connection('db_user_log')->table($dbRes['table_log'])->where($case)->orderBy('ltime', 'desc')->skip($startNum)->take($pageNum)->get();
        $videos = [];
        if ($row) {
            foreach ($row as $k => $v) {
                $videoInfo = DB::connection('db_operate')->table('t_video')->where("video_id", $v['appid'])->first();
                if ($videoInfo) {
                    $videoArr = [
                        'id'       => $videoInfo['video_id'],
                        'tp'       => 'video',
                        'name'     => $videoInfo['video_name'],
                        // 'spell'    => $v['video_spell'],
                        'desc'     => $videoInfo['video_intro'],
                        'category' => $videoInfo['video_class'],
                        'support'  => $videoInfo['video_upfacility'],
                        'play'     => $videoInfo['video_view'],
                        'score'    => $videoInfo['agreenum'],
                        'cover'    => static_image($videoInfo['video_cover'], 226),
                        "url"      => $videoInfo['video_link'],
                    ];
                    $videos[] = $videoArr;
                }
            }
        }
        return $videos;
    }

    /**
     * [getAdminVideoDate description]
     * @param  [type] $date [description] 格式20161127 ->精确到日
     * @return [type]       [description]
     */
    public function getAdminVideoDate($date = null)
    {
        if (!$date) {
            $ret = DB::connection('record')->table('videoDate_log')->paginate(10);
        } else {
            $where = [
                'date' => $date,
            ];
            $ret = DB::connection('record')->table('videoDate_log')->where($where)->paginate(10);
        }
        if ($ret) {
            return $ret;
        }
        return [];
    }

    /*
     * 时长格式化
     */
    public function getTimeFormat($tmp)
    {
        if ($tmp > 3600) {
            $hours  = floor($tmp / 3600);
            $hasTmp = $tmp - $hours * 3600;
            $min    = ceil($hasTmp / 60);
            $format = $hours . '小时' . $min . '分钟';
            return $format;
        }
        $min    = ceil($tmp / 60);
        $format = $min . '分钟';
        return $format;
    }

    ##### new video #######
    /*
     * 查询多部视频的信息
     */
    public function getVideoByIds($vid)
    {
        $videoInfo = DB::connection('db_operate')->table('t_video')->whereIn("video_id", $vid)->get();
        return $videoInfo;
    }

}
