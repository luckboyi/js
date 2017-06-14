<div class="{{ isset($fr) ? "fr":""}} popular_recommende">
    <div class="title">
        <h3 class="fl">热门推荐</h3>
        <div class="clearfix fr detail-top">
            <a class="fl cur" href="javascript:;" id="detail-top-news"><span>新闻</span>/</a>
            <a class="fl" id="detail-top-game" href="javascript:;"><span>游戏</span>/</a>
            <a class="fl" id="detail-top-video" href="javascript:;"><span>视频</span></a>
        </div>
    </div>
    @if(isset($data))
        @foreach($data as $k=>$v)
            <div class="recommende_con {{$k}}_hot @if($k=='detail-top-news') cur showB @else cur hideB @endif">
                <ul>
                    @if(!empty($v))
                        @foreach($v as $ik=>$info)
                            <li>
                                <a href="/news/detail/{{ $info['itemid'] }}.html">
                                    <div class="@if($ik == 0) conceal @endif clearfix">
                                        <span class="fl ranking @if($ik<3) ranking_good @endif">{{ $ik+1 }}</span>
                                        <span class="fl ells ranking_con">{{ $info['title'] }}</span>
                                    </div>
                                    <div class="clearfix bewrite @if($ik == 0) shows @endif">
                                        <span class="ells2 fl">
                                            <img src="{{static_image($info['cover'],133)}}" style="width: 133px; height:99px;">
                                        </span>
                                        <div class="fr">
                                            <span class="ells2">{{ $info['title'] }}</span>
                                            <p class="ells2">{{ $info['desc'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endforeach
    @endif
</div>
