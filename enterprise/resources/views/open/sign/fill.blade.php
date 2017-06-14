@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection



@section('content')

<div class="ui  container" style="width:1000px">
    <div class="ui ordered steps developer">
        <div class="active step">
            <div class="content">
                <div class="title">填写资料</div>
                <div class="description">Fill information</div>
            </div>
        </div>
        <div class="active step">
            <div class="content">
                <div class="title">验证邮箱</div>
                <div class="description">Verify email</div>
            </div>
        </div>
        <div class="active step">
            <div class="content">
                <div class="title">等待审核</div>
                <div class="description">Waiting review</div>
            </div>
        </div>
    </div>

<h4 class="ui top attached block header">
    @if($tp=="user") 个人开发者 @else 企业开发者 @endif
</h4>
  <div class="ui bottom attached segment">
<form class="ui form">
  <table class="ui very basic user table">
  <tbody>
    <tr>
      <td style="width:20%;">用户ID</td>
      <td>{{ $user['uid'] }}</td>
      <td style="width:20%;"></td>
    </tr>
    <tr>
      <td>手机号</td>
       <td>@if(!$mobile) 还没有绑定手机，<a href="https://www.vronline.com/profile" target="blank">去绑定手机</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:location.reload()" >已经绑定?</a> @else {{ $mobile }} @endif</td>
      <td></td>
    </tr>
    @if($tp=="user")
     <tr>
      <td>姓名</td>
       <td><div class="field"><input type="text" id="name" placeholder="请输入开发者的姓名"></div></td>
      <td>*</td>
    </tr>
    <tr>
      <td>身份证</td>
       <td><div class="field"><input type="text" id="idcard" placeholder="请输入开发者的身份证号码" maxlength="18"></div></td>
      <td>*</td>
    </tr>
    @else
    <tr>
      <td>公司名称</td>
      <td><div class="field"><input type="text" id="name" placeholder="请输入公司名称"></div></td>
      <td>*</td>
    </tr>
    <tr>
      <td>营业执照注册号</td>
      <td><div class="field"><input type="text"   id="idcard" placeholder="请输入公司营业执照注册号"></div></td>
      <td>*</td>
    </tr>
    <tr>
      <td>联系人</td>
       <td><div class="field"><input type="text"  id="contacts" placeholder="请输入联系人姓名"></div></td>
      <td>*</td>
    </tr>
    @endif
    <tr>
      <td>电子邮箱</td>
       <td><div class="field"><input type="text" id="email" placeholder="请输入开发者的邮箱"></div></td>
      <td>*</td>
    </tr>
     <tr>
      <td>联系地址</td>
      <td><div class="field"><input type="text" id="address" placeholder="请输入开发者的联系地址"></div></td>
      <td>*</td>
    </tr>
    <tr>
      <td> @if($tp=="user") 手执身份证照片 @else 营业执照正面 @endif</td>
      <td>
            <div class="inline fields">
                <div class="field">
                    <div class="ui segment">
                        <img id="idcard_pic" class="preview ui small image">
                    </div>
                </div>
                <div class="field" id="idcard_pic_container">
                    <button class="ui teal  button" id="idcard_pic_browser">选择</button>
                </div>
            </div>
      </td>
      <td>* (2M 以内 JPG,PNG) </td>
    </tr>

  </tbody>
</table>
</form>

</div>
<center>
  <div class="ui blue button action-save">下一步</div>
</center>


@endsection
@section('javascript')
<script type="text/javascript">
var loi = new loiForm();
var idcard_pic_obj;
var tp = "{{ $tp }}";
var mobile =  "{{ $mobile }}";
var loiVerify = new loiValidator();
$(function(){
   idcard_pic_obj = new loiUploadContainer({
    id:"idcard_pic",
    upload:{
        tp:"openuser",addParams:{"assign":"idcard"},
        error:function(){}
    }
  });
  loi.edit("sign",{tp:tp},function(data){

  });
  $(".action-save").click(function() {
    if(mobile.length<1) {
      loiMsg("请绑定手机");
      return false;
    }
    var editData = loi.save();
    if(tp=="user") {
      var nameCk = loiVerify.check("nameCN",editData.name.val);
      if(nameCk==false) {
        loiMsg(loiVerify.errorMsg());
        return false;
      }
       var idcardCk = loiVerify.check("idcard",editData.idcard.val);
      if(idcardCk==false) {
        loiMsg(loiVerify.errorMsg())
         return false;
      }
    } else {
      if(editData.name.val.length<4 || editData.name.val.length>40) {
        loiMsg("请输入正确的公司名称")
         return false;
      }
      if(editData.idcard.val.length<5 || editData.idcard.val.length>30) {
        loiMsg("请输入正确的营业执照")
         return false;
      }
      var nameCk = loiVerify.check("nameCN",editData.contacts.val);
      if(nameCk==false) {
        loiMsg(loiVerify.errorMsg());
        return false;
      }
    }
    var emailCk = loiVerify.check("email",editData.email.val);
    if(emailCk==false) {
      loiMsg(loiVerify.errorMsg())
       return false;
    }
    if(editData.address.val.length<5 || editData.address.val.length>30) {
      loiMsg("请输入正确的地址")
       return false;
    }
    if(editData.idcard_pic.val.length<5 ) {
      loiMsg("请上传照片")
       return false;
    }
    var formData = loi.submit();
    formData.tp = tp;
    permPost("/json/save/sign",formData,function(res){
        if(res.code==0) {
          location.href = "/developer/sign/email"
        } else {
          loiMsg(res.msg);
        }
      });
  })
})

</script>
@endsection
