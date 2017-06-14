@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/common/js/clipboard.min.js') }}"></script>

@endsection




@section('content')


<h4 class="ui top attached block header">
    {{ $data['name'] }} <a class="ui {{  $blade->showHtmlStat('game_color',$data["stat"],$data["send_time"]) }} tag label game-title">{{  $blade->showHtmlStat('game',$data["stat"],$data["send_time"]) }}</a><i class="small iconfont-fire iconfire-fanhui icon action-audit action-back"  onclick="javascript:location.href='/developer/vrgame/'"></i>
  </h4>
  <div class="ui bottom attached segment">

  <div class="ui segment noborder">  <span class="appinfo">APP ID: <i>{{ $data['appid'] }}</i></span>  <span class="appinfo">APP KEY: <i>{{ $data['appkey'] }} </i></span>  <span class="appinfo">PAY KEY: <i>{{ $data['paykey'] }} </i></span> <span class="text-copy"  data-title="复制成功" data-clipboard-text="APP ID:{{ $data['appid'] }} APP KEY:{{ $data['appkey'] }} PAY KEY:{{ $data['paykey'] }} "><i class="small iconfont-fire iconfire-fuzhi icon   action-audit"></i>点击复制  </span> &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-pay" onclick="dataEdit('{{ $id }}','pay')"><i class=" iconfont-fire iconfire-zhifu blue icon   action-audit"></i>支付设置 </span></div>


    <div class="ui link cards game">
        <div class="card" >
            <div class="content"  onclick="dataEdit('{{ $id }}')">
                <i class="great iconfont-fire iconfire-jibenxinxi icon   action-audit"></i>
                <div class="header">基本信息</div>
                <div class="description">产品基础信息，包括名称分类等，上线后无法修改</div>
            </div>
        </div>
        <div class="card">
            <div class="content"  onclick="dataEdit('{{ $id }}','pic')">
                <i class="great iconfont-fire iconfire-tupian icon  action-audit"></i>
                <div class="header">图片资源</div>
                <div class="description">平台上显示的图标，可在上线后进行修改</div>
            </div>
        </div>
        <div class="card">
            <div class="content"  onclick="edit()">
                <i class="great iconfont-fire iconfire-agreement icon  action-audit" ></i>
                <div class="header">电子合同</div>
                <div class="description">电子合同以及版权文件</div>
            </div>
        </div>
        <div class="card">
            <div class="content" onclick="version()">
                <i class="great iconfont-fire iconfire-banbenguanli icon  action-audit" ></i>
                <div class="header">版本管理</div>
                <div class="description">上传最新的客户端版本</div>
            </div>
        </div>
       <!--  <div class="card">
            <div class="content">
                <i class="great iconfont-fire iconfire-shuju icon  action-audit"></i>
                <div class="header">数据统计</div>
                <div class="description">查看游戏数据统计</div>
            </div>
        </div> -->

    </div>

</div>

<div class="developer next">
      <div class="ui teal button @if($data['stat']==1)  disabled @endif" onclick="review()">@if($data['stat']==1) 正在审核 @else 提交审核 @endif</div>
      &nbsp; &nbsp; &nbsp; &nbsp;
      @if($data['send_time']==0)<div class="ui teal button @if($data['stat']!=5) disabled  @endif" onclick="online()">发布上线</div> @endif
<div>



@include("open.admin.vrgame_edit")



@endsection
@section('javascript')
<script type="text/javascript">
var appid = {{ $id }}
function add() {
    modal_edit = $('.ui.modal.modal-add').modal('show');
}

function edit() {
    location.href = '/developer/vrgame/agreement/'+appid;
}
function version() {
     location.href = '/developer/vrgame/version/'+appid;
}
function review() {
    $.post('/json/submit/vrgame_review',{id:appid},function(data){
        if(data.code==0) {
            location.reload();
        } else {
            loiMsg(data.msg);
        }
    },"json")
}
function online() {
    $.post('/json/submit/vrgame_publish',{id:appid},function(data){
        if(data.code==0) {
             loiMsg("发布成功，请到线上查看",function(){
                location.reload();
             });
        } else {
            loiMsg(data.msg);
        }
    }
    ,"json")
}
$(function(){
    var clipboard = new Clipboard('.text-copy');
    clipboard.on('success', function(e) {
        loiMsg("复制成功",function(){},"success");
        e.clearSelection();
    });

    clipboard.on('error', function(e) {
         loiMsg("复制失败");
    });

})
</script>
@endsection
