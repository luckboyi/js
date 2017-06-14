<div class="{{isset($class)?$class:"fl industry"}}">
    <div class="title">
        <h3 class="fl">{{isset($title)?$title:""}}</h3>
        <a class="fr" href="{{isset($link)?$link:"javascript:;"}}" target="_blank">更多</a>
    </div>
    <div>
        <ul>
            @if(is_array($data))
            @foreach($data as $value)
            <li class="clearfix">
                <a class="fl" href="{{$value["link"]?:"javascript:;"}}" target="_blank"><img src="{{static_image($value["cover"],133)}}"></a>
                <div class="fr">
                    <a class="@if(isset($class)) ells2 @else ells @endif" href="{{$value["link"]?:"javascript:;"}}" target="_blank">{{$value["title"]}}</a>
                    <p class="ells2">{{$value["desc"]}}</p>
                </div>
            </li>
            @endforeach
            @endif
        </ul>
    </div>
</div>
