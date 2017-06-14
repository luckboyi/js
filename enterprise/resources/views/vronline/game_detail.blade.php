@inject('blade', 'App\Helper\BladeHelper')
@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 游戏详情</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{ static_res('/vronline/style/gamedetail.css') }}">
<script src="{{ static_res('/assets/loi/message.js') }}"></script>
<script src="{{ static_res('/vronline/js/comment.js') }}"></script>
<script src="{{ static_res('/vronline/js/vronline.js') }}"></script>
@endsection

@section('content')
  <div class="detail_container">
    <div class="header">我的位置: <a href="//www.vronline.com/" target="_blank">VROnline</a> > <a href="/vronline/game" target="_blank">VR游戏库</a> > <a>{{$detail['game_name']}}</a></div>
    <div class="curmbs_con">
    </div>
    <div class="gamedetail_con">
      <div class="in_gamedetail_con clearfix">
        <div class="img_con fl f16">
          <div class="in_img_con">
            <img src="//image.vronline.com/{{$detail['game_image']}}" >
          </div>
          <div class="text_con">
            <p class="clearfix">
              <span class="fl ells">开发商:<b>{{$detail['game_company']}}</b></span>
              <span class="fr ells">运营商:<b>{{$detail['game_operator']}}</b></span>
            </p>
            <p>
              <span>官网: <a href="{{$detail['game_offical_link']}}" target="_blank">{{$detail['game_offical_url']}}</a></span>
            </p>
          </div>
        </div>
        <div class="fl gamename_con f16">
          <div class="gamename_con_head clearfix">
            <div class="fl">
              <h3>{{$detail['game_name']}}</h3>
              <p>{{$detail['game_alias']}}</p>
            </div>
            <div class="fr tac f20">
            @if ($detail['game_price'] > 0)
                @if ($detail['game_buy_url'])
              <span class="bug_btn"><a href="{{$detail['game_buy_url']}}" target="_blank">购买</a></span>
                @endif
            @else
                @if ($detail['game_down_url'])
              <span class="download_btn"><a href="{{$detail['game_down_url']}}" >下载</a></span>
                @endif
            @endif
            </div>
          </div>
          <p class="game_text game_time">
            上市时间:<b>{{date("Y-m-d", $detail['game_sell_date'])}}</b>
          </p>
          <p class="game_text game_price">
            价格：<b>{{$detail['game_price']}}元</b>
          </p>
          <p class="game_text game_sup clearfix">
            <span class="fl">设备:
            @if(is_array($device) && $device)
                @foreach($device as $dev)
                    <i class="icon {{$dev}}"></i>
                @endforeach
            @endif
			</span>
            <span class="fr">平台:
            @if(is_array($platform) && $platform)
                @foreach($platform as $plat)
                    <i class="icon {{$plat}}"></i>
                @endforeach
            @endif
			</span>
          </p>
          <p class="game_text game_msg">
            <span><b>{{$detail['game_video_num']}}</b>个视频</span>
            <span><b>{{$detail['game_pic_num']}}</b>张图片</span>
            <span><b>{{$detail['game_pc_num']}}</b>篇评测</span>
            <span><b>{{$detail['game_news_num']}}</b>条新闻</span>
          </p>
          <p class="game_text game_type">
            类型:
            @if(is_array($classname) && $classname)
                @foreach($classname as $class)
                    <b>{{$class}}</b>
                @endforeach
            @endif
          </p>
          <p class="game_text game_state">题材:
            @if(is_array($detail['game_theme']) && $detail['game_theme'])
                @foreach($detail['game_theme'] as $game_theme)
                    <b>{{$game_theme}}</b>
                @endforeach
            @endif
			</p>
          <p class="game_text game_language">语言:
            @if(is_array($detail['game_lang']) && $detail['game_lang'])
                @foreach($detail['game_lang'] as $game_lang)
                    <b>{{config("category.language.".$game_lang.".name")}}</b>
                @endforeach
            @endif
          </p>
          <p class="game_text game_word">
            标签：
            @if(is_array($detail['game_tag']) && $detail['game_tag'])
                @foreach($detail['game_tag'] as $game_tag)
                    <span>{{config("category.vrgame_tags.".$game_tag.".name")}}</span>
                @endforeach
            @endif
          </p>
          <p class="game_text game_information ells6"><b>简介：</b>{{$detail['game_desc']}}</p>
        </div>
        <div class="fl game_mark">
          <div class="game_mark_con f20 tac">
            <p>编辑评分</p>
            <p class="score">{{$detail['game_mark']}}</p>
            <!-- 注释  如果是1到5星好评 score1-score2-score3-score4-score5   如果有半数好评 1.5  就.scorehafl0 scorehafl1，scorehafl2，scorehafl3，scorehafl4-->
            <p class="pr commend_con"><i class="icon pa commend_icon pr"><b class="icon pa commend_score_icon {{$detail['game_star']}}"></b></i></p>
          </div>
          <div class="game_advantage">
            <h4 >优点</h4>
            @if(is_array($detail['game_merit']) && $detail['game_merit'])
                @for($i = 0; $i < count($detail['game_merit']) && $i < 3; $i++)
                    <p class="pr ells"><i class="icon good_icon pa"></i>{{$detail['game_merit'][$i]}}</p>
                @endfor
            @endif
          </div>
          <div class="game_lack">
            <h4  >不足</h4>
            @if(is_array($detail['game_week']) && $detail['game_week'])
                @for($i = 0; $i < count($detail['game_week']) && $i < 3; $i++)
                    <p class="pr ells"><i class="icon lack_icon pa"></i>{{$detail['game_week'][$i]}}</p>
                @endfor
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="game_detail_msg">
      <div class="game_resource">
        @if ($pics && is_array($pics) || $videos && is_array($videos))
        <div class="game_resource_head tac">
          <ul class="clearfix game_table_head">
            @if ($pics && is_array($pics))
            <li class="fl cur cp f18">图片</li>
            @endif
            @if($videos && is_array($videos))
            <li class="fl cp f18">视频</li>
            @endif
          </ul>
          <!-- 修改替换 2017-04-06 -->
          <div class="game_table_body" style="height:520px; overflow:hidden;">
            <!-- 图片 -->
            @if($pics && is_array($pics))
            <div class="in_game_resource_head cur">
              <div class="swiper-container swiper-container1">
                <ol class="swiper-wrapper">
                @for($i = 0; $i < count($pics); $i++)
                  <li class="swiper-slide blue-slide clearfix">
                    @if(isset($pics[$i][0]) && $pics[$i][0])
                    <div class="fl">
                      <img src="//image.vronline.com/{{$pics[$i][0]}}">
                    </div>
                    @endif
                    @if(isset($pics[$i][1]) && $pics[$i][1])
                    <div class="fr">
                      <img src="//image.vronline.com/{{$pics[$i][1]}}" class="top_img">
                      @if (isset($pics[$i][2]) && $pics[$i][2])
                      <img src="//image.vronline.com/{{$pics[$i][2]}}">
                      @endif
                    </div>
                    @endif
                  </li>
                @endfor
                </ol>
                <!-- 如果需要分页器 -->
                <div class="clearfix btn_container">
                  <div class="button-prev fl "></div>
                  <div class="swiper-pagination pagination1 fl"></div>
                  <!-- 如果需要导航按钮 -->
                  <div class="button-next fl "></div>
                </div>

              </div>
            </div>
            @endif
            @if($videos && is_array($videos))
           	<div class="in_game_resource_head cur" >
              <div class="swiper-container swiper-container2">
                <ol class="swiper-wrapper">
                @for($i = 0; $i < count($videos); $i++)
                  <li class="swiper-slide blue-slide clearfix">
                    @if(isset($videos[$i][0]) && $videos[$i][0])
                    <div class="fl">
                      <a href="/vronline/video/detail/{{$videos[$i][0]['itemid']}}" target="_blank"><img src="//image.vronline.com/{{$videos[$i][0]['cover']}}"></a>
                    </div>
                    @endif
                    @if(isset($videos[$i][1]) && $videos[$i][1])
                    <div class="fr">
                      <a href="/vronline/video/detail/{{$videos[$i][1]['itemid']}}" target="_blank"><img src="//image.vronline.com/{{$videos[$i][1]['cover']}}" class="top_img"></a>
                      @if (isset($videos[$i][2]) && $videos[$i][2])
                      <a href="/vronline/video/detail/{{$videos[$i][2]['itemid']}}" target="_blank"><img src="//image.vronline.com/{{$videos[$i][2]['cover']}}"></a>
                      @endif
                    </div>
                    @endif
                  </li>
                @endfor
                </ol>
                <!-- 如果需要分页器 -->
                <div class="clearfix btn_container">
                  <div class="button-prev fl "></div>
                  <div class="swiper-pagination pagination2 fl"></div>
                  <!-- 如果需要导航按钮 -->
                  <div class="button-next fl "></div>
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
        @endif
        <div class="game_resource_body clearfix">
          @if($pingce && is_array($pingce))
          <div class="game_resource_left fl">
            <h3 class="til ells">{{$detail['game_search_name']}}</h3>
            <ul class="in_game_resource_con">
             @for($i = 0; $i < count($pingce) && $i < 3; $i++)
              <li class="clearfix">
                <a href="//www.vronline.com/vronline/article/detail/{{$pingce[$i]['itemid']}}" target="_blank" class="clearfix">
                  <div class="fl img_con">
                    <img src="//image.vronline.com/{{$pingce[$i]['cover']}}" >
                  </div>
                  <div class="text_con fr">
                    <h5 class="f20 ells">{{$pingce[$i]['title']}}</h5>
                    <p  class="f16 ells4">{{strip_tags($pingce[$i]['intro'])}}</p>
                    <p class="f16 look_text">[查看详细]</p>
                  </div>
                </a>
              </li>
            @endfor
            </ul>
          </div>
          @endif
          @if($recommends['rankvrgame'] && is_array($recommends['rankvrgame']))
          <div class="game_resource_right fr">
            <div class="popularity pr">
            	<h3 class="til">VR人气游戏</h3>
            	<i class="hot pa icon"></i>
            	<ul>
                @for($i = 0; $i < count($recommends['rankvrgame']); $i++)
                  <?php
                    if ($recommends['rankvrgame'][$i]['intro'] == "1" || $i == 0) {
                        $flag      = "&uarr;";
                        $flagclass = "up";
                    } else if ($recommends['rankvrgame'][$i]['intro'] == "2") {
                        $flag      = "&darr;";
                        $flagclass = "down";
                    } else {
                        $flag      = "&minus;";
                        $flagclass = "type";
                    }
                    switch ($i + 1) {
                        case 1:$rankclass = "first icon";
                            break;
                        case 2:$rankclass = "second icon";
                            break;
                        case 3:$rankclass = "third icon";
                            break;
                        default:$rankclass = "";
                            break;
                    }
                    $icons = $blade->handleDeviceIconSuper($recommends['rankvrgame'][$i]['device'], "www_icon_class");
                  ?>
            		<li>
            			<a class="clearfix" href="{{$recommends['rankvrgame'][$i]['target_url']}}" target="_blank">
            				<p class="fl left pr">
            					<img src="//image.vronline.com/{{$recommends['rankvrgame'][$i]['cover']}}" />
            					<i class="pa {{$rankclass}}"></i>
            					<!--<i class="pa second icon"></i>-->
            					<!--<i class="pa third icon"></i>-->
            				</p>
            				<div class="fl gamesname">
            					<p class="name ells f18"><{{$recommends['rankvrgame'][$i]['title']}}></p>
            					<p>
            						<span>设备:</span>
                                    @if(isset($icons) && is_array($icons))
                                      @foreach($icons as $icon)
                                    <i class="icon {{$icon}}"></i>
                                      @endforeach
                                    @endif
            					</p>
            				</div>
            				<p class="fr popularity_value clearfix">
            					<span class="fl num">{{$recommends['rankvrgame'][$i]['weight']}}</span>
            					<span class="fr {{$flagclass}}">{{$flag}}</span>
            					<!--<span class="up">&uarr;</span>-->
            					<!--<span class="down">&darr;</span>-->
            				</p>
            			</a>
            		</li>
                  @endfor
            	</ul>
            </div>
          </div>
          @endif
        </div>
        <div class="game_resource_body game_resource_body_con clearfix">
          @if($articles && is_array($articles))
          <div class="game_resource_left fl">
            <h3 class="til">{{$detail['game_search_name']}}</h3>
            <ul class="clearfix">
             @for($i = 0; $i < count($articles) && $i < 4; $i++)
              <li class="clearfix fl">
                <a href="//www.vronline.com/vronline/article/detail/{{$articles[$i]['itemid']}}" target="_blank" class="clearfix">
                  <div class="fl img_con">
                    <img src="//image.vronline.com/{{$articles[$i]['cover']}}" >
                  </div>
                  <div class="text_con fr">
                    <p  class="f16 ells2">{{$articles[$i]['title']}}</p>
                    <p class="f16 time_con">{{date("n.j", $articles[$i]['time'])}}</p>
                  </div>
                </a>
              </li>
            @endfor
            </ul>
          </div>
          @endif
          @if($recommends['hottopic'] && is_array($recommends['hottopic']))
          <div class="game_resource_right fr">
            <h3 class="til">热点专题</h3>
            <ul class="ad_con">
            @for($i = 0; $i < count($recommends['hottopic']); $i++)
              <li>
                <a href="{{$recommends['hottopic'][$i]['target_url']}}" target="_blank">
                  <img src="//image.vronline.com/{{$recommends['hottopic'][$i]['cover']}}" >
                </a>
              </li>
            @endfor
            </ul>
          </div>
          @endif
        </div>

        <div class="game_resource_commend_con">
            <div class="add_word">
                <p class="title">我有话说：</p>
                <textarea placeholder="我有话要说......" class="words" id="words" name="txb_Content0"></textarea>
                <input type="button" value="评论" id="btn_commentadd" class="send"  name="send" data-id="0" group="g0" data-qpid="0" data-qid="0"><a id="zxpl"></a>
            </div>
            <div class="comment mt20">
              <h2 class="comment_heading">
                  <span class="title">最新评论</span>
              </h2>
              <div id="comment_con">
                <div id="in_comment_con"></div>
                <div class="comment commMore2" id="load_more" style="display:none;"><a class="commMoreA"  href="javascript:;">加载更多</a></div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('javascript')
