<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/2
 * Time: 14:32
 */
?>
@extends('layouts.app2')

{{--@include('common.errors')--}}
@section('content')
    <style type="text/css">
        body{
            padding:0; margin-left:50px;margin-top:50px; font-size:12px;  color:#333333; font-family:'宋体',Verdana, Geneva, sans-serif;
        }
        .tips{padding:10px;color:red;margin-top:20px;}
    </style>
    <script>
        $(document).ready(function(e){

            $('#head_photo').live('change',function(){
                ajaxFileUploadview('head_photo','photo_pic',"cropUpload");
            });

        });


        function show_head(head_file){

            //插入数据库
            //$.post("{:U('Home/Index/update_head')}",{head_file:head_file},function(result){
            $("#head_photo_src").attr('src',head_file);
            //});

        }

        //文件上传带预览
        function ajaxFileUploadview(imgid,hiddenid,url){


            $.ajaxFileUpload
            ({
                url:url,
                secureuri:false,
                fileElementId:imgid,
                dataType: 'json',
                data:{name:'logan', id:'id'},
                success: function (data, status)
                {
                    if(typeof(data.error) != 'undefined')
                    {
                        if(data.error != '')
                        {
                            var dialog = art.dialog({title:false,fixed: true,padding:0});
                            dialog.time(2).content("<div class='tips'>"+data.error+"</div>");
                        }else{
                            var resp = data.msg;
                            if(resp != '0000'){
                                var dialog = art.dialog({title:false,fixed: true,padding:0});
                                dialog.time(2).content("<div class='tips'>"+data.error+"</div>");
                                return false;
                            }else{
                                $('#'+hiddenid).val(data.imgurl);

                                art.dialog.open("corpImg?img="+data.imgurl,{
                                    title: '裁剪头像',
                                    width:'580px',
                                    height:'400px'
                                });

                                //dialog.time(3).content("<div class='msg-all-succeed'>上传成功！</div>");
                            }




                        }
                    }
                },
                error: function (data, status, e)
                {

                    dialog.time(3).content("<div class='tips'>"+e+"</div>");
                }
            })

            return false;
        }

    </script>
    </head>
    <body>
    <input type="file" name="head_photo" id="head_photo" value="">
    <input type="hidden" name="photo_pic" id="photo_pic" value="">
    <!--头像显示-->
    <div id="show_photo" style="border:1px solid #f7f7f7;width:66px;height:66px;"><img id="head_photo_src" src="images/default.gif"></div>

@endsection