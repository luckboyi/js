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



<!-- <h4 class="ui top attached block header">
    兽人塔防 <a class="ui red tag label game-title">未上线</a>
  </h4>
  <div class="ui bottom attached segment">

  <div class="ui segment"> <span class="appinfo">APP ID: <i>1000377</i></span>  <span class="appinfo">APP KEY: <i>Zl15gs3QB4mPiObzvpGcjYyCkKNicDCp</i></span>  <span class="appinfo">PAY KEY: <i>z_ySpPOEAcYPiK4nATDw1ouaL-Kajq6L</i></span> <span class="text-copy"><i class="small iconfont-fire iconfire-fuzhi icon   action-audit"></i>点击复制</span></div>
    <div class="ui link cards" style="margin-top: 2em">
        <div class="card" style="box-shadow:none;width: 300px">
            <div class="content" style="text-align: center;">
                <i class="great iconfont-fire iconfire-jibenxinxi icon   action-audit"></i>
                <div class="header" style="margin:1.2em 0 0.5em 0;font-size: 2em;color:#4d4d4d">基本信息</div>
                <div class="description">产品基础信息，包括名称分类等，上线后无法修改</div>
            </div>
        </div>
        <div class="card" style="box-shadow:none;width: 300px">
            <div class="content" style="text-align: center;">
                <i class="great iconfont-fire iconfire-tupian icon  action-audit"></i>
                <div class="header" style="margin:1.2em 0 0.5em 0;font-size: 2em;color:#4d4d4d">图片资源</div>
                <div class="description">平台上显示的图标，可在上线后进行修改</div>
            </div>
        </div>
        <div class="card" style="box-shadow:none;width: 300px">
            <div class="content" style="text-align: center;">
                <i class="great iconfont-fire iconfire-banbenguanli icon  action-audit"></i>
                <div class="header" style="margin:1.2em 0 0.5em 0;font-size: 2em;color:#4d4d4d">版本管理</div>
                <div class="description">上传最新的客户端版本</div>
            </div>
        </div>
        <div class="card" style="box-shadow:none;width: 300px">
            <div class="content" style="text-align: center;">
                <i class="great iconfont-fire iconfire-agreement icon  action-audit"></i>
                <div class="header" style="margin:1.2em 0 0.5em 0;font-size: 2em;color:#4d4d4d">电子合同</div>
                <div class="description">电子合同以及版权文件</div>
            </div>
        </div>
    </div>
</div>
 -->
 <div style="width:1100px;margin: 0 auto">
<div class="ui ordered steps">
  <div class="completed step">
    <div class="content">
      <div class="title">基本信息</div>
      <div class="description">产品基础信息，包括名称分类等，上线后无法修改</div>
    </div>
  </div>
  <div class="completed step">
    <div class="content">
      <div class="title">资源信息</div>
      <div class="description">产品基础信息，包括名称分类等，上线后无法修改</div>
    </div>
  </div>
  <div class="active step">
  <i class="large iconfont-fire iconfire-agreement icon  action-audit"></i>
    <div class="content">
      <div class="title">电子合同</div>
      <div class="description">Verify order details</div>
    </div>
  </div>
</div>



<div class="ui segment" >
<div class="ui grid" style="clear:both">
<div class="sixteen wide column">
<form class="ui form" onsubmit="return false;">
    <input id="game_id" type="hidden">
        <div class="field">
            <label>游戏名称</label>
            <input id="game_name" type="text">
        </div>
    <div class="field">
        <label>视频分类</label>
        <select id="game_class" class="ui selection dropdown" multiple="">
            <option value="">请选择</option>

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
        <label>视频简介</label>
        <textarea id="game_intro" rows="2"></textarea>
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
</form>
</div>

</div>
</div>

</div>
<div class="ui modal modal-edit">
  <i class="close icon"></i>
  <div class="header">添加游戏</div>
  <div class="content">

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
                    <i class="desc"> 480x270 JPG,PNG </i>
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
                    <label>排行图片</label>
                    <i class="desc"> 238*60 JPG,PNG </i>
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
                <i class="desc"> 600*340  JPG,PNG </i>
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
  audit_modal.modal('hide');
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
