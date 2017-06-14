@extends('news.layout')

@section('meta')
<title>资讯 - VRonline - vr虚拟现实第一门户网站 - VRonline.com</title>
@endsection

@section('content')
<div class="all_container clearfix">
       <!--  <div class="sec_nav">
            <ul class="clearfix">
                @if(isset($classArticle) && !empty($classArticle))
                    @foreach($classArticle as $class)
                        <li class="@if(isset($catId) && $class['id'] == $catId) cur @endif listClass fl" data-catid="{{ $class['id'] }}">
                            <a href="javascript:;"  class="f14 tac">{{ $class['name'] }}</a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div> -->
        <div class="sec_nav"> {!! $crumbs !!} </div>
        <div class="fl">
            <ul class="all_list_container" id="page-content">
                @if(isset($catData) && !empty($catData))
                    @foreach($catData as $info)
                        <li class="clearfix">
                            <div class="img_container fl">
                                <a href="javascript:;" class="catDetail" data-id="{{ $info['id'] }}">
                                    <img src="{{static_image($info['cover'],384)}}" width="100%" height="100%">
                                </a>
                            </div>
                            <div class="fr text_container">
                                <a href="javascript:;" class="title ells f20 catDetail" data-id="{{ $info['id'] }}">{{ $info['title'] }}</a>
                                <span  class="intro">
                                    {{ htmlSubStr($info['content'], 200) . "..." }}
                                </span>
                                <div class="author f14">
                                    <div class="fl">
                                        <b class="clo39 titleBorder f12">@if($info['tp'] !== '' && isset($classArticle[$info['tp']]['name'])) {{ $classArticle[$info['tp']]['name'] }} @else 未知 @endif</b>
                                        <b>&nbsp;&nbsp;作者：Vronline</b>
                                        <b>▪</b>
                                        <b class="time">{{ date('Y-m-d', strtotime($info['vtime'])) }}</b>
                                    </div>
                                   <!--  <div class="fr pr f12 interested_tag clearfix">
                                        <span class="fl">不感兴趣</span>
                                        <i class="fr close_btn pa"></i>
                                    </div> -->
                                </div>
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
            <p class="loading_more tac i-need-more" style="display: none">
                <a href="javascript:;" class="f14">正在加载更多……</a>
            </p>
        </div>
        <div class="fr">
            @include("news.components.act",['type'=>'ad','data'=>$recommend['detail-ad1']])
            @include("news.components.hot")
            @include("news.components.act",['type'=>'act','data'=>$recommend['detail-act']])
        </div>
    </div>
@endsection

@section('javascript')
<script src="{{static_res('/common/js/pagination.js')}}"></script>
<script type="text/javascript">
    pagination.init({
        type: "scroll", //type=page，普通翻页加载，scroll为滚动加载
        url: "/news/list/more/{{$catId}}", //ajaxType=ajax时为请求地址
        ajaxType:"get",
        page:2,
        contentHtmlTmp:'<li class="clearfix">\
                            <div class="img_container fl">\
                                <a href="javascript:;" class="catDetail" data-id="{id}">\
                                    <img src="{cover}" width="100%" height="100%">\
                                </a>\
                            </div>\
                            <div class="fr text_container">\
                                <a href="javascript:;" class="title ells f20 catDetail" data-id="{id}">{title}</a>\
                                <a href="javascript:;" class="intro catDetail" data-id="{id}">\
                                    {desc}\
                                </a>\
                                <div class="author f14">\
                                    <div class="fl">\
                                        <b class="clo39 titleBorder f12">{tp_name}</b>\
                                        <b>作者：kingnetVr</b>\
                                        <b>▪</b>\
                                        <b class="time">{time}</b>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
        first_get_num:10,
        get_num:10,
        offsetTop:-$(".foot").outerHeight(),
        addLoading:"",
        loadingObj:$(".loading_more"),
        showSpeed:"normal",
        loadingTime:800,
    });

    // (function(){
    //     var _this=this;
    //     this.page=1;
    //     this.pageSize={{$pageSize}};
    //     this.loading=0;
    //     this.listTemp='<li class="clearfix cur">\
    //                     <div class="img_container fl">\
    //                         <a href="javascript:;" class="catDetail" data-id="{id}">\
    //                             <img src="{cover}" width="100%" height="100%">\
    //                         </a>\
    //                     </div>\
    //                     <div class="fr text_container">\
    //                         <a href="javascript:;" class="title ells f20 catDetail" data-id="{id}">{title}</a>\
    //                         <a href="javascript:;" class="intro catDetail" data-id="{id}">\
    //                             {desc}\
    //                         </a>\
    //                         <div class="author f14">\
    //                             <div class="fl">\
    //                                 <b class="clo39 titleBorder f12">{tp_name}</b>\
    //                                 <b>作者：kingnetVr</b>\
    //                                 <b>▪</b>\
    //                                 <b class="time">{time}</b>\
    //                             </div>\
    //                         </div>\
    //                     </div>\
    //                 </li>';

    //     $(".i-need-more").click(function(){
    //         if(_this.loading==1){
    //             return false;
    //         }
    //         _this.loading=1;
    //         _this.page++;
    //         $.get('/news/list/more/{{$catId}}',{
    //             "page":_this.page
    //         }, function(data){
    //             if(data.code==0){
    //                 var html="";
    //                 if(!data.data || data.data.length<{{$pageSize}}){
    //                     $(".i-need-more").remove();
    //                 }
    //                 $(data.data).each(function(i,e){
    //                     html+=tmpReplace(_this.listTemp,e);
    //                 });
    //                 $(".all_list_container").append(html);

    //             }else{
    //                 $(".i-need-more").remove();
    //             }
    //             _this.loading=0;
    //         });
    //     });

    //     var tmpReplace = function(tmp, data) {
    //         return tmp.replace(/\\?\{([^{}]+)\}/g, function(match, name) {
    //             return (data[name] === undefined) ? '' : data[name];
    //         });
    //     }
    // })();
</script>
@endsection
