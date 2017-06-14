<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>AdminCp - vronline</title>
    <link rel="stylesheet" type="text/css" href="/admincp/semantic/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="/admincp/semantic/base.css">
    <script language="JavaScript" src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
    <script language="JavaScript" src="/admincp/semantic/semantic.min.js"></script>
<style type="text/css">
body {
  background-color: #DADADA;
}
body > .grid {
  height: 100%;
}
.image {
  margin-top: -100px;
}
.column {
  max-width: 450px;
}
</style>
</head>
<body>
<div class="ui middle aligned center aligned grid">
  <div class="column">
    <h2 class="ui black image header">
      <img src="images/logo.png" class="image">
      <div class="content">
        Admin
      </div>
    </h2>
    <div class="ui warning message">
       请设置您的密码
    </div>
    <form class="ui large form" onsubmit="return reset()">
      <div class="ui stacked segment">
        <div class="field">
          <div class="ui left icon input">
            <i class="mail icon"></i>
            <input type="hidden" name="uid" value="{{ $uid }}">
            <input type="hidden" name="code" value="{{ $code }}">
            <input type="email" name="account" value="{{ $email }}" readonly="true">
          </div>
        </div>
         <div class="field">
          <div class="ui left icon input">
            <i class="user icon"></i>
            <input type="text" name="name" value="{{ $name }}" readonly="true">
          </div>
        </div>
        <div class="field">
          <div class="ui left icon input">
            <i class="lock icon"></i>
            <input type="password" name="password" placeholder="请输入密码" >
          </div>
        </div>
        <div class="ui fluid large blue  button action-login">确定</div>
      </div>
      <div class="ui error message"></div>
    </form>
  </div>
</div>
<script>
var v_form;
$(function(){
  $("input[name=password]").focus();
  v_form = $('.ui.form').form({
    on: 'blur',
    fields: {
      password: {
        identifier  : 'password',
        rules: [
          {
            type   : 'length[6]',
            prompt : 'Please enter a value'
          }
        ]
      }
    }
  });

  $(".action-login").click(function() {
    reset();
  })
})

function reset() {
  var code = $("input[name='code']").val();
  var uid = $("input[name='uid']").val();
  var password = $("input[name='password']").val();
  var v = v_form.form('is valid');
  if(v==true) {
    $.post('/sys/resetPwd',{uid:uid,code:code,password:password},function(data) {
      if(data.code!=0) {
         v_form.form('add errors',[data.msg]);
       } else {
         location.href = "/";
       }
    },"json");
  }
  return false;
}
</script>
</body>
</html>