@inject('blade', 'App\Helper\BladeHelper')
@include('layouts.baidu_js')
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
        <meta name="keywords" content="vr,vronline,vr视频,vr资讯,vr助手,vr开发者,大朋,vr资源,vr电影,vr虚拟现实,vr眼镜,steam,vr游戏">
        <meta name="description" content="VRonline是一家集vr资讯、vr视频、vr资源、vr游戏为一体的综合性VR门户网站，您可以在这里获得最专业的vr资讯、最新的vr视频资源、最全的vr游戏下载。">
        <meta name="renderer" content="webkit">
        <title>关于VRonline - VRonline - vr虚拟现实第一门户网站 - VRonline.com</title>
        <link href="{{static_res('/common/style/base.css') }}" rel="stylesheet">
        <link href="{{static_res('/website/min/css/wap.min.css') }}" rel="stylesheet">
            </head>
    <body >
    <!--头部-->
    <div class="head">
        <div class="pr clearfix">
            <a class="fl" href="javascript:;"><i class="logo"></i></a>
            <ul class="fl">
                <li class="fl"><a href="http://www.vronline.com/">首页</a></li>
                <li class="fl zx">
                    <a href="http://www.vronline.com/news/list/0">资讯<i class="zixun"></i></a>
                    <div class="list_con clearfix">
                        {!! $blade->showHtmlClass('article_ex','','link1') !!}
                    </div>
                </li>
                <li class="fl"><a href="http://www.vronline.com/news/list/6">游戏</a></li>
                <li class="fl"><a href="http://www.vronline.com/news/list/7">视频</a></li>
                <li class="fl"><a href="https://open.vronline.com" target="_blank">开发者</a></li>
                <li class="fl cur"><a href="javascript:;" >VR助手</a></li>

            </ul>
            <div class="fr head_right clearfix">
                <a class="fl pr wx_hover">
                    <i class="weixin"></i>
                    <p class="code_bg wx_code_bg">
                        <img src="{{ static_res('/news/images/VRonline_platform.jpg') }}">
                        <img src="{{ static_res('/news/images/VRonline_game.jpg') }}">
                    </p>
                </a>
                <a class="fl pr wb_hover">
                    <i class="weibo"></i>
                    <p class="code_bg wb">
                        <img src="{{ static_res('/news/images/sina.png') }}">
                    </p>
                </a>
            </div>
        </div>
    </div>

    <div class="VRonline" >
    <img class="img" src="{{static_res('/website/images/VRonlineTopBg.png')}}" />
    <div class="VRonline_down">
        <div class="install">
            <p class="welcome">欢迎至VRonline 娱乐平台</p>
            <p>享受虚拟现实、游戏体验、合作发布及更多</p>
            <p class="system clearfix"><i class="fl"></i><span class="fl">Windows</span></p>
            <div class="clearfix">
                <a class="fl" href="{{$toolurl}}">
                    <i class="online_install"></i>
                    <p><span>大小: {{$toolsize}}M</span><span>版本号: {{$version}}</span></p>
                </a>
                <a class="fr" href="{{$whileurl}}">
                    <i class="complete_install"></i>
                    <p><span>大小: {{$whilesize}}M</span><span>版本号: {{$version}}</span></p>
                </a>
            </div>
        </div>
    </div>
    <ul class="clearfix">
        <li class="fl VRgames">
            <a class="li_a">
                <div class="frame"></div>
                <div class="gamespic"></div>
                <div class='games_mask'></div>
                <div>
                    <span>
                        <p>体验VR游戏</p>
                    </span>
                    <p class="VRgamesTxt">我们将不断为广大玩家提供<br />丰富的VR游戏体验， <br />通过VR设备连接，体验身临其境的游戏感受。<br />另外，还可以享受到游戏下载、安装、<br />自动更新以及更多的特色服务。</p>
                </div>
            </a>
        </li>
        <li class="fl VRvideos">
            <a class="li_a">
                <div class="frame"></div>
                <div class="gamespic"></div>
                <div class='games_mask'></div>
                <div>
                    <span>
                        <p>丰富的VR视频</p>
                    </span>
                    <p class="VRgamesTxt">不出家门，您可以在VRonline上找到 <br />丰富的视频资源，<br />浏览全景视频、<br />游戏视频和世界各地的风景名胜之地。</p>
                </div>
            </a>
        </li>
        <li class="fl share">
            <a class="li_a">
                <div class="frame"></div>
                <div class="gamespic"></div>
                <div class='games_mask'></div>
                <div>
                    <span>
                        <p>分享与发布</p>
                    </span>
                    <p class="VRgamesTxt">我们为开发者提供一个专注于<br />VR游戏的自主发布平台， <br />将为每位开发者<br />提供私人定制的平台特色服务<br />和游戏管理综合服务。</p>
                </div>
            </a>
        </li>
    </ul>
</div>
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
                <div style="padding: 0 20px;line-height: 24px;color: #7a838a;margin: -60px 0 40px 0">上海恺英网络科技有限公司[1]  (股票代码:002517)成立于2008年，总部位于上海，是一家拥有移动互联网流量入口、集平台运营和产品研发为一体的互联网企业。公司拥有近千人的团队，开发并运营了《摩天大楼》、《蜀山传奇》、[2]  《全民奇迹》[3]  等多款热门游戏，旗下有业内领先的多款互联网平台型产品，例如XY游戏、XY苹果助手等。公司已经实现了向互联网多平台运营商以及由 PC 端到移动端的转型，并成功打造了“流量获取-流量经营-流量变现”的闭环互联网生态系统。2015年11月，恺英网络完成A股上市。[4]  2016年恺英网络制定了“平台+内容+VR/AR”三大战略。</div>
            </div>

        </div>

    </body>
</html>
@yield("baidu_stat")