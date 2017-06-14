@extends('layouts.website')

@section('title')VR-注册@endsection

@section('content')
<div class="login_container">
    <div class="login_con clearfix fl tac register_con mar0">
        <h4 class="blueColor2 f18 tal">账号注册</h4>
        <div class="reg_container">
        <p>
            <label class="pr">
                <span>账号名称</span>
                <i class="pa"></i>
                <input type="text" placeholder="输入账号名称" name="accountnum">
                <span class="erro_msg pa errorColor tal pr f12" ><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>输入密码</span>
                <i class="pa pwd_icon"></i>
                <input type="password" class="loginPassword" placeholder="输入密码" name="loginPassword">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p>
            <label class="pr">
                <span>确认密码</span>
                <i class="pa pwd_icon"></i>
                <input type="password" placeholder="确认密码" name="checkLoginPassword">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
        <p class="check" style="margin-top:20px">
            <i class="sel pa selected"></i><span>我已阅读并同意<a href="user_agreement" target="_blank">《VRonline用户协议》</a></span>
            <span class="erro_msg pa errorColor tal f12 erro_msg_check " style="left: 640px;top:1px;position: absolute;"><i class="pa"></i><b>请阅读《VRonline用户协议》并打勾</b></span>
        </p>
        </div>
        <p class="verify_container" style="display:none">
            <label class="pr" >
                <span>验证码</span>
                <i class="pa yzm"></i>
                <input type="text" name="verifyCode" placeholder="输入验证码">
                <img src="" class="pa">
                <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
            </label>
        </p>
         <p><span class="errorColor subErr"></span></p>
        <p  class="btn f16">提交</p>
        <p class="has_aco">
            <span class="f12">
                已有账号？<a href="/login" class="blueColor2">马上登录</a>
            </span>
        </p>
    </div>
    <!--注册成功-->
    <div class="login_con clearfix fl tac suc_register">
        <h4 class="blueColor2 f16 pr"><i class="pa"></i>注册成功</h4>
        <p class="f12">恭喜你，您的账号注册成功</p>
        <br />
        <p class="f12">系统将在<b class="blueColor2 ">5</b>秒内，自动跳转</p>
        <br />
        <br />
        <p  class="btn f16 login_immediately" id="startLogin">立即登录</p>
    </div>
    <div class="download_vr fr tac">
        <p class="f16 title">专注于VR内容的综合性开放平台</p>
        <p class="img_box"></p>
        <a href="http://www.vronline.com/vronline" target="_blank"><p class="f16 btn">安装VR助手</p></a>
    </div>
</div>
@endsection

@section('javascript')
<script>
var referer = "{!! $referer !!}";
var nameCheck = false;
var passCheck = true;
var selected = true;
$(function(){
    if(browserRedirect) {
        phoneSize();
    }
    //注册失去焦点
    $('.register_con input').each(function(){
        $(this).blur(function () {
            var errmsg = register_check(this);
            var valid = $(this).siblings(".erro_msg");
            if(errmsg !== ''){
                valid.show().find('b').html(errmsg);
                $(this).css('border-color','#c83434');
                passCheck = false;
            }else{
                valid.hide().find('b').html('');
                $(this).css('border-color','#828f9e');
                if($(this).attr('name') == 'accountnum') {
                    accountCheck();
                }
            }
        });
    });

    //点击注册提交提示注册成功
    $('.register_con').on('click','.btn',function(){
        var errmsg = '';
        passCheck = true;
        $('.register_con input').each(function(){
            errmsg = register_check(this);
            if(errmsg != '') {
                var valid = $(this).siblings(".erro_msg");
                valid.show().find('b').html(errmsg);
                $(this).css('border-color','#c83434');
                passCheck = false;
            }
        });
        if(passCheck == true  ) {
            if(nameCheck == false) {
                accountCheck(function() {
                    registerSubmit();
                });
            }   else if(selected == false) {
                $(".erro_msg_check").show();
            } else {
                registerSubmit();
            }
        }
    });

    $('.verify_container img').click(function() {
        var src = $(this).attr('src');
        var i = src.indexOf('v=');
        if(i<0) {
            src = src+'&v='+Math.random();
            $(this).attr('src',src);
        } else {
            var v = src.substr(i,src.length);
            src = src.replace(v,"v="+Math.random());
            $(this).attr('src',src);
        }
    })
});

