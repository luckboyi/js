var loginFn ={
    login:function(){
        var _this=this;
        loginHtml = '<div class="mask pr pageGame_login_in show">\
                <div class="window_login_in pr">\
                    <div class="win_close pa hide"></div>\
                    <ul class="clearfix">\
                        <li class="fl cur"><a href="javascript:;">用户登录</a></li>\
                        <li class="fl "><a href="javascript:;">新用户注册</a></li>\
                    </ul><ol>\
                        <li class="login_in_input cur" id="user_login">\
                            <p>\
                                <span class="pr">\
                                    <input type="text" placeholder="请输入用户名" name="login_in_phoneNum">\
                                    <i class="pa"></i>\
                                </span>\
                            </p>\
                            <p class="login_in_paw">\
                                <span class="pr">\
                                    <input type="password" placeholder="输入登录密码" name="login_in_password">\
                                    <i class="pa"></i>\
                                    <span class="user_login pa" style="top: 30px;"></span>\
                                </span>\
                            </p>\
                            <p class="login_in_btn" style="margin-top: 20px;"><a href="javascript:;">登录</a></p>\
                            <p class="login_in_quick">\
                                <span>\
                                    <a class="qq" href="javascript:;">QQ登录</a>\
                                    <a class="wx" href="javascript:;">微信登录</a>\
                                    <a class="weibo" href="javascript:;">微博登录</a>\
                                </span>\
                            </p>\
                        </li>';
        loginHtml +='<li class="login ">\
                            <div class="login_body">\
                                <form id="user_registerForm">\
                                    <p class="pr">\
                                        <i ></i>\
                                        <input type="text" placeholder="输入账号" name="phoneNumber" class="login_phoneNum" id="login_phoneNum">\
                                        <span class="Validform_checktip pa "></span>\
                                    </p>\
                                    <p class="pr hide">\
                                        <i></i>\
                                        <input type="text" placeholder="输入昵称" name="nickName">\
                                        <span class="Validform_checktip pa"></span>\
                                    </p>\
                                    <p class="pr">\
                                        <i></i>\
                                        <input type="password" placeholder="密码" name="loginPassword" class="loginPassword">\
                                        <span class="Validform_checktip pa"></span>\
                                    </p>\
                                    <p class="pr">\
                                        <i></i>\
                                        <input type="password" placeholder="再次输入密码" name="checkLoginPassword">\
                                        <span class="Validform_checktip pa"></span>\
                                    </p>\
                                    <p class="clearfix pr message_code ">\
                                        <i></i>\
                                        <input type="text" placeholder="输入验证码" class="verification fl" name="verifyCode">\
                                        <img src="image/yanzm.jpg">\
                                        <span class="Validform_checktip pa"></span>\
                                    </p>\
                                </form>\
                                <div class="clearfix login_agreement">\
                                    <i class="login_sel cur fl"></i>\
                                    <div class="fl agreement" ><b class="fl language" data-name="agreed">我已阅读并同意</b><a href="javascript:;" class="language fl" data-name="vo_agreement">《维奥相关用户协议》</a></div>\
                                </div>\
                                <div class="register language" data-name="register">注册</div>\
                                <div class="hasAccount language" data-name="existing_account">\
                                    <a href="javascript:;" class="log_in language" data-name="log_in">游戏试玩>></a>\
                                </div>\
                            </div>\
                        </li>\
                    </ol>\
                </div>\
            </div>';
        $('body').append(loginHtml);
        //点击tab切换
        $('.window_login_in ul li').on('click',function(){
            var i = $(this).index();
            $(this).addClass('cur').siblings().removeClass('cur');
            $(this).parents('.window_login_in').find('ol li').eq(i).addClass('cur').siblings().removeClass('cur');
        });
        //点击登录的时候
        $('.login_in_btn a').on('click',function(){
            var name = $('#user_login').find("input[name='login_in_phoneNum']").val();
            var pwd = $('#user_login').find("input[name='login_in_password']").val();
            if(name == ''){
                $('.user_login').html('账号不能为空').addClass('error_msg');
            }else if(pwd == ''){
                $('.user_login').html('密码不能为空').addClass('error_msg');
            }else{
                $.ajax({
                    url:'http://www.vronline.com/web/login',
                    async:true,
                    type:'GET',
                    dataType:'json',
                    data:{name:name, pwd:pwd},
                    headers: {  // header属性，是为了避免跨站伪造请求攻击写的
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function(data){
                        if(data.status ==  1){
                            window.location.href =window.location.href
                        }else if(data.status == 0){
                            $('.user_login').html('用户名或密码错误').addClass('error_msg');
                        }
                    }
                })
            }

        });
        //点击注册
        $('.login .register').on('click',function(){
            var name = $('#user_registerForm input[name="phoneNumber"]').val();
            var pwd = $('#user_registerForm input[name="loginPassword"]').val();
            var confirPwd = $('#user_registerForm input[name="checkLoginPassword"]').val();
            if(name != '' ||pwd !='' || confirPwd !='' ){
                $.ajax({
                    url:'http://www.vronline.com/web/regiser',
                    type:'GET',
                    dataType:'json',
                    data:{name:name, pwd:pwd, confirPwd:confirPwd},
                    headers: {  // header属性，是为了避免跨站伪造请求攻击写的
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success:function(data){
                        if(data.status == 1){
                            _this.success_login();
                            $('.pageGame_login_in ').detach();
                        }else{
                            //注册失败
                            _this.error_login();
                        }
                    }
                })
            }
        });
        //注册账号失去焦点的时候
        $('#user_registerForm input[name="phoneNumber"]').blur(function(){
            var name = $(this).val();
            var _this =$(this)
            $.ajax({
                url:'http://www.vronline.com/web/isExistAcc',
                type:'GET',
                dataType:'json',
                data:{name:name},
                headers: {  // header属性，是为了避免跨站伪造请求攻击写的
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success:function(data){
                    if(data.status == 0){
                        _this.focus();
                        _this.next('.Validform_checktip').html('账号已经被注册').addClass('error_msg');
                        _this.parents('#user_registerForm').find('input[name="loginPassword"]').next('.Validform_checktip').html('').removeClass('error_msg');
                        _this.parents('#user_registerForm').find('input[name="loginPassword"]').css('border-color','#828f9e')
                    }else{
                        console.dir('用户名正确')
                    }
                }
            })
        })
        //登录检测
        //登录失去焦点
        $('#user_login input').each(function(){
            $(this).blur(function(){
                login_check($(this));
            });
        });
        //登录检测
        function login_check(obj){
            var name = obj.attr('name');
            var value = obj.val();
            if(name == 'login_in_phoneNum'){
                if (typeof (value) == 'undefined' || value == "") {
                    $('.user_login').html('账号不能为空').addClass('error_msg');
                    return '账号不能为空';
                }else{
                    $('.user_login').html('').removeClass('error_msg');
                }
            };
            if(name == 'login_in_password'){
                if (typeof (value) == 'undefined' || value == "") {
                    obj.addClass('bdcol-red');
                    $('.user_login').html('密码不能为空').addClass('error_msg');
                    return '密码不能为空';
                }else{
                    $('.user_login').html('').removeClass('error_msg');
                }
            };
        }
        //注册失去焦点
        $('#user_registerForm input').each(function(){
            $(this).blur(function () {
                var errmsg = register_check(this);
                var valid = $(this).siblings(".Validform_checktip");
                if(errmsg !== ''){
                    valid.addClass('error_msg').html(errmsg);
                    $(this).css('border-color','#c83434')
                }else{
                    valid.removeClass('error_msg').html('');
                    $(this).css('border-color','#828f9e')
                }
                if($(this).hasClass('login_phoneNum')){
                    var  json ={};
                    json.account =$(this).val();
                }
            });
        });
        //注册获取焦点的时候
        $('#user_registerForm input').each(function(){
            $(this).focus(function () {
                var valid = $(this).siblings(".Validform_checktip");
                valid.removeClass('error_msg').html('');
            });
        });
        //注册检测
        function register_check(e){
            var o =$(e);
            var value = $.trim(o.val());
            var errmsg = '';
            var name = o.attr('name');
            switch (name){
                case 'phoneNumber':
                    if(value == ''){
                        errmsg ='不能为空';
                        break;
                    }
                    var rs = /^(13|14|15|17|18)[0-9]{9}$/;
                    if(rs.test(value)){
                        errmsg = '不能使用手机号注册';
                        break;
                    }
                    var a = value.length;
                    if(a<6 || a>18){
                        errmsg = '账号长度只能6~18个字符'
                    }
                    break;
                case 'nickName':
                    if (value == "") {
                        errmsg = "昵称不能为空";
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
                        errmsg = "昵称长度只能6~18个字符";
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
                case 'verifyCode':
                    if (value == "") {
                        errmsg = "验证码不能为空" ;
                        break;
                    }
            }
            return errmsg;
        };
        //点击注册的时候;


    },
    success_login:function(){
        var result = '<div class="mask success_register show">\
                        <div class="verification_con show">\
                            <div class="verification_head clearfix" >\
                                <h3 class="fl language" data-name="registration">注册</h3>\
                                <i class="verification_close fr"></i>\
                            </div>\
                            <div class="verification_body" style="">\
                                <p><i  class="language " data-name="your_msg">你的账户</i><em  class="language " data-name="registration_msg" style="color: #23a0bf;">注册成功！</em></p>\
                                <p class="language" data-name="registration_save">请妥善保管好账号密码，确保账号安全</p>\
                            </div>\
                            <div class="success_register_btn language" data-name="success_register">完成并登录</div>\
                        </div>\
                    </div>';
        $('body').append(result);
        $('.success_register_btn').on('click',function(){
            $('.pageGame_login_in ').detach();
            $(this).parents('.success_register').detach();
            window.location.href = window.location.href;
        })

    },
    error_login:function(){var result = '<div class="mask success_register show error_register">\
                        <div class="verification_con show">\
                            <div class="verification_head clearfix" >\
                                <h3 class="fl language" data-name="registration">注册</h3>\
                                <i class="verification_close fr hide"></i>\
                            </div>\
                            <div class="verification_body" style="">\
                                <p><i  class="language " data-name="your_msg">你的账户</i><em  class="language " data-name="registration_msg" style="color: #23a0bf;">注册失败！</em></p>\
                            </div>\
                            <div class="success_register_btn language" data-name="success_register">返回并重新注册</div>\
                        </div>\
                    </div>';
        $('body').append(result);
        $('.error_register .success_register_btn').on('click',function(){
            $(this).parents('.error_register').detach();
            loginFn.login();
        })
    }
};