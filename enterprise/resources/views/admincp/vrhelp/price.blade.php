@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')
<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索VR游戏" value="{{ $searchText }}">
  @if($searchText)
  <i class="large remove link icon"></i>
  @else
  <i class="large search link icon"></i>
  @endif
</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="10%">ID</th>
      <th width="25%">名称</th>
      <th width="20%">开发商</th>
      <th width="20%">游戏类型</th>
      <th width="10%">原价(RMB)</th>
      <th width="10%">现价(RMB)</th>
      <th width="5%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="7" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td>{{ $val["appid"] }}</td>
              <td>{{ $val["name"] }}</td>
              <td class="dev_uid">{{  $val["uid"] }}</td>
              <td>{{  $blade->showHtmlClass('vrgame',$val["first_class"]) }}</td>
              <td >{{ $val["original_sell"] }}</td>
              <td >{{ $val["sell"] }}</td>
              <td class="right aligned"><i data-id="{{ $val["appid"] }}" class="large iconfont-fire iconfire-zhifu icon teal action-edit"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="7">
        {!! $data->appends(['search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>




<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">修改定价</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
     <input id="appid" type="hidden"  >
      <div class="field">
       <label>原价</label>
      <input id="original_sell" type="text" >
      </div>
      <div class="field">
       <label>现价</label>
      <input id="sell" type="text" >
      </div>
    </form>
     <div class="ui bottom attached warning message"></div>
  </div>

  <div class="actions">
    <div class="ui blue button action-save">保存</div>
  </div>
</div>

@endsection
@section('javascript')
<script type="text/javascript">

var loi = new loiForm();
var warning_msg = $(".warning.message");
$(function(){


  $(".action-edit").click(function() {
     var id =  $(this).attr("data-id");
     priceEdit(id)
  });
  $(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/vrhelp/price?search="+searchText;
  }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/price?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/price";
  });

  $(".action-save").click(function(){
  var editData = loi.save();
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    formData = loi.submit();
    permPost("/json/save/vrhelp_price",formData,function(data){
      location.reload();
    });
    edit_modal.modal('hide');
  }

});
});

function priceEdit(id) {
  loi.edit("vrhelp_price",id,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
  })
}
</script>
@endsection
