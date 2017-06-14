@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')
<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加用户组</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="10%">ID</th>
      <th width="80%">名称</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if(count($data)<1)
                <tr><td colspan="3" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td  data-val="{{ $val["id"] }}">{{ $val["id"] }}</td>
              <td class="action-edit"  data-id="{{ $val["id"] }}">{{ $val["name"] }}</td>
              <td class="right aligned"><i class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
</table>


<div class="ui modal modal-add perm">
  <i class="close icon"></i>
  <div class="header">添加用户组</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
    	<input id="group_id" type="hidden" >
		<div class="field">
		<label>名称</label>
		<input id="group_name" type="text" >
		</div>
    <div class="field">
    <label>默认首页</label>
    <input id="group_path" type="text" >
    </div>
    	{!! $blade->permHtml() !!}
    </form>
     <div class="ui bottom attached warning message"></div>
  </div>

  <div class="actions">
    <div class="ui blue button action-save">保存</div>
  </div>
</div>



<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">删除用户组</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要删除该用户组吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
var edit_modal;
var del_modal;
var warning_msg = $(".warning.message");
var del_id;
var group_perms;

$(function(){
	group_perms = $('.ui.checkbox').checkbox();
	$('.ui.checkbox').click(function(){
	 	var isChecked = $(this).checkbox("is checked");
	 	if(isChecked) {
	 		var first =  $(this).parent().find('.checkbox:first');
	 		first.checkbox("set checked",true);
	 	}
	});
});


var loi = new loiForm();
$(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   dataEdit(id)
});

$(".action-save").click(function(){
  var editData = loi.save();
  console.log(editData);
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    formData = loi.submit();
    permPost("/json/save/sys_group",formData,function(data){
     // location.reload();
    });
    edit_modal.modal('hide');
  }

});


function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/sys_group",{del_id:del_id},function(data){
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
	loi.edit("sys_group",id,function(data){
		edit_modal = $('.ui.modal.modal-add').modal('show');
		warning_msg.hide();
	});
}


</script>
@endsection