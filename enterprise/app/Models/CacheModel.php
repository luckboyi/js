<?php
/*
 * 缓存设置
 */
namespace App\Models;

use App\Helper\Vredis;
use App\Helper\Memcached;
use Illuminate\Database\Eloquent\Model;

class CacheModel extends Model
{
    /**
     * vr客户端版本信息字段
     */
    private $clientVersionFields = ["version", "pushtime", "newfeature", "whole_size", "online_size", "pushnum", "updtype"];

    /**
     * 推送列表过期时间
     */
    private $clientPushListExpire = 86400 * 90;

    /*
      +-----------------------------------------------------------------------------+
      |                                                                             |
      |             客 户 端 版 本 信 息                                            |
      |                                                                             |
      +-----------------------------------------------------------------------------+
    */
    /**
     * 设置客户端版本信息
     * @param   string  type    版本类型，stable:稳定版本;latest:最新版本;
     * @param   array   info    版本信息 array("version" => xxx, "pushtime" => xxx, "newfeature" => xxx, "whole_size" => xxx, "online_size" => xxx, "pushnum" => xxx, "updtype" => xxx)
     * @return  bool
     */
    public function setClientVersionInfo($type, $info)
    {
        if (!$type || !in_array($type, array("stable", "latest")) || !$info || !is_array($info)) {
            return false;
        }
        if(count($info) != count($this->clientVersionFields)) {
            return false;
        }
        for($i = 0; $i < count($this->clientVersionFields); $i++) {
            if(!isset($info[$this->clientVersionFields[$i]])) {
                return false;
            }
        } // end for

        $ret = Vredis::hmset("vrclient", "version_".$type, $info);
        Vredis::close();
        return $ret;
    }

    /**
     * 客户端版本信息
     * @param   string  type    版本类型，stable:稳定版本;latest:最新版本;
     * @return  array
     */
    public function getClientVersionInfo($type)
    {
        $ret = Vredis::hgetall("vrclient", "version_".$type);
        Vredis::close();
        return $ret;
    }

    /**
     * 删除某个客户端版本信息
     * @param   string  type    版本类型，stable:稳定版本;latest:最新版本;
     * @return  array
     */
    public function delClientVersionInfo($type)
    {
        $ret = Vredis::del("vrclient", "version_".$type);
        Vredis::close();
        return $ret;
    }

    /**
     * 获取最新版本的推送数量
     * @param   string  version  版本
     * @return  int
     */
    public function getLatestPushNum($version)
    {
        if(!$version) {
            return false;
        }
        $ret = Vredis::scard("vrclient", "push_num_".$version);
        Vredis::close();
        return $ret;
    }

    /**
     * 当前设备是否推送过该版本
     * 读的时候设置过期时间3个月，如果3个月都没有读了，说明这个已经不是最新版本，就可以过期了
     * @param   string  version  版本
     * @param   string  did      设备号
     * @return  bool
     */
    public function isLatestPushed($version, $did)
    {
        if(!$version || !$did) {
            return false;
        }
        $ret = Vredis::sismember("vrclient", "push_num_".$version, $did);
        if($this->clientPushListExpire > 0) {
            Vredis::expire("vrclient", "push_num_".$version, $this->clientPushListExpire);
        }
        Vredis::close();
        return $ret;
    }

    /**
     * 增加推送记录
     * @param   string  version  版本
     * @param   string  did      设备号
     * @return  bool
     */
    public function addLatestPushLog($version, $did)
    {
        if(!$version || !$did) {
            return false;
        }
        $ret = Vredis::sadd("vrclient", "push_num_".$version, $did);
        if($this->clientPushListExpire > 0) {
            Vredis::expire("vrclient", "push_num_".$version, $this->clientPushListExpire);
        }
        Vredis::close();
        if($ret === false) {
            return false;
        }
        return true;
    }

    /**
     * 获取在线安装客户端版本号
     * @return  string
     */
    public function getOnlinePreVersion()
    {
        $info = Vredis::hgetall("vrclient", "install_tool");
        Vredis::close();
        return $info;
    }

