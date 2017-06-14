@inject('blade', 'App\Helper\BladeHelper')
@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 游戏详情</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{ static_res('/vronline/style/vr-list.css') }}">
<script src="{{ static_res('/vronline/js/vronline.js') }}"></script>
@endsection

@section('content')
<div class="content">
    <div class="header">我的位置: <a href="http://www.vronline.com/" target="_blank">首页</a> > <a href="/vronline/game" target="_blank">VR游戏</a> > <a href="#">游戏列表</a></div>
    <div class="game-filter">
        <div class="choose">
            <h3><span class="pr">分类筛选</span></h3>
            <div class="list-box clearfix">
                <div class="title">平台</div>
                <div class="ch-cont">
                    <ul class="plat-list clearfix game-platform">
                        <li class="all" query_type="pf" query_id="">全部</li>
                    @foreach($platforms as $platid => $platinfo)
                        <li query_type="pf" query_id={{$platid}}>{{$platinfo['name']}}</li>
                    @endforeach
                    </ul>
                </div>
            </div>
            <div class="list-box clearfix">
                <div class="title">设备</div>
                <div class="ch-cont">
                    <ul class="plat-list clearfix game-device">
                        <li class="all" query_type="d" query_id="">全部</li>
                    @foreach($devices as $deviceid => $deviceinfo)
                        <li query_type="d" query_id={{$deviceid}}>{{$deviceinfo['name']}}</li>
                    @endforeach
                    </ul>
                </div>
            </div>
            <div class="list-box clearfix">
                <div class="title">游戏类型</div>
                <div class="ch-cont">
                    <ul class="plat-list clearfix game-category">
                        <li class="all" query_type="c" query_id="">全部</li>
                    @foreach($gametypes as $gametypeid => $gametypeinfo)
                        <li query_type="c" query_id={{$gametypeid}}>{{$gametypeinfo['name']}}</li>
                    @endforeach
                    </ul>
                </div>
            </div>
            <div class="more">
                <div class="list-box clearfix">
                    <div class="title">标签</div>
                    <div class="ch-cont">
                        <ul class="plat-list clearfix game-tag">
                            <li class="all" query_type="t" query_id="">全部</li>
                        @foreach($tags as $tagid => $taginfo)
                            <li query_type="t" query_id={{$tagid}}>{{$taginfo['name']}}</li>
                        @endforeach
                        </ul>
                    </div>
                </div>
                <div class="list-box clearfix">
                    <div class="title">价格</div>
                    <div class="ch-cont">
                        <ul class="plat-list clearfix game-price">
                            <li class="all" query_type="pr" query_id="">全部</li>
                        @foreach($prices as $priceid => $priceinfo)
                            <li query_type="pr" query_id={{$priceid}}>{{$priceinfo['name']}}</li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <p class="moreChoose" class="btn"><span>更多选项（标签、价格等）</span><i class="icon change"></i></p>
        </div>
    </div>
    <div class="vr-game-list clearfix">
        <div class="vr-show">
            <div class="vr-show-content">
                <div class="vr-show-con-header clearfix">
                    <div class="sortList game-sort">
                        <span class="cur" sortby="z">综合排序</span>
                        <span sortby="v">人气</span>
                        <span sortby="s">评分</span>
                        <span sortby="t">发布时间</span>
                    </div>
                    <div class="numPage">
                        <span>共<i id="totalnum"></i>款</span><span><i> < </i><i id="currPage"></i><i> ></i></span>
                    </div>
                </div>
                <ul class="list-show" id="ul-content">
                </ul>
                <ul class="pager">
                </ul>
            </div>
        </div>
        <div class="right-show">
            <div class="vr-new">
                <div class="new-in">
                    <h1 class="new-in-header">最新入库</h1>
                    <div class="zs">
                        <ul class="newVR clearfix">
                        @if($newest && is_array($newest))
                          @for($i = 0; $i < count($newest) && $i < 6; $i++)
                            <li class="newVRList">
                                <a href="/vronline/game/detail/{{$newest[$i]['itemid']}}" target="_blank">
                                    <img src="{{ static_image($newest[$i]['cover'],'1-98-126') }}" alt="{{ $newest[$i]['title'] }}">
                                    <span class="newtxt">{{ $newest[$i]['title'] }}</span>
                                </a>
                            </li>
                          @endfor
                        @endif
                        </ul>
                    </div>
                    <h1 class="new-in-header">热门类型</h1>
                    <ul class="hotClass clearfix">
                      @for($i = 0; $i < count($recommends['hottyperecomm']); $i++)
                        <li class="classSort" onclick="recommendTypeSearch('{{$recommends['hottyperecomm'][$i]['intro']}}', {{$recommends['hottyperecomm'][$i]['target_url']}});"><a class="hotSort">{{$recommends['hottyperecomm'][$i]['title']}}</a></li>
                      @endfor
                    </ul>
                </div>
            </div>
            <div class="weekDown">
                @if(isset($recommends['downmost']) && $recommends['downmost'] && is_array($recommends['downmost']))
                <div class="hotDown">
                    <h1 class="rank"><span class="rh">一周下载榜</span></h1>
                    <ul>
                    @for($i = 0; $i < count($recommends['downmost']); $i++)
                        <?php
