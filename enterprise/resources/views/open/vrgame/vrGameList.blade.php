@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')
@section('content')
<!--内容-->
<div class="container container-list">
    <div class="container-head clearfix">
        <p class="numTag"><span class="{{ $left }} btn-online-vr">已上线({{ $onlineNum }})</span><span class="{{ $right }} btn-offline-vr">未上线({{ $offlineNum }})</span></p>
         @if(!isset($user['parentid']))
        <button type="button" class="btn btn-vrgame-create">
            <span class="icon icon-small"><i class="icon icon-small icon-plus"></i></span> 添加游戏
        </button>
        @endif
    </div>
    <div class="table-con">
        <table class="personal-table" border="0">
         <tr class="title">
                <th>游戏名称</th>
                <th>当日营收</th>
                <th>当日新增用户数</th>
                <th>当日活跃用户数</th>
            </tr>
            @if($games->count()<1)
                <tr><td colspan="4">暂无数据</td></tr>
            @else
                @foreach ($games as $game)
                <tr>
                <td class="btn-vrgame-detail" data-id="{{ $game["appid"] }}">{{ $game["name"] }}</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                </tr>
                @endforeach
            @endif
        </table>
          <div class="page"> {!! $games->render() !!}</div>
    </div>

</div>
@endsection
