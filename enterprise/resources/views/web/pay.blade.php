@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="_token" content="{{ csrf_token() }}"/>
		<title>支付中心</title>
		<link rel="stylesheet" href="{{static_res('/common/style/base.css')}}">
		<link rel="stylesheet" href="{{static_res('guanwang/style/personal_center.css')}}">
		<script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}" type="text/javascript"></script>
		<script src="{{static_res('/common/js/ClientConfig.js')}}" type="text/javascript"></script>
		<script src="{{static_res('/common/js/tips.js')}}" type="text/javascript"></script>
		<script language="JavaScript" src="{{asset('js/tool/tinyscrollbar.js')}}"></script>
		<script src="{{static_res('/pay/payFun/payFun.js')}}" type="text/javascript"></script>
		<script language="JavaScript" src="{{static_res('/js/personalCenter.js')}}"></script>
	</head>
	<body>
		<div class="personal_center clearfix">
			<div class="left_per fl">
				<ul>
					<li class="pr userMsg"><a href="{{url("web")}}">用户资料</a></li>
					<li class="pr charge cur">
						<i></i>
						<a href="javascript:;">充值中心</a>
						<ol class="">
							<li class="{{$channel=="alipay"?"cur":""}} switch-pay" channel="alipay">支付宝</li>
							<li class="{{$channel=="wxpay"?"cur":""}} switch-pay" channel="wxpay">微信支付</li>
						</ol>
					</li>
					<li class="pr problem"><a href="{{url("qa")}}">常见问题</a></li>
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
						<!--平台支付S-->
						<li class="list_con paymentCenter cur" style="display: block;">
							<h4 class="payment_title">充值账号</h4>
							<div class="clearfix">
								<span class="fl">平台账号：</span>
								<span class="fl name" user-id="{{$uid}}" token="{{$token}}">{{$nick}}</span>
								<span class="fl blueCor balance_title">{{config("common.platform_coin_name")}}余额：</span>
								<span class="fl blueCor balance_num">{{$money}}</span>
							</div>
							<div class="pay_sel_btn select-item">
								<span class="cur vr_charge" item="platformCoin">充值{{config("common.platform_coin_name")}}</span>
								<span class="pageGame_charge" item="gameCoin">网页游戏</span>
							</div>

							<h4 class="payment_title">选择充值金额</h4>
							<div class="pageGame_sel clearfix">
								<span class="fl">游戏名称：</span>
								<select class="fl blueCor" name="list" id="gameSelect">
									@foreach ($allgame as $game)
									<option class="blueCor" value="{{$game["appid"]}}">{{$game["name"]}}</option>
									@endforeach
								</select>
								<span class="fl server">服务器：</span>
								<span class="fl">
									<select name="list" id="serverSelect">
									</select>
								</span>
								<span class="fl total_scale">总付比例：</span>
								<span class="fl">￥</span>
								<span class="fl">1:</span>
								<span class="fl scale_num"></span>
								<span class="fl unit"></span>
							</div>
							<div class="clearfix payment_num">
								<span class="fl cur" num="100">￥100</span>
								<span class="fl" num="500">￥500</span>
								<span class="fl" num="2000">￥2000</span>
								<span class="fl" num="3000">￥3000</span>
								<span class="fl" num="5000">￥5000</span>
							</div>
							<div class="clearfix other_pay_num pr">
								<label class="clearfix fl">
									<span class="fl">其他金额</span>
									<input type="text" id="pay-num" placeholder="填写1~50000之间的整数" class="fl" value="100">
								</label>
								<i class="fl"></i>
								<p class="pa pay_num_tips">金额填写1~50000之间的整数，不含小数点</p>
								<span class="fl pageGame_sel">获得游戏币：</span>
								<span class="fl blueCor pageGame_sel" id="getGameCon"></span>
								<span class="fl unit pageGame_sel"></span>
								<p class="pa erroColor erro_tips">金额填写1~50000之间的整数，不含小数点</p>
							</div>
							<div class="payment_select clearfix pageGame_sel pageGame_pay">
								<div class="fl select_right clearfix">
									<div class="vr_pay_con pageGame_choice_mode">
										<p class="pr">
											<span>{{config("common.platform_coin_name")}}余额：</span>
											<span class="blueCor balance_num">{{$money}}</span>
											<!-- <span class="blueCor">点</span> -->
											<span class="choice_box" id="use_vr"></span>
											<span class="balance user_vr_pay">使用{{config("common.platform_coin_name")}}支付本次交易</span>
										</p>
										<p class="choice_vr_pay">
											<span>支付总额：</span>
											<span class="whiteCor">￥</span>
											<span class="whiteCor total_num"></span>
											<span>/</span>
											<span class="blueCor user_num"></span>
											<span class="blueCor">{{config("common.platform_coin_name")}}被使用</span>
										</p>
										<p>
											<span>你需支付</span>
											<span class="erroColor">￥</span>
											<span class="erroColor need_num" id="need-num"></span>
										</p>
									</div>
								</div>
							</div>
							<div class="pay_zhi_btn pr" id="button-container">
							</div>
							<div class="clearfix payment_tips ">
								<i class="fl"></i>
								<div class="fl">
									<p>您的{{config("common.platform_coin_name")}}余额不足，无法支付。您可以通过以下方法进行充值：</p>
									<p>方法1：对平台账号充值VR点数。<a href="javascript:;" class="blueCor">如何获得{{config("common.platform_coin_name")}}>></a></p>
									<p>方法2：使用支付宝进行支付。</p>
								</div>
							</div>
							<h4 class="payment_title wamp_title">温馨提示 <a href="javascript:;" class="fr blueCor contact">联系客服</a></h4>
							<div class="tips_con">
								<p>1、您充入的{{config("common.platform_coin_name")}}不可返还，并且无法提现，某些游戏和（或）服务可能会收费额外费用。</p>
								<p>2、支付宝账户额支付：只要您的支付宝账户中存有余额，就可以为游戏进行充值。</p>
								<p>3、使用支付宝支付，对消费者来说，目前不需要任何的手续费。</p>
								<p>想要了解更多信息，请阅读 <a href="javascript:;" class="blueCor">《VRonline使用条款》</a>和 <a href="javascript:;" class="blueCor">《销售条款》</a></p>
							</div>

						</li>
						<!--平台支付E-->
					</ul>
				</div>
			</div>
		</div>
		<!--弹窗-->
		<div id="hideParam" style="display: none">
			<input type="hidden" name = "pay_rate" value="{{$payRate}}">
			<input type="hidden" name="money" value="{{$money}}">
			<input type="hidden" name = "price" value="">
			<input type="hidden" name = "num" value="1">
			<input type="hidden" name = "uid" value="{{$uid}}">
			<input type="hidden" name = "try_uid" value="">
			<input type="hidden" name = "vrkey" value="{{$token}}">
			<input type="hidden" name = "callback_url" value="https://pay.vronline.com/callback">
			<input type="hidden" name = "jump_url" value="http://payres.vronline.com/result">
			<input type="hidden" name = "surl" value=":">
			<input type="hidden" name = "from" value="plat">
			<input type="hidden" name = "paytoken" value="{{$paytoken}}">
			<input type="hidden" name = "game_type" value="">
			<input type="hidden" name = "sid" value="">
			<input type="hidden" name = "wp_pid" value="">
			<input type="hidden" name = "wp_uid" value="">
			<input type="hidden" name = "pay_total" value="">
			<input type="hidden" name = "pay_rmb" value="">
			<input type="hidden" name = "login_openid" value="">
			<input type="hidden" name = "login_type" value="">
			<input type="hidden" name = "attach" value="">
			<input type="hidden" name = "game_owner" value="">
			<input type="hidden" name = "is_contract" value="0">
			<input type="hidden" name = "sign" value="">
			<input type="hidden" name = "user_ip" value="1000">
			<input type="hidden" name = "channel" value="">
			<input type="hidden" name = "pay_vr" value="1">
			<input type="hidden" name = "isdev" value="{{$isdev?$isdev:0}}">
			<input type="hidden" name = "env" value="{{$env}}">
		</div>
		<div class='mask_layer ali_window' style='display: none;' id="payFrameCon">
			<div class='aliPay_window pr' style='border:0;background:#fff; position: absolute; top:50%;left: 50%;z-index: 999; transform: translate(-50%,-50%); width:450px; height:530px;'><i class='close pa'></i>
				<iframe scrolling='no' id="payFrame" width='100%' height='100%'  frameborder='no' border='0' src=''></iframe>
			</div>
		</div>
	</body>
</html>
<script src="{{static_res('/common/js/datacenter_stat.js')}}" type="text/javascript"></script>
<script type="text/javascript">
resizeFn();

$(function(){
	PayFun.init({
		funType: "web",
		cannel:"alipay"
	});

	$(".switch-pay").click(function(){
		$(this).addClass("cur").siblings().removeClass('cur');
		var channel=$(this).attr("channel");
		PayFun.loadChannelJs(channel);
	});
});

</script>
