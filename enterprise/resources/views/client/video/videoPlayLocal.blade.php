@extends('client.video.layout')
@section('title')多媒体@endsection
@section('content')
	<div class="multimedia_right_con  video_con_hei pr"  id="video_right_con">
		<div class="scrollbar  video_con_hei pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
		<div class="viewport  video_con_hei">
			<div class="overview">
				<div class="video_play_container local_video_play">
					{{--<div class="valiantPhoto" id="valiantPhoto" data-video-src="@if(!empty($videoInfoDet)) {{ $videoInfoDet['resources'] }} @endif" style="width: 976px; height: 540px;background: #000"></div>--}}
				</div>
			</div>
		</div>
	</div>

@endsection

@section('javascript')
<script type="text/javascript">
	$(".choice-tab").click(function(){
		$(this).addClass('tab_titleBg').siblings().removeClass('tab_titleBg');
		var id=$(this).attr("tag-id");
		$(".tag-content").hide();
		$("#"+id).show();
	})
</script>

@endsection

