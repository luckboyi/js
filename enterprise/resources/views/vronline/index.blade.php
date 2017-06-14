@inject('blade', 'App\Helper\BladeHelper')
@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 首页</title>
@endsection

@section("head")
<link href="{{ static_res('/vronline/style/index.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ static_res('/vronline/js/jquery.SuperSlide.2.1.1.js') }}" type="text/javascript"></script>
<script src="{{ static_res('/vronline/js/home.js') }}" type="text/javascript"></script>
<script src="{{ static_res('/vronline/js/slideshow.js') }}" type="text/javascript"></script>
<script src="{{ static_res('/vronline/js/index_righenav.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="pn pn-news clearfix w_1100 mb30" id="part_1">
      <div class="col1 fr">
       <div class="topnew forsetLink11 tc">
       <h2 class="tit"><a href="{{ $tops['index-top'][0]['target_url'] }}" target="_blank" class="orange-dark">{{ $tops['index-top'][0]['title'] }}</a></h2>
        <p>
          <a href="{{ $tops['index-top'][1]['target_url'] }}" target="_blank">{{ $tops['index-top'][1]['title'] }}</a> <span class="sep">|</span>
          <a href="{{ $tops['index-top'][2]['target_url'] }}" target="_blank">{{ $tops['index-top'][2]['title'] }}</a> <span class="sep">|</span>
          <a href="{{ $tops['index-top'][3]['target_url'] }}" target="_blank">{{ $tops['index-top'][3]['title'] }}</a> <span class="sep">|</span>
          <a href="{{ $tops['index-top'][4]['target_url'] }}" target="_blank">{{ $tops['index-top'][4]['title'] }}</a>
        </p>
        <h2 class="tit"><a href="{{ $tops['index-top'][5]['target_url'] }}" target="_blank" class="orange-dark">{{ $tops['index-top'][5]['title'] }}</a></h2>
        <p>
        <a href="{{ $tops['index-top'][6]['target_url'] }}" target="_blank">{{ $tops['index-top'][6]['title'] }}</a> <span class="sep">|</span>
        <a href="{{ $tops['index-top'][7]['target_url'] }}" target="_blank"> {{ $tops['index-top'][7]['title'] }} </a> <span class="sep">|</span>
        <a href="{{ $tops['index-top'][8]['target_url'] }}" target="_blank">{{ $tops['index-top'][8]['title'] }}</a></p></div>
       <div class="mod forsetLink12">
        <div class="component cms-icon-code" cms-data-type="html">
         <div class="hd">
          <h3 class="tit">今日要闻</h3>
          <div class="more fr">
           <a href="/vronline/tag/今日要闻" >更多<i class="ico ico-more"></i></a>
          </div>
         </div>
        </div>
        <div class="bd">
         <ul class="list">
         @foreach($topNews[0] as $top)
            <li class="item">
            <div class="c1">{{ date("Y-m-d",$top['time']) }}</div>
            <div class="c2">
              <div class="tit">
                <a href="/vronline/article/detail/{{ $top['itemid'] }}" target="_blank" class="">{{ $top['title'] }}</a>
              </div>
            </div>
          </li>
          @endforeach
      </ul>
        </div>
       </div>
       <div class="mod forsetLink15">
        <div class="component cms-icon-code" cms-data-type="html">
         <div class="hd">
          <h3 class="tit">国内热点</h3>
          <div class="more fr">
           <a href="/vronline/tag/国内热点" >更多<i class="ico ico-more"></i></a>
          </div>
         </div>
        </div>
        <div class="bd forsetLink15">
         <ul class="list">
        @foreach($topNews[1] as $top)
          <li class="item">
          <div class="c1">{{ date("Y-m-d",$top['time']) }}</div>
          <div class="c2">
            <div class="tit">
              <a href="/vronline/article/detail/{{ $top['itemid'] }}" target="_blank" class="">{{ $top['title'] }}</a>
            </div>
          </div>
        </li>
        @endforeach
        </ul>
        </div>
       </div>
       <div class="mod forsetLink17">
        <div class="component cms-icon-code" cms-data-type="html">
         <div class="hd">
          <h3 class="tit">海外热点</h3>
          <div class="more fr">
           <a href="/vronline/tag/海外热点" >更多<i class="ico ico-more"></i></a>
          </div>
         </div>
        </div>
        <div class="bd">
         <ul class="list">

        @foreach($topNews[2] as $top)
          <li class="item">
          <div class="c1">{{ date("Y-m-d",$top['time']) }}</div>
          <div class="c2">
            <div class="tit">
              <a href="/vronline/article/detail/{{ $top['itemid'] }}" target="_blank" class="">{{ $top['title'] }}</a>
            </div>
          </div>
        </li>
        @endforeach
        </ul>
        </div>
       </div>
       <div class="forsetLink18">
        <a href="/vronline/article"  class="btn bnt-new-more ">+ 更多新闻</a>
       </div>
      </div>
      <div class="col2 fl">
       <div class="idx-focus" id="j_idx_focus" monkey="idxfocus">
  <div class="idx-foc-tmp">
    <ul class="focus-pic" rel="xtaberItems">
         @foreach($tops['index-slider'] as $top)
            <li class="xtaber-item">
            <a href="{{ $top['target_url'] }}" class="white" target="_blank" title="{{ $top['title'] }}">
            <img alt="" src="{{ static_image($top['cover']) }}" width="365" height="395" data-ui-mark="img">
            <span class="txt tc">{{ $top['title'] }}</span> <i class="bg"></i></a>
            </li>
        @endforeach
    </ul>
  </div>
  <ul rel="xtaberTabs" class="xtaber-tabs">
    @foreach($tops['index-slider'] as $top)
    <li rel="xtaberTabItem"><a href="{{ $top['target_url'] }}" title="{{ $top['title'] }}">
    <i></i><img alt="" src="{{ static_image($top['cover']) }}" width="365" height="395" data-ui-mark="img"></a></li>
    @endforeach
  </ul>

  <a href="javascript:;" class="btn-prev"></a>
  <a href="javascript:;" class="btn-next"></a>
