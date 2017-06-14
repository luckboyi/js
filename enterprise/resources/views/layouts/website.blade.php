@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
        <meta name="keywords" content="vr,vronline,vr视频,vr资讯,vr助手,vr开发者,大朋,vr资源,vr电影,vr虚拟现实,vr眼镜,steam,vr游戏">
        <meta name="description" content="VRonline是一家集vr资讯、vr视频、vr资源、vr游戏为一体的综合性VR门户网站，您可以在这里获得最专业的vr资讯、最新的vr视频资源、最全的vr游戏下载。">
        <meta name="renderer" content="webkit">
        @yield('meta')
        <!--页面的Title-->
        <title>@yield('title') - vr虚拟现实第一门户网站 - VRonline.com</title>
        <!--页面的Title End-->
        <link href="{{static_res('/common/style/base.css')}}" rel="stylesheet">
        <link href="{{static_res('/common/style/login.css')}}" rel="stylesheet">
        <link href="{{static_res('/website/min/css/wap.min.css')}}" rel="stylesheet">
        <link href="{{static_res('/website/style/valiant360.css')}}" rel="stylesheet">
        @if(isset($platform)&&$platform=="pc")
        <link href="{{static_res('/client/style/main.css')}}" rel="stylesheet">
        @else
        <link href="{{static_res('/website/style/size.css')}}" rel="stylesheet">
        @endif
        <script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}"></script>
        @if(isset($platform)&&$platform=="pc")
        <script src="{{static_res('/common/js/messenger.js')}}"></script>
        <script src="{{static_res('/client/js/common.js')}}"></script>
        @endif

        @yield('css')
    </head>
    <body @if (isset($homebg) && $homebg) class="home" @elseif(isset($needbg) && $needbg && isset($game['bg']) && $game['bg']) style="background:url({{$game['bg']}}) top center;" bg="{{$game['bg']}}" @endif>
        @if(!isset($platform)||$platform!="pc")
        <!-- <header> -->
        <div class="header">
            <div class='top clearfloat'>
                <div class='fl left'>
                    <a class="home_logo" href="http://www.vronline.com/"><img src='{{static_res('/website/images/logo.png')}}' /></a>
                    {{$blade->websiteTop(isset($current)?$current:"home")}}
                </div>
                <div class='fr right clearfix'>
                    <a class='fl blue' href='{{$client}}'>安装VR助手</a>
                    @if ((!isset($uid) || !$uid))
                        @if(!isset($nologin))
                        <a class='login' href="/login?referer={{ rawurlencode($blade->getLocalUrl()) }}">登录</a>
                        @endif
                    <!--  <a class='language clearfix hide' href="javascript:;">
                        <span>语言</span>
                        <div class="change_language">
                            <p class="chinese">中文</p>
                            <p class="english">英文</p>
                        </div>
                    </a> -->
                    @else
                    <div class='fr login_user'>
                        <div class="clearfix">
                            <span class="user_pic fl">
                                <img id="headFace" src='{{$face}}' width="30" height="30" />
                            </span>
                            <div class="fl user_detail clearfix">
                                <h3 id="headNick">{{ $nick }}</h3>
                                <!-- <div class="grade clearfix">
                                    <span class="fl level2"></span>
                                    <span class="fl level_num">2345</span>
                                </div>-->
                            </div>
                        </div>
                        <ul>
                            <li class="geren datacenter-onclick-stat" stat-actid="click_user_center_button"><a href="{{url("profile")}}"><span></span>个人中心</a></li>
                            {{-- <li class="change"><a href="javascript:;"><span></span>更改语言</a></li> --}}
                            {{-- <li class="switch"><a href="javascript:;"><span></span>切换账户</a></li> --}}
                            <li class="quit datacenter-onclick-stat" stat-actid="click_logout_button"><a href="{{url("logout?referer=".request()->url())}}"><span></span>退出</a></li>
                        </ul>
                    </div>
                @endif
                </div>
            </div>
        </div>
        @endif
        <div class='sec'>
            @if(!isset($platform)||$platform!="pc")
            <div class="sec_nav">
                <div class="nav clearfloat hide">
                    <ul class='fl clearfloat'>
                        <li class="fl">
                            <a href='{{url("vrgame")}}'><span>VR游戏</span></a>
                        </li>
                        <li class="fl">
                            <a href='{{ url("media") }}'><span>多媒体</span></a>
                            <!-- <div class='con'>
                                    <a href='{{ url("media") }}'>全景VR</a>
                                    <a href='{{ url("media") }}'>游戏视频</a>
                                    <a href='{{ url("media") }}'>风景名胜</a>
                            </div> -->
                        </li>
                        <li class="fl">
                            <a href='{{ url("webgame") }}'><span>网页游戏</span></a>
                        </li>
                        <li class="fl">
                            <a href='{{ url("device") }}'><span>设备支持</span></a>
                        </li>
                    </ul>
                    <!--<div class='search fr'>
                                    <input class='txt' type='text' placeholder="请输入游戏名称" />
                                    <input type='submit' value='' class='sub' />
                    </div>-->
                </div>
            </div>
            @endif
            @yield('content')
            @if(!isset($platform)||$platform!="pc")
            <div class="side">
                <div class="sideCon">
                    <p>联系我们</p>
                    <ul>
                        <li>
                            <a href="javascript:;"><span class="service">QQ交流群</span></a>
                            <div class="follow qq_follow">
                                <img src="{{static_res('/website/images/qq.png')}}" />
                            </div>
                        </li>
                        <li>
                            <a href="http://weibo.com/6041941286/profile?topnav=1&wvr=6" target="_blank"><span class="sina">新浪微博</span></a>
                            <div class="follow sina_follow">
                                <img src="{{static_res('/website/images/sina.png')}}" />
                            </div>
                        </li>
                        <li>
                            <a href="javascript:;"><span class="code"></span></a>
                            <div class="follow">
                                <img src="{{static_res('/website/images/VRonline_platform.jpg')}}" />
                                <img style="margin-top:8px;" src="{{static_res('/website/images/VRonline_game.jpg')}}" />
                            </div>
                        </li>
                        <li><a href='#'><span class="up">返回顶部</span></a></li>
                    </ul>
                </div>
            </div>
            @endif
        </div>
        @if((isset($current)&&$current=="home")&&(!isset($platform)||$platform!="pc"))
        <div class="footer">
            <div class='foot'>
                <a class="foot_logo" href="http://www.vronline.com/"><img src="{{static_res('/website/images/logo.png')}}" /></a>
                <div class="aboutUS">
                    <a href="http://www.vronline.com/vronline" target="_blank">关于VRonline</a>
                    <a href="http://www.kingnet.com/" target="_blank">关于恺英</a>
                    <a href="//www.vronline.com/contact" target="_blank">商务合作</a>
                    <a href="//open.vronline.com/" target="_blank">我是开发者</a>
                    <a class='none' href="http://developer.deepoon.com/" target="_blank">大朋开发者网站</a>
                </div>
                <div class="hide">
                    <span>友情链接：</span>
                    <a href="javascript:;">OculusRift贴吧</a>
                    <a href="javascript:;">妖界VRVR</a>
                    <a href="javascript:;">345导航</a>
                    <a href="javascript:;">VR科技网</a>
                    <a href="javascript:;">一起爱VR</a>
                    <a href="javascript:;">VR玩家</a>
                    <a href="javascript:;">智壹VR网</a>
                    <a href="javascript:;">07073VR</a>
                    <a href="javascript:;">VR眼镜vr论坛</a>
                    <a href="javascript:;">vv麦逗VR</a>
                    <a href="javascript:;">VR专业导航网站</a>
                    <a class='none' href="javascript:;">高手VR</a>
                </div>
                <div style="width:300px;margin:0 auto; " >
                    <a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=31011202001649" style="border:0;display:inline-block;text-decoration:none;height:20px;line-height:20px;"><img src="{{static_res('/website/images/pl.png')}}" style="float:left;"/><p  class=""  style="float:left;height:20px;line-height:20px;margin: 0px 0px 0px 5px; ">沪公网安备 31011202001649号</p></a>
                </div>
                <p>
                    <span>沪网文[2013]0667-082</span>
                    <span>沪ICP备16034129号-1</span>
                    <span>文化部网络游戏举报和联系电子邮箱:</span>
                    <a href="Mailto:wlyxjb@gmail.com" style="border: none;">wlyxjb@gmail.com</a>
                </p>
                <p>
                    <span>Copyright&copy;2008-2015 vronline.com All Rights Reserved</span>
                    <span>上海恺英网络科技有限公司 版权所有</span>
                </p>
            </div>
        </div>
        @endif
        @if($platform=="web")
        <script src="{{ static_res('/dist/weball.js') }}"></script>
        @else
        <script src="{{ static_res('/dist/pcall.js') }}"></script>
        @endif
        <script type="text/javascript">
        @if($platform=="pc" && !isset($no_bac_transparent))
        $("body").addClass("bac_transparent");
        @endif
        //百度统计代码
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "https://hm.baidu.com/hm.js?f908059df69511e714bd4bdaf91bcd93";
          var s = document.getElementsByTagName("script")[0];
          s.parentNode.insertBefore(hm, s);
        })();
            //判断PC和移动端
            function browserRedirect() {
                var sUserAgent = navigator.userAgent.toLowerCase();
                var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
                var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
                var bIsMidp = sUserAgent.match(/midp/i) == "midp";
                var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
                var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
                var bIsAndroid = sUserAgent.match(/android/i) == "android";
                var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
                var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
                if ((bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) ){
                    return true;
                }
            }
        var browserRedirect = browserRedirect();
        if(browserRedirect) {
            $(".header .blue").css('display','none');
            $(".login").css('margin-right', '35px');
        }
        </script>
        @yield("javascript")
    </body>
</html>
