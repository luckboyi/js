@inject('blade', 'App\Helper\BladeHelper')
@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 视频首页</title>
@endsection

@section("head")
<link href="{{ static_res('/vronline/style/video.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ static_res('/vronline/js/jquery.SuperSlide.2.1.1.js') }}"></script>

@endsection

@section('content')
    <div class="slide">
      <div>
<div class="banner_slide mb30">
  <div class="w_1200 clearfix">
    <div class="cast fl pr">
                    <div class="hd pa">
                        <ul><li></li><li></li><li></li><li></li><li></li></ul>
                    </div>
                    <div class="bd pr">
                        <ul class="cast-list">
                        @if(isset($tops) && isset($tops['video-index-slider1']) && count($tops['video-index-slider1'])>0)
                          @foreach($tops['video-index-slider1'] as $k=>$slider)
                            <li><a href="@if($slider['tp'] === 'banner') {{ $slider['target_url'] }} @elseif($slider['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$slider['itemid']}} @endif" target="_blank" title="{{ $slider['title'] }}"><img src="{{ static_image($slider['cover']) }}"><h3>{{ $slider['title'] }}</h3></a></li>
                          @endforeach
                        @endif
                        </ul>
                        <div class="arrow">
                            <a class="prev pa" href="javascript:;" style="display:none"></a>
                            <a class="next pa" href="javascript:;" style="display:none"></a>
                        </div>
                    </div>
                </div>
    <div class="banner_pics fr">
        @if(isset($tops) && isset($tops['index-video']) && count($tops['index-video'])>0)
          @foreach($tops['index-video'] as $k=>$rightInfo)
            <div class="bpic tbpic pr">
              <a href="@if($rightInfo['tp'] === 'banner') {{ $rightInfo['target_url'] }} @elseif($rightInfo['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$rightInfo['itemid']}} @endif">
                <img src="{{ static_image($rightInfo['cover']) }}" alt="{{ $rightInfo['title'] }}" width="399" height="195">
                <h3>{{ $rightInfo['title'] }}</h3>
              </a>
            </div>
          @endforeach
        @endif
    </div>
  </div>
</div></div>
    </div>

