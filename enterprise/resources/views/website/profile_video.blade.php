@extends('layouts.website')
@inject('blade', 'App\Helper\BladeHelper')
@section('title')VRonline个人中心@endsection
@section('css')
<script language="JavaScript" src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
@if($a=="upload")
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/base/md5.js') }}"></script>
@endif
@endsection

@section('content')
<div class="personal_center clearfix official_personal_center">
    <div class="left_per fl">
        <ul>
            <li class="pr userMsg datacenter-onclick-stat" stat-actid="click_user_info_menu"><a href="/profile">用户资料</a></li>
            <li class="pr my_video cur datacenter-onclick-stat" stat-actid="click_my_video_menu"><a href="/profile/video">我的视频</a></li>
            <li class="pr problem datacenter-onclick-stat" stat-actid="click_nomal_question_menu"><a href="/profile/problem">常见问题</a></li>
            <li class="pr about_vr datacenter-onclick-stat" stat-actid="click_about_vr_menu"><a href="/profile/about">关于VR助手</a></li>
        </ul>
    </div>
    <div class="right_per">
        <ul class="in_right_per">
             <!--我的视频S-->
            <li class="my_video_con">
                <div class="video_title">
                    <span class="play_record @if($a=="history") video_active @endif"><a href="/profile/video?a=history"><i></i>播放记录</a></span>
                    <span class="personal_video @if($a=="my") video_active @endif"><a href="/profile/video?a=my"><i></i>个人视频</a></span>
                    <span class="upload_video @if($a=="upload") video_active @endif"><a href="/profile/video?a=upload"><i></i>上传视频</a></span>
                </div>

                @if($a == "history")
                <!--播放记录-->
                <div class="videoCon play_record_con">
                    <!--当没有播放记录时-->
                    @if($historys->count()==0)
                    <div class="play_record_no hide">
                        <img src="static_res('/website/images/noLoginGame.png')" />
                        <p>暂无播放历史记录</p>
                        <p>可前往 <a href="/media">多媒体</a> 体验精彩VR视频</p>
                    </div>
                    @else
                    <!--当有播放记录时-->
                    <div class="play_record_more">
                        <ul class="clearfix" id="page-content">
                            {{-- @foreach($historys as $history)
                            <li class="fl">
                                <a href="javascript:;">
                                    <img src="{{ static_image($videos[$history['appid']]['video_cover']) }} " />
                                    <p>{{ $history['appname'] }} </p>
                                </a>
                            </li>
                            @endforeach --}}
                        </ul>
                    </div>
                    {{-- <div class="page">
                    {!! $historys->appends(['a' => $a])->render() !!}
                    </div> --}}
                    @endif
                </div>

                @elseif($a == "my")
                <!--个人视频-->
                <div class="videoCon personal_video_con">
                    <div class="personal_video_title">
                        <span>视频数量 (<i>{{ count($videos) }}</i>)</span>
                        <a @if($t=="all") class="all" @endif href="/profile/video?a=my&t=all">全部</a>
                        <a @if($t=="pass") class="all" @endif href="/profile/video?a=my&t=pass">已通过</a>
                        <a @if($t=="wait") class="all" @endif href="/profile/video?a=my&t=wait">审核中</a>
                        <a @if($t=="deny") class="all" @endif href="/profile/video?a=my&t=deny">未通过</a>
                    </div>
                    <ul class="clearfix">
                    @if(count($videos)>0)
                    @foreach($videos as $video)
                        <li class="fl">
                            <a class="videoConA" href="javascript:;">
                                <span>
                                    <img src="{{ static_image($video['video_cover'],226) }}" />
                                </span>
                                <p>{{ $video['video_name'] }}</p>
                            </a>
                            <div class="clearfix">
                                <span class="fl">播放次数:<i>{{ $video['video_view'] }}</i>次</span>
                                <a class="fr" href="/profile/video?a=upload&id={{ $video['video_id'] }}">修改</a>
                            </div>
                        </li>
                    @endforeach
                    @endif
                    </ul>
                    {{-- <div class="page"> {!! $videos->appends(['a' => $a,'t'=>$t])->render() !!} </div> --}}
                </div>
                @elseif($a == "upload")
                <!--上传视频-->
                <div class="videoCon upload_video_con">
                    <!--填写视频信息-->
                    <div class="video_info">
                        <h3>视频详情</h3>
                        <div>
                            <ul>
                                <li class="clearfix">
                                    <span class="fl title">视频名称</span>
                                    <input id="video_id" type="hidden"  value="{{ $video['video_id'] }}"/>
                                    <input class="fl upload_con" id="video_name" type="text" placeholder="请填写上传的视频名称"  value="{{ $video['video_name'] }}" />
                                </li>
                                <li class="clearfix text_con">
                                    <span class="fl title">视频简介</span>
                                    <textarea class="fl upload_con" id="video_intro" rows="" cols="" placeholder="请对上传的视频内容进行简单描述"  >{{ $video['video_intro'] }}</textarea>
                                </li>
                                <li class="clearfix">
                                    <span class="fl title">视频分类</span>
                                    <select id="video_class" class="fl upload_con">
                                            <option value="0">请选择</option>
                                            {!! $blade->adminCpVideoClass(2) !!}
                                    </select>
                                </li>
                                <li class="clearfix">
                                    <span class="fl title">VR视频</span>
                                    <div class="fl upload_con" id="video_vr">
                                        <span><i class="sel"></i> &nbsp;是</span>
                                    </div>
                                </li>
                                <li class="clearfix upload_pic">
                                    <span class="fl title">视频封面</span>
                                    <div class="fl upload_con_pic" id="video_cover_container">
                                        <div id="video_cover_browser" >
                                            <input  type="button" value="" />
                                            <p id="uploadImg">
                                                <i></i>
                                                <span>上传图片</span>
                                            </p>
                                            <input type="hidden" id="video_cover" value="{{ $video['video_cover'] }}">
                                        </div>
                                        <p>仅支持JPG及PNG格式,尺寸为<i>xx*xx</i>,大小不超过500K</p>
                                    </div>
                                </li>
                                <li class="clearfix">
                                    <span class="fl title">视频地址</span>
                                    <input class="fl upload_con" id="video_link" type="text"  value="{{ $video['video_link'] }}"/>
                                </li>
                            </ul>
                            <div class="sc_video" id="video_link_container">
                                <p id="video_link_browser">上传视频</p>
                                <span><em id="video_link_desc"></em> &nbsp;&nbsp;上传文件不能超过10G,必须为mp4格式</span>
                            </div>
                            <div>
                                <p class="progress_bar" style="display: none"><span class="progress_bar_length"></span></p>
                            </div>
                        </div>
                        <div class="tj_video">
                            <p onclick="saveVideo()">提交审核</p>
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;<i class="sel selected"></i>&nbsp;&nbsp;同意<a href="http://www.vronline.com/license/video_upload" target="_blank">VRonline视频上传服务条款</a></span>
                        </div>
                    </div>
                    <!--视频提交成功-->
                    <div class="video_success hide">
                        <p class="state"><i></i><span>视频提交成功！</span></p>
                        <p class="success_after">您可以在 <a href="/profile/video?a=my">个人视频</a> 中查看审核进度</p>
                        <p class="go_on"><a href="/profile/video?a=upload">继续上传视频</a></p>
                    </div>
                </div>
                @endif
            </li>
            <!--我的视频E-->
        </ul>
    </div>
