@extends('client.video.layout')
@section('title')多媒体@endsection
@section('content')



    <div class="multimedia_right_con  video_con_hei pr"  id="video_right_con">
        <div class="scrollbar  video_con_hei pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
        <div class="viewport  video_con_hei">
            <ul class="overview">
                @if(isset($videoInfo) && !empty($videoInfo))
                    @foreach($videoInfo as $vk=>$vv)
                        @if($vk == 0)
                            <li>
                                <div class="videoTitle">
                                    <p>{{ $recommendSort[0]['name'] }}</p>
                                </div>
                                <div class="multimediaCon clearfix">
                                    <div class="fl big bigBgm" data-vid="{{ $vv['info'][0]['video_id'] }}" data-sort="1">
                                        <a href="javascript:;">
                                            <!--	        				<div class="pic_border"></div>-->
                                            <img src="{{ $vv['info'][0]['video_cover'] }}" />
                                            <div class="bigPic_mask"></div>
                                            <div class="video_news">{{ $vv['info'][0]['video_times'] }}</div>
                                            <div class="play"></div>
                                            <p>{{ $vv['info'][0]['video_intro'] }}</p>
                                        </a>
                                    </div>
                                    <ul class="fl clearfix min">
                                        @foreach($vv['info'] as $jk=>$jv)
                                            @if($jk != 0)
                                                <li class="fl minBgm"  data-vid="{{ $jv['video_id'] }}" data-sort="1">
                                                    <a href="javascript:;">
                                                        <img src="{{ $jv['video_cover'] }}" />
                                                        <div class="minPic_mask"></div>
                                                        <div class="min_video_news clearfix">
                                                            <span class="fl look">{{ $jv['video_view'] }}</span>
                                                            <span class="fr">{{ $jv['video_times'] }}</span>
                                                        </div>
                                                        <div class="play"></div>
                                                        <p class="textCon">{{ $jv['video_intro'] }}</p>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if($vk == 1)
                            @foreach($vv['info'] as $sk=>$sv)
                                <li class="marginTop">
                                    <div class="videoTitle clearfix">
                                        <p class="fl">{{ $recommendSort[1][$sk]['name'] }}</p>
                                        <span class="fl">共{{ $vv['info'][$sk]['num'] }}部视频</span>
                                        <a class="fr" href="{{ url('client/video/index/' . $sk) }}">更多></a>
                                    </div>
                                    <div class="multimediaCon clearfix">
                                        <div class="fl big bigBgm" data-vid="{{ $sv[0]['video_id'] }}" data-sort="{{ $sk }}">
                                            <a href="javascript:;">
                                                <!--	        				<div class="pic_border"></div>-->
                                                <img src="{{ $sv[0]['video_cover'] }}" />
                                                <div class="bigPic_mask"></div>
                                                <div class="video_news">{{ $sv[0]['video_times'] }}</div>
                                                <div class="play"></div>
                                                <p>{{ $sv[0]['video_name'] }}</p>
                                            </a>
                                        </div>
                                        <ul class="fl clearfix min">
                                            @foreach($sv as $skk=>$skv)
                                                @if($skk != 0)
                                                    <li class="fl minBgm" data-vid="{{ $skv['video_id'] }}" data-sort="{{ $sk }}">
                                                        <a href="javascript:;">
                                                            <img src="{{ $skv['video_cover'] }}" />
                                                            <div class="minPic_mask"></div>
                                                            <div class="min_video_news clearfix">
                                                                <span class="fl look">{{ $skv['video_view'] }}</span>
                                                                <span class="fr">{{ $skv['video_times'] }}</span>
                                                            </div>
                                                            <div class="play"></div>
                                                            <p class="textCon">{{ $skv['video_name'] }}</p>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">

        $(".bigBgm,.minBgm").click(function(){
            var videoId = $(this).attr("data-vid"),
                    sort = $(this).attr("data-sort");
            window.location.href="http://www.vronline.com/client/video/player?vid=" + videoId + "&vsort=" + sort;
        });

        /*$(function(){
            //获取浏览器高度

            videoResize();

            $(window).resize(function(){
                videoResize();
            })


        });
        function videoResize(){
            var winHeight,winWidth;
            if (window.innerHeight){
                winHeight = window.innerHeight;
            }else if ((document.body) && (document.body.clientHeight)) {
                winHeight = document.body.clientHeight;
            }
            /!*if (window.innerWidth){
             winHeight = window.innerWidth;
             }else if ((document.body) && (document.body.clientWidth)){
             winWidth = document.body.clientWidth;
             }*!/
            console.dir($('#video_right_con .overview').width());
            $('.video_con_hei').height(winHeight);
            $('#video_right_con').tinyscrollbar();
            $('#video_left_con').tinyscrollbar();

        }*/
    </script>
@endsection