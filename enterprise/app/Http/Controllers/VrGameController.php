<?php
namespace App\Http\Controllers;

use App;
use App\Helper\BladeHelper;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\CommentModel;
use App\Models\CookieModel;
use App\Models\DeveloperModel;
use App\Models\RecommendModel;
use App\Models\SolrModel;
use App\Models\WebgameModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class VrGameController extends Controller
{
    use SimpleResponse;

    /**
     * VR游戏首页
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $solrModel = new SolrModel();

        $posCode = [
            'vrgame-rank'     => 12,
            'hot-vr-game'     => 4,
            'vr-index-banner' => 4,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
        }

        $webGameModel = new WebGameModel();
        $vrGames      = $webGameModel->vrGameCategoryPage(1, 6);

        $uid     = CookieModel::getCookie("uid");
        $payRate = Config::get("common.pay_rate");
        return view('website.vrgame.index', compact("indexBanner", "recommend", "uid", "payRate"));
    }

    /**
     * VR游戏列表页
     *
     * @return \Illuminate\Http\Response
     */
    public function vrGameList(Request $request)
    {
        $class_id  = (int) $request->input("class_id", 0);
        $device_id = (int) $request->input("device_id", 0);

        return view('website.vrgame.list', compact("class_id", "device_id"));
    }

    /**
     * VR游戏列表页 client
     *
     * @return \Illuminate\Http\Response
     */
    public function vrGameApiList(Request $request)
    {
        $page     = (int) $request->input("page", 1);
        $class_id = (int) $request->input("class_id", 0);
        $num      = (int) $request->input("num", 6);

        $params = [];
        if ($class_id) {
            $params["category"] = $class_id;
        }

        $limit           = [($page - 1) * $num, $num];
        $params['limit'] = $limit;
        $solrModel       = new SolrModel();

        $vrGames = $solrModel->search('vrgame', $params);
        if (!$vrGames) {
            return $this->outputJsonWithCode(0, ["data" => false]);
        }

        foreach ($vrGames as &$game) {
            $game["logo"]        = static_image($game["image"]["logo"], 226);
            $game["score"]       = number_format($game["score"], 1);
            $game["device-icon"] = BladeHelper::handleDeviceIcon($game["support"]);
            $game["type-span"]   = "<span>" . BladeHelper::transConetentClass($game["category"], "vrgame", "</span><span>") . "</span>";
            $game["date"]        = date("Y年m月d日", $game["publish_date"]);
        }
        return $this->outputJsonWithCode(0, ["data" => $vrGames]);
    }

    /**
     * VR游戏详细
     *
     * @return \Illuminate\Http\Response
     */
    public function vrGameDetail(Request $request, $appid)
    {
        $appid = intval($appid);
        $isDev = $request->input("dev", 0);
        if ($appid <= 0) {
            return $this->error404(2304);
        }
        $solrModel = new SolrModel();
        if ($isDev == 1) {
            $developerModel = new DeveloperModel;
            $game           = $developerModel->getGameById($appid);
        } else {
            $webgameModel = new WebgameModel;
            $game         = $webgameModel->getOneGameInfo($appid);
        }

        if (!$game) {
            return $this->error404(2304);
        }

        $allDevices  = Config::get("vrgame.support_device");
        $deviceTypes = explode(",", $game['support']);
        if ($allDevices && count($allDevices) > 0) {
            foreach ($deviceTypes as &$type) {
                $type = isset($allDevices[$type]['name']) ? $allDevices[$type]['name'] : "";
            }
        }

        $uid = $request->cookie('uid');

        $images = ImageHelper::url("vrgame", $appid, $game['img_version'], $game['img_slider'], false);

        $comment = new CommentModel;

        $game['comment_info'] = $comment->getComments($uid, $appid, 2);
        $game['device_types'] = $deviceTypes;
        $game['imginfo']      = $images;
        if (isset($images['bg']) && $images['bg']) {
            $game['bg'] = static_image($images['bg'], 100);
        }

        $needbg = 1;

        if ($game['recomm_device'] && is_array($game['recomm_device'])) {
            foreach ($game['recomm_device'] as &$device) {
                $device = trim($device, "　");
            }
        }
        if (!isset($game['score'])) {
            $game['score'] = 0;
        }

        $posCode = [
            'hot-vr-game' => 4,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
        }

        $payRate = Config::get("common.pay_rate");
        // echo "<pre>";
        // print_r($game);die;
        return view('website.vrgame.detail', compact("game", "needbg", "recommend", "payRate"));
    }

    /**
     * 获取游戏的热门游戏取四个
     * [getHotVrGame description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getHotVrGame(Request $request)
    {
        $recommendModel = new RecommendModel();

        $number = 4;
        if ($request->input('num')) {
            $number = $request->input('num');
        }
        $posCode = [
            'hot-vr-game' => 20,
        ];
        $solrModel = new SolrModel();
        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
            $ret              = isset($recommend[$code]['data']) ? $recommend[$code]['data'] : [];
        }

        $recommendArr = [];
        foreach ($ret as $k => $v) {
            $recommendArr[$k]['id']    = $v['id'];
            $recommendArr[$k]['name']  = $v['name'];
            $recommendArr[$k]['score'] = number_format($v['score'], 1);
            $recommendArr[$k]['sell']  = $v['sell'];
            $recommendArr[$k]['logo']  = static_image($v['image']['logo'], 466);
        }
        if (count($recommendArr) > $number) {
            $randArr = array_rand($recommendArr, $number);
            foreach ($randArr as $rk => $rv) {
                $recommendRandArr[$rk] = $recommendArr[$rv];
            }
            return $this->outputJsonWithCode(0, $recommendRandArr);
        }
        return $this->outputJsonWithCode(0, $recommendArr);
    }

    /**
     * [newerTop 新手推荐]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function newerTop(Request $request, $tp)
    {
        $support   = $request->input('support');
        $solrModel = new SolrModel();

        if ($tp == "vrgame") {
            $posCode = [
                'newer-game' => 6,
            ];
        } else {
            $posCode = [
                'newer-video' => 6,
            ];
        }

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
        }
        return Library::output(0, ['recommend' => current($recommend)]);
    }

    /**
     * [newerTips 设备购买提示]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function newerBuyTips(Request $request)
    {
        $info = Config::get("vrgame.newer_guide.device_sale");
        return Library::output(0, $info);
    }
}
