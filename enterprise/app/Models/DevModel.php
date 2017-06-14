<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class DevModel extends Model
{
    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             页 游 信 息 表                                                  |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取某一账户下所有的游戏
     * @param   int     uid   uid
     * @param   int     pagesize   每页显示数量
     * @return  array   info    数组，游戏信息
     */
    public function getAppsByUid($uid, $tp, $pagesize = 10, $gameType = 0, $inIds = false)
    {
        if (!$uid || !in_array($gameType, [0, 1])) {
            return false;
        }
        $res = DB::connection("db_dev")->table("t_webgame")->where("uid", $uid);
        if ($inIds !== false) {
            $res->whereIn('appid', $inIds);
        }
        if ($tp == "all") {
            $row = $res->orderBy("appid", "desc")->paginate($pagesize);
        } elseif ($tp == "vr") {
            $row = $res->where("game_type", $gameType)->orderBy("appid", "desc")->paginate($pagesize);
        } else if ($tp == "online") {
            $row = $res->where("game_type", $gameType)->where("push_time", ">", 0)->orderBy("appid", "desc")->paginate($pagesize);
        } else {
            $row = $res->where("game_type", $gameType)->where("push_time", 0)->orderBy("appid", "desc")->paginate($pagesize);
        }
        // $row = DB::connection("db_dev")->table("t_webgame")->where("uid", $uid)->where("game_type", $gameType)
        //     ->orderBy("appid", "desc")->paginate($pagesize);
        return $row;
    }

    /**
     * 获取某一账户下所有的游戏
     * @param   int     uid   uid
     * @param   int     pagesize   每页显示数量
     * @return  array   info    数组，游戏信息
     */
    public function getAppsByUidCount($uid, $tp, $gameType, $inIds = false)
    {
        $res = DB::connection("db_dev")->table("t_webgame")->where("uid", $uid)->where("game_type", $gameType);
        if ($inIds !== false) {
            $res->whereIn('appid', $inIds);
        }
        if ($tp == "offline") {
            $row = $res->where("push_time", ">", 0)->count();
        } else {
            $row = $res->where("push_time", 0)->count();
        }
        return $row;
    }

    /**
     * 获取一个游戏信息
     * @param   int     appid   游戏id
     * @return  array   info    数组，游戏信息
     */
    public function getWebgameInfo($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->first();
        return $row;
    }

    /**
     * 检查游戏名称
     * @param   string     name   游戏名称
     * @return  bool
     */
    public function checkWebgameName($name, $appid = 0)
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

    /**
     * 添加一个游戏信息
     * @param   array   info    数组，游戏信息
     * @return  int     appid
     */
    public function addWebgameInfo($info)
    {
        if (!$info || !is_array($info)) {
        }
        $appid = DB::connection("db_dev")->table("t_webgame")->insertGetId($info);
        return $appid;
    }

    /**
     * 更新一个游戏信息
     * @param   int     appid   游戏id
     * @param   array   info    数组，游戏信息
     * @return  bool
     */
    public function updWebgameInfo($appid, $info)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }

        $ret = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->update($info);
        return $ret;
    }

    /**
     * 更新游戏资源版本号信息
     * @param   int     appid   游戏id
     * @param   array   info    数组，游戏信息
     * @return  bool
     */
    public function incWebgameVerion($appid)
    {
        if (!$appid) {
            return false;
        }
        $ret = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->increment('img_version');
        return $ret;
    }

    /**
     * 根据状态获取游戏列表
     * @param   int     uid     uid
     * @param   int     page    页数
     * @param   int     pagesize   每页显示数量
     * @return  array   info    数组，游戏信息
     */
    public function getWebgameByStat($game_type, $stat, $pagesize)
    {
        $stat      = intval($stat);
        $game_type = intval($game_type);
        $row       = DB::connection("db_dev")->table("t_webgame")->where(array("game_type" => $game_type, "stat" => $stat))->orderBy("appid", "asc")->paginate($pagesize);
        return $row;
    }

    /**
     * 根据状态获取游戏数量
     * @param   int     stat   状态
     * @return  int     count
     */
    public function getWebgameCountByStat($game_type, $stat)
    {
        $stat      = intval($stat);
        $game_type = intval($game_type);
        //$row = DB::connection("db_dev")->table("t_webgame")->where("stat", $stat)->orderBy("appid", "asc")->forPage($page, $pagesize);
        $row = DB::connection("db_dev")->table("t_webgame")->where(array("game_type" => $game_type, "stat" => $stat))->count();
        return $row;
    }

    /**
     * 获取审核过的app
     * @param   int     pagesize   每页显示数量
     * @return  obj     info    数组，游戏信息
     */
    public function getReviewedWebgame($game_type, $pagesize)
    {
        $game_type = intval($game_type);
        $row       = DB::connection("db_dev")->table("t_webgame")->where("game_type", $game_type)->where("stat", ">", 1)->orderBy("appid", "asc")->paginate($pagesize);
        return $row;
    }

    /**
     * 获取审核过的app
     * @return  int     count
     */
    public function getReviewWebgameCount($game_type, $stat)
    {
        $game_type = intval($game_type);
        if ($stat == 0) {
            $row = DB::connection("db_dev")->table("t_webgame")->where("game_type", $game_type)->where("stat", ">", 1)->count();
        } else {
            $row = DB::connection("db_dev")->table("t_webgame")->where("game_type", $game_type)->where("stat", "=", 1)->count();
        }

        return $row;
    }

    /**
     * 根据appid获取服务器列表
     * @param   int     uid     uid
     * @param   int     pagesize   每页显示数量
     * @return  object       分页信息
     */
    public function getWebgameServers($appid, $pagesize = 10)
    {
        $row = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->orderBy("serverid", "desc")->paginate($pagesize);
        return $row;
    }

    /**
     * 添加服务器信息
     * @param   int     appid
     * @param   array    info   服务器信息
     * @return  bool
     */
    public function addWebgameServers($info)
    {
        if (!$info || !is_array($info) || !$info['serverid']) {
            return false;
        }

        $ret = DB::connection("db_webgame")->table("t_game_server")->insert($info);
        if ($ret) {
            return true;
        }
        return false;
    }

    /**
     * 更新一个服务器信息
     * @param   int     appid   游戏id
     * @param   int     serverid   服务器id
     * @param   array   info    数组，服务器信息
     * @return  bool
     */
    public function updWebgameServerInfo($appid, $oldserverid, $info)
    {
        if (!$appid || !$info || !is_array($info) || !$info['serverid']) {
            return false;
        }
        $ret = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->where('serverid', $oldserverid)->update($info);
        return $ret;
    }

    /**
     * 更新一个AppId下服务器信息
     * @param   int     appid   游戏id
     * @param   int     serverid   服务器id
     * @param   array   info    数组，服务器信息
     * @return  bool
     */
    public function updWebgameServer($appid, $info)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }
        $ret = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->update($info);
        return $ret;
    }

    /**
     * 检查服务器名称
     * @param   int     appid
     * @param   string     name
     * @return  bool
     */
    public function checkWebGameServerName($appid, $serverid, $oldServerId, $name)
    {
        if (!$appid || !$name || !$serverid) {
            return false;
        }
        if ($oldServerId != $serverid) {
            $row = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->where("serverid", $serverid)->first();
            if ($row) {
                return 2504;
            }
        }
        if ($oldServerId > 0) {
            $row = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->where("name", $name)->where("serverid", "<>", $oldServerId)->first();
        } else {
            $row = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->where("name", $name)->first();
        }
        if ($row) {
            return 2503;
        }
        return 1;
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
    public function updUser($uid, $info)
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
     * 获取open后台的主账号下配置的子账号
     * [getSonUser description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getSonUser($uid)
    {
        if (!$uid) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_user")->where("parentid", $uid)->get();
        return $row;
    }
    /**
     * 删除子账号
     * [delSonAccount description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function delSonAccount($uid)
    {
        if (!$uid) {
            return false;
        }
        $where = [
            'uid' => $uid,
        ];
        $row = DB::connection("db_dev")->table("t_user")->where($where)->delete();
        return $row;
    }

    /**
     * 根据状态获取用户列表
     * @param   int     uid     uid
     * @param   int     page    页数
     * @param   int     pagesize   每页显示数量
     * @return  array   info    数组，游戏信息
     */
    public function getUserByStat($stat, $pagesize)
    {
        $stat = intval($stat);
        if ($pagesize) {
            $row = DB::connection("db_dev")->table("t_user")->where("stat", $stat)->orderBy("ltime", "asc")->paginate($pagesize);
        } else {
            $row = DB::connection("db_dev")->table("t_user")->where("stat", $stat)->orderBy("ltime", "asc")->paginate($pagesize);
        }
        return $row;
    }
    /**
     * [getUserByStat description]
     * @param  [type] $stat     [description]
     * @param  [type] $pagesize [description]
     * @return [type]           [description]
     */
    public function getUserByStatCount($stat)
    {
        $stat = intval($stat);
        $row  = DB::connection("db_dev")->table("t_user")->where("stat", $stat)->count();
        return $row;
    }

    /**
     * [getUsers description]
     * @param  [type] $stat     [description]
     * @param  [type] $pagesize [description]
     * @return [type]           [description]
     */
    public function getUsersName()
    {
        $out  = [];
        $rows = DB::connection("db_dev")->table("t_user")->select('uid', 'name')->get();
        if ($rows) {
            foreach ($rows as $key => $row) {
                $out[$row['uid']] = $row;
            }
        }
        return $out;
    }

    public function devPerms($userInfo)
    {
        if (!$userInfo) {
            return false;
        }
        $sub = false;
        if (isset($userInfo['parentid'])) {
            $uid     = $userInfo['parentid'];
            $gameIds = array_keys($userInfo['gameperms']);
            $sub     = true;
        } else {
            $uid = $userInfo['uid'];
        }
        if (!isset($gameIds)) {
            $gameIds = false;
        }
        return ['uid' => $uid, 'inIds' => $gameIds, 'sub' => $sub];
    }
}
