@inject('blade', 'App\Helper\BladeHelper')
@extends('layouts.website')

@section('title')VRonline官网@endsection

@section('content')
<div class="home_banner home_page">
    <ul class="clearfix">
        @if(isset($indexBanner) && is_array($indexBanner))
        @foreach($indexBanner as $k=> $banner)
        <li class="fl {{$k==0?"cur":""}}">
            <a {!! $blade->handleBannerAttr($banner) !!} >
            @if(isset($banner["image"]["cover"]))
                <img src="{{static_image($banner["image"]["cover"],100)}}" >
            @endif
            </a>
        </li>
        @endforeach
        @endif
    </ul>
    <div class="thumbnail">
        <ol>
            @if(isset($indexBanner) && is_array($indexBanner))
            @foreach($indexBanner as $k=> $banner)
            <li class="{{$k==0?"active":""}}">
                <a {!!$blade->handleBannerAttr($banner,["class"=>"clearfix"])!!} >
                    <span class="fl">
                    @if(isset($banner["image"]["icon"]))
                        <img src="{{static_image($banner["image"]["icon"],54)}}" >
                    @endif
                    </span>
                    <div class="fr">
                        <h3>{{$banner["name"]}}</h3>
                        <p title="{{$banner["desc"]}}">{{$banner["desc"]}}</p>
                    </div>
                </a>
            </li>
            @endforeach
            @endif
        </ol>
    </div>
