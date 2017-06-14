@extends('layouts.website')
@inject('blade', 'App\Helper\BladeHelper')

@section("css")
@yield("css-vrgame")
<style type="text/css">

</style>
@endsection

@section('content')
<div class="VRgame_home clearfix">
    <div class="fl VRgame_home_left need-tiny-scroll left-fix-con" style="position: relative;">
        <div class="in_webgame_recent in_vr_list_hei">
            <div class="scrollbar pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
            <div class="webgame_recent_con pr in_vr_list_hei viewport">
                <div class="in_vr_list_hei pa overview">
                    @if(isset($playHistory) && is_array($playHistory) && count($playHistory)>0)
                    <div class="VR_games">
                        <p class="clearfix">
                            <i class="fl VR_game_icon VR_games_left"></i>VR游戏<i class="fr VR_game_icon VR_games_right active"></i>
                        </p>
                        <div class="VR_games_con" style="display: block;">
                                <ul>
                                    @foreach ($playHistory as $history)
                                        <li>
                                            <a href="/vrgame/{{ $history['appid'] }}">
                                                <img src="@if(isset($history['images']['logo'])){{static_image($history['images']['logo'],466)}}@endif" />
                                                <p class="clearfix">
                                                    <span class="fl">{{$history['name']}}</span>
                                                    <span class="fr"><b>评分：</b>{{ number_format($history['score'], 1) }}</span>
                                                </p>
                                                <!-- <span class="price @if(isset($history['sell']) && $history['sell'] == 0) free @endif">@if(isset($history['sell']) && $history['sell'] == 0) 免费 @else ￥{{ $history['sell'] }} @endif</span> -->
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                        </div>
                    </div>
                    @endif
                    <div class="hot_game">
                        <h3 class="f14">推荐游戏</h3>
                        <div class="VR_games_con" style="display: block;">
                            @if (isset($vrgameLeftRecommend["data"]) && is_array($vrgameLeftRecommend["data"]))
                            <ul>
                                @foreach ($vrgameLeftRecommend["data"] as $key=>$content)
                                <a href="/vrgame/{{ $content['id'] }}">
                                        <img src="{{static_image($content["image"]["logo"],466)}}" />
                                        <p class="clearfix">
                                            <span class="fl">{{$content["name"]}}</span>
                                            <span class="fr"><b>评分：</b>{{ number_format($content["score"], 1)}}</span>
                                        </p>
                                        <!-- <span class="price @if(isset($history['sell']) && $history['sell'] == 0) free @endif">@if($content["sell"]==0) 免费 @else ￥{{$content["sell"]}} @endif</span> -->
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @yield('vrgameRight')
</div>
@endsection

@section('javascript')
<script type="text/javascript">
//左侧VR游戏列表
$(".VR_games>p").click(function(){
$(this).next('.VR_games_con').toggle();
$(".VR_games_right").toggleClass("active");
});
</script>
@yield("javascript-vrgame")
@endsection
