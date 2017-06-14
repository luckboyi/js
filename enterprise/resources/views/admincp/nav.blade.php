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

     <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_x6hla3zo5lvxi529.css">
    <script language="JavaScript" src="/admincp/semantic/semantic.min.js"></script>
    @yield('head')
</head>
<body>
<!-- menu start -->
<div class="ui menu">
  <a class="item left-15" ><span ></span></a>
  {!! $blade->adminCpMenu('main',$cur,'index',$user['perms']) !!}
  <div class="right menu">
    <div class="ui dropdown item">{{ $user['name'] }} <i class="dropdown icon"></i>
      <div class="menu">
<!--         <a class="item">个人资料</a>
        <a class="item">修改密码</a> -->
        <a class="item" data-value="loginOut">退出</a>
      </div>
    </div>
  </div>
</div>
<!-- menu end -->
<!-- left start -->
<div class="full">
	<div class="ui left vertical menu full-nav">
	  {!! $blade->adminCpMenu('sub',$cur,$path,$user['perms']) !!}
	</div>
	<div class="full-container">
	@yield('content')
	</div>
<!-- left end -->

</div>
</body>
<script type="text/javascript">
var $dropdown     = $('.menu .ui.dropdown');
$dropdown.dropdown({on: 'hover',onChange:function(v){
   if(v=="loginOut") {
    location.href = "/loginOut";
   }
}});
// if(typeof(Notification)!="undefined"){
//   Notification.requestPermission(function (permission) {
//   });
// }
</script>
@yield('javascript')
</html>
