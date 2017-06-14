@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link href="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<h3 class="page-title">
页游数据
</h3>
<ul class="breadcrumb">
	<li>
		<a href="#">首页</a>
		<span class="divider">/</span>
	</li>
	<li>
		<a href="#">数据查询</a>
		<span class="divider">/</span>
	</li>
	<li class="active">
		页游数据
	</li>
</ul>

<div class="widget orange">
	<div class="widget-title">
		<h4><i class="icon-reorder"></i> 页游数据</h4>
	</div>
	<div class="widget-body">
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group dataDateChoice">
					<label class="control-label" style="float: left;line-height: 30px;">选择时间：</label>
					<div class="controls">
						<div class="input-append date" date-data="" data-date-format="yyyy-mm-dd hh:ii:00">
							<input class="medium" name="ed"  type="text" value="" readonly="readonly">
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
						<button class="btn" type="submit" style="margin-bottom: 10px;height: 30px;"><i class="icon-search"></i> </button>
					</div>
				</div>
			</div>
			<div class="span6">
				<table class="table table-striped table-hover table-bordered">
					<tbody>
						<tr>
							<td>总注册：</td>
							<td>总登陆：</td>
							<td>总充值：</td>
							<td>总消耗：</td>
							<td>游戏总量：</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<table class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th>日期</th>
					<th>注册量</th>
					<th>登录</th>
					<th>充值</th>
					<th>消耗</th>
					<th>次日留存</th>
					<th>三日留存</th>
					<th>七日留存</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
@endsection

@section('javascript')
<script src="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js')}}" ></script>
<script type="text/javascript">
$(".date").datetimepicker();
</script>
@endsection
