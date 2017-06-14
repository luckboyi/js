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
<a href="/vrhelp/client"><div class="ui basic  button blue">完整包</div></a>
<a href="/vrhelp/clientup"><div class="ui basic  button ">更新包</div></a>
</div>
<div class="ui small button right floated blue" onclick="videEdit(0)"><i class="plus icon"></i>添加版本</div>
<div class="ui small button right floated blue" id='versionPublic'><i class="plus icon"></i>发布版本</div>
<div class="ui small button right floated blue" id='alreadyPublicBtn'><i class="plus icon"></i>查看已发布版本</div>
<div id="publishShow" class="hide">
<table  class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="10%" title="版本号">版本号</th>
      <th width="10%" title="发布时间">发布时间</th>
      <th width="30%" title="更新说明">更新说明</th>
      <th width="15%" title="完整包大小">完整包大小</th>
      <th width="15%" title="在线包大小">在线包大小</th>
      <th width="10%" title="推送数量">推送数量</th>
      <th width="20%" title="更新方式">更新方式</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($publish) && !empty($publish))
      @foreach($publish as $info)
        @if(count($info) > 0)
          <tr>
            <td>{{ $info['version'] }}</td>
            <td>{{ $info['pushtime'] }}</td>
            <td>{{ $info['newfeature'] }}</td>
            <td>{{ $info['whole_size'] }}</td>
            <td>{{ $info['online_size'] }}</td>
            <td>{{ $info['pushnum'] }}</td>
            <td>{!! $blade->getUpdtype($info['updtype']) !!}</td>
          </tr>
        @endif
      @endforeach
    @else
       <tr><td colspan="7" class="center aligned">暂无数据</td></tr>
    @endif
    <tfoot>
    <tr><th colspan="7">
    </th>
  </tr></tfoot>
  </tbody>
  </table>
</div>

<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="4%">ID</th>
      <th width="5%" title="版本号">版本号</th>
      <th width="10%" title="发布时间">发布时间</th>
      <th width="25%" title="更新说明">更新说明</th>
      <th width="8%" title="完整包大小">完整包大小</th>
      <th width="8%" title="在线包大小">在线包大小</th>
      <th width="5%" title="推送数量">推送数量</th>
      <th width="5%" title="更新方式">更新方式</th>
      <th width="10%" title="版本状态">版本状态</th>
      <th width="10%" title="创建时间">创建时间</th>
      <th width="8%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="11" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-key="client_id" class="action-edit" data-id="{{ $val["id"] }}">{{ $val["id"] }}</td>
              <td data-key="version" data-version="{{ $val["version"] }}">{{ $val["version"] }}</td>
              <td data-key="pushtime" >{{ $val["pushtime"] }}</td>
              <td data-key="newfeature" >{{ $val["newfeature"] }}</td>
              <td data-key="whole_size" >{{ $val["whole_size"] }}</td>
              <td data-key="online_size" >{{ $val['online_size'] }}</td>
              <td data-key="pushnum" >{{ $val["pushnum"] }}</td>
              <td data-key="updtype">{!! $blade->getUpdtype($val['updtype']) !!}</td>
              <td data-key="status">
                <select  name="vstatus" class="selection dropdown" data-id="{{ $val['id'] }}" id="status{{ $val['id'] }}" onchange="gradeChange($(this).attr('data-id'), $(this).attr('id'))">
                  @if($val['status'] === 1)
                      <option selected value="1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;新版本&nbsp;&nbsp;&nbsp;</option>
                      <option value="2">&nbsp;&nbsp;&nbsp;&nbsp;稳定版本&nbsp;&nbsp;&nbsp;</option>
                      <option value="4">废弃的新版本</option>
                  @elseif($val['status'] === 2)
                      <option selected value="2">&nbsp;&nbsp;&nbsp;&nbsp;稳定版本&nbsp;&nbsp;&nbsp;</option>
                      <option value="1">&nbsp;&nbsp;&nbsp;&nbsp;新版本&nbsp;&nbsp;&nbsp;</option>
                  @elseif($val['status'] === 4)
                      <option selected value="4">废弃的新版本</option>
                      <option value="1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;新版本&nbsp;&nbsp;&nbsp;</option>
                  @else
                      <option selected value="9">老的稳定版本</option>
                      <option value="2">&nbsp;&nbsp;&nbsp;&nbsp;稳定版本&nbsp;&nbsp;&nbsp;</option>
                  @endif
                </select>
              </td>
              <td data-key="createtime">{{ $val['ctime'] }}</td>
              <td class="right aligned"><i data-id="{{ $val["version"] }}" class="action-rsync"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="11">
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
      <th width="25%" title="更新方式">更新方式</th>
      <th width="25%" title="推送人数">推送人数</th>
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
      <label>发布时间</label>
      <input id="pushtime" type="text">
      </div>
      <div class="field">
      <label>版本说明</label>
      <textarea id="newfeature" rows="2" placeholder='版本说明内容'></textarea>
      </div>
      <div class="field">
        <label>更新方式</label>
        <select  name="updtype" class="ui selection dropdown" id="updtype">
          <option value="">请选择</option>
          <option selected value="force">强制更新</option>
          <option  value="silence">静默更新</option>
          <option  value="normal">普通更新</option>
        </select>
      </div>

      <div class="field">
       <label>完整包大小</label>
      <input id="whole_size" type="text" placeholder="填写完整包大小（单位：MB）">
      </div>

      <div class="field">
       <label>在线包大小</label>
      <input id="online_size" type="text" placeholder="填写在线包大小（单位：MB）">
      </div>

      <div class="field">
       <label>推送数量</label>
      <input id="pushnum" type="text" placeholder="填写数字">
      </div>
      <div class="field hide">
       <label>版本状态(0:新添加，未发布;1:新版本;2:稳定版本;4:废弃的新版本;9:老的稳定版本，不升级到该版本)</label>
      <input id="status" type="text" placeholder="填写数字，0:新添加，未发布;1:新版本;2:稳定版本;4:废弃的新版本;9:老的稳定版本，不升级到该版本;">
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
  console.log(editData);
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    formData = loi.submit();
    $.post("/json/save/product_client",formData,function(data){
      console.log(data);
      location.reload();
    });
    edit_modal.modal('hide');
  }

});


