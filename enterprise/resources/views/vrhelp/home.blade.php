@extends('vrhelp.layout')
@section('meta')
<title></title>
@endsection

@section('content')
<style>
.chooseBar{margin-left: -50px;}
.slideImageBox ul{margin-left: calc(0px - 55%);}
</style>
        <div class="main_con" >
            <div class="home_banner pr" id="home_banner">

            </div>
            <div class="home_con">
                <div class="in_home_con">
                    <div class="home_classify  fl">
                        <h3 class="pr f18"><i class="fl icon game_events"></i><b>游戏大事件</b></h3>
                        <div class="in_home_classify">
                            <ul class="time_shaft clearfix tac">
                                @foreach($recommend['index-game-event']['data'] as $value)
                                <li class="fl pr"><i class="triangle pa"></i>{{ $value['sub_title'] }}</li>
                                @endforeach
                            </ul>
                            <ol class="game_events_con clearfix">
                            @foreach($recommend['index-game-event']['data'] as $value)
                                <li class="fl">
                                    <a href="{{  $value['target_url'] }}">
                                        <div class="img_con" style="background-image:url('{!! static_image($value['image']['cover']) !!}');"></div>
                                        <div class="msg_con">
                                            <h4 class="f14">{{ $value['name'] }}</h4>
                                            <p class="els">{{ $value['desc'] }}</p>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                            </ol>
                        </div>
                    </div>
                    <div class="fr home_right_con game_type">
                        <h3 class="f18"><i class="icon fl game_type"></i>游戏类型</h3>
                        <div class="in_right_con">
                            <ul class="game_type_con tac">
                            <?php $index = 0;?>
                                @foreach(config("vrgame.class") as $key=>$value)
                                  <li><a href="/vrhelp/game#category={{ $value['id'] }}">{{$value["name"]}}</a></li>
                                  <?php $index++; if($index==10) break; ?>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="in_home_con">
                    <div class="home_classify  fl">
                        <h3 class="pr f18"><i class="fl icon hot_game"></i><b>热门游戏</b><div class="refresh_con fr f12"><div class="in_resh_con fl cp" data-val="game"><i class="icon fl resh_con"></i>换一批</div><a href="/vrhelp/game"><i class="more fr cp">...</i></a></div></h3>
                        <div class="in_home_classify hot_game_con clearfix">
                            <div class="fl">
                                 @foreach($recommend['index-hot-game']['data'] as $key=>$value)
                                <a class="game_detail" href="javascript:;" data-val="{{ $value['id'] }}">
                                    <div class="img_con"  style="background-image:url('{!! static_image($value['image']['cover']) !!}');"></div>
                                    <div class="msg_con">
                                        <h4><i class="f18">{{ $value['name'] }}</i><b class="fr">下载量:{{ $value['play'] }}</b></h4>
                                        <p class="ells2">{{ $value['name'] }}</p>
                                        <p class="tag els clearfix">
                                            <span class="fl">站着</span>
                                            <span class="fl">设计</span>
                                        </p>
                                    </div>
                                </a>
                                <?php if($key==0) break;?>
                                @endforeach
                            </div>
                            <div class="fr">
                                <ul class="game-li">
                                @foreach($recommend['index-hot-game']['data'] as $key=>$value)
                                 <?php if($key==0) continue;?>
                                <li class="game_detail fl game-page-<?php $gamePage =  intval(($key-1)/6)+1; echo $gamePage;if($gamePage>1) echo " hide";  ?>" data-val="{{ $value['id'] }}">
                                        <a href="javascript:;" class="pr f12" style="background-image:url('{!! static_image($value['image']['cover']) !!}');">
                                            <div class="msg_con pa">
                                                <h4 class="f12 els">{{ $value['name'] }}</h4>
                                                <p class="els els">下载量:{{ $value['play'] }}</p>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="fr home_right_con game_type">
                        <h3 class="f18">
                            <i class="icon fl game_download_list"></i>游戏下载榜
                            <div class="game_next_btn fr">
                                <div class="fl game_prev_btn icon"></div>
                                <div class="fl game_next_btn icon tar"></div>
                            </div>
                        </h3>
                        <div class="gamedownload_list_container">
                         <ul class="gamedownload_list_con cur">
                            @foreach($topGame as $key=>$game)
                                <li class="game_detail gamerank-page-<?php $gamePage =  intval($key/8)+1; echo $gamePage;if($gamePage>1) echo " hide";  ?>" data-val="{{ $game['id'] }}">
                                    <a href="javascript:;">
                                        <i class="icon fl <?php echo  $key+1<=4?"ranking".($key+1):""; ?> tac rank ">{{ $key+1 }}</i>
                                        <div class="game_name fl els f14 ">{{ $game['name']}} </div>
                                        <div class="game_down_num fr els">
                                            下载量: <b>{{ $game['play']}}</b>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                           </ul>
                        </div>
                    </div>
                </div>
                <div class="in_home_con">
                    <div class="home_classify  fl">
                        <h3 class="pr f18"><i class="fl icon hot_video"></i><b>热门视频</b><div class="refresh_con fr f12"><div class="in_resh_con fl cp" data-val="video"><i class="icon fl resh_con"></i>换一批</div><a href="/vrhelp/video/list"><i class="more fr cp">...</i></a></div></h3>
                        <div class="in_home_classify hot_game_con hot_video_con clearfix">
                            <div class="fl">
                                @foreach($recommend['index-hot-video']['data'] as $key=>$value)
                                    <a href="JavaScript:;" class="pr video_play" data-val="{!!$value['id']!!}" style="background-image:url('{!! static_image($value['image']['cover']) !!}');">
                                    <div class="msg_con pa pr">
                                        <div class="play_icon  pr fl"><i class="triangle pa"></i></div>
                                        <p class="video_name els fl els">{{ $value['name'] }}</p>
                                        <p class="video_play_num fr els">
                                            <span class="min_play_icon pr">
                                                <i class="triangle pa"></i>
                                            </span>
                                            <span>{{ $value['play'] }}</span>
                                        </p>
                                    </div>
                                </a>
                                    <?php if($key==0) break;?>
                                @endforeach
                            </div>
                            <div class="fr">
                                <ul class="video-li">
                                 @foreach($recommend['index-hot-video']['data'] as $key=>$value)
                                 <?php if($key==0) continue;?>
                                    <li class="fl video-page-<?php $videoPage =  intval(($key-1)/4)+1; echo $videoPage;if($videoPage>1) echo " hide";  ?>">
                                        <a href="JavaScript:;" data-val="{!!$value['id']!!}" class="pr f12 video_play" style="background-image:url('{!! static_image($value['image']['cover']) !!}');">
                                            <div class=" video_msg_con pa">
                                                <h4 class="fl els">{{ $value['name'] }}</h4>
                                                <p class="video_play_num fr els">
                                                    <span class="min_play_icon fl pr">
                                                        <i class="triangle pa"></i>
                                                    </span>
                                                    <span class="els fl">{{ $value['play'] }}</span>
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="fr home_right_con game_type">
                        <h3 class="f18"><i class="icon fl hot_video_list"></i>热门视频榜</h3>
                        <div class="hot_video_con video-rank">
                            <ul>
                                @foreach($topVideo as $key=>$video)
                                <li class="">
                                    <a href="JavaScript:;" data-val="{!!$video['id']!!}" class="video_play">
                                        <div class="clearfix">
                                            <div class="fl video_rank <?php echo  $key+1<=4?"video_rank".($key+1):""; ?>">{{ $key+1 }}</div>
                                            <div class="fl">
                                                <div class="hot_list_video_name els">{{ $video['name']}}</div>
                                                <div class="hover_show clearfix">
                                                    <div class="img_con fl" style="background-image:url('{!! static_image($value['image']['cover']) !!}')"></div>
                                                    <div class="fl msg_con els">
                                                        <h4 class="els">{{ $video['desc']}}</h4>
                                                        <p class="els">播放：{{ $video['play']}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('javascript')
<script language="JavaScript" src="{{ static_res('/vrhelp/js/plugin/jquery-slideShow.js') }}"></script>
<script>
    var image= {!! $slider_image !!};
    var href= {!! $slider_url !!};
    var lock = {};
    var cur_page = {
        game:1,
        gamerank:1,
        video:1
    };

    // $("#home_banner").slideShow({images:image,//必选
    //     autoPlay:true,
    //     href:href,
    //     height:230,//可指定轮播图高度
    //     interval:6000
    // });

    // var image=["images/1.png","images/2.png","images/3.png","images/4.png"];
    // var href=["https://www.baidu.com","http://www.jq22.com","http://www.jq22.com","http://www.jq22.com"];
    $("#home_banner").slideShow({images:image,//必选
        autoPlay:true,
        href:href,
        height:230,//可指定轮播图高度
        interval:6000});

    $(function(){
        $('.video-rank li:first').addClass('cur');

        $('.video-rank li').hover(function(){
            $(this).addClass('cur').siblings().removeClass('cur');
        })
        var top_game_len = $('.gamedownload_list_container ul li').length
        if(top_game_len >8){
            $('.game_next_btn ').show();
        } else {
            $('.game_next_btn ').hide();
        }
        $('.game_prev_btn').click(function(){
           nextPage('gamerank',cur_page['gamerank']-1);
        });
        $('.game_next_btn .game_next_btn').click(function(){
            nextPage('gamerank',cur_page['gamerank']+1);
        });

        $(".in_resh_con").click(function(){
             var tp = $(this).attr('data-val');
             refresh(tp,$(this))
        })
    })

    function refresh(tp,obj) {
        var len = $('.'+tp+'-li li').length
        var page_len =  Math.ceil(len/6)
        if(page_len<=1) return false;
        if(typeof(lock[tp])=="undefined") {
            lock[tp] = 1
        } else {
            return false;
        }
        var i_obj = obj.find('i')
        i_obj.addClass("resh_con_trans");
        setTimeout(function(){
            nextPage(tp,randCur(cur_page[tp],page_len));
            i_obj.removeClass("resh_con_trans");
            delete(lock[tp]);
        },600)
    }

    function nextPage(tp,page) {
        if($("."+tp+"-page-"+page).length>0) {
            $("."+tp+"-page-"+cur_page[tp]).hide();
            $("."+tp+"-page-"+page).removeClass('hide');
            $("."+tp+"-page-"+page).show();
            cur_page[tp] = page;
        } else {
            console.log(page);
        }
    }

    function randCur(cur,all) {
        var arr = [];
        for(var i=1;i<=all;i++) {
            if(i!=cur) {
              arr.push(i)
            }
        }
        var n = Math.floor(Math.random() * arr.length + 1)-1;
        return arr[n];
    }

    $('body').css('background','rgba(255, 255, 255, 0)');
</script>
@endsection
