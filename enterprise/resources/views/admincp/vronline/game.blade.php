@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')
<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加游戏</div>

<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索游戏" value="{{ $searchText }}">
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
      <th width="10%">游戏名称</th>
      <th width="10%">游戏别名</th>
      <th width="10%">搜索</th>
      <th width="10%">游戏分类</th>
      <th width="10%">标签</th>
      <th width="10%">发售日期</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if(empty($data) && $data->count()<1)
                <tr><td colspan="8" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-val="{{ $val["game_id"] }}">{{ $val["game_id"] }}</td>
              <td class="action-edit" data-id="{{ $val["game_id"] }}">{{ $val["game_name"] }}</td>
              <td>{{ $val['game_alias'] }}</td>
              <td>{{ $val["game_search_name"] }}</td>
              <td title="{!! $blade->showHtmlClass('vrgame',$val['game_category'] ,'text') !!}">{!! $blade->showHtmlClass('vronline_game_class',$val['game_category'] ,'text') !!}</td>
              <td title="{!! $blade->showHtmlClass('vrgame_tags',$val['game_tag'] ,'text') !!}">{!! $blade->showHtmlClass('vrgame_tags',$val['game_tag'] ,'text') !!}</td>
              <td class="dev_uid">{{ date('Y-m-d H:i:s', $val['game_sell_date']) }}</td>
              <td class="right aligned">
                <i data-id="{{ $val["game_id"] }}" class="large upload icon blue action-upload"></i>
                <i class="large edit icon blue action-edit" data-id="{{ $val["game_id"] }}"></i>
                <i class="large minus circle icon red action-del" data-id="{{ $val["game_id"] }}"></i></td>
              </td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="8">
         {!! $data->appends(['search'=>$searchText])->render() !!}
    </th>
  </tr></tfoot>
</table>

<div class="ui modal modal-add">
    <i class="close icon"></i>
    <div class="header">添加游戏</div>
    <div class="content">
        <form class="ui form " onsubmit="return false;">
            <input type="hidden" id="game_id">
            <div class="three fields">
                <div class="field">
                    <label>游戏名称</label>
                    <input id="game_name" type="text" value="">
                </div>
                <div class="field">
                    <label>游戏别名</label>
                    <input id="game_alias" type="text" value="">
                </div>
                <div class="field">
                    <label>搜索名称</label>
                    <input id="game_search_name" type="text" value="">
                </div>
            </div>
            <div class="field">
                <label>游戏描述</label>
                <textarea id="game_desc" type="text" value="" placeholder="游戏描述"></textarea>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>游戏分类</label>
                    <select id="game_category" name="game_category" class="ui selection dropdown" multiple="">
                        <option value="">请选择</option>
                        {!! $blade->showHtmlClass('vronline_game_class','','select') !!}
                    </select>
                </div>
                <div class="field">
                    <label>标签</label>
                    <!-- <input id="game_tag" type="text" value=""> -->
                    <select id="game_tag" name="game_tag" class="ui selection dropdown" multiple="">
                        <option value="">请选择</option>
                        {!! $blade->showHtmlClass('vrgame_tags','','select') !!}
                    </select>
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>发售日期(例:2017-04-04 12:00:00)</label>
                    <input id="game_sell_date" type="text" value="">
                </div>
                <div class="field">
                    <label>价格</label>
                    <input id="game_price" type="text" value="">
                </div>
            </div>
            <div class="field">
                <label>支持设备</label>
                <select id="game_device" name="game_device" class="ui selection dropdown" multiple="">
                    <option value="">请选择</option>
                    {!! $blade->showHtmlClass('vr_device','','select') !!}
                </select>
            </div>
            <div class="field">
                <label>支持平台</label>
                <!-- <input id="game_platform" type="text" value=""> -->
                <select id="game_platform" name="game_platform" class="ui selection dropdown" multiple="">
                    <option value="">请选择</option>
                    {!! $blade->showHtmlClass('platform','','select') !!}
                </select>
            </div>
            <div class="four fields">
                <div class="field">
                    <label>支持语言</label>
                    <!-- <input id="game_lang" type="text" value=""> -->
                    <select id="game_lang" name="game_lang" class="ui selection dropdown" multiple="">
                        <option value="">请选择</option>
                        {!! $blade->showHtmlClass('language','','select') !!}
                    </select>
                </div>
                <div class="field">
                    <label>游戏题材</label>
                    <input id="game_theme" type="text" value="">
                </div>
                <div class="field">
                    <label>开发商</label>
                    <input id="game_developer" type="text" value="">
                </div>
                <div class="field">
                    <label>运营商</label>
                    <input id="game_operator" type="text" value="">
                </div>
            </div>
            <div class="three fields">
                <div class="field">
                    <label>官网</label>
                    <input id="game_website" type="text" value="">
                </div>
                <div class="field">
                    <label>购买地址</label>
                    <input id="game_address" type="text" value="">
                </div>
                <div class="field">
                    <label>下载地址</label>
                    <input id="game_download" type="text" value="">
                </div>
            </div>
            <div class="inline fields">
                <div class="field">
                    <label>封面图片</label>
                    <div class="ui segment">
                        <img id="top_cover" class="preview ui tiny image">
                    </div>
                </div>
                <div class="field" id="cover_container">
                    <button class="ui teal button" id="cover_browser">选择</button>
                </div>
            </div>
        </form>
        <div class="ui bottom attached warning message"></div>
    </div>
    <div class="actions">
        <div class="ui button cancle-btn" onclick="cancleBtn()">取消</div>
        <div class="ui button action-save">确定</div>
    </div>
