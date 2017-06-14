<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title>VRonline-开放平台</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="Mosaddek" name="author" />
    @yield('meta')
    <link rel="stylesheet" href="{{static_res('/open/style/base.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/register.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/personal_center.css')}}">
    @yield('css')
</head>
<body class="personal">
    <header>
        <div class="apply_header">
            <img src="{{static_res('/open/images/logo.png')}}" /><span class="name">@if(isset($user['account'])){{ $user['account'] }}@else{{ $user['name'] }}@endif</span>@if(isset($nologin) && $nologin == 1)@else<span class="logout"><a href="/logout">退出</a></span>@endif
        </div>

    </header>
    <section class='sec'>
        @yield('content')
    </section>
    <footer>
        <div class='cooperate'>
            <a href="javascript:;">合作伙伴</a>
            <a href="javascript:;">合作伙伴</a>
        </div>
        <div>
    		<span> &copy;2010-2015 xxxx.com 版权所有
    		 	<a href="javascript:;">著作权与商标声明 |</a>
    		 	<a href="javascript:;">法律声明 |</a>
    		 	<a href="javascript:;">隐私声明 |</a>
    		 	<a href="javascript:;">关于恺英网络</a>
    		</span>
        </div>
    </footer>
<script language="JavaScript" src="{{static_res('/open/js/jquery-1.12.3.min.js')}}"></script>
<script type="text/javascript" src="{{static_res('/open/js/open.js')}}"></script>
@yield('javascript')
</body>
</html>