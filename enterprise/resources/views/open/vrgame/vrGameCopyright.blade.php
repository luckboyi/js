@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')

@section('head')
    <style>
    #shortInput{
        width: 40px;
    }
      .whole p{
        text-indent: 2em;
        font-size: 14px;
        color:#4d4d4d
      }
      .table{
        margin-left: 30px;
        font-size: 14px;
        color: #4d4d4d;
      }
      .strong{
        font-size: 16px;
        font-weight: bold;
      }
      .whole{
        width: 100%;
        height: auto;
      }
      .header{
        font-size: 24px;
        margin: 0 auto;
        text-align: center;
      }
      .tdTitle{
        width: 150px;
        text-align: center;
      }
      .supply{
        text-align: center;
        font-size: 18px;
      }
      input.shortInput{
        width: 40px;
      }
    </style>
    <script src="{{static_res('/open/js/tinyscrollbar.js')}}"></script>
    <script type="text/javascript" src="{{static_res('/open/assets/upload/moxie.min.js')}}"></script>
    <script type="text/javascript" src="{{static_res('/open/assets/upload/plupload.min.js')}}"></script>
     <script type="text/javascript" src="{{static_res('/open/assets/upload/swfobject.js')}}"></script>
    <script type="text/javascript" src="{{static_res('/open/assets/upload/fireuploader.js')}}"></script>
@endsection

