@inject('blade', 'App\Helper\BladeHelper')
@extends('website.media.layout')

@section('title')VRonline官网@endsection

@section('videoLeft')
<div class="webgame_right_con fr">
    <div class="video_play_container">
        <i class="suspend"></i>
    </div>

    <div class="in_video_information">
        <div class="video_content">
            <div class="video_content_head clearfix">
                <h4 class="fl">{{ $videoInfo['video_name'] }}</h4>
                <div id="disagree"  class="fr pr unlike @if(isset($clickArr) && $clickArr['alreadyClick'] == 'N') cur @endif" style="padding: 0 2px"><b id="disagreenum"> {{ $videoInfo['disagreenum'] }}</b></div>
                <div id="agree" class="fr pr like @if(isset($clickArr) && $clickArr['alreadyClick'] == 'Y') cur @endif" style="padding: 0 2px"><b id="agreenum">{{ $videoInfo['agreenum'] }}</b></div>
                <p class="fr clearfix"><b class="fl" style="padding: 0 2px;">{{ $videoInfo['video_view'] }}</b><i class="fl language" data-name="haveWatch">次观看</i></p>
            </div>
        </div>
        <div class="video_intro">{{ $videoInfo['video_intro'] }}</div>
    </div>
</div>
@endsection
@section('javascript-media')
<script src="{{ static_res('/website/js/three.min.js') }}"></script>
<script src="{{ static_res('/website/js/jquery.valiant360.js') }}"></script>
<script src="{{ static_res('/common/js/tips.js') }}"></script>
@if($platform=="pc")
<script src="{{ static_res('/client/js/video_platform.js') }}"></script>
@endif
<script>
function ifExistCur(){
    var id = $("#agree,#disagree");
    return id.hasClass('cur');
}

var video_link = "{{ $videoInfo['video_link'] }}";
var video_id = "{{ $videoInfo['video_id'] }}";
$(function(){
    //vr视频播放
    if(video_link.indexOf("vronline.com")>0) {
        var html = '<div class="valiantPhoto" id="valiantPhoto" data-video-src="'+video_link+'" style="width: 100%; height:100%;background: #000"></div>';
        $(".video_play_container").append(html);
        $('#valiantPhoto').Valiant360({
            clickAndDrag:true,
            muted:false,
            loop:false
         });
    } else {
        var html = ' <iframe allowfullscreen class="valiantPhoto" src="'+video_link+'" style="width: 100%; height: 100%;background: #000;border:none"></iframe>';
        $(".video_play_container").append(html);
    }

    //赞
    $("#agree").click(function(){
        var ifLogin = "{{ $uid }}";
        if(!ifExistCur()) {
            //var vid = $('#valiantPhoto').attr("data-vid");
            var url = 'http://www.vronline.com/video/addSupport?json={"vid":{{$videoInfo["video_id"]}},"support":1}';

            $.ajax({
                type: "GET",
                url: url,
                data: {},
                dataType: "json",
                success: function(data){
                    if(data.code == 0) {
                        var agreeNum = $("#agreenum").text();
                        $("#agreenum").text(parseInt(agreeNum) + 1);
                        $("#agree").addClass('cur');
                    } else {
                        myAlert("提示",data.msg);
                    }
                }
            });
        } else {
            myAlert("提示","您已经点过了！");
        }
    });
    //踩
    $("#disagree").click(function(){
        if(!ifExistCur()) {
            //var vid = $('#valiantPhoto').attr("data-vid");
            var url = 'http://www.vronline.com/video/addSupport?json={"vid":{{$videoInfo["video_id"]}},"support":0}';

            $.ajax({
                type: "GET",
                url: url,
                data: {},
                dataType: "json",
                success: function(data){
                    if(data.code == 0) {
                        var disagreeNum = $("#disagreenum").text();
                        $("#disagreenum").text(parseInt(disagreeNum) + 1);
                        $("#disagree").addClass('cur');
                    } else {
                        myAlert("提示",data.msg);
                    }
                }
            });
        } else {
            myAlert("提示","您已经点过了！");
        }
    });
    //记录用户的播放记录
    function addOnBeforeUnload(e) {
        var ev = e || window.event;
        var appid = "@if(isset($videoInfo['video_id'])) {{ $videoInfo['video_id'] }} @endif";
        var getTimeLen = $('.timeLabel').text();
        console.dir(getTimeLen)
        var timelen = getTimeLen;
        //ev && (ev.returnValue = timelen);

        if(timelen == '') {
            timelen = "00:00:20/00:05:20";
        }


        var timelenArr = timelen.split('/');

        var h,m,s;
        secondArr = timelenArr[0].split(':');
        if(secondArr.length > 2) {
            $.each(secondArr, function(key, val) {
                if(key == 0){
                    h = val*3600;
                }
                if(key == 1){
                    m = val*60;
                }
                if(key == 2){
                    s = val;
                }
            });
        } else {
            $.each(secondArr, function(key, val) {
                h = 0;
                if(key == 0){
                    m = val*60;
                }
                if(key == 1){
                    s = val;
                }
            });
        }

        var secondTotal = parseInt(h) + parseInt(m) + parseInt(s);
        //ev && (ev.returnValue = secondTotal);
        if(secondTotal > 5) {
            $.post('{{ url("video/addHistory") }}',{'_token':'{{csrf_token()}}','appid':appid,'timelen':secondTotal},function(data) //第二个参数要传token的值 再传参数要用逗号隔开
            {
                console.log(data);
            });
        }
    }


    if(window.attachEvent){
        window.attachEvent('onbeforeunload', addOnBeforeUnload);
    } else {
        window.addEventListener('beforeunload', addOnBeforeUnload, false);
    }

    function myAlert(title,content){
        var config = {
            headerMsg: title,
            msg: content,
            model: "tips"
        }

        if (typeof obj == "object") {
            config = $.extend({}, config, obj);
        }

        tipsFn.init(config);
    }
});
var channelName = "web";
var channel = "0";
var version = "1.0.0.1";
var args = "channelName="+channelName+"&channelId="+channel+"&videoId="+video_id+"&version="+version+"&uid=0"
var img = new Image(1, 1);
img.src = 'http://stat2.vronline.com:8801/image.gif?' + args;

</script>
@endsection
