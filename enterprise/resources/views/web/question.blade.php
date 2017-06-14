@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="_token" content="{{ csrf_token() }}"/>
		<title>常见问题</title>
		<link rel="stylesheet" href="{{static_res('/common/style/base.css')}}">
		<link rel="stylesheet" href="{{static_res('guanwang/style/personal_center.css')}}">
		<script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}" type="text/javascript"></script>
		<script language="JavaScript" src="{{asset('js/tool/tinyscrollbar.js')}}"></script>
		<script language="JavaScript" src="{{asset('js/personalCenter.js')}}"></script>
	</head>
	<body>
		<div class="personal_center clearfix">
			<div class="left_per fl">
				<ul>
					<li class="pr userMsg"><a href="{{url("web")}}">用户资料</a></li>
					<li class="pr charge">
						<i></i>
						<a href="javascript:;">充值中心</a>
						<ol class="">
							<a href="{{url("pay")}}"><li channel="alipay">支付宝</li></a>
							<a href="{{url("pay?channel=wxpay")}}"><li channel="wxpay">微信支付</li></a>
						</ol>
					</li>
					<li class="pr problem cur"><a href="javascript:;">常见问题</a></li>
				</ul>
			</div>
			<div class="right_per personal_center_height" id="personal_center_scroll">
				<div class="scrollbar fr pr  personal_center_height">
					<div class="track">
						<div class="thumb pa"></div>
					</div>
				</div>
				<div class="viewport pr  personal_center_height">
					<ul class="in_right_per overview">
						<li class="list_con problem_center cur">
							<h2 class="f20 tac fw">VRonline常见问题FAQ</h2>
							<div class="pro_content ">
								<h4 class="f14 fw">问：如何要注册一个VRonline平台账号</h4>
								<div>
									<p>答：1.点击VRonline客户端顶部用户头像，或访问VRonline官网（wwww.vronline.com），点击注册/登录。</p>
									<p class="tin">2.VR平台目前支持普通账户注册和登录、第三方账户（QQ/微信/新浪微博）登录。</p>
								</div>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：账号密码忘了，如何找回？</h4>
								<div>
									<p>答：目前只开放手机找密码，注册账号成功后登录个人中心，建议进行手机绑定，提高账号安全性，操作方法具体如下：</p>
									<p class="tin">步骤1：成功绑定手机。</p>
									<p class="tin">步骤2：获取短信验证码。</p>
									<p class="tin">步骤3：输入新密码+确认新密码。</p>
									<p class="tin">步骤4：密码找回成功，使用新密码登录。</p>
								</div>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：我是游客账号，如何修改为自己的VRonline平台账号？</h4>
								<p>答：以游客身从登录VR平台，可以进入“个人中心→账号安全”，填写新的平台账号+新密码进行修改，每个游戏账号只提供1次修改机会。</p>
							</div>
							<div class="pro_content">
								<h4>问：如何绑定手机/解绑手机/修改绑定手机操作？</h4>
								<p>答：进入“个人中心”通过绑定手机+验证码，完成绑定/解绑/修改等操作。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：VR游戏要付费吗？</h4>
								<p>答：VRonline客户端内目前提供的游戏都是免费的，不需要支付任何费用。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：我要如何玩平台内提供的VR游戏？</h4>
								<p>答：步骤1：登录VR平台，选择“VR游戏”，选择任意一款VR游戏进入游戏详情页。</p>
								<p class="tin">步聚2：点击“安装”，安装成功。</p>
								<p class="tin">步骤3：安装设备驱动和连接线正确与电脑连接。</p>
								<p class="tin">步聚4：点击“开始”按钮，启动游戏。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：如何安装本地VR游戏至平台客户端内？</h4>
								<p>答：点击VR游戏左侧底部“添加游戏”，可选择本地桌面的VR游戏安程包进行安装。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：平台账户如何充值？</h4>
								<p>答：右击头像，选择下拉菜单“充值”，进入充值中心界面。目前只推出支付宝充值，充值时可选择“平台虚拟币”或“网页游戏”、充值金额、选择充值方式。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：充值平台虚拟币，如何使用？</h4>
								<p>答：VRonline平台充获得可以获得对应数据的虚拟币。作为VR平台统一货币，目前可用于支付或抵扣网页游戏的充值金额，后续将陆续开放更多可以支付的商品。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：目前平台支持哪些方式的充值？</h4>
								<p>答：支付宝手机扫码、登录支付宝账号</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：我充值页游，但平台虚拟币还有余额，可以合并支付吗？</h4>
								<p>答：可以。如想要对页游充值时，用户可以选择使用平台虚拟币余额支付抵扣一部分的充值金额或全额支付（余额足够充值页游的金额），或不需要使用平台余额而采用全额现金充值。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：页游礼包如何领取？</h4>
								<p>答：进入“网页游戏”界面，选择任意一款游戏点击“领取礼包”，进入领取页面，选择礼包种类，查看礼包内容后点击领取。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：礼包领取了CDKEY如何使用？能查看领取过的CDKEY吗？</h4>
								<p>答：CDKEY可以进行复制，然后至启动页游进入游戏内输入CDKEY领取。如果忘了礼包的CDKEY，可以进入“礼包中心→我的礼包”内查询领取记录。</p>
							</div>
							<div class="pro_content">
								<h4 class="f14 fw">问：我想提些意见，可以去哪里反馈？</h4>
								<p>答：VRonline客户端内可以至右上角下拉菜单“意见”中反馈。官网可以至右侧侧边栏中通过在线客户或意见进行反馈。</p>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</body>
</html>
<script src="{{static_res('/common/js/datacenter_stat.js')}}" type="text/javascript"></script>
<script type="text/javascript">
resizeFn();
</script>