</div>
@endsection

@section('javascript')
<script language="JavaScript" src="{{ static_res('/common/js/tips.js') }}"></script>
<script src="{{static_res('/common/js/pagination.js')}}"></script>
@if($a=="upload")
<script type="text/javascript">
var VIDEO_FILE_NO = 0;
var VIDEO_FILE_SELECT = 1;
var VIDEO_FILE_UPLOADING = 2;
var VIDEO_FILE_UPLOAD_COMPLETE = 3;

var video_cover_obj,video_link_obj;
var cover_link = "{{ static_image($video['video_cover']) }}";
var is_vr = {{ $video['video_vr'] }};
var class_id = "{{ $video['video_class'] }}";
var videoFileStat = VIDEO_FILE_NO;


$(document).ready(function(){
     var video_link = $("#video_link").val();
     if(video_link.length>5) {
        videoFileStat = VIDEO_FILE_UPLOAD_COMPLETE;
     }
    $("#video_cover_browser").css('backgroundImage',"url("+cover_link+")");
    if(is_vr==1) {
        $("#video_vr span>i").addClass("selected");
    }

    if(class_id.length>0) {
        var class_arr = class_id.split(",");
        if(class_arr.length>0) {
            $('#video_class option[value='+class_arr[0]+']').prop("selected",true);
        }
    }

    $("#video_vr span>i").click(function() {
        var that = $(this);
        var className = that.attr("class");
        if(className.indexOf("selected")>0) {
            that.removeClass("selected");
        } else {
            that.addClass("selected");
        }
    });

    video_cover_obj = new loiUploadContainer({
        container:"video_cover_container",
        choose:"video_cover_browser",
        ext:"jpg,png",
        upload:{tp:"videoimg",success:function(json){
          var jsonResult = $.parseJSON(json);
          var path = jsonResult.data.fileid;
          $("#video_cover_browser").css('backgroundImage',"url(http://image.vronline.com/"+path+")");
          $("#video_cover_browser p").hide();
          $("#video_cover").val(path);
        },error:function(json){
            console.log(json);
        }},
          filesAdd:function(files){
          }
    });
     video_link_obj = new loiUploadContainer({
        container:"video_link_container",
        choose:"video_link_browser",
        ext:"mp4",
        upload:{tp:"netcvideo",success:function(json){
            var res = $.parseJSON(json)
            var percent = "100%";
            $(".progress_bar_length").css('width',percent);
            var jsonResult = $.parseJSON(json);
            $("#video_link").val('http://netctvideo.vronline.com/'+res.key);
             $(".video_link_browser").text('上传成功');
             $("#"+video_link_obj.inputId).prop("disabled",false);
        },sliceback:function(per){
            var num = new Number(per);
            num = num*100;
            var percent = num.toFixed(2)+"%";
             $(".progress_bar_length").css('width',percent);
        },error:function(msg){
            if(typeof(msg)=="string") {
                loiNotifly(msg)
            } else if(typeof(msg)=="object") {
                if(typeof(msg.responseText)!="undefined") {
                    var errJson =  $.parseJSON(msg.responseText)
                    loiNotifly("文件已经上传,请更换名称后重新上传",3000)
                }
            }
            $('.progress_bar_length').parent().hide();
            $("#"+video_link_obj.inputId).prop("disabled",false);
        }},
        filesAdd:function(files){
            $(".progress_bar_length").css('width',0);
            $('.progress_bar_length').parent().show();
            var file = files[0];
            $("#video_link_desc").text(file.name);
            $(".video_link_browser").text('上传中...');
            $("#"+video_link_obj.inputId).prop("disabled",true);
        }
      });

});

