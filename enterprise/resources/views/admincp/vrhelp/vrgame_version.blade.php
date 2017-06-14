@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<link rel="stylesheet" type="text/css" href="/admincp/editor/css/editor.min.css">
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')

<div class="ui basic small buttons">
<a href="#"><div class="ui basic  button blue">{{ $gameName }}</div></a>
<a href="/vrhelp/vrgame"><div class="ui basic  button ">返回游戏</div></a>
</div>

<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>新建版本</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="10%">名称</th>
      <th width="10%">版权号</th>
      <th width="20%">启动路径</th>
      <th width="10%">子版本</th>
      <th width="30%"  >版权说明</th>
      <th width="10%">状态</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="7" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td>{{ $gameName }}</td>
              <td class="action-edit" data-val="{{ $val["version_name"] }}">{{ $val["version_name"] }}</td>
              <td>{{ $val["version_start_exe"] }}</td>
              <td>{{ $val["version_id"] }}</td>
              <td>{{ $val["version_desc"] }}</td>
              <td>@if($val["stat"]==1) 上线 @else 未上线 @endif </td>
              <td class="right aligned" data-val="{{ $val["version_name"] }}" data-id="{{ $val["version_id"] }}">  <i  class="large  iconfont-fire iconfire-xianshangxianxia icon red action-sub"></i><i  class="large list layout icon  blue action-audit"></i><i  class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="7">
         {!! $data->render() !!}
    </th>
  </tr></tfoot>
</table>


<div class="ui modal modal-edit">
  <i class="close icon"></i>
  <div class="header">添加版本</div>
  <div class="content">
    <form class="ui form " onsubmit="return false;">
      <div class="field">
       <label>版本号</label>
      <input id="version_name" type="text" >
      </div>
       <div class="field">
       <label>启动路径</label>
     <input id="version_start_exe" type="text" >
      </div>
      <div class="field">
       <label>更新说明</label>
      <textarea id="version_desc"  rows="5"></textarea>
      </div>
    </form>
  </div>
  <div class="actions">
    <div class="ui button">取消</div>
    <div class="ui button action-save">确定</div>
  </div>
</div>


<div class="ui small modal modal-audit">
  <i class="close icon"></i>
  <div class="header">选择版本</div>
  <div class="content" >
  <div class="ui form" >
    <div class="grouped fields" >
    </div>
  </div>
  </div>
  <div class="actions">
  <div class="ui positive button" onclick="chooseSubVersion()">确定 </div>
  </div>
</div>


<div class="ui modal modal-sub">
  <i class="close icon"></i>
  <div class="header"></div>
  <div class="content">
  你确定要上线该版本<label></label>吗？
  </div>
  <div class="actions">
  <div class="ui positive button" onclick="publishVersion()">确定 </div>
  </div>
</div>

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header"></div>
  <div class="content">
  你确定要删除该版本<label></label>吗？
  </div>
  <div class="actions">
  <div class="ui positive button" onclick="delVersion()">确定 </div>
  </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
var modal_audit,modal_edit,modal_sub,modal_del;
var appid =  '{{ $appid }}';
var loi = new loiForm();
var versionName;
$(function(){
  $(".action-edit").click(function() {
    var versionName =  $(this).attr("data-val");
    dataEdit(appid,versionName)
  });
  $(".action-save").click(function(){
    var editData = loi.save();
    if(typeof(editData.err) != "undefined") {
      loiMsg(editData.err+"未填写");
    } else {
      formData = loi.submit();
      formData.appid = appid
      permPost("/json/save/vrgame_version",formData,function(data){
        location.reload();
      });
      modal_edit.modal('hide');
    }
  });
  $(".action-audit").click(function() {
    versionName = $(this).parent().attr("data-val");
    var checkedId = $(this).parent().attr("data-id");
    permPost("/vrhelp/vrgame/subversions",{appid:appid,version_name:versionName},function(res) {
      if(res.code==0) {
        var html = ''
        var checked = '';
        $.each(res.data,function(a,b){
          if(b.id == checkedId) {
             checked =  "checked true";
          } else {
            checked =  "";
          }
          html = html+'<div class="field"><div class="ui radio checkbox"><input type="radio" name="sub_version" value="'+b.id+'" '+checked+'><label>'+b.id+' ['+b.ltime+']</label></div></div>'
        })
        $(".modal-audit .grouped.fields").html(html);
      }
    })
    modal_audit = $('.ui.modal.modal-audit').modal('show');
  });
  $(".action-sub").click(function() {
    versionName = $(this).parent().attr("data-val");
    modal_sub = $('.ui.modal.modal-sub').modal('show');
  });
   $(".action-del").click(function() {
    versionName = $(this).parent().attr("data-val");
    modal_sub = $('.ui.modal.modal-del').modal('show');
  });
})

function dataEdit(appid,versionName) {
  loi.edit("vrgame_version",{appid:appid,version_name:versionName},function(data){
    if(data.version_name.val.length>0) {
      $('#version_name').attr('readonly',true)
    } else {
      $('#version_name').attr('readonly',false)
    }
    modal_edit = $('.ui.modal.modal-edit').modal('show');
  });
}


function chooseSubVersion() {
  var version_id = $("input[name='sub_version']:checked").val();
  if(typeof(version_id)=="undefined") {
    loiMsg("请选择上传版本")
  }
  permPost("/json/save/vrgame_version",{appid:appid,version_name:versionName,version_id:version_id},function(res) {
    location.reload();
  });
}

function publishVersion() {
  permPost("/json/pass/vrgame_version",{appid:appid,version_name:versionName},function(res){
    location.reload();
  })
}

function delVersion() {
  permPost("/json/del/vrgame_version",{appid:appid,version_name:versionName},function(res){
    location.reload();
  })
}
</script>
@endsection
