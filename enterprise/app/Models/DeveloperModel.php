<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models;

use App\Helper\Vmemcached;
use DB;
use Illuminate\Database\Eloquent\Model;
use Mail;

class DeveloperModel extends Model
{
    const PAGE_SIZE = 10;

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             邮件                                                             |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */
    /**
     * 发送邮件方法
     * @param $email
     * @param $name
     * @param $msgDataArr
     */
    public function sendVerifyMail($email, $name, $msgDataArr)
    {
        $data = ['email' => $email, 'name' => $name, 'uid' => $msgDataArr['uid'], 'title' => $msgDataArr['title'], 'activeCode' => $msgDataArr['activeCode']];
        Mail::queue('open.sign.mail', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject($data['title']);
        });
        return true;
    }

    /**
     * 设置激活码的缓存
     * @param $uid
     * @return bool
     */
    public function setActiveEmailCode($uid)
    {
        if (!$uid) {
            return false;
        }
        $code = md5(md5($uid) . time() . mt_rand(1111, 9999));
        $ret  = Vmemcached::set("active_code", $uid, $code);
        return $ret;
    }

    /**
     * 获取邮件链接中的激活code是否有效
     * @param $uid
     * @return bool
     */
    public function getActiveEmailCode($uid)
    {
        if (!$uid) {
            return false;
        }
        return Vmemcached::get("active_code", $uid);
    }

    /**
     * 删除激活码缓存
     * @param $uid
     * @return bool
     */
    public function delActiveEmailCode($uid)
    {
        if (!$uid) {
            return false;
        }
        return Vmemcached::delete("active_code", $uid);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             游戏                                               |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取某一账户下的游戏
     * @param   int     uid   uid
     * @param   string     tp   游戏类型
     * @param   array     ext   附加条件
     * @return  array   info    数组，游戏信息
     */
    public function getGameByUid($uid, $tp = -1, $ext = [])
    {
        if (!$uid || !is_numeric($tp)) {
            return false;
        }
        $res = DB::connection("db_dev")->table("t_webgame")->where("uid", $uid);
        if ($tp >= 0) {
            $res->where("game_type", $tp);
        }
        if (isset($ext['choose'])) {
            if ($ext['choose'] == "online") {
                $res->where("send_time", ">", 0);
            }
            if ($ext['choose'] == "offline") {
                $res->where("send_time", "=", 0);
            }
        }
        if (isset($ext['search'])) {
            if (is_numeric($ext['search']) && strlen($ext['search']) == 7) {
                $res->where("appid", $ext['search']);
            } else {
                $res->where("name", "LIKE", '%' . $ext['search'] . '%');
            }

        }
        $row = $res->orderBy("appid", "desc")->paginate(self::PAGE_SIZE);
        return $row;
    }

    /**
     * 获取一个游戏信息
     * @param   int     appid   游戏id
     * @return  array   info    数组，游戏信息
     */
    public function getGameById($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->first();
        return $row;
    }

    public function updateGameInfo($appid, $info)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }

        $ret = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->update($info);
        return $ret;
    }

    public function addGameInfo($info)
    {
        if (!$info || !is_array($info)) {
        }
        $appid = DB::connection("db_dev")->table("t_webgame")->insertGetId($info);
        return $appid;
    }

    public function checkGameName($name, $appid = 0)
    {
        if (!$name) {
            return false;
        }
        if ($appid > 0) {
            $row = DB::connection("db_dev")->table("t_webgame")->where("name", $name)->where("appid", "<>", $appid)->first();
        } else {
            $row = DB::connection("db_dev")->table("t_webgame")->where("name", $name)->value('appid');
        }
        return (bool) $row;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             服务器信息                                                   |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */
    /**
     * 根据appid获取支付地址
     * @param   int     appid     uid
     * @return  object       支付信息
     */
    public function getPayUrl($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->where("serverid", 0)->first();
        return $row;
    }

    /**
     * 添加服务器信息
     * @param   int     appid
     * @param   string    url   支付url
     * @param   string    url   支付测试url
     * @return  bool
     */
    public function updatePayUrl($appid, $url, $url_test)
    {
        if (!$appid || !$url) {
            return false;
        }
        $row = $this->getPayUrl($appid);
        if (!$row) {
            $ret = DB::connection("db_webgame")->table("t_game_server")->insert(['appid' => $appid, 'serverid' => 0, 'name' => '', 'domain' => '', 'payurl' => $url, 'payurltest' => $url_test]);
        } else {
            $ret = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->where('serverid', 0)->update(['payurl' => $url, 'payurltest' => $url_test]);
        }

        return $ret;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             用 户 信 息                                                     |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */
    /**
     * 添加用户
     * @param   int     uid    uid
     * @param   array   info   用户信息
     * @return  bool
     */
    public function addUser($uid, $info)
    {
        if (!$uid || !$info || !is_array($info)) {
            return false;
        }
        $info['uid'] = $uid;
        $ret         = DB::connection("db_dev")->table("t_user")->insert($info);
        return $ret;
    }

    /**
     * 更新用户信息
     * @param   int     uid     uid
     * @param   array   info    数组，游戏信息
     * @return  bool
     */
    public function updateUser($uid, $info)
    {
        if (!$uid || !$info || !is_array($info)) {
            return false;
        }
        if (isset($info['uid'])) {
            unset($info['uid']);
        }
        $ret = DB::connection("db_dev")->table("t_user")->where("uid", $uid)->update($info);
        return $ret;
    }

    /**
     * 设置用户信息，不存在插入，存在更新
     * @param   int     uid     uid
     * @param   array   info    数组，游戏信息
     * @return  bool
     */
    public function setUser($uid, $info)
    {
        if (!$uid || !$info || !is_array($info)) {
            return false;
        }
        if (isset($info['uid'])) {
            unset($info['uid']);
        }
        $user = $this->getUser($uid);
        if ($user) {
            $ret = $this->updUser($uid, $info);
        } else {
            $ret = $this->addUser($uid, $info);
        }
        return $ret;
    }

    /**
     * 获取一个用户信息
     * @param   int     uid    uid
     * @return  array   info    数组，游戏信息
     */
    public function getUser($uid)
    {
        if (!$uid) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_user")->where("uid", $uid)->first();
        return $row;
    }

    /**
     * 获取开发者
     * @param   array     ext   uid
     * @return  array   info    数组，开发者信息
     */
    public function getDevelopers($ext)
    {
        $res = DB::connection("db_dev")->table("t_user");
        if (isset($ext['choose']) && $ext['choose'] != -1) {
            $res->where("stat", $ext['choose']);
        }
        if (isset($ext['search']) && $ext['search']) {
            if (is_numeric($ext['search'])) {
                $res->where("uid", $ext['search']);
            } else {
                $res->where("name", "LIKE", '%' . $ext['search'] . '%');
            }
        }
        $row = $res->orderBy("ctime", "desc")->paginate(self::PAGE_SIZE);
        return $row;
    }
}
