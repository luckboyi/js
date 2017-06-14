@inject('blade', 'App\Helper\BladeHelper')
@extends('website.media.layout')

@section('title')VRonline官网@endsection

@section("css")
<style type="text/css" >
.webgame_con_head.has-fixed{
    width: 730px;
    background:rgba(32,66,107,.3);
    z-index: 99;
}
.screen{
    margin-top: 0px;
}
.all-fixed{
    position: fixed;
    top:148px;
    margin-left:15px;
}
.autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
</style>
@endsection

@section('videoLeft')
<div class="fr VRgame_home_right">
    <div class="clearfix">
        <div class="fl in_webgame_left_con">
            <!--最新推荐-->
            <div class="new_webgame assortment">
                <div class="webgame_con_head fix-con" fix-left="250">
                    <h3 class="blueColor pr">
                        <i class="pa game_list_icon"></i>
                        <span>{{ $cateGoryName }} </span>
                    </h3>
                </div>
                <div class="new_webgame_con video_good_con clearfix list-con-change-for-fix">
                    <ul class="clearfix" id="page-content">
                    {{-- @if(isset($medias) && is_array($medias))
                    @foreach($medias as $content)
                        <li class="fl pr show-video-detail" video-id="{{$content["video_id"]}}">
                            <a href="javascript:;">
                                <img src="{{ static_image($content['video_cover'],226) }}" >
                            </a>
                            <p class="clearfix pa title">
                                <span class="fl ells" title="{{ $content['video_name'] }}">{{ $content['video_name'] }}</span>
                              <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{{ $content['video_view'] }}</span>
                            </p>
                            <i class="play"></i>
                        </li>
                    @endforeach
                    @endif --}}
                    </ul>
                </div>
            </div>
            <!--最新推荐-->
        </div>
        <div class="fr all-fixed" fix-left="972" style="width: 228px;">
            <!--搜索-->
            <div class="screen">
                <div class="screen_title">
                    <h3><i></i>按类型筛选</h3>
                </div>
                <div class="screen_con">
                    <div class="search">
                        <input type="text" name="name" id="autocomplete" placeholder="请输入视频名称" />
                        <i class="get-list-btn"></i>
                    </div>
                    <div class="search_bar">
                        <h4 class="blueColor f14">类型：</h4>
                        <ul class="clearfix">
                            <li class="fl filter filter-default {{$class_id?"":"cur"}}" filter-name="category_all" filter-val="1"  title="全部">全部&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
                            @foreach(config("video.class") as $id=>$class)
                            <li class="fl filter {{($class_id&&$class_id==$id)?"cur":""}}" filter-name="category" filter-val="{{$id}}" title="{{$class['name']}}">{{$class["name"]}}</li>
                            @endforeach
                        </ul>
                    </div>
                    {{-- <div class="search_bar">
                        <h4 class="blueColor f14">支持VR：</h4>
                        <ul class="clearfix">
                            <li class="fl filter cur" filter-name="support_all" filter-val="1">全部</li>
                            <li class="fl filter" filter-name="support" filter-val="1">VR</li>
                            <li class="fl filter" filter-name="support" filter-val="0">普通</li>
                        </ul>
                    </div> --}}
                    <div class="search_bar">
                        <h4 class="blueColor f14">字母：</h4>
                        <ul class="clearfix">
                            <li class="fl filter" filter-name="spell_all" filter-val="1">全部</li>
                            <li class="fl filter" filter-name="spell" filter-val="a b c d">ABCD</li>
                            <li class="fl filter" filter-name="spell" filter-val="e f g h">EFGH</li>
                            <li class="fl filter" filter-name="spell" filter-val="i j k l">IJKL</li>
                            <li class="fl filter" filter-name="spell" filter-val="m n o p">MNOP</li>
                            <li class="fl filter" filter-name="spell" filter-val="q r s t">QRST</li>
                            <li class="fl filter" filter-name="spell" filter-val="u v w x">UVWX</li>
                            <li class="fl filter" filter-name="spell" filter-val="y z">YZ</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('javascript-media')
<script src="{{static_res('/website/js/banner.js')}}"></script>
<script src="{{static_res('/common/js/pagination.js')}}?{{Config::get('staticfiles.file_version')}}"></script>
<script src="{{static_res('/assets/autocomplete/jquery.autocomplete.min.js')}}"></script>
<script>
    $(function(){
        //轮播
        $('.home_banner').bannerVideo();
        $('.hotList_con ul li').hover(function(){
            $(this).addClass('cur').siblings().removeClass('cur')
        });
        pagination.init({
            type: "scroll", //type=page，普通翻页加载，scroll为滚动加载
            url: "/search", //ajaxType=ajax时为请求地址
            ajaxType:"get",
            ajaxData:{
                tp:"video",
                category:{!!$class_id?:'""'!!}
            },
            contentHtmlTmp: '<li class="fl pr show-video-detail" video-id="{id}">\
                                <a href="javascript:;">\
                                    <img src="{cover}" >\
                                </a>\
                                <p class="clearfix pa title">\
                                    <span class="fl ells" title="{name}">{name}</span>\
                                  <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{play}</span>\
                                </p>\
                                <div class="shade_layer">\
                                </div>\
                                <i class="play"></i>\
                            </li>',
            contentHtmlContainer: "#page-content", //生成html后存放的容器
            handleData:function(e){
                e.cover=static_image(e.image.cover);
                return e;
            },
            first_get_num:30,
            get_num:30,
            scorllContent:".new_webgame_con",
            offsetTop:$(".new_webgame_con").offset().top
        });

        $(".search_bar li").click(function(){
            $(this).addClass('cur').siblings().removeClass('cur');
            reloadList();
        });

        function reloadList(searchName){
            var _this=this;
            this.data={
                tp:"video"
            }
            if(searchName){
                var name=$("input[name='name']").val();
                if(name){
                    this.data.name=name;
                }else{
                    this.data.name_null="1";
                }
                $(".search_bar li").removeClass('cur');
                $(".filter-default").addClass('cur');
            }
            $(".filter.cur").each(function(i,e){
                var name=$(e).attr("filter-name");
                var val=$(e).attr("filter-val");
                if(name&&val){
                    _this.data[name]=val;
                }
            });

            window.pagination.reload({
                ajaxData:_this.data
            });
        }

        $(".get-list-btn").click(function(){
            reloadList(true);
        });

        $("input[name='name']").keydown(function(e){
            if(e.keyCode==13){
                reloadList(true); //处理事件
            }
        });

        $('#autocomplete').autocomplete({
            serviceUrl: "/suggest",
            params:{
                tp:"video"
            },
            paramName:"name",
            type:"post",
            onSelect: function (suggestion) {
                reloadList(true);
                //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
            }
        });
    });
</script>
@endsection
