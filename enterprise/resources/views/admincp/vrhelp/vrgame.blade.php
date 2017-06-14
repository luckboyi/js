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
{!! $blade->showHtmlClass('all_stat',$choose,'menu') !!}
</div>

 <select id="support" class="ui selection dropdown">
{!! $blade->showHtmlClass('support',$support,'select') !!}
</select>

<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加VR游戏</div>

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
      <th width="5%">ID</th>
      <th width="15%">名称</th>
      <th width="10%">开发商</th>
      <th width="15%">游戏类型</th>
       <th width="25%">支持设备</th>
      <th width="10%">状态</th>
      <th width="5%">大小</th>
      <th width="15%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if($data->count()<1)
                <tr><td colspan="8" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td  data-val="{{ $val["appid"] }}">{{ $val["appid"] }}</td>
              <td class="action-edit" data-id="{{ $val["appid"] }}" >{{ $val["name"] }}</td>
              <td class="dev_user" data-val="{{  $val["uid"] }}">{{  $val["uid"] }}</td>
              <td>{{  $blade->showHtmlClass('vrgame',$val["first_class"]) }}</td>
              <td>{{  $blade->showHtmlClass('vr_device',$val["support"]) }}</td>
              <td>{{  $blade->showHtmlStat('game',$val["stat"],$val["send_time"]) }}</td>
              <td>{{ gameSize($val["client_size"]) }}</td>
              <td class="right aligned"  data-id="{{ $val["appid"] }}"><i class="large iconfont-fire iconfire-tupian icon blue action-pic"></i><i class="large iconfont-fire iconfire-agreement icon blue action-copyright"></i><i class="large iconfont-fire iconfire-banbenguanli icon blue action-version"></i><i class="large iconfont-fire iconfire-anquan icon teal action-audit"></i><i data-stat="{{ $val["stat"] }}" data-send="{{ $val["send_time"] }}" class="large  iconfont-fire iconfire-xianshangxianxia icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="8">
        {!! $data->appends(['choose' => $choose,'support' => $support,'search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>



<div class="ui modal modal-edit">
  <i class="close icon"></i>
  <div class="header">添加游戏</div>
  <div class="content">
        <form class="ui form" onsubmit="return false;">
        <input id="game_id" type="hidden">
        <div class="three fields">
            <div class="field">
                <label>游戏名称</label>
                <input id="game_name" type="text">
            </div>
            <div class="field">
                <label>游戏标签</label>
                <input id="game_tag" type="text">
            </div>
            <div class="field">
                <label>开发商UID</label>
                <input id="game_uid" type="text">
            </div>
        </div>
        <div class="field">
            <label>游戏分类</label>
            <select id="game_class" class="ui selection dropdown" multiple="">
                <option value="">请选择</option>
                {!! $blade->showHtmlClass('vrgame','','select') !!}
            </select>
        </div>
        <div class="field">
            <label>支持设备</label>
            <select id="game_device" class="ui selection dropdown" multiple="">
                <option value="">请选择</option>
                {!! $blade->showHtmlClass('vr_device','','select') !!}
            </select>
        </div>
        <div class="field">
            <label>支持配件</label>
            <select id="game_mountings" class="ui selection dropdown" multiple="">
                <option value="">请选择</option>
                {!! $blade->showHtmlClass('vr_mountings','','select') !!}
            </select>
        </div>
        <div class="field">
            <label>游戏简介</label>
            <textarea id="game_intro" rows="2"></textarea>
        </div>
        <div class="four fields">
            <div class="field">
                <label>原价</label>
                <input id="game_original_sell" type="text">
            </div>
            <div class="field">
                <label>现价</label>
                <input id="game_sell" type="text">
            </div>
            <div class="field">
                <label>Oculus</label>
                <input id="game_oculus" type="text">
            </div>
            <div class="field">
                <label>游戏大小（M）</label>
                <input id="game_size" type="text">
            </div>
        </div>
        <div class="three fields">
            <div class="field">
                <label>系统</label>
                <select id="game_recommend_system" class="ui selection dropdown">
                    <option value="">请选择</option>
                    {!! $blade->showHtmlClass('vr_device_system','','select') !!}
                </select>
            </div>
            <div class="field">
                <label>CPU</label>
                <select id="game_recommend_cpu" class="ui selection dropdown">
                    <option value="">请选择</option>
                    {!! $blade->showHtmlClass('vr_device_cpu','','select') !!}
                </select>
            </div>
            <div class="field">
                <label>内存</label>
                <select id="game_recommend_memory" class="ui selection dropdown">
                    <option value="">请选择</option>
                    {!! $blade->showHtmlClass('vr_device_memory','','select') !!}
                </select>
            </div>
        </div>
        <div class="two fields">
            <div class="field">
                <label>Directx</label>
                <select id="game_recommend_directx" class="ui selection dropdown">
                    <option value="">请选择</option>
                    {!! $blade->showHtmlClass('vr_device_directx','','select') !!}
                </select>
            </div>
            <div class="field">
                <label>显卡</label>
                <select id="game_recommend_graphics" class="ui selection dropdown">
                    <option value="">请选择</option>
                    {!! $blade->showHtmlClass('vr_device_graphics','','select') !!}
                </select>
            </div>
        </div>
        <div class="three fields">
            <div class="field">
                <label>语言</label>
                <input id="game_language" type="text">
            </div>
            <div class="field">
                <label>制作公司</label>
                <input id="game_product_com" type="text">
            </div>
            <div class="field">
                <label>发行公司</label>
                <input id="game_issuing_com" type="text">
            </div>
        </div>
    </form>
  </div>
  <div class="actions">
  <div class="ui blue button action-save">保存</div>
  </div>
</div>


<div class="ui large modal modal-pic">
  <i class="close icon"></i>
  <div class="header">审核VR游戏</div>
  <div class="content">
       <form class="ui form"  onsubmit="return false;">
        <div class="three fields">
            <div class="inline fields">
                <div class="field">
                    <label>游戏LOGO</label>
                    <i class="desc"> 310x180 JPG,PNG </i>
                    <div class="ui segment">
                        <img id="game_logo" class="preview ui small image">
                    </div>
                </div>
                <div class="field" id="game_logo_container">
                    <button class="ui teal  button" id="game_logo_browser">选择</button>
                </div>
            </div>
            <div class="inline fields">
                <div class="field">
                    <label>游戏ICON</label>
                    <i class="desc">128*128 png</i>
                    <div class="ui segment">
                        <img id="game_icon" class="preview ui tiny image">
                    </div>
                </div>
                <div class="field" id="game_icon_container">
                    <button class="ui teal  button" id="game_icon_browser">选择</button>
                </div>
            </div>
            <div class="inline fields">
                <div class="field">
                    <label>游戏库</label>
                    <i class="desc"> 158*210 JPG,PNG </i>
                    <div class="ui segment">
                        <img id="game_rank" class="preview ui small image">
                    </div>
                </div>
                <div class="field" id="game_rank_container">
                    <button class="ui teal  button" id="game_rank_browser">选择</button>
                </div>
            </div>
        </div>
        <div class="inline fields">
            <div class="field">
                <label>背景图片</label>
                <i class="desc"> 1920*1080 JPG,PNG </i>
                <div class="ui segment small images">
                <img id="game_bg" class="preview ui small image">
                </div>
            </div>
            <div class="field" id="game_bg_container">
                <button class="ui teal  button" id="game_bg_browser">选择</button>
            </div>
        </div>
        <div class="inline fields">
            <div class="field">
                <label>轮播图片</label>
                <i class="desc"> 610*340  JPG,PNG </i>
                <div class="ui segment small images" id="game_slider">
                </div>
            </div>
            <div class="field" id="game_slider_container">
                <button class="ui teal  button" id="game_slider_browser">选择</button>
            </div>
        </div>
    </form>
  </div>
  <div class="actions">
  <div class="ui blue button action-save">保存 </div>
  </div>
</div>


<div class="ui modal modal-audit">
  <i class="close icon"></i>
  <div class="header">审核VR游戏</div>
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
var del_id,del_modal,del_tp,audit_id,modal_audit,modal_edit,modal_pic;

var loi = new loiForm();
$("#support").dropdown();

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

  $("#support").change(function(){
      var support_val = $(this).val();
      var choose = getUrlVar('choose');
      choose = choose?choose:-1;
      location.href = '/vrhelp/vrgame/?choose='+choose+'&support='+support_val;
  })

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
     location.href = "/vrhelp/vrgame?search="+searchText;
  }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/vrgame?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/vrgame";
  });

  $(".action-edit").click(function() {
      var id = $(this).attr("data-id");
      dataEdit(id)
  })

  $(".action-pic").click(function() {
      var id = $(this).parent().attr("data-id");
      dataEdit(id,'pic')
  })


  $(".action-save").click(function() {
    var editData = loi.save();
    if(typeof(editData.err) != "undefined") {
      loiMsg(editData.err+"未填写");
    } else {
      formData = loi.submit();
      permPost("/json/save/vrhelp_vrgame",formData,function(res){
        if(res.code==0) {
          if(typeof(formData.game_bg)!="undefined") {
             modal_pic.modal('hide');
           } else {
            modal_edit.modal('hide');
           }
          location.reload();
        } else {
          loiMsg(res.msg);
        }
      });
    }
  })

  $(".action-version").click(function() {
    var id = $(this).parent().attr("data-id");
    location.href= '/vrhelp/vrgame/version/'+id
  })

});



