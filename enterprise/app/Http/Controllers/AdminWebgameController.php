<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/10/8
 * Time: 16:22
 * 运营后台的页游接口
 */
namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\MiddleModel;
use App\Models\OperateModel;
use App\Models\VideoModel;
use App\Models\VrgameModel;
use App\Models\WebgameModel;
use Auth; // 使用open的Helper
use Config;
use Excel;

// 引用页游Model
use Helper\Library;

// 引用视频Model
use Illuminate\Http\Request;

// 引用VR游戏的Model

//获取游戏的图片展示资源
use Input;

class AdminWebgameController extends Controller
{
    public function test()
    {
        $vrGame = new VrgameModel();
        var_dump($vrGame->getVrGameSort());
    }

    protected function output($code, $data = null)
    {
        //$passport = new PassportModel();
        if ($code == 0 && $data) {
            $msg = Config::get("errorcode.{$code}");
            return json_encode(array("code" => $code, "data" => $data, "msg" => $msg));
        } else {
            $msg = Config::get("errorcode.{$code}");
            return json_encode(array("code" => $code, "msg" => $msg));
        }
    }

    protected function outputForPc($code, $data = null)
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

    public function webgameAd(Request $request, $vtid)
    {
        $webgame = new WebgameModel();
        //$searchword = $request->input('searchword');
        //$vuid = $request->input('userid');
        $sort = array(
            '0' => array(
                'vtid' => 1,
                'typename' => 'Banner推荐',
            ),
            '1' => array(
                'vtid' => 2,
                'typename' => '下方推荐',
            ),
        );
        $data = array(
            'vtid' => $vtid,
        );
        $webgameData = $webgame->getWebGameInfo($data);
        return view('admin/webgame/webgameAd', ['data' => $webgameData, 'vtid' => $vtid, 'sort' => $sort]);
    }

    /**
     * 删除页游的广告位
     * @param Request $request
     * @return string
     */
    public function webgameAdDel(Request $request)
    {
        $webgame = new WebgameModel();
        $action = $request->input('action');
        $id = $request->input('id');
        //$userId = $request->input('userid');
        $userId = 0;
        if ($action === '' || $id === '' || $userId === '') {
            return Library::output(2011);
        }
        $dataArr = array(
            'id' => $id,
        );
        $result = $webgame->webGameAdDel($dataArr);
        $code = 1;
        if ($result) {
            $code = 0;
        }
        return Library::output($code);
    }

    /*
     * 上传广告位
     */

