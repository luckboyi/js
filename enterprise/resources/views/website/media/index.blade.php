@inject('blade', 'App\Helper\BladeHelper')
@extends('website.media.layout')

@section('title')VRonline官网@endsection

@section('videoLeft')
<div class="webgame_right_con fr">
    @include("website.components.banner",["data"=>$recommend['video-index-banner']['data']])

    <div class="in_webgame_right_con">
        <div class="clearfix">
            <div class="fl in_webgame_left_con">
                <!--最新推荐-->
                <div class="new_webgame">
                    <div class="webgame_con_head">
                        <h3 class="blueColor pr">
                            <i class="pa good_icon"></i>
                            <span>热门推荐</span>
                        </h3>
                    </div>
                    <div class="new_webgame_con video_good_con clearfix">
                        <ul class="clearfix">
                        @if(isset($recommend["hot-video"]["data"]) && is_array($recommend["hot-video"]["data"]))
                        @foreach ($recommend["hot-video"]["data"] as $content)
                            <li class="fl pr show-video-detail" video-id="{{ $content["id"] }}">
                                <a href="javascript:;">
                                    <img src="{{static_image($content["image"]["cover"],226)}}" >
                                </a>
                                <p class="clearfix pa title">
                                    <span class="fl ells" title="{{$content["name"]}}">{{$content["name"]}}</span>
                                    <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{{$content["play"]}}</span>
                                </p>
                                <div class="shade_layer"></div>
                                <i class="play"></i>
                            </li>
                        @endforeach
                        @endif
                        </ul>
                    </div>
                </div>
                <!--最新推荐-->
            </div>
            <div class="fr video_hot_list" style="width: 228px;">
                <!--热门榜单-->
                <div class="hotList">
                    <div class="hotList_title clearfix">
                        <span class="fl cur">精彩视频</span>
                    </div>
                    @include("website.components.rank",["type"=>"video","data"=>$recommend["video-rank"]["data"]])
                </div>
            </div>
        </div>
        <!--游戏视频-->
        @if(isset($mediaCateGorys) && is_array($mediaCateGorys))
        @foreach($mediaCateGorys as $cate)
        <div class="video_list_con">
            <div class="video_lsit_head clearfix">
                 <h3 class="fl blueColor f14 pr"><i class="VR_title_icon {{ $cate['class'] }}_title_icon pa"></i><b>{{ $cate['name'] }}</b></h3>
                <span class="fr look_more show-video-class" class-id="{{ $cate["id"] }}">查看更多></span>
            </div>
            <div class="in_video_list_con clearfix">
                <ul class="clearfix ">
                @if(isset($medias[$cate['id']]) && is_array($medias[$cate['id']]))
                @foreach($medias[$cate['id']] as $content)
                    <li class="fl pr show-video-detail" video-id="{{$content["id"]}}">
                        <a href="javascript:;">
                            <img src="{{ static_image($content['image']['cover'],226) }}" >
                        </a>
                        <p class="clearfix pa title">
                            <span class="fl ells" title="{{ $content['name'] }}">{{ $content['name'] }}</span>
                            <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{{ $content['play'] }}</span>
                        </p>
                        <div class="shade_layer"></div>
                        <i class="play"></i>
                    </li>
                @endforeach
                @endif
                </ul>
            </div>
        </div>
         @endforeach
         @endif
    </div>
</div>
@endsection
@section('javascript-media')
<script src="{{static_res('/website/js/banner.js')}}"></script>
<script>
    $(function(){
        //轮播
        $('.home_banner').bannerVideo();
        $('.hotList_con ul li').hover(function(){
            $(this).addClass('cur').siblings().removeClass('cur')
        });

    })
</script>
@endsection
