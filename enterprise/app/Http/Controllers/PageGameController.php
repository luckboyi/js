<?php
/**
 * 页游
 */
namespace App\Http\Controllers;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\SolrModel;
use App\Models\WebgameLogicModel;
use App\Models\WebgameModel;
use Config;
use Redirect;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;

class PageGameController extends Controller
{

    use SimpleResponse;

    public function __construct()
    {
        $this->middleware("vrauth:0:webgame", ['only' => ["index", "play", "detail", "getGameNewsList"]]);
    }

    public function index(Request $request)
    {
        $userInfo = $request->userinfo;
        if (isset($userInfo['uid'])) {
            $uid        = $userInfo['uid'];
            $nick       = isset($userInfo['nick']) && $userInfo['nick'] ? $userInfo['nick'] : $userInfo['account'];
            $account    = $userInfo['account'];
            $login_time = isset($userInfo['login_time']) && $userInfo['login_time'] ? $userInfo['login_time'] : 0;
        } else {
            $uid = $token = $nick = $account = $login_time = '';
        }
        //获取首页banner数据
        $solrModel = new SolrModel();
        $webgame   = new WebgameModel();
        $posCode   = [
            'webgame-rank'  => 25,
            'web-hotgame'   => 16,
            'web-othergame' => 24,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
            foreach ($recommend[$code]['data'] as $k => $v) {
                if (!isset($v['category'][0])) {
                    $category = '无分类';
                } else {
                    foreach ($v['category'] as $ck => $cv) {
                        $category[$ck] = Config::get("webgame.class." . $cv . '.name');
                    }
                }
                $recommend[$code]['data'][$k]['category'] = $category;
            }
        }

        $posCodeBan = [
            'web-slider'  => 4,
            'web-actgame' => 6,
            'web-ad1'     => 1,
            'web-ad2'     => 1,
            'web-ad3'     => 10,
        ];
        $indexBanner = [];
        foreach ($posCodeBan as $code => $num) {
            $indexBannerCode    = $code;
            $indexBanner[$code] = $solrModel->getTop($indexBannerCode, $num);
        }
        $downs   = Config::get("vrclient.latest_client");
        $current = "down";
        $history = [];
        if ($uid) {
            $history = $webgame->getWebGameHistory($uid, 5);
            foreach ($history as $k => $v) {
                $webgameInfo          = $webgame->getOneGameInfo($v['appid']);
                $history[$k]['image'] = ImageHelper::path('webgameimg', $webgameInfo['appid'], $webgameInfo['img_version'], $webgameInfo['img_slider'], false);
            }
        }

        return view('pagegame.index', compact("uid", "nick", "recommend", "indexBanner", "downs", "history"));
    }

