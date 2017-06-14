@inject('blade', 'App\Helper\BladeHelper')
<div class="game_ranking">
    <div class="title clearfix">
        <i class="fl"></i>
        <span class="fl f16">游戏排行</span>
        <div class="fr clearfix">
            <p class="fl clearfix">
                <span class="fl red gamePages">1</span>
                <span class="fl">/</span>
                <span class="fl gameTotalPages">@if(isset($data) && is_array($data) && count($data)>0) {{ ceil(count($data)/5) }} @endif</span>
            </p>
            <p class="fl tab clearfix choice">
                <span class="fl cur gamesPro"><</span>
                <span class="fl gamesNext">></span>
            </p>
        </div>
    </div>
    <div>

<?php
$pagesCount = ceil(count($data) / 5);
if ($pagesCount == 0) {
    $pagesCount = 1;
}
?>
            @if(isset($data) && count($data)>0)
            @for($i=1;$i<=$pagesCount;$i++)
                <ul id="gameRank{{$i}}" class="@if($i!==1) hide @endif">
                @foreach($data as $k=>$v)
                    @if(($i-1)*5 <= $k && $k < $i*5)
                    <li class="@if($k === 0) cur @endif">
                        <a href="javascript:;" class="website-jump" appid="{{$v['id']}}" game-name="{{ $v['name'] }}" >
                            <div class="clearfix">
                                <span class="fl">{{ $k+1 }}</span>
                                <span class="fl game_icon"><img src="{{ static_image($v["image"]["icon"]) }}" /></span>
                                <span class="fl game_name ells">{{$v["name"]}}</span>
                                <span class="fl">{{$v['category'][0]}}</span>
                                <span class="fr paly_game website-jump" appid="{{$v['id']}}" game-name="{{ $v['name'] }}" >进入游戏</span>
                            </div>
                            <div class="pr">
                                <i>{{ $k+1 }}</i>
                                <span class="game_img"><img src="{{ static_image($v["image"]["rank"]) }}" /></span>
                                <p>{{$v["name"]}}</p>
                            </div>
                        </a>
                    </li>
                    @endif
                @endforeach
        </ul>
        @endfor
        @endif
    </div>
</div>