function register_check(e){
    var o = $(e);
    var value = $.trim(o.val());
    var errmsg = '';
    var name = o.attr('name');
    switch (name){
        case 'accountnum':
            if (value == "") {
                errmsg = "账号不能为空";
                break;
            }
            var rs=/^[a-zA-Z\u4E00-\u9FA50-9][a-zA-Z\u4E00-\u9FA50-9_]*$/;
            if (!rs.test(value)) {
                errmsg="只允许中英文、数字、下划线，且不能以下划线开头"
                break;
            }
            var a =value.length;
            if (value.match(/[^\x00-\xff]/ig) != null) {
                var b = value.match(/[^\x00-\xff]/ig).length;
                a = a + b * 2
            }
            if (a < 6 || a > 18) {
                errmsg = "账号长度只能6~18个字符";
                break;
            }

            break;
        case 'loginPassword':
            if (value == "") {
                errmsg = "密码不能为空";
                break;
            }
            if (value.length<6 || value.length>16) {
                errmsg ="输入6-16位密码";
                break;
            }
            break;
        case 'checkLoginPassword':
            if(value == ''){
                errmsg = '密码不能为空';
                break;
            }
            if(value != $('.loginPassword').val()){
                errmsg = '两次密码不同';
                break;
            }
            break;
        default:
            break;
    }
    return errmsg;
}

function accountCheck(callback) {
    var obj = $('input[name="accountnum"]');
    var account = obj.val();
        $.post("/api/account",{account:account},function(data) {
            if(data.code==0) {
                nameCheck = true;
                if(typeof(callback)=="function") {
                    callback();
                }
            } else {
                nameCheck = false;
                obj.siblings(".erro_msg").show().find('b').html(data.msg);
                obj.css('border-color','#c83434');
            }
    },"json");
}

function registerSubmit() {
     var account  = $("input[name='accountnum']").val();
     var pwd =  $("input[name='loginPassword']").val();
     var confirmPwd = $("input[name='checkLoginPassword']").val();
     var code = $("input[name='verifyCode']").val();
     $.post("/api/register",{account:account,pwd:pwd,confirmPwd:confirmPwd,code:code},function(data){
        if(data.code==0) {
          $('.register_con').hide().siblings('.suc_register').show();
          countdownFn();
        } else {
            if(data.code==1115 || data.code==2006) {
                $(".verify_container img").attr('src',data.data.img+"?w=84&h=38&v="+Math.random());
                $(".reg_container").hide();
                $(".verify_container").show();
                 $(".subErr").text(data.msg);
            } else {
                $(".subErr").text(data.msg);
            }
        }
     },"json");
}

//注册成功倒计时
function countdownFn(){
    var countdownnum = 4;
    function countdown(){
        if(countdownnum == 0){
            redirect()
        }else{
            countdownnum--;
            $('.suc_register').find('b').text(countdownnum);
        }
    }
    setInterval(function(){
        countdown();
    },1000);
}

//添加用户协议的勾选框样式selected
$(".check").click(function(){
    $(".sel").toggleClass("selected");
    hasClass();
});

//判断是否有这个样式
function hasClass() {
    if($(".sel").hasClass("selected")){
        selected = true;
        $(".erro_msg_check").hide();
    } else {
        selected = false;
    }
}

// 立即登录
$("#startLogin").click( function () {
    redirect()
});

function redirect() {
     var http_proto = "http"
   if(typeof(document.referrer)!="undefined" && document.referrer.indexOf('https')==0) {
        http_proto = "https"
   }
    if(referer.length>0) {
        location.href =  http_proto+"://"+document.domain+'/'+referer;
    } else {
        location.href =  http_proto+"://"+document.domain;
    }
}

function phoneSize() {

    $(".login_container").css('width',"100%");
    $(".login_con").css('width',"100%");
    $(".download_vr").css('display',"none");
};
</script>
@endsection
