@extends('layouts.website')
@inject('blade', 'App\Helper\BladeHelper')

@section("css")
<style>
.webgame_recent_con{
    height: auto;
}
.overview .webgame_recent_con li {
    width: 208px;
    height: 117px;
}
.overview .webgame_recent_con li a {
    width: 206px;
    height: 115px;
    border: 1px solid #000;
}
</style>
@yield("css-webgame")
@endsection

@section('content')
<div class="webgame_con pr clearfix">
    @if($platform=="web"||!isset($noLeft))
    <div class="webgame_left_con fl left-fix-con need-tiny-scroll" id="webgame_left_con" style="position: relative;">
        <div class="scrollbar pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
        <div class="webgame_login viewport" style="height: 300px; overflow: hidden; position: relative;">
            <!--未登录的状态-->
            <?php if (!isset($uid)) {$uid = '';}?>
            @if(!$uid)
                <div class="unlogin_con overview">
                @if($platform == 'web')
                    <div class="webgame_login_con">
                        <h3 class="f14">登录</h3>
                        <div class="webgame_login">
                            <p class="pr">
                                <input type="text" placeholder="手机/VRonline账号"  name="accountnum">
                                <span class="erro_msg erroColor pa"><i class="erro_icon"></i><b>错误信息</b></span>
                            </p>
                            <p class="pr">
                                <input type="password" placeholder="请输入密码"  name="password">
                                <span class="erro_msg erroColor pa"><i class="erro_icon"></i><b>错误信息</b></span>

                            </p>
                            <p class="pr verify_container" style="display: none">
                                <input type="text" placeholder="请输入验证码" name="verifycode">
                                <img id="verify_container" src="">
                                <span class="erro_msg erroColor pa"><i class="erro_icon"></i><b>错误信息</b></span>
                            </p>
                            <p class="clearfix set_login_state">
                                <span class="fl web_auto_login pr f12"><i class="pa has_auto"></i>自动登录</span>
                                <span class="fr forget_psw blueColor f12" onclick="window.location.href='{{ url("forgetpwd") }}'">忘记密码</span>
                            </p>
                            <p class="clearfix login_btn tac">
                                <span class="fl" id="loginBtn"><a href="javascript:;">登录</a></span>
                                <span class="fr"><a href="{{ url('register') }}">注册</a></span>
                            </p>
                        </div>
                    </div>
                    <div class="quick_login">
                        <h3 class="f14">第三方登录</h3>
                        <ul class="clearfix">
                            <li class="fl qq"><a href="http://passport.vronline.com/auth/qq"></a></li>
                            <li class="fl wx"><a href="http://passport.vronline.com/auth/wx"></a></li>
                            <li class="fl weibo"><a href="http://passport.vronline.com/auth/weibo"></a></li>
                        </ul>
                    </div>
                    @endif
                    <div class="webgame_recent">
                        <h3>热门游戏</h3>
                        <div class="in_webgame_recent webgame_list_hei" id="hot_recent">
                            <div class="webgame_recent_con pr hot_game_hei">
                                <div class="hot_game_hei">
                                    <ul>
                                        @if(isset($webgamehot['webgame-hot']['data']) && !empty($webgamehot['webgame-hot']['data']))
                                            @foreach($webgamehot['webgame-hot']['data'] as $info)
                                                <li class="pr start-web-game" game-id="{{$info["id"]}}" server-id=-1 game-name="{{$info["name"]}}">
                                                    <a href="javascript:;">
                                                        <img src="{{ static_image(json_decode($info['image'], true)['logo'], 226) }}" >
                                                        <p class="pa game_title">
                                                            <span class="fl">{{ $info['name'] }}</span>
                                                            <span class="fr"></span>
                                                        </p>
                                                        <!-- <div class="go_btn pa">
                                                            <p class="go_detail show-webgame-detail" appid="{{$info["id"]}}">进入专区</p>
                                                            <p class="start_game cur start-web-game" game-id="{{$info["id"]}}" server-id=-1 game-name="{{$info["name"]}}">开始游戏</p>
                                                        </div> -->
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
                <!--未登录的状态-->
                @else
                <!--登录成功的状态-->
                <div class="suc_login overview">
                    @if($platform == 'web')
                    <div class="user_msg">
                        <h3>登录</h3>
                        <p class="clearfix in_user_msg_con">
                            <span class="fl">{{ $userInfo['nick'] }}</span>
                            <span class="fr blueColor f12" onclick="location.href='{{ url("logout?referer=".request()->url()) }}'">退出</span>
                        </p>
                        <p class="clearfix login_btn tac">
                            <span class="fl" onclick="window.location.href='{{ url("profile") }}'"><a id="personal" href="javascript:;" data-uid="{{ $userInfo['uid'] }}">个人中心</a></span>
                            <span class="fr"><a href="{{ url('charge') }}" data-uid="{{ $userInfo['uid'] }}">充值</a></span>
                        </p>
                    </div>
                    @endif
                    <div class="webgame_recent">
                        @if(isset($webGameHistory) && !empty($webGameHistory))
                        <h3>最近游戏</h3>
                        <div class="in_webgame_recent webgame_list_hei" id="webgame_recent">
                            <div class="webgame_recent_con pr webgame_list_hei">
                                <div class="webgame_list_hei">
                                    <ul>
                                        @foreach($webGameHistory as $list)
                                            <li class="pr start-web-game" game-id="{{$list["appid"]}}" server-id={{ $list['serverid'] }} game-name="{{$list["appname"]}}">
                                                <a href="javascript:;">
                                                    <img src="{{ static_image($list["image"]["logo"],226) }}" >
                                                    <p class="pa game_title">
                                                        <span class="fl">{{ $list['appname'] }}</span>
                                                        <span class="fr">{{ $list['servername'] }}</span>
                                                    </p>
                                                    <!-- <div class="go_btn pa">
                                                        <p class="go_detail show-webgame-detail" appid="{{$list["appid"]}}">进入专区</p>
                                                        <p class="start_game cur start-web-game" game-id="{{$list["appid"]}}" server-id={{ $list['serverid'] }} game-name="{{$list["appname"]}}">开始游戏</p>
                                                    </div> -->
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                        <?php if (isset($webGameHistory) && !empty($webGameHistory)) {$number = 5 - count($webGameHistory);} else { $number = 0;}?>
                        @if($number >= 0)
                        <h3>热门游戏</h3>
                        <div class="in_webgame_recent webgame_list_hei" id="hot_recent">
                            <div class="webgame_recent_con pr hot_game_hei">
                                <div class="hot_game_hei">
                                    <ul>
                                        @if(isset($webgamehot['webgame-hot']['data']) && !empty($webgamehot['webgame-hot']['data']))
                                            @foreach($webgamehot['webgame-hot']['data'] as $k => $info)
                                                <?php 
                                                    if($number != 0 && $k > $number-1){ continue; }
                                                ?>
                                                <li class="pr start-web-game" game-id="{{$info["id"]}}" server-id=-1 game-name="{{$info["name"]}}">
                                                    <a href="javascript:;">
                                                        <img src="{{ static_image(json_decode($info["image"], true)["logo"],226) }}" >
                                                        <p class="pa game_title">
                                                            <span class="fl">{{ $info['name'] }}</span>
                                                            <span class="fr"></span>
                                                        </p>
                                                        <!-- <div class="go_btn pa">
                                                            <p class="go_detail show-webgame-detail" appid="{{$info["id"]}}">进入专区</p>
                                                            <p class="start_game cur start-web-game" game-id="{{$info["id"]}}" server-id=-1 game-name="{{$info["name"]}}">开始游戏</p>
                                                        </div> -->
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <!--未登录的状态-->
            @endif
        </div>
    </div>
    @endif
    @yield('webgameRight')
</div>
@endsection

@section('javascript')
<script type="text/javascript">
   var verifyCodeUrl = "{{ $codeImg }}";
</script>
<script src="{{static_res('/website/js/webgameLogin.js')}}"></script>
@yield("javascript-webgame")
@endsection
