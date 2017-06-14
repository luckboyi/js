@extends('pagegame.layout')
@include('pagegame.detail_section')
@section('title', $gameInfo["name"])

@section("head")
<script type="text/javascript" src="//pic.vronline.com/common/js/messenger.js"></script>
<script type="text/javascript" src="//pic.vronline.com/webgames/js/login.js"></script>
@endsection

@section('content')
<div class="menu">
    <div>
        <ul class="clearfix">
            <li class="fl cur">
                <a href="//web.vronline.com/" target="_blank">
                    <p class="f18">官网首页</p>
                    <p>HOME</p>
                </a>
            </li>
            <li class="fl">
                <a href="//www.vronline.com/" target="_blank">
                    <p class="f18">新闻资讯</p>
                    <p>NEWS</p>
                </a>
            </li>
            <li class="fl">
                <a href="//www.vronline.com/" target="_blank">
                    <p class="f18">游戏资料</p>
                    <p>GAME INFO</p>
                </a>
            </li>
            <li class="fl game_icon">
                @yield("detail_top_logo")
            </li>
            <li class="fl">
                <a href="//www.vronline.com/charge?uid={{$uid}}&appid={{$appid}}" target="_blank">
                    <p class="f18">充值中心</p>
                    <p>PAY</p>
                </a>
            </li>
            <li class="fl">
                <a href="javascript:;">
                    <p class="f18">客服中心</p>
                    <p>SERVICE</p>
                </a>
            </li>
            <li class="fl">
                <a @if($gameInfo['forumid']) href="//bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$gameInfo['forumid']}}" @else href="//bbs.vronline.com/forum.php" @endif target="_blank">
                    <p class="f18">游戏论坛</p>
                    <p>BBS</p>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="pagesgamebg2 @if(isset($appid) && $appid == 1000011) pagesgamebg2 @endif">
    <div>

    </div>
    @if(isset($appid) && $appid == 1000011)
        <embed src="//pic.vronline.com/webgames/images/1000011/jewel.swf" width=170 height=213 type=application/x-shockwave-flash wmode="transparent" quality="high" >
    @endif
