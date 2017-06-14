@inject('blade', 'App\Helper\ActBladeHelper')
@include('layouts.baidu_js')
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>2017VR全景视频大赛-VRONLINE&&3D播播</title>
	<link rel="stylesheet" href="{{ static_res('/videoGame/style/base.css') }} ">
	<link rel="stylesheet" href="{{ static_res('/videoGame/style/3dbobo.css') }}">
	<link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_qjmu58j8vwt81tt9.css">
</head>
<body>
	<div class="container">
		<div class="video">
			<video id="top_video" class="pr head cen" style="width:1920px; height:506px;" src="http://netctvideo.vronline.com/6ce2dc7ac13670077fbc09789a6ca8e9_720.mp4"  loop="loop" x-webkit-airplay="true" webkit-playsinline="true" autoplay="autoplay"></video>
		</div>
		<p class="logo"></p>
		<div class="login">
			<!--登录前-->
			<div class="clearfix" id="3dbbuser" style="display: none">
				<span class="f16 name ells fl"></span>
				<a class="f16 fl" href="/logout?referer=http://www.vronline.com/3dbb">退出</a>
			</div>
			<div id="3dbblogin">
				<a class="f16" href="https://www.vronline.com/login?referer=3dbb" target="_blank">登录</a>
				<a class="f16" href="https://www.vronline.com/register?referer=3dbb" target="_blank">注册</a>
			</div>
		</div>
		<div class="share clearfix bdsharebuttonbox" data-tag="share_1">
			<a class="fl bds_mshare" data-cmd="mshare"></a>
			<a class="fl bds_qzone" data-cmd="qzone" href="#"></a>
			<a class="fl bds_tsina" data-cmd="tsina"></a>
			<a class="fl bds_baidu" data-cmd="baidu"></a>
			<a class="fl bds_renren" data-cmd="renren"></a>
			<a class="fl bds_tqq" data-cmd="tqq"></a>
			<a class="fl bds_more" data-cmd="more"></a>
	<!--		<a class="fl bds_count" data-cmd="count"></a>-->
		</div>
		<div class="nav">
			<a class="f20" target="_blank" href="http://3dbobovr.com/">3D播播</a>
			<span class="f20">强强联合</span>
			<a class="f20" target="_blank" href="http://www.vronline.com/">VRonline</a>
		</div>
		<!--1-->
		<div class="one_item pr cen">
			<div class="con">
				<ul class="clearfix">
					<li class="fl"></li>
					<li class="fl icon1"></li>
					<li class="fl icon2"></li>
					<li class="fl icon3"></li>
					<li class="fl icon4"></li>
					<li class="fl icon5"></li>
				</ul>
				<div class="title">
					<i></i>
					<p class="f16">2017VR全景视频大赛是由国内首家泛VR娱乐聚合移动平台<a href="http://3dbobovr.com/" target="_blank">3D播播</a>联合<a href="http://www.vronline.com/" target="_blank">恺英网络VRonline</a>联合主办，携手得图（唯一指定拍摄硬件）、VR助手、沙发管家、360手机助手（支持平台），华数、720云、爱奇艺VR、腾讯视频VR、Insta360、小米VR、VRlet、王老吉等共同推出的VR视频行业顶级赛事。大赛旨在聚集国内外优秀VR摄制作者及团队，挖掘更多优质原创内容，以平台聚众合力，繁荣VR视频创作圈，为VR爱好者提供展示、交流、分享的平台。让我们在VR视频领域：用“心”玩出“新”视界！</p>
				</div>
			</div>
		</div>
		<!--2-->
		<div class="two_item cen pr">
			<div class="con">
				<i></i>
				<div class="cleafix">
					<div class="fl left">
						<div class="up pr video">
							{!! isset($info[0])?$blade::outputInfo($info[0]):"" !!}
						</div>
						<div class="down clearfix">
							<div class="fl pr video">
							{!! isset($info[1])?$blade::outputInfo($info[1]):"" !!}
							</div>
							<div class="fl pr video">
							{!! isset($info[2])?$blade::outputInfo($info[2]):"" !!}
							</div>
						</div>
					</div>
					<div class="fl middle">
						<div class="up pr video">
							{!! isset($info[3])?$blade::outputInfo($info[3]):"" !!}
						</div>
						<div class="pr down video">
							{!! isset($info[4])?$blade::outputInfo($info[4]):"" !!}
						</div>
					</div>
					<div class="fl right">
						<div class="pr video">
							{!! isset($info[5])?$blade::outputInfo($info[5]):"" !!}
						</div>
						<div class="pr video">
							{!! isset($info[6])?$blade::outputInfo($info[6]):"" !!}
						</div>
						<div class="pr video">
							{!! isset($info[7])?$blade::outputInfo($info[7]):"" !!}
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--3-->
		<div class="three_item cen pr"></div>
		<!--4-->
		<div class="four_item cen pr">
			<div class="con">
				<i></i>
				<div class="prize">
					<ul class="clearfix">
						<li class="fl"><img src="{{ static_res('/videoGame/images/5W.png') }}" /></li>
						<li class="fl"><img src="{{ static_res('/videoGame/images/720cloud.png') }}" /></li>
						<li class="fl"><img src="{{ static_res('/videoGame/images/dpE3.png') }}" /></li>
						<li class="fl"><img src="{{ static_res('/videoGame/images/dpM2pro.png') }}" /></li>
						<li class="fl"><img src="{{ static_res('/videoGame/images/dtF4.png') }}" /></li>
						<li class="fl"><img src="{{ static_res('/videoGame/images/dtTwin.png') }}" /></li>
					</ul>
				</div>
			</div>
		</div>
		<!--投票-->
		<div class="five_item">
			<div class="con clearfix">
				<div class="fl left">
					<ul>
						<li class="clearfix">
							<i class="fl"></i>
							<div class="fl bg">
								<span class="bold">大赛细则</span>
								<span class="f16">时间安排、作品要求、作品评选、奖项设置。</span>
								<a href="javascript:goToPage('rule')""></a>
							</div>
						</li>
						<li class="clearfix">
							<i class="fl enroll"></i>
							<div class="fl">
								<span class="bold">前往报名</span>
								<span class="f16">作品名称、摄制团队、联系方式、联系地址。</span>
								<a href="javascript:goToPage('signup')""></a>
							</div>
						</li>
						<li class="clearfix">
							<i class="fl upload"></i>
							<div class="fl bg">
								<span class="bold">上传视频</span>
								<span class="f16">填写相应视频信息，根据参赛要求上传视频。</span>
								<a href="javascript:goToPage('upload')""></a>
							</div>
						</li>
						<li class="clearfix">
							<i class="fl auditing"></i>
							<div class="fl">
								<span class="bold">等待审核</span>
								<span class="f16">6月1日，用户在作品展示页面进行投票评选。</span>
								<a href="javascript:goToPage('vote')""></a>
							</div>
						</li>
					</ul>
				</div>
				<div class="fr vote" onclick="goToPage('vote')">
					<p class="f40">我要投票</p>
					<p class="date">6.1-7.18开启</p>
				</div>
			</div>
		</div>
		<!--底部-->
		<div class="footer">
			<div class="con">
				<div class="partner">
					<h3>深度合作伙伴</h3>
					<ul class="clearfix">
						<li class="fl"><a href="http://3dbobovr.com/" target="_blank"><img src="{{ static_res('/videoGame/images/pr_1.jpg') }}" /></a></li>
						<li class="fl"><a href="//www.vronline.com/vronline" target="_blank"><img src="{{ static_res('/videoGame/images/pr_2.jpg') }}" /></a></li>
						<li class="fl"><a href="http://www.kingnet.com/" target="_blank"><img src="{{ static_res('/videoGame/images/pr_3.jpg') }}" /></a></li>
						<li class="fl"><a href="http://www.deepoon.com/" target="_blank"><img src="{{ static_res('/videoGame/images/pr_4.jpg') }}" /></a></li>
					</ul>
				</div>
				<div class="aboutvr">
					<a href="//www.vronline.com/vronline" target="_blank">关于VR助手</a>
	                <a href="http://www.kingnet.com" target="_blank">关于恺英</a>
	                <a href="//www.vronline.com/contact" target="_blank">商务合作</a>
	                <a href="//open.vronline.com" target="_blank">我是开发者</a>
	                <a href="//www.vronline.com/parent_intro" target="_blank">家长监护</a>
	                <a href="http://developer.deepoon.com/" target="_blank">大朋开发者</a>
	                <a href="//www.vronline.com/customer/service" target="_blank">客服中心</a>
				</div>
				<p>VRonline平台：适合12岁及以上成年人游戏，建议游戏者适当游戏。</p>
	            <p>抵制不良游戏，拒绝盗版游戏，注意自我保护，谨防受骗上当；适度游戏益脑，沉迷游戏伤身，合理安排时间，享受健康生活！</p>
	            <div class="police">
	            <a class="clearfix" target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=31011202001649">
	                <img class="fl" src="{{ static_res('/website/images/pl.png') }}">
	                <p class="fl">沪公网安备 31011202001649号</p>
	            </a>
	            </div>
	            <p>
		            <span>沪网文[2016] 2600-152号</span>
		            <span>沪ICP备10215773号-37</span>
		            <span>文化部网络游戏举报和联系电子邮箱:</span>
		            <a href="Mailto:wlyxjb@gmail.com">wlyxjb@gmail.com</a>
	            </p>
	            <p>
		            <span>Copyright©2008-2015 vronline.com All Rights Reserved</span>
		            <span>上海恺英网络科技有限公司 版权所有</span>
	            </p>
	            <p>
	                <span>上海陈行路2388号浦江科技广场3号3F&nbsp;&nbsp;&nbsp;</span>
	                <span>客服电话：021-54310366-8065</span>
	            </p>
			</div>
		</div>
		<div class="side">
			<ul>
				<li class="cur"><a class="f16" href="javascript:goToPage('signup')">我要报名</a></li>
				<li><a class="f16" href="javascript:goToPage('upload')">提交作品</a></li>
				<li><a class="f16" href="javascript:goToPage('vote')">我要投票</a></li>
				<li><a href="/" target="_blank"><p class="f16">VR</p><p class="f14">最新资讯</p></a></li>
				<li><a href="/news/list/6" target="_blank"><p class="f16">VR</p><p class="f14">游戏推荐</p></a></li>
			</ul>
			<a class="top f14" href="#">TOP</a>
		</div>
	</div>
	<div class="maskLayer" style="display: none">
		<div class="popup">
			<i class="close"></i>
			<div class="popupCon">
				<div class="clearfix work">
					<div class="fl" onclick="goToPage('upload')">
						<p class="f24 submit">提交作品</p>
						<p>大赛正式比赛时间为2017.6.1—2017.7.18，审核通过后的参赛作品将在正式比赛开始后展示在3D播播的大赛页面，越早提交作品、位置越靠前。</p>
					</div>
					<div class="fr again close" >
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
<script type="text/javascript" src="{{ static_res('/assets/loi/message.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/assets/vrplayer/vr.js') }}"></script>
<script type="text/javascript" src="{{ static_res('/videoGame/js/scrollForever.js') }}"></script>

