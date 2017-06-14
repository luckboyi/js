<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Corp;
use App\Models\JCrop;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;

class CropPicController extends Controller{
    public function cropUpload()  //上传的接口
    {
        $maxSize  = 1024*1024 ;//1M 设置附件上传大小
        $allowExts  = array("gif","jpg","jpeg","png");// 设置附件上传类型
        $upload = new Corp();// 实例化上传类
        $upload->maxSize = $maxSize;
        $upload->allowExts = $allowExts;
        $upload->savePath =  'upload/';// 设置附件
        $upload->saveRule = time().sprintf('%04s',mt_rand(0,1000));
        if(!$upload->upload()) {// 上传错误提示错误信息
            $errormsg = $upload->getErrorMsg();
            $arr =  array(
                'msg'=>'1111',
                'error'=>$errormsg, //返回错误
                'imgurl'=>'',//返回图片名
            );
            echo json_encode($arr);
            exit;

        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            $imgurl = $info[0]['savename'];
        }
        $arr =  array(
            'msg'=>'0000',
            'error'=>'', //返回错误
            'imgurl'=>$imgurl,//返回图片名
        );
        echo json_encode($arr);
        exit;
    }
    
    public function corpSubmit()
    {return false;
        $pic_name=$_REQUEST['pic_name'];
        $x=$_REQUEST['x'];
        $y=$_REQUEST['y'];
        $w=$_REQUEST['w'];
        $h=$_REQUEST['h'];
        $targ_w = $targ_h = 66;
        $filep="upload/";
        $crop=new jCrop($filep, $pic_name, $x, $y, $w, $h, $targ_w, $targ_h);
        $file=$crop->crop();
    }

}