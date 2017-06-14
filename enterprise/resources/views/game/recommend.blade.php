@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link href="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<h3 class="page-title">
游戏推荐
</h3>
<ul class="breadcrumb">
	<li>
		<a href="#">首页</a>
		<span class="divider">/</span>
	</li>
	<li class="active">
		游戏推荐
	</li>
</ul>

<p style="margin: 20px 0;">
	<button class="btn btn btn-info"><i class="icon-ok icon-white"></i> 发布</button>
	<button class="btn btn link-to open-url" href="{{url("game")}}"><i class="icon-eye-open"></i> 游戏查询</button>
</p>

@for ($i = 1; $i <= 6; $i++)
@if ($i%3==1)
<div class="row-fluid">
	@endif
	<div class="span4 ">
		<!-- BEGIN widget widget-->
		<div class="widget yellow">
			<div class="widget-title">
				<h4><i class="icon-location-arrow"></i> 位置{{ $i }} (game_0_{{$i}})</h4>
			</div>
			<div class="widget-body">
				<div style="overflow: hidden; outline: none; height:200px; text-align:center; display: table-cell; vertical-align: middle; width:1%;  table-layout: fixed;">
					<div style="width:100%;text-align:center">
						@if (isset($recommends["game_0_".$i]))
						<img src="{{ isset($recommends["game_0_".$i]->game)?$recommends["game_0_".$i]->game->imginfo["icon"]:"" }}" alt="" style="max-height:200px; max-width:100%">
						@else
						<div>该位置暂无推荐</div>
						@endif
					</div>
				</div>
			</div>
			<div class="clearfix" style="padding: 15px" id="game_0_{{$i}}">
				<div style="height:40px; float: left; width:186px; margin-right: 20px;">
					@if (isset($recommends["game_0_".$i]))
					<div>开始时间：{{$recommends["game_0_".$i]->opening_time}}</div>
					<div>结束时间：{{$recommends["game_0_".$i]->end_time}}</div>
					<input type="hidden" name="game_0_{{$i}}" value="1">
					<input type="hidden" name="game_0_{{$i}}_appid" value="{{$recommends["game_0_".$i]->content_id}}">
					<input type="hidden" name="game_0_{{$i}}_op" value="{{$recommends["game_0_".$i]->opening_time}}">
					<input type="hidden" name="game_0_{{$i}}_ed" value="{{$recommends["game_0_".$i]->end_time}}">
					@else
					<div>开始时间：-</div>
					<div>结束时间：-</div>
					<input type="hidden" name="game_0_{{$i}}" value="0">
					@endif
				</div>
				<div style="height:40px;display: table-cell; vertical-align: middle; word-break: keep-all;white-space:nowrap; width:1%; text-align:right;">
					<button class="btn btn btn-primary edit-recommend" position-id="game_0_{{$i}}"><i class="icon-pencil icon-white"></i> 修改</button>
				</div>
			</div>
		</div>
		<!-- END widget widget-->
	</div>
	@if ($i%3==0)
</div>
@endif
@endfor

<div style="display: none" id="editRecommendTemp">
	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label">对应位置：</label>
			<div class="controls">
				<input type="text" name="position_id" class="medium" value="" disabled="disabled">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">游戏id：</label>
			<div class="controls">
				<input type="text" name="appid" class="medium" value="">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">开始时间：</label>
			<div class="controls">
				<div class="input-append date" date-data="" data-date-format="yyyy-mm-dd hh:ii:00">
					<input class="medium" name="op"  type="text" value="" readonly="readonly">
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">结束时间：</label>
			<div class="controls">
				<div class="input-append date" date-data="" data-date-format="yyyy-mm-dd hh:ii:00">
					<input class="medium" name="ed"  type="text" value="" readonly="readonly">
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('javascript')
<script src="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js')}}" ></script>
<script type="text/javascript">
$(function() {
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$(".edit-recommend").click(function() {
		var position_id = $(this).attr("position-id");

		var isSetGame=$("input[name='"+position_id+"_appid']").val();

		var editRecommendClone=$("#editRecommendTemp").clone();

		var appid,op,ed;
		if(isSetGame){
			var position=$("#"+position_id);
			appid=position.find("input[name='"+position_id+"_appid']").val();
			op=position.find("input[name='"+position_id+"_op']").val();
			ed=position.find("input[name='"+position_id+"_ed']").val();
		}

		editRecommendClone.find(".form-horizontal").attr("id","editRecommendCon");
		var editRecommendHtml=editRecommendClone.html();
		editRecommendClone.remove();

		myDialog.dialog({
			id:"editRecommend",
			title:"修改游戏推荐",
			content:editRecommendHtml,
			ok: function() {
				var editRecommendCon=$("#editRecommendCon");
				myDialog.loading();
				var data={
					position_id:editRecommendCon.find("input[name='position_id']").val(),
					appid:editRecommendCon.find("input[name='appid']").val(),
					op:editRecommendCon.find("input[name='op']").val(),
					ed:editRecommendCon.find("input[name='ed']").val()
				};
				editRecommend(data);
				return false;
			},
			onshow: function () {
				var editRecommendCon=$("#editRecommendCon");
				editRecommendCon.find("input[name='position_id']").val(position_id);
				editRecommendCon.find("input[name='appid']").val(appid);
				editRecommendCon.find("input[name='op']").val(op);
				editRecommendCon.find("input[name='ed']").val(ed);
				editRecommendCon.find(".date").datetimepicker();
			}
		});
	});
});

function editRecommend(data){
	$.ajax({
		url: '{{ url("grecommend") }}',
		type: 'POST',
		dataType: 'json',
		data: data
	})
	.done(function(data) {
		if(data.code!=1){
			myDialog.alert(data.msg);
		}else{
			myDialog.alert("保存游戏推荐成功",function(){
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
