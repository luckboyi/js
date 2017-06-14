@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')
@section('content')
<!--内容-->
<div class="container container-list">
    <div class="container-head clearfix">
        <p class="numTag"><span class="{{ $left }} btn-online-product">已上线({{ $onlineNum }})</span><span class="{{ $right }} btn-offline-product">未上线({{ $offlineNum }})</span></p>
         @if(!isset($user['parentid']))
        <button type="button" class="btn btn-webgame-create">
            <span class="icon icon-small"><i class="icon icon-small icon-plus"></i></span> 添加游戏
        </button>
        @endif
    </div>
    <div class="table-con">
        <table class="personal-table" border="0">
         <tr class="title">
                <th>页游名称</th>
                <th>运营状况</th>
                <th>上线时间</th>
                <th>总营收</th>
            </tr>
            @if($games->count()<1)
                <tr><td colspan="4">暂无数据</td></tr>
            @else
                @foreach ($games as $game)
                <tr class="btn-webgame-detail" data-id="{{ $game["appid"] }}">
                <td>{{ $game["name"] }}</td>
                <td>{{ $blade->showStat($game["stat"],"text") }}</td>
                <td>{{ $blade->showDateTime($game["send_time"]) }}</td>
                <td>0</td>
                </tr>
                @endforeach
            @endif
        </table>
          <div class="page"> {!! $games->render() !!}</div>
    </div>

</div>
@endsection