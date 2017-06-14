@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')
@section('head')
<link href="{{ static_res('/assets/mutiselect/sumoselect.css') }}" rel="stylesheet" />
<script src="{{ static_res('/open/assets/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ static_res('/open/assets/jquery-validation/additional-methods.min.js') }}"></script>
<script src="{{ static_res('/open/assets/jquery-validation/messages_zh.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/mutiselect/jquery.sumoselect.min.js') }}"></script>
<style>
textarea{
    height: 120px;
}
</style>
@endsection
@section('content')
<!--内容-->
   <div class="container">
   <form id="product-form">
     @if(!isset($detail["new"]))
        <div class="in_con">
            <h4 class="f14">编辑产品</h4>

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
        @endif
        <div class="in_con">
            <h4 class="f14">编辑产品</h4>

            <div class="content ">
                <p class="clearfix">
                    <span class="title fl">游戏名称：</span>
                    <span><input type="text" name="name" value="{{ $detail["name"] }}" placeholder="2-20字符"></span>
                </p>
                 <p class="clearfix">
                    <span class="title fl">游戏标签：</span>
                    <span><input type="text" name="tags" value="{{ $detail["tags"] }}" placeholder="请输入游戏标签,逗号隔开2-40个字符"></span>
                </p>
                 <p class="clearfix">
                    <span class="title fl">游戏类型：</span>
                    <span>
                      <select multiple="multiple" name="first" placeholder="选择类型" class="topSelect" required>
                        {!! $blade->showChooseType('webgame',$detail["first_class"]) !!}
                      </select>
                    </span>
                </p>
                <p class="clearfix">
                    <span class="title fl">游戏简介：</span>
                    <span><textarea name="content" class="textarea" cols="36" rows="8" placeholder="输入产品描述,10-200字符">{{ $detail["content"] }}</textarea></span>
                </p>
                <p class="clearfix">
                    <span class="title fl">货币名称：</span>
                    <span><input type="text" class="fire-text" name="rmb_name" value="{{ $detail["gameb_name"] }}" placeholder="请输入货币名称"></span>
                </p>
                <p class="clearfix">
                    <span class="title fl">兑换比例：</span>
                    <span>1RMB可兑换<input type="text" class="fire-num" name="rmb_rate" value="{{ $detail["rmb_rate"] }}" placeholder="请输入兑换比例"></span>
                </p>
            </div>

        </div>
        <div class="button-con">
            <button type="button" class="btn btn-history">返回</button>
            <button type="submit" class="btn">保存</button>
        </div>
        </form>
    </div>
@endsection
@section('javascript')
<script type="text/javascript">
var appid = {{ $detail["appid"] }};
var tp_select;

$(function() {
    var  top_select_obj = $('.topSelect').SumoSelect({ csvDispCount: 4, captionFormatAllSelected: "已经全部选择" });
    top_select =  $(top_select_obj)[0].sumo;
})

// 自定义校验方法
$.validator.addMethod("required", function(value, element) {
    if($.trim(value)){
        return true;
    }else{
        return false;
    }
}, "这是必填的字段");

var validator=$("#product-form").validate({
    rules: {
        // simple rule, converted to {required:true}
        name: {
            required: true,
            rangelength: [2, 20],
        },
        tags:{
            required: true,
            rangelength: [2, 40],
        },
        first: {
            required: true,
        },
        content:{
            required: true,
            rangelength: [10, 200]
        },
        rmb_name: {
            required: true,
        },
        rmb_rate: {
            required: true,
            number: true,
            min:1
        }
    },
    messages:{
        name:"请填写2-20个字符的游戏名称",
        first:"请填写游戏类型",
        tags:"请输入标签",
        content:"请填写10-200个字符的游戏描述",
        rmb_name:"请填写货币名称",
        rmb_rate:"请填写兑换比例"
    },
    submitHandler: function() {
        var name = $("input[name='name']").val();
        var first =  top_select.getSelStr();
        var tags  = $("input[name='tags']").val();
        var content = $("textarea[name='content']").val();
        var rmb_name = $("input[name='rmb_name']").val();
        var rmb_rate = $("input[name='rmb_rate']").val();

        var reqUrl = Open.urls.webGameSave+"/base/"+appid;
        if(appid<1) {
            reqUrl =Open.urls.webGameCreateSubmit
        }
        $.post(reqUrl,{name:name,first:first,tags:tags,content:content,rmb_name:rmb_name,rmb_rate:rmb_rate},function(data){
            if(data.code==0) {
                 if(appid>0) {
                     Open.navigation.webGameDetail("all",appid);
                } else {
                    Open.navigation.webGameOffline();
                }
            } else if(data.code==2){
                validator.showErrors({
                    "name": "游戏名称已经被使用",
                });
            }else{
                Open.showMessage(data.msg)
            }
        },"json");
    }
});
</script>
@endsection