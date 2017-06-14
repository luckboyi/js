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
<a href="/vrhelp/webgame"><div class="ui basic  button ">返回列表</div></a>
</div>

<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>新建</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="10%">ID</th>
      <th width="10%">游戏ID</th>
      <th width="40%">名称</th>
      <th width="10%">分类</th>
      <th width="20%">链接</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="6" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td  data-val="{{ $val["id"] }}">{{ $val["id"] }}</td>
              <td >{{ $val["gameid"] }}</td>
              <td class="action-edit"  data-id="{{ $val["id"] }}">{{ $val["title"] }}</td>
             <td>{{  $blade->showHtmlClass('webgame_news',$val["tp"]) }}</td>
              <td>{{ $val["link"] }}</td>
              <td class="right aligned"><i  class="large  minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="6">
         {!! $data->render() !!}
    </th>
  </tr></tfoot>
</table>



<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加新闻/攻略</div>
  <div class="content">
    <form class="ui form " onsubmit="return false;">
      <input type="hidden"  id="news_id" >
      <div class="field">
       <label>标题</label>
      <input id="news_title" type="text" >
      </div>
      <div class="field">
      <label>分类</label>
      <select id="news_tp" class="ui dropdown"   >
      <option value="">请选择</option>
      @foreach($tps as $val)
      <option value="{{ $val['id'] }}" >{{ $val['name'] }}</option>
      @endforeach
      </select>
      </div>
      <div class="field">
       <label>链接</label>
      <input id="news_link" type="text" >
      </div>
    </form>
    <div class="ui bottom attached warning message"></div>
  </div>
  <div class="actions">
    <div class="ui button">取消</div>
    <div class="ui button action-save">确定</div>
  </div>
</div>


<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">移除新闻/攻略</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要移除该新闻/攻略吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>

@endsection
@section('javascript')
<script type="text/javascript">
var del_id,del_modal,edit_modal;
var warning_msg = $(".warning.message");
var loi = new loiForm();
var news_tp = $('select.dropdown').dropdown();


function dataEdit(id) {
  loi.edit("webgame_news",id,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
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
    formData.news_gameid = '{{ $appid }}'
   permPost("/json/save/webgame_news",formData,function(data){
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


function deleteData(tp) {
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/webgame_news",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}
</script>
@endsection