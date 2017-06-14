@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')
<div class="ui basic small buttons">
{!! $blade->showHtmlClass('video_all',$curClass,'menu') !!}
</div>
<div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索视频" value="{{ $searchText }}">
  @if($searchText)
  <i class="large remove link icon"></i>
  @else
  <i class="large search link icon"></i>
  @endif
</div>

<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加视频</div>
<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="5%">ID</th>
      <th width="15%">名称</th>
      <th width="10%">简介</th>
      <th width="15%">封面</th>
      <th width="10%">预览</th>
      <th width="20%">分类</th>
      <th width="10%">转码</th>
      <th width="5%">状态</th>
      <th width="10%" class="right aligned">操作</th>
    </tr>
  </thead>

  <tbody>
          @if($data->count()<1)
                <tr><td colspan="9" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr>
              <td data-key="video_id" data-val="{{ $val["video_id"] }}">{{ $val["video_id"] }}</td>
              <td class="action-edit" data-key="video_name" data-id="{{ $val["video_id"] }}">{{ $val["video_name"] }}</td>
              <td>{{ $val["video_intro"] }}</td>
              <td><a href="{{ static_image($val["video_cover"]) }}" target="_blank"><img class="ui tiny image" src="{{ static_image($val["video_cover"]) }}"></a></td>
              <td class="action-preview" data-title="{{ $val["video_name"] }}" data-tp="{{ $val["video_link_tp"] }}" data-val="{{ $val["video_link"] }}" data-trans="{{ $val["video_trans"] }}" > 播放 </td>
              <td >{{  $blade->showHtmlClass('video',$val["video_class"]) }}</td>
              <td data-val="{{ $val["video_trans"] }}" >@if($val["video_trans"]=="")<a class="ui blue label action-sub">转码 </a>@elseif(strlen($val["video_trans"])>10)  <a class="ui blue label action-trans">转码中 </a> @elseif(strlen($val["video_trans"])=="1") <a class="ui teal label ">转码成功 </a> @endif</td>
              <td>{{  $blade->showHtmlStat('video',$val["video_stat"]) }}</td>
              <td class="right aligned" data-val="{{ $val["video_id"] }}"><i class="large check circle icon teal action-audit" title="审核"></i> <i class="large minus circle icon red action-del"></i></td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="9">
         {!! $data->appends(['choose' => $curClass])->render() !!}
    </th>
  </tr></tfoot>
</table>

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">下架视频</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要下架该视频吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>

<div class="ui modal modal-audit">
  <i class="close icon"></i>
  <div class="header">审核视频</div>
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

<div class="ui modal modal-sub">
  <i class="close icon"></i>
  <div class="header">视频转码</div>
  <div class="content">
  您确定对该视频转码吗？
  </div>
  <div class="actions">
  <div class="ui positive button" onclick="transcoding()">确定 </div>
  </div>
</div>

