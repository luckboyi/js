@extends('website.webgame.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')网页游戏@endsection

@section('webgameRight')
<div class="webgame_right_con fr">
    @include("website.components.banner",["data"=>$recommend['landspace-video-banner']['data']])
    <div class="in_webgame_right_con clearfix">
        <div class="fl in_webgame_left_con">
            <!--猜你喜欢-->
            <div class="love_webgame">
                <div class="webgame_con_head">
                    <h3 class="blueColor pr">
                    <i class="pa"></i>
                    <span>猜你喜欢</span>
                    </h3>
                </div>
                <div class="love_webgame_con">
                    <ul class="clearfix">
                        @if(isset($recommend['webgame-love']['data']) && !empty($recommend['webgame-love']['data']))
                        @foreach($recommend['webgame-love']['data'] as $info)
                        <li class="fl start-web-game" game-id="{{$info["id"]}}" appid="{{$info['id']}}" server-id=-1 game-name="{{$info["name"]}}">
                            <a href="javascript:;" class="pr clearfix">
                                <div class="fl img_con">
                                    <img src="@if(isset($love_icon[$info['id']])){{$love_icon[$info['id']]}}@else{{ static_image($info["image"]["icon"]) }}@endif" >
                                </div>
                                <div class="fl text_con">
                                    <p class="title f14">{{ $info['name'] }}</p>
                                    <p class="play_num">{{ $info['play'] }}人在玩</p>
                                </div>
                                <!--new 2016-11-19-->
                                <div class="go_btn pa">
                                    <p class="start_game cur start-web-game" game-id="{{$info["id"]}}" appid="{{$info["id"]}}" server-id=-1 game-name="{{$info["name"]}}">开始游戏</p>
                                    <p class="go_detail show-webgame-detail" appid="{{$info["id"]}}" game-name="{{$info["name"]}}">进入专区</p>
                                </div>
                                <!--new 2016-11-19-->
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <!--猜你喜欢-->
            <!--最新推荐-->
            <div class="new_webgame">
                <div class="webgame_con_head">
                    <h3 class="blueColor pr">
                    <i class="pa good_icon"></i>
                    <span>热门推荐</span>
                    </h3>
                </div>
                <div class="new_webgame_con">
                    <ul class="clearfix ">
                        @if(isset($recommend['webgame-new-recommend']['data']) && !empty($recommend['webgame-new-recommend']['data']))
                        @foreach($recommend['webgame-new-recommend']['data'] as $info)
                        <li class="fl pr start-web-game" game-id="{{$info['id']}}" appid="{{$info['id']}}" server-id=-1 game-name="{{$info['name']}}">
                            <a href="javascript:;">
                                <img src='@if(isset($hot_icon[$info['id']])){{$hot_icon[$info['id']]}}@else{{ static_image($info["image"]["logo"],226) }}@endif' >
                            </a>
                            <p class="clearfix pa title">
                                <span class="fl ells" title="{{ $info['name'] }}">{{ $info['name'] }}</span>
                                <span class="play_num fr ells" title="{{ $info['play'] }}玩过">{{ $info['play'] }}人玩过</span>
                            </p>
                            <div class="go_btn pa">
                                <p class="go_detail show-webgame-detail" appid="{{$info["id"]}}" game-name="{{$info["name"]}}">进入专区</p>
                                <p class="start_game cur start-web-game" game-id="{{$info["id"]}}" appid="{{$info['id']}}" server-id=-1 game-name="{{$info["name"]}}">开始游戏</p>
                            </div>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <!--最新推荐-->
            <!-- 活动专区-->
            <div class="active_webgame hide">
                <div class="webgame_con_head">
                    <h3 class="blueColor pr">
                    <i class="pa"></i>
                    <span>活动专区</span>
                    </h3>
                    <a class="fr blueColor f12 activeArea" href="javascript:;">换一批</a>
                </div>
                <div class="love_webgame_con">
                    <ul class="clearfix" id="activeArea">

                    </ul>
                </div>
            </div>
            <!-- 活动专区-->
            <!-- 游戏列表-->
            <div class="webgame_list">
                <div class="webgame_con_head">
                    <h3 class="blueColor pr">
                    <i class="pa"></i>
                    <span>游戏列表</span>
                    </h3>
                </div>
                <div class="webgame_list_con">
                    <ul class="clearfix" id="page-content">
                        {{-- @if(isset($firstSortInfo))
                        @foreach($firstSortInfo as $info)
                        <li class="fl pr start-web-game game-id="{{$info['appid']}}" appid="{{$info['appid']}}" server-id=-1 game-name="{{$info['name']}}">
                            <a href="javascript:;">
                                <img src="{{ static_image($info["image"]["logo"],226) }}" >
                            </a>
                            <p class="title tac ells">{{ $info['name'] }}</p>
                            <div class="go_btn pa">
                                 <p class="start_game cur start-web-game game-id="{{$info['appid']}}" server-id=-1 game-name="{{$info['name']}}">开始游戏</p>
                                <p class="go_detail show-webgame-detail" appid="{{$info["appid"]}}">进入专区</p>
                            </div>
                        </li>
                        @endforeach
                        @endif --}}
                    </ul>
                </div>
            </div>
            <!-- 游戏列表-->
        </div>
        <div class="fr right_list_con">
            <!--礼包专区-->
            <div class="gift_con">
                <div class="gift_title">
                    <span class="f14">礼包专区</span>
                    <a href="{{ url('webgame/giftList') }}" class="blueColor f12 fr giftArea">更多</a>
                </div>
                <div class="in_gift_con">
                    <ul id="giftList">
                    </ul>
                </div>
            </div>
            <!--礼包专区-->
            <!--热门榜单-->
            <div class="hotList">
                <div class="hotList_title clearfix">
                    <!-- <span class="fl cur">VR游戏</span> -->
                    <span class="fl">热门榜单</span>
                    <!--   <span class="fl">精彩视频</span> -->
                </div>
                @include("website.components.rank",["type"=>"webgame","hide"=>1,"data"=>$recommend["webgame-rank"]["data"]])
            </div>
            <!--开服-->
            @include("website.components.server")
            <!--搜索-->
           <!--  @include("website.components.class",["type"=>"webgame","doClass"=>"get-list"]) -->
        </div>

    </div>