    public function detail(Request $request, $appid)
    {
        $userInfo = $request->userinfo;
        if (isset($userInfo['uid'])) {
            $uid        = $userInfo['uid'];
            $nick       = isset($userInfo['nick']) && $userInfo['nick'] ? $userInfo['nick'] : $userInfo['account'];
            $account    = $userInfo['account'];
            $login_time = isset($userInfo['login_time']) && $userInfo['login_time'] ? $userInfo['login_time'] : 0;
        } else {
            $uid = $token = $nick = $account = $login_time = '';
        }

        $webGameModel = App::make('webGame');
        $gameInfo     = $webGameModel->getOneGameInfo($appid);

        if (!$gameInfo) {
            return $this->error404(2304);
        }

        $images = ImageHelper::url("webgame", $appid, $gameInfo['img_version'], $gameInfo['img_slider'], false, $gameInfo['screenshots'], "url");

        /**
         * 游戏还未发布，普通用户不能进
         */
        $isStart = 0;

        /**
         * 最后玩过的服务器ID
         */
        $lastserverid = 0;
        $serverinfo   = array();
        if ($uid) {
            $lastserverid = $webGameModel->getLastGameServerid($uid, $appid);
            if ($lastserverid) {
                $isStart = 1;
                $serverinfo = $webGameModel->getOneServer($appid, $lastserverid);
                if (!$serverinfo || !is_array($serverinfo)) {
                    $serverinfo = array();
                }
            }
        }
        if(!$lastserverid) {
            $maxserverid = $webGameModel->maxServeridByAppid($appid);
            if($maxserverid) {
                $isStart = 1;
            }
        }

        if ($gameInfo['stat'] != 0) {
            $isStart = 0;
        }

        /**
         * 是否有高级权限用户
         */
        $isSuperUser = $webGameModel->isTestAccount($uid, $gameInfo['uid']);
        if($isSuperUser) {
            $isStart = 1;
        }

        $news      = array();
        $newsTypes = Config::get("category.webgame_news");
        if (!$newsTypes || !is_array($newsTypes)) {
            $newsTypes = array();
        }
        $newsFirstSort = '';
        foreach ($newsTypes as $arr_detail) {
            $typeid = $arr_detail['id'];
            $arr    = $webGameModel->gameNewsByGameCategory($appid, $typeid);
            if (!$arr || !is_array($arr)) {
                $arr = array();
            }
            if(!$newsFirstSort) {
                $newsFirstSort = $typeid;
            }
            
            $news[$typeid]['id']       = $typeid;
            $news[$typeid]['name']     = $arr_detail['name'];
            $news[$typeid]['selected'] = isset($arr_detail['selected']) ? $arr_detail['selected'] : "";
            $news[$typeid]['en']       = isset($arr_detail['en']) ? $arr_detail['en'] : "";
            $news[$typeid]['data']     = $arr;
        }

        $publicPath = public_path();
        $path       = pathinfo($publicPath);
        $viewfile   = $path['dirname'] . "/resources/views/pagegame/detail/detail{$appid}.blade.php";
        if (file_exists($viewfile)) {
            $blade = "pagegame.detail.detail{$appid}";
        } else {
            return $this->error404(2304);
        }
        $bgimage = isset($images['bg2']) && $images['bg2'] ? $images['bg2'] : $images['bg'];
        return view($blade, ["uid" => $uid, "appid" => $appid, "isStart" => $isStart, "news" => $news, "newsFirstSort" => $newsFirstSort, "gameInfo" => $gameInfo, "images" => $images, "needbg" => 1, "bgimage" => $bgimage, "nick" => $nick, "serverinfo" => $serverinfo, "login_time" => $login_time]);
    }

    /**
     * 选服务页面
     *
     * @param  Request $request [description]
     * @param  [type]  $appid   [description]
     * @return [type]           [description]
     */
    public function play(Request $request, $appid)
    {
        $islogin  = false;
        $userInfo = $request->userinfo;
        // var_dump($userInfo);exit;
        if (isset($userInfo['uid'])) {
            $islogin    = true;
            $uid        = $userInfo['uid'];
            $token      = $userInfo['token'];
            $nick       = $userInfo['nick'];
            $account    = $userInfo['account'];
            $login_time = $userInfo['login_time'] ?? 0;
        } else {
            $uid = $token = $nick = $account = $login_time = '';
        }
        $appid = intval($appid);

        $now          = time();
        $webgameModel = App::make('webGame');

        $gameinfo = $webgameModel->getOneGameInfo($appid);
        if (!$gameinfo || !is_array($gameinfo)) {
            $gameinfo = array();
        }

        if (!isset($gameinfo['uid'])) {
            $gameinfo['uid'] = 0;
        }

        /**
         * 是否有高级权限用户
         */
        $isSuperUser = $webgameModel->isTestAccount($uid, $gameinfo['uid']);

        /**
         * 游戏还未发布，普通用户不能进
         */
        if ($gameinfo['stat'] != 0) {
            if (!$uid || !$isSuperUser) {
                $jump = "http://web.vronline.com/detail/{$appid}";
                return Redirect::to($jump);
            }
        }

        /**
         * 推荐服
         */
        $recommend = $webgameModel->recommendServer($appid);
        if (!$recommend || !is_array($recommend)) {
            $recommend = array();
        }

        /**
         * 我玩过的服
         */
        $login     = false;
        $myservers = array();
        if ($islogin) {
            $login     = true;
            $myservers = $webgameModel->getGameServerLogByAppid($uid, $appid);
        }

        if (!$myservers || !is_array($myservers)) {
            $myservers = array();
        }

        /**
         * 所有服
         */
        $webgameLogic = new WebgameLogicModel;
        $allservers   = $webgameLogic->getAllServer($appid);
        if (!$allservers || !is_array($allservers)) {
            $allservers = array();
        }
        $lastsid = 0;
        foreach ($allservers as $key => $arr_detail) {
            if ($arr_detail['start'] > $now) {
                if (!$isSuperUser) {
                    // 如果是有高级权限用户，可以看到所有服务器
                    unset($allservers[$key]);
                }
                continue;
            }
        }

        $images = ImageHelper::url("webgame", $appid, $gameinfo['img_version'], $gameinfo['img_slider'], false, "", "url");

        //var_dump($myservers);exit;

        return view('pagegame.play', ['appid' => $appid, "uid" => $uid, "nick" => $nick, "account" => $account, "needbg" => 1, "bgimage" => $images['bg'], 'islogin' => $login, 'gameinfo' => $gameinfo, 'recommend' => $recommend, 'myservers' => $myservers, 'allservers' => $allservers, "images" => $images, "login_time" => $login_time]);
    }

