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
<div class="ui basic small buttons">
{!! $blade->showHtmlClass('service_question_stat',$curClass,'menu') !!}
</div>

<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>新建问题</div>

<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索问题" value="{{ $searchText }}">
  <i class="large search link icon search-icon"></i>
  @if($searchText)
  <!-- <i class="large remove link icon clear-icon"></i> -->
  @else

  @endif
</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="12%">编号</th>
      <th width="7%">账号</th>
      <th width="36%">问题</th>
      <th width="5%">分类</th>
      <th width="12%">分类</th>
      <th width="5%">姓名</th>
      <th width="20%">联系方式</th>
      <th width="8%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="8" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-val="{{ $val["code"] }}">{{ $val["code"] }}</td>
              <td>{{ $val["account"] }}</td>
              <td>{{ htmlSubStr($val["title"],20) }}</td>
              <td>{{ $blade->showHtmlClass('service_question_tp',$val["tp"]) }}</td>
              <td>{{ $blade->showSubClass('service_question_sub_tp',$val["tp"],$val["sub_tp"]) }}</td>
              <td>{{ $val["name"] }}</td>
              <td><i class="mobile icon"></i>{{ $val["mobile"] }} <i class="qq icon"></i>{{ $val["qq"] }} <i class="mail icon"></i>{{ $val["email"] }}</td>
              <td class="right aligned"><i class="large reply icon  blue action-edit" data-id="{{ $val["id"] }}"></i><i class="large minus circle icon red action-del" onclick="delFeedback('{{$val["code"]}}')"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="8">
         {!! $data->appends(['choose'=>$curClass,'search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>


<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加玩家反馈</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
    	<input id="question_id" type="hidden" >
      <div class="four fields">
    <div class="field">
    <label>用户ID</label>
    <input  id="question_uid" type="text" />
    </div>
    <div class="field">
      <label>用户账号</label>
      <input type="text" id="question_account" >
    </div>
    <div class="field">
      <label>分类</label>
      <select  id="question_tp" class="ui dropdown" >
      <option value="">请选择</option>
      {!! $blade->showHtmlClass('service_question_tp','','select') !!}
      </select>
      </div>
      <div class="field">
      <label>子分类</label>
      <select id="question_sub_tp" class="ui dropdown" >
      <option value="">请选择</option>
      </select>
      </div>
    </div>
    <div class="field">
    <label>问题</label>
    <textarea  id="question_title" rows="3"></textarea>
    </div>
    <div class="inline fields">
        <div class="field">
        <label>截图</label>
        <div class="ui segment">
        <img id="question_screenshots" class="preview ui small image">
        </div>
        </div>
        <div class="field" id="question_screenshots_container">
        <button  class="ui teal  button" id="question_screenshots_browser">选择</button>
        </div>
      </div>
     <div class="four fields">
    <div class="field">
      <label>姓名</label>
      <input type="text" id="question_name" >
    </div>
    <div class="field">
      <label>手机</label>
      <input type="text" id="question_mobile" maxlength="11" >
    </div>
    <div class="field">
      <label>QQ</label>
      <input type="text" id="question_qq"  >
    </div>
    <div class="field">
      <label>邮箱</label>
      <input type="email" id="question_email" >
    </div>
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
var warning_msg = $(".warning.message");
var edit_modal;
var question_tp = $('#question_tp').dropdown();
var question_sub_tp = $('#question_sub_tp').dropdown();
var question_screenshots_obj;
var loi = new loiForm();

function dataEdit(id) {
  loi.edit("service_feedback",id,function(data){
      edit_modal = $('.ui.modal.modal-add').modal('show');
      warning_msg.hide();
      if(typeof(question_screenshots_obj)=="undefined") {
        question_screenshots_obj = new loiUploadContainer({
        container:"question_screenshots_container",
        choose:"question_screenshots_browser",
        ext:"jpg,png",
        upload:{tp:"faqimg",success:function(json){
          var jsonResult = $.parseJSON(json);
          var path = jsonResult.data.fileid;
           $("#question_screenshots").attr('src',img_domain+path);
           $("#question_screenshots").attr('data-val',path);
        },error:function(){}},
          filesAdd:function(files){
           // console.log(files)
          }
        });
      }
  });
}

$(function(){

  $(".action-save").click(function(){
    var editData = loi.save();
    if(typeof(editData.err) != "undefined") {
      warning_msg.html("还有未填写项目");
      warning_msg.show();
    } else {
      formData = loi.submit();
      console.log(formData);
      permPost("/json/save/service_feedback",formData,function(data){
        location.reload();
      });
      edit_modal.modal('hide');
    }
  });

  $("#question_tp").change(function(){
   var v = $(this).val();
   $.post("/service/feedback_tps",{tp:v},function(res) {
      $("#question_sub_tp").empty();
      $("#question_sub_tp").append(res.data.html);
      question_sub_tp.dropdown('clear');
   },"json");
  });

  $(".action-edit").click(function(){
     var that = $(this);
     var obj = that.parent().parent().find("td:first");
    var code = obj.attr('data-val');
    location.href= "/service/feedbackInfo/"+code
  });
  $(".search-icon").click(function(){
    var that = $(this);
    var searchKey = $(".action-search").val();
    if(!searchKey || searchKey == '搜索问题') {
      alert("请输入问题内容或问题编号。");
    } else {
      location.href= "/service/feedback/?search="+searchKey;
    }
  });
  $(".clear-icon").click(function(){
    $(".action-search").val('');
  })
});
function delFeedback(code) {
    $.post("/service/feedbackDel",{code:code},function(res) {
      if(res.code === 0) {
        location.reload();
      }
    },"json");
  }

</script>
@endsection