</div>
       <div class="mod mod-vrbk forsetLink43">
        <div class="hd">
         <h3 class="tit"><i class="ico-hd"></i>VR热点</h3>
         <div class="btn-refresh setlink-keyword" onclick="refreshVrHot()">
          <i class="ico ico-bk-refresh" ></i>换一批
         </div>
        </div>
        <div class="bd randomData" >
        @foreach($tops['index-vr-hot'] as $top)
        <a href="{{ $top['target_url'] }}" class="link">{{ $top['title'] }}</a>
        @endforeach
        </div>
       </div>
       <div class="forsetLink20">
        <ul class="plist plist-video ">


      @foreach($tops['index-video-hot'] as $top)
      <li class="item">
      <a href="{{ $top['target_url'] }}" target="_blank" class="art-item">
      <span class="avatar">
      <img src="{{ static_image($top['cover']) }}" width="180" height="100" alt="">
      <i class="ico ico-play"></i>
      </span>
      <span class="tit">{{ $top['title'] }}</span>
      </a>
      </li>
      @endforeach

</ul>
       </div>
      </div>
     </div>
<!-- 原创栏目 -->
<div class="pn-special mb30 pt10" id="part_2">
      <div class="mod w_1100">
       <div class="hd">
        <h3 class="tit"><i class="ico-hd"></i>VRONLINE原创专栏</h3>
        <div class="more fr">
         <a href="/vronline/top/index-vr-special">更多<i class="ico ico-more"></i></a>
        </div>
       </div>
       <div class="column-wrap">
            <div class="column-cast">
                <ul>
      @foreach($tops['index-vr-special'] as $top)
        <li><a href="{{ $top['target_url'] }}" target="_blank" class="avatar"><span class="img-wrap"><img src="{{ static_image($top['cover']) }}" width="204" height="158" alt=""></span><span class="tbox tc"></span><span class="column-title"><span class="date tc">3月10日</span>{{ $top['title'] }}</a></span></span></li>
      @endforeach
                </ul>
            </div>
            <a class="column-prev" href="javascript:;"><i></i></a>
            <a class="column-next" href="javascript:;"><i></i></a></div>
      </div>
     </div>