function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      $.post("/json/del/product_client",{del_id:del_id},function(data){
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
   $.post("{{ url('client/databasePublic') }}",{},function(data){
        // console.log(data.data);
        var html = '';
        var ary = data.data;
        console.log(ary);
        for (var k in ary) {
                if(ary[k].length < 1) {
                  continue;
                }
                var status = '新版本';
                if(k == 1) {
                  status = '稳定版本';
                }
                var updtype = '强制更新';
                if(ary[k][0]["updtype"] == 'silence') {
                  updtype = '静默更新';
                }else if(ary[k][0]["updtype"] == 'normal'){
                  updtype = '普通更新';
                }
                html += '<tr><td>'+ status +'</td><td>'+ary[k][0]["version"]+'</td><td>'+updtype+'</td><td>'+ary[k][0]["pushnum"]+'</td></tr>';
            }
        $('#publicInfo').html(html);
      },"json");
  public_modal = $('.ui.modal.modal-public').modal('show');
})
function publicVersion(tp) {
  public_modal.modal('hide');
  if(tp==1) {
    $.post("{{ url('client/versionPublic') }}",{},function(data){
      console.log(data);
      // data = eval("("+data+")");
      if (data.code != 0) {
        alert(data.msg);
      }
      location.reload();
    })
  }
}



function videEdit(id) {
  $(".start.button").prop('disabled',false);
  loi.edit("product_client",id,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
  })
}

//判断下拉select事件=>并修改其版本状态
function gradeChange(id, class_id){
    var selected = $('#' + class_id).val();
    var msg = "";
    if (selected == 1) {
        msg = "如改为新版本，其他的新版本将同时改为废弃版本，是否继续";
    }else if (selected == 2) {
        msg = "如改为稳定版本，其他的稳定版本将同时改为历史版本，是否继续";
    }
    if (msg && confirm(msg)) {
        $.post("/json/update/product_client",{id:id, status:selected},function(data){
            console.log(data);
        });
    }else {
        if (!msg) {
            $.post("/json/update/product_client",{id:id, status:selected},function(data){
                console.log(data);
            });
        }
    }
    location.reload();
}

$("#alreadyPublicBtn").click(function(){
  $("#publishShow").toggleClass("hide");
});

var cdn_stat_url = "http://192.168.75.252/stat.php";
var codeHtml = {0:'未同步',10:'检查xml中',11:'xml检查成功',12:'xml检查失败',20:'同步中',21:'同步成功',22:'同步失败'}
$(function(){
  $.ajax(
    {
      url:cdn_stat_url+'?func=ping',
      timeout:1000,
      error:function(){
        console.log("offline");
      },
      success:function(){
        loadCdnStat()
      }
  });
  $("i.action-rsync").click(function(){
      var thatClass = $(this).attr('class');
      var that = $(this);
      var version = that.attr('data-id');
      if(thatClass.indexOf('lock')>=0) {
        loiMsg("进行中 请稍后");
        return;
      } else {
        $.get(cdn_stat_url+'?func=shell&mode=client&name='+version,function(data) {
          showCdnStat(that,version);
          checkCdnStat(that,version);
        });
      }
  })
});

function loadCdnStat() {
  $.each($("i.action-rsync"),function(a,b){
    var that = $(b)
    var version = that.attr('data-id');
    showCdnStat(that,version)
  })
}

function showCdnStat(that,name,callback) {
   $.get(cdn_stat_url+'?func=getStat&mode=client&name='+name,function(data) {
      if(data.res == 0) {
        that.removeClass('lock');
      } else if (data.res == 20) {
        that.addClass('lock');
      } else if (data.res == 21) {
        that.removeClass('lock');
      } else if (data.res == 22) {
        that.removeClass('lock');
      }
      that.html(codeHtml[data.res]);

      if(data.res==21 || data.res==22) {
         if(typeof(callback)=="function") {
            callback(data.res);
         }
      }
    },"json");
}

function checkCdnStat(that,name) {
  var loop = setInterval(function() {
       showCdnStat(that,name,function(code){
          clearInterval(loop);
          if(typeof(Notification)!="undefined") {
              new Notification('客户端版本'+name, {
                  body: " "+codeHtml[code],
              });
          }
       });
  },10000);
}

</script>
@endsection
