@inject('blade', 'App\Helper\BladeHelper')
@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 搜索结果</title>
@endsection

@section("head")

<link href="{{ static_res('/vronline/style/information.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ static_res('/vronline/style/gamedetail.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ static_res('/vronline/js/vronline.js') }}"></script>
@endsection

@section('content')
    <div class=" clearfix">
        <div class="new-wrap">
            @if(isset($category))
                 <div class="arrow hy-arrow">
                   <a href="/vronline/index">首页</a><span>&gt;</span></span><a href="/vronline/article/list/{{ $category['id'] }} ">{{ $category['name'] }}</a><span>
                </div>
            @elseif(isset($authorId))
              <div class="arrow search">
                作者为<span class="red author_name" data-val="{{ $authorId }}"></span>的结果如下
                </div>
            @elseif(isset($total))
              <div class="arrow search">
                查找到<span class="red">{{ $total }}</span>条结果
                </div>
            @endif


            <div class="left-wrap">
                <!--游戏评测列表-->
               <div class="newsList-nav"></div>
                <div class="news-contentWrap">
                    <div class="news-content news-current">
                        <ul class="news-list">
                            @if(isset($articles))
                            @foreach($articles as $article)
                            <li>
                            <div class="wrap">
                            <span class="img-wrap">
                            <a href="/vronline/article/detail/{{ $article['itemid'] }}" title="{{ $article['title'] }}" target="_blank">
                            <img src="{{ static_image($article['cover']) }}" alt="{{ $article['title'] }}"><em class="label">{{ $blade->showSerachTp($article['tp']) }}</em></a>
                            </span>
                            <span class="content">
                            <a class="news-tit" href="/vronline/article/detail/{{ $article['itemid'] }}"" title="{{ $article['title'] }}" target="_blank">{{ $article['title'] }}</a>
                            <span class="news-txt">{{ htmlSubStr($article['intro'],100) }}</span>
                            <span class="info-wrap">
                            @if(isset($article['author']))
                            <a href="/vronline/author/{{ $article['author'] }}" target="_blank">
                            <span class="author-img">
                            <img src="https://image.vronline.com/newsimg/1/6556dc3a75962a2ccdba462fae092efd1491393581882.jpg" class="author_cover" data-val="{{ $article['author'] }}">
                            </span>
                            <span class="author-name author_name" data-val="{{ $article['author'] }}"></span>
                            </a>
                            @endif
                            <span class="time">{{ date("Y-m-d",$article['time']) }}</span>

                            <span class="eqpt">
                                @if(isset($article['device']) && $article['device'])
                                    <?php
$icons = $blade->handleDeviceIconSuper($article['device'], "www_icon_class");
?>
                                @foreach($icons as $icon)
                                <i class="icon {{$icon}}"></i>
                                @endforeach
                            @endif
                            </span>
                            </span>
                            </span>
                            </div>
                            </li>
                            @endforeach
                            @elseif($topArticles)
                            @foreach($topArticles as $article)
                            <li>
                            <div class="wrap">
                            <span class="img-wrap">
                            <a href="{{ $article['target_url'] }}" title="{{ $article['title'] }}" target="_blank">
                            <img src="{{ static_image($article['cover']) }}" alt="{{ $article['title'] }}"><em class="label">行业</em></a>
                            </span>
                            <span class="content">
                            <a class="news-tit" href="{{ $article['target_url'] }}"" title="{{ $article['title'] }}" target="_blank">{{ $article['title'] }}</a>
                            <span class="news-txt">{{ htmlSubStr($article['intro'],100) }}</span>
                            <span class="info-wrap">
                            <span class="time">{{ date("Y-m-d",$article['time']) }}</span>
                            </span>
                            </span>
                            </div>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                @if(isset($pageObj))
                <div class="bigPage pt20 pb30 vm tc">
                 {!!  $pageObj->render()!!}
                </div>
                 @endif
            </div>
        </div>
    </div>
    <div class="right-wrap">
        <!--广告-->
        <div class="ad-top mb30">
        @foreach($tops['search-ad1'] as $top)
            <a href="{{ $top['target_url'] }}" title="{{ $top['title'] }}" target="_blank">
            <img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}">
        </a>
        @endforeach

        </div>
        <!--最新评测-->
        <div class="mod mb30">
            <div class="tt1 title">最新评测</div>
            <div class="bd">
                <ul class="ptlist">
                    @foreach($tops['pc-new'] as $top)
                    <li class="item">
                    <div class="pic"><a href="#" target="_blank"><img src="{{ static_image($top['cover']) }}" width="135" height="80" alt="{{ $top['title'] }}"></a></div>
                    <div class="text">
                    <h3 class="tit"><a href="{{ $top['target_url'] }}" target="_blank">{{ $top['title'] }}</a></h3>
                    <p class="dec"><span class="date">{{ date("Y-m-d",$top['time']) }}</span><span class="score">评分：<em>{{ $top['score'] }}</em></span></p>
                    </div>
                    </li>
                    @endforeach
                 </ul>
            </div>
        </div>
        <!--VR游戏人气-->
        @if(isset($tops['game-rank-vrgame-mostpv']) && is_array($tops['game-rank-vrgame-mostpv']))
        <div class="mod mb20">
            <div class="tt1 title">VR游戏人气
            <div class="game-tab">
                <span class="on">热门人气<i></i></span>
            </div></div>
            <div class="game-main">
                <ul class="game-list">
                    @foreach($tops['game-rank-vrgame-mostpv'] as $key=>$top)
                    <?php
if ($top['intro'] == "1" || $key == 0) {
    $flag      = "&uarr;";
    $flagclass = "aico-up";
} else if ($top['intro'] == "2") {
    $flag      = "&darr;";
    $flagclass = "aico-down";
} else {
    $flag      = "&minus;";
    $flagclass = "aico-flat";
}
$rankclass = "";
if ($key < 3) {
    $rankclass = "num" . ($key + 1);
}
?>
                        <li><span class="info-wrap"><span class="num {{$rankclass}} tc">{{ $key+1 }} </span>
                        <span class="pic"><img src="{{ static_image($top['cover'],'1-72-52') }}" alt="{{ $top['title'] }}" ></span>
                        <span class="tit"><span class="tit1"><a href="{{ $top['target_url'] }}" target="_blank">{{ $top['title'] }}</a></span>
                        <br><a href="{{ $top['target_url'] }}" target="_blank" target="_blank" title="{{ $top['title'] }}">下载</a>
                        <span class="sx">|</span><span class="eqpt">{!! $blade->handleDeviceIcon($top['device']) !!}</span></span><span class="score tr">{{ $top['weight'] }}<i class="aico {{$flagclass}}"></i></span></span></li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection
