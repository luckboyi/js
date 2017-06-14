@inject('blade', 'App\Helper\BladeHelper')
@extends('website.vrgame.layout')

@section('title')VRonline官网@endsection
@section('vrgameRight')
<div class="fr VRgame_home_right">
 @include("website.components.banner",["data"=>$recommend['vr-index-banner']['data']])
    <div class="VRgame_home_con clearfix">
        <div class="fl VRhotGames">
            <div class="VRhotGames_title clearfix">
                <h3 class="fl"><i></i>热门推荐</h3>
                <span class="fr switch-recommend">换一换</span>
            </div>
            <div class="VRhotGames_con videosCon">
                <ul class="clearfix" id="hot-vr-game">
                    @if (isset($recommend["hot-vr-game"]["data"]) && is_array($recommend["hot-vr-game"]["data"]))
                    @foreach ($recommend["hot-vr-game"]["data"] as $key=>$content)
                    <?php if($key>3) {continue;}?>
                    <!--新版的修改部分结构-->
                    <li class="fl pr">
                            <a href="/vrgame/{{ $content['id'] }}">
                                <div class="gameDetails">
                                <img src="{{static_image($content["image"]["logo"],466)}}" >
                                <p class="clearfix">
                                    <span class="fl" title="{{$content["name"]}}">{{$content["name"]}}</span>
                                    <span class="fr">{{$blade->getScoreOrNum($content)}}</span>
                                </p>
                                </div>
                                <div class="game_tips_con pa">
                                    <i class="triangle"></i>
                                    <div class="game_tips_head">
                                        <h4 class="f16">《{{$content["name"]}}》</h4>
                                        <p class="clearfix">
                                            <span class="fl">发行于：{{date("Y年m月d日", $content["publish_date"])}}</span>
                                            <span class="fr">{{$blade->getScoreOrNum($content)}}</span>
                                        </p>
                                    </div>
                                    <div class="game_tips_body">
                                        <p>
                                            {{$content["desc"]}}
                                        </p>
                                        <ul class="clearfix">
                                            <li class="fl device">
                                                <p class="game_tips_title">
                                                支持设备：
                                                </p>
                                                <p class="device">
                                                     {!! $blade->handleDeviceIcon($content['support']) !!}
                                                </p>
                                            </li>
                                            <li class="fr">
                                                <p class="game_tips_title">游戏售价：</p>
                                                @if($content["sell"]==0)
                                                <span class="freeGmae show f16">免费</span>
                                                @else
                                                <p class="price">
                                                    <span class="fl money f14"> <b class="f20">{{$content["sell"]*$payRate}} V</b></span>
                                                    <span class="fl original f14">{{$content["sell"]*$payRate}} V</span>
                                                </p>
                                                @endif
                                                <!-- <span class="freeGmae show f16">免费</span> -->
                                            </li>
                                        </ul>
                                        <p>游戏分类：</p>
                                        <p class="tag">
                                            <span><?=$blade->transConetentClass($content["category"], $content["tp"], "</span><span>");?></span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                    @endif
                </ul>
            </div>
            <div class="VRhotGames_title clearfix">
                <h3 class="fl game_library"><a name="all_contents"></a><i></i>游戏库</h3>
            </div>
            <div class="VRgames_con VRhotGames_con">
                <ul class="clearfix" id="page-content">
                </ul>
                </ul>
            </div>
        </div>
        <div class="fr">
            <div class="hotList">
                <div class="hotList_title clearfix">
                    <span class="fl"><i></i>热门榜单</span>
                </div>
                @include("website.components.rank",["type"=>"vrgame","payRate"=>$payRate,"data"=>$recommend["vrgame-rank"]["data"]])
            </div>
            @include("website.components.class",["type"=>"vrgame","doClass"=>"to-list"])
        </div>
    </div>
</div>
@endsection

