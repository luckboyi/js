@inject('blade', 'App\Helper\BladeHelper')
@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 视频列表</title>
@endsection

@section("head")
<link href="{{ static_res('/vronline/style/video.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="w_1200 clearfix mb50">
<!--视频内容-->
<div class="left-wrap fl">
            <div class="list-h2">
                @if(isset($category) && count($category)>0)
                    @foreach($category as $k=>$v)
                        <a href="/vronline/video/list?class={{ $v['id'] }}" class="@if($v['id'] == $videoClass) current @endif">{{ $v['name'] }}</a>@if($k< count($category))|@endif
                    @endforeach
                @endif
            </div>
            <ul class="image-list sight-list">
                @if(isset($videoList) && count($videoList) > 0)
                    @foreach($videoList as $vk=>$vv)
                        <li>
                             <a href="@if($vv['article_tp'] === 'banner') {{ $vv['article_video_source_url'] }} @elseif($vv['article_tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$vv['article_id']}} @endif" target="_blank" title="{{ $vv['article_title'] }}">
                                <span class="img-wrap">
                                    <img src="{{ static_image($vv['article_cover']) }}">
                                <span class="play-tag"><i></i></span>
                                </span>
                                <span class="tag">{!! $blade->time2secondForVideo($vv['article_video_time']) !!}</span>
                                <span class="title">{{ $vv['article_title'] }}</span>
                                <span class="cover"></span>
                                <span class="view">{{$vv['article_view_num']}}次播放</span>
                                <em></em>
                                <b class="icon_bg2"></b>
                            </a>
                            <a href="#" target="_blank" title="下载" class="download fr"></a>
                        </li>
                    @endforeach
                @endif
			</ul>
            <div class="bigPage pt20 pb30 vm tc">
                @if($videoList->currentPage() > 1)
                    <a class="pagePrev" href="//www.vronline.com/vronline/video/list?class={{$videoClass}}&page={{$videoList->currentPage()-1}}" btnmode="true" hidefocus=""><b></b></a>
                 @endif
                @if($videoList->total() > 0)
                    @if(isset($showPages) && count($showPages)>0)
                        @foreach($showPages as $sv)
                            <a href="//www.vronline.com/vronline/video/list?class={{$videoClass}}&page={{$sv}}" btnmode="true" hidefocus="" class="@if(!$videoList->currentPage() || $sv == $videoList->currentPage()) selected @endif">{{$sv}}</a>
                        @endforeach
                    @endif
                @endif

                @if($videoList->total() > 10)
                ...
                    @for($i=$videoList->total()-1;$i<=$videoList->total();$i++)
                        <a href="//www.vronline.com/vronline/video/list?class={{$videoClass}}&page={{$i}}" btnmode="true" hidefocus="" class="@if(!$videoList->currentPage() || $i == $videoList->currentPage()) selected @endif">{{$i}}</a>
                    @endfor
                @endif

                @if($videoList->currentPage() < $videoList->total())
                    <a href="//www.vronline.com/vronline/video/list?class={{$videoClass}}&page={{$videoList->currentPage()+1}}" btnmode="true" hidefocus="" class="pageNext"><b></b></a>
                @endif

                @if($videoList->total() >0)
                    <span class="pl30 f14 c999">跳转到:</span> <input class="isTxtBig" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onafterpaste="this.value=this.value.replace(/[^0-9]/g,'')"type="text" value=""><a href="javascript:void(0);" class="pageJump">GO</a>
                @else
                    <span style="text-align: center;">暂无数据，敬请期待...</span>
                @endif
            </div>
        </div>
<!--热门推荐-->
<div class="right-wrap fr">
<div class="h2-wrap">
                    <h2>热门推荐</h2>
                    </div>
                    <ul class="activity-list mb30">
                        @if(isset($tops['video-list-recommend']) && count($tops['video-list-recommend']) > 0)
                            @foreach($tops['video-list-recommend'] as $tk=>$tv)
                                <li>
                                    <a href="@if($tv['tp'] === 'banner') {{ $tv['target_url'] }} @elseif($tv['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$tv['itemid']}} @endif" title="{{ $tv['title'] }}" target="_blank">
                                        <span class="tag"></span>
                                        <span class="tag"></span>
                                        <span class="date">{{ date('m月d日', $tv['time']) }}</span>
                                        <span class="tit dot">{{ $tv['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <!--广告-->
                    <div class="ad-bottom">
                        @if(isset($tops['video-list-adhref']) && count($tops['video-list-adhref']) > 0)
                            @foreach($tops['video-list-adhref'] as $k=>$v)
                            <a href="@if($v['tp'] === 'banner') {{ $v['target_url'] }} @elseif($v['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$v['itemid']}} @endif" title="{{ $v['title'] }}" target="_blank">
                                <img src="{{ static_image($v['cover']) }}">
                            </a>
                            @endforeach
                        @endif
                    </div>
</div>
        </div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(".pageJump").click(function(){
        var page = $(".isTxtBig").val();
        if(page > {{ $videoList->total() }}) {
            return false;
        }
        if(!page) {
            page = 1;
        }
        location.href = '//www.vronline.com/vronline/video/list?class={{$videoClass}}&page=' + page;
    });
</script>
@endsection
