<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @yield('meta')
    <!--页面的Title-->
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ static_res('/common/style/base.css') }}">
    <link href="{{ static_res('/website/style/valiant360.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ static_res('/client/style/client.css') }}">
</head>
<body>
<div class=" clearfix multimedia_left_con " id="video_container">
    <div class="left_con fl video_con_hei pr ul_bg" id="video_left_con">
        <p class="pa left_con_btn clearfix">
            <a href="{{ url('client/video/history') }}" class="fl" id="video_history">历史记录</a>
            {{--<a href="{{ url('client/video/playerlocal') }}" class="fr">本地播放器</a>--}}
        </p>
        <div class="scrollbar  video_con_hei pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
            <div class="viewport  video_con_hei " >
                <ul class="overview ">
                    @if(isset($recommendSort) && !empty($recommendSort))
                        @foreach($recommendSort as $rk=>$rv)
                            @if($rk == 0)
                                <li class="@if(isset($_GET['vsort']) && $_GET['vsort'] == 1) cur @elseif(isset($_GET['vsort']) && ($_GET['vsort'] == 'no' || $_GET['vsort'] > 100)) @elseif(!isset($sort)) cur @endif">
                                    <a href="{{ url('client/video/index') }}">
                                    <span class="fl">
                                        <img src="{{ $rv['show_pic'] }}"  width="56" height="56" alt="">
                                    </span>
                                        <div class="title fl">
                                            <p class="name">{{ $rv['name'] }}</p>
                                        </div>
                                    </a>
                                </li>
                            @endif
                            @if($rk == 1)
                                @foreach($rv as $sk=>$sv)
                                    <li class="@if(isset($_GET['vsort']) && $_GET['vsort'] == $sv['id']) cur @elseif(isset($sort) && $sort == $sv['id']) cur @endif">
                                        <a href="{{ url('client/video/index/' . $sv['id']) }}">
                                        <span class="fl">
                                            <img src="{{ $sv['img'] }}"  width="56" height="56" alt="">
                                        </span>
                                            <div class="title fl">
                                                <p class="name">{{ $sv['name'] }}</p>
                                                <p class="service" title="{{ $sv['name'] }}">{{ $sv['name'] }}</p>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    @yield('content')
    </div>
</div>
</body>
</html>
<script src="{{ static_res('common/js/jquery-1.12.3.min.js'） }}"></script>
<script src="{{ static_res('/website/js/three.min.js'） }}"></script>
<script src="{{ static_res('/client/js/jquery.valiant360.js'） }}"></script>
<script src="{{ static_res('/website/js/tinyscrollbar.js'） }}"></script>
<script src="{{ static_res('/website/js/bannerVideo.js'） }}"></script>
<script src="{{ static_res('/client/js/video_platform.js'） }}"></script>
<script src="{{ static_res('/client/js/interface.js'） }}"></script>
<script src="{{ static_res('/common/js/datacenter_stat.js'） }}"></script>

<!--最后添加的js代码-->
@yield('javascript')
<script>

</script>