@section('javascript-vrgame')
<script src="{{ static_res('/website/js/banner.js') }}"></script>
<script src="{{static_res('/common/js/pagination.js')}}?{{Config::get('staticfiles.file_version')}}"></script>
<script type="text/javascript">
    $(".home_banner").bannerVideo();
    $(function(){
        $('.hotList_con ul li').hover(function(){
            $(this).addClass('cur').siblings().removeClass('cur')
        });

        pagination.init({
            type: "scroll", //type=page，普通翻页加载，scroll为滚动加载
            url: "/vrgame/api/list", //ajaxType=ajax时为请求地址
            ajaxType:"get",
            ajaxData:{
                class_id:"0"
            },
            contentHtmlTmp: '<li class="fl pr">\
                                <a href="/vrgame/{id}">\
                                    <div class="gameDetails"><img src="{logo}" >\
                                    <p class="clearfix">\
                                        <span class="fl" title="{name}">{name}</span>\
                                        <span class="fr">评分：{score}</span>\
                                    </p></div>\
                                    <div class="game_tips_con pa">\
                                        <i class="triangle"></i>\
                                        <div class="game_tips_head">\
                                            <h4 class="f16">《{name}》</h4>\
                                            <p class="clearfix">\
                                                <span class="fl">发行于：{date}</span>\
                                                <span class="fr">评分：{score}</span>\
                                            </p>\
                                        </div>\
                                        <div class="game_tips_body">\
                                            <p>\
                                                {desc}\
                                            </p>\
                                            <ul class="clearfix">\
                                                <li class="fl device">\
                                                    <p class="game_tips_title">支持设备：</p>\
                                                    <p class="device">\
                                                         {device-icon}\
                                                    </p>\
                                                </li>\
                                                <li class="fr">\
                                                    <p class="game_tips_title">游戏售价：</p>\
                                                        <span class="freeGmae show f16">免费</span>\
                                                    </li>\
                                            </ul>\
                                            <p>游戏分类：</p>\
                                            <p class="tag">\
                                                {type-span}\
                                            </p>\
                                        </div>\
                                    </div>\
                                </a>\
                            </li>',
            contentHtmlContainer: "#page-content",
            first_get_num:30,
            get_num:30
        });

        $(window).scroll(function(event) {
            var sct = $(this).scrollTop();
            var scl=$(this).scrollLeft();
            var windowWidth=$(this).width();

            if(sct >= 706){
                $('.filter-con').css({
                    'position':'fixed',
                    'top':'66px',
                }).addClass('has-fixed');
            }else{
                $('.filter-con').css({
                    'position':'relative',
                    'top':'0',
                    'left':'auto'
                }).removeClass('has-fixed');
            }

            if(windowWidth<1240){
                if(sct >= 706){
                    $('.filter-con').css({
                        'left':992-scl
                    });
                }
            }else{
                $('.filter-con').css({
                    'left':'auto'
                });
            }
            resize();

        });

        $(".switch-recommend").click(function(){
            $.ajax({
                url: '/switch',
                type: 'GET',
                dataType: 'json',
                data: {code: 'hot-vr-game'},
            })
            .done(function(obj) {
                var html="";
                var i=0;
                if(obj.code==0 && obj.data.length>0){
                    $.each(obj.data,function(i,e){
                        html+=window.tmpReplace(recommendTmp,e);
                        if(i>2){
                            return false;
                        }
                    });
                    $("#hot-vr-game").html(html);
                }
                return false;
            })
            .fail(function() {
                return false;
            })
            .always(function() {
            });
            
        })
    });

    var recommendTmp = '<li class="fl pr">\
                                <a href="/vrgame/{id}">\
                                     <div class="gameDetails"><img src="{logo}" >\
                                    <p class="clearfix">\
                                        <span class="fl" title="{name}">{name}</span>\
                                        <span class="fr">评分：{score}</span>\
                                    </p></div>\
                                    <div class="game_tips_con pa">\
                                        <i class="triangle"></i>\
                                        <div class="game_tips_head">\
                                            <h4 class="f16">《{name}》</h4>\
                                            <p class="clearfix">\
                                                <span class="fl">发行于：{date}</span>\
                                                <span class="fr">评分：{score}</span>\
                                            </p>\
                                        </div>\
                                        <div class="game_tips_body">\
                                            <p>\
                                                {desc}\
                                            </p>\
                                            <ul class="clearfix">\
                                                <li class="fl device">\
                                                    <p class="game_tips_title">支持设备：</p>\
                                                    <p class="device">\
                                                         {device-icon}\
                                                    </p>\
                                                </li>\
                                                <li class="fr">\
                                                    <p class="game_tips_title">游戏售价：</p>\
                                                        <span class="freeGmae show f16">免费</span>\
                                                    </li>\
                                            </ul>\
                                            <p>游戏分类：</p>\
                                            <p class="tag">\
                                                {type-span}\
                                            </p>\
                                        </div>\
                                    </div>\
                                </a>\
                            </li>';

    window.tmpReplace = function(tmp, data) {
        return tmp.replace(/\\?\{([^{}]+)\}/g, function(match, name) {
            return (data[name] === undefined) ? '' : data[name];
        });
    }
    window.onresize = function(){
            resize();
        }
    function resize(){
        $('.VRhotGames_con ul li').hover(function(){
            var windowHeight=$(window).height(); //
            var boxHeight = $(this).offset().top-$(window).scrollTop();  //
            var tipsHeight=$(this).find('.game_tips_con').height();
            var distance=windowHeight-boxHeight-200;  //
            if(distance<tipsHeight){
                $(this).find('.game_tips_con').css({
                    top:-tipsHeight+94
                });
                $(this).find('.triangle').css({
                    bottom:20
                });
                $(this).parents('.videosCon').find('.game_tips_con').css({
                    top:-tipsHeight+158
                });
            }
        },function(){
            $(this).parents('.videosCon').find('.game_tips_con').removeAttr("style");
            $(this).find('.game_tips_con').removeAttr("style");
            $(this).find('.triangle').removeAttr("style");
        })
    }
    resize();

   $("body").on('mouseenter','ul li a .gameDetails',function(){
        $(this).next('.game_tips_con').css({'opacity':'100','transition':'all 1s ease-in-out','-webkit-transition':'all 1s ease-in-out','-moz-transition':'all 1s ease-in-out','-o-transition':'all 1s ease-in-out'});
    }).on('mouseleave','ul li a .gameDetails',function(){
       $(this).next('.game_tips_con').css({'opacity':'0','transition':'all 1s ease-in-out','-webkit-transition':'all 1s ease-in-out','-moz-transition':'all 1s ease-in-out','-o-transition':'all 1s ease-in-out'}); 
    });

</script>
@endsection
