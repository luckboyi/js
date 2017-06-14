@extends('client.video.layout')
@section('title')多媒体@endsection
@section('content')

	<div class="multimedia_right_con  video_con_hei pr"  id="video_right_con">
		{{--<div class="scrollbar  video_con_hei pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>--}}
		<div class="viewport  video_con_hei">
			<div class="overview">
				<div class="pageGame_item more">
					@if(isset($videoInfo) && !empty($videoInfo))
						<div class="title">
							<p class="video_more_title historyRecord">今天</p>
						</div>
						<ul class="clearfix">
							@if(isset($videoInfo['today']) && !empty($videoInfo['today']))
								@foreach($videoInfo['today'] as $tk=>$tv)
									<li class="fl pr">
										<a href="{{ url('client/video/player?vid=' . $tv['video_id'] . '&vsort=no')}}">
											<img src="{{ $tv['video_cover'] }}">
											<div class="play_mask"></div>
											<div class="play_video"></div>
										</a>
										<p class="pa clearfix">
											<span class="fl look">{{ $tv['video_view'] }}</span>
											<span class="fr">{{ $tv['videotimes'] }}</span>
										</p>
										<div class="clearfix">
											<span class="fl">{{ $tv['video_name'] }}</span>
										</div>
									</li>
								@endforeach
							@else
								<h3></h3>
							@endif
						</ul>
					</div>
					<div class="pageGame_item more">
						<div class="title">
							<p class="video_more_title historyRecord">一周内</p>
						</div>
						<ul class="clearfix">
							@if(isset($videoInfo['week']) && !empty($videoInfo['week']))
								@foreach($videoInfo['week'] as $wk=>$wv)
									<li class="fl pr">
										<a href="{{ url('client/video/player?vid=' . $wv['video_id'] . '&vsort=no')}}">
											<img src="{{ $wv['video_cover'] }}">
											<div class="play_mask"></div>
											<div class="play_video"></div>
										</a>
										<p class="pa clearfix">
											<span class="fl look">{{ $wv['video_view'] }}</span>
											<span class="fr">{{ $wv['videotimes'] }}</span>
										</p>
										<div class="clearfix">
											<span class="fl">{{ $wv['video_name'] }}</span>
										</div>
									</li>
								@endforeach
							@else
								<h3></h3>
							@endif
						</ul>
					</div>
					<div class="pageGame_item more">
						<div class="title">
							<p class="video_more_title historyRecord">更早</p>
						</div>
						<ul class="clearfix">
							@if(isset($videoInfo['earlier']) && !empty($videoInfo['earlier']))
								@foreach($videoInfo['earlier'] as $ek=>$ev)
									<li class="fl pr">
										<a href="{{ url('client/video/player?vid=' . $ev['video_id'] . '&vsort=no')}}">
											<img src="{{ $ev['video_cover'] }}">
											<div class="play_mask"></div>
											<div class="play_video"></div>
										</a>
										<p class="pa clearfix">
											<span class="fl look">{{ $ev['video_view'] }}</span>
											<span class="fr">{{ $ev['videotimes'] }}</span>
										</p>
										<div class="clearfix">
											<span class="fl">{{ $ev['video_name'] }}</span>
										</div>
									</li>
								@endforeach
							@else
								<h3></h3>
							@endif
						</ul>
					</div>
				@else
					<h3>暂无数据！</h3>
				@endif
			</div>
		</div>
	</div>
@endsection

@section('javascript')

	<script>
		$('#videoBanner').movingBoxes({
			startPanel   : 1,
			reducedSize  : .9,
			wrap         : true,
			buildNav     : true,
			navFormatter : function(){ return ""; } // 指示器格式，为空即会显示123
		});



		//导航条
		$('.nav ul li').each(function(){
			$(this).mouseover(function(){
				$(this).children('.con').show();
			});
			$(this).mouseout(function(){
				$(this).children('.con').hide();
			})
		});
		$(".bigPic,.minPic").click(function(){
			var videoId = $(this).attr("data-vid"),
					sort = $(this).attr("data-sort");
			window.location.href="http://www.vronline.com/client/video/player?vid=" + videoId + "&vsort=" + sort;
		});
	</script>
@endsection