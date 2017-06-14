@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')

<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加用户</div>

<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="20%">ID</th>
      <th width="20%">姓名</th>
      <th width="20%">账号</th>
      <th width="20%">用户组</th>
      <th width="20%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="5" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td  data-val="{{ $val["id"] }}">{{ $val["id"] }}</td>
              <td class="action-edit"  data-id="{{ $val["id"] }}">{{ $val["name"] }}</td>
              <td>{{ $val["account"] }}</td>
              <td>@if(isset($groups[$val["group_id"]])) {{  $groups[$val["group_id"]]['name'] }} @endif</td>
              <td class="right aligned"><i class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="5">
         {!! $data->render() !!}
    </th>
  </tr></tfoot>
</table>

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">删除用户</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要删除该用户吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>


<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加用户</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
     <input id="user_id" type="hidden"  >
      <div class="field">
       <label>用户名称</label>
      <input id="name" type="text" >
      </div>
      <div class="field">
       <label>用户账号</label>
      <input id="account" type="text" >
      </div>
      <div class="field">
       <label>用户组</label>
        <select id="group" class="ui dropdown"   >
         <option value="">请选择</option>
         @foreach($groups as $val)
            <option value="{{ $val['id'] }}" >{{ $val['name'] }}</option>
         @endforeach
        </select>
      </div>
       <div class="field">
       <label>用户密码</label>
      <input id="password" type="text" >
      </div>
    </form>
     <div class="ui bottom attached warning message"></div>
  </div>

  <div class="actions">
    <div class="ui blue button action-save">保存</div>
  </div>
</div>

<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="/admincp/Somethingtest.swf" style="visibility: visible;"></object>
@endsection

@section('javascript')
<script type="text/javascript">
var edit_modal;
var del_modal;
var warning_msg = $(".warning.message");
var del_id;
var group = $('select.dropdown').dropdown();

var loi = new loiForm();
$(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   dataEdit(id)
});

$(".action-save").click(function(){
  var editData = loi.save();
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    formData = loi.submit();
    permPost("/json/save/sys_user",formData,function(data){
      location.reload();
    });
    edit_modal.modal('hide');
  }

});


function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/sys_user",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}

$(".action-del").click(function() {
  var that = $(this);
  var obj = that.parent().parent().find("td:first");
  del_id = obj.attr('data-val');
  del_modal = $('.ui.modal.modal-del').modal('show');
})


function dataEdit(id) {
  loi.edit("sys_user",id,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
})
}
</script>
@endsection