<script>
  $(function(){

  	swiper('.swiper-container1','.pagination1');
    swiper('.swiper-container2','.pagination2');
    $('.game_table_head').on('click','li',function(){
      var i = $(this).index();
      $(this).addClass('cur').siblings().removeClass('cur');
      $(this).parents('').find('.in_game_resource_head').eq(i).show().siblings('').hide();
    });
    $('.video_play_con').on('click','ol li',function(){
      var src = $(this).attr('src');
      videoPlay(src);
    });
    //播放视频
    function videoPlay(src){
      $('body').find('#video_play').detach();
      var html = '<div id="video_play" class="video_play pr" style="z-index:999;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);transform:-webkit-translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-o-transform:translate(-50%,-50%); width:800px; height:600px; background:#000;">\
      <i class="icon close_icon pa"></i>\
      <embed style="width:100%; height:100%;" src="'+src+'" loop controls="controls"/>\
      </div>';
      $('body').append(html);
    }
    $('body').on('click','#video_play .close_icon',function(){
      $(this).parents('#video_play').detach();
    });
    //qiehuan
    function swiper(obj,page){
    	var mySwiper = new Swiper(obj,{
                  loop: true,
              	autoplay: 3000,
              	// 如果需要分页器
                  pagination: ''+page+'',
                });
                $('.button-prev').on('click', function (e) {
                    e.preventDefault();
                    mySwiper.swipePrev();
                });
                $('.button-next').on('click', function (e) {
                    e.preventDefault();
               	    mySwiper.swipeNext();
                });

    };
    Comment.init({
        userid:'{{$uid}}',
        target_id:'{{$game_id}}',
        type:'news_game',
      });
    statPV('news_game', '{{$game_id}}');
  })
</script>
@endsection
