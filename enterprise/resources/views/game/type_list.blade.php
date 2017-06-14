@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link href="{{asset('assets/jquery-tags-input/jquery.tagsinput.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<h3 class="page-title">
分类设置
</h3>
<ul class="breadcrumb">
	<li>
		<a href="#">首页</a>
		<span class="divider">/</span>
	</li>
	<li>
		<a href="#">平台游戏</a>
		<span class="divider">/</span>
	</li>
	<li class="active">
		分类设置
	</li>
</ul>
<div class="widget green">
	<div class="widget-title">
		<h4><i class="icon-reorder"></i> 分类管理</h4>
	</div>
	<div class="widget-body">
		<div class="form-horizontal">
			@if (!$gameTpyes)
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading">还没有分类</h4>
			</div>
			@else
			<div class="control-group">
				<label class="control-label">现有分类：</label>
				<div class="controls">
					<ul class="ui-tag">
						@foreach ($gameTpyes as $gameTpye)
						<li>{{$gameTpye->typename}}</li>
						@endforeach
					</ul>
				</div>
			</div>
			@endif
			<div class="form-actions">
				<button class="btn btn-warning add-type"><i class="icon-plus icon-white"></i> 新增分类</button>
				<button class="btn btn-success"><i class="icon-ok icon-white"></i> 分类发布</button>
			</div>
		</div>
	</div>
</div>

<div style="display: none" id="addTypeTemp">
	<div class="addTypeTemp">
		<div class="control-group">
			<label class="control-label">分类名称：</label>
			<div class="controls">
				<input type="text" name="name" class="medium" value="">
			</div>
		</div>
	</div>
</div>
@endsection

@section('javascript')
<script src="{{asset('assets/jquery-tags-input/jquery.tagsinput.min.js')}}"></script>
<script type="text/javascript">
$(function() {
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
	});

	$(".add-type").click(function() {

		var addTypeClone=$("#addTypeTemp").clone();
		addTypeClone.find(".addTypeTemp").attr("id","addTypeCon");
		var addTypeHtml=addTypeClone.html();
		addTypeClone.remove();

		myDialog.dialog({
			title:"新增分类",
			content:addTypeHtml,
			ok: function() {
				var name=$("#addTypeCon").find(".medium").val();
				myDialog.loading();
				addTypeName(name);
				return false;
			}
		});
	});
});

function addTypeName(name){

	$.ajax({
		url: '{{ url("gtype/store") }}',
		type: 'POST',
		dataType: 'json',
		data: {
			name: name
		},
	})
	.done(function(data) {
		if(data.code!=1){
			myDialog.alert(data.msg);
		}else{
			myDialog.alert("新增分类成功",function(){
				window.location.href=window.location.href;
			});
		}
	})
	.fail(function() {
		myDialog.alert("请求失败");
	})
}
</script>
@endsection
