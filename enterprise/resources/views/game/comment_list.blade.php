@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<h3 class="page-title">
评论控制
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
	<li>
		<a href="{{ url("game") }}">游戏查询</a>
		<span class="divider">/</span>
	</li>
	<li class="active">
		评论控制
	</li>
</ul>

<h4>
@if ($game)
游戏名称：{{ $game->gname }}
@else
游戏不存在
@endif
</h4>

@if (count($comments)>0)
<table class="table table-striped table-hover table-bordered" id="editable-sample">
	<thead>
		<tr>
			<th>评论账号</th>
			<th>UID</th>
			<th>评论内容</th>
			<th>评论时间</th>
			<th>编辑</th>
		</tr>
	</thead>
	<tbody>
		@foreach($comments as $comment)
		<tr class="">
			<td>{{ $comment->uid }}</td>
			<td>{{ $comment->uid }}</td>
			<td style="max-width: 400px;" >{{ $comment->gcontents }}</td>
			<td>{{ date("Y-m-d H:i:s" , $comment->tmcreate) }}</td>
			<td style="vertical-align: middle">
				<button class="btn btn-primary edit link-to-url" href={{ url("gcomment/{$game->appid}/{$comment->id}") }}><i class="icon-pencil"></i></button>
				<button class="btn btn-danger del" comment-id={{$comment->id}}><i class="icon-trash "></i></button>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

{!! $comments->render() !!}

@else

<div class="alert alert-block alert-warning fade in">
	<h4 class="alert-heading">暂无评论!</h4>
</div>

@endif

@endsection

@section('javascript')

<script type="text/javascript">
$(function() {
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
	});

	$(".del").click(function() {
		var delButton = $(this);
		var comment_id=delButton.attr("comment-id");

		myDialog.confirm({
			content: '确认删除该评论（id：'+comment_id+'）？',
			ok: function() {
				myDialog.loading();
				delComment(comment_id);
				return false;
			}
		});

	});
});

function delComment(comment_id){

	$.ajax({
		url: '{{ url("gcomment") }}',
		type: 'POST',
		dataType: 'json',
		data: {
			id: comment_id,
			_method:"delete"
		},
	})
	.done(function(data) {
		if(data.code!=1){
			myDialog.alert(data.msg);
		}else{
			myDialog.alert("删除评论成功",function(){
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
