<div class="h2-wrap mt20">
    <h2>热门活动</h2>
    <a href="/vronline/top/news-index-act" target="_blank" class="h2-more">查看更多</a>
</div>
<ul class="activity-list">
    @foreach($data as $top)
     <li>
       <a href="{{ $top['target_url'] }}" target="_blank">
            <span class="tag"></span>
            <span class="tag"></span>
            <span class="date">{{ $top['title'] }}</span>
            <span class="tit dot">{{ $top['intro'] }}</span>
        </a>
    </li>
    @endforeach
</ul>
