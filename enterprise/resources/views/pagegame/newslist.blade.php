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
                <a href="javascript:;">
                    <img src="{{$images['slogo']}}" />
                </a>
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
    <div>

    </div>
</div>
<!--内容-->
<div class="pagegame_container">
    <div class="clearfix">
        <div class="fl start left">
          @if(isset($isStart) && $isStart)
            <p class="play_game pr go_servers" appid={{$appid}}></p>
          @else
            <p class="coming_soon pr"></p>
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
    </div>
    <div class="clearfix third">
        <div class="fl customer_center bg left">
            <div class="nav">
                <p>
                    <span class="f20">客服中心</span>
                    <span class="red">CUSTOMER CENTER</span>
                </p>
            </div>
            <div class="clearfix">
                <div class="fl">
                    <p><span>传真电话：</span><span>021-54310366</span></p>
                    <p><span>客服电话：</span><span>021-54310366</span></p>
                    <p class="clearfix"><span class="fl">游戏咨询：</span><i class="fl">在线客服</i></p>
                    <p class="clearfix"><span class="fl">充值咨询：</span><i class="fl">在线客服</i></p>
                </div>
                <div class="fr">
                    <span><img src="//pic.vronline.com/webgames/images/kefu.jpg" title="请扫描二维码" /></span>
                    <p>扫二维码</p>
                </div>
            </div>
            <!-- @yield("custom_center") -->
        </div>
    </div>

    <!--右侧内容-->
    <div class="con_conr">
        <div class="con_til">
            <h3>
                {{$bannerDesc}} <span>NEWS</span></h3>
            <p>
                您所在的位置：
                <a href="#" target="_blank">首页</a>
                &gt;
                <em>{{$bannerDesc}}</em>
            </p>
            <div class="clear"></div>
        </div>
        <div class="con_box">
            <div class="con_li">
                <ul>
                    @if(is_array($news) && count($news) > 0)
                        @foreach($news as $newsinfo)
                            <li>
                                <p>
                                    <a href='{{ $newsinfo["link"] }}' target="_blank">[{{$bannerDesc}}]{{ $newsinfo['title'] }}</a>
                                </p>
                                <span>{{ $newsinfo['ltime'] }}</span>
                                <div class="clear"></div>
                            </li>
                        @endforeach
                    @else
                        <h2>暂时无相关游戏新闻！！</h2>
                    @endif
                </ul>
            </div>
            <div class="page">
            {!! $paginator->appends(['action'=> $action])->render() !!}
                <!-- <div class="pager">
                    <a class="current">1</a>
                    <a href="#"><span>2</span></a> <a href="#"><span>3</span></a> <a href="#"><span>4</span></a> <a href="#"><span>5</span></a> <a href="#"><span>6</span></a>
                    <a href="#">下一页&gt;
    </a> &nbsp;
                    <a href="#">末页</a>
                </div> -->
            </div>
        </div>
    </div>
</div>
<!--底部-->
<div></div>
<script type="text/javascript">

</script>
@endsection
