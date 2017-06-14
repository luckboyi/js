<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>开放平台</title>
    <link rel="stylesheet" href="{{static_res('/open/style/base.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/personal_center.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/jason.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/cikonss.css')}}" />
    <script type="text/javascript" src="{{static_res('/open/js/jquery-1.12.3.min.js')}}"></script>
    @yield('head')
</head>
<body>
    <!--侧边栏S-->
    <div class="siderbar tac wordColor">
        <a href="http://www.vronline.com"></a>
        @if (isset($nav) && $nav == "review")
        <ul>
            <li class="@if(isset($tag) && $tag == 'webgame') cur @endif pr btn-review-product"><i class="pageGame"></i>页游审核</li>
            <li class="@if(isset($tag) && $tag == 'vrgame') cur @endif pr btn-review-vrgame"><i class="VR_product"></i>VR审核</li>
            <!-- <li class="@if(isset($tag) && $tag == 'video') cur @endif pr btn-review-video"><i class="bank"></i>视频审核</li> -->
            <li class="@if(isset($tag) && $tag == 'user') cur @endif pr btn-review-user"><i class="bank"></i>用户审核</li>
        </ul>
        @elseif (isset($nav) && $nav == "product")
        <ul>
            <li class="@if(isset($tag) && $tag == 'webgame') cur @endif pr btn-offline-product"><i></i>网页游戏</li>
            <li class="@if(isset($tag) && $tag == 'vrgame') cur @endif pr btn-offline-vr"><i></i>VR产品</li>
        </ul>
        @elseif (isset($nav) && $nav == "my")
        <ul>
            <li class="cur pr"><i></i>基本资料</li>
        </ul>
        @elseif (isset($nav) && $nav == "account")
        <ul>
            <li class="@if(isset($tag) && $tag == 'list') cur @endif pr btn-sub-account"><i></i>账号管理</li>
            <li class="@if(isset($tag) && $tag == 'perm') cur @endif pr btn-sub-account-perm"><i></i>权限管理</li>
        </ul>
        @endif
    </div>
    <!--侧边栏E-->
    <!--顶部栏S-->
    <div class="personal_header clearfix">
        <div class="nav_con fl">
            <ul class="clearfix">
                <li class="fl btn-my @if(isset($nav) && $nav == 'my') cur @endif"><a href="javascript:;">开发者信息</a></li>
                <li class="fl btn-offline-product @if(isset($nav) && $nav == 'product') cur @endif"><a href="javascript:;">产品列表</a></li>
                @if(!isset($user['parentid']))
                <li class="fl btn-sub-account @if(isset($nav) && $nav == 'account') cur @endif"><a href="javascript:;">子账号管理</a></li>
                @endif
                <!-- <li class="fl"><a href="javascript:;">数据查询</a></li> -->
                @if(in_array(1,$user['perm']))
                <li class="fl btn-review-product @if(isset($nav) && $nav == 'review') cur @endif"><a href="javascript:;">资质审核</a></li>
                @endif
            </ul>
        </div>
        <div class="login_con fr clearfix">
            <span class="fl">
                <img src="{{ $user['face'] }}">
            </span>
            <p class="fl">{{ $user['nick'] }}</p>
            <p class="exit fl btn-logout">退出</p>
        </div>
    </div>
    <!--顶部栏E-->
     @yield('content')

     <div class="side">
    <ul>
        <li class="qq">
            <a href="javascript:;">
                <span></span>
                <p>技术支持</p>
                <img src="{{ static_res('/open/images/helper.png') }}" />
            </a>
        </li>
        <li class="VRgame">
            <a href="//open.vronline.com/sdk/vronlineapi_sdk.zip" target="_blank"  >
                <span></span>
                <p>VR游戏SDK</p>
            </a>
        </li>
        <li class="webgame">
           <a href="//open.vronline.com/sdk/VRonline_doc.md" target="_blank"  >
                <span></span>
                <p>网页游戏SDK</p>
            </a>
        </li>
    </ul>
</div>
 </body>

<script type="text/javascript" src="{{static_res('/open/js/open.js')}}"></script>
<script type="text/javascript">
    //百度统计代码
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?f908059df69511e714bd4bdaf91bcd93";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(hm, s);
    })();
</script>
@yield('javascript')
</html>