</div>
@endsection

@section('javascript-webgame')
<!-- <script src="{{static_res('/website/js/bannerVideo.js')}}"></script> -->
<script src="{{static_res('/website/js/banner.js')}}"></script>
<script src="{{static_res('/common/js/common-my.js')}}"></script>
<script src="{{static_res('/common/js/pagination.js')}}?{{Config::get('staticfiles.file_version')}}"></script>
<script>
$(function() {
    $(".pageGame_item").delegate('.showWebGameDetail', 'click', function(event) {
        var appid = $(this).attr("appid");
        window.location.href = "/webgame/" + appid;
    });
    $(".pageGame_item").delegate('.in_game', 'click', function(event) {
        var appid = $(this).attr("game-id");
        var appname = $(this).attr("game-name");
        var json = {
            gameid: appid,
            gamename: appname,
            gameSrc: ClientConfig.Host + "/servers/" + appid
        };
        PL.callFun('webpagegamehallframe', 'openarea', json);
    });
    $('.webgame_hot').show();
    var giftList = '<?php echo urlencode(json_encode($GiftNumList)); ?>';
    var activeList = '<?php echo urlencode(json_encode($recommend['webgame-activity']['data'])); ?>';

    var giftInfo = randomArr.getRandom({
        'arry': eval(decodeURIComponent(giftList)),
        'range': 4
    });
    //console.log(giftInfo);
    $('#giftList').html(randomArr.areaHtml(giftInfo, 'gift'));
    var activeInfo = randomArr.getRandom({
        'arry': eval(decodeURIComponent(activeList)),
        'range': 12
    });
    $('#activeArea').html(randomArr.areaHtml(activeInfo, 'active'));
    console.log(eval(decodeURIComponent(activeList)));
    //activeArea
    $(".activeArea").click(function(event) {
        /* Act on the event */
        var info = randomArr.getRandom({
            'arry': eval(decodeURIComponent(activeList)),
            'range': 12
        });
        $('#activeArea').html(randomArr.areaHtml(info, 'active'));
        console.log(info);
    });

    $('.home_banner').bannerVideo();
    //领取礼包事件
    $("body").on("click", ".get_gift_btn", function() {
        var userId = "{{ $uid }}",
            gid = $(this).attr("data-gid"),
            gameName = $(this).attr("data-gameName"),
            serverId = $(this).attr("data-serverId"),
            appId = $(this).attr("data-appId");
        console.log(userId);
        if ($.trim(userId) == "" || $.trim(userId) == "undefine") {
            //window.CppCall('loginframe', 'showlogin', '');
            randomArr.createHtml(0, '领取失败', '请先登录', 'erro'); //失败调用数据
            return false;
        }
        if ($.trim(userId) == "" || $.trim(gid) == "") {
            //pubApi.showDomError($("#addErrorBox"), "非法参数");
            alert("非法参数！");
            return false;
        }

        var paramObj = {
            "action": "getCode",
            "gid": gid,
            "userid": userId,
            'appname': gameName,
            'appid': appId,
            'serverd': serverId,
        };

        var ajaxUrl = "{{ url('getGiftCode') }}";

        var jumpUrl = '',
            url = 'website/getMyPackage';
        if (/^http/i.test(url)) {
            jumpUrl = url;
        } else {
            jumpUrl = 'http://' + window.location.host + '/' + url;
        }
        pubApi.ajaxPost(ajaxUrl, paramObj, function(result) {
            if (result.code == 0) {
                randomArr.createHtml(1, '领取成功', result.data.vCode, '', jumpUrl); //领取成功
            }
        }, function(result) {
            if (result.code == 2301) { //已经领取过，也提示领取成功页面
                randomArr.createHtml(1, '领取成功', result.data.vCode, '', jumpUrl); //领取成功
            } else {
                randomArr.createHtml(0, '领取失败', result.msg, 'erro'); //失败调用数据
            }

            pubApi.showError();
        });
    });

    /*
     **关闭弹窗
     */
    $("body").on("click", "#sureBtn", function() {
        $('.gift_get').removeClass('show');
    });

    /**
     * 滚动加载内容
     */
     pagination.init({
        type: "scroll", //type=page，普通翻页加载，scroll为滚动加载
        url: "/search", //ajaxType=ajax时为请求地址
        ajaxType:"get",
        ajaxData:{
            tp:"webgame"
        },
        contentHtmlTmp:'<li class="fl pr start-web-game" game-id="{id}" appid="{id}" server-id=-1 game-name="{name}">\
                            <a href="javascript:;">\
                                <img src="{icon}">\
                            </a>\
                            <p class="title tac ells">{name}</p>\
                            <div class="go_btn pa">\
                                <p class="start_game cur start-web-game" game-id="{id}" server-id=-1 game-name="{name}">开始游戏</p>\
                                <p class="go_detail show-webgame-detail" game-name="{name}" appid="{id}">进入专区</p>\
                            </div>\
                        </li>',
        contentHtmlContainer: "#page-content",
        handleData:function(e){
            e.icon=static_image(e.image.icon);
            return e;
        },
        first_get_num:28,
        get_num:28
    });
   //开始游戏进入专区
    $('.go_btn p').hover(function(){
        $(this).addClass('cur').siblings().removeClass('cur')
    });


    $(window).scroll(function(event) {
        var sct = $(this).scrollTop();
        var scl=$(this).scrollLeft();
        var windowWidth=$(this).width();

        if(sct >= 1332){
            $('.filter-con').css({
                'position':'fixed',
                'top':'66px',
                'width':'228px'
            }).addClass('has-fixed');
        }else{
            $('.filter-con').css({
                'position':'relative',
                'top':'0',
                'left':'auto'
            }).removeClass('has-fixed');
        }

        if(windowWidth<1240){
            if(sct >= 1332){
                $('.filter-con').css({
                    'left':992-scl
                });
            }
        }else{
            $('.filter-con').css({
                'left':'auto'
            });
        }

    });
})
</script>
@endsection
