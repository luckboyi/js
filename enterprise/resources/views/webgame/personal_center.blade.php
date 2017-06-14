<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <title>个人中心</title>
    <link rel="stylesheet" href="{{asset('css/base.css')}}">
    <link rel="stylesheet" href="{{asset('css/personal_center.css')}}">
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery-1.11.3.min.js"></script>
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.Jcrop.js"></script>
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
    {{--<script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>--}}
    <link href="{{asset('css/webuploader.css')}}" rel="stylesheet">


    <script src="{{asset('js/jquery-1.7.1.min.js')}}"></script>
    <script src="{{asset('js/ajaxfileupload.js')}}"></script>
    <script src="{{asset('js/artDialog4.1.6/jquery.artDialog.js?skin=default')}}"></script>
    <script src="{{asset('js/artDialog4.1.6/plugins/iframeTools.js')}}"></script>
</head>
<body>
    <div class="personal_center clearfix">
        <div class="left_per fl">
            <ul>
                <li class="pr cur userMsg"><a href="javascript:;">用户资料</a></li>
                <li class="pr charge">
                    <i class="cur"></i>
                    <a href="javascript:;">充值中心</a>
                    <ol class="">
                        <li class="cur">支付宝</li>
                    </ol>

                </li>
                <li class="pr problem"><a href="javascript:;">常见问题</a></li>
            </ul>
        </div>
        <div class="right_per">
            <ul class="in_right_per">
                <!--用户资料S-->
                <li class="list_con user_msg cur">
                    <div class="user_header">
                        <p class="pr img_con">
                           <!--  <input type="file" name="head_photo" id="head_photo" value=""> -->

                            <img src="{{asset('images/list_img.jpg')}}" id="head_photo_src">
                            <input type="hidden" name="photo_pic" id="photo_pic" value="">
                            <!-- <i class="edit pa" name="head_photo" id="head_photo"></i> -->
                            <input type="file" name="head_photo" id="head_photo" value="">

                            <!-- <input type="file" name="head_photo" id="head_photo" value="">
                            <input type="hidden" name="photo_pic" id="photo_pic" value=""> -->
                            <!--头像显示-->
                            <!-- <div id="show_photo" <img id="head_photo_src" src="images/default.gif"></div> -->
                        </p>
                        <p class="title" id="title">
                            @if ($userinfo['account'])
                                {{ $userinfo['account'] }}
                            @else
                                游客
                            @endif
                        </p>
                        <div class="clearfix submit_msg pr">
                            <i></i>
                            <p class="fl">最近一次登录:</p>
                            <span class="fl month">{{ $userinfo['last_month'] }}</span>
                            <span class="fl time">{{ $userinfo['last_time'] }}</span>
                            <span class="fl country">{{ $userinfo['country']}}</span>
                        </div>
                    </div>
                    <div class="user_con">
                        <ul class="clearfix">
                            <li class="fl">
                                <h4>基础资料：</h4>
                                <p class="clearfix">
                                    <span class="fl">昵称</span>
                                    <input id="nick" type="text" class="fl" value="{{ $userinfo['nick']}}">
                                    <span class="fr save_btn" id="btnNick">保存</span>
                                </p>
                            </li>
                            <li class="fl">
                                <h4 class="icon pr"><i></i>账号安全：<p class="tips pa user_tips">温馨提示：建议您完善您的账户资料，账号更安全！</p></h4>
                                <p class="clearfix">
                                    <span class="fl">平台账号</span>
                                    <span class="fl name" id="accountName">
                                        @if ($userinfo['account'])
                                            {{ $userinfo['account'] }}
                                        @else
                                            游客
                                        @endif
                                    </span>

                                    @if (!$userinfo['account'])
                                        <span class="fr revamp_btn" id="openBtn">修改</span>
                                    @endif


                                </p>
                            </li>
                            <li class="fl">
                                <h4 class="icon pr"><i></i>绑定手机：<p class="tips pa phone_tips">检测到您的账号还没有绑定手机，为了提高账号安全和密码找回，请及时进行手机绑定！</p></h4>
                                <p class="clearfix">
                                    <span class="fl" id="bindMobile">
                                         @if ($userinfo['bindmobile'])
                                            {{ $userinfo['bindmobile'] }}
                                        @else
                                            你的账户未绑定手机
                                        @endif

                                    </span>

                                    @if (!$userinfo['bindmobile'])
                                        <span class="fr phone_btn" id="btnBindMobile">绑定手机</span>
                                    @endif


                                </p>
                            </li>
                            <li class="fl">
                                <h4>基础资料：</h4>
                                <p class="clearfix">
                                    <span class="fl">不定期修改密码 增加账号安全</span>
                                    <span class="fr revamp_paw_btn">修改密码</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--用户资料E-->
            </ul>

        </div>
    </div>
    <!--弹窗-->
    <!--修改头像S-->
    <!-- <div class="mask_layer  modify_box">
        <div class=" modify_head_portrait popup_window" id="jcropdiv">
            <div class="wl">
                <div class="jc-demo-box" data="0">
                    <div class="jcrop-holder" style="width: 395px; height: 340px; position: relative; background-color: rgb(21, 25, 32);"><div id="small" style="position: absolute; z-index: 600; width: 100px; height: 123px; top: 120px; left: 148px;"><div style="width: 100%; height: 100%; z-index: 310; position: absolute; overflow: hidden;"><div class="jcrop-hline" style="position: absolute; opacity: 0.4;"></div><div class="jcrop-hline bottom" style="position: absolute; opacity: 0.4;"></div><div class="jcrop-vline right" style="position: absolute; opacity: 0.4;"></div><div class="jcrop-vline" style="position: absolute; opacity: 0.4;"></div><div class="jcrop-tracker" style="cursor: move; position: absolute; z-index: 360;"></div></div><div style="width: 100%; height: 100%; z-index: 320; display: block;"><div class="ord2-n jcrop-dragbar" style="cursor: n-resize; position: absolute; z-index: 370;"></div><div class="ord2-s jcrop-dragbar" style="cursor: s-resize; position: absolute; z-index: 371;"></div><div class="ord2-e jcrop-dragbar" style="cursor: e-resize; position: absolute; z-index: 372;"></div><div class="ord2-w jcrop-dragbar" style="cursor: w-resize; position: absolute; z-index: 373;"></div><div class="ord-n jcrop-handle" style="cursor: n-resize; position: absolute; z-index: 374; opacity: 0.5;"></div><div class="ord-s jcrop-handle" style="cursor: s-resize; position: absolute; z-index: 375; opacity: 0.5;"></div><div class="ord-e jcrop-handle" style="cursor: e-resize; position: absolute; z-index: 376; opacity: 0.5;"></div><div class="ord-w jcrop-handle" style="cursor: w-resize; position: absolute; z-index: 377; opacity: 0.5;"></div><div class="ord-nw jcrop-handle" style="cursor: nw-resize; position: absolute; z-index: 378; opacity: 0.5;"></div><div class="ord-ne jcrop-handle" style="cursor: ne-resize; position: absolute; z-index: 379; opacity: 0.5;"></div><div class="ord-se jcrop-handle" style="cursor: se-resize; position: absolute; z-index: 380; opacity: 0.5;"></div><div class="ord-sw jcrop-handle" style="cursor: sw-resize; position: absolute; z-index: 381; opacity: 0.5;"></div></div></div><div class="jcrop-tracker" style="width: 395px; height: 340px; position: absolute; top: 0px; left: 0px; z-index: 290; cursor: crosshair;"></div><div id="opa" style="position: absolute; z-index: 240; opacity: 0.5; top: 0px; left: 0px; width: 100%; height: 100%;"><div style="position: absolute; left: 148px; width: 100px; height: 120px; top: 0px; background-color: rgb(21, 25, 32);"></div><div style="position: absolute; height: 340px; width: 148px; top: 0px; left: 0px; background-color: rgb(21, 25, 32);"></div><div style="position: absolute; height: 340px; left: 248px; width: 147px; top: 0px; background-color: rgb(21, 25, 32);"></div><div style="position: absolute; top: 243px; left: 148px; width: 100px; height: 97px; background-color: rgb(21, 25, 32);"></div></div><div id="target" class="jcrop_w" style="display: block; visibility: visible; border: none; margin: 0px; padding: 0px; position: absolute; top: 0px; left: 0px; width: 395px; height: 340px; opacity: 1;">
                        <img src="{{asset('images/touxiang.jpg')}}" width="602" height="602" style="height: 340px; width: 340px; top: 0px; left: 27.5px;">
                    </div></div>
                </div>
                <p class="title">如果您还没有设置头像，系统为显示默认头像，您可以上传本地照片作为个人头像</p>
            </div>
            <div class="wr" id="preview-pane">
                <div class="preview-container">
                    <p class="title">预览</p>
                    <p>
                    <span class="pre-1">
                        <img src="{{asset('images/touxiang.jpg')}}" class="jcrop-preview jcrop_preview_s" alt="" style="width: 340px; height: 276px; margin-left: -121px; margin-top: -98px;">
                    </span>
                    </p>
                    <p class="big_img_size">100px<i>X</i>100px</p>
                    <p>
                    <span class="pre-2">
                        <img src="{{asset('images/touxiang.jpg')}}" class="jcrop-preview jcrop_preview_s" alt="" style="width: 204px; height: 166px; margin-left: -72px; margin-top: -59px;">
                    </span>
                    </p>
                    <p class="big_img_size">60px<i>X</i>60px</p>
                </div>
            </div>
            <div class="popup_btn">
                <ul class="clearfix">
                    <li class="fl  pr">上传
                        <form action="xxx.php" method="post" enctype="multipart/form-data"></form><input type="file" name="file" id="file" onchange="previewHeadFile()"  style="opacity: 0; position: absolute; left: 0; top: 0; height: 30px; width: 100px;"></li>
                    <li class="fl cancel">取消</li>
                </ul>
            </div>
        </div>
    </div> -->
    <!--修改头像E-->
    <!--绑定手机号S-->
    <div class="bind_phoneNum mask_layer"  style="display: none;">
        <div class="popup_window  ">
            <div class="in_popup">
                <p class="input_phone">
                    <label>
                        <span>输入手机号</span>
                        <input type="text" id="txtMobile">
                    </label>
                    </p><p class="erroColor erro_phone_msg" id="mobileFlag">请输入手机号码</p>
                <p></p>
                <p class="message">
                    <label>
                        <span>短信验证码</span>
                        <input type="text" id="verNumber" name="verNumber" class="itxt">
                    </label>
                    <span class="get_code" id="setVetify">获取验证码</span>
                </p>
                <p class="system_tip">系统将会以短信方式发送至需要绑定的手机</p>
                <p style="display:none;margin-top:20px;line-height:20px;" id="showText">校验码已发出，请注意查收短信，如果没有收到，<br/>你可以在<span id="showTime" style="color:#f00;font-size:20px;"></span>秒后要求系统重新发送
                        </p>
            </div>
            <div class="popup_btn">
                <ul class="clearfix">
                    <li class="fl sure">确定</li>
                    <li class="fl cancel">取消</li>
                </ul>
            </div>
        </div>
    </div>
    <!--绑定手机号E-->
    <!--修改密码-->
    <div class="revamp_password mask_layer ">
        <div class="popup_window">
            <div class="in_popup">
                <p class="">
                    <label>
                        <span>原密码</span>
                        <input type="password" id="oldPwd">
                    </label>
                    </p><p class="erroColor erro_phone_msg" id="pwdMsg1" flag="1">请输入原密码</p>
                <p></p>
                 <p>
                    <label>
                        <span>新密码</span>
                        <input type="password" id="newPwd">
                        </label></p><p class="erroColor erro_phone_msg" id="pwdMsg2" flag="1">请输入新密码</p>

                <p></p>
                <p>
                     <label>
                        <span>确认密码</span>
                        <input type="password" id="newPwdConfirm">
                        </label></p><p class="erroColor erro_phone_msg" id="pwdMsg3" flag="1">请再次输入新密码</p>

                <p></p>
            </div>
            <div class="popup_btn">
                <ul class="clearfix">
                    <li class="fl sure">确定</li>
                    <li class="fl cancel">取消</li>
                </ul>
            </div>
        </div>
    </div>
    <!--手机绑定成功-->
    <div class="mask_layer success_popup_window">
        <div class="popup_window" >
            <div class="popup_head clearfix pr hide">
                <h4 class="fl">提示</h4>
                <i class=" close pa"></i>
            </div>
            <div class="in_popup success_popup">
                <p class="">手机绑定成功</p>
            </div>
            <div class="popup_btn success_popup_btn">
                <ul class="clearfix">
                    <li class="fl sure">确定</li>
                </ul>
            </div>
        </div>
    </div>
    <!--修改平台账号-->
    <div class="revamp_account mask_layer">
        <div class="popup_window">
            <div class="in_popup">
                <p class="">
                    <label>
                        <span>平台账号</span>
                        <input id="openAccount" type="text">
                    </label>
                </p><p class="erroColor erro_phone_msg"><lable id="openLbl1" flag="1">账号必须是英文字母组合</lable></p>
                <p></p>
                <p>
                    <label>
                        <span>新密码</span>
                        <input id="openPwd" type="password">
                    </label>
                </p><p class="erroColor erro_phone_msg"><lable id="openLbl2"  flag="1">密码不能为空、密码需要6~16位数字或字母</lable></p>
                <p></p>
                <p>
                    <label>
                        <span>确认密码</span>
                        <input  id="openPwdConfirm" type="password">
                    </label>
                </p><p class="erroColor erro_phone_msg"><lable id="openLbl3"  flag="1">两次密码必须一致</lable></p>
                <p></p>
            </div>
            <div class="popup_btn">
                <ul class="clearfix">
                    <li class="fl sure" id="btnAccount">修改</li>
                </ul>
                <p class="erroColor erro_phone_msg">账号只能提供1次修改,确认无误后请点击“修改”</p>
            </div>
        </div>
    </div>
    <!--//确定修改平台账号-->
    <div class="mask_layer sure_revamp_account">
        <div class="popup_window" >
            <div class="popup_head clearfix pr hide">
                <h4 class="fl">提示</h4>
                <i class=" close pa"></i>
            </div>
            <div class="in_popup success_popup">
                <p class="">新设置的平台账号和密码可直接登录VR平台</p>
                <p>是否确定修改账号?</p>
            </div>
            <div class="popup_btn success_popup_btn">
                <ul class="clearfix">
                    <li class="fl sure">确定</li>
                </ul>
            </div>
        </div>
    </div>
    <!--//成功修改平台账号-->
    <div class="mask_layer success_revamp_account">
        <div class="popup_window" >
            <div class="popup_head clearfix pr hide">
                <h4 class="fl">提示</h4>
                <i class=" close pa"></i>
            </div>
            <div class="in_popup success_popup">
                <p class="">修改成功！</p>
                <p>您的账号还没有绑定手机，绑定手机后可享受手机找密码等功能</p>
                <p>个人中心→用户资料→手机绑定</p>
            </div>
            <div class="popup_btn success_popup_btn">
                <ul class="clearfix">
                    <li class="fl sure">确定</li>
                </ul>
            </div>
        </div>
    </div>

