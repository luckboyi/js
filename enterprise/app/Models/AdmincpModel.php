<?php

namespace App\Models;

use App\Helper\Vmemcached;
use DB;
use Illuminate\Database\Eloquent\Model;
use Mail;

class AdmincpModel extends Model
{
    private $pageSize    = 10;
    private $picPageSize = 8;

    public function getOneData($name, $id)
    {
        switch ($name) {
            case "vrhelp_vrgame":
                $row = DB::connection("db_dev")->table("t_webgame")->where("appid", $id)->first();
                break;
            case "vrhelp_video":
                $row = DB::connection("db_dev")->table("t_video")->where("video_id", $id)->first();
                break;
            case "vrhelp_price":
                $row = DB::connection("db_webgame")->table("t_webgame")->where("appid", $id)->first();
                break;
            case "top_banner":
                $row = DB::connection("db_operate")->table("top_recommend")->where("id", $id)->first();
                break;
            case "sys_user":
                $row = DB::connection("system")->table("users")->where("id", $id)->first();
                break;
            case "vrhelp_position":
                $row = DB::connection("db_operate")->table("top_postion")->where("posid", $id)->first();
                break;
            case "vronline_position":
                $row = DB::connection("db_vronline")->table("t_position")->where("pos_id", $id)->first();
                break;
            case "vronline_game":
                $row = DB::connection("db_vronline")->table('t_game')->where("game_id", $id)->first();
                break;
            case "product_client":
                $row = DB::connection("db_operate")->table("t_client_version")->where("id", $id)->first();
                break;
            case "online_client":
                $row = DB::connection("db_operate")->table("t_client_uponline_version")->where("id", $id)->first();
                break;
            case "news_article":
                if ($id > 0) {
                    $row = DB::connection("db_dev")->table("t_news")->where("id", $id)->first();
                } else {
                    $row = ['id' => 0, 'title' => '', 'tp' => '', 'content' => '', 'source' => '', 'source_link' => '', 'cover' => ''];
                }
                break;
            case "vronline_news":
            case "vronline_pc":
                if ($id > 0) {
                    $row = DB::connection("db_vronline")->table("draft_article")->where("article_id", $id)->first();
                } else {
                    $row = ['article_id' => 0, 'article_title' => '', 'article_category' => '', 'article_content' => '', 'article_source' => '', 'article_tag' => '', 'article_cover' => '', 'article_keywords' => '', 'article_alias' => '', 'article_target_id' => '', 'article_pc_match' => 0];
                }
                break;
            case "vronline_video":
                if ($id > 0) {
                    $row = DB::connection("db_vronline")->table("draft_article")->where("article_id", $id)->first();
                } else {
                    $row = ['article_id' => 0, 'article_title' => '', 'article_category' => '', 'article_content' => '', 'article_video_source_tp' => '', 'article_tag' => '', 'article_cover' => '', 'article_video_source_url' => '', 'article_video_tp' => '', 'article_video_time' => ''];
                }
                break;
            case "vronline_top":
                $row = DB::connection("db_vronline")->table("t_top")->where("id", $id)->first();
                break;
            case "news_position":
                $row = DB::connection("db_operate")->table("news_position")->where("posid", $id)->first();
                break;
            case "news_recommend":
            case "news_banner":
                $row = DB::connection("db_operate")->table("news_recommend")->where("id", $id)->first();
                break;
            case "sys_group":
                $row = DB::connection("system")->table("user_group")->where("id", $id)->first();
                break;
            case "service_qa":
                $row = DB::connection("db_operate")->table("service_faq")->where("id", $id)->first();
                break;
            case "webgame_news":
                $row = DB::connection("db_webgame")->table("t_game_news")->where("id", $id)->first();
                break;
            case "tob_merchats":
                $row = DB::connection("db_2b_store")->table("t_2b_merchant")->where("id", $id)->first();
                break;
            case "tob_banner":
                $row = DB::connection("db_2b_store")->table("t_2b_banner")->where("id", $id)->first();
                break;
            case "dbb_info":
                $row           = DB::connection("db_act")->table("t_vronline_3dbb_info")->where("id", $id)->first();
                $row["detail"] = json_decode($row["detail"], true);
                break;
        }

        return $row;
    }

    public function sysUserPage()
    {
        $row = DB::connection("system")->table("users")->orderBy('ltime', 'desc')->paginate($this->pageSize);
        return $row;
    }

    public function sysUserGroup()
    {
        $row = DB::connection("system")->table("user_group")->get();
        return $row;
    }

