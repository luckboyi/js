@extends('vrhelp.layout')
@section('meta')
<title>3D播播首页</title>
@endsection

@section('head')
<link rel="stylesheet" href="{{ static_res('/vrhelp/style/3dbobo.css') }}">
<style media="screen">
  .nth4 li:nth-child(4n){margin-right: 0px;}
  .nth3 li:nth-child(3n){margin-right: 0px;}
</style>

<style type="text/css">
.picFocus .hd{    
    bottom: 0;
    height: 52px;
    padding: 0;
    background: rgba(0,0,0,0.3);
}

.picFocus .hd>a{
  width: 19px;
  height: 35px;
  opacity: 1;
}

.picFocus .hd .next{
  right:22px;
  bottom: 8px;
  top: inherit;
  background-position: -27px -106px;
}

.picFocus .hd .prev{
  left: 183px;
  bottom: 8px;
  top: inherit;
  background-position: -4px -106px;
}

.picFocus .hd ul{
      width: 500px;
    right: 50px;
    position: absolute;
}

.picFocus .hd ul li{
    padding-top: 9px;
    width:75px;
    height: 35px;
    margin-right: 10px;
}

.picFocus .hd ul li img{
    border: 0px;
    width:100%;
    height: 100%;
    opacity: 0.7;
}
.picFocus .hd ul li.on img{
    opacity: 1;
}


</style>
@endsection

