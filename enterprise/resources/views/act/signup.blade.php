@include('layouts.baidu_js')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
		<title>2017VR全景视频大赛-VRONLINE&&3D播播</title>
		<link rel="stylesheet" href="{{ static_res('/videoGame/style/base.css') }} ">
		<link rel="stylesheet" href="{{ static_res('/videoGame/style/3dbobo.css') }}">
    </head>
    <body>
	<div class="uploading_con tac submit_con">
		<div class="in_uploading_con">
			<div class="uploading_head pr">
				<h3 class="f20">大赛报名</h3>
				<a href="/3dbb" class="pa">返回首页</a>
			</div>
			<div class="uploading_body">
				<div class="uploading_content company_con pr">
					<p><i>※</i>参赛者名称</p>
					<input id="name" type="text" placeholder="公司/工作室/团队/个人作者名称">
					<p class="pa erroMsg"></p>
				</div>
				@if($bindMobile)
				<div class="uploading_content  pr">
					<p><i>※</i>手机号</p>
					<input id="mobile" type="text" placeholder="请输入你的手机号" value="{{ $bindMobile }}" readonly="true">
					<p class="pa erroMsg"></p>
				</div>
				@else
				<div class="uploading_content phone_con  pr">
					<p><i>※</i>手机号</p>
					<input id="mobile" type="text" placeholder="请输入你的手机号">
					<div class="verification clearfix">
						<input class="fl" type="text" id="code" placeholder="请输入验证码" />
						<p class="fl send" id="sendBtn" onclick="sendCode()">发 送</p>
					</div>
					<p class="pa erroMsg"></p>
				</div>
				@endif
				<div class="uploading_content">
					<p><i>※</i>参与组别</p>
					<select id="tp">
						<option value="1">CG制作单元</option>
						<option value="2">实拍全景【专业组】</option>
                        <option value="3">实拍全景【玩家组】</option>
					</select>
					<p id="tp_desc" class="f12"></p>
				</div>
				<div class="uploading_content city_select">
					<p><i>※</i>省/城市</p>
					<div class="select-box clearfix">
                        <select class="fl" tabindex="1" name="province" id="province"><option value="">请选择省份</option></select>
                        <select class="fl" tabindex="1" name="city" id="city"><option value="">请选择城市</option></select>
                        <select class="fl" tabindex="1" name="district" id="district"><option value="">请选择区域</option></select>
					</div>
				</div>
				<div class="uploading_content  pr">
					<p><i>※</i>常用拍摄设备</p>
					<input id="device" type="text" placeholder="请输入你的常用拍摄设备">
					<p class="pa erroMsg"></p>
				</div>
				<div class="uploading_content  pr">
					<p><i>※</i>常用制作软件</p>
					<input id="soft" type="text" placeholder="请输入你的常用制作软件">
					<p class="pa erroMsg"></p>
				</div>
			</div>
			<div class="uploading_foot">
				<p class="submit_btn f20" onclick="signUp()">提交</p>
			</div>
		</div>
	</div>

	<div class="maskLayer" style="display: none">
		<div class="popup">
			<div class="popupCon">
				<div class="clearfix work">
					<div class="fl" onclick="goTo('/3dupload')">
						<p class="f24 submit">提交作品</p>
						<p>大赛正式比赛时间为2017.6.1—2017.7.18，审核通过后的参赛作品将在正式比赛开始后展示在3D播播的大赛页面，越早提交作品、位置越靠前。</p>
					</div>
					<div class="fr again" onclick="goTo('/3dbb')">
						<p class="submit" >
							<span class="f24">下次再来</span>
							<span>（视频还未完成，以后再提交）</span>
						</p>
						<p>添加大赛客服微信，大赛实时信息一手掌握</p>
						<p>微信客服：3D播播-小溪</p>
						<p class="red">搜索微信号：3116751169</p>
					</div>
				</div>
			</div>
		</div>
	</div>

    </body>
    <script type="text/javascript" src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
     <script type="text/javascript" src="{{ static_res('/common/js/citypicker.min.js')}}"></script>
     <script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
    <script type="text/javascript">
    	var desc = [
        '视频制作中混合以虚拟角色形象、虚拟场景的360°VR全景类视频（包括3D全景视频）；同时包含：实景+虚拟角色、虚拟场景+人物的形式，不限制作软件包括 MAYA 、3Dmax、blender、Softimage、C4D、Unity、MMD等制作软件',
    	'使用例如GoPro、insta360等实景拍摄设备采录的影响素材进行剪辑制作而成的360°全景视频（包括3D全景视频）；镜头构建基于现实场景和真实人物角色为主，使用后期制作软件不限于AE、NUKE、Pr、AVP、APG等进行后期渲染、特效包装为辅',
        '使用例如GoPro、insta360等实景拍摄设备采录的影响素材进行剪辑制作而成的360°全景视频（包括3D全景视频）；镜头构建基于现实场景和真实人物角色为主，使用后期制作软件不限于AE、NUKE、Pr、AVP、APG等进行后期渲染、特效包装为辅',
    	];
    	var canSend = true
    	$(function(){
    		$('.company_con input').blur(function() {
    			//alert(1)
    			var val = $(this).val();
    			if(val.length == 0){
    				$(this).nextAll('.erroMsg').text('不能为空').addClass('erroColor')
    			}
    		});
    		$('.phone_con input').blur(function() {
    			var val = $(this).val();
    			var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    			if(val.length == 0){
    				$(this).nextAll('.erroMsg').text('不能为空').addClass('erroColor')
    			}
    			if (!myreg.test(val)){
    				$(this).nextAll('.erroMsg').text('请输入正确的手机号').addClass('erroColor')
    			}
    		});
    		$('.uploading_content input').focus(function(event) {
    			$(this).nextAll('.erroMsg').text('').removeClass('erroColor')
    		});

            $("#tp_desc").text(desc[0])
            $("#tp").change(function(a,b) {
            	 var desc_id = $("#tp").val()
            	 $("#tp_desc").text(desc[desc_id-1])
            });
    	});
    	function signUp() {
    		var name = $("#name").val();
    		var mobile = $("#mobile").val();
    		var tp = $("#tp").val();
    		var device = $("#device").val();
    		var soft = $("#soft").val();
    		var code = $("#code").val();
            var province = $("#province").val();
            var city = $("#city").val();
            var district = $("#district").val();

    		if(name.length<2 || name.length>30) {
    			loiNotifly("请输入正确的名称")
    			return false;
    		}
    		if(tp!=1 && tp!=2 && tp!=3) {
    			loiNotifly("请选择参与组别")
    			return false;
    		}
    		var loiV = new loiValidator
    		if(!loiV.check('mobileCN',mobile)) {
    			loiNotifly(loiV.errorMsg())
    			return false;
    		}
            if(province.length<1 || city.length<1 || district.length<1) {
                loiNotifly("请选择省市区")
                return false;
            }
    		if(device.length<2 || device.length>50) {
    			loiNotifly("请输入拍摄设备")
    			return false;
    		}
    		if(soft.length<2 || soft.length>100) {
    			loiNotifly("请输入制作软件")
    			return false;
    		}
    		if(typeof(code)!='undefined' && code.length!=6 ) {
    			loiNotifly("请输入验证码")
    			return false;
    		}
    		var fromData = {}
    		fromData.name = name
    		fromData.mobile = mobile
    		fromData.tp = tp
    		fromData.device = device
    		fromData.soft = soft
            fromData.district = district
    		if(typeof(code)!='undefined') {
    			fromData.code = code
    		}
    		$.post('/signUpSubmit',fromData,function(res) {
    			if(res.code==0) {
    				loiNotifly("恭喜您报名成功！",2000,function(){
    					$(".maskLayer").show();
    				});
    			} else {
    				loiNotifly(res.msg);
    			}
    		},"json");
    	}

    	function sendCode() {
    		var mobile = $("#mobile").val();
    		var loiV = new loiValidator
    		if(!loiV.check('mobileCN',mobile)) {
    			loiNotifly(loiV.errorMsg())
    			return false;
    		}
    		if(!canSend) {
    			loiNotifly("请稍后获取");
    			return false;
    		}
    		$.post('/signUpCode',{mobile:mobile},function(res) {
    			if(res.code==0) {
    				loiNotifly("获取验证码成功");
		    		canSend = false
		    		var sec = 60
		    		var loop = setInterval(function(){
		    			if(sec<1) {
		    				$("#sendBtn").text('发送');
		    				canSend = true;
		    				clearInterval(loop);
		    				return;
		    			}
		    			$("#sendBtn").text(sec)
		    			sec--
		    		},1000);
    			} else {
                    if(typeof(res.msg)!="undefined") {
                        loiNotifly(res.msg);
                    } else {
                        loiNotifly("请稍后获取");
                    }

    			}
    		},"json");
    	}

    	function goTo(tp) {
    		location.href = tp
    	}
    </script>
</html>
@yield("baidu_stat")
