<div class="{{isset($class)?$class:"fl Manufacturer"}}">
    <div class="title">
        <h3 class="fl">{{isset($title)?$title:""}}</h3>
        <a class="fr" href="{{isset($link)?$link:"javascript:;"}}" target="_blank">更多</a>
    </div>
    <div class="Manufacturer_con">
        @if(isset($data[0]))
        <div class="clearfix">
            <a class="fl" href="{{$data[0]["link"]?:"javascript:;"}};" target="_blank"><img src="{{static_image($data[0]["cover"],133)}}"></a>
            <div class="fr">
                <a class="ells2" href="{{$data[0]["link"]?:"javascript:;"}}" target="_blank">{{$data[0]["title"]}}</a>
                <p class="ells2">{{$data[0]["desc"]}}</p>
            </div>
        </div>
        <?php unset($data[0]);?>
        @endif
        <ul>
            @if(is_array($data))
            @foreach($data as $value)
            <li>
                <a class="ells" href="{{$value["link"]?:"javascript:;"}}" target="_blank">{{$value["title"]}}</a>
                <p class="ells">{{$value["desc"]}}</p>
            </li>
            @endforeach
            @endif
        </ul>
    </div>
</div>