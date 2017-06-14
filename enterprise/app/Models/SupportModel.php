<?php

/*
 * 点赞列表、玩游戏用户列表
 * 点赞分顶和踩两个列表
 * 玩游戏用户列表，不需要判断是否玩过，只管往里写就行
 * date:2016/8/22
 */

namespace App\Models;

use App\Helper\Vredis;
use Illuminate\Database\Eloquent\Model;

class SupportModel extends Model
{
    /**
     * 点赞操作类型，up:支持;down:不支持;
     */
    public $support_action = ["up", "down"];

    /**
     * 支持的所有操作
     * up 点赞中的顶
     * down 点赞中的踩
     * play 玩游戏、播放视频，一般用作页游，需要登录
     */
    public $actionlist = ["up", "down", "play"];

    /**
     * 增加列表
     * 保证 type+itemid+action是唯一的
     * @param   int     uid     用户id
     * @param   int     itemid  目标id，比如页游id/视频id/新闻id/评论id等
     * @param   string  type    目标类型, webgame/vrgame/video/comment/news_game/news_video/news_article ，用来区分itemid，是视频，还是页游，还是vr游戏，还是评论
     * @param   string  action  操作类型，up:支持;down:不支持;play:玩游戏用户列表
     */
	public function add($uid, $itemid, $type, $action = 'up')
    {
        if(!$uid || !$itemid || !$type || !$action || !in_array($action, $this->actionlist)) {
            return false;
        }
        $ret = Vredis::sadd("support", $type."_".$action.'_'.$itemid, $uid);
        Vredis::close();
        return $ret;
	}

    /**
     * 增加列表
     * @param   int     uid     用户id
     * @param   int     itemid  目标id，比如页游id，视频id等
     * @param   string  type    目标类型, webgame/vrgame/video/comment ，用来区分itemid，保证 type+itemid是唯一的
     * @param   string  action  操作类型，up:支持;down:不支持;
     */
	public function support($uid, $itemid, $type, $action = 'up')
    {
        return $this->add($uid, $itemid, $type, $action);
	}

	/**
	 * 获取数量
	 */
	public function getCount($uid, $itemid, $type, $action)
    {
        if(!$uid || !$itemid || !$type) {
            return false;
        }
        $num = Vredis::scard("support", $type."_".$action.'_'.$itemid, $uid);
        Vredis::close();
        return $num;
	}

	/**
	 * 判断是否存在
	 */
	public function isExists($uid, $itemid, $type, $action)
    {
        if(!$uid || !$itemid || !$type) {
            return false;
        }
        $ret = Vredis::sismember("support", $type."_".$action.'_'.$itemid, $uid);
        Vredis::close();
        return $ret;
	}

	/**
	 * 获取点赞的数量
     * 专门用于点赞，因为是分顶和踩2个
	 */
	public function getSupportNum($uid, $itemid, $type)
    {
        if(!$uid || !$itemid || !$type) {
            return false;
        }
        $arr = array();
        for($i = 0; $i < count($this->actionlist); $i++) {
            $act = $this->actionlist[$i];
            $num = Vredis::scard("support", $type."_".$act.'_'.$itemid, $uid);
            if(!$num) {
                $num = 0;
            }
            $arr[$act] = $num;
        }
        Vredis::close();
        return $arr;
	}

	/**
	 * 是否点过，并获得支持类型
	 */
	public function isSupported($uid, $itemid, $type)
    {
        if(!$uid || !$itemid || !$type) {
            return false;
        }
        for($i = 0; $i < count($this->support_action); $i++) {
            $act = $this->support_action[$i];
             $ret = Vredis::sismember("support", $type."_".$act.'_'.$itemid, $uid);
            if($ret) {
                Vredis::close();
                return $act;
            }
        }
        Vredis::close();
        return null;
	}

}
