@extends('layouts.website')
@inject('blade', 'App\Helper\BladeHelper')

@section('content')
<div class="webgame_con pr clearfix">
    <div class=" fl video_con left-fix-con">
        <!-- <div class="history_record">
            <a class="local_players" href="javascript:;"><i class="local_players_icon"></i>本地播放器</a>
            <a class="clearfix" href="{{ url('mediaHistory') }}"><i class="fl history_icon_left"></i>历史记录<i class="fr history_icon_right"></i></a>
        </div> -->
        <div class="video_con_left">
            <ul>
                <li class="history_record">
                    <a class="clearfix datacenter-onclick-stat" href="{{ url('mediaHistory') }}" stat-actid="click_video_history_button"><i class="fl iconLeft history_icon_left"></i><span>历史记录</span><i class="fr iconRight"></i></a>
                </li>
               @if(isset($mediaCateGorys) && is_array($mediaCateGorys))
                    @foreach($mediaCateGorys as $content)
                        <li class="show-video-class" class-id="{{ $content["id"] }}">
                            <a class="clearfix" href="javascript:;">
                                <i class="fl iconLeft {{ $content['class'] }}_icon"></i>
                                <span>{{ $content['name'] }}</span>
                                <i class="fr iconRight"></i>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
    @yield('videoLeft')
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(".choice-tab").click(function(){
    $(this).addClass('tab_titleBg').siblings().removeClass('tab_titleBg');
    var id=$(this).attr("tag-id");
    $(".tag-content").hide();
    $("#"+id).show();
});

</script>
@yield("javascript-media")
@endsection