function toDecimal(x){
  var f = parseFloat(x);
  if (isNaN(f)) {
    return;
  }
  f = Math.round(x*100)/100;
  return f;
}


function saveVideo() {
    var video_id = $("#video_id").val();
    var video_name = $("#video_name").val();
    var video_intro = $("#video_intro").val();
    var video_class = $("#video_class").val();
    var video_vr = 0;
    var className = $("#video_vr span>i").attr("class");
    if(className.indexOf("selected")>0) {
        video_vr = 1;
    }
    var video_cover = $("#video_cover").val();
    var video_link = $("#video_link").val();
    var err;
    if(video_name.length<5 || video_name.length>20) {
        err = "视频名称在5-20字符之间";
    }
    if(video_intro.length<5 || video_intro.length>30) {
        err = "视频简介在5-30字符之间";
    }
    if(video_class<1) {
        err = "请选择视频分类";
    }
    if(video_cover.length<5) {
        err = "请上传封面图片";
    }
    if(video_link.length<5) {
        err = "请上传视频";
    }
    if(typeof(err) !="undefined") {
        var config = {
            headerMsg: "提示信息",
            msg:err,
            model: "tips"
        }
        tipsFn.init(config);
        return;
    }

    var formData = {video_id:video_id,video_name:video_name,video_intro:video_intro,video_class:video_class,video_cover:video_cover,video_link:video_link,video_vr:video_vr}
    $.post("/profile/video/save",formData,function(data) {
         location.href = "/profile/video?a=my&t=wait";
    });
}
</script>
@elseif($a=="history")
<script>
    pagination.init({
        type: "scroll", //type=page，普通翻页加载，scroll为滚动加载
        url: "/media/history/api", //ajaxType=ajax时为请求地址
        ajaxType:"get",
        contentHtmlTmp: '<li class="fl show-video-detail" video-id="{id}"">\
                            <a href="javascript:;">\
                                <img src="{cover}" />\
                                <p>{name}</p>\
                            </a>\
                        </li>',
        contentHtmlContainer: "#page-content",
        first_get_num:20,
        get_num:20
        //offsetTop:$(".new_webgame_con").offset().top
    });
</script>
@endif
@endsection
