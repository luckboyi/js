@extends('layouts.third')

@section('content')
    <div class="state clearfix">
        <span class='fl second_step'><span class='icon'></span>1 填写资料</span>
        <span class='fl line'>-----------------------------------</span>
        <span class='fl first_step'><span class='icon'></span>2 验证邮箱</span>
        <span class='fl line'>-----------------------------------</span>
        <span class='fl third_step'><span class='icon'></span>3 完成注册</span>
    </div>
    <div class='account'>
        <div style="text-align: center; margin:40px auto;">
            <input type="hidden" id="email" value="{{ $email }}"/>
            <input type="hidden" id="uid" value="{{$uid}}"/>
            <input type="hidden" id="name" value="{{ $name }}"/>
            <span> @if(isset($msg)) {{ $msg }} @endif</span><br />
            <span class=''>邮箱 <span style="color: #00a2d4">{{ $email }}</span> 验证失败，请重新发送邮件验证({{$code}})。（<a href="/userApply/edit" style="text-decoration: underline">修改资料</a>）</span>
        </div>
    </div>

    <div class='sub'>
        <div class='sub_btn'>
            <input type="submit" id="resendEmail"  style="cursor: pointer;" name='' value="重新发送验证邮件"/>
            <input type="submit" id="auth" name=''style="cursor: pointer;" value="验证完成"/>
        </div>
    </div>
@endsection
<!-- BEGIN PAGE CONTENT-->

@section('javascript')
    <script type="text/javascript">
        $("body").on("click", "#resendEmail", function() {
            var uid = $("#uid").val(),
                    email = $("#email").val(),
                    name = $("#name").val();

            var paramObj = {
                "uid" : uid,
                "email" : email,
                "userName" : name
            };
            var ajaxUrl = "{{ url('resendActiveEmail') }}";
            $.post(ajaxUrl, paramObj, function(result) {
                if(result.code == 0){
                    Open.showMessage("发送邮件成功");
                } else {
                    Open.showMessage(result.msg);
                }
            },"json");
        });
        $("body").on("click", "#auth", function() {
            var uid = $("#uid").val(),
                    email = $("#email").val(),
                    name = $("#name").val();

            var paramObj = {
                "uid" : uid,
                "email" : email,
                "userName" : name
            };
            var ajaxUrl = "{{ url('AuthEmail') }}";
            $.post(ajaxUrl, paramObj, function(result) {
                if(result.code == 0){
                    pubApi.jumpUrl("{{ url('open/userRegSuccess') }}");
                } else {
                    if(result.code == 0){
                        Open.showMessage("您已经提交过申请了，请等待审核！");
                    } else {
                        Open.showMessage("请查看邮件激活后点击！");
                    }
                }
            },"json");
        });
    </script>

@endsection