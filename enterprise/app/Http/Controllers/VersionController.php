<?php
namespace App\Http\Controllers;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use Config;
use Helper\Library;
use Illuminate\Http\Request;
use \App\Models\DeveloperModel;
use \App\Models\VersionModel;

class VersionController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:json:dev", ['only' => ["versionInfo", "addVersion", "addSubVersion", "completeSubVersion", "chooseSubVersion", "publishVersion", "getUploadToken"]]);
    }

    /**
     * 获取版本信息
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function versionInfo(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $appid    = $request->input('appid');
        $devModel = new DeveloperModel;
        $appInfo  = $devModel->getGameById($appid);
        if ($appInfo['uid'] != $uid) {
            return Library::output(1);
        }

        $publishVersion = "";
        $devVersion     = "";
        $files          = [];
        $versionModel   = new VersionModel;
        $versions       = $versionModel->getVersions($appid);
        if (!empty($versions)) {
            foreach ($versions as $version) {
                if ($version['stat'] == 1) {
                    $vfiles         = $versionModel->getVersionFile($version['version_id']);
                    $files          = $this->meregeFiles($files, $version['version_id'], $vfiles);
                    $publishVersion = $version['version_name'];
                } else {
                    $devVersion = $version['version_name'];
                    break;
                }
            }
        }

        if ($devVersion) {
            $versionId = $versionModel->getLastVersioniId($appid, $devVersion);
        } else {
            $versionId = 0;
        }

        $data = ['publish' => $publishVersion, 'dev' => $devVersion, 'version_id' => $versionId, 'files' => $files];
        return Library::output(0, $data);
    }

    /**
     * [addVersion 添加dev版本]
     *  @param [int] $[appid] [<游戏ID>]
     *  @param [string] $[version_name] [<游戏名称>]
     *  @param [string] $[version_desc] [<游戏描述>]
     */
    public function addVersion(Request $request)
    {
        $userInfo    = $request->userinfo;
        $uid         = $userInfo['uid'];
        $appid       = $request->input('appid');
        $versionName = $request->input('version_name');
        $versionDesc = $request->input('version_desc');

        $devModel = new DeveloperModel;
        $appInfo  = $devModel->getGameById($appid);
        if ($appInfo['uid'] != $uid) {
            return Library::output(1);
        }

        $versionModel = new VersionModel;
        $versions     = $versionModel->getVersions($appid, ['stat' => 0]);
        if ($versions) {
            return Library::output(3302);
        }
        $versions = $versionModel->getVersions($appid, ['version_name' => $versionName]);
        if ($versions) {
            return Library::output(3301);
        }
        $ret = $versionModel->addVersion($appid, ['version_name' => $versionName, 'version_desc' => $versionDesc]);
        if (!$ret) {
            return Library::output(1);
        } else {
            return Library::output(0);
        }
    }

    /**
     * 添加sub版本
     * @param [int] $[appid] [<游戏ID>]
     * @param [string] $[version_name] [<版本号>]
     * @param [json] $[name] [<文件信息>]
     */
    public function addSubVersion(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];

        $appid       = $request->input('appid');
        $versionName = $request->input('version_name');
        $files       = $request->input('files');
        $size        = $request->input('size');

        $fileArr = json_decode($files, true);
        if (!$fileArr) {
            return Library::output(3304);
        }

        $devModel = new DeveloperModel;
        $appInfo  = $devModel->getGameById($appid);
        if ($appInfo['uid'] != $uid) {
            return Library::output(1);
        }
        $versionModel = new VersionModel;
        $versions     = $versionModel->getVersions($appid, ['version_name' => $versionName]);
        if (!$versions) {
            return Library::output(1);
        }

        $versionId = $versionModel->addSubVersion($appid, $versionName, $files);
        if ($versionId) {
            $serverCfg = Config::get("server.cosv4");
            $token     = ImageHelper::appSignBase($serverCfg['appid'], $serverCfg['sid'], $serverCfg['skey'], time() + 86400 * 30, null, $serverCfg['bucket'], false);
            return Library::output(0, ['id' => $versionId, 'token' => $token]);
        } else {
            return Library::output(1);
        }
    }

    public function getUploadToken(Request $request)
    {
        $serverCfg = Config::get("server.cosv4");
        $token     = ImageHelper::appSignBase($serverCfg['appid'], $serverCfg['sid'], $serverCfg['skey'], time() + 86400 * 30, null, $serverCfg['bucket'], false);
        return Library::output(0, ['token' => $token]);
    }

    /**
     * 完成sub版本
     * @param [int] $[version_id] [<sub version id>]
     * @param [int] $[appid] [<游戏ID>]
     */
    public function completeSubVersion(Request $request)
    {
        $userInfo  = $request->userinfo;
        $uid       = $userInfo['uid'];
        $appid     = $request->input('appid');
        $versionId = $request->input('version_id');
        $devModel  = new DeveloperModel;
        $appInfo   = $devModel->getGameById($appid);
        if ($appInfo['uid'] != $uid) {
            return Library::output(1);
        }
        $versionModel = new VersionModel;
        $files        = $versionModel->getVersionFile($versionId);
        if (!$files) {
            return Library::output(1);
        }
        $ret = $versionModel->updateSubVersion($versionId, ['stat' => 1]);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function transcodingBack(Request $request)
    {
        error_log(date("Y-m-d H:i:s") . "\t" . json_encode($_POST) . "|" . json_encode($_GET) . "|" . file_get_contents("php://input"), "/tmp/trans.log");
    }
    /**
     * [meregeFiles 合并版本文件信息]
     * @param  [array] $files    [原文件]
     * @param  [int] $versionId    [版本ID]
     * @param  [array] $addFiles [添加文件]
     * @return [array]           [合并好的文件]
     */
    private function meregeFiles($files, $versionId, $addFiles)
    {
        if (!$addFiles) {
            return $files;
        }
        foreach ($addFiles as $key => $info) {
            if (isset($info['del'])) {
                unset($files[$key]);
            } else {
                $info['version_id'] = $versionId;
                $files[$key]        = $info;
            }

        }
        return $files;
    }
}
