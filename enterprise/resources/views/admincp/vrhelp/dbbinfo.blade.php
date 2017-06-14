@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')
<div class="ui basic small buttons">
 {!! $blade->adminCpClass("3dbb_info",$choose,'a') !!}
</div>
<div class="ui small button right floated blue" onclick="bannerEdit(0)"><i class="plus icon"></i>添加信息</div>

 <table class="ui sortable  celled table">
  <thead>
    <tr>
      <th width="5%">ID</th>
      <th width="15%">推荐位名称</th>
      <th width="15%">推荐位代码</th>
      <th width="5%">排序</th>
      <th width="40%">推荐位内容</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>
  <tbody>
          @if(count($data)<1)
                <tr><td colspan="5" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td>{{ $val["id"] }}</td>
              <td>{{ config("admincp.3dbb_info")[$val["position"]] }}</td>
              <td class="action-edit"}}">{{ $val["position"] }}</td>
              <td>{{ $val["sort"] }}</td>
              <td>{!! $blade->handle3dbbInfo($val["detail"]) !!}</td>
              <td class="right aligned">
                <i class="large edit icon blue action-edit" data-id="{{ $val["id"] }}"></i>
                <i class="large minus circle icon red action-del" data-id="{{ $val["id"] }}"></i>
              </td>
              </tr>
              @endforeach
          @endif
  </tbody>{{--
   <tfoot>
    <tr><th colspan="9">
         {!! $data->appends(['choose' => $choose])->render() !!}
    </th>
  </tr></tfoot> --}}
</table>

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">删除信息</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要删除该信息吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>


<div class="ui modal modal-banner">
  <i class="close icon"></i>
  <div class="content">
    <form class="ui form" onsubmit="return false;">
      <div class="inline fields">
        <div class="field">
        <label>图片</label>
        <input  id="info_id" type="hidden" >
        <div class="ui segment">
        <img id="info_img_url" class="preview ui tiny image">
        </div>
        </div>
        <div class="field" id="info_img_url_container">
        <button  class="ui teal button" id="info_img_url_browser">选择</button>
        </div>
      </div>
      <div class="field">
      <label>排序</label>
      <input id="info_sort" type="text" >
      </div>
      <div class="field">
      <label>标题</label>
      <input id="info_title" type="text" >
      </div>
      <div class="field">
      <label>视频链接</label>
      <input id="video_url" type="text" >
      </div>
      <div class="ui bottom attached warning message"></div>
    </form>
  </div>
  <div class="actions">
    <div class="ui button action-banner-save">确定</div>
  </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
var top_tp = $('select.dropdown').dropdown();
var del_modal;
var edit_modal;
var warning_msg = $(".warning.message");
var del_id;
var defaultImg = "semantic/images/image.png";

var loi = new loiForm();
$(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   console.log(id);
   bannerEdit(id)
});


function bannerEdit(id) {
  loi.edit("dbb_info",id,function(data){
    banner_modal = $('.ui.modal.modal-banner').modal('show');

    warning_msg.hide();
    if(typeof(info_img_url_obj)=="undefined") {
        banner_url_obj = new loiUploadContainer({
        container:"info_img_url_container",
        choose:"info_img_url_browser",
        ext:"jpg,png",
        upload:{tp:"bannerimg",success:function(json){
          var jsonResult = $.parseJSON(json);
          var path = jsonResult.data.fileid;
           $("#info_img_url").attr('src',img_domain+path);
           $("#info_img_url").attr('data-val',path);
        },error:function(){}},
          filesAdd:function(files){
           // console.log(files)
          }
      });
    }
})
}

$(".action-banner-save").click(function(){
  var editData = loi.save();
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    warning_msg.hide();
    formData = loi.submit();
    formData.info_position="3dbb_index";
    permPost("/json/save/dbb_info",formData,function(data){
       banner_modal.modal('hide');
       location.reload();
    });
  }
});


$(".action-del").click(function() {
  del_id = $(this).attr("data-id");
  del_modal = $('.ui.modal.modal-del').modal('show');
})


function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/dbb_info",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}

</script>
@endsection
