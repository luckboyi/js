@extends('client.video.layout')
@section('title')多媒体@endsection
@section('content')
	<div class="multimedia_right_con  video_con_hei pr"  id="video_right_con">
		<div class="scrollbar  video_con_hei pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
		<div class="viewport  video_con_hei">
			<div class="overview">
				<div class="banner">
					<ul id="videoBanner">
						@if(isset($bannerDate) && !empty($bannerDate))
							@foreach($bannerDate as $bk=>$bv)
								<li class="poster-item ">
									<a href="{{ $bv['target_url'] }}">
										<img src="{{ $bv['banner_url'] }}">
									</a>
								</li>
							@endforeach
						@endif
					</ul>
				</div>
				<div class="pageGame_item more">
					<div class="title"></div>
					<ul class="clearfix"  id="pageData">

					</ul>
				</div>
			</div>
		</div>
	</div>



@endsection

@section('javascript')
	<script src="{{ static_res('/common/js/scrollloading.js') }}"></script>
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
		//滚动刷新方法
		scrollLoading.init({
			url:"{{ url('video/page2/'. $sort) }}",//type=ajax时为请求地址
			contentHtmlCon:'#pageData',
			ajaxDataType:'html',
		});
	</script>
@endsection