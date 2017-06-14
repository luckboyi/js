<?php
namespace App\Http\Controllers;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\CookieModel;
use App\Models\DeveloperModel;
use App\Models\GameModel;
use App\Models\GameRecordModel;
use App\Models\SolrModel;
use App\Models\VideoModel;
use App\Models\VideoRecordModel;
use App\Models\VrhelpModel;
use App\Models\WebgameModel;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Illuminate\Http\Request;

class VrhelpController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:0", ['only' => ["index", "home", "game", "video", "videoList", "searchGame", "searchVideo", "vrfrom3d", "user", "drive", "download", "left"]]);
        $this->middleware("vrauth:0:json", ['only' => ["order", "consume", "search", "gameInfo", "gameDetail", "videoListInterface", "addGameHistory"]]);
        $this->solr = new SolrModel();
    }

    public function index(Request $request)
    {
        $userInfo = $request->userinfo;

        $webgameModel = new WebgameModel;
        $res          = $webgameModel->getGameLog($userInfo['uid'], 8, 1);
        $history      = [];
        if (is_array($res) && !empty($res)) {
            foreach ($res as $value) {
                $history[] = ['appid' => $value['appid'], 'name' => $value['appname'], 'time' => $value['ltime']];
            }
        }
        return view('vrhelp.left', ['user' => $userInfo, 'left' => true, 'history' => json_encode($history)]);
    }

    public function home(Request $request)
    {
        $userInfo = $request->userinfo;
        $posCode  = [
            'index-top-slider' => 5,
            'index-game-event' => 5,
            'index-hot-game'   => 19,
            'index-hot-video'  => 13,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $this->solr->getTop($code, $num, 'vrhelp');
        }
        foreach ($recommend['index-top-slider']['data'] as $key => $value) {
            $slider_image[] = static_image($value['image']['cover']);
            $slider_url[]   = $value['target_url'];
        }
        $slider_image_str = '["' . implode('","', $slider_image) . '"]';
        $slider_url_str   = '["' . implode('","', $slider_url) . '"]';
        $topGame          = $this->solr->search('vrgame', ['orderBy' => 'play desc', 'limit' => [0, 24]]);
        $topVideo         = $this->solr->search('video', ['orderBy' => 'play desc', 'limit' => [0, 7]]);
        // dump($slider_image);exit;
        return view('vrhelp.home', ['user' => $userInfo, 'recommend' => $recommend, 'slider_image' => $slider_image_str, 'slider_url' => $slider_url_str, 'topGame' => $topGame, 'topVideo' => $topVideo]);
    }

    public function game(Request $request)
    {
        $userInfo = $request->userinfo;
        // dump($request);
        $posCode = [
            'vrgame-slider' => 8,
            'vrgame-top'    => 5,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $this->solr->getTop($code, $num, 'vrhelp');
        }

        return view('vrhelp.game', ['user' => $userInfo, 'recommend' => $recommend]);
    }

    /**
     * 3d播播视频首页
     * @var [type]
     */
    public function video(Request $request)
    {
        $userInfo = $request->userinfo;
        $posCode  = [
            'tdbobo-banner'      => 6,
            'tdbobo-hot-label'   => 10,
            'tdbobo-hot-video'   => 12,
            'tdbobo-oneweek'     => 12,
            'thbobo-series'      => 19,
            'thbobo-documentary' => 19,
            'thbobo-testlink'    => 1,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $this->solr->getTop($code, $num);
        }
        // dump($recommend['thbobo-testlink']['data']);exit;
        // dump([]);exit;
        return view('vrhelp.video', ['user' => $userInfo, 'recommend' => $recommend]);
    }

    /**
     * 视频列表页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function videoList(Request $request)
    {
        $userInfo = $request->userinfo;
        $posCode  = [
            'tdbobo-banner' => 4,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $this->solr->getTop($code, $num);
        }

        // var_dump($recommend['tdbobo-banner']['data']);exit;
        return view('vrhelp.video_list', ['user' => $userInfo, 'recommend' => $recommend]);
    }

    /**
     * 游戏搜索
     * @return [type] [description]
     */
    public function searchGame(Request $request)
    {
        $userInfo = CookieModel::checkLogin();
        $uid      = isset($userInfo['uid']) && $userInfo['uid'] ? $userInfo['uid'] : 0;

        $userInfo = $request->userinfo;
        $name     = trim($request->input('name'));
        $page     = (int) $request->input('page');
        $params   = array();
        if (!empty($name)) {
            $params['name'] = $name;
        }

        if ($page > 0) {
            $params['limit'] = [($page - 1) * 20, 20];
        } else {
            $params['limit'] = [0, 20];
        }
        $solrModel = new SolrModel;
        $data      = $solrModel->search('vrgame', $params, true, $total);
        // var_dump($data);exit;
        return view('vrhelp.searchGame', ['user' => $userInfo, 'vrgame' => $data, 'total' => $total, 'name' => $name, 'page' => $page, 'uid' => $uid]);
    }
    /**
     * 视频搜索
     * @return [type] [description]
     */
    public function searchVideo(Request $request)
    {
        $userInfo = $request->userinfo;
        $name     = trim($request->input('name'));
        $page     = (int) $request->input('page');
        $params   = array();
        if (!empty($name)) {
            $params['name'] = $name;
        }

        if ($page > 0) {
            $params['limit'] = [($page - 1) * 20, 20];
        } else {
            $params['limit'] = [0, 20];
        }
        $solrModel = new SolrModel;
        $data      = $solrModel->search('video', $params, true, $total);
        $hotWord   = $this->solr->getTop('hot-word', 10);
        return view('vrhelp.searchVideo', ['user' => $userInfo, 'hotWord' => $hotWord, 'video' => $data, 'total' => $total, 'name' => $name, 'page' => $page]);
    }

    public function vrfrom3d(Request $request)
    {
        $userInfo = $request->userinfo;
        return view('vrhelp.3dvr', ['user' => $userInfo]);
    }

    public function user(Request $request)
    {
        $userInfo = $request->userinfo;

        $accountModel = new AccountCenter();
        $res          = $accountModel->info($userInfo['uid'], $userInfo['token']);
        if (!$res || !isset($res['data']) || !isset($res['data']['bindmobile'])) {
            return redirect('/404', 302, [], true);
        }
        $mobile = $res['data']['bindmobile'] ? $res['data']['bindmobile'] : "";

        $userInfo['mobile'] = $mobile ? str_replace(substr($mobile, 3, 4), "****", $mobile) : '***********';
        return view('vrhelp.user', ['usercenter' => 1, 'user' => $userInfo, 'mobile' => $mobile]);
    }

    public function search(Request $request)
    {
        $userInfo = $request->userinfo;
        $tp       = $request->input("tp");
        $device   = (int) $request->input("device");
        $category = (int) $request->input("category");
        $sort     = $request->input("sort");
        $page     = (int) $request->input("page");

        if ($device) {
            $params['support'] = $device;
        }
        if ($category) {
            $params['category'] = $category;
        }
        if ($sort) {
            $params['orderBy'] = str_replace(['size', 'time', 'up', 'down'], ['client_size', 'publish_date', 'asc', 'desc'], $sort);
        }
        if ($page > 0) {
            $params['limit'] = [($page - 1) * 20, 20];
        }
        $solrModel = new SolrModel;
        $data      = $solrModel->search($tp, $params, true, $total);
        return Library::output(0, ['user' => $userInfo, 'data' => $data, 'total' => $total]);
    }

    public function gameInfo(Request $request, $id)
    {
        $userInfo  = $request->userinfo;
        $gameModel = new GameModel();
        $row       = $gameModel->getGameById($id);
        return Library::output(0, $row);
    }

    /**
     * 游戏单页
     * @param  Request $request [description]
     * @param  [type]  $id      游戏的appid
     * @return [type]           [description]
     */
    public function gameDetail(Request $request, $id)
    {
        $userInfo  = $request->userinfo;
        $gameModel = new GameModel();
        $game      = $gameModel->getGameById($id);
        //判断游戏存在
        if (empty($game)) {
            return Library::output(1, 'null');
        }

        $detail = [];

        $detail['name']          = $game['name'];
        $detail['content']       = $game['content'];
        $detail['play']          = $game['play'];
        $detail['mini_device']   = $game['mini_device'];
        $detail['recomm_device'] = $game['recomm_device'];
        $detail['img']           = ImageHelper::getUrl("vrgameimg", ['id' => $id, 'version' => $game['img_version'], 'img_slider' => $game['img_slider'], 'publish' => false]);

        //只取第一分类
        $fc              = explode(',', $game['first_class']);
        $detail['class'] = Config::get('vrgame.class.' . $fc[0]);
        //配件
        if ($game['mountings']) {
            $mo = explode(',', $game['mountings']);
            foreach ($mo as $key => $value) {
                $mo[$key] = Config::get('category.vr_mountings.' . $value);
            }
        }

        $detail['accessories'] = isset($mo) ? $mo : [];
        //设备
        $su = explode(',', $game['support']);
        foreach ($su as $key => $value) {
            $su[$key] = Config::get('vrgame.support_device.' . $value);
        }
        $detail['support']     = $game['support'];
        $detail['equipment']   = $su;
        $detail['size']        = gameSize($game['client_size']);
        $detail['lang']        = $game['language'];
        $detail['product_com'] = $game['product_com'];
        $detail['issuing_com'] = $game['issuing_com'];

        if (!isset($game['sell'])) {
            $game['sell'] = 0;
        }
        if ($game['sell'] == 0) {
            $isBuy  = 1;
            $isFree = 1;
        } else {
            $isFree       = 0;
            $webgameModel = new WebgameModel;
            $gameLog      = $webgameModel->getOneGameLog($userInfo['uid'], $id);
            if (!$gameLog) {
                $isBuy = 0;
            } else {
                $isBuy = 1;
            }
        }

        $detail['isbuy'] = $isBuy;
        $detail['price'] = $game['sell'];

        return Library::output(0, $detail);
    }

    public function videoDetail(Request $request, $id)
    {
        $VideoModel = new VideoModel();
        $data       = $VideoModel->getVideoById($id);
        return Library::output(0, $data);
    }

    /**
     * 视频列表页获取数据接口
     * @return [type] [description]
     */
    public function videoListInterface(Request $request)
    {
        $userInfo = $request->userinfo;

        $type = $request->input("type");
        $sort = $request->input("sort");
        $page = (int) $request->input("page");
        if (empty($type)) {
            $type = '30101';
        }

        if (empty($page)) {
            $page = 1;
        }

        $params['category'] = $type;

        switch ($sort) {
            case '1':
                $params['orderBy'] = 'play desc';
                break;
            case '2':
                $params['orderBy'] = 'score desc';
                break;
            case '3':
                $params['orderBy'] = 'time desc';
                break;

            default:
                $params['orderBy'] = 'play desc';
                break;
        }
        if ($page > 0) {
            $params['limit'] = [($page - 1) * 20, 20];
        }
        // var_dump($params);exit;
        $solrModel         = new SolrModel;
        $videoList         = $solrModel->search('video', $params, true, $total);
        $data['videoList'] = $videoList;
        $data['total']     = $total;
        return Library::output(0, $data);
    }

    public function order(Request $request)
    {
        $userinfo = $request->userinfo;
        $uid      = $userinfo['uid'];
        $token    = $userinfo['token'];
        $page     = $request->input("page", 1);
        $len      = 10;

        $accountCenter = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $orders        = $accountCenter->getUserOrders($uid, $token, 0, $page, $len);
        if (!$orders || !isset($orders['code']) || $orders['code'] != 0 || !isset($orders['data']) || !$orders['data']) {
            $code = isset($orders['code']) ? $orders['code'] : 1;
            $msg  = isset($orders['msg']) ? $orders['msg'] : null;
            return Library::output($code, null, $msg);
        }
        $record = isset($orders['data']['orders']) ? $orders['data']['orders'] : [];
        $ids    = [];
        for ($i = 0; $i < count($record); $i++) {
            $ids[] = $record[$i]['appid'];
        }
        if ($ids) {
            $webgameModel = new WebgameModel;
            $gameinfo     = $webgameModel->getMultiGameName($ids, 1);
            for ($i = 0; $i < count($orders['data']['orders']); $i++) {
                $orders['data']['orders'][$i]['appname'] = $gameinfo[$orders['data']['orders'][$i]['appid']];
            }
        }
        return Library::output(0, $orders['data']);
    }

    public function consume(Request $request)
    {
        $userinfo = $request->userinfo;
        $uid      = $userinfo['uid'];
        $token    = $userinfo['token'];
        $page     = $request->input("page", 1);
        $len      = 10;

        $accountCenter = new AccountCenter(Config::get("common.uc_appid"), Config::get("common.uc_appkey"), Config::get("common.uc_paykey"));
        $orders        = $accountCenter->getUserOrders($uid, $token, 9, $page, $len);
        if (!$orders || !isset($orders['code']) || $orders['code'] != 0 || !isset($orders['data']) || !$orders['data']) {
            $code = isset($orders['code']) ? $orders['code'] : 1;
            $msg  = isset($orders['msg']) ? $orders['msg'] : null;
            return Library::output($code, null, $msg);
        }
        $record = isset($orders['data']['orders']) ? $orders['data']['orders'] : [];
        $ids    = [];
        for ($i = 0; $i < count($record); $i++) {
            $ids[] = $record[$i]['appid'];
        }
        if ($ids) {
            $webgameModel = new WebgameModel;
            $gameinfo     = $webgameModel->getMultiGameName($ids, 1);
            for ($i = 0; $i < count($orders['data']['orders']); $i++) {
                $orders['data']['orders'][$i]['appname'] = $gameinfo[$orders['data']['orders'][$i]['appid']];
            }
        }
        return Library::output(0, $orders['data']);
    }

    public function loginTop()
    {
        header("Access-Control-Allow-Origin:*");
        $res      = $this->solr->getTop('login-bg', 1, 'vrhelp');
        $loginTop = $res['data'][0];
        $out      = ['logo' => static_image($loginTop['image']['cover']), 'link' => $loginTop['target_url']];
        return Library::output(0, $out);
    }

    //视频记录
    public function videoRecord(Request $request, $uid, $vid)
    {
        $record = new VideoRecordModel();
        $res    = $record->addRecord($uid, $vid);
        // dump($res);
        return Library::output(0, $res);
    }

    //游戏记录
    public function gameRecord(Request $request, $uid, $gid, $type)
    {
        $record = new GameRecordModel();
        $res    = $record->addRecord($uid, $gid, $type);
        // dump($res);
        return Library::output(0, $res);
    }

    //游戏驱动
    public function drive(Request $request, $did)
    {
        if (!is_numeric($did) || $did < 1 || $did > 6) {
            $did = 1;
        }
        $userInfo = $request->userinfo;
        // dump($userInfo);
        $params['support'] = $did;
        $params['limit']   = [0, 6];
        $solrModel         = new SolrModel;
        $data              = $solrModel->search('vrgame', $params, true, $total);
        $cfg               = Config::get("category.vr_device");

        return view('vrhelp.drive', ['user' => $userInfo, 'recommend' => $data, 'drive' => $cfg[$did]]);
    }

    public function download(Request $request)
    {
        $userInfo     = $request->userinfo;
        $webgameModel = new WebgameModel;
        $res          = $webgameModel->getGameLog($userInfo['uid'], 8, 1);
        $history      = [];
        if (is_array($res) && !empty($res)) {
            foreach ($res as $value) {
                $history[] = ['appid' => $value['appid'], 'name' => $value['appname'], 'time' => $value['ltime']];
            }
        }
        return view('vrhelp.download', ['user' => $userInfo, 'history' => $history]);
    }

    public function addGameHistory(Request $request)
    {
        $userInfo = $request->userinfo;
        $gameId   = $request->input('game_id');
        $action   = $request->input('action');
        if ($action == 'install') {
            $webgameModel = new WebgameModel;
            $gameInfo     = $webgameModel->getOneGameInfo($gameId);
            $webgameModel->addGameLog($userInfo['uid'], $gameId, 0, ['game_type' => 1, 'appname' => $gameInfo['name']]);
        }

        $res     = $webgameModel->getGameLog($userInfo['uid'], 8, 1);
        $history = [];
        if (is_array($res) && !empty($res)) {
            foreach ($res as $value) {
                $history[] = ['appid' => $value['appid'], 'name' => $value['appname'], 'time' => $value['ltime']];
            }
        }
        return Library::output(0, $history);
    }

}
