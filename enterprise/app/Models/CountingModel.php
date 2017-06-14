<?php

/*
 * 统计数量
 * 不去重
 * 使用方法：
 *      type: 统计的目标类型，vrgame/video/comment/webgame等，可以自己添加
 *      action: 统计的操作类型，play:视频播放次数/游戏下载次数
 *      itemid: 统计的项目ID，不同类型，ID可能有重复，所以type+action+itemid一定保证是唯一的
 *      一定要配置isSource，是否有数据源，对应的是 type+action，如果有数据源，在add的时候如果返回false，要先从数据库中读出来，然后通过set方法初始化缓存
 * date:2016/8/22
 */

namespace App\Models;

use App\Helper\Vredis;
use Illuminate\Database\Eloquent\Model;

class CountingModel extends Model
{
    /**
     * type+action对应的数据，是否有数据源
     * 如果有数据源，当缓存的field不存在时，不能自增，需要读数据源初始化缓存
     * 如果没有数据源，缓存不存在该field，先创建成0，再自增
     * 每个type+action都必须配置，true是有数据源，false是无数据源
     * 二维数组，[type=>[action=>true], type=>[action=>false]]
     */
    private $isSource = [
                            "video"     => ["play" => true],        // 视频的播放次数有数据源，当key不存在时，必须先读入数据初始化
                            "webgame"   => ["play" => true],        // vr游戏的下载次数有数据源，当key不存在时，必须先读入数据初始化
                        ];

    /**
     * 增加次数
     * 如果field不存在，增加失败，需要先读数据库，写入缓存
     * @param   int     itemid  目标id，比如页游id，视频id等
     * @param   string  type    目标类型, webgame/vrgame/video/comment ，用来区分itemid，保证 type+itemid是唯一的
     * @param   string  action  操作类型，play:视频播放次数/游戏下载次数;
     * @param   int     count   增加的次数
     * @return  int     增加后的数量，field不存在，或失败，返回false
     */
	public function add($itemid, $type, $action, $count = 1)
    {
        if(!$itemid || !$type || !isset($this->isSource[$type][$action])) {
            return false;
        }
        $suffix = $type."_".$action;
        if($this->isSource[$type][$action]) {
            $exists = Vredis::hexists("counting", $suffix, $itemid);
            if(!$exists) {
                return false;
            }
        }
        $ret = Vredis::hincrby("counting", $suffix, $itemid, $count);
        Vredis::close();
        return $ret;
	}

	/**
	 * 设置field值
     * 如果有数据源，从数据源读到结果，设置到redis里
	 */
	public function set($itemid, $type, $action, $value)
    {
        if(!$itemid || !$type || !is_numeric($value)) {
            return false;
        }
        $ret = Vredis::hset("counting", $type."_".$action, $itemid, $value);
        Vredis::close();
        return $ret;
	}

	/**
	 * 获取一个id的总次数
	 */
	public function get($itemid, $type, $action)
    {
        if(!$itemid || !$type) {
            return false;
        }
        $ret = Vredis::hget("counting", $type."_".$action, $itemid);
        Vredis::close();
        return $ret;
	}

	/**
	 * 获取多个id的总次数
     * 返回数组 index为itemid
	 */
	public function mget($itemids, $type, $action)
    {
        if(!$itemids || !$type) {
            return false;
        }
        $ret = Vredis::hmget("counting", $type."_".$action, $itemids);
        Vredis::close();
        return $ret;
	}

}
