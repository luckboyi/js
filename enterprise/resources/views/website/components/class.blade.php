<div class="screen filter-con @if($type == 'video') videos_screen @endif">
    <div class="screen_title">
        <h3>按类型筛选</h3>
    </div>
    <div class="screen_con">
        <ul class="clearfix">
            @foreach(config($type . ".class") as $value)
            <li class="fl {{isset($doClass)?$doClass:""}}" type="{{$type}}" class-id="{{$value['id']}}">{{$value["name"]}}</li>
            @endforeach
        </ul>
    </div>
</div>
