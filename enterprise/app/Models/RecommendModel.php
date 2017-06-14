<?php

/*
推荐相关model
date:2017/5/18
 */

namespace App\Models;

use App\Helper\ImageHelper;
use App\Models\GameModel;
use App\Models\OperateModel;
use App\Models\VideoModel;
use DB;
use Illuminate\Database\Eloquent\Model;

class RecommendModel extends Model
{

    /**
     * 根据位置码，获取某个推荐位的位置ID
     * @param   string  code    位置码
     * @return  int     posid   位置id
     */
    public function getPosId($code)
    {
        if (!$code) {
            return false;
        }
        $row = DB::connection("db_operate")->table("top_postion")->where("code", $code)->first();
        return $row;
    }

    /**
     * 获取某个推荐位的内容ID列表
     * @param   string  code    位置码
     * @return  array
     */
    public function getItemsByPosId($posid, $num = false)
    {
        if (!$posid) {
            return false;
        }
        $row = DB::connection("db_operate")->table("top_recommend")->where("posid", $posid)->orderBy("weight", "desc");
        $num = (int) $num;
        if ($num > 0) {
            $row = $row->take($num);
        }
        return $row->get();
    }

    /**
     * 获取某个推荐位的内容ID列表
     * @param   string  code    位置码
     * @return  array
     */
    public function getRecommendContentByCode($code, $num = false)
    {
        if (!$code) {
            return false;
        }

        $posinfo = $this->getPosId($code);
        if (!$posinfo) {
            return false;
        }
        $posid = $posinfo['posid'];
        $type  = $posinfo['content_tp'];

        $info = $this->getRecommendContentByPosid($posid, $type, $num);
        if (!$info) {
            $info = array();
        }
        return array("pos" => $posinfo, "data" => $info);
    }

    public function solrRecommendContentByCode($code, $num = false)
    {
        if (!$code) {
            return false;
        }

        $posinfo = $this->getPosId($code);
        if (!$posinfo) {
            return false;
        }

        $posid = $posinfo['posid'];

        $info = $this->getRecommendContentByPosid($posid, $num);
        if (!$info) {
            $info = array();
        }
        return $info;
    }

    /**
     * 获取某个推荐位的内容ID列表
     * @param   int     posid   位置ID
     * @param   string  type    类型，webgame/vrgame/video/banner
     * @param   int     num     获取内容的数量，默认获取所有
     * @return  array
     */
    public function getRecommendContentByPosid($posid, $num = false)
    {
        if (!$posid) {
            return false;
        }
        $items = $this->getItemsByPosId($posid, $num);
        if (!$items || !is_array($items)) {
            return false;
        }
        $videoStats = $gameStats = $gameWeights = $videoWeights = $gameUnIds = $videoUnIds = [];
        $videoIds   = $gameIds   = [];
        $out        = [];
        foreach ($items as $key => $arr_detail) {
            if ($arr_detail['tp'] == "banner") {
                $banner = [
                    'id'        => $arr_detail['id'],
                    'tp'        => 'banner',
                    'image'     => json_encode([
                        'cover' => $arr_detail['banner_url'],
                        'icon'  => $arr_detail['top_icon'],
                    ]),
                    'name'      => $arr_detail['top_title'],
                    'sub_title' => $arr_detail['top_sub_title'],
                    'desc'      => $arr_detail['top_desc'],
                    'link'      => $arr_detail['target_url'],
                    'link_tp'   => intval($arr_detail['link_tp']),
                    'stat'      => $arr_detail['stat'],
                    'weight'    => $arr_detail['weight'],
                    'unid'      => $arr_detail['id'],
                ];
                $out[] = $banner;
            } else if ($arr_detail['tp'] == "video") {
                if ($arr_detail['itemid']) {
                    $videoIds[]                          = $arr_detail['itemid'];
                    $videoDetails[$arr_detail['itemid']] = $arr_detail;
                }
            } else {
                if ($arr_detail['itemid']) {
                    $gameIds[]                          = $arr_detail['itemid'];
                    $gameDetails[$arr_detail['itemid']] = $arr_detail;
                }
            }
        }

        if ($videoIds) {
            $videoModel = new VideoModel;
            $videos     = $videoModel->getVideoByIds($videoIds);
            $tp         = "video";

            foreach ($videos as $key => $video) {

                if ($videoDetails[$video['video_id']]['banner_url']) {
                    $cover = $videoDetails[$video['video_id']]['banner_url'];
                } else {
                    $cover = $video['video_cover'];
                }

                $category = arrayToInt(explode(",", $video['video_class']));
                $element  = [
                    'id'        => $video['video_id'],
                    'name'      => $videoDetails[$video['video_id']]['top_title'] ? $videoDetails[$video['video_id']]['top_title'] : $video['video_name'],
                    'score'     => roundFloat($video['agreenum']),
                    'play'      => intval($video['video_view']),
                    'tp'        => $tp,
                    'image'     => json_encode(['cover' => $cover, 'rank' => $video['video_rank']]),
                    'category'  => $category,
                    'sell'      => 0,
                    'sub_title' => $videoDetails[$video['video_id']]['top_sub_title'],
                    'stat'      => $videoDetails[$video['video_id']]['stat'],
                    'weight'    => $videoDetails[$video['video_id']]['weight'],
                    'unid'      => $videoDetails[$video['video_id']]['id'],
                    'desc'      => $videoDetails[$video['video_id']]['top_desc'] ? $videoDetails[$video['video_id']]['top_desc'] : $video['video_intro'],
                ];

                $out[] = $element;
            }
        }
        if ($gameIds) {
            $gameModel = new GameModel;
            $games     = $gameModel->getGameByIds($gameIds);
            foreach ($games as $key => $game) {
                $tp      = $game['game_type'] == 0 ? 'webgame' : 'vrgame';
                $resInfo = ImageHelper::getUrl($tp . 'img', ['id' => $game['appid'], 'version' => $game['img_version'], 'img_slider' => $game['img_slider'], 'img_screenshots' => $game['screenshots'], 'publish' => true]);
                if ($gameDetails[$game['appid']]['banner_url']) {
                    $resInfo['cover'] = $gameDetails[$game['appid']]['banner_url'];
                } else {
                    $resInfo['cover'] = $resInfo['rank'];
                }
                $category = arrayToInt(explode(",", $game['first_class']));
                $support  = arrayToInt(explode(",", $game['support']));
                $element  = [
                    'id'            => $game['appid'],
                    'name'          => $gameDetails[$game['appid']]['top_title'] ? $gameDetails[$game['appid']]['top_title'] : $game['name'],
                    'score'         => roundFloat($game['score']),
                    'play'          => $game['play'],
                    'tp'            => $tp,
                    'image'         => json_encode($resInfo),
                    'category'      => $category,
                    'original_sell' => roundFloat($game['original_sell']),
                    'sell'          => roundFloat($game['sell']),
                    'support'       => $support,
                    'desc'          => $gameDetails[$game['appid']]['top_desc'] ? $gameDetails[$game['appid']]['top_desc'] : $game['content'],
                    'publish_date'  => strtotime($game['send_time']),
                    'sub_title'     => $gameDetails[$game['appid']]['top_sub_title'],
                    'stat'          => $gameDetails[$game['appid']]['stat'],
                    'weight'        => $gameDetails[$game['appid']]['weight'],
                    'unid'          => $gameDetails[$game['appid']]['id'],
                ];
                $out[] = $element;
            }
        }

        return $out;
    }

}
