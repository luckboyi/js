@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')

<div class="ui small button right floated blue" onclick="makeCdk()"><i class="plus icon"></i>生成CDK</div>

<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索游戏ID" value="{{ $searchText }}">
  @if($searchText)
  <i class="large remove link icon"></i>
  @else
  <i class="large search link icon"></i>
  @endif
</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="25%">批号</th>
      <th width="20%">游戏ID</th>
      <th width="20%">数量</th>
      <th width="10%">时间</th>
      <th width="5%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="5" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td>{{ $val["batchno"] }}</td>
              <td>{{ $val["itemid"] }}</td>
              <td>{{ $val["total"] }}</td>
              <td >{{ $val["ctime"] }}</td>
              <td class="right aligned"><a href="/vrhelp/cdkDown?batchno={{ $val["batchno"] }}" target="_blank"><i data-id="{{ $val["batchno"] }}" class="large iconfont-fire iconfire-ttpodicon icon teal"></i></a></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="5">
        {!! $data->appends(['search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>




<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">CDK生成</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
      <div class="field">
       <label>类型</label>
      <input id="type" type="text" >
      </div>
      <div class="field">
       <label>游戏ID</label>
      <input id="itemid" type="text" >
      </div>
      <div class="field">
       <label>数量</label>
      <input id="num" type="text" >
      </div>
    </form>
     <div class="ui bottom attached warning message"></div>
  </div>

  <div class="actions">
    <div class="ui blue button action-save">生成</div>
  </div>
</div>

@endsection
@section('javascript')
<script type="text/javascript">

var loi = new loiForm();
var warning_msg = $(".warning.message");
$(function(){
  $(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/vrhelp/cdk?search="+searchText;
  }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/cdk?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/cdk";
  });

  $(".action-save").click(function(){
  var editData = loi.save();
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    formData = loi.submit();
    permPost("/json/save/vrhelp_cdk",formData,function(data){
      location.reload();
    });
    edit_modal.modal('hide');
  }
});
});

function makeCdk() {
  loi.edit("vrhelp_cdk",0,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
  })
}
</script>
@endsection