    public function updateSysGroup($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection("system")->table("user_group")->where("id", $id)->update($info);
        } else {
            $ret = DB::connection("system")->table("user_group")->insert($info);
        }
        return $ret;
    }

    public function delSysGroup($id)
    {
        $ret = DB::connection("system")->table("user_group")->where('id', $id)->delete();
        return $ret;
    }

    public function sendMail($email, $name, $msgDataArr)
    {
        $data = ['email' => $email, 'name' => $name, 'uid' => $msgDataArr['uid'], 'title' => $msgDataArr['title'], 'code' => $msgDataArr['code']];
        Mail::queue('admincp.resetEmail', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject($data['title']);
        });
        return true;
    }

    public function getEmailCode($account)
    {
        if (!$account) {
            return false;
        }
        $row = $this->sysUser($account);
        if (isset($row['id'])) {
            $code = md5(md5($row['id']) . time() . mt_rand(1111, 9999));
            $ret  = Vmemcached::set("admincp_emailcode", $row['id'], $code);
            if ($ret) {
                $row['code'] = $code;
                return $row;
            }
        }
        return false;
    }

    public function checkEmailCode($uid, $code)
    {
        if (!$uid || !$code) {
            return false;
        }
        $memCode = Vmemcached::get("admincp_emailcode", $uid);

        if ($memCode) {
            if ($code == $memCode) {
                $row = $this->sysUser($uid);
                return $row;
            }
        }
        return false;
    }

    public function updateSysPwdByEmail($uid, $code, $pwd)
    {
        if (!$uid || !$code || !$pwd) {
            return false;
        }
        $memCode = Vmemcached::get("admincp_emailcode", $uid);
        if ($memCode) {
            if ($code == $memCode) {
                $row = $this->sysUser($uid);
                if (!$row) {
                    return false;
                }
                if ($row['password'] == $pwd) {
                    return true;
                }
                return $this->updateSysUser($uid, ['password' => $pwd]);
            }
        }
        return false;
    }

    public function sysUser($account)
    {
        $res = DB::connection("system")->table("users");
        if (is_numeric($account)) {
            $row = $res->where('id', $account)->first();
        } else {
            $row = $res->where('account', $account)->first();
        }

        return $row;
    }

    public function updateSysUser($userid, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($userid > 0) {
            $ret = DB::connection("system")->table("users")->where("id", $userid)->update($info);
        } else {
            $ret = DB::connection("system")->table("users")->insert($info);
        }
        return $ret;
    }

    public function delSysUser($id)
    {
        $ret = DB::connection("system")->table("users")->where('id', $id)->delete();
        return $ret;
    }

    public function topPostionData($posid)
    {
        $row = DB::connection("db_operate")->table("top_recommend")->where('posid', $posid)->where('stat', 0)->orderBy("weight", "desc")->get();
        return $row;
    }

    public function topPostionWeight($drag, $drop)
    {
        $rowDrag = DB::connection("db_operate")->table("top_recommend")->select('weight')->where('id', $drag)->first();
        $rowDrop = DB::connection("db_operate")->table("top_recommend")->select('weight')->where('id', $drop)->first();

        if (!$rowDrag || !$rowDrop) {
            return false;
        }
        $ret1 = DB::connection("db_operate")->table("top_recommend")->where("id", $drag)->update(['weight' => $rowDrop['weight']]);
        $ret2 = DB::connection("db_operate")->table("top_recommend")->where("id", $drop)->update(['weight' => $rowDrag['weight']]);

        return $ret1 && $ret2;
    }

    public function topPostion($tp, $page = 12)
    {
        $res = DB::connection("db_operate")->table("top_postion");
        if ($tp) {
            $res->where('tp', $tp);
        }
        if ($page > 0) {
            $row = $res->orderBy("ctime", "asc")->paginate($page);
        } else {
            $row = $res->orderBy("ctime", "asc")->get();
        }
        return $row;
    }

    public function updateTopPostion($posid, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($posid > 0) {
            $ret = DB::connection("db_operate")->table("top_postion")->where("posid", $posid)->update($info);
        } else {
            $ret = DB::connection("db_operate")->table("top_postion")->insert($info);
        }
        return $ret;
    }

    public function delTopPostion($posid)
    {
        if ($posid > 0) {
            $ret = DB::connection("db_operate")->table("top_postion")->where("posid", $posid)->delete();
        }
        return $ret;
    }

    public function getGameByIds($appids)
    {
        $row = DB::connection("db_webgame")->table("t_webgame")->select('appid', 'name', 'img_version', 'game_type')->whereIn("appid", $appids)->get();
        return $row;
    }

    public function getVideoByIds($videoids)
    {
        $row = DB::connection("db_operate")->table("t_video")->select('video_id', 'video_name', 'video_cover')->whereIn("video_id", $videoids)->get();
        return $row;
    }

    public function searchContent($tp, $content)
    {
        $row = array();
        switch ($tp) {
            case "webgame":
            case "vrgame":
                $row = DB::connection("db_webgame")->table("t_webgame")->select('appid as id', 'name as title')->where('name', 'like', '%' . $content . '%')->get();
                break;
            case "video":
                $row = DB::connection("db_operate")->table("t_video")->select('video_id as id', 'video_name as title')->where('video_name', 'like', '%' . $content . '%')->get();
                break;
        }
        return $row;
    }

    public function updateTopRecommend($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection("db_operate")->table("top_recommend")->where("id", $id)->update($info);
        } else {
            $retId = DB::connection("db_operate")->table("top_recommend")->insertGetId($info);
            if ($retId) {
                $ret = DB::connection("db_operate")->table("top_recommend")->where("id", $retId)->update(['weight' => $retId]);
            } else {
                $ret = false;
            }
        }
        return $ret;
    }

    public function delTopRecommend($id)
    {
        if ($id > 0) {
            $ret = DB::connection("db_operate")->table("top_recommend")->where("id", $id)->update(['stat' => 9]);
        }
        return $ret;
    }

    /**
     * 获取客户端版本的信息
     * [getClientVersion description]
     * @param  [type] $status [description] 版本的状态：0:新添加，未发布;1:新版本;2:稳定版本;4:废弃的新版本;9:老的稳定版本，不升级到该版本;',
     * @return [type]         [description]
     */
    public function getClientVersion($status = null)
    {
        if ($status) {
            $ret = DB::connection("db_operate")->table("t_client_version")->where('status', $status)->orderBy('id', 'desc')->get();
        } else {
            $ret = DB::connection("db_operate")->table("t_client_version")->orderBy('id', 'desc')->paginate($this->pageSize);
        }
        return $ret;
    }

    public function updateClientVersion($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection("db_operate")->table("t_client_version")->where("id", $id)->update($info);
        } else {
            $ret = DB::connection("db_operate")->table("t_client_version")->insert($info);
        }
        return $ret;
    }

    public function delClientVersion($id)
    {
        if ($id > 0) {
            $ret = DB::connection("db_operate")->table("t_client_version")->where("id", $id)->delete();
        }
        return $ret;
    }

    public function updateVersionStatus($status)
    {
        if ($status == 1) {
            $where = [
                'status' => 1,
            ];
            $update = [
                'status' => 4,
            ];
            $ret = DB::connection("db_operate")->table("t_client_version")->where($where)->update($update);
        } elseif ($status == 2) {
            $where = [
                'status' => 2,
            ];
            $update = [
                'status' => 9,
            ];
            $ret = DB::connection("db_operate")->table("t_client_version")->where($where)->update($update);
        }
        return $ret;
    }

    /**
     * 获取客户端在线更新版本的信息
     * [getClientVersion description]
     * @param  [type] $status [description] 版本的状态：0:新添加，未发布;1:发布版本;2:老版本;',
     * @return [type]         [description]
     */
    public function getUpOnlineVersion($status = null)
    {
        if ($status) {
            $ret = DB::connection("db_operate")->table("t_client_uponline_version")->where('status', $status)->orderBy('id', 'desc')->get();
        } else {
            $ret = DB::connection("db_operate")->table("t_client_uponline_version")->orderBy('id', 'desc')->paginate($this->pageSize);
        }
        return $ret;
    }
    /**
     * 添加在线更新版本的信息
     * [updateClientVersion description]
     * @param  [type] $id   [description]
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function updateOnlineVersion($id, $info)
    {
        if (!is_array($info)) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection("db_operate")->table("t_client_uponline_version")->where("id", $id)->update($info);
        } else {
            $ret = DB::connection("db_operate")->table("t_client_uponline_version")->insert($info);
        }
        return $ret;
    }
    /**
     * 更新在线更新版本的状态
     * [updateVersionStatus description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function updateOnlineStatus($status)
    {
        if ($status == 1) {
            $where = [
                'status' => 1,
            ];
            $update = [
                'status' => 2,
            ];
            $ret = DB::connection("db_operate")->table("t_client_uponline_version")->where($where)->update($update);
        }
        return $ret;
    }
    /**
     * 删除在线更新的版本信息
     * [delUpClientVersion description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delUpClientVersion($id)
    {
        if ($id > 0) {
            $ret = DB::connection("db_operate")->table("t_client_uponline_version")->where("id", $id)->delete();
        }
        return $ret;
    }
}
