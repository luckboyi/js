@extends('tob.layout')

@section('css')
<style>
.personal table tr.v-align-top{
    vertical-align: top;
}
.personal textarea{
    position: static;
    margin-left: 5px;
}
.personal .pic{
    margin-top: 20px;
}
.personal .preview{
    margin-top:20px;
}
.personal .mind{
    padding-top: 0;
    top:180px;
}
</style>
@endsection

@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <div class="state clearfix">
        <span class='fl first_step'><span class='icon'></span>1 填写资料</span>
        <span class='fl line'>------------------------</span>
        <span class='fl second_step'><span class='icon'></span>2 验证邮箱</span>
        <span class='fl line'>------------------------</span>
        <span class='fl third_step'><span class='icon'></span>3 等待审核</span>
    </div>
    <form id="user-form" >
        <div class='account'>
            <p class='f18 title'>平台账号</p>
            <div>
                <span class='dt'>平台账号：</span>
                <span>{{ $user["account"] }}</span>
            </div>
            <div>
                <span class='dt'>绑定手机：</span>
                @if($bindMobile !== '')
                    <b id="bindMobile">{{ $bindMobile }}</b>
                @else
                    <a id="bindMobile1" href="javascript:;">绑定手机</a>
                @endif
            </div>
        </div>
        <div class='message'>
            <p class='f18 title'>体验店信息</p>
            <table>
                <tr>
                    <td>
                        <span>体验店名称：</span>
                    </td>
                    <td>
                        <input type="text" name="merchantName" id="merchantName"  placeholder='请填写体验店名称' value="{{ $merchant }}"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>营业执照注册号：</span>
                    </td>
                    <td>
                        <input type="text" name="license" id="license"  placeholder='请填写营业执照注册号,即统一社会信用代码' value="{{ $license }}" /></td>
                </tr>
                <tr>
                    <td>
                        <span>体验店联系人：</span>
                    </td>
                    <td>
                        <input type="text" name="connector" id="connector"  placeholder='请填写联系人姓名' value="{{ $contact }}" /></td>
                </tr>
                <tr>
                    <td>
                        <span>联系人身份证：</span>
                    </td>
                    <td>
                        <input type="text" name="idCard" id="idCard"   placeholder='请填写个人身份证' value="{{ $idcard }}"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>联系人手机号：</span>
                    </td>
                    <td>
                        <input type="text" name="mobile" id="mobile"   placeholder='请填写联系人手机号' value="{{ $tel }}"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>电子邮箱：</span>
                    </td>
                    <td>
                        <input type="text" name="email" id="email" placeholder='请填写有效邮箱，用于接收平台审核通知等重要信息'  value="{{ $email }}" />
                    </td>
                </tr>
                <tr class="v-align-top">
                    <td>
                        <span>体验店地址：</span>
                    </td>
                    <td id="distpicker3" style="padding: 20px 0;">
                        <select class="input-medium m-wrap sel1" tabindex="1" name="province" id="province10"></select>
                        <select class="input-medium m-wrap sel2" tabindex="1" name="city" id="city10"></select>
                        <textarea name="address" id="address" placeholder='请填写有效地址，该地址作为联系您和回寄纸质协议等使用'>{{ $address }}</textarea>
                    </td>
                </tr>
                <tr class="v-align-top" style="height: 380px">
                    <td>
                        <span>手持身份证照片：</span>
                    </td>
                    <td id="idcard_container">
                        <div class="pic" id="idcard_browser" >
                            <span id="pic-bg"></span>
                            <p >上传图片</p>
                        </div>
                        {!! $idCardPreview  !!}
                        <div class='mind'>
                            <a href="{{ static_res('/open/images/card.jpg') }}"" target="_blank">上传图片实例></a>
                            <p>1: 2M以内，JPG格式的图片</p>
                            <p>2: 开发者手持身份证/护照正面进行拍照，要求五官可见，证件信息清晰无遮挡</p>
                            <p>3: 目前我们只针对境外开发者开放使用本人出镜手持护照截图，如您属于中国大陆国籍
                                的必须使用有效期内的本人出镜手持身份证截图</p>
                            <p>4: 我司资质验证系统已与工商部门联网，请勿提供虚假证件</p>
                        </div>
                    </td>
                </tr>
                <tr class="v-align-top">
                    <td>
                        <span>上传营业执照：</span>
                    </td>
                    <td id="idcard_container2">
                        <div class="pic" id="idcard_browser2">
                            <span id="pic-bg"></span>
                            <p>上传图片</p>
                        </div>
                        {!! $licensePreview  !!}
                        <div class='mind'>
                             <a href="{{ static_res('/open/images/companycard.jpg') }}" target="_blank">上传图片实例&gt;</a>
                            <p>1: 2M以内，JPG格式的图片</p>
                            <p>2: 请上传企业营业执照原件,或者复印件加盖公司印章</p>
                            <p>2: 我司资质验证系统已与工商部门联网，请勿提供虚假证件</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class='sub'>
            <div class='accept'>
                 <span><input type="checkbox" id="deal" name='deal'  {{-- {!! $deal !!} --}} /></span>
                <span>同意接受<a href="//open.vronline.com/agreement" target="_blank">VRonline开发者协议</a></span>
            </div>
            <div class='sub_btn'>
               <input type="submit"  name='' style="cursor: pointer;" value="{{$edit?"保存":"下一步"}}"/>
               @if($edit)
                <input type="button" id="toBack" style="cursor: pointer;" value="返回"/>
               @endif
            </div>
        </div>
    </form>
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
                {{--<li class="fl sure">确定</li>--}}
                <li class="fl cancel" >取消</li>
            </ul>
        </div>
    </div>
