 <div class="h2-wrap">
    <h2>专题栏目</h2>
</div>
@foreach($data as $top)
    <div class="collomn-item">
    <a href="{{ $top['target_url'] }}" target="_blank">
        <img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}">
        <span class="tit dot">{{ $top['title'] }}</span>
    </a>
</div>
@endforeach
