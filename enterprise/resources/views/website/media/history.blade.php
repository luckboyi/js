@extends('website.media.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')历史记录@endsection

@section('videoLeft')
    <div class="multimedia_right_con fr video_play_con">
    @if(!isset($userId) || $userId == '')
        <h1 style="font-size: 16px;width: 100%;text-align: center;line-height: 250px">您还没有登录，请<a style="margin-left: 2px;color:#0096ff;font-size: 16px " href="javascript:;" class="login-btn">登录</a>！</h1>
    @elseif(isset($videoInfo) && !empty($videoInfo))
        <div class="historyRecord today">
            @if(isset($videoInfo['today']) && !empty($videoInfo['today']) && is_array($videoInfo['today']))
                <div class="title">
                    <p class="today_title">今天</p>
                </div>
                <ul class="clearfix">
                @foreach($videoInfo['today'] as $tk=>$tv)
                    <li class="fl pr playJump" data-id="{{ $tv['video_id'] }}">
                        <a href="{{ url('media/play/' . $tv['video_id'] . '&vsort=no')}}">
                            <img src="{{ static_image($tv['video_cover'], 226) }}" >
                        </a>
                        <p class="clearfix pa title">
                            <span class="fl ells" title="{{ $tv['video_name'] }}">{{ $tv['video_name'] }}</span>
                            <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{{ $tv['video_view'] }}</span>
                        </p>
                        <div class="shade_layer"></div>
                        <i class="play"></i>
                    </li>
                @endforeach
            @else
                <h3></h3>
                </ul>
            @endif
        </div>
        <div class="historyRecord week">
            @if(isset($videoInfo['week']) && !empty($videoInfo['week']) && is_array($videoInfo['week']))
                <div class="title">
                    <p class="today_title">一周内</p>
                </div>
                <ul class="clearfix">
                    @foreach($videoInfo['week'] as $wk=>$wv)
                    <li class="fl pr playJump" data-id="{{ $wv['video_id'] }}">
                        <a href="{{ url('media/play/' . $wv['video_id'] . '&vsort=no')}}">
                            <img src="{{ static_image($wv['video_cover'], 226) }}" >
                        </a>
                        <p class="clearfix pa title">
                            <span class="fl ells" title="{{ $wv['video_name'] }}">{{ $wv['video_name'] }}</span>
                            <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{{ $wv['video_view'] }}</span>
                        </p>
                        <div class="shade_layer"></div>
                        <i class="play"></i>
                    </li>
                    @endforeach
            @else
                <h3></h3>
                </ul>
            @endif
        </div>
        <div class="historyRecord earlier">
            @if(isset($videoInfo['earlier']) && !empty($videoInfo['earlier']) && is_array($videoInfo['earlier']))
                <div class="title">
                    <p class="today_title">更早</p>
                </div>
                <ul class="clearfix">
                @foreach($videoInfo['earlier'] as $ek=>$ev)
                    <li class="fl pr playJump" data-id="{{ $ev['video_id'] }}">
                        <a href="{{ url('media/play/' . $ev['video_id'] . '&vsort=no')}}">
                            <img src="{{ static_image($ev['video_cover'], 226) }}" >
                        </a>
                        <p class="clearfix pa title">
                            <span class="fl ells" title="{{ $ev['video_name'] }}">{{ $ev['video_name'] }}</span>
                            <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{{ $ev['video_view'] }}</span>
                        </p>
                        <div class="shade_layer"></div>
                        <i class="play"></i>
                    </li>
                @endforeach
            @else
                <h3></h3>
                </ul>
            @endif
        </div>
    @else
        <h1 style="font-size: 16px;width: 100%;text-align: center;line-height: 250px">您还没有浏览记录！</h1>
    @endif
    </div>
@endsection

@section('javascript-media')

    <script>
        $('#videoBanner').movingBoxes({
            startPanel   : 1,
            reducedSize  : .9,
            wrap         : true,
            buildNav     : true,
            navFormatter : function(){ return ""; } // 指示器格式，为空即会显示123
        });

        $(".login-btn").click(function(){
            var json={};
            json.referer="{{request()->url()}}"
            PL.callFun('loginframe', 'showlogin', json);
        });

        //导航条
        $('.nav ul li').each(function(){
            $(this).mouseover(function(){
                $(this).children('.con').show();
            });
            $(this).mouseout(function(){
                $(this).children('.con').hide();
            })
        });
        $(".playJump").click(function(){
            var videoId = $(this).attr("data-id");
            window.location.href="http://www.vronline.com/media/play/" + videoId + "&vsort=no";
        });
    </script>
@endsection
