@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')
<div class="ui basic small buttons">
{!! $blade->showHtmlClass('tob_defaultgame',$choose,'menu') !!}
</div>
<!-- <div class="ui small button right floated blue" onclick="show()"><i class="plus icon"></i>添加VR游戏</div> -->
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
      <th width="15%">名称</th>
      <th width="25%">简介</th>
      <th width="15%">开发商</th>
      <th width="15%">游戏类型</th>
      <th width="10%" class="right aligned">添加</th>
      <th width="10%" class="right aligned">默认</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="6" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td  data-val="{{ $val["appid"] }}">{{ $val["appid"] }}</td>
              <td>{{ $val["name"] }}</td>
              <td>{{  $val["content"] }}</td>
              <td class="dev_uid">{{  $val["uid"] }}</td>
              <td>{{  $blade->showHtmlClass('vrgame',$val["first_class"]) }}</td>
              <td class="right aligned"  data-id="{{ $val["appid"] }}">@if($val["tob_in"]>=1) 已添加 <i class="large minus circle icon red action-del"></i>  @else 未添加<i class="large add circle icon teal action-edit"></i> @endif   </td>
              <td class="right aligned"  data-id="{{ $val["appid"] }}">@if($val["tob_in"]==2) 是<i class="large minus circle icon red action-default-del"></i> @else 否<i class="large add circle icon teal action-default-add"></i>  @endif
                </td>
                </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="6">
        {!! $data->appends(['choose' => $choose,'search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>


@endsection
@section('javascript')
<script type="text/javascript">
var del_id,del_modal,del_tp,audit_id,audit_modal;

$(function(){
  $(document).on('click',".action-del",function(){
     var that = $(this);
      var id = that.parent().attr('data-id');
      eidtData(that,'del',id);
  })

  $(document).on('click',".action-edit",function(){
     var that = $(this);
      var id = that.parent().attr('data-id');
      eidtData(that,'add',id);
  });

  $(document).on('click',".action-default-add",function(){
     var that = $(this);
      var id = that.parent().attr('data-id');
      eidtData(that,'default-add',id);
  });

   $(document).on('click',".action-default-del",function(){
     var that = $(this);
      var id = that.parent().attr('data-id');
      eidtData(that,'default-del',id);
  });

  $(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/tob/defaultgame?search="+searchText;
  }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/tob/defaultgame?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/tob/defaultgame";
  });

});


function eidtData(that,tp,id) {
   permPost("/json/save/tob_addgame",{tp:tp,id:id}, function(data){
      if(data.code==0) {
          var html;
          if(tp=='add') {
            html = '已添加 <i class="large minus circle icon red action-del"></i>';
          } else if (tp=='del'){
            html = '未添加<i class="large add circle icon teal action-edit"></i>';
            that.parent().next().html('否<i class="large add circle icon teal action-default-add"></i>');
          } else if (tp=='default-add'){
            html = '是<i class="large minus circle icon red action-default-del"></i> ';
          }  else if (tp=='default-del'){
            html = '否<i class="large add circle icon teal action-default-add"></i> ';
          }
          that.parent().html(html);

       } else {
         loiMsg("处理失败",function(){location.reload();},"success");
       }
    });
}
</script>
@endsection