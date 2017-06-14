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
use App\Models\SupportModel;
use Config;
use DB;
use Helper\AccountCenter as Account;
use Illuminate\Database\Eloquent\Model;

class CommentModel extends Model
{

    /**
     * 获取整理整理评论信息
     *
     * @param  int  $uid    用户id
     * @param  int  $appid  应用id
     * @param  int  $type   应用类型
     * @return arr          评论数组：包含是否评论、好评数、差评数、总评论数
     */
    public function getComments($uid, $appid, $type)
    {

        if ($uid) {
            $arr = array(
                'uid'       => $uid,
                'target_id' => $appid,
            );
            if ($this->alreadyComment($arr)) {
                $return["comment"] = 1;
            }
        }

        $clause = array(
            'target_id'    => $appid,
            'target_type'  => $type,
            'status'       => 1,
            'comment_type' => 1,
        );

        /**
         * 好评
         */
        $return["good_comment_num"] = $this->getCommentCount($clause);

        /**
         * 差评
         */
        $clause["comment_type"]    = "2";
        $return["bad_comment_num"] = $this->getCommentCount($clause);

        /**
         * 当天的最新评价
         */
        unset($clause["comment_type"]);
        $spClause["create_at"]     = array(">", strtotime(date("Y-m-d")));
        $return["new_comment_num"] = $this->getCommentCount($clause, $spClause);

        /**
         * 总评价
         */
        $return["all_comment_num"] = $return["good_comment_num"] + $return["bad_comment_num"];
        return $return;
    }

    /*
     * 添加用户评论评分的redis缓存key
     */
    //private $key_add_comment_score = "add_comment_score_";

    /*
     * 获取用户评论游戏是否玩过，或购买
     * 玩过或购买了才可以评论
     */
    public function ifComment($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }

        $uid        = (int) $info['uid'];
        $targetId   = (int) $info['targetId'];
        $targetType = (int) $info['targetType'];

        switch ($info["targetType"]) {
            case 1:
            case 2:
                $targetType   = $targetType - 1;
                $webGameModel = App::make('webGame');
                $ret          = $webGameModel->getGameLogByAppid($uid, $targetId, $targetType);
                break;
            case 3:
                $videoModel = App::make('video');
                $ret        = $videoModel->getVideoHistoryOne($uid, $targetId);
                break;
            default:
                break;
        }

