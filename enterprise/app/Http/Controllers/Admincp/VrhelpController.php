<?php
namespace App\Http\Controllers\Admincp;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\ActModel;
use App\Models\AdmincpModel;
use App\Models\CacheModel;
use App\Models\CdkDBModel;
use App\Models\DeveloperModel;
use App\Models\DevModel;
use App\Models\GameModel;
use App\Models\ThreeDBBDBModel;
use App\Models\VersionModel;
use App\Models\VideoModel;
use App\Models\WebgameModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class VrhelpController extends Controller
{
    private $perPage = 20;
    public function __construct()
    {
        $this->middleware("vrauth:jump:admincp", ['only' => ["index", "top", "video", "vrgame", "vrgameEdit", "vrgameVersion", "webgame", "webgameNews", "position", "client", "clientup", "price", "cdk", "cdkDown", "threedbb", "dbbRegInfo", "dbbinfo", "developer"]]);
        $this->middleware("vrauth:json:admincp", ['only' => ["transcoding"]]);

    }

    public function index(Request $request)
    {
        $userInfo = $request->userinfo;
        if ($userInfo['nextPath']) {
            return redirect($userInfo['nextPath']);
        } else {
            return redirect('index/help', 302, [], true);
        }
    }

    public function video(Request $request)
    {
        $userInfo   = $request->userinfo;
        $class_id   = intval($request->input("choose"));
        $curClass   = $class_id ? $class_id : 0;
        $searchText = trim($request->input('search'));
        $searchText = $searchText ? $searchText : '';
        $videoModel = new VideoModel;
        $videos     = $videoModel->getDevVideoClassPage($curClass, $searchText);
        return view('admincp.vrhelp.video', ['cur' => 'vrhelp', 'user' => $userInfo, 'path' => 'video', 'data' => $videos, 'curClass' => $curClass, 'searchText' => $searchText]);
    }

    public function vrgame(Request $request)
    {
        $userInfo = $request->userinfo;
        $choose   = $request->input('choose');
        if ($choose == null) {
            $choose = -1;
        } else {
            $choose = intval($choose);
        }

        $support = $request->input('support');
        if ($support == null) {
            $support = 0;
        } else {
            $support = intval($support);
        }
        $searchText = trim($request->input('search'));
        $searchText = $searchText ? $searchText : '';
        $gameModel  = new GameModel;
        $data       = $gameModel->gameByPage(1, $choose, $searchText, $support);
        return view('admincp.vrhelp.vrgame', ['cur' => 'vrhelp', 'path' => 'vrgame', 'user' => $userInfo, 'data' => $data, 'choose' => $choose, 'support' => $support, 'searchText' => $searchText]);
    }

    public function vrgameEdit(Request $request, $id = 0)
    {
        $userInfo = $request->userinfo;
        return view('admincp.vrhelp.vrgame_edit', ['cur' => 'vrhelp', 'user' => $userInfo, 'path' => 'vrgame', 'id' => $id]);
    }

    public function vrgameVersion(Request $request, $appid)
    {
        $userInfo     = $request->userinfo;
        $dev          = new DevModel;
        $versionModel = new VersionModel;
        $data         = $versionModel->getVersions($appid, [], 10);

        $gameInfo = $dev->getWebgameInfo($appid);
        $gameName = $gameInfo['name'];

        return view('admincp.vrhelp.vrgame_version', ['cur' => 'vrhelp', 'path' => 'vrgame', 'user' => $userInfo, 'data' => $data, 'appid' => $appid, 'gameName' => $gameName]);
    }

    public function vrgameSubVersion(Request $request)
    {
        $appid       = $request->input('appid');
        $versionName = $request->input('version_name');

        $versionModel = new VersionModel;
        $data         = $versionModel->getSubVersions($appid, $versionName);
        return Library::output(0, $data);
    }

    public function developer(Request $request)
    {
        $userInfo = $request->userinfo;
        $choose   = $request->input('choose');
        if ($choose == null) {
            $choose = -1;
        } else {
            $choose = intval($choose);
        }

        $searchText     = trim($request->input('search'));
        $searchText     = $searchText ? $searchText : '';
        $developerModel = new DeveloperModel;
        $data           = $developerModel->getDevelopers(['choose' => $choose, 'search' => $searchText]);
        return view('admincp.vrhelp.developer', ['cur' => 'vrhelp', 'path' => 'developer', 'user' => $userInfo, 'data' => $data, 'choose' => $choose, 'search' => $searchText]);
    }

    public function price(Request $request)
    {
        $userInfo   = $request->userinfo;
        $searchText = trim($request->input('search'));
        $searchText = $searchText ? $searchText : '';
        $gameModel  = new GameModel;
        $data       = $gameModel->gameOnlieByPage(1, $searchText);
        return view('admincp.vrhelp.price', ['cur' => 'vrhelp', 'path' => 'price', 'user' => $userInfo, 'data' => $data, 'searchText' => $searchText]);
    }

    public function cdk(Request $request)
    {
        $userInfo   = $request->userinfo;
        $searchText = trim($request->input('search'));
        $searchText = $searchText ? $searchText : '';
        $cdkModel   = new CdkDBModel;
        $data       = $cdkModel->getBatch($searchText);
        return view('admincp.vrhelp.cdk', ['cur' => 'vrhelp', 'path' => 'cdk', 'user' => $userInfo, 'data' => $data, 'searchText' => $searchText]);
    }

    public function threedbb(Request $request)
    {
        $userInfo = $request->userinfo;
        $choose   = $request->input('choose');
        if ($choose == null) {
            $choose = -1;
        } else {
            $choose = intval($choose);
        }
        $searchText = trim($request->input('search'));
        $searchText = $searchText ? $searchText : '';
        $dbbModel   = new ThreeDBBDBModel();
        $data       = $dbbModel->dbbByPage(0, $choose, $searchText);
        return view('admincp.vrhelp.threedbb', ['cur' => 'vrhelp', 'path' => 'dbb', 'user' => $userInfo, 'data' => $data, 'choose' => $choose, 'searchText' => $searchText]);
    }

    /**
     * 3D播播用户注册数据中心统计数据展示
     * [vrGameData description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function dbbRegInfo(Request $request)
    {
        $userInfo = $request->userinfo;
        $start    = $request->input('start');
        $end      = $request->input('end');
        $action   = $request->input('action');

        $start = $start != '' ? $start : '2017-03-10';
        $end   = $end != '' ? $end : date('Y-m-d H:i:s', time());

        $dbbModel = new ThreeDBBDBModel();
        $data     = $dbbModel->get3DBBRegData($start, $end);
        $lastRet  = [];
        return view('admincp.stat.dbbreginfo', ['cur' => 'stat', 'user' => $userInfo, 'path' => 'dbbreginfo', 'retDate' => $data, 'lastRet' => $lastRet, 'action' => $action, 'start' => $start, 'end' => $end]);
    }

    public function cdkDown(Request $request)
    {
        $userInfo    = $request->userinfo;
        $batchno     = trim($request->input('batchno'));
        $cdkModel    = new CdkDBModel;
        $data        = $cdkModel->getCdkByBatch($batchno, true);
        $savename    = "VRCDK-" . $batchno . "-" . date("YmdHis");
        $file_type   = "vnd.ms-excel";
        $file_ending = "xls";
        header("Content-Type: application/$file_type;charset=utf8");
        header("Content-Disposition: attachment; filename=" . $savename . ".$file_ending");
        header("Pragma: no-cache");
        $str = "CDK\t游戏ID\t时间\n";
        foreach ($data as $cdk) {
            $str .= $cdk['cdk'] . "\t" . $cdk['itemid'] . "\t" . $cdk['ctime'] . "\n";
        }
        echo $str;
    }

    public function webgame(Request $request)
    {
        $userInfo = $request->userinfo;
        $choose   = $request->input('choose');
        if ($choose == null) {
            $choose = -1;
        } else {
            $choose = intval($choose);
        }
        $searchText = trim($request->input('search'));
        $searchText = $searchText ? $searchText : '';
        $gameModel  = new GameModel;
        $data       = $gameModel->gameByPage(0, $choose, $searchText);
        return view('admincp.vrhelp.webgame', ['cur' => 'vrhelp', 'path' => 'webgame', 'user' => $userInfo, 'data' => $data, 'choose' => $choose, 'searchText' => $searchText]);
    }

    public function webgameNews(Request $request, $appid)
    {
        $userInfo = $request->userinfo;
        $webGame  = new WebgameModel;
        $data     = $webGame->gameNewsByPage($appid);

        $gameInfo = $webGame->getOneGameInfo($appid);
        $gameName = $gameInfo['name'];
        $tps      = Config::get("category.webgame_news");

        return view('admincp.vrhelp.webgamenews', ['cur' => 'vrhelp', 'path' => 'webgame', 'user' => $userInfo, 'data' => $data, 'tps' => $tps, 'appid' => $appid, 'gameName' => $gameName]);
    }

    public function position(Request $request)
    {
        $userInfo     = $request->userinfo;
        $choose       = trim($request->input('choose'));
        $admincpModel = new AdmincpModel;
        $data         = $admincpModel->topPostion($choose);
        return view('admincp.vrhelp.position', ['cur' => 'vrhelp', 'user' => $userInfo, 'path' => 'position', 'choose' => $choose, 'data' => $data]);
    }

    /**
     * 客户端完整包控制
     * [client description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function client(Request $request)
    {
        $userInfo     = $request->userinfo;
        $AdmincpModel = new AdmincpModel();
        $versionInfo  = $AdmincpModel->getClientVersion();
        $publish      = $this->alreadyPublic();
        return view('admincp.vrhelp.clientVersion', ['cur' => 'vrhelp', 'user' => $userInfo, 'path' => 'client', 'data' => $versionInfo, 'publish' => $publish]);
    }

    public function alreadyPublic()
    {
        $CacheModel = new CacheModel();
        $statusArr  = [
            1 => 'latest',
            2 => 'stable',
        ];
        $versionInfo = '';
        foreach ($statusArr as $k => $v) {
            $versionInfo[] = $CacheModel->getClientVersionInfo($v);
        }
        return $versionInfo;
    }

    public function alreadyPublicOnline()
    {
        $CacheModel  = new CacheModel();
        $statusJson  = $CacheModel->getOnlinePreVersion();
        $versionInfo = '';

        return $statusJson;
    }

    /**
     * 客户端的在线版本更新
     * [clientup description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function clientup(Request $request)
    {
        $userInfo     = $request->userinfo;
        $AdmincpModel = new AdmincpModel();
        $versionInfo  = $AdmincpModel->getUpOnlineVersion();
        $publish      = $this->alreadyPublicOnline();

        return view('admincp.vrhelp.clientUpdateOnline', ['cur' => 'vrhelp', 'user' => $userInfo, 'path' => 'client', 'data' => $versionInfo, 'publish' => $publish]);
    }

    public function top(Request $request)
    {
        $userInfo = $request->userinfo;
        $posid    = $request->input('choose');

        $groups = Config::get("admincp.vrhelp_group");

        $defaultContentTp = '';
        $defaultName      = '';
        $defaultGroup     = '';
        $admincpModel     = new AdmincpModel;
        $poses            = $admincpModel->topPostion('', 0);
        $posGroup         = [];
        foreach ($poses as $key => $value) {
            if (!isset($posGroup[$value['tp']])) {
                $posGroup[$value['tp']] = [];
            }
            $posGroup[$value['tp']][] = $value;
            if ($key == 0 && !$posid && !$defaultContentTp) {
                $posid            = $value['posid'];
                $defaultContentTp = $value['content_tp'];
                $defaultName      = $value['name'];
                $defaultGroup     = $value['tp'];
            } else if ($posid == $value['posid'] && !$defaultContentTp) {
                $defaultContentTp = $value['content_tp'];
                $defaultName      = $value['name'];
                $defaultGroup     = $value['tp'];
            }
        }

        $defaultContentTp = $defaultContentTp ? $defaultContentTp : "video";
        $data             = $admincpModel->topPostionData($posid);

        $appids   = [];
        $videoids = [];
        foreach ($data as $key => $value) {
            if ($value['tp'] == 'webgame' || $value['tp'] == 'vrgame') {
                $appids[] = $value['itemid'];
            } else if ($value['tp'] == 'video') {
                $videoids[] = $value['itemid'];
            }
        }
        $games   = [];
        $appData = $admincpModel->getGameByIds($appids);
        foreach ($appData as $value) {
            $games[$value['appid']]         = $value;
            $imgTp                          = $value['game_type'] == 0 ? 'webgame' : 'vrgame';
            $resInfo                        = ImageHelper::url($imgTp, $value['appid'], $value['img_version'], null, false);
            $games[$value['appid']]['logo'] = $resInfo['logo'];
        }
        $videos = [];

        $videoData = $admincpModel->getVideoByIds($videoids);
        foreach ($videoData as $value) {
            $videos[$value['video_id']] = $value;
        }
        return view('admincp.vrhelp.top', ['cur' => 'vrhelp', 'user' => $userInfo, 'path' => 'top', 'groups' => $groups, 'posGroup' => $posGroup, 'defaultGroup' => $defaultGroup, 'posid' => $posid, 'data' => $data, 'games' => $games, 'videos' => $videos, 'content_tp' => $defaultContentTp, 'posName' => $defaultName]);
    }

    public function search(Request $request)
    {
        $tp    = $request->input('tp');
        $query = $request->input('q');

        $admincpModel = new AdmincpModel;
        $res          = $admincpModel->searchContent($tp, $query);
        $arr          = [];
        if ($res) {
            $arr = $res;
        }
        $out = ['results' => $arr];
        return json_encode($out);
    }

    public function switchWeight(Request $request)
    {
        $dragId = intval($request->input('drag'));
        $dropId = intval($request->input('drop'));

        $admincpModel = new AdmincpModel;
        $ret          = $admincpModel->topPostionWeight($dragId, $dropId);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function dataRecSave(Request $request)
    {
        $id      = intval($request->input('rec_id'));
        $posid   = intval($request->input('rec_posid'));
        $item_id = intval($request->input('rec_item_id'));
        $tp      = $request->input('rec_item_tp');

        $info         = ['posid' => $posid, 'itemid' => $item_id, 'tp' => $tp];
        $admincpModel = new AdmincpModel;
        $ret          = $admincpModel->updateTopRecommend($id, $info);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function dataDelRec(Request $request)
    {
        $id           = intval($request->input('id'));
        $admincpModel = new AdmincpModel;
        $ret          = $admincpModel->delTopRecommend($id);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function dbbinfo(Request $request)
    {
        $userInfo = $request->userinfo;
        $choose   = trim($request->input('choose', "3dbb_index"));
        $actModel = new ActModel;
        $data     = $actModel->actGetInfoByPosition($choose, false);
        //var_dump($data);
        return view('admincp.vrhelp.dbbinfo', ['cur' => 'vrhelp', 'user' => $userInfo, 'path' => 'dbbinfo', 'choose' => $choose, 'data' => $data]);
    }

    public function transcoding(Request $request)
    {
        $video_id   = intval($request->input('video_id'));
        $videoModel = new VideoModel;
        $videoInfo  = $videoModel->getDevVideoById($video_id);
        if ($videoInfo['video_link_tp'] == 1 && $videoInfo['video_trans'] == '') {
            $fileName = str_replace('http://netctvideo.vronline.com/', '', $videoInfo['video_link']);
            $ret      = ImageHelper::videoTranscoding($fileName);
            if (strlen($ret) > 10) {
                $videoModel->saveDevVideoInfo($video_id, ['video_trans' => $ret]);
                return Library::output(0, ['id' => $ret]);
            } else {
                return Library::output(1);
            }
        } else {
            return Library::output(1);
        }
    }

    public function transcodingStat(Request $request)
    {
        $persistentId = trim($request->input('persistentId'));
        $videoModel   = new VideoModel;
        $videoModel->updateTrans($persistentId);
        return Library::output(0);
    }
}
