<div class="detail_foot_list clearfix realnews">
	<ul class="fl tac">
		<li class="cur" data-val="news">
			<a href="javascript:;">新闻</a>
		</li>
		<li data-val="game">
			<a href="javascript:;">游戏</a>
		</li>
		<li data-val="video">
			<a href="javascript:;">视频</a>
		</li>
	</ul>
	@foreach($realnews as $realKey=>$arr)
	<ol class="fl {{ $realKey }}" @if($realKey!="news") style="display:none" @endif>
		@foreach($arr as $val)
		<li class="clearfix">
			<div class="img_con fl">
				<a href="/news/detail/{{ $val['id'] }}.html">
					<img src="{{ static_image($val['cover']) }} " width="100%" height="100%">
				</a>
			</div>
			<div class="fl txt">
				<h4 class="ells f16"><a href="/news/detail/{{ $val['id'] }}.html">{{ $val['title'] }}</a></h4>
				<p class="msg f12">{{ htmlSubStr($val['content']) }}</p>
			</div>
		</li>
		@endforeach
	</ol>
	@endforeach
</div>