<?php

namespace App\Http\ViewComposers;

use App;
use App\Helper\ImageHelper;
use App\Models\CookieModel;
use App\Models\OpenModel;
use App\Models\RecommendModel;
use Config;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WebGameComposer
{

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function Compose(View $view)
    {
        $webgame        = App::make('webGame');
        $recommendModel = new RecommendModel();
        //获取用户的历史记录
        $webGameHistory      = [];
        $userInfo['uid']     = CookieModel::getCookie('uid');
        $userInfo['nick']    = CookieModel::getCookie('nick');
        $userInfo['account'] = CookieModel::getCookie('account');
        $userInfo['face']    = CookieModel::getCookie('face');
        if ($userInfo['uid']) {
            $webGameHistoryArr = $webgame->getWebGameHistory($userInfo['uid']);
            foreach ($webGameHistoryArr as $k => $v) {
                if ($k > 4) {
                    continue;
                }
                $webGameHistory[$k]          = $v;
                $webGameHistory[$k]['image'] = $this->getGameImages('webgame', $v['appid']);
            }
        }

        $posCode = [
            'webgame-hot' => 5,
        ];

        $webgamehot = [];
        foreach ($posCode as $code => $num) {
            $webgamehot[$code] = $recommendModel->getRecommendContentByCode($code, $num);
        }

        /**
         * 是否有首页背景图
         */
        $homebg  = 1;
        $current = "home";
        // $sid     = Session::getId();
        // $device  = md5(md5($sid) . "login");

        // $request = "http://passport.vronline.com/getImgCode/" . $device;
        // $codeImg = json_decode(file_get_contents($request), 1)['data']['img'];
        $codeImg = "http://passport.vronline.com/getImgCode";
        $view->with(compact("webGameHistory", "userInfo", "webgamehot", "codeImg"));
    }

    /**
     * 获取游戏的image资源
     */
    public function getGameImages($tp, $appid)
    {

        $webgame     = App::make('webGame');
        $webGameInfo = $webgame->getOneGameInfo($appid);
        $version     = $webGameInfo['img_version'];
        $slider      = $webGameInfo['img_slider'];
        $result      = ImageHelper::url($tp, $appid, $version, $slider, false);
        return $result;
    }

    public function oldCompose(View $view)
    {

        //获取用户id
        $uid = CookieModel::getCookie("uid");

        $environment = Config::get('common.environment');

        $webGameModel = App::make('webGame');

        $history_logs = $webGameModel->getGameLog($uid, 4);

        $appids    = [];
        $serverids = [];

        if (!$history_logs || !is_array($history_logs)) {
            $history_logs = array();
        }
        foreach ($history_logs as $history) {
            $appids[]        = $history["appid"];
            $key             = $history["appid"] . "_" . $history["serverid"];
            $serverids[$key] = [
                "serverid" => $history["serverid"],
                "appid"    => $history["appid"],
            ];
        }

        $preServers = $webGameModel->latestGameServer(5); //准备中的服务器
        $newServers = $webGameModel->newGameServer(5); //已经开启的服务器

        foreach ($preServers as $tmp) {
            $appids[] = $tmp['appid'];
        }

        foreach ($newServers as $tmp) {
            $appids[] = $tmp['appid'];
        }

        $webgamesInfo = $webGameModel->getMultiGameInfo($appids);

        if (!$webgamesInfo || !is_array($webgamesInfo)) {
            $webgamesInfo = array();
        }

        $openModel = new OpenModel;

        foreach ($webgamesInfo as $webgame) {

            $resInfo = ImageHelper::path('webgameimg', $webgame['appid'], $webgame['img_version'], $webgame['img_slider'], false);

            $webgame['img_url']          = $resInfo['history'];
            $webgames[$webgame["appid"]] = $webgame;

        }

        $serversInfo = $webGameModel->getMultiServer(array_values($serverids));
        if (!$serversInfo || !is_array($serversInfo)) {
            $serversInfo = array();
        }
        foreach ($serversInfo as $server) {
            $key           = $server["appid"] . "_" . $server["serverid"];
            $servers[$key] = $server;
        }

        $sideAds = $webGameModel::getAd("webgame_2", 2);

        $view->with(compact("history_logs", "preServers", "newServers", "webgames", "servers", "sideAds"));
    }

}
