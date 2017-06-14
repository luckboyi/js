<div class="ui modal modal-edit">
  <i class="close icon"></i>
  <div class="header">修改基本信息</div>
  <div class="content">
    <form class="ui form" onsubmit="return false;">
    <input id="game_id" type="hidden">
    <div class="two fields">
      <div class="field">
      <label>游戏名称</label>
      <input id="game_name" type="text">
      </div>
      <div class="field">
      <label>游戏标签</label>
      <input id="game_tag" type="text">
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
    <div class="three fields">
    <div class="field">
        <label>游戏原价</label>
       <input id="game_original_sell" type="text">
    </div>
    <div class="field">
        <label>游戏现价</label>
        <input id="game_sell" type="text">
    </div>
    <div class="field">
        <label>oculus</label>
        <input id="game_oculus" type="text">
    </div>
    </div>
    <div class="field">
        <label>游戏简介</label>
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
  <div class="actions">
  <div class="ui blue button action-save" >确定 </div>
  </div>
</div>


<div class="ui large modal modal-pic">
  <i class="close icon"></i>
  <div class="header">图片资源</div>
  <div class="content" style="height:700px">
       <form class="ui form"  onsubmit="return false;">
        <div class="three fields">
            <div class="inline fields">
                <div class="field">
                    <label>游戏LOGO</label>
                    <i class="desc"> 310*280 JPG,PNG </i>
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
                <i class="desc"> 610*340  JPG,PNG 至少4张</i>
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


<div class="ui modal modal-pay">
  <i class="close icon"></i>
  <div class="header">支付设置</div>
  <div class="content" >
       <form class="ui form"  onsubmit="return false;">
        <div class="field">
        <label>支付回调地址</label>
        <input id="game_pay_url" type="text">
        </div>
        <div class="field">
        <label>支付回调地址(测试)</label>
        <input id="game_pay_url_test" type="text">
        </div>
    </form>
  </div>
  <div class="actions">
  <div class="ui blue button action-save">保存 </div>
  </div>
</div>


<script type="text/javascript">
var modal_edit,modal_pic,modal_pay;
var game_logo_obj,game_icon_obj,game_rank_obj,game_bg_obj,game_slider_obj;
var loi = new loiForm();
function dataEdit(id,tp) {
  loi.edit("vrgame",{id:id,tp:tp},function(data){
    var appid = data.game_id.val;
    if(tp == "pic") {
      if(typeof(game_logo_obj)=="undefined") {
        game_logo_obj = new loiUploadContainer({
          id:"game_logo",
          upload:{
              tp:"vrgameimg",addParams:{appid:appid,"assign":"logo"},
              error:function(){}
          }
        });
      }

      if(typeof(game_icon_obj)=="undefined") {
        game_icon_obj = new loiUploadContainer({
            id:"game_icon",
            upload:{
                tp:"vrgameimg",addParams:{appid:appid,"assign":"icon"},
                error:function(){}
            }
        });
      }

      if(typeof(game_rank_obj)=="undefined") {
        game_rank_obj = new loiUploadContainer({
            id:"game_rank",
            upload:{
                tp:"vrgameimg",addParams:{appid:appid,"assign":"rank"},
                error:function(){}
            }
        });
      }

      if(typeof(game_bg_obj)=="undefined") {
        game_bg_obj = new loiUploadContainer({
            id:"game_bg",
            upload:{
                tp:"vrgameimg",addParams:{appid:appid,"assign":"bg"},
                error:function(){}
            }
        });
      }

      if(typeof(game_slider_obj)=="undefined") {
        game_slider_obj = new loiUploadContainer({
            id:"game_slider",
            upload:{
               tp:"vrgameimg",multi:true,addParams:{appid:appid},
               error:function(){}
            }
        });
      }

      modal_pic =  $('.ui.modal.modal-pic').modal('show');
    } else if(tp=="pay") {
       modal_pay=  $('.ui.modal.modal-pay').modal('show');
    } else {
      if(id==0) {
        $('.ui.modal.modal-edit .header').text('添加VR游戏');
      }
      modal_edit =  $('.ui.modal.modal-edit').modal('show');
    }
  });
}

$(function(){
  $(".action-save").click(function(){
    var editData = loi.save();

    if(typeof(editData.err) != "undefined") {
      loiMsg(editData.err+"未填写");
    } else {
      formData = loi.submit();
      permPost("/json/save/vrgame",formData,function(res){
        if(res.code==0) {
          if(typeof(formData.game_bg)!="undefined") {
              modal_pic.modal('hide');
              location.reload();
           } else if (typeof(formData.game_pay_url)!="undefined") {
              modal_pay.modal('hide');
           } else {
              modal_edit.modal('hide');
              location.reload();
           }

        } else {
          loiMsg(res.msg);
        }
      });
    }
  })
})
</script>
