<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 15:44
 */
namespace App\Models;

// 引用Model
use Config;
use DB;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class NewCommentDBModel extends Model
{

    private $pageSize = 10;
    /**
     * 添加评论
     * @param  int      uid         用户id
     * @param  int      target_id   应用id
     * @param  int      target_type   client_webgame/client_vrgame/client_video/news_video/news_game/news_news
     * @param  array    uinfo       用户信息 ['nick' => 'xxx', 'face' => 'xxxxxx']
     * @param  array    cinfo       评论信息 ['content' => 'xxx', 'praise' => '', 'score' => 0.0, 'cip' => '', 'location' => 'xxx']
     * @return bool
     */
    public function addComment($uid, $target_id, $target_type, $uinfo, $cinfo)
    {
        if (!$uid || !$target_id || !$target_type || !is_array($uinfo) || !$uinfo || !is_array($cinfo) || !$cinfo) {
            return false;
        }
        if (!isset($cinfo['cip']) || !$cinfo['cip']) {
            $cinfo['cip'] = Library::realIp();
        }

        $info                = array_merge($uinfo, $cinfo);
        $info['uid']         = $uid;
        $info['target_id']   = $target_id;
        $info['target_type'] = $target_type;
        try {
            $ret = DB::connection("db_comment")->table('t_comment')->insertGetId($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 修改评论
     * @param  int      id      评论id
     * @param  array    info   修改信息 ['status' => 'xxx', 'isdel' => 'xxxxxx']
     * @return bool
     */
    public function updComment($id, $info)
    {
        if (!$id || !is_array($info) || !$info) {
            return false;
        }

        try {
            $ret = DB::connection("db_comment")->table('t_comment')->where("id", $id)->update($info);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 增加评论的点赞、回复等数量
     * @param  int      id      评论id
     * @param  array    info   修改信息 ['up_num' => 1, 'down_num' => -1]
     * @return bool
     */
    public function incCommentNum($id, $info)
    {
        if (!$id || !$info || !is_array($info)) {
            return false;
        }

        try {
            $uinfo = [];
            foreach ($info as $field => $num) {
                $num = intval($num);
                if ($num == 0) {
                    continue;
                }
                $uinfo[$field] = DB::raw("{$field} + {$num}");
            }
            $ret = DB::connection("db_comment")->table('t_comment')->where("id", $id)->update($uinfo);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取最新的评论
     * @param  array  clause   查询条件
     * @param  int  page   页数
     * @param  int  len   记录数
     * @return array
     */
    public function getComments($clause, $page, $len)
    {
        if (!$clause || !is_array($clause) || $page <= 0 || $len <= 0) {
            return false;
        }
        try {
            $ret = DB::connection("db_comment")->table('t_comment')->select("id", "uid", "nick", "face", "content", "reply_to", "praise", "score", "up_num", "down_num", "reply_num", "cip", "location", "ctime")->where($clause)->orderBy("id", "desc")->forPage($page, $len)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        foreach ($ret as $field => $arr_detail) {
            if ($arr_detail['reply_to']) {
                $ret[$field]['reply_to'] = json_decode($arr_detail['reply_to'], true);
            }
        }
        return $ret;
    }

    /**
     * 根据条件获取评论数量
     * @param  array  clause   查询条件
     * @return array
     */
    public function getCommentCount($clause)
    {
        if (!$clause || !is_array($clause)) {
            return false;
        }
        try {
            $ret = DB::connection("db_comment")->table('t_comment')->where($clause)->count();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 获取最新的评论
     * @param  array  clause   查询条件
     * @param  int  page   页数
     * @param  int  len   记录数
     * @return array
     */
    public function getCommentsUpOrder($clause, $page, $len)
    {
        if (!$clause || !is_array($clause) || $page <= 0 || $len <= 0) {
            return false;
        }
        try {
            $ret = DB::connection("db_comment")->table('t_comment')->where($clause)->orderBy("id", "asc")->forPage($page, $len)->get();
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        foreach ($ret as $field => $arr_detail) {
            if ($arr_detail['reply_to']) {
                $ret[$field]['reply_to'] = json_decode($arr_detail['reply_to'], true);
            }
        }
        return $ret;
    }

    /**
     * 获取某评论
     * @param  int      id      评论id
     * @return array
     */
    public function getCommentById($id)
    {
        if (!$id) {
            return false;
        }

        try {
            $clause = ['id' => $id];
            $row    = DB::connection("db_comment")->table('t_comment')->where($clause)->first();
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
     * 获取最新的评论=>给cp后台审核使用
     * @param  array  clause   查询条件
     * @return array
     */
    public function getCommentsForCp($clause)
    {
        // var_dump($clause['status']);die;
        try {
            $res = DB::connection("db_comment")->table('t_comment')->select("id", "uid", "nick", "face", "content", "reply_to", "praise", "score", "up_num", "down_num", "reply_num", "status", "cip", "location", "ctime");
            if (isset($clause['search']) && $clause['search']) {
                $res->where("content", "LIKE", '%' . $clause['search'] . '%');
            }

            if (isset($clause['status']) && $clause['status'] >= 0) {
                $res->where("status", $clause['status']);
            }
            $ret = $res->orderBy("id", "desc")->paginate($this->pageSize);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        // foreach ($ret as $field => $arr_detail) {
        //     if ($arr_detail['reply_to']) {
        //         $ret[$field]['reply_to'] = json_decode($arr_detail['reply_to'], true);
        //     }
        // }
        return $ret;
    }

}
