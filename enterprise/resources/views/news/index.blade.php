@extends('news.layout')

@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - VRonline.com</title>
@endsection

@section("head")
@endsection

@section('content')
<div class="con">
    <div class="clearfix">
        <!--左侧新闻-->
        <div class="fl con_left">
            <ul class="headlines">
                @if(isset($news["index-top"]) && is_array($news["index-top"]))
                @foreach ($news["index-top"] as $newsArr)
                @if(is_array($newsArr) && count($newsArr)>0)
                <li>
                    <a class="big_title ells" href="{{$newsArr[0]["link"]?:"javascript:;"}}" target="_blank">{{$newsArr[0]["title"]}}</a>
                    @if(count($newsArr)>1)
                    <div class="clearfix">
                        <a class="fl ells" href="{{$newsArr[1]["link"]?:"javascript:;"}}" target="_blank">{{$newsArr[1]["title"]}}</a>
                        @if(isset($newsArr[2]))
                        <a class="fr ells" href="{{$newsArr[2]["link"]?:"javascript:;"}}" target="_blank">{{$newsArr[2]["title"]}}</a>
                        @endif
                    </div>
                    @endif
                </li>
                @endif
                @endforeach
                @endif
            </ul>
            <ul class="news">
                @if(isset($news["list"]) && is_array($news["list"]))
                @foreach ($news["list"] as $value)
                <li class="clearfix">
                    <a class="fl news_head" href="/news/list/{{$value["tp"]}}" target="_blank">[<span>{{config("category.article.".$value["tp"].".name")}}</span>]</a>
                    <a class="fl news_con clearfix"  href="/news/detail/{{$value["id"]}}.html"  target="_blank">
                        <span class="fl ells">{{$value["title"]}}</span>
                        <span class="fr">{{ substr($value["vtime"],0,10) }} </span>
                    </a>
                </li>
                @endforeach
                @endif
            </ul>
        </div>
        <div class="fr con_right">
            <!--右侧轮播-->
            <div class="content" style="width:730px;height:310px; margin-bottom:20px; overflow:hidden;">
                <div id="slider">
                    @if(isset($news["index-slider"]) && is_array($news["index-slider"]))
                    @foreach ($news["index-slider"] as $value)
                    <a href="{{$value["link"]?:"javascript:;"}}" target="_blank">
                        <img src="{{static_image($value["cover"])}}"/>
                        <p>{{$value["title"]}}</p>
                    </a>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="clearfix">
                <div class="fl">
                    <div class="VR_assistant">
                        <div class="title clearfix">
                            <i class="fl title_icon"></i>
                            <h3 class="fl">VR助手</h3>
                        </div>
                        <div class="clearfix VR_assistant_con">
                            <a class="fl" href="http://www.vronline.com/vronline" target="_blank"><img src="{{ static_res('/news/images/vronlin2.png') }}"></a>
                            <div class="fr introduce">
                                <a class="text"  href="http://www.vronline.com/vronline" target="_blank">市面上最大、最全的VR游戏和VR视频资源的内容平台。独创的3D游戏VR模式，目前支持《魔兽世界》、《守望先锋》等游戏在主流VR设备上体验。</a>
                                <a class="down" href="http://www.vronline.com/vronline" target="_blank" style="margin-top: 54px">下 载</a>
                            </div>
                        </div>
                    </div>
                    <div class="hardware">
                        <div class="title clearfix">
                            <i class="fl title_icon"></i>
                            <h3 class="fl">{{$posName["index-hardware"]}}</h3>
                        </div>
                        <div>
                            @if(isset($news["index-hardware"][0]))
                            <p>
                                <a class="ells title" href="{{$news["index-hardware"][0]["link"]?:"javascript:;"}}" target="_blank">{{$news["index-hardware"][0]["title"]}}</a>
                                <span class="ells2">{{$news["index-hardware"][0]["desc"]}}</span>
                            </p>
                            @endif
                            @if(isset($news["index-hardware"][1]))
                            <div class="level_title">
                                <p class="fl">
                                    <a class="title ells" href="{{$news["index-hardware"][1]["link"]?:"javascript:;"}}" target="_blank">{{$news["index-hardware"][1]["title"]}}</a>
                                    <span class="ells2">{{$news["index-hardware"][1]["desc"]}}</span>
                                </p>
                                @if(isset($news["index-hardware"][2]))
                                <p class="fr">
                                    <a class="title ells" href="{{$news["index-hardware"][2]["link"]?:"javascript:;"}}" target="_blank">{{$news["index-hardware"][2]["title"]}}</a>
                                    <span class="ells2">{{$news["index-hardware"][2]["desc"]}}</span>
                                </p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="fr right_img">
                    <ul>
                        @if(isset($news["index-ad1"]) && is_array($news["index-ad1"]))
                        @foreach ($news["index-ad1"] as $value)
                        <li class="pr">
                            <a href="{{ $value['link'] }}" target="_blank">
                                <img src="{{static_image($value["cover"],280)}}">
                                <p class="ells">{{$value["title"]}}</p>
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="index_w_container">
        @if(isset($news["index-ad-w"]) && is_array($news["index-ad-w"]))
        @foreach ($news["index-ad-w"] as $value)
            <a href="{{ $value['link'] }}" target="_blank">
                <img src="{{static_image($value["cover"])}}">
            </a>
        @endforeach
        @endif
    </div>
    <div class="clearfix classify">
        @include("website.components.news.list1",["title"=>$posName["index-category-2"],"data"=>$news["index-category-2"],"link"=>"/news/list/2"])
        @include("website.components.news.list2",["title"=>$posName["index-category-3"],"data"=>$news["index-category-3"],"link"=>"/news/list/3"])
        @include("website.components.news.list1",["title"=>$posName["index-category-4"],"data"=>$news["index-category-4"],"link"=>"/news/list/4","class"=>"fr people"])
    </div>
    <div class="clearfix classify">
        @include("website.components.news.list1",["title"=>$posName["index-category-5"],"data"=>$news["index-category-5"],"link"=>"/news/list/5"])
        @include("website.components.news.list2",["title"=>$posName["index-category-6"],"data"=>$news["index-category-6"],"link"=>"/news/list/6","class"=>"fl Manufacturer games"])
        @include("news.components.hot",["fr"=>"1"])
    </div>
    <div class="recommend_video">
        <div class="title clearfix">
            <h3 class="fl">{{$posName["index-category-7"]}}</h3>
            <a class="fr" href="/news/list/7" target="_blank">更多</a>
        </div>
        <div>
            <ul class="clearfix">
                @if(is_array($news["index-category-7"]))
                @foreach($news["index-category-7"] as $value)
                <li class="fl pr">
                    <a href="{{$value["link"]?:"javascript:;"}}" target="_blank">
                        <img src="{{static_image($value["cover"],384)}}" />
                        <p class="play"></p>
                        <p class="mask"></p>
                        <p class="describe">{{$value["title"]}}</p>
                    </a>
                </li>
                @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="//pic.vronline.com/news/min/js/vmc.slider.full.js"></script>
<!--轮播-->
<script type="text/javascript">
    $(function() {
        $('#slider').vmcSlider({
            width: 730,
            height: 310,
            gridCol: 10,
            gridRow: 5,
            gridVertical: 20,
            gridHorizontal: 10,
            autoPlay: true,
            ascending: true,
            effects: [
                'fade', 'fadeLeft', 'fadeRight', 'fadeTop', 'fadeBottom', 'fadeTopLeft', 'fadeBottomRight',
                'blindsLeft', 'blindsRight', 'blindsTop', 'blindsBottom', 'blindsTopLeft', 'blindsBottomRight',
                'curtainLeft', 'curtainRight', 'interlaceLeft', 'interlaceRight', 'mosaic', 'bomb', 'fumes'
            ],
            ie6Tidy: false,
            random: false,
            duration: 2000,
            speed: 900
        });
    });
</script>
@endsection
