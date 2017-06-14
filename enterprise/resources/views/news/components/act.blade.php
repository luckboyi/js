@if($type=="act")
<div class="active_list">
    <h3 class="f18">活动推广</h3>
@endif
    @foreach($data as $val)
    <div class="add_container">
    <a href="{!! $val['link'] !!}"  target="_blank">
        <img src="{{ static_image($val['cover']) }}" width="100%" height="100%">
    </a>
    </div>
    @endforeach
@if($type=="act")
</div>
@endif
