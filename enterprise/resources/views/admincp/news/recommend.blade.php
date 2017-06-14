@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/swfobject.js') }}"></script>
@endsection

@section('content')
<div class="ui basic small buttons">
{!! $blade->adminCpTopPos(1,$pos,$posid) !!}
</div>

<div class="ui small button right floated blue" onclick="edit(0)"><i class="plus icon"></i>新建</div>

<div class="ui segment">
<div class="ui  link cards">
@foreach($data as $val)
 <div id="card-{{ $val['id'] }}" class="card" draggable="true" ondrop="drop(event)" ondragstart="drag(event)" ondragover="allowDrop(event)">
 <i class="large red close icon action-del"></i>
<div class="image">
<img src="@if($val["cover"]) {!! static_image($val["cover"]) !!} @else  {!! static_image($articles[$val["itemid"]]['cover']) !!} @endif" draggable="false" >
@if($val['tp']=='article')
</div>
    <div class="content" data-val="{{ $articles[$val["itemid"]]['title']  }}">
      <div class="header"  >{{ $articles[$val["itemid"]]['title']  }}  ID: {{ $articles[$val["itemid"]]['id']  }}</div>
@else
</div>
    <div class="content">
      <div class="header">{{ $val['title']  }}</div>
@endif
<div class="meta">
        <a>{{ $val["tp"] }}</a>
      </div>
      <div class="description">
        {{ $val["target_url"] }}
      </div>
    </div>
      <div class="extra content">
        <div class="action-edit" data-id="{{ $val['id'] }}" >编辑</div>
    </div>
  </div>
@endforeach
</div>
</div>

<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">删除推荐</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要下线该推荐吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>

@if($postp=="banner")
<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加广告 到 {{ $posName }}</div>
  <div class="content">
    <form class="ui form" onsubmit="return false;">
      <div class="inline fields">
        <div class="field">
        <label>广告图片</label>
        <input  id="rec_id" type="hidden" >
        <div class="ui segment">
        <img id="rec_cover" class="preview ui tiny image">
        </div>
        </div>
        <div class="field" id="cover_container">
        <button  class="ui teal button" id="cover_browser">选择</button>
        </div>
      </div>
      <div class="field">
      <label>广告链接</label>
      <input id="rec_target_url" type="text" >
      </div>
      <div class="field">
      <label>广告标题</label>
      <input id="rec_title" type="text" >
      </div>
      <div class="field">
      <label>广告描述</label>
      <textarea id="rec_intro" rows="2"></textarea>
      </div>
      <div class="ui bottom attached warning message"></div>
    </form>
  </div>
  <div class="actions">
    <div class="ui button action-save">确定</div>
  </div>
</div>
@else
<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加推荐</div>
  <div class="content">
    <form class="ui form " onsubmit="return false;">
      <input type="hidden"  id="rec_id" >
      <input type="hidden"  id="rec_item_id" >
      <div class="field">
      <label>选择内容</label>
      <div class="ui search  left icon input">
      <i class="search icon"></i>
      <input class="prompt"  type="text" placeholder="Search">
      <div class="results"></div>
      </div>
      </div>
      <div class="field">
      <label>推荐位置</label>
      <input  readonly="" type="text" value="{{ $posName }}">
      </div>
      <div class="field">
      <label>推荐标题</label>
      <input id="rec_title" type="text" >
      </div>
      <div class="inline fields">
        <div class="field">
        <label>推荐图片</label>
        <div class="ui segment">
        <img id="rec_cover" class="preview ui tiny image">
        </div>
        </div>
        <div class="field" id="cover_container">
        <button  class="ui teal button" id="cover_browser">选择</button>
        </div>
      </div>
      <div class="ui bottom attached warning  message"></div>
    </form>
  </div>
  <div class="actions">
    <div class="ui button action-save">确定</div>
  </div>
</div>
@endif
<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="/js/Somethingtest.swf" style="visibility: visible;"></object>
@endsection

@section('javascript')
<script type="text/javascript">
var del_id;
var rec_modal;
var posid = {{ $posid }};
var loi = new loiForm();
var warning_msg = $(".warning.message");
var searchTitle;
var defaultImg = "semantic/images/image.png";
var postp = "{{ $postp }}";


var search_obj = $('.ui.search').search({
     apiSettings: {
      url: '/news/search?&q={query}'
    },
    onSelect:function(result, response){
      $("#rec_item_id").val(result.id);
    }
});


function allowDrop(ev)
{
  ev.preventDefault();
}

function drag(ev)
{
  var id = ev.target.id;
  ev.dataTransfer.setData("id",id);
}

function drop(ev)
{
  ev.preventDefault();
  var id = ev.dataTransfer.getData("id");
  var drop = $(ev.target).parents(".card");
  var dropId = drop.attr("id");
  var dragHtml = $("#"+id).clone().prop("outerHTML");
  var dropHtml = drop.prop("outerHTML")
  $("#"+id).replaceWith(dropHtml);
  drop.replaceWith(dragHtml);
  id = id.replace("card-","");
  dropId = dropId.replace("card-","");
  permPost("/news/top/switchWeight",{drag:id,drop:dropId},function(data){
  })
}

$(".action-save").click(function(){
  var editData = loi.save();
  if(typeof(editData.err) != "undefined") {
    warning_msg.html("还有未填写项目");
    warning_msg.show();
  } else {
    warning_msg.hide();
    formData = loi.submit();
    formData.posid = posid;
    formData.tp = postp;
    permPost("/json/save/news_recommend",formData,function(data){
       recommend_modal.modal('hide');
       location.reload();
    });
  }
});



function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      permPost("/json/del/news_recommend",{del_id:del_id},function(data){
        location.reload();
      })
    }
  }
}


$(".action-edit").click(function() {
   var id =  $(this).attr("data-id");
   var obj = $(this).parent().prev();
   var val = obj.attr('data-val');
   if(typeof(val)!="undefined" && val.length>0 ) {
      searchTitle = val;
   } else {
      searchTitle = "";
   }
   edit(id)
});


$(".action-del").click(function() {
  var that = $(this);
  var obj = that.parent();
  del_id = obj.attr('id').replace("card-","");
  del_modal = $('.ui.modal.modal-del').modal('show');
})


function edit(id) {
  if(postp=="article") {
    recommendEdit("news_recommend",id)
  } else {
    recommendEdit("news_banner",id)
  }
}

function recommendEdit(name,id) {
  loi.edit(name,id,function(data){
    if(postp == "article" && typeof(searchTitle)!="undefined") {
      $(".search .prompt").val(searchTitle);
    }
    recommend_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
    if(typeof(cover_obj)=="undefined") {
        cover_obj = new loiUploadContainer({
        container:"cover_container",
        choose:"cover_browser",
        ext:"jpg,png",
        upload:{tp:"newsimg",success:function(json){
          var jsonResult = $.parseJSON(json);
          var path = jsonResult.data.fileid;
           $("#rec_cover").attr('src',img_domain+path);
           $("#rec_cover").attr('data-val',path);
        },error:function(){}},
          filesAdd:function(files){
           // console.log(files)
          }
      });
    }
})
}

</script>
@endsection
