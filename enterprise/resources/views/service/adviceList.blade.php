@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link href="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
<style>
.ask{white-space: nowrap;
	text-overflow: ellipsis;
	-o-text-overflow: ellipsis;
	overflow: hidden;
	width:200px;
	margin:0;
}
td{
	text-indent:8px;
	height:40px!important;
	line-height:40px!important;
	padding:0!important;
}
</style>
@endsection
@section('content')
<h3 class="page-title">
意见反馈
</h3>
<ul class="breadcrumb">
	<li>
		<a href="#">首页</a>
		<span class="divider">/</span>
	</li>
	<li>
		<a href="#">意见反馈</a>
		<span class="divider">/</span>
	</li>
	<!-- <li class="active">
		意见反馈
	</li>
 --></ul>

<div class="widget orange">
	<div class="widget-title">
		<h4><i class="icon-reorder"></i> 意见反馈</h4>
	</div>
	<div class="widget-body">
		<div class="row-fluid">
			<div class="span6">
				<!-- <div class="control-group dataDateChoice">
					<label class="control-label" style="float: left;line-height: 30px;">选择时间：</label>
					<div class="controls">
						<div class="input-append date" date-data="" data-date-format="yyyy-mm-dd hh:ii:00">
							<input class="medium" name="ed"  type="text" value="" readonly="readonly">
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
						<button class="btn" type="submit" style="margin-bottom: 10px;height: 30px;"><i class="icon-search"></i> </button>
					</div>
				</div> -->
			</div>
			<div class="span6">
				
			</div>
		</div>
		<table class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th>提交日期</th>
					<th>用户账号</th>
					<th>问题描述</th>
					<th>联系方式</th>
					<th>QQ号</th>
					<th>操作</th>
				</tr>
				@if(isset($result) && !empty($result))
				@foreach($result as $k=>$v)

					<tr>
						<td>{{ $v['ctime'] }}</td>
						<td>{{ $v['account'] }}</td>
						<td><p class="ask">{{ $v['ask'] }}</p></td>
						<td>{{ $v['mobile'] }}</td>
						<td>{{ $v['qq'] }}</td>
						<td><a target="_blank" href="/adviceInfo?id={{ $v['id']}}&uid={{ $v['uid'] }}">查看</a></td>
					</tr>
				@endforeach
				@else
					<p>无反馈</p>
				@endif

			</thead>
		</table>
	</div>
</div>
@endsection

