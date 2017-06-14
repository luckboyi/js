@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')
<div class="ui basic small buttons">
 <a href="/vronline/position"><div class="ui basic button  @if(!$choose) blue @endif">所有</div></a>
 {!! $blade->adminCpClass("vronline_pos_group",$choose,'a') !!}
</div>
<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加推荐位</div>

 <table class="ui sortable  celled table">
  <thead>
    <tr>
      <th width="10%">ID</th>
      <th width="20%">推荐位代码</th>
      <th width="20%">推荐位名称</th>
      <th width="20%">推荐位分组</th>
      <th width="20%">推荐位描述</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="6" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td  data-id="{{ $val["pos_id"] }}">{{ $val["pos_id"] }}</td>
              <td>{{ $val["pos_code"] }}</td>
              <td class="action-edit"   data-id="{{ $val["pos_id"] }}">{{ $val["pos_name"] }}</td>
              <td>{!! $blade->adminCpClass('vronline_pos_group',$val["pos_group"]) !!}</td>
              <td>{{ $val["pos_desc"] }}</td>

              <td class="right aligned"><i class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="6">
         {!! $data->appends(['choose' => $choose])->render() !!}
    </th>
  </tr></tfoot>
</table>

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">删除推荐位</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要删除该推荐位吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>


<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加推荐位</div>
  <div class="content">
    <form class="ui form ">
      <div class="field">
       <input id="pos_id" type="hidden" >
       <label>推荐位代码</label>
      <input id="pos_code" type="text" >
      </div>
      <div class="field">
       <label>推荐位名称</label>
      <input id="pos_name" type="text" >
      </div>
       <div class="field">
      <label>推荐位分组</label>
      <select id="pos_group" class="ui dropdown"   >
      <option value="">选择分组</option>
       {!! $blade->adminCpClass('vronline_pos_group','','select') !!}
      </select>
      </div>
      <div class="field">
      <label>推荐位描述</label>
      <input id="pos_desc" type="text"  >
      </div>
    </form>
     <div class="ui bottom attached warning message"></div>
  </div>
  <div class="actions">
    <div class="ui button action-save">保存</div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
var del_modal;
var edit_modal;
var warning_msg = $(".warning.message");
var del_id;


var loi = new loiForm();
$(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   dataEdit(id)
});

function dataEdit(id) {
  loi.edit("vronline_position",id,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
  })
}

$(".action-save").click(function(){
  var editData = loi.save();
  if(typeof(editData.err) != "undefined") {
      warning_msg.html("还有未填写项目");
      warning_msg.show();
    } else {
      formData = loi.submit();
      permPost("/json/save/vronline_position",formData,function(data){
        location.reload();
      });
      edit_modal.modal('hide');
    }
});


$(".action-del").click(function() {
  var that = $(this);
  var obj = that.parent().parent().find("td:first");
  del_id = obj.attr('data-id');
  del_modal = $('.ui.modal.modal-del').modal('show');
})


function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/vronline_position",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}

</script>
@endsection
