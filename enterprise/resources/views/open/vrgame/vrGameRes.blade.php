@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/base/swfobject.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/base/md5.js') }}"></script>
@endsection

@section('content')
 <div class="container">
        <div class="in_con">
            <h4 class="f14">产品详情</h4>
            <div class="content product-detail">
                <p>
                     <span class="product-title">{{ $detail["name"] }}</span>
                     <span class="product-id">APP ID:{{ $detail["appid"] }}</span>
                     {!! $blade->showStat($detail["stat"],"style") !!}
                       @if($detail["stat"]==3) {{ $detail["msg"] }} @endif
                </p>
                <p>
                    <span>创建时间：{{ $detail["ctime"] }}</span>
                    <span>上线时间：{{ $blade->showDateTime($detail["send_time"]) }}</span>
                </p>
                <p>
                     <span class="product-id">APP KEY:{{ $detail["appkey"] }}</span>
                    <span class="product-id">PAY KEY:{{ $detail["paykey"] }}</span>
                </p>
            </div>
        </div>
        <div class="in_con">
            <h4 class="f14">图标素材</h4>
            <div class="content product-detail product-detail-info">
                <div class="product-row clearfix">
                    <p class="clearfix">
                        <span class="title fl">游戏LOGO：</span>
                        <span>在热门游戏以及游戏列表显示 <font color="red">推荐 480x270 JPG,PNG 格式</font></span>
                    </p>
                    <div class="pic-con clearfix" id="logo_container">
                        @if(isset($detail["logo"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["logo"]) }}" target="_blank"><img src="{{ static_image($detail["logo"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="logo_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">480x270 JPG,PNG</span>
                            <span class="pic-txt underline">上传图片</span>
                        </div>
                    </div>
                </div>
                 <div class="product-row clearfix" >
                    <p class="clearfix">
                        <span class="title fl">游戏ICON：</span>
                        <span>游戏ICON <font color="red">128*128 JPG,PNG 格式，圆角</font></span>
                    </p>
                    <div class="pic-con clearfix" id="icon_container">
                        @if(isset($detail["icon"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["icon"]) }}" target="_blank"><img src="{{ static_image($detail["icon"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="icon_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">128*128 JPG,PNG </span>
                            <span class="pic-txt underline" >上传图片</span>
                        </div>
                    </div>
                </div>
                 <div class="product-row clearfix" >
                    <p class="clearfix">
                        <span class="title fl">游戏排行：</span>
                        <span>在游戏排行中显示的图片 <font color="red">238*60 JPG,PNG 格式</font></span>
                    </p>
                    <div class="pic-con clearfix" id="rank_container">
                        @if(isset($detail["rank"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["rank"]) }}" target="_blank"><img src="{{ static_image($detail["rank"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="rank_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">238*60 JPG,PNG</span>
                            <span class="pic-txt underline" >上传图片</span>
                        </div>
                    </div>
                </div>
                <div class="product-row clearfix">
                    <p class="clearfix">
                        <span class="title fl">游戏背景：</span>
                        <span>游戏的背景图片 <font color="red">推荐 1920*1080 JPG,PNG 格式</font></span>
                    </p>
                    <div class="pic-con clearfix" id="bg_container">
                        @if(isset($detail["bg"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["bg"]) }}" target="_blank"><img src="{{ static_image($detail["bg"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="bg_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">1920*1080  JPG,PNG</span>
                            <span class="pic-txt underline">上传图片</span>
                        </div>
                    </div>
                </div>
               <div class="product-row clearfix">
                    <p class="clearfix">
                        <span class="title fl">详情轮播图：</span>
                        <span>在游戏中轮播的图片 <font color="red">600*340 JPG,PNG 格式,4-8张以内</font></span>
                    </p>
                    <div class="pic-con clearfix " id="slider_container">
                        @if(isset($detail["slider"]) and $detail["slider"])
                            @foreach($detail["slider"] as $v)
                            <div class='pic fl preview'><span class="close">x</span><a href="{{ static_image($v) }}" target="_blank"><img src="{{ static_image($v) }}" /></a></div>
                            @endforeach
                        @endif
                        <div class='pic fl' id="slider_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">600*340 JPG,PNG</span>
                            <span class="pic-txt underline">上传图片</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="button-con" >
            <button type="button" class="btn btn-history">返回</button>
            <button type="button" class="btn btn-vrgame-save-res">保存</button>
        </div>
    </div>
    <object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="/js/Somethingtest.swf" style="visibility: visible;"></object>
    <script>
    var appid = {{ $detail["appid"] }};
    var stat = {{ $detail["stat"] }};
    var operr = "资料审核中 无法修改"
    $(function(){
     $(".btn-vrgame-save-res").click(function(){
          var logo,img_icon,bg,img_rank;
        var slider = [];
        var obj =  $("#logo_container").find("img");
        if(obj.length>0) {
            logo =  obj.attr("src")
        }
        obj =  $("#icon_container").find("img");
        if(obj.length>0) {
            img_icon =  obj.attr("src")
        }
        obj =  $("#bg_container").find("img");
        if(obj.length>0) {
            bg =  obj.attr("src")
        }
        obj =  $("#rank_container").find("img");
        if(obj.length>0) {
            img_rank =  obj.attr("src")
        }
        obj =  $("#slider_container").find("img");
        if(obj.length>=4) {
            $.each(obj,function(i,j){
               var n =  Open.getImageName(j.src);
               if(n) {
                    slider.push(n);
               }
            })
        }
        if(typeof(logo)=="undefined"  || typeof(img_rank)=="undefined" || typeof(bg)=="undefined" || typeof(img_icon)=="undefined" || slider.length<4 || slider.length>8) {
            Open.showMessage("资源未上传完整");
            return;
        } else {
            $.post(Open.urls.vrGameSave+"/res/"+appid,{logo:logo,img_rank:img_rank,img_icon:img_icon,bg:bg,slider:JSON.stringify(slider)},function(data){
            if(data.code==0) {
                Open.navigation.vrGameDetail("all",appid);
            } else {
                Open.showMessage(data.msg);
            }
        },"json")
        }
     });

         var logo_obj = new loiUploadContainer({
            id:"logo",
            upload:{tp:"vrgameimg",addParams:{appid:appid,"assign":"logo"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#logo_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#logo_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}}
        });


        var icon_obj = new loiUploadContainer({
            id:"icon",
            upload:{tp:"vrgameimg",addParams:{appid:appid,"assign":"icon"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#icon_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#icon_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });


          var rank_obj = new loiUploadContainer({
            id:"rank",
            upload:{tp:"vrgameimg",addParams:{appid:appid,"assign":"rank"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#rank_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#rank_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });

         var bg_obj = new loiUploadContainer({
            id:"bg",
            upload:{tp:"vrgameimg",addParams:{appid:appid,"assign":"bg"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#bg_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#bg_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });





        var slider_obj = new loiUploadContainer({
            id:"slider",
            upload:{tp:"vrgameimg",addParams:{appid:appid},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                $("#slider_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
            },error:function(){}},
              filesAdd:function(f){
                console.log(f);
              }
        });

    })
    </script>
@endsection
