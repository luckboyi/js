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
.error-msg,.account-mobile{
    display: none;
}
</style>
@endsection
@section('content')
<!--内容-->
   <div class="container ">
   <form id="product-form" onsubmit="return false">
        <div class="in_con">
            <h4 class="f14">子账号关联</h4>
            <div class="content child_account">
                <p class="clearfix ">
                   您的开发者账号可以最多关系3个子账号，并且通过OPEN后台可以分配给每个子账号使用不同的权限功能
                </p>
                 <p class="clearfix">
                    <span class="title fl">子账号</span>
                    <span><input type="text" name="account"  placeholder="请输入子账号"></span>
                </p>
                <p class="clearfix account-mobile">
                    <span class="title fl">绑定手机</span>
                    <span><input type="text" name="mobile" value="" readonly="true"></span>
                </p>
                <p class="clearfix account-mobile">
                    <span class="title fl">短信验证码</span>
                    <span><input type="text" class="fire-text" name="code" value="" placeholder="请输入验证码">&nbsp;&nbsp;<button type="button" class="btn btn-code">发送验证码</button></span>
                </p>
                <p class="clearfix prompt error-msg"></p>
                <p class="clearfix next">
                    <span><button type="button" class="btn btn-next">下一步</button></span>
                </p>
            </div>
        </div>
        </form>
    </div>
@endsection
@section('javascript')
<script type="text/javascript">
var next = 1;
$(function(){

    $(".btn-next").click(function() {
        var account = $("input[name=account]").val();
        if(next==1) {
           $.post("/checkAccount",{account:account},function(res){
            if(res.code==0) {
                if(res.data.mobile.length<11) {
                   showMessage(9101,'');
                } else {
                    $("input[name=mobile]").val(res.data.mobile);
                    $(".error-msg").hide();
                    $(".account-mobile").show();
                    $(".btn-next").text('关联');
                    next = 2;
                }
            } else {
                showMessage(res.code,res.msg);
            }
          },"json")
        } else {
            var mobile = $("input[name=mobile]").val();
            var code = $("input[name=code]").val();
            $.post("/addAccount",{account:account,mobile:mobile,code:code},function(res){
                console.log(res);
                if(res.code==0) {

                } else {
                    showMessage(res.code,res.msg);
                }
          },"json");
        }
    });
    $(".btn-code").click(function() {
         var account = $("input[name=account]").val();
         var mobile = $("input[name=mobile]").val();
         $.post("/addSonSendMsg",{account:account,mobile:mobile},function(res){
            if(res.code==0) {
                secResend();
            } else {
                showMessage(res.code,res.msg);
            }
        },"json")
    });
})

function showMessage(code,msg) {
    switch(code) {
        case 1302:
            msg = '您填写的账号不存在，请至<a href="#" target="_blank">VR助手官网</a>注册账号！'
        break;
        case 2014:
            msg =  '您填写的账号为开发者账号，建立子账号关系必须为VR助手普通账号，请至<a href="#" target="_blank">VR助手</a>申请注册账号';
        break;
        case 9101:
            msg = '您填写的账号还没有绑定手机号，请在<a href="#" target="_blank">VR助手</a>内绑定手机号';
        break;
    }
    $(".error-msg").html(msg);
    $(".error-msg").show();
}

function secResend() {
   $(".btn-code").html('<i class="sms_sec">60</i>后重新发送');
   $(".btn-code").prop("disabled",true);
    var seconds = 0;
    var secIntervalId = setInterval(function(){
        seconds += 1;
        var dis = 60-seconds;
        if(dis<=0) {
            $(".sms_sec").html('');
        } else {
              $(".sms_sec").html(dis);
        }
        if(dis<=0) {
            clearInterval(secIntervalId);
            $(".btn-code").prop("disabled",false);
            $(".btn-code").html('重新发送');
        }
  },1000);
}
</script>
@endsection