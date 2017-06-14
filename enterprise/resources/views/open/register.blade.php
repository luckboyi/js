
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>官网注册</title>
    <link rel="stylesheet" href="{{static_res('/guanwang/style/base.css')}}">
    <link rel="stylesheet" href="{{static_res('/guanwang/style/register.css')}}">
    <script language="JavaScript" src="{{static_res('/common/js/jquery-1.12.3.min.js')}}"></script>
</head>
<body>
<div class="login_container">
    <div class="login_con clearfix fl tac register_con mar0">
        <h4 class="blueColor2 f18 tal">账号注册</h4>
        <p>
            <label class="pr">
                <span>账号名称</span>
                <i class="pa"></i>
                <input type="text" placeholder="输入账号名称" name="accountnum" id="name" flag="1">
                <span class="erro_msg pa errorColor tal pr f12" ><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>输入密码</span>
                <i class="pa pwd_icon"></i>
                <input type="password" placeholder="输入密码" class='loginPassword' name="loginPassword" id="pwd" falg="1">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>确认密码</span>
                <i class="pa pwd_icon"></i>
                <input type="password" placeholder="确认密码" name="checkLoginPassword" id="confirPwd" falg="1">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
<!--         <p class="hide">
            <label class="pr">
                <span>验证码</span>
                <i class="pa yzm"></i>
                <input type="password" placeholder="输入验证码">
                <img src="images/" class="pa">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p> -->
        <p  class="btn f16" id="startRegister">提交</p>
        <p class="has_aco">
            <span class="f12">
                已有账号？<a href="javascript:;" class="blueColor2" id="startLogin">马上登录</a>
            </span>
        </p>
    </div>
    <!--注册成功-->
    <div class="login_con clearfix fl tac suc_register" id="register_user">
        <h4 class="blueColor2 f16 pr"><i class="pa"></i>注册成功</h4>
        <p class="f12">恭喜你，您的账号注册成功</p>
        <p class="f12">系统将在<b class="blueColor2 ">5</b>秒内，自动跳转到首页</p>
        <p  class="btn f16 login_immediately">立即登录</p>
    </div>
    <div class="download_vr fr tac">
        <p class="f16 title">专注于VR内容的综合性开放平台</p>
        <p class="img_box"></p>
        <p class="f16 btn">安装VR客户端</p>
    </div>
</div>

</body>
</html>


<script>
    //init
    $(function(){
    })

	// 马上登陆
	$("#startLogin").click( function () {
		window.location.href = '/open/login';
	})

    // 申明全局变量name
    var nameFalg = 0;

	//点击注册提交提示注册成功
    $("#startRegister").click( function () {

        // 判断用户名是否合法
        var name = $("#name").val();

        if (nameFalg == -3) {
            return;
        };

        var flag = blurName(name);
        if (flag < 1) {  // 如果小于1，则说明返回的是错误代码
            return;
        };

        // 判断密码和确认密码是否为空
        var pwd = $("#pwd").val();
        var confirPwd = $("#confirPwd").val();
        if (pwd.length == 0) {
            $("#pwd").siblings(".erro_msg").show().find('b').html("密码不能为空");
            return;
        }else{
            $("#pwd").siblings(".erro_msg").show().find('b').html('');
            $("#pwd").siblings(".erro_msg").hide();
        }

        if (pwd.length < 6) {
            $("#pwd").siblings(".erro_msg").show().find('b').html("密码长度不够");
            return;
        }else{
            $("#pwd").siblings(".erro_msg").show().find('b').html('');
            $("#pwd").siblings(".erro_msg").hide();
        }

        if (confirPwd.length == 0) {
            $("#confirPwd").siblings(".erro_msg").show().find('b').html("确认密码不能为空");
            return;
        }else{
            $("#confirPwd").siblings(".erro_msg").show().find('b').html('');
            $("#confirPwd").siblings(".erro_msg").hide();
        }

        if (confirPwd.length < 6) {
            $("#confirPwd").siblings(".erro_msg").show().find('b').html("确认密码长度不够");
            return;
        }else{
            $("#confirPwd").siblings(".erro_msg").show().find('b').html('');
            $("#confirPwd").siblings(".erro_msg").hide();
        }

        // 判断两次密码是否一致
        if (pwd != confirPwd) {
            $("#confirPwd").siblings(".erro_msg").show().find('b').html("两次密码不一致");
            return;
        }else{
            $("#confirPwd").siblings(".erro_msg").show().find('b').html('');
            $("#confirPwd").siblings(".erro_msg").hide();
        }

        $.ajax({
            url:'/ajax/openReg',
            type:'GET',
            dataType:'json',
            data:{name:name, pwd:pwd, confirPwd:confirPwd},
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success:function(data){
                if(data.status == 1){
                    $("#startRegister").parents('.register_con').hide().siblings('.suc_register').show();
                    countdownFn();
                }else{
                    alert(data.msg)
                }
            }
        })
    });

    // 用户名离开按钮事件
    $("#name").blur( function () {

        var name = $("#name").val();
        var flag = blurName(name);
        if (flag < 1) {
            return;
        }else{
            $("#name").siblings(".erro_msg").show().find('b').html('');
            $("#name").siblings(".erro_msg").hide();
        }
    });

    function blurName(name){

        if (name.length == 0) {
            $("#name").siblings(".erro_msg").show().find('b').html("用户名不能为空");
            nameFalg = -1;
        }else{
            // 判断用户名是否可用
            var rs=/^[a-zA-Z\u4E00-\u9FA50-9][a-zA-Z\u4E00-\u9FA50-9_]*$/;
            if (!rs.test(name)) {
               $("#name").siblings(".erro_msg").show().find('b').html("只允许中英文、数字、下划线，且不能以下划线开头");
                nameFalg = -2;
            }else{

                $.ajax({
                url:'/open/isExistAcc',
                async:false,
                type:'GET',
                contentType: "application/json; charset=utf-8",
                dataType:'json',
                data:{name:name},
                headers: {  // header属性，是为了避免跨站伪造请求攻击写的
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success:function(data){
                    if(data.status == 0){
                        $("#name").siblings(".erro_msg").show().find('b').html("用户名已被注册");
                        nameFalg = -3;
                    }else{
                        $("#name").siblings(".erro_msg").show().find('b').html('');
                        $("#name").siblings(".erro_msg").hide();
                        nameFalg = 1;
                    }
                }
            })
            }
        }

        return nameFalg;
    }

    //注册成功倒计时
    function countdownFn(){
        var countdownnum = 5;
        function countdown(){
            if(countdownnum == 0){
                window.location.href = '/open/login';
            }else{
                countdownnum--;
                $('.suc_register').find('b').text(countdownnum);
            }
        }
        setInterval(function(){
            countdown();
        },1000);
        //点击立即登录
        $('.suc_register').on('click','.login_immediately',function(){
            window.location.href = '/login';
        })
    }

</script>