@inject('blade', 'App\Helper\BladeHelper')
@include('layouts.baidu_js')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <meta name="keywords" content="vr,vronline,vr视频,vr资讯,vr助手,vr开发者,大朋,vr资源,vr电影,vr虚拟现实,vr眼镜,steam,vr游戏">
    <meta name="description" content="VRonline是一家集vr资讯、vr视频、vr资源、vr游戏为一体的综合性VR门户网站，您可以在这里获得最专业的vr资讯、最新的vr视频资源、最全的vr游戏下载。">
    @yield("meta")
    <link rel="stylesheet" href="{{ static_res('/webgames/style/base.css') }}">
    <link rel="stylesheet" href="{{ static_res('/webgames/style/webgame.css') }}">
    <script src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
     @yield("head")
    <title>@yield('title') - VRonline.com</title>
</head>
<body class="{{isset($appid) ? "webgames_".$appid : ""}} @if(isset($needbg) && $needbg && isset($bgimage) && $bgimage)webgamebg @endif" @if(isset($needbg) && $needbg && isset($bgimage) && $bgimage) style="background:url({{$bgimage}}) no-repeat;" bg="{{$bgimage}}" @endif>
    @yield('content')
    <script src="{{ static_res('/web/js/web.js') }}"></script>
     @yield("javascript")
     @yield("baidu_stat")
</body>
</html>
