<div class="tab">
    <div class="tab_title clearfix">
        <span class="fl tab_titleBg">新服公告</span>
        <span class="fl">已开新服</span>
    </div>
    <div class="tab_con">
        <div class="clearfix">
            <span class="fl">游戏名称</span>
            <span class="fl">服务器</span>
            <span class="fl">时间</span>
        </div>
        <ul>
            @if(isset($preServers) && is_array($preServers) && count($preServers)>0)
            @foreach ($preServers as $server)
            <li class="clearfix start-web-game" game-id="{{$server["appid"]}}" game-name="{{isset($webgames[$server["appid"]])?$webgames[$server["appid"]]["name"]:"未知游戏"}}" server-id={{$server["serverid"]}} server-name="{{$server["name"]}}">
                <span class="fl">
                    {{isset($webgames[$server["appid"]])?$webgames[$server["appid"]]["name"]:"未知游戏"}}
                </span>
                <span class="fl">{{ $server["name"] }}</span>
                <span class="fl tab_conTime show">{{ date("m-d H:i",$server["start"]) }}</span>
                <!-- <span class="fl gameEnter">进入游戏</span> -->
            </li>
            @endforeach
            @else
            <li class="clearfix">
                <span class="fl">暂无新服</span>
                <span class="fl">暂无新服</span>
                <span class="fl">暂无新服</span>
            </li>
            @endif
        </ul>
        <ul class='tab_hide'>
            @if(isset($newServers) && is_array($newServers) && count($newServers)>0)
            @foreach ($newServers as $server)
            <li class="clearfix start-web-game" game-id="{{$server["appid"]}}" game-name="{{isset($webgames[$server["appid"]])?$webgames[$server["appid"]]["name"]:"未知游戏"}}" server-id={{$server["serverid"]}} server-name="{{$server["name"]}}">
                <span class="fl">
                    @if(isset($webgames[$server["appid"]]))
                    {{ $webgames[$server["appid"]]["name"] }}
                    @else
                    未知游戏
                    @endif
                </span>
                <span class="fl">{{ $server["name"] }}</span>
                <span class="fl tab_conTime">{{ date("m-d H:i",$server["start"]) }}</span>
                <span class="fl gameEnter">进入游戏</span>
            </li>
            @endforeach
            @else
            <li class="clearfix">
                <span class="fl">暂无新服</span>
                <span class="fl">暂无新服</span>
                <span class="fl">暂无新服</span>
            </li>
            @endif
        </ul>
    </div>
</div>