<!-- 每日热图 -->
<div class="pn pn-orz w_1100 mb30" id="part_3">
      <div class="mod">
       <div class="hd">
        <h3 class="tit"><i class="ico-hd"></i>VRONLINE每日热图</h3>
        <div class="more fr">
         <a href="/vronline/top/index-pic" target="_blank">更多<i class="ico ico-more"></i></a>
        </div>
       </div>
       <div class="bd">
        <div class="plist orz-photo">
          @foreach($tops['index-pic'] as $key=>$top)
          <div class="item item{{ $key+1 }}">
            <a href="{{ $top['target_url'] }}" target="_blank" class="con">
              <img src="{{ static_image($top['cover']) }}" width="100%" height="100%" alt="">
              <span class="tit">
                {{ $top['title'] }}
                <b class="mask"></b>
              </span>
            </a>
          </div>
          @endforeach

        </div>
       </div>
      </div>
     </div>
<!-- VR游戏 -->
<div class="mod w_1100 mb30 first-screen" id="part_4">
  <div class="hd">
    <h3 class="tit"><i class="ico-yx"></i>VR游戏</h3>
  </div>
  <div class="main">

  <div class="qwpc ycsp">
    <div class="f-left">
      <div class="tt1 title">VR游戏评测、攻略</div>
        <a class="yc" href="{{ $tops['index-vrgame-pic'][0]['target_url'] }}" target="_blank">
          <div class="avatar">
            <img src="{{ static_image($tops['index-vrgame-pic'][0]['cover']) }}">
            <div class="cover">{{ $tops['index-vrgame-pic'][0]['title'] }}</div>
          </div>
        </a>
        <div class="col1-list">
        <ul>
        @foreach($tops['index-vrgame-pic'] as $key=>$top)
        <?php
          if($key==0) continue;
        ?>
        <li><a href="{{ $top['target_url'] }}" target="_blank">
        <div class="avatar"><img src="{{ static_image($top['cover']) }}" width="180" height="100">
        </div>
        <p>{{ $top['title'] }}</p></a>
        </li>
        @endforeach
        </ul>
        </div>
      </div>
      <div class="f-mid">
        <div class="tt1 title">VR新游速递 <a href="/vronline/top/index-vrgame-new" target="_blank" class="fr">更多<i class="ico ico-more"></i></a></div>
        <div class="col2-list">
          <ul>
          @foreach($tops['index-vrgame-new'] as $top)
          <li><a href="{{ $top['target_url'] }}" target="_blank">
          <div class="fl"><img width="180" height="100" src="{{ static_image($top['cover']) }}" style="display: inline;">
          </div>
          <div class="fr">
          <p class="tit">{{ $top['title'] }}</p>
          <p class="cont">{{ htmlSubStr($top['intro'],40) }}</p></div></a>
          </li>
          @endforeach
          </ul>
        </div>
        <div class="tt1 title">VR游戏新闻 <a href="/vronline/top/index-vrgame-news" target="_blank" class="fr">更多<i class="ico ico-more"></i></a></div>
          <div class="news-list">
            @foreach($tops['index-vrgame-news'] as $key=>$top)
            @if($key==0) 
            <div class="title"><a href="{{ $top['target_url'] }}" target="_blank">{{ $top['title'] }}</a> </div>
            @else
            <p><span class="time">{{ date("Y-m-d",$top['time']) }}</span><span class="cont"><a href="{{ $top['target_url'] }}" target="_blank"><i></i>{{ $top['title'] }}</a></span></p>
            @endif
            @endforeach
           </div>
        </div>
      </div>
    </div>
    <div class="side">
      <div class="mod1">
          <div class="tt1 title">VR游戏榜单
          <div class="game-tab">
              <a href="javascript:;" class="on">热门榜<i></i></a>
              <a href="javascript:;">发售榜<i></i></a>
          </div></div>
          <div class="game-main">
              <ul class="game-list">
                @foreach($tops['index-game-hot-rank'] as $key=>$top)
                <?php
                    $icons = $blade->handleDeviceIconSuper($top['device'], "www_icon_class");
                ?>
                  <li>
                    <span class="info-wrap">
                      <span class="num num{{$key+1}} tc">{{$key+1}}</span>
                      <span class="pic"><img src="{{ static_image($top['cover']) }}" width="72" height="52"></span>
                      <span class="tit">
                        <span class="tit1"><a href="{{ $top['target_url'] }}" target="_blank">{{ $top['title'] }}</a></span>
                        <br>
                        <a href="{{ $top['target_url'] }}" target="_blank" title="{{ $top['title'] }}">下载</a>
                        <span class="sx">|</span>
                        <span>设备：
                            @if(isset($icons) && is_array($icons))
                              @foreach($icons as $icon)
                            <i class="icon {{$icon}}"></i>
                              @endforeach
                            @endif
                        </span>
                      </span>
                      <span class="score tc">{{ $top['intro'] }}</span>
                    </span>
                  </li>
               @endforeach
              </ul>
              <ul class="game-list">
                @foreach($tops['index-game-sell-rank'] as $key=>$top)
                <?php
                    $icons = $blade->handleDeviceIconSuper($top['device'], "www_icon_class");
                ?>
                  <li>
                    <span class="info-wrap">
                      <span class="num num{{$key+1}} tc">{{$key+1}}</span>
                      <span class="pic"><img src="{{ static_image($top['cover']) }}" width="72" height="52"></span>
                      <span class="tit">
                        <span class="tit1"><a href="{{ $top['target_url'] }}" target="_blank">{{ $top['title'] }}</a></span>
                        <br>
                        <a href="{{ $top['target_url'] }}" target="_blank" title="{{ $top['title'] }}">下载</a>
                        <span class="sx">|</span>
                        <span>设备：
                            @if(isset($icons) && is_array($icons))
                              @foreach($icons as $icon)
                            <i class="icon {{$icon}}"></i>
                              @endforeach
                            @endif
                        </span>
                      </span>
                      <span class="score tc">{{ $top['intro'] }}</span>
                    </span>
                  </li>
               @endforeach
              </ul>

          </div>
      </div>
      <div class="mod1">
        <div class="tt1 title">热点专题</div>
          <div class="hot">
            @foreach($tops['index-vrgame-subject'] as $top)
            <a href="{{ $top['target_url'] }}" target="_blank">
            <div class="avatar"><img width="300" height="169" src="{{ static_image($top['cover']) }}">
            </div>
            </a>
            @endforeach
          </div>
        </div>
  </div>
