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
use App\Models\NewCommentDBModel;
use App\Models\SupportModel;
use App\Models\VronlineModel;
use Config;
use DB;
use Helper\AccountCenter;
use Illuminate\Database\Eloquent\Model;

class NewCommentModel extends Model
{

    /**
     * 根据名称得到评论目标类型id
     */
    private function getTargetTypeByName($name)
    {
        switch($name) {
            case "client_webgame":          // 客户端的页游
                        return 1;
            case "client_vrgame":           // 客户端的vr游戏
                        return 2;
            case "client_video":            // 客户端的视频
                        return 3;
            case "news_video":              // 资讯站视频
                        return 4;
            case "news_game":               // 资讯站游戏
                        return 5;
            case "news_news":               // 资讯站新闻
                        return 6;
            default:    return 0;
        }
        return 0;
    }

    /**
     * 查询正常的评论
     *
     * @param  int  target_id   应用id
     * @param  int  type        应用类型名称
     * @param  int  page        页数
     * @param  int  len         记录数
     * @return arr              评论数组
     */
    public function getComments($target_id, $type, $page, $len)
    {
        if (!$target_id || !$type || $page <= 0 || $len <= 0) {
            return false;
        }
        $target_type = $this->getTargetTypeByName($type);
        if(!$target_type) {
            return false;
        }

        $clause = ['target_id' => $target_id, 'target_type' => $target_type, 'status' => 1, 'isdel' => 0];
        $newCommentDBModel = new NewCommentDBModel;
        $num  = $newCommentDBModel->getCommentCount($clause);
        if($num > 0) {
            $rows = $newCommentDBModel->getComments($clause, $page, $len);
            if($rows === false) {
                return false;
            }
        }else {
            $rows = [];
        }
        return ['total' => $num, 'comment' => $rows];
    }

    /**
     * 查询未审核的评论，用于审核
     *
     * @param  int  page        页数
     * @param  int  len         记录数
     * @return arr          评论数组
     */
    public function getUnReviewComments($page, $len)
    {
        if ($page <= 0 || $len <= 0) {
            return false;
        }
        $clause = ['status' => 0, 'isdel' => 0];
        $newCommentDBModel = new NewCommentDBModel;
        $num  = $newCommentDBModel->getCommentCount($clause);
        if($num > 0) {
            $rows = $newCommentDBModel->getCommentsUpOrder($clause, $page, $len);
            if($rows === false) {
                return false;
            }
        }else {
            $rows = [];
        }
        return ['total' => $num, 'comment' => $rows];
    }

    /*
     * 添加评论信息
     * @param  int      uid         uid
     * @param  int      target_id   应用id
     * @param  int      type        应用类型名称
     * @param  array    uinfo       用户信息 ['nick' => xxx, 'face' => xxx]
     * @param  array    info        评论信息 ['content' => xxx, 'score' => xxx, 'praise' => xxx]
     * @return bool
     */
    public function addComment($uid, $target_id, $type, $uinfo, $info)
    {
        if (!$target_id || !$type || !$info || !is_array($info) || !$uinfo || !is_array($uinfo)) {
            return false;
        }
        $target_type = $this->getTargetTypeByName($type);
        if(!$target_type) {
            return false;
        }
        $newCommentDBModel = new NewCommentDBModel;
        $ret = $newCommentDBModel->addComment($uid, $target_id, $target_type, $uinfo, $info);
        return $ret;
    }

    /*
     * 添加回复
     * @param   int     cid     回复的目标评论ID
     * @param   int     uid     用户id
     * @param   array   uinfo   用户信息 ['nick' => xxx, 'face' => xxx]
     * @param   array   cinfo   回复内容信息 ['content' => xxx, 'score' => xxx, 'praise' => xxx]
     */
    public function addReply($cid, $uid, $uinfo, $cinfo)
    {
        if (!$cid || !$cinfo || !is_array($cinfo) || !$uinfo || !is_array($uinfo)) {
            return false;
        }
        $newCommentDBModel = new NewCommentDBModel;
        $father_row = $newCommentDBModel->getCommentById($cid);
        if(!$father_row) {
            return false;
        }
        $father_id   = $father_row['uid'];
        $father_nick = $father_row['nick'];
        $father_face = $father_row['face'];
        $content     = $father_row['content'];
        $father_ts   = $father_row['ctime'];
        $reply_to    = $father_row['reply_to'] ? json_decode($father_row['reply_to'], true) : "";        // 如果 reply_to 里有内容，内容是json
        /**
         * 如果 reply_to 里有内容，但json_decode失败，丢掉reply_to里的内容
         */
        if(!$reply_to) {
            $reply_to = [];
        }

        /**
         * 多级回复，暂时不用
         */
        // $reply_to[] = ['fcid' => $cid, 'fuid' => $father_id, 'fnick' => $father_nick, 'fface' => $father_face, 'fts' => $father_ts, 'reply' => $content, 'local' => ''];

        /**
         * 单级回复
         * 必须要初始化空数组，返回给前端的要是数组，不能是对象
         */
        $reply_to = [];     // 去掉初始化为多级回复

        /**
         * 不能加下标0赋值，否则返回的是对象，前端解析错误
         */
        $reply_to[] = ['fcid' => $cid, 'fuid' => $father_id, 'fnick' => $father_nick, 'fface' => $father_face, 'fts' => $father_ts, 'reply' => $content, 'local' => ''];
        $cinfo['reply_to'] = json_encode($reply_to);

        $target_id   = $father_row['target_id'];
        $target_type = $father_row['target_type'];
        $ret = $newCommentDBModel->addComment($uid, $target_id, $target_type, $uinfo, $cinfo);
        if(!$ret) {
            return false;
        }
        $data = ['cid' => $ret, 'reply' => $reply_to];
        return $data;
    }

