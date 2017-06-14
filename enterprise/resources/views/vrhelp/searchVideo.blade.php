@extends('vrhelp.layout')
@section('meta')
<title>搜索视频</title>
@endsection

@section('head')
<link rel="stylesheet" href="{{ static_res('/vrhelp/style/3dbobo.css') }}">
<style type="text/css">
        .paging{float:right;}
        .paging li{float:left;color:#fff;margin-left: 5px;cursor:pointer;}
    </style>
@endsection

@section('content')
      <div class="grid">
      <div class="search_text f14"><span class="f30">搜索结果</span>　搜索到“<span class="orange">{!!$name!!}</span>”相关<span class="orange">{!!$total!!}</span>条结果</div>
      <div class="fl">
      <div class="s_video_list">
      <ul>
      @foreach($video as $value)
      <li>
      <div class="fl s_video_cover pr f14"><a href="JavaScript:;" class="video_play" data-val="{!!$value['id']!!}" style="background-image:url('{!!static_image($value['image']['cover'])!!}');">
      <div class="msg_con pa">@if(!empty($value['show_time'])){!!floor($value['show_time']/3600)!!}:{!!floor($value['show_time']/60)!!}:{!!$value['show_time']%60!!}@endif</div>
      <span class="cover"></span>
      <span class="video_play_icon pa"><i class="video_triangle pa"></i></span>
      </a></div>
      <div class="fr s_video_intr">
      <p class="f20"><a href="JavaScript:;" class="video_play" data-val="{!!$value['id']!!}">@if(!empty($name)){!!preg_replace('/'.$name.'/','<span class="orange">'.$name.'</span>',$value['name'])!!}@else {!!$value['name']!!} @endif</a></p>
      <p class="f14 clearfix"><span class="fl">时  间：{!!date('Y-m-d',$value['time'])!!}</span><span class="fr">来  源：腾讯视频</span></p>
      <p class="f14 els">主  题：</p>
      <p class="f14 els">简  介：{!!$value['desc']!!} </p>
      </div>
      </li>
      @endforeach
      </ul>
      </div>
      <div class="s_page">
      <?php $count = 20;
$p                 = 1;
$n                 = ceil($total / $count);
$flag  = 0;
if ($page > 1) {
    $p = $page - 1;
}
if ($page < ceil($total / $count)) {
    $n = $page + 1;
}
//优化分页样式专用
if($page<4){
  $npage=3;
}
else{
  $npage=$page;
}
?>
      <a href="/vrhelp/searchVideo?name={!!$name!!}&page={!!$p!!}">上一页</a>
      @for ($i = 1; $i <= ceil($total/$count); $i++)
        @if($i==1||$i==2||$i>$npage-3&&$i<$npage+3)
          <a href="/vrhelp/searchVideo?name={!!$name!!}&page={!!$i!!}" @if($page==$i)class="active"@endif>{!!$i!!}</a>
        @else
          @if($i>2&&$npage-$i>2&&$flag==0)
            <a class="ellipsis">...</a>
            <?php $flag=1;?>
          @endif
        @endif
      @endfor
      @if(ceil($total / $count)-$npage>2)
        <a class="ellipsis">...</a>
      @endif
      <a href="/vrhelp/searchVideo?name={!!$name!!}&page={!!$n!!}">下一页</a>
      </div>
      </div>

      <div class="fr s_word"><p class="f18 pb10">热搜关键词</p>
      @foreach($hotWord['data'] as $key => $word)
      <a href="JavaScript:;" class="f18"><i class="s_ranking tac f14 fl">{!!$key+1!!}</i><div class="s_video_name fl">{!!$word['name']!!}</div></a>
      @endforeach
      </div>
      </div>
    </div>
@endsection

@section('javascript')

<script type="text/javascript">
videoPlay.init(config);

</script>
@endsection
