<?php

namespace App\Http\Controllers;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\CommentModel;
use App\Models\CookieModel;
use App\Models\RecommendModel;
use Config;
use Illuminate\Http\Request;

class WebGameTController extends Controller
{

    use SimpleResponse;

    public function __construct()
    {
        $this->middleware("vrauth:0:webgame", ['only' => ["getServers"]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $webGameModel   = App::make('webGame');
        $bannerCode     = "landspace-video-banner";
        $recommendModel = new RecommendModel;
        $bannerRecomm   = $recommendModel->getRecommendContentByCode($bannerCode);

        $banners = [];
        if ($bannerRecomm && isset($bannerRecomm['data'])) {
            $banners = $bannerRecomm['data'];
        }

        $recommend_ids = $webGameModel::getWebGameRecommend("0", 3);

        $gameTypes = Config::get("webgame.class");

        $allGameInfo = $webGameModel->getAllGameInfo();

        $allGame = array();
        foreach ($allGameInfo as $game) {
            $allGame[$game["appid"]] = $game;
        }
        return view('webgame.index', ["banners" => $banners, "recommend_ids" => $recommend_ids, "allGame" => $allGame, "gameTypes" => $gameTypes, "needbg" => 1]);
    }

    /**
     * 获取游戏列表
     *
     * @param  Request $request [description]
     * @param  [type]  $tp      [description]
     * @return [type]           [description]
     */
    public function getGameList(Request $request, $tp)
    {
        $gameTypes    = Config::get("webgame.class");
        $webGameModel = App::make('webGame');
        $games        = $webGameModel->getAllGameInfo($tp);
        foreach ($games as $key => &$game) {
            $game['first_class'] = isset($gameTypes[$game['first_class']]) ?: "";
            $resInfo             = ImageHelper::path("webgameimg", $game['appid'], $game['img_version'], $game['img_slider'], false);
            $game['img']         = $resInfo['logo'];
        }
        return $this->outputJsonWithCode(0, $games);
    }

    /**
     * API:获取游戏信息
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getGameInfo(Request $request)
    {
        $appid = intval($request->input("appid"));

        if (!$appid > 0) {
            return $this->outputJsonWithCode(2001);
        }

        $webGameModel = App::make('webGame');

        $game = $webGameModel->getOneGameInfo($appid);

        if (strlen($game['img_slider']) > 1) {
            $img_temp = json_decode($game['img_slider'], true);
        }

        $resInfo      = ImageHelper::path("webgameimg", $appid, $game['img_version'], $game['img_slider'], false);
        $game['base'] = $resInfo['base'];
        $game['bg']   = $resInfo['bg'];

        foreach ($img_temp as $value) {
            $game['img_list'][] = $game['base'] . $value;
        }

        if ($game['send_time'] > 0) {
            $game['send_time'] = date("Y-m-d", $game['send_time']);
        } else {
            $game['send_time'] = "暂无";
        }
        if (!$game) {
            return $this->outputJsonWithCode(2304);
        }

        $gift = $webGameModel->getAllGift($appid);

        $uid = CookieModel::getCookie("uid");

        if ($uid) {
            $game["isLogin"] = 1;
        }

        $comment = new CommentModel();

        if ($uid) {
            $arr = array(
                'uid'       => $uid,
                'target_id' => $appid,
            );
            if ($comment->alreadyComment($arr)) {
                $game["comment"] = 1;
            }
        }

        $clause = array(
            'target_id'   => $appid,
            'target_type' => 1,
            'status'      => 1,
        );

        $gameTypes = $webGameModel::getGameTypes();

        $game["first_class"]   = $gameTypes[$game["first_class"]]["name"];
        $game["allCommentNum"] = $comment->getCommentCount($clause);

        return $this->outputJsonWithCode(0, $game);
    }

    /**
     * API:获取游戏分类信息
     *
     * @return [type]           [description]
     */
    public function getGameType()
    {
        $webGameModel = App::make('webGame');

        $gameTypes = $webGameModel::getGameTypes();

        return $this->outputJsonWithCode(0, $gameTypes);
    }

    /**
     * API:获取游戏区服
     *
     * @return [type]           [description]
     */
    public function getServers(Request $request)
    {

        $appid = intval($request->input("appid"));

        if (!$appid) {
            return $this->outputJsonWithCode(2001);
        }

        $userInfo = $request->userinfo;
        if (isset($userInfo['uid'])) {
            $uid = $userInfo['uid'];
        } else {
            $uid = '';
        }

        $webGameModel = App::make('webGame');

        $game = $webGameModel->getOneGameInfo($appid);

        if (!$game) {
            return $this->outputJsonWithCode(2304);
        }

        $isSuperUser = $webGameModel->isTestAccount($uid, $game['uid']);

        $now = time();
        $servers = array();
        $allservers = $webGameModel->getAllServer($appid);
        if($allservers && is_array($allservers) && count($allservers) > 0) {
            foreach ($allservers as $key => $arr_detail) {
                if ($arr_detail['start'] > $now || $arr_detail['is_publish'] == 0) {
                    if (!$isSuperUser) {
                        continue;
                    }
                }
                $servers[] = $arr_detail;
            }
        }
        if (!$servers) {
            return $this->outputJsonWithCode(2305);
        }

        $ret["rmb_rate"]   = $game["rmb_rate"];
        $ret["gameb_name"] = $game["gameb_name"] ? $game["gameb_name"] : "元宝";
        $ret["servers"]    = $servers;

        return $this->outputJsonWithCode(0, $ret);
    }

}