<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加视频</div>
  <div class="content">
    <form class="ui form"  onsubmit="return false;">
     <input id="video_id" type="hidden"  >
      <div class="three fields">
      <div class="field">
       <label>视频名称</label>
      <input id="video_name" type="text" >
      </div>
      <div class="field">
       <label>视频标签</label>
      <input id="video_keywords" type="text" >
      </div>
      <div class="field">
       <label>视频时间</label>
      <input id="video_times" type="text" >
      </div>
      </div>
      <div class="field">
      <label>视频分类</label>
      <select  name="video_class" class="ui selection dropdown" multiple="" id="video_class">
      <option value="">请选择</option>
       {!! $blade->adminCpVideoClass(2) !!}
      </select>
      </div>
      <div class="field">
      <label>视频简介</label>
      <textarea id="video_intro" rows="2"></textarea>
      </div>
      <div class="fields">
        <div class="eight wide field">
          <div class="inline fields">
            <label>内容</label>
            <div class="field">
            <div class="ui radio checkbox">
            <input type="radio" name="video_vr" value="1">
            <label>普通</label>
            </div>
            </div>
            <div class="field">
            <div class="ui radio checkbox">
            <input type="radio" name="video_vr" value="2">
            <label>3D</label>
            </div>
            </div>
            <div class="field">
            <div class="ui radio checkbox">
            <input type="radio" name="video_vr" value="3">
            <label>VR</label>
            </div>
            </div>
          </div>
        </div>
        <div class="eight wide field">
          <div class="inline fields">
            <label>来源</label>
            <div class="field">
            <div class="ui radio checkbox">
            <input type="radio" name="video_link_tp" value="1">
            <label>本站</label>
            </div>
            </div>
            <div class="field">
            <div class="ui radio checkbox">
            <input type="radio" name="video_link_tp" value="2">
            <label>其他</label>
            </div>
            </div>
          </div>
        </div>
      </div>
      <div class="inline fields">
        <div class="field">
        <label>封面图片</label>
        <div class="ui segment">
        <img id="video_cover" class="preview ui small image">
        </div>
        </div>
        <div class="field" id="video_cover_container">
        <button  class="ui teal  button" id="video_cover_browser">选择</button>
        </div>
      </div>
      <div class="field video-other">
      <label>播放器代码</label>
      <textarea id="video_source_code" rows="2" placeholder="请填写通用代码，没有则填写html代码"></textarea>
      </div>
      <div class="fields video-local">
        <div class="eight wide field">
        <div class="ui action input" id="video_link_container">
        <input id="video_link" type="text" placeholder="" >
        <button  class="ui teal button action-video" id="video_link_browser">选择</button>
        </div>
        </div>
      </div>
      <div class="field" style="display: none">
        <div class="ui blue progress" data-percent="0" id="progress" style="width:100%">
        <div class="bar"></div>
        <div class="label">0%</div>
        </div>
      </div>
    </form>
  </div>

  <div class="actions">
    <div class="ui blue button action-save">保存</div>
  </div>
</div>


<div class="ui modal modal-preview">
  <i class="close icon" onclick="stopPlay()"></i>
  <div class="header"></div>
  <div class="content">
  </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
var video_class = $('#multi-select').dropdown();
var edit_modal;
var del_modal;
var progress = $('#progress').progress();
var video_link_obj;
var totalSize;
var del_id;
var wwwUid = "{{ $user['wwwUid'] }}";
var modal_sub;
var transcoding_id;
var lock = false;
var loi = new loiForm();

$(function(){
  $('input[name="video_link_tp"]').change(function(){
     var sel_val = $(this).val()
     if(sel_val==1) {
        $('.video-local').show()
        $('.video-other').hide()
      } else {
        $('.video-local').hide()
        $('.video-other').show()
      }
  });

    $(".action-search").keypress(function() {
  if(event.keyCode==13) {
     var searchText = $(this).val();
     location.href = "/vrhelp/video?search="+searchText;
  }
  });
  $(".action-trans").hover(function() {
    if(lock==true) {
      return ;
    }
    lock = true
    var persistentId = $(this).parent().attr('data-val');
    var that = $(this).parent();
     $.get("http://vronline.mgr9.v1.wcsapi.com/status/get/prefop?persistentId="+persistentId,function(res){
      lock = false;
      if(res.code>=3) {
         permPost("/vrhelp/transcoding/stat",{persistentId:persistentId},function(data){
          that.html('<a class="ui teal label">转码成功 </a>');
        })
      }

     },"json")
  });

   $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/video?search="+searchText;
  });


  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/vrhelp/video";
  });

  $(".action-preview").click(function() {
    var tp = $(this).attr("data-tp");
    var url = $(this).attr('data-val');
    var title = $(this).attr('data-title');
    var is_trans = $(this).attr('data-trans');
    $('.ui.modal.modal-preview .header').text(title);
    if(tp==2) {
      $('.ui.modal.modal-preview .content').html(url);
      if(url.indexOf('embed')!=-1) {
           $('.ui.modal.modal-preview .content embed').css("width","99%");
      } else {
         $('.ui.modal.modal-preview .content iframe').css("width","99%");
      }
    } else {
      // var playUrl = '<video width="100%" height="500px" controls><source src="'+url+'" type="video/mp4"></video>'
      // $('.ui.modal.modal-preview .content').html(playUrl);
      if(is_trans==1) {
        url = url.replace(".mp4","_blue.mp4");
      }
      var playUrl = '<iframe scrolling=no allowFullScreen=true frameborder=0 width="98%" height="450px" src="http://www.vronline.com/play.html?source='+url+'"></iframe>';
       $('.ui.modal.modal-preview .content').html(playUrl);
    }
    preview_modal = $('.ui.modal.modal-preview').modal('show');
  });

  $(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   dataEdit(id)
  });

  $(".action-sub").click(function() {
    transcoding_id =  $(this).parent().parent().find("td:first").attr('data-val');
    modal_sub = $('.ui.modal.modal-sub').modal('show');
  });

  $(".action-del").click(function() {
    var that = $(this);
    var obj = that.parent().parent().find("td:first");
    del_id = obj.attr('data-val');
    del_modal = $('.ui.modal.modal-del').modal('show');
  })

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
       loiMsg(editData.err+"未填写");
    } else {
      formData = loi.submit();
      if(formData.video_link_tp==1 && formData.video_link.length<5) {
         loiMsg("请上传视频");
      } else if(formData.video_link_tp==2 && formData.video_source_code.length<5) {
         loiMsg("请粘贴播放器代码");
      } else {
         permPost("/json/save/vrhelp_video",formData,function(data){
           location.reload();
        });
        edit_modal.modal('hide');
      }
    }
  });
})

