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
<div class="pagesgamebg">
    <div @if(isset($needbg) && $needbg && isset($bgimage) && $bgimage) style="background:url({{$bgimage}});" bg="{{$bgimage}}" @endif>

    </div>
</div>
<!--内容-->
<div class="pagegame_container">
    <div class="clearfix">
        <div class="fl start left">
          @if(isset($isStart) && $isStart)
            <p class="play_game go_servers" appid={{$appid}}></p>
          @else
            <p class="coming_soon"></p>
          @endif
            <div class="clearfix">
                <p class="fl game_recharge go_paycenter" uid={{$uid}} appid={{$appid}}></p>
                <p class="fl itunes go_register"></p>
            </div>

            @if(isset($uid) && $uid)
            <!--登录后-->
            <div class="entry">
                <p>您好！<span>{{$nick}}</span></p>
                @if (isset($login_time) && $login_time)
                <p>登录时间：<span>{{$login_time}}</span></p>
                @endif
                @if(isset($serverinfo['serverid']) && $serverinfo['serverid'])
                <p>您上次进入的服是：</p>
                    @if(isset($serverinfo['name']) && $serverinfo['name'])
                <a style="cursor:pointer;" class="start-web-game" appid={{$appid}} server-id={{$serverinfo['serverid']}}>{{$serverinfo['name']}}</a>
                    @else
                <a style="cursor:pointer;" class="start-web-game" appid={{$appid}} server-id={{$serverinfo['serverid']}}>{{$serverinfo['serverid']}}</a>
                    @endif
                @else
                <p>您暂时没有玩过游戏</p>
                @endif
                <p class="fr"><a href="//www.vronline.com/profile" target="_blank">用户中心</a><a href="//www.vronline.com/logout?referer=//web.vronline.com/detail/{{$appid}}"><span>注销</span></a></p>
            </div>
            @else
            <div class="bg">
                <p class="pr">
                    <input type="text" placeholder="账号" id="login-username"/>
                    <span class="error" id="login-username-error"></span>
                </p>
                <p class="pr">
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
                <p class="login" id="loginBtn"></p>
                <p class="clearfix third_logins">
                    <span class="fl">其他账号登录:</span>
                    <i class="fl qq"></i>
                    <i class="fl wx"></i>
                    <i class="fl wb"></i>
                </p>
            </div>
            @endif
        </div>
        <div class="fl banner cen">
            <div class="pr">
                <ul>
                @if(isset($images['screenshots']) && is_array($images['screenshots']) && $images['screenshots'])
                    @for($jj = 0; $jj < count($images['screenshots']); $jj++)
                    <li><a ><img src="{{$images['screenshots'][$jj]}}" /></a></li>
                    @endfor
                @endif
                </ul>
                <ol class="clearfix">
                    <li class="fl cur"></li>
                    <li class="fl"></li>
                </ol>
            </div>
        </div>
        <div class="fl news bg right">
            <div class="nav">
                <p class="fl">
                @if(isset($news) && is_array($news) && $news)
                    @foreach($news as $index => $newsinfo)
                        @if(!isset($newsinfo['en']) || $newsinfo['en'] != "strategy")
                            <span class="f18 fl @if(isset($newsinfo['selected']) && $newsinfo['selected']) cur @endif">{{$newsinfo['name']}}</span>
                        @endif
                    @endforeach
                @endif
                </p>
                <a class="fr clearfix" href="//bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$gameInfo['forumid']}}" target="_blank"><span class="fl f14">更多</span><span class="fr">+</span></a>
            </div>
            @if(isset($news) && is_array($news) && $news)
                @foreach($news as $index => $newsinfo)
                    @if(!isset($newsinfo['en']) || $newsinfo['en'] != "strategy")
            <div class="newsCon @if(isset($newsinfo['selected']) && $newsinfo['selected']) show @endif">
                <ul>
                @if(isset($newsinfo['data']) && $newsinfo['data'] && is_array($newsinfo['data']))
                    @foreach($newsinfo['data'] as $index => $newsdata)
                    <li>
                        <a href="{{$newsdata['link']}}" target="_balnk">
                            <p class="fl"> [ <span>{{$newsinfo['name']}}</span> ] </p>
                            <p class="fl">{{$newsdata['title']}}</p>
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
    <div class="clearfix second">
        <div class="fl games_introduce bg left">
            <div class="nav">
                <p>
                    <span class="f20">游戏介绍</span>
                    <span class="red">INTRODUCTION</span>
                </p>
            </div>
            <div class="f14 text">{{$gameInfo['content']}}</div>
        </div>
        <div class="fl bg cen role news">
            @yield("role_intro")
        </div>
        <div class="fl bg walkthrough right">
            <div class="nav clearfix">
                <p class="fl">
                @if(isset($news) && is_array($news) && $news)
                    @foreach($news as $index => $newsinfo)
                        @if(isset($newsinfo['en']) && $newsinfo['en'] == "strategy")
                    <span class="f20">游戏攻略</span>
                    <span class="red">GAME STRATEGY</span>
                        @endif
                    @endforeach
                @endif
                </p>
                <a class="fr clearfix" href="//bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$gameInfo['forumid']}}" target="_blank"><span class="fl f14">更多</span><span class="fr">+</span></a>
            </div>

            @if(isset($news) && is_array($news) && $news)
                @foreach($news as $index => $newsinfo)
                    @if(isset($newsinfo['en']) && $newsinfo['en'] == "strategy")
            <div class="newsCon show">
                <ul>
                @if(isset($newsinfo['data']) && $newsinfo['data'] && is_array($newsinfo['data']))
                    @foreach($newsinfo['data'] as $index => $newsdata)
                    <li>
                        <a href="{{$newsdata['link']}}" target="_balnk">
                            <p class="fl">{{$newsdata['title']}}</p>
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
    <div class="clearfix third">
        <div class="fl customer_center bg left">
            <div class="nav">
                <p>
                    <span class="f20">客服中心</span>
                    <span class="red">CUSTOMER CENTER</span>
                </p>
            </div>
            @yield("custom_center")
        </div>
        <div class="fl bg cen screenshots">
            <div class="nav clearfix">
                <p>
                    <span class="f20">游戏截图</span>
                    <span class="red">SCREENSHOTS</span>
                </p>
            </div>
            @if(isset($images['slider']) && is_array($images['slider']) && $images['slider'])
            <div class="pr">
                <i class="left_icon"></i>
                <div>
                    <ul class="clearfix">
                        <li class="fl clearfix">
                        @for($ii = 0; $ii < count($images['slider']); $ii++)
                            <span class="fl">
                                <img src="{{$images['slider'][$ii]}}" />
                            </span>
                            @if($ii % 2 == 1 && count($images['slider']) > $ii+1)
                        </li>
                        <li class="fl clearfix">
                            @endif
                        @endfor
                        </li>
                    </ul>
                </div>
                <i class="right_icon"></i>
            </div>
            @endif
        </div>
        <div class="fl bg media right">
            <div class="nav">
                <p>
                    <span class="f20">合作媒体</span>
                    <span class="red">MEDIA</span>
                </p>
            </div>
        @yield("media")
        </div>
    </div>
</div>
<!--底部-->
<div></div>
@endsection