</div>
<!-- VR视频 -->
<div class="mod w_1100 mb30 first-screen" id="part_5">
  <div class="hd">
    <h3 class="tit"><i class="ico-sp"></i>VR视频</h3>
  </div>
  <div class="main">

  <div class="qwpc ycsp">
    <div class="f-left">
      <div class="tt1 title">VR最新视频</div>
        <a class="yc" href="{{ $tops['index-video-pic'][0]['target_url'] }}" target="_blank">
          <div class="avatar">
            <img src="{{ static_image($tops['index-video-pic'][0]['cover']) }}">
            <div class="cover">{{ $tops['index-video-pic'][0]['title'] }}</div>
          </div>
        </a>
        <div class="col1-list">
        <ul>
        @foreach($tops['index-video-pic'] as $key=>$top)
        <?php
          if($key==0) continue;
        ?>
        <li><a href="{{ $top['target_url'] }}" target="_blank">
        <div class="avatar"><img src="{{ static_image($top['cover']) }}" width="180" height="100">
        </div>
        <p>{{ $top['title'] }}</p></a>
        </li>
        @endforeach
        </ul>
        </div>
      </div>
      <div class="f-mid">
        <div class="tt1 title">3D播播推荐视频 <a href="/vronline/top/index-video-new" target="_blank" class="fr">更多<i class="ico ico-more"></i></a></div>
        <div class="col2-list">
          <ul>
          @foreach($tops['index-video-new'] as $top)
          <li><a href="{{ $top['target_url'] }}" target="_blank">
          <div class="fl"><img width="180" height="100" src="{{ static_image($top['cover']) }}" style="display: inline;">
          </div>
          <div class="fr">
          <p class="tit">{{ $top['title'] }}</p>
          <p class="cont">{{ htmlSubStr($top['intro'],40) }}</p></div></a>
          </li>
          @endforeach
          </ul>
        </div>
        <div class="tt1 title">VR视频新闻 <a href="/vronline/top/index-video-news" target="_blank" class="fr">更多<i class="ico ico-more"></i></a></div>
          <div class="news-list">
            @foreach($tops['index-video-news'] as $key=>$top)
            @if($key==0) 
            <div class="title"><a href="{{ $top['target_url'] }}" target="_blank">{{ $top['title'] }}</a> </div>
            @else
            <p><span class="time">{{ date("Y-m-d",$top['time']) }}</span><span class="cont"><a href="{{ $top['target_url'] }}" target="_blank"><i></i>{{ $top['title'] }}</a></span></p>
            @endif
            @endforeach
           </div>
        </div>
      </div>
    </div>
    <div class="side">
      <div class="mod1">
          <div class="tt1 title">VR视频榜单</div>
          <div class="game-main">
              <ul class="game-list">
                @foreach($tops['index-video-rank'] as $key=>$top)
                  <li>
                    <span class="info-wrap">
                      <span class="num num{{ $key+1 }} tc">{{ $key+1 }}</span>
                      <span class="pic"><img src="{{ static_image($top['cover']) }}" width="72" height="52"></span>
                      <span class="tit">
                        <span class="tit1"><a href="/vronline/video/detail/{{ $top['itemid'] }}" target="_blank">{{ $top['title'] }}</a></span>
                        <br>
                        <a class="icon_paly">{{ $top['view'] }}次</a>
                        <span class="sx">|</span>
                        <a class="icon_za">{{ $top['agree'] }}次</a>
                      </span>
                        <span class="score tc">{{ $top['intro'] }}</span>
                    </span>
                  </li>
                @endforeach
              </ul>
          </div>
      </div>
      <div class="mod1">
        <div class="tt1 title">热点专题</div>
          <div class="hot">
            @foreach($tops['index-vrgame-subject'] as $top)
            <a href="{{ $top['target_url'] }}" target="_blank">
            <div class="avatar"><img width="300" height="169" src="{{ static_image($top['cover']) }}">
            </div>
            </a>
            @endforeach
          </div>
        </div>
  </div>
