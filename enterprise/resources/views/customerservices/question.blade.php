@extends('news.layout')

@section('meta')
<title>VRonline客服中心</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{static_res("/news/style/customer.css")}}" />
<script language="JavaScript" src="{{ static_res('/assets/loi/cos.js') }}"></script>
<script language="JavaScript" src="{{ static_res('/assets/loi/loiupload.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/swfobject.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/loi/md5.js') }}"></script>
@endsection

@section('content')
<div class="container" >
		<div class="clearfix all_con">
		<div class="crumbs clearfix" style="margin-bottom:20px">
			<a class="fl" href="/customer/service">客服中心</a>
			<span class="fl">&gt;</span>
			<a class="fl" href="#">提交问题</a>
		</div>
			<div class="fl left" >
				<ul>
					@foreach($questionTps as $questionTp)
					<li class="@if($questionTp['id']==$tp) cur @endif">
						<a class="clearfix" href="/customer/service/question/{{ $questionTp['id'] }}">
							<i class="fl {{ $questionTp['icon'] }}"></i>
							<span>{{ $questionTp['name'] }}</span>
							<i class="fr"></i>
						</a>
					</li>
					@endforeach
					<li>
						<a class="clearfix" href="/customer/service/myquestion">
							<i class="fl queryResults"></i>
							<span>查询结果</span>
							<i class="fr"></i>
						</a>
					</li>
				</ul>
			</div>
			<div class="fl right">
				<ul>
					<!--账号密码-->
					<li class="list_con account cur">
						<div class="account_con">
							<input type="hidden" id="tp" value="{{ $tp }}" />
							<div class="clearfix">
								<span class="fl"><em>*</em>我的账号</span>
								<input type="text" id="account" placeholder="请输入您的账号" />
							</div>
							@if(isset($questionTps[$tp]))
							<div class="clearfix">
								<span class="fl"><em>*</em>问题分类</span>
								<select id="sub_tp">
									@foreach($questionTps[$tp]['sub'] as $subTp)
									<option value="{{ $subTp['id'] }}">{{ $subTp['name'] }}</option>
									@endforeach
								</select>
							</div>
							@endif

							<div class="clearfix">
								<span class="fl"><em>*</em>问题描述</span>
								<div class="fl text_wrap">
									<textarea id="title"  class="text" placeholder="为保障账号安全，请勿在描述中填写账号或密码等信息。"></textarea>
								</div>

								<!-- <div class="fl clearfix upload" id="question_screenshots_container">
									<span class="fl submit upload_img" id="question_screenshots_browser">上传截图</span>
									<p class="fl upload_limit">请保证您提交的文件总大小＜5MB（支持jpg png格式）</p>
									<div class="fl select_file preview">
									</div>
								</div> -->

							</div>

							<div class="clearfix">
								<span class="fl"><em></em>填写姓名</span>
								<input type="text" id="name" placeholder="请填写您的姓名" />
								<input type="hidden" id="gender" value="1" />
								<div class="clearfix sex">
									<p class="fl clearfix"><i class="fl selected" data-val="1"></i><span class="fl">先生</span></p>
									<p class="fl clearfix"><i class="fl" data-val="2"></i><span class="fl">女士</span></p>
								</div>
							</div>
							@if($tp==1)
							<div class="clearfix">
								<span class="fl"><em>*</em>身份证号</span>
								<input type="text" id="idcard" placeholder="找回密码请输入身份证号码" />
							</div>
							@endif
							<div class="clearfix">
								<span class="fl"><em></em>联系方式</span>
								<input type="text" id="mobile" placeholder="请填写手机号" />
							</div>
							<div class="clearfix">
								<span class="fl"><em></em>EMALL</span>
								<input type="text" id="email" placeholder="请至少填写一个联系方式，方便我们与您联系" />
							</div>
							<div class="clearfix">
								<span class="fl"><em></em>QQ号码</span>
								<input type="text" id="qq" placeholder="请至少填写一个联系方式，方便我们与您联系" />
							</div>
							<div>
								<p class="submit form-submit">提交</p>
								<p class="notes">问题处理中如需进一步确认，我们可能会通过您提供的联系方式和您取得联系</p>
							</div>
						</div>
						<div class="successfully" style="display:none;">
							<p class="clearfix"><i class="fl"></i><span class="fl">提交成功</span></p>
							<p>事件编号：<span class="num question_code"></span></p><br />
							<p class="tips">感您的反馈~我们会尽快进行核实，并给予回复。</p>
							<p>如您当前账号未登录，可通过事件编号查看事件处理进度；如当前账号登录，您可以通过“查询结果”查看事件处理进度。</p><br />
							<p>请牢记您的事件编号：</p>
							<div><a href="/customer/service"><返回客服中心</a></div>
						</div>
					</li>
				</ul>
			</div>
		</div>
