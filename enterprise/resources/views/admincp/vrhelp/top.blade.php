@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>

<style>

</style>
@endsection

@section('content')

<div class="ui basic small buttons">
 <div class="ui basic button   blue ">{{ $posName }}</div>
</div>


<div class="ui small button right floated blue" onclick="dataEdit(0);"><i class="plus icon"></i>新建推荐</div>

<div class="ui grid" style="clear:both">
  <div class="fourteen wide column">
    <div class="ui segment">
    <div class="ui  link cards">
    @foreach($data as $val)
    <div id="card-{{ $val['id'] }}" class="card" draggable="true" ondrop="drop(event)" ondragstart="drag(event)" ondragover="allowDrop(event)">
    <i class="large red close icon action-del"></i>
    <div class="image">
    @if($val['tp']=='video')
    <img src="{!! static_image($videos[$val["itemid"]]['video_cover'])  !!}" draggable="false" >
    </div>
    @elseif($val['tp']=='webgame' || $val['tp']=='vrgame')
    <img src="{!! static_image($games[$val["itemid"]]['logo'])  !!}" draggable="false" >
    </div>
    @elseif($val['tp']=='banner')
    <img src="{!! static_image($val["banner_url"])  !!}" draggable="false" >
    </div>
    @endif
    <div class="content">
      <div class="header">
      @if($val['tp']=='video')
       {{ $videos[$val["itemid"]]['video_name'] }}
      @elseif($val['tp']=='vrgame')
        {{ $games[$val["itemid"]]['name'] }}
      @else
      {{ $val["top_title"] }}
      @endif
      </div>
    <div class="meta">
        <a>{{ $val["tp"] }}</a>
      </div>
      <div class="description">
        {{ $val["target_url"] }}
      </div>
    </div>

      @if($val['tp']=='video')
        <div class="extra content action-edit" data-id="{{ $val['id'] }}" data-val="{{ $videos[$val["itemid"]]['video_name'] }}">
      @elseif($val['tp']=='vrgame')
        <div class="extra content action-edit" data-id="{{ $val['id'] }}" data-val="{{ $games[$val["itemid"]]['name'] }}">
      @else
      <div class="extra content action-edit" data-id="{{ $val['id'] }}" data-val="{{ $val["top_title"] }}">
      @endif
       编辑
    </div>
    </div>
    @endforeach
    </div>
    </div>
  </div>
  <div class="two wide column">
   <div class="ui segment">
      <div class="ui sticky sidetop">
        <h4 class="ui header">推荐分组</h4>
        <div class="ui vertical following fluid accordion text menu">
         @foreach($posGroup as $groupName=>$poses)
          <div class="item @if($groupName==$defaultGroup) active @endif">
            <a class="title @if($groupName==$defaultGroup) active  @endif"><i class="dropdown icon"></i><b>{{ $groups[$groupName] }}</b></a>
            <div class="content menu @if($groupName==$defaultGroup) active @endif">
            @foreach($poses as $pos)
            <a class="item @if($pos['posid']== $posid) active @endif " href="?choose={{ $pos['posid'] }}">{{ $pos['name'] }}</a>
             @endforeach
            </div>
          </div>
         @endforeach
          <!-- <div class="item">
            <a class="title"><i class="dropdown icon"></i> <b>Content</b></a>
            <div class="content menu">
            <a class="item" href="#">标题</a>
            </div>
          </div> -->
        </div>
      </div>
      </div>
  </div>
</div>



<div class="ui modal modal-del">
  <i class="close icon"></i>
  <div class="header">删除栏目</div>
  <div class="content">
  <input id="del_id" type="hidden"  >
  你确定要下线该栏目吗？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="deleteData(0)">取消 </div>
  <div class="ui positive button" onclick="deleteData(1)">确定 </div>
  </div>
</div>