@endsection
<!-- BEGIN PAGE CONTENT-->

@section('javascript')
<script src="{{ static_res('/open/assets/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ static_res('/open/assets/jquery-validation/additional-methods.min.js') }}"></script>
<script src="{{ static_res('/open/assets/jquery-validation/messages_zh.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/base/md5.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/open/assets/distpicker/distpicker.min.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/common/js/tips.js') }}"></script>
<script type="text/javascript">

var validator = $("#user-form").validate({
    rules: {
        merchantName: {
            chinacompany: true,
        },
        license:{
            socialcode: true,
        },
        idCard: {
            chinaidcard: true,
        },
        connector: {
            chinaname: true,
        },
        email:{
            required:true,
            cemail: true
        },
        mobile:{
            chinaphone:true
        }
    },
    messages:{
        email:{
            required:"请输入有效的电子邮件地址"
        },
        merchantName:"请填写您的公司名称",
        connector:"请填写联系人姓名"
    },
    submitHandler: function(form) {
        var merchantName = $("#merchantName").val(),
        mobile = $("#mobile").val(),
        connector=$("#connector").val(),
        idCard = $("#idCard").val(),
        email = $("#email").val(),
        address = $("#address").val(),
        province = $("#province10").val(),
        city = $("#city10").val(),
        license=$("#license").val(),
        bindMobile = $("#bindMobile").text();

        if($.trim(bindMobile) == '' || bindMobile.length !== 11){
            Open.showMessage("必须绑定手机的账号才能注册成为开发者！");
            return false;
        }

        if($.trim(province) == '' || $.trim(city) == ''){
                Open.showMessage("请选择省份,城市！");
                return false;
        }
        if($.trim(address) == '' || address.length > 128){
                Open.showMessage("请填写有效地址！");
                return false;
        }
        if($(".preview-idcard img").length<1) {
             Open.showMessage("请上传手持身份证照片");
            return false;
        }
        if($(".preview-license img").length<1) {
             Open.showMessage("请上传营业执照照片");
            return false;
        }
        if($("#deal").prop("checked") != true){
            Open.showMessage("请勾选同意VROnline协议");
            return false;
        }

        var data = {merchantName:merchantName,idCard:idCard,email:email,province:province,city:city,address:address,license:license,mobile:mobile,connector:connector};
        $.post("/apply",data,function(data) {
            if(data.code==0) {
                Open.showMessage("保存成功");
                location.href = "/validateEmail";
            } else {
                Open.showMessage(data.msg);
            }
        },"json")

        return false;
    }
});

var stat;
var err;
//绑定手机号码的逻辑
$(function(){
    if(stat==1 || stat==5) {
         err = "审核中,不能修改"
    }
    var idcardUploader = new loiUploadContainer({
        container:"idcard_container",
        choose:"idcard_browser",
        ext:"jpg,png",
        upload:{tp:"tob_idcard",err:err,success:function(json){
            var jsonResult ;
            if(typeof(json)=="string") {
                jsonResult = $.parseJSON(json);
             } else {
                jsonResult = json
             }

            if(jsonResult.code!=0) {
                 Open.showMessage(jsonResult.msg)
            } else {
                var access_url =  jsonResult.data.access_url;
                var previewObj = $(".preview-idcard");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'?v='+Open.randVersion()+'" width="100%" height="100%"/></a>');
                }
                previewObj.show();
            }
        },error:function(a,msg){
            if(typeof(msg)!="undefined") {
                     Open.showMessage(msg)
             } else {
                 Open.showMessage("系统错误")
             }
        }},
        filesAdd:function(){}
    });

    var idcardUploader2 = new loiUploadContainer({
        container:"idcard_container2",
        choose:"idcard_browser2",
        ext:"jpg,png",
        upload:{tp:"tob_license",err:err,success:function(json){
            var jsonResult ;
            if(typeof(json)=="string") {
                jsonResult = $.parseJSON(json);
             } else {
                jsonResult = json
             }
            if(jsonResult.code!=0) {
                 Open.showMessage(jsonResult.msg)
            } else {
                var access_url =  jsonResult.data.access_url;
                var previewObj = $(".preview-license");
                if(previewObj.length>0) {
                    previewObj.html('<a href="'+access_url+'" target="_blank"><img src="'+access_url+'?v='+Open.randVersion()+'" width="100%" height="100%"/></a>');
                }
                previewObj.show();
            }
        },error:function(a,msg){
            if(typeof(msg)!="undefined") {
                     Open.showMessage(msg)
             } else {
                 Open.showMessage("系统错误")
             }
        }},
        filesAdd:function(){}
    });

    $('#distpicker3').distpicker({
        province: '{{ $province }}',
        city: '{{ $city }}'
    });

    var bindMobileStat = 0;
    $('.bind_container').find('input[name="phoneNun"]').focus();
    //点击确定
    $('.bindBtn').on('click',function(){
        var checkMobile = check_bind_num($("#bindMobileOpen"));
        if(bindMobileStat == 1) {
            var phoneNum = $("#bindMobileOpen").val();
            var code = $("#bindCodeOpen").val();
            verifyBindCodeOpen(phoneNum, code, 1212,function(callback) {
                Open.showMessage("绑定成功",1000,callback)
            });
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

    var countdown = 60;
    function setTime(obj){
        //alert(1)
        if(countdown === 0){
            obj.text("重新发送");
            obj.css({'pointer-events':'auto','background':'#22a1bf','border-color':'#22a1bf'});
            countdown = 60;
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

$("#toBack").click(function(){
    location.href = "/validateEmail";
});
</script>

@endsection