    public function uploadWebgameAd(Request $request)
    {
        $webgame = new WebgameModel();
        $vtid = $request->input('vtid');
        $vid = $request->input('videoId');
        $url = $request->input('videoUrl');
        $userId = $request->input('userid');
        $tmBegin = $request->input('tmBegin');
        $tmEnd = $request->input('tmEnd');
//        var_dump($_POST);var_dump($_FILES);die;
        $uploadDir = "../public/upload" . DIRECTORY_SEPARATOR . "webgame" . DIRECTORY_SEPARATOR . "webgameAd" . DIRECTORY_SEPARATOR . $vtid . DIRECTORY_SEPARATOR;

        $trueDir = "upload/webgame" . DIRECTORY_SEPARATOR . "webgameAd" . DIRECTORY_SEPARATOR . $vtid . DIRECTORY_SEPARATOR;
        if (!empty($_FILES)) {
            $ret = json_decode($this->uploadPic($vtid, $uploadDir), true);
            if ($ret['code'] != 0) {
                return Library::output($ret['code']);
            }
        }

        $resource = url($trueDir . $ret['data']['fileNewName']);

        $timeStamp = time();
        $addData = array(
            'content_sortid' => $vtid,
            'ad_id' => 'webgame_' . $vtid,
            'content_id' => $vid,
            'content_url' => $url,
            'resource' => $resource,
            'create_userid' => 0,
            'content_type' => 1,
            'opening_time' => $tmBegin,
            'end_time' => $tmEnd,
        );
        $result = $webgame->addWebGameAd($addData);
        if (!$result) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /*
     * 获取所有页游相关信息
     */
    public function webgameInfo()
    {
        $webgame = new WebgameModel();
        $data = array();
        $webgaemInfo = $webgame->getAllGameInfoPage($data);
        //echo '<pre>';print_r($webgaemInfo);
        return view('admin/webgame/webgameSearch', ['data' => $webgaemInfo]);
    }

    /*
     * 获取所有页游相关信息
     */
    public function getWebgameSearch(Request $request, $searchword)
    {
        $where = array(
            'searchword' => $searchword,
        );
        $webgame = new WebgameModel();
        $webgaemInfo = $webgame->getAllGameInfoPage($where);
        //echo '<pre>';print_r($webgaemInfo);
        return view('admin/webgame/webgameSearch', ['data' => $webgaemInfo, 'searchword' => $searchword]);
    }

    public function webgameGift(Request $request)
    {
        $webgame = new WebgameModel();

        $webgameInfo = $webgame->getAllGiftListPage();
        $webgameInfoArr = array();
        foreach ($webgameInfo as $k => $v) {
            $trueDir = "upload/webgame" . DIRECTORY_SEPARATOR . "webgameAd" . DIRECTORY_SEPARATOR . $v['gid'] . DIRECTORY_SEPARATOR;
            $webgameInfoArr[$k]['url'] = url($trueDir . substr(md5($v['gid']), 0, 12));
            $webgameName = $webgame->getOneGameInfo($v['appid']);
            $webgameInfoArr[$k]['gname'] = $webgameName['name'];
            $num = $webgame->getGiftNum($v['gid']);
            $webgameInfoArr[$k]['num'] = $num;
        }

        //echo '<pre>'; print_r($webgameInfoArr);
        return view('admin/webgame/webgameGiftAdd', ['data' => $webgameInfo, 'extendInfo' => $webgameInfoArr]);
    }

    /**
     * 上传礼包信息
     * @param Request $request
     * @return string
     */
    public function uploadWebgameGift(Request $request)
    {
        $webgame = new WebgameModel();
        $appid = $request->input('appid');
        $content = $request->input('content');
        $name = $request->input('name');
        $desc = $request->input('desc');
        $tmBegin = $request->input('tmBegin');
        $tmEnd = $request->input('tmEnd');
        //var_dump($_POST);var_dump($_FILES);die;

        if (empty($_FILES)) {
            return Library::output(1);
        }

        $giftLastInfo = $webgame->getLastGiftInfo();
        $type = 'gift';
        $gid = $giftLastInfo['gid'] + 1;
        $uploadDir = "../public/upload" . DIRECTORY_SEPARATOR . "webgame" . DIRECTORY_SEPARATOR . "webgameAd" . DIRECTORY_SEPARATOR . $gid . DIRECTORY_SEPARATOR;
        $trueDir = "upload/webgame" . DIRECTORY_SEPARATOR . "webgameAd" . DIRECTORY_SEPARATOR . $gid . DIRECTORY_SEPARATOR;

        $ret = json_decode($this->uploadPic($gid, $uploadDir, $type), true);
        if ($ret['code'] != 0) {
            return Library::output($ret['code']);
        }

        $resource = url($trueDir . $ret['data']['fileNewName']);

        $timeStamp = time();
        $addData = array(
            'appid' => $appid,
            'name' => $name,
            'content' => $content,
            'desc' => $desc,
            'serverid' => 0,
            'start' => strtotime($tmBegin),
            'end' => strtotime($tmEnd),
        );
        $result = $webgame->addGift($addData);
        if (!$result) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /*
     * 上传游戏激活码的excel文件，以及处理逻辑
     */
    public function uploadWebgameGiftCode(Request $request)
    {
        $webgame = new WebgameModel();
        $appid = $request->input('upAppid');
        $gid = $request->input('upGid');
        $content = $request->input('content');

        $type = 'gift';
        $uploadDir = "../public/upload" . DIRECTORY_SEPARATOR . "webgame" . DIRECTORY_SEPARATOR . "webgameCodeExcel" . DIRECTORY_SEPARATOR . $gid . DIRECTORY_SEPARATOR;
        $trueDir = "upload/webgame" . DIRECTORY_SEPARATOR . "webgameCodeExcel" . DIRECTORY_SEPARATOR . $gid . DIRECTORY_SEPARATOR;

        $ret = json_decode($this->uploadExcel($gid, $uploadDir, $type), true);
        if ($ret['code'] != 0) {
            return Library::output($ret['code']);
        }

        $resource = url($trueDir . $ret['data']['fileNewName']);

        $timeStamp = time();
        $path = $uploadDir . $ret['data']['fileNewName'];
        $res = $this->Importexcel($path);

        $allArr = array();
        foreach ($res as $v) {
            $filesArr = array_filter($v);
            $allArr = array_merge($allArr, $filesArr);
        }
        if ($allArr) {
            $serverid = 5;
            $result = $webgame->addGiftCodes($gid, $appid, $serverid, $allArr);
            if (!$result) {
                return Library::output(1);
            }
            return Library::output(0);
        }
        return Library::output(1);
    }

    /*
     * 推荐位的接口
     */
    public function recommendWebGame(Request $request, $code)
    {
        $recommendModel = new OperateModel();
        $webgame = new WebgameModel();

        $videoModel = new MiddleModel();

        $recommendSortArr = $recommendModel->getPosIdsByStat(0);
        if ($code == '{code}') {
            $code = 'officialwebgame';
        }
        $startPosidArr = $recommendModel->getPosId($code);
        $posid = isset($startPosidArr['posid']) ? $startPosidArr['posid'] : 1;
        $recommendCount = [];
        for ($i = 1; $i < 10; $i++) {
            $recommendCount[$i] = [];
        }

        //获取正式表中数据
        $recommendArrAll = $recommendModel->getItemsByPosid($posid);
        $recommendArr = [];

        //获取平台的历史表中数据
        $recommendTmpArr = $recommendModel->getScheduleItemsByPosid($posid);

        foreach ($recommendArrAll as $rk => $rv) {
            $recommendArr[$rv['weight']]['posid'] = $posid;
            $recommendArr[$rv['weight']]['itemid'] = $rv['itemid'];
            $recommendArr[$rv['weight']]['weight'] = $rv['weight'];
            $recommendArr[$rv['weight']]['ts'] = $rv['ts'];

            if ($startPosidArr['type'] == 'webgame') {
                $webgameInfoArr = $webgame->getOneGameInfo($rv['itemid']);
                $recommendArr[$rv['weight']]['name'] = $webgameInfoArr['name'];
                $showPicArr = ImageHelper::path("webgameimg", $rv['itemid'], $webgameInfoArr['img_version'], "", false);
                $recommendArr[$rv['weight']]['showPic'] = isset($showPicArr['logo']) ? $showPicArr['logo'] : 'http://pic2015.ytqmx.com:82/2016/0930/17/1.jpg%21960.jpg';
            } else {
                $webgameInfoArr = $webgame->getOneGameInfo($rv['itemid']);
                if (empty($webgameInfoArr)) {
                    $webgameInfoArr = $videoModel->getVideoInfoByVid($rv['itemid']);
                    $recommendArr[$rv['weight']]['name'] = $webgameInfoArr[0]['videoname'];
                    $recommendArr[$rv['weight']]['showPic'] = $webgameInfoArr[0]['videologo'];
                } else {
                    $recommendArr[$rv['weight']]['name'] = $webgameInfoArr['name'];
                    $showVrPicArr = ImageHelper::path("vrgameimg", $rv['itemid'], $webgameInfoArr['img_version'], "", false);
                    $recommendArr[$rv['weight']]['showPic'] = isset($showVrPicArr['logo']) ? $showVrPicArr['logo'] : 'http://pic2015.ytqmx.com:82/2016/0930/17/1.jpg%21960.jpg';
                }
            }
        }

        //处理临时文件
        //              var_dump($recommendTmpArr);die;
        foreach ($recommendCount as $k => $v) {
            if (isset($recommendArr[$k])) {
            } else {
                $recommendArr[$k] = $recommendCount[$k];
            }

            foreach ($recommendTmpArr as $tk => $tv) {
                $n = 0;
                if ($tv['weight'] == $k) {
                    if (isset($recommendArr[$k]['tmp'][$n]) && !empty($recommendArr[$k]['tmp'][$n])) {
                        $n = $n + 1;
                    }
                    if ($n > 1) {
                        continue;
                    }
                    //判断该推荐位是哪一个类型 ，现在只有3个标识类，分别是：video=>视频,vrgame=>VR游戏，webgame=>页游
                    if ($startPosidArr['type'] == 'webgame') {
                        $webgameInfoArrTmp = $webgame->getOneGameInfo($tv['itemid']);
                        $tmpArr = [
                            'id' => $tv['id'],
                            'posid' => $posid,
                            'itemid' => $tv['itemid'],
                            'weight' => $tv['weight'],
                            'start' => $tv['start'],
                            'end' => $tv['end'],
                            'name' => $webgameInfoArrTmp['name'],
                        ];
                    } else {
                        $webgameInfoArrTmp = $videoModel->getVideoInfoByVid($tv['itemid']);

                        if (empty($webgameInfoArrTmp)) {
                            $webgameInfoArrTmp = $webgame->getOneGameInfo($tv['itemid']);

                            $tmpArr['name'] = $webgameInfoArrTmp['name'];
                            $tmpArr['showPic'] = 'http://pic2015.ytqmx.com:82/2016/0930/17/1.jpg%21960.jpg';
                            if (intval($webgameInfoArrTmp['game_type']) === 1) {
                                $showVrPicArr = ImageHelper::path("vrgameimg", $tv['itemid'], $webgameInfoArrTmp['img_version'], "", false);
                                $tmpArr['showPic'] = isset($showVrPicArr['logo']) ? $showVrPicArr['logo'] : 'http://pic2015.ytqmx.com:82/2016/0930/17/1.jpg%21960.jpg';
                            } else if (intval($webgameInfoArrTmp['game_type']) === 0) {
                                $showPicArr = ImageHelper::path("webgameimg", $tv['itemid'], $webgameInfoArrTmp['img_version'], "", false);
                                $tmpArr['showPic'] = isset($showVrPicArr['logo']) ? $showVrPicArr['logo'] : 'http://pic2015.ytqmx.com:82/2016/0930/17/1.jpg%21960.jpg';
                            }
                        } else {
                            $tmpArr['name'] = $webgameInfoArrTmp[0]['videoname'];
                            $tmpArr['showPic'] = $webgameInfoArrTmp[0]['videologo'];
                        }
//                        var_dump($webgameInfoArrTmp);die;
                        $tmpArr['id'] = $tv['id'];
                        $tmpArr['posid'] = $tv['posid'];
                        $tmpArr['itemid'] = $tv['itemid'];
                        $tmpArr['weight'] = $tv['weight'];
                        $tmpArr['start'] = $tv['start'];
                    }
                    $recommendArr[$k]['tmp'][$n] = $tmpArr;
                }
            }
        }
        //序列化数组
        $recommendArrInfo = [];
        foreach ($recommendCount as $key => $value) {
            $recommendArrInfo[$key] = $recommendArr[$key];
        }

        $sortArr = [];
        $type = ['video', 'webgame', 'vrgame', 'mix'];
        $getSortByType = $recommendModel->getSortByType($type);

        foreach ($getSortByType as $gk => $gv) {
            foreach ($recommendSortArr as $rsk => $rsv) {
                if ($gv['posid'] == $rsv['posid']) {
                    continue;
                }
                if ($gv['type'] == $rsv['type']) {
                    $sortArr[$gk][] = $recommendSortArr[$rsk];
                }
            }
        }
//        echo '<pre>';print_r($recommendArrInfo);die;
        return view('admin.webgame.recommend', ['data' => $recommendArrInfo, 'getSortByType' => $getSortByType, 'sort' => $sortArr, 'posid' => $posid, 'desc' => $startPosidArr['desc'], 'code' => $code]);
    }
    /*
     * 推荐位的分类的banner添加页面
     */
    public function bannerRecommend(Request $request, $code)
    {
        $recommendModel = new OperateModel();
        $webgame = new WebgameModel();

        $videoModel = new MiddleModel();

        $recommendSortArr = $recommendModel->getPosIdsByStat(0);
        if ($code == '{code}') {
            $code = 'game-video-banner';
        }
        $startPosidArr = $recommendModel->getPosId($code);
        $posid = isset($startPosidArr['posid']) ? $startPosidArr['posid'] : 1;
        $recommendCount = [];
        for ($i = 1; $i < 10; $i++) {
            $recommendCount[$i] = [];
        }
        //获取正式表中数据
        $recommendArrAll = $recommendModel->getItemsByPosid($posid);
        $recommendArr = [];

        //获取平台的历史表中数据
        $recommendTmpArr = $recommendModel->getScheduleItemsByPosid($posid);

        foreach ($recommendArrAll as $rk => $rv) {
            $recommendArr[$rv['weight']]['posid'] = $posid;
            $recommendArr[$rv['weight']]['itemid'] = $rv['itemid'];
            $recommendArr[$rv['weight']]['weight'] = $rv['weight'];
            $recommendArr[$rv['weight']]['ts'] = $rv['ts'];

            if ($startPosidArr['type'] == 'webgame') {
                $webgameInfoArr = $webgame->getOneGameInfo($rv['itemid']);
                $recommendArr[$rv['weight']]['name'] = $webgameInfoArr['name'];
                $recommendArr[$rv['weight']]['showPic'] = 'http://pic2015.ytqmx.com:82/2016/0930/17/1.jpg%21960.jpg';
            } else {
                $webgameInfoArr = $videoModel->getVideoInfoByVid($rv['itemid']);
                $recommendArr[$rv['weight']]['name'] = isset($webgameInfoArr[0]['videoname']) ? $webgameInfoArr[0]['videoname'] : $rv['banner_url'];
                $recommendArr[$rv['weight']]['showPic'] = isset($webgameInfoArr[0]['videologo']) ? $webgameInfoArr[0]['videologo'] : $rv['banner_url'];
            }
        }

        //处理临时文件
        //              var_dump($recommendArr);die;
        foreach ($recommendCount as $k => $v) {
            if (isset($recommendArr[$k])) {
            } else {
                $recommendArr[$k] = $recommendCount[$k];
            }

            foreach ($recommendTmpArr as $tk => $tv) {
                $n = 0;
                if ($tv['weight'] == $k) {
                    if (isset($recommendArr[$k]['tmp'][$n]) && !empty($recommendArr[$k]['tmp'][$n])) {
                        $n = $n + 1;
                    }
                    if ($n > 1) {
                        continue;
                    }
                    //判断该推荐位是哪一个类型 ，现在只有3个标识类，分别是：video=>视频,vrgame=>VR游戏，webgame=>页游
                    if ($startPosidArr['type'] == 'webgame') {
                        $webgameInfoArrTmp = $webgame->getOneGameInfo($tv['itemid']);
                        $tmpArr = [
                            'id' => $tv['id'],
                            'posid' => $posid,
                            'itemid' => $tv['itemid'],
                            'weight' => $tv['weight'],
                            'start' => $tv['start'],
                            'end' => $tv['end'],
                            'name' => $webgameInfoArrTmp['name'],
                            'target_url' => $tv['target_url'],
                        ];
                    } else {
                        $webgameInfoArrTmp = $videoModel->getVideoInfoByVid($tv['itemid']);
                        $tmpArr = [
                            'id' => $tv['id'],
                            'posid' => $posid,
                            'itemid' => $tv['itemid'],
                            'weight' => $tv['weight'],
                            'start' => $tv['start'],
                            'end' => $tv['end'],
                            'name' => isset($webgameInfoArrTmp[0]['videoname']) ? $webgameInfoArrTmp[0]['videoname'] : 'banner',
                            'target_url' => isset($webgameInfoArrTmp[0]['target_url']) ? $webgameInfoArrTmp[0]['target_url'] : $tv['target_url'],
                            'showPic' => isset($webgameInfoArrTmp[0]['videologo']) ? $webgameInfoArrTmp[0]['videologo'] : 'http://pic2015.ytqmx.com:82/2016/0930/17/1.jpg%21960.jpg',
                        ];

                    }
                    $recommendArr[$k]['tmp'][$n] = $tmpArr;
                }
            }
        }

        //序列化数组
        $recommendArrInfo = [];
        foreach ($recommendCount as $key => $value) {
            $recommendArrInfo[$key] = $recommendArr[$key];
        }

        //获取banner分类
        $sortArr = [];
        $type = ['banner'];
        $getSortByType = $recommendModel->getSortByType($type);
        foreach ($getSortByType as $gk => $gv) {
            foreach ($recommendSortArr as $rsk => $rsv) {
                if ($gv['posid'] == $rsv['posid']) {
                    continue;
                }
                if ($gv['type'] == $rsv['type']) {
                    $sortArr[$gk][] = $recommendSortArr[$rsk];
                }
            }
        }

//        echo '<pre>';print_r($recommendArrInfo);print_r($sortArr);die;
        return view('admin.webgame.bannerRecommend', ['data' => $recommendArrInfo, 'getSortByType' => $getSortByType, 'sort' => $sortArr, 'posid' => $posid, 'desc' => $startPosidArr['desc']]);
    }

    /*
     * 推荐位的分类的banner添加页面
     */
    public function recommendMenuAdd(Request $request)
    {
        $recommendModel = new OperateModel();
        $webgame = new WebgameModel();

        $videoModel = new VideoModel();

        $sortArr = [];
        $type = [];
        $getSortByType = $recommendModel->getSortByType($type);
        $sortArr = $getSortByType;

        $videoSort = $videoModel->getVideoSortByConfig();
        $videoSortArr = [];
        foreach ($videoSort as $k => $v) {
            $ifHasBanner = $recommendModel->getSortByBannerPosid($v['code']);
            $videoSortArr[$k]['id'] = $v['id'];
            $videoSortArr[$k]['name'] = $v['name'];
            $videoSortArr[$k]['desc'] = $v['desc'];
            $videoSortArr[$k]['img'] = $v['img'];
            $videoSortArr[$k]['code'] = $v['code'];
            $videoSortArr[$k]['ifcode'] = isset($ifHasBanner['code']) ? $ifHasBanner['code'] : '';
        }

//        echo '<pre>';
        //        print_r($sortArr);die;
        return view('admin.webgame.recommendNav', ['getSortByType' => $getSortByType, 'sort' => $sortArr, 'videoSortArr' => $videoSortArr]);
    }

    /*
     * 发布按钮
     */
    public function recommendPublish(Request $request)
    {
        $recommendModel = new OperateModel();
        //根据posid同步数据到t_comment表中发布
        $posid = $request->input('posid');
        $recommendArr = $recommendModel->getScheduleItemsByPosid($posid);
        $result = [];
        foreach ($recommendArr as $k => $v) {
            $info['weight'] = $v['weight'];
            $itemid = $v['itemid'];
            $ret = $recommendModel->insItem($posid, $itemid, $info);
            if (!$ret) {
                $result[$k]['posid'] = $posid;
                $result[$k]['itemid'] = $itemid;
                $result[$k]['weight'] = $v['weight'];
            }
        }
        if (empty($result)) {
            return Library::output(0);
        }
        return Library::output(1, $result);
    }

    /*
     * 添加推荐位的分类位置信息
     */
    public function insPosByCode(Request $request)
    {
        $recommendModel = new OperateModel();
        $code = $request->input('code');
        $type = $request->input('type');
        $desc = $request->input('desc');
        $sortmark = $request->input('sortmark');
        $detailed = $request->input('detail');
        $resource = $request->input('showpic');
        if ($resource == '') {
            $resource = 'nopic';
        }

        //获取posid最大的数据
        $maxPosidInfo = $recommendModel->getMaxPosId();

        $inputArr = [
            'posid' => $maxPosidInfo['posid'] + 1,
            'type' => $type,
            'desc' => $desc,
            'sortmark' => $sortmark,
            'detailed' => $detailed,
            'showpic' => $resource,
        ];
        $ret = $recommendModel->insPosByCode($code, $inputArr);

        if (!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /**
     * 获取配置中的视频分类
     * @param Request $request
     * @return string
     */

    public function addVideoByConfig(Request $request)
    {
        $videoModel = new VideoModel();
        $operateModel = new OperateModel();
        $sortArr = $videoModel->getVideoSortByConfig();
        $info = [];
        $code = 'banner';
        foreach ($sortArr as $k => $v) {
            $code = $v['code'];
        }
    }

    /*
     * 添加推荐位的
     */
    public function addNewRecommend(Request $request)
    {
        $recommendModel = new OperateModel();
        $posid = $request->input('posid');
        $itemid = $request->input('qId');
        $weight = $request->input('weight');
        $start = $request->input('tmBegin');
        $end = $request->input('tmEnd');
        $insetArr = [
            'weight' => $weight,
            'start' => strtotime($start),
            'end' => strtotime($end),
        ];
        $ret = $recommendModel->insScheduleItem($posid, $itemid, $insetArr);
        if (!$ret) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /*
     * 获取系统配置信息接口
     */
    public function getSystemInfo()
    {
        $keyArr = array('videoCard', 'cpu', 'system', 'Direct', 'memory');
        $info = [];
        foreach ($keyArr as $v) {
            $config = Config::get("system." . $v);
            $videoCardArr = '';
            //$videoCardArr[1] = '';
            foreach ($config as $vv) {
                if ($videoCardArr == '') {
                    $videoCardArr = $vv;
                } else {
                    $videoCardArr = $videoCardArr . ',' . $vv;
                }
            }
            $info[$v] = $videoCardArr;
        }
        return $this->outputForPc(0, $info);
    }

    /*
     * 获取系统配置信息接口
     */
    public function getSystemInfo1()
    {
        $uploadDir = "../upload" . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR;
        $fileArr = array('videoCard', 'cpu', 'system', 'Direct', 'memory');
        $info = array();
        foreach ($fileArr as $fv) {
            $path = $uploadDir . $fv . '.xlsx';
            switch ($fv) {
                case "videoCard":
                    $videoCardInfo = $this->Importexcel($path);
                    $videoCardArr = '';
                    //$videoCardArr[1] = '';
                    foreach ($videoCardInfo as $vv) {
                        if ($videoCardArr == '') {
                            $videoCardArr = $vv[0];
                        } else {
                            $videoCardArr = $videoCardArr . ',' . $vv[0];
                        }
                    }
                    $info['videoCard'] = $videoCardArr;
                    break;
                case 'cpu':
                    $cpuInfo = $this->Importexcel($path);
                    $cpu = '';
                    foreach ($cpuInfo as $cv) {
                        if ($cpu == '') {
                            $cpu = $cv[0];
                        } else {
                            $cpu = $cpu . ',' . $cv[0];
                        }
                    }
                    $info['cpu'] = $cpu;
                    break;
                case 'system':
                    $systemInfo = $this->Importexcel($path);
                    $system = '';
                    foreach ($systemInfo as $sv) {
                        if ($system == '') {
                            $system = $sv[0];
                        } else {
                            $system = $system . ',' . $sv[0];
                        }
                    }
                    $info['system'] = $system;
                    break;
                case 'Direct':
                    $DirectInfo = $this->Importexcel($path);
                    $Direct = '';
                    foreach ($DirectInfo as $dv) {
                        if ($Direct == '') {
                            $Direct = $dv[0];
                        } else {
                            $Direct = $Direct . ',' . $dv[0];
                        }
                    }
                    $info['Direct'] = $Direct;
                    break;
                case 'memory':
                    $memoryInfo = $this->Importexcel($path);
                    $memory = '';
                    foreach ($memoryInfo as $mv) {
                        if ($memory == '') {
                            $memory = $mv[0];
                        } else {
                            $memory = $memory . ',' . $mv[0];
                        }
                    }
                    $info['memory'] = $memory;
                    break;
                default:

            }

        }
        if (empty($info)) {
            return Library::output(1);
        }

        return $this->outputForPc(0, $info);
    }

    /*
     * 添加游戏礼包领取码
     */
    public function addGiftCode(Request $request)
    {
        $webGame = new WebgameModel();
//      $data = array();
        //      for($i=0; $i<10; $i++) {
        //          $data[$i] = array(
        //              'codes' => md5(time() + $i),
        //          );
        //      }
        $data = array(20000021, 20000121, 20000221, 20000321, 20000421, 20000521, 20000621, 20000721, 20000821, 20000921);
        $gid = 1001;
        $appid = 1002;
        $serverid = 1;
//      echo '<pre>';
        //      print_r($data);
        $ret = $webGame->addGiftCodes($gid, $appid, $serverid, $data);
    }

    public function Importexcel($files)
    {

        $res = [];
        Excel::load($files, function ($reader) use (&$res) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
        });

        return $res;
    }

    /*
     * 上传图片的接口
     */
    public function uploadPic($gid, $uploadDir, $type = false)
    {
        // 创建用户的头像目录
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        $fileAllowExt = 'gif|jpg|jpeg|png|gif'; //限制上传图片的格式
        $fileAllowSize = 2 * 1024 * 1024; //限制最大尺寸是2MB
        //$submit = isset($_POST['submit']) ? $_POST['submit'] : 123;
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $fileName = $_FILES['file']['name'];
            $fileError = $_FILES['file']['error'];
            $fileType = $_FILES['file']['type'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];
            $fileExt = substr($fileName, strrpos($fileName, '.') + 1);
            $data['oldName'] = $fileName;
            $data['fileExt'] = $fileExt;
            $data['fileType'] = $fileType;
            switch ($fileError) {
                case 0:
                    $code = 0;
                    $data['msg'] = "文件上传成功!";
                    break;

                case 1:
                    $code = 2202;
                    $data['msg'] = "文件上传失败，文件大小" . $fileSize . "超过限制,允许上传大小2M";
                    break;

                case 3:
                    $code = 2203;
                    $data['msg'] = "上传失败，文件只有部份上传!";
                    break;

                case 4:
                    $code = 2204;
                    $data['msg'] = "上传失败，文件没有被上传!";
                    break;

                case 5:
                    $code = 2205;
                    $data['msg'] = "文件上传失败，文件大小为0!";
                    break;
            }
            if (stripos($fileAllowExt, $fileExt) === false) {
                $code = 2206;
                $data['msg'] = "该文件扩展名不允许上传";
            }
            if ($fileSize > $fileAllowSize) {
                $code = 2202;
                $data['msg'] = "文件大小超过限制,只能上传2M的文件!";
            }
            if ($code !== 0) {
                $data['msg'] = $data['msg'];
                return Library::output($code, $data);
            }
            if (file_exists($uploadDir)) {
                $fileNewName = substr(md5($fileName), 0, 12) . time() . '.' . $fileExt;
                if ($type == 'gift') {
                    $fileNewName = substr(md5($gid), 0, 12);
                }

                //$fileNewName = "credentials.png";
                $data['fileNewName'] = $fileNewName;
                $fileSavePath = $uploadDir . $fileNewName;
                move_uploaded_file($fileTmpName, $fileSavePath);
                return Library::output($code, $data);
            }
        }
        return Library::output(1);
    }

    /*
     * 上传excel表文件
     */
    public function uploadExcel($gid, $uploadDir, $type = false)
    {
        // 创建用户的头像目录
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        $fileAllowExt = 'csv|xlsx'; //限制上传图片的格式
        $fileAllowSize = 2 * 1024 * 1024; //限制最大尺寸是2MB
        //$submit = isset($_POST['submit']) ? $_POST['submit'] : 123;
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $fileName = $_FILES['file']['name'];
            $fileError = $_FILES['file']['error'];
            $fileType = $_FILES['file']['type'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];
            $fileExt = substr($fileName, strrpos($fileName, '.') + 1);
            $data['oldName'] = $fileName;
            $data['fileExt'] = $fileExt;
            $data['fileType'] = $fileType;
            switch ($fileError) {
                case 0:
                    $code = 0;
                    $data['msg'] = "文件上传成功!";
                    break;

                case 1:
                    $code = 2202;
                    $data['msg'] = "文件上传失败，文件大小" . $fileSize . "超过限制,允许上传大小2M";
                    break;

                case 3:
                    $code = 2203;
                    $data['msg'] = "上传失败，文件只有部份上传!";
                    break;

                case 4:
                    $code = 2204;
                    $data['msg'] = "上传失败，文件没有被上传!";
                    break;

                case 5:
                    $code = 2205;
                    $data['msg'] = "文件上传失败，文件大小为0!";
                    break;
            }
            if (stripos($fileAllowExt, $fileExt) === false) {
                $code = 2206;
                $data['msg'] = "该文件扩展名不允许上传";
            }
            if ($fileSize > $fileAllowSize) {
                $code = 2202;
                $data['msg'] = "文件大小超过限制,只能上传2M的文件!";
            }
            if ($code !== 0) {
                $data['msg'] = $data['msg'];
                return Library::output($code, $data);
            }
            if (file_exists($uploadDir)) {
                $fileNewName = substr(md5($fileName), 0, 12) . time() . '.' . $fileExt;
                if ($type == 'gift') {
                    $fileNewName = substr(md5($gid), 0, 12);
                }

                //$fileNewName = "credentials.png";
                $data['fileNewName'] = $fileNewName;
                $fileSavePath = $uploadDir . $fileNewName;
                move_uploaded_file($fileTmpName, $fileSavePath);
                return Library::output($code, $data);
            }
        }
        return Library::output(1);
    }
}