<div class="ui modal modal-banner">
  <i class="close icon"></i>
  <div class="header">添加推荐 到 {{ $posName }}</div>
  <div class="content">
    <form class="ui form" onsubmit="return false;">
     <input   type="hidden"  id="top_id">
     <input type="hidden"  id="top_item_id" >
     <div class="inline fields">
          <label>推荐位内容</label>
          <div class="field">
          <div class="ui radio checkbox">
          <input type="radio" name="top_tp" value="vrgame">
          <label>vrgame</label>
          </div>
          </div>
           <div class="field">
          <div class="ui radio checkbox">
          <input type="radio" name="top_tp" value="video">
          <label>video</label>
          </div>
          </div>
          <div class="field">
          <div class="ui radio checkbox">
          <input type="radio" name="top_tp" value="banner">
          <label>banner</label>
          </div>
          </div>
     </div>
      <div class="field" style="display:none">
      <label>搜索内容</label>
      <div class="ui search right action left icon input">
      <i class="search icon"></i>
      <input class="prompt"  type="text" placeholder="Search">
      <div class="results"></div>
      </div>
      </div>
       <div class="field">
      <label>推荐标题</label>
      <input id="top_title" type="text" >
      </div>
       <div class="field">
      <label>副标题</label>
      <input id="top_sub_title" type="text" >
      </div>
      <div class="field">
      <label>推荐简介</label>
      <input id="top_desc" type="text" >
      </div>
      <div class="field">
      <label>链接</label>
      <input id="target_url" type="text" >
      </div>
      <div class="inline fields">
        <div class="field">
        <label>推荐图片</label>
        <div class="ui segment">
        <img id="banner_url" class="preview ui small image">
        </div>
        </div>
        <div class="field" id="banner_url_container">
        <button  class="ui teal button" id="banner_url_browser">选择</button>
        </div>
         <div class="field">
        <label>推荐ICON</label>
        <div class="ui segment">
        <img id="top_icon" class="preview ui tiny image">
        </div>
        </div>
        <div class="field" id="top_icon_container">
        <button  class="ui teal button" id="top_icon_browser">选择</button>
        </div>
      </div>
      <div class="inline fields">

      </div>
      <div class="field">
        <label>链接跳转方式</label>
        <select class="ui selection dropdown" id="top_link_tp">
          <option value="0">直接跳转</option>
          <option value="1">官网新标签打开，客户端新窗口打开</option>
          <option value="2">官网客户端全部新窗口打开（只建议打开页游时使用）</option>
        </select>
      </div>
    </form>
  </div>
  <div class="actions">
    <div class="ui button action-save">确定</div>
  </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
var del_id;
var edit_modal;
var posid = {{ $posid }};
var search_tp = '';
var loi = new loiForm();
var top_link_tp = $('#top_link_tp').dropdown();
var search_obj;
var top_item_id;

$('.ui.accordion').accordion();


$(function() {
  $("input[name='top_tp']").change(function(){
     updateSearch($(this).val());
  })

  $(".action-edit").click(function() {
    var id =  $(this).attr("data-id");
     var title =  $(this).attr("data-val");
    dataEdit(id,title)
  });

  $(".action-save").click(function(){
    var editData = loi.save();
    if(typeof(editData.err) != "undefined") {
      loiMsg(editData.err+"未填写");
      return
    } else {
        if(editData.top_tp.val=='banner' ) {
          if(editData.top_title.val=="") {
            loiMsg("未填写完整");
           return
          }
        } else {
          if(top_item_id=="") {
            loiMsg("未填写完整");
            return
          }
        }
        formData = loi.submit();
        formData.posid = posid;
        if(typeof(top_item_id)!="undefined") {
          formData.top_item_id = top_item_id;
        }
        permPost("/json/save/top_banner",formData,function(data){
           edit_modal.modal('hide');
           location.reload();
        });
    }
  });

  $(".action-del").click(function() {
    var that = $(this);
    var obj = that.parent();
    del_id = obj.attr('id').replace("card-","");
    del_modal = $('.ui.modal.modal-del').modal('show');
  })

})


function dataEdit(id,searchTitle) {
  loi.edit("top_banner",id,function(data){
     var banner_url_obj = new loiUploadContainer({
          id:"banner_url",
          upload:{
              tp:"bannerimg",
              error:function(){}
          }
      });
      var top_icon_obj = new loiUploadContainer({
          id:"top_icon",
          upload:{
              tp:"bannerimg",
              error:function(){}
          }
      });
    if(typeof(searchTitle)!="undefined") {
       $(".search .prompt").val(searchTitle);
    }
    updateSearch(data.top_tp.val);
    edit_modal = $('.ui.modal.modal-banner').modal('show');
  })
}

function updateSearch(val) {
  search_tp = val;
  if(val!="banner") {
    $('.ui.search').parent().show();
    search_obj = $('.ui.search').search({
       apiSettings: {
        url: '/vrhelp/search?tp='+search_tp+'&q={query}'
      },
      onSelect:function(result, response){
        top_item_id = result.id;
      }
    });
  } else {
    $('.ui.search').parent().hide();
  }
}

function deleteData(tp) {
  del_modal.modal('hide');
  if(tp==1) {
    if(del_id>0) {
      permPost("/vrhelp/rec/del",{id:del_id},function(data){
        location.reload();
      })
    }
  }
}



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
  permPost("/vrhelp/switchWeight",{drag:id,drop:dropId},function(data){
  })
}
</script>
@endsection
