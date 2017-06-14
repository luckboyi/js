@extends('layouts.website')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')VR-登入@endsection

@section('content')
<div class="login_container">
    <div class="login_con clearfix fl">
        <div class="login fl tac">
            <h4 class="blueColor2 f18 tal">登录</h4>
            <div class="in_login">
                <div class="reg_container">
                <p class="pr">
                    <input type="text" placeholder="手机/VRonline账号" name="accountnum">
                    <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
                </p>
                <p class="pr">
                    <input type="password" placeholder="请输入密码" name="password">
                    <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b></b></span>
                </p>

                <p class="pr rem_pwd ">
                    <i class="sel pa"></i>
                    <span class="fl f12 ">在这台电脑上记住我</span>
                    <span class="fr f12 blueColor2 curp" id="forgetPwd">找回密码</span>
                </p>
                </div>
                <p class="verify_container" style="display:none">
                    <label class="pr">
                        <i class="pa yzm"></i>
                        <input type="text" name="verifyCode" placeholder="输入验证码">
                        <img src="" class="pa">
                        <span class="erro_msg pa errorColor tal pr f12"><i class="pa"></i><b>错误</b></span>
                    </label>
                </p>

                <p class="pr error-p"> <span class="error"></span></p>
                <p class="btn f16" id="loginBtn">登录</p>
                <p class="registerBtn btn f16 btn-register" style="display:none">注册</p>
                <p class="third_login">
                    <a href="//passport.vronline.com/auth/qq" class="pr">
                        <i class="pa"> </i>
                        <b>QQ登录</b>
                    </a>
                     <a href="//passport.vronline.com/auth/3Dbobo" class="pr">
                        <i class="pa bobo"></i>
                        <b>3D播播</b>
                    </a>
                    <a href="//passport.vronline.com/auth/wx" class="pr">
                        <i class="pa wx"></i>
                        <b>微信登录</b>
                    </a>
                    <a href="//passport.vronline.com/auth/weibo" class="pr">
                        <i class="pa wb"></i>
                        <b>微博登录</b>
                    </a>
                </p>
            </div>
        </div>
        <div class="register fl tac">
            <h4 class="blueColor2 f18 tal">注册</h4>
            <div class="register_con">
                <p class="tal f14">新建一个免费账号</p>
                <p class="tal f12">欢迎免费加入VRonline，我们将为您创建VRonline账户，<!-- </p> -->
                <!-- <p class="tal f12"> -->同时您可以安装VRonline体验更多精彩内容。</p>
                <p  class="btn f16 btn-register">注册</p>
            </div>
        </div>
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
var remember = 0;
var referer = "{!! $referer !!}";
var redirect_uri = "{!! $redirect_uri !!}";
var appid = "{!! $appid !!}";
var ispartner = "{!! $ispartner !!}";
var thirdpart = 0;      // 是否是当做第三方登录
if (ispartner == 1 && redirect_uri.length > 0 && appid > 0) {
    thirdpart = 1;
}

