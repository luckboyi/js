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

{{ $posCode }}

<div class="ui small button right floated blue" onclick="dataEdit(0,'')"><i class="plus icon"></i>新建推荐</div>

<div class="ui grid" style="clear:both">
  <div class="fourteen wide column">
    <div class="ui segment">
    <div class="ui  link cards">
    @foreach($data as $val)
    <div id="card-{{ $val['id'] }}" class="card" draggable="true" ondrop="drop(event)" ondragstart="drag(event)" ondragover="allowDrop(event)">
    <i class="large red close icon action-del"></i>
    <div class="image">
    @if($val['cover'])
    <img src="{!! static_image($val['cover'])  !!}" draggable="false" >
    @else
      @if($val['tp']=="news" || $val['tp']=="video" || $val['tp']=="pc")
      <img src="{!! static_image($articles[$val["itemid"]]['article_cover'])  !!}" draggable="false" >
      @elseif($val['tp']=="game")
      <img src="{!! static_image($games[$val["itemid"]]['game_image'])  !!}" draggable="false" >
      @endif
    @endif
    </div>
    <div class="content">
    @if($val['title'])
      <div class="header">{{ $val['title']  }} </div>
    @else
      @if($val['tp']=="news" || $val['tp']=="video" || $val['tp']=="pc")
       <div class="header">{{ $articles[$val["itemid"]]['article_title']  }} </div>
      @elseif($val['tp']=="game")
      <div class="header">{{ $games[$val["itemid"]]['game_name']  }} </div>
      @endif
    @endif
    <div class="meta">
        <a>{{ $val["tp"] }}</a>
      </div>
      <div class="description">
        {{ $val["target_url"] }}
      </div>
    </div>
      <div class="extra content">
       @if($val['tp']=="news" || $val['tp']=="video" || $val['tp']=="pc")
       <div class="action-edit" data-val="{{ $articles[$val["itemid"]]['article_title'] }}" data-id="{{ $val['id'] }}" >编辑</div>
       @elseif($val['tp']=="game")
       <div class="action-edit" data-val="{{ $games[$val["itemid"]]['game_name'] }}" data-id="{{ $val['id'] }}" >编辑</div>
       @else
       <div class="action-edit" data-id="{{ $val['id'] }}" >编辑</div>
       @endif
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
            <a class="item @if($pos['pos_code']== $posCode) active @endif " href="?choose={{ $pos['pos_code'] }}">{{ $pos['pos_name'] }}</a>
             @endforeach
            </div>
          </div>
         @endforeach
        </div>
      </div>
      </div>
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

<div class="ui modal modal-add">
  <i class="close icon"></i>
  <div class="header">添加推荐到: {{ $posName }}</div>
  <div class="content">
    <form class="ui form " onsubmit="return false;">
      <input type="hidden"  id="top_id" >
      <input type="hidden"  id="top_itemid" >
       <div class="inline fields">
          <label>推荐位内容</label>
          <div class="field">
          <div class="ui radio checkbox">
          <input type="radio" name="top_tp" value="news">
          <label>news</label>
          </div>
          </div>
          <div class="field">
          <div class="ui radio checkbox">
          <input type="radio" name="top_tp" value="pc">
          <label>pc</label>
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
          <input type="radio" name="top_tp" value="game">
          <label>game</label>
          </div>
          </div>
          <div class="field">
          <div class="ui radio checkbox">
          <input type="radio" name="top_tp" value="banner">
          <label>banner</label>
          </div>
          </div>
          <div class="field">
          <div class="ui radio checkbox">
          <input type="radio" name="top_tp" value="sort">
          <label>游戏分类推荐</label>
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
      <div class="field recommend_sort" style="display:none">
      <label>分类推荐</label>
      <select  id="recommend_sort" name="recommend_sort" class="ui selection dropdown">
      <option value="">请选择</option>
      {!! $blade->showHtmlClass('recommend_sort','','select') !!}
      </select>
      </div>
      <div class="field game_category" style="display:none">
      <label>游戏分类</label>
      <select  id="game_category" name="game_category" class="ui selection dropdown">
      <option value="">请选择</option>
      {!! $blade->showHtmlClass('vrgame','','select') !!}
      </select>
      </div>
      <div class="field game_tags" style="display:none">
      <label>游戏标签</label>
      <select  id="game_tags" name="game_tags" class="ui selection dropdown">
      <option value="">请选择</option>
      {!! $blade->showHtmlClass('vrgame_tags','','select') !!}
      </select>
      </div>
      <div class="field game_device" style="display:none">
      <label>支持设备</label>
      <select  id="game_device" name="game_device" class="ui selection dropdown">
      <option value="">请选择</option>
      {!! $blade->showHtmlClass('vr_device','','select') !!}
      </select>
      </div>
      <div class="field game_price" style="display:none">
      <label>价格</label>
      <select  id="game_price" name="game_price" class="ui selection dropdown">
      <option value="">请选择</option>
      {!! $blade->showHtmlClass('searchPrice','','select') !!}
      </select>
      </div>
      <div class="field">
      <label>推荐标题</label>
      <input id="top_title" type="text" >
      </div>
      <div class="field">
      <label>推荐简介</label>
      <input id="top_intro" type="text" >
      </div>
      <div class="field">
      <label>链接</label>
      <input id="top_target_url" type="text" >
      </div>
      <div class="inline fields">
        <div class="field">
        <label>推荐图片</label>
        <div class="ui segment">
        <img id="top_cover" class="preview ui tiny image">
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
    <div class="ui button">取消</div>
    <div class="ui button action-save">确定</div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