        return $ret;
    }

    /*
     * 判断用户是否评论过
     */
    public function alreadyComment($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = array(
            'uid'       => $info['uid'],
            'target_id' => $info['target_id'],
        );
        $ret = DB::connection("db_operate")->table("t_comment")->where($where)->get();
        return $ret;
    }

    /*
     * 添加评论信息
     */
    public function addComment($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $ret = DB::connection("db_operate")->table("t_comment")->insert($info);
        return $ret;
    }

    /*
     * 删除评论接口
     */
    public function delComment($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = array(
            'id' => $info['commentId'],
        );
        $ret = DB::connection("db_operate")->table("t_comment")->where($where)->delete();
        return $ret;
    }

    /*
     * 添加用户评论的支持与否的数据更新
     */
    public function updateSupport($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = array(
            'uid'         => $info['uid'],
            'target_id'   => $info['target_id'],
            'target_type' => $info['target_type'],
        );

        $infoArr = DB::connection("db_operate")->table("t_comment")->where($where)->get();
        $update  = [];
        if ($info['support'] == 1) {
            $action = "up";
            $update = array('support' => $infoArr[0]['support'] + 1);
        } else if ($info['support'] == 0) {
            $action = "down";
            $update = array('unsupport' => $infoArr[0]['unsupport'] + 1);
        }

        $ret = DB::connection("db_operate")->table("t_comment")->where($where)->update($update);
        if ($ret) {
            //存取缓存
            $supportModel = new SupportModel;
            $result       = $supportModel->support($info['loginUid'], $info['id'], "comment", $action);
            return $result;
        }
        return $ret;
    }

    /*
     * 获取某条评论的信息=》comnentId
     */
    public function getCommentInfoOne($commentId)
    {
        if (!$commentId || $commentId == '') {
            return false;
        }
        $where = array(
            'id' => $commentId,
        );
        $ret = DB::connection("db_operate")->table("t_comment")->where($where)->get();
        return $ret;
    }
    /*
     * 获取分页的数据
     */
    public function getComment($info, $page = "", $startNum = "", $pageNum = "")
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        if (isset($info['loginUid']) && $info['loginUid'] == '') {
            $loginUid = '';
        }
        $loginUid = $info['loginUid'];
        $clause   = array(
            'target_id'   => $info['target_id'],
            'target_type' => $info['target_type'],
            'status'      => 1,
        );

        $row = [];

        //获取游戏的评论评分
        $row['score'] = 9;
        $commentScore = $this->getCommentScore($clause);
        if (!empty($commentScore)) {
            if (isset($commentScore[0]['score'])) {
                $row['score'] = $commentScore[0]['score'];
            } else {
                if (($commentScore[0]['gfavournum'] + $commentScore[0]['gopposenum'] - 1) == 0) {
                } else {
                    $row['score'] = intval($commentScore[0]['ggrade'] / ($commentScore[0]['gfavournum'] + $commentScore[0]['gopposenum'] - 1));
                }
            }

        }
        //获取评论详情信息
        if ($page != '' && $pageNum != '') {
            if ($startNum == '') {
                $startNum = ($page - 1) * $pageNum;
            }
            if (isset($info['type']) && $info['type'] != '') {
                switch ($info['type']) {
                    case 'hot':
                        $row['count'] = $this->getCommentCount($clause);
                        $commentArr   = DB::connection("db_operate")->table("t_comment")->where($clause)->orderBy("support", "desc")->skip($startNum)->take($pageNum)->get();
                        $row['data']  = $this->getExtInfo($commentArr, $loginUid);
                        return $row;
                    case 'new':
                        $todayTime             = strtotime(date("Y-m-d"));
                        $spClause["create_at"] = array(">", $todayTime);
                        $row['count']          = $this->getCommentCount($clause, $spClause);

                        $commentArr = DB::connection("db_operate")->table("t_comment")
                            ->where($clause)->where("create_at", ">", $todayTime)
                            ->orderBy("create_at", "desc")->skip($startNum)
                            ->take($pageNum)->get();

                        $row['data'] = $this->getExtInfo($commentArr, $loginUid);
                        return $row;
                    case 'positive':
                        $clause['comment_type'] = 1;
                        $row['count']           = $this->getCommentCount($clause);
                        $commentArr             = DB::connection("db_operate")->table("t_comment")->where($clause)->orderBy("id", "desc")->skip($startNum)->take($pageNum)->get();
                        $row['data']            = $this->getExtInfo($commentArr, $loginUid);
                        return $row;
                    case 'negative':
                        $clause['comment_type'] = 2;
                        $row['count']           = $this->getCommentCount($clause);
                        $commentArr             = DB::connection("db_operate")->table("t_comment")->where($clause)->orderBy("id", "desc")->skip($startNum)->take($pageNum)->get();
                        $row['data']            = $this->getExtInfo($commentArr, $loginUid);
                        return $row;
                    case 'my':
                        $clause['uid'] = $info['loginUid'];
                        $row['count']  = $this->getCommentCount($clause);
                        $commentArr    = DB::connection("db_operate")->table("t_comment")->where($clause)->get();
                        $row['data']   = $this->getExtInfo($commentArr, $loginUid);
                        return $row;
                    default:
                        $row['count'] = $this->getCommentCount($clause);
                        $commentArr   = DB::connection("db_operate")->table("t_comment")->where($clause)->get();
                        $row['data']  = $this->getExtInfo($commentArr, $loginUid);
                        return $row;
                }
            }
        } else {
            $row['count'] = $this->getCommentCount($clause);
            $commentArr   = DB::connection("db_operate")->table("t_comment")->where($clause)->get();
            $row['data']  = $this->getExtInfo($commentArr, $loginUid);
        }

        return $row;
    }

    /*
     * 获取评论评分的接口
     */
    public function getCommentScore($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = array(
            'appid' => $info['target_id'],
        );
        $retArr = DB::connection("db_webgame")->table("t_webgame")->where($where)->get();
        if (!$retArr) {
            $case   = ['gid' => $info['target_id']];
            $retArr = DB::connection("db_operate")->table("v_games")->where($case)->get(['ggrade', 'gfavournum', 'gopposenum']);
        }
        return $retArr;
    }
    /*
     * 获取评论的总数
     */
    public function getCommentCount($case, $spCase = array())
    {
        if (!$case || !is_array($case)) {
            return false;
        }

        $model = DB::connection("db_operate")->table("t_comment")->where($case);

        if ($spCase && is_array($spCase)) {
            foreach ($spCase as $key => $value) {
                if (!is_array($value)) {
                    continue;
                }
                $model->where($key, $value[0], $value[1]);
            }
        }

        $ret = $model->count();

        return $ret;
    }

    /*
     * 添加页游的评论评分
     */
    public function addCommentScore($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = array(
            'appid' => $info['target_id'],
        );
        $infoArr = $this->getCommentScore($info);
        if (isset($infoArr[0]['score'])) {
            $old_score_num = intval($infoArr[0]["score_num"]);
            $old_score     = round($infoArr[0]["score"], 1);
            $new_score     = round($info['score'], 1);
            $score         = ($old_score * $old_score_num + $new_score) / ($old_score_num + 1);
            $update        = array(
                'score_num' => $old_score_num + 1,
                'score'     => $score,
            );
            $ret = DB::connection("db_webgame")->table("t_webgame")->where($where)->update($update);
        } else {
            $case       = ['gid' => $info['target_id']];
            $gameUpdate = array('ggrade' => $infoArr[0]['ggrade'] + $info['score'], 'gfavournum' => $infoArr[0]['gfavournum'] + 1);
            $ret        = DB::connection("db_operate")->table("v_games")->where($case)->update($gameUpdate);
        }
        //存取缓存
        $result = $this->addCommentScoreRedis($info);
        return $result;
    }

    /*
     * 添加用户的评分redis缓存
     */
    public function addCommentScoreRedis($info)
    {
        return true;
        /*
    if (!$info || !is_array($info)) {
    return false;
    }
    $redis   = Vredis::connection("webgame");
    $setKey  = $this->key_add_comment_score . $info['uid'] . '_' . $info['target_id'];
    $setMark = $redis->set($setKey, $info['score']);
    return $setMark;
     */
    }

    /*
     * 获取用户信息以及扩展信息
     */
    public function getExtInfo($arr, $loginUid = "")
    {
        if (!$arr || !is_array($arr)) {
            return false;
        }

        $resultArr    = [];
        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $keyCode      = 'vrOnline_comment';

        foreach ($arr as $k => $v) {
            $resultArr[$k]['id']           = $v['id'];
            $resultArr[$k]['uid']          = $v['uid'];
            $resultArr[$k]['target_id']    = $v['target_id'];
            $resultArr[$k]['target_type']  = $v['target_type'];
            $resultArr[$k]['content']      = $v['content'];
            $resultArr[$k]['comment_type'] = $v['comment_type'];
            $resultArr[$k]['support']      = $v['support'];
            $resultArr[$k]['unsupport']    = $v['unsupport'];
            $resultArr[$k]['create_at']    = $this->getCommentTimeFormat($v['create_at']);
            $resultArr[$k]['alreadyClick'] = '';
            $resultArr[$k]['totalTime']    = 0;
            // $webgameLogArr = DB::connection("db_webgame")->table("t_webgame_log")->select('uid', 'timelen')->where('uid', $v['uid'])->where('appid', $v['target_id'])->get();
            $dbRes         = $this->getGamelogDB($v['uid']);
            $webgameLogArr = DB::connection("db_user_log")->table($dbRes['table_log'])->select('uid', 'timelen')->where('uid', $v['uid'])->where('appid', $v['target_id'])->get();
            if (!empty($webgameLogArr) && $webgameLogArr[0]['timelen'] != 0) {
                $resultArr[$k]['totalTime'] = $this->getTimeFormat($webgameLogArr[0]['timelen']);
            }

            //获取用户的信息
            $userInfoArr                = $accountModel->getCommentUserInfo($v['uid'], md5($keyCode));
            $resultArr[$k]['account']   = isset($userInfoArr['account']) ? $userInfoArr['account'] : 'user';
            $resultArr[$k]['headerPic'] = isset($userInfoArr['faceUrl']) ? $userInfoArr['faceUrl'] : Config::get('resource.face_host') . "/default.png";

            //添加留言是否点过支持与否
            if ($loginUid != '') {
                $infoArr = array(
                    'id'       => $v['id'],
                    'loginUid' => $loginUid,
                );
                /*
            $supportModel = new SupportModel;
            $supp = $supportModel->isSupported($loginUid, $v['id'], "comment");
            if($supp == "up") {
            $resultArr[$k]['alreadyClick'] = "Y";
            }else if($supp == "down") {
            $resultArr[$k]['alreadyClick'] = "N";
            }else {
            $resultArr[$k]['alreadyClick'] = "";
            }
             */
            }

        }
        return $resultArr;
    }

    /**
     * 获取游戏历史记录库、表名后缀
     */
    protected function getGamelogDB($uid)
    {
        if (!$uid) {
            return false;
        }
        $tbl_suff = $uid % 32;
        return array('db' => "db_user_log", 'table_log' => "t_game_log_" . $tbl_suff);
    }

    /*
     * 时长格式化
     * $tmp init 秒数
     */
    public function getTimeFormat($tmp)
    {
        if ($tmp > 3600) {
            $hours  = floor($tmp / 3600);
            $hasTmp = $tmp - $hours * 3600;
            $min    = ceil($hasTmp / 60);
            $format = $hours . '小时' . $min . '分钟';
            return $format;
        }
        $min    = ceil($tmp / 60);
        $format = $min . '分钟';
        return $format;
    }

    /*
     * 格式化评论时间
     */
    public function getCommentTimeFormat($stamp)
    {
        $nowStmp  = time();
        $timeDiff = time() - $stamp;
        if ($timeDiff < 60) {
            return '刚刚';
        }
        if ($timeDiff >= 60 && $timeDiff < 60 * 30) {
            $min = floor($timeDiff / 60);
            return $min . '分钟前';
        }
        //获取当天的年份
        $y = date("Y");

        //获取当天的月份
        $m = date("m");

        //获取当天的号数
        $d = date("d");

        $todayStamp = mktime(0, 0, 0, $m, $d, $y);
        if ($timeDiff >= 30 * 60 && $timeDiff < 24 * 3600) {
            return date("H:i", $stamp);
        }

        $yesterdayStamp = mktime(0, 0, 0, $m, $d - 1, $y);
        if ($timeDiff - 2 * 24 * 3600 > $yesterdayStamp && $timeDiff - 2 * 24 * 3600 > $todayStamp) {
            return '昨天 ' . date("H:i", $stamp);
        }
        //大于昨天的
        $weekStamp = mktime(0, 0, 0, $m, $d - 7, $y);
        if ($timeDiff < 7 * 24 * 3600) {
            $seconds = 24 * 3600;
            $dayNum  = floor($timeDiff / $seconds);
            return $dayNum . ' 天前' . date("H:i", $stamp);
        }

        if ($timeDiff >= 7 * 24 * 3600) {
            return date("m月d日 H:i", $stamp);
        }

        $yearStamp = mktime(0, 0, 0, 1, 1, $y);

        if ($timeDiff >= $yearStamp) {
            return date("Y年m月d日 H:i", $stamp);
        }

    }
}
