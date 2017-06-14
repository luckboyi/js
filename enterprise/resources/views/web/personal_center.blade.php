<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <title>个人中心</title>
    <link rel="stylesheet" href="http://pic.vronline.com/common/style/base.css">
    <link rel="stylesheet" href="http://pic.vronline.com/guanwang/style/personal_center.css">
    <link rel="stylesheet" href="http://pic.vronline.com/assets/jcrop/jquery.Jcrop.min.css">
    <script language="JavaScript" src="http://pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
    <script language="JavaScript" src="http://pic.vronline.com/assets/jcrop/jquery.Jcrop.min.js"></script>
    <script language="JavaScript" src="http://pic.vronline.com/open/assets/upload/faceUpload.js"></script>
    <!-- 公共js库 -->
    <script language="JavaScript" src="http://pic.vronline.com/common/js/tips.js"></script>
    <style type="text/css">
    /*a  upload */
    .a-upload {
        padding: 4px 10px;
        height: 30px;
        line-height: 20px;
        position: relative;
        cursor: pointer;
        color: #888;
        background: #fafafa;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        display: inline-block;
        *display: inline;
        *zoom: 1
    }

    .a-upload  input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
        filter: alpha(opacity=0);
        cursor: pointer
    }

    .a-upload:hover {
        color: #444;
        background: #eee;
        border-color: #ccc;
        text-decoration: none
    }



    </style>
