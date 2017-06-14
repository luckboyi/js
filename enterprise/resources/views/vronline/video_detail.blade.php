@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - 视频播放</title>
@endsection

@section("head")
<link href="{{ static_res('/vronline/style/video.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ static_res('/videoGame/style/valiant360.css') }}">
<script src="{{ static_res('/vronline/js/xb_scroll.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ static_res('/website/js/three.min.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/website/js/jquery.valiant360.js') }}"></script>
<script src="{{ static_res('/assets/loi/message.js') }}"></script>
<script src="{{ static_res('/vronline/js/comment.js') }}"></script>
<script src="{{ static_res('/vronline/js/vronline.js') }}"></script>
@endsection

@section('content')
<div class="videoInfo-videoBg">
            <div class="w_1200 ov">
                <p class="title mb20">{{ $videoInfo['article_title']}}</p>
                <div class="video-embed fl  pr">
                    <div class="video_con pa" id="video_con" style="width:100%; height: 100%;">
                        @if($videoInfo['article_video_source_tp'] == 2)
                        {!! $videoInfo['article_video_source_url'] !!}
                        @else
                            @if($videoInfo['article_video_tp']<=2)
                                <video class="" src="{{ $videoInfo['article_video_source_url'] }}" controls="controls" height="580" width="880" loop=""><video>
                            @else
                               <div class="valiantPhoto" data-video-src="{{ $videoInfo['article_video_source_url'] }}" style="width:100%; height: 100%;"><i class="close_btn pa"></i></div>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="video-moreList fr">
                    <div class="h3"><span class="tit fl">推荐视频</span></div>
                    <div class="panel1" id="videolist">
                    <ul class="panel-box">
                        @if(isset($videoInfoAfter) && count($videoInfoAfter)>0)
                            @foreach($videoInfoAfter as $av)
                            <li>
                                <a href="/vronline/video/detail/{{$av['itemid']}}" title="{{$av['title']}}"><span class="img-wrap fl"><img src="{{static_image($av['cover'])}}"></span><span class="info-wrap fr"><span class="tit dot">{{$av['title']}}</span><span class="info"><em class="v-count">{{number_format($videoInfo['article_view_num'])}}次播放</em></span></span></a>
                            </li>
                            @endforeach
                        @else

                        @endif
                    </ul></div>
                </div></div>
                <!--播放器下方工具条-->
                <div class="toolbar w_1200 mt10">
                    <span class="info">
                        <em class="v-count fl">@if(isset($videoInfo['article_view_num'])) {{ number_format($videoInfo['article_view_num']) }} @endif次播放　|</em>
                        <a href="javascript::" class="fl"><em class="rise action_support" suppport-act="up" target-type="news_video" suppport-itemid={{intval($videoInfo['article_id'])}}>@if(isset($videoInfo['article_agree_num'])) {{ number_format($videoInfo['article_agree_num']) }} @endif</em></a>
                        <a href="javascript::" class="fl"><em class="drop action_support" suppport-act="down" target-type="news_video" suppport-itemid={{intval($videoInfo['article_id'])}}>@if(isset($videoInfo['article_disagree_num'])) {{ number_format($videoInfo['article_disagree_num']) }} @endif</em>
                    </a></span>
                </div>
            </div>

<div class="new-wrap w_1200 ov">
            <div class="left-wrap fl">
              <!--评论区-->
                <div class="game_resource_commend_con">
                    <div class="add_word">
                        <p class="title">我有话说：</p>
                        <textarea placeholder="我有话要说......" class="words" id="words" name="txb_Content0"></textarea>
                        <input type="button" value="评论" id="btn_commentadd" class="send"  name="send" data-id="0" group="g0" data-qpid="0" data-qid="0"><a id="zxpl"></a>
                    </div>
                    <div class="comment mt20">
                      <h2 class="comment_heading">
                          <span class="title">最新评论</span>
                      </h2>
                      <div id="comment_con">
                        <div id="in_comment_con"></div>
                        <div class="comment commMore2" id="load_more" style="display:none;"><a class="commMoreA"  href="javascript:;">加载更多</a></div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="right-wrap fr">
               <div class="ad-top mt30">
                        @if(isset($tops['video-detail-ad']) && count($tops['video-detail-ad'])>0)
                            @foreach($tops['video-detail-ad'] as $ad)
                            <a href="{{$ad['target_url']}}" title="{{$ad['title']}}" target="_blank">
                                <img width="320" height="auto" src="{{static_image($ad['cover'])}}">
                            </a>
                            @endforeach
                        @endif
                    </div>
                    <div class="h2-wrap mt20">
                        <h2>相关推荐</h2>
                    </div>
                    <div class="panel">
                        <ul>
                        @if(isset($tops['video-detail-recommend']) && count($tops['video-detail-recommend'])>0)
                            @foreach($tops['video-detail-recommend'] as $recommend)
                            <li><a href="{{$ad['target_url']}}" title="{{$recommend['title']}}"><span class="img-wrap fl"><img src="{{static_image($recommend['cover'])}}"></span><span class="info-wrap fr"><span class="tit dot">{{$recommend['title']}}</span><span class="info"><em class="v-count">{{number_format($recommend['view'])}}次播放</em></span></span></a></li>
                            @endforeach
                        @endif
                        </ul>
                    </div>
            </div>
        </div></div>
@endsection

@section('javascript')

<script type="text/javascript">
    $('.valiantPhoto').Valiant360({clickAndDrag:true,muted:false});
</script>
<script>
    /*固定高度*/
    $("#videolist").xb_scroll();

    function elemAdd()
    {
      $(".panel1 .panel-box")
    }

    function elemRemove()
    {
      $(".panel1 .panel-box li").last().remove();
    }

    // 评论
    Comment.init({
        userid:'{{$uid}}',
        target_id:'{{$videoInfo['article_id']}}',
        type:'news_video',
    });
    statPV('news_video', '{{$videoInfo['article_id']}}'); //统计视频的pv

    $(function(){
        $("#video_con embed").css("width","100%");
        $("#video_con embed").css("height","100%");
        $("#video_con iframe").css("width","100%");
        $("#video_con iframe").css("height","100%")
    })
</script>
@endsection
