@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')

@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection



@section('content')


<div class="ui basic small buttons">
<a href="#"><div class="ui basic  button blue">合同签署</div></a>
<a href="javascript:dataEdit()"><div class="ui basic  button">版权文件</div></a>
<a href="/developer/vrgame/detail/{{ $id }}"><div class="ui basic  button ">返回兽人塔防</div></a>
</div>

 {!! $blade->showNewProtocol($data["agreement_type"], $data["agreement"]) !!}



<div class="ui large modal modal-pic">
  <i class="close icon"></i>
  <div class="header">版权相关</div>
  <div class="content" style="height:700px">
       <form class="ui form"  onsubmit="return false;">
      <div class="inline fields">
            <div class="field">
                <label>软件著作权</label>
                <i class="desc">  JPG,PNG </i>
                <div class="ui segment small images" id="soft">
                </div>
            </div>
            <div class="field" id="soft_container">
                <button class="ui teal  button" id="soft_browser">选择</button>
            </div>
        </div>
        <div class="inline fields">
            <div class="field">
                <label>游戏备案通知单</label>
                <i class="desc">   JPG,PNG </i>
                <div class="ui segment small images" id="record">
                </div>
            </div>
            <div class="field" id="record_container">
                <button class="ui teal  button" id="record_browser">选择</button>
            </div>
        </div>
        <div class="inline fields">
            <div class="field">
                <label>游戏版号通知单</label>
                <i class="desc"> JPG,PNG </i>
                <div class="ui segment small images" id="publish">
                </div>
            </div>
            <div class="field" id="publish_container">
                <button class="ui teal  button" id="publish_browser">选择</button>
            </div>
        </div>
    </form>
  </div>
  <div class="actions">
  <div class="ui blue button action-copyright">保存 </div>
  </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
var appid = {{ $id }};

var modal_pic;
var game_soft_obj,game_record_obj,game_publish_obj;
var loi = new loiForm();
function dataEdit() {
  loi.edit("vrgame_copyright",{id:appid},function(data){
        if(typeof(game_soft_obj)=="undefined") {
        game_soft_obj = new loiUploadContainer({
            id:"soft",
            upload:{
               tp:"openapp",multi:true,addParams:{appid:appid},
               error:function(){}
            }
        });
      }
      if(typeof(game_record_obj)=="undefined") {
        game_record_obj = new loiUploadContainer({
            id:"record",
            upload:{
               tp:"openapp",multi:true,addParams:{appid:appid},
               error:function(){}
            }
        });
      }
      if(typeof(game_publish_obj)=="undefined") {
        game_publish_obj = new loiUploadContainer({
            id:"publish",
            upload:{
               tp:"openapp",multi:true,addParams:{appid:appid},
               error:function(){}
            }
        });
      }
      modal_pic =  $('.ui.modal.modal-pic').modal('show');
  });
}


function add() {
    modal_edit = $('.ui.modal.modal-add').modal('show');
}

$(function(){
    $(".action-back").click(function(){
        location.href = "/developer/vrgame/detail/"+appid
    });
    $(".action-save").click(function(){
        saveAgreement();
    });
    $(".action-copyright").click(function(){
        saveCopyRgiht();
    });
})

function saveAgreement() {
    var input = $("#subform input");
    for (var i = 0; i < input.length; i++) {
        if (input[i].value == "") {
            input[i].focus();
            loiMsg("请填写完整");
            return false;
        }
    }
    var cp_deal = $("input[name='cp_deal']:checked").val();
    if(typeof(cp_deal)=="undefined") {
        $("input[name='cp_deal']").focus();
        loiMsg("请同意以上所有协议");
        return false;
    }


    var formData = $("#subform").serialize();
    formData  = formData+"&id="+appid;
    permPost("/json/save/vrgame_agreement",formData,function(res){
        if(res.code==0) {
            loiMsg("保存成功",function(){
                location.href = "/developer/vrgame/detail/"+appid;
            },"blue");
        } else {
            loiMsg(res.msg);
        }
    });
}
function saveCopyRgiht() {
    var editData = loi.save();
    if(typeof(editData.err) != "undefined") {
      loiMsg(editData.err+"未填写");
    } else {
      formData = loi.submit();
      formData.id = appid;
      permPost("/json/save/vrgame_copyright",formData,function(res){
        if(res.code==0) {
            loiMsg("保存成功",function(){
                 modal_pic.modal('hide');
            },"blue",500);
        } else {
            loiMsg(res.msg);
        }
      });
    }
}
</script>
@endsection