</head>
<body>
    <div class="personal_center clearfix">
        <div class="left_per fl">
            <ul>
                <li class="pr cur userMsg"><a href="javascript:;">用户资料</a></li>
                <li class="pr charge">
                    <i></i>
                    <a href="javascript:;">充值中心</a>
                    <ol class="" style="display: none;">
                        <a href="{{url("pay")}}"><li channel="alipay">支付宝</li></a>
                        <a href="{{url("pay?channel=wxpay")}}"><li channel="wxpay">微信支付</li></a>
                    </ol>

                </li>
                <li class="pr problem"><a href="{{url("qa")}}">常见问题</a></li>
            </ul>
        </div>
        <div class="right_per">
            <ul class="in_right_per">
                <!--用户资料S-->
                <li class="list_con user_msg cur">
                    <div class="user_header">
                        <p class="pr img_con">
                            <img src="http://pic.vronline.com/guanwang/images/default.png" id="head_photo_src">
                            <input type="hidden" name="photo_pic" id="photo_pic" value="">
                            <i class="edit pa"></i>
                            <a href="javascript:;" class="a-upload" id="upload-photo-con" style="display:block;width:120px;height:120px;position:absolute; left:402px; top:14px; opacity:0;">
                                <input type="file" name="head_photo" id="head_photo" accept="image/png, image/jpeg" >
                            </a>
                        </p>
                        <p class="title" id="title">
                            @if ($userinfo['account'])
                                {{ $userinfo['account'] }}
                            @else
                                游客
                            @endif
                        </p>
                        <div class="clearfix submit_msg pr">

                            @if (isset($userinfo['last_month']) && isset($userinfo['last_time']) && isset($userinfo['country']))
                                <i></i>
                                <p class="fl">最近一次登录:</p>
                                <span class="fl month">{{ $userinfo['last_month'] }}</span>
                                <span class="fl time">{{ $userinfo['last_time'] }}</span>
                                <span class="fl country">{{ $userinfo['country']}}</span>
                            @endif

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
                                <h4 class="icon pr"><i id="signMobile"></i>绑定手机：<p class="tips pa phone_tips" id="signMobile2">检测到您的账号还没有绑定手机，为了提高账号安全和密码找回，请及时进行手机绑定！</p></h4>
                                <p class="clearfix">
                                    <span class="fl" id="bindMobile" for="">
                                         @if ($userinfo['bindmobile'])
                                            {{ $userinfo['bindmobile'] }}
                                        @else
                                            你的账户未绑定手机
                                        @endif
                                    </span>

                                    <!-- @if (!$userinfo['bindmobile']) -->
                                    <span class="fr phone_btn" id="btnBindMobile" style="display:none">绑定手机</span>
                                   <!--  @endif -->
                                    <b class="fr unbind clearfix" id="mobileAction" style="display:none">
                                        <!-- <i class="fl modify" id="modify_phone" style="display:none">修改</i> -->
                                        <i class="fl unbind_btn" id="unbind_phone">解绑</i>
                                    </b>


                                </p>
                            </li>
                            <li class="fl">
                                <h4>密码修改：</h4>
                                <p class="clearfix">
                                    <span class="fl">不定期修改密码 增加账号安全</span>
                                    <span class="fr revamp_paw_btn" id="btnModity">修改</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--用户资料E-->
            </ul>

        </div>
    </div>

    <!--绑定手机号S-->
    <div class="bind_phoneNum mask_layer" id="mobileDiv"  style="display: none;">
        <div class="popup_window  ">
            <div class="in_popup">
                <p class="input_phone">
                    <label>
                        <span>输入手机号</span>
                        <input type="text" id="txtMobile">
                    </label>
                    </p><p class="erroColor erro_phone_msg" id="mobileFlag"></p>
                <p></p>
                <p class="message">
                    <label>
                        <span>短信验证码</span>
                        <input type="text" id="verNumber" name="verNumber" class="itxt">
                    </label>
                    <input class="get_code" id="setVetify" type="text" value="获取验证码" sytle="width:90px;"></span>
                </p>
                <p class="system_tip">系统将会以短信方式发送至需要绑定的手机</p>
                <!-- <p style="display:none;margin-top:20px;line-height:20px;" id="showText">校验码已发出，请注意查收短信，如果没有收到，<br/>你可以在<span id="showTime" style="color:#f00;font-size:20px;"></span>秒后要求系统重新发送
                        </p> -->
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

    <!--解绑手机号S-->
    <div class="bind_phoneNum mask_layer" id="unBindMobileDiv"  style="display: none;">
        <div class="popup_window  ">
            <div class="in_popup">
                <p class="input_phone tal" >
                    <label>
                        <span style="margin-left:114px;">您的手机号</span>
                        <label id="labMobile"></label>
                    </label>

                <p></p>
                <p class="message">
                    <label>
                        <span>短信验证码</span>
                        <input type="text" id="unVerNumber" name="unVerNumber" class="itxt">
                    </label>
                    <input class="get_code" id="unSetVetify" type="text" value="获取验证码" sytle="width:90px;"></span>
                </p>
                <p class="system_tip">系统将会以短信方式发送至需要绑定的手机</p>
                <!-- <p style="display:none;margin-top:20px;line-height:20px;" id="showText">校验码已发出，请注意查收短信，如果没有收到，<br/>你可以在<span id="showTime" style="color:#f00;font-size:20px;"></span>秒后要求系统重新发送
                        </p> -->
            </div>
            <div class="popup_btn">
                <ul class="clearfix">
                    <li class="fl sure" id="unbindPhone">确定</li>
                    <li class="fl cancel">取消</li>
                </ul>
            </div>
        </div>
    </div>
    <!--解绑手机号E-->


    <!--修改密码-->
    <div class="revamp_password mask_layer ">
        <div class="popup_window">
            <div class="in_popup">
                <p class="">
                    <label>
                        <span>原密码</span>
                        <input type="password" id="oldPwd">
                    </label>
                    </p><p class="erroColor erro_phone_msg" id="pwdMsg1" flag="1"></p>
                <p></p>
                 <p>
                    <label>
                        <span>新密码</span>
                        <input type="password" id="newPwd">
                        </label></p><p class="erroColor erro_phone_msg" id="pwdMsg2" flag="1"></p>

                <p></p>
                <p>
                     <label>
                        <span>确认密码</span>
                        <input type="password" id="newPwdConfirm">
                        </label></p><p class="erroColor erro_phone_msg" id="pwdMsg3" flag="1"></p>

                <p></p>
            </div>
            <div class="popup_btn">
                <ul class="clearfix">
                    <li class="fl sure" id="btnModifyOK">确定</li>
                    <li class="fl cancel">取消</li>
                </ul>
            </div>
        </div>
    </div>
    <!--手机绑定成功-->
    <div class="mask_layer success_popup_window" id="bindMobileOk">
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
                </p><p class="erroColor erro_phone_msg"><lable id="openLbl1" flag="1"></lable></p>
                <p></p>
                <p>
                    <label>
                        <span>新密码</span>
                        <input id="openPwd" type="password">
                    </label>
                </p><p class="erroColor erro_phone_msg"><lable id="openLbl2"  flag="1"></lable></p>
                <p></p>
                <p>
                    <label>
                        <span>确认密码</span>
                        <input  id="openPwdConfirm" type="password">
                    </label>
                </p><p class="erroColor erro_phone_msg"><lable id="openLbl3"  flag="1"></lable></p>
                <p></p>
            </div>
            <div class="popup_btn">
                <ul class="clearfix">
                    <li class="fl sure" id="btnAccount">修改</li>
                    <li class="fl cancel">取消</li>
                </ul>
                <p class="erroColor erro_phone_msg"></p>
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
                    <li class="fl" id="accountBack">返回</li>
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
    <script language="JavaScript" src="http://pic.vronline.com/guanwang/js/person_center.js?20170315"></script>
    <script language="JavaScript" src="http://pic.vronline.com/common/js/datacenter_stat.js"></script>