    public function getGameNewsList(Request $request, $appid, $sort) {
        $userInfo = $request->userinfo;

        $page = $request->input("page");
        if(!$page){
            $page = 1;
        }
        $perPage = 1;
        $start = ($page-1)*$perPage;
        
        if (isset($userInfo['uid'])) {
            $uid        = $userInfo['uid'];
            $nick       = isset($userInfo['nick']) && $userInfo['nick'] ? $userInfo['nick'] : $userInfo['account'];
            $account    = $userInfo['account'];
            $login_time = isset($userInfo['login_time']) && $userInfo['login_time'] ? $userInfo['login_time'] : 0;
        } else {
            $uid = $token = $nick = $account = $login_time = '';
        }

        $webGameModel = App::make('webGame');
        $gameInfo     = $webGameModel->getOneGameInfo($appid);

        if (!$gameInfo) {
            return $this->error404(2304);
        }

        $images = ImageHelper::url("webgame", $appid, $gameInfo['img_version'], $gameInfo['img_slider'], false, $gameInfo['screenshots'], "url");

        /**
         * 游戏还未发布，普通用户不能进
         */
        $isStart = 0;

        /**
         * 最后玩过的服务器ID
         */
        $lastserverid = 0;
        $serverinfo   = array();
        if ($uid) {
            $lastserverid = $webGameModel->getLastGameServerid($uid, $appid);
            if ($lastserverid) {
                $isStart = 1;
                $serverinfo = $webGameModel->getOneServer($appid, $lastserverid);
                if (!$serverinfo || !is_array($serverinfo)) {
                    $serverinfo = array();
                }
            }
        }
        if(!$lastserverid) {
            $maxserverid = $webGameModel->maxServeridByAppid($appid);
            if($maxserverid) {
                $isStart = 1;
            }
        }

        if ($gameInfo['stat'] != 0) {
            $isStart = 0;
        }

        /**
         * 是否有高级权限用户
         */
        $isSuperUser = $webGameModel->isTestAccount($uid, $gameInfo['uid']);
        if($isSuperUser) {
            $isStart = 1;
        }

        $news      = array();
        $newsTypes = Config::get("category.webgame_news");
        $bannerDesc = isset($newsTypes[$sort]['name']) ? $newsTypes[$sort]['name'] . '中心' : '新闻中心';
        if (!$newsTypes || !is_array($newsTypes)) {
            $news = [];
        } else {
             $arr    = $webGameModel->gameNewsByGameCategoryForPage($appid, $sort, $start, $perPage);
            if (!$arr || !is_array($arr)) {
                $arr = array();
            }
            $news['data']     = $arr;
        }

        $publicPath = public_path();
        $path       = pathinfo($publicPath);

        $blade = "pagegame.newslist";
        $bgimage = isset($images['bg2']) && $images['bg2'] ? $images['bg2'] : $images['bg'];

        $retDate = isset($news['data']['data']) ? $news['data']['data'] : [];
        if ($page != '') {
            $currentPage = $page;
            $currentPage = $currentPage <= 0 ? 1 : $currentPage;
        } else {
            $currentPage = 1;
        }
        $item      = array_slice($retDate, ($currentPage - 1) * $perPage, $perPage); //注释1
        $total     = count($retDate);
        $paginator = new LengthAwarePaginator($item, $total, $perPage, $currentPage, [
            'path'     => Paginator::resolveCurrentPath(), //注释2
            'pageName' => 'page',
        ]);
        $retDate = $paginator->toArray()['data'];

        return view($blade, ["uid" => $uid, "appid" => $appid, "isStart" => $isStart, "news" => $retDate, "gameInfo" => $gameInfo, "images" => $images, "needbg" => 1, "bgimage" => $bgimage, "nick" => $nick, "serverinfo" => $serverinfo, "login_time" => $login_time, "action" => 1, "paginator" => $paginator, "bannerDesc" => $bannerDesc]);
    }
}