if ($i == 0) {
    $cls = "fir";
} else if ($i < 3) {
    $cls = "ts";
} else {
    $cls = "pt";
}
$icons = $blade->handleDeviceIconSuper($recommends['downmost'][$i]['device'], "www_icon_class");
?>
                        <li @if($i == 0) class="cur" @endif>
                        	<a href="{{$recommends['downmost'][$i]['target_url']}}" target="_blank">
                        		<div class="games clearfix">
                        			<p class="fl f14 ranking first">{{$i+1}}.</p>
		                            <p class="fl f16 gameName ells">{{$recommends['downmost'][$i]['title']}}</p>
		                            <p class="fr gameSort">类型：
                                    @if(isset($recommends['downmost'][$i]['category'][0]) && $recommends['downmost'][$i]['category'][0] && config("category.vronline_game_class.".$recommends['downmost'][$i]['category'][0].".name"))
                                      {{config("category.vronline_game_class.".$recommends['downmost'][$i]['category'][0].".name")}}
                                    @endif
                                    </p>
                        		</div>
	                            <div class="clearfix gameCon">
	                            	<p class="fl nameImg pr">
	                            		<i class="pa {{$cls}} icon">{{$i+1}}</i>
	                            		<img src="{{ static_image($recommends['downmost'][$i]['cover']) }}" />
	                            	</p>
	                            	<div class="fl details">
	                            		<p class="name f20">{{$recommends['downmost'][$i]['title']}}</p>
	                            		<p class="type">
	                            			<span>类型：</span>
                                            @if(isset($recommends['downmost'][$i]['category'][0]) && $recommends['downmost'][$i]['category'][0] && config("category.vronline_game_class.".$recommends['downmost'][$i]['category'][0].".name"))
                                              <span>{{config("category.vronline_game_class.".$recommends['downmost'][$i]['category'][0].".name")}}</span>
                                            @endif
	                            		</p>
	                            		<p>
	                            			<span class="device">设备：</span>
                                            @if(isset($icons) && is_array($icons))
                                              @foreach($icons as $icon)
	                            			<i class="icon {{$icon}}"></i>
                                              @endforeach
                                            @endif
	                            		</p>
	                            	</div>
	                            </div>
                        	</a>
                        </li>
                    @endfor
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
	$(function(){
		//排行榜
		$('.hotDown ul li').hover(function(){
			var i = $(this).index();
			$(this).addClass('cur').siblings().removeClass('cur');
		});
		//选项伸展
		$(".moreChoose").click(function(){
			if($(".choose .more").css('display')=='block'){
				$(".choose .more").css('display','none');
				$(".moreChoose span").text('更多选项（标签、价格等）');
				$(".moreChoose .change").css('background-position-x','-15px');
			}else{
				$(".choose .more").css('display','block');
				$(".moreChoose span").text('精简选项');
				$(".moreChoose .change").css('background-position-x','0');
			}
		})
        $(document).on("click", ".tag_link",function(){
            var tag_name = $(this).text()
            $(".game-category li").each(function(){
                if($(this).text()==tag_name) {
                    $(this).siblings(".all").removeClass();
                    $(this).addClass("all cur")
                    searchGame(1);
                    $('body,html').animate({ scrollTop: 400 }, 0);
                }
            })
        })
	})

$(".list-box .plat-list li").click(function(){
	var type = $(this).attr("query_type");
	var val  = $(this).attr("query_id");
	$(this).siblings(".all").removeClass();
	$(this).addClass("all cur");
	searchGame(1, false);
});
$(".sortList span").click(function(){
	$(this).siblings(".cur").removeClass();
	$(this).addClass("cur");
	searchGame(1, false);
});

searchGame(1, false);

//$(window).on("hashchange", function(){
//    alert(window.location.hash);
//});
</script>
@endsection