var del_id;
var edit_modal;
var posCode = '{{ $posCode }}';
var search_tp = '';
var loi = new loiForm();
// var game_category = $('#game_category').dropdown();
// var game_tags = $('#game_tags').dropdown();
var warning_msg = $(".warning.message");
var defaultImg = "semantic/images/image.png";
var top_cover_obj;
var search_obj;
var top_itemid;
$('.ui.accordion').accordion();


$(function(){
  $("#recommend_sort").change(function(){
    var sortArr = [
        {
            'id'   : 1,
            'name'  :  "设备",
            'code'  :  'game-device',
        },
        {
            'id'    :  2,
            'name'  :  "游戏分类",
            'code'  :  'game-category',
        },
        {
            'id'    :  3,
            'name'  :  "标签",
            'code'  :  'game-tag',
        },
        {
            'id'    :  4,
            'name'  :  "价格",
            'code'  :  'game-price',
        },
    ];
    var sortId = $(this).val();
    if(sortId == 1) {
      $(".game_device").show();
      $(".game_tags").hide();
      $(".game_category").hide();
      $(".game_price").hide();
    } else if(sortId == 2) {
      $(".game_device").hide();
      $(".game_tags").hide();
      $(".game_category").show();
      $(".game_price").hide();
    } else if(sortId == 3) {
      $(".game_category").hide();
      $(".game_device").hide();
      $(".game_tags").show();
      $(".game_price").hide();
    } else if(sortId == 4) {
      $(".game_category").hide();
      $(".game_tags").hide();
      $(".game_device").hide();
      $(".game_price").show();
    }
    $("#top_intro").val(sortArr[sortId-1].code);
    // console.log($(this).find("option:selected").text());
  });

  $("#game_category,#game_tags,#game_device,#game_price").change(function(){
    var selectText = $(this).find("option:selected").text();
    $("#top_title").val(selectText);
    $("#top_target_url").val($(this).val());
    // console.log(selectText);
  });

  $(".action-save").click(function(){
    var editData = loi.save();
  //  console.log(editData);
    if(typeof(editData.err) != "undefined") {
      warning_msg.html("还有未填写项目");
      warning_msg.show();
    } else {
      warning_msg.hide();
      formData = loi.submit();
      formData.pos_code = posCode;
      if(typeof(top_itemid)!="undefined") {
        formData.top_itemid = top_itemid;
      }
      permPost("/json/save/vronline_top",formData,function(data){
       edit_modal.modal('hide');
      location.reload();
      });
    }
  });

  $(".action-edit").click(function() {
     var id =  $(this).attr("data-id");
     var title = $(this).attr("data-val");
     dataEdit(id,title)
  });

  $(".action-del").click(function() {
    var that = $(this);
    var obj = that.parent();
    del_id = obj.attr('id').replace("card-","");
    del_modal = $('.ui.modal.modal-del').modal('show');
  })
  $("input[name='top_tp']").change(function(){
       updateSearch($(this).val());
  })
});

function dataEdit(id,searchTitle) {
  loi.edit("vronline_top",id,function(data){
    if(typeof(searchTitle)!="undefined") {
       $(".search .prompt").val(searchTitle);
    }
    updateSearch(data.top_tp.val);
    edit_modal = $('.ui.modal.modal-add').modal('show');
    warning_msg.hide();
    if(typeof(top_cover_obj)=="undefined") {
        top_cover_obj = new loiUploadContainer({
        container:"cover_container",
        choose:"cover_browser",
        ext:"jpg,png,gif",
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

function updateSearch(val) {
  search_tp = val;

  if(val == "sort") {
    $('.recommend_sort').show();
  } else {
    $('.recommend_sort').hide();
    $(".game_category").hide();
    $(".game_device").hide();
    $(".game_tags").hide();
    $(".game_price").hide();
  }

  if(val!="banner" && val!="sort") {
    $('.ui.search').parent().show();
    search_obj = $('.ui.search').search({
       apiSettings: {
        url: '/vronline/search?tp='+search_tp+'&q={query}'
      },
      onSelect:function(result, response){
        top_itemid = result.id;
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
      permPost("/json/del/vronline_top",{del_id:del_id},function(data){
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
  permPost("/vronline/switchWeight",{drag:id,drop:dropId},function(data){
  })
}

</script>
@endsection
