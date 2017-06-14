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
                        <span class="title fl">游戏图标：</span>
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
                <div class="product-row clearfix">
                    <p class="clearfix">
                        <span class="title fl">游戏详情图标：</span>
                        <span>在游戏详情页的中间显示 <font color="red">推荐 160x70 JPG,PNG 格式</font></span>
                    </p>
                    <div class="pic-con clearfix" id="slogo_container">
                        @if(isset($detail["slogo"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["slogo"]) }}" target="_blank"><img src="{{ static_image($detail["slogo"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="slogo_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">160x70 JPG,PNG</span>
                            <span class="pic-txt underline">上传图片</span>
                        </div>
                    </div>
                </div>
                 <div class="product-row clearfix">
                    <p class="clearfix">
                        <span class="title fl">游戏卡片：</span>
                        <span>在热门游戏以及游戏列表显示 <font color="red">推荐 150*216 JPG,PNG 格式</font></span>
                    </p>
                    <div class="pic-con clearfix" id="card_container">
                        @if(isset($detail["card"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["card"]) }}" target="_blank"><img src="{{ static_image($detail["card"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="card_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">150*216 JPG,PNG</span>
                            <span class="pic-txt underline">上传图片</span>
                        </div>
                    </div>
                </div>
                <div class="product-row clearfix" >
                    <p class="clearfix">
                        <span class="title fl">游戏ICON：</span>
                        <span>游戏ICON <font color="red">128*128 PNG 格式，圆角</font></span>
                    </p>
                    <div class="pic-con clearfix" id="icon_container">
                        @if(isset($detail["icon"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["icon"]) }}" target="_blank"><img src="{{ static_image($detail["icon"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="icon_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">128*128 PNG</span>
                            <span class="pic-txt underline" >上传图片</span>
                        </div>
                    </div>
                </div>
                <div class="product-row clearfix" >
                    <p class="clearfix">
                        <span class="title fl">游戏小ICON：</span>
                        <span>游戏小ICON <font color="red">16*16 PNG 格式，圆角</font></span>
                    </p>
                    <div class="pic-con clearfix" id="ico_container">
                        @if(isset($detail["ico"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["ico"]) }}" target="_blank"><img src="{{ static_image($detail["ico"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="ico_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">16*16 PNG</span>
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
                        <span class="title fl">游戏背景2：</span>
                        <span>游戏的背景图片 <font color="red">推荐 1920*1080 JPG,PNG 格式</font></span>
                    </p>
                    <div class="pic-con clearfix" id="bg2_container">
                        @if(isset($detail["bg2"]))
                           <div class='pic fl preview'><a href="{{ static_image($detail["bg2"]) }}" target="_blank"><img src="{{ static_image($detail["bg2"]) }}" /></a></div>
                        @endif
                        <div class='pic fl' id="bg2_browser">
                            <span class="pic-icon"></span>
                            <span class="pic-txt">1920*1080  JPG,PNG</span>
                            <span class="pic-txt underline">上传图片</span>
                        </div>
                    </div>
                </div>
                <div class="product-row clearfix">
                    <p class="clearfix">
                        <span class="title fl">截图轮播图：</span>
                        <span>在游戏中轮播的截图 <font color="red">600*340 JPG,PNG 格式,4-8张以内</font></span>
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
                <div class="product-row clearfix">
                    <p class="clearfix">
                        <span class="title fl">宣传轮播图：</span>
                        <span>在游戏中的截图 <font color="red">600*340 JPG,PNG 格式,4-8张以内</font></span>
                    </p>
                    <div class="pic-con clearfix " id="screenshots_container">
                        @if(isset($detail["screenshots"]) and $detail["screenshots"])
                            @foreach($detail["screenshots"] as $v)
                            <div class='pic fl preview'><span class="close">x</span><a href="{{ static_image($v) }}" target="_blank"><img src="{{ static_image($v) }}" /></a></div>
                            @endforeach
                        @endif
                        <div class='pic fl' id="screenshots_browser">
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
            <button type="button" class="btn btn-webgame-save-res">保存</button>
        </div>
    </div>
    <object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="/js/Somethingtest.swf" style="visibility: visible;"></object>
    <script>
    var appid = {{ $detail["appid"] }};
    var stat = {{ $detail["stat"] }};
    var operr = "资料审核中 无法修改"
    $(function(){
     $(".btn-webgame-save-res").click(function(){
        var logo,img_icon,img_ico,img_rank,bg,bg2,card,slogo;
        var slider = [];
        var screenshots = [];
        var obj =  $("#logo_container").find("img");
        if(obj.length>0) {
            logo =  obj.attr("src")
        }
        var obj =  $("#slogo_container").find("img");
        if(obj.length>0) {
            slogo =  obj.attr("src")
        }
        var obj =  $("#card_container").find("img");
        if(obj.length>0) {
            card =  obj.attr("src")
        }
        obj =  $("#icon_container").find("img");
        if(obj.length>0) {
            img_icon =  obj.attr("src")
        }
         obj =  $("#ico_container").find("img");
        if(obj.length>0) {
            img_ico =  obj.attr("src")
        }
        obj =  $("#rank_container").find("img");
        if(obj.length>0) {
            img_rank =  obj.attr("src")
        }
        obj =  $("#bg_container").find("img");
        if(obj.length>0) {
            bg =  obj.attr("src")
        }
        obj =  $("#bg2_container").find("img");
        if(obj.length>0) {
            bg2 =  obj.attr("src")
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
        obj =  $("#screenshots_container").find("img");
        if(obj.length>=2) {
            $.each(obj,function(i,j){
               var n =  Open.getImageName(j.src);
               if(n) {
                    screenshots.push(n);
               }
            })
        }
        if(typeof(logo)=="undefined" || typeof(slogo)=="undefined" || typeof(img_icon)=="undefined" || typeof(img_ico)=="undefined" || typeof(img_rank)=="undefined" || typeof(bg)=="undefined" || typeof(bg2)=="undefined" || typeof(card)=="undefined" || slider.length<4 || slider.length>8 || screenshots.length<2 || screenshots.length>8) {
            Open.showMessage("资源未上传完整");
            return;
        } else {
            $.post(Open.urls.webGameSave+"/res/"+appid,{logo:logo,slogo:slogo,img_icon:img_icon,img_ico:img_ico,img_rank:img_rank,card:card,bg:bg,b2g:bg2,screenshots:JSON.stringify(screenshots),slider:JSON.stringify(slider)},function(data){
            if(data.code==0) {
                Open.navigation.webGameDetail("all",appid);
            } else {
                Open.showMessage(data.msg);
            }
        },"json")
        }
    });

        var logo_obj = new loiUploadContainer({
            container:"logo_container",
            choose:"logo_browser",
            ext:"jpg,png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"logo"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#logo_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#logo_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });
        var slogo_obj = new loiUploadContainer({
            container:"slogo_container",
            choose:"slogo_browser",
            ext:"jpg,png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"slogo"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#slogo_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#slogo_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });
        var card_obj = new loiUploadContainer({
            container:"card_container",
            choose:"card_browser",
            ext:"jpg,png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"card"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#card_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#card_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });

        var icon_obj = new loiUploadContainer({
            container:"icon_container",
            choose:"icon_browser",
            ext:"png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"icon"},success:function(json){
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

         var ico_obj = new loiUploadContainer({
            container:"ico_container",
            choose:"ico_browser",
            ext:"png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"ico"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#ico_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#ico_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });


        var rank_obj = new loiUploadContainer({
            container:"rank_container",
            choose:"rank_browser",
            ext:"jpg,png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"rank"},success:function(json){
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
            container:"bg_container",
            choose:"bg_browser",
            ext:"png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"bg"},success:function(json){
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

        var bg2_obj = new loiUploadContainer({
            container:"bg2_container",
            choose:"bg2_browser",
            ext:"png",
            upload:{tp:"webgame",addParams:{appid:appid,"pic":"bg2"},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                var previewObj = $("#bg2_container").find(".preview");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'" width="130px"/></a>');
                } else {
                    $("#bg2_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
                }
            },error:function(){}},
              filesAdd:function(){}
        });


        var slider_obj = new loiUploadContainer({
            container:"slider_container",
            choose:"slider_browser",
            ext:"jpg,png",
            upload:{tp:"webgame",addParams:{appid:appid},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                $("#slider_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
            },error:function(){}},
              filesAdd:function(f){
                console.log(f);
              }
        });

         var screenshots_obj = new loiUploadContainer({
            container:"screenshots_container",
            choose:"screenshots_browser",
            ext:"jpg,png",
            upload:{tp:"webgame",addParams:{appid:appid},success:function(json){
                var jsonResult = $.parseJSON(json);
                var path = jsonResult.data.fileid;
                var access_url = img_domain+path+'?v='+Open.randVersion();
                $("#screenshots_container").prepend('<div class="pic fl preview"><a href="'+access_url+'" target="_blank"><img src="'+access_url+'" /></a></div>');
            },error:function(){}},
              filesAdd:function(f){
                console.log(f);
              }
        });
    });
    </script>
@endsection
