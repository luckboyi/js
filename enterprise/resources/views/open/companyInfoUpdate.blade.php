@extends('layouts.third')

@section('content')
    <div class="state clearfix">
        {{--<span class='fl first_step'><span class='icon'></span>1 填写资料</span>--}}
        {{--<span class='fl line'>-----------------------------------</span>--}}
        {{--<span class='fl second_step'><span class='icon'></span>2 验证邮箱</span>--}}
        {{--<span class='fl line'>-----------------------------------</span>--}}
        {{--<span class='fl third_step'><span class='icon'></span>3 完成注册</span>--}}
    </div>
    <form class="form-horizontal" role="form"  action="{{ url('updateUserInfo') }}" method="post" name="addForm" id="addForm1" enctype="multipart/form-data">
        <div class='account'>
            <p class='f18 title'>平台账号</p>
            <div>
                <span class='dt'>平台账号：</span>
                <span>{{ $name }}</span>
                <input class='btn' type='button' name='button' value="切换账号" />
            </div>
            <div>
                <span class='dt'>绑定手机：</span>
                @if($bindMobile !== '')
                    <b id="mobile">{{ $bindMobile }}</b>
                @else
                    <a id="bindMobile" href="javascript:;">绑定手机</a>
                @endif
                <span class='prompt'>必须绑定手机的账号才能注册成为开发者</span>
                <input type="hidden" name="uid" id="uid" value="{{ $uid }}" />
                <input type="hidden" name="type" value="2" />
            </div>
        </div>
        <div class='message'>
            <p class='f18 title'>个人信息</p>
            <table>
                <tr>
                    <td>
                        <span>姓名：</span>
                    </td>
                    <td>
                        <input type="text" name="userName" id="userName" placeholder='与身份证上姓名保持一致' value="{{ $data['name'] }}" />
                        <span class='prompt' id="userNameAlert">请规范填写姓名</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>身份证：</span>
                    </td>
                    <td>
                        <input type="text" name="idCard" id="idCard"  placeholder='请填写个人身份证'  value="{{ $data['idcard'] }}"/>
                        <span class='prompt' id="idCardAlert" style="top: 90px;">请填写正确的身份证号</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>电子邮箱：</span>
                    </td>
                    <td>
                        <input type="text" name="email" id="email" placeholder='请填写有效邮箱，用于接收平台审核通知等重要信息'  value="{{ $data['email'] }}"/>
                        <span class='prompt' id="emailAlert" style="top: 145px;">请填写有效邮箱</span>
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
                        <textarea name="address" id="address" placeholder='请填写有效地址，该地址作为联系您和回寄纸质协议等使用'>{{ $data['address1'] }}</textarea>
                        <span class='prompt' id="addressAlert" style="top: 270px;">请填写有效地址</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>手持身份证照片：</span>
                    </td>
                    <td>
                        <div class="pic">
                            <span id="pic-bg"></span>
                            <p id="pic-font">上传图片</p>
                            <img src="{{ $data['url']['credentials'] }}" id="img0" width="100%" height="100%" style="display: none"/>
                        </div>
                        <input  type="file" name="file" id="file" class='pic' alt="点击上传图片" style="display: block;opacity: 0;filter: alpha(opacity=0);cursor: pointer">

                        <div class='mind'>

                            <a href='javascript:;'>上传图片实例></a>
                            <p>1: 2M以内，JPG/PNG格式的图片</p>
                            <p>2: 开发者手持身份证/护照正面进行拍照，要求五官可见，证件信息清晰无遮挡</p>
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
                <input type="submit" id="sureBtn" style="margin:0 26%;cursor: pointer;" name='' value="保存"/>
            </div>
        </div>
    </form>
@endsection
<!-- BEGIN PAGE CONTENT-->

@section('javascript')

    <script type="text/javascript">
        $("#pic-font").hide();
        $("#pic-bg").hide();
        $("#img0").show();
        $("body").on("click", "#sureBtn", function() {
            var authCode = 1;
            var userName = $("#userName ").val(),
                    userId = $("#userId").val(),
                    email = $("#email").val(),
                    address = $("#address").val();
            idCard = $("#idCard").val();
//            if ($.trim(email) == "" && $.trim(idCard) == "" && $.trim(userId) == ""){
//                //pubApi.showDomError($("#addErrorBox"), "非法参数");
//                alert("非法参数");
//                return false;
//            }

            //判断提交参数是否确实或恶意申请
            if($.trim(userName) == '' || userName.length > 60){
                $("#userNameAlert").show();
                setTimeout("$('#userNameAlert').hide()", 5000);
                return false;
            }

            if($.trim(idCard) == '' || !isCardNo(idCard)){
                $("#idCardAlert").show();
                setTimeout("$('#idCardAlert').hide()", 5000);
                return false;
            }

            if($.trim(email) == '' || !email.match(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/)){
                $("#emailAlert").show();
                setTimeout("$('#emailAlert').hide()", 5000);
                return false;
            }

            if($.trim(address) == '' || address.length > 128){
                $("#addressAlert").show();
                setTimeout("$('#addressAlert').hide()", 5000);
                return false;
            }

            //由于可能不修改图片
//            if($("input[type=file]").val()==""){
//                alert("请选择要上传的图片！！");
//                return false;
//            }

            if($(".checked").attr("class") == "checked"){
                alert("请勾选同意XXX协议");
                return false;
            }

            //addForm.submit();



            // 验证身份证
            function isCardNo(card) {
                var pattern = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                return pattern.test(card);
            }

            var options = {
                type:"POST",
                dataType:"json",
                resetForm:true,
                success:function(result){
                    if(result.code !== 0){
                        console.dir(result);
                    }else{
                        alert("信息更新成功");
                    }
                },
                error:function(result){
                    console.dir(result.code);
                    //console.dir(result);
                }
            };
            $("#addForm1").ajaxForm(options).submit(function(){
                return false;
            });

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

        $(function () {

            'use strict';

            var $distpicker = $('#distpicker');

            $distpicker.distpicker({
                province: '福建省',
                city: '厦门市',
                district: '思明区'
            });

            $('#reset').click(function () {
                $distpicker.distpicker('reset');
            });

            $('#reset-deep').click(function () {
                $distpicker.distpicker('reset', true);
            });

            $('#destroy').click(function () {
                $distpicker.distpicker('destroy');
            });

            $('#distpicker3').distpicker({
                province: "{{ $data['province'] }}",
                city: "{{ $data['city'] }}",
                district: '浙江区'
            });

        });
    </script>
    <!--引入地区选择插件-->
    <script type="text/javascript" src="{{static_res('/common/js/distpicker.data.js')}}"></script>
    <script type="text/javascript" src="{{static_res('/common/js/distpicker.js')}}"></script>
    {{--<script type="text/javascript" src="{{static_res('/common/js/main.js')}}"></script>--}}
@endsection