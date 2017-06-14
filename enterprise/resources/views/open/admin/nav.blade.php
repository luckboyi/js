<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>开发者 - vronline</title>
    <link rel="stylesheet" type="text/css" href="{{ static_public('/admincp/semantic/semantic.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ static_public('/admincp/semantic/base.css') }}">
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_wcj5tog56j4np14i.css">
    <script language="JavaScript" src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
    <script language="JavaScript" src="{{ static_public('/admincp/semantic/semantic.min.js') }}"></script>
    @yield('head')
</head>
<body>
<!-- menu start -->
<div class="ui menu">
  <a class="item left-15" ><span ></span></a>
   <a  href="/developer/vrgame" class="item active">首页</a><a  href="/docs" target="_blank" class="item ">开发者文档</a>

  <div class="right menu">
    <div class="ui dropdown item">{{ $user['nick'] }} <i class="dropdown icon"></i>
      <div class="menu">
<!--         <a class="item">个人资料</a>
        <a class="item">修改密码</a> -->
        <a class="item" data-value="logout">退出</a>
      </div>
    </div>
  </div>
</div>
<!-- menu end -->
<!-- left start -->
<div class="full">
  @if(isset($cur))
	<div class="ui left vertical menu full-nav">
	  <div class="head item">首页</div>
    {!! $blade->openAdminMenu($cur) !!}
	</div>
  @endif
	<div class="full-container">
	@yield('content')
	</div>
<!-- left end -->

</div>
</body>
<script type="text/javascript">
var $dropdown     = $('.menu .ui.dropdown');
$dropdown.dropdown({on: 'hover',onChange:function(v){
   if(v=="logout") {
    location.href = "/logout";
   }
}});
function addSearch(link) {
$(".action-search").keypress(function() { if(event.keyCode==13) { var searchText = $(this).val(); location.href = link+"?search="+searchText; } }); $(".search.link").click(function() { var searchText = $(this).prev().val(); location.href = link+"?search="+searchText; }); $(".remove.link").click(function() { var searchText = $(this).prev().val(); location.href = link; });
}
</script>
@yield('javascript')
</html>
