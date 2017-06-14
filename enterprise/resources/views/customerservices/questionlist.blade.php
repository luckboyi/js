@inject('blade', 'App\Helper\BladeHelper')
@extends('news.layout')

@section('meta')
<title>VRonline客服中心</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{static_res("/news/style/customer.css")}}" />
@endsection

@section('content')
<div class="container">
		<div class="clearfix all_con">
			<div class="fl left">
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
					<!--查询结果-->
					<li class="list_con inquiry_con" style="display:block">
						<!--问题列表-->
						<div class="result">
							<div class="status">
								<p class="fl cur clearfix"><i class="fl handling"></i><span class="fl">问题列表</span></p>
								<!-- <p class="fl clearfix"><i class="fl handled"></i><span class="fl">已处理</span></p> -->
								<div class="fr">
									<input type="text" id="search" class="action-search" placeholder="请输入事件编号或账号" />
									<i onclick="searchQues()"></i>
								</div>
							</div>
							<table class="cur" cellpadding="0" cellspacing="0">
								 <tr>
								    <th>事件编号</th>
								    <th>问题类型</th>
								    <th>问题分类</th>
								    <th>反馈时间</th>
								    <th>事件状态</th>
								    <th>操作</th>
								</tr>
								@if(isset($data))
									<tr>
								    <td>{{ $data['code'] }}</td>
								    <td>{{ $blade->showHtmlClass('service_question_tp',$data['tp'],'text') }}</td>
								    <td>{{ $blade->showSubClass('service_question_sub_tp',$data['tp'],$data['sub_tp']) }}</td>
								    <td>{{ $data['ctime'] }}</td>
								    <td><span class="red">{{ $blade->showHtmlClass('service_question_stat_view',$data['stat'],'text') }}</span></td>
								    <td><a href="/customer/service/questioninfo/{{ $data['code'] }}">查看</a></td>
									</tr>
								@else
								<tr>
								    <td colspan="6" class="center aligned">暂无数据</td>
								</tr>
								@endif
							</table>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
<script type="text/javascript">

function searchQues() {
	var search = $("#search").val();
	location.href = "/customer/service/myquestion?search="+search;
}

$(function() {
	$(".action-search").keypress(function() {
		if(event.keyCode==13) {
			searchQues();
		}
	});
})


</script>
@endsection