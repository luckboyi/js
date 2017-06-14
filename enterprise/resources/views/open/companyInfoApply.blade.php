@extends('layouts.third')

@section('content')
    <div class="state clearfix">
        <span class='fl first_step'><span class='icon'></span>1 填写资料</span>
        <span class='fl line'>-----------------------------------</span>
        <span class='fl second_step'><span class='icon'></span>2 验证邮箱</span>
        <span class='fl line'>-----------------------------------</span>
        <span class='fl third_step'><span class='icon'></span>3 完成注册</span>
    </div>
    <form class="form-horizontal" role="form"  action="{{ url('uploadUserInfo') }}" method="post" name="addForm" id="addForm1" enctype="multipart/form-data">
        <div class='account'>
            <p class='f18 title'>平台账号</p>
            <div>
                <span class='dt'>平台账号：</span>
                <span>{{ $name }}</span>
                <input class='btn' type='button' name='button' onclick="window.location.href='{{url('logout')}}'" value="切换账号" />
            </div>
            <div>
                <span class='dt'>绑定手机：</span>
                @if($bindMobile !== '')
                    <b id="mobile">{{ $bindMobile }}</b>
                @else
                    <a id="bindMobile1" href="javascript:;">绑定手机</a>
                @endif
                {{--<span class='prompt' id="ifBindMobile">必须绑定手机的账号才能注册成为开发者</span>--}}
                <input type="hidden" name="uid" id="uid" value="{{ $uid }}" />
                <input type="hidden" name="type" value="1" />
            </div>
        </div>
        <div class='message'>
            <p class='f18 title'>公司信息</p>
            <table>
                <tr>
                    <td>
                        <span>公司名称：</span>
                    </td>
                    <td>
                        <input type="text" name="userName" id="userName" placeholder='与营业执照上保持一致' />
                        {{--<span class='prompt' id="userNameAlert">请规范填写公司名称</span>--}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>营业执照注册号：</span>
                    </td>
                    <td>
                        <input type="text" name="idCard" id="idCard"  placeholder='请填写营业执照注册号' />
                        {{--<span class='prompt' id="idCardAlert" style="top: 90px;">请填写正确的营业执照注册号</span>--}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>联系人：</span>
                    </td>
                    <td>
                        <input type="text" name="connector" id="connector"  placeholder='请填写联系人' />
                        {{--<span class='prompt' id="connectorAlert" style="top: 145px;">请填写联系人</span>--}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>电子邮箱：</span>
                    </td>
                    <td>
                        <input type="text" name="email" id="email" placeholder='请填写有效邮箱，用于接收平台审核通知等重要信息' />
                        {{--<span class='prompt' id="emailAlert" style="top: 200px;">请填写有效邮箱</span>--}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>联系地址：</span>
                    </td>
                    <td id="distpicker3">
                        <select class="input-medium m-wrap sel1" tabindex="1" name="province" id="province10"></select>
                        <select class="input-medium m-wrap sel2" tabindex="1" name="city" id="city10"></select>
                        {{--<select class="input-medium m-wrap sel3" tabindex="1" name="district" id="district10"></select>--}}
                        <textarea name="address" id="address" placeholder='请填写有效地址，该地址作为联系您和回寄纸质协议等使用'></textarea>
                        {{--<span class='prompt' id="addressAlert" style="top: 330px;">请填写有效地址</span>--}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>上传营业执照：</span>
                    </td>
                    <td>
                        <div class="pic">
                            <span id="pic-bg"></span>
                            <p id="pic-font">上传图片</p>
                            <img src="" id="img0" width="100%" height="100%" style="display: none"/>
                        </div>
                        <input  type="file" name="file" id="file" class='pic' alt="点击上传图片" style="display: block;opacity: 0;filter: alpha(opacity=0);cursor: pointer">

                        <div class='mind'>

                            <a href='javascript:;'>上传图片实例></a>
                            <p>1: 2M以内，JPG/PNG格式的图片</p>
                            <p>2: 我司资质验证系统已与工商部门联网，请勿提供虚假证件</p>
                            <p>3: 目前我们只针对境外开发者开放使用本人出镜手持护照截图，如您属于中国大陆国籍
                                的必须使用有效期内的本人出镜手持身份证截图</p>
                            <p>4: 我司资质验证系统已与工商部门联网，请勿提供虚假证件</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class='sub'>
            <div class='accept'>
                <input type="checkbox" id='checkbox' />
                <span class='checked' id='checkboxSure'></span>
                <span>同意接受<a href="javascript:;">XXXXXXXX开发者协议</a></span>
            </div>
            <div class='sub_btn'>
                {{--<input type="submit" name='' value="上一步"/>--}}
                <input type="button" id="sureBtn" style="margin:0 26%;cursor: pointer;" name='' value="下一步"/>
            </div>
        </div>
    </form>
    <!--绑定手机的弹窗-->
    <div class="bind_container tac " id="bindWindows">
        <div class="in_bind_container">
            <div class="close_btn"></div>
            <div class="in_con_text">
                <p class="bind_phone_num pr">
                    <span >绑定手机号：</span>
                    <input type="text" id="bindMobileOpen" name="phoneNun">
                    <span class="errorColor pa">xxxx</span>
                </p>
                <p class="send_code pr">
                    <span>输入验证码：</span>
                    <input type="text" name="codeNum" id="bindCodeOpen">
                    <span class="unbind_send_mes"  style="" id="send_code">发送验证码</span>
                    <span class="errorColor pa">xxxx</span>
                </p>
            </div>
            <ul class="unbind_btn tac clearfix" >
                <li class="fl sure bindBtn">确定</li>
                <li class="fl cancel" >取消</li>
            </ul>
        </div>
    </div>
    <div class="bind_container tac" id="bindAlert">
        <div class="in_bind_container">
            <div class="close_btn"></div>
            <div class="in_con_text">
                <p class="unbind_tipsCon">xxxxx</p>
            </div>
            <ul class="unbind_btn tac clearfix" >
                <li class="fl sure">确定</li>
                <li class="fl cancel" >取消</li>
            </ul>
        </div>
    </div>
@endsection
<!-- BEGIN PAGE CONTENT-->

@section('javascript')

    <script type="text/javascript">
        //判断提交参数是否确实或恶意申请
        var mobile = $("#mobile").text();
        if($.trim(mobile) == '' || mobile.length !== 11){
            //Open.showMessage("必须绑定手机的账号才能注册成为开发者！", 1000000);
        }
        $("body").on("click", "#sureBtn", function() {
            var authCode = 1;
            var userName = $("#userName ").val(),
                    userId = $("#userId").val(),
                    connector = $("#connector").val(),
                    email = $("#email").val(),
                    address = $("#address").val(),
                    idCard = $("#idCard").val();

            //判断提交参数是否确实或恶意申请
            if($.trim(mobile) == '' || mobile.length !== 11){
                Open.showMessage("必须绑定手机的账号才能注册成为开发者！");
                return false;
            }
            //判断提交参数是否确实或恶意申请
            if($.trim(userName) == '' || userName.length > 60){
                Open.showMessage("请规范填写公司名称！");
                return false;
            }

            if($.trim(idCard) == ''){
                Open.showMessage("请填写正确的营业执照号码！");
                return false;
            }

            if($.trim(connector) == '' || connector.length > 128){
                Open.showMessage("请填写正确的企业联系人！");
                return false;
            }

            if($.trim(email) == '' || !email.match(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/)){
                Open.showMessage("请填写有效邮箱！");
                return false;
            }

            if($.trim(address) == '' || address.length > 128){
                Open.showMessage("请填写有效地址！");
                return false;
            }

            if($("input[type=file]").val()==""){
                Open.showMessage("请选择要上传营业执照图片！");
                return false;
            }

            if($(".checked").attr("class") == "checked"){
                Open.showMessage("请勾选同意VROnline协议");
                return false;
            }

            addForm.submit();



            // 验证身份证
            function isCardNo(card) {
                var pattern = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                return pattern.test(card);
            }

//            var options = {
//                type:"POST",
//                dataType:"json",
//                resetForm:true,
//                success:function(result){
//                    if(result.code !== 0){
//                        console.dir(result);
//                    }else{
//                        alert(121);
//                    }
//                },
//                error:function(result){
//                    console.dir(result.code);
//                    //console.dir(result);
//                }
//            };
//            $("#addForm1").ajaxForm(options).submit(function(){
//                return false;
//            });

            // 绑定表单提交事件处理器
        });



        $("#file").change(function(){
            $("#pic-font").hide();
            $("#pic-bg").hide();
            $("#img0").show();
            var objUrl = getObjectURL(this.files[0]) ;
            console.log("objUrl = "+objUrl) ;
            if (objUrl) {
                $("#img0").attr("src", objUrl) ;
            }
        }) ;
        //建立一個可存取到該file的url
        function getObjectURL(file) {
            var url = null ;
            if (window.createObjectURL!=undefined) { // basic
                url = window.createObjectURL(file) ;
            } else if (window.URL!=undefined) { // mozilla(firefox)
                url = window.URL.createObjectURL(file) ;
            } else if (window.webkitURL!=undefined) { // webkit or chrome
                url = window.webkitURL.createObjectURL(file) ;
            }
            return url ;
        }

        //修改绑定
        //绑定手机弹窗
//        $('#bindMobile1').on('click',function() {
//            var phonemsg = $("#bindMobile").text();
//            tipsFn.init({
//                model:'bindPhoneNum',
//                headerMsg:'',
//                msg:'',
//                msg2:'',
//                errmsg:'',
//                btnState:0,
//            })
//        });
        //绑定手机号码的逻辑
        $(function(){
            var bindMobileStat = 0;
            $('.bind_container').find('input[name="phoneNun"]').focus();
            //点击确定
            $('.bindBtn').on('click',function(){
                var checkMobile = check_bind_num($("#bindMobileOpen"));
                if(bindMobileStat == 1) {
                    var phoneNum = $("#bindMobileOpen").val();
                    var code = $("#bindCodeOpen").val();
                    verifyBindCodeOpen(phoneNum, code, 1212);
                }
            });
            //点击取消
            $('.cancel').on('click',function(){
                $(this).parents('.bind_container').hide();
            });
            //点击关闭
            $('.close_btn').on('click',function(){
                $(this).parents('.bind_container').hide();
            });

            //点击显示绑定手机框
            $('#bindMobile1').on('click',function() {
                $("#bindWindows").show();
            });
            //点击倒计时
            $('#send_code').on('click',function(){
                // 发送短信验证码
                var checkMobile = check_bind_num($("#bindMobileOpen"));
                if(bindMobileStat == 1){
                    var phoneNumber = $("#bindMobileOpen").val();
                    sendMobileMsgOpen(phoneNumber);
                    setTime($(this))
                }
            });



            //检测
            $('.bind_container input').each(function(){
                $(this).blur(function(){
                    check_bind_num($(this));
                });
            });
            function check_bind_num(obj){
                var name = obj.attr('name');
                var value = obj.val();

                var rs = /^(13|14|15|17|18)[0-9]{9}$/;
                if(name == 'phoneNun'){
                    if (typeof (value) == 'undefined' || value == "") {
                        obj.next('.errorColor').show().html('不能为空');
                    }else if(!rs.test(value)){
                        obj.next('.errorColor').show().html('请输入正确的手机号！');
                    }else{
                        obj.next('.errorColor').hide().html('');
                        bindMobileStat = 1;
                    }
                };
            }

            var countdown = 10;
            function setTime(obj){
                //alert(1)
                if(countdown === 0){
                    obj.text("重新发送");
                    obj.css({'pointer-events':'auto','background':'#22a1bf','border-color':'#22a1bf'});
                    countdown = 10;
                    //clearTimeout(timer)
                }else{
                    //val.attr('disabled',true);
                    obj.css({'pointer-events':'none','background':'#384455','border-color':'#384455'});
                    obj.text("重新发送("+countdown+")");
                    countdown--;
                    setTimeout(function(){
                        setTime(obj)
                    },1000);
                };
            };


        })
    </script>
    <!--引入地区选择插件-->
    <script type="text/javascript" src="{{static_res('/common/js/distpicker.data.js')}}"></script>
    <script type="text/javascript" src="{{static_res('/common/js/distpicker.js')}}"></script>
    <script type="text/javascript" src="{{static_res('/common/js/main.js')}}"></script>
    <!--引入绑定手机号的弹出层-->
    <script language="JavaScript" src="{{static_res('/common/js/tips.js')}}"></script>
@endsection