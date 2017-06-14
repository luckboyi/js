@extends('pagegame.layout')

@section('title', "网页游戏")
@section("head")
<script type="text/javascript" src="//pic.vronline.com/common/js/messenger.js"></script>
<script type="text/javascript" src="//pic.vronline.com/webgames/js/login.js"></script>
@endsection

@section('content')
<style type="text/css">
    .show {display: block;}
    .hide {display: none;}
</style>
<!--头部-->
<div class="head">
    <div class="pr clearfix">
        <a class="fl" href="javascript:;"><i class="logo"></i></a>
        <ul class="fl">
            <li class="fl cur"><a href="javascript:;">首页</a></li>
            <li class="fl"><a href="//www.vronline.com/profile" target="_blank">账号中心</a></li>
            <li class="fl"><a href="//www.vronline.com/charge" target="_blank">充值中心</a></li>
            <li class="fl"><a href="//bbs.vronline.com/" target="_blank">玩家论坛</a></li>
            <li class="fl"><a href="//www.vronline.com/vronline" target="_blank">VR助手</a></li>
        </ul>
        <div class="fr head_right clearfix">
            <a class="fl f16" href="javascript:;">登录</a>
            <a class="fr f16" href="//www.vronline.com/register" target="_blank">注册</a>
        </div>
    </div>