</div>
<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="/js/Somethingtest.swf" style="visibility: visible;"></object>
@endsection

@section('javascript')
<script type="text/javascript">
$(function(){
	$(".sex p i").click(function(){
		var that = $(this);
		that.parent().siblings().find("i").removeClass("selected");
		that.addClass("selected");
		var val = that.attr('data-val');
		$("#gender").val(val);
	})
	$(".form-submit").click(function(){
		var tp = $("#tp").val();
		var sub_tp = $("#sub_tp").val();
		var account = $("#account").val();
		var title = $("#title").val();
		var name = $("#name").val();
		var idcard = $("#idcard").val();
		var mobile = $("#mobile").val();
		var email = $("#email").val();
		var qq = $("#qq").val();
		var gender  = $("#gender").val();
		if(account.length<6) {
			showMessage("请填写您的账号");
			return false;
		}
		if(title.length<5) {
			showMessage("请详细填写您的问题");
			return false;
		}
		if(title.length>200) {
			showMessage("您的问题描述不能超过200字");
			return false;
		}
		if(tp=="1") {
			if(idcard.length<18) {
				showMessage("密码相关问题请填写您的身份证");
				return false;
			}
		}
		if(mobile.length>0) {
			if(!isMobile(mobile)) {
				showMessage("请填写正确的手机号");
				return false;
			}
		}
		if(email.length>0) {
			if(!isEmail(email)) {
				showMessage("请填写正确的邮箱");
				return false;
			}
		}
		if(qq.length>0) {
			if(!isQQ(qq)) {
				showMessage("请填写正确的QQ号");
				return false;
			}
		}
		if(mobile.length<1 && qq.length <1 && email.length<1) {
			showMessage("至少填写一个联系方式");
			return false;
		}
		var formData = {
			tp:tp,
			sub_tp:sub_tp,
			account:account,
			title:title,
			name:name,
			idcard:idcard,
			mobile:mobile,
			email:email,
			qq:qq,
			gender:gender,
		}
		$.post("/customer/service/submitQuestion",formData,function(res) {
			if(res.code==0) {
				$(".account_con").hide();
				$(".question_code").html(res.data.code);
				$(".successfully").show();
				$(window).scrollTop(0);
			} else {
				showMessage(res.msg);
				return false;
			}
		},"json")

	});
})

function isMobile(phone){
  var reValue = /^(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/;
  if(!reValue.test(phone)){
    return false;
  }else{
    return true;
  }
}

function isEmail(email){
  var reEmail = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
  if(!reEmail.test(email)){
    return false;
  }else{
    return true;
  }
}

function isQQ(qq){
  var reQQ = /^[1-9]\d{4,9}$/;
  if(!reQQ.test(qq)){
    return false;
  }else{
    return true;
  }
}
var question_screenshots_obj = new loiUploadContainer({
	container:"question_screenshots_container",
	choose:"question_screenshots_browser",
	ext:"jpg,png",
	upload:{tp:"faqimg",success:function(json){
	  var jsonResult = $.parseJSON(json);
	  var path = jsonResult.data.fileid;
	  var img = img_domain+path;
	  var html = '<p class="fl"><i></i><span><img src="'+img+'"></span></p>';
	   $(".preview").append(html);
	},error:function(){}},
	  filesAdd:function(files){
	   // console.log(files)
	  }
	}
);

function showMessage (msg,timeout,callback) {
    if(typeof(timeout)=="undefined") {
        timeout = 2000;
    }
    var obj = $(".firetips_layer_wrap");
    if(obj.length>0) {
        $(".firetips").html(msg)
        $(".firetips_layer_wrap").fadeIn()
    } else {
        $("body").append('<style>.firetips_layer_wrap {position: fixed;width: 100%;top: 46%;left: 0;z-index: 10000;font-size: 16px;text-align: center;font-size: 16px;}.firetips_layer {display: inline-block;height: 68px;line-height: 68px;background: #FFFBEA;-webkit-box-shadow: 0 5px 5px rgba(0, 0, 0, .15);box-shadow: 0 5px 5px rgba(0, 0, 0, .15);border: 1px solid #D7CAA7;padding: 0 28px 0 67px;position: relative;color: #ba9c6d;}.firetips_layer .hits {position: absolute;width: 28px;height: 28px;left: 23px;top: 20px;background-repeat: no-repeat;background-position: -80px -103px;background-size: 351px 332px;background-image: url("http://pic.vronline.com/open/images/personal.png");}</style><div class="firetips_layer_wrap"><span class="firetips_layer" style="z-index: 10000;"><span class="hits"></span><span class="firetips">'+msg+'</span></span></div>');
    }
    setTimeout(function(){
        $(".firetips_layer_wrap").fadeOut();

        if(typeof(callback)=="function") {
            callback()
        }
    }, timeout);
}
</script>
@endsection
