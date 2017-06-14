<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="UTF-8">
    <title>首页</title>
    <link rel="stylesheet" href="{{static_res('/open/style/base.css')}}">
    <link rel="stylesheet" href="{{static_res('/open/style/home.css')}}">
    <script language="JavaScript" src="{{static_res('/open/js/jquery-1.12.3.min.js')}}"></script>
</head>
<body>
    <div class="home_header">
        <div class="home_container pr">
             <div class="information pa clearfix">
                <p class="fl">开放</p>
                <p class="fl">创新</p>
                <p class="fl">服务</p>
                <p class="fl">共赢</p>
                <p>VRonline开放平台,提供VR内容一站式解决方案</p>
            </div>
            <div class="login pa login_in_pre">
                <h3 class="f20">登录账号</h3>
                <div class="reg_container" >
                <p>
                    <input type="text" id="username" placeholder="输入你的平台账号">
                </p>
                <p>
                    <input type="password" id="pwd" placeholder="输入你的平台密码">
                </p>
                <p class="pr rem_psw"><i class="pa sel"></i>记住密码<a href="/forgetpwd" class="fr blueColor">找回密码</a></p>
                </div>
                <p class="pr verify_container" style="display:none">
                    <input type="text" name="verifyCode" placeholder="输入验证码">
                    <img src="" class="pa">
                </p>
                <p class="pr error-p"><span class="error"></span></p>
                <p class="btn">
                    <span class="f14" id="login">登录</span>
                    <span class="f14 btn-register">注册</span>
                </p>
            </div>
        </div>
    </div>
    <div class="home_con clearfix">
        <ul class="clearfix">
            <li class="fl tac">
                <span></span>
                <p class="f26">企业对接</p>
                <p class="text f14">提供详细完善</p>
                <p class="text f14">的后台系统</p>
            </li>
            <li class="fl tac">
                <span class="extension"></span>
                <p class="f26">推广服务</p>
                <p class="text f14">为你量身定做</p>
                <p class="text f14">丰富的推广位</p>
            </li>
            <li class="fl tac">
                <span class="inquiry"></span>
                <p class="f26">精确查询</p>
                <p class="text f14">平台数据</p>
                <p class="text f14">精确查询</p>
            </li>
            <li class="fl tac">
                <span class="other"></span>
                <p class="f26">其他功能</p>
                <p class="text f14">更多内容</p>
                <p class="text f14">敬请期待</p>
            </li>

        </ul>
    </div>
    <div class="foot_contain tac">
        <p class="company">
                    <a href="http://www.vronline.com/vronline" target="_blank">关于VRonline</a>
                    <a href="http://www.kingnet.com/" target="_blank">关于恺英</a>
                    <a href="http://www.vronline.com/contact" target="_blank">商务合作</a>
                    <a class='none' href="http://developer.deepoon.com/" target="_blank">大朋开发者网站</a>
        </p>
                <div style="width:300px;margin:0 auto; " >
                    <a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=31011202001649" style="border:0;display:inline-block;text-decoration:none;height:20px;line-height:20px;"><img src="{{static_res('/website/images/pl.png')}}" style="float:left;"/><p  class=""  style="float:left;height:20px;line-height:20px;margin: 0px 0px 0px 5px; ">沪公网安备 31011202001649号</p></a>
                </div>
                <p style='line-height:32px;'>
                    <span>沪网文[2013]0667-082</span>
                    <span>沪ICP备16034129号-1</span>
                    <span>文化部网络游戏举报和联系电子邮箱:</span>
                    <a href="Mailto:wlyxjb@gmail.com" style="border: none;">wlyxjb@gmail.com</a>
                </p>
                <p>
                    <span>Copyright&copy;2008-2015 vronline.com All Rights Reserved</span>
                    <span>上海恺英网络科技有限公司 版权所有</span>
                </p>
    </div>
<script type="text/javascript">
// init
var referer = "";
var remember = 0;
$(function(){

    $('.sel').click(function() {
        var t = $(this).toggleClass("selected");
        if(t.attr("class").indexOf("selected")>0) {
            remember = 1;
        } else {
            remember = 0;
        }
    })

      $(".btn-review-user").click(function() {
         location.href = "/review/user";
    });

    $(".btn-product").click(function(){
        location.href = "/product/webgamelist/online";
    });

     $(".btn-my").click(function() {
        location.href = "/getDeveloperInfo";
    });

    $(".btn-register").click(function(){
        location.href= "/register"
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
    });


    $("#login").click( function () {
        $(".error").text('');
        var name = $("#username").val();
        var pwd = $("#pwd").val();
        var  code=$('input[name="verifyCode"]').val();
        if (username.length == 0) {
            $(".error").text('用户名不能为空');
            $("#username").focus();
            return;
        };

        if (pwd.length == 0) {
            $(".error").text('密码不能为空');
            $("#pwd").focus();
            return;
        };
        $(".error").hide();
        $.post('/api/login',{name:name,pwd:pwd,remember:remember,code:code},function(data) {
            if(data.code==0) {
                if(referer.length>1) {
                    location.href = referer
                } else {
                    location.href = "/enter"
                }
            } else {
                if(data.code==1115 || data.code==2006) {
                    $(".verify_container img").attr('src',data.data.img+"?w=84&h=38&v="+Math.random());
                    $(".reg_container").hide();
                    $(".verify_container input").val('');
                    $(".verify_container").show();
                    $(".error").text(data.msg);
                    $(".error").show();
                } else {
                    $(".reg_container").show();
                    $(".verify_container").hide();
                    $(".error").text(data.msg);
                    $(".error").show();
                }
            }
        },'json');
    });
});
</script>
</body>
</html>