@section('content')
<!--内容-->
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
            <h4 class="f14">
                <span>版权协议</span>
                <span class="protocol-tag-con">
                    <span class="protocol-tag {{ $blade->toogleProtocol(1,0,$detail["is_deal"]) }}" tag=1 >电子合同</span>
                    <span class="protocol-tag {{ $blade->toogleProtocol(1,1,$detail["is_deal"]) }}" tag=2 >软件著作权</span>
                </span>
            </h4>
            <div class="content product-detail">
                <div class="tag-show-con-1 protocol-content" {!! $blade->toogleProtocol(2,0,$detail["is_deal"]) !!}>
                    <div id="user-protocol" style="height: 415px;">
                        <div class="scrollbar">
                            <div class="track">
                                <div class="thumb">
                                    <div class="end"></div>
                                </div>
                            </div>
                        </div>
                        <div class="viewport">
                            <div class="overview">
                                {!! $blade->showProtocol($detail["agreement_type"], $detail["agreement"]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="protocol-checkbox-con">
                        <label>
                            <input class="protocol-checkbox" type="checkbox" value="1" {!! $blade->checkProtocol($detail["is_deal"]) !!}>同意上述所有的协议</label>
                    </div>
                </div>
                <div class="tag-show-con-2 product-detail-info" {!! $blade->toogleProtocol(2,1,$detail["is_deal"]) !!}>
                    <div class="product-row clearfix">
                        <p>
                            <span class="title fl">软件著作权：</span>
                        </p>
                        <div class="pic-con clearfix " id="soft-container">
                            @if(isset($detail["cp_soft"]) and $detail["cp_soft"])
                            @foreach($detail["cp_soft"] as $v)
                            <div class='pic fl preview'><span class="close">x</span><a href="{{ $detail["base"].$v }}" target="_blank"><img src="{{ $detail["base"].$v }}" /></a></div>
                            @endforeach
                            @endif
                            <div class='pic fl'  id="soft-upload">
                                <span class="pic-icon"></span>
                                <span class="pic-txt">320*180 JPG</span>
                                <span class="pic-txt underline">上传图片</span>
                            </div>
                        </div>
                    </div>
                    <div class="product-row clearfix">
                        <p>
                            <span class="title fl">游戏备案通知单：</span>
                        </p>
                        <div class="pic-con clearfix " id="record-container" >
                             @if(isset($detail["cp_record"]) and $detail["cp_record"])
                            @foreach($detail["cp_record"] as $v)
                            <div class='pic fl preview'><span class="close">x</span><a href="{{ $detail["base"].$v }}" target="_blank"><img src="{{ $detail["base"].$v }}" /></a></div>
                            @endforeach
                            @endif
                            <div class='pic fl' id="record-upload">
                                <span class="pic-icon"></span>
                                <span class="pic-txt">320*180 JPG</span>
                                <span class="pic-txt underline" >上传图片</span>
                            </div>
                        </div>
                    </div>
                    <div class="product-row clearfix">
                        <p>
                            <span class="title fl">游戏版号通知单：</span>
                        </p>
                        <div class="pic-con clearfix " id="publish-container">
                            @if(isset($detail["cp_publish"]) and $detail["cp_publish"])
                            @foreach($detail["cp_publish"] as $v)
                            <div class='pic fl preview'><span class="close">x</span><a href="{{ $detail["base"].$v }}" target="_blank"><img src="{{ $detail["base"].$v }}" /></a></div>
                            @endforeach
                            @endif
                            <div class='pic fl' id="publish-upload">
                                <span class="pic-icon"></span>
                                <span class="pic-txt">320*180 JPG</span>
                                <span class="pic-txt underline" >上传图片</span>
                            </div>
                        </div>
                    </div>
                    <p style="text-align: center;">依据国家法规，游戏类应用需获取著作权、游戏运营备案以及游戏版号才能上线运营，请上传相关著作权文件，游戏备案通知单，游戏版号通知单的扫描件。大小2M以内，支持JPG/PNG格式</p>
                </div>
            </div>
        </div>
        <div class="button-con" >
            <button type="button" class="btn btn-history">返回</button>
            <button type="button" class="btn btn-webgame-save-copyright">保存</button>
        </div>
    </div>
    <!--弹窗1-->
    <div class="pop" id="confirm_div" style="display: none;">
      <span class="prompt">提示</span>
      <span class="inClose" id="confirm_close">×</span>
      <p class="declare" id="confirm_content">电子合同一经签定与纸质合同均具有法律效力</p>
      <p class="warning" id="confirm_warning">请您务必再次审核和确定合同内所填写的内容，一旦提交将无法修改！</p>
      <div class="subt"><button class="butt reme" id="confirm_cancle">取消</button><button class="butt" id="confirm_submit">确定</button></div>
    </div>
    <!--弹窗2-->
    <div class="pop" id="alert_div" style="display: none;">
      <span class="prompt">提示</span>
      <span class="inClose" id="alert_close">×</span>
      <p class="alertSuc">合同请填写完整</p>
      <button class="butt succ" id="alert_ok">确定</button>
    </div>
    <script>
    var appid = {{ $detail["appid"] }};
    var stat = {{ $detail["stat"] }};
    var operr = "资料审核中 无法修改"

    function formsubmit()
    {
        $.post(Open.urls.webGameSave+"/copyright/"+appid,$("#subform").serialize(),function(data){
            if(data.code==0) {
                Open.navigation.vrGameDetail("all",appid);
            } else {
                Open.showMessage(data.msg);
            }
        },"json")
    }
    $(function(){
        $("#confirm_submit").click(function() {
            formsubmit();
        });
        $("#confirm_close").click(function() {
            $("#confirm_div").attr("style", "display:none");
        });
        $("#confirm_cancle").click(function() {
            $("#confirm_div").attr("style", "display:none");
        });
        $("#alert_close").click(function() {
            $("#alert_div").attr("style", "display:none");
        });
        $("#alert_ok").click(function() {
            $("#alert_div").attr("style", "display:none");
        });
        $('#user-protocol').tinyscrollbar();
        $(".protocol-tag").click(function() {
            var tag = $(this).attr("tag");
            $(this).addClass('cur').siblings().removeClass('cur');
            $(".tag-show-con-" + tag).show();
            $(".tag-show-con-" + tag).siblings().hide();
            $('#user-protocol').tinyscrollbar();
        });
        $(".btn-webgame-save-copyright").click(function(){
           var is_deal =  $(".protocol-checkbox").prop("checked");
           if(is_deal!=true) {
              Open.showMessage("必须勾选开发者协议");
              return;
           }
            var soft =[] ,record = [],publish = [];
            var obj =  $("#soft-container").find("img");
            if(obj.length>0) {
                $.each(obj,function(i,j){
                   var n =  Open.getImageName(j.src);
                   if(n) {
                        soft.push(n);
                   }
                })
            }
            obj =  $("#record-container").find("img");
            if(obj.length>0) {
                $.each(obj,function(i,j){
                   var n =  Open.getImageName(j.src);
                   if(n) {
                        record.push(n);
                   }
                })
            }
            obj =  $("#publish-container").find("img");
            if(obj.length>0) {
                 $.each(obj,function(i,j){
                   var n =  Open.getImageName(j.src);
                   if(n) {
                        publish.push(n);
                   }
                })
            }
            if(soft.length<=0 || record.length<=0  || publish.length<=0  ) {
                 Open.showMessage("资源文件不全");
                return;
            } else {

                $("#softfield").attr("value", soft.join(",,"));
                $("#recordfield").attr("value", record.join(",,"));
                $("#publishfield").attr("value", publish.join(",,"));
                $("#cp_dealfield").attr("value", 1);

                var input = $("#subform input");

                for (var i = 0; i < input.length; i++) {
                    if (input[i].value == "") {
                        input[i].focus();
                        $("#alert_div").attr("style", "");
                        return false;
                    }
                }
                // 需要填合同的
                if (input.length > 30)
                {
                    $("#confirm_div").attr("style", "");
                    return false;
                }

                formsubmit();
            }
        });
        var softOp = {name:"soft",extensions:"jpg,png",add_params:{appid:appid,tp:'openapp'}}
        if(stat==1) {
            softOp.err = operr
        }
        var softUploader = Open.upload(softOp,function(up, file, info) {
            $("#soft-container").prepend('<div class="pic fl preview"><span class="close">x</span><a href="'+info.private+'" target="_blank"><img src="'+info.private+'?v='+Open.randVersion()+'" /></a></div>');
        },function(up,err,msg){
            Open.showMessage(msg)
        })

        var recordOp =  {name:"record",extensions:"jpg,png",add_params:{appid:appid,tp:'openapp'}}
        if(stat==1) {
            recordOp.err = operr
        }
        var recordUploader = Open.upload(recordOp,function(up, file, info) {
            $("#record-container").prepend('<div class="pic fl preview"><span class="close">x</span><a href="'+info.private+'" target="_blank"><img src="'+info.private+'?v='+Open.randVersion()+'" /></a></div>');
        },function(up,err,msg){
           Open.showMessage(msg)
        })

        var publishOp = {name:"publish",extensions:"jpg,png",add_params:{appid:appid,tp:'openapp'}}
        if(stat==1) {
            publishOp.err = operr
        }
        var publishUploader = Open.upload(publishOp,function(up, file, info) {
            $("#publish-container").prepend('<div class="pic fl preview"><span class="close">x</span><a href="'+info.private+'" target="_blank"><img src="'+info.private+'?v='+Open.randVersion()+'" /></a></div>');
        },function(up,err,msg){
            Open.showMessage(msg)
        })
    })
    </script>
@endsection
