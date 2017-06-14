@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection



@section('content')

<div class="ui  container" style="width:1000px">


    <div class="ui ordered attached steps developer">
        <div class="completed step">
            <div class="content">
                <div class="title">填写资料</div>
                <div class="description">Fill information</div>
            </div>
        </div>
        <div class="@if($status=='wait' || $status=='success' || $status=='reject' ) completed @else active @endif step">
            <div class="content">
                <div class="title">验证邮箱</div>
                <div class="description">Verify email</div>
            </div>
        </div>
        <div class="@if($status=='success') completed @else active @endif step step">
            <div class="content">
                <div class="title">等待审核</div>
                <div class="description">Waiting review</div>
            </div>
        </div>
    </div>
    <div class="ui attached segment desc">

        @if($status=="email")
        <i class="iconfont-fire iconfire-youxiang-copy icon common"></i>
        <p class="email"> 已经向您的邮箱<span> {{ $dev['email'] }} </span>发送了邮件，请收到邮件后点击链接确认！</p>
        @elseif($status=="email_error")
         <i class="iconfont-fire iconfire-shibai icon fail"></i>
        <p class="email"> 您的链接已经过期，请重新发送邮件验证 </p>
        @elseif($status=="wait")
        <i class="iconfont-fire iconfire-dengdai icon common"></i>
        <p class="email">  您的开发者信息正在审核，请耐心等待</p>
        @elseif($status=="reject")
        <i class="iconfont-fire iconfire-shibai icon fail"></i>
        <p class="email"> 你的开发者信息未通过审核 <span class="fail">{{ $msg }}<span></p>
        @elseif($status=="success")
        <i class="iconfont-fire  iconfire-chenggong icon success"></i>
        <p class="email"> 你的开发者信息已经通过审核 </p>
        @endif

        @if($status=="email" || $status=="email_error" )
        <div class="developer next">
          <div class="ui teal button" onclick="goTo('/developer/sign/fill')">修改开发者资料</div>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <div class="ui teal button" onclick="sendMail()">重新发送验证邮件</div>
        <div>
        @elseif($status=="reject")
        <div class="developer next">
          <div class="ui teal button" onclick="goTo('/developer/sign/fill')">修改开发者资料</div>
        <div>
        @elseif($status=="success")
        <div class="developer next">
          <div class="ui teal button" onclick="goTo('/developer/vrgame')">进入管理后台</div>
        <div>
        @endif
    </div>
</div>



@endsection
@section('javascript')
<script type="text/javascript">

function goTo(url) {
    location.href = url
}

function sendMail() {
    permPost("/json/submit/dev_mail",{},function(res){
       if(res.code==0) {
            loiMsg("发送成功",function(){
                $(".email").text("邮件已经发送成功")
            },'success');
       }
    });
}
</script>
@endsection
