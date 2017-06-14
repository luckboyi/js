<?php
namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\OpenModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:json", ['only' => ["imgCosAppSign", "netCenterSign", "uploadPrivate", "imagePrivate"]]);
        $this->middleware("vrauth:jump:admincp", ['only' => ["adminCosAppSign"]]);
    }

    public function adminCosAppSign(Request $request)
    {
        $tp     = $request->input('tp');
        $assign = $request->input('assign');
        $id     = $request->input('id');
        $sha1   = $request->input('sha1');
        $size   = $request->input('size');
        $name   = $request->input('name');
        $add    = [];
        if ($id) {
            $add['id'] = $id;
        }
        if ($assign) {
            $add['assign'] = $assign;
        }
        if ($sha1) {
            $add['sha1'] = $sha1;
        }
        if ($size) {
            $add['size'] = $size;
        }
        if ($name) {
            $add['name'] = $name;
        }
        return $this->cosSign($tp, $add);
    }

    public function imgCosAppSign(Request $request)
    {
        $userInfo = $request->userinfo;
        $tp       = $request->input('tp');
        $assign   = $request->input('assign');
        $sha1     = $request->input('sha1');
        $size     = $request->input('size');
        $name     = $request->input('name');
        $appid    = $request->input('appid');
        $add      = [];

        if ($appid && in_array($tp, ['openapp', 'vrgameimg', 'webgameimg'])) {
            $add['id'] = $appid;
        } else {
            $add['id'] = $userInfo['uid'];
        }

        if ($assign) {
            $add['assign'] = $assign;
        }
        if ($sha1) {
            $add['sha1'] = $sha1;
        }
        if ($size) {
            $add['size'] = $size;
        }
        if ($name) {
            $add['name'] = $name;
        }
        return $this->cosSign($tp, $add);
    }

    public function netCenterSign(Request $request)
    {
        $key       = $request->input('name');
        $overwrite = $request->input('overwrite');
        $policy    = [
            'scope'     => 'vronline-video:' . $key,
            'deadline'  => round(1000 * (microtime(true) + 3600)),
            'overwrite' => $overwrite,
        ];

        $ppString = json_encode($policy);
        $ppString = Library::base64Urlsafeencode($ppString);

        $ak    = "c91f234f4050e303f74c30e0d056f11f21241d93";
        $sk    = "bedcf5564aeb498fa87a7bc7c9d5acab54a0dd70";
        $sign  = hash_hmac('sha1', $ppString, $sk, false);
        $token = $ak . ':' . Library::base64Urlsafeencode($sign) . ':' . $ppString;

        return Library::output(0, ['token' => $token]);
    }

    public function uploadPrivate(Request $request)
    {
        $userInfo = $request->userinfo;
        $tp       = $request->input('tp');
        $appid    = $request->input('appid');
        $assign   = $request->input('assign');
        $ext      = [];
        if ($tp == "openapp") {
            $ext['id'] = $appid;
        } else {
            $ext['id'] = $userInfo['uid'];
        }
        $uploadInfo = ImageHelper::getUploadInfo($tp, $ext);

        $fileAllowExts = [
            "image/png"  => "png",
            "image/jpeg" => "jpg",
            "image/gif"  => "gif",
        ];

        $fileAllowSize = 2 * 1024 * 1024; //限制最大尺寸是2MB
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $fileName    = $_FILES['file']['name'];
            $fileError   = $_FILES['file']['error'];
            $fileType    = $_FILES['file']['type'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $fileSize    = $_FILES['file']['size'];
            $fileExt     = isset($fileAllowExts[$fileType]) ? $fileAllowExts[$fileType] : "";
            if (!$fileExt) {
                $code       = 2206;
                $out['msg'] = "该文件扩展名不允许上传";
            }
            switch ($fileError) {
                case 0:
                    $code       = 0;
                    $out['msg'] = "文件上传成功!";
                    break;
                case 1:
                    $code       = 2202;
                    $out['msg'] = "文件上传失败，文件大小" . $fileSize . "超过限制,允许上传大小2M";
                    break;
                case 3:
                    $code       = 2203;
                    $out['msg'] = "上传失败，文件只有部份上传!";
                    break;
                case 4:
                    $code       = 2204;
                    $out['msg'] = "上传失败，文件没有被上传!";
                    break;
                case 5:
                    $code       = 2205;
                    $out['msg'] = "文件上传失败，文件大小为0!";
                    break;
            }
            if ($fileSize > $fileAllowSize) {
                $code       = 2202;
                $out['msg'] = "文件大小超过限制,只能上传2M的文件!";
            }
            if ($code !== 0) {
                return Library::output($code, $out);
            }

            if ($assign) {
                $fileNewName = $assign;
            } else {
                $fileNewName = $this->randFileName() . "." . $fileExt;
            }
            $res = ImageHelper::cosUploadFile($fileTmpName, "/" . $uploadInfo['path'] . "/" . $fileNewName, true);
            if (!isset($res['data'])) {
                return Library::output(1);
            } else {
                ImageHelper::cosUpdatePrivate($res['data']['resource_path']);
                $url = Library::base64Urlsafeencode($res['data']['resource_path']);
                return Library::output(0, ['private' => 'https://open.vronline.com/private/' . $url]);
            }
        }
    }

    public function imagePrivate(Request $request, $path)
    {
        $path = Library::base64Urlsafedecode($path);
        $res  = ImageHelper::cosGetPrivateFile($path);

        if ($res) {
            $tmp = explode(".", $path);
            if (count($tmp) < 2) {
                $headerStr = strtolower(substr($res, 0, 10));
                if (strstr($headerStr, 'png')) {
                    $ext = "png";
                }
                if (strstr($headerStr, 'jfif')) {
                    $ext = "jpg";
                }
                if (!isset($ext)) {
                    return "";
                }
            } else {
                $ext = $tmp[count($tmp) - 1];
            }

            $exts = ["png" => "image/png",
                "jpg"          => "image/jpeg",
                "gif"          => "image/gif"];
            if (!isset($exts[$ext])) {
                return "";
            }
            return response()->make($res, '200')->header('Content-Type', $exts[$ext]);
        } else {
            return "";
        }
    }

    private function cosSign($tp, $add = [])
    {
        $ext = [];
        if (isset($add['id'])) {
            $ext['id'] = $add['id'];
        }
        $uploadInfo = ImageHelper::getUploadInfo($tp, $ext);
        if (isset($add['assign'])) {
            $fileName = $uploadInfo['path'] . $add['assign'];
        } else {
            $fileName = $uploadInfo['path'] . $add['name'];
        }
        $expired = $uploadInfo['expired'];
        $once    = $uploadInfo['once'];
        if ($uploadInfo['storage'] == "cosimg") {
            $serverCfg = Config::get("server.cosimg");
            $bucket    = $serverCfg['bucket'];
            $sign      = ImageHelper::imgSignBase($fileName, $expired, $once);
            if (isset($add['sha1']) && isset($add['size'])) {
                ImageHelper::saveFile($fileName, $add['sha1'], $add['size']);
            }
        } else {
            return json_encode(array('code' => '1', 'message' => '失败'));
        }

        $json = array('code' => '0', 'message' => '成功', 'data' => array('sign' => $sign, 'remote' => $fileName, 'bucket' => $bucket));
        return json_encode($json);
    }

    private function randFileName()
    {
        return substr(md5(md5(time() . mt_rand(11111, 99999))), 0, 16);
    }
}
