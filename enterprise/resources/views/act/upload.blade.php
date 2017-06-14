@include('layouts.baidu_js')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>2017VR全景视频大赛-VRONLINE && 3D播播</title>
		<link rel="stylesheet" href="{{ static_res('/videoGame/style/base.css') }} ">
		<link rel="stylesheet" href="{{ static_res('/videoGame/style/3dbobo.css') }}">
    </head>
    <body>
	<div class="uploading_con tac">
		<div class="in_uploading_con">
			<div class="uploading_head pr">
				<h3 class="f20">视频上传</h3>
				<a href="/3dbb" class="pa">返回首页</a>
			</div>
			<div class="uploading_body">
				<div class="uploading_content">
					<p class="pr"><i>※</i>选择传送方式</p>
					<select id="source_tp">
						<option value="1">直传</option>
						<option value="2">第三方</option>
					</select>
				</div>
				<div class="uploading_content source_1">
					<p><i>※</i>视频地址</p>
                      <div class="verification clearfix uploadVideo"  id="video_container">
                        <input class="fl" type="text" id="source_url_1" readonly="true" />
                        <p class="fl send" id="video_browser">上传</p>
                        <p class="fl schedule" style="display: none"><span class="num upload-per"></span></p>
                    </div>

				</div>
                <div class="uploading_content source_2" style="display: none">
                    <p><i>※</i>视频地址</p>
                    <input type="text" id="source_url_2" placeholder="请输入视频地址">
                </div>
				<div class="uploading_content uploading_content2 pr">
					<p><i>※</i>视频名称</p>
					<input type="text" id="video_name" placeholder="不超过11字">
					<p class="pa erroMsg"></p>
				</div>
				<div class="uploading_content">
					<p><i>※</i>视频类型</p>
					<select id="is_vr">
						<option value="1">3D</option>
						<option value="2">全景</option>
					</select>
				</div>
                <div class="uploading_content">
                    <p><i>※</i>参与组别</p>
                    <select id="group_tp">
                        <option value="1">CG制作单元</option>
                        <option value="2">实拍全景【专业组】</option>
                        <option value="3">实拍全景【玩家组】</option>
                    </select>
                    <p id="group_tp_desc" class="f12"></p>
                </div>
				<div class="uploading_content uploading_content3 pr">
					<p><i>※</i>视频简介</p>
					<textarea  id="intro" cols="30" rows="8" placeholder="不超过200字"></textarea>
					<p class="pa erroMsg"></p>
				</div>
				<div class="uploading_content">
					<p><i>※</i>横向缩略图(小于100K)</p>
					<div class="img_con small_img pr" id="video_w_container">
						<img src="{{ static_res('/videoGame/images/h_image.jpg') }}" height="187" width="355" data="" id="video_w_browser">
					</div>
				</div>
				<div class="uploading_content ">
					<p><i>※</i>竖向缩略图(小于100K)</p>
					<div  class="img_con big_img pr" id="video_h_container">
						<img src="{{ static_res('/videoGame/images/s_image.jpg') }}" height="331" width="233" data="" id="video_h_browser">
					</div>
				</div>
			</div>
			<div class="uploading_foot">
				<p class="submit_btn f20" onclick="uploadSubmit()">同意上传协议并提交</p>
				<p class="f14">请仔细阅读开发者协议<a href="/3dbbprotocol" target="_blank">[上传协议]</a></p>
				<p  class="f14">如不同意请关闭本页面</p>
			</div>

		</div>
	</div>
    </body>
    <script type="text/javascript" src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
    <script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
    <script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
    <script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
    <script type="text/javascript">
    var desc = [
        '视频制作中混合以虚拟角色形象、虚拟场景的360°VR全景类视频（包括3D全景视频）；同时包含：实景+虚拟角色、虚拟场景+人物的形式，不限制作软件包括 MAYA 、3Dmax、blender、Softimage、C4D、Unity、MMD等制作软件',
        '使用例如GoPro、insta360等实景拍摄设备采录的影响素材进行剪辑制作而成的360°全景视频（包括3D全景视频）；镜头构建基于现实场景和真实人物角色为主，使用后期制作软件不限于AE、NUKE、Pr、AVP、APG等进行后期渲染、特效包装为辅',
        '使用例如GoPro、insta360等实景拍摄设备采录的影响素材进行剪辑制作而成的360°全景视频（包括3D全景视频）；镜头构建基于现实场景和真实人物角色为主，使用后期制作软件不限于AE、NUKE、Pr、AVP、APG等进行后期渲染、特效包装为辅',
        ];
    	$(function(){
    		$('.uploading_content2 input').blur(function() {
    			//alert(1)
    			var val = $(this).val();
    			if(val.length == 0){
    				$(this).nextAll('.erroMsg').text('不能为空').addClass('erroColor')
    			}
    			if(val.length >11){
    				$(this).nextAll('.erroMsg').text('不能超过11字').addClass('erroColor')
    			}
    		});
    		$('.uploading_content3 textarea').blur(function() {
    			var val = $(this).val();
    			if(val.length == 0){
    				$(this).nextAll('.erroMsg').text('不能为空').addClass('erroColor')
    			}
    			if(val.length >200){
    				$(this).nextAll('.erroMsg').text('不能超过200字').addClass('erroColor')
    			}
    		});
    		$('.uploading_content2 textarea').focus(function(event) {
    				$(this).nextAll('.erroMsg').text('').removeClass('erroColor')

    		});

            $("#group_tp_desc").text(desc[0])
            $("#group_tp").change(function(a,b) {
                 var desc_id = $("#group_tp").val()
                 $("#group_tp_desc").text(desc[desc_id-1])
            });

            $("#source_tp").change(function(){
                var tp = $("#source_tp").val();
                if(tp==1) {
                    $(".source_1").show();
                    $(".source_2").hide();
                } else {
                    $(".source_1").hide();
                    $(".source_2").show();
                }
            })
            var video_w_obj = new loiUploadContainer({
                container:"video_w_container",
                choose:"video_w_browser",
                ext:"jpg,png",
                max:200000,
                upload:{tp:"bannerimg",success:function(json){
                    var jsonResult = $.parseJSON(json);
                    $("#video_w_browser").attr("src",static_image(jsonResult.data.fileid));
                    $("#video_w_browser").attr("data",jsonResult.data.fileid);
                },error:function(msg){
                if(typeof(msg)=="string") {
                    loiNotifly(msg)
                }
                }},
                filesAdd:function(files){}
            });

             var video_h_obj = new loiUploadContainer({
                container:"video_h_container",
                choose:"video_h_browser",
                ext:"jpg,png",
                max:200000,
                upload:{tp:"bannerimg",success:function(json){
                var jsonResult = $.parseJSON(json);
                    $("#video_h_browser").attr("src",static_image(jsonResult.data.fileid));
                    $("#video_h_browser").attr("data",jsonResult.data.fileid);
                },error:function(msg){
                if(typeof(msg)=="string") {
                    loiNotifly(msg)
                }
                }},
                filesAdd:function(files){}
            });


             var video_h_obj = new loiUploadContainer({
                container:"video_container",
                choose:"video_browser",
                ext:"mp4",
                upload:{tp:"netcvideo",success:function(json){
                    var res = $.parseJSON(json)
                    $("#source_url_1").val('http://netctvideo.vronline.com/'+res.key);
                    $("#video_browser").text("上传");
                    $("#"+video_h_obj.inputId).prop("disabled",false);
                },sliceback:function(per){
                    var num = new Number(per);
                    num = num*100;
                    var p = num.toFixed(2)+"%";
                    $('.upload-per').css('width',p);
                    $('.upload-per').text(p);
                },error:function(msg){
                    if(typeof(msg)=="string") {
                        loiNotifly(msg)
                    } else if(typeof(msg)=="object") {
                        if(typeof(msg.responseText)!="undefined") {
                            var errJson =  $.parseJSON(msg.responseText)
                            loiNotifly("文件已经上传,请更换名称后重新上传",3000)
                            $('.upload-per').parent().hide();
                        }
                    }
                    $("#video_browser").text("上传");
                    $("#"+video_h_obj.inputId).prop("disabled",false);
                }},
                filesAdd:function(files){
                    $("#"+video_h_obj.inputId).prop("disabled",true);
                    $("#video_browser").text("上传中...");
                    $('.upload-per').css('width',0);
                    $('.upload-per').parent().show();
                }
            });
    	})

    	function uploadSubmit() {
            var group_tp = $("#group_tp").val();
    		var source_tp = $("#source_tp").val();
            var source_url_1 = $("#source_url_1").val();
            var source_url_2 = $("#source_url_2").val();
    		var video_name = $("#video_name").val();
    		var is_vr = $("#is_vr").val();
    		var intro = $("#intro").val();
            var video_w_img = $("#video_w_browser").attr('data');
            var video_h_img = $("#video_h_browser").attr('data');
            var source_url;
            if(source_tp==1) {
                source_url = source_url_1
            } else {
                source_url = source_url_2
            }
    		if(video_name.length<4 || video_name.length>11) {
    			loiNotifly("请输入视频名称")
    			return false;
    		}
            if(source_tp!=1 && source_tp!=2) {
                loiNotifly("请选择视频链接方式")
                return false;
            }
            if(source_url.length<5) {
                loiNotifly("请上传视频")
                return false;
            }
    		if(intro.length<8 || intro.length>200) {
    			loiNotifly("请输入视频视频简介")
    			return false;
    		}
    		if(is_vr!=1 && is_vr!=2) {
    			loiNotifly("请选择视频类型")
    			return false;
    		}
            if(video_w_img.length<5 || video_h_img.length<5) {
                loiNotifly("请上传视频封面图片")
                return false;
            }
            if(group_tp!=1 && group_tp!=2 && group_tp!=3) {
                loiNotifly("请选择参与组别")
                return false;
            }

    		var fromData = {}
            fromData.name = video_name
    		fromData.source_tp = source_tp
            fromData.source_url = source_url
    		fromData.is_vr = is_vr
    		fromData.intro = intro
    		fromData.thumb_w = video_w_img
            fromData.thumb_h = video_h_img
            fromData.group_tp = group_tp
    		$.post('/uploadVideoSubmit',fromData,function(res) {
    			if(res.code==0) {
                    loiNotifly("恭喜您,上传视频成功!",2000,function(){
                        location.href = "/3dbb"
                    })
                } else {
                    loiNotifly(res.msg);
                }
    		},"json");
    	}
    </script>
</html>
@yield("baidu_stat")