</div>
<!-- VR硬件 -->
<div class="mod w_1100 mb30 first-screen" id="part_6">
    <div class="hd">
        <h3 class="tit"><i class="ico-zx"></i>VR硬件</h3>
    </div>
    <div class="main">
        <div class="qwpc ycsp">
            <div class="f-left">
                <div class="tt1 title">硬件评析</div>
                <a class="yc" href="{{ $tops['index-hardware-pic'][0]['target_url'] }}" target="_blank">
                    <div class="avatar">
                        <img src="{{ static_image($tops['index-hardware-pic'][0]['cover']) }}">
                        <div class="cover">{{ $tops['index-hardware-pic'][0]['title'] }}</div>
                    </div>
                </a>
                <div class="col1-list">
                    <ul>
                        @foreach($tops['index-hardware-pic'] as $key=>$top)
                        <?php
          if($key==0) continue;
        ?>
                            <li>
                                <a href="{{ $top['target_url'] }}" target="_blank">
                                    <div class="avatar"><img src="{{ static_image($top['cover']) }}" width="180" height="100">
                                    </div>
                                    <p>{{ $top['title'] }}</p>
                                </a>
                            </li>
                            @endforeach
                    </ul>
                </div>
            </div>
            <div class="f-mid">
                <div class="tt1 title">新品速递 <a href="/vronline/top/index-hardware-new" target="_blank" class="fr">更多<i class="ico ico-more"></i></a></div>
                <div class="col2-list">
                    <ul>
                        @foreach($tops['index-hardware-new'] as $top)
                        <li>
                            <a href="{{ $top['target_url'] }}" target="_blank">
                                <div class="fl"><img width="180" height="100" src="{{ static_image($top['cover']) }}" style="display: inline;">
                                </div>
                                <div class="fr">
                                    <p class="tit">{{ $top['title'] }}</p>
                                    <p class="cont">{{ htmlSubStr($top['intro'],40) }}</p>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="tt1 title">硬件新闻 <a href="/vronline/top/index-hardware-news" target="_blank" class="fr">更多<i class="ico ico-more"></i></a></div>
                <div class="news-list">
                    @foreach($tops['index-hardware-news'] as $key=>$top) @if($key==0)
                    <div class="title"><a href="{{ $top['target_url'] }}" target="_blank">{{ $top['title'] }}</a> </div>
                    @else
                    <p><span class="time">{{ date("Y-m-d",$top['time']) }}</span><span class="cont"><a href="{{ $top['target_url'] }}" target="_blank"><i></i>{{ $top['title'] }}</a></span></p>
                    @endif @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="side">
        <div class="mod1">
            <div class="tt1 title">硬件榜单</div>
            <div class="game-main">
                <ul class="game-list">
                  @foreach($tops['index-hardware-rank'] as $key=>$top)
                    <li>
                      <span class="info-wrap">
                        <span class="num num{{$key+1}} tc">{{$key+1}}</span>
                        <span class="pic"><img src="{{ static_image($top['cover']) }}" width="72" height="52"></span>
                        <span class="tit">
                          <span class="tit1"><a href="/vronline/article/detail/{{ $top['itemid'] }}" target="_blank">{{ $top['title'] }}</a></span>
                          <br>
                          <a href="/vronline/pc/search/{{ rawurlencode($top['title']) }}" target="_blank">评测</a>
                          <span class="sx">|</span>
                          <a href="{{ $top['target_url'] }}" target="_blank">专区</a>
                        </span><span class="score tc">{{ $top['intro'] }}</span>
                      </span>
                    </li>
                  @endforeach
                </ul>
            </div>
        </div>
        <div class="mod1">
            <div class="tt1 title">热点专题</div>
            <div class="hot">
                @foreach($tops['index-hardware-subject'] as $top)
                <a href="{{ $top['target_url'] }}" target="_blank">
                    <div class="avatar"><img width="300" height="169" src="{{ static_image($top['cover']) }}">
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>



 
<div class="scrollTop" style="display:block">
    <ul>
        <li class="needChange"><a href="javascript:;" onclick="clickPosition('part_1',this);" class="show"><i></i><em>行业</br>新闻</em></a></li>
        <li class="needChange"><a href="javascript:;" onclick="clickPosition('part_2',this);"><i></i><em>原创</br>专栏</em></a></li>
        <li class="needChange"><a href="javascript:;" onclick="clickPosition('part_3',this);"><i></i><em>每日</br>热图</em></a></li>
        <li class="needChange"><a href="javascript:;" onclick="clickPosition('part_4',this);"><i></i><em>VR</br>游戏</em></a></li>
        <li class="needChange"><a href="javascript:;" onclick="clickPosition('part_5',this);"><i></i><em>VR</br>视频</em></a></li>
        <li class="needChange"><a href="javascript:;" onclick="clickPosition('part_6',this);"><i></i><em>VR</br>硬件</em></a></li>
        <li style="height: 60px;"><div class="fd_div"><a href="javascript:;" onclick="$('body,html').animate({ scrollTop: 0 }, 800);" class="fd"></a></div></li>

    </ul>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
Array.prototype.shuffle=function(){
    _this=this;
    this.re=[];
    this.t=this.length;
    for(var i=0;i<this.t;i++){
        (function(i){
            var temp=_this;
            var m=Math.floor(Math.random()*temp.length);
            _this.re[i]=temp[m];
            _this.splice(m,1);
        })(i)    
    }
    return this.re
}
function refreshVrHot(){

  var obj = $(".bd.randomData a");
  var arr = [];
  for(var i=0 ; i< obj.length ; i++){
    arr.push(obj[i]);
  };
  arr.shuffle();
  $('.bd.randomData').empty();
  for(var i = 0 ;i<arr.re.length;i++){
    $('.bd.randomData').append(arr.re[i]);
  }
}

</script>
@endsection
