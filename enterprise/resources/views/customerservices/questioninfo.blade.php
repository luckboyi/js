@inject('blade', 'App\Helper\BladeHelper')
@extends('news.layout')

@section('meta')
<title>VRonline客服中心</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{static_res("/news/style/customer.css")}}" />
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
					<li>
						<a class="clearfix" href="/customer/service/question/{{ $questionTp['id'] }}">
							<i class="fl {{ $questionTp['icon'] }}"></i>
							<span>{{ $questionTp['name'] }}</span>
							<i class="fr"></i>
						</a>
					</li>
				@endforeach
					<li class="cur">
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
					<li class="list_con inquiry_con cur">
						<div class="see">
							<div class="see_title">
								<h3 class="title">我的问题</h3>
							</div>
							<div class="see_con">
								<div class="clearfix see_con_title">
									<p class="fl"><span>事件编号:</span><span>{{ $data['code'] }}</span></p>
									<p class="fl"><span>问题类型:</span><span>{{ $blade->showHtmlClass('service_question_tp',$data['tp'],'text') }}</span></p>
									<p class="fl"><span>问题分类:</span><span>{{ $blade->showSubClass('service_question_sub_tp',$data['tp'],$data['sub_tp']) }}</span></p>
									<p class="fl"><span>反馈时间:</span><span>{{ $data['ctime'] }}</span></p>
								</div>
								<p class="problem_description"><span>问题描述：</span><span class="wordWrap"> {{ $data['title'] }}</span></p>
								<div class="reply">
									@if($content)
									@foreach($content as $reply)
									@if($reply['tp']==1)
										<div class="clearfix">
											<p class="fl reply_name"><span>客服回复:</span><span>{{ $reply['name'] }}</span></p>
											<div class="fl reply_con">
											    <span class="wordWrap">{{  $reply['cn'] }}</span>
												<span>{{ date("Y-m-d H:i:s",$reply['time']) }}</span>
											</div>
										</div>
									@else
										<div class="my_reply clearfix">
											<p class="fl reply_name"><span>我的回复:</span></p>
											<div class="fl reply_con">
												  <span class="wordWrap">{{ $reply['cn'] }}</span>
												<span>{{ date("Y-m-d H:i:s",$reply['time']) }}</span>
											</div>
										</div>
									@endif
									@endforeach
									@endif
								</div>
								@if($data['score']==0)
								<div class="write" >
									<div class="clearfix">
										<div class="fl clearfix score complete-score" style="display:none">
											<i class="fl closed close-complete"></i>
											<span class="fl">感谢您的咨询，请您对本次服务进行评分：</span>
											<span class="fl clearfix complete-ques" data-val="1"><i class="fl satisfied"></i>满意</span>
											<span class="fl clearfix complete-ques"  data-val="2"><i class="fl unsatisfied"></i>不满意</span>
										</div>
										<span class="fr evaluate" >结束并评价</span>
									</div>
									<div class="communication clearfix">
										<span class="fl">我的回复：</span>
										<div class="fl text_wrap">
											<textarea id="complete-cn" rows="" cols="" class="text" placeholder="字数长度在1-200个字之间"></textarea>
										</div>
									</div>
									<p class="submit complete-submit">提 交</p>
								</div>
								@else
								<div class="end clearfix">
									<span class="fl"></span>
									<p class="fl">事件已结束，如有新问题请重新提交</p>
									<span class="fl"></span>
								</div>
								@endif
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
var code = "{{ $code }}";
$(function(){
	$(".evaluate").click(function(){
		$(".complete-score").fadeIn();
	});
	$(".close-complete").click(function(){
		$(".complete-score").fadeOut();
	});
	$(".complete-ques").click(function(){
		var score = parseInt($(this).attr('data-val'));
		$.post('/customer/service/completeQuestion',{code:code,score:score},function(res){
			if(res.code==0) {
				location.reload();
			}
		},"json");
	});
	$(".complete-submit").click(function(){
		var cn = $("#complete-cn").val();
		if(cn.length < 1) {
			showMessage("评论不能为空。");
			return false;
		} else if(cn.length > 200) {
			showMessage("评论文字不能超过200字。");
			return false;
		} else {
			$.post('/customer/service/replyQuestion',{code:code,cn:cn},function(res){
			if(res.code==0) {
				location.reload();
			}
		},"json");
		}
	});
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
})
</script>
@endsection