@section('content')
      <div class="grid main_con">
      <div class="clf pf" style="display:none;"><ul>
      <?php $index = 0;?>
      @foreach(config("video.class") as $key=>$value)

        <li><a href="JavaScript:;">{{$value["name"]}}</a></li>
        <?php $index++; if($index==9) break; ?>
      @endforeach
      </ul></div>
      <div class="bobo_banner">
        <div class="bobo_slide fl picFocus pr">
            <div class="bd">
                <ul>
                  @foreach($recommend['tdbobo-banner']['data'] as $key => $value)
                  <li><a target="_blank" href="#"><img src="{!! static_image($value['image']['cover']) !!}" /></a></li>
                  @endforeach
                </ul>
            </div>
            <div class="hd pa">
                <div class="bobo_text f16 els"><a href="{!!$recommend['thbobo-testlink']['data'][0]['link']!!}" target="view_window">{!!$recommend['thbobo-testlink']['data'][0]['name']!!}</a></div>
                <a class="prev icon" href="javascript:void(0)"></a>
                <ul>
                  @foreach($recommend['tdbobo-banner']['data'] as $key => $value)
                    <li><img src="{!! static_image($value['image']['cover']) !!}" /></li>
                  @endforeach
                </ul>
                <a class="next icon" href="javascript:void(0)"></a>
            </div>
        </div>

     <div class="fr bobo_right">
                        <h3 class="f18"><i class="icon fl bobo_label"></i>热门标签</h3>
                        <div class="in_right_con">
                            <ul class="bobo_label_con tac">
                                @foreach($recommend['tdbobo-hot-label']['data'] as $index=>$value)
                                  <li><a href="/vrhelp/searchVideo?name={!!$value['name']!!}" @if($index==0) class="cur" @endif>{!!$value['name']!!}</a></li>
                                  <?php $index++;if($index>9)break;?>
                                @endforeach
                            </ul>
                        </div>
                    </div></div>
                    <div class="bobo_recommend">
                    <div class="grid pt30 clearfix recommend_list">
                    <h3 class="f18"><i class="icon fl bobo_icon"></i>人气视频<div class="refresh_con fr f12"><div class="in_resh_con fl cp" data-page="1"><i class="icon fl resh_con"></i>换一批</div><a href="/vrhelp/video/list"<i class="more fr cp">...</i></a></div></h3>
                    <ul class="nth4">
                          <?php $index = 0;?>
                            @foreach($recommend['tdbobo-hot-video']['data'] as $value)
                              <li @if($index>3) style="display:none;" @endif class="video-page-{!!floor($index/4+1)!!}" ><a href="JavaScript:;" class="video_play" data-val="{!!$value['id']!!}" style="background-image:url('{!! static_image($value['image']['cover']) !!}');"><div class="msg_con pa pr">
                                      <div class="play_icon  pr fl"><i class="triangle pa"></i></div>
                                      <p class="video_name els fl els">{!!$value['name']!!}</p>
                                      <p class="video_play_num fr els">
                                          <span class="min_play_icon pr">
                                              <i class="triangle pa"></i>
                                          </span>
                                          <span>{{ $value['play'] }} </span>
                                      </p>
                                  </div></a></li>
                              <?php $index++;if($index>11)break;?>
                            @endforeach


                            </ul></div>
                            <div class="grid pt30 clearfix recommend_list">
                    <h3 class="f18"><i class="icon fl bobo_icon"></i>一周更新<div class="refresh_con fr f12"><div class="in_resh_con fl cp" data-page="1"><i class="icon fl resh_con"></i>换一批</div><a href="/vrhelp/video/list"<i class="more fr cp">...</i></a></div></h3>
                    <ul class="nth4">
                              <?php $index = 0;?>
                              @foreach($recommend['tdbobo-oneweek']['data'] as $value)
                              <li @if($index>3) style="display:none;" @endif class="video-page-{!!floor($index/4+1)!!}"><a href="JavaScript:;" class="video_play" data-val="{!!$value['id']!!}" style="background-image:url('{!! static_image($value['image']['cover'])!!}');"><div class="msg_con pa pr">
                                      <div class="play_icon  pr fl"><i class="triangle pa"></i></div>
                                      <p class="video_name els fl els">{!!$value['name']!!}</p>
                                      <p class="video_play_num fr els">
                                          <span class="min_play_icon pr">
                                              <i class="triangle pa"></i>
                                          </span>
                                          <span>{{ $value['play'] }} </span>
                                      </p>
                                  </div></a></li>
                                <?php $index++;if($index>11)break;?>
                              @endforeach


                            </ul></div>
                            <div class="grid pt30 clearfix recommend_list">
                    <h3 class="f18"><i class="icon fl bobo_icon"></i>影视剧集<div class="refresh_con fr f12"><div class="in_resh_con fl cp" data-page="1"><i class="icon fl resh_con"></i>换一批</div><a href="/vrhelp/video/list"<i class="more fr cp">...</i></a></div></h3>
                    <div class="video_recommend fl pr"><a href="JavaScript:;" class="video_play" data-val="{!!$recommend['thbobo-series']['data'][0]['id']!!}" style="background-image:url('{!! static_image($recommend['thbobo-series']['data'][0]['image']['cover'])!!}');"><div class="msg_con pa pr">
                                        <div class="play_icon  pr fl"><i class="triangle pa"></i></div>
                                        <p class="video_name els fl els">{!!$recommend['thbobo-series']['data'][0]['name']!!}</p>
                                        <p class="video_play_num fr els">
                                            <span class="min_play_icon pr">
                                                <i class="triangle pa"></i>
                                            </span>
                                            <span>{{ $value['play'] }} </span>
                                        </p>
                                    </div></a></div>

                    <div class="video_list fr">
                    <ul class="nth3">
                    <?php $index = 0;?>
                    @foreach($recommend['thbobo-series']['data'] as $value)
                      @if($index>0&&$index<19)
                      <li @if($index>6) style="display:none;" @endif class="video-page-{!!floor(($index-1)/6+1)!!}"><a href="javascript:;" data-val="{!!$value['id']!!}" class="video_play pr f12">
                      <div class="video_sst" style="background-image:url('{!! static_image($value['image']['cover'])!!}');"></div>
                      <span class="cover"></span>
                      <span class="video_play_icon pa"><i class="video_triangle pa"></i></span>
                                              <div class=" video_msg_con pa">
                                                  <h4 class="fl els">{!!$value['name']!!}</h4>
                                                  <p class="video_play_num fr els">
                                                      <span class="els fr">{{ $value['play'] }} </span><span class="min_play_icon fr pr">
                                                          <i class="triangle pa"></i>
                                                      </span>
                                                  </p>
                                              </div>
                                          </a></li>
                      @endif
                      <?php $index++;?>
                    @endforeach

                                        </ul></div>
                    </div>
                    <div class="grid pt30 clearfix recommend_list">
                    <h3 class="f18"><i class="icon fl bobo_icon"></i>记录片<div class="refresh_con fr f12"><div class="in_resh_con fl cp" data-page="1"><i class="icon fl resh_con"></i>换一批</div><a href="/vrhelp/video/list"><i class="more fr cp">...</i></a></div></h3>
                    <div class="video_recommend fl pr"><a href="JavaScript:;" class="video_play" data-val="{!!$recommend['thbobo-documentary']['data'][0]['id']!!}" style="background-image:url('{!! static_image($recommend['thbobo-documentary']['data'][0]['image']['cover'])!!}');"><div class="msg_con pa pr">
                                        <div class="play_icon  pr fl"><i class="triangle pa"></i></div>
                                        <p class="video_name els fl els">{!!$recommend['thbobo-documentary']['data'][0]['name']!!}</p>
                                        <p class="video_play_num fr els">
                                            <span class="min_play_icon pr">
                                                <i class="triangle pa"></i>
                                            </span>
                                            <span>{{ $value['play'] }} </span>
                                        </p>
                                    </div></a></div>
                    <div class="video_list fr">
                    <ul class="nth3">
                    <?php $index = 0;?>
                    @foreach($recommend['thbobo-documentary']['data'] as $value)
                      @if($index>0&&$index<19)
                    <li @if($index>6) style="display:none;" @endif class="video-page-{!!floor(($index-1)/6+1)!!}"><a href="javascript:;" data-val="{!!$value['id']!!}" class="pr f12 video_play">
                    <div class="video_sst" style="background-image:url('{!! static_image($value['image']['cover'])!!}');"></div>
                    <span class="cover"></span>
                    <span class="video_play_icon pa"><i class="video_triangle pa"></i></span>
                                            <div class=" video_msg_con pa">
                                                <h4 class="fl els">{!!$value['name']!!}</h4>
                                                <p class="video_play_num fr els">
                                                    <span class="els fr">{{ $value['play'] }} </span><span class="min_play_icon fr pr">
                                                        <i class="triangle pa"></i>
                                                    </span>
                                                </p>
                                            </div>
                                        </a></li>
                      @endif
                      <?php $index++;?>
                    @endforeach
                                        </ul></div>
                    </div>
                            </div>
      </div>
    </div>
    <script type="text/javascript">

      (function(){
        var videoIndex = {
          init:function(){
            //分类条滚动
            $(window).scroll(function(e){
              if(window.scrollY>500){
                $('.clf').show();
              }
              else{
                $('.clf').hide();
              }
            })

            var p = this;
            //绑定事件
            $('.in_resh_con').click(function(){
              p.nextPage($(this));
            })
          },
          nextPage:function(obj){
            $(obj).find('i').addClass('resh_con_trans');
            setTimeout(function () {
              page = $(obj).attr('data-page');
              n_page = parseInt(page)+1;
              $(obj).parents('.recommend_list').find('ul li').hide();
              $(obj).parents('.recommend_list').find('ul .video-page-'+n_page).show();
              $(obj).attr('data-page',n_page%3);
              $(obj).find('i').removeClass('resh_con_trans');
            }, 600);
          }
        }
        return videoIndex.init();
      })()

      this.slide =  $(".picFocus").slide({ mainCell:".bd ul",effect:"left",autoPlay:true });
    $('body').css('background','rgba(255, 255, 255, 0)');

    </script>

 @endsection

 
