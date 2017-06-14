<html lang="en"><head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="//pic.vronline.com/common/style/base.css">
    <link rel="stylesheet" href="//pic.vronline.com/common/style/login.css">
    <link rel="stylesheet" href="//pic.vronline.com/webgames/style/web-login.css">
    <script language="JavaScript" src="//pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
    <script language="JavaScript" src="//pic.vronline.com/common/js/messenger.js"></script>
    <script type="text/javascript" src="//pic.vronline.com/webgames/js/login.js"></script>
    <style type="text/css" media="screen">
        .forget{
            color:#2b89cf;
        }
        .forget:hover{
            color:#39baf3;
        }
    </style>
</head>
<body>
    <div class="mask pr pageGame_login_in show">
        <div class="window_login_in pr">
            <div class="win_close pa ">
            </div>
            <ul class="clearfix">
                <li class="fl{{$type=="login"?" cur":""}}"><a href="javascript:;">用户登录</a></li>
                <li class="fl{{$type=="reg"?" cur":""}}"><a href="javascript:;">新用户注册</a></li>
            </ul>
            <ol>
                <li class="login_in_input{{$type=="login"?" cur":""}}" id="user_login">
                    <div class="logo">
                        <img width="80" src="{{static_res("/webgame/images/logo.png")}}" alt="">
                    </div>
                    <p class="login_container">
                        <span class="pr"><input type="text" placeholder="请输入用户名" name="login_in_phoneNum" id="login-username"></span>
                        <span class="login-name-errormsg errormsg" id="login-username-error">{{-- <i></i>账号不能为空</span> --}}</span>
                    </p>
                    <p class="login_in_paw login_container">
                        <span class="pr"><input type="password" placeholder="输入登录密码" name="login_in_password" class="bdcol-red" id="login-pwd"></span>
                        <span class="login-name-errormsg errormsg" id="login-pwd-error">{{-- <i></i>账号不能为空 --}}</span>
                    </p>
                    <p class="clearfix login_container verifiy-code-container" id="login-captacha-con">
                        <input type="text" placeholder="输入验证码" class="verification fl" name="login_code" id="login-captcha">
                        <img class="fl verifiy-code-img" src="">
                        <span class="login-name-errormsg errormsg" id="login-captcha-error">{{-- <i></i>账号不能为空 --}}</span></span>
                    </p>
                    <p class="login_in_btn">
                        <a href="javascript:;" id="loginBtn">登录</a>
                    </p>
                    <p style="text-align: right; width: 300px;margin-bottom: 20px;"><a class="forget" href="//www.vronline.com/forgetpwd" target="_blank">忘记密码？</a></p>
                    @if($third)
                    <p class="login_in_quick">
                        <span><a class="qq" id="login-qq">QQ登录</a><a class="wx" id="login-wx">微信登录</a><a class="weibo" id="login-wb">微博登录</a></span>
                    </p>
                    @endif
                </li>
                <li class="login{{$type=="reg"?" cur":""}}" id="user_register">
                    <div class="login_body">
                        <form id="user_registerForm">
                            <p class="pr reg_container">
                                <i></i><input type="text" placeholder="输入账号" name="phoneNumber" class="login_phoneNum" id="reg-username">
                            </p>
                            <span class="login-name-errormsg errormsg" id="reg-username-error">{{-- <i></i>账号不能为空 --}}</span></span>
                            <p class="pr hide">
                                {{-- <i></i><input type="text" placeholder="输入昵称" name="nickName">
                                <span class="login-name-errormsg errormsg"></span></span> --}}
                            </p>
                            <p class="pr reg_container">
                                <i></i><input type="password" placeholder="密码" name="loginPassword" class="loginPassword" id="reg-pwd">
                            </p>
                            <span class="login-name-errormsg errormsg" id="reg-pwd-error">{{-- <i></i>账号不能为空</span> --}}</span>
                            <p class="pr reg_container">
                                <i></i><input type="password" placeholder="再次输入密码" name="checkLoginPassword" id="reg-pwd-confirm">
                            </p>
                            <span class="login-name-errormsg errormsg" id="reg-pwd-confirm-error">{{-- <i></i>账号不能为空 --}}</span></span>
                            <div class="verifiy-code-container reg clearfix" id="reg-captacha-con">
                                <i></i><input type="text" placeholder="输入验证码" class="verification fl" name="reg_code" id="reg-captcha"><img src="" class="fl">
                                <span class="login-name-errormsg errormsg" id="reg-captcha-error">{{-- <i></i>账号不能为空</span> --}}</span>
                            </div>
                        </form>
                        <div class="clearfix login_agreement">
                            <i class="login_sel cur fl" id="agreement"></i>
                            <div class="fl agreement">
                                <b class="fl language" data-name="agreed">我已阅读并同意</b><a href="//www.vronline.com/user_agreement" class="language fl" target="_blank" data-name="vo_agreement">《VRonline用户协议》</a>
                            </div>
                            <span class="login-name-errormsg errormsg" id="agreement-error">{{-- <i></i>账号不能为空</span> --}}</span>
                        </div>
                        <div class="register language" data-name="register" id="regBtn">
                            注册
                        </div>
                        {{-- <div class="hasAccount language" data-name="existing_account">
                            <a href="javascript:;" class="log_in language" data-name="log_in">游戏试玩&gt;
&gt;
</a>
                        </div> --}}
                    </div>
                </li>
            </ol>
        </div>
    </div>
</body>
</html>
<script type="text/javascript">
    $('.window_login_in ul li').click(function(){
        var i = $(this).index();
        $(this).addClass('cur').siblings().removeClass('cur');
        $(this).parents('.window_login_in').find('ol li').eq(i).addClass('cur').siblings().removeClass('cur');
    });

    var messenger = new Messenger('minilogin', 'web-vronline');
    messenger.addTarget(window.parent, 'loginOpener');

    $(".win_close").click(function(){
        var msg = {call:"closeLogin"};
        messenger.targets["loginOpener"].send(JSON.stringify(msg));
    });

    webgameLogin.init({
        type:'bind',
        errorCallBack:function(errorCon,error){
            if(!error){
                $(errorCon).html("");
            }else{
                $(errorCon).html("<i></i>"+error);
            }
        },
        successCallBack:function(type){
            var msg = {call:"successCallBack"};
            messenger.targets["loginOpener"].send(JSON.stringify(msg));
        }
    });
</script>
