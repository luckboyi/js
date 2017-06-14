@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')


@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/swfobject.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')


<div class="ui small button right floated blue" onclick="tickBack()">返回列表</div>
<div class="ui grid" style="clear:both">
  <div class="ten wide column">
      <h3 class="ui dividing header">问题编号 </h3>
      {{ $val["code"] }}
      <h3 class="ui dividing header">用户信息 </h3>
      <label>用户ID:</label>
      {{ $val["uid"] }}
      <label>用户账号:</label>
      {{ $val["account"] }}
      <h3 class="ui dividing header">分类信息 </h3>
      <label>分类:</label>
      {{ $blade->showHtmlClass('service_question_tp',$val["tp"]) }}
      <label>子分类:</label>
      {{ $blade->showSubClass('service_question_sub_tp',$val["tp"],$val["sub_tp"]) }}
      <h3 class="ui dividing header">问题 {{ $val["ctime"] }}</h3>
      {{ $val["title"] }}
      <h3 class="ui dividing header">截图 </h3>
      <h3 class="ui dividing header">联系方式 </h3>
      <label>姓名:</label>
       {{ $val["name"] }}
      <label>手机:</label>
      {{ $val["mobile"] }}
      <label>QQ:</label>
      {{ $val["qq"] }}
      <label>邮箱:</label>
      {{ $val["email"] }}
    </div>
    <div class="six wide column">
      <div class="ui comments">
      <h3 class="ui dividing header">问题跟踪</h3>
      @if(!empty($content))
      @foreach($content as $comment)
      <div class="comment">
      <a class="avatar">
      @if($comment['tp']==1)
      <i class="large iconfont-fire iconfire-kefu icon  blue"></i>
      @else
      <i class="large iconfont-fire iconfire-yonghu icon teal "></i>
      @endif
      </a>
      <div class="content">
      <a class="author"> @if($comment['tp']==1) 客服  @else 用户 @endif</a>
      <div class="metadata">
      <div class="date">{{ date("Y-m-d H:i:s",$comment['time']) }}</div>
      </div>
      <div class="text">{{ $comment['cn'] }} </div>
      </div>
      </div>
      @endforeach
      @endif
      <form class="ui reply form">
      <div class="field">
      <textarea id="reply-cn"></textarea>
      </div>
      <div class="ui blue labeled submit icon button action-submit"><i class="icon edit"></i> 添加回复 </div>
      </form>
      </div>
      </div>
      </div>
    </div>
 </div>
@endsection

@section('javascript')
<script type="text/javascript">
var code = "{{ $val['code'] }}"
$(function(){
  $(".action-submit").click(function(){
    var cn = $("#reply-cn").val();
    if (cn.length < 1) {
      alert("回复不能为空");
      return false;
    }
    permPost("/json/save/service_feedback_reply",{code:code,cn:cn},function(data){
      location.reload()
    });
  })
})
function tickBack(){
 location.href= "/service/feedback"
}
</script>
@endsection
