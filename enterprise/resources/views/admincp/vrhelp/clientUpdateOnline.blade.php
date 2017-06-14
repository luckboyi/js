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
<a href="/vrhelp/client"><div class="ui basic  button ">完整包</div></a>
<a href="/vrhelp/clientup"><div class="ui basic  button blue">更新包</div></a>
</div>
<div class="ui small button right floated blue" onclick="videEdit(0)"><i class="plus icon"></i>添加版本</div>
<div class="ui small button right floated blue" id='versionPublic'><i class="plus icon"></i>发布版本</div>
<div class="ui small button right floated blue" id='alreadyPublicBtn'><i class="plus icon"></i>查看已发布版本</div>
<div id="publishShow" class="hide">
<table  class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="10%" title="版本号">版本号</th>
      <th width="10%" title="在线包大小">在线包大小</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($publish) && !empty($publish))
      <tr>
        <td>{{ $publish['version'] }}</td>
        <td>{{ $publish['size'] }}</td>
      </tr>
    @else
       <tr><td colspan="2" class="center aligned">暂无数据</td></tr>
    @endif
    <tfoot>
    <tr><th colspan="2">
    </th>
  </tr></tfoot>
  </tbody>
  </table>
</div>

<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="3%">ID</th>
      <th width="5%" title="版本号">版本号</th>
      <th width="5%" title="在线包大小">在线包大小</th>
      <th width="10%" title="版本状态">版本状态</th>
      <th width="10%" title="创建时间">创建时间</th>
      <th width="5%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="6" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-key="client_id" class="action-edit" data-id="{{ $val["id"] }}">{{ $val["id"] }}</td>
              <td data-key="version" data-version="{{ $val["version"] }}">{{ $val["version"] }}</td>
              <td data-key="online_size" >{{ $val['online_size'] }}</td>
              <td data-key="status">
                <select  name="vstatus" class="selection dropdown" data-id="{{ $val['id'] }}" id="status{{ $val['id'] }}" onchange="gradeChange($(this).attr('data-id'), $(this).attr('id'))">
                  @if($val['status'] === 0)
                      <option selected value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;新版本&nbsp;&nbsp;</option>
                      <option value="1">&nbsp;&nbsp;&nbsp;发布版本&nbsp;&nbsp;</option>
                      <option value="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;旧版本&nbsp;&nbsp;&nbsp;</option>
                  @elseif($val['status'] === 1)
                      <option selected value="1">&nbsp;&nbsp;&nbsp;发布版本&nbsp;&nbsp;</option>
                  @elseif($val['status'] === 2)
                      <option selected value="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;旧版本&nbsp;&nbsp;</option>
                      <option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;新版本&nbsp;&nbsp;</option>
                  @endif
                </select>
              </td>
              <td data-key="createtime">{{ $val['ctime'] }}</td>
              <td class="right aligned"><i class="large minus circle icon red action-del"></i></td>
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

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">下架版本</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要删除该版本吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>

<div class="ui modal modal-public">
  <i class="close icon"></i>
  <div class="header">发布版本</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  <table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="25%" title="版本状态">版本状态</th>
      <th width="25%" title="版本号">版本号</th>
      <th width="25%" title="更新包大小">更新包大小</th>
    </tr>
  </thead>
  <tbody id="publicInfo">
  </tbody>
  </table>
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="publicVersion(0)">取消 </div>
  <div class="ui positive button" onclick="publicVersion(1)">确定 </div>
  </div>
</div>


<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加新版本</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
     <input id="id" type="hidden"  >
      <div class="field">
       <label>版本号</label>
      <input id="version" type="text" placeholder="填写版本号">
      </div>
      <div class="field">
       <label>在线包大小</label>
      <input id="online_size" type="text" placeholder="填写在线包大小（单位：MB）">
      </div>
      <div class="field hide">
       <label>版本状态(0:新添加，未发布;1:新版本;2:稳定版本;4:废弃的新版本;9:老的稳定版本，不升级到该版本)</label>
      <input id="status" type="text" placeholder="填写数字，0:新添加，未发布;1:发布版本;2:老版本;">
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
var updtype = $('#multi-select').dropdown();
var edit_modal;
var del_modal;
var warning_msg = $(".warning.message");
var progress = $('#example1').progress();
var defaultImg = "semantic/images/image.png";
var video_cover_obj,video_link_obj;
var totalSize;
var del_id;


var loi = new loiForm();
$(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   videEdit(id)
});

$(".action-save").click(function(){
  var editData = loi.save();
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    formData = loi.submit();
    $.post("/json/save/online_client",formData,function(data){
      location.reload();
    });
    edit_modal.modal('hide');
  }

});


function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      $.post("/json/del/online_client",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}

$(".action-del").click(function() {
  var that = $(this);
  var obj = that.parent().parent().find("td:first");
  del_id = obj.attr('data-id');
  del_modal = $('.ui.modal.modal-del').modal('show');
})

$("#versionPublic").click(function() {
   $.post("{{ url('client/databaseUpPublic') }}",{},function(data){
        console.log(data.data);
        var html = '';
        var ary = data.data;
        for (var k in ary) {
                var status = '发布版本';

                html += '<tr><td>'+ status +'</td><td>'+ary[k][0]["version"]+'</td><td>'+ary[k][0]["online_size"]+'MB</td></tr>';
            }
        $('#publicInfo').html(html);
      },"json");
  public_modal = $('.ui.modal.modal-public').modal('show');
})
function publicVersion(tp) {
  public_modal.modal('hide');
  if(tp==1) {
    $.post("{{ url('client/versionUpPublic') }}",{},function(data){
      console.log(data);
      data = eval("("+data+")");
      if (data.code != 0) {
        alert(data.msg);
      }
      location.reload();
    })
  }
}



function videEdit(id) {
  $(".start.button").prop('disabled',false);
  loi.edit("online_client",id,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
  })
}

//判断下拉select事件=>并修改其版本状态
function gradeChange(id, class_id){
    var selected = $('#' + class_id).val();
    var msg = "";
    if (selected == 1) {
        msg = "如改为新版本，其他的发布版本将同时改为老版本，是否继续";
    }
    if (msg && confirm(msg)) {
        $.post("/json/update/online_client",{id:id, status:selected},function(data){
            console.log(data);
        });
    } else {
        if (!msg) {
            $.post("/json/update/online_client",{id:id, status:selected},function(data){
                console.log(data);
            });
        }
    }
    location.reload();
}

$("#alreadyPublicBtn").click(function(){
  $("#publishShow").toggleClass("hide");
});
</script>
@endsection
