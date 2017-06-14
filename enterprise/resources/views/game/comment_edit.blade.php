@extends('layouts.admin')

@section('content')
<h3 class="page-title">
评论编辑
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
    <li>
        <a href="{{ url("game") }}">评论控制</a>
        <span class="divider">/</span>
    </li>
    <li class="active">
        评论编辑
    </li>
</ul>

<h4>
@if ($game)
游戏名称：{{ $game->gname }}
@else
游戏不存在
@endif
</h4>
@if (session('status'))
<div class="alert alert-block alert-success fade in">
    <button data-dismiss="alert" class="close" type="button">×</button>
    <h4 class="alert-heading">{{ session('status') }}</h4>
</div>
@endif

@if (session('error'))
<div class="alert alert-block alert-error fade in">
    <button data-dismiss="alert" class="close" type="button">×</button>
    <h4 class="alert-heading">{{ session('error') }}</h4>
</div>
@endif


@if ($comment)
<div class="widget green">
    <div class="widget-title">
        <h4><i class="icon-reorder"></i> 编辑</h4>
    </div>
    <div class="widget-body">
        <form action="" class="form-horizontal" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="PUT">
            <div class="control-group">
                <label class="control-label">评论账号：</label>
                <div class="controls">
                    <input type="text" class="medium" value="{{ $comment->uid }}" disabled="disabled">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">评论id：</label>
                <div class="controls">
                    <input type="text" class="medium" value="{{ $comment->uid }}" disabled="disabled">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">评论内容：</label>
                <div class="controls">
                    <textarea class="input-xxlarge" name="content" rows="3">{{ $comment->content }}</textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> 保存</button>
                <button type="button" class="btn link-to-url" href="{{url("gcomment/{$game->gid}")}}"><i class=" icon-remove"></i> 取消</button>
            </div>
        </form>
    </div>
</div>
@else

<div class="alert alert-block alert-warning fade in">
    <h4 class="alert-heading">评论不存在!</h4>
</div>

@endif

@endsection
