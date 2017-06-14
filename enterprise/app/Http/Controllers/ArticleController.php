<?php
namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use Config;
use Helper\Library;
use Illuminate\Http\Request;
use App\Models\ArticleModel;
use App\Models\CookieModel;

class ArticleController extends Controller
{

    public function __construct()
    {

    }

    public function article(Request $request)
    {
        
    }

    public function articleList(Request $request)
    {

    }

    public function gameDetail(Request $request, $game_id)
    {
        if(!$game_id) {
            return redirect('/game', 302, [], true);
        }

        $userInfo = CookieModel::checkLogin();
        $uid = isset($userInfo['uid']) && $userInfo['uid'] ? $userInfo['uid'] : 0;

        $articleModel = new ArticleModel;
        $detail = $articleModel->getGameDetail($game_id);
        if(!$detail || !is_array($detail) || $detail['game_status'] != 0) {
            return redirect('/game', 302, [], true);
        }
        $arr = parse_url($detail['game_offical_url']);
        $detail['game_offical_link'] = $detail['game_offical_url'];
        if(isset($arr['scheme']) && $arr['scheme']) {
            $detail['game_offical_link'] = str_replace($arr['scheme'].":", "", $detail['game_offical_url']);
        }
        if($detail['game_lang']) {
            $detail['game_lang'] = explode(",", $detail['game_lang']);
        }
        if($detail['game_tag']) {
            $detail['game_tag'] = strToArr($detail['game_tag']);
        }
        if($detail['game_theme']) {
            $detail['game_theme'] = explode(",", $detail['game_theme']);
        }
        if($detail['game_merit']) {
            $detail['game_merit'] = explode("|", $detail['game_merit']);
        }
        if($detail['game_week']) {
            $detail['game_week'] = explode("|", $detail['game_week']);
        }
        $classname = [];
        if($detail['game_category']) {
            $detail['game_category'] = explode(",", $detail['game_category']);
            for($i = 0; $i < count($detail['game_category']); $i++) {
                $tmp = Config::get("category.vronline_game_class.".$detail['game_category'][$i]);
                if($tmp) {
                    $classname[] = $tmp['name'];
                }
            }
        }
        $device = [];
        if($detail['game_device']) {
            $detail['game_device'] = explode(",", $detail['game_device']);
            for($i = 0; $i < count($detail['game_device']); $i++) {
                $tmp = Config::get("vrgame.support_device.".$detail['game_device'][$i].".www_icon_class");
                if($tmp) {
                    $device[] = $tmp;
                }
            }
        }
        $platform = [];
        if($detail['game_platform']) {
            $detail['game_platform'] = explode(",", $detail['game_platform']);
            for($i = 0; $i < count($detail['game_platform']); $i++) {
                $tmp = Config::get("category.platform.".$detail['game_platform'][$i].".www_icon_class");
                if($tmp) {
                    $platform[] = $tmp;
                }
            }
        }
        $detail['game_star'] = $articleModel->getStarByScore($detail['game_mark']);

        $pics = $videos = $pingce = $articles = [];
        if(isset($detail['pics']) && is_array($detail['pics']) && $detail['pics']) {
            $tmppics = $detail['pics'];
            for($i = 0; $i < count($tmppics); $i++) {
                $pics[] = $tmppics[$i]['game_pic_url'];
            }
            $pics = array_chunk($pics, 3);
        }
        if(isset($detail['videos']) && is_array($detail['videos']) && $detail['videos']) {
            $tmpvideos = $detail['videos'];
            $videos = array_chunk($tmpvideos, 3);
        }
        if(isset($detail['pingce']) && is_array($detail['pingce']) && $detail['pingce']) {
            $pingce = $detail['pingce'];
        }
        if(isset($detail['news']) && is_array($detail['news']) && $detail['news']) {
            $articles = $detail['news'];
        }
        $recommends = $detail['recommend'];
        return view('vronline.game_detail', compact("detail", "classname", "device", "platform", "pics", "videos", "pingce", "articles", "recommends", "game_id", "uid"));
    }

    public function gameDetailTopic()
    {
        $pos_code = "game-detail-subject";
        $articleModel = new ArticleModel;
        $topic = $articleModel->getTopicInGameDetail($pos_code);
        return view('vronline.game_detail_topic', compact("topic"));
    }

