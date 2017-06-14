<?php

namespace App\Helper;

use App\Helper\Vredis;
use Config;
use Helper\HttpRequest;
use Helper\Library;

class ImageHelper
{
    const DOMAIN_PRIVATE = "https://open.vronline.com/private/";
    public static function getUploadInfo($tp, $ext = [])
    {
        $res = [];
        switch ($tp) {
            case 'vrgameimg':
            case 'webgameimg':
            case 'openapp':
            case 'openuser':
            case 'userimg':
                if (!isset($ext['id'])) {
                    return false;
                }
                $id             = $ext['id'];
                $res['devpath'] = $tp . '/dev/' . $id . "/";
                $res['pubpath'] = $tp . '/pub/' . $id . "/";
                if (isset($ext['publish'])) {
                    $res['path'] = $res['pubpath'];
                } else {
                    $res['path'] = $res['devpath'];
                }
                $res['expired'] = time() + 60;
                $res['once']    = 0;
                $res['storage'] = 'cosimg';
                break;
            case 'video':
                $res['expired'] = time() + 60;
                $res['once']    = 0;
                $res['storage'] = 'netcenter';
                break;
            default:
                $res['path']    = $tp . '/';
                $res['expired'] = time() + 60;
                $res['once']    = 0;
                $res['storage'] = 'cosimg';
                break;
        }

        return $res;
    }

    public static function getUrl($tp, $ext = [])
    {
        $res        = [];
        $uploadInfo = self::getUploadInfo($tp, $ext);
        $path       = $uploadInfo['path'];
        switch ($tp) {
            case 'vrgameimg':
            case 'webgameimg':
                if (!isset($ext['version'])) {
                    return false;
                }
                $version = $ext['version'];

                $res['logo'] = $version == 0 ? "" : $path . "logo?{$version}";
                $res['rank'] = $version == 0 ? "" : $path . "rank?{$version}";
                $res['icon'] = $version == 0 ? "" : $path . "icon?{$version}";
                $res['bg']   = $version == 0 ? "" : $path . "bg?{$version}";
                if ($tp == 'webgameimg') {
                    $res['slogo'] = $version == 0 ? "" : $path . "slogo?{$version}";
                    $res['ico']   = $version == 0 ? "" : $path . "ico?{$version}";
                    $res['bg2']   = $version == 0 ? "" : $path . "bg2?{$version}";
                    $res['card']  = $version == 0 ? "" : $path . "card?{$version}";
                }
                $res['slider'] = [];
                if (isset($ext['img_slider']) && strlen($ext['img_slider']) > 2) {
                    $slider = json_decode($ext['img_slider'], true);
                    foreach ($slider as $value) {
                        $res['slider'][] = $path . $value;
                    }
                }
                $res['screenshots'] = [];
                if (isset($ext['img_screenshots']) && strlen($ext['img_screenshots']) > 2) {
                    $screenshots = json_decode($ext['img_screenshots'], true);
                    if (!empty($screenshots)) {
                        foreach ($screenshots as $value) {
                            $res['screenshots'][] = $path . $value;
                        }
                    }
                }
                break;
            case 'openuser':
                if (!isset($ext['version'])) {
                    return false;
                }
                $serverCfg     = Config::get('server.cosv4');
                $version       = $ext['version'];
                $res['idcard'] = $version == 0 ? "" : self::DOMAIN_PRIVATE . base64Urlsafeencode('/' . $serverCfg['appid'] . '/' . $serverCfg['bucket'] . '/' . $path . "idcard");
                break;
            default:
                # code...
                break;
        }
        return $res;
    }

