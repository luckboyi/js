<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 15:44
 */
namespace App\Models;

// 引用Model
use App;
use App\Models\CacheModel;
use App\Models\SolrModel;
use App\Models\SupportModel;
use App\Models\VronlineModel;
use Config;
use DB;
use Illuminate\Database\Eloquent\Model;

class ArticleModel extends Model
{
    /*
     * 增加评论点赞数、吐槽数
     * @param   int     game_id     游戏id
     * @return  array   结果
     */
    public function getGameDetail($game_id)
    {
        if (!$game_id) {
            return false;
        }

        $vronlineModel = new VronlineModel;
        $detail        = $vronlineModel->getGameById($game_id);
        if (!$detail || !is_array($detail) || !isset($detail['game_status']) || $detail['game_status'] != 0) {
            return [];
        }

        /**
         * 图片
         */
        $pics = [];
        if (isset($detail['game_pic_num']) && $detail['game_pic_num'] > 0) {
            $pics = $vronlineModel->getGamePics($game_id, 1, $detail['game_pic_num']);
        }
        $detail['pics'] = $pics;

        $searchName = $detail['game_search_name'];
        if ($searchName) {
            $solrModel = new SolrModel;

            /**
             * 搜索相关视频
             */
            $vparams = ['tp' => 'video', 'title' => $searchName];
            $videos  = $solrModel->searchVronline($vparams, true, $videoNum);
            if ($videos && is_array($videos)) {
                $detail['game_video_num'] = $videoNum;
            } else {
                $videos                   = [];
                $detail['game_video_num'] = 0;
            }
            $detail['videos'] = $videos;

            /**
             * 搜索相关新闻
             */
            $aparams  = ['tp' => 'news', 'title' => $searchName];
            $articles = $solrModel->searchVronline($aparams, true, $articleNum);
            if ($articles && is_array($articles)) {
                $detail['game_news_num'] = $articleNum;
            } else {
                $articles                = [];
                $detail['game_news_num'] = 0;
            }
            $detail['news'] = $articles;

            /**
             * 搜索相关评测
             */
            $pparams = ['tp' => 'pc', 'title' => $searchName];
            $pingce  = $solrModel->searchVronline($pparams, true, $pingceNum);
            if ($articles && is_array($articles)) {
                $detail['game_pc_num'] = $pingceNum;
            } else {
                $pingce                = [];
                $detail['game_pc_num'] = 0;
            }
            $detail['pingce'] = $pingce;
        }

        $pos_codes = [
            'rankvrgame' => ['code' => 'game-rank-vrgame-mostpv', 'num' => 7],   // 人气游戏榜
            'hottopic'   => ['code' => 'game-detail-subject', 'num' => 2],   // 热点专题
        ];
        $recommends = [];
        foreach($pos_codes as $flag => $info) {
            $code = $info['code'];
            $codes[] = $code;
            $num  = $info['num'];
            $recommend = $vronlineModel->getTopByCode($code, 0, $num);
            if(!$recommend || !is_array($recommend)) {
                $recommend = [];
            }
            $recommends[$flag] = $recommend;
        }
        $detail['recommend'] = $recommends;
        return $detail;
    }

    /*
     * 增加评论点赞数、吐槽数
     * @param   int     game_id     游戏id
     * @return  array   结果
     */
    public function gameSearch($searchParams)
    {
        $solrModel = new SolrModel;
        $games = $solrModel->searchVronline($searchParams, true, $gameNum);

        return ['result' => $games, 'num' => intval($gameNum)];
    }

    /*
     * 游戏详情页的专题
     * @param   string  pos_code  推荐位关系码
     * @return  array
     */
    public function getTopicInGameDetail($pos_code)
    {
        if (!$pos_code) {
            return false;
        }

        $vronlineModel = new VronlineModel;
        $topid         = $vronlineModel->getTopByCode($pos_code, 0, 2);
        if (!$topid || !is_array($topid)) {
            return [];
        }

        return $topid;
    }