    public function gameSearch(Request $request)
    {
        $pagesize  = 20;

        $platform = $request->input("pf", "");
        $device   = $request->input("d", "");
        $gametype = $request->input("c", "");
        $tag      = $request->input("t", "");
        $price    = $request->input("pr", "");
        $orderby  = $request->input("s", "z");
        $page     = intval($request->input("page", 1));
        if($page <= 0) {
            $page = 1;
        }

        $searchParams = ['tp' => 'game'];
        if(Config::get('category.searchPrice.'.$price.'.str')) {
            $searchParams['price'] = Config::get('category.searchPrice.'.$price.'.str');
        }
        if($platform) {
            $searchParams['platform'] = $platform;
        }
        if($gametype) {
            $searchParams['category'] = $gametype;
        }
        if($tag) {
            $searchParams['tag'] = $tag;
        }
        if($device) {
            $searchParams['device'] = $device;
        }
        $start = ($page - 1) * $pagesize;
        $searchParams['limit'] = [$start, $pagesize];
        switch($orderby) {
            case "z":
                $searchParams['orderBy'] = "weight_score desc";
                break;
            case "v":
                $searchParams['orderBy'] = "view desc";
                break;
            case "t":
                break;
            case "s":
                $searchParams['orderBy'] = "score desc";
                break;
            default:
                $searchParams['orderBy'] = "weight_score desc";
                break;
        }
        $articleModel = new ArticleModel;
        $search = $articleModel->gameSearch($searchParams);
        if(!is_array($search) && is_array($search['result'])) {
            return Library::output(1);
        }
        $search['page'] = $page;
        $search['totalpage'] = ceil($search['num'] / $pagesize);
        for($i = 0; $i < count($search['result']); $i++)
        {
            /**
             * 处理分类
             */
            if(isset($search['result'][$i]['category']) && is_array($search['result'][$i]['category'])) {
                for($j = 0; $j < count($search['result'][$i]['category']); $j++) {
                    $classid = $search['result'][$i]['category'][$j];
                    $search['result'][$i]['classname'][] = Config::get("category.vronline_game_class.{$classid}.name");
                }
            }else {
                $search['result'][$i]['classname'] = [];
            }

            /**
             * 处理简介
             */
            $search['result'][$i]['intro'] = mb_substr(strip_tags($search['result'][$i]['intro']), 0, 75, "UTF-8");

            /**
             * 处理星级
             */
            $search['result'][$i]['star'] = mb_substr(strip_tags($search['result'][$i]['intro']), 0, 75, "UTF-8");
        }
        return Library::output(0, $search);
    }

    public function game(Request $request)
    {
        $articleModel = new ArticleModel;
        $result = $articleModel->gameHome();
        $recommends = $result['recommend'];
        $pos = $result['pos'];
        $news = $result['news'];
        $oculusNews = $result['oculusNews'];
        $htcNews = $result['htcNews'];
        $psvrNews = $result['psvrNews'];
        return view('vronline.game', compact("recommends", "pos", "news", "oculusNews", "htcNews", "psvrNews"));
    }

    public function gameList(Request $request)
    {
        $platforms = Config::get("category.platform");
        $devices = Config::get("vrgame.support_device");
        $gametypes = Config::get("category.vronline_game_class");
        $prices = Config::get('category.searchPrice');
        $tags = Config::get("category.vrgame_tags");

        $articleModel = new ArticleModel;
        $result = $articleModel->gameList();
        $newest = $result['newest'];
        $recommends = $result['recommend'];
        return view('vronline.game_list', compact("newest", "recommends", "platforms", "devices", "gametypes", "prices", "tags"));
    }

    public function addPv(Request $request, $type, $itemid)
    {
        if(!$type || !$itemid) {
            return Library::output(2001);
        }
        $articleModel = new ArticleModel;
        $result = $articleModel->addPv($type, $itemid);
        if($result) {
            return Library::output(0);
        }else {
            return Library::output(1);
        }
    }

    public function support(Request $request)
    {
        $userInfo = CookieModel::checkLogin();
        $uid = isset($userInfo['uid']) && $userInfo['uid'] ? $userInfo['uid'] : 0;
        if(!$uid) {
            return Library::output(1301);
        }

        $action = $request->input("act", "");
        $type   = $request->input("type", "");
        $itemid = $request->input("itemid", "");

        $articleModel = new ArticleModel;
        $result = $articleModel->support($uid, $action, $type, $itemid);
        if($result == "already") {
            return Library::output(1, null, "已经点过一次了");
        }else if(!$result) {
            return Library::output(0);
        }
        return Library::output(0);
    }

}
