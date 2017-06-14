@extends('website.media.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')全部视频@endsection

@section('webgameRight')
	<div class="right_con fr">
		<div class="banner">
			<ul id="videoBanner">
				@if(isset($bannerDate) && !empty($bannerDate) && is_array($bannerDate))
					@foreach($bannerDate as $bk=>$bv)
						<li class="poster-item " bannerid="{{$bv['id']}}" itemid="{{$bv['itemid']}}" bannertype="banner">
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
			<ul class="clearfix" id="pageData">
				{{--@if(isset($videoInfo) && !empty($videoInfo) && is_array($videoInfo))--}}
					{{--@foreach($videoInfo as $vk=>$vv)--}}
						{{--<li class="fl pr">--}}
							{{--<a href="{{ url('videoPlay?vid=' . $vv['vid'] . '&vsort=' . $sort)}}">--}}
								{{--<img src="{{ $vv['videologo'] }}">--}}
								{{--<div class="play_mask"></div>--}}
								{{--<div class="play_video"></div>--}}
							{{--</a>--}}
							{{--<p class="pa">--}}
								{{--<span class="fl look">{{ $vv['viewednum'] }}</span>--}}
								{{--<span class="fr">{{ timeFormat($vv['videotimes']) }}</span>--}}
							{{--</p>--}}
							{{--<div class="clearfix">--}}
								{{--<span class="fl">{{ $vv['videoname'] }}</span>--}}
							{{--</div>--}}
						{{--</li>--}}
					{{--@endforeach--}}
				{{--@endif--}}
			</ul>
		</div>
		<!-- END PAGE -->
		{{--<div class="paginateStart">--}}
			{{--{!! $videoInfo->render() !!}--}}
		{{--</div>--}}
	</div>



@endsection

@section('javascript-media')
	<script src="{{static_res('/common/js/scrollloading.js')}}"></script>
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
			window.location.href="http://www.vronline.com/mediaPlayer?vid=" + videoId + "&vsort=" + sort;
		});

		//滚动刷新方法
		scrollLoading.init({
			url:"{{ url('video/page/'. $sort) }}",//type=ajax时为请求地址
			contentHtmlCon:'#pageData',
			ajaxDataType:'html',
		});
	</script>
@endsection
