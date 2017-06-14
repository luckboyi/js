@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection


@section('content')
<div class="ui basic small buttons">
{!! $blade->showHtmlClass('tob_status',$status,'menu') !!}
</div>
<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加商户</div>



<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="5%">ID</th>
      <th width="10%">账号</th>
      <th width="10%">UID</th>
      <th width="15%">名称</th>
      <th width="10%">联系人</th>
      <th width="10%">电话</th>
      <th width="20%">地址</th>
      <th width="10%">审核状态</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="8" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr id="merchant-{{$val["merchantid"]}}">
              <td  data-val="{{ $val["id"] }}" merchant-id="{{$val["merchantid"]}}">{{ $val["id"] }}</td>
              <td class="action-edit"  data-id="{{ $val["id"] }}">{{ $val["account"] }}</td>
              <td>{{ $val["merchantid"] }}</td>
              <td>{{ $val["merchant"] }}</td>
              <td>{{ $val["contact"] }}</td>
              <td>{{ $val["tel"] }}</td>
              <td>{{ $val["address"] }}</td>
              <td>{{ config("category.tob_status")[$val["status"]]["name"]??"未知状态" }}</td>

              <td class="right aligned">
                @if($val["status"]==7)
                <i class="large iconfont-fire iconfire-anquan icon teal action-audit"></i>
                @endif
                <i class="large minus circle icon red action-del"></i>
              </td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="9">
         {!! $data->render() !!}
    </th>
  </tr></tfoot>
</table>

<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加商户</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
      <input id="id" type="hidden" >
        <div class="two fields">
          <div class="field">
          <label>平台账号</label>
          <input id="account" type="text" >
          </div>
          <div class="field">
          <label>平台UID</label>
          <input id="merchantid" type="text" >
          </div>
        </div>
        <div class="three fields">
        <div class="field">
        <label>商户名称</label>
        <input id="merchant" type="text" >
        </div>
        <div class="field">
        <label>联系人</label>
        <input id="contact" type="text" >
        </div>
        <div class="field">
        <label>手机号</label>
        <input id="tel" type="text" >
        </div>
      </div>
      <div class="field">
       <label>商户地址</label>
      <input id="address" type="text" >
      </div>
      <div class="three fields">
        <div class="field">
        <label>提现账号</label>
        <input id="bank_account" type="text" >
        </div>
        <div class="field">
        <label>账号类型</label>
        <select  id="bank_type" class="ui dropdown" >
        <option value="">请选择</option>
        {!! $blade->showHtmlClass('tob_merchats_bank_type','','select') !!}
        </select>
        </div>
        <div class="field">
        <label>提现密码</label>
        <input id="pay_pwd" type="text" >
        </div>
      </div>
    </form>
     <div class="ui bottom attached warning message"></div>
  </div>

  <div class="actions">
    <div class="ui blue button action-save">保存</div>
  </div>
</div>



<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">移除商户</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要移除该商户吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>


<div class="ui modal modal-audit">
  <i class="close icon"></i>
  <div class="header">审核VR游戏</div>
  <div class="content">
  <form class="ui form"  onsubmit="return false;">
  <input id="del_id" type="hidden"  >
  <div class="field">
      <label>审核批注</label>
      <textarea id="passmsg" rows="2"></textarea>
  </div>
  </form>
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="audit(0)">驳回 </div>
  <div class="ui positive button" onclick="audit(1)">通过 </div>
  </div>
</div>
@endsection

@section('javascript')
<script>
var warning_msg = $(".warning.message");
var edit_modal,del_modal,del_id;
var bank_type = $('#bank_type').dropdown();
var loi = new loiForm();


function dataEdit(id) {
  loi.edit("tob_merchats",id,function(data){
      edit_modal = $('.ui.modal.modal-add').modal('show');
      warning_msg.hide();
  });
}

function deleteData(tp) {
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/tob_merchats",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}



$(function(){
  $(".action-save").click(function(){
    var editData = loi.save();
    if(typeof(editData.err) != "undefined") {
      warning_msg.html("还有未填写项目");
      warning_msg.show();
    } else {
      formData = loi.submit();
      permPost("/json/save/tob_merchats",formData,function(data){
        location.reload();
      });
      edit_modal.modal('hide');
    }
  });

  // $("#question_tp").change(function(){
  //  var v = $(this).val();
  //  $.post("/service/feedback_tps",{tp:v},function(res) {
  //     $("#question_sub_tp").empty();
  //     $("#question_sub_tp").append(res.data.html);
  //     question_sub_tp.dropdown('clear');
  //  },"json");
  // });
  $(".action-del").click(function(){
     var that = $(this);
     del_id = that.parent().parent().find('td:first').attr('data-val');
     del_modal = $('.ui.modal.modal-del').modal('show');
  })

  $(".action-edit").click(function(){
     var that = $(this);
     var id = that.attr('data-id');
     dataEdit(id);
  })

  $(".action-audit").click(function() {
    var that = $(this);
    var obj = that.parent().parent().find('td:first');
    audit_id = obj.attr('merchant-id');
    $("#passmsg").val('');
    audit_modal = $('.ui.modal.modal-audit').modal('show');
  })
});

function audit(tp) {
  audit_modal.modal('hide');
  var msg = '';
  if(tp==0) {
    msg = $("#passmsg").val();
  }
  if(audit_id>0) {
    permPost("/json/pass/merchant",{edit_id:audit_id,tp:tp,msg:msg}, function(data){
      $("#merchant-"+audit_id).find(".action-audit").hide();
      if(tp==1) {
         loiMsg("审核成功",function(){location.reload();},"success");
       } else {
         loiMsg("驳回成功",function(){location.reload();},"success");
       }
    });
  }
}

</script>
@endsection