    /**
     * 设置在线安装客户端版本号
     * @param   json  包含版本号version、大小size
     * @return  bool
     */
    public function setOnlinePreVersion($json)
    {
        if(!$json) {
            return false;
        }
        $ret = Vredis::hmset("vrclient", "install_tool", $json, 0);
        Vredis::close();
        return $ret;
    }

    /*
      +-----------------------------------------------------------------------------+
      |                                                                             |
      |                         数 量 统 计 缓 存 (hash)                            |
      |                                                                             |
      +-----------------------------------------------------------------------------+
    */

    /**
     * 数量统计缓存，页面浏览数、评论数量、点赞数量，获取
     * @param   string  type    类型，新闻、游戏、视频等
     * @param   string  targetid    目标id，新闻id、游戏id、视频id等
     * @param   array   fields    字段列表，和数据库的字段对应
     * @return  array
     */
    public function getCounts($type, $targetid, $fields)
    {
        if(!$type || !$targetid || !$fields) {
            return false;
        }
        $key = $type.":".$targetid;
        $ret = Vredis::hmget("whole_counter", $key, $fields);
        Vredis::close();
        return $ret;
    }

    /**
     * 数量统计缓存，页面浏览数、评论数量、点赞数量，写入
     * @param   string  type    类型，新闻、游戏、视频等
     * @param   string  targetid    目标id，新闻id、游戏id、视频id等
     * @param   array   items    字段-值数组
     * @return  bool
     */
    public function setCounts($type, $targetid, $items)
    {
        if(!$type || !$targetid || !$items || !is_array($items)) {
            return false;
        }
        $key = $type.":".$targetid;
        $ret = Vredis::hmset("whole_counter", $key, $items);
        Vredis::close();
        return $ret;
    }

    /**
     * 数量统计缓存，页面浏览数、评论数量、点赞数量，增加
     * @param   string  type    类型，新闻、游戏、视频等
     * @param   string  targetid    目标id，新闻id、游戏id、视频id等
     * @param   string  field    字段，和数据库的字段对应
     * @param   int     num    自增的数量，负为减
     * @return  bool
     */
    public function incCounts($type, $targetid, $field, $num)
    {
        if(!$type || !$targetid || !$field || $num == 0) {
            return false;
        }
        $key = $type.":".$targetid;
        $ret = Vredis::hincrby("whole_counter", $key, $field, $num);
        Vredis::close();
        return $ret;
    }

    /**
     * 数量统计缓存，页面浏览数、评论数量、点赞数量，删除缓存一个字段
     * @param   string  type    类型，新闻、游戏、视频等
     * @param   string  targetid    目标id，新闻id、游戏id、视频id等
     * @param   array   fields    字段列表，和数据库的字段对应
     * @return  bool
     */
    public function delCounts($type, $targetid, $fields)
    {
        if(!$type || !$targetid || !$fields) {
            return false;
        }
        $key = $type.":".$targetid;
        $ret = Vredis::hmset("whole_counter", $key, $fields);
        Vredis::close();
        return $ret;
    }

    /**
     * 数量统计缓存，页面浏览数、评论数量、点赞数量，删除整个缓存
     * @param   string  type    类型，新闻、游戏、视频等
     * @param   string  targetid    目标id，新闻id、游戏id、视频id等
     * @return  bool
     */
    public function delWholeCounts($type, $targetid)
    {
        if(!$type || !$targetid) {
            return false;
        }
        $key = $type.":".$targetid;
        $ret = Vredis::del("whole_counter", $key);
        Vredis::close();
        return $ret;
    }

    /**
     * 数量统计缓存，页面浏览数、评论数量、点赞数量，判断某个统计的缓存是否存在
     * @param   string  type    类型，新闻、游戏、视频等
     * @param   string  targetid    目标id，新闻id、游戏id、视频id等
     * @return  bool
     */
    public function countExists($type, $targetid, $field)
    {
        if(!$type || !$targetid || !$field) {
            return false;
        }
        $key = $type.":".$targetid;
        $ret = Vredis::hexists("whole_counter", $key, $field);
        Vredis::close();
        return $ret;
    }

}
