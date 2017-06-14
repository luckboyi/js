<?php

namespace App\Models;

// 引用Model
use Config;
use Helper\Library;
use Helper\HttpRequest;
use Illuminate\Database\Eloquent\Model;
use \App\Models\DataCenterStatModel;

class ImModel extends Model
{
    private $imApiHost = "http://192.168.74.48";
    /**
     * 签名校验
     */
    public function getImToken($uid)
    {
        $uid = intval($uid);
        if(!$uid) {
            return false;
        }
        $params = ["userid" => $uid];
        $json = json_encode($params);
        $url = $this->imApiHost . "/kca/login/generate_auth_secret";
        $ret = HttpRequest::post($url, $json);
        if(!$ret) {
            return false;
        }
        $info = json_decode($ret, true);
        if(!$info) {
            return false;
        }
        return $info;
    }

}
