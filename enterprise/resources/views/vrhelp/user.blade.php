@extends('vrhelp.layout')
@section('meta')
<title></title>
@endsection

@section('head')
<link rel="stylesheet" href="{{ static_res('/assets/jcrop/jquery.Jcrop.min.css') }}">
<style type="text/css">
/*a  upload */
.a-upload {
    padding: 4px 10px;
    height: 30px;
    line-height: 20px;
    position: relative;
    cursor: pointer;
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



</style>
@endsection

@section('content')
<div class="user_con">
<div class="user_top pr"><ul><li class="fl img_con"><img src="{{ $user['face'] }}" width="100" height="100"/></li>
<li class="fl pt20">
<p><span class="f18 fl">{{ $user['nick'] }}</span><!-- <span class="f12 fl grade">LV18</span><span class="f12 fl">游戏时长：1301小时</span><a href="#" class="f12 fl tac">编辑资料</a> --></p>
<!-- <p class="f14">个人签名：世界之大，无奇不有</p> -->
</li></ul></div>
<div class="u_main">
<div class="fl u_nav"><ul><li class="pr profile" data-val="profile"><i class="u_icon u_icon_data fl pa"></i>个人资料</li>
<li class="pr secure" data-val="secure"><i class="u_icon u_icon_security fl pa"></i>账号安全</li>
<li class="pr pense" data-val="pense"><i class="u_icon u_icon_consumption fl pa"></i>消费记录</li>
<!-- <li class="pr" data-val="profile"><i class="u_icon u_icon_exchange fl pa"></i>兑换记录</li> --></ul></div>
<div class="fr">
<div class="u_contentWrap">
<div class="u_content cn-profile">
<form class="u-form u-form-horizontal" onsubmit="return false">
<div class="u-form-group clearfix mb20">
    <label class="u-2 u-form-label fl f14">头像</label>
    <div class="u-10 fl pr img_con">
      <img src="{{ $user['face'] }}" width="80" height="80"/><button type="button" class="u-btn u-btn-mod f12 a-upload">修改头像<input type="file" id="head_photo" name="head_photo" ></button>
    </div>
  </div>
  <div class="u-form-group clearfix mb20">
    <label class="u-2 u-form-label fl f14">昵称</label>
    <div class="u-10 fl">
      <input type="text" id="nick" placeholder="输入你的昵称" value="{{ $user['nick'] }}">
    </div>
  </div>
 <!--  <div class="u-form-group clearfix mb20">
    <label class="u-2 u-form-label fl f14">性别</label>
    <div class="u-10 fl">
      <label class="u-radio-inline"><input type="radio" value="" name="docInlineRadio"> 男</label>
      <label class="u-radio-inline"><input type="radio" value="" name="docInlineRadio"> 女</label>
    </div>
  </div>
  <div class="u-form-group clearfix mb20">
    <label class="u-2 u-form-label fl f14">生日</label>
    <div class="u-10 fl">
      <input type="text" placeholder="输入你的生日">
    </div>
  </div> -->
<!--   <div class="u-form-group clearfix mb20">
    <label class="u-2 u-form-label fl f14">地域</label>
    <div class="u-10 fl">
      <select id="doc-select-1"><option value="option1">请选择</option><option value="option2">上海</option><option value="option3">选项三........</option></select>
      <select id="doc-select-1"><option value="option1">请选择</option><option value="option2">上海市</option><option value="option3">选项三........</option></select>
      <select id="doc-select-1"><option value="option1">请选择</option><option value="option2">徐汇区</option><option value="option3">选项三........</option></select>
    </div>
  </div> -->
  <!-- <div class="u-form-group clearfix mb20">
    <label class="u-2 u-form-label fl f14">签名</label>
    <div class="u-10 fl">
      <textarea class="" rows="5" id="doc-ta-1"></textarea>
    </div>
  </div> -->
  <button type="botton" onclick="saveProfile()" class="u-btn u-btn-primary f14" style="margin-left:250px">保存</button>
</form></div>
<div class="u_content u_security pr cn-secure">
<p><span class="f14 tar clearfix">VRonline账号：</span><span class="gray">{{ $user['account'] }}</span><button type="button" class="u-btn u-btn-primary f14 fl" onclick="toggleSecure('password')">修改密码</button></p>
<p><span class="f14 tar clearfix">绑定手机：</span><span class="gray">{{ $user['mobile'] }}</span><button type="button" class="u-btn u-btn-primary f14 fl" onclick="toggleSecure('mobile')">@if($mobile) 解除 @else 绑定 @endif</button></p>
<!--密码修改弹窗-->
<div class="u_box pa  modal-password" style="display: none"><div class="u_box_title f14 mb20">密码修改</div>
<form class="u-form u-form-horizontal" style="margin-left:50px;" onsubmit="return false">
  <div class="u-form-group clearfix">
    <label class="u-2 u-form-label fl f14">当前登录密码</label>
    <div class="u-10 fl">
      <input type="password" id="last_passwd"><span class="u_password_tips f12">&nbsp;</span>
    </div>
  </div>
  <div class="u-form-group clearfix">
    <label class="u-2 u-form-label fl f14">新的密码</label>
    <div class="u-10 fl">
      <input type="password" id="new_passwd"><span class="u_password_tips f12">&nbsp;</span>
    </div>
  </div>
  <div class="u-form-group clearfix">
    <label class="u-2 u-form-label fl f14">确认新的密码</label>
    <div class="u-10 fl">
      <input type="password" id="repeat_passwd"><span class="u_password_tips f12">&nbsp;</span>
    </div>
  </div>
  <button type="button" class="u-btn u-btn-primary f14" style="margin-left:120px" onclick="savePasswd()">确定</button><button type="button" class="u-btn u-btn-default f14" onclick="toggleSecure('password',true)">取消</button>
</form>
</div>
<!--密码修改弹窗结束-->
<!--手机绑定第一步弹窗-->

@if($mobile)
<div class="u_box pa  modal-mobile" style="display: none"><div class="u_box_title f14 mb20">解除绑定</div>
<form class="u-form u-form-horizontal" style="margin-left:50px;" onsubmit="return false">
  <div class="u-form-group clearfix">
    <label class="u-2 u-form-label fl f14">绑定手机号</label>
    <div class="u-10 fl">
      <span class="gray f14 mt10 show">{{ $user['mobile'] }}</span>
      <input type="hidden" class="mobile-num" value="{{ $mobile }}"><span class="u_password_tips f12">&nbsp;</span>
    </div>
  </div>
  <div class="u-form-group clearfix">
    <label class="u-2 u-form-label fl f14">短信验证码</label><button type="button" class="u-btn u-btn-code f12" data-val="mobileChange">获取验证码</button>
    <div class="u-10 fl">
      <input type="text" class="code-num" placeholder="请输入验证码"><span class="u_password_tips f12">&nbsp;</span>
    </div>
  </div>
  <span class="tac f12 u_tips mb20">系统将会以短信方式发送至<span class="red">绑定的手机上</span>，请注意查收！</span>
  <button type="button" class="u-btn u-btn-primary f14" style="margin-left:120px" onclick="bindMobile('mobileChange')">确定</button><button type="button" class="u-btn u-btn-default f14" onclick="toggleSecure('mobile',true)">取消</button>
</form>
</div>
@else
<div class="u_box pa  modal-mobile" style="display:none"><div class="u_box_title f14 mb20">绑定手机</div>
<form class="u-form u-form-horizontal" style="margin-left:50px;" onsubmit="return false">
  <div class="u-form-group clearfix">
    <label class="u-2 u-form-label fl f14">手机号</label>
    <div class="u-10 fl">
      <input type="text" class="mobile-num" maxlength="11" placeholder="请输入手机号"><span class="u_password_tips f12">&nbsp;</span>
    </div>
  </div>
  <div class="u-form-group clearfix">
    <label class="u-2 u-form-label fl f14">短信验证码</label><button type="button" class="u-btn u-btn-code f12" data-val="mobileBind">获取验证码</button>
    <div class="u-10 fl">
      <input type="text" class="code-num" placeholder="请输入验证码"><span class="u_password_tips f12">&nbsp;</span>
    </div>
  </div>
    <span class="tac f12 u_tips mb20">系统将会以短信方式发送至<span class="red">您输入的手机号上</span>，请注意查收！</span>
  <button type="button" class="u-btn u-btn-primary f14" style="margin-left:120px" onclick="bindMobile('mobileBind')">确定</button><button type="button" class="u-btn u-btn-default f14" onclick="toggleSecure('mobile',true)">取消</button>
</form>
</div>
@endif

</div>
<div class="u_content cn-pense">
<div class="u_consumption f14 tac oh span-pense"><span class="charge" data-val="charge">充值记录</span><span data-val="buy">购买记录</span></div>
<div>
<div class="u_cscontent cn-charge"><table class="tac f12">
  <tr style="background-color:#082333" class="u_cscontenttop">
    <td>订单号</td>
    <td>时间</td>
    <td>充值金额</td>
    <td>获得V币</td>
    <td>状态</td>
  </tr>
  <tbody id="paylog">
  </tbody>
</table>
<div class="s_page"><a href="/vrhelp/video/list#type=&amp;sort=&amp;page=1">上一页</a><a href="/vrhelp/video/list#type=&amp;sort=&amp;page=0">下一页</a></div>
</div>
<div class="u_cscontent cn-buy">
<table class="tac f12">
  <tr style="background-color:#082333" class="u_cscontenttop">
    <td>订单号</td>
    <td>时间</td>
    <td>游戏名称</td>
    <td>消费金额</td>
    <td>状态</td>
  </tr>
    <tbody id="buylog">
  </tbody>
</table>
<div class="s_page"><a href="/vrhelp/video/list#type=&amp;sort=&amp;page=1">上一页</a><a href="/vrhelp/video/list#type=&amp;sort=&amp;page=0">下一页</a></div>
</div>
</div>
</div>
<!-- <div class="u_content"><table class="tac f12">
  <tr style="background-color:#082333" class="u_cscontenttop">
    <td>时间</td>
    <td>兑换内容</td>
    <td>兑换码</td>
    <td>状态</td>
  </tr>
  <tr>
    <td>2017-05-05 08:24:58</td>
    <td>我的VR女友</td>
    <td>ad545897813</td>
    <td>交易成功</td>
  </tr>
</table></div> -->

</div>
</div></div>
</div>
@endsection

@section('javascript')
<script src="{!!URL::asset('js/paging.js')!!}" charset="utf-8"></script>
<script language="JavaScript" src="{{ static_res('/assets/jcrop/jquery.Jcrop.min.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/base/md5.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/faceCrop.js') }}"></script>
<script>
var last_nick = "{{ $user['nick'] }}";

$(function() {
  loadUser();
  window.onhashchange=function(){
     loadUser();
  };
  $('.u_nav').on('click','li',function(){
    var tp = $(this).attr('data-val');
    location.href = '/vrhelp/user#tp='+tp
  });
  $('.span-pense span').click(function() {
      $(this).addClass('active').siblings().removeClass('active');
      var tp = $(this).attr('data-val')
      location.href = '/vrhelp/user#tp='+tp;
      $('.cn-'+tp).show().siblings().hide();
      loadPense(tp)
  })
  $(document).on("change","#head_photo",function() {
    var files =  $(this).prop("files");
    if(files.length>=1) {
        var file = files[0];
        var crop = new Crop(file);
        crop.show(function(json){
            $('#head_photo').replaceWith('<input type="file" name="head_photo" id="head_photo">');
            var jsonResult = $.parseJSON(json);
            var random = Math.random();
            var face = img_domain+jsonResult.data.fileid+"?v="+random;
            $(".img_con img").attr("src",face);
            //同步头部图片
            PL.callFun('loginframe','updateuserinfo',{img:face});
            $.get('/web/modifyPic',function(){});
            // if(t.choose.h<60 || t.choose.w<60) {
            //     console.log("no crop");
            // }
        },function(errorObj) {
            loiNotifly('修改失败');
            return false;
        })
    }
  });
  $(".u-btn-code").click(function() {
    var tp =  $(this).attr('data-val');
    var mobile = $('.mobile-num').val();
    var verify = new loiValidator();
    var ck = verify.check('mobileCN',mobile)
    if(ck==false) {
      loiNotifly(verify.errorMsg());
      return ;
    } else {
      sendSms(tp,mobile);
    }
  })
})

function loadUser() {
    var tp  = getUrlHash('tp','profile');
    $('.u_nav li.'+tp).addClass('active').siblings().removeClass('active');
    $('.u_content.cn-'+tp).show().siblings().hide();
    if(tp=='pense'||tp=='charge') {
      $('.u_content.cn-pense').show().siblings().hide();
      $('.charge').addClass('active').siblings().removeClass('active');
      $('.cn-charge').show().siblings().hide();
      loadPense('charge')
    }
    if(tp=='buy'){
      $('.u_content.cn-pense').show().siblings().hide();
      $('.buy').addClass('active').siblings().removeClass('active');
      $('.cn-buy').show().siblings().hide();
      loadPense('buy')
    }

}

 // 修改昵称
 function saveProfile() {
    var nick = $("#nick").val();
    nick = $.trim(nick);
    if(last_nick==nick) {
      loiNotifly('昵称更新成功');
      return false;
    }
    if (nick.length == 0) {
         loiNotifly('昵称不能为空');
        return false;
    }

    $.post('/profile/edit',{ nick : nick},function(res){
      if(res.code==0) {
         loiNotifly('昵称更新成功');
         PL.callFun('loginframe','updateuserinfo',nick);
      } else {
         loiNotifly(res.msg);
      }
    },"json")
 }

function toggleSecure(tp,close) {
  if(typeof(close)!="undefined" && close==true ) {
    $('.modal-'+tp).hide();
  } else {
    $('.modal-'+tp).show();
  }

}

function savePasswd() {
    var oldPwd = $("#last_passwd").val();
    var newPwd = $("#new_passwd").val();
    var newPwdConfirm = $("#repeat_passwd").val();
    if (oldPwd == newPwd) {
        loiNotifly('新密码和旧密码相同');
        return false;
    } else if(newPwd != newPwdConfirm){
        loiNotifly('重复密码和新密码不一致');
        return false;
    }
    $.post('/password/edit', { oldPwd : oldPwd, newPwd : newPwd},function(res) {
      if(res.code==0) {
          loiNotifly('修改成功',1000,function(){
              toggleSecure('password',true)
          });
      } else {
        loiNotifly('修改失败');
      }
    },"json")
}


function loadPense(tp,page) {
  page = getUrlHash('page');
  if(typeof(page)=="undefined") {
    page = 1;
  }
  if(tp=='charge')  {
    $.get('/vrhelp/order?page='+page,function(res) {
      if(res.code==0) {
        var html = ''
        var orders = res.data.orders;
        for(var i=0;i<orders.length;i++) {
           html = html+'<tr><td>'+orders[i]['tradeid']+'</td>'+
            '<td>'+unixToDate(orders[i]['ctime'])+'</td>'+
            '<td>'+orders[i]['rmb']+'</td>'+
            '<td>'+orders[i]['plantb']+'V币</td>'+
            '<td class="orange">'+showStat(orders[i]['stat'])+'</td></tr>';
        }
        $('#paylog').html(html)
        config = {
          target_obj:$('.page_s'),
          total_page:Math.ceil(res.data.total/10),
          page:page,
          callback:function(){
            console.log('callback');
          }
        }
        paging.init(config);
      }
    },"json")
  } else {
    $.get('/vrhelp/consume?page='+page,function(res) {
      if(res.code==0) {
        var html = ''
        var orders = res.data.orders;
        for(var i=0;i<orders.length;i++) {
           html = html+'<tr><td>'+orders[i]['tradeid']+'</td>'+
            '<td>'+unixToDate(orders[i]['ctime'])+'</td>'+
            '<td>'+orders[i]['appname']+'</td>'+
            '<td>'+orders[i]['plantb']+'V币</td>'+
            '<td class="orange">'+showStat(orders[i]['stat'])+'</td></tr>';
        }
        $('#buylog').html(html)
        config = {
          target_obj:$('.page_s'),
          total_page:Math.ceil(res.data.total/10),
          page:page,
          callback:function(){
            console.log('callback');
          }
        }
        paging.init(config);
      }
    },"json")
  }
}


function showStat(stat) {
  stat = parseInt(stat)
  var order_stat = {1:"交易未完成",8:"交易成功"}
  if(typeof(order_stat[stat])=="undefined'") {
    return order_stat[1]
  } else {
     return order_stat[stat]
  }
}

function sendSms(tp,mobile) {
  $('.u-btn-code').attr('disabled',true)
  var sec = 60
  var wait =  setInterval(function(){
    $('.u-btn-code').text(sec+"s后获取")
    sec--
    if(sec<=0) {
      clearInterval(wait)
      $('.u-btn-code').text("获取验证码")
      $('.u-btn-code').attr('disabled',false)
    }
  },1000)
  $.post('/mobile/sms',{mobile:mobile,action:tp},function(res){
    if(res.code==0) {
      loiNotifly('发送验证码成功')
    } else {
      loiNotifly('发送验证码失败')
    }
  },"json")
}



function bindMobile(action) {
  var mobile =  $('.mobile-num').val();
  var code =  $('.code-num').val();

  var verify = new loiValidator();
  var ck = verify.check('mobileCN',mobile)
  if(ck==false) {
    loiNotifly(verify.errorMsg());
    return ;
  }
  if(code=="" || code.length<4) {
     loiNotifly('验证码错误')
     return ;
  }
  $.post('/mobile/bind',{mobile:mobile,code:code,action:action},function(res){
      if(res.code==0) {
        if(action="mobileBind") {
          loiNotifly('绑定成功',1000,function(){
              location.reload()
          });
        } else {
          loiNotifly('解除绑定成功',1000,function(){
              location.reload()
          });
        }
      } else {
        if(action="mobileBind") {
          loiNotifly('绑定失败')
        } else {
           loiNotifly('解除绑定失败')
        }
      }
  },"json")
}

function cancelMobile() {
  var mobile = $("#mobile").val();
  var code = $("#code").val();
  if(!mobile || !code) {
    loiNotifly('修改失败');
    return false;
  }
  $.post('',{ mobile : mobile, code : code},function(res){
      if(res.code==0) {
          loiNotifly('修改成功',1000,function(){
              toggleSecure('password',true)
          });
      } else {
        loiNotifly('修改失败');
      }
  },"json");
}
</script>
@endsection
