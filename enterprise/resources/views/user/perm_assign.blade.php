@extends('layouts.admin')

@section('content')
<h3 class="page-title">
权限分配
</h3>
<ul class="breadcrumb">
	<li>
		<a href="#">首页</a>
		<span class="divider">/</span>
	</li>
	<li class="active">
		权限分配
	</li>
</ul>

<label style="display:none" id="userid" tar="{{ $userId }}"></label>
@inject('blade', 'App\Helper\BladeHelper')
<!-- actionList 所有的功能列表 -->
@foreach($actionList[0] as $action)
<!-- 第一层 -->
<div class="row-fluid">
	<div class="span12">
		<!-- BEGIN ALERTS PORTLET-->
		<div class="widget orange">
			<div class="widget-title">
				<h4><i class="icon-reorder"></i> {{ $action["name"] }}</h4>
				<span class="tools">
					<a href="javascript:;" class="icon-chevron-down"></a>
				</span>
			</div>
			<div class="widget-body">
				{{ $blade->createAction($action,$actionList) }}
			</div>
		</div>
		<!-- END ALERTS PORTLET-->
	</div>
</div>
@endforeach



<!--     <h2>首页</h2>
	<h3>登录权限</h3><input type="checkbox">
	<h3>权限分配</h3><input type="checkbox">
	<h4>创建账号</h4><input type="checkbox">

	<h2>用户查询</h2>
	<h3>用户查询</h3><input type="checkbox">
	<h3>账号封停</h3><input type="checkbox">
	<h3>账号解封</h3><input type="checkbox">
	<h3>游戏查询</h3><input type="checkbox">
	<h3>视频查询</h3><input type="checkbox">
-->

<input class="btn btn-default" id="btnSumbit" type="submit" value="提交">
@endsection

@section('javascript')
<script type="text/javascript">

jQuery(document).ready(function() {
	// 界面初始化
	init();

	// 初始化函数
	function init(){
		var permList = "{{$permList}}";
		// 如果permList = all,则所有的checkbox选中
		if (permList == 'all') {
			$("input[type=checkbox]").attr('checked','checked');
		}else if(permList != ''){  // 只是选中相应的
			var perm = permList.split(','); // 这是一个数组
			for (var i = 0; i <= perm.length-1; i++) {
				$("input[type=checkbox][id=" + perm[i] + "]").attr('checked','checked');
			}
		}
	}
});


// 保存按钮事件
$("#btnSumbit").click( function () {

	var userId = $("#userid").attr('tar');
	var arrPerm=[];  // 保存哪些勾选的权限，存储主键id
	var index = 0;
	$('input[type=checkbox]').each(function(i,e){
		// 便利checkbox 是否选中

		if ($(e).is(':checked')) {  // 表示选中了
			arrPerm.push($(e).attr("id"));  // 存储被选中的主键集合
		}
	});

	var perm = arrPerm.toString();
		perm = $.trim(perm);

	// 通过异步ajax 更新具体权限
	$.ajax({
		type: 'get',
		url: '/ajax/updatePerm',
		data: {userId : userId, perm : perm},
		dataType: 'json',
		headers: {  // header属性，是为了避免跨站伪造请求攻击写的
		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		},
		success: function(data){
			if (data.status == 1) {
				alert('更新权限成功')
				// 刷新整个页面
				window.location.href =window.location.href
			}else{
				alert(data.msg);
			}
		},
		error: function(xhr, type){
		}
		});
	});

</script>
@endsection