    /**
     * @param   string  type    path:返回路径：url:返回链接
     */
    public static function url($tp, $id, $version = 1, $img_slider = "", $isdev = true, $img_screenshots = "", $type = "path")
    {
        $id = intval($id);
        if (!$id) {
            return false;
        }
        $cfg = Config::get('server.img.' . $tp);
        switch ($tp) {
            case 'userimg':
                $res['face'] = $cfg['url'] . $cfg['dev'] . $id . "/120.png";
                break;
            case 'vrgame':
            case 'webgame':
                if ($isdev) {
                    $base = $cfg['dev'] . $id;
                } else {
                    $base = $cfg['pub'] . $id;
                }
                if ($type == "url") {
                    $base = $cfg['url'] . $base;
                }
                $res['logo']  = $base . "/logo?{$version}";
                $res['slogo'] = $base . "/slogo?{$version}";
                $res['rank']  = $base . "/rank?{$version}";
                $res['icon']  = $base . "/icon?{$version}";
                $res['bg']    = $base . "/bg?{$version}";
                if ($tp == 'webgame') {
                    $res['ico']  = $base . "/ico?{$version}";
                    $res['bg2']  = $base . "/bg2?{$version}";
                    $res['card'] = $base . "/card?{$version}";
                }
                if (strlen($img_slider) > 2) {
                    $slider = json_decode($img_slider, true);
                    foreach ($slider as $value) {
                        $res['slider'][] = $base . '/' . $value;
                    }
                }
                if (strlen($img_screenshots) > 2) {
                    $screenshots = json_decode($img_screenshots, true);
                    if (!empty($screenshots)) {
                        foreach ($screenshots as $value) {
                            $res['screenshots'][] = $base . '/' . $value;
                        }
                    } else {
                        $res['screenshots'] = [];
                    }
                } else {
                    $res['screenshots'] = [];
                }
                break;
            case "tob_idcard":
            case "tob_license":
                $res['dir']    = '../upload/tob/dev/' . $id . '/';
                $res['pubdir'] = '../upload/tob/pub/' . $id . '/';
                break;
            default:
                # code...
                break;
        }

        return $res;
    }

    /**
     * 上传到线上的图片信息
     * @param   string   tp   图片类型 webgameimg vrgameimg  openuser openapp video
     * @param   int     appid   appid
     * @param   int     version 图片版本号，如果版本号是0，没传过图片
     * @param   bool    isdev   是否查看开发者后台的图片
     * @return  array   所有图片列表
     */
    public static function path($tp, $id, $version = 1, $img_slider = "", $isdev = true)
    {

        $id = intval($id);
        if (!$id) {
            return false;
        }

        if (!$version) {
            return array();
        }
        $cfg = Config::get('server.img.' . $tp);

        $path = $cfg['url'];

        if ($isdev) {
            $path .= "dev/";
        } else {
            $path .= "pub/";
        }
        $path .= $id . "/";
        $res = array();
        switch ($tp) {
            case 'webgameimg':
                $res['logo']    = $path . "logo?{$version}";
                $res['slogo']   = $path . "slogo?{$version}";
                $res['bg']      = $path . "bg?{$version}";
                $res['history'] = $path . "history?{$version}";
                $res['bg2']     = $path . "bg2?{$version}";
                $res['card']    = $path . "card?{$version}";
                $res['icon']    = $path . "icon?{$version}";
                $res['ico']     = $path . "ico?{$version}";
                break;
            case 'vrgameimg':
                $res['logo'] = $path . "logo?{$version}";
                $res['icon'] = $path . "icon?{$version}";
                $res['bg']   = $path . "bg?{$version}";
                break;
            case 'openapp':
                $res['dir']    = '../upload/app/dev/' . $id . '/';
                $res['pubdir'] = '../upload/app/pub/' . $id . '/';
                break;
            case 'openuser':
                $res['dir']         = '../upload/user/dev/' . $id . '/';
                $res['pubdir']      = '../upload/user/pub/' . $id . '/';
                $res['credentials'] = $path . "idcard?{$version}";
                break;
            case 'userimg':
                $res['remote'] = '/userimg/dev/' . $id . '/120.png';
                $res['face']   = $path . "120.png";
                $res['dir']    = '../upload/www/' . substr(md5($id), 0, 6) . "/";
                $res['bucket'] = $cfg['bucket'];
                break;
            case 'tob':
                $res['dir'] = '../upload/tob/dev/' . $id . "/";
                break;
            case 'service':
                $hashId      = md5($id);
                $res['dir']  = '../upload/service/' . substr($hashId, 0, 4) . "/" . substr($hashId, 4, 4) . "/";
                $path        = $cfg['url'] . substr($hashId, 0, 4) . "/" . substr($hashId, 4, 4) . "/";
                $res['ext']  = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif'];
                $res['size'] = 2 * 1024 * 1024;
                break;
            case 'wwwimg':
                $res['remote'] = 'wwwimg-';
                $res['bucket'] = $cfg['bucket'];
                break;
            case 'video':
                $res['remote'] = '/dev/';
                $res['bucket'] = $cfg['bucket'];
                break;
            default:
                break;
        }
        if (strlen($img_slider) > 2) {
            $slider = json_decode($img_slider, true);
            foreach ($slider as $value) {
                $res['slider'][] = $path . $value;
            }
        }
        if ($isdev == false) {
            $res['dev_path'] = $cfg['bucket'] . '/' . $tp . '/dev/' . $id . '/';
            $res['pub_path'] = $cfg['bucket'] . '/' . $tp . '/pub/' . $id . '/';
            $res['bucket']   = $cfg['bucket'];
        }
        $res['base'] = $path;
        return $res;
    }