function dataEdit(id) {

  loi.edit("vrhelp_video",id,function(data){
    if(data.video_link_tp.val==2) {
      $('.video-local').hide()
      $('.video-other').show()
    } else {
      $('.video-local').show()
      $('.video-other').hide()
    }
    if(id==0 &&  data.wwwUid.length<1) {
      loiMsg("请用账号登录官网后，刷新本页面添加视频");
      return false;
    }
    edit_modal = $('.ui.modal.modal-add').modal('show');
     var video_cover_obj = new loiUploadContainer({
      id:"video_cover",
      upload:{
        tp:"videoimg",
        addParams:{"wwwUid":data.wwwUid},
        error:function(){}
      },
    });

     if(typeof(video_link_obj)=="undefined") {
          video_link_obj = new loiUploadContainer({
          id:"video_link",
          ext:"mp4",
          upload:{tp:"netcvideo",addParams:{"wwwUid":data.wwwUid},success:function(json){
            var res = $.parseJSON(json)
            var percent = 100;
            progress.progress({percent: percent});
            progress.find(".label").text(percent+"%");
            $("#video_link").val(video_domain+"/"+res.key);
          },sliceback:function(per){
              var num = new Number(per);
              num = num*100;
              var percent = num.toFixed(2)+"%";
              progress.progress({percent: num.toFixed(2)});
              progress.find(".label").text(percent)
          },error:function(msg){
            if(typeof(msg)=="string") {
                  loiNotifly(msg)
              } else if(typeof(msg)=="object") {
                  if(typeof(msg.responseText)!="undefined") {
                      var errJson =  $.parseJSON(msg.responseText)
                      loiNotifly("文件已经上传,请更换名称后重新上传",3000)
                  }
              }
              progress.parent().hide();
          }},
          filesAdd:function(files){
            progress.progress({percent: 0});
            progress.find(".label").text("0%");
            progress.parent().show();
          }
          });
        }
})
}

function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/vrhelp_video",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}

function toDecimal(x){
  var f = parseFloat(x);
  if (isNaN(f)) {
    return;
  }
  f = Math.round(x*100)/100;
  return f;
}

//审核视频
var audit_modal;
var audit_id;

function audit(tp) {
  audit_modal.modal('hide');
  var msg = '';
  if(tp==0) {
    msg = $("#passmsg").val();
  }
  if(audit_id>0) {
    permPost("/json/pass/vrhelp_video",{edit_id:audit_id,tp:tp,msg:msg},function(data){
      if(tp==1) {
         loiMsg("审核成功",function(){location.reload();},"success");
       } else {
         loiMsg("驳回成功",function(){location.reload();},"success");
       }
    })
  }
}

function stopPlay() {
   $('.ui.modal.modal-preview .content').html('');
}

function transcoding() {
  if(typeof(transcoding_id)=="undefined") return;
  permPost("/vrhelp/transcoding",{video_id:transcoding_id},function(res){
    if(res.code==0) {
      location.reload();
    }
  })
}

</script>
@endsection
