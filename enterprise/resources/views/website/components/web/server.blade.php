@inject('blade', 'App\Helper\BladeHelper')
<div class="open_service">
    <div class="title clearfix">
        <i class="fl"></i>
        <span class="fl f16">页游开服表</span>
        <div class="fr clearfix">
            <p class="fl clearfix">
                <span class="fl red pages">1</span>
                <span class="fl">/</span>
                <span class="fl totalPages">@if(isset($newServers) && is_array($newServers) && count($newServers)>0) {{ ceil(count($newServers)/5) }} @endif</span>
            </p>
            <p class="fl tab clearfix choice">
                <span class="fl cur pro"><</span>
                <span class="fl next">></span>
            </p>
        </div>
    </div>
    <div>

<?php
$pagesCount = ceil(count($newServers) / 5);
?>
    @for($i=1;$i<=$pagesCount;$i++)
        <ul id="webservers{{$i}}" class="@if($i!==1) hide @endif">
            @if(isset($newServers) && is_array($newServers) && count($newServers)>0)
                @foreach ($newServers as $sk => $server)
                    @if(($i-1)*5 <= $sk && $sk < $i*5)
                        <li class="start-web-game" appid="{{$server["appid"]}}" game-name="{{isset($webgames[$server["appid"]])?$webgames[$server["appid"]]["name"]:"未知游戏"}}" server-id={{$server["serverid"]}} server-name="{{$server["name"]}}">
                            <a href="javascript:;">
                                <p class="clearfix">
                                    <span class="fl"><img src="{{ $webgames[$server["appid"]]["img_url"] }}" /></span>
                                    <span class="fl">{{isset($webgames[$server["appid"]])?$webgames[$server["appid"]]["name"]:"未知游戏"}}</span>
                                </p>
                                <div class="clearfix">
                                    <span class="fl">{{$server["name"]}}</span>
                                    <p class="fr clearfix"><i class="fl"></i><span>{{ date('Y-m-d H:i', $server['start']) }}</span></p>
                                </div>
                            </a>
                        </li>
                    @endif
                @endforeach
            @else
            <li>
                <a href="javascript:;">
                    <p class="clearfix">
                        <span class="fl"><img src="http://pic.vronline.com/webgames/images/02.png" /></span>
                        <span class="fl">青云志</span>
                    </p>
                    <div class="clearfix">
                        <span class="fl">双线361区</span>
                        <p class="fr clearfix"><i class="fl"></i><span>9:00</span></p>
                    </div>
                </a>
            </li>
            @endif
        </ul>
    @endfor
    </div>
</div>
