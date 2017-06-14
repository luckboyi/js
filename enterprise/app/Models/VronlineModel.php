<?php
namespace App\Models;

use App\Models\CacheModel;
use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class VronlineModel extends Model
{
    private $pageSize    = 10;
    private $picPageSize = 8;

    public function getAuthors()
    {
        $rows = DB::connection("db_vronline")->table("t_writer")->where("stat", 1)->get();
        return $rows;
    }

    public function getTopByCode($code, $startNum = 0, $pagenum = 6)
    {
        $rows = DB::connection('db_vronline')->table('t_top')->where('pos_code', $code)->orderBy('weight', 'desc')->skip($startNum)->take($pagenum)->get();

        if ($rows) {
            $out = [];
            foreach ($rows as $row) {
                switch ($row['tp']) {
                    case 'news':
                    case 'pc':
                    case 'video':
                        $id        = $row['itemid'];
                        $article   = $this->getArticleById($id);
                        $title     = $row['title'] ? $row['title'] : $article['article_title'];
                        $intro     = $row['intro'] ? $row['intro'] : $article['article_content'];
                        $cover     = $row['cover'] ? $row['cover'] : $article['article_cover'];
                        $category  = strToArrInt($article['article_category']);
                        $tag       = strToArr($article['article_tag']);
                        $device    = [];
                        $author    = $article['article_author_id'];
                        $source_tp = $article['article_video_source_tp'];
                        $time      = strtotime($article['vtime']);
                        $view      = $article['article_view_num'];
                        if ($row['tp'] == "news") {
                            $link = $row['target_url'] ? $row['target_url'] : '/vronline/article/detail/' . $article['article_id'];
                        } elseif ($row['tp'] == "pc") {
                            $link = $row['target_url'] ? $row['target_url'] : '/vronline/article/detail/' . $article['article_id'];
                        } elseif ($row['tp'] == "video") {
                            $link = $row['target_url'] ? $row['target_url'] : '/vronline/video/detail/' . $article['article_id'];
                        }
                        $agree   = $article['article_agree_num'];
                        $comment = $article['article_comment_num'];
                        $score   = $article['article_pc_match'];
                        break;
                    case 'game':
                        $id        = $row['itemid'];
                        $article   = $this->getGameById($id);
                        $title     = $row['title'] ? $row['title'] : $article['game_name'];
                        $intro     = $row['intro'] ? $row['intro'] : $article['game_desc'];
                        $cover     = $row['cover'] ? $row['cover'] : $article['game_image'];
                        $link      = $row['target_url'] ? $row['target_url'] : '/vronline/game/detail/' . $article['game_id'];
                        $category  = strToArrInt($article['game_category']);
                        $tag       = strToArr($article['game_tag']);
                        $device    = strToArrInt($article['game_device']);
                        $time      = $article['game_sell_date'];
                        $comment   = $article['game_comment_num'];
                        $score     = $article['game_mark'];
                        $source_tp = '';
                        $view      = 0;
                        break;
                    case 'banner':
                        $id        = $row['itemid'];
                        $title     = $row['title'];
                        $intro     = $row['intro'];
                        $cover     = $row['cover'];
                        $link      = $row['target_url'];
                        $tag       = [];
                        $category  = [];
                        $device    = [];
                        $time      = strtotime($row['ltime']);
                        $source_tp = 2;
                        $view      = 0;
                        break;
                }
                $tmp = [
                    'itemid'     => $id,
                    'title'      => $title,
                    'intro'      => $intro,
                    'cover'      => $cover,
                    'target_url' => $link,
                    'source_tp'  => $source_tp,
                    'category'   => $category,
                    'tp'         => $row['tp'],
                    'code'       => $code,
                    'weight'     => $row['weight'],
                    'stat'       => 0,
                    'score'      => isset($score) ? $score : 0,
                    'view'       => $view,
                    'agree'      => isset($agree) ? $agree : 0,
                    'comment'    => isset($comment) ? $comment : 0,
                    'device'     => $device,
                    'tag'        => $tag,
                    'time'       => $time,
                ];
                if (isset($author) && $author) {
                    $tmp['author'] = $article['article_author_id'];
                }
                $out[] = $tmp;
            }
            return $out;
        }
        return false;
    }

    public function getPosByCode($codes)
    {
        if (!$codes || !is_array($codes)) {
            return false;
        }
        $rows = DB::connection('db_vronline')->table('t_position')->whereIn('pos_code', $codes)->get();
        return $rows;
    }

    public function searchContent($tp, $content)
    {
        $row = array();
        switch ($tp) {
            case "news":
                $row = DB::connection("db_vronline")->table("t_article")->select('article_id as id', 'article_title as title')->where('article_tp', 'news')->where('article_title', 'like', '%' . $content . '%')->get();
                break;
            case "pc":
                $row = DB::connection("db_vronline")->table("t_article")->select('article_id as id', 'article_title as title')->where('article_tp', 'pc')->where('article_title', 'like', '%' . $content . '%')->get();
                break;
            case "video":
                $row = DB::connection("db_vronline")->table("t_article")->select('article_id as id', 'article_title as title')->where('article_tp', 'video')->where('article_title', 'like', '%' . $content . '%')->get();
                break;
            case "game":
                $row = DB::connection("db_vronline")->table("t_game")->select('game_id as id', 'game_name as title')->where('game_name', 'like', '%' . $content . '%')->get();
                break;
        }
        return $row;
    }

    public function getDevNews($tp = "news", $class_id = 0, $search = '')
    {

        $res = DB::connection('db_vronline')->table('draft_article')->where('article_tp', $tp);
        if ($class_id > 0) {
            $findCase = 'FIND_IN_SET(' . $class_id . ', article_category)';
            $res->whereRaw($findCase);
        }

        if ($search) {
            if (is_numeric($search)) {
                $res->where("article_id", $search);
            } else {
                $res->where("article_title", "LIKE", '%' . $search . '%');
            }
        }

        $row = $res->orderBy('ctime', 'desc')->paginate($this->pageSize);

        return $row;
    }
    public function getDevNewsForWeb($tp = "news", $class_id = 0, $pageSize = '')
    {

        $res = DB::connection('db_vronline')->table('t_article')->where('article_tp', $tp);
        if ($class_id > 0) {
            $findCase = 'FIND_IN_SET(' . $class_id . ', article_category)';
            $res->whereRaw($findCase);
        }
        if (!$pageSize) {
            $pageSize = $this->pageSize;
        }

        $row = $res->orderBy('ctime', 'desc')->paginate($pageSize);

        return $row;
    }

    public function getDevAuditNews($class_id)
    {
        if ($class_id > 0) {
            $row = DB::connection('db_dev')->table('t_news')->where('stat', 1)->where('tp', $class_id)->orderBy('ltime', 'desc')->paginate($this->newsDevSize);
        } else {
            $row = DB::connection('db_dev')->table('t_news')->where('stat', 1)->orderBy('ltime', 'desc')->paginate($this->newsDevSize);
        }

        return $row;
    }

    public function getDevNewsById($id)
    {
        $row = DB::connection('db_dev')->table('t_news')->where('id', $id)->first();
        return $row;
    }

    public function updateDevNews($id, $info)
    {
        if (!$info) {
            return false;
        }
        if ($id > 0) {
            $row = DB::connection('db_vronline')->table('draft_article')->select('article_stat')->where('article_id', $id)->first();
            if ($row['article_stat'] == 1) {
                return false;
            }
            $ret = DB::connection('db_vronline')->table('draft_article')->where('article_id', $id)->update($info);
        } else {
            $ret = DB::connection('db_vronline')->table('draft_article')->insertGetId($info);
        }

        return $ret;
    }

    public function passDevNews($id)
    {
        $row = DB::connection('db_vronline')->table('draft_article')->where('article_id', $id)->first();
        if ($row) {
            $row['article_stat'] = 0;
            unset($row['ctime']);
            unset($row['ltime']);
            $vtime        = date("Y-m-d H:i:s");
            $row['vtime'] = $vtime;
            $ret          = DB::connection('db_vronline')->table('t_article')->replace($row);
            if ($ret) {
                $ret = DB::connection('db_vronline')->table('draft_article')->where('article_id', $id)->update(['article_stat' => 0, 'vtime' => $vtime]);
            }
        }

        return $ret;
    }

    public function delDevNews($id)
    {
        $row = DB::connection('db_vronline')->table('t_article')->where('article_id', $id)->first();
        if ($row) {
            $ret1 = DB::connection('db_vronline')->table('t_article')->where('article_id', $id)->update(['article_stat' => 9]);
            $ret2 = DB::connection('db_vronline')->table('draft_article')->where('article_id', $id)->update(['article_stat' => 9]);
            return $ret1 && $ret2;
        } else {
            $ret = DB::connection('db_vronline')->table('draft_article')->where('article_id', $id)->delete();
            return $ret;
        }
    }

    public function getArticlesByIds($ids)
    {
        $row = DB::connection("db_vronline")->table("t_article")->select('article_id', 'article_title', 'article_cover', 'article_content', 'article_tp')->whereIn("article_id", $ids)->get();
        return $row;
    }
    public function getArticlesById($id)
    {
        $row = DB::connection('db_vronline')->table('t_article')->where('article_id', $id)->first();
        return $row;
    }
    /**
     * 获取某个视频后的10条
     * [getArticlesByIdAfter description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getArticlesByIdAfter($id)
    {
        $where = [
            'article_tp' => 'video',
        ];
        $row = DB::connection('db_vronline')->table('t_article')->where('article_id', '>', $id)->where($where)->orderBy('article_id')->take(10)->get();
        return $row;
    }
    public function getGamesByIds($ids)
    {
        $row = DB::connection("db_vronline")->table("t_game")->select('game_id', 'game_name', 'game_image', 'game_desc', 'game_category')->whereIn("game_id", $ids)->get();
        return $row;
    }

    public function updateTop($id, $info)
    {
        if (!$info) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection("db_vronline")->table("t_top")->where('id', $id)->update($info);
        } else {
            $retId = DB::connection("db_vronline")->table("t_top")->insertGetId($info);
            if ($retId) {
                $ret = DB::connection("db_vronline")->table("t_top")->where("id", $retId)->update(['weight' => $retId]);
            } else {
                $ret = false;
            }
        }
        return $ret;
    }

    public function delTop($topId = 0)
    {
        if (!$topId) {
            return false;
        }

        $ret = DB::connection("db_vronline")->table("t_top")->where('id', $topId)->delete();
        return $ret;
    }

    public function topByCode($pos_code)
    {
        $row = DB::connection("db_vronline")->table("t_top")->where('pos_code', $pos_code)->where('stat', 0)->orderBy("weight", "desc")->get();
        return $row;
    }

    public function topPostionWeight($drag, $drop)
    {
        $rowDrag = DB::connection("db_vronline")->table("t_top")->select('weight')->where('id', $drag)->first();
        $rowDrop = DB::connection("db_vronline")->table("t_top")->select('weight')->where('id', $drop)->first();

        if (!$rowDrag || !$rowDrop) {
            return false;
        }
        $ret1 = DB::connection("db_vronline")->table("t_top")->where("id", $drag)->update(['weight' => $rowDrop['weight']]);
        $ret2 = DB::connection("db_vronline")->table("t_top")->where("id", $drop)->update(['weight' => $rowDrag['weight']]);

        return $ret1 && $ret2;
    }

    public function position($tp, $page = 12)
    {
        $res = DB::connection("db_vronline")->table("t_position");
        if ($tp) {
            $res->where('pos_group', $tp);
        }
        if ($page > 0) {
            $row = $res->orderBy("ctime", "asc")->paginate($page);
        } else {
            $row = $res->orderBy("ctime", "asc")->get();
        }
        return $row;
    }

    public function updatePostion($pos_id = 0, $info)
    {
        if (!$info) {
            return false;
        }
        if (!$pos_id) {
            $ret = DB::connection("db_vronline")->table("t_position")->insert($info);
        } else {
            $ret = DB::connection("db_vronline")->table("t_position")->where('pos_id', $pos_id)->update($info);
        }
        return $ret;
    }

    public function delPostion($pos_id = 0)
    {
        if (!$pos_id) {
            return false;
        }

        $ret = DB::connection("db_vronline")->table("t_position")->where('pos_id', $pos_id)->delete();
        return $ret;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |                           游    戏                                          |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 添加文章内容
     */
    public function addGame($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        try {
            $ret = DB::connection("db_vronline")->table('t_game')->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }
    /**
     * Admincp后台保存和修改接口
     * [saveGameInfo description]
     * @param  [type] $id   [description]
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function saveGameInfo($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }

        try {
            if ($id > 0) {
                $ret = DB::connection("db_vronline")->table('t_game')->where('game_id', $id)->update($info);
            } else {
                $ret = DB::connection("db_vronline")->table('t_game')->insert($info);
            }

        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }
    /**
     * 获取Admincp后台的数据列表
     * [getGamesList description]
     * @return [type] [description]
     */
    public function getGamesList($searchText)
    {
        try {
            $ret = DB::connection("db_vronline")->table('t_game');
            if ($searchText) {
                $ret->where("game_name", "LIKE", '%' . $searchText . '%');
            }
            $row = $ret->orderBy('ltime', 'desc')->paginate($this->pageSize);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $row;
    }

    /**
     * 获取文章内容
     */
    public function getGameById($game_id, $fields = null)
    {
        if (!$game_id) {
            return false;
        }
        try {
            $raw = DB::connection("db_vronline")->table('t_game')->where("game_id", $game_id);
            if ($fields) {
                $raw = $raw->select($fields);
            }
            $row = $raw->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($row === null) {
            $row = [];
        }
        return $row;
    }

    /**
     * 修改文章内容
     */
    public function updGameById($game_id, $info)
    {
        if (!$game_id || !$info || !is_array($info)) {
            return false;
        }
        try {
            $ret = DB::connection("db_vronline")->table('t_game')->where("game_id", $game_id)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 取文章点赞、评论、浏览数量
     *
     * @param  int  article_id  id
     * @return arr          数组
     */
    public function getGameNums($game_id)
    {
        if (!$game_id) {
            return false;
        }

        $fields     = ['game_view_num', 'game_comment_num'];
        $cacheModel = new CacheModel;
        $counts     = $cacheModel->getCounts("t_game", $game_id, $fields);
        if (is_array($counts) && isset($counts['game_view_num'])) {
            return $counts;
        }

        $info = $this->getGameById($game_id);
        if (!$info || !is_array($info)) {
            return false;
        }
        for ($i = 0; $i < count($fields); $i++) {
            $field          = $fields[$i];
            $counts[$field] = isset($info[$field]) ? intval($info[$field]) : 0;
        }
        $cacheModel->setCounts("t_game", $game_id, $counts);
        return $counts;
    }

    /**
     * 删除游戏信息操作
     * [delGame description]
     * @param  [type] $game_id [description]
     * @return [type]          [description]
     */
    public function delGame($game_id)
    {
        if (!$game_id) {
            return false;
        }

        $ret = DB::connection("db_vronline")->table("t_game")->where('game_id', $game_id)->delete();
        return $ret;
    }
    /**
     * 增加游戏的点赞、回复等数量
     * @param  int      id      评论id
     * @param  array    info   修改信息 ['up_num' => 1, 'down_num' => -1]
     * @return bool
     */
    public function incGameNum($id, $info)
    {
        if (!$id || !$info || !is_array($info)) {
            return false;
        }

        try {
            $uinfo      = [];
            $setCache   = true;
            $cacheModel = new CacheModel;
            foreach ($info as $field => $num) {
                $num = intval($num);
                if ($num == 0) {
                    unset($info[$field]);
                    continue;
                }
                $exists = $cacheModel->countExists("t_game", $id, $field);
                if (!$exists) {
                    $setCache = false;
                }
                $uinfo[$field] = DB::raw("{$field} + {$num}");
            }
            $ret = DB::connection("db_vronline")->table('t_game')->where("game_id", $id)->update($uinfo);
            if ($ret && $setCache) {
                foreach ($info as $field => $num) {
                    $cacheModel->incCounts("t_game", $id, $field, $num);
                }
            }
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 添加游戏图片
     */
    public function addGamePic($game_id, $url)
    {
        if (!$game_id || !$url) {
            return false;
        }
        try {
            $info   = ['game_id' => $game_id, 'game_pic_url' => $url];
            $ret    = DB::connection("db_vronline")->table('t_game_pic')->insert($info);
            $idArr  = DB::connection("db_vronline")->table('t_game_pic')->orderBy('id', 'desc')->skip(0)->take(1)->get();
            $count  = $this->getGamePicNum($game_id);
            $update = [
                'game_pic_num' => $count,
            ];
            $result = DB::connection("db_vronline")->table('t_game')->where("game_id", $game_id)->update($update);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($ret) {
            return $idArr;
        }
        return $ret;
    }

    /**
     * 删除游戏图片
     */
    public function deleteGamePic($id)
    {
        if (!$id) {
            return false;
        }
        try {

            $where = [
                'id' => $id,
            ];
            $getGameId = DB::connection("db_vronline")->table('t_game_pic')->where($where)->get();
            $ret       = DB::connection("db_vronline")->table('t_game_pic')->where($where)->delete();

            $count  = $this->getGamePicNum($getGameId[0]['game_id']);
            $update = [
                'game_pic_num' => $count,
            ];
            $result = DB::connection("db_vronline")->table('t_game')->where("game_id", $getGameId[0]['game_id'])->update($update);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }
    /**
     * 获取游戏上传的图片
     * [getGameImg description]
     * @param  [type] $game_id [description]
     * @return [type]          [description]
     */
    public function getGameImg($game_id)
    {
        if (!$game_id) {
            return false;
        }
        try {
            $where = [
                'game_id' => $game_id,
            ];
            $ret = DB::connection("db_vronline")->table('t_game_pic')->where($where)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }
    /**
     * 删除游戏图片
     */
    public function getGamePics($game_id, $page, $len)
    {
        if (!$game_id) {
            return false;
        }
        try {

            $ret = DB::connection("db_vronline")->table('t_game_pic')->where("game_id", $game_id)->forPage($page, $len)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 删除游戏图片
     */
    public function getGamePicNum($game_id)
    {
        if (!$game_id) {
            return false;
        }
        try {
            $ret = DB::connection("db_vronline")->table('t_game_pic')->where("game_id", $game_id)->count();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |                           新 闻 资 讯                                       |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 添加文章内容
     */
    public function addArticle($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        try {
            $ret = DB::connection("db_vronline")->table('t_article')->insert($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取文章内容
     */
    public function getArticleById($article_id, $fields = null)
    {
        if (!$article_id) {
            return false;
        }
        try {
            $raw = DB::connection("db_vronline")->table('t_article')->where("article_id", $article_id);
            if ($fields) {
                $raw = $raw->select($fields);
            }
            $row = $raw->first();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        if ($row === null) {
            $row = [];
        }
        return $row;
    }

    /**
     * 修改文章内容
     */
    public function updArticleById($article_id, $info)
    {
        if (!$article_id || !$info || !is_array($info)) {
            return false;
        }
        try {
            $ret = DB::connection("db_vronline")->table('t_article')->where("article_id", $article_id)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取上一篇文章的id
     */
    public function getPreArticleId($article_id)
    {
        if (!$article_id) {
            return false;
        }
        try {
            $aid = DB::connection("db_vronline")->table('t_article')->where("article_stat", 0)->where("article_id", "<", $article_id)->max("article_id");
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $aid;
    }

    /**
     * 获取上一篇文章的id
     */
    public function getNextArticleId($article_id)
    {
        if (!$article_id) {
            return false;
        }
        try {
            $aid = DB::connection("db_vronline")->table('t_article')->where("article_stat", 0)->where("article_id", ">", $article_id)->min("article_id");
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $aid;
    }

    /**
     * 增加资讯的点赞、回复等数量
     * @param  int      id      评论id
     * @param  array    info   修改信息 ['up_num' => 1, 'down_num' => -1]
     * @return bool
     */
    public function incArticleNum($id, $info)
    {
        if (!$id || !$info || !is_array($info)) {
            return false;
        }

        try {
            $uinfo      = [];
            $setCache   = true;
            $cacheModel = new CacheModel;
            foreach ($info as $field => $num) {
                $num = intval($num);
                if ($num == 0) {
                    unset($info[$field]);
                    continue;
                }
                $exists = $cacheModel->countExists("t_article", $id, $field);
                if (!$exists) {
                    $setCache = false;
                }
                $uinfo[$field] = DB::raw("{$field} + {$num}");
            }
            if (!$uinfo) {
                return false;
            }
            $ret = DB::connection("db_vronline")->table('t_article')->where("article_id", $id)->update($uinfo);
            if ($ret && $setCache) {
                foreach ($info as $field => $num) {
                    $cacheModel->incCounts("t_article", $id, $field, $num);
                }
            }
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 取文章点赞、评论、浏览数量
     *
     * @param  int  article_id  id
     * @return arr          数组
     */
    public function getArticleNums($article_id)
    {
        if (!$article_id) {
            return false;
        }

        $fields     = ['article_view_num', 'article_comment_num', 'article_agree_num', 'article_disagree_num'];
        $cacheModel = new CacheModel;
        $counts     = $cacheModel->getCounts("t_article", $article_id, $fields);
        if (is_array($counts) && isset($counts['article_view_num']) && $counts['article_view_num'] > 0) {
            return $counts;
        }

        $info = $this->getArticleById($article_id, $fields);
        if (!$info || !is_array($info)) {
            return false;
        }
        for ($i = 0; $i < count($fields); $i++) {
            $field          = $fields[$i];
            $counts[$field] = isset($info[$field]) ? intval($info[$field]) : 0;
        }
        $cacheModel->setCounts("t_article", $article_id, $counts);
        return $counts;
    }

}
