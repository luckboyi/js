<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models;

use App\Helper\ImageHelper;
use App\Helper\Vredis;
use Config;
use DB;
use Illuminate\Database\Eloquent\Model;

class WebgameModel extends Model
{
    /**
     * 获取礼包库的库、表名后缀
     */
    protected function getDB($uid)
    {
        if (!$uid) {
            return false;
        }
        $db_suff  = 0;
        $tbl_suff = $uid % 32;
        return array('db' => "db_webgame", 'table_mygift' => "t_my_gift_" . $tbl_suff);
    }

    /**
     * 获取礼包库的库、表名后缀
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
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             页 游 信 息 表                                                  |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取一个游戏信息
     * @param   int     appid   游戏id
     * @return  array   info    数组，游戏信息
     */
    public function getOneGameInfo($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_webgame")->where("appid", $appid)->first();
        if ($row && isset($row['mini_device'])) {
            $row['mini_device'] = json_decode($row['mini_device'], true);
        }
        if ($row && isset($row['recomm_device'])) {
            $row['recomm_device'] = json_decode($row['recomm_device'], true);
        }
        return $row;
    }

    /**
     * 获取多个游戏信息
     * @param   array  appids   游戏id数组
     * @return  array   info    二维数组，游戏信息
     */
    public function getMultiGameName($appids, $format = 0)
    {
        if (!$appids || !is_array($appids)) {
            return false;
        }

        // 以 $appids 序列化后做缓存的key
        $row = DB::connection("db_webgame")->table("t_webgame")->select('appid', 'name')->whereIn("appid", $appids)->get();
        if ($format == 1 && is_array($row)) {
            $result = [];
            for ($i = 0; $i < count($row); $i++) {
                $result[$row[$i]['appid']] = $row[$i]['name'];
            }
            return $result;
        }
        return $row;
    }

    /**
     * 获取多个游戏信息
     * @param   array  appids   游戏id数组
     * @return  array   info    二维数组，游戏信息
     */
    public function getMultiGameInfo($appids)
    {
        if (!$appids || !is_array($appids)) {
            return false;
        }

        // 以 $appids 序列化后做缓存的key
        $row = DB::connection("db_webgame")->table("t_webgame")->whereIn("appid", $appids)->get();
        return $row;
    }

    /**
     * 获取所有游戏信息
     * @param   array  appids   游戏id数组
     * @return  array   info    二维数组，游戏信息
     */
    public function getAllGameInfo($tp = 0, $getModel = 0, $game_type = 0, $stat = 0)
    {
        if ($getModel) {
            $model = CommonModel::set("Game");
        } else {
            $model = DB::connection("db_webgame")->table("t_webgame");
        }

        if ($stat == -1) {
            // 查询所有
            $clause = array("game_type" => $game_type);
        } else {
            $clause = array("game_type" => $game_type, "stat" => 0);
        }
        $model = $model->where($clause);

        if ($tp > 0) {
            $model = $model->where('first_class', $tp);
        }

        if ($getModel) {
            return $model;
        }

        $row = $model->get();

        return $row;
    }

    /**
     * 根据类型获取游戏信息
     *
     * @param  [type] $gameType [description]
     * @return [type]           [description]
     */
    public function getGamesInfo($gameType)
    {
        $model = CommonModel::set("Game")->where("stat", 0)
            ->where("game_type", $gameType);

        return $model;
    }

    /**
     * 添加游戏信息
     * @param   array  appids   游戏id数组
     * @return  bool
     */
    public function addGameInfo($info)
    {
        if (!$info || !is_array($info) || !$info['appid']) {
            return false;
        }
        $appid = DB::connection("db_webgame")->table("t_webgame")->insertGetId($info);
        return $appid;
    }

