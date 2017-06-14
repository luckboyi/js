@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')


<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>


@endsection
@section('content')

<div class="ui basic small buttons">
{!! $blade->showHtmlClass('vronline_comments',$choose,'menu') !!}
</div>
<!-- <div class="ui small button right floated blue" onclick="show()"><i class="plus icon"></i>新建</div> -->
<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索评论内容" value="{{ $searchText }}">
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
      <th width="10%">UID</th>
      <th width="10%">昵称</th>
      <th width="15%">评论内容</th>
      <th width="10%">回复数</th>
      <th width="20%">评论时间</th>
      <th width="10%">状态</th>
      <th width="5%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if(empty($data) && $data->count()<1)
                <tr><td colspan="8" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-val="{{ $val["id"] }}">{{ $val["id"] }}</td>
              <td class="action-edit" data-val="{{ $val["id"] }}">{{ $val["uid"] }}</td>
              <td>{{ $val['nick'] }}</td>
              <td>{{ $val["content"] }}</td>
              <td>{{ $val['reply_num'] }}</td>
              <td>{{ $val["ctime"] }}</td>
              <td class="dev_uid">{!! $blade->showHtmlStat('vronline_comments', $val["status"],'') !!}</td>
              <td class="right aligned">
                <i class="large iconfont-fire iconfire-anquan icon teal action-audit"></i>
                <!-- <i data-id="{{ $val["id"] }}" class="large iconfont-fire  iconfire-gonglve icon blue action-news"></i>
                <i data-stat="{{ $val["status"] }}" data-send="{{ $val["ctime"] }}" class="large  iconfont-fire iconfire-xianshangxianxia icon red action-del"></i> -->
              </td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="8">
         {!! $data->appends(['choose' => $choose,'search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>


<div class="ui modal modal-play">
  <i class="close icon"></i>
  <div class="header"></div>
  <div class="content">

  </div>
  <div class="actions">
  <div class="ui positive button" onclick="audit(3)">关闭 </div>
  </div>
</div>

<div class="ui modal modal-audit">
  <i class="close icon"></i>
  <div class="header">审核评论</div>
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
  <div class="ui negative button" onclick="audit(2)">驳回 </div>
  <div class="ui positive button" onclick="audit(1)">通过 </div>
  </div>
</div>


<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header"></div>
  <div class="content">
  你确定要<label></label>吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
var del_id,del_modal,del_tp,audit_id,audit_modal,modal_play;


$(function(){
  $(".action-del").click(function() {
    var that = $(this);
    var stat = that.attr("data-stat");
    var send_time = parseInt(that.attr("data-send"));
    var tp;
    if (send_time<=0 && stat==5) {
      tp = "上线游戏"
      del_tp = 1;
    } else if (send_time>0) {
      tp = "下线游戏"
      del_tp = 2;
    }
    if (typeof(tp)=="undefined") {
       loiMsg("状态错误 无法操作上下线");
       return
    }
    $(".ui.modal.modal-del .header").html(tp)
    $(".ui.modal.modal-del label").html(tp)
    var obj = that.parent().parent().find("td:first");
    del_id = obj.attr('data-val');
    del_modal = $('.ui.modal.modal-del').modal('show');
  });

  $(".action-audit").click(function() {
    var that = $(this);
    var obj = that.parent().parent().find("td:first");
    audit_id = obj.attr('data-val');
    $("#passmsg").val('');
    audit_modal = $('.ui.modal.modal-audit').modal('show');
  })


  $(".action-edit").click(function() {
    var that = $(this);
    var name = that.text();
    var url = that.attr('data-val');
    $('.modal-play .header').text(name);
    var html ='<video src='+url+' width="900" height="480" controls="controls"></video>';
     $('.modal-play .content').html(html);
    modal_play = $('.ui.modal.modal-play').modal('show');

  })

  $(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/vronline/comments?search="+searchText;
  }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vronline/comments?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vronline/comments";
  });
});


function audit(tp) {
  audit_modal.modal('hide');
  var msg = '';
  if(tp==2) {
    msg = $("#passmsg").val();
  }
  if(audit_id>0) {
    permPost("/json/save/vronline_comments",{id:audit_id,tp:tp,msg:msg}, function(data){
      if(tp==3) {
         loiMsg("审核成功",function(){location.reload();},"success");
       } else {
         loiMsg("驳回成功",function(){location.reload();},"success");
       }
    });
  }
}

function deleteData(tp) {
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/vronline_comments",{del_id:del_id,del_tp:del_tp},function(data){
       // location.reload();
      })
    }
  }
}
</script>
@endsection