<script type="text/javascript">
// init
    $(document).ready(function(){
        // 判断用户图象有没有
        var face = "{{ $userinfo['faceUrl']}}";
        if (face != '') {
            $("#head_photo_src").attr('src',face);
        };

        // 绑定账号
        var userAccount = "{{ $userinfo['account']}}";
        if (!userAccount) {
            // 修改密码不可点
            $("#btnModity").addClass('disabled');
        }else{
            // 修改密码可点
            $("#btnModity").removeClass('disabled');
        }

        // 绑定手机
        var userMobile = "{{ $userinfo['bindmobile'] }}";
        if (!userMobile) {  // 如果没有值，则显示绑定手机号码
            $("#btnBindMobile").show();
            $("#bindmobile").attr('for','');
            $("#bindMobile").text('你的账户未绑定手机');

            // 绑定手机提示显示
            $("#signMobile").show();
        }else{  // 如果有值，则显示修改和解绑按钮
            $("#mobileAction").show();
            $("#bindMobile").attr('for',userMobile);
            $("#bindMobile").html(userMobile.substring(0,3)+"****"+userMobile.substring(7,11));
            $("#btnBindMobile").hide();
            $("#bindmobile").text(userMobile);

            // 绑定手机提示不显示
            $("#signMobile").hide();
        }
        $(document).on("change","#head_photo",function() {
            var files =  $(this).prop("files")
            if(files.length>=1) {
                var file = files[0]
                var crop = new Crop(file);
                crop.show(function(json){
                    $('#head_photo').replaceWith('<input type="file" name="head_photo" id="head_photo">');
                    var jsonResult = $.parseJSON(json);
                    $(".img_con img").attr("src",jsonResult.data.access_url+"?v="+Math.random());
                    $.get('web/modifyPic',function(){});
                    // if(t.choose.h<60 || t.choose.w<60) {
                    //     console.log("no crop");
                    // }
                },function(errorObj) {
                        var config = {
                    headerMsg: "错误",
                    msg: errorObj.msg,btnState:1,
                    model: "tips"
                }

                if (typeof obj == "object") {
                    config = $.extend({}, config, obj);
                }

                tipsFn.init(config);
                })
            }
        });
    });
</script>
</body>
</html>