    /**
     * 修改游戏信息
     * @param   array  appids   游戏id数组
     * @return  bool
     */
    public function updGameInfo($appid, $info)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }
        $res = DB::connection("db_webgame")->table("t_webgame")->where("appid", $appid)->update($info);
        return $res;
    }

    public function gameNewsByPage($gameid, $pageSize = 10)
    {
        $row = DB::connection('db_webgame')->table('t_game_news')->where("gameid", $gameid)->orderBy("ctime", "desc")->paginate($pageSize);
        return $row;
    }

    public function gameNewsByGameCategory($gameid, $tp, $num = 10)
    {
        $row = DB::connection('db_webgame')->table('t_game_news')->where("gameid", $gameid)->where('tp', $tp)->orderBy("ctime", "desc")->skip(0)->take($num)->get();
        return $row;
    }

    public function gameNewsByGameCategoryForPage($gameid, $tp, $start, $count)
    {
        $row['count'] = DB::connection('db_webgame')->table('t_game_news')->where("gameid", $gameid)->where('tp', $tp)->count();
        $row['data']  = DB::connection('db_webgame')->table('t_game_news')->where("gameid", $gameid)->where('tp', $tp)->orderBy("ctime", "desc")->get();
        return $row;
    }

    public function updateWebGameNews($id, $info)
    {
        if (!$info) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection('db_webgame')->table('t_game_news')->where('id', $id)->update($info);
        } else {
            $ret = DB::connection('db_webgame')->table('t_game_news')->insert($info);
        }

        return $ret;
    }

    public function delWebGameNews($id)
    {
        if (!$id) {
            return false;
        }

        $ret = DB::connection('db_webgame')->table('t_game_news')->where('id', $id)->delete();

        return $ret;
    }
    public function vrGameCategoryPage($page = 1, $pageNum = 10, $search = [])
    {
        $startNum = ($page - 1) * $pageNum;
        if (isset($search['class_id'])) {
            $class_id  = intval($search['class_id']);
            $findCase  = 'FIND_IN_SET(' . $class_id . ', first_class)';
            $vrGameRow = DB::connection('db_webgame')->table('t_webgame')->where("game_type", 1)->whereRaw($findCase)->skip($startNum)->take($pageNum)->get();
        } else {
            $vrGameRow = DB::connection('db_webgame')->table('t_webgame')->where("game_type", 1)->skip($startNum)->take($pageNum)->get();
        }

        $vrGames = [];
        if ($vrGameRow) {
            foreach ($vrGameRow as $key => $vrGame) {
                $resInfo = ImageHelper::url('vrgame', $vrGame['appid'], $vrGame['img_version'], $vrGame['img_slider'], false);
                $game    = [
                    'id'           => $vrGame['appid'],
                    'name'         => $vrGame['name'],
                    'sell'         => $vrGame['sell'],
                    'score'        => $vrGame['score'],
                    'first_class'  => $vrGame['first_class'],
                    'support'      => $vrGame['support'],
                    'image'        => $resInfo,
                    'play'         => $vrGame['play'],
                    'desc'         => $vrGame['desc'],
                    'content'      => $vrGame['content'],
                    'publish_date' => $vrGame['send_time'],
                ];
                $vrGames[] = $game;
            }
        }
        return $vrGames;
    }

    public function webGameCategoryPage($page = 1, $pageNum = 10, $search = [])
    {
        $startNum = ($page - 1) * $pageNum;
        if (isset($search['class_id'])) {
            $class_id   = intval($search['class_id']);
            $findCase   = 'FIND_IN_SET(' . $class_id . ', first_class)';
            $webGameRow = DB::connection('db_webgame')->table('t_webgame')->where("game_type", 0)->whereRaw($findCase)->skip($startNum)->take($pageNum)->get();
        } else {
            $webGameRow = DB::connection('db_webgame')->table('t_webgame')->where("game_type", 0)->skip($startNum)->take($pageNum)->get();
        }

        $webGames = [];
        if ($webGameRow) {
            foreach ($webGameRow as $key => $webGame) {
                $resInfo = ImageHelper::url('webgame', $webGame['appid'], $webGame['img_version'], $webGame['img_slider'], false);
                $game    = [
                    'id'           => $webGame['appid'],
                    'name'         => $webGame['name'],
                    'sell'         => $webGame['sell'],
                    'score'        => $webGame['score'],
                    'first_class'  => $webGame['first_class'],
                    'support'      => $webGame['support'],
                    'image'        => $resInfo,
                    'play'         => $webGame['play'],
                    'desc'         => $webGame['desc'],
                    'publish_date' => $webGame['send_time'],
                ];
                $webGames[] = $game;
            }
        }
        return $webGames;
    }
    /**
     * 获取VR游戏的下拉刷新的数据
     * @param $gameType
     * @param $page
     * @param $pagenum
     * @return bool
     */
    public function vrPageDate($gameType, $page, $pagenum)
    {
        if ($gameType == '' || $page == '' || $pagenum == '') {
            return false;
        }
        $startNum = ($page - 1) * $pagenum;
        $gameInfo = DB::connection('db_webgame')->table('t_webgame')->where("game_type", $gameType)->skip($startNum)->take($pagenum)->get();
        return $gameInfo;
    }

    /**
     * 获取网页游戏的下拉刷新的数据
     * @param $gameType
     * @param $page
     * @param $pagenum
     * @return bool
     */
    public function webPageDate($type, $gameType, $page, $pagenum)
    {
        if ($gameType === '' || $page == '' || $pagenum == '') {
            return false;
        }
        $startNum = ($page - 1) * $pagenum;
        if ($type === 0) {
            $gameInfo = DB::connection('db_webgame')->table('t_webgame')->where("stat", 0)->where("game_type", $gameType)->skip($startNum)->take($pagenum)->get();
        } else {
            $gameInfo = DB::connection('db_webgame')->table('t_webgame')->where("stat", 0)->where("game_type", $gameType)->where('first_class', $type)->skip($startNum)->take($pagenum)->get();
        }
        return $gameInfo;
    }

    /**
     * 修改游戏信息
     * @param   array  appids   游戏id数组
     * @return  bool
     */
    public function setGameInfo($appid, $info, &$isnew = null)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }
        $row = $this->getOneGameInfo($appid);
        if ($row) {
            return $this->updGameInfo($appid, $info);
        } else {
            $isnew         = true;
            $info['stat']  = 5;
            $info['appid'] = $appid;
            return $this->addGameInfo($info);
        }
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             页 游 服 务 器 表                                               |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取一个server信息
     * @param   int     appid   appid
     * @param   int     serverid   serverid
     * @return  array   info    数组，服务器信息
     */
    public function getOneServer($appid, $serverid)
    {
        if (!$appid || !$serverid) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_game_server")->where(array("appid" => $appid, "serverid" => $serverid))->first();
        return $row;
    }

    /**
     * 获取一个app的所有服信息
     * @param   int     appid   appid
     * @return  array   info    数组，服务器信息
     */
    public function getAllServer($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->orderBy("serverid", "asc")->get();
        return $row;
    }

    /**
     * 获取一个app的可用服信息
     * @param   int     appid   appid
     * @return  array   info    数组，服务器信息
     */
    public function getEnableServers($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_game_server")
            ->where("appid", $appid)->where("is_publish", 1)
            ->where("start", "<=", time())->where("status", "<", 9)
            ->orderBy("serverid", "asc")->get();
        return $row;
    }

    /**
     * 获取一个app的所有服信息
     * @param   int     appid   appid
     * @return  array   info    数组，服务器信息
     */
    public function getOneApp($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_game_server")->where("appid", $appid)->orderBy("serverid", "asc")->get();
        return $row;
    }

    /**
     * 获取多个信息
     * @param   array   $servers=array(array('appid'=>$appid1, 'serverid'=>$serverid1), array('appid'=>$appid2, 'serverid'=>$serverid2));
     * @return  array   info    二维数组，服务器信息
     */
    public function getMultiServer($servers)
    {
        if (!$servers || !is_array($servers)) {
            return false;
        }
        $result = array();
        for ($i = 0; $i < count($servers); $i++) {
            $appid    = $servers[$i]['appid'];
            $serverid = $servers[$i]['serverid'];
            $row      = DB::connection("db_webgame")->table("t_game_server")->where(array("appid" => $appid, "serverid" => $serverid))->first();
            if ($row) {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * 添加一个server信息
     * @param   int     appid   appid
     * @param   int     serverid   serverid
     * @param   array   info    数组，服务器信息
     * @return  bool
     */
    public function addServer($appid, $serverid, $info)
    {
        if (!$appid || !$serverid || !$info || !is_array($info)) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_game_server")->where(array("appid" => $appid, "serverid" => $serverid))->first();
        if ($row) {
            return false;
        }
        $info['appid']    = $appid;
        $info['serverid'] = $serverid;
        $ret              = DB::connection("db_webgame")->table("t_game_server")->insert($info);
        return $ret;
    }

    /**
     * 最新的游戏服
     * 10天内的将要开的服
     * @param   int     num     获取的记录数量
     * @return  array   info    数组，服务器信息
     */
    public function latestGameServer($num)
    {
        $nowstamp = time();
        $endstamp = $nowstamp + 86400 * 10;
        $row      = DB::connection("db_webgame")->table("t_game_server")->where('is_publish', 1)->where('start', ">", $nowstamp)->where('start', "<", $endstamp)->orderBy("start", "asc")->forPage(0, $num)->get();
        return $row;
    }

    /**
     * 某个游戏最新的游戏服
     * @param   int     num     获取的记录数量
     * @return  array   info    数组，服务器信息
     */
    public function maxServeridByAppid($appid)
    {
        if (!$appid) {
            return false;
        }
        $nowstamp = time();
        $serverid = DB::connection("db_webgame")->table("t_game_server")->where('appid', $appid)->where('is_publish', 1)->where('status', '<', 9)->where('start', "<=", $nowstamp)->max("serverid");
        return intval($serverid);
    }

    /**
     * 已经开的新游戏服
     * @param   int     num     获取的记录数量
     * @return  array   info    数组，服务器信息
     */
    public function newGameServer($num)
    {
        $nowstamp = time();
        $row      = DB::connection("db_webgame")->table("t_game_server")->where('is_publish', 1)->where('start', "<=", $nowstamp)->orderBy("start", "desc")->forPage(0, $num)->get();
        return $row;
    }

    /**
     * 修改一个server信息
     */
    public function updServer($appid, $serverid, $info)
    {
        if (!$appid || !$serverid || !$info || !is_array($info)) {
            return false;
        }
        if (isset($info['appid'])) {
            unset($info['appid']);
        }

        if (isset($info['serverid'])) {
            unset($info['serverid']);
        }

        $res = DB::connection("db_webgame")->table("t_game_server")->where(array("appid" => $appid, "serverid" => $serverid))->update($info);
        return $res;
    }

    /**
     * 删除一个server信息
     */
    public function delServer($appid, $serverid)
    {
        if (!$appid || !$serverid) {
            return false;
        }
        $res = DB::connection("db_webgame")->table("t_game_server")->where(array("appid" => $appid, "serverid" => $serverid))->delete();
        return $res;
    }

    /**
     * 获取推荐的服务器列表
     * 缓存2个小时
     */
    public function recommendServer($appid)
    {
        if (!$appid) {
            return false;
        }
        $nowstamp = time();
        $res      = DB::connection("db_webgame")->table("t_game_server")->where(array("appid" => $appid, "recommend" => 1))->where('start', "<=", $nowstamp)->where('status', '<', 9)->get();
        return $res;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             页 游 游 戏 记 录 表                                            |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 设置某个页游的最后进的服务器id
     * @param   int     uid
     * @param   int     appid
     * @param   int     serverid
     * @return bool
     */
    public function setLastGameServerid($uid, $appid, $serverid)
    {
        if (!$uid || !$appid || !$serverid) {
            return false;
        }
        $ret = Vredis::hset("user_flags", $uid, "webgame_last_serverid_" . $appid, $serverid);
        Vredis::close();
        if ($ret === false) {
            return false;
        }
        return true;
    }

    /**
     * 获取某个页游的最后进的服务器id
     * @param   int     uid
     * @param   int     appid
     * @return  string
     */
    public function getLastGameServerid($uid, $appid)
    {
        if (!$uid || !$appid) {
            return false;
        }
        $ret = Vredis::hget("user_flags", $uid, "webgame_last_serverid_" . $appid);
        Vredis::close();
        return $ret;
    }

    /**
     * 获取页游游戏记录
     */
    public function getGameLog($uid, $num, $game_type = 0)
    {
        if (!$uid) {
            return false;
        }

        $clause = [
            "uid"       => $uid,
            "game_type" => $game_type,
        ];
        if ($game_type == 1) {
            $clause['status'] = 0;
        }
        $dbRes = $this->getGamelogDB($uid);
        $model = DB::connection("db_user_log")->table($dbRes['table_log'])->distinct("appid")->where($clause)->orderBy("ltime", "desc");

        if ($num != "all") {
            $model = $model->forPage(0, (int) $num);
        }

        $row = $model->get();
        return $row;
    }

    /**
     * 获取游戏记录
     */
    public function getOneGameLog($uid, $appid, $serverid = 0)
    {
        if (!$uid || !$appid) {
            return false;
        }

        $clause = [
            "uid"      => $uid,
            "appid"    => $appid,
            "serverid" => $serverid,
        ];
        $dbRes = $this->getGamelogDB($uid);
        $row   = DB::connection("db_user_log")->table($dbRes['table_log'])->where($clause)->first();
        return $row;
    }

    /**
     * 根据appid获取游戏记录
     */
    public function getGameLogByAppid($uid, $appid, $gameType)
    {
        if (!$uid || !$appid || !in_array($gameType, [0, 1])) {
            return false;
        }
        $clause = [
            "uid"       => $uid,
            "appid"     => $appid,
            "game_type" => $gameType,
        ];
        $dbRes = $this->getGamelogDB($uid);

        $row = DB::connection("db_user_log")->table($dbRes['table_log'])->distinct("appid")->where($clause)->orderBy("ltime", "desc")->get();
        return $row;
    }

    /**
     * 添加页游游戏记录
     */
    public function addGameLog($uid, $appid, $serverid, $info)
    {
        if (!$uid || !$appid || !$info || !is_array($info)) {
            return false;
        }
        if (isset($info['uid'])) {
            unset($info['uid']);
        }

        if (isset($info['appid'])) {
            unset($info['appid']);
        }

        if (isset($info['serverid'])) {
            unset($info['serverid']);
        }

        $dbRes = $this->getGamelogDB($uid);

        $info['uid']      = $uid;
        $info['appid']    = $appid;
        $info['serverid'] = $serverid;
        $info['ltime']    = time();
        $ret              = DB::connection("db_user_log")->table($dbRes['table_log'])->insertUpdate($info);
        return $ret;
    }

    /**
     * 获取某个游戏的服务器记录
     * 实时缓存
     */
    public function getGameServerLogByAppid($uid, $appid)
    {
        if (!$uid || !$appid) {
            return false;
        }
        $dbRes = $this->getGamelogDB($uid);
        $row   = DB::connection("db_user_log")->table($dbRes['table_log'])->where(array('uid' => $uid, "appid" => $appid))->orderBy("ltime", "desc")->get();
        return $row;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             礼 包                                                           |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取礼包信息
     */
    public function hasGiftGames()
    {
        $now    = time();
        $clause = array("stat");
        $arr    = DB::connection("db_webgame")->table("t_gift")->distinct("appid")->where('end' > $now)->get();
        $row    = array();
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr['stat'] == 1 || $arr['start'] > $now) {
                continue;
            }
            $row[] = $arr['appid'];
        }
        return $row;
    }

    /*
     * 判断礼包是否可以领取
     */
    public function getGiftTm($gid)
    {
        $now   = time();
        $where = [
            'gid' => $gid,
        ];
        $row = DB::connection("db_webgame")->table("t_gift")->where($where)->get();
        if ($now >= $row[0]['start'] && $row[0]['end'] >= $now) {
            return true;
        }
        return false;
    }

    /**
     * 获取礼包信息
     */
    public function getGiftInfoById($gid)
    {
        if (!$gid) {
            return false;
        }
        //$row = DB::connection("db_webgame")->table("t_gift")->where('gid', $gid)->orderBy("ltime", "desc")->forPage(0, 15);
        $row = DB::connection("db_webgame")->table("t_gift")->where('gid', $gid)->orderBy("ltime", "desc")->get();
        return $row;
    }

    /**
     * 获取礼包信息
     * @param   int     appid   appid
     * @param   int     serverid    serverid
     * @param   array   数组
     */
    public function getAllGift($appid = null, $serverid = null)
    {
        $clause = array();
        if ($appid) {
            $clause['appid'] = $appid;
            if ($serverid) {
                $clause['serverid'] = $serverid;
            }
        }
        if ($appid !== '' || $serverid !== '') {
            $row = DB::connection("db_webgame")->table("t_gift")->where($clause)->get();
        } else {
            $row = DB::connection("db_webgame")->table("t_gift")->where($clause)->groupBy('appid')->get();
        }

        return $row;
    }

    /**
     * 获取礼包信息
     * @param   int     appid   appid
     * @param   int     serverid    serverid
     * @param   array   数组
     */
    public function getAllGiftOne($appid = null, $serverid = null)
    {
        $clause = array();
        if ($appid) {
            $clause['appid'] = $appid;
            if ($serverid) {
                $clause['serverid'] = $serverid;
            }
        }
        $row = DB::connection("db_webgame")->table("t_gift")->where($clause)->first();

        return $row;
    }

    /**
     * @return mixed
     */
    public function getAllGiftList()
    {
        $row = DB::connection("db_webgame")->table("t_gift")->get();
        return $row;
    }

    /**
     * 获取礼包剩余数量
     */
    public function getGiftNum($gid)
    {
        if (!$gid) {
            return false;
        }
        $num = Vredis::llen("webgame_gift", $gid);
        Vredis::close();
        return $num;
    }

    /**
     * 添加一个礼包
     */
    public function addGift($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $gid = DB::connection("db_webgame")->table("t_gift")->insertGetId($info);
        return $gid;
    }

    /**
     * 获取一个礼包兑换码
     */
    public function getOneGiftCode($gid)
    {
        if (!$gid) {
            return false;
        }
        $code = Vredis::lpop("webgame_gift", $gid);
        Vredis::close();
        return $code;
    }

    /**
     * 设置领取记录的缓存记录
     * @param   int     uid
     * @param   int     gid
     * @param   string  礼包兑换码
     * @return bool
     */
    public function setGetGiftRedis($uid, $gid, $code)
    {
        if (!$uid || !$gid || !$code) {
            return false;
        }
        $ret = Vredis::hsetnx("webgame_user_gift", $uid, $gid, $code);
        Vredis::close();
        return $ret;
    }

    /**
     * 获取领取礼包的redis缓存
     * @param $uid
     * @param $gid
     * @return string
     */
    public function getGiftRedis($uid, $gid)
    {
        if (!$uid || !$gid) {
            return false;
        }
        $ret = Vredis::hget("webgame_user_gift", $uid, $gid);
        Vredis::close();
        return $ret;
    }

    /**
     * 添加礼包兑换码
     * 先添加到数据库，然后导入到队列中
     * @param   int     gid
     * @param   array   codes   兑换码数组
     * @return  bool
     */
    public function addGiftCodes($gid, $appid, $serverid, $codes)
    {
        if (!$gid || !$appid || !$codes || !is_array($codes)) {
            return false;
        }
        /**
         * 插入数据库
         * 成功的写到队列里
         */
        $info = array_chunk($codes, 50);
        for ($i = 0; $i < count($info); $i++) {
            $subinfo = $info[$i];
            $insert  = array();
            for ($j = 0; $j < count($subinfo); $j++) {
                if (!$subinfo[$j]) {
                    continue;
                }
                $insert[]   = array("appid" => $appid, "gid" => $gid, "serverid" => $serverid, "code" => $subinfo[$j]);
                $thiscode[] = $subinfo[$j];
            }
            //这里就不用try-catch了，否则数据太多，会有南无几个插入不成功的。
            $ret = DB::connection("db_webgame")->table("t_gift_code")->insert($insert);
            // try {
            //     $ret = DB::connection("db_webgame")->table("t_gift_code")->insert($insert);
            // } catch (\Exception $e) {
            //     return false;
            // }

            for ($k = 0; $k < count($thiscode); $k++) {
                $code = trim($thiscode[$k]);
                $ret  = Vredis::rpush("webgame_gift", $gid, $code);
            }
            Vredis::close();

            $insert   = array();
            $thiscode = array();
        }

        /**
         * 写到redis队列
         */

        //写入t_webgame是否有礼包状态
        $where = array(
            'appid' => $appid,
        );
        $updata = array(
            'hasgift' => 1,
        );
        $result = DB::connection("db_webgame")->table("t_webgame")->where($where)->update($updata);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             我 的 礼 包                                                     |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 我领到的礼包
     */
    public function getMyGiftCodes($uid, $appid = "", $gid = "", $serverid = "", $page = "", $pagenum = "")
    {
        if (!$uid) {
            return false;
        }

        $clause = array("uid" => $uid);
        if ($appid) {
            $clause['appid'] = $appid;
            if ($gid) {
                $clause['gid'] = $gid;
            }
            if ($serverid) {
                $clause['serverid'] = $serverid;
            }
        }

        $dbRes    = $this->getDB($uid);
        $startNum = ($page - 1) * $pagenum;
        //$row = DB::connection("db_webgame")->table($dbRes['table_mygift'])->where($clause)->orderBy("id", "desc")->forPage($page, $pagenum);
        if ($page !== '' && $pagenum !== '') {
            $row = DB::connection("db_webgame")->table($dbRes['table_mygift'])->where($clause)->orderBy("id", "desc")->skip($startNum)->take($pagenum)->get();
        } else {
            $row = DB::connection("db_webgame")->table($dbRes['table_mygift'])->where($clause)->get();
        }
        return $row;
    }
    /**
     * 我领到的礼包的总数
     */
    public function getMyGiftCodesNum($uid, $appid = "", $serverid = "")
    {
        if (!$uid) {
            return false;
        }

        $clause = array("uid" => $uid);
        if ($appid) {
            $clause['appid'] = $appid;
            if ($serverid) {
                $clause['serverid'] = $serverid;
            }
        }

        $dbRes = $this->getDB($uid);
        //$row = DB::connection("db_webgame")->table($dbRes['table_mygift'])->where($clause)->orderBy("id", "desc")->forPage($page, $pagenum);
        $row = DB::connection("db_webgame")->table($dbRes['table_mygift'])->where($clause)->orderBy("id", "desc")->count();
        return $row;
    }
    /**
     * 添加我领到的礼包
     */
    public function addMyGiftCode($uid, $info)
    {
        if (!$uid || !$info || !is_array($info)) {
            return false;
        }

        $dbRes = $this->getDB($uid);
        $row   = DB::connection("db_webgame")->table($dbRes['table_mygift'])->insert($info);
        return $row;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             获 取 首 页 信 息                                                     |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 根据广告位获取广告
     *
     * @param  [type] $adid  [description]
     * @param  [type] $count [description]
     * @return [type]        [description]
     */
    public static function getAd($adid, int $count)
    {
        return CommonModel::set("Ad")->where("ad_id", $adid)->limit($count)->get();
    }

    /**
     * 获取推荐游戏
     *
     * @param  [type] $pos_id [description]
     * @param  int    $count  [description]
     * @return [type]         [description]
     */
    public static function getWebGameRecommend($pos_id, int $count)
    {
        return CommonModel::set("Recommend")->select("content_id")
            ->where("position_id", "like", 'webgame\_' . $pos_id . '\_%')
            ->limit($count)->get()->toArray();
    }

    /**
     * 获取游戏类型
     *
     * @return [type] [description]
     */
    public static function getGameTypes($type = 0)
    {
        if ($type == 0) {
            return Config::get("webgame.class");
        } else {
            return Config::get("vrgame.class");
        }
    }

    /**
     * 获取设备类型
     *
     * @return [type] [description]
     */
    public static function getDeviceTypes()
    {
        return Config::get("vrgame.support_device");
    }
    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             获 取 首 页 广 告 位 信 息                                                     |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取页游的广告位信息（banner和推荐游戏）
     * @param $data
     * @return string
     */
    public function getWebGameInfo($data)
    {
        $webGameInfo = '';
        if (!empty($data)) {
            $vtid  = $data['vtid'];
            $where = array(
                'ad_id' => 'webgame_' . $vtid,
            );
            //$sortInfo = DB::connection('db_operate')->table('v_videotype')->where($where)->get();

            $adInfo = DB::connection('db_operate')->table('v_add_ad')->where($where)->paginate(15);

            //$case = "SELECT * FROM v_videos WHERE find_in_set('" . intval($data['vtid']) . "', videotypeid) AND ispassed='Y' AND recommend=1";
            if (empty($adInfo)) {
                return $webGameInfo;
            }
            $webGameInfo = $adInfo;
        }
        return $webGameInfo;
    }

    /**
     * 添加页游广告位数据
     * @param $data
     * @return bool
     */
    public function addWebGameAd($data)
    {
        if (empty($data)) {
            return false;
        }

        $ret = DB::connection('db_operate')->table('v_add_ad')->insert($data);
        return $ret;
    }

    /**
     * 删除页游的广告信息
     * @param $data
     */
    public function webGameAdDel($data)
    {
        if (empty($data)) {
            return false;
        }

        $where = array(
            'id' => $data['id'],
        );

        $ret = DB::connection('db_operate')->table('v_add_ad')->where($where)->delete();
        return $ret;
    }

    /**
     * 获取所有页游分页的信息
     * @param $data
     * @return string
     */
    public function getAllGameInfoPage($data)
    {
        if (!empty($data)) {
            $webgameInfo = '';
            if (intval($data['searchword'])) {
                $case1 = array(
                    'appid' => $data['searchword'],
                );
                $webgameInfo = DB::connection("db_webgame")->table("t_webgame")->where("stat", 0)->where('game_type', 0)->where($case1)->paginate(15);
                //return $webgameInfo;
            }
            if ($webgameInfo === '') {
                $webgameInfo = DB::connection("db_webgame")->table("t_webgame")->where("stat", 0)->where('game_type', 0)->where('name', 'like', '%' . $data['searchword'] . '%')->paginate(15);
            }
            return $webgameInfo;
        }
        $webgameInfo = DB::connection("db_webgame")->table("t_webgame")->where("stat", 0)->where('game_type', 0)->paginate(15);
        return $webgameInfo;
    }

    /*
     * 获取最后一个礼包的信息
     */
    public function getLastGiftInfo()
    {
        $ret = DB::connection('db_webgame')->table('t_gift')->orderBy('gid', 'desc')->first();
        return $ret;
    }

    /*
     * 获取所有的页游礼包数据
     */
    public function getAllGiftListPage()
    {
        $row = DB::connection("db_webgame")->table("t_gift")->where("stat", 0)->paginate(15);
        return $row;
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             VR 游 戏 的 方 法                                                |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取一个游戏信息
     * @param   int     appid   游戏id
     * @return  array   info    数组，游戏信息
     */
    public function getOneVrGameInfo($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_webgame")->table("t_webgame")->where("appid", $appid)->first();
        if ($row && isset($row['mini_device'])) {
            $row['mini_device'] = json_decode($row['mini_device'], true);
        }
        if ($row && isset($row['recomm_device'])) {
            $row['recomm_device'] = json_decode($row['recomm_device'], true);
        }
        return $row;
    }

    /**
     * 获取多个游戏信息
     * @param   array  appids   游戏id数组
     * @return  array   info    二维数组，游戏信息
     */
    public function getMultiVrGameInfo($appids)
    {
        if (!$appids || !is_array($appids)) {
            return false;
        }

        // 以 $appids 序列化后做缓存的key
        $row = DB::connection("db_webgame")->table("t_webgame")->whereIn("appid", $appids)->get();
        return $row;
    }

    /**
     * 获取所有游戏信息
     * @param   array  appids   游戏id数组
     * @return  array   info    二维数组，游戏信息
     */
    public function getAllVrGameInfo($tp = 0, $getModel = 0)
    {
        if ($getModel) {
            $model = CommonModel::set("Game");
        } else {
            $model = DB::connection("db_webgame")->table("t_webgame");
        }

        $model = $model->where("stat", 0);

        if ($tp > 0) {
            $model = $model->where('first_class', $tp);
        }

        $model = $model->where('game_type', 1);

        if ($getModel) {
            return $model;
        }

        $row = $model->get();

        return $row;
    }

    /**
     * 根据类型获取游戏信息
     *
     * @param  [type] $gameType [description]
     * @return [type]           [description]
     */
    public function getVrGamesInfo($gameType)
    {
        $model = CommonModel::set("Game")->where("stat", 0)
            ->where("game_type", $gameType);

        return $model;
    }

    /**
     * 添加VR游戏的购买和下载的历史记录
     */
    public function addVrGameHistory($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = [
            'uid'   => $info['uid'],
            'appid' => $info['appid'],
        ];

        $dbRes  = $this->getGamelogDB($info['uid']);
        $result = DB::connection("db_user_log")->table($dbRes['table_log'])->where($where)->get();
        if ($result) {
            return true;
        }

        $row = DB::connection("db_user_log")->table($dbRes['table_log'])->insert($info);
        //添加下载次数-累加
        $this->addPlayNum($info['appid']);
        return $row;
    }
    /**
     * 添加游戏的play次数
     * [addPlayNum description]
     * @param [type] $appid [description]
     */
    public function addPlayNum($appid)
    {
        $playInfo = DB::connection("db_webgame")->table("t_webgame")->where("appid", $appid)->select("play")->first();
        $update   = [
            'play' => $playInfo['play'] + 1,
        ];
        $ret = DB::connection("db_webgame")->table("t_webgame")->where("appid", $appid)->update($update);
        return $ret;
    }

    /**
     * 删除用户的VR游戏的购买和下载的历史记录
     */
    public function delVrGameHistory($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = [
            'uid'   => $info['uid'],
            'appid' => $info['appid'],
        ];

        $update = [
            'status' => 1,
        ];

        $dbRes = $this->getGamelogDB($info['uid']);
        $row   = DB::connection("db_user_log")->table($dbRes['table_log'])->where($where)->update($update);
        return $row;
    }

    /**
     * 获取用户的购买下载记录
     */
    public function getVrGameHistory($uid)
    {
        if (!$uid) {
            return [];
        }
        $where = [
            'uid'       => $uid,
            'game_type' => 1,
            'status'    => 0,
        ];

        $dbRes  = $this->getGamelogDB($uid);
        $result = DB::connection("db_user_log")->table($dbRes['table_log'])->where($where)->get();

        return $result;
    }
    /**
     * 添加用户的游戏记录时长
     */
    public function addVrGameTimes($info)
    {
        if (!$info || !is_array($info)) {
            return false;
        }
        $where = [
            'uid'   => $info['uid'],
            'appid' => $info['appid'],
        ];

        $dbRes  = $this->getGamelogDB($info['uid']);
        $result = DB::connection("db_user_log")->table($dbRes['table_log'])->where($where)->get();
        if (!$result) {
            return false;
        }

        $update = [
            'timelen' => $result[0]['timelen'] + $info['times'],
        ];

        $ret = DB::connection("db_user_log")->table($dbRes['table_log'])->where($where)->update($update);
        return $ret;
    }

    /**
     * 获取用户的历史记录
     */
    public function getWebGameHistory($uid, $count = 0)
    {
        if (!$uid) {
            return [];
        }
        $where = [
            'uid'       => $uid,
            'game_type' => 0,
            'status'    => 0,
        ];

        $dbRes = $this->getGamelogDB($uid);
        $query = DB::connection("db_user_log")->table($dbRes['table_log'])->where($where)->orderBy("ltime", "desc");
        if ($count && $count > 0) {
            $query = $query->limit($count);
        }
        $result = $query->get();
        return $result;
    }

    /**
     * 是否是测试账号
     */
    public function isTestAccount($uid, $ownerid)
    {
        if (!$uid) {
            return false;
        }
        if ($uid >= 100 && $uid <= 200 || $uid == $ownerid) {
            return true;
        }
        return false;
    }
}
