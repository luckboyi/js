<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Helper\HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DataController extends Controller
{

    private $passport_protocal = "http://dcinter.vronline.com/dataCenter.php";
    // private $passport_protocal = "http://pic.vronline.com/dataCenter/dataCenter.php";
    private $perPage = 20;

    public function getDateCenter(Request $request)
    {
        $action = $request->input('action');
        $date   = $request->input('date');
        $page   = $request->input('page');
        if ($action == '') {
            $action = 'getall';
        }
        $request = $this->passport_protocal;
        $param   = array("action" => $action, "date" => $date);
        //$ret = $this->post($param, $request);
        $ret     = json_decode(HttpRequest::post($request, $param), 1);
        $retDate = isset($ret['data']) ? $ret['data'] : [];

        $perPage = $this->perPage;
        if ($page != '') {
            $currentPage = $page;
            $currentPage = $currentPage <= 0 ? 1 : $currentPage;
        } else {
            $currentPage = 1;
        }
        $item      = array_slice($retDate, ($currentPage - 1) * $perPage, $perPage); //注释1
        $total     = count($retDate);
        $paginator = new LengthAwarePaginator($item, $total, $perPage, $currentPage, [
            'path'     => Paginator::resolveCurrentPath(), //注释2
            'pageName' => 'page',
        ]);
        $retDate = $paginator->toArray()['data'];

        //获取数据中心的最后一条数据
        $lastRet = $this->getLastDate();
        // echo '<pre>';
        // print_r($lastRet);die;
        return view('data.all', compact("retDate", "paginator", "lastRet", "action", "date"));
    }

    /**
     * 获取数据的最后一条
     * [getLastDate description]
     * @return [type] [description]
     */
    public function getLastDate()
    {
        $request = $this->passport_protocal;
        $param   = array("action" => "getlast");
        $ret     = json_decode(HttpRequest::post($request, $param), 1);
        $lastRet = isset($ret['data'][0]) ? $ret['data'][0] : [];
        return $lastRet;
    }

    /**
     * 获取数据的post方法
     * [post description]
     * @param  [type] $data [description]
     * @param  [type] $url  [description]
     * @return [type]       [description]
     */
    public static function post($data, $url = null)
    {
        if ($url == null) {
            $url = self::$url;
        }
        $return = array("state" => 0, "data" => "");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //10秒超时
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch); //接收返回信息

        if (curl_errno($ch)) {
            //出错则返回错误信息
            $return["data"] = curl_errno($ch);
        } else {
            $return["state"] = 1;
            $return["data"]  = $response;
        }
        curl_close($ch); //关闭curl链接
        return $return;
    }
}
