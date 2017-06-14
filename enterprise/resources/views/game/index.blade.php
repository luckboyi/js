@extends('layouts.admin')

@section('content')
<h3 class="page-title">
游戏查询
</h3>
<ul class="breadcrumb">
    <li>
        <a href="#">首页</a>
        <span class="divider">/</span>
    </li>
    <li class="active">
        游戏查询
    </li>
    <li class="pull-right search-wrap">
        <form action="" class="hidden-phone">
            <div class="input-append search-input-area">
                <input class="" type="text" name="name" placeholder="搜索游戏" value="{{ $name }}">
                <button class="btn" type="submit"><i class="icon-search"></i> </button>
            </div>
        </form>
    </li>
</ul>

<p>
    @if ($name)
    搜索：{{ $name }} <a style="margin-left:10px;" href="{{ url("game") }}" >显示全部</a>
    @endif
</p>

@if (count($games)>0)
<table class="table table-striped table-hover table-bordered" id="editable-sample">
    <thead>
        <tr>
            <th>游戏编号</th>
            <th>游戏名称</th>
            <th>游戏类型</th>
            <th>支持设备</th>
            <th>游戏大小</th>
            <th>评论数量</th>
        </tr>
    </thead>
    <tbody>
        @foreach($games as $game)
        <tr class="">
            <td>{{ $game->appid }}</td>
            <td>{{ $game->name }}</td>
            <td>{{ $game->type }}</td>
            <td>{{ $game->device }}</td>
            <td>{{$game->gpackagesize}}</td>
            <td><a href="{{ url("gcomment/".$game->appid) }}" >{{$game->comment->count()}}</a></td>
        </tr>
        @endforeach
    </tbody>
</table>

{!! $games->render() !!}
@else

<div class="alert alert-block alert-warning fade in">
    <h4 class="alert-heading">没有搜索到游戏!</h4>
</div>

@endif

@endsection
