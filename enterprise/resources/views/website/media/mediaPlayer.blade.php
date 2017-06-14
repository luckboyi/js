<!DOCTYPE html>
<html lang="en">
<?php header("Access-Control-Allow-Origin: *");?>
<head>
    <meta charset="UTF-8">
    <title>视频播放器</title>
    <link rel="stylesheet" href="{{static_res('/common/style/base.css')}}">
    <link href="{{asset('style/website/valiant360.css')}}" rel="stylesheet">
    {{--<link rel="stylesheet" href="{{static_res('/website/style/valiant360.css')}}">--}}
    <script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}"></script>
    <script src="{{static_res('/website/js/three.min.js')}}"></script>
    <script src="{{static_res('/website/js/jquery.valiant360.js')}}"></script>
</head>
<body>
<div class="video_play_container">
    <div class="valiantPhoto" data-video-src="@if(!empty($videoInfo)) {{ $videoInfo[0]['resources'] }} @endif" style="width: 960px; height: 540px;background: #000"></div>
</div>
</body>
</html>
<script>
    $(function(){
        //vr视频播放
        $('.valiantPhoto').Valiant360({
            clickAndDrag:true,
            muted:false,
            loop:false
        });
        //点击喜欢或者不喜欢是
        /* $('.video_content_head').on('click','.comment',function(){
         if(!$('body').find('.comment').hasClass('cur')){
         var num = parseInt($(this).find('b').html());
         num = num+1;
         $(this).addClass('cur').find('b').text(num);
         }
         })*/


    })
</script>