<script>
	var signup=0;
	var video = document.getElementById('top_video');
	var is_play_vr = false;
	$(function(){
		$.get("/load3dbbStat",function(res) {
			if(res.code==0) {
				signup = res.data.signup;
				var str = res.data.nick+" "
				if(res.data.signup==0) {
					str = str+"您尚未报名"
				} else {
					if(res.data.upload==0) {
						str = str+"您已报名,未上传视频"
					} else {
						str = str+"您已成功参赛"
					}
				}
				$("#3dbbuser span").text(str)
				$("#3dbblogin").hide();
				$("#3dbbuser").show();
			}
		},"json")
		$(".maskLayer .close").click(function(){
			$(".maskLayer").hide();
		});
		$('.side ul').on('click','li',function(){
			var i = $(this).index();
			$(this).addClass('cur').siblings().removeClass('cur');
		});
		//视频
		$('.two_item .video').on('click',function(){
			video.pause();
			creatVideoHtml($(this).find('img').attr('data-val'));
		});
		//奖品轮播
		$(".prize").scrollForever();
	})
	function goToPage(page) {
		switch (page) {
			case "signup":
				if(signup==1) {
					$(".maskLayer").show();
				} else {
					//location.href = "/signup";
                    window.open("/signup");
				}
			break;
			case "upload":
				if(signup==0) {
					loiNotifly("您还没有报名,去报名吧",2000,function(){
						//location.href = "/signup";
                        window.open("/signup");
					})
				} else {
					//location.href = "/3dupload";
                    window.open("/3dupload");
				}
			break;
			case "rule":
				//location.href= "/3dbbrule";
                window.open("/3dbbrule");
			break;
			default:
				window.open("http://api.vrbig.com/active/allmovielist");
				//loiNotifly("6.1-7.18开启 尽请期待！")
			break;
		}
	}
	function creatVideoHtml(src){
		var arr = src.split('/')
		var fielname = arr[arr.length-1]
		var extLen = fielname.indexOf(".");
		var fileStr = fielname.substring(0,extLen)
		if(is_play_vr) {
			KrPlayer.remove('mplayer');
			is_play_vr = false;
		}
		$('body').find('#vr_player').detach();
		is_play_vr = true;
		var html ='<div class="video_con" id="vr_player" style="width: 960px; height: 480px;"><i onclick="closeVR()" class="iconfont icon-iconfontcha" style="cursor:pointer;color:#fff;font-size:30px;position: relative;top:35px;right:5px;float:right;z-index:999999"></div>';
		$('body').append(html);
		KrPlayer.setup('mplayer', 'vr_player', 'http://www.vronline.com/0/'+fileStr+'/vrplayer.xml');
	}
	function closeVR() {
		if(is_play_vr) {
			KrPlayer.remove('mplayer');
			is_play_vr = false;
		}
		$('#vr_player').detach();
		video.play();
	}
</script>
</html>
@yield("baidu_stat")
