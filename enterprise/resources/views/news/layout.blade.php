@inject('blade', 'App\Helper\BladeHelper')
@include('layouts.baidu_js')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="vr,vronline,vr视频,vr资讯,vr助手,vr开发者,大朋,vr资源,vr电影,vr虚拟现实,vr眼镜,steam,vr游戏">
    <meta name="description" content="VRonline是一家集vr资讯、vr视频、vr资源、vr游戏为一体的综合性VR门户网站，您可以在这里获得最专业的vr资讯、最新的vr视频资源、最全的vr游戏下载。">
    @yield("meta")
    <link rel="stylesheet" href="{{ static_res('/news/min/style/wap.min.css') }}">
     @yield("head")
</head>
<body>
    <!--广告位-->
    <div></div>
    <!--头部-->
    <div class="head" @if(Request::path() == 'parent_intro') style="display: none" @endif>
        <div class="pr clearfix">
            <a class="fl" href="javascript:;"><i class="logo"></i></a>
            <ul class="fl">
                <li class="fl"><a href="//www.vronline.com/">首页</a></li>
                <li class="fl zx">
                    <a href="//www.vronline.com/news/list/0">资讯<i class="zixun"></i></a>
                    <div class="list_con clearfix">
                        {!! $blade->showHtmlClass('article_ex','','link1') !!}
                    </div>
                </li>
                <li class="fl"><a href="//www.vronline.com/news/list/6">游戏</a></li>
                <li class="fl"><a href="//www.vronline.com/news/list/7">视频</a></li>
                <li class="fl"><a href="https://open.vronline.com" target="_blank">开发者</a></li>
                <li class="fl"><a href="//www.vronline.com/vronline" target="_blank">VR助手</a></li>
                <!-- <li class="fl"><a href="#" target="_blank">客服中心</a></li> -->
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
     @yield('content')
    <!-- <div class="friend_link">
        <div class="title clearfix">
            <h3 class="fl">友情链接</h3>
        </div>
    </div> -->
    <div class="foot">
        <div class="VR_foot">
            <!-- <div class="aboutUS">
            <a href="http://www.kingnet.com" target="_blank">关于VRonline</a>
            <a  href="http://www.kingnet.com" target="_blank">关于恺英</a>
            </div> -->
            <div class="aboutUS">
                <a href="{{ url('vronline') }}" target="_blank">关于VR助手</a>
                <a href="http://www.kingnet.com" target="_blank">关于恺英</a>
                <a href="//www.vronline.com/contact" target="_blank">商务合作</a>
                <a href="https://open.vronline.com" target="_blank">我是开发者</a>
                <a href="//www.vronline.com/parent_intro" target="_blank">家长监护</a>
                <a href="http://developer.deepoon.com/" target="_blank">大朋开发者</a>
                <a href="//www.vronline.com/customer/service" target="_blank">客服中心</a>
            </div>
            <p>VRonline平台：适合12岁及以上成年人游戏，建议游戏者适当游戏。</p>
            <p>抵制不良游戏，拒绝盗版游戏，注意自我保护，谨防受骗上当；适度游戏益脑，沉迷游戏伤身，合理安排时间，享受健康生活！</p>
            </br>
            <div class="police">
            <a class="clearfix" target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=31011202001649">
                <img class="fl" src="{!! static_res('/website/images/pl.png') !!}" />
                <p class="fl">沪公网安备 31011202001649号</p>
            </a>
            </div>
            <p>
            <span>沪网文[2016] 2600-152号</span>
            <span>沪ICP备10215773号-37</span>&nbsp;&nbsp;<span>沪IAS备201703010012</span>
            <span>文化部网络游戏举报和联系电子邮箱:</span>
            <a href="Mailto:wlyxjb@gmail.com">wlyxjb@gmail.com</a>
            </p>
            <p>
            <span>Copyright&copy;2008-2015 vronline.com All Rights Reserved</span>
            <span>上海恺英网络科技有限公司 版权所有</span>
            </p>
            <p>
                <span>上海陈行路2388号浦江科技广场3号3F&nbsp;
&nbsp;
&nbsp;
</span>
                <span>客服电话：021-54310366-8065</span>
            </p>
        </div>
    </div>
    <script src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
    <script src="{{ static_res('/news/js/index.bottom.js') }}"></script>
    @yield("javascript")
</body>
</html>
@yield("baidu_stat")
