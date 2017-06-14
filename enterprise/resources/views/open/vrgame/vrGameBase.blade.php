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
        @if(!isset($detail['new']))
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
            <h4 class="f14">产品详情</h4>

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
                       {!! $blade->showChooseType('vrgame',$detail["first_class"]) !!}
                      </select>
                    </span>
                </p>
                 <p class="clearfix">
                    <span class="title fl">支持设备：</span>
                    <span>
                    <select multiple="multiple" name="support" placeholder="选择设备" class="supportSelect" required>
                       {!! $blade->showChooseType('vr_device',$detail["support"]) !!}
                      </select>
                    </span>
                </p>
                <p class="clearfix">
                    <span class="title fl">调用SDK：</span>
                    <span>
                        <select name="sdk" placeholder="选择是否调用" id="sdk" class="supportSelect" required>
                            <option value="0" @if(isset($detail['bits_status']) && $detail['bits_status'] == 0) selected @endif>无调用</option>
                            <option value="1" @if(isset($detail['bits_status']) && $detail['bits_status'] == 1) selected @endif>调用大朋SDK</option>
                        </select>
                    </span>
                </p>
                 <p class="clearfix">
                    <span class="title fl">原价</span>
                    <span><input type="text" name="original_sell" value="{{ $detail["original_sell"] }}" placeholder="请输入原价"></span>
                </p>
                 <p class="clearfix">
                    <span class="title fl">现价：</span>
                    <span><input type="text" name="sell" value="{{ $detail["sell"] }}" placeholder="请输入现价"></span>
                </p>

                <p class="clearfix">
                    <span class="title fl">最低配置：</span>
                    <span><textarea name="min_device" class="textarea" cols="36" rows="8" placeholder="输入最低配置,10-200字符">{{ $detail["mini_device"] }}</textarea></span>
                </p>
                <p class="clearfix">
                    <span class="title fl">推荐配置：</span>
                    <span><textarea name="rec_device" class="textarea" cols="36" rows="8" placeholder="输入推荐配置,10-200字符">{{ $detail["recomm_device"] }}</textarea></span>
                </p>
                <p class="clearfix">
                    <span class="title fl">游戏简介：</span>
                    <span><textarea name="content" class="textarea" cols="36" rows="8" placeholder="输入游戏简介,10-200字符">{{ $detail["content"] }}</textarea></span>
                </p>
            </div>

        </div>
        <div class="button-con" >
            <button type="button" class="btn btn-history">返回</button>
            <button type="submit" class="btn">保存</button>
        </div>
        </form>
    </div>
@endsection
@section('javascript')
<script type="text/javascript">
var appid = {{ $detail["appid"]}};
var tp_select;
var support_select;

$(function() {
    var  top_select_obj = $('.topSelect').SumoSelect({ csvDispCount: 4, captionFormatAllSelected: "已经全部选择" });
    var  support_select_obj = $('.supportSelect').SumoSelect({ csvDispCount: 4, captionFormatAllSelected: "已经全部选择" });
    top_select =  $(top_select_obj)[0].sumo;
    support_select =  $(support_select_obj)[0].sumo;
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
         sell: {
            required: true,
        },
        original_sell: {
            required: true,
        },
        support:{
            required: true,
        },
        min_device:{
            required: true,
            rangelength: [10, 200]
        },
        rec_device:{
            required: true,
            rangelength: [10, 200]
        },
        content:{
            required: true,
            rangelength: [10, 200]
        }
    },
    messages:{
        name:"请填写2-20个字符的游戏名称",
        tags:"请输入标签",
        first:"请选择游戏类型",
        sell:"请输入价格",
        original_sell:"请输入价格",
        content:"请填写10-200个字符的游戏简介",
        support:"请选择支持设备",
        min_device:"输入最低配置,10-200字符",
        rec_device:"输入推荐配置,10-200字符"
    },
    submitHandler: function() {
        var name = $("input[name='name']").val();
        var tags  = $("input[name='tags']").val();
        var first = top_select.getSelStr();
         var sell  = $("input[name='sell']").val();
          var original_sell  = $("input[name='original_sell']").val();
        var content = $("textarea[name='content']").val();
        var support = support_select.getSelStr();
        var sdk = $("#sdk").val();
        var min_device = $("textarea[name='min_device']").val();
        var rec_device = $("textarea[name='rec_device']").val();
        var reqUrl = Open.urls.vrGameSave+"/base/"+appid;
        if(appid<1) {
            reqUrl =Open.urls.vrGameCreateSubmit
        }
        console.log(sdk);
        $.post(reqUrl,{name:name,tags:tags,first:first,sell:sell,original_sell:original_sell,support:support,min_device:min_device,sdk:sdk,rec_device:rec_device,content:content},function(data){
            if(data.code==0) {
                if(appid>0) {
                     Open.navigation.vrGameDetail("all",appid);
                } else {
                    Open.navigation.vrGameOffline();
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
