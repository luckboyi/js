<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>第三方后台</title>
    <link rel="stylesheet" href="{{static_res('/open/style/base.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/personal_center.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/jason.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/cikonss.css')}}" />
    <script language="JavaScript" src="{{static_res('/common/js/jquery-1.11.3.min.js')}}"></script>
</head>

<body>
    <!--侧边栏S-->
    <div class="siderbar tac wordColor">
        <a href="javascript:;"></a>
        <ul>
            <li class="cur pr"><i></i>基本资料</li>
            <li class="pr "><i class="bank"></i>银行账号</li>
        </ul>
    </div>
    <!--侧边栏E-->
    <!--顶部栏S-->
    <div class="personal_header clearfix">
        <div class="nav_con fl">
            <ul class="clearfix">
                <li class="fl"><a href="javascript:;">首页</a></li>
                <li class="fl"><a href="javascript:;">产品列表</a></li>
                <li class="fl"><a href="javascript:;">数据查询</a></li>
                <li class="fl"><a href="javascript:;">产品审核</a></li>
            </ul>
        </div>
        <div class="login_con fr clearfix">
            <span class="fl">
                <img src="{{ isset($face) ? $face : 'http://imgsrc.baidu.com/forum/w%3D580%3B/sign=7f91b36bfad3572c66e29cd4ba286227/eaf81a4c510fd9f9a26ab1eb2d2dd42a2834a437.jpg' }}">
            </span>
            <p class="fl">@if (isset($nick) && $nick) {{ $nick }} @else {{ @account }} @endif</p>
        </div>
    </div>
    <!--顶部栏E-->
    @yield('content')