    public static function uploadDataUrl($tp, $urls, $id)
    {
        $resInfo   = self::path($tp, $id);
        $uploadDir = $resInfo['dir'];
        if (!file_exists($uploadDir)) {
            self::mkdirs($uploadDir);
        }

        $out = [];
        foreach ($urls as $key => $url) {
            if (!preg_match('/data:([^;]*);base64,(.*)/', $url, $matches)) {
                continue;
            }
            $fileType = $matches[1];
            $fileBlob = base64_decode($matches[2]);

            if (!in_array($fileType, array_keys($resInfo['ext']))) {
                continue;
            }

            if (!isset($resInfo['ext'][$fileType])) {
                continue;
            }

            if (!$fileBlob) {
                continue;
            }

            $fileName = self::randName() . '.' . $resInfo['ext'][$fileType];

            $ret = file_put_contents($uploadDir . $fileName, $fileBlob);
            if ($ret) {
                $out[] = $fileName;
            }
        }
        return $out;
    }

    public static function cosCopyFiles($appInfo)
    {
        $appid = $appInfo['appid'];
        if ($appInfo['game_type'] == 0) {
            $tp = "webgameimg";
        } else {
            $tp = "vrgameimg";
        }

        $resInfo = self::getUrl($tp, ['id' => $appid, 'version' => $appInfo['img_version'], 'img_slider' => $appInfo['img_slider'], 'screenshots' => $appInfo['screenshots']]);
        foreach ($resInfo as $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    $urls[] = $val;
                }
            } else {
                $pos = strpos($value, '?');
                if ($pos > 0) {
                    $urls[] = substr($value, 0, $pos);
                } else {
                    $urls[] = substr($value);
                }
            }
        }
        $rsync      = true;
        $serverCfg  = Config::get("server.cosimg");
        $requestUrl = $serverCfg['url'];
        $cosAppId   = $serverCfg['appid'];
        $bucket     = $serverCfg['bucket'];
        foreach ($urls as $url) {
            $rsync_ret = self::cosCopyFile($cosAppId, $bucket, $requestUrl, $url);

            if (!$rsync_ret) {
                $rsync = false;
            }
        }
        return $rsync;
    }

    private static function cosCopyFile($appid, $bucket, $baseUrl, $url)
    {

        $fileInfo = self::getFile($url);
        if (!$fileInfo) {
            return false;
        }
        $path       = str_replace("dev", "pub", $url);
        $requestUrl = $baseUrl . $appid . "/" . $bucket . "/0/" . urlencode($path);
        $sign       = ImageHelper::imgSignBase($path, time() + 120, 0);
        $sha1       = $fileInfo[0];
        $size       = $fileInfo[1];
        $params     = ["Sha" => $sha1, "Op" => "upload_slice", "FileSize" => $size, "Slice_size" => 3145728];

        $copyRes = HttpRequest::cosPost($requestUrl, $sign, $params);
        if (isset($copyRes['code'])) {
            if ($copyRes['code'] == -1886) {
                $retDel = self::cosDelFile($requestUrl, $path);
                if ($retDel) {
                    $copyRes = HttpRequest::cosPost($requestUrl, $sign, $params);
                    if (isset($copyRes['code']) && $copyRes['code'] == 0) {

                        return true;
                    }
                }
            } else {
                if ($copyRes['code'] == 0) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function cosDelFile($requestUrl, $path)
    {
        $requestUrl .= "/del";
        $sign   = ImageHelper::imgSignBase($path, time() + 60, 1);
        $delRes = HttpRequest::cosPost($requestUrl, $sign, []);
        if (isset($delRes['code']) && $delRes['code'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function cosGetPrivateFile($file)
    {
        $serverCfg = Config::get("server.cosv4");
        $fileUrl   = str_replace("/" . $serverCfg['appid'] . "/" . $serverCfg['bucket'] . "/", "", $file) . "?v=" . mt_rand(1000, 9999);

        $bucket = $serverCfg['bucket'];
        $url    = $serverCfg['downurl'] . $fileUrl;
        $sign   = self::appSignBase($serverCfg['appid'], $serverCfg['sid'], $serverCfg['skey'], 0, $file, $bucket, false);

        $header = array(
            'Authorization: ' . $sign,
        );
        $res = HttpRequest::cosPost($url, $sign, ['privateimg' => true], $header);
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    public static function cosUpdatePrivate($dst)
    {
        $serverCfg = Config::get("server.cosv4");
        $bucket    = $serverCfg['bucket'];
        $url       = $serverCfg['url'] . $dst;
        $sign      = self::appSignBase($serverCfg['appid'], $serverCfg['sid'], $serverCfg['skey'], 0, $dst, $bucket, false);
        $params    = array(
            'op'        => 'update',
            'authority' => "eWRPrivate",
        );

        $header = array(
            'Authorization: ' . $sign,
            'Content-Type: application/json',
        );

        $res = HttpRequest::cosPost($url, $sign, json_encode($params), $header);
        if ($res && isset($res['code']) && $res['code'] == 0) {
            return $res;
        } else {
            return $res;
        }
    }

    public static function cosUploadFile($file, $dst)
    {
        $serverCfg = Config::get("server.cosv4");
        $bucket    = $serverCfg['bucket'];
        $url       = $serverCfg['url'] . $serverCfg['appid'] . "/" . $bucket . $dst;
        $sign      = self::appSignBase($serverCfg['appid'], $serverCfg['sid'], $serverCfg['skey'], time() + 60, null, $bucket, false);
        $sha1      = hash_file('sha1', $file);
        $params    = array(
            'op'       => 'upload',
            'sha'      => $sha1,
            'biz_attr' => '',
        );
        $params['filecontent'] = curl_file_create($file);
        $params['insertOnly']  = 0;

        $header = array(
            'Authorization: ' . $sign,
        );
        $res = HttpRequest::cosPost($url, $sign, $params, $header);
        if ($res && isset($res['code']) && $res['code'] == 0) {
            unlink($file);
            return $res;
        } else {
            return $res;
        }
    }

    public static function saveFile($name, $sha1, $fileSize)
    {
        $str = [$sha1, $fileSize];
        $ret = Vredis::set("cos_file", $name, json_encode($str));
        Vredis::close();
        return $ret;
    }

    public static function getFile($name)
    {
        $str = Vredis::get("cos_file", $name);
        Vredis::close();
        if ($str) {
            return json_decode($str, true);
        } else {
            return false;
        }

    }

    public static function imgSignBase($fileId, $expired, $once)
    {
        $serverCfg = Config::get("server.cosimg");
        $puserid   = 0;
        $now       = time();
        $rdm       = rand();
        if ($once == 1) {
            $expired = 0;
        }
        $plainText = 'a=' . $serverCfg['appid'] . '&b=' . $serverCfg['bucket'] . '&k=' . $serverCfg['sid'] . '&e=' . $expired . '&t=' . $now . '&r=' . $rdm . '&u=' . $puserid . '&f=' . $fileId;
        $bin       = hash_hmac("SHA1", $plainText, $serverCfg['skey'], true);
        $bin       = $bin . $plainText;
        $sign      = base64_encode($bin);
        return $sign;
    }

    public static function appSignBase($appId, $secretId, $secretKey, $expired, $fileId, $bucketName, $json = true)
    {
        $now       = time();
        $rdm       = rand();
        $plainText = "a=$appId&k=$secretId&e=$expired&t=$now&r=$rdm&f=$fileId&b=$bucketName";
        $bin       = hash_hmac('SHA1', $plainText, $secretKey, true);
        $bin       = $bin . $plainText;
        $sign      = base64_encode($bin);
        if (!$json) {
            return $sign;
        } else {
            $json = array('code' => '0', 'message' => '成功', 'data' => array('sign' => $sign));
            return json_encode($json);
        }
    }

    public static function localImg($url)
    {
        $url     = str_replace(["&amp;", 'tp=webp'], ["&", 'tp=jpg'], $url);
        $options = array(
            'http' => array(
                'method' => "GET",
                'header' => "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36\r\n", // i.e. An iPad
            ),
        );
        $context = stream_context_create($options);
        $img     = file_get_contents($url, false, $context);
        if (!$img) {
            return false;
        }
        $extArr = explode('.', $url);
        $end    = strtolower(end($extArr));
        if (strstr("gif", $end)) {
            $ext = "gif";
        } elseif (strstr("png", $end)) {
            $ext = "png";
        } else {
            $ext = "jpg";
        }
        $fileName  = md5($img) . "." . $ext;
        $uploadDir = "../upload/news/";
        if (!file_exists($uploadDir)) {
            self::mkdirs($uploadDir);
        }
        $ret = file_put_contents($uploadDir . $fileName, $img);
        if ($ret) {
            $path = self::newsUploadFile($uploadDir, $fileName);
            if ($path) {
                unlink($uploadDir . $fileName);
                return $path;
            } else {
                return false;
            }
        }
        return false;
    }

    public static function videoTranscoding($fileName, $ops = 'avthumb/mp4/vcodec/libx264/crf/30', $name = 'blue')
    {
        $serverCfg = Config::get("server.netcenter");

        $ak = $serverCfg['ak'];
        $sk = $serverCfg['sk'];

        $bucket = $serverCfg['bucket'];
        $body   = 'bucket=' . Library::base64Urlsafeencode($bucket);
        $body .= '&key=' . Library::base64Urlsafeencode($fileName);
        $newName      = str_replace(".mp4", "_$name.mp4", $fileName);
        $newNameParam = "|saveas/" . Library::base64Urlsafeencode($bucket . ":" . $newName);
        $fops         = Library::base64Urlsafeencode($ops . $newNameParam);
        $notifyUrl    = Library::base64Urlsafeencode("http://www.vronline.com/callback/transcoding");
        $body .= '&fops=' . $fops . '&notifyURL=' . $notifyUrl . '&separate=1';

        $authStr     = "/fops\n$body";
        $sign        = hash_hmac('sha1', $authStr, $sk, false);
        $encodeSign  = Library::base64Urlsafeencode($sign);
        $accessToken = $ak . ':' . $encodeSign;
        $url         = $serverCfg['url'] . "fops";

        $resStr = HttpRequest::post($url, $body, 0, [], ['Authorization:' . $accessToken]);
        if ($resStr) {
            $res = json_decode($resStr, true);
            if (isset($res['persistentId'])) {
                return $res['persistentId'];
            }
        }
        return false;
    }

    private static function newsUploadFile($uploadDir, $fileName)
    {
        $serverCfg  = Config::get("server.cosimg");
        $baseUrl    = $serverCfg['url'];
        $cosAppId   = $serverCfg['appid'];
        $bucket     = $serverCfg['bucket'];
        $path       = 'newsimg/auto/' . $fileName;
        $requestUrl = $baseUrl . $cosAppId . "/" . $bucket . "/0/" . urlencode($path);
        $sign       = ImageHelper::imgSignBase($path, time() + 120, 0);
        $filePath   = $uploadDir . $fileName;
        $params     = [];
        if (function_exists('curl_file_create')) {
            $params['FileContent'] = curl_file_create(realpath($filePath));
        } else {
            $params['FileContent'] = '@' . realpath($filePath);
        }
        $copyRes = HttpRequest::cosPost($requestUrl, $sign, $params);
        if (isset($copyRes['code'])) {
            if ($copyRes['code'] == -1886) {
                return $path;
            } else {
                if ($copyRes['code'] == 0) {
                    return $path;
                }
            }
        }
        return false;
    }

    private static function randName()
    {
        return str_random(15);
    }

    private static function mkdirs($dir)
    {
        return is_dir($dir) or (self::mkdirs(dirname($dir)) and mkdir($dir, 0777));
    }

}
