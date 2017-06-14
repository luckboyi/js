@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link href="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">

@endsection

@section('content')
<h3 class="page-title">
反馈详情
</h3>
<ul class="breadcrumb">
	<li>
		<a href="#">首页</a>
		<span class="divider">/</span>
	</li>
	<li>
		<a href="#">反馈详情</a>
		<span class="divider">/</span>
	</li>
	<!-- <li class="active">
		意见反馈
	</li>
 --></ul>

<div class="widget orange">
	<div class="widget-title">
		<h4><i class="icon-reorder"></i> 反馈详情</h4>
	</div>
	<div class="widget-body">
		<div class="row-fluid">
			<div class="span6">
			</div>

		</div>
		<label class="control-label">用户账号：{{ $result['account']}}</label></br>
		<label class="control-label">联系方式:{{ $result['mobile']}}</label></br>
		<label class="control-label">qq号：{{ $result['qq']}}</label></br>
		<label class="control-label">问题描述：</label><textarea style="width:600px;" id="content">{{ $result['ask']}}</textarea></br>
		<label class="control-label">上传图片<br>
		<div>

			@if(isset($result['pic_json']) && !empty($result['pic_json']))
			@foreach($result['pic_json'] as $k=>$v)
				<a style="margin-right:10px;" target="_blank" href="{{ $v }}"><img style="width:100px;height:100px;" src="{{ $v}}"></img></a>
				
			@endforeach
			@else
				<p>无</p>
			@endif

		</div>
			
		


	</div>
</div>
@endsection

