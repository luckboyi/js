@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 资讯首页</title>
@endsection

@section("head")

<link href="{{ static_res('/vronline/style/information.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ static_res('/vronline/js/jquery.SuperSlide.2.1.1.js') }}"></script>
<script src="{{ static_res('/vronline/js/information.js') }}"></script>
<script src="{{ static_res('/vronline/js/vronline.js') }}"></script>
@endsection

@section('content')
<div class=" clearfix mt20">
            <div class="new-wrap" style="">
                <div class="left-wrap">
                    <div class="news-castWrap">
                        <div class="swiper-container news-cast">
                            <div class="swiper-wrapper" style="width: 3230px; height: 360px; transform: translate3d(-1938px, 0px, 0px); transition-duration: 0.3s;">
                            @foreach($tops['news-index-slider'] as $top)
                                <div class="swiper-slide swiper-slide-duplicate" style="width: 646px; height: 360px;">
                                    <a href="{{ $top['target_url'] }}" target="_blank">
                                        <img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}">
                                        <span class="txt dot">{{ $top['title'] }}</span>
                                    </a>
                                </div>
                            @endforeach
                            </div>
                            <a class="swiper-prev" id="first-prev" href="javascript:;"></a>
                            <a class="swiper-next" id="first-next" href="javascript:;"></a>
                            <div class="pagination">
                            <span class="swiper-pagination-switch"></span>
                            <span class="swiper-pagination-switch"></span>
                            <span class="swiper-pagination-switch swiper-visible-switch swiper-active-switch"></span></div>
                        </div>
                    </div>
                    <div class="three-news">
                            @foreach($tops['news-index-slider-right'] as $top)
                                <a href="{{ $top['target_url'] }}" target="_blank">
                                <img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}">
                                <span class="dot">{{ $top['title'] }}</span>
                                </a>
                            @endforeach
                    </div>
<!--资讯列表-->
                    <div class="news-nav">
                        <span class="current"><em>•</em>最新资讯</span>
                        <span data-id="1"><em>•</em>行业动态</span>
                        <span data-id="2"><em>•</em>人物专访</span>
                        <span data-id="3"><em>•</em>投资创业</span>
                        <span data-id="4"><em>•</em>数据分析</span>
                        <span data-id="5"><em>•</em>VR独家</span>
                        <span data-id="6" ><em>•</em>游戏专区</span>
                    </div>
                    <div class="news-contentWrap">
                        <div class="news-content news-current">
                            <ul class="news-list">
                            @foreach($tops['news-index-list'] as $top)
                                <li>
                                    <div class="wrap">
                                        <span class="img-wrap">
                                            <a href="{{ $top['target_url'] }}" title="{{ $top['title'] }}" target="_blank">
                                                <img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}"></a>
                                        </span>
                                        <span class="content">
                                            <a class="news-tit" href="{{ $top['target_url'] }}" title="{{ $top['title'] }}" target="_blank">专业驱动IC设计公司奇景光电或为下代iPhone提供3D感应摄像头</a>
                                            <span class="news-txt">{{ htmlSubStr($top['intro'],100) }}</span>
                                            <span class="info-wrap">
                                                <a href="/vronline/author/{{ $top['author'] }}" target="_blank">
                                                    <span class="author-img">
                                                        <img src="https://image.vronline.com/newsimg/1/6556dc3a75962a2ccdba462fae092efd1491393581882.jpg" class="author_cover" data-val="{{ $top['author'] }}">
                                                    </span>
                                                    <span class="author-name author_name" data-val="{{ $top['author'] }}"></span>
                                                </a>
                                                <span class="time">{{ date("Y-m-d",$top['time']) }}</span>
                                            </span>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                        </div>

                    </div>

                </div>
                <div class="right-wrap">
                    <div class="say-vr">
                        <a class="say-vr" href="{{ $tops['news-index-viewpoint'][0]['target_url'] }}" title="{{ $tops['news-index-viewpoint'][0]['title'] }}" target="_blank">
                            <img src="{{ static_image($tops['news-index-viewpoint'][0]['cover']) }}" alt="{{ $tops['news-index-viewpoint'][0]['title'] }}">
                            <span class="img-opacity"></span>
                            <span class="say-vrTag">VR观点</span>
                            <span class="contents">
                                <span class="tit">{{ $tops['news-index-viewpoint'][0]['title'] }}</span>
                                <span class="content">
                                     {{ $tops['news-index-viewpoint'][0]['intro'] }}
                                </span>
                            </span>
                        </a>


                    </div>
                    <div class="h2-wrap mt20">
                        <h2>资讯热点</h2>
                        <a href="/vronline/top/news-index-hot" target="_blank" class="h2-more">查看更多</a>
                    </div>
                    @foreach($tops['news-index-hot'] as $top)
                        <div class="right-smallNews">
                            <a href="{{ $top['target_url'] }}" target="_blank">
                            <span class="img-wrap"><img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}"></span>
                            <span class="tit">{{ $top['title'] }}</span>
                            </a>
                        </div>
                    @endforeach
                    <div class="h2-wrap mt20">
                        <h2>厂商新游</h2>
                    </div>
                    <ul class="team-list">
                        <li>
                            <a href="http://www.87870.com/aupublist-27.html" target="_blank">
                                <span class="img-wrap">
                                    <img src="http://pic.87870.com/upload/images/vr87870/2017/2/26/th_100x100_8b380855-c8c2-4bb2-bfc5-8fbbfea2849b.jpg" alt="">
                                </span>
                                <span class="content">
                                    <span class="tit">PSVR</span>
                                    <span class="txt" title="喜欢姑父喜欢动漫更喜欢为VR粉们奉上VR圈最新发生的那些事儿~so想要一起聊VR就关注我吧-，-">喜欢姑父喜欢动漫更喜欢为VR粉们奉上VR圈最新发生的那些事儿~so想要一起聊VR就关注我吧-，-</span>
                                    <span class="check">查看</span>
                                </span>
                            </a>
                        </li>
                     </ul>
                    @include("vronline.components.news_subject",["data"=>$tops["news-index-subject"]])
                    @include("vronline.components.news_act",["data"=>$tops["news-index-act"]])
                    <div class="h2-wrap mt20">
                        <h2>一周热点</h2>
                        <a href="/vronline/top/news-index-week" target="_blank" class="h2-more">查看更多</a>
                    </div>
                    <ul class="hot-list">
                        @foreach($tops['news-index-week'] as $key=>$top)
                        <li>
                             <a href="{{ $top['target_url'] }}" target="_blank">
                                <em class="num num3">{{ $key+1 }}</em>
                                <span class="tit">{{ $top['title'] }}</span>
                                <span class="img-wrap">
                                    <img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}">
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(function(){
        $(".hot-list li:first").addClass('current');
        $(".hot-list li").hover(function(){
            $(this).addClass('current').siblings().removeClass('current');
        });
    })
</script>
@endsection