function dataEdit(id,tp) {

  loi.edit("vrhelp_vrgame",{id:id,tp:tp},function(data){
    var appid = data.game_id.val;
    if(tp == "pic") {
      var game_logo_obj = new loiUploadContainer({
          id:"game_logo",
          upload:{
              tp:"vrgameimg",addParams:{id:appid,"assign":"logo"},
              error:function(){}
          }
      });

      var game_icon_obj = new loiUploadContainer({
          id:"game_icon",
          upload:{
              tp:"vrgameimg",addParams:{id:appid,"assign":"icon"},
              error:function(){}
          }
      });

      var game_rank_obj = new loiUploadContainer({
          id:"game_rank",
          upload:{
              tp:"vrgameimg",addParams:{id:appid,"assign":"rank"},
              error:function(){}
          }
      });


      var game_bg_obj = new loiUploadContainer({
          id:"game_bg",
          upload:{
              tp:"vrgameimg",addParams:{id:appid,"assign":"bg"},
              error:function(){}
          }
      });

      var game_slider_obj = new loiUploadContainer({
          id:"game_slider",
          upload:{
             tp:"vrgameimg",multi:true,addParams:{id:appid},
             error:function(){}
          }
      });

      modal_pic =  $('.ui.modal.modal-pic').modal('show');

    } else {
      modal_edit =  $('.ui.modal.modal-edit').modal('show');
    }

  });
}

function audit(tp) {
  modal_audit.modal('hide');
  var msg = '';
  if(tp==0) {
    msg = $("#passmsg").val();
  }
  if(audit_id>0) {
    permPost("/json/pass/vrhelp_vrgame",{edit_id:audit_id,tp:tp,msg:msg}, function(data){
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
