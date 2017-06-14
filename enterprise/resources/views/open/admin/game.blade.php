@inject('blade', 'App\Helper\BladeHelper')
@extends('open.admin.nav')
@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection
@section('content')

<div class="ui basic small buttons">
{!! $blade->showHtmlClass('game_stat',$choose,'menu') !!}
</div>

<div class="ui small button right floated blue" onclick="dataEdit(0)"><i class="plus icon"></i>添加VR游戏</div>

@include('open.admin.search')

<table class="ui selectable celled table fixed">
  <thead>
    <tr>
      <th width="10%">ID</th>
      <th width="25%">名称</th>
      <th width="10%">状态</th>
      <th width="10%">活跃</th>
      <th width="10%">新增</th>
      <th width="10%">收入</th>
      <th width="20%">进度</th>
    </tr>
  </thead>
    <tbody>
          @if($data->count()<1)
              <tr><td colspan="7" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($data as $val)
              <tr data-val="{{ $val["appid"] }}" class="warning open-game">
              <td>{{ $val["appid"] }}</td>
              <td>{{ $val["name"] }}</td>
             <td>{{  $blade->showHtmlStat('game',$val["stat"],$val["send_time"]) }}</td>
              <td>/</td>
              <td>/</td>
              <td>/</td>
              <td class="right aligned">
              <!-- <a class="ui green label">基本信息</a>
              <a class="ui green label">图片资源</a>
              <a class="ui green label">电子合同</a>
              <a class="ui green label">版本信息</a> -->
              </td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr><th colspan="7">
        {!! $data->appends(['choose'=>$choose,'search'=>$search])->render() !!}
    </th>
  </tr></tfoot>
</table>

@include("open.admin.vrgame_edit")

@endsection

@section('javascript')
<script type="text/javascript">

$(function(){
  addSearch('/developer/vrgame');
  $(".open-game").click(function(){
    var id = $(this).attr("data-val");
    location.href = "/developer/vrgame/detail/"+id
  })
});

</script>
@endsection
