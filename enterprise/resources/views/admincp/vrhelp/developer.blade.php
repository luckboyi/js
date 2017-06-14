@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
<script type="text/javascript" src="/admincp/public.js"></script>
@endsection


@section('content')
<div class="ui basic small buttons">
{!! $blade->showHtmlClass('developer_stat',$choose,'menu') !!}
</div>


<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索开发者" value="{{ $search }}">
  @if($search)
  <i class="large remove link icon"></i>
  @else
  <i class="large search link icon"></i>
  @endif
</div>


<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="10%">UID</th>
      <th width="15%">名称</th>
      <th width="5%">状态</th>
      <th width="5%">类型</th>
      <th width="15%">证件</th>
      <th width="15%">图片</th>
      <th width="10%">邮箱</th>
      <th width="20%">地址</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="9" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td  data-val="{{ $val["uid"] }}">{{ $val["uid"] }}</td>
              <td>{{ $val["name"] }}</td>
              <td>{{ $blade->showHtmlClass('developer_stat',$val['stat'],'text') }}</td>
              <td>@if($val["type"]==1)  公司 @else 个人 @endif</td>
              <td>{{  $val["idcard"] }}</td>
              <td>{!! $blade->openAppImage($val['uid']) !!}</td>
              <td>{{  $val["email"] }}</td>
              <td>{{  $val["address"] }}</td>
              <td class="right aligned" ><i class="large iconfont-fire iconfire-anquan icon teal action-audit"></i><!-- <i class="large minus circle icon red action-del"></i> --></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="9">
        {!! $data->appends(['choose' => $choose,'search'=>$search])->render() !!}
    </th>
  </tr></tfoot>
</table>



<div class="ui modal modal-audit">
  <i class="close icon"></i>
  <div class="header">审核VR游戏</div>
  <div class="content">
  <form class="ui form"  onsubmit="return false;">
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
var audit_id,modal_audit,modal_edit,modal_pic;

var loi = new loiForm();
$("#support").dropdown();

$(function(){
  // $(".action-del").click(function() {
  //   var that = $(this);
  //   var stat = that.attr("data-stat");
  //   var send_time = parseInt(that.attr("data-send"));
  //   var tp;
  //   if (send_time<=0 && stat==5) {
  //     tp = "上线游戏"
  //     del_tp = 1;
  //   } else if (send_time>0) {
  //     tp = "下线游戏"
  //     del_tp = 2;
  //   }
  //   if (typeof(tp)=="undefined") {
  //      loiMsg("状态错误 无法操作上下线");
  //      return
  //   }
  //   $(".ui.modal.modal-del .header").html(tp)
  //   $(".ui.modal.modal-del label").html(tp)
  //   var obj = that.parent().parent().find("td:first");
  //   del_id = obj.attr('data-val');
  //   del_modal = $('.ui.modal.modal-del').modal('show');
  // });


  $(".action-audit").click(function() {
    var that = $(this);
    var obj = that.parent().parent().find("td:first");
    audit_id = obj.attr('data-val');
    $("#passmsg").val('');
    modal_audit = $('.ui.modal.modal-audit').modal('show');
  })


  $(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/vrhelp/developer?search="+searchText;
  }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/developer?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/developer";
  });
});



function audit(tp) {
  modal_audit.modal('hide');
  var msg = '';
  if(tp==0) {
    msg = $("#passmsg").val();
  }
  if(audit_id>0) {
    permPost("/json/pass/vrhelp_developer",{edit_id:audit_id,tp:tp,msg:msg}, function(data){
      if(tp==1) {
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
      permPost("/json/del/vrhelp_vrgame",{del_id:del_id,del_tp:del_tp},function(data){
       location.reload();
      })
    }
  }
}

</script>
@endsection