<!-- 内容 -->
<div class="column-content w_1200 mb20 clearfix" id="part_1">
  <div class="common pr clearfix mb10">
    <h2>原创栏目</h2><!-- <a target="_blank" href="#">VR一周曝</a>/<a target="_blank" href="#">VR任意门</a>/<a target="_blank" href="#">颤抖吧路人</a> --><a class="h2-more" target="_blank" href="//www.vronline.com/vronline/video/list?class=1">查看全部</a>
  </div>
  @if(isset($tops) && isset($tops['video-index-original']) && count($tops['video-index-original'])>0)
        @foreach($tops['video-index-original'] as $k=>$original)
          @if($k < 1)
          <div class="column-large pr">
            <a href="@if($original['tp'] === 'banner') {{ $original['target_url'] }} @elseif($original['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$original['itemid']}} @endif" target="_blank" title="{{ $original['title'] }}"><img class="img" src="{{ static_image($original['cover']) }}"><span class="cover"></span><span class="title">{{$original['title'] }}</span><span>@if(isset($original['tag']) && count($original['tag'])>0) {!! $blade->getVideoTags($original['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a>
          </div>
          @endif
        @endforeach
  @endif
  <ul class="column-list fr">
  @if(isset($tops) && isset($tops['video-index-original']) && count($tops['video-index-original'])>0)
        @foreach($tops['video-index-original'] as $k=>$original)
          @if($k > 0)
            <li><a href="@if($original['tp'] === 'banner') {{ $original['target_url'] }} @elseif($original['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$original['itemid']}} @endif" target="_blank" title="{{ $original['title'] }}"><img src="{{ static_image($original['cover']) }}"><span class="title">{{ $original['title'] }}</span><span class="cover"></span><span>@if(isset($original['tag']) && count($original['tag'])>0) {!! $blade->getVideoTags($original['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a></li>
        @endif
      @endforeach
  @endif
  </ul>
</div>

<div class="column-content w_1200 mb20 clearfix" id="part_2">
  <div class="common pr clearfix mb10">
    <h2>3D播播</h2><!-- <a target="_blank" href="#">VR一周曝</a>/<a target="_blank" href="#">VR任意门</a>/<a target="_blank" href="#">颤抖吧路人</a> --><a class="h2-more" target="_blank" href="//www.vronline.com/vronline/video/list?class=2">查看全部</a>
  </div>
  @if(isset($tops) && isset($tops['video-index-3dbb']) && count($tops['video-index-3dbb'])>0)
    @foreach($tops['video-index-3dbb'] as $k=>$dbb)
      @if($k < 1)
        <div class="column-large pr">
          <a href="@if($dbb['tp'] === 'banner') {{ $dbb['target_url'] }} @elseif($dbb['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$dbb['itemid']}} @endif" target="_blank" title="{{ $dbb['title'] }}"><img class="img" src="{{ static_image($dbb['cover']) }}"><span class="cover"></span><span class="title">{{ $dbb['title'] }}</span><span>@if(isset($dbb['tag']) && count($dbb['tag'])>0) {!! $blade->getVideoTags($dbb['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a>
        </div>
      @endif
    @endforeach
  @endif

  <ul class="column-list fr">
  @if(isset($tops) && isset($tops['video-index-3dbb']) && count($tops['video-index-3dbb'])>0)
    @foreach($tops['video-index-3dbb'] as $k=>$dbb)
      @if($k > 0)
        <li><a href="@if($dbb['tp'] === 'banner') {{ $dbb['target_url'] }} @elseif($dbb['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$dbb['itemid']}} @endif" target="_blank" title="{{ $dbb['title'] }}"><img src="{{ static_image($dbb['cover']) }}"><span class="title">{{ $dbb['title'] }}</span><span class="cover"></span><span>@if(isset($dbb['tag']) && count($dbb['tag'])>0) {!! $blade->getVideoTags($dbb['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a></li>
      @endif
    @endforeach
  @endif
  </ul>
</div>

<div class="column-content w_1200 mb20 clearfix" id="part_3">
  <div class="common pr clearfix mb10">
    <h2>游戏试玩</h2><!-- <a target="_blank" href="#">VR一周曝</a>/<a target="_blank" href="#">VR任意门</a>/<a target="_blank" href="#">颤抖吧路人</a> --><a class="h2-more" target="_blank" href="//www.vronline.com/vronline/video/list?class=3">查看全部</a>
  </div>
  @if(isset($tops) && isset($tops['video-index-games']) && count($tops['video-index-games'])>0)
    @foreach($tops['video-index-games'] as $k=>$games)
      @if($k < 1)
        <div class="column-large pr">
          <a href="@if($games['tp'] === 'banner') {{ $games['target_url'] }} @elseif($games['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$games['itemid']}} @endif" target="_blank" title="{{ $games['title'] }}"><img class="img" src="{{ static_image($games['cover']) }}"><span class="cover"></span><span class="title">{{ $games['title'] }}</span><span>@if(isset($games['tag']) && count($games['tag'])>0) {!! $blade->getVideoTags($games['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a>
        </div>
      @endif
    @endforeach
  @endif

  <ul class="column-list fr">
  @if(isset($tops) && isset($tops['video-index-games']) && count($tops['video-index-games'])>0)
    @foreach($tops['video-index-games'] as $k=>$games)
      @if($k > 0)
        <li><a href="@if($games['tp'] === 'banner') {{ $games['target_url'] }} @elseif($games['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$games['itemid']}} @endif" target="_blank" title="{{ $games['title'] }}"><img src="{{ static_image($games['cover']) }}"><span class="title">{{ $games['title'] }}</span><span class="cover"></span><span>@if(isset($games['tag']) && count($games['tag'])>0) {!! $blade->getVideoTags($games['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a></li>
      @endif
    @endforeach
  @endif
  </ul>
</div>

<div class="column-content w_1200 mb20 clearfix" id="part_4">
  <div class="common pr clearfix mb10">
    <h2>硬件评测</h2><!-- <a target="_blank" href="#">VR一周曝</a>/<a target="_blank" href="#">VR任意门</a>/<a target="_blank" href="#">颤抖吧路人</a> --><a class="h2-more" target="_blank" href="//www.vronline.com/vronline/video/list?class=4">查看全部</a>
  </div>
  <ul class="column-list-f fl">
    @if(isset($tops) && isset($tops['video-index-headwear']) && count($tops['video-index-headwear'])>0)
      @foreach($tops['video-index-headwear'] as $k=>$headwear)
        <li><a href="@if($headwear['tp'] === 'banner') {{ $headwear['target_url'] }} @elseif($headwear['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$headwear['itemid']}} @endif" target="_blank" title="{{ $headwear['title'] }}"><img src="{{ static_image($headwear['cover']) }}"><span class="title">{{ $headwear['title'] }}</span><span class="cover"></span><span>@if(isset($headwear['tag']) && count($headwear['tag'])>0) {!! $blade->getVideoTags($headwear['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a></li>
      @endforeach
    @endif
  </ul>
</div>

<div class="column-content w_1200 mb20 clearfix" id="part_5">
  <div class="common pr clearfix mb10">
    <h2>报道专访</h2><!-- <a target="_blank" href="#">VR一周曝</a>/<a target="_blank" href="#">VR任意门</a>/<a target="_blank" href="#">颤抖吧路人</a> --><a class="h2-more" target="_blank" href="//www.vronline.com/vronline/video/list?class=5">查看全部</a>
  </div>
  <ul class="column-list-f fl">
    @if(isset($tops) && isset($tops['video-index-reported']) && count($tops['video-index-reported'])>0)
      @foreach($tops['video-index-reported'] as $k=>$reported)
        <li><a href="@if($reported['tp'] === 'banner') {{ $reported['target_url'] }} @elseif($reported['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$reported['itemid']}} @endif" target="_blank" title="{{ $reported['title'] }}"><img src="{{ static_image($reported['cover']) }}"><span class="title">{{ $reported['title'] }}</span><span class="cover"></span><span>@if(isset($reported['tag']) && count($reported['tag'])>0) {!! $blade->getVideoTags($reported['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a></li>
      @endforeach
    @endif
  </ul>
</div>

<div class="column-content w_1200 mb20 clearfix" id="part_6">
  <div class="common pr clearfix mb10">
    <h2>VR视界</h2><!-- <a target="_blank" href="#">VR一周曝</a>/<a target="_blank" href="#">VR任意门</a>/<a target="_blank" href="#">颤抖吧路人</a> --><a class="h2-more" target="_blank" href="//www.vronline.com/vronline/video/list?class=6">查看全部</a>
  </div>
  <ul class="column-list-f fl">
    @if(isset($tops) && isset($tops['video-index-vreyeshot']) && count($tops['video-index-vreyeshot'])>0)
      @foreach($tops['video-index-vreyeshot'] as $k=>$eyeshot)
        <li><a href="@if($eyeshot['tp'] === 'banner') {{ $eyeshot['target_url'] }} @elseif($eyeshot['tp'] === 'video') //www.vronline.com/vronline/video/detail/{{$eyeshot['itemid']}} @endif" target="_blank" title="{{ $eyeshot['title'] }}"><img src="{{ static_image($eyeshot['cover']) }}"><span class="title">{{ $eyeshot['title'] }}</span><span class="cover"></span><span>@if(isset($eyeshot['tag']) && count($eyeshot['tag'])>0) {!! $blade->getVideoTags($eyeshot['tag']) !!}  @endif</span><em></em><b class="icon_bg2"></b></a></li>
      @endforeach
    @endif
  </ul>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
  //单张轮播
    var $cast = $(".cast");
    $cast.slide({
        mainCell: ".cast-list",
        titCell:".hd ul",
        effect: "leftLoop",
        autoPage:"<li></li>",
        autoPlay: true,
        interTime:"3000",
        prevCell:".arrow .prev",
        nextCell:".arrow .next"
    });
    if($cast.find("li").size() > 3){
        $cast.hover(function() {
            $(this).find(".prev,.next").stop(true, true).fadeIn(300)
        }, function() {
            $(this).find(".prev,.next").fadeOut(300)
        });
    }
</script>
@endsection
