<?php
namespace App\Models;

use Config;
use DB;
use Illuminate\Database\Eloquent\Model;

class VersionModel extends Model
{
    public function getVersions($appid, $ext = [], $page = 0)
    {
        if (!$appid) {
            return false;
        }
        $ret = DB::connection("db_dev")->table("t_vrgame_version")->where(['appid' => $appid]);
        if ($ext) {
            $ret->where($ext);
        }
        if ($page) {
            $row = $ret->orderBy('ctime', 'asc')->paginate($page);
        } else {
            $row = $ret->orderBy('ctime', 'asc')->get();
        }

        return $row;
    }

    public function getVersionFile($versionId)
    {
        if (!$versionId) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_vrgame_sub_version")->where(['id' => $versionId])->first();
        if (!$row) {
            return false;
        }
        return json_decode($row['files'], true);
    }

    public function getSubVersions($appid, $versionName)
    {
        if (!$appid || !$versionName) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_vrgame_sub_version")->where(['appid' => $appid, 'version_name' => $versionName, 'stat' => 1])->orderBy("ltime", "desc")->get();
        return $row;
    }

    public function getLastVersioniId($appid, $versionName)
    {
        if (!$appid || !$versionName) {
            return false;
        }
        $row = DB::connection("db_dev")->table("t_vrgame_sub_version")->where(['appid' => $appid, 'version_name' => $versionName, 'stat' => 0])->orderBy("id", "desc")->first();
        if (!$row) {
            return false;
        } else {
            return $row['id'];
        }
    }

    public function addVersion($appid, $info)
    {
        if (!$appid || empty($info)) {
            return false;
        }
        $info['appid'] = $appid;
        $ret           = DB::connection("db_dev")->table("t_vrgame_version")->insert($info);
        return $ret;
    }

    public function addSubVersion($appid, $versionName, $files)
    {
        if (!$appid || !$versionName || !$files) {
            return false;
        }
        $lastVersion = DB::connection("db_dev")->table("t_vrgame_sub_version")->where(['appid' => $appid, 'version_name' => $versionName, 'stat' => 0])->orderBy("id", "desc")->first();
        if ($lastVersion) {
            if ($files != $lastVersion['files']) {
                $ret = DB::connection("db_dev")->table("t_vrgame_sub_version")->where(['id' => $lastVersion['id']])->update(['files' => $files]);
                if ($ret) {
                    return $lastVersion['id'];
                } else {
                    return false;
                }
            } else {
                return $lastVersion['id'];
            }
        } else {
            $info  = ['appid' => $appid, 'version_name' => $versionName, 'files' => $files];
            $retId = DB::connection("db_dev")->table("t_vrgame_sub_version")->insertGetId($info);
            return $retId;
        }
    }

    public function updateSubVersion($versionId, $up)
    {
        if (!$versionId || empty($up)) {
            return false;
        }
        $ret = DB::connection("db_dev")->table("t_vrgame_sub_version")->where(['id' => $versionId])->update($up);
        return $ret;
    }

    public function updateVersion($appid, $versionName, $up)
    {
        if (!$appid || !$versionName || empty($up)) {
            return false;
        }
        $ret = DB::connection("db_dev")->table("t_vrgame_version")->where(['appid' => $appid, 'version_name' => $versionName])->update($up);
        return $ret;
    }

    public function chooseSubVersion($appid, $versionName, $versionId)
    {
        if (!$appid || !$versionName || !$versionId) {
            return false;
        }
        $versions = $this->getVersions($appid, ['version_name' => $versionName]);
        if (!$versions) {

            return false;
        }
        $files = $this->getVersionFile($versionId);
        if (!$files) {
            return false;
        }
        $ret = $this->updateVersion($appid, $versionName, ['version_id' => $versionId]);
        if (!$ret) {
            return false;
        } else {
            return true;
        }
    }

    public function delVersion($appid, $versionName)
    {
        if (!$appid || !$versionName) {
            return false;
        }
        $ret  = DB::connection("db_dev")->table("t_vrgame_version")->where(['appid' => $appid, 'version_name' => $versionName])->delete();
        $ret1 = DB::connection("db_dev")->table("t_vrgame_sub_version")->where(['appid' => $appid, 'version_name' => $versionName])->delete();
        return $ret;
    }
    public function publishVersion($appid, $versionName)
    {
        if (!$appid || !$versionName) {
            return false;
        }
        $versions = $this->getVersions($appid, ['version_name' => $versionName]);
        if (!$versions) {
            return false;
        }
        $version = $versions[0];
        if (!$version['version_id']) {
            return false;
        }
        $ret = $this->updateVersion($appid, $versionName, ['stat' => 1]);
        if (!$ret) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取下载版本信息
     * @param [int] $[appid] [<游戏ID>]
     * @param [int] $[test] [<是否测试>]
     */
    public function downGame($appid, $isTest = false)
    {
        $publishVersion = "";
        $devVersion     = "";
        $files          = [];
        $exe            = "";
        $versions       = $this->getVersions($appid);
        if (!empty($versions)) {
            foreach ($versions as $version) {
                if ($version['stat'] == 1) {
                    $vfiles         = $this->getVersionFile($version['version_id']);
                    $files          = $this->meregeFiles($files, $version['version_id'], $vfiles);
                    $publishVersion = $version['version_name'];
                    $exe            = $version['version_start_exe'];
                } else {
                    $devVersion = $version['version_name'];
                    if ($isTest == true && isset($version['version_id'])) {
                        $files = $this->meregeFiles($files, $version['version_id'], $vfiles);
                        $exe   = $version['version_start_exe'];
                    }

                    break;
                }
            }
        }

        $data = ['publish' => $publishVersion, 'dev' => $devVersion, 'files' => $files, 'exe' => $exe];
        return $data;
    }

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
