<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CheckUpdateModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class CheckUpdateController extends Controller
{
    /**
     * 检测客户端是否有更新，如果有更新，返回更新信息
     * 如果有did，才推送新版本，否则只判断文档版本
     * @param   string  version     当前已安装的客户端版本号
     * @param   string  did         设备号
     */
    public function client(Request $request, $type)
    {
        $version          = $request->input('version', "");
        $did              = $request->input('did', "");
        $checkUpdateModel = new CheckUpdateModel;
        $info             = $checkUpdateModel->clientCheckUpdate($version, $did, $type);
        if (!$info || !is_array($info) || !isset($info['version']) || !$info['version']) {
            return Library::output(2801);
        }
        $data               = array();
        $data['version']    = $info['version'];
        $data['address']    = $info['address'];
        $data['newfeature'] = $info['newfeature'];
        $data['size']       = $info['whole_size'];
        $data['updtype']    = $info['updtype'];
        $data['type']       = $info['type'];

        return Library::output(0, $data);
    }

    public function oculusProxy(Request $request)
    {
        $time   = dechex(time());
        $expire = dechex(time() + 60);
        $sign   = md5("s7forfirej8l" . $time . "kn" . $expire);

        $arr          = [];
        $arr['hosts'] = ["119.28.64.14 graph.oculus.com", "119.28.64.14 secure.oculus.com", "119.28.64.14 auth.oculus.com", "119.28.64.14 www.oculus.com", "119.28.64.14 securecdn.oculus.com", "119.28.16.127 scontent.oculuscdn.com", "119.28.71.162 static.xx.fbcdn.net"];
        $arr['ips']   = ["119.28.64.14", "119.28.16.127", "119.28.71.162"];
        $arr['sign']  = $sign . '-' . $time . "-" . $expire;
        return Library::output(0, $arr);
    }

    public function deviceDrives(Request $request)
    {
        return json_encode(Config::get('device_drives'), JSON_UNESCAPED_UNICODE);
    }
    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             第 一 个 版 本                                                  |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */
    /**
     * 检测客户端是否有更新
     * 下载客户端
     */
    public function clientTemp()
    {
        header("Content-Type:text/xml; charset=UTF-8");
        header('Content-Disposition: attachment; filename="VerCompClient.dat"');
        $path = public_path();
        $file = Config::get("oldclient.client_xml_update_file");
        $xml  = $path . $file;
        if (file_exists($xml)) {
            $cont = file_get_contents($xml);
            return $cont;
        }
        return "";
    }

    /**
     * 在线更新接口
     */
    public function clientTempOnlineUpd(Request $request)
    {
        $version = Config::get("oldclient.version");
        $down    = Config::get("oldclient.down");
        return json_encode(array("version" => $version, "down" => $down));
    }
}