    /**
     * 查询未审核的评论，用于审核
     *
     * @param  int  page        页数
     * @param  int  len         记录数
     * @return arr          评论数组
     */
    public function gameHome()
    {
        $pos_codes = [
            'topbanner'     => ['code' => 'game-home-topbanner', 'num' => 1],       // 游戏首页顶部banner
            'topgame0'      => ['code' => 'game-index-topgame0', 'num' => 5],        // 游戏首页左上角 VRonline推荐
            'topgame1'      => ['code' => 'game-index-topgame1', 'num' => 5],      // 游戏首页左上角 oculus推荐
            'topgame2'      => ['code' => 'game-index-topgame2', 'num' => 5],         // 游戏首页左上角 htc推荐
            'topgame3'      => ['code' => 'game-index-topgame3', 'num' => 5],        // 游戏首页左上角 psvr推荐
            'topleftslider' => ['code' => 'game-index-slider', 'num' => 5],         // 游戏首页左上角 滚动推荐
            'companylogo'   => ['code' => 'game-home-left-company', 'num' => 1],    // 游戏首页左侧中部游戏厂商logo
            'gamevideo'     => ['code' => 'game-home-left-gamevideo', 'num' => 1],  // 游戏首页左侧中部游戏视频
            'toprighttopic' => ['code' => 'game-home-topright-topic', 'num' => 2],  // 游戏首页右侧上部栏目专题
            'rightadv'      => ['code' => 'game-home-right-adv', 'num' => 2],       // 游戏首页右侧广告
            'newgame1'      => ['code' => 'game-home-right-newgame1', 'num' => 5],   // 游戏首页右侧底部新游推荐1
            'newgame2'      => ['code' => 'game-home-right-newgame2', 'num' => 5],   // 游戏首页右侧底部新游推荐2
            'newgame3'      => ['code' => 'game-home-right-newgame3', 'num' => 5],   // 游戏首页右侧底部新游推荐3
            'middlenews'    => ['code' => 'game-home-middle-news', 'num' => 3],     // 游戏首页中部游戏专区推荐
            'bottomnews1'   => ['code' => 'game-home-bottom-news1', 'num' => 4],     // 游戏首页底部新闻1
            'bottomnews2'   => ['code' => 'game-home-bottom-news2', 'num' => 4],     // 游戏首页底部新闻2
            'bottomnews3'   => ['code' => 'game-home-bottom-news3', 'num' => 4],     // 游戏首页底部新闻3
            'hotpc'         => ['code' => 'game-home-bottom-hotpc', 'num' => 10],   // 游戏首页热门评测
            'newpc1'        => ['code' => 'game-home-bottom-newpc1', 'num' => 1],   // 游戏首页最新评测1
            'newpc2'        => ['code' => 'game-home-bottom-newpc2', 'num' => 1],   // 游戏首页最新评测2
            'newpc3'        => ['code' => 'game-home-bottom-newpc3', 'num' => 1],   // 游戏首页最新评测3
            'bottomrank1'   => ['code' => 'game-home-bottom-rank1', 'num' => 3],   // 游戏首页底部榜单1
            'bottomrank2'   => ['code' => 'game-home-bottom-rank2', 'num' => 3],   // 游戏首页底部榜单2
            'rankvrgame'    => ['code' => 'game-rank-vrgame-mostpv', 'num' => 3],   // 游戏首页底部榜单2
            'rankvideo'     => ['code' => 'game-rank-video-mostpv', 'num' => 3],   // 游戏首页底部榜单2
        ];
        $recommends = [];
        $vronlineModel = new VronlineModel;
        foreach($pos_codes as $flag => $info) {
            $code = $info['code'];
            $codes[] = $code;
            $num  = $info['num'];
            $recommend = $vronlineModel->getTopByCode($code, 0, $num);
            if(!$recommend || !is_array($recommend)) {
                $recommend = [];
            }
            $recommends[$flag] = $recommend;
        }
        $pos = $vronlineModel->getPosByCode($codes);
        if(!$pos || !is_array($pos)) {
            $pos = [];
        }
        $tmppos = $postions = [];
        for($i = 0; $i < count($pos); $i++) {
            $tmppos[$pos[$i]['pos_code']] = $pos[$i];
        }
        foreach($pos_codes as $flag => $info) {
            $code = $info['code'];
            if(isset($tmppos[$code])) {
                $postions[$flag] = $tmppos[$code];
            }
        }

        $solrModel = new SolrModel;
        $params = ['tp' => 'news', 'title' => "oculus"];
        $oculusNews = $solrModel->searchVronline($params);
        $params = ['tp' => 'news', 'title' => "HTC"];
        $htcNews = $solrModel->searchVronline($params);
        $params = ['tp' => 'news', 'title' => ["PS","VR"]];
        $psvrNews = $solrModel->searchVronline($params);

        $params = ['tp' => 'news', 'category' => 6];
        $news = $solrModel->searchVronline($params);
        return ['recommend' => $recommends, 'pos' => $postions, 'oculusNews' => $oculusNews, 'htcNews' => $htcNews, 'psvrNews' => $psvrNews, "news" => $news];
    }

