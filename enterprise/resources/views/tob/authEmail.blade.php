@extends('tob.layout')

@section('content')
    <div class="state clearfix">
        <span class='fl second_step'><span class='icon'></span>1 填写资料</span>
        <span class='fl line'>-----------------------</span>
        <span class='fl first_step'><span class='icon'></span>2 验证邮箱</span>
        <span class='fl line'>-----------------------</span>
        <span class='fl third_step'><span class='icon'></span>3 等待审核</span>
    </div>
    <div class='account'>
        <div style="text-align: center; margin:40px auto;">
            <span class=''>您申请验证的邮箱为：<span style="color: #00a2d4">{{ $merchant["email"] }}<!-- （<a href="/?edit=1" style="text-decoration: underline">修改资料</a>） --></span>
            @if(isset($msg) && $msg)
            ，$msg
            @elseif(!$merchant["email_verfy"])
            ，我们将为您发送一封验证邮箱至该邮箱内，请登录邮箱点击链接验证。请确认后进行邮箱验证！</span>
            @else
            ，已经通过验证，可以直接提交审核。</span>
            @endif
        </div>
    </div>
    <div class='sub'>
        <div class='sub_btn' style="text-align: center;">
            @if(!$merchant["email_verfy"])
            <input type="submit" id="resendEmail"  style="cursor: pointer;" name='' value="重新发送验证邮件"/>
            <input type="submit" id="verified" name='' style="cursor: pointer;" value="我已经完成验证"/>
            @else
            <input type="submit" id="auth" name='' style="cursor: pointer;" value="提交审核"/>
            @endif
        </div>
    </div>
@endsection
<!-- BEGIN PAGE CONTENT-->

@section('javascript')
    <script type="text/javascript">
        $("body").on("click", "#resendEmail", function() {
            var ajaxUrl = "/sendEmail";
            $.post(ajaxUrl, {}, function(result) {
                if(result.code == 0){
                    Open.showMessage("发送邮件成功");
                } else {
                    Open.showMessage(result.msg);
                }
            },"json");
        });
        $("body").on("click", "#auth", function() {
            var ajaxUrl = "/submit";
            $.post(ajaxUrl, {}, function(result) {
                if(result.code == 0){
                    Open.showMessage("提交审核成功");
                    location.href="/wait";
                } else {
                    Open.showMessage(result.msg);
                }
            },"json");
        });
        $("#verified").click(function(){
            location.href=location.href;
        });
    </script>

@endsection
