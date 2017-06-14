@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<link rel="stylesheet" type="text/css" href="/admincp/editor/css/editor.min.css">
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/swfobject.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
<script language="JavaScript" src="/admincp/editor/js/editor.min.js?v=1"></script>
@endsection

@section('content')
<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>新建QA</div>

<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索QA" value="{{ $searchText }}">
  @if($searchText)
  <i class="large remove link icon"></i>
  @else
  <i class="large search link icon"></i>
  @endif
</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="5%">ID</th>
      <th width="15%">分类</th>
      <th width="20%">问题</th>
      <th width="40%">答案</th>
      <th width="10%">编辑时间</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="6" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-val="{{ $val["id"] }}">{{ $val["id"] }}</td>
              <td>{{  $blade->showHtmlClass('service_qa',$val["tp"]) }}</td>
              <td>{{ $val["question"] }}</td>
              <td>{{ htmlSubStr($val["answer"],20) }}</td>
              <td>{{ $val["ltime"] }}</td>
              <td class="right aligned"><i class="large edit icon  blue action-edit" data-id="{{ $val["id"] }}"></i><i class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="6">
         {!! $data->appends(['search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>



<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加QA</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
    	<input id="qa_id" type="hidden" >

		<div class="field">
		<label>问题</label>
		<textarea  id="qa_question" rows="2"></textarea>
		</div>
		<div class="field">
		<label>分类</label>
		<select id="qa_tp" class="ui dropdown"   >
		<option value="">请选择</option>
		@foreach($qaTps as $val)
		<option value="{{ $val['id'] }}" >{{ $val['name'] }}</option>
		@endforeach
		</select>
        </div>
		<div class="field">
		<div id="qa_answer" style="height:400px;"></div>
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
  <div class="header">移除QA</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要移除该QA吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>

<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="/admincp/Somethingtest.swf" style="visibility: visible;"></object>
@endsection


@section('javascript')
<script type="text/javascript">
var del_id,del_modal,edit_modal;
var warning_msg = $(".warning.message");
var loi = new loiForm();
var qa_tp = $('select.dropdown').dropdown();
var qa_answer = new wangEditor('qa_answer');
edit_modal = $('.ui.modal.modal-add').modal({closable:false});

function dataEdit(id) {
	loi.edit("service_qa",id,function(data){
		edit_modal.modal('show');
		warning_msg.hide();
	});
}

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
   permPost("/json/save/service_qa",formData,function(data){
      location.reload();
   });
    edit_modal.modal('hide');
  }
});


$(".action-del").click(function() {
  var that = $(this);
  var obj = that.parent().parent().find("td:first");
  del_id = obj.attr('data-val');
  del_modal = $('.ui.modal.modal-del').modal('show');
});


$(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/service/qa?search="+searchText;
  }
});

$(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/service/qa?search="+searchText;
})

$(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/service/qa";
})

$(function(){
	 qa_answer.config.menus = [
        'source',
        '|',
        'bold',
        'underline',
        'italic',
        'strikethrough',
        'eraser',
        'forecolor',
        'bgcolor',
        '|',
        'quote',
        'fontfamily',
        'fontsize',
        'head',
        'unorderlist',
        'orderlist',
        'alignleft',
        'aligncenter',
        'alignright',
        '|',
        'link',
        'unlink',
        'table',
        'emotion',
        'img',
        'video',
        'insertcode',
        'undo',
        'redo',
        'fullscreen'
    ];
    qa_answer.config.uploadImgUrl = '/upload';
    qa_answer.config.uploadParams = {cos:true,tp:'faqimg'};
    qa_answer.create();
})

function deleteData(tp) {
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/service_qa",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}
</script>
@endsection