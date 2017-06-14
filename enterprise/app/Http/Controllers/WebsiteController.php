<?php

namespace App\Http\Controllers;

use Agent;
use App;
use App\Helper\BladeHelper;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\CheckUpdateModel;
use App\Models\CommentModel;
use App\Models\MiddleModel;
use App\Models\SolrModel;
use App\Models\VideoModel;
use App\Models\WebgameLogicModel;
use App\Models\WebgameModel;
use Config;
use Cookie;
use Helper\AccountCenter as Account;
use Helper\IPSearch;
use Helper\Library;
use Illuminate\Http\Request;
use Redirect;

class WebsiteController extends Controller
{

    use SimpleResponse;

    public function __construct()
    {
        $this->middleware("vrauth:jump::https", ['only' => ["profile"]]);
        $this->middleware("vrauth:jump", ['only' => ["charge", "profileVideo", "profileVideoSave"]]);
        $this->middleware("vrauth:jump:webindex", ['only' => ["index"]]);
        //$this->middleware("vrauth:json", ['only' => ["hasRole"]]);
    }

    /**
     * 首页
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ret             = Agent::match('VRonlinePlat');
        $HttpGetPlatform = $request->input("platform");
        if ($ret || $HttpGetPlatform == "pc") {
        } else {
            return $this->down();
        }

        //获取首页banner数据
        $solrModel = new SolrModel();

        $indexBannerCode = 'index-banner';
        $indexBanner     = $solrModel->getTop($indexBannerCode);
        $indexBanner     = $indexBanner['data'];
        $posCode         = [
            'video-rank'      => 7,
            'vrgame-rank'     => 7,
            'webgame-rank'    => 7,
            'recommend-index' => 5,
            'hot-vr-game'     => 8,
            'hot-video'       => 8,
            'hot-webgame'     => 8,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
        }

        /**
         * 是否有首页背景图
         */
        $homebg  = 1;
        $current = "home";
        $payRate = Config::get("common.pay_rate");
        return view('website.index', compact("current", "recommend", "indexBanner", "payRate"));
    }

    public function switchRecommend(Request $request)
    {
        $code = $request->input("code");

        if (!$code) {
            $this->outputJsonWithCode(1);
        }

        $solrModel = new SolrModel();

        $res  = $solrModel->getTop($code, 32);
        $data = [];
        if (isset($res["data"])) {
            foreach ($res["data"] as &$game) {
                $game["logo"]        = static_image($game["image"]["logo"], 226);
                $game["score"]       = number_format($game["score"], 1);
                $game["device-icon"] = BladeHelper::handleDeviceIcon($game["support"]);
                $game["type-span"]   = "<span>" . BladeHelper::transConetentClass($game["category"], "vrgame", "</span><span>") . "</span>";
                $game["date"]        = date("Y年m月d日", $game["publish_date"]);
            }
            shuffle($res["data"]);
            $data = $res["data"];
        }

        return $this->outputJsonWithCode(0, $data);
    }

    public function search(Request $request)
    {
        $params = $this->handleParamsForSearchOrSuggest($request, $tp);
        if ($params === false) {
            return Library::output(1);
        }

        $start = intval($request->input('start', 0));
        $num   = intval($request->input('num', 6));

        if ($start < 0) {
            return Library::output(1);
        }
        if ($num < 1) {
            return Library::output(1);
        }

        $params['limit'] = [$start, $num];

        $solrModel = new SolrModel();
        $res       = $solrModel->search($tp, $params);

        if (!$res) {
            $out = ['data' => false];
        } else {
            $out = ['data' => $res];
        }
        return Library::output(0, $out);
    }

    public function suggest(Request $request)
    {
        $params = $this->handleParamsForSearchOrSuggest($request, $tp);

        $data = ["suggestions" => []];
        if ($params === false) {
            return $this->outputJson($data);
        }
        $params['suggest'] = true;
        $params['limit']   = [0, 10];

        $solrModel = new SolrModel();
        $res       = $solrModel->search($tp, $params, false);

        foreach ($res as $value) {
            $data["suggestions"][] = ["value" => $value["name"]];
        }
        return $this->outputJson($data);
    }

    protected function handleParamsForSearchOrSuggest($request, &$tp)
    {
        $tp       = $request->input('tp');
        $category = intval($request->input('category'));
        $support  = intval($request->input('support'));
        $name     = trim($request->input('name'));
        $spell    = trim($request->input('spell'));
        if (!in_array($tp, array('video', 'vrgame', 'webgame'))) {
            return false;
        }

        $params = [];

        if ($category > 0) {
            $params['category'] = $category;
        }
        if ($support > 0) {
            $params['support'] = $support;
        }

        if ($spell) {
            $params['spell'] = $spell;
        }

        if ($name) {
            if (preg_match("/[\x80-\xff]./", $name) || strlen($name) > 1) {
                $params['name'] = $name;
            } else {
                $params['spell'] = $name;
            }
        }

        return $params;
    }

    /*
     * 根据id获取其信息的方法
     */
    public function getInfoByitemid($itemid)
    {
        $webgame    = new WebgameModel();
        $videoModel = new MiddleModel();

        $infoArr    = $webgame->getOneGameInfo($itemid);
        $oneInfoArr = [];
        if (empty($infoArr)) {
            $infoArr = $videoModel->getVideoInfoByVid($itemid);
            if ($infoArr && is_array($infoArr)) {
                $oneInfoArr['appid']     = $infoArr[0]['video_id'];
                $oneInfoArr['name']      = $infoArr[0]['video_name'];
                $oneInfoArr['desc']      = $infoArr[0]['video_intro'];
                $oneInfoArr['picshow']   = $infoArr[0]['video_cover'];
                $oneInfoArr['resources'] = url("videoPlay?vid=" . $infoArr[0]['video_id']);
            }
        } else {
            $oneInfoArr['appid'] = $infoArr['appid'];
            $oneInfoArr['name']  = $infoArr['name'];
            $oneInfoArr['desc']  = $infoArr['desc'];
            if ($infoArr['game_type'] == 1) {
                $oneInfoArr['picshow']   = ImageHelper::path("vrgameimg", $infoArr['appid'], $infoArr['img_version'], "", false);
                $oneInfoArr['resources'] = url("vrgame/" . $infoArr['appid']);
            } else {
                $oneInfoArr['picshow']   = ImageHelper::path("webgameimg", $infoArr['appid'], $infoArr['img_version'], "", false);
                $oneInfoArr['resources'] = url("webgame/" . $infoArr['appid']);
            }

        }
        return $oneInfoArr;
    }

    /**
     * 页游首页
     *
     * @return \Illuminate\Http\Response
     */
    public function webgame()
    {
        //获取首页banner数据
        $solrModel = new SolrModel();

        $posCode = [
            'webgame-rank'           => 8,
            'landspace-video-banner' => 4,
            'webgame-love'           => 16,
            'webgame-new-recommend'  => 6,
            'webgame-activity'       => 20,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $solrModel->getTop($code, $num);
        }

        $hot_icon = [
            1000035 => "http://image.vronline.com/bannerimg/341bc073e77e5e84c86a0e705c1eff2b1480665684084.jpg",
            1000036 => "http://image.vronline.com/bannerimg/eb6254edb9cb828d97bfb91a89b9a4fb1480759906539.jpg",
            1000024 => "http://image.vronline.com/bannerimg/8b4c17aee5eead4cc886d4435c9c6cb11480759997424.jpg",
            1000131 => "http://image.vronline.com/bannerimg/57233dda3461b20a6d924d68442c135c1480760349402.jpg",
            1000104 => "http://image.vronline.com/bannerimg/265602aa9c2d5d4f11208ec8f606592e1480760404792.jpg",
            1000148 => "http://image.vronline.com/bannerimg/7ee2271f7b3654c8ba6f575023e495e81480760454919.jpg",
        ];

        $love_icon = [
            1000035 => "http://image.vronline.com/bannerimg/07b877dbcb63be3b66a664eff680c2d81480759184974.jpg",
            1000034 => "http://image.vronline.com/bannerimg/217ef421b992358e58cfdfc8f2b8decb1480759125649.png",
            1000104 => "http://image.vronline.com/bannerimg/b79e60fdbf9a2b092c667341209783921480759226097.jpg",
            1000024 => "http://image.vronline.com/bannerimg/73be6ca4b193fb83a18be5fb32135c891480759369663.jpg",
            1000131 => "http://image.vronline.com/bannerimg/2df8666d16c908cdfa9307db821932b11480759593573.jpg",
            1000148 => "http://image.vronline.com/bannerimg/3c7be5a4060c226659f596783b467adc1480759712575.jpg",
            1000127 => "http://image.vronline.com/bannerimg/f2d144b910ad1dbaed76b3b2cd97efb11480759746790.jpg",
            1000036 => "http://image.vronline.com/bannerimg/952cb9f5fc0c971f4735e9da47bf12ae1480836051689.jpg",
            1000011 => "http://image.vronline.com/bannerimg/c47ee45dbe699910485559411065963f1480836104175.jpg",
        ];

        $act_icon = [
            1000035 => "bannerimg/10bc01326fad599177e794a7a11122521480836000822.jpg",
            1000034 => "bannerimg/9e4e744de9b7db404b99890611eb45791480836017822.jpg",
            1000104 => "bannerimg/530e85b7a045ad91cbf09ba17b5f9fdb1480835986669.jpg",
            1000024 => "bannerimg/dccd3da3af168cf21483adc5a8a43bc81480835973319.jpg",
            1000131 => "bannerimg/ce9780f70bb194c53c9244bf1547fc931480835954910.png",
            1000148 => "bannerimg/0cbafd716d07726efbf953abdfd4a6331480835939596.png",
            1000127 => "bannerimg/489362f36ddf8de79646d4d2b563a05d1480835923335.png",
            1000036 => "bannerimg/0beba0eb5758010f32913c5c7b8f3c1f1480836058195.png",
            1000011 => "bannerimg/6a09f4a11c9a9b1e375565363822085e1480836099869.png",
        ];
        if ($recommend["webgame-activity"]['data']) {
            foreach ($recommend["webgame-activity"]['data'] as $key => $arr_detail) {
                if (isset($act_icon[$arr_detail['id']])) {
                    $recommend["webgame-activity"]['data'][$key]["image"]["icon"] = $act_icon[$arr_detail['id']];
                }
            }
        }

        /**
         * 是否有首页背景图
         */
        $homebg = 1;

        //获取页游的分类数据
        $webGameSortArr = config::get("webgame.class");

        $GiftNumList = $this->getGiftNum();
        //获取分类的页游
        $firstSortInfo = $this->getFirstSortInfo();
        $hasLeft       = 1;
        return view('website.webgame.index', compact("recommend", "webGameSortArr", "GiftNumList", "firstSortInfo", "hasLeft", "hot_icon", "love_icon"));
    }

    /**
     * 获取页游首页礼包专区的数据
     */
    public function getGiftNum()
    {
        $webgame = new WebgameModel();
        //获取页游的所有数据领取礼包的数据
        $getAllGameArr = $webgame->getAllGameInfo('', '', 0);
        $giftArr       = [];
        $gifts         = [];
        //获取礼包的数量getGiftNum($gid)
        foreach ($getAllGameArr as $k => $v) {
            $giftInfoArr = $webgame->getAllGift($v['appid']);
            if (empty($giftInfoArr)) {
                continue;
            }
            foreach ($giftInfoArr as $info) {
                $hasNum    = $webgame->getGiftNum($info['gid']);
                $getGiftTm = $webgame->getGiftTm($info['gid']);

                $giftArr[$k]['hasNum']    = $hasNum;
                $giftArr[$k]['getGiftTm'] = $getGiftTm;
                $giftArr[$k]['gid']       = $info['gid'];

            }

            $giftArr[$k]['appid'] = $v['appid'];
            $giftArr[$k]['name']  = $v['name'];
            $giftArr[$k]['image'] = ImageHelper::url('webgame', $v['appid'], $v['img_version'], $v['img_slider'], false);
            if ($giftArr[$k]['hasNum'] == 0 || !$giftArr[$k]['getGiftTm']) {
                unset($giftArr[$k]);
            }
        }

        foreach ($giftArr as $v) {
            $gifts[] = $v;
        }

        return $gifts;
    }

    /**
     * 获取页游首页礼包专区的数据
     */
    public function getFirstSortInfo()
    {
        $webgame = new WebgameModel();
        //获取页游的所有数据领取礼包的数据
        ////获取页游的分类数据
        $webGameSortArr = config::get("webgame.class");

        $firstSort = current($webGameSortArr);
        //获取分类的页游
        $firstSortInfo = $webgame->getAllGameInfo($firstSort['id'], '', 0);

        foreach ($firstSortInfo as $k => $v) {
            $giftArr[$k]['appid'] = $v['appid'];
            $giftArr[$k]['name']  = $v['name'];
            $giftArr[$k]['image'] = ImageHelper::url('webgame', $v['appid'], $v['img_version'], $v['img_slider'], false);
        }
        return $giftArr;
    }

    /**
     * 页游详情
     *
     * @return \Illuminate\Http\Response
     */
    public function webgameDetail(Request $request, $appid)
    {

        $appid = intval($appid);

        if ($appid <= 0) {
            return $this->error404(2304);
        }

        $uid = Cookie::get("uid");

        $webGameModel = App::make('webGame');

        $game = $webGameModel->getOneGameInfo($appid);

        if (!$game) {
            return $this->error404(2304);
        }

        /**
         * 游戏还未发布，普通用户不能进
         */
        if ($game['stat'] != 0) {
            if (!$uid || !$webGameModel->isTestAccount($uid, $game['uid'])) {
                $jump = "http://www.vronline.com/webgame";
                return Redirect::to($jump);
            }
        }

        $resInfo = ImageHelper::url("webgame", $appid, $game['img_version'], $game['img_slider'], false);

        $game['img_list'] = $resInfo['slider'];
        if (isset($resInfo['bg']) && $resInfo['bg']) {
            $game['bg'] = static_image($resInfo['bg']);
        }

        if ($game['send_time'] < "2016-09-10") {
            $game['send_time'] = "暂无";
        }

        $class     = Config::get("webgame.class");
        $gameTypes = explode(",", $game['first_class']);
        if ($class && count($class) > 0) {
            foreach ($gameTypes as &$type) {
                $type = isset($class[$type]['name']) ? $class[$type]['name'] : "";
            }
        }

        $game['first_class'] = $gameTypes[0];

        /**
         * 先判断有没有可用的服
         */
        $myserverid  = 0;
        $maxserverid = $webGameModel->maxServeridByAppid($appid);
        if ($maxserverid) {
            /**
             * 我玩过的最后一个服
             */
            $myserverid = $webGameModel->getLastGameServerid($uid, $appid);

            if (!$myserverid) {
                $recommend = $webGameModel->recommendServer($appid);
                if ($recommend && is_array($recommend)) {
                    $newest = end($recommend);
                    if ($newest && is_array($newest)) {
                        $myserverid = $newest['serverid'];
                    }
                }
            }
            if (!$myserverid) {
                $myserverid = $maxserverid;
            }
        }
        if ($myserverid) {
            $playedServer = $webGameModel->getOneServer($appid, $myserverid);
        }

        $commentModel = new CommentModel();
        $commentInfo  = $commentModel->getComments($uid, $appid, 1);

        $game = $game + $commentInfo;

        /**
         * 是否需要背景图片
         */
        $needbg             = 1;
        $no_bac_transparent = 1;

        $notice      = Config::get("notice.{$appid}");
        $giftInfoArr = $webGameModel->getAllGift($appid);
        $noLeft      = 1;

        return view('website.webgame.detail', compact("game", "appid", "needbg", 'notice', "playedServer", "needbg", "giftInfoArr", "myserverid", "noLeft", "no_bac_transparent"));
    }

    /**
     * 获取分页的接口
     * @param $array
     * @param $keys
     * @param string $type
     */
    public function vrPageDate(Request $request)
    {
        $webgame  = new WebgameModel();
        $page     = $request->input('page');
        $pageSize = 12;
        $gameType = 1;
        $gameInfo = $webgame->vrPageDate($gameType, $page, $pageSize);
        $imgUri   = Config::get('resource.img_host') . '/vrgameimg/pub/';

        $htmlTags = '';
        if ($gameInfo) {
            foreach ($gameInfo as $k => $v) {
                $gameInfo['imginfo']['logo'] = $imgUri . $v['appid'] . '/logo';
                $gameLogo                    = $imgUri . $v['appid'] . '/logo';
                $htmlTags .= '<li class="fl pr" appid="' . $v["appid"] . '"><a href="/vrgame/' . $v["appid"] . '"><img src="' . $gameLogo . '"></a><p class="clearfix pa"><span class="fl">' . $v['name'] . '</span><i class="fr win"></i></p></li>';
            }
        }
        return $htmlTags;
    }

    /**
     * 充值
     *
     * @return \Illuminate\Http\Response
     */
    public function charge(Request $request)
    {

        $userinfo = $request->userinfo;

        $uid   = $userinfo["uid"];
        $token = $userinfo["token"];

        $account = new Account(Config::get("common.uc_appid"), Config::get("common.uc_key"), Config::get("common.uc_paykey"));
        $ret     = $account->info($uid, $token);
        if (!$ret || $ret["code"] != 0) {
            return view('login');
        }

        /**
         * 通过游戏进充值页面，传openid或uid
         * 校验传个用户id和当前登录的用户id是否是同一个人
         */
        $openid   = $request->input("openid", "");
        $gameuid  = $request->input("uid", "");
        $appid    = intval($request->input("appid"));
        $serverid = intval($request->input("sid"));
        if ($openid) {
            $check_info = App\Models\OpenidModel::getUid($openid);
            if (!$check_info || !is_array($check_info) || !isset($check_info['uid']) || $uid != $check_info['uid']) {
                return view('website.error.charge', compact("appid", "serverid"));
            }
        }
        if ($gameuid) {
            if ($uid != $gameuid) {
                return view('website.error.charge', compact("appid", "serverid"));
            }
        }

        $money = $ret["data"]["money"];

        $tokeninfo = $account->getPayToken($uid, $token);
        if (isset($tokeninfo['code']) && $tokeninfo['code'] == 0) {
            $paytoken = $tokeninfo['data']['paytoken'];
        } else {
            $paytoken = "";
        }

        $payRate = Config::get("common.pay_rate");

        $isdev = 0;

        $webGameModel = App::make('webGame');

        /**
         * 测试账号，显示所有游戏，用于测试未上线的游戏
         */
        if ($uid >= 100 and $uid <= 120) {
            $allgame = $webGameModel->getAllGameInfo(0, 0, 0, -1);
        } else {
            $allgame = $webGameModel->getAllGameInfo();
        }

        $env = Library::getCurrEnv();

        $isSuperUser = $webGameModel->isTestAccount($uid, 0);

        $role = 0;
        if ($isSuperUser) {
            $role = 1;
        }

        $payChannels = Library::getPayChannels("charge", $role);
        $banks       = config::get("bank");

        return view('website.charge', compact("allgame", "money", "payRate", "isdev", "current", "paytoken", "env", "appid", "serverid", "payChannels", "banks"));
    }

    /**
     * 用户资料
     *
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {

        $userinfo = $request->userinfo;
        // 得到uid 和 token
        $uid   = $userinfo['uid'];
        $token = $userinfo['token'];

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $res          = $accountModel->info($uid, $token);

        if ($res['code'] == 0) {
            $userinfo               = array();
            $userinfo['account']    = $res['data']['account'];
            $userinfo['nick']       = $res['data']['nick'];
            $userinfo['bindmobile'] = $res['data']['bindmobile'];
            $userinfo['faceUrl']    = $res['data']['faceUrl'];
            $userinfo['account']    = $res['data']['account'];
            // 得到最后一次登录时间以及ip所在地

            $record = $accountModel->getLoginRecord($uid);
            if ($record['code'] == 0) {
                // 如果code = 0表示有登录记录
                $userinfo['last_month'] = date("Y年m月d日", $record['data']['ts']); // 08/29
                $userinfo['last_time']  = date("H:i", $record['data']['ts']); // 16:16
                $userinfo['country']    = IPSearch::find($record['data']['ip']);
                // 判断是否是一个数组
                if ($userinfo['country'] && is_array($userinfo['country']) && count($userinfo['country']) > 0) {
                    $countryKey = isset($userinfo['country'][11]) ? $userinfo['country'][11] : 'unknown'; // 国家
                    $city       = isset($userinfo['country'][9]) ? $userinfo['country'][9] : 0; // 具体城市

                    if ($countryKey != 'unknown') {
                        $country = Config::get("country_list.{$countryKey}"); // 得到具体的国家

                        if ($countryKey == 'CN' && $city != 0) {
                            // 如果是中国
                            $country = Config::get("china_city_code.{$city}"); // 得到具体的城市
                            $country = implode('-', $country);
                        }
                    }
                }
                $userinfo['country'] = isset($country) ? $country : $record['data']['ip']; //
            }

            return view('website.profile', compact('userinfo'));
        }
    }

    public function profileProblem()
    {
        return view('website.profile_problem', ['cur' => 'problem']);
    }

    public function profileAbout()
    {
        return view('website.profile_problem', ['cur' => 'about']);
    }

    public function profileVideo(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $a        = $request->input('a');
        $a        = $a ? $a : "my";

        $out = ['a' => $a];
        switch ($a) {
            case "history":
                $videoModel = new VideoModel();
                $historys   = $videoModel->getVideoHistoryPage($uid);
                $videos     = [];
                if ($historys) {
                    $videoids = [];
                    foreach ($historys as $history) {
                        $videoids[] = $history['appid'];
                    }
                    $videoArr = $videoModel->getMultiVideoInfoByIds($videoids);
                    foreach ($videoArr as $key => $video) {
                        $videos[$video['video_id']] = $video;
                    }
                } else {
                    $historys = [];
                }
                $out['historys'] = $historys;
                $out['videos']   = $videos;
                break;
            case "my":
                $t             = $request->input('t');
                $t             = $t ? $t : "all";
                $videoModel    = new VideoModel();
                $devVideos     = $videoModel->getDevVideoByUid($uid, $t);
                $out['videos'] = $devVideos;
                $out['t']      = $t;
                break;
            case "upload":
                $id = intval($request->input('id'));
                if ($id > 0) {
                    $videoModel = new VideoModel();
                    $video      = $videoModel->getDevVideoById($id);
                } else {
                    $video = array(
                        'video_id'    => 0,
                        'video_name'  => '',
                        'video_intro' => '',
                        'video_class' => '',
                        'video_cover' => '',
                        'video_vr'    => 1,
                        'video_link'  => '',
                    );
                }
                $out['video'] = $video;
                break;
        }

        return view('website.profile_video', $out);

    }

    public function profileVideoSave(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $video_id = intval($request->input('video_id'));
        if ($video_id < 1) {
            $info['video_uid'] = $uid;
        }
        $info['video_stat']      = 1;
        $info['video_name']      = $request->input('video_name');
        $info['video_intro']     = $request->input('video_intro');
        $info['video_class']     = $request->input('video_class');
        $info['video_cover']     = $request->input('video_cover');
        $info['video_link']      = $request->input('video_link');
        $info['video_link_tp']   = 1;
        $info['video_copyright'] = 1;
        $info['video_vr']        = intval($request->input('video_vr'));

        $videoModel = new VideoModel();
        $ret        = $videoModel->saveDevVideoInfo($video_id, $info);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 下载页
     *
     * @return \Illuminate\Http\Response
     */
    public function down()
    {
        $checkUpdateModel = new CheckUpdateModel;
        $vinfo            = $checkUpdateModel->latestClientDown();
        $tinfo            = $checkUpdateModel->clientOnlinepre();
        $version          = isset($vinfo['info']['version']) ? $vinfo['info']['version'] : "1.0";
        $whilesize        = isset($vinfo['info']['whole_size']) ? trim(strtoupper($vinfo['info']['whole_size']), "M") : "0";
        $whileurl         = isset($vinfo['download']['client']) ? $vinfo['download']['client'] : "";
        $downs            = Config::get("vrclient.latest_client.updateol");
        $toolsize         = isset($tinfo['info']['size']) ? trim(strtoupper($tinfo['info']['size']), "M") : "0";
        $toolurl          = isset($tinfo['url']) ? $tinfo['url'] : "";
        $current          = "down";

        return view('website.down', compact('version', 'whilesize', 'whileurl', 'toolsize', 'toolurl', 'current'));
    }

    /**
     * 关于vrOnline页
     */
    public function vrOnline()
    {
        $checkUpdateModel = new CheckUpdateModel;
        $vinfo            = $checkUpdateModel->latestClientDown();
        $tinfo            = $checkUpdateModel->clientOnlinepre();
        $version          = isset($vinfo['info']['version']) ? $vinfo['info']['version'] : "1.0";
        $whilesize        = isset($vinfo['info']['whole_size']) ? trim(strtoupper($vinfo['info']['whole_size']), "M") : "0";
        $whileurl         = isset($vinfo['download']['client']) ? $vinfo['download']['client'] : "";
        $downs            = Config::get("vrclient.latest_client.updateol");
        $toolsize         = isset($tinfo['info']['size']) ? trim(strtoupper($tinfo['info']['size']), "M") : "0";
        $toolurl          = isset($tinfo['url']) ? $tinfo['url'] : "";
        $current          = "vronline";

        return view('website.vronline', compact('version', 'whilesize', 'whileurl', 'toolsize', 'toolurl', 'current'));
    }

    /**
     * 供flash登入注册
     *
     * @param  int $appid 应用的appid
     * @return \Illuminate\Http\Response
     */
    public function flashLogin(Request $request)
    {
        $appid    = (int) $request->input("appid");
        $serverid = (int) $request->input("serverid", 0);
        $adid     = (int) $request->input("adid", 0);

        if (!$appid || !$adid) {
            return $this->outputJsonWithCode(2001);
        }

        $account = Cookie::get("account");

        return view('website.flash.login', compact("appid", "serverid", "adid", "account"));
    }

    /**
     * 是否有可用服务器
     *
     * @param  int $appid 应用的appid
     * @return json
     */
    public function isValidServer($appid)
    {
        if (!$appid) {
            return 0;
        }
        $webgameLogicModel = new WebgameLogicModel;
        $lastserverid      = $webgameLogicModel->checkServer($appid);
        if ($lastserverid > 0) {
            return 1;
        }
        return 0;
    }

    /**
     * 根据游戏、服务器id判断登入用户是否存在角色
     *
     * @param  int $appid 游戏id
     * @param  int $serverid 服务器id
     * @return \Illuminate\Http\Response
     */
    public function hasRole(Request $request)
    {
        $appid    = intval($request->input("appid"));
        $serverid = intval($request->input("serverid"));

        if ($appid <= 0 || $serverid <= 0) {
            return $this->outputJsonWithCode(2001);
        }

        $uid = Cookie::get("uid");

        if (!file_exists(app_path() . "/Models/WebGame/Webgame" . $appid . "Class.php")) {
            return $this->outputJsonWithCode(0);
        }

        $webGameModel = App::make('webGame');

        $ainfo = $webGameModel->getOneGameInfo($appid);
        if (!$ainfo) {
            return $this->outputJsonWithCode(2304);
        }

        $sinfo = $webGameModel->getOneServer($appid, $serverid);
        if (!$sinfo) {
            return $this->outputJsonWithCode(2307);
        }

        $openid = App\Models\OpenidModel::getOpenid($appid, $uid);
        if (!$openid) {
            return $this->outputJsonWithCode(1, "获取openid失败");
        }

        $modelName = "App\Models\WebGame\Webgame" . $appid . "Class";

        $ret = true;
        $ret = $modelName::getRole($serverid, "", $uid, $openid, $ainfo, $sinfo);
        if ($ret && isset($ret['has'])) {
            $ret["hasRole"] = $ret["has"];
        }

        if (!$ret || !isset($ret["hasRole"]) || !$ret["hasRole"]) {
            return $this->outputJsonWithCode(2308);
        }

        return $this->outputJsonWithCode(0);
    }

}
