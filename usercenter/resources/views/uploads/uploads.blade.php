<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2016/8/22
 * Time: 19:45
 */
?>
@extends('layouts.app')

{{--@include('common.errors')--}}
@section('content')
    <style type="text/css">
        *{ margin:0; padding:0;}
        #box{ margin:50px auto; width:540px; min-height:400px; background:#FF9}
        #demo{ margin:50px auto; width:540px; min-height:800px; background:#CF9}
    </style>
    <body>
    <div id="box">
        <div id="test" ></div>
    </div>

    <div id="demo">
        <div id="as" ></div>
    </div>
    <!-- TODO: Current Tasks -->
    <script type="text/javascript">
        $('#test').diyUpload({
            url:'/face/upload',
            'formData'     : {
                'timestamp' : '<?php echo time();?>',
                '_token'     : '<?php echo csrf_token();?>',
                'uid' : 1212,
                'gid' : 2222
            },
            'fileTypeExts'   : '*.jpg;*.jpeg;*.gif;*.png',//允许上传的文件类型
            fileNumLimit: 1,
            success:function( data ) {
                console.info( data );
            },
            error:function( err ) {
                console.info( err );
            }
        });

        $('#as').diyUpload({
            url:'/face/upload',
            'formData'     : {
                'timestamp' : '<?php echo time();?>',
                '_token'     : '<?php echo csrf_token();?>',
                'uid' : 1212,
                'gid' : 2222
            },
            success:function( data ) {
                console.info( data );
            },
            error:function( err ) {
                console.info( err );
            },
            buttonText : '选择文件',
            auto: false, //选择文件后是否自动上传
            chunkRetry : 2, //如果某个分片由于网络问题出错，允许自动重传次数
            runtimeOrder: 'html5,flash',
            chunked: true, //是否要分片处理大文件上传
            chunkSize:5*1024*1024, //分片上传，每片2M，默认是5M
//            //最大上传的文件数量, 总文件大小,单个文件大小(单位字节);
            fileNumLimit:500,
            fileSizeLimit:1024*1024*1024,
            fileSingleSizeLimit:1024*1024*1024,
            accept: {}
        });
    </script>
@endsection