</div>




<div class="ui modal modal-upload">
  <i class="icon close close_icon"></i>
  <div class="header">添加游戏图片（最多6张）</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
        <div class="inline fields">
          <div class="field">
          <label>上传图片</label>
          <div class="ui segment games_img" style="height: 180px;">

          </div>
          </div>
          <div class="field" id="video_cover_container">
          <button  class="ui teal  button" id="video_cover_browser">选择</button>
          </div>
      </div>
    </form>
  </div>
</div>

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header"></div>
  <div class="content">
  你确定要<label>删除该游戏</label>吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>
@endsection



@section('javascript')
<script type="text/javascript">
var del_id,del_modal,del_tp,audit_id,audit_modal,modal_play,top_cover_obj;
var warning_msg = $(".warning.message");
var defaultImg = "semantic/images/image.png";


var loi = new loiForm();
$(function() {
  $(".action-del").click(function() {
    var that = $(this);
    del_id = that.attr('data-id');
    del_modal = $('.ui.modal.modal-del').modal('show');
  });
  $(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   dataEdit(id)
  });
  $(".close_icon").click(function(){
    location.reload();
  });
  $(".action-audit").click(function() {
    var that = $(this);
    var obj = that.parent().parent().find("td:first");
    audit_id = obj.attr('data-val');
    $("#passmsg").val('');
    audit_modal = $('.ui.modal.modal-audit').modal('show');
  })
  $(".action-save").click(function(){
    var editData = loi.save();
    if(typeof(editData.err) != "undefined") {
      console.log(editData)
      warning_msg.html("还有未填写项目");
      warning_msg.show();
    } else {
      formData = loi.submit();
      console.log(formData);
      permPost("/json/save/vronline_game",formData,function(data){
        location.reload();
      });
      edit_modal.modal('hide');
    }
  });

  $(".action-search").keypress(function() {
    if(event.keyCode==13) {
      var searchText = $(this).val();
      location.href = "/vronline/game?search="+searchText;
    }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
     location.href = "/vronline/game?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
     location.href = "/vronline/game";
  });

  $(document).on('click','.del_game_img',function(){
    var id = $(this).attr("data-id");
    var dom = $(this).parents('.addimg');
    permPost("/vronline/game/delgameimg",{'id':id},function(data){
      if(data.code == 0) {
        dom.detach();
      }
    });
  });
  $(".action-upload").click(function(){
    var that = $(this);
    game_id = that.attr('data-id');
    permPost("/vronline/game/getgameimg",{'game_id':game_id},function(data){
      var domhtml = '';
      if(data.code == 0) {
        for(var i=0; i<data.data.length; i++) {
          domhtml += '<div style="display:inline-block;" class="addimg" id="gameimg_'+data.data[i].id+'"><i class="icon close del_game_img" data-id="'+data.data[i].id+'" style="position:absolute;z-index:100;width:16px;height:16px;"></i><img style="width:100px;height:150px;margin-right:10px;" class="preview ui small image"  src="' +img_domain+data.data[i].game_pic_url+ '"></div>';
        }
        $(".games_img").html(domhtml);
      }
        });
    upload_modal = $('.ui.modal.modal-upload').modal('show');
    var video_cover_obj = new loiUploadContainer({
      container:"video_cover_container",
      choose:"video_cover_browser",
      ext:"jpg,png",
      upload:{tp:"bannerimg",success:function(json){
      var jsonResult = $.parseJSON(json);
      var path = jsonResult.data.fileid;
      var src = img_domain+path;
      permPost("/vronline/game/addgameimg",{'game_id':game_id, 'game_pic_url':path},function(data){
        if(data.code == 0) {
          var html = '<div style="display:inline-block;" class="addimg" id="gameimg_'+data.data.id+'"><i class="icon close del_game_img"  data-id="'+data.data.id+'"  style="position:absolute;z-index:100;width:16px;height:16px;"></i><img style="width:100px;height:150px;margin-right:10px;" class="preview ui small image" src="' +src+ '" data-val="' +path+ '" ></div>';
          $(".games_img").append(html);
        }
          });
      },error:function(){}},
      filesAdd:function(files){
      // console.log(files)
      }
    });
  });
})

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
    console.log(del_id);
    if(del_id>0) {
      permPost("/json/del/vronline_game",{del_id:del_id,del_tp:del_tp},function(data){
       location.reload();
      })
    }
  }
}

function dataEdit(id) {
  loi.edit("vronline_game",id,function(data){
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
    if(typeof(top_cover_obj)=="undefined") {
        top_cover_obj = new loiUploadContainer({
        container:"cover_container",
        choose:"cover_browser",
        ext:"jpg,png",
        upload:{tp:"bannerimg",success:function(json){
          var jsonResult = $.parseJSON(json);
          var path = jsonResult.data.fileid;
           $("#top_cover").attr('src',img_domain+path);
           $("#top_cover").attr('data-val',path);
        },error:function(){}},
          filesAdd:function(files){
           // console.log(files)
          }
      });
    }
  })
}

function cancleBtn() {
  var close_modal = $('.ui.modal.modal-add').modal('hide');
}
</script>
@endsection