    /*
     * 修改评论，用于审核、评分、好评等
     * 审核通过后，给对应的内容的评论数，或回复的评论的回复数 +1
     * @param   int     cid     评论id
     * @param   array   action  审核操作 pass: 通过; deny:拒绝; delete: 删除;
     */
    public function reviewComment($cid, $action)
    {
        if (!$cid || !$action) {
            return false;
        }
        $newCommentDBModel = new NewCommentDBModel;
        $cinfo = $newCommentDBModel->getCommentById($cid);
        if(!$cinfo) {
            return false;
        }

        /**
         * 判断是不是回复，如果是回复，找到回复对应的评论
         */
        $fatherid = 0;
        $reply_to = $cinfo['reply_to'] ? json_decode($cinfo['reply_to'], true) : "";
        if($reply_to && is_array($reply_to)) {
            $father = end($reply_to);
            $fatherid = isset($father['fcid']) ? $father['fcid'] : 0;
        }

        switch($action)
        {
            case "pass":
                if($cinfo['status'] == 1) {
                    return true;
                }
                $info['status'] = 1;
                $ret = $newCommentDBModel->updComment($cid, $info);
                if($ret === false) {
                    return false;
                }

                // 给对应的目标内容增加一评论数量
                $this->addCommentNum2Target($cinfo['target_id'], $cinfo['target_type'], 1);
                /**
                 * 如果是一条回复，找到回复的那条父评论，给其回复数+1
                 */
                if($fatherid) {
                    $rinfo['reply_num'] = 1;
                    $newCommentDBModel->incCommentNum($fatherid, $rinfo);
                }
                return true;
            case "deny":
                if($cinfo['status'] == 2) {
                    return true;
                }
                $info['status'] = 2;
                $ret = $newCommentDBModel->updComment($cid, $info);
                if($ret === false) {
                    return false;
                }
                if($cinfo['status'] == 1) {

                    // 给对应的目标内容 -1 评论数量
                    $this->addCommentNum2Target($cinfo['target_id'], $cinfo['target_type'], -1);
                    if($fatherid) {
                        $rinfo['reply_num'] = -1;
                        $newCommentDBModel->incCommentNum($fatherid, $rinfo);
                    }
                }
                return true;
            case "delete":
                if($cinfo['isdel'] == 1) {
                    return true;
                }
                $info['isdel'] = 1;
                $ret = $newCommentDBModel->updComment($cid, $info);
                if($ret === false) {
                    return false;
                }

                // 给对应的目标内容 -1 评论数量
                $this->addCommentNum2Target($cinfo['target_id'], $cinfo['target_type'], -1);
                if($fatherid) {
                    $rinfo['reply_num'] = -1;
                    $newCommentDBModel->incCommentNum($fatherid, $rinfo);
                }
                return true;
            default:    return false;
        }
        return true;
    }

    /*
     * 增加评论点赞数、吐槽数
     * @param   int     cid     评论id
     * @param   string  action  操作 up/down
     */
    public function support($uid, $cid, $action)
    {
        if (!$uid || !$cid || !$action) {
            return false;
        }

        $info = [];
        switch($action) {
            case "up":
                $info = ['up_num' => 1];
                break;
            case "down":
                $info = ['down_num' => 1];
                break;
            default:    return false;
        }

        $supportModel = new SupportModel;
        $ismem = $supportModel->isSupported($uid, $cid, "comment");
        if($ismem === false) {
            return false;
        }if($ismem === null) {
        }else {
            return "already";
        }
        $ret = $supportModel->add($uid, $cid, "comment", $action);
        $newCommentDBModel = new NewCommentDBModel;
        $ret = $newCommentDBModel->incCommentNum($cid, $info);
        return $ret;
    }

    /*
     * 给对应的目标增加或减少评论数
     * @param   int     target_id       目标id
     * @param   int     target_type     目标类型
     * @param   string  action          操作,add/sub
     */
    public function addCommentNum2Target($target_id, $target_type, $num)
    {
        $num = intval($num);
        if (!$target_id || !$target_type || $num == 0) {
            return false;
        }
        switch($target_type) {
            case 6:             // 新闻
                $vronlineModel = new VronlineModel;
                $vronlineModel->incArticleNum($target_id, ['article_comment_num' => $num]);
                break;
            case 5:             // 资讯-游戏
                $vronlineModel = new VronlineModel;
                $vronlineModel->incGameNum($target_id, ['game_comment_num' => $num]);
                break;
            case 4:             // 资讯-视频
                $vronlineModel = new VronlineModel;
                $vronlineModel->incArticleNum($target_id, ['article_comment_num' => $num]);
                break;
            case 3:             // 客户端-视频
                break;
            case 2:             // 客户端-vr游戏
                break;
            case 1:             // 客户端-页游
                break;
            default:    break;
        }
                    // 给对应的目标内容增加一评论数量     **************************************************************************************************************************************

        return true;
    }

}
