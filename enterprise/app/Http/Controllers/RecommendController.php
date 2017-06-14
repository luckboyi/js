<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\OperateModel;
use App\Models\RecommendModel;
use Helper\Library;
use Illuminate\Http\Request;

class RecommendController extends Controller
{
    /**
     * 添加一个排期内容
     *
     * @param  int  posid   位置id
     * @param  int  itemid  排期内容ID
     * @param  int  weight  权重
     * @param  int  start   开始时间戳
     * @return bool
     */
    public function addScheduleItem(Request $request)
    {
        $posid      = intval($request->input("posid"));
        $itemid     = intval($request->input("itemid"));
        $weight     = intval($request->input("weight"));
        $type       = $request->input("type");
        $start      = $request->input("start");
        $target_url = $request->input("targetUrl") != '' ? $request->input("targetUrl") : '';
        $action     = $request->input("action") != '' ? $request->input("action") : '';
        if (!$posid || !$itemid) {
            return Library::output(2001);
        }

        $info           = array();
        $info['weight'] = $weight;
        $info['start']  = strtotime($start);
        $operateModel   = new OperateModel;

        $typeArr = $operateModel->getPosId($type);

        $info['type'] = isset($typeArr['type']) ? $typeArr['type'] : 'mix';

        $ifExistItemId = $operateModel->ifExistItemId($typeArr['type'], $itemid);
        if (!$ifExistItemId && $action != 'banner') {
            return Library::output(2304);
        }

        if ($action == 'banner') {
            $resource = $request->input("showpic");

            $info['banner_url'] = $resource;
            $info['target_url'] = $target_url;
            $ret                = $operateModel->insScheduleItem($posid, $itemid, $info);
        } else {
            $ret = $operateModel->insScheduleItem($posid, $itemid, $info);
        }

        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 修改一个线上的推荐内容
     * 修改权重、更换内容
     *
     * @param  int  posid   位置id
     * @param  int  itemid  排期内容ID
     * @return bool
     */
    public function updOneItem(Request $request)
    {
        $posid     = intval($request->input("posid"));
        $itemid    = intval($request->input("itemid"));
        $newitemid = intval($request->input("newitemid"));
        $weight    = intval($request->input("weight"));
        if (!$posid || !$itemid) {
            return Library::output(2001);
        }
        $info = array();
        if ($newitemid) {
            $info['itemid'] = $newitemid;
        }
        if ($weight) {
            $info['weight'] = $weight;
        }
        $operateModel = new OperateModel;

        $ret = $operateModel->updItem($posid, $itemid, $info);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 删除一个线上推荐内容
     *
     * @param  int  posid   位置id
     * @param  int  itemid  排期内容ID
     * @return bool
     */
    public function delOneItem(Request $request)
    {
        $posid  = intval($request->input("posid"));
        $itemid = intval($request->input("itemid"));
        if (!$posid || !$itemid) {
            return Library::output(2001);
        }
        $operateModel = new OperateModel;
        $ret          = $operateModel->delItem($posid, $itemid);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 修改一个排期推荐内容
     * 修改权重、更换内容、修改时间
     *
     * @param  int  $id
     * @return bool
     */
    public function updScheduleItem(Request $request)
    {
        $id         = intval($request->input("id"));
        $itemid     = intval($request->input("itemid"));
        $weight     = intval($request->input("weight"));
        $start      = $request->input("start");
        $type       = $request->input("type");
        $target_url = $request->input("targetUrl") != '' ? $request->input("targetUrl") : '';
        $action     = $request->input("action") != '' ? $request->input("action") : '';

        if (!$id) {
            return Library::output(2001);
        }
        $info = array();
        if ($itemid) {
            $info['itemid'] = $itemid;
        }
        if ($weight) {
            $info['weight'] = $weight;
        }
        if ($start) {
            $info['start'] = strtotime($start);
        }
        if (!$info) {
            return Library::output(2001);
        }

        $operateModel = new OperateModel;

        $typeArr = $operateModel->getPosId($type);

        $info['type'] = isset($typeArr['type']) ? $typeArr['type'] : 'mix';

        $ifExistItemId = $operateModel->ifExistItemId($typeArr['type'], $itemid);
        if (!$ifExistItemId && $action != 'banner') {
            return Library::output(2304);
        }

        if ($action == 'banner') {
            $info['target_url'] = $target_url;
            $ret                = $operateModel->updScheduleItem($id, $info);
        } else {
            $ret = $operateModel->updScheduleItem($id, $info);
        }

        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 删除一个排期推荐内容
     *
     * @param  int  $id
     * @return bool
     */
    public function delScheduleItem($id)
    {
        if (!$id) {
            return Library::output(2001);
        }
        $operateModel = new OperateModel;
        $ret          = $operateModel->delScheduleItem($id);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 发布
     * 将排期表里的数据同步到线上
     *
     * @param  int  $id
     * @return bool
     */
    public function publish($posid)
    {
        // if(!$posid) {
        //     return Library::output(2001);
        // }
        // $recommendModel = new RecommendModel;
        // $ret = $recommendModel->publish($posid);

        // if($ret) {
        //     return Library::output(0);
        // }else {
        //     return Library::output(1);
        // }
    }

    /**
     * 添加删除视频banner推荐位数据
     * @param $v
     */
    public function operateSortBanner($info)
    {

    }

    /*
     * 上传图片的接口
     */
    public function uploadPic($gid, $uploadDir, $type = false)
    {
        // 创建用户的头像目录
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        $fileAllowExt  = 'gif|jpg|jpeg|png|gif'; //限制上传图片的格式
        $fileAllowSize = 2 * 1024 * 1024; //限制最大尺寸是2MB
        //$submit = isset($_POST['submit']) ? $_POST['submit'] : 123;
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $fileName         = $_FILES['file']['name'];
            $fileError        = $_FILES['file']['error'];
            $fileType         = $_FILES['file']['type'];
            $fileTmpName      = $_FILES['file']['tmp_name'];
            $fileSize         = $_FILES['file']['size'];
            $fileExt          = substr($fileName, strrpos($fileName, '.') + 1);
            $data['oldName']  = $fileName;
            $data['fileExt']  = $fileExt;
            $data['fileType'] = $fileType;
            switch ($fileError) {
                case 0:
                    $code        = 0;
                    $data['msg'] = "文件上传成功!";
                    break;

                case 1:
                    $code        = 2202;
                    $data['msg'] = "文件上传失败，文件大小" . $fileSize . "超过限制,允许上传大小2M";
                    break;

                case 3:
                    $code        = 2203;
                    $data['msg'] = "上传失败，文件只有部份上传!";
                    break;

                case 4:
                    $code        = 2204;
                    $data['msg'] = "上传失败，文件没有被上传!";
                    break;

                case 5:
                    $code        = 2205;
                    $data['msg'] = "文件上传失败，文件大小为0!";
                    break;
            }
            if (stripos($fileAllowExt, $fileExt) === false) {
                $code        = 2206;
                $data['msg'] = "该文件扩展名不允许上传";
            }
            if ($fileSize > $fileAllowSize) {
                $code        = 2202;
                $data['msg'] = "文件大小超过限制,只能上传2M的文件!";
            }
            if ($code !== 0) {
                $data['msg'] = $data['msg'];
                return Library::output($code, $data);
            }
            if (file_exists($uploadDir)) {
                $fileNewName = substr(md5($fileName), 0, 12) . time() . '.' . $fileExt;

                //$fileNewName = "credentials.png";
                $data['fileNewName'] = $fileNewName;
                $fileSavePath        = $uploadDir . $fileNewName;
                move_uploaded_file($fileTmpName, $fileSavePath);
                return Library::output($code, $data);
            }
        }
        return Library::output(1);
    }

}