$(function(){
    // var mobileStatus = browserRedirect();
    // console.log(1212);
    if(browserRedirect) {
        phoneSize();
    }
    //失去焦点
    $('.sel').click(function() {
        var t = $(this).toggleClass("selected");
        if(t.attr("class").indexOf("selected")>0) {
            remember = 1;
        } else {
            remember = 0;
        }
    })
    $('.btn-register').click(function(){
        var url = "//www.vronline.com/register"
        if(referer.length>0) {
            url+="?referer="+referer;
        }
        location.href = url;
    });

    /*$('.in_login input').blur(function(){
        checkLogin($(this));
    });*/

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
    });

    $("#loginBtn").click(function(){
        var check = checkLogin();
        if(!check){
            return false;
        }

        var accountFlag = checkLogin($('.in_login input[name=accountnum]'));
        if (!accountFlag) {
            return false;
        }

        var passwordFlag = checkLogin($('.in_login input[name=password]'));
        if (!passwordFlag) {
            return false;
        }


        var name=$('.in_login input[name="accountnum"]').val();
        var pwd=$('.in_login input[name="password"]').val();
        var code=$('.in_login input[name="verifyCode"]').val();
        $(".error").text("");
        var postdata = {name:name,pwd:pwd,remember:remember,code:code};
        if (thirdpart == 1) {
            postdata = {name:name,pwd:pwd,remember:remember,code:code, thirdpart:1, thirdappid:appid};
        }
        $.post("api/login",postdata,function(data){
            if(data.code==0) {

                if (thirdpart == 1) {
                    if (redirect_uri.indexOf("?") > 0){
                       location.href = redirect_uri + "&code=" + data.data.logincode;
                    }else {
                        location.href = redirect_uri + "?code=" + data.data.logincode;
                    }
                }else  {
                    redirect()
                }
            } else {
                if(data.code==1115 || data.code==2006) {
                    $(".verify_container img").attr('src',data.data.img+"?w=84&h=38&v="+Math.random());
                    $(".reg_container").hide();
                    $(".verify_container input").val('');
                    $(".verify_container").show();
                } else if (data.code == 1302) {  //用户名不存在
                    $(".reg_container").show();
                    $(".verify_container").hide();
                    $('.in_login input[name=accountnum]').next('span.erro_msg').show().find('b').text(data.msg);
                    $('.in_login input[name=accountnum]').css('border-color','#ea5a5a');
                } else if(data.code == 1303) {  //密码错误
                    $(".reg_container").show();
                    $(".verify_container").hide();
                    $('.in_login input[name=password]').next('span.erro_msg').show().find('b').text(data.msg);
                    $('.in_login input[name=password]').css('border-color','#ea5a5a');
                }
                //$(".error").text(data.msg)
            }
         },"json");
    });
});

//登录 检测
function checkLogin(obj){
    if(!obj){
        var error=0;
        $('.in_login input').each(function(i,e){
            if(!checkLogin($(e))){
                error+=1;
            }
        });
        if(error>0){
            return false;
        }
        return true;
    }

    var name = obj.attr('name');
    var value = obj.val();
    if(name == 'accountnum'){
        if(typeof value == 'undefinde' || value == ''){
            obj.next('span.erro_msg').show().find('b').text('账号不能为空');
            obj.css('border-color','#ea5a5a');
            return false;
        }else{
            obj.next('span.erro_msg').hide().find('b').text('');
            obj.css('border-color','#2a3343')
        }
    };
    if(name == 'password'){
        if(typeof value == 'undefinde' || value == ''){
            obj.next('span.erro_msg').show().find('b').text('密码不能为空');
            obj.css('border-color','#ea5a5a');
            return false;
        }else{
            obj.next('span.erro_msg').hide().find('b').text('');
            obj.css('border-color','#2a3343')
        }
    }
    return true;
}

// 找回密码跳转
$("#forgetPwd").click(function(){
    window.location.href = "//www.vronline.com/forgetpwd";
})

function redirect() {
     var http_proto = "http"
   if(typeof(document.referrer)!="undefined" && document.referrer.indexOf('https')==0) {
        http_proto = "https"
   }
    if(referer.length>0) {
        if (referer.indexOf("http://") > -1 || referer.indexOf("https://") > -1) {
            location.href =  referer;
        }else {
            location.href =  http_proto+"://"+document.domain+'/'+referer;
        }
    } else {
        location.href =  http_proto+"://"+document.domain;
    }
}

function phoneSize() {
    if (screen.width<=640) {
        $(".register").css('display','none');
        $(".download_vr").css('display','none');
        // $(".header .blue").css('display','none');
        $(".login_con").css('width',"100%");
        $(".login_con").css('padding',"30px 0");
        $(".login_con .login").css('margin',"0 auto");
        $(".btn-register").css('margin-top',"20px");
        $(".login_con .login").removeClass("fl");
        $(".in_login").css('border','none');
        $(".registerBtn").css('display','inline-block');
    }
};
</script>
@endsection
