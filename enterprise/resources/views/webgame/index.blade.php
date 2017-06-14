<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 17:52
 */
;?>
@inject('blade', 'App\Helper\BladeHelper')
@extends('layouts.webgame')

@section('title', '网页游戏')

{{--@include('common.errors')--}}
@section('content')
<!-- BEGIN PAGE -->
<div class="pageGame_right_con pr pageGame_con_hei pageGame_index " id="pageGame_list_scrollbar" style="display: none">
    <div class="scrollbar fr pa  pageGame_con_hei">
        <div class="track">
            <div class="thumb pa"></div>
        </div>
    </div>
    <div class="viewport pr pageGame_con_hei">
        <div class="overview">
            <div class="banner">
                <ul id="videoBanner">
                    @foreach ($banners as $banner)
                    <li class="poster-item">
                        <a href="javascript:;">
                            <img src="{{ $banner['banner_url'] }}" width="100%" height="100%">
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="pageGame_list_item">
                @if(isset($recommend_ids) && count($recommend_ids)>0)
                <div class="hot_item">
                    <div class="item_header">
                        <h4>热门游戏</h4>
                    </div>
                    <div class="hot_list">
                        <ul class="clearfix">
                            @foreach ($recommend_ids as $recommend_id)
                            <li class="fl">
                                <a href="javascript:;" class="pr">
                                    <img src="{{ $blade->webgameRes($recommend_id["content_id"],$allGame[$recommend_id["content_id"]]['img_version'],'logo') }}" >
                                    @if(strtotime($allGame[$recommend_id["content_id"]]["ctime"])>strtotime("-5 day"))
                                    <i class="new"></i>
                                    @endif
                                    <p class="pa title">{{ $allGame[$recommend_id["content_id"]]["name"] }}</p>
                                    <div class="btn pa">
                                        <div class="in_btn">
                                            <p class="in_game" game-id="{{$recommend_id["content_id"]}}" game-name="{{ $allGame[$recommend_id["content_id"]]["name"] }}">进入游戏</p>
                                            <p class="go_home showWebGameDetail" appid="{{$recommend_id["content_id"]}}">游戏首页</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="list_item">
                    <div class="item_header">
                        <h4>游戏列表</h4>
                    </div>
                    <div class="list_title clearfix btn-game-class-list">
                        <span class="fl cur"><a href="javascript:;" data-id="0">全部</a></span>
                        @foreach ($gameTypes as $k=>$type)
                        <span class="fl"><a href="javascript:;" data-id="{{ $k }}">{{ $type["name"] }}</a></span>
                        @endforeach
                    </div>
                    <div class="hot_list">
                        <ul class="clearfix category-list">
                            @foreach ($allGame as $game)
                            <li class="fl" first-class="{{ $game["first_class"] }}">
                                <a href="javascript:;" class="pr">
                                    <img src="{{ $blade->webgameRes($game['appid'],$game['img_version'],'logo') }}" >
                                    @if(strtotime($game["ctime"])>strtotime("-5 day"))
                                    <i class="new"></i>
                                    @endif
                                    <p class="pa title">{{ $game["name"] }}</p>
                                    <div class="btn pa">
                                        <div class="in_btn">
                                            <p class="in_game" game-id="{{$game["appid"]}}" game-name="{{ $game["name"] }}">进入游戏</p>
                                            <p class="go_home showWebGameDetail" appid="{{$game["appid"]}}">游戏首页</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="foot_item">
                    <div class="item_header">
                        <h4>友情链接</h4>
                    </div>
                    <div class="foot_con">
                        <ol class="clearfix">
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                            <li class="fl"><a href="javascript:;">凯英网络</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE -->
@endsection

@section('javascript')
<script type="text/javascript">
$('#getPackage').on('click',function(){
createHtml(0,'领取失败','该账号已领取过','erro'); //失败调用数据
//        createHtml(1,'领取成功','1246633scsdsd123','');  //领取成功
})
$(function() {
    var hisw;
    var hish;
    $(".btn-game-class-list a").click(function(){
        var firstli = $(".category-list li").first();
        if(typeof(hisw)=="undefined" && firstli.length>0) {
           hisw = firstli.css('width');
           hish = firstli.css('height');
        }
        var id = $(this).attr("data-id");
        $(".btn-game-class-list span").removeClass("cur");
        $(this).parent().addClass("cur");
        $(".category-list").empty();
        $.post("/webGame/list/"+id,function(res) {
            var html = ''
            $.each(res.data,function(k,v){
                html = html+'<li class="fl" style="width:'+hisw+';height:'+hish+'"><a href="javascript:;" class="pr"><img src="'+v.img+'" ><i class="new"></i><p class="pa title">'+v.name+'</p>';
                html = html+'<div class="btn pa"><div class="in_btn"><p class="in_game" game-id="'+v.appid+'" game-name="'+v.name+'">进入游戏</p> <p class="go_home showWebGameDetail" appid="'+v.appid+'">游戏首页</p></div></div></a></li>';
            })
            $(".category-list").append(html)
        },"json")
    });
})
</script>
@endsection