<script>
    $(function(){
        //hover-tip
        $('.user_con .icon i').hover(function() {
            $(this).parents('.icon').find('p').show()
        }, function() {
            $(this).parents('.icon').find('p').hide()
        });

        //点击左侧进入
        $('.personal_center .left_per ul').on('click','li.pr',function(){
            var i = $(this).index();
            $(this).addClass('cur').siblings().removeClass('cur');
            $('.right_per li.list_con').eq(i).addClass('cur').siblings().removeClass('cur');

        });
        //点击充值
        $('.personal_center .left_per ul').on('click','li.charge',function(){
            $(this).find('ol').toggle();
            $(this).find('i').toggleClass('cur')
        })
        //点击支付列表
        $('.personal_center .left_per  ol').on('click','li',function(){
            $(this).parents('ol').show();
        });

        //点击充值中心 下拉
        $('.personal_center .left_per li.charge i').on('click',function(){
           $(this).toggleClass('cur');
            $(this).hasClass('cur') ? $(this).parents('li.charge').find('ol').show():$(this).parents('li.charge').find('ol').hide();
        });
        //点击充值V币或者页游
        $('.paymentCenter  .pay_sel_btn').on('click','span',function(){
           $(this).addClass('cur').siblings().removeClass('cur');
            $('.paymentCenter .pageGame_sel').toggle();
        });



        //点击修改头像显示
        $('.user_header p.img_con').on('click',function(){
            $('.modify_box').show()
        });
        //点击绑定手机号弹窗
        $('.phone_btn').on('click',function(){
            $('.bind_phoneNum').show()
        });
        //修改密码
        $('.revamp_paw_btn').on('click',function(){
            $('.revamp_password').show()
        });
        //点击确定
        $('.revamp_password  .sure').on('click',function(){

            // 在这里判断，绑定账号和密码输入密码是否合法
            var flag1 = parseInt($("#pwdMsg1").attr('flag'));
            var flag2 = parseInt($("#pwdMsg2").attr('flag'));
            var flag3 = parseInt($("#pwdMsg3").attr('flag'));

            if (flag1 == 1) {  // 原密码不合法
                $("#oldPwd").focus();
                return;
            };
            if (flag2 == 1) {  // 新密码
                $("#newPwd").focus();
                return;
            };
            if (flag3 == 1) {  // 确认新密码
                $("#newPwdConfirm").focus();
                return;
            };

            // 开始向控制器发送请求修改密码
            var oldPwd = $("#oldPwd").val();
            var newPwd = $("#newPwd").val();
            var newPwdConfirm = $("#newPwdConfirm").val();

            $.ajax({
            type: 'get',
            url: '/webgame/modifyPwd',
            data: { oldPwd : oldPwd, newPwd : newPwd, confirPwd : newPwdConfirm},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {
                    // 重置文本框中的值
                    $("#oldPwd").val('');
                    $("#newPwd").val('');
                    $("#newPwdConfirm").val('');

                    // 修改密码成功后
                    alert('修改密码成功!');

                    $('.revamp_password ').hide()

                }else{
                    alert(data.msg)
                    return;
                }
            },
            error: function(xhr, type){
            }
            });
        });

        //点击取消
        $('.cancel').on('click',function(){
            $(this).parents('.mask_layer').hide();

            var imgDate = {};
            imgDate.w = $('.wl').find('#small').width();
            imgDate.h = $('.wl').find('#small').height();
            imgDate.l = $('.wl').find('#small').position().left;
            imgDate.t = $('.wl').find('#small').position().top;
            //console.dir(imgDate);
        });
        //点击保存

        //点击绑定成功确定或者取消关闭
        $('.bind_phoneNum .popup_btn').on('click','li',function(){
            if($(this).hasClass('sure')){
                // 这里向controller 控制发起请求绑定手机号码逻辑
                var txtMobile = $("#txtMobile").val();  // 手机号码
                validateMobile(txtMobile);
                var verNumber = $("#verNumber").val();  // 验证码

                if (verNumber.length == 0) {
                    alert('验证码不能为空');
                    return;
                };

                $.ajax({
                type: 'get',
                url: '/webgame/bindMobile',
                data: { mobile : txtMobile, code : verNumber},
                dataType: 'json',
                headers: {  // header属性，是为了避免跨站伪造请求攻击写的
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(data){
                    if (data.status == 1) {
                        // 手机号码绑定成功
                        $("#bindMobile").text(txtMobile);  // 显示绑定的手机号码
                        $("#btnBindMobile").hide();        // 绑定手机号 按钮不可见

                        $('.success_popup_window').show();
                        $(this).parents('.bind_phoneNum').hide()
                    }else{
                        alert(data.msg);
                        return;
                    }
                },
                error: function(xhr, type){
                }
                });
            }
        });
        //点击绑定成功确定按钮
        $('.success_popup_window .sure').on('click',function(){
            $(this).parents('.success_popup_window').hide()
        });

        //修改平台账号
        $('.revamp_btn').on('click',function(){
            $('.revamp_account').show()
        });
        //点击修改
        $('.revamp_account .sure').on('click',function(){

            // 在这里判断，绑定账号和密码输入密码是否合法
            var flag1 = parseInt($("#openLbl1").attr('flag'));
            var flag2 = parseInt($("#openLbl2").attr('flag'));
            var flag3 = parseInt($("#openLbl3").attr('flag'));

            if (flag1 == 1) {  // 账户不合法
                $("#openAccount").focus();
                return;
            };
            if (flag2 == 1) {
                $("#openPwd").focus();
                return;
            };
            if (flag3 == 1) {
                $("#openPwdConfirm").focus();
                return;
            };

            $('.revamp_account').hide();
            $('.sure_revamp_account').show()
        });
        //点击确定
        $('.sure_revamp_account .sure').on('click',function(){
            $('.sure_revamp_account').hide();
            // 这里开始绑定账号
            var account = $("#openAccount").val();
            var pwd = $("#openPwd").val();
            var confirPwd = $("#openPwdConfirm").val();

            $.ajax({
            type: 'get',
            url: '/webgame/bindAccount',
            data: { name : account, pwd : pwd, confirPwd : confirPwd},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {
                    // 账号绑定成功后，需要给账户名，以及绑定账号旁边的按钮不可见
                    $("#title").text(account);
                    $("#accountName").text(account);
                    $("#openBtn").hide();


                }else{
                    alert(data.msg)
                    return;
                }
            },
            error: function(xhr, type){
            }
            });





        });
        //充值中心
        $('.other_pay_num i').hover(function(){
            $('.pay_num_tips').show();
        },function(){
            $('.pay_num_tips').hide();
        });
        //选择充值金额
        $('.payment_num span').on('click',function(){
            $(this).addClass('cur').siblings().removeClass('cur');
            var num = $(this).text();
            var i = num.substring(1,num.length);
            if($('.pay_sel_btn span').eq(0).hasClass('cur')){
                if(i > $('.select_right  .balance_num').text()){
                    $('.payment_tips').show();
                    $('.select_right .pay_now').addClass('disabled');
                }else{
                    $('.payment_tips').hide();
                    $('.select_right .pay_now').removeClass('disabled');
                }
            }

            $('.other_pay_num input').val(i);
            $('.select_right .pay_num').text(i);
        });
        //判断输入是否为整数；
        function checkRate(input){
            var re = /^(\+|-)?\d+$/;
            if(re.test(input) && input > 0){
                $('.erro_tips').hide();
                return true;
            }else{
                $('.erro_tips').show();
            }
        };
        //失去焦点
        $('.other_pay_num input').blur(function(){
            var num = $('.other_pay_num input').val();
            $('.payment_num').find('span').each(function(key){
                $(this).removeClass('cur')
            });
            if(num != ''){
                checkRate(num);
            }else{
                $('.payment_num').find('span').each(function(key){
                    if($(this).eq(key).hasClass('cur')){
                        var n = $(this).eq(key).text();
                        num = n.substring(1,n.length) ;
                        $('.other_pay_num input').val(num);
                    }
                });
            };
            $('.select_right .pay_num').text(num);
            //v支付几个点
            //判断当前余额与充值金额    有问题
            if(num > $('.select_right  span.balance_num').text()){

            }
        });
        //点击确定
        $('.payment_btn').on('click',function(){
            var num = $('.other_pay_num input').val();

        });
        //选择充值方式
        $('.payment_select ul li').on('click',function(){
            $(this).addClass('cur').siblings().removeClass('cur');
            if($(this).hasClass('vt_pay')){
                $('.vr_pay_con').show();
                $('.zhi_pay_con').hide();
            }else{
                $('.vr_pay_con').hide();
                $('.zhi_pay_con').show();
            }
        });
        /* //点击关闭
         $('.close').on('click',function(){
         $(this).parents('.success_popup_window').hide(100);
         });*/
    })
    //图片预览
    /*function previewHeadFile(){
        var preview = document.querySelector('.pre-1 img');
        var preview2 = document.querySelector('.jc-demo-box img');
        var preview3 = document.querySelector('.pre-2 img');
        var file  = document.querySelector('input#file').files[0];
        var reader = new FileReader();
        reader.onloadend = function () {
            preview.src = reader.result;
            preview2.src = reader.result;
            preview3.src = reader.result;
            $('.modify_head_portrait .popup_btn .cancel').text('保存');
        };
        if(file){
            reader.readAsDataURL(file);
        }else{
            preview.src = '';
        }
        // var
    }*/

        $(document).ready(function(){

        // 判断用户图象有没有
        var face = "{{ $userinfo['faceUrl']}}";
        if (face != '') {
            $("#head_photo_src").attr('src',face);
        };

        // 输入平台账号，失去焦点事件
        $("#openAccount").blur(function(){  // 失去焦点

            var account = $("#openAccount").val();
            if (account.length == 0) {
                $("#openLbl1").text('平台账号不能为空');
                $("#openLbl1").attr('flag',1);
                $("#openAccount").focus();
                return;
            }
            if (account.length < 6) {
                $("#openLbl1").text('平台账号不合法');
                $("#openLbl1").attr('flag',1);
                $("#openAccount").focus();
                return;
            }

            // 判断是否账户可用
            $.ajax({
            type: 'get',
            url: '/webgame/isExistAcc',
            data: { name : account},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {
                    // 表示账号可用
                    $("#openLbl1").text('平台账号可用');
                    $("#openLbl1").attr('flag',0);
                }else{
                    $("#openLbl1").text('平台账户已被注册');
                    $("#openLbl1").attr('flag',1);
                    $("#openAccount").focus();
                    return;
                }
            },
            error: function(xhr, type){
            }
            });
        });

        // 绑定账号密码1
        $("#openPwd").blur(function(){

            var pwd = $("#openPwd").val();
            if (pwd.length == 0) {
                $("#openLbl2").text('密码不能为空');
                $("#openLbl2").attr('flag',1);
                $("#openPwd").focus();
                return;
            }
            if (pwd.length < 6) {
                $("#openLbl2").text('密码不合法');
                $("#openLbl2").attr('flag',1);
                $("#openPwd").focus();
                return;
            }
            $("#openLbl2").text('ok');
            $("#openLbl2").attr('flag',0);
            var pwdConfirm = $("#openPwdConfirm").val();
            // 如果确认密码不为空，则判断下两次密码是否一致

            if (pwdConfirm.length > 5) {
                if (pwd !== pwdConfirm) {
                    $("#openLbl2").text('密码和确认密码不一致');
                    $("#openLbl2").attr('flag',1);
                    return;
                };
                $("#openLbl3").text('ok');
                $("#openLbl3").attr('flag',0);
            };

        });

        // 绑定账号密码2
        $("#openPwdConfirm").blur(function(){

            var pwdConfirm = $("#openPwdConfirm").val();
            if (pwdConfirm.length == 0) {
                $("#openLbl3").text('密码不能为空');
                $("#openLbl3").attr('flag',1);
                $("#openPwdConfirm").focus();
                return;
            }
            if (pwdConfirm.length < 6) {
                $("#openLbl3").text('密码不合法');
                $("#openLbl3").attr('flag',1);
                $("#openPwdConfirm").focus();
                return;
            }
            $("#openLbl3").text('ok');
            $("#openLbl3").attr('flag',0);
            var openPwd = $("#openPwd").val();
            // 如果确认密码不为空，则判断下两次密码是否一致

            if (openPwd.length > 5) {
                if (pwdConfirm !== openPwd) {
                    $("#openLbl3").text('密码和确认密码不一致');
                    $("#openLbl3").attr('flag',1);
                    return;
                };
                $("#openLbl2").text('ok');
                $("#openLbl2").attr('flag',0);
            };

        });

        // 手机号码离开文本框逻辑判断
        $("#txtMobile").blur(function(){

            var txtMobile = $("#txtMobile").val();
            validateMobile(txtMobile);
        });


        //发送手机验证码
        $("#setVetify").on("click",function(e){

            var txtMobile = $("#txtMobile").val();
            validateMobile(txtMobile);

            e.preventDefault();
            var _this=$(this);
            time(_this);
        });


        // 修改密码逻辑
        // 原密码失去焦点事件
        $("#oldPwd").blur(function(){

            var oldPwd = $("#oldPwd").val();
            if (oldPwd.length == 0) {
                $("#pwdMsg1").text('原密码不能为空');
                $("#pwdMsg1").attr('flag',1);
                $("#oldPwd").focus();
                return;
            }
            if (oldPwd.length < 6) {
                $("#pwdMsg1").text('原密码不合法');
                $("#pwdMsg1").attr('flag',1);
                $("#oldPwd").focus();
                return;
            }
            $("#pwdMsg1").text('ok');
            $("#pwdMsg1").attr('flag',0);
        });

        // 输入新密码
        $("#newPwd").blur(function(){

            var newPwd = $("#newPwd").val();
            if (newPwd.length == 0) {
                $("#pwdMsg2").text('新密码不能为空');
                $("#pwdMsg2").attr('flag',1);
                $("#newPwd").focus();
                return;
            }
            if (newPwd.length < 6) {
                $("#pwdMsg2").text('新密码不合法');
                $("#pwdMsg2").attr('flag',1);
                $("#newPwd").focus();
                return;
            }
            $("#pwdMsg2").text('ok');
            $("#pwdMsg2").attr('flag',0);
            var newPwdConfirm = $("#newPwdConfirm").val();
            // 如果确认密码不为空，则判断下两次密码是否一致

            if (newPwdConfirm.length > 5) {
                if (newPwdConfirm !== newPwd) {
                    $("#pwdMsg2").text('新密码和确认密码不一致');
                    $("#pwdMsg2").attr('flag',1);
                    return;
                };
                $("#pwdMsg2").text('ok');
                $("#pwdMsg2").attr('flag',0);
                $("#pwdMsg3").text('ok');
                $("#pwdMsg3").attr('flag',0);
            };

        });

        // 输入确认密码
        $("#newPwdConfirm").blur(function(){

            var newPwdConfirm = $("#newPwdConfirm").val();
            if (newPwdConfirm.length == 0) {
                $("#pwdMsg3").text('确认密码不能为空');
                $("#pwdMsg3").attr('flag',1);
                $("#newPwdConfirm").focus();
                return;
            }
            if (newPwdConfirm.length < 6) {
                $("#pwdMsg3").text('确认密码不合法');
                $("#pwdMsg3").attr('flag',1);
                $("#newPwdConfirm").focus();
                return;
            }
            $("#pwdMsg3").text('ok');
            $("#pwdMsg3").attr('flag',0);
            var newPwd = $("#newPwd").val();
            // 如果确认密码不为空，则判断下两次密码是否一致

            if (newPwd.length > 5) {
                if (newPwd !== newPwdConfirm) {
                    $("#pwdMsg3").text('新密码和确认密码不一致');
                    $("#pwdMsg3").attr('flag',1);
                    return;
                };

                $("#pwdMsg3").text('ok');
                $("#pwdMsg3").attr('flag',0);
                $("#pwdMsg2").text('ok');
                $("#pwdMsg2").attr('flag',0);

            };

        });


        // 修改昵称
        $("#btnNick").click( function () {
            var nick = $("#nick").val();

            if (nick.length == 0) {
                alert('昵称不能为空');
                return;
            };

            $.ajax({
            type: 'get',
            url: '/webgame/modifyNick',
            data: { nick : nick},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {  // 昵称修改成功
                    alert('昵称修改成功');
                }else{
                    alert(data.msg);
                }
            },
            error: function(xhr, type){
            }
            });
        });

    })

    function time(o) {

        timeMins(o);

        var txtMobile = $("#txtMobile").val();
        //发送短信验证码
        $.ajax({
            type: 'get',
            url: '/webgame/sendMobileMsg',
            data: { mobile : txtMobile},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {
                   // 验证码发送成功
                   $("#showText").show();
                   $("#verNumber").attr("disabled",false);
                   $(o).css("color","#999").attr("onclick","");

                }else{

                    alert("短信发送失败，失败原因:"+data.msg);
                    return;
                }
            },
            error: function(xhr, type){
            }
        });
    }
    //时间倒计时
    var wait=60,timeOut;
    function timeMins(o) {
        if (wait == 0) {
             $("#showText").hide();
             $(o).html("获取验证码").css("color","#005ea7").attr("onclick","time(this)");
             clearTimeout(timeOut);
             wait = 60;
        } else {
            wait--;
            timeOut=setTimeout(function(){
                    timeMins(o);
            },1000);
            $("#showTime").html(wait+"s");
        }
    }

    // 验证手机号码是否合法
    function validateMobile(mobile){
        if(mobile.length==0) {
                $("#mobileFlag").text('手机号码不能为空');
                $("#txtMobile").focus();
                return;
            }
            if(mobile.length!=11) {
               $("#mobileFlag").text('请输入有效的手机号码！');
               $("#txtMobile").focus()
               return;
            }

            var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
            if(!myreg.test(mobile)) {
                   $("#mobileFlag").text('请输入有效的手机号码！');
                   $("#txtMobile").focus()
                   return;
            }
            $("#mobileFlag").text('ok');
    }


    $('#head_photo').live('change',function(){
            ajaxFileUploadview('head_photo','photo_pic',"cropUpload");
    });

    function show_head(head_file){

        var faceFile = head_file;
        $.ajax({
            type: 'get',
            url: '/webgame/modifyPic',
            data: { faceFile : faceFile},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {  // 更新图象成功
                    // 刷新当前页面
                    /*window.location.reload();*/

                    $("#head_photo_src").attr('src',data.picSrc);
                }else{
                    alert(data.msg);
                }
            },
            error: function(xhr, type){
            }
        });
    }

    //文件上传带预览
    function ajaxFileUploadview(imgid,hiddenid,url){

        $.ajaxFileUpload
        ({
            url:url,
            secureuri:false,
            fileElementId:imgid,
            dataType: 'json',
            data:{name:'logan', id:'id'},
            success: function (data, status)
            {
                if(typeof(data.error) != 'undefined')
                {
                    if(data.error != '')
                    {
                        var dialog = art.dialog({title:false,fixed: true,padding:0});
                        dialog.time(2).content("<div class='tips'>"+data.error+"</div>");
                    }else{
                        var resp = data.msg;
                        if(resp != '0000'){
                            var dialog = art.dialog({title:false,fixed: true,padding:0});
                            dialog.time(2).content("<div class='tips'>"+data.error+"</div>");
                            return false;
                        }else{
                            $('#'+hiddenid).val(data.imgurl);

                            art.dialog.open("corpImg?img="+data.imgurl,{
                                title: '裁剪头像',
                                width:'580px',
                                height:'400px'
                            });

                            //dialog.time(3).content("<div class='msg-all-succeed'>上传成功！</div>");
                        }




                    }
                }
            },
            error: function (data, status, e)
            {

                dialog.time(3).content("<div class='tips'>"+e+"</div>");
            }
        })

        return false;
    }



</script></body></html>