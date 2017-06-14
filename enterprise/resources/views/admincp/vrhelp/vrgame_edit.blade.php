@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')

<div class="ui small button right floated blue action-save" ><i class="save icon"></i>保存</div>
<div class="ui small button right floated orange" onclick="history.back(-1)">返回列表</div>



@endsection

@section('javascript')
<script type="text/javascript">
var defaultImg = "semantic/images/image.png";
var game_id = '{{ $id }}';

var loi = new loiForm();
$(function () {
    loi.edit("vrhelp_vrgame",game_id,function(data){
        if(typeof(game_logo_obj)=="undefined") {
            game_logo_obj = new loiUploadContainer({
                container:"game_logo_container",
                choose:"game_logo_browser",
                ext:"jpg,png",
                upload:{
                    tp:"vrgame",addParams:{appid:game_id,"pic":"icon"},
                    success:function(json){
                        var jsonResult = $.parseJSON(json);
                        var path = jsonResult.data.fileid;
                        $("#game_logo").attr('src',img_domain+path);
                        $("#game_logo").attr('data-val',path);
                    },error:function(){}},
                    filesAdd:function(files){
                    // console.log(files)
                    }
            });
        }
    });
});
</script>
@endsection
