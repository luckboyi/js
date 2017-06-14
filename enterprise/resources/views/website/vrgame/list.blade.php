@inject('blade', 'App\Helper\BladeHelper')
@extends('website.vrgame.layout')

@section('title')VRonline官网@endsection
@section('css-vrgame')
<link rel="stylesheet" href="{{static_res('/website/style/VRgame.css')}}" />
<link rel="stylesheet" href="{{static_res('/website/style/webgame.css')}}" />
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
.autocomplete-suggestions {background: #212b3c; overflow: auto; color: #5a697d; border: inset 1px;border-color: #10141b; box-sizing: border-box;  -webkit-box-sizing: border-box;  -moz-box-sizing: border-box;margin-top:2px;}
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #384355; color:#8196b0;}
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
</style>
@endsection

@section('vrgameRight')
<div class="fr VRgame_home_right">
    <div class="clearfix">
        <div class="fl in_webgame_left_con">
            <!--最新推荐-->
            <div class="new_webgame">
                <div class="webgame_con_head fix-con" fix-left="250">
                    <h3 class="blueColor pr">
                    <i class="pa game_list_icon"></i>
                    <span>游戏库</span>
                    </h3>
                </div>
                <div class="new_webgame_con video_good_con list-con-change-for-fix clearfix">
                    <ul class="clearfix" id="page-content">
                    </ul>
                </div>
            </div>
            <!--最新推荐-->
        </div>
        <div class="fr all-fixed" fix-left="972">
            <!--搜索-->
            <div class="screen">
                <div class="screen_title">
                    <h3><i></i>按类型筛选</h3>
                </div>
                <div class="screen_con">
                    <div class="search">
                        <input type="text" name="name" id="autocomplete" placeholder="请输入游戏名称" />
                        <i class="get-list-btn"></i>
                    </div>
                    <div class="search_bar">
                        <h4 class="blueColor f14">类型：</h4>
                        <ul class="clearfix">
                            <li class="fl filter filter-default {{$class_id?"":"cur"}}" filter-name="category_all" filter-val="1">全部</li>
                            @foreach(config("vrgame.class") as $id=>$class)
                            <li class="fl filter {{($class_id&&$class_id==$id)?"cur":""}}" filter-name="category" filter-val="{{$id}}">{{$class["name"]}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="search_bar">
                        <h4 class="blueColor f14">设备：</h4>
                        <ul class="clearfix">
                            <li class="fl filter filter-default {{$device_id?"":"cur"}}" filter-name="support_all" filter-val="1">全部</li>
                            @foreach(config("vrgame.support_device") as $id=>$device)
                            <li class="fl filter {{($device_id&&$device_id==$id)?"cur":""}}" filter-name="support" filter-val="{{$id}}" title="{{$device["name"]}}">{{$device["name"]}}</li>
                            @endforeach
                        </ul>
                    </div>
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

@section('javascript-vrgame')
{{-- <script src="{{static_res('/website/js/banner.js')}}"></script> --}}
<script src="{{static_res('/common/js/pagination.js')}}"></script>
<script src="{{static_res('/assets/autocomplete/jquery.autocomplete.min.js')}}"></script>
<script type="text/javascript">
    pagination.init({
        type: "scroll", //type=page，普通翻页加载，scroll为滚动加载
        url: "/search", //ajaxType=ajax时为请求地址
        ajaxType:"get",
        ajaxData:{
            tp:"vrgame",
            category:{!!$class_id?:'""'!!},
            support:{!!$device_id?:'""'!!}
        },
        contentHtmlTmp: '<li class="fl pr" game-name="{name}" ">\
                            <a href="/vrgame/{id}">\
                                <img src="{logo}" >\
                            </a>\
                            <p class="clearfix pa title">\
                                <span class="fl ells" title="游戏名">{name}</span>\
                                <span class="play_num fr ells pr" title=""><i class="pa look_icon"></i>{play}</span>\
                            </p>\
                        </li>',
        scorllContent:".new_webgame_con",
        handleData:function(e){
            e.logo=static_image(e.image.logo,100);
            return e;
        },
        first_get_num:30,
        get_num:30,
        offsetTop:$(".new_webgame_con").offset().top
    });

    $(".search_bar li").click(function(){
        $(this).addClass('cur').siblings().removeClass('cur');
        reloadList();
    });


    function reloadList(searchName){
        var _this=this;
        this.data={
            tp:"vrgame"
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
            tp:"vrgame"
        },
        paramName:"name",
        type:"post",
        onSelect: function (suggestion) {
            reloadList(true);
            //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
        }
    });
</script>
@endsection
