@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<link rel="stylesheet" type="text/css" href="/admincp/editor/css/editor.min.css">
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
<script language="JavaScript" src="/admincp/editor/js/editor.min.js?v=1"></script>
@endsection
@section('content')


<div class="ui  left floated vertical segment auto-save" style="border:none">
</div>
<div class="ui small button right floated blue action-save" ><i class="save icon"></i>保存</div>
<div class="ui small button right floated green" onclick="localImg()">图片本地</div>
<div class="ui small button right floated orange" onclick="tickBack()">返回列表</div>




<div class="ui grid" style="clear:both">
  <div class="ten wide column">
        <form class="ui form"  onsubmit="return false;">
            <input id="article_id" type="hidden">
        <div class="field">
            <label>标题</label>
            <input id="article_title" type="text">
        </div>
        <div class="two fields">
            <div class="field">
            <label>分类</label>
            <select id="article_category" class="ui dropdown"   >
            <option value="">请选择</option>
            {!! $blade->showHtmlClass('vronline_pc','','select') !!}
            </select>
            </div>
            <div class="field">
            <label>标签</label>
            <input id="article_tag" type="text">
            </div>
        </div>

        <div class="field">
            <div id="article_content" style="height:500px;">
            </div>
        </div>
        </form>
  </div>
  <div class="six wide column">
     <form class="ui form"  onsubmit="return false;">
        <div class="inline fields">
        <div class="field">
        <label>封面图片</label>
        <div class="ui segment">
        <img id="article_cover" class="preview ui small image">
        </div>
        </div>
        <div class="field" id="article_cover_container">
        <button  class="ui teal  button" id="article_cover_browser">选择</button>
        </div>
        </div>
        <div class="field">
            <label>评分</label>
            <input id="article_pc_match" type="text">
        </div>
        <div class="field">
            <label>游戏ID</label>
            <input id="article_target_id" type="text">
        </div>
        <div class="field">
            <label>副标题</label>
            <input id="article_alias" type="text">
        </div>
        <div class="field">
            <label>关键词</label>
            <input id="article_keywords" type="text">
        </div>
         <div class="field">
            <label>来源</label>
            <input id="article_source" type="text">
        </div>
    </form>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
var defaultImg = "semantic/images/image.png";
var article_category = $('select.dropdown').dropdown();
var article_id = '{{ $id }}';
var article_content = new wangEditor('article_content');
var last_content;
var loi = new loiForm();

var video_cover_obj = new loiUploadContainer({
    container:"article_cover_container",
    choose:"article_cover_browser",
    ext:"jpg,png",
    upload:{tp:"newsimg",success:function(json){
      var jsonResult = $.parseJSON(json);
      var path = jsonResult.data.fileid;
       $("#article_cover").attr('src',img_domain+path);
       $("#article_cover").attr('data-val',path);
    },error:function(){}},
      filesAdd:function(files){
       // console.log(files)
    }
});


$(function () {
    article_content.config.menus = [
        'source',
        '|',
        'bold',
        'underline',
        'italic',
        'strikethrough',
        'eraser',
        'forecolor',
        'bgcolor',
        '|',
        'quote',
        'fontfamily',
        'fontsize',
        'head',
        'unorderlist',
        'orderlist',
        'alignleft',
        'aligncenter',
        'alignright',
        '|',
        'link',
        'unlink',
        'table',
        'emotion',
        'img',
        'video',
        'insertcode',
        'undo',
        'redo',
        'fullscreen'
    ];
    article_content.config.uploadImgUrl = '/upload';
    article_content.config.uploadParams = {cos:true,tp:'newsimg'};
    article_content.create();

    loi.edit("vronline_pc",article_id,function(data){
        last_content = data.article_content.val;
    });
    //setInterval
    setInterval(function() {
         save(1);
    },10000);

});

function tickBack() {
  location.href = "/vronline/pc";
}


function uploadInit() {
     var article_img_obj = new loiUploadContainer({
        container:article_content.customUploadContainerId,
        choose:article_content.customUploadBtnId,
        ext:"jpg,png",
        upload:{tp:"newsimg",success:function(json){
          var jsonResult = $.parseJSON(json);
          var path = jsonResult.data.fileid;
          article_content.command(null, 'insertHtml', '<img src="' +  img_domain+path + '" style="max-width:100%;"/>');
        },error:function(){}},
          filesAdd:function(files){
           //\\ console.log(files)
        }
    });
}




function save(style) {
    var editData = loi.save();
    if(typeof(editData.err) == "undefined") {
        formData = loi.submit();
        if(formData.article_content!=last_content || style==2) {
            permPost("/json/save/vronline_pc",formData,function(res){
                if(res.code!=0) {
                    loiMsg(res.msg);
                } else {
                    last_content = formData.article_content;
                    if(typeof(res.data)!="undefined" && typeof(res.data.id)!="undefined" ) {
                        article_id = res.data.id;
                        $("#article_id").val(article_id);
                    }
                    if(style==1) {
                        var date = new Date();
                        $(".auto-save").html("稿件于"+date.pattern("yyyy-MM-dd hh:mm:ss") +"自动保存成功");
                        console.log("auto save");
                    } else {
                        loiMsg("保存草稿成功",function(){},'success');
                    }
                }
            },"json");
        }
    } else {
        if(style==2) {
            loiMsg("还有项目未填写");
        }
    }
}

$(".action-save").click(function(){
   save(2);
});


function localImg() {
    var html =  article_content.$txt.html();
    permPost("/news/localimg",{html:html},function(res) {
        if(typeof(res.code)!="undefined" && res.code==0) {
            article_content.$txt.html(res.data.html);
            if(typeof(res.data.cover)!="undefined") {
                $("#article_cover").attr('src',img_domain+res.data.cover);
                $("#article_cover").attr('data-val',res.data.cover);
            }

            loiMsg("图片本地成功",function(){},'success');
        }
    },"json");
}
</script>
@endsection
