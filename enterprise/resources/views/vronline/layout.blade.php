@inject('blade', 'App\Helper\BladeHelper')
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	@yield("meta")
	<link rel="stylesheet" href="{{ static_res('/vronline/style/base.css') }}">
	<link rel="stylesheet" href="{{ static_res('/vronline/style/head.css') }}"   />
	<link rel="stylesheet" href="{{ static_res('/vronline/style/style.css') }}">
	<link rel="stylesheet" href="{{ static_res('/vronline/style/swiper.min.css') }}">
	<script src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
	<script src="{{ static_res('/vronline/js/swiper.min.js') }}"></script>
   @yield("head")
</head>
<body>
<!--导航开始-->
<div id="header" class="mb20">
  <div class="top-bar new-wrap @if(isset($index)) w_1100 @else  w_1200 @endif clearfix">
  <a class="logo" href="/"><img src="{{ static_res('/vronline/images/logo.png') }}" alt=""></a>
    <div class="header-bar">
      <div class="search-wrap"><input class="text" type="text" id="hssearch" value="@if(isset($words)) {{ $words }} @endif" placeholder="请输入您想要的信息"><button class="search-btn" type="submit"></button></div>
      <div class="sign-area fr"><a href="https://www.vronline.com/login" class="login">登录</a> | <a href="https://www.vronline.com/register"  class="register">注册</a></div>
    </div>
  </div>
  <div class="nav-wrap @if(isset($index)) w_1100 @endif">
  <div class="border"></div>
    <div class="new-wrap w_1200">
      <ul class="nav"><li class="li"><h3><a href="/vronline/index" >首页</a></h3>
      </li>
      <li class="li"><h3><a href="/vronline/article" >业界资讯</a><i></i></h3>
      <ul class="sub">
      <li></li>
      <li><a href="/vronline/article/list/1">行业动态</a></li>
      <li><a href="/vronline/article/list/2">人物专访</a></li>
      <li><a href="/vronline/article/list/3">投资创业</a></li>
      <li><a href="/vronline/article/list/4">数据分析</a></li>
      <li><a href="/vronline/article/list/5">VR独家</a></li>
      <li><a href="/vronline/article/list/6">游戏专区</a></li>
      </ul></li>
      <li class="li"><h3><a href="/vronline/game" target="_blank">游戏频道</a><i></i></h3>
      <ul class="sub"><li></li>
       <li><a href="/vronline/game/list" target="_blank">游戏库</a></li>
       <li><a href="/vronline/pc/list/1" target="_blank">游戏评测</a></li>
       <li><a href="/vronline/pc/list/2" target="_blank">硬件评测</a></li>

      </ul></li>
      <li class="li"><h3><a href="/vronline/video" >视频专区</a><i></i></h3>
      <ul class="sub"><li></li>
      <li><a href="#" >原创栏目</a></li>
      <li><a href="#">3D播播</a></li>
      <li><a href="#">游戏试玩</a></li>
      <li><a href="#" target="_blank">报道专访</a></li>
      <li><a href="#" target="_blank">VR视界</a></li>
      </ul></li>
      <li class="li"><h3><a href="http://www.vronline.com/vronline" target="_blank">VR助手</a></h3>
      </li>
      <li class="li"><h3><a href="https://open.vronline.com" target="_blank">开发者</a></h3>
      </li>
      </ul>
    </div>
  </div>
</div>
@yield('content')
<!-- 底部 -->
<div class="foot">
    <div class="VR_foot">
        <div class="aboutUS">
            <a href="http://www.vronline.com/vronline" target="_blank">关于VR助手</a>
            <a href="http://www.kingnet.com" target="_blank">关于恺英</a>
            <a href="//www.vronline.com/contact" target="_blank">商务合作</a>
            <a href="//open.vronline.com" target="_blank">我是开发者</a>
            <a href="//www.vronline.com/parent_intro" target="_blank">家长监护</a>
            <a href="http://developer.deepoon.com/" target="_blank">大朋开发者</a>
            <a href="//www.vronline.com/customer/service" target="_blank">客服中心</a>
        </div>
        <p>VRonline平台：适合12岁及以上成年人游戏，建议游戏者适当游戏。</p>
        <p>抵制不良游戏，拒绝盗版游戏，注意自我保护，谨防受骗上当；适度游戏益脑，沉迷游戏伤身，合理安排时间，享受健康生活！</p>
        <br>
        <div class="police">
        <a class="clearfix" target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=31011202001649">
            <img class="fl" src="http://pic.vronline.com/website/images/pl.png?57">
            <p class="fl">沪公网安备 31011202001649号</p>
        </a>
        </div>
        <p>
        <span>沪网文[2016] 2600-152号</span>
        <span>沪ICP备10215773号-37</span>
        <span>文化部网络游戏举报和联系电子邮箱:</span>
        <a href="Mailto:wlyxjb@gmail.com">wlyxjb@gmail.com</a>
        </p>
        <p>
        <span>Copyright©2008-2015 vronline.com All Rights Reserved</span>
        <span>上海恺英网络科技有限公司 版权所有</span>
        </p>
        <p>
            <span>上海陈行路2388号浦江科技广场3号3F&nbsp;&nbsp;&nbsp;</span>
            <span>客服电话：021-54310366-8065</span>
        </p>
    </div>
</div>

@yield("javascript")
<script type="text/javascript">
  $(".search-btn").click(function(){
    var words = $("#hssearch").val();
    location.href = "/vronline/search/"+words
  })
   $("#hssearch").keypress(function() {
  if(event.keyCode==13) {
     var words = $(this).val();
     location.href = "/vronline/search/"+words;
  }
  });
</script>
</body>
</html>
