<?php
/**
 * 检测更新
 */
namespace App\Models;

use Config;
use App\Models\CacheModel;
use Illuminate\Database\Eloquent\Model;

class CheckUpdateModel extends Model
{
    /**
     * 计算下载地址
     */
    public function getCliendAddress($version, $type="")
    {
        if(!$version) {
            return false;
        }
        $base = "http://down.client.vronline.com/client";
        $data = array();
        $data['client'] = "{$base}/VRonline/{$version}/VRassistant_FullInstaller_{$version}.exe";
        $data['online'] = "{$base}/VRonline/{$version}/VRassistant_OnlineInstaller_{$version}.zip";
        $data['update'] = "{$base}/VRonline/{$version}/update/VerCompClient.dat";
        $data['onlinepre'] = "{$base}/VRonline_Installer/VRassistant_OnlineInstaller_{$version}.exe";
        if($type) {
            return $data[$type];
        }
        return $data;
    }

    /**
     * 下载客户端，或者下载在线包
     * 只有稳定版本
     */
    public function clientDown($type)
    {
        $cacheModel = new CacheModel;

        $stable  = $cacheModel->getClientVersionInfo("stable");
        if(!$stable || !isset($stable['version']) || !$stable['version']) {
            return false;
        }
        return $this->getCliendAddress($stable['version'], $type);
    }

    /**
     * 下载客户端，或者下载在线包
     * 只有稳定版本
     */
    public function latestClientDown()
    {
        $cacheModel = new CacheModel;

        $stable  = $cacheModel->getClientVersionInfo("latest");
        if(!$stable || !isset($stable['version']) || !$stable['version']) {
            return false;
        }
        $down = $this->getCliendAddress($stable['version'], "");
        return ["info" => $stable, "download" => $down];
    }

    /**
     * 判断客户端是否需要升级
     * 有did的都优先下载最新版本包，没有did的只下载稳定版本
     * @param   string  oldVersion  用户当前版本号，如果是新安装，传""
     * @param   string  did         客户端设备号
     * @param   string  type        类型，update:更新;online:在线包下载;client:完整包下载
     * @return  array   升级信息    false:不需要更新; version:最新版本号;release_note:升级说明;address:下载地址;isforce:是否强制升级;
     */
    public function clientCheckUpdate($oldVersion, $did, $type)
    {
        $cacheModel = new CacheModel;

        /**
         * 必须有设备号，才能装最新版本，否则没办法定量推送
         */
        if($did) {
            /**
             * 先判断最新版本
             * 是最新版本，不升级
             */
            $latest  = $cacheModel->getClientVersionInfo("latest");
            if(isset($latest['version']) && $latest['version'] == $oldVersion) {
                return false;
            }

            /**
             * 如果有最新版本，并且需要推送的数量大于0
             * 推送数量为0或没有版本号，视为没有最新版本
             */
            if($latest && isset($latest['version']) && $latest['version'] && isset($latest['pushnum']) && $latest['pushnum'] > 0)
            {
                $url = $this->getCliendAddress($latest['version'], $type);
                $latest['address'] = $url;
                $latest['type'] = "latest";
                /**
                 * 如果不是最新版本
                 * 判断是否推送过新版本
                 * 如果推送过新版本，还返回新版本的链接
                 */
                $isPushed = $cacheModel->isLatestPushed($latest['version'], $did);
                if($isPushed) {
                    return $latest;
                }

                /**
                 * 如果没推送过这个用户
                 * 并且新版本还有推送名额
                 * 给这个用户推送
                 */
                $num = $cacheModel->getLatestPushNum($latest['version']);
                if(!$num) {
                    $num = 0;
                }
                if($latest['pushnum'] > $num) {
                    $cacheModel->addLatestPushLog($latest['version'], $did);
                    return $latest;
                }
            }

        }
        /**
         * 如果没有最新版本
         * 或者最新版本没有给这个用户推送，并且推送名额满了
         * 就判断稳定版本是否可升级
         * 没有稳定版本信息，或和用户版本一致，不升级
         */
        $stable  = $cacheModel->getClientVersionInfo("stable");
        if(!$stable || !isset($stable['version']) || !$stable['version'] || isset($stable['version']) && $stable['version'] == $oldVersion) {
            return false;
        }
        $url = $this->getCliendAddress($stable['version'], $type);
        $stable['address'] = $url;
        $stable['type'] = "stable";
        return $stable;
    }

    /**
     * 在线安转包预下载的客户端下载地址
     * @param   string  did         客户端设备号
     * @param   string  type        类型，update:更新;online:在线包下载;client:完整包下载
     * @return  array   升级信息    false:不需要更新; version:最新版本号;release_note:升级说明;address:下载地址;isforce:是否强制升级;
     */
    public function clientOnlinepre()
    {
        $cacheModel = new CacheModel;

        $info = $cacheModel->getOnlinePreVersion();
        if(!isset($info['version'])) {
            $info['version'] = 0;
        }
        $url = $this->getCliendAddress($info['version'], "onlinepre");
        $info['address'] = $url;
        $info['type'] = "onlinepre";
        return ['info' => $info, 'url' => $url];
    }



    /**
     * 计算下载地址
     */
    public function oldClient($type)
    {
        $info = Config::get("vrclient.latest_client.{$type}");
        return $info;
    }

}