</div>
<!--内容-->
<div class="pagegame_container clearfix">
    <div class="fl left_con">
        <!--登录-->
        <div class="loginCon">
            @if(isset($isStart) && $isStart)
                <p class="play_game pr go_servers" appid={{$appid}}></p>
            @else
                <p  class="coming_soon pr"></p>
            @endif
            <div class="clearfix">
                <p class="fl game_recharge go_paycenter" uid={{$uid}} appid={{$appid}}></p>
                <p class="fl itunes"></p>
            </div>
            <!--登录后-->
            @if(isset($uid) && $uid)
                <div class="entry">
                    <p>您好！<span class="red">{{$nick}}</span></p>
                    @if (isset($login_time) && $login_time)
                        <p>登录时间：<span class="red">{{$login_time}}</span></p>
                    @endif
                    @if(isset($serverinfo['serverid']) && $serverinfo['serverid'])
                        <p>您上次进入的服是：</p>
                        @if(isset($serverinfo['name']) && $serverinfo['name'])
                            <a class="red start-web-game" appid={{$appid}} server-id={{$serverinfo['serverid']}} href="javascript:;">{{$serverinfo['name']}}</a>
                        @endif
                    @else
                        <p>您暂时没有玩过游戏</p>
                    @endif
                    <p class="fr"><a href="//www.vronline.com/profile" target="_blank">用户中心</a><a href="//www.vronline.com/logout?referer=//web.vronline.com/detail/{{$appid}}"><span>注销</span></a></p>
                </div>
            @else
                <div class="bg">
                    <p class="text">
                        <input type="text"  placeholder="账号" id="login-username" />
                        <span class="error" id="login-username-error"></span>
                    </p>
                    <p class="text">
                        <input type="password" placeholder="密码" id="login-pwd"/>
                        <span class="error" id="login-pwd-error"></span>
                    </p>
                    <div class="clearfix">
                        <label class="fl clearfix">
                            <input class="fl" type="checkbox" id="remember"/>
                            <span class="fr">自动登录</span>
                        </label>
                        <a class="fr">忘记密码？</a>
                    </div>
                    <p class="login"  id="loginBtn"></p>
                    <p class="clearfix third_logins">
                        <span class="fl">其他账号登录:</span>
                        <i class="fl qq"></i>
                        <i class="fl wx"></i>
                        <i class="fl wb"></i>
                    </p>
                </div>
            @endif
        </div>
        <!--游戏截图-->
        <div class="leftgames">
            <div class="introduction">
                <p class="title">
                    <span class="f18">游戏介绍</span>
                    <span class="">INTRODUCTION</span>
                </p>
                <div class="f14" title="{{$gameInfo['content']}}">{{$gameInfo['content']}}</div>
            </div>
            <div class="customer_center">
                <p class="title">
                    <span class="f18">客服中心</span>
                    <span>CUSTOMER CENTER</span>
                </p>
                @yield("custom_center")
            </div>
        </div>
    </div>
    <div class="fl center_con">
        <div class="news">
            <div class="title clearfix">
                <p class="fl">
                    <span class="f20">新闻资讯</span>
                    <span>NEWS</span>
                </p>
                <!-- <a class="fr" href="javascript:;">更多</a> -->
                <!-- <a class="fr clearfix" href="//bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$gameInfo['forumid']}}" target="_blank"><span class="fl f14">更多</span><span class="fr">+</span></a> -->
                <a class="fr clearfix newslistmore" href="//web.vronline.com/newslist/{{$appid}}/{{$newsFirstSort}}" target="_blank"><span class="fl f14">更多</span><span class="fr">+</span></a>
            </div>
            <div class="newsTitle">
                <ul class="clearfix newslist">
                    @if(isset($news) && is_array($news) && $news)
                        @foreach($news as $index => $newsinfo)
                            @if(!isset($newsinfo['en']) || $newsinfo['en'] != "strategy")
                                <li class="fl f16 @if(isset($newsinfo['selected']) && $newsinfo['selected']) cur @endif" data-sortId="{{$newsinfo['id']}}">{{$newsinfo['name']}}</li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
            @if(isset($news) && is_array($news) && $news)
                @foreach($news as $index => $newsinfo)
                    @if(!isset($newsinfo['en']) || $newsinfo['en'] != "strategy")
                    <div class="newsCon @if(isset($newsinfo['selected']) && $newsinfo['selected']) show @endif"">
                        @if(isset($newsinfo['data']) && $newsinfo['data'] && is_array($newsinfo['data']) && count($newsinfo['data']) > 0)
                            <p>{{$newsinfo['data'][0]['title']}}</p>
                        @endif
                        <ul>
                            @if(isset($newsinfo['data']) && $newsinfo['data'] && is_array($newsinfo['data']))
                                @foreach($newsinfo['data'] as $index => $newsdata)
                                <li>
                                    <a href="{{$newsdata['link']}}" target="_balnk">
                                        <p class="fl">N</p>
                                        <p class="fl content ells"><<span>{{$newsinfo['name']}}</span>>{{$newsdata['title']}}</p>
                                        <p class="fr">{{date("m-d", strtotime($newsdata['ltime']))}}</p>
                                    </a>
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>
        <div class="news walkthrough">
            <div class="title clearfix">
                @if(isset($news) && is_array($news) && $news)
                    @foreach($news as $index => $newsinfo)
                        @if(isset($newsinfo['en']) && $newsinfo['en'] == "strategy")
                        <p class="fl">
                            <span class="f20">游戏攻略</span>
                            <span>GAME STRATEGY</span>
                        </p>
                        @endif
                    @endforeach
                @endif
                <a class="fr clearfix" href="//bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$gameInfo['forumid']}}" target="_blank"><span class="fl f14">更多</span><span class="fr">+</span></a>
            </div>
            @if(isset($news) && is_array($news) && $news)
                @foreach($news as $index => $newsinfo)
                    @if(isset($newsinfo['en']) && $newsinfo['en'] == "strategy")
                    <div class="newsCon">
                        <ul>
                        @if(isset($newsinfo['data']) && $newsinfo['data'] && is_array($newsinfo['data']))
                            @foreach($newsinfo['data'] as $index => $newsdata)
                            <li>
                                <a href="{{$newsdata['link']}}" target="_balnk">
                                    <p class="fl">N</p>
                                    <p class="fl content ells">{{$newsdata['title']}}</p>
                                    <p class="fr">{{date("m-d", strtotime($newsdata['ltime']))}}</p>
                                </a>
                            </li>
                            @endforeach
                        @endif
                        </ul>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    <div class="fl right_con">
        <div class="banner">
            <div class="pr">
                <ul>
                     @if(isset($images['screenshots']) && is_array($images['screenshots']) && $images['screenshots'])
                        @for($jj = 0; $jj < count($images['screenshots']); $jj++)
                            <li class="@if($jj==0) active @endif"><a href="javascript:;" target="_blank"><img src="{{$images['screenshots'][$jj]}}" /></a></li>
                        @endfor
                    @endif
                </ul>
                <ol class="clearfix">
                    <li class="fl cur"></li>
                    <li class="fl"></li>
                </ol>
            </div>
        </div>
        <!--游戏截图-->
        <div class="screenshots">
        @if(isset($images['slider']) && is_array($images['slider']) && $images['slider'])
            <ul class="clearfix">
                @for($ii = 0; $ii < count($images['slider']); $ii++)
                    @if($ii < 4)
                        <li class="fl"><img src="{{$images['slider'][$ii]}}" ></li>
                    @endif
                @endfor
            </ul>
        @endif
        </div>
    </div>
</div>
<!--底部-->
<div></div>
<script type="text/javascript">
    //新闻活动切换
    $('.newsTitle ul').on('click','li',function(){
        var i = $(this).index();
        $(this).addClass('cur').siblings().removeClass('cur');
        $(this).parents('.news').find('.newsCon').eq(i).addClass('show').siblings().removeClass('show');
        $(".newslist").each(function(){
            var dom = $(this).children();
            if(dom.hasClass('cur')) {
                console.log(dom.attr('data-sortId'));
                var href = "//web.vronline.com/newslist/{{$appid}}/" + dom.attr('data-sortId');
                $(".newslistmore").attr("href", href);
            }
        });
    });


</script>
@endsection
