<?php

namespace App\Http\ViewComposers;

use App;
use App\Helper\ImageHelper;
use App\Models\CookieModel;
use App\Models\SolrModel;
use App\Models\WebgameModel;
use Illuminate\Contracts\View\View;

class VrGameComposer
{

    public function compose(View $view)
    {

        //获取用户id

        $uid = CookieModel::getCookie("uid");

        $playHistory = array();
        if ($uid) {
            $webgameModel = new WebgameModel;
            $gameLog      = $webgameModel->getGameLog($uid, "all", 1);
            $appids       = array();
            if ($gameLog && is_array($gameLog)) {
                foreach ($gameLog as $key => $arr_detail) {
                    if ($arr_detail['appid']) {
                        $appids[] = $arr_detail['appid'];
                    }
                }
            }

            $playHistory = $webgameModel->getMultiGameInfo($appids);

            if ($playHistory && is_array($playHistory)) {
                foreach ($playHistory as $key => $arr_detail) {
                    $images = ImageHelper::url("vrgame", $arr_detail['appid'], $arr_detail['img_version'], $arr_detail['img_slider'], false);

                    $playHistory[$key]['images'] = $images;
                }
            }
        }

        $solrModel = new SolrModel();

        $vrgameLeftRecommend = $solrModel->getTop("vrgame-left-recommend", 5);
        //var_dump($vrgameLeftRecommend);exit;
        $view->with(compact("playHistory", "vrgameLeftRecommend"));
    }

}