</div>
<!--内容-->
<div class="container">
    <!--banner-->
    <div class="banner pr">
        <ul class="pr">
            @if(isset($indexBanner['web-slider']['data']) && count($indexBanner['web-slider']['data']) > 0)
                @foreach($indexBanner['web-slider']['data'] as $bk=>$bv)
                <li class="@if($bk === 0) active @endif">
                    <a href="{{ $bv['link'] }}" target="_blank"><img src="{{ static_image($bv['image']['cover']) }}" /></a>
                </li>
                @endforeach
            @endif
        </ul>
        <ol class="clearfix">
            @if(isset($indexBanner['web-slider']['data']) && count($indexBanner['web-slider']['data']) > 0)
                @foreach($indexBanner['web-slider']['data'] as $bk=>$bv)
                <li class="fl @if($bk === 0) cur @endif"></li>
                @endforeach
            @endif
        </ol>
        <div class="login pr login-con">
            <div class="login_con">
                <!--登录前-->
                <div style="@if(isset($uid) && !$uid) @else display: none @endif">
                    <h3 class="f22">VRonline登录</h3>
                    <p class="clearfix">
                        <input class="fl" type="text" placeholder="账号" id="login-username"/>
                        <i class="fl account_number"></i>
                        <span class="fl error" id="login-username-error"></span>
                    </p>
                    <p class="clearfix">
                        <input class="fl" type="password" placeholder="密码" id="login-pwd"/>
                        <i class="fl key"></i>
                        <span class="fl error" id="login-username-error"></span>
                    </p>
                    <p class="clearfix" id="login-captacha-con" style="display: none">
                        <input class="fl" type="text" style="width: 168px;" placeholder="请输入验证码" id="login-captcha" />
                        <img src="" class="fl">
                        <span class="fl error" style="clear:both;" id="login-captcha-error"></span>
                    </p>
                    <div class="clearfix">
                        <label class="fl clearfix"><input class="fl" type="checkbox" id="remember"><span class="fl">下次自动登录</span></label>
                        <a class="fr" href="//open.vronline.com/forgetpwd" target="_blank"><i></i><span>忘记密码？</span></a>
                    </div>
                    <p class="sign_in f14 btn" id="loginBtn">登 录</p>
                    <p class="register"><span>还没有帐号？</span><a href="//www.vronline.com/register" target="_blank">立即注册</a></p>
                    <div class="clearfix other_login">
                        <p class="fl clearfix" id="login-qq"><i class="fl"></i><span class="fl">QQ登录</span></p>
                        <p class="fl clearfix" id="login-wx"><i class="fl weixin"></i><span class="fl">微信登录</span></p>
                        <p class="fr clearfix" id="login-wb"><i class="fl weibo"></i><span class="fl">微博登录</span></p>
                    </div>
                </div>
                <!--登录后-->
                <div style="@if(isset($uid) && $uid) @else display: none @endif">
                    <h3 class="f22">VRonline登录</h3>
                    <p class="clearfix"><span class="fl">@if(isset($nick)) {{$nick}} @endif</span> ，你好！<a class="fr sign_out" data-id="{{$uid}}" href="{{url("logout?referer=".request()->url())}}">退出</a></p>
                    <p class="f14 recharge btn go_paycenter">充 值</p>
                    <div class="lately">
                            <p class="f16">最近玩过</p>
                            @if(isset($history) && count($history) > 0)
                                @foreach($history as $k=>$info)
                                @if($k < 5)
                                    <p class="clearfix">
                                        <span class="fl game_icon" appid="{{ $info['appid'] }}"><img src="{{ $info['image']['ico'] }}" /></span>
                                        <span class="fl game_name ells" title="{{$info['appname']}}">{{$info['appname']}}</span>
                                        <span class="fl service ells">{{ $info['servername'] }}</span>
                                        <a class="fr start-web-game" appid="{{ $info['appid'] }}" server-id="{{ $info['serverid'] }}" >开始游戏</a>
                                    </p>
                                @endif
                                @endforeach
                            @endif
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content clearfix">
        <div class="fl">
            <div class="hot_games">
                <div class="title clearfix">
                    <i class="fl"></i>
                    <span class="fl f16">最热游戏</span>
                </div>
                <div>
                    <ul>
                        @if(isset($recommend['web-hotgame']['data']) && count($recommend['web-hotgame']['data']) > 0)
                        @foreach($recommend['web-hotgame']['data'] as $hotInfo)
                        <li class="fl">
                            <a href="javascript:;">
                                <div class="pr">
                                    <img src="{{ static_image($hotInfo['image']['card']) }}" class="website-jump" game-id="{{$hotInfo['id']}}" game-name="{{ $hotInfo['name'] }}" />
                                    <p class="clearfix type_score">
                                        <span class="fl">{{ $hotInfo['category'][0] }}</span>
                                        <span class="fr">{{ sprintf('%.1f', $hotInfo['score']) }}分</span>
                                    </p>
                                    <div class="details"  style="height: 24px;">
                                        <!-- <p class="clearfix detail_con">
                                            <span class="fl website-jump" game-id="{{$hotInfo['id']}}">官网</span>
                                            <span class="fr webgame-gift" game-id="{{$hotInfo['id']}}">游戏礼包</span>
                                        </p> -->
                                        <div class="num clearfix">
                                            <p>
                                                <i class="fl"  style="margin-top:5px;"></i>
                                                <span class="fl" style="line-height: 24px;"><em>{{ $hotInfo['play'] }}</em>人在玩</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <p class="clearfix">
                                    <span class="fl game_name go_servers" appid="{{$hotInfo['id']}}" game-name="{{ $hotInfo['name'] }}">{{ $hotInfo['name'] }}</span>
                                    <span class="fr play_games start go_servers" appid="{{$hotInfo['id']}}" game-name="{{ $hotInfo['name'] }}">开始游戏</span>
                                </p>
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="activity">
                <div class="title clearfix">
                    <i class="fl"></i>
                    <span class="fl f16">活动专区</span>
                </div>
                <div>
                    <ul class="clearfix">
                        @if(isset($indexBanner['web-actgame']['data']) && count($indexBanner['web-actgame']['data']) > 0)
                        @foreach($indexBanner['web-actgame']['data'] as $actInfo)
                        <li class="fl">
                            <a href="{{ $actInfo['link'] }}" target="_blank" title="{{ $actInfo['name'] }}">
                                <span><img src="{{ static_image($actInfo['image']['cover']) }}" /></span>
                                <p>{{$actInfo['name']}}</p>
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <!--广告栏-->
            <div class="advertisement" style="">
                <a href="@if(isset($indexBanner['web-ad1']['data'][0]['link'])) {{ $indexBanner['web-ad1']['data'][0]['link'] }} @endif" target="_blank" style="width: 100%;height: 100%" title="@if(isset($indexBanner['web-ad1']['data'][0]['name'])) {{ $indexBanner['web-ad1']['data'][0]['name'] }} @endif">
                    <img src="@if(isset($indexBanner['web-ad1']['data'][0]['image']['cover'])) {{ static_image($indexBanner['web-ad1']['data'][0]['image']['cover']) }} @endif" width="100%" height="100%">
                </a>
            </div>
            <div class="other_games">
                <div class="title clearfix">
                    <i class="fl"></i>
                    <span class="fl f16">其他游戏</span>
                </div>
                <div>
                    <ul class="clearfix">
                    @if(isset($recommend['web-othergame']['data']) && count($recommend['web-othergame']['data']) > 0)
                        @foreach($recommend['web-othergame']['data'] as $othInfo)
                        <li class="fl">
                            <a class="clearfix" href="javascript:;">
                                <div class="fl">
                                    <img src="{{ static_image($othInfo['image']['icon']) }}" class="website-jump" game-id="{{$othInfo['id']}}" game-name="{{ $othInfo['name'] }}" />
                                </div>
                                <div class="fr">
                                    <p class="game_name go_servers" appid="{{$othInfo['id']}}" game-name="{{ $othInfo['name'] }}">{{ $othInfo['name'] }}</p>
                                    <p class="game_type">{{ $othInfo['category'][0] }}</p>
                                    <p class="clearfix">
                                        <span class="fl website-jump" game-id="{{$othInfo['id']}}">官网</span>
                                        <span class="fl enter_game go_servers" appid="{{$othInfo['id']}}" game-name="{{ $othInfo['name'] }}">进入游戏</span>
                                    </p>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="fr">
            <div class="down_vr">
                <div class="title clearfix">
                    <i class="fl"></i>
                    <span class="fl f16">下载VR助手</span>
                    <a class="fr f14" href="//www.vronline.com/customer/service" target="_blank">意见反馈</a>
                </div>
                <div>
                    <div class="pr">
                        <img src="//image.vronline.com/videoimg/10036/19ef3d24c7e6d411d8ca9868eb1ccddf1484644514490.jpg" />
                        <p class="clearfix">
                            <span class="fl">更新时间：</span>
                            <span class="fl">@if(isset($downs['client']) && !empty($downs['client'])) {{ $downs['client']['update_time'] }} @endif</span>
                            <span class="fl">@if(isset($downs['client']) && !empty($downs['client'])) {{ $downs['client']['size'] }} @endif</span>
                        </p>
                    </div>
                    <div class="clearfix install">
                        <a class="fl" href="@if(isset($downs['updateol']) && !empty($downs['updateol'])) {{ $downs['updateol']['address'] }} @endif" target="_blank"><i class="fl"></i><span class="fl">在线安装包</span><span>(@if(isset($downs['updateol']) && !empty($downs['updateol'])) {{ $downs['updateol']['size'] }}  @endif)</span></a>
                        <a class="fl" href="@if(isset($downs['client']) && !empty($downs['client'])) {{ $downs['client']['address'] }} @endif" target="_blank"><i class="fl"></i><span class="fl">完整安装包</span><span>(@if(isset($downs['client']) && !empty($downs['client'])) {{ $downs['client']['size'] }} @endif)</span></a>
                    </div>
                </div>
            </div>
            <!--页游开服表-->
            @include("website.components.web.server", ["num" => 15])
            <!--排行榜-->
            @include("website.components.web.rank",["type"=>"webgame","hide"=>1,"data"=>$recommend["webgame-rank"]["data"]])
            <!--第二个广告位-->
            <div class="receive">
                <a href="@if(isset($indexBanner['web-ad2']['data'][0]['link'])) {{ $indexBanner['web-ad2']['data'][0]['link'] }} @endif" target="_blank"  title="@if(isset($indexBanner['web-ad2']['data'][0]['name'])) {{ $indexBanner['web-ad2']['data'][0]['name'] }} @endif"><img src="@if(isset($indexBanner['web-ad2']['data'][0]['image']['cover'])) {{ static_image($indexBanner['web-ad2']['data'][0]['image']['cover']) }} @endif" title="@if(isset($indexBanner['web-ad2']['data'][0]['name'])) {{ $indexBanner['web-ad2']['data'][0]['name'] }} @endif" /></a>
            </div>
            <div class="recommend">
                <div class="title clearfix">
                    <i class="fl"></i>
                    <span class="fl f16">其他推荐</span>
                </div>
                <div>
                    <ul>
                        @if(isset($indexBanner['web-ad3']['data']) && count($indexBanner['web-ad3']['data']) > 0)
                            @foreach($indexBanner['web-ad3']['data'] as $otherInfo)
                            <li>
                                <a href="@if(isset($otherInfo['link'])) {{ $otherInfo['link'] }} @endif" target="_blank" title="@if(isset($otherInfo['name'])) {{ $otherInfo['name'] }} @endif">
                                    <img src="@if(isset($otherInfo['image']['cover'])) {{ static_image($otherInfo['image']['cover']) }} @endif" title="@if($otherInfo['name']) {{$otherInfo['name']}} @endif" />
                                </a>
                            </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--底部-->
<div></div>
<script type="text/javascript">
webgameLogin.init({
    type:'bind',
    captchaHeight:40,
    showLoginCaptcha:function(img,con){
        $(con + " img").attr('src', img + "?w=" + webgameLogin.config.captchaWidth + "&h=" + webgameLogin.config.captchaHeight + "&v=" + Math.random());
        $(con + " input").val('');
        $(".login-con").css("padding-top","22px");
        $(con).show();
    }
});
</script>
@endsection
