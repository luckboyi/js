@inject('blade', 'App\Helper\BladeHelper')
@extends('news.layout')

@section('meta')
<title>{{ $article['title'] }} - vr虚拟现实第一门户网站 - VRonline.com</title>
@endsection


@section('content')
<div class="all_container clearfix">
    <!-- <div class="sec_nav">
        <ul class="clearfix">
            {!! $blade->showHtmlClass('article_all',0,'link2') !!}
        </ul>
    </div> -->
   <div class="sec_nav clearfix">
        <div class="fl">
           {!! $crumbs !!}
        </div>
        <!-- JiaThis Button BEGIN -->
        <div class="jiathis_style fr" style="display: inline-block;line-height: 22px;margin-right: 320px;">
            <span class="jiathis_txt">&nbsp;&nbsp;&nbsp;&nbsp;分享到：</span>
            <a class="jiathis_button_weixin"></a>
            <!-- <a href="http://www.jiathis.com/share" class="jiathis jiathis_txt jiathis_separator jtico jtico_jiathis" target="_blank"></a> -->
            <a class="jiathis_counter_style"></a>
        </div>
        <!-- JiaThis Button END -->
   </div>
    <div class="fl">
        <div class="detail_container">
            <h3 class="f26 ells tac">{{ $article['title'] }}</h3>
            <div class="text_container article_head">
                <div class="author f14 clearfix">
                    <div class="fl">
                        <b class="clo39 titleBorder f12">{{ $blade->showHtmlClass('article',$article['tp']) }}</b>
                        <b>作者：Vronline</b>
                        <b>▪</b>
                        <b class="time">{{ $article['vtime']  }}</b>
                        <!-- <b class="share_icon wx"></b> -->
                    </div>
                </div>
            </div>
            <div class="article_content">
                {!! str_replace('【<b><font color="#ff0000">VRonline讯</font></b>】', '', $article['content']) !!}

            </div>
            <div class="comment_icon tac">
                <ul class="clearfix">
                    <li class=" fl">
                        <p class="like_icon icon support{{$isSupport==="1"?" icon_selected":""}}" support="1"></p>
                        <p class="support-num">{{$article["support"]}}</p>
                    </li>
                    <li class=" fl">
                        <p class="unlike_icon icon support{{$isSupport==="0"?" icon_selected":""}}" support="0"></p>
                        <p class="unsupport-num">{{$article["unsupport"]}}</p>
                    </li>
                </ul>
            </div>
            <div class="detail_foot ">
                <div class="clearfix">
                    <div class="fl">
                        来源： {{ $article['source'] }}
                    </div>
                    <!-- <div class="fr">
                        <span>分享：</span>
                        <span class="wx"></span>
                    </div> -->
                </div>
                <p>
                    声明：www.vronline.com所发布的内容均来源于互联网，目的在于传递信息，但不代表本站赞同其观点及立场，版权归属原作者，如有侵权
                    请联系删除。
                </p>
            </div>
        </div>
         @include("news.components.realtime",['realnews'=>$realnews])
    </div>
    <div class="fr">
         @include("news.components.act",['type'=>'ad','data'=>$recommend['detail-ad1']])
         @include("news.components.hot")
         @include("news.components.act",['type'=>'act','data'=>$recommend['detail-act']])
    </div>
</div>
@endsection


@section('javascript')
<!--轮播-->
<script type="text/javascript">

    var supporting=0;
    var isSupport={{$isSupport===null?0:1}};
    $(function() {
        $(".realnews ul li").hover(function(){
            $(this).addClass('cur').siblings().removeClass('cur');
            var catName = $(this).attr('data-val');
            $(".realnews ol."+catName).show().siblings('ol').hide();
            //console.log(catName)
        });

        $(".support").click(function(){
            if(isSupport>0||supporting==1){
                return false;
            }
            supporting=1;
            isSupport=1;
            $(this).addClass("icon_selected");
            var support=$(this).attr("support");
            if(support==1){
                $(".support-num").text(parseInt($(".support-num").text())+1);
            }else{
                $(".unsupport-num").text(parseInt($(".unsupport-num").text())+1);
            }
            support=parseInt(support);
            $.get("/news/support", {
                id:{{$article['id']}},
                support:support
            }, function(data){
                supporting=0;
                return false;
            },"json")
        })
    });
    var jiathis_config={
        summary:"",
        shortUrl:false,
        hideMore:true
    }
</script>
{{-- <script type="text/javascript" src="http://v3.jiathis.com/code_mini/jia.js" charset="utf-8"></script> --}}
<script type="text/javascript" src="{{ static_res('/common/js/share.js') }}" charset="utf-8"></script>
@endsection