</div>
<div class="homeCon clearfix">
    <div class="fl">
        <div class="recommend">
            <div class="recommendCon clearfix">
                <div class="clearfix home_title">
                    <h3 class="fl clearfix"><i class="fl"></i><span class="fl">最新推荐</span></h3>
                </div>
                @if(isset($recommend["recommend-index"]["data"][0]))
                <div {!! $blade->handleRecommendAttr($recommend["recommend-index"]["data"][0], ["class" => "fl best"]); !!}>
                    <a href="javascript:;">
                        <div>
                            @if($recommend["recommend-index"]["data"][0]["tp"]!="video")
                            <img src="{{static_image($recommend["recommend-index"]["data"][0]["image"]["logo"],466)}}" />
                            @else
                            <img src="{{static_image($recommend["recommend-index"]["data"][0]["image"]["cover"],466)}}" />
                            @endif
                        </div>
                    </a>
                    <p class="clearfix">
                        <span class="fl">{{$recommend["recommend-index"]["data"][0]["name"]}}</span>
                        <span class="fr">{{$blade->getScoreOrNum($recommend["recommend-index"]["data"][0])}}</span>
                    </p>
                </div>
                @endif
                <?php unset($recommend["recommend-index"]["data"][0]);?>
                <ul class="fl clearfix">
                    @if (isset($recommend["recommend-index"]["data"]) && is_array($recommend["recommend-index"]["data"]))
                    @foreach($recommend["recommend-index"]["data"] as $content)
                    <li {!! $blade->handleRecommendAttr($content, ["class" => "fl"]); !!}>
                        <a href="javascript:;">
                            <div>
                                @if($content["tp"]!="video")
                                <img src="{{static_image($content["image"]["logo"],226)}}" >
                                @else
                                <img src="{{static_image($content["image"]["cover"],226)}}" >
                                @endif
                            </div>
                        </a>
                        <p class="clearfix">
                            <span class="fl">{{$content["name"]}}</span>
                            <span class="fr">{{$blade->getScoreOrNum($content)}}</span>
                        </p>
                    </li>
                    @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <!--VR游戏-->
        <div class="VRgames games_list">
            <div class="VRgames_con webgames_con">
                <div class="home_title clearfix">
                    <h3 class="fl"><i class="fl"></i><span class="fl">热门VR</span></h3>
                    <a class="fr" href="/vrgame">更多></a>
                </div>
                <ul class="clearfix">
                    @if (isset($recommend["hot-vr-game"]["data"]) && is_array($recommend["hot-vr-game"]["data"]))
                    @foreach ($recommend["hot-vr-game"]["data"] as $content)
                        <li class="fl">
                            <a href="/vrgame/{{ $content['id'] }}">
                                <div>
                                    <img src="{{static_image($content["image"]["logo"],226)}}" >
                                </div>
                            </a>
                            <p class="clearfix">
                                <span class="fl">{{$content["name"]}}</span>
                                <span class="fr"><b></b>{{$blade->getScoreOrNum($content)}}</span>
                            </p>
                        </li>
                    @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <!--<div class="webgames games_list">
            <div class="webgames_con">
                <div class="home_title clearfix">
                    <h3 class="fl"><i class="fl"></i><span class="fl">热门页游</span></h3>
                    <a class="fr" href="/webgame">更多></a>
                </div>
                <ul class="clearfix">
                    @if (isset($recommend["hot-webgame"]["data"]) && is_array($recommend["hot-webgame"]["data"]))
                    @foreach ($recommend["hot-webgame"]["data"] as $content)
                    <li class="fl start-web-game" game-id="{{$content["id"]}}" server-id=-1 game-name="{{$content["name"]}}">
                        <a href="javascript:;">
                            <div>
                                <img src="{{static_image($content["image"]["logo"],226)}}" >
                            </div>
                        </a>
                        <p class="clearfix">
                            <span class="fl">{{$content["name"]}}</span>
                            <span class="fr">{{$blade->getScoreOrNum($content)}}</span>
                        </p>
                        <p class="enter">
                            <span class="fl show-webgame-detail" appid="{{$content["id"]}}" game-name="{{$content["name"]}}">进入专区</span>
                            <span class="fr cur start-web-game" game-id="{{$content["id"]}}" server-id=-1 game-name="{{$content["name"]}}">开始游戏</span>
                        </p>
                    </li>
                    @endforeach
                    @endif
                </ul>
            </div>
        </div>-->
        <div class="videos games_list">
            <div class="webgames_con videos_con">
                <div class="home_title clearfix">
                    <h3 class="fl"><i class="fl"></i><span class="fl">热门视频</span></h3>
                    <a class="fr" href="/media">更多></a>
                </div>
                <ul class="clearfix">
                    @if(isset($recommend["hot-video"]["data"]) && is_array($recommend["hot-video"]["data"]))
                    @foreach ($recommend["hot-video"]["data"] as $content)
                    <li class="fl show-video-detail" video-id="{{$content["id"]}}">
                        <a href="javascript:;">
                            <div>
                                <img src="{{static_image($content["image"]["cover"],226)}}" >
                                <p class="mask"></p>
                                <i class="play"></i>
                            </div>
                        </a>
                        <p class="clearfix">
                            <span class="fl">{{$content["name"]}}</span>
                            <span class="fr">{{$blade->getScoreOrNum($content)}}</span>
                        </p>
                    </li>
                    @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="fr">
        <!--热门榜单-->
        <div class="hotList">
            <div class="hotList_title clearfix">
                <span class="fl cur">VR游戏</span>
                <!-- <span class="fl">网页游戏</span> -->
                <span class="fl">精彩视频</span>
            </div>
            @include("website.components.rank",["type"=>"vrgame","data"=>$recommend["vrgame-rank"]["data"]])
            <!--<div>
            include("website.components.rank",["type"=>"webgame","hide"=>1,"data"=>$recommend["webgame-rank"]["data"]])
            </div>-->
            @include("website.components.rank",["type"=>"video","hide"=>1,"data"=>$recommend["video-rank"]["data"]])
        </div>
        @include("website.components.class",["type"=>"vrgame","doClass"=>"to-list"])
        <!--开服-->
        <!--先隐藏掉页游选服 <div>
        include("website.components.server")
        </div> -->
        @include("website.components.class",["type"=>"video","doClass"=>"to-list"])
    </div>
</div>
@endsection

@section('javascript')
<script src="{{static_res('/website/js/banner.js')}}?{{Config::get('staticfiles.file_version')}}"></script>
<script type="text/javascript">
    $(".home_banner").bannerVideo();
    $(function() {
        $('.hotList_con ul li').hover(function(){
            $(this).addClass('cur').siblings().removeClass('cur')
        });
        $('.hotList_title').on('click','span',function(){
            var i = $(this).index();
            $(this).addClass('cur').siblings().removeClass('cur');
            $(this).parents('.hotList').find('.hotList_con').eq(i).addClass('cur').siblings().removeClass('cur');
        });
        //按类型选择 点击选中
        $('.screen ul li').click(function(){
            $(this).addClass('cur').siblings().removeClass('cur')
        });
    })
</script>
@endsection