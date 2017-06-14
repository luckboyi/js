<?php
/**
 * Created by PhpStorm.
 * User: kira
 * Date: 2016/9/5
 * Time: 15:44
 */
namespace App\Models;

// 引用Model
use Config;
use \App\Models\VideoModel;

use Illuminate\Database\Eloquent\Model;

class MiddleModel extends Model
{
    /*
    * 获取所有的视频相关信息
    */
    public function getAll()
    {
        $videoModel = new VideoModel();
        $info = $videoModel->getAll();
        return $info;
    }

    /*
     * 获取所有视频相关分类
     */
    public function getVideoSort()
    {
        $videoModel = new VideoModel();
        $sortInfo = $videoModel->getVideoSort();
        return $sortInfo;
    }

    /*
     * 获取所有视频信息
     */
    public function getVideoInfo($data)
    {
        $videoModel = new VideoModel();
        $videoInfo = $videoModel->getVideoInfo($data);
        return $videoInfo;
    }

    /*
     * 获取推荐位查询vid视频信息
     */
    public function getVideoInfoGet($data)
    {
        $videoModel = new VideoModel();
        $videoInfo = $videoModel->getVideoInfoGet($data);
        return $videoInfo;
    }

    /*
     * 获取所有视频广告信息
     */
    public function getVideoAdInfo($data)
    {
        $videoModel = new VideoModel();
        $videoInfo = $videoModel->getVideoAdInfo($data);
        return $videoInfo;
    }


    /*
     *获取推荐位信息
     */
    public function getVideoRecommend($data)
    {
        $videoModel = new VideoModel();
        $videoInfo = $videoModel->getVideoRecommend($data);
        return $videoInfo;
    }

    /*
     * 添加推荐位数据
     */
    public function videoRecommendAdd($data)
    {
        $videoModel = new VideoModel();
        $ret = $videoModel->videoRecommendAdd($data);
        return $ret;
    }

    /*
     * 添加视频分类
     */
    public function addVideoSort($data)
    {
        $videoModel = new VideoModel();
        $ret = $videoModel->addVideoSort($data);
        return $ret;
    }

    /*
     * 添加广告位信息
     */
    public function addVideoAd($data)
    {
        $videoModel = new VideoModel();
        $ret = $videoModel->addVideoAd($data);
        return $ret;
    }

    /*
     * 删除视频分类
     */
    public function videoSortDel($data)
    {
        $videoModel = new VideoModel();
        $ret = $videoModel->videoSortDel($data);
        return $ret;
    }

    /*
     * 删除广告栏
     */
    public function videoAdDel($data)
    {
        $videoModel = new VideoModel();
        $ret = $videoModel->videoAdDel($data);
        return $ret;
    }

    /*
     * 获取视频信息通过vid
     */
    public function getVideoInfoByVid($vid)
    {
        $videoModel = new VideoModel();
        $ret = $videoModel->getVideoInfoById($vid);
        return $ret;
    }

    /*
     * 获取用户的视频历史记录
     */
    public function getVideoHistory($uid, $sort)
    {
        $videoModel = new VideoModel();
        $ret = $videoModel->getVideoHistory($uid, $sort);
        return $ret;
    }

    /**
     * 提供的curlPost方法
     * @param $url
     * @param $param
     * @return string
     */
    public function curlPost($url, $param)
    {
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $param);
        ob_start();
        $return = curl_exec($process);
        $content = ob_get_contents();
        ob_end_clean();
        curl_close($process);
        return $content;
    }


    /**
     * @decodeUnicode
     * @param $str
     * @return mixed
     */
    public function decodeUnicode($str) {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function( '$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");' ), $str);
    }
    /**
     * unicodeDecode
     */
    public function unicodeDecode($string) {
        return json_decode('"'. $string .'"');
    }

    /**
     * -json_encode中文乱码转码函数
     * @param $string
     * @return mixed
     */
    public function jsonEncode($string) {
        return self::decodeUnicode(json_encode($string));
    }


    /**
     * 获取上传图片的大小
     * @param $size
     * @return string
     */
    public function sizeFormat($size)
    {
        $sizeStr='';
        if($size<1024)
        {
            return $size."bytes";
        } else if($size<(1024*1024)) {
            $size=round($size/1024,1);
            return $size."KB";
        } else if($size<(1024*1024*1024)) {
            $size=round($size/(1024*1024),1);
            return $size."MB";
        } else  {
            $size=round($size/(1024*1024*1024),1);
            return $size."GB";
        }
    }
}