<?php

/*
游戏相关model
date:2017/1/33
 */

namespace App\Models;

use App\Helper\ImageHelper;
use App\Helper\Vredis;
use DB;
use Illuminate\Database\Eloquent\Model;

class GameModel extends Model
{

    private $pageSize = 10;

    /**
     * 添加tob游戏
     * @param   int  appid  gameid
     * @return  bool
     */
    public function updateTobGame($appid, $tob_in)
    {
        if (!$appid) {
            return false;
        }
        $ret = DB::connection("db_webgame")->table("t_webgame")->where(['appid' => $appid])->update(['tob_in' => $tob_in]);
        return $ret;
    }

    ## game by page
    public function gameOnlieByPage($type = 0, $words = '', $tob_in = 0)
    {
        $res = DB::connection('db_webgame')->table('t_webgame')->where("game_type", $type)->where("stat", 0);
        if ($words) {
            $res->where("name", "LIKE", '%' . $words . '%');
        }
        if ($tob_in == 1) {
            $res->where('tob_in', '>', 0);
        } else if ($tob_in == 2) {
            $res->where('tob_in', '=', 0);
        } else if ($tob_in == 3) {
            $res->where('tob_in', '=', 2);
        } else if ($tob_in == 4) {
            $res->where('tob_in', '=', 1);
        }
        $row = $res->orderBy("ctime", "desc")->paginate($this->pageSize);
        return $row;
    }

    ## game by page
    public function gameByPage($type = 0, $stat = -1, $words = '', $support = -1)
    {
        $res = DB::connection('db_dev')->table('t_webgame')->where("game_type", $type);
        if ($stat >= 0) {
            if ($stat == 0) {
                $res->where("stat", '<>', 9)->where("send_time", '>', 0);
            } else {
                $res->where("stat", $stat);
            }
        }
        if ($support > 0) {
            $findCase = 'FIND_IN_SET(' . $support . ', support)';
            $res->whereRaw($findCase);
        }
        if ($words) {
            $res->where("name", "LIKE", '%' . $words . '%');
        }
        $row = $res->orderBy("ctime", "desc")->paginate($this->pageSize);
        return $row;
    }

    ## 审核app
    public function passDevGame($game)
    {
        if (!$game || !is_array($game)) {
            return false;
        }
        $appid                      = $game['appid'];
        $gameType                   = $game['game_type'];
        $rsync_ret                  = ImageHelper::cosCopyFiles($game);
        $upinfo                     = array();
        $upinfo['uid']              = $game['uid'];
        $upinfo['name']             = $game['name'];
        $upinfo['letter1']          = $game['letter1'];
        $upinfo['first_class']      = $game['first_class'];
        $upinfo['second_class']     = $game['second_class'];
        $upinfo['desc']             = $game['desc'];
        $upinfo['content']          = $game['content'];
        $upinfo['forumid']          = $game['forumid'];
        $upinfo['img_version']      = $game['img_version'];
        $upinfo['img_slider']       = $game['img_slider'];
        $upinfo['screenshots']      = $game['screenshots'];
        $upinfo['microclient']      = $game['microclient'];
        $upinfo['support']          = $game['support'];
        $upinfo['mini_device']      = $game['mini_device'];
        $upinfo['recomm_device']    = $game['recomm_device'];
        $upinfo['hasgift']          = $game['hasgift'];
        $upinfo['back_img_servers'] = $game['back_img_servers'];
        $upinfo['auth']             = $game['auth'];
        $upinfo['game_type']        = $game['game_type'];
        $upinfo['gameb_name']       = $game['gameb_name'];
        $upinfo['rmb_rate']         = $game['rmb_rate'];
        $upinfo['ocruntimeversion'] = $game['ocruntimeversion'];
        $upinfo['client_size']      = $game['client_size'];
        $upinfo['mountings']        = $game['mountings'];
        $upinfo['language']         = $game['language'];
        $upinfo['product_com']      = $game['product_com'];
        $upinfo['issuing_com']      = $game['issuing_com'];
        $ret                        = $this->passGameInfo($appid, $upinfo);

        $ret = $this->updateDevGameInfo($appid, ['stat' => 5]);

        return $ret;
    }

    public function onlineGame($appid)
    {
        if (!$appid) {
            return false;
        }
        $pub = $this->getPubGameInfo($appid);
        if (!$pub) {
            return false;
        } else {
            $info         = ['send_time' => time()];
            $ret          = $this->updateDevGameInfo($appid, $info);
            $info['stat'] = 0;
            $ret          = $this->updatePubGameInfo($appid, $info);
        }
        return $ret;
    }

    public function offlineGame($appid)
    {
        if (!$appid) {
            return false;
        }
        $pub = $this->getPubGameInfo($appid);
        if (!$pub) {
            $ret = $this->delDevGame($appid);
        } else {
            $info = ['stat' => 9, 'send_time' => 0];
            $ret  = $this->updateDevGameInfo($appid, $info);
            $ret  = $this->updatePubGameInfo($appid, $info);
        }
        return $ret;
    }

    private function passGameInfo($appid, $info)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }
        $row = $this->getPubGameInfo($appid);
        if ($row) {
            return $this->updatePubGameInfo($appid, $info);
        } else {
            $info['stat']  = 5;
            $info['appid'] = $appid;
            return $this->addPubGameInfo($info);
        }
    }

    private function getPubGameInfo($appid)
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

    public function updatePubGameInfo($appid, $info)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }
        $res = DB::connection("db_webgame")->table("t_webgame")->where("appid", $appid)->update($info);
        return $res;
    }

    private function addPubGameInfo($info)
    {
        if (!$info || !is_array($info) || !$info['appid']) {
            return false;
        }
        $appid = DB::connection("db_webgame")->table("t_webgame")->insertGetId($info);
        return $appid;
    }

    public function getDevGameInfo($appid)
    {
        if (!$appid) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->first();
        return $row;
    }

    public function updateDevGameInfo($appid, $info)
    {
        if (!$appid || !$info || !is_array($info)) {
            return false;
        }
        $res = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->update($info);
        return $res;
    }

    private function delDevGame($appid)
    {
        if (!$appid) {
            return false;
        }
        $ret = DB::connection("db_dev")->table("t_webgame")->where("appid", $appid)->delete();
        return $ret;
    }

    ##### 获取多个游戏 ########
    /**
     * 获取多个游戏信息
     * @param   array  appids   游戏id数组
     * @return  array   info    二维数组，游戏信息
     */
    public function getGameByIds($appids)
    {
        if (!$appids || !is_array($appids)) {
            return false;
        }

        // 以 $appids 序列化后做缓存的key
        $row = DB::connection("db_webgame")->table("t_webgame")->whereIn("appid", $appids)->get();
        return $row;
    }

    ##### 获取单个游戏 ########
    /**
     * 获取多个游戏信息
     * @param   array  appids   游戏id数组
     * @return  array   info    二维数组，游戏信息
     */
    public function getGameById($appid)
    {
        if (!$appid) {
            return false;
        }

        $row = DB::connection("db_webgame")->table("t_webgame")->where("appid", $appid)->first();
        return $row;
    }

}
