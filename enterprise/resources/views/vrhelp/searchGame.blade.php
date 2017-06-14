@extends('vrhelp.layout')
@section('meta')
<title>搜索游戏</title>
<link rel="stylesheet" href="{{ static_res('/vrhelp/style/3dbobo.css') }}">

@endsection

@section('head')


<style type="text/css">
        .paging{float:right;}
        .paging li{float:left;color:#fff;margin-left: 5px;cursor:pointer;}
    </style>
@endsection

@section('content')
      <div class="grid">
      <div class="search_text f14"><span class="f30">搜索结果</span>　搜索到“<span class="orange">{!!$name!!}</span>”相关<span class="orange">{!!$total!!}</span>条结果</div>
      <div class="search_list">
      <ul>
      @foreach($vrgame as $value)
      <li>
      <div class="fl"><img src="{!!static_image($value['image']['cover'])!!}" width="139" height="185"></div>
      <div class="fr slr">
      <div class="sgt"><div class="fl f18">@if(!empty($name)){!!preg_replace('/'.$name.'/','<span class="orange">'.$name.'</span>',$value['name'])!!}@else {!!$value['name']!!} @endif　</div><div class="fl f12 tag"><span>VR</span><span>页游</span></div><div class="fr f14">下载量 {!!$value['play']!!}</div></div>
      <div class="g_info f14 clearfix"><div class="fl s_type">类型：{!!config("vrgame.class")[$value['category'][0]]['name'];!!}</div><div class="fl s_eq"><span class="fl">设备：</span>
        <div class="fl f12 tag">
          @foreach(config("vrgame.support_device") as $dev)
            @if(in_array($dev['id'],$value['support']))
              <span>{!!$dev['name']!!}</span>
            @endif
          @endforeach
        </div></div><div class="fl s_parts"><span class="fl">游戏配件：</span><div class="fl f12 tag">
          @foreach(config("category.vr_mountings") as $dev)
            @if(!empty($value['mountings']))
              @if(in_array($dev['id'],$value['mountings']))
                <span>{!!$dev['name']!!}</span>
              @endif
            @endif
          @endforeach
        </div></div><div class="fl s_time">上架时间：{!!date('Y-m-d',$value['time'])!!}</div></div>
      <div class="s_intr f12">{!!$value['desc']!!}</div>
      <div class="s_button"><div class="fl f14"><a href="#" class="game_play game_btn">开始游戏</a></div><div class="fr f16"><a class="gdt game_detail" data-val="{{ $value['id'] }}">游戏详情>></a></div></div>
      </div>
      </li>
      @endforeach
      </ul>
      </div>
      <div class="s_page">
      <?php
$count = 20;
$p     = 1;
$n     = ceil($total / $count);
$flag  = 0;
if ($page > 1) {
    $p = $page - 1;
}
if ($page < ceil($total / $count)) {
    $n = $page + 1;
}
//优化分页样式专用
if ($page < 4) {
    $npage = 3;
} else {
    $npage = $page;
}
?>
      <a href="/vrhelp/searchGame?name={!!$name!!}&page={!!$p!!}">上一页</a>
      @for ($i = 1; $i <= ceil($total/$count); $i++)
        @if($i==1||$i==2||$i>$npage-3&&$i<$npage+3)
          <a href="/vrhelp/searchGame?name={!!$name!!}&page={!!$i!!}" @if($page==$i)class="active"@endif>{!!$i!!}</a>
        @else
          @if($i>2&&$npage-$i>2&&$flag==0)
            <a class="ellipsis">...</a>
            <?php $flag = 1;?>
          @endif
        @endif
      @endfor
      @if(ceil($total / $count)-$npage>2)
        <a class="ellipsis">...</a>
      @endif
      <a href="/vrhelp/searchGame?name={!!$name!!}&page={!!$n!!}">下一页</a>
      </div>
      </div>
    </div>

@endsection


@section('javascript')

<script type="text/javascript">

</script>
@endsection
