<?php

/*
网页游戏相关model
date:2016/9/13
 */

namespace App\Models;

use App\Helper\ImageHelper;
use App\Helper\Vmemcached;
use App\Models\WebgameLogicModel;
use Config;
use Illuminate\Database\Eloquent\Model;
use Mail;

class OpenModel extends Model
{

    /**
     * 获取开发者信息
     * @param   int     uid   uid
     * @return  array
     */
    public function getDevUserInfo($uid)
    {
        $uid = intval($uid);
        if (!$uid) {
            return false;
        }
        $devModel = new DevModel;
        $info     = $devModel->getUser($uid);
        if (!$info) {
            return $info;
        }
        if ($info['pic_version']) {
            $pic                 = ImageHelper::path('openuser', $uid, $info['pic_version']);
            $info['credentials'] = $pic['credentials'];
        }
        return $info;
    }

    /**
     * 获取页游信息
     * @param   int     uid   uid
     * @param   bool    devpic   是否是查看的后台的照片，true:在后台查看后台上传图片; false: 查看线上图片
     * @return  array
     */
    public function getDevWebgameInfo($appid, $devpic)
    {
        $appid = intval($appid);
        if (!$appid) {
            return false;
        }
        $devModel = new DevModel;
        $info     = $devModel->getWebgameInfo($appid);
        if (!$info) {
            return false;
        }
        return $info;
    }

    /**
     * 发送邮件方法
     * @param $email
     * @param $name
     * @param $msgDataArr
     */
    public function sendVerifyMail($email, $name, $msgDataArr)
    {
        $data = ['email' => $email, 'name' => $name, 'uid' => $msgDataArr['uid'], 'title' => $msgDataArr['title'], 'activeCode' => $msgDataArr['activeCode']];
        Mail::queue('open.apply.activeEmail', $data, function ($message) use ($data) {
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

    /**
     * 判断open后台登录权限
     * @param   int  uid     uid
     * @return  string
     */
    public function checkOpenRight($uid)
    {
        $weblogic   = new WebgameLogicModel;
        $cookid_uid = CookieModel::getCookie("uid");
        $token      = CookieModel::getCookie("token");
        if (!$cookid_uid || !$token || $cookid_uid != $uid) {
            return "unlogin";
        }
    }

    /**
     * 审核页游
     * @param   int     uid   uid
     * @param   bool    devpic   是否是查看的后台的照片，true:在后台查看后台上传图片; false: 查看线上图片
     * @return  array
     */
    public function reviewPassApp($game)
    {
        if (!$game || !is_array($game)) {
            return false;
        }
        $appid     = $game['appid'];
        $gameType  = $game['game_type'];
        $rsync_ret = ImageHelper::cosCopyFiles($game);
        // if (!$rsync_ret) {
        //     return "error:rsync_pic";
        // }
        $upinfo                     = array();
        $upinfo['uid']              = $game['uid'];
        $upinfo['name']             = $game['name'];
        $upinfo['letter1']          = $game['letter1'];
        $upinfo['first_class']      = $game['first_class'];
        $upinfo['second_class']     = $game['second_class'];
        $upinfo['send_time']        = $game['send_time'];
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
        $webModel                   = new WebgameModel;
        $isnew                      = false;
        $ret                        = $webModel->setGameInfo($appid, $upinfo, $isnew);
        if ($ret === false) {
            return "error:rsync_data";
        }
        return true;
    }

}
