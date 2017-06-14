@extends('pagegame.layout')

@section("head")
<script src="{{ static_res('/webgames/js/tinyscrollbar.js') }}"></script>
<script type="text/javascript" src="//pic.vronline.com/common/js/messenger.js"></script>
<script type="text/javascript" src="//pic.vronline.com/webgames/js/login.js"></script>
@endsection

@section("title")
{{$gameinfo["name"]}}
@endsection

@section('content')
<div class="whole">
  <div class="choose">
    <div class="header">
      <ul class="title clearfix">
        <li><a href="javascript:;" class="titleShow titleBegin start-web-game" appid={{$appid}} server-id=-1></a></li>
        <li><a @if($gameinfo['forumid']) href="//bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$gameinfo['forumid']}}" @else href="//bbs.vronline.com/forum.php" @endif target="_blank" class="titleShow titleForm"></a></li>
        <li><a href="javascript:;" class="titleShow titleRecharge go_paycenter" appid="{{$appid}}" uid="{{$uid}}"></a></li>
        <li><a href="javascript:;" class="titleShow titleSave" onclick="addFavorite()"></a></li>
        <!-- <li><a href="javascript:;"class="titleShow titleOffWeb"></a></li> -->
      </ul>
      <p class="login"></p>
      <div class="msg clearfix">
        @if(!$islogin)
        <div class="LoginChoose">
          <a href="javascript:;" class="butt loginNow" id="loginShowBtn"></a>
          <a href="javascript:;" class="butt registerNow" id="regShowBtn"></a>
        </div>
        @else
        <div>
          <div class="fl showMsg">
            <p class="userName">你好,<span class="name">{{$account}}</span></p>
            @if(isset($myservers[0]))
            <p class="loginTime">登录时间：<span class="TimeDate">{{date("Y-m-d",$myservers[0]["ltime"])}}</span><span class="TimeTime">{{date("H:i:s",$myservers[0]["ltime"])}}</span></p>
            <p class="serverName">{{$myservers[0]["servername"]}}</p>
            @endif
          </div>
          <div class="fr recommend">
            <p ><a href="//www.vronline.com/profile" target="_blank" class="profile">完善个人资料</a></p>
            <p>{{$gameinfo["name"]}}推荐您进入：</p>
            <p class="fr"><a href="//www.vronline.com/profile" target="_blank" class="userCenter">用户中心</a><a href="//www.vronline.com/logout?referer=//web.vronline.com/detail/{{$appid}}" class="layout">注销</a></p>
          </div>
        </div>
        @endif
      </div>
      <p class="serverList"></p>
      <div class="subject">
        <div class="distance" id="distance">
          @if(isset($myservers) && is_array($myservers) && count($myservers)>0)
<?php $counter = 0;?>
<p class="myServer">我的服务器</p>
          @foreach($myservers as $server)
<?php $counter++;
if ($counter > 3) {continue;
}

?>
<a href="javascript:;" class="start-web-game" appid="{{$server["appid"]}}" server-id="{{$server["serverid"]}}"><button class="myServerList">{{$server["servername"]}}</button></a>
          @endforeach
          @endif
          <p class="recServer">推荐服务器</p>
          @if($recommend && is_array($recommend))
          @foreach($recommend as $k => $info)
          <a href="javascript:;" class="start-web-game" appid="{{$info["appid"]}}" server-id="{{$info["serverid"]}}"><button class="recServerList"><span>{{$info['name']}}</span><span class="hotOpen">火爆开启</span></button></a>
          @endforeach
          @endif
          <p class="recServer">选择服务器</p>
          <div class="rolling" id="userRolling">
            <div class="scrollbar">
              <span class="upRolling"></span>
              <span class="downRolling"></span>
              <div class="track">
                <div class="thumb" style="height:20px;"><div class="end"></div></div>
              </div>
            </div>
            <div class="viewport">
              <div class="overview">
                @foreach($allservers as $server)
                <a href="javascript:;" class="start-web-game" appid="{{$server["appid"]}}" server-id="{{$server["serverid"]}}"><button class="recServerList"><span>{{$server["name"]}}</span><span class="hotOpen">火爆开启</span></button></a>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('javascript')
<script>
$(function(){
  $("#userRolling").tinyscrollbar();
});

webgameLogin.init();

function addFavorite() {
  var url = window.location;
  var title = document.title;
  var ua = navigator.userAgent.toLowerCase();
  if (ua.indexOf("360se") > -1) {
    alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
  }
  else if (ua.indexOf("msie 8") > -1) {
    window.external.AddToFavoritesBar(url, title); //IE8
  }else if (document.all) {
    try{
      window.external.addFavorite(url, title);
    }catch(e){
      alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
    }
  }else if (window.sidebar) {
    window.sidebar.addPanel(title, url, "");
  }else {
    alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
  }
}
</script>
@endsection
