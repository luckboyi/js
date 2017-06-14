@extends('layouts.website')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')VR-找回密码@endsection

@section('content')
<div class="login_container">
    <div class="login_con clearfix fl tac register_con find_pwd mar0">
        <h4 class="blueColor2 f18 tal">密码找回</h4>
        <p>
            <label class="pr">
                <span>用户账号</span>
                <i class="pa"></i>
                <input type="text" placeholder="输入用户账号" id="name" name="accountnum">
                <span class="erro_msg pa errorColor tal pr f12" ><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>手机号码</span>
                <i class="pa"></i>
                <input type="text" placeholder="输入手机号" id="mobile" name="accountnum">
                <span class="erro_msg pa errorColor tal pr f12" ><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>输入密码</span>
                <i class="pa pwd_icon"></i>
                <input type="password" placeholder="输入新密码" id="pwd" name="loginPassword">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>确认密码</span>
                <i class="pa pwd_icon"></i>
                <input type="password" placeholder="确认新密码" id="confirPwd" name="checkLoginPassword">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>验证码</span>
                <i class="pa yzm"></i>
            <!-- new 2016-10-17-zy-->
                <input class="input_code" id="verNumber" type="text" placeholder="输入验证码">
                <span class="send_code" id="setVetify">发送验证码</span>
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            <!-- new 2016-10-17-zy-->
            </label>
        </p>
        <p  class="btn f16" id="btnForgetPwd">提交</p>
        <p class="has_aco">
            <span class="f12">
                已有账号？<a href="/login" class="blueColor2">马上登录</a>
            </span>
        </p>
    </div>
    <div class="download_vr fr tac">
        <p class="f16 title">专注于VR内容的综合性开放平台</p>
        <p class="img_box"></p>
        <a href="http://www.vronline.com/vronline" target="_blank"><p class="f16 btn">安装VRonline</p></a>
    </div>
</div>
@endsection