    /*
     * 增加评论点赞数、吐槽数
     * @param   int     game_id     游戏id
     * @return  array   结果
     */
    public function gameList()
    {
        $solrModel = new SolrModel;
        $searchParams = ['tp' => 'game'];
        $games = $solrModel->searchVronline($searchParams);

        $pos_codes = [
            'downmost'      => ['code' => 'game-search-down-most', 'num' => 4],             // 一周下载榜
            'hottyperecomm' => ['code' => 'game-search-right-category', 'num' => 20],       // 热门类型
        ];
        $vronlineModel = new VronlineModel;
        foreach($pos_codes as $flag => $info) {
            $code = $info['code'];
            $codes[] = $code;
            $num  = $info['num'];
            $recommend = $vronlineModel->getTopByCode($code, 0, $num);
            if(!$recommend || !is_array($recommend)) {
                $recommend = [];
            }
            $recommends[$flag] = $recommend;
        }
        return ['newest' => $games, 'recommend' => $recommends];
    }

    public function addPv($type, $itemid)
    {
        if(!$type || !$itemid) {
            return false;
        }
        $vronlineModel = new VronlineModel;

        $ret = false;
        switch($type) {
            case "news_video":
            case "news_news":
                $info = ['article_view_num' => 1];
                $ret = $vronlineModel->incArticleNum($itemid, $info);
                break;
            case "news_game":
                $info = ['game_view_num' => 1];
                $ret = $vronlineModel->incGameNum($itemid, $info);
                break;
            default:    return false;break;
        }
        return $ret;
    }

    public function support($uid, $action, $type, $itemid)
    {
        if (!$uid || !$action || !$type || !$itemid) {
            return false;
        }

        $info = [];
        switch($action) {
            case "up":
                $info = ['article_agree_num' => 1];
                break;
            case "down":
                $info = ['article_disagree_num' => 1];
                break;
            default:    return false;
        }

        $supportModel = new SupportModel;
        $ismem = $supportModel->isSupported($uid, $itemid, "news_video");
        if($ismem === false) {
            return false;
        }if($ismem === null) {
        }else {
            return "already";
        }
        $ret = $supportModel->add($uid, $itemid, "news_video", $action);
        $vronlineModel = new VronlineModel;
        $ret = $vronlineModel->incArticleNum($itemid, $info);
        return $ret;
    }

    /**
     * 根据评分生成星级
     */
    public function getStarByScore($score)
    {
        if ($score <= 0) {
            $score = 0;
        }
        $tmp1 = $score / 2;
        $star = floor($tmp1);
        $half = $tmp1 - $star;
        if ($half >= 0.5) {
            $star = "scorehafl" . $star;
        } else {
            $star = "score" . $star;
        }
        return $star;
    }

}
