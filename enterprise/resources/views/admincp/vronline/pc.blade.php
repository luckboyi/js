@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')

<div class="ui basic small buttons">
{!! $blade->showHtmlClass('vronline_pc_all',$curClass,'menu') !!}
</div>
<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加评测</div>


<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索文章" value="{{ $searchText }}">
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
      <th width="25%">标题</th>
      <th width="5%">状态</th>
      <th width="20%">分类</th>
      <th width="10%">来源</th>
      <th width="5%">作者</th>
      <th width="15%">创建时间</th>
      <th width="15%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="8" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-val="{{ $val["article_id"] }}">{{ $val["article_id"] }}</td>
              <td class="action-preview" data-id="{{ $val["article_id"] }}">{{ $val["article_title"] }}</td>
              <td id="stat-{{ $val["article_id"] }}" data-val="{{ $val["article_stat"] }}">{{  $blade->showHtmlStat('article',$val["article_stat"]) }}</td>
              <td>{{  $blade->showHtmlClass('vronline_pc',$val["article_category"]) }}</td>
              <td>{{ $val["article_source"] }}</td>
              <td>{{ $val["article_author_id"] }}</td>
               <td>{{ $val["ctime"] }}</td>
              <td class="right aligned"><i class="large check circle icon teal action-audit"></i><i class="large send icon blue action-sub" data-id="{{ $val["article_id"] }}"></i>  <i class="large edit icon @if($val['article_stat']==1) grey @else blue @endif  action-edit" data-id="{{ $val["article_id"] }}"></i><i class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="8">
         {!! $data->appends(['choose' => $curClass,'search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>



<div class="ui modal modal-sub">
  <i class="close icon"></i>
  <div class="header">提交审核</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要将该稿件提交审核吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="subData(0)">取消 </div>
  <div class="ui positive button" onclick="subData(1)">确定 </div>
  </div>
</div>


<div class="ui modal modal-audit">
  <i class="close icon"></i>
  <div class="header">审核新闻</div>
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


<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">移除稿件</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要移除该稿件吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>


@endsection

@section('javascript')
<script type="text/javascript">
var del_id,del_modal,sub_id,sub_modal;


function dataEdit(id) {
  location.href = "/vronline/pcEdit/"+id
}

$(".action-edit").click(function() {
    var id = $(this).attr("data-id");
    var stat = $("#stat-"+id).attr('data-val');
    if(stat==1 ) {
      loiMsg("审核中无法修改");
    } else {
      dataEdit(id)
    }
})

$(".action-preview").click(function() {
    var id = $(this).attr("data-id");
    window.open("/vronline/pcPreview/"+id)
})

$(".action-del").click(function() {
  var that = $(this);
  var obj = that.parent().parent().find("td:first");
  del_id = obj.attr('data-val');
  del_modal = $('.ui.modal.modal-del').modal('show');
});

$(".action-sub").click(function() {
  var that = $(this);
  var obj = that.parent().parent().find("td:first");
  sub_id = obj.attr('data-val');
  sub_modal = $('.ui.modal.modal-sub').modal('show');
});

$(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/vronline/pc?search="+searchText;
  }
});

$(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vronline/pc?search="+searchText;
})

$(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vronline/pc";
})

function subData(tp) {
  if(tp==1) {
    if(sub_id>0) {
      permPost("/json/save/vronline_pc_sub",{id:sub_id},function(res){
      location.reload();
    });
    }
  }
}

function deleteData(tp) {
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/vronline_pc",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}


var audit_modal;
var audit_id;

function audit(tp) {
  audit_modal.modal('hide');
  var msg = '';
  if(tp==0) {
    msg = $("#passmsg").val();
  }
  if(audit_id>0) {
    permPost("/json/pass/vronline_pc",{edit_id:audit_id,tp:tp,msg:msg}, function(data){
      if(tp==1) {
         loiMsg("审核成功",function(){location.reload();},"success");
       } else {
         loiMsg("驳回成功",function(){location.reload();},"success");
       }
    });
  }
}

$(".action-audit").click(function() {
  var that = $(this);
  var obj = that.parent().parent().find("td:first");
  audit_id = obj.attr('data-val');
  $("#passmsg").val('');
  audit_modal = $('.ui.modal.modal-audit').modal('show');
})


</script>
@endsection