@section('javascript')
<!-- 公共js库 -->
<script language="JavaScript" src="{{static_res('/common/js/tips.js')}}"></script>
<script>
    $(function(){
        browserRedirect();
        /*new   2016-10-18-zy*/
        //点击发送验证码
        $('.send_code').on('click',function(){

            // 调用通用函数，判断用户名，手机是否为空
            var flag = validateNAM();

            if (!flag) {
                return false;
            }else{
                var _this=$(this);
                time(_this);
            }
        })
        /*new   2016-10-18-zy*/

    });
    // 提价按钮事件
    $("#btnForgetPwd").click(function(){

        var flag1 = validateNAM();  // 判断用户名和密码是否为空
        if (!flag1) {
            return false;
        }

        var flag2 = validaPAC();    // 判断密码和确认密码和验证码不为空
        if (!flag2) {
            return false;
        }


        // 发起找回密码ajax
        var name = $("#name").val();
        var pwd = $("#pwd").val();
        var code = $("#verNumber").val();

        $.ajax({
            type: 'get',
            url: '/ajax/resetPwd',
            data: { account : name,pwd : pwd,code : code},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.code == 0) {
                    tipsFn.init({
                        model:'tips',
                        msg:"密码找回成功",
                        msg2:'',
                        btnState:1,
                        sucCallback:''
                    });
                    setTimeout('delayer()', 3000);
                }else{
                    tipsFn.init({
                        model:'tips',
                        msg:data.msg,
                        msg2:'',
                        btnState:1,
                        sucCallback:''
                    });

                }
            },
            error: function(xhr, type){
            }
        });
    })

    function time(o) {

        var name = $("#name").val();
        var mobile = $("#mobile").val();

        //找回密码发送短信验证码
        $.ajax({
            type: 'get',
            url: '/ajax/sendFindCode',
            data: { account : name,mobile : mobile},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.code == 0) {
                    setTime($('.send_code'));
                   // 验证码发送成功
                   // 短信验证码发送成功，就不能及时点击获取验证码
                   $("#setVetify").addClass('.disabled');
                   /*$("#showText").show();*/
                   $("#verNumber").attr("disabled",false);
                   $(o).css("color","#999").attr("onclick","");

                   // 发送成功，让文本框不能编辑
                   $("#name").attr('disabled','disabled');
                   $("#mobile").attr('disabled','disabled');

                }else{

                    tipsFn.init({
                        model:'tips',
                        msg:data.msg,
                        msg2:'',
                        btnState:1,
                        sucCallback:''
                    });

                }
            },
            error: function(xhr, type){
            }
        });
    //console.dir(o)

}

    var countdown = 60;
    function setTime(obj){
        //alert(1)
        if(countdown == 0){
            obj.removeAttr('disabled');
            obj.text('重新发送');
            obj.css({'pointer-events':'auto','background':'#22a1bf','border-color':'#22a1bf'});
            countdown = 60;
            //clearTimeout(timer)
        }else{
            //val.attr('disabled',true);
            obj.attr('disabled','true');
            obj.css({'pointer-events':'none','background':'#666','border-color':'#666'});
            obj.text("已发送("+countdown+")");
            countdown--;
            setTimeout(function(){
                setTime(obj)
            },1000);
        };
        //获取短信信息;
    };
    /*new   2016-10-18-zy*/
    /**延时跳转**/
    function delayer() {
         window.location.href = '/login';
    }
    // 文本框离开事件
    $("#name").blur( function () {

        var name = $("#name").val();

        if (name.length > 0) {
            $("#name").siblings(".erro_msg").show().find('b').html('');
            $("#name").siblings(".erro_msg").hide();
        };
    });

    // 验证码
    $("#verNumber").blur( function () {

        var verNumber = $("#verNumber").val();

        if (verNumber.length > 0) {
            $("#verNumber").siblings(".erro_msg").show().find('b').html('');
            $("#verNumber").siblings(".erro_msg").hide();
        };
    });

    // 用户名 和 手机号码
    function validateNAM(){

        var name = $("#name").val();
        var mobile = $("#mobile").val();

        if (name.length == 0) {
            $("#name").siblings(".erro_msg").show().find('b').html("用户名不能为空");
            return false;
        }else{
            $("#name").siblings(".erro_msg").show().find('b').html('');
            $("#name").siblings(".erro_msg").hide();
        }

        if(mobile.length==0) {
            $("#mobile").siblings(".erro_msg").show().find('b').html("手机号不能为空");
            return false;
        }
        if(mobile.length!=11) {
           $("#mobile").siblings(".erro_msg").show().find('b').html("手机号非法");
           return false;
        }

        var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if(!myreg.test(mobile)) {
            $("#mobile").siblings(".erro_msg").show().find('b').html("手机号非法");
            return false;
        }

        $("#mobile").siblings(".erro_msg").show().find('b').html('');
        $("#mobile").siblings(".erro_msg").hide();

        return true;
    }

    // 判断输入密码、确认密码、验证码
    function validaPAC(){

        // 判断密码和确认密码是否为空
        var pwd = $("#pwd").val();
        var confirPwd = $("#confirPwd").val();
        var verNumber = $("#verNumber").val();

        if (pwd.length == 0) {
            $("#pwd").siblings(".erro_msg").show().find('b').html("密码不能为空");
            return false;
        }else{
            $("#pwd").siblings(".erro_msg").show().find('b').html('');
            $("#pwd").siblings(".erro_msg").hide();
        }

        if (pwd.length < 6) {
            $("#pwd").siblings(".erro_msg").show().find('b').html("密码长度不够");
            return false;
        }else{
            $("#pwd").siblings(".erro_msg").show().find('b').html('');
            $("#pwd").siblings(".erro_msg").hide();
        }

        if (confirPwd.length == 0) {
            $("#confirPwd").siblings(".erro_msg").show().find('b').html("确认密码不能为空");
            return false;
        }else{
            $("#confirPwd").siblings(".erro_msg").show().find('b').html('');
            $("#confirPwd").siblings(".erro_msg").hide();
        }

        if (confirPwd.length < 6) {
            $("#confirPwd").siblings(".erro_msg").show().find('b').html("确认密码长度不够");
            return false;
        }else{
            $("#confirPwd").siblings(".erro_msg").show().find('b').html('');
            $("#confirPwd").siblings(".erro_msg").hide();
        }

        // 判断两次密码是否一致
        if (pwd != confirPwd) {
            $("#confirPwd").siblings(".erro_msg").show().find('b').html("两次密码不一致");
            return false;
        }else{
            $("#confirPwd").siblings(".erro_msg").show().find('b').html('');
            $("#confirPwd").siblings(".erro_msg").hide();
        }

        if (verNumber.length == 0) {
            $("#verNumber").siblings(".erro_msg").show().find('b').html("验证码不能为空");
            return false;
        }else{
            $("#verNumber").siblings(".erro_msg").show().find('b').html('');
            $("#verNumber").siblings(".erro_msg").hide();
        }

        return true;
    }

function phoneSize() {
    $(".header .blue").css('display','none');
    $(".login_container").css('width',"100%");
    $(".login_con").css('width',"100%");
    $(".download_vr").css('display',"none");
};
function browserRedirect() {
    var sUserAgent = navigator.userAgent.toLowerCase();
    var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
    var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
    var bIsMidp = sUserAgent.match(/midp/i) == "midp";
    var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
    var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
    var bIsAndroid = sUserAgent.match(/android/i) == "android";
    var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
    var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
    if ((bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) ){
        // window.location.href=B页面;
        phoneSize();
    }
}



</script>
@